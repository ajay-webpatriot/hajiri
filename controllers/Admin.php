<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MX_Controller {

    public $data;
	public function __construct() {
        parent::__construct();
		$this->data['menu_title'] = 'Admin';
        $this->load->library('breadcrumbcomponent');
        $this->load->model('admin_model', 'admin');
        $this->load->model('foreman_model', 'foreman');
        $this->load->model('manager_model', 'manager');
        $this->load->library("PHPMailer_Library");
    }

    public function index() {
        checkAdmin();
        date_default_timezone_set('Asia/Kolkata');

        $this->breadcrumbcomponent->add('Website', base_url('/'));
        $this->breadcrumbcomponent->add('Dashboard', base_url('/admin'));
		$data = $this->data;
		
        $data['breadcrumb'] = $this->breadcrumbcomponent->output();
        $role = $this->session->userdata('user_designation');
		
        if ( $role == 'superadmin' ){
            $data['companies'] = $this->admin->getCount( 'company', ['status' => 1] );
    		$data['active_managers'] = $this->admin->getCount( 'user', ['user_designation' => 'admin', 'status' => 1] );
    		$data['inactive_managers'] = $this->admin->getCount( 'user', ['user_designation' => 'admin', 'status' => 0] );
    		$data['supervisors'] = $this->admin->getCount( 'user', ['user_designation' => 'Supervisor'] );
        }else{
            $company_id =  $this->session->userdata('company_id');

            $data['todaysStrength'] = $this->admin->count_all_strength('attendance');
            $data['todayAttendancePW'] = $this->admin->todayap();
            $data['todayAttendanceCategorywise'] = $this->admin->todayac();
            $data['averageAttendance'] = $this->admin->average_attendance('attendance');
            $data['attendanceTillDate'] = $this->admin->total('count(hajiri) AS total',$company_id,'attendance');
            $data['attendanceProjectWise'] = $this->admin->total_wise('count(a.hajiri) AS total, p.project_name AS pName','project p','p.project_id = a.project_id',$company_id,'attendance');
            $data['workerExpense'] = $this->admin->totalExpense('sum(worker_total_wage) AS total',$company_id,'worker_wage');
            $data['wExpProj'] = $this->admin->total_expense_cat_wise('sum(ww.worker_total_wage) AS total, cat.category_name AS pName',$company_id);
            $data['totalDue'] = $this->admin->totalExpense('sum(worker_due_wage) AS total',$company_id,'worker_wage');
            $data['totalDuePW'] = $this->admin->total_expense_cat_wise('sum(ww.worker_due_wage) AS total, cat.category_name AS pName',$company_id);
            $data['todaysCost'] = $this->admin->todays_cost('sum(a.amount) AS total',$company_id,'attendance');
            $data['todayscostPW'] = $this->admin->totalCost_wise('sum(a.amount) AS total, p.project_name AS pName','project p','p.project_id = a.project_id',$company_id,'attendance');
            $data['avgAttendanceProjectwise'] = $this->admin->avgap($company_id);
        }

        $data['title'] = 'dashboard';
        $data['description'] = 'Main dashboard';
        $data['page'] = 'admin/dashboard';
        $this->load->view('includes/template', $data);
    }

    public function login() {
        if (isset($_POST['submit'])) {
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('username', 'Email id', 'trim|required');
            $this->form_validation->set_rules('password', 'Password', 'trim|required');

            if ($this->form_validation->run() == TRUE) {

                $email = $this->input->post('username');
                $password = $this->input->post('password');

                $result = $this->admin->getLogin("user", $email, $password);               
                
                if (!empty($result)) {
                    if($result->user_designation != 'Superadmin'){
                        $planId = $this->admin->planId('company_plan', 'company_id', $result->company_id);
                        if($planId->plan_id != 1){
                           if($planId->plan_id == 2 && strtotime($planId->due_date) > time()){
                                $company = $this->admin->get_company_name($result->company_id);

                                $plugin = $this->foreman->get_where('plugin_assign', 'user_id', $result->user_id);
                                foreach ($plugin as $key) {
                                    $permissions[] = $key->plugin_id;
                                }

                                $data = array(
                                    'id' => $result->user_id,
                                    'company_name' => $company->company_name,
                                    'company_logo' => $company->company_logo_image,
                                    'company_id' => $result->company_id,
                                    'name' => $result->user_name.' '.$result->user_last_name,
                                    'user_email' => $result->user_email,
                                    'is_user_login' => 1,
                                    'image' => $result->user_profile_image,
                                    'user_designation' => $result->user_designation,
                                    'permissions' => $permissions,
                                    'plan_id'   => $planId->plan_id,
                                    'due_date'   => $planId->due_date,
                                );

                                $this->session->set_userdata($data);
                                redirect(base_url('admin'));
                            }elseif ($planId->plan_id == 3) {
                                $company = $this->admin->get_company_name($result->company_id);

                                $plugin = $this->foreman->get_where('plugin_assign', 'user_id', $result->user_id);
                                foreach ($plugin as $key) {
                                    $permissions[] = $key->plugin_id;
                                }

                                $data = array(
                                    'id' => $result->user_id,
                                    'company_name' => $company->company_name,
                                    'company_logo' => $company->company_logo_image,
                                    'company_id' => $result->company_id,
                                    'name' => $result->user_name.' '.$result->user_last_name,
                                    'user_email' => $result->user_email,
                                    'is_user_login' => 1,
                                    'image' => $result->user_profile_image,
                                    'user_designation' => $result->user_designation,
                                    'permissions' => $permissions,
                                    'plan_id'   => $planId->plan_id,
                                    'due_date'   => $planId->due_date,
                                );

                                $this->session->set_userdata($data);
                                redirect(base_url('admin'));
                            } 
                            else {
                                $this->session->set_flashdata('error', 'Your account has expired.');
                            }
                        } else {
                            $this->session->set_flashdata('error', 'Access denied.');
                        }
                    }else{
                        $data = array(
                            'id' => $result->user_id,
                            'company_name' => $company->company_name,
                            'company_logo' => $company->company_logo_image,
                            'company_id' => $result->company_id,
                            'name' => $result->user_name.' '.$result->user_last_name,
                            'user_email' => $result->user_email,
                            'is_user_login' => 1,
                            'image' => $result->user_profile_image,
                            'user_designation' => $result->user_designation,
                        );

                        $this->session->set_userdata($data);
                        redirect(base_url('admin'));
                    }
                }else {
                    $this->session->set_flashdata('error', 'Invalid login details');
                }
            }
        }
        $data['title'] = 'login';
        $data['description'] = 'login';
        $this->load->view('admin/login', $data);
    }     

    public function profile() {
        $data['menu_title'] = '';
        
        $fileError = array();
        if (isset($_POST['submit'])) {
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('name', 'First Name', 'trim|required|regex_match[/[a-zA-Z]/]');
            $this->form_validation->set_rules('lname', 'Last Name', 'trim|regex_match[/[a-zA-Z]/]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('contact', 'Contact No.', 'trim|required|regex_match[/^[0-9]{10}$/]|min_length[10]');
            if ($this->form_validation->run() == true) {
                $data = array(
                    'user_name' => $this->input->post('name'),
                    'user_last_name' => $this->input->post('lname'),
                    'user_email' => $this->input->post('email'),
                    'user_contact' => $this->input->post('contact'),
                    'user_profile_image' => $this->input->post('profile_image'),
                ); 
                if (empty($fileError)) {
                    $id = $this->session->userdata('id');
                    if (checkUniqueUpdate('user', $id, $col = 'user_email', $this->input->post('email'))) {
                        $result = $this->admin->updateRecord('user', $id, 'user_id', $data);
                        if ($result) {
                            $this->session->set_flashdata('success', 'Profile Data Successfully Updated');
                            $data['menu_title'] = '';

                            if (!empty($this->input->post('profile_image'))) {
                                $this->session->set_userdata('image', $this->input->post('profile_image'));
                                //REMAINING*****************
                            }//if
							$name = $data['user_name'].' '.$data['user_last_name'];
                            $this->session->set_userdata('name', $name);
							redirect(base_url('admin'));
                        } else {
                            $data['menu_title'] = '';
                            $this->session->set_flashdata('error', ' Profile Data Not Updated');
                        }//else
                    } else {
                        //$fileError['email'] = 'Sorry, that email is already being used.';
                        $data['menu_title'] = '';
                        $this->session->set_flashdata('error', ' Email id already exist.');
                    }
                } else {
                    $data['menu_title'] = '';
                    $this->session->set_flashdata('error', 'Sorry, Profile Data Not Updated');
                }
            }
        }
        $this->breadcrumbcomponent->add('Dashboard', base_url('/admin'));
        $this->breadcrumbcomponent->add('Profile', base_url('/admin/profile'));
        $data['breadcrumb'] = $this->breadcrumbcomponent->output();
        $id = $this->session->userdata('id');
        $data['results'] = $this->admin->get_where_data_results($id);
        $data['fileError'] = $fileError;
        $data['title'] = 'Profile';
        $data['description'] = 'Profile Form';
        $data['page'] = 'admin/profile';
        $this->load->view('includes/template', $data);
    }

   public function companyprofile() {
        checkAdmin();
        $fileError = array();
        if (isset($_POST['submit'])) {
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('name', 'Name', 'trim|required|regex_match[/[a-zA-Z]/]');
            /* $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('contact', 'Contact No.', 'trim|required|regex_match[/^[0-9]{10}$/]|min_length[10]');*/
            if ($this->form_validation->run() == true) {
                $data = array(
                    'company_name' => $this->input->post('name'),
                    'company_type' => $this->input->post('company_type'),
                    'company_address' => $this->input->post('company_address'),

                    'company_pincode' => $this->input->post('company_pincode'),
                    'company_city' => $this->input->post('company_city'),
                    'company_state' => $this->input->post('company_state'),
                    'company_country' => $this->input->post('company_country'),
                    'company_email' => $this->input->post('company_country'),
                    'company_contact_no' => $this->input->post('company_contact_no'),
                    'company_website' => $this->input->post('company_website'),
                    'company_pan' => $this->input->post('company_pan'),
                    'status' => $this->input->post('status')
                );
                $associatedFileNames = array('company_logo_image');
                foreach ($associatedFileNames as $fileName) {
                    if (!empty($_FILES[$fileName]['name'])) {
                        $result = uploadStaffFile('uploads/user/', $fileName);
                        if ($result['flag'] == 1) {
                            $data[$fileName] = $result['filePath'];
                        } else {
                            $fileError[$fileName] = $result['error'];
                        }
                    }
                }
                if (empty($fileError)) {
                    $id = $this->session->userdata('company_id');
                    $result = $this->admin->updateRecord('company', $id, 'compnay_id', $data);
                    if ($result) {
                        $this->session->set_flashdata('success', 'Company Data Successfully Updated');
                        if (!empty($_FILES[$fileName]['name'])) {
                            $this->session->set_userdata('image', $data['company_logo_image']);
                            //REMAINING*****************
                        }//if
                        $this->session->set_userdata('name', $data['company_name']);
                    } else {
                        $this->session->set_flashdata('error', 'Sorry,  Data Not Updated');
                    }
                }
            }
        }

        $this->breadcrumbcomponent->add('Dashboard', base_url('/admin'));
        $this->breadcrumbcomponent->add('Profile', base_url('/admin/profile'));
        $data['breadcrumb'] = $this->breadcrumbcomponent->output();
        $id = $this->session->userdata('id');
        $data['results'] = $this->admin->get_company_detl($id);
        $data['fileError'] = $fileError;
        $data['title'] = 'Company Profile';
        $data['description'] = 'Company Profile Form';
        $data['page'] = 'admin/companyprofile';
        $this->load->view('includes/template', $data);
    }


    public function changePassword() {
        checkAdmin();
        $data['menu_title'] = '';
        $custom_error = '';
        $id = $this->session->userdata('id');
        if (isset($_POST['changepassword'])) {
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('oldpassword', 'Old Password', 'required');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[36]|matches[conf_password]');
            $this->form_validation->set_rules('conf_password', 'Confirm Password', 'required');
            if ($this->form_validation->run() == TRUE) {
                $result = $this->admin->getwhere("user", "user_id", $id);
                extract($_POST);
                if (md5( $oldpassword ) == $result->password) {
                    $newdata = array(
                        'password' => md5( $password),
                    );
                    $newresult = $this->admin->updateRecord("user", $id, 'user_id', $newdata);
                    if ($newresult) {
                        $this->session->set_flashdata('successMsg', 'Password Updated Successfully.');
                    } else {
                        $this->session->set_flashdata('errorMsg', 'Password Not Updated.');
                    }
                } else {
                    $custom_error = 'Old passsword doesn\'t match with database password';
                    $this->session->set_flashdata('warningMsg', 'Old Password doesn\'t match with database password.');
                }
            }
        }
        $data['custom_error'] = $custom_error;
        $data['title'] = 'Change Password';
        $data['description'] = 'Customer Change Password';
        $data['page'] = 'admin/changepassword';
        $this->load->view('includes/template', $data);
    }

    public function logout() {
        checkAdmin();
        $data = array(
            'id' => '',
            'name' => '',
            'email' => '',
            'is_user_login' => 0,
            'image' => '',
            'login_as' => '',
        );
        $this->session->unset_userdata($data);
        $this->session->sess_destroy();
        redirect(base_url('admin'));
    }

    public function ajax_email_check_onupdate() {
        $email = (isset($_POST['email']) && !empty($_POST['email'])) ? $_POST['email'] : '';
        $userid = (isset($_POST['userid']) && !empty($_POST['userid'])) ? $_POST['userid'] : '';
        $where = array(
            'email' => $email,
            'id!=' => $userid,
        );
        $result = $this->admin->checkUniqueEmailUpdate('user', $where);
        if ($result) {
            echo json_encode(array("status" => TRUE));
        } else {
            echo json_encode(array("status" => FALSE));
        }
    }
    public function ajax_email_check() {
        $email = (isset($_POST['email']) && !empty($_POST['email'])) ? $_POST['email'] : '';
        $result = $this->manager->checkUniqueEmail('user', array('user_email' => $email, 'status' => 1));
        if ($result) {
            echo json_encode(array("status" => TRUE));
        } else {
            echo json_encode(array("status" => FALSE));
        }
    }

    public function ajax_forgot_pass(){
        $otp = $this->input->post('otp');
        $subject = "Forgot password | The Hajiri App";
        $content = "<p>Your OTP for mobile number verification and update new password is ".$otp." </p>";
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
        $name='aa';                              // Recipient's name
        $mail->From = $webmaster_email;
        $mail->Port = 465;
        $mail->FromName = "The Hajiri App";
        $mail->AddAddress($this->input->post('email'),'aa');
        $mail->AddReplyTo($webmaster_email,"The Hajiri App");
        $mail->WordWrap = 50;                         // set word wrap
        $mail->IsHTML(true);                          // send as HTML
        $mail->Subject = $subject;
        $mail->Body = $content;  
        if(!$mail->Send()){
            echo json_encode(array("status" => FALSE));
        } else {
            echo json_encode(array("status" => TRUE));
        }
    }

    public function ajax_update_pass(){
        $newdata = array(
            'password' => md5( $this->input->post('password') ),
        );
        $newresult = $this->admin->updateRecord("user", $this->input->post('email'), 'user_email', $newdata);
        if ($newresult) {
            $this->session->set_flashdata('success', 'Password Updated Successfully.');
            echo json_encode(array("status" => true));
        } else {
            echo json_encode(array("status" => FALSE));
        }
    }

    public function uploadImage(){
        if($this->input->post("image") != ''){
            $data = $this->input->post("image");
            $image_array_1 = explode(";", $data);
            $image_array_2 = explode(",", $image_array_1[1]);
            $data = base64_decode($image_array_2[1]);
            $imageName = time() . '.png';
            file_put_contents('uploads/user/'.$imageName, $data);
            echo $imageName;
        }
    }

}

?>