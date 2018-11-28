<?php

defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('max_execution_time', 0); 
ini_set('memory_limit','2048M');
class CalculationDailywage extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('CalculationDailywage_model', 'labourimport');
        checkAdmin();
    }

    public function index() {
        
    }

    public function importLabourFile() {
        $fileError = $skipped_data = array();
        $skip_row = $insert_row = $PHPdateValue = $joinDate = "";
        if (isset($_POST['submit'])) {
           // echo "In submit";

            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('method_id', 'User Name', 'trim|required');
            if ($this->form_validation->run() == TRUE) {
                $user_id = $this->session->userdata('id'); 
                $company_id = $this->session->userdata('company_id');
                $data = array(
                    'method_id' => $this->input->post('method_id'),
                    'company_id' => $company_id,
                    'user_id' => $user_id,
                    'custom_value' => $this->input->post('day'),
                    'status' => 1,
                );
                 $this->labourimport->delete('daily_wage_calculation_method_assign', 'company_id', $company_id);
                $labour_id = $this->labourimport->save('daily_wage_calculation_method_assign', $data);
                               // echo "string". $labour_id;
                if ($labour_id)
                       { 
                        $this->session->set_flashdata('success', "Calculation Method Added Successfully");
                        }//if file upload fail
                        else 
                        {
                            //$fileError[$fileName] = $result['error'];
                            $this->session->set_flashdata('error', "Failed to Add");
                        }
                    
                
            }
        }
         $id = $this->session->userdata('id');
        $data['users'] = $this->labourimport->get_all_wagemethod('daily_wage_calculation_method',$id);
        $methods = $this->labourimport->get_selected_method('daily_wage_calculation_method_assign',$id);
        if($methods == 3){
            $days = $this->labourimport->get_days('daily_wage_calculation_method_assign',$id);
            //echo "Days".$days;
            $data['days'] = $days;
        }else{
            $data['days']=0;
        }
        $data['title'] = 'Calculation Daily Wage';
        $data['fileError'] = $fileError;
        $data['skippedData'] = $skipped_data;
        $data['insertRows'] = $insert_row;
        $data['skipRows'] = $skip_row;
        $data['selected_method'] = $methods;
        $data['description'] = 'Calculation Daily Wage';
        $data['page'] = 'dailywage/calculationwage';
        $this->load->view('includes/template', $data);
    }

}
