<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Material extends CI_Controller {

    public $data;
	public function __construct() {
        parent::__construct();
        
		$this->data['menu_title'] = 'Material';
        $this->load->model('Material_model', 'Material');    
        $this->load->model('MaterialCategory_model', 'MaterialCategory');
        $this->load->model('project_model', 'project');         
        checkAdmin();
    }

    public function index() {
        
        // Display material list
        $data = $this->data;
        $data['materials'] = $this->Material->get_all_material();
        $data['title'] = 'Material List';
        $data['description'] = 'All Material list';
        $data['page'] = 'material/list_material';
        
        $this->load->view('includes/template', $data);

    }
    public function addMaterial() {
        
        
        if (isset($_POST['submit'])) {

           if(!empty($this->input->post('project_id'))){

                $project_id  = $this->input->post('project_id');
                $material_name = $this->input->post('material_name');

                $original_value = $this->Material->caheckIsExistMaterial('', $project_id, $material_name);
                 
                if(!empty($original_value)){
                    $is_unique = '|is_unique[materials.name]';
                }else{
                    $is_unique = '';
                }
            } 
            
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('project_id', 'Project', 'callback_selectCategory_validate['.$this->input->post('project_id').']');
            $this->form_validation->set_rules('category_id', 'Material Category', 'callback_selectCategory_validate['.$this->input->post('category_id').']');
            $this->form_validation->set_rules('material_name', 'Material Name', 'trim|required'.$is_unique);
            $this->form_validation->set_rules('unit_measurement', 'Unit of Measurement', 'trim|required');
            // $this->form_validation->set_rules('hsn_code', 'HSN Code', 'trim|required');
            $this->form_validation->set_rules('bound_start_range', 'Bound Range', 'trim|required');
            $this->form_validation->set_rules('bound_end_range', 'Bound Range', 'trim|required');
            
            if ($this->form_validation->run() == TRUE) {

                $bound_start_range = $this->input->post('bound_start_range');
                $bound_end_range = $this->input->post('bound_end_range');

                if($bound_start_range > $bound_end_range){
                    $this->session->set_flashdata('error', 'Please enter Bond start range is greater than or equal to the Bond end range.');
                } else{

                    $data = array(
                        'name' => $this->input->post('material_name'),
                        'category_id' => $this->input->post('category_id'),
                        'unit_measurement' => $this->input->post('unit_measurement'),
                        'hsn_code' => $this->input->post('hsn_code'),
                        'bound_start_range' => $this->input->post('bound_start_range'),
                        'bound_end_range' => $this->input->post('bound_end_range'),
                        'status' => 1,
                    );
                     
                    $material_id = $this->Material->save('materials', $data);
                    if ($material_id) {

                        $data = array(
                            'material_id' => $material_id,
                            'project_id' => $this->input->post('project_id'),
                            
                        );
                        $material_project_id = $this->Material->save('material_projects', $data);

                        $this->session->set_flashdata("success", "Supervisor added successfully.");
                        redirect(base_url('admin/material'));
                    } else {
                        $this->session->set_flashdata('error', 'Failed To Add Material');
                        redirect(base_url('admin/material/addMaterial'));
                    }
                }
            }
        }

        $data = $this->data;
        $data['Categories'] = $this->MaterialCategory->get_active_material_category();
        $data['projects'] = $this->project->get_active_projects();
        $data['title'] = 'Add Material';
        $data['description'] = 'Add Material';
        $data['page'] = 'material/add_material';
        $this->load->view('includes/template', $data);
    
    }
    function selectCategory_validate($field, $id)
    {
        if($id != '') {
            return true;
        } else {
            $this->form_validation->set_message('selectCategory_validate', 'The Material Category is required.');
            return false;
        }
    }
    public function editMaterial($id) {
        $company_id = $this->session->userdata('company_id');
        $fileError = array();
        
        if (isset($_POST['submit'])) {

            if(!empty($this->input->post('project_id'))){
                $project_id = $this->input->post('project_id');
                $material_name = $this->input->post('material_name');
            }
            //id, project_id, material Name
            $original_value = $this->Material->caheckIsExistMaterial($id, $project_id, $material_name);
            
            if(!empty($original_value)){
                $is_unique = '|is_unique[materials.name]';
            }else{
                $is_unique = '';
            }
            
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('project_id', 'Project', 'callback_selectCategory_validate['.$this->input->post('project_id').']');
            $this->form_validation->set_rules('category_id', 'Material Category', 'callback_selectCategory_validate['.$this->input->post('category_id').']');
            $this->form_validation->set_rules('material_name', 'Material Name', 'trim|required'.$is_unique);
            $this->form_validation->set_rules('unit_measurement', 'Unit of Measurement', 'trim|required');
            $this->form_validation->set_rules('bound_start_range', 'Bound Range', 'trim|required');
            $this->form_validation->set_rules('bound_end_range', 'Bound Range', 'trim|required');

            
            if ($this->form_validation->run() == TRUE) {

                $bound_start_range = $this->input->post('bound_start_range');
                $bound_end_range = $this->input->post('bound_end_range');

                if($bound_start_range > $bound_end_range){
                    $this->session->set_flashdata('error', 'Please enter Bond start range is greater than or equal to the Bond end range.');
                } else{
                    
                    $data = array(
                        'name' => $this->input->post('material_name'),
                        'category_id' => $this->input->post('category_id'),
                        'unit_measurement' => $this->input->post('unit_measurement'),
                        'hsn_code' => $this->input->post('hsn_code'),
                        'bound_start_range' => $this->input->post('bound_start_range'),
                        'bound_end_range' => $this->input->post('bound_end_range'),
                        'status' => $this->input->post('status'),
                    );
                    
                    $material_id = $this->Material->update('materials', array('id' => $id), $data);
                    $material_project_id = $this->Material->update('material_projects',array('material_id' => $id), array('project_id' => $this->input->post('project_id')));
                    $this->session->set_flashdata('success', 'Material Updated Successfully. ');
                    redirect(base_url('admin/material'));
                }
            }
        }

        $data = $this->data;
        $data['projects'] = $this->project->get_active_projects();
        $data['Categories'] = $this->MaterialCategory->get_active_material_category();
        $result = $this->Material->get_by_id($id);
        $data['result'] = $result;

        $data['title'] = 'Edit Material';
        $data['description'] = 'Edit Supervisor Description';
        $data['page'] = 'material/edit_material';
        $this->load->view('includes/template', $data);
    }
    public function ajax_change_status($id) {
        $result = $this->Material->get_status('materials', $id);
        if ($result->status == "1") {
            $material_id = $this->Material->update('materials', array('id' => $id), array('status' => 0));
            
        } else {
            $material_id = $this->Material->update('materials', array('id' => $id), array('status' => 1));
        }
        
        $this->session->set_flashdata('success', 'Status Changed Successfully');
        echo json_encode(array("status" => TRUE));
    }
    
    public function ajax_delete($id) {
        
        $worker_id = $this->MaterialCategory->update('materials', array('id' => $id), array('is_deleted' => '1'));
        // $this->Material->delete('materials', 'id', $id);
        $this->session->set_flashdata('success', 'Material Deleted Successfully');
    }
}
?>