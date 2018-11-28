<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Upgrade extends CI_Controller {

	public $data;
    public function __construct() {
        parent::__construct();
		$this->data['menu_title'] = 'Upgrade';
        checkAdmin();
        $this->load->library('session');
        $this->load->model('Companies_model', 'company');
        $this->load->model('Plan_model', 'plan');
    }

    public function index() {

        if (isset($_POST['submit'])) {
            $this->session->set_flashdata('amount', $this->input->post('amount'));
            redirect(base_url('admin/upgrade/payment'));
        }

        $data = $this->data;
        $data['title'] = 'Select Plan';
        $data['page'] = 'upgrade/plans';
        $this->load->view('includes/template', $data);
        
    }

    public function payment() {

        $data = $this->data;
        if (isset($_POST['signature'])) {
            $orderDetails 
                = 
                array(
                    'company_id' => $this->session->userdata('company_id'), 
                    'user_id' => $this->session->userdata('id'), 
                    'order_id' => $this->input->post('orderId'), 
                    'order_amount' => $this->input->post('orderAmount'),
                    'reference_id' => $this->input->post('referenceId'), 
                    'order_amount' => $this->input->post('orderAmount'),
                    'payment_mode' => $this->input->post('paymentMode'), 
                    'txt_status' => $this->input->post('txStatus'), 
                    'txt_msg' => $this->input->post('txMsg'), 
                    'txt_time' => $this->input->post('txTime'), 
                );
            $insertId = $this->company->save('PG_Details', $orderDetails);
            if ($insertId) {
                $this->session->set_flashdata('success', 'Payment made successfully.');
                
                $updateWhere 
                    = 
                    array(
                        'company_id' => $this->session->userdata('company_id'), 
                    );
                if($this->input->post('orderAmount') == 49){
                    if(strtotime($this->session->userdata('due_date')) < strtotime(date('Y-m-d')) )
                        $dueDate = date('Y-m-d', strtotime('+1 months'));
                    else
                        $dueDate = date('Y-m-d', strtotime('+1 months', strtotime($this->session->userdata('due_date'))));        
                    $plan_type = 0;
                }
                else{
                    if(strtotime($this->session->userdata('due_date')) < strtotime(date('Y-m-d')) )
                        $dueDate = date('Y-m-d', strtotime('+1 years'));
                    else
                        $dueDate = date('Y-m-d', strtotime('+1 years', strtotime($this->session->userdata('due_date'))));     
                    $plan_type = 1;
                }
                $updateData 
                = 
                array(
                    'due_date' => $dueDate, 
                    'plan_type' => $plan_type,
                );
                $updateId = $this->company->update('company_plan', $updateWhere, $updateData);
                if ($insertId) {
                    $this->session->set_flashdata('dueDate', date('d-m-Y', strtotime($dueDate)));
                    $this->session->set_flashdata('success', 'Account upgraded successfully.');
                }else{
                    $this->session->set_flashdata('error', 'Error occured while upgrading account.');
                }

            }else{
                $this->session->set_flashdata('error', 'Error occured while upgrading account.');
            }

        }
        if($this->session->flashdata('amount') != null || isset($_POST["orderAmount"])){
            $data['amount'] = $this->session->flashdata('amount');
            $data['title'] = 'Make payment';
            $data['page'] = 'upgrade/payment';
            $this->load->view('includes/template', $data);
        }else{
            redirect(base_url('admin/upgrade'));

        }

    }

    public function enterprise_inquiry(){
        $email = $this->session->userdata('user_email');
        $name = $this->session->userdata('name');

        $companyDetails = $this->company->get_company_detl( $this->session->userdata('company_id') );
        $userDetails = $this->plan->get_where('user','user_id', $this->session->userdata('id') );

        $subject = 'Enterprise inquiry '.$companyDetails->company_name;
        $content = "<p>Enterprise request received.</p>";
        $content .= "<p>Company details:</p>";
        

        $content .= "<p>Company name: ".$companyDetails->company_name."</p>";
        $content .= "<p>Company email: ".$companyDetails->company_email."</p>";
        $content .= "<p>User email: ".$email."</p>";
        $content .= "<p>User name: ".$name."</p>";
        $content .= "<p>User contact: ".$userDetails->user_contact."</p>";

        $emailSent = $this->htmlmail('hajiri@aasaan.co',$name,$subject, $content);
        if ($emailSent == 'true') {
            echo json_encode('True');
        } else {
            echo json_encode('False');
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
            return 'false';
        else
            return 'true';
    }
}
    ?>