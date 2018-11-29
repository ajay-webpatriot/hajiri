<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class materialLog extends CI_Controller {

    public $data;
	public function __construct() {
        parent::__construct();
		$this->data['menu_title'] = 'Entry Log';
        $this->load->model('Material_model', 'Material_model');
        $this->load->model('MaterialLog_model', 'MaterialLog_model');
        $this->load->model('MaterialCategory_model', 'MaterialCategory_model');
        $this->load->model('Supplier_model', 'Supplier_model');
        $this->load->model('Admin_model', 'admin');
        $this->load->model('project_model', 'project');        
        checkAdmin();
    }

    public function index(){
        $data = $this->data;
        $data['materialLog'] = $this->MaterialLog_model->getMaterialLog();
        $data['title'] = 'Material Entry';
        $data['page'] = 'MaterialLog/materialLog_list';
        $this->load->view('includes/template', $data);
    }
    public function Entry() {
        
        if (isset($_POST['submit'])){

            $this->form_validation->set_rules('challan_date', 'Date', 'trim|required');
            $this->form_validation->set_rules('challan_no', 'challan no', 'trim|required');
            $this->form_validation->set_rules('project_name', 'Project Name', 'trim|required');
            $this->form_validation->set_rules('supplier_name', 'supplier name', 'required');
            $this->form_validation->set_rules('material_category[]', 'material category', 'required');
            $this->form_validation->set_rules('material_name[]', 'material name', 'required');
            $this->form_validation->set_rules('quantity[]', 'quantity', 'required');

            if ( $this->form_validation->run() == TRUE) {

                $material_quantity = $this->input->post('quantity');

                $valid_quantity = true;
                foreach ($material_quantity as  $value) {
                    if($value <= 0){
                      $valid_quantity = false;
                    }
                }
                 
                if($valid_quantity == true){
                 
                    $challan_image=$_FILES['challan_file']['name'];
                    if (!empty($challan_image)) {
                        $result = uploadStaffFile('uploads/materialLog/challan/', 'challan_file');
                        if ($result['flag'] == 1) {
                            $challan_image = $result['filePath'];
                        } else {
                            $fileError[$fileName] = $result['error'];
                        }
                    }
                    // resolved image issue e
                    
                    // material image upload work
                    $cntFile=0;
                    $material_image_arr=array();
                    foreach ($_FILES['material_file']['name'] as $file)
                    {
                        $newfilename ="";
                        if($file != ""){
                            $file_name = $_FILES['material_file']['name'][$cntFile];
                            $file_size =$_FILES['material_file']['size'][$cntFile];
                            $file_tmp =$_FILES['material_file']['tmp_name'][$cntFile];
                            $file_type=$_FILES['material_file']['type'][$cntFile]; 

                            $file_basename = substr($file_name, 0, strripos($file_name, '.')); // get file name
                            $file_ext = substr($file_name, strripos($file_name, '.'));  // get file extention

                            $newfilename = $file_basename."_".time(). $file_ext;
                            $file = "./uploads/materialLog/material_image/".$newfilename;  

                            move_uploaded_file($_FILES['material_file']['tmp_name'][$cntFile],$file);     
                            
                        }
                        array_push($material_image_arr, $newfilename);
                        //File Loading Successfully
                        $cntFile++;
                    }
               
                    $challan_date = $this->input->post('challan_date');
                    $challan_no = $this->input->post('challan_no');
                    $supplier_name = $this->input->post('supplier_name');
                    $project_name = $this->input->post('project_name');
                    $supervisor_name = $this->input->post('supervisor_name');
                    $material_category = $this->input->post('material_category');
                    $material_name = $this->input->post('material_name');
                    $quantity = $this->input->post('quantity');
                    // resolved image issue s
                    // $challan_image = $_FILES['challan_file']['name'];
                    // resolved image issue e

                    $createdate = date_create($challan_date);
                    $date = date_format($createdate,'Y-m-d');
                    // insert material log 
                    $materialLogArr = array(
                        'challan_date' => $date,
                        'challan_no'   => $challan_no,
                        'challan_image' => $challan_image,
                        'supplier_id'   => $supplier_name,
                        'receiver_id'   => $supervisor_name,
                        'project_id'   => $project_name,
                        'status'   => 'Pending',
                    );
                    $materialLogId = $this->MaterialLog_model->materialLogsave($materialLogArr);
                
                    // insert material log detail
                    if($materialLogId){

                        $material_name=$this->input->post('material_name');
                        $material_quantity=$this->input->post('quantity');
                        $cntDetail=0;

                        foreach ($material_category as $key => $value) {
                           $materialLogDetailArr = array(
                            'material_entry_log_id' => $materialLogId,
                            'material_id'   => $material_name[$cntDetail],
                            'quantity' => $material_quantity[$cntDetail],
                            'material_image'  => empty($material_image_arr[$cntDetail])?"":$material_image_arr[$cntDetail],
                            // 'rate'   => '1',
                            // 'total_rate'   => '1',
                            );

                            $insertId = $this->MaterialLog_model->materialLogDetailsave($materialLogDetailArr);
                            $cntDetail++;
                        }
                    }
                    if($insertId){
                        $this->session->set_flashdata('success', 'Material Log Added Successfully!');
                        redirect(base_url('admin/materialLog/index'));
                    }
                }else{
                    $this->session->set_flashdata('error', 'Please enter quantity more than zero');
                }
            }
        }
        
        $data = $this->data;
        // $data['material_category'] = $this->MaterialCategory_model->get_active_material_category(90);
        // echo "<pre>";
        // print_r ($data['material_category']);
        // exit();
        $data['material'] = $this->Material_model->get_active_material();
        // $data['supplier'] = $this->Supplier_model->get_active_supplier();

        $data['projects'] = $this->project->get_active_projects();
        // $data['supervisor'] = $this->MaterialLog_model->getProjectSupervisor(90);
        
        $data['title'] = 'Material Entry';
        $data['page'] = 'MaterialLog/materialLog_add';
        $this->load->view('includes/template', $data);
    }
    public function ajax_delete($id) {

        $materialLogStatus = array(
                    'status'   => "Deleted",
                );
        
        $materialLogId = $this->MaterialLog_model->update('material_entry_log', array('id' => $id), $materialLogStatus);
        
        redirect(base_url('admin/materialLog/index'));
        // $this->MaterialLog_model->delete('material_entry_log', 'id', $id);
        // $this->MaterialLog_model->delete('material_entry_logdetail', 'material_entry_log_id', $id);
        $this->session->set_flashdata('success', 'Material Entry Log Deleted Successfully');
    }
    public function getmaterialAjax($id) { 
       $result = $this->MaterialLog_model->getMaterialByCategory($id);
       echo json_encode($result);
       exit;
    }
    public function getSupervisorAjax($id) {
        
        $getProjectSupervisor = array();
        $getSupplierByProjectId = array();
        
        $getProjectSupervisor = $this->MaterialLog_model->getProjectSupervisor($id);
        $getSupplierByProjectId = $this->Supplier_model->getProjectSupplier($id);
        
        echo json_encode([
            'success'=> true, 
            'getProjectSupervisor' => $getProjectSupervisor,
            'getProjectSupplier' => $getSupplierByProjectId
        ]);  exit();
    }
    public function getSupplierCategoryAjax($supplier_id){
        $getCategoryByProjectId = array();
        $getCategoryByProjectId = $this->MaterialCategory_model->get_active_material_category_byProject($supplier_id);
        echo json_encode([
            'success'=> true, 
            'getProjectCategory' => $getCategoryByProjectId
        ]);  exit();
    }
    public function editEntry($id) {

        $data = $this->data;
        $result = $this->MaterialLog_model->get_materiallog_by_id($id);
        $data['result'] = $result;

        $result_detail = $this->MaterialLog_model->get_materiallog_detail_by_id($id);
        $data['result_detail'] = $result_detail;

        if (isset( $_POST['submit']) || isset($_POST['verify'])){
 
            $this->form_validation->set_rules('challan_date', 'Date', 'trim|required');
            $this->form_validation->set_rules('challan_no', 'challan no', 'trim|required');
            $this->form_validation->set_rules('supplier_name', 'supplier name', 'required');
            $this->form_validation->set_rules('material_category[]', 'material category', 'required');
            $this->form_validation->set_rules('material_name[]', 'material name', 'required');
            $this->form_validation->set_rules('quantity[]', 'quantity', 'required');

            if ( $this->form_validation->run() == false ) {
                $this->session->set_flashdata( 'error', 'Sorry,  Error while adding Material details.' );
                redirect( base_url( 'admin/materialLog/editEntry/') );
            }else{
               
                
                // resolved image issue s
                // $associatedFileNames = array('challan_file');
                $challan_image=$_FILES['challan_file']['name'];
                if (!empty($challan_image)) {
                    $result = uploadStaffFile('uploads/materialLog/challan/', 'challan_file');
                    if ($result['flag'] == 1) {
                        $challan_image = $result['filePath'];
                    } else {
                        $fileError[$fileName] = $result['error'];
                    }
                }
                // resolved image issue e
                // material image upload work
                $cntFile=0;
                $material_image_arr=array();
                foreach ($_FILES['material_file']['name'] as $file)
                {
                    $newfilename ="";    
                    if($file != "")
                    {
                        $file_name = $_FILES['material_file']['name'][$cntFile];
                        $file_size =$_FILES['material_file']['size'][$cntFile];
                        $file_tmp =$_FILES['material_file']['tmp_name'][$cntFile];
                        $file_type=$_FILES['material_file']['type'][$cntFile]; 

                        $file_basename = substr($file_name, 0, strripos($file_name, '.')); // get file name
                        $file_ext = substr($file_name, strripos($file_name, '.'));  // get file extention

                        $newfilename = $file_basename."_".time(). $file_ext;
                        $file = "./uploads/materialLog/material_image/".$newfilename;   
                        move_uploaded_file($_FILES['material_file']['tmp_name'][$cntFile],$file);     
                           
                    }    
                    array_push($material_image_arr, $newfilename); 
                        //File Loading Successfully
                   $cntFile++;
                }
               
                $challan_date = $this->input->post('challan_date');
                $challan_no = $this->input->post('challan_no');
                $supplier_name = $this->input->post('supplier_name');
                $project_name = $this->input->post('project_name');
                $supervisor_name = $this->input->post('supervisor_name');
                $material_category = $this->input->post('material_category');
                $material_name = $this->input->post('material_name');
                $quantity = $this->input->post('quantity');
                // resolved image issue s
                // $challan_image = $_FILES['challan_file']['name'];
                // resolved image issue e 

                $createdate = date_create($challan_date);
                $date = date_format($createdate,'Y-m-d');
                if (isset($_POST['verify'])) {
                    $log_status = "Approved";
                    $comment = $this->input->post('comment');
                }
                else 
                {
                    $comment = '';
                    $log_status = "Pending";
                }

                // delete existing challan image work
                $uploaded_challan_img="";
                if($_FILES['challan_file']['name'] != "")
                {
                    // resolved image issue s
                    $uploaded_challan_img=$challan_image;
                    // resolved image issue e
                    if (file_exists('./uploads/materialLog/challan/'.$data['result']->challan_image))
                    {
                        unlink('./uploads/materialLog/challan/'.$data['result']->challan_image);
                        
                    }    
                }
                else if(!empty($data['result']))
                {
                    $uploaded_challan_img=$data['result']->challan_image;
                }

                // insert material log 
                $materialLogArr = array(
                    'challan_date' => $date,
                    'challan_no'   => $challan_no,
                    'challan_image' => $uploaded_challan_img,
                    'supplier_id'   => $supplier_name,
                    'receiver_id'   => $supervisor_name,
                    'project_id'   => $project_name,
                    'comment' => $comment,
                    'status'   => $log_status,
                );
                
                $materialLogId = $this->MaterialLog_model->update('material_entry_log', array('id' => $id), $materialLogArr);

                // insert material log detail
                if($id){
                    $material_name=$this->input->post('material_name');
                    $material_quantity=$this->input->post('quantity');
                    $material_rate = $this->input->post('rate');
                    $material_amount=$this->input->post('amount');

                    $cntDetail=0;

                    $this->MaterialLog_model->delete('material_entry_logdetail','material_entry_log_id', $id);
                    
                    foreach ($material_category as $key => $value) {
                      if (isset($_POST['verify'])) {
                        $rate = $material_rate[$cntDetail];
                        $total_rate = $material_amount[$cntDetail];
                      }
                      else{
                        $rate = 0;
                        $total_rate = 0;
                      }
                        // delete material image work
                        $uploaded_material_img="";
                        if($_FILES['material_file']['name'][$cntDetail] != "")
                        {
                            $uploaded_material_img=$material_image_arr[$cntDetail];

                            if(!empty($data['result_detail'][$cntDetail])){
                                if (file_exists('./uploads/materialLog/material_image/'.$data['result_detail'][$cntDetail]->material_image))
                                {
                                    unlink('./uploads/materialLog/material_image/'.$data['result_detail'][$cntDetail]->material_image);
                                    
                                } 
                            }
                        }
                        else if(!empty($data['result_detail'][$cntDetail]))
                        {
                            $uploaded_material_img=$data['result_detail'][$cntDetail]->material_image;
                        }
            
                       $materialLogDetailArr = array(
                        'material_entry_log_id' => $id,
                        'material_id'   => $material_name[$cntDetail],
                        'quantity' => $material_quantity[$cntDetail],
                        'material_image'  => $uploaded_material_img,
                        'rate'   => $rate,
                        'total_rate'   => $total_rate,
                        );

                        $insertId = $this->MaterialLog_model->materialLogDetailsave($materialLogDetailArr);
                        $cntDetail++;
                    }
                }
                if($insertId){
                     $this->session->set_flashdata('success', 'Material Log Updated Successfully!');
                    redirect(base_url('admin/materialLog/index'));
                }
            }
        }

        $getProjectSupervisor = array();
        $getSupplierByProjectId = array();

        if(isset($data['result']->project_id) && !empty($data['result']->project_id)) {
            
            $project_id = $data['result']->project_id;

            $getProjectSupervisor = $this->MaterialLog_model->getProjectSupervisor($project_id);
            $getSupplierByProjectId = $this->Supplier_model->getProjectSupplier($project_id);
        }
        $getCategoryByProjectId = array();
        if(isset($data['result']->supplier_id) && !empty($data['result']->supplier_id)){
            $supplier_id = $data['result']->supplier_id;
            $getCategoryByProjectId = $this->MaterialCategory_model->get_active_material_category_byProject($supplier_id);
        }
        
        // $data['material_category'] = $this->MaterialCategory_model->get_active_material_category();
        $data['material_category'] = $getCategoryByProjectId;

        $data['material'] = $this->Material_model->get_active_material();
        // $data['supplier'] = $this->Supplier_model->get_active_supplier();
        $data['supplier'] = $getSupplierByProjectId ;
        
        $data['projects'] = $this->project->get_active_projects();
        // $data['supervisor'] = $this->MaterialLog_model->getProjectSupervisor();

        $data['title'] = 'Edit Material Entry';
        $data['description'] = 'Edit Material Entry';
        $data['page'] = 'materialLog/materialLog_edit';
        $this->load->view('includes/template', $data);
    }
}