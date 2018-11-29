<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MaterialCategory extends CI_Controller {

    public $data;
	public function __construct() {
        parent::__construct();
        
		$this->data['menu_title'] = 'Material Management';
        $this->load->model('MaterialCategory_model', 'MaterialCategory'); 
        checkAdmin();
    }

    public function index() {
        
        // Display category list
        $this->data['menu_title'] = 'Material Category';
        $this->breadcrumbcomponent->add('Dashboard', base_url('/admin'));
        $this->breadcrumbcomponent->add('All Category', base_url('/admin/MaterialCategory'));
        $data['breadcrumb'] = $this->breadcrumbcomponent->output();
        
		$data = $this->data;
		$data['Category'] = $this->MaterialCategory->get_all_material_category();

        if (isset($_POST['submit'])) {
            // Add category
            // $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('categoryName', 'Category', 'trim|required|is_unique[categories.name]|regex_match[/[a-zA-Z]/]');
             
            if ($this->form_validation->run() == TRUE) {
                $data = array(
                    'name' => $this->input->post('categoryName'),
                    'approximate_estimate_ratio'=> $this->input->post('approximate_estimate_ratio'),
                    'status' => 1,
                );
                $cat_id = $this->MaterialCategory->save('categories', $data);
                if ($cat_id) {

                    $this->session->set_flashdata('success', 'Category Added Successfully! ');
                    redirect(base_url('admin/MaterialCategory'));
                } else {
                    $this->session->set_flashdata('error', 'Failed To Add Category.');
                    redirect(base_url('admin/MaterialCategory'));
                }
            }else{
               $this->session->set_flashdata('error', validation_errors());
               redirect(base_url('admin/MaterialCategory'));
            }
        }

        if (isset($_POST['edit'])) {

            $id = $this->input->post('catId');

            $original_value = $this->MaterialCategory->caheckIsExistCategory($id);
            
            if($this->input->post('categoryName') != $original_value->name){
                $is_unique = '|is_unique[categories.name]';
            }else{
                $is_unique = '';
            }

            // update category
            // $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('categoryName', 'Category', 'trim|required|regex_match[/[a-zA-Z]/]'.$is_unique);
            
            if ($this->form_validation->run() === TRUE) {
                $data = array(
                    'name' => $this->input->post('categoryName'),
                    'approximate_estimate_ratio'=> $this->input->post('approximate_estimate_ratio')
                );
                $where = "id = ".$id;
                $cat_id = $this->MaterialCategory->update('categories',$where ,$data);
                if ($cat_id) { 
                    $this->session->set_flashdata('success', 'Category Updated Successfully! ');
                    redirect(base_url('admin/MaterialCategory'));
                } else {
                    $this->session->set_flashdata('error', validation_errors());
                    redirect(base_url('admin/MaterialCategory'));
                }
            }else{
                $this->session->set_flashdata('error', validation_errors());
                redirect(base_url('admin/MaterialCategory'));
            }
        }

        $data['title'] = 'Material Category List';
        $data['description'] = 'All categoy list';
        $data['page'] = 'materialcategory/materialcategory';
       // print_r($data);
        $this->load->view('includes/template', $data);

    }
    public function ajax_change_status($id) {
        $result = $this->MaterialCategory->get_status('categories', $id);
        if ($result->status == "1") {
            $material_category_id = $this->MaterialCategory->update('categories', array('id' => $id), array('status' => 0));
            
        } else {
            $material_category_id = $this->MaterialCategory->update('categories', array('id' => $id), array('status' => 1));
           
        }
        
        $this->session->set_flashdata('success', 'Status Changed Successfully');
        echo json_encode(array("status" => TRUE));
    }
    public function ajax_delete($id) {
        $where = 'id = '.$id;
        $data = array(
            'status' => 0,
        );
        $check = $this->MaterialCategory->checkCategory($id);
        if($check->id == 0){
           $worker_id = $this->MaterialCategory->update('categories', array('id' => $id), array('is_deleted' => '1'));
            $this->session->set_flashdata('success', 'Category deleted successfully.');
        }else{
            $this->session->set_flashdata('warning', 'Category is already assigned to a material.');
        }
    }
}
?>