<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Manager extends CI_Controller {

    public $data;
	public function __construct() {
        parent::__construct();
		$this->data['menu_title'] = 'Manager';
        $this->load->model('admin_model', 'admin');
        $this->load->model('manager_model', 'manager');
		$this->load->model('Companies_model', 'company');
		$this->load->model('foreman_model', 'foreman');
        $this->load->model('project_model', 'project');
		$this->load->model('plan_model', 'plan');
        checkAdmin();
    }

    public function index() {
		$data = $this->data;
        $data['managers'] = $this->manager->get_datatables();
        $data['title'] = 'Manager List';
        $data['description'] = 'All manager list';
        $data['page'] = 'manager/list_manager';
        if($this->session->userdata('user_designation') == 'Superadmin'){
            $data['planId'] = (object) array('id' =>  '3');
            $data['limit']  = (object) array('wLimit' =>  '1');
        }else{
            $data['planId'] = $this->plan->get_PlanId($this->session->userdata('company_id'));
            $data['limit'] = $this->plan->get_limit('pp.no_of_admin - count(w.company_id) AS wLimit',$this->session->userdata('company_id'),'user','w. status != 0 AND w. user_designation = "admin"');
        }
        $this->load->view('includes/template', $data);
    }

    public function addManager() {
       
		$fileError = array();
        if (isset($_POST['submit'])) {
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('name', 'First Name', 'trim|required|regex_match[/[a-zA-Z]/]');
            $this->form_validation->set_rules('lname', 'Last Name', 'trim|regex_match[/[a-zA-Z]/]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[user.user_email]|callback_updateEmail');
            $this->form_validation->set_rules('contact', 'Contact No.', 'trim|required|is_unique[user.user_contact]|regex_match[/^[0-9]{10}$/]|min_length[10]');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[36]');
            $this->form_validation->set_rules('conf_password', 'Confirm Password', 'trim|required|matches[password]');
			$this->form_validation->set_rules('pid[]', 'Projects', 'trim|required');
            //$this->form_validation->set_rules('organization_name', 'Organization Name', 'trim|required');
            if ($this->form_validation->run() == TRUE) {
				$company_id = $this->input->post('company_id');
                $data = array(
					'company_id' => $company_id,
                    'user_name' => $this->input->post('name'),
                    'user_last_name' => $this->input->post('lname'),
                    'user_email' => $this->input->post('email'),
                    'password' => md5( $this->input->post('password') ),
                    'user_contact' => $this->input->post('contact'),
                    'user_designation' => 'admin',
                    'portal_access' => $this->input->post('access'),
                    'status' => $this->input->post('status'),
                );
                $associatedFileNames = array('image');
                foreach ($associatedFileNames as $fileName) {
                    if (!empty($_FILES[$fileName]['name'])) {

                        $result = uploadStaffFile('uploads/user/', $fileName);
                        if ($result['flag'] == 1) {
                            $data['user_profile_image'] = $result['filePath'];
                        } else {
                            $fileError[$fileName] = $result['error'];
                        }
                    }
                }
                $manager_id = $this->manager->save('user', $data);
                if ($manager_id) {
					
					$projectids = $this->input->post('pid');
					foreach ($projectids as $projectid) {
                        $this->manager->save('user_project', array('project_id' => $projectid, 'user_id' => $manager_id, 'status' => 1));

                    }
					
					$pluginids = $this->input->post('plugin');
					//For Assign Plug In
					foreach ($pluginids  as $pluginid) {
						$this->manager->save('plugin_assign', array('plugin_id' => $pluginid,'company_id'=>$company_id, 'user_id' => $manager_id, 'status' => 1));
					}					
					
                    $subject = "Hajiri app account registration";
                    $content = "<p>Dear " . ucwords($this->input->post('name')).",</p>";

                    $content .= "<p>Congratulations, you have been successfullly ";
                    $content .= "registered for The Hajiri app.Below are the details:</p>";

                    //$content .= "<p>Organization name: " . $this->input->post('organization_name') . "</p>";
                    
                    $content .= "<p>Keep connected,we surely have a lot more features coming up ";
                    $content .= "that will simplify your business processes.Do rate us on Google Playstore <a href='https://play.google.com/store/apps/details?id=com.hajiri.aasaan'>Hajiri app</a></p>";
                    
                    $content .= "<p>Thanking you in anticipation.</p>";
                    $content .= "<p>Regards,</p>";
                    $content .= "<p>Team Aasaan</p>";
                    $content .= "<p>http://www.aasaan.co/hajiri</p>";
                    $content .= "<img src='" . base_url('assets/admin/images/AASAAN-LOGO.png') . "' height='80' width='250'/>";

                    $this->session->set_flashdata('success', 'Admin Added Successfully. ');
					
					redirect(base_url('admin/manager'));
                } else {
                    $this->session->set_flashdata('error', 'Failed To Add Manager');
                    redirect(base_url('admin/manager/addManager'));
                }
            }
        }
        $data = $this->data;
		$data['title'] = 'Add Manager';
        $data['fileError'] = $fileError;
        if($this->session->userdata('user_designation') == 'Superadmin'){
            $data['planId'] = (object) array('id' =>  '3');
            $data['limit']  = (object) array('wLimit' =>  '1');
        }else{
            $data['planId'] = $this->plan->get_PlanId($this->session->userdata('company_id'));
            $data['limit'] = $this->plan->get_limit('pp.no_of_admin - count(w.company_id) AS wLimit',$this->session->userdata('company_id'),'user','w. status != 0 AND w. user_designation = "admin"');
        }
        $data['description'] = 'Add Manager';
        $data['page'] = 'manager/add_manager';
		$data['companies'] = $this->company->get_datatables();
        $data['projects'] = $this->project->get_datatables();
        $this->load->view('includes/template', $data);
    }


    public function editManager($id) {
        $fileError = array();
        $company_id = $this->session->userdata('company_id');
		
        if (isset($_POST['submit'])) {
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('name', 'First Name', 'trim|required|regex_match[/[a-zA-Z]/]');
            $this->form_validation->set_rules('lname', 'Last Name', 'trim|regex_match[/[a-zA-Z]/]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_updateEmail');
            $this->form_validation->set_rules('pid[]', 'Projects', 'trim|required');
            $this->form_validation->set_rules('contact', 'Contact No.', 'trim|required|callback_updateContact|regex_match[/^[0-9]{10}$/]|min_length[10]');
            //$this->form_validation->set_rules('organization_name', 'Organization Name', 'trim|required');
            
			if ($this->form_validation->run() == TRUE) {
				$company_id = $this->input->post('company_id');
                  $status = $this->input->post('status');
				$data = array(
					'company_id' => $company_id,
                    'user_name' => $this->input->post('name'),
                    'user_last_name' => $this->input->post('lname'),
                    'user_email' => $this->input->post('email'),
                    'user_contact' => $this->input->post('contact'),
                    'user_designation' => 'admin',
                    'portal_access' => $this->input->post('access'),
                    'status' => $status,
                );
                $associatedFileNames = array('image');
                foreach ($associatedFileNames as $fileName) {
                    if (!empty($_FILES[$fileName]['name'])) {

                        $result = uploadStaffFile('uploads/user/', $fileName);
                        if ($result['flag'] == 1) {
                            $data['user_profile_image'] = $result['filePath'];
                        } else {
                            $fileError[$fileName] = $result['error'];
                        }
                    }
                }
                $manager_id = $this->manager->update('user', array('user_id' => $id), $data);
                if ($manager_id) {					
					$this->manager->delete('user_project', 'user_id', $id);
					$projectids = $this->input->post('pid');
					foreach ($projectids as $projectid) {
                        $this->manager->save('user_project', array('project_id' => $projectid, 'user_id' => $id, 'status' => 1));
                    }					
					$this->manager->delete('plugin_assign', 'user_id', $id);
					$pluginids = $this->input->post('plugin');
					//For Assign Plug In
					foreach ($pluginids  as $pluginid) {
						$this->manager->save('plugin_assign', array('plugin_id' => $pluginid,'company_id'=>$company_id, 'user_id' => $id, 'status' => 1));
					}				
                    $this->session->set_flashdata('success', 'Manager Updated Successfully. ');
                    redirect(base_url('admin/manager'));
                } else {
                    $this->session->set_flashdata('error', 'Failed To Update Manager');
                    redirect(base_url('admin/manager/editManager/' . $id));
                }
            }
        }
		$data = $this->data;
        $result = $this->manager->get_by_id($id);
        $data['result'] = $result;
        $data['title'] = 'Edit Manager';
        $data['fileError'] = $fileError;
        $data['description'] = 'Edit Manager Description';
        $data['page'] = 'manager/edit_manager';
		$data['companies'] = $this->company->get_datatables();
		$data['manager_project_ids'] = array();
		$data['projects'] = $this->project->getProjectDetailsByCompanyId($result->company_id);
		$data['manager_project'] = $this->foreman->get_where('user_project', 'user_id', $id);
        if (!empty($data['manager_project'])) {
            foreach ($data['manager_project'] as $value) {
                $data['manager_project_ids'][] = $value->project_id;
            }
        }
		$data['plugins'] = $this->foreman->get_all_plugin($company_id);
		$data['plugin_assign'] = $this->foreman->get_where('plugin_assign', 'user_id', $id);

        $data['plugin_assign_ids'] = array();
        if (!empty($data['plugin_assign'])) {
            foreach ($data['plugin_assign'] as $value) {
                $data['plugin_assign_ids'][] = $value->plugin_id;
            }
        }
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

    public function ajax_delete($id) {
        $this->manager->delete('user', 'id', $id);
        $this->session->set_flashdata('success', 'Data Deleted Successfully');
        echo json_encode(array("status" => TRUE));
    }

    /*
     * Change data status 
     */

    public function ajax_change_status($id) {
        $result = $this->manager->get_status('user', $id);
        if ($result->status == "1") {
            $project_id = $this->manager->update('user', array('user_id' => $id), array('status' => 0));
            
            $subject = "Account Deactivation Notification";
            $content = "<p>Dear " . ucwords($result->name) . ",</p>";

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
            $project_id = $this->manager->update('user', array('user_id' => $id), array('status' => 1));
            
            $subject = "Account Activation Notification";

            $content = "<p>Dear " . ucwords($result->name) . ",</p>";
            $content .= "<p>We feel glad to inform you that  your account ";
            $content .= "has been enabled!</p>";

            $content .= "<p>For any inconvenience do call our customer care services at +91 8369516308 ";
            $content .= "or write to us at care@aasaan.co</p>";

            $content .= "<p>Thanking you in anticipation.</p>";
            $content .= "<p>Regards,</p>";
            $content .= "<p>Team Aasaan</p>";
            $content .= "<p>http://www.aasaan.co/hajiri</p>";
            $content .= "<img src='" . base_url('assets/admin/images/AASAAN-LOGO.png') . "' height='80' width='250'/>";
        }
         //$result = htmlmail($result->email, $subject, $content);
        $this->session->set_flashdata('success', 'Status Changed Successfully');
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_delete_manager($id) {
        $manager_id = $this->manager->update('user', array('user_id' => $id), array('status' => 0));
        if($manager_id){
            $this->session->set_flashdata('success', 'User deleted Successfully');
            echo json_encode(array("status" => TRUE));
        }else{
            $this->session->set_flashdata('danger', 'Failed to delete user');
            echo json_encode(array("status" => TRUE));
        }
    }

}
