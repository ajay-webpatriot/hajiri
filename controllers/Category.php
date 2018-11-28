<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class category extends CI_Controller {

    public $data;
	public function __construct() {
        parent::__construct();
		$this->data['menu_title'] = 'category';
        $this->load->model('Category_model', 'Category');        
        $this->load->model('Register_model', 'register');        
        $this->load->model('Labour_model', 'labour');        
        checkAdmin();
    }

    public function index() {
        $this->data['menu_title'] = 'category';
        $this->breadcrumbcomponent->add('Dashboard', base_url('/admin'));
        $this->breadcrumbcomponent->add('All Category', base_url('/admin/category'));
        $data['breadcrumb'] = $this->breadcrumbcomponent->output();
        
		$data = $this->data;
		$data['Category'] = $this->Category->get_all_category($this->session->userdata('company_id'));

        if (isset($_POST['submit'])) {
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('categoryName', 'Category', 'trim|required|regex_match[/[a-zA-Z]/]');
            
            if ($this->form_validation->run() == TRUE) {
                $data = array(
                    'company_id' => $this->session->userdata('company_id'),
                    'category_name' => $this->input->post('categoryName'),
                    'status' => 1,
                );
                $cat_id = $this->labour->save('worker_category', $data);
                if ($cat_id) {

                    $this->session->set_flashdata('success', 'Category Added Successfully! ');
                    redirect(base_url('admin/category'));
                   
                } else {
                    $this->session->set_flashdata('error', 'Failed To Add Category.');
                    redirect(base_url('admin/category'));
                }
            }
        }

        if (isset($_POST['edit'])) {
            //echo "<script type='text/javascript'>alert('edit entered');</script>";
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('categoryName', 'Category', 'trim|required|regex_match[/[a-zA-Z]/]');
            
            if ($this->form_validation->run() == TRUE) {
                $data = array(
                    'category_name' => $this->input->post('categoryName'),
                );
                $where = "category_id = ".$this->input->post('catId');
                $cat_id = $this->labour->update('worker_category',$where ,$data);
                if ($cat_id) {

                    $this->session->set_flashdata('success', 'Category Updated Successfully! ');
                    redirect(base_url('admin/category'));
                   
                } else {
                    $this->session->set_flashdata('error', 'Failed To Updat Category.');
                    redirect(base_url('admin/category'));
                }
            }
        }


        $data['title'] = 'Category List';
        $data['description'] = 'All categoy list';
        $data['page'] = 'category/category';
       // print_r($data);
        $this->load->view('includes/template', $data);

    }

    public function ajax_delete($id) {
        $where = 'category_id = '.$id;
        $data = array(
            'status' => 0,
        );
        $check = $this->Category->checkCategory($id);
        if($check->id == 0){
            $worker_id = $this->register->abscent('worker_category',$data,$where);
            $this->session->set_flashdata('success', 'Category deleted successfully.');
        }else{
            $this->session->set_flashdata('warning', 'Category is already assigned to a worker.');
        }
    }
}
?>