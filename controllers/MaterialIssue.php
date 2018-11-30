<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MaterialIssue extends CI_Controller {
   
    public $data;   
    public function __construct() {
        parent::__construct();
        $this->data['menu_title'] = 'Issue Log';
        $this->load->model('MaterialCategory_model');
        $this->load->model('MaterialIssueModel');
        $this->load->model('Project_model');
        $this->load->model('MaterialLog_model');
        $this->load->model('Admin_model', 'admin');        
        checkAdmin();
    }
    public function index() {
        $data['title'] = 'Material Issue';
        $data['menu_title'] = 'Issue Log';
        $data['materialIssue'] = $this->MaterialIssueModel->getIssueLog();
        $data['page'] = 'matirealIssue/matirealIssueIndex';

        $this->load->view('includes/template', $data);
    }
    public function addIssueLog(){
        if($this->input->post()){
            $Issuefile = $_FILES["Issuefile"];
            
            $this->form_validation->set_rules('issueNo', 'Issue No', 'trim|required');    
            $this->form_validation->set_rules('issueDate', 'Date', 'trim|required');
            $this->form_validation->set_rules('MaterialName', 'Material Name', 'trim|required');
            $this->form_validation->set_rules('IssueQuantity', 'Quantity', 'required');
            $this->form_validation->set_rules('materialCategory', 'Material Category', 'required');
            $this->form_validation->set_rules('sites', 'Consumption Place', 'required');
            $this->form_validation->set_rules('project_name', 'Project Name', 'required');
            
            if ($this->form_validation->run() == false ) {
                
                $this->session->set_flashdata( 'error', 'Sorry,  Error while adding Material Issue details.' );
                // redirect( base_url( 'admin/MaterialIssue/addIssueLog/') );
            }else{

                $material_quantity = $this->input->post('IssueQuantity');

                $valid_quantity = true;

                foreach ($material_quantity as  $value) {
                    if($value <= 0){
                      $valid_quantity = false;
                    }
                }
                if($valid_quantity == true){


                    $issue_image=$Issuefile['name'];
                    if (!empty($issue_image)) {
                        $Issuefile = $_FILES["Issuefile"]['name'];
                        $fileResult = uploadStaffFile('uploads/MaterialIssue/', 'Issuefile');
                        $issue_image=$fileResult['filePath'];
                    }
                    
                    $createdate = date_create($this->input->post('issueDate'));
                    $date = date_format($createdate,'Y-m-d');
                    $materialIssueArr = array(
                        'issue_no' => $this->input->post('issueNo'),
                        'date' => $date,
                        'issue_by'=>$this->session->userdata('id'),
                        'material_id'   => $this->input->post('MaterialName'),
                        'project_id'   => $this->input->post('project_name'),
                        'quantity' => $this->input->post('IssueQuantity'),
                        'material_image'   =>  $issue_image,
                        'consumption_place'   => $this->input->post('sites'),
                        'consumption_outsite_project_id'   => ($this->input->post('sites') == "outsite")?$this->input->post('Projects'):'',
                        'issue_comment'   => $this->input->post('issueComment')
                    );
                    
                    $materialIssueId = $this->MaterialIssueModel->save($materialIssueArr);
                    
                    if($materialIssueId){
                         $this->session->set_flashdata('success', 'Material Issue Added Successfully!');
                        redirect(base_url('admin/MaterialIssue'));
                    }
                }else{
                    $this->session->set_flashdata('error', 'Please enter quantity more than zero');
                }
            }
        }
        // $data['materialCategory'] = $this->MaterialCategory_model->get_active_material_category();
        
        $data['ActiveProjects'] = $this->Project_model->get_active_projects();
        $data['materialCategory'] = $this->MaterialCategory_model->get_all_material_category();
        $data['title'] = 'Add Material Issue';
        $data['menu_title'] = 'Issue Log';
        $data['page'] = 'matirealIssue/insertMaterialIssue';
        $this->load->view('includes/template', $data);
    }
    public function editIssueLog($id)
    {
        $data = $this->data;
        $data['result'] = $this->MaterialIssueModel->get_materialIssue_by_id($id);
         
        if (isset( $_POST['submit']) || isset($_POST['verify'])){

            $Issuefile = $_FILES["Issuefile"];
            
            $this->form_validation->set_rules('issueNo', 'Issue No', 'trim|required');    
            $this->form_validation->set_rules('issueDate', 'Date', 'trim|required');
            $this->form_validation->set_rules('MaterialName', 'Material Name', 'trim|required');
            $this->form_validation->set_rules('IssueQuantity', 'Quantity', 'required');
            $this->form_validation->set_rules('materialCategory', 'Material Category', 'required');
            $this->form_validation->set_rules('sites', 'Consumption Place', 'required');
            // $this->form_validation->set_rules('issueComment', 'Issue Comment', 'required');  
            
            if ($this->form_validation->run() == false ) {
                $this->session->set_flashdata( 'error', 'Sorry,  Error while adding Material Issue details.' );
                redirect( base_url( 'admin/MaterialIssue/addIssueLog/') );
            }else{

                $material_quantity = $this->input->post('IssueQuantity');

                $valid_quantity = true;

                foreach ($material_quantity as  $value) {
                    if($value <= 0){
                      $valid_quantity = false;
                    }
                }
                if($valid_quantity == true){


                    if (!empty($Issuefile['name'])) {
                        $Issuefile = $_FILES["Issuefile"]['name'];
                        $fileResult = uploadStaffFile('uploads/MaterialIssue/', 'Issuefile');
                        $issue_image=$fileResult['filePath'];
                    }
                    if (isset($_POST['verify'])) {
                        $log_status = "Verified";
                        $verify_comment = $this->input->post('verifyComment');
                    }
                    else 
                    {
                        $verify_comment = '';
                        $log_status = "Issued";
                    }

                    // delete existing challan image work
                    $uploaded_material_img="";
                    if($_FILES['Issuefile']['name'] != "")
                    {
                        $uploaded_material_img=$issue_image;
                        if (file_exists('./uploads/MaterialIssue/'.$data['result']->material_image))
                        {
                            unlink('./uploads/MaterialIssue/'.$data['result']->material_image);
                            
                        }    
                    }
                    else if(!empty($data['result']))
                    {
                        $uploaded_material_img=$data['result']->material_image;
                    }

                    $createdate = date_create($this->input->post('issueDate'));
                    $date = date_format($createdate,'Y-m-d');
                    $materialIssueArr = array(
                        'issue_no' => $this->input->post('issueNo'),
                        'date' => $date,
                        'issue_by'=>$this->session->userdata('id'),
                        'material_id'   => $this->input->post('MaterialName'),
                        'quantity' => $this->input->post('IssueQuantity'),
                        'material_image'   =>  $uploaded_material_img,
                        'consumption_place'   => $this->input->post('sites'),
                        'consumption_outsite_project_id'   => ($this->input->post('sites') == "outsite")?$this->input->post('Projects'):'',
                        'issue_comment'   => $this->input->post('issueComment'),
                        'status'=>$log_status,
                        'verify_comment' => $verify_comment
                    );
                    $materialIssueId = $this->MaterialIssueModel->update('material_issue_log', array('id' => $id), $materialIssueArr);
                    // if($materialIssueId){
                        $this->session->set_flashdata('success', 'Material Issue updated Successfully!');
                        redirect(base_url('admin/MaterialIssue'));
                    // }
                }else{
                    $this->session->set_flashdata('error', 'Please enter quantity more than zero');
                }
            }
        }
        $data['materialCategory'] = $this->MaterialCategory_model->get_active_material_category();

       $issue = 0;
       $entry = 0;

        if(isset($data['result']->material_id) && isset($data['result']->project_id)){
            $project_id = $data['result']->project_id;
            $material_id = $data['result']->material_id;
            $result = $this->MaterialIssueModel->getMaterialIssueQuantitybyProjectId($project_id, $material_id);
            $entryResult = $this->MaterialLog_model->getMaterialEntryQuantitybyProjectId($project_id, $material_id);
            
            if(count($issue) > 0){
                $issue = $result->issueQuantity;
            }
            if(count($entryResult) > 0){
                $entry = $entryResult->entryQuantity;
            }
        }

        $totalQuantity =  $entry - $issue;
         
        $data['totalQuantity'] = $totalQuantity;
         
        $data['ActiveProjects'] = $this->Project_model->get_active_projects();
        $data['title'] = 'Material Issue edit Data';
        $data['menu_title'] = 'Issue Log';
        $data['page'] = 'matirealIssue/editMaterialIssue';
        $this->load->view('includes/template', $data);
    }
    public function ajax_delete($id) {

        $materialIssueStatus = array(
                    'status'   => "Deleted",
                    'is_deleted' => "1"
                );
        
        $materialLogId = $this->MaterialIssueModel->update('material_issue_log', array('id' => $id), $materialIssueStatus);
        
        redirect(base_url('admin/materialIssue/index'));
        // $this->MaterialLog_model->delete('material_entry_log', 'id', $id);
        // $this->MaterialLog_model->delete('material_entry_logdetail', 'material_entry_log_id', $id);
        $this->session->set_flashdata('success', 'Material Issue Log Deleted Successfully');
    }
    public function getMaterialIssueQuantity(){
       
        $material_id = $this->input->get('material_id');
        $project_id = $this->input->get('project_id');
        
        $issue = $this->MaterialIssueModel->getMaterialIssueQuantitybyProjectId($project_id, $material_id);
       
        $entry = $this->MaterialLog_model->getMaterialEntryQuantitybyProjectId($project_id, $material_id);
        
        $entryQuantity = '';
        $issueQuantity = '';
        
        $entryQuantity = isset($entry->entryQuantity)? $entry->entryQuantity : '';
        $issueQuantity = isset($issue->issueQuantity)? $issue->issueQuantity : '';
        
        $quantity = $entryQuantity - $issueQuantity; 
        if($quantity > 0){
            echo json_encode([
                        'status'=> true, 
                        'quantity' => $quantity
                    ]);
        }else{
            echo json_encode([
                        'status'=> false, 
                        'quantity' => $quantity
                    ]);
        }
        exit();
    }
    public function getProjectMaterialAjax(){
        $material = array();
        if(isset($_GET['project_id']) && isset($_GET['category_id'])){
            if(!empty($_GET['project_id']) && !empty($_GET['category_id'])){
                $project_id = $_GET['project_id'];
                $category_id = $_GET['category_id'];
                // $material = $this->MaterialIssueModel->getMaterialAjax($project_id, $category_id);
                $material = $this->MaterialLog_model->getMaterialByCategory($category_id, $project_id);
                if(count($material) > 0 && !empty($material)){
                    echo json_encode([
                        'status'=> true, 
                        'material' => $material
                    ]); 
                } else{
                    echo json_encode([
                        'status'=> false, 
                        'material' => $material
                    ]); 
                }
                exit();
            }
        }
    }
}