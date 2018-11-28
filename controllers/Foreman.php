<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Foreman extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->data['menu_title'] = 'Foreman';
        $this->load->model('admin_model', 'admin');
        $this->load->model('foreman_model', 'foreman');
        $this->load->model('project_model', 'project');
        $this->load->model('Companies_model', 'company');
        $this->load->model('plan_model', 'plan');
        $this->load->library("PHPMailer_Library");
        $this->load->helper('SendSms');
        checkAdmin();
    }

    public function index() {
        
        $data = $this->data;
        $data['foremans'] = $this->foreman->get_datatables();
        $data['title'] = 'Supervisor List';
        $data['description'] = 'All Supervisor list';
        $data['page'] = 'foreman/list_foreman';
        if($this->session->userdata('user_designation') == 'Superadmin'){
            $data['planId'] = (object) array('id' =>  '3');
            $data['limit']  = (object) array('wLimit' =>  '1');
        }else{
            $data['planId'] = $this->plan->get_PlanId($this->session->userdata('company_id'));
            $data['limit'] = $this->plan->get_limit('pp.no_of_supervisor - count(w.company_id) AS wLimit',$this->session->userdata('company_id'),'user','w. status != 2 AND w. user_designation = "supervisor"');
        }
        $this->load->view('includes/template', $data);
    }

    public function addForeman() {
        $fileError = array();
        
        if (isset($_POST['submit'])) {
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('name', 'First Name', 'trim|required|regex_match[/[a-zA-Z]/]');
            $this->form_validation->set_rules('lname', 'Last Name', 'trim|required|regex_match[/[a-zA-Z]/]');
            $this->form_validation->set_rules('plugin[]', 'Plugin Name', 'trim');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[user.user_email]|callback_updateEmail');
            $this->form_validation->set_rules('pid[]', 'Projects', 'trim|required');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[36]');
            $this->form_validation->set_rules('conf_password', 'Confirm Password', 'trim|required|matches[password]');

            $this->form_validation->set_rules('contact', 'Contact No.', 'trim|required|is_unique[user.user_contact]|regex_match[/^[0-9]{10}$/]|min_length[10]|numeric');
            
            if ($this->form_validation->run() == TRUE) {
                       
                $projectids = $this->input->post('pid');
                $pluginids = $this->input->post('plugin');
                $company_id = $this->input->post('company_id') ? $this->input->post('company_id') : $this->session->userdata('company_id');

                $data = array(
                    'user_name' => $this->input->post('name'),
                    'user_last_name' => $this->input->post('lname'),
                    'user_email' => $this->input->post('email'),
                    'password' => md5( $this->input->post('password') ),
                    'user_contact' => $this->input->post('contact'),
                    'company_id' => $company_id,
                    'user_designation' => 'Supervisor',
                    'portal_access' => $this->input->post('access'),
                    'status' => $this->input->post('status'),
                );
                 
                $foreman_id = $this->foreman->save('user', $data);
                
                //For Assign Plug In
				if($pluginids != ''){
					foreach ($pluginids  as $pluginid) {
						$this->foreman->save('plugin_assign', array('plugin_id' => $pluginid,'company_id'=>$company_id, 'user_id' => $foreman_id, 'status' => 1));
					}
				}

                if ($foreman_id) {
                        $counter = 0;
                    foreach ($projectids as $projectid) {
                    $counter++;
                        $this->foreman->save('user_project', array('project_id' => $projectid, 'user_id' => $foreman_id, 'status' => 1));

                    }
                    $forman_proj_list=$this->foreman->get_Formanprojects('project',$projectids);
                   if (!empty($forman_proj_list)) {
                        $FORMAN_PROJECTS = "<ul>";
                        foreach ($forman_proj_list as $project) {
                            $FORMAN_PROJECTS .= "<li>" . $project->project_name ." </li>";
                        }
                        $FORMAN_PROJECTS .= "</ul>";
                    }
                    
                    $projects = $this->foreman->get_projectDetails($this->input->post('user_id'));
                    if (!empty($projects)) {
                        $project_list = "<ul>";
                        foreach ($projects as $project) {
                            $project_list .= "<li>" . $project->project_name . "</li>";
                            $contractor_name = $project->project_id;
                        }
                        $project_list .= "</ul>";
                    }

                    $company_name = $this->company->companyName($company_id);

                    $subject = "Welcome to The Hajiri App";
                    $content = "<p>Hi " . ucwords($this->input->post('name'))." ".$this->input->post('lname').",</p>";
                    if($this->session->userdata('user_designation') == 'Superadmin'){
                        $content .= "<p>The Hajiri app has added you as a supervisor for the company ".$company_name->company_name.". Please find your login credentials;</p>";
                    }
                    else{
                        $content .= "<p>Congratulations! you have been selected as a Supervisor for the Hajiri app for ".$company_name->company_name.". Please find your login credentials;</p>";

                    }
                    $content .= "<p>Login ID: " . $this->input->post('email') . "</p>";

                    $content .= "<p>Your password: " . $this->input->post('password') . "</p>";
                    //$content .= "<p>Company name: " . $this->input->post('organization_name') . "</p>";
                    $content .= "<p>You have been assigned for the following projects;</p>";
                    $content .= "<p>Projects: " . $FORMAN_PROJECTS . "</p>";

                    $content .= "<p>Your app operations are constantly being monitored by your admin. Please carry out the app operations honestly!</p>";

                    $content .= "<p> </p>";
                    $content .= "<p>Regards,</p>";
                    $content .= "<p>Team Aasaan</p>";
                    $content .= "<p>http://www.aasaan.co/</p>";
                    $content .= "<img src='" . base_url('assets/admin/images/aasaan-footer-logo.jpg') . "' height='80' width='250'/>";
                    $this->load->library("PHPMailer_Library");
                    $mail = $this->phpmailer_library->load();
                    $mail->IsSMTP();                              // send via SMTP
                    $mail->Host = "ssl://smtp.zoho.com";
                    $mail->SMTPAuth = true;                       // turn on SMTP authentication
                    $mail->Username = "hajiri@aasaan.co";        // SMTP username
                    $mail->Password = "hajiriaasaan"; // SMTP password
                    $webmaster_email = "hajiri@aasaan.co";       //Reply to this email ID
                    $email=$this->input->post('email');                // Recipients email ID
                    $name=$this->input->post('name')." ".$this->input->post('lname');                              // Recipient's name
                    $mail->From = $webmaster_email;
                    $mail->Port = 465;
                    $mail->FromName = "The Hajiri App";
                    $mail->AddAddress($this->input->post('email'),$this->input->post('name').' '.$this->input->post('lname'));
                    $mail->AddReplyTo($webmaster_email,"The Hajiri App");
                    $mail->WordWrap = 50;                         // set word wrap
                    $mail->IsHTML(true);                          // send as HTML
                    $mail->Subject = $subject;
                    $mail->Body = $content;   
                   
                   if(!$mail->Send()){
						$this->session->set_flashdata("error", "Error in sending Email.");
				    }
                    else
                    {
                        $number= $this->input->post('contact');
                        $message_body = 'Congratulations '.$this->input->post('name').' '.$this->input->post('lname').', your account has been created on the Hajiri app for '.$company_name->company_name.'. Your login ID i"s '.$this->input->post('email').' and your password is '.$this->input->post('password').' Please do not share your login credentials with anyone. Enjoy using the Hajiri app!';
                                                    
                        $sms = sendsms($number,$message_body);
						$this->session->set_flashdata("success", "Supervisor added successfully.");
                    }
                    redirect(base_url('admin/foreman'));
                } else {
                    $this->session->set_flashdata('error', 'Failed To Add Supervisor');
                    redirect(base_url('admin/foreman/addForeman'));
                }
            }
        }
        $company_id = $this->session->userdata('company_id');
        $data = $this->data;
        if($this->session->userdata('user_designation') == 'Superadmin'){
            $data['planId'] = (object) array('id' =>  '3');
            $data['limit']  = (object) array('wLimit' =>  '1');
        }else{
            $data['planId'] = $this->plan->get_PlanId($this->session->userdata('company_id'));
            $data['limit'] = $this->plan->get_limit('pp.no_of_supervisor - count(w.company_id) AS wLimit',$this->session->userdata('company_id'),'user','w. status != 2 AND w. user_designation = "supervisor"');
        }
        $data['users'] = $this->foreman->get_all_managers('user'); //get_all_plugin
        $data['plugins'] = $this->foreman->get_all_plugin($company_id);

        $data['title'] = 'Add Supervisor';
        $data['fileError'] = $fileError;
        $data['description'] = 'Add Supervisor';
        $data['page'] = 'foreman/add_foreman';
        $data['companies'] = $this->company->get_datatables();
        $this->load->view('includes/template', $data);
    }

    function updateEmail() {
        
        if ($this->admin->checkUniqueUpdate('user','user_id', $this->uri->segment(4), 'user_email', $this->input->post('email'))) {
            $this->form_validation->set_message('updateEmail', 'Email address already exists');
            return false;
        }
        return true;
    }

	function updateContact() {
        
        if ($this->admin->checkUniqueUpdate('user','user_id', $this->uri->segment(4), 'user_contact', $this->input->post('contact'))) {
            $this->form_validation->set_message('updateContact', 'Contact number already exists');
            return false;
        }
        return true;
    }

    public function editForeman($id) {
        $company_id = $this->session->userdata('company_id');
        $fileError = array();
        
        if (isset($_POST['submit'])) {
			$userId = $this->session->userdata('company_id');
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('name', 'First Name', 'trim|required|regex_match[/[a-zA-Z]/]');
            $this->form_validation->set_rules('lname', 'Last Name', 'trim|required|regex_match[/[a-zA-Z]/]');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_updateEmail');
            $this->form_validation->set_rules('contact', 'Contact No.', 'trim|required|callback_updateContact|regex_match[/^[0-9]{10}$/]|min_length[10]');
            $this->form_validation->set_rules('pid[]', 'Project Name', 'trim|required|numeric');
            
            if ($this->form_validation->run() == TRUE) {
                $status = $this->input->post('status');
                
                $pluginids = $this->input->post('plugin');
                $projectids = $this->input->post('pid');
                $company_id = $this->input->post('company_id') ? $this->input->post('company_id') : $this->session->userdata('company_id');

                $data = array(
                    'user_name' => $this->input->post('name'),
                    'user_last_name' => $this->input->post('lname'),
                    'user_email' => $this->input->post('email'),
                    'user_contact' => $this->input->post('contact'),
                    'company_id' => $company_id,
                    'portal_access' => $this->input->post('access'),
                    'status' => $status,
                );
                
                 

                $foreman_id = $this->foreman->update('user', array('user_id' => $id), $data);
                if ($foreman_id) {
                    $this->foreman->delete('user_project', 'user_id', $id);
                    foreach ($projectids as $projectid) {
                        $this->foreman->save('user_project', array('project_id' => $projectid, 'user_id' => $id, 'status' => 1));
                    }

                    $this->foreman->delete('plugin_assign', 'user_id', $id);
                    //For Assign Plug In
                    foreach ($pluginids  as $pluginid) {
                        $this->foreman->save('plugin_assign', array('plugin_id' => $pluginid,'company_id'=>$company_id, 'user_id' => $id, 'status' => 1));
                    }
                    $this->session->set_flashdata('success', 'Supervisor Updated Successfully. ');
                    redirect(base_url('admin/foreman'));
               } else {
                    $this->session->set_flashdata('error', 'Failed To Update Supervisor');
                    redirect(base_url('admin/foreman/editForeman/' . $id));
                }
            }
        }

        $data = $this->data;
        $data['users'] = $this->foreman->get_all_managers('user');
        $result = $this->foreman->get_by_id($id);
        $data['result'] = $result;

        $data['foreman_project'] = $this->foreman->get_where('user_project', 'user_id', $id);
        $data['plugin_assign'] = $this->foreman->get_where('plugin_assign', 'user_id', $id);
        
        $data['foreman_project_ids'] = array();
        if (!empty($data['foreman_project'])) {
            foreach ($data['foreman_project'] as $value) {
                $data['foreman_project_ids'][] = $value->project_id;
            }
        }
        $data['plugins'] = $this->foreman->get_all_plugin($company_id);

        $data['plugin_assign_ids'] = array();
        if (!empty($data['plugin_assign'])) {
            foreach ($data['plugin_assign'] as $value) {
                $data['plugin_assign_ids'][] = $value->plugin_id;
            }
        }

        $data['title'] = 'Edit Supervisor';
        $data['fileError'] = $fileError;
        $data['description'] = 'Edit Supervisor Description';
        $data['page'] = 'foreman/edit_foreman';
        $data['projects'] = $this->project->getProjectDetailsByCompanyId($result->company_id);
        $data['companies'] = $this->company->get_datatables();
        $this->load->view('includes/template', $data);
    }

    public function ajax_delete($id) {
        //For Delete Plugins
        $this->foreman->delete('plugin_assign', 'user_id', $id);
        $this->foreman->delete('user', 'user_id', $id);
        $this->session->set_flashdata('success', 'Data Deleted Successfully');
        echo json_encode(array("status" => TRUE));
    }

    /*
     * Change data status 
     */

    public function ajax_change_status($id) {
        $result = $this->foreman->get_status('user', $id);
       
        if ($result->status == "1") {
            $project_id = $this->foreman->update('user', array('user_id' => $id), array('status' => 0));

            $subject = "Account Deactivation Notification";
            $content = "<p>Dear " . ucwords($result->user_name) . ",</p>";

            $content .= "<p>We regret to to inform to you that your account ";
            $content .= "has been automatically disabled from our system due to some problems.</p>";

            $content .= "<p>Kindly contact our customer care operators at +91 8369516308 ";
            $content .= "or write to us at care@aasaan.co</p>";

            $content .= "<p>With your assistance,our team will surely help you to enable your systems again.</p>";

            $content .= "<p>Thanking you in anticipation.</p>";
            $content .= "<p>Regards,</p>";
            $content .= "<p>Team Aasaan</p>";
            $content .= "<p>http://www.aasaan.co/hajiri</p>";
            $content .= "<img src='" . base_url('assets/admin/images/AASAAN-LOGO.png') . "' height='80' width='250'/>";
        } else {
            $project_id = $this->foreman->update('user', array('user_id' => $id), array('status' => 1));

            $subject = "Account Activation Notification";

            $content = "<p>Dear " . ucwords($result->user_name) . ",</p>";
            $content .= "<p>We feel glad to inform you that your account ";
            $content .= "has been enabled!</p>";

            $content .= "<p>For any inconvenience do call our customer care services at +91 8369516308 ";
            $content .= "or write to us at care@aasaan.co</p>";

            $content .= "<p>Thanking you in anticipation.</p>";
            $content .= "<p>Regards,</p>";
            $content .= "<p>Team Aasaan</p>";
            $content .= "<p>http://www.aasaan.co/hajiri</p>";
            $content .= "<img src='" . base_url('assets/admin/images/AASAAN-LOGO.png') . "' height='80' width='250'/>";
        }
        $this->session->set_flashdata('success', 'Status Changed Successfully');
        echo json_encode(array("status" => TRUE));
        //$result = $this->htmlmail($result->user_email,$result->user_name,$subject, $content);
       // exit();
        
        
    }

    public function htmlmail($email,$name,$subject,$content){
        $this->load->library("PHPMailer_Library");
        $mail = $this->phpmailer_library->load();
        $mail->IsSMTP();                              // send via SMTP
        $mail->Host = "ssl://smtp.zoho.com";
        $mail->SMTPAuth = true;                       // turn on SMTP authentication
        $mail->Username = "hajiri@aasaan.co";        // SMTP username
        $mail->Password = "hajiriaasaan"; // SMTP password
        $webmaster_email = "hajiri@aasaan.co";       //Reply to this email ID
        $email="care@aasaan.co";                // Recipients email ID
        $name="Hajiri App";                              // Recipient's name
        $mail->From = $webmaster_email;
        $mail->Port = 465;
        $mail->FromName = "Hajiri App";
        $mail->AddAddress($email,$name);
        $mail->AddReplyTo($webmaster_email,"Hajiri App");
        $mail->WordWrap = 50;                         // set word wrap
        $mail->IsHTML(true);                          // send as HTML
        $mail->Subject = $subject;
        $mail->Body = $content;   
       // $mail->Send();
       if(!$mail->Send())
         {
            echo "Eroor in send";
         }

    }
    
    public function ajax_email_check() {
        $email = (isset($_POST['email']) && !empty($_POST['email'])) ? $_POST['email'] : '';
        $result = $this->foreman->checkUniqueEmail('user', array('user_email' => $email));
        echo json_encode($result->count);
    }

    public function ajax_number_check() {
        $email = (isset($_POST['email']) && !empty($_POST['email'])) ? $_POST['email'] : '';
        $result = $this->foreman->checkUniqueEmail('user', array('user_contact' => $email));
        echo json_encode($result->count);
    }

    public function ajax_email_check_onupdate() {
        $email = (isset($_POST['email']) && !empty($_POST['email'])) ? $_POST['email'] : '';
        $userid = (isset($_POST['userid']) && !empty($_POST['userid'])) ? $_POST['userid'] : '';
        $where = array(
            'user_email' => $email,
            'user_id != ' => $userid,
        );
        $result = $this->foreman->checkUniqueEmailUpdate('user', $where);
        echo json_encode($result->count);
    }

    public function ajax_number_check_onupdate() {
        $email = (isset($_POST['email']) && !empty($_POST['email'])) ? $_POST['email'] : '';
        $userid = (isset($_POST['userid']) && !empty($_POST['userid'])) ? $_POST['userid'] : '';
        $where = array(
            'user_contact' => $email,
            'user_id != ' => $userid,
        );
        $result = $this->foreman->checkUniqueEmailUpdate('user', $where);
        echo json_encode($result->count);
    }

    public function ajax_get_projectList($id) {
        $organization_name = $prjlist = "";
        $projects = $this->foreman->get_projectDetails($id);
        if (!empty($projects)) {
            $organization_name = $projects[0]->ORG_NAME;
            foreach ($projects as $project) {
                $prjlist .= "<label><input type='checkbox' name ='pid[]' value= '" . $project->project_id . "'>" . $project->project_name . "</label>";
            }
        } else {
            $prjlist .= "No projects found.";
        }
        $result['projectlist'] = $prjlist;
        $result['organization'] = $organization_name;
        echo json_encode($result);
    }


    public function ajax_get_selected_projectList() {
        $organization_name = $prjlist = "";
        $user_id = $this->input->post('user_id');
        $company_id = $this->input->post('company_id');
        $projects = $this->foreman->get_projectDetails($company_id);
        $projectAssigned = $this->foreman->get_where('user_project','user_id',$user_id);
        $project_assign_ids = array();
        if (!empty($projectAssigned)) {
            foreach ($projectAssigned as $value) {
                $project_assign_ids[] = $value->project_id;
            }
            foreach ($projects as $project) {
                $prjlist .= "<label><input type='checkbox' name ='pid[]' " . (in_array($project->project_id, $project_assign_ids) ? "checked" : "") . " value='" . $project->project_id . "'>" . $project->project_name . "</label>";
            }
        } else {
            foreach ($projects as $project) {
                $prjlist .= "<label><input type='checkbox' name ='pid[]' value= '" . $project->project_id . "'>" . $project->project_name . "</label>";
            }
        }
        $result[] = $prjlist;
        echo json_encode($result);
    }

    public function ajax_get_pluginList($id) {
        $organization_name = $prjlist = "";
        $plugin = $this->foreman->get_all_plugin($id);
        if (!empty($plugin)) {
            foreach ($plugin as $plugin) {
                $prjlist .= "<label><input type='checkbox' name='plugin[]' id='plugCheckBox".$plugin->plugin_id."' value= '" . $plugin->plugin_id . "' >" . $plugin->plugin_name . "</label>";
            }
        } else {
            $prjlist .= "No plugins assigned to your company.";
        }
        $result['pluginList'] = $prjlist;
        echo json_encode($result);
    }

    public function ajax_get_selected_pluginList() {
        $prjlist = "";
        $user_id = $this->input->post('user_id');
        $company_id = $this->input->post('company_id');
        $plugin = $this->foreman->get_all_plugin($company_id);
        $pluginAssigned = $this->foreman->get_where('plugin_assign','user_id',$user_id);
        $plugin_assign_ids = array();
        if (!empty($pluginAssigned)) {
            foreach ($pluginAssigned as $value) {
                $plugin_assign_ids[] = $value->plugin_id;
            }
            foreach ($plugin as $plugin) {
                $prjlist .= "<label><input type='checkbox' name ='plugin[]' " . (in_array($plugin->plugin_id, $plugin_assign_ids) ? "checked" : "") . " value='" . $plugin->plugin_id . "'>" . $plugin->plugin_name . "</label>";
            }            
        } else {
            foreach ($plugin as $plugin) {
                $prjlist .= "<label><input type='checkbox' name ='plugin[]'  value='" . $plugin->plugin_id . "'>" . $plugin->plugin_name . "</label>";
            }
        }
		$prjlist .= '</select>';
        $result[] = $prjlist;
        echo json_encode($result);
    }

}
?>