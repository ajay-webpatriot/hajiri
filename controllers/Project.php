<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends CI_Controller {

    public $data;
	public function __construct() {
        parent::__construct();
		$this->data['menu_title'] = 'Project';
        $this->load->model('project_model', 'project');
        $this->load->model('Companies_model', 'company');
        $this->load->model('plan_model', 'plan');
        $this->load->model('foreman_model', 'foreman');
        $this->load->library("PHPMailer_Library");
		$this->load->model('manager_model', 'manager');
        checkAdmin();
    }

    /*
     * Display list in table
     */

    public function index() {
        $this->breadcrumbcomponent->add('Dashboard', base_url('/admin'));
        $this->breadcrumbcomponent->add('All Project', base_url('/admin/project'));

        if (isset($_POST['submit'])) {
            $user_email = null;
            $this->form_validation->set_rules('projectName', 'Project name', 'trim|required');
            
            if ($this->form_validation->run() == TRUE) {
                $company_id = $this->input->post('company_id') ? $this->input->post('company_id') : $this->session->userdata('company_id');
                $data = array(
                    'project_name' => $this->input->post('projectName'),
                    'man_hr_day' => 10,
                    'company_id' => $company_id,
                    'project_start_date' => NULL,
                    'project_end_date' => NULL,
                    'no_of_months' => 0,
                    'status' => 1
                );

                $project_id = $this->project->save('project', $data);

                if ($project_id) {
                    //For Adding in user project table
                    $this->project->save('user_project', array('project_id' => $project_id, 'user_id' =>$this->session->userdata('id'), 'status' => 1));
                    if($this->session->userdata('user_designation') != 'Superadmin'){
                        $user_email = $this->project->get_mail_byid('user', $this->session->userdata('id'));
                    }else{
                        $where = array('user_designation' => 'admin', 'status' => '1', 'company_id' => $company_id );
                        $adminEmail = $this->manager->get_where('user_email, user_name, user_last_name ', $where);
                    }
                   
                    $company_name = $this->company->companyName($company_id);
                    
                    $subject = "The Hajiri app is now active for ".$this->input->post('projectName');
                    $content = "<p>Congratulations, The Hajiri app is now active for your project ".$this->input->post('projectName')." </p> ";
                    $content .= "<p>You can now assign supervisors, add workers in bulk and control multiple projects by logging into www.hajiri.co </p> ";
                    $content .= "<p>Hoping to deliver our best services to ".$company_name->company_name."</p>";
                    
                    $content .= "<p></p>";
                    $content .= "<p>Regards,</p>";
                    $content .= "<p>Team Aasaan</p>";
                    $content .= "<p>http://www.aasaan.co</p>";
                    $content .= "<img src='" . base_url('assets/admin/images/aasaan-footer-logo.jpg') . "' height='80' width='250'/>";
                
                    if($this->session->userdata('user_designation') != 'Superadmin'){
                        $result= 0;
                        $result = htmlmail($user_email->user_email,$this->session->userdata('name'), $subject, $content);
                    }else{
                        $result= 0;
                        foreach ($adminEmail as $value) {
                            $result = htmlmail($value->user_email,$value->user_name.' '.$value->user_last_name, $subject, $content);
                        }
                    }
                    if ($result == 1) {
                        $this->session->set_flashdata('success', 'Project Added Successfully');
                    } else {
                        $this->session->set_flashdata("error", "Error in sending Email.");
                    }
                    redirect(base_url('admin/project'));
                } else {
                    $this->session->set_flashdata('error', 'Failed To Add Project.');
                    redirect(base_url('admin/project/'));
                }
            }
        }

        if (isset($_POST['edit'])) {
            $this->form_validation->set_rules('projectName', 'Project name', 'trim|required');
            
            if ($this->form_validation->run() == TRUE) {
                  $status = $this->input->post('status');
                $data = array(
                    'project_name' => $this->input->post('projectName'),
                    'status' => $status,
                );
                $where = "project_id = ".$this->input->post('project_id');
                $cat_id = $this->project->update('project',$where ,$data);
                if ($cat_id) {

                    $this->session->set_flashdata('success', 'Project Updated Successfully! ');
                    redirect(base_url('admin/project'));
                   
                } else {
                    $this->session->set_flashdata('error', 'Failed To Updat Project.');
                    redirect(base_url('admin/project'));
                }
            }
        }
		
		$data = $this->data;
        $data['breadcrumb'] = $this->breadcrumbcomponent->output();
        $data['projects'] = $this->project->get_datatables();
        if( $this->session->userdata('user_designation') == 'Superadmin' ){
            $data['companies'] = $this->company->get_datatables();
        }
        $data['title'] = 'Project List';
        $data['description'] = 'All project list';
        if($this->session->userdata('user_designation') == 'Superadmin'){
            $data['planId'] = (object) array('id' =>  '3');
            $data['limit']  = (object) array('wLimit' =>  '1');
        }else{
            $data['planId'] = $this->plan->get_PlanId($this->session->userdata('company_id'));
            $data['limit'] = $this->plan->get_limit('pp.no_of_project - count(w.company_id) AS wLimit',$this->session->userdata('company_id'),'project','w. status != 0');
        }
        $data['page'] = 'project/list_project';
       // print_r($data);
        $this->load->view('includes/template', $data);
    }

    /*
     * Soft delete data from list
     */

    public function ajax_delete($id) {
        $this->project->delete('project', 'project_id', $id);
        $this->session->set_flashdata('success', 'Data Deleted Successfully');
        echo json_encode(array("status" => TRUE));
    }


    public function ajax_project_check() {
        $proj = (isset($_POST['project']) && !empty($_POST['project'])) ? $_POST['project'] : '';
        $result = $this->foreman->checkUniqueEmail('project', array('project_name' => $proj, 'status' => 1, 'company_id' => $this->session->userdata('company_id') ));
        echo json_encode($result->count);
    }
    /*
     * Change data status 
     */

    public function ajax_change_status($id) {
        $result = $this->project->get_project_details('project', 'project_id', $id);
       
        if ($result->status == "1") {

            $project_id = $this->project->update('project', array('project_id' => $id), array('status' => 2));
             $this->session->set_flashdata('success', 'Status Changed Successfully');
              echo json_encode(array("status" => TRUE));
             exit();
            $subject = "Project Deactivation Notification";
            $content = "<p>Dear " . ucwords($result->project_name).",</p>";
            $content .= "<p>We regret to to inform to you that your project, " . $result->project_name;
            $content .= " has been automatically disabled from our system due to some problems.</p>";

            $content .= "<p>Kindly contact our customer care operators at +91 8369516308 ";
            $content .= "or write to us at care@aasaan.co</p>";

            $content .= "<p>With your assistance,our team will surely help you to enable your systems again.</p>";

            $content .= "<p>Thanking you in anticipation.</p>";
            $content .= "<p>Regards,</p>";
            $content .= "<p>Team Aasaan</p>";
            $content .= "<p>http://www.aasaan.co/hajiri</p>";
            $content .= "<img src='" . base_url('assets/admin/images/AASAAN-LOGO.png') . "' height='80' width='250'/>";
        } else {

          
            $project_id = $this->project->update('project', array('project_id' => $id), array('status' => 1));

             $this->session->set_flashdata('success', 'Status Changed Successfully');
              echo json_encode(array("status" => TRUE));
             exit();
            $subject = "Project Activation Notification";

            $content = "<p>Dear " . ucwords($result->project_name).",</p>";
            $content .= "<p>We feel glad to inform you that your project, " . $result->project_name;
            $content .= " has been enabled!</p>";

            $content .= "<p>For any inconvenience do call our customer care services at +91 8369516308 ";
            $content .= "or write to us at care@aasaan.co</p>";

            $content .= "<p>Thanking you in anticipation.</p>";
            $content .= "<p>Regards,</p>";
            $content .= "<p>Team Aasaan</p>";
            $content .= "<p>http://www.aasaan.co/hajiri</p>";
            $content .= "<img src='" . base_url('assets/admin/images/AASAAN-LOGO.png') . "' height='80' width='250'/>";
        }
		
		$this->session->set_flashdata('success', 'Status Changed Successfully');
        /*$result = htmlmail($result->project_name, $subject, $content);
        if ($result) {
            $this->session->set_flashdata('success', 'Status Changed Successfully');
        } else {
            $this->session->set_flashdata("error", "Error in sending Email.");
        }*/
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_delete_project($id) {
        $project_id = $this->project->update('project', array('project_id' => $id), array('status' => 0));
        if($project_id){
            $this->session->set_flashdata('success', 'Project delete Successful.');
            echo json_encode(array("status" => TRUE));
            exit();
        }else{
            $this->session->set_flashdata('danger', 'Failed to delete project.');
            echo json_encode(array("status" => False));
            exit();
        }
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
        $email=$email;                // Recipients email ID
        $name=$name;                              // Recipient's name
        $mail->From = $webmaster_email;
        $mail->Port = 465;
        $mail->FromName = "The Hajiri App";
        $mail->AddAddress($email,$name);
        $mail->AddReplyTo($webmaster_email,"The Hajiri App");
        $mail->WordWrap = 50;                         // set word wrap
        $mail->IsHTML(true);                          // send as HTML
        $mail->Subject = $subject;
        $mail->Body = $content;     
        $sent = $mail->Send();
		if(!$sent){
			return 0;
		}else{
			return 1;
		}
    }

}
