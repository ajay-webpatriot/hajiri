<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier extends CI_Controller {

    public $data;
	public function __construct() {
        parent::__construct();
        
		$this->data['menu_title'] = 'Supplier';
        $this->load->model('Supplier_model', 'Supplier');    
        $this->load->model('MaterialCategory_model', 'MaterialCategory');       
        $this->load->model('project_model', 'project'); 

        checkAdmin();
    }

    public function index() {
        
        // Display supplier list
        $data = $this->data;
        $data['suppliers'] = $this->Supplier->get_all_supplier();
        $data['title'] = 'Supplier List';
        $data['description'] = 'All Supplier list';
        $data['page'] = 'supplier/list_supplier';
        
        $this->load->view('includes/template', $data);

    }
    public function addSupplier() {
        
        
        if (isset($_POST['submit'])) {
            
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('company_name', 'Company Name', 'trim|required');
            $this->form_validation->set_rules('supplier_name', 'Supplier Name', 'trim|required');
            $this->form_validation->set_rules('address', 'Address', 'trim|required');
            // $this->form_validation->set_rules('gst_number', 'GST No', 'trim|required');
            $this->form_validation->set_rules('email_id', 'Email Id', 'trim|required|valid_email');
            $this->form_validation->set_rules('contact_number', 'Contact Number', 'trim|required|numeric');
            
            if ($this->form_validation->run() == TRUE) {
                       
                $data = array(
                    'name' => $this->input->post('supplier_name'),
                    'company_name' => $this->input->post('company_name'),
                    'contact_number' => $this->input->post('contact_number'),
                    'gst_number' => $this->input->post('gst_number'),
                    'address' => $this->input->post('address'),
                    'email' => $this->input->post('email_id'),
                    'status' => 1,
                );
                $project_ids=  $this->input->post('project_id');
                $category_ids=  $this->input->post('category_id');

                $supplier_id = $this->Supplier->save('suppliers', $data);
                if ($supplier_id) {

                    foreach ($project_ids as $key => $value) {
                        # code...
                        $data=array(
                            'supplier_id'=>$supplier_id,
                            'project_id'=> $value
                        );
                        $this->Supplier->save('supplier_projects', $data);
                    }

                    foreach ($category_ids as $key => $value) {
                        # code...
                        $data=array(
                            'supplier_id'=>$supplier_id,
                            'category_id'=> $value
                        );
                        $this->Supplier->save('supplier_categories', $data);
                    }
                    $this->session->set_flashdata("success", "Supplier added successfully.");
                    redirect(base_url('admin/supplier'));
                } else {
                    $this->session->set_flashdata('error', 'Failed To Add Supplier');
                    redirect(base_url('admin/supplier/addSupplier'));
                }
            }
        }

        $data = $this->data;
        $data['Categories'] = $this->MaterialCategory->get_active_material_category();

        $data['projects'] = $this->project->get_active_projects();

        $data['title'] = 'Add Supplier';
        $data['description'] = 'Add Supplier';
        $data['page'] = 'supplier/add_supplier';
        $this->load->view('includes/template', $data);
    
    }
    public function editSupplier($id) {
        $company_id = $this->session->userdata('company_id');
        $fileError = array();
        
        if (isset($_POST['submit'])) {
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('company_name', 'Company Name', 'trim|required');
            $this->form_validation->set_rules('supplier_name', 'Supplier Name', 'trim|required');
            $this->form_validation->set_rules('address', 'Address', 'trim|required');
            // $this->form_validation->set_rules('gst_number', 'GST No', 'trim|required');
            $this->form_validation->set_rules('email_id', 'Email Id', 'trim|required|valid_email');
            $this->form_validation->set_rules('contact_number', 'Contact Number', 'trim|required|numeric');
            
            if ($this->form_validation->run() == TRUE) {
                
                
                $data = array(
                    'name' => $this->input->post('supplier_name'),
                    'company_name' => $this->input->post('company_name'),
                    'contact_number' => $this->input->post('contact_number'),
                    'gst_number' => $this->input->post('gst_number'),
                    'address' => $this->input->post('address'),
                    'email' => $this->input->post('email_id'),
                    'status' => $this->input->post('status'),
                );
                 
                $supplier_id = $this->Supplier->update('suppliers', array('id' => $id), $data);

                $project_ids=  $this->input->post('project_id');
                $category_ids=  $this->input->post('category_id');
                 
                $this->Supplier->delete('supplier_projects', 'supplier_id', $id);
                foreach ($project_ids as $key => $value) {
                    # code...
                    $data=array(
                        'supplier_id'=>$id,
                        'project_id'=> $value
                    );
                    $this->Supplier->save('supplier_projects', $data);
                }

                $this->Supplier->delete('supplier_categories', 'supplier_id', $id);
                foreach ($category_ids as $key => $value) {
                    # code...
                    $data=array(
                        'supplier_id'=>$id,
                        'category_id'=> $value
                    );
                    $this->Supplier->save('supplier_categories', $data);
                }

                $this->session->set_flashdata('success', 'Supplier Updated Successfully. ');
                redirect(base_url('admin/supplier'));
            }
        }

        $data = $this->data;
        $data['Categories'] = $this->MaterialCategory->get_active_material_category();
        $data['projects'] = $this->project->get_active_projects();
        $result = $this->Supplier->get_by_id($id);
        $data['result'] = $result;
        $data['title'] = 'Edit Supplier';
        $data['description'] = 'Edit Supplier Description';
        $data['page'] = 'supplier/edit_supplier';
        $this->load->view('includes/template', $data);
    }
    public function ajax_change_status($id) {
        $result = $this->Supplier->get_status('suppliers', $id);
        if ($result->status == "1") {
            $supplier_id = $this->Supplier->update('suppliers', array('id' => $id), array('status' => 0));
            
        } else {
            $supplier_id = $this->Supplier->update('suppliers', array('id' => $id), array('status' => 1));
           
        }
        
        $this->session->set_flashdata('success', 'Status Changed Successfully');
        echo json_encode(array("status" => TRUE));
    }
    
    public function ajax_delete($id) {
        $supplier_id = $this->Supplier->update('suppliers', array('id' => $id), array('is_deleted' => '1'));
        $this->session->set_flashdata('success', 'Supplier Deleted Successfully');
    }
}
?>