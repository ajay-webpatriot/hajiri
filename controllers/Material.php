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
        // $data['materials'] = $this->Material->get_all_material();
        $data['projects'] = $this->project->get_active_projects();
        $data['title'] = 'Material List';
        $data['description'] = 'All Material list';
        $data['page'] = 'material/list_material';
        
        $this->load->view('includes/template', $data);

    }
    public function materialDatatable()
    {
        $columns = array( 
                            0 =>'materials.name',
                            1 =>'categories.name',
                            2 => 'unit_measurement',
                            3 => 'project_name',
                            4 => 'status'
                        );
        // print_r($this->input->post('order'));exit;
        $limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = $columns[$this->input->post('order')[0]['column']];
        $dir = $this->input->post('order')[0]['dir'];
  
        $totalData = $this->Material->allMaterial_count();
            
        $totalFiltered = $totalData; 
        $where = null;
        if(!empty($this->input->post('search')['value']))
        {            
            $where .= '(materials.name LIKE "'.$this->input->post('search')['value'].'%" or ';
            $where .= 'categories.name LIKE "'.$this->input->post('search')['value'].'%" or ';
            $where .= 'materials.unit_measurement LIKE "'.$this->input->post('search')['value'].'%" or ';
            $where .= 'project.project_name LIKE "'.$this->input->post('search')['value'].'%")';
        }
        if(!empty($this->input->post('project')))
        {   
            if($where == null)
            $where .= 'material_projects.project_id = "'.$this->input->post('project').'"';
            else
            $where .= ' AND material_projects.project_id = "'.$this->input->post('project').'"';
        }
    
        if($where == null)
        {            
            $materialData = $this->Material->allMaterial($limit,$start,$order,$dir);
        }
        else {                

            $materialData =  $this->Material->material_custom_search($limit,$start,$where,$order,$dir);

            $totalFiltered = $this->Material->material_custom_search_count($where);
        }

        $data = array();
        if(!empty($materialData))
        {   
            foreach ($materialData as $material)
            {   
                
                $nestedData['name'] = $material->name;
                $nestedData['category_name'] = $material->category_name;
                $nestedData['unit_measurement'] = $material->unit_measurement;
                $nestedData['project_name'] = $material->project_name;
                
                if($material->status == 0)
                    $nestedData['status'] = '<a class="btn btn-sm btn-danger btn-xs" href="#" title="Status" data-status="' . $material->status . '" onclick="change_status(' . "'" . $material->id . "'" . ')">Inactive</a>';
                else if($material->status == 1)
                    $nestedData['status'] = '<a class="btn btn-sm btn-success btn-xs" href="#" title="Status" data-status="' . $material->status . '" onclick="change_status(' . "'" . $material->id . "'" . ')">Active</a>';
                //Edit Action                   
               
                $nestedData['action'] = '<a class="btn btn-sm btn-primary" href="'.base_url('admin/material/editMaterial/') . $material->id.'" title="Edit material">
                                    <i class="glyphicon glyphicon-pencil"></i> </a>

                                    <button class="btn btn-sm btn-danger" title="Delete material" onclick="material_delete('. $material->id.')">
                                    <i class="glyphicon glyphicon-trash"></i> 
                                </button>';
                    


                $data[] = $nestedData;

            }
        }
          
        $json_data = array(
                    "draw"            => intval($this->input->post('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
            
        echo json_encode($json_data); 
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
                        'company_id' => $this->session->userdata('company_id'),
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

                        $this->session->set_flashdata("success", "Material added successfully.");
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
                        'company_id' => $this->session->userdata('company_id'),
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