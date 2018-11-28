<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class YearlyHolidays extends CI_Controller {

    public function __construct() {
        parent::__construct();
		$this->data['menu_title'] = 'YearlyHolidays';
        $this->load->model('holidays_model', 'holidays');        
        $this->load->model('Register_model', 'register');        
        checkAdmin();
    }

    public function index() {
       
        $this->breadcrumbcomponent->add('Dashboard', base_url('/admin'));
        $this->breadcrumbcomponent->add('Yearly Holidays', base_url('/admin/YearlyHolidays'));
        $data['breadcrumb'] = $this->breadcrumbcomponent->output();
        
		$data = $this->data;
		$data['holidays'] = $this->holidays->get_datatables();
        $data['title'] = 'Yearly holidays';
        $data['description'] = 'All Holidays list';
        $data['page'] = 'holidays/list_holidays';
        $this->load->view('includes/template', $data);

        if (isset($_POST['submit'])) {
            //echo "<script type='text/javascript'>alert('edit entered');</script>";
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('name', 'Name', 'trim|required|regex_match[/[a-zA-Z]/]');
            $this->form_validation->set_rules('date', 'Date', 'trim|required');
            
            if ($this->form_validation->run() == TRUE) {
                $date = DateTime::createFromFormat('d-m-Y', $this->input->post('date'));
                $selected_date= $date->format('Y-m-d');
                $data = array(
                    'holiday_name'  => $this->input->post('name'),
                    'holiday_date'  => $selected_date,
                    'status'        => 1,
                    'company_id'    => $company_id = $this->session->userdata('company_id'),
                    'user_id'       => $this->session->userdata('id'),
                );
                if($this->input->post('hId') != NULL){
                //---- Edit holiday yearly ------

                    $checkDate = $this->holidays->checkUpdateDate($selected_date, $this->input->post('hId'),$company_id);   // Check if record for specified worker is present for selected date. If yes than change status
                    if($checkDate->count == 0){
                        $YH_id = $this->holidays->update('holidays', array('holiday_id' => $this->input->post('hId')), $data);
                        if ($YH_id) {
                            $this->session->set_flashdata('success', 'Yearly holiday Updated Successfully! ');
                            redirect(base_url('admin/YearlyHolidays'));
                        } else {
                            $this->session->set_flashdata('error', 'Failed To Update Yearly holiday.');
                            redirect(base_url('admin/YearlyHolidays'));
                        }
                    }else{
                        $this->session->set_flashdata('warning', 'Holiday with selected date <strong>'.$this->input->post('date').'</strong> already exist. Please select other date.');
                            redirect(base_url('admin/YearlyHolidays'));
                    }

                }else{
                    //Add holiday yearly

                    $checkDate = $this->holidays->checkDate($selected_date,$company_id);   // Check if record for specified worker is present for selected date. If yes than change status
                    if($checkDate->count == 0){
                        $YH_id = $this->holidays->save('holidays', $data);
                        if ($YH_id) {
                            $this->session->set_flashdata('success', 'Yearly holiday Added Successfully! ');
                            redirect(base_url('admin/YearlyHolidays'));
                        } else {
                            $this->session->set_flashdata('error', 'Failed To Add Yearly holiday.');
                            redirect(base_url('admin/YearlyHolidays'));
                        }
                    }else{
                        $this->session->set_flashdata('warning', 'Holiday with selected date <strong>'.$this->input->post('date').'</strong> already exist. Please select other date.');
                        redirect(base_url('admin/YearlyHolidays'));
                    }
                }
            }
        }

    }

    public function ajax_delete($id) {
        $where = 'holiday_id = '.$id;
        $data = array(
            'status' => 0,
        );
        $worker_id = $this->register->abscent('holidays',$data,$where);
        $this->session->set_flashdata('success', 'Data Deleted Successfully');
        echo json_encode(array("status" => TRUE));
    }

}
