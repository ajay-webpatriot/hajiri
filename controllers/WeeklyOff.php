<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class WeeklyOff extends CI_Controller {

    public $data;
	public function __construct() {
        parent::__construct();
		$this->data['menu_title'] = 'WeeklyOff';
        $this->load->model('weeklyof_model', 'weekly');        
       // $this->load->model('project_model', 'project');
        checkAdmin();
    }

    public function index() {
       
        $data['title'] = 'Week off';
        $data['breadcrumb'] = $this->breadcrumbcomponent->output();
        
		$data = $this->data;
		$data['weeklyoff'] = $this->weekly->get_datatables();
        
        $data['result'] = array();
        if (!empty($data['weeklyoff'])) {
            foreach ($data['weeklyoff'] as $value) {
                $data['result'][] = $value->day;
            }
        }
       
        $data['title'] = 'WeeklyOff List';
        $data['page'] = 'weeklyoff/edit_weeklyoff';
       // print_r($data);
        $this->load->view('includes/template', $data);

    }

    public function addWeeklyOff() {
        $fileError = array();
        if (isset($_POST['submit'])) {
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('name', 'Name', 'trim|required|regex_match[/[a-zA-Z]/]');
            if ($this->form_validation->run() == TRUE) {
                $data = array(
                    'day' => $this->input->post('name'),
                    'status' => $this->input->post('status'),
                    'company_id' =>  $company_id = $this->session->userdata('company_id'),
                     'user_id' =>  $company_id = $this->session->userdata('id'),
                );
                
                $check_day = $this->weekly->check_day($this->input->post('name'));
                if(!$check_day ==''){
                     $this->session->set_flashdata('error', 'Day Already Present..!! ');
                     redirect(base_url('admin/WeeklyOff/addWeeklyOff'));
                }else{
                    
                    $labour_id = $this->weekly->save('week_off_days', $data);
                    if ($labour_id) {
                        $this->session->set_flashdata('success', 'Weekly of Added Successfully..!! ');
                        redirect(base_url('admin/WeeklyOff'));
                    } else {
                        $this->session->set_flashdata('error', 'Failed To Add ');
                        redirect(base_url('admin/WeeklyOff/addWeeklyOff'));
                    }
                 
                }
                

            }
        }
       
        $data = $this->data;
		$data['title'] = 'Add Weekly Off';
        $data['fileError'] = $fileError;
        $data['description'] = 'Add Weekly Off';
        $data['page'] = 'weeklyoff/add_weeklyoff';
        $this->load->view('includes/template', $data);
    }

    public function editWeeklyOff() {

        $fileError = array();
        if (isset($_POST['submit'])) {

            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('name', 'Name', 'trim|required|regex_match[/[a-zA-Z]/]');
          // if ($this->form_validation->run() == TRUE) {
              
               $data = array(
                    'day' => $this->input->post('working_days'),
                    'status' => 1,
                    'company_id' =>  $company_id = $this->session->userdata('company_id'),
                    'user_id' =>  $this->session->userdata('id'),
                );
               //For Delete Record
              
              $this->weekly->delete('week_off_days', 'company_id', $this->session->userdata('company_id'));

               foreach ($this->input->post('working_days') as $days) {
               // echo "In loop ".$days."<br>";
                        $this->weekly->save('week_off_days', array('day' => $days, 'user_id' => $this->session->userdata('id'),'company_id' => $this->session->userdata('company_id'), 'status' => 1));

                    }


               
                    $this->session->set_flashdata('success', ' Added Successfully..!! ');
                    redirect(base_url('admin/WeeklyOff/editWeeklyOff'));
               
            //}
        }
        
        $data = $this->data;
		$company_id = $this->session->userdata('company_id');
        $data['weeklyoff'] = $this->weekly->get_by_id($company_id);
        $data['result'] = array();
        if (!empty($data['weeklyoff'])) {
            foreach ($data['weeklyoff'] as $value) {
                $data['result'][] = $value->day;
            }
        }

        $data['title'] = 'Weekly Off';
        $data['fileError'] = $fileError;
        $data['page'] = 'weeklyoff/edit_weeklyoff';
        
        $this->load->view('includes/template', $data);
    }

    public function ajax_delete($id) {

        $this->weekly->delete('week_off_days', 'week_off_day_id', $id);
        $this->session->set_flashdata('success', 'Data Deleted Successfully');
        echo json_encode(array("status" => TRUE));
    }

    public function generate_PDF() {}

    public function pdfLabour($id, $month) {}

    function test() {}

}
