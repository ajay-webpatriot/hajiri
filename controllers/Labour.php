<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Labour extends CI_Controller {

    public $data;
	public function __construct() {
        parent::__construct();
		$this->data['menu_title'] = 'Worker';
        $this->load->model('labour_model', 'labour');        
        $this->load->model('Category_model', 'category');
        $this->load->model('project_model', 'project');
        checkAdmin();
    }

    public function index() {
       
        $this->breadcrumbcomponent->add('Dashboard', base_url('/admin'));
        $this->breadcrumbcomponent->add('All Worker', base_url('/admin/labour'));
        $data['breadcrumb'] = $this->breadcrumbcomponent->output();
        $data = $this->data;
        $role = $this->session->userdata('user_designation');
        if ( $role == 'admin' ){
            $company_id = $this->session->userdata('company_id');
            $data['labours'] = $this->labour->get_datatables_admin($company_id);
        }else{
            $data['labours'] = $this->labour->get_datatables();
        }
        
        $data['title'] = 'Worker List';
        $data['description'] = 'All worker list';
        $data['page'] = 'labour/list_labour';
       // print_r($data);
        $this->load->view('includes/template', $data);

    }

   /* public function addLabour() {
        $fileError = array();
        if (isset($_POST['submit'])) {
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('first_name', 'First name', 'trim|required|regex_match[/[a-zA-Z]/]');
            $this->form_validation->set_rules('last_name', 'Last name', 'trim|regex_match[/[a-zA-Z]/]');
            $this->form_validation->set_rules('contact', 'Contact No.', 'trim|regex_match[/^[0-9]{10}$/]|min_length[10]');
            $this->form_validation->set_rules('daily_wage', 'Daily Wage', 'trim|required');
            $this->form_validation->set_rules('company', 'Company', 'trim|required');
            $this->form_validation->set_rules('cid', 'Category', 'trim|required');
            $this->form_validation->set_rules('due_amount', 'Opening Amount', 'trim|required|numeric');
            if ($this->form_validation->run() == TRUE) {
                $data = array(
                    'company_id' => $this->input->post('company'),
                    'labour_name' => $this->input->post('first_name'),
                    'labour_last_name' => $this->input->post('last_name'),
                    'worker_contact' => $this->input->post('contact'),
                    'category_id' => $this->input->post('cid'),
                    'labour_join_date' => date('Y-m-d'),
                    'status' => 1,
                    //'due_amount' => $this->input->post('due_amount'),
                );
                $labour_id = $this->labour->save('worker', $data);
                if ($labour_id) {
                     $wageData = array(
                        'worker_id' => $labour_id,
                        'worker_wage' => $this->input->post('daily_wage'),
                        'worker_opening_wage' => $this->input->post('due_amount'),
                        'wage_start_date' => date('Y-m-d'),
                        'worker_wage_type' => 0,
                    );
                    $wage_id = $this->labour->save('worker_wage', $wageData);
                     if ($wage_id) {
                        $wageIdData = array(
                            'worker_wage' => $wage_id,
                        );
                        $labour_id = $this->labour->update('worker', array('worker_id' => $labour_id), $wageIdData);

                        $this->session->set_flashdata('success', 'Worker Added Successfully! ');
                        redirect(base_url('admin/labour'));
                    }
                    else {
                        $this->session->set_flashdata('error', 'Failed To Add Wage');
                        redirect(base_url('admin/labour/addLabour'));
                    }
                } else {
                    $this->session->set_flashdata('error', 'Failed To Add Labour');
                    redirect(base_url('admin/labour/addLabour'));
                }
            }
        }
        $data = $this->data;
        $data['company'] = $this->labour->get_all_company('company');
		//$data['category'] = $this->category->get_all_category();
        $data['projects'] = $this->labour->get_all_project('project');
        $data['title'] = 'Add worker';
        $data['fileError'] = $fileError;
        $data['description'] = 'Add worker';
        $data['page'] = 'labour/add_labour';
        $this->load->view('includes/template', $data);
    }*/

    public function editLabour($id) {
        $fileError = array();
        if (isset($_POST['submit'])) {
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('name', 'Name', 'trim|required|regex_match[/[a-zA-Z]/]');
            $this->form_validation->set_rules('contact', 'Contact No.', 'trim|required|regex_match[/^[0-9]{10}$/]|min_length[10]');
            $this->form_validation->set_rules('aadhar_id', 'Aadhar No.', 'trim|required');
            $this->form_validation->set_rules('user_id', 'User Name', 'trim|required');
            $this->form_validation->set_rules('daily_wage', 'Daily Wage', 'trim|required');
            $this->form_validation->set_rules('category', 'Category', 'trim|required');
            $this->form_validation->set_rules('total_amount', 'Total Amount', 'trim|required|numeric');
            $this->form_validation->set_rules('paid_amount', 'Paid Amount', 'trim|required|numeric');
            $this->form_validation->set_rules('due_amount', 'Due Amount', 'trim|required|numeric');
           
           // if ($this->form_validation->run() == TRUE) {
              
                
                $data = array(
                    'labour_name' => $this->input->post('name'),
                    'worker_contact' => $this->input->post('contact'),
                    'labour_aadhar' => $this->input->post('aadhar_id'),
                    'labour_join_date' => $this->input->post('joindate'),
                    'worker_wage' => $this->input->post('daily_wage'),
                    'category_id' => $this->input->post('category'),
                );
               //print_r($data);
                $associatedFileNames = array('image');
                foreach ($associatedFileNames as $fileName) {
                    if (!empty($_FILES[$fileName]['name'])) {

                        $result = uploadStaffFile('uploads/labour/', $fileName);
                        if ($result['flag'] == 1) {
                            $data['worker_qrcode_image'] = $result['filePath'];
                        } else {
                            $fileError[$fileName] = $result['error'];
                        }
                    }
                }
                $labour_id = $this->labour->update('worker', array('worker_id' => $id), $data);
                if ($labour_id) {
                    $this->session->set_flashdata('success', 'Labour Updated Successfully..!! ');
                    redirect(base_url('admin/labour'));
                } else {
                    $this->session->set_flashdata('error', 'Failed To Update Labour');
                    redirect(base_url('admin/labour/editLabour/' . $id));
                }
            //}
        }
        
        $data = $this->data;
        if( $this->uri->segment(4) > 0 ){
            $data['result'] = $this->labour->get_by_id($this->uri->segment(4));
            $data['title'] = 'Edit Worker';
        }
        $data['fileError'] = $fileError;
        $data['description'] = 'Edit Labour Description';
        $data['page'] = 'labour/edit_labour';
        $this->load->view('includes/template', $data);
    }

    public function ajax_delete($id) {
        $this->labour->delete('worker', 'worker_id', $id);
        $this->session->set_flashdata('success', 'Data Deleted Successfully');
        echo json_encode(array("status" => TRUE));
    }


    public function ajax_get_categoryList($id) {
        $prjlist = "";
        $category = $this->category->get_all_category($id);
        if (!empty($category)) {
            $prjlist .= '<select name="category" class="form-control">';
            $prjlist .= '<option value = "">--Select Category--</option>';
            foreach ($category as $project) {
                $prjlist .= "<option value = '" . $project->id . "'>" . $project->category . "</option>";
            }
            $prjlist .= '</select>';
        } else {
            $prjlist .= '<select name="category" class="form-control">';
            $prjlist .= '<option value = "">--No Category Available--</option>';
            $prjlist .= '</select>';
        }
        $result['projectlist'] = $prjlist;
        echo json_encode($result);
    }

    public function generate_PDF() {}

    public function pdfLabour($id, $month) {}

    function test() {}

}
