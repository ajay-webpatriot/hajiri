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
        $this->load->model('Foreman_model', 'foreman');      
        checkAdmin();
    }

    public function index(){
        $data = $this->data;
        if($this->session->userdata('user_designation') == 'Supervisor'){
            $data['projects'] = $this->foreman->get_project_foremanId($this->session->userdata('id'));
        }
        else
        {
            $data['projects'] = $this->project->get_active_projects();
        }

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
            $this->form_validation->set_rules('supplier_name', 'Supplier name', 'required');
            $this->form_validation->set_rules('material_category[]', 'Material category', 'required');
            $this->form_validation->set_rules('material_name[]', 'Material name', 'required');
            $this->form_validation->set_rules('quantity[]', 'Quantity', 'required');
            if($this->session->userdata('user_designation') != 'Supervisor')
            {
                $this->form_validation->set_rules('supervisor_name', 'Supervisor name', 'required');
            }
            // if($this->session->userdata('user_designation') == 'admin'){
            //     $this->form_validation->set_rules('rate[]', 'Rate', 'required');
            // }
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
                    if($this->session->userdata('user_designation') == 'Supervisor')
                    {
                        $supervisor_name = $this->session->userdata('id');
                    }
                    else
                    {
                        $supervisor_name = $this->input->post('supervisor_name');
                    }
                    $material_category = $this->input->post('material_category');
                    $material_name = $this->input->post('material_name');
                    $quantity = $this->input->post('quantity');
                    // resolved image issue s
                    // $challan_image = $_FILES['challan_file']['name'];
                    // resolved image issue e
                    // if($this->session->userdata('user_designation') == 'admin')
                    // {
                    //     $log_status = "Approved";
                    //     $comment = $this->input->post('comment');
                    // }
                    // else 
                    {
                        $log_status = "Pending";
                        $comment = "";
                    }
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
                        'company_id' => $this->session->userdata('company_id'),
                        'comment' => $comment,
                        'status'   => $log_status
                    );
                    $materialLogId = $this->MaterialLog_model->materialLogsave($materialLogArr);
                
                    // insert material log detail
                    if($materialLogId){

                        $material_name=$this->input->post('material_name');
                        $material_quantity=$this->input->post('quantity');
                        $material_rate = $this->input->post('rate');
                        $material_amount=$this->input->post('amount');

                        $cntDetail=0;

                        foreach ($material_category as $key => $value) {

                            // if($this->session->userdata('user_designation') == 'admin'){
                            //     $rate = $material_rate[$cntDetail];
                            //     $total_rate = $material_amount[$cntDetail];
                            // }
                            // else
                            {
                                $rate = 0;
                                $total_rate = 0;
                            } 
                           $materialLogDetailArr = array(
                            'material_entry_log_id' => $materialLogId,
                            'material_id'   => $material_name[$cntDetail],
                            'quantity' => $material_quantity[$cntDetail],
                            'material_image'  => empty($material_image_arr[$cntDetail])?"":$material_image_arr[$cntDetail],
                            'rate'   => $rate,
                            'total_rate'   => $total_rate,
                            );

                            $insertId = $this->MaterialLog_model->materialLogDetailsave($materialLogDetailArr);
                            $cntDetail++;
                        }
                    }
                    if($insertId){
                        if($this->session->userdata('user_designation') == 'Supervisor')
                        {
                            // $this->send_log_mail($materialLogId);
                            $this->load->library("sendMailLog");
                            $this->sendmaillog->send_entryLog_mail($materialLogId,$this->session->userdata('id'));
                        }   
                        $this->session->set_flashdata('success', 'Material Log Added Successfully!');
                        redirect(base_url('admin/MaterialLog/index'));
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
        if($this->session->userdata('user_designation') == 'Supervisor'){
            $data['projects'] = $this->foreman->get_project_foremanId($this->session->userdata('id'));
        }
        else
        {
            $data['projects'] = $this->project->get_active_projects();
        }
        // $data['supervisor'] = $this->MaterialLog_model->getProjectSupervisor(90);
        
        $data['title'] = 'Material Entry';
        $data['page'] = 'MaterialLog/materialLog_add';
        $this->load->view('includes/template', $data);
    }
    public function ajax_delete($id) {

        $materialLogStatus = array(
                    'status'   => "Deleted",
                    'is_deleted'   => "1",
                );
        
        $materialLogId = $this->MaterialLog_model->update('material_entry_log', array('id' => $id), $materialLogStatus);
        
        redirect(base_url('admin/MaterialLog/index'));
        // $this->MaterialLog_model->delete('material_entry_log', 'id', $id);
        // $this->MaterialLog_model->delete('material_entry_logdetail', 'material_entry_log_id', $id);
        $this->session->set_flashdata('success', 'Material Entry Log Deleted Successfully');
    }
    public function getmaterialAjax() { 
        
        $result = array();
        if(isset($_GET['category_id']) && isset($_GET['project_id'])){
            $category_id = $_GET['category_id'];
            $project_id = $_GET['project_id'];
            $result = $this->MaterialLog_model->getMaterialByCategory($category_id, $project_id,$this->session->userdata('company_id'));
        }
        if(count($result) > 0) {
            echo json_encode([
                'status'=> true, 
                'material' => $result,
            ]);
        }else{
            echo json_encode([
                'status'=> false, 
                'material' => $result,
            ]);
        }
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
    public function getSupplierCategoryAjax($supplier_id,$project_id){
        $getCategoryByProjectId = array();
        $getCategoryByProjectId = $this->MaterialCategory_model->get_active_material_category_byProject($supplier_id,$project_id,$this->session->userdata('company_id'));
        echo json_encode([
            'success'=> true, 
            'getProjectCategory' => $getCategoryByProjectId
        ]);  exit();
    }
    public function entryDetail($id) {

        $data = $this->data;
        $result = $this->MaterialLog_model->get_materiallog_by_id($id);
        $data['result'] = $result;

        $result_detail = $this->MaterialLog_model->get_materiallog_detail_by_id($id);
        $data['result_detail'] = $result_detail;

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
            $getCategoryByProjectId = $this->MaterialCategory_model->get_active_material_category_byProject($supplier_id,$project_id,$this->session->userdata('company_id'));
        }
        
        $data['material_category'] = $getCategoryByProjectId;
        $data['material'] = $this->Material_model->get_active_material();
        $data['supplier'] = $getSupplierByProjectId ;
        if($this->session->userdata('user_designation') == 'Supervisor'){
            $data['projects'] = $this->foreman->get_project_foremanId($this->session->userdata('id'));
        }
        else
        {
            $data['projects'] = $this->project->get_active_projects();
        }

        $data['title'] = 'Material Entry Detail';
        $data['description'] = 'Material Entry Detail';
        $data['page'] = 'MaterialLog/materialLog_detail';
        $this->load->view('includes/template', $data);
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
            $this->form_validation->set_rules('project_name', 'Project Name', 'trim|required');
            $this->form_validation->set_rules('supplier_name', 'Supplier name', 'required');
            $this->form_validation->set_rules('material_category[]', 'Material category', 'required');
            $this->form_validation->set_rules('material_name[]', 'Material name', 'required');
            $this->form_validation->set_rules('quantity[]', 'Quantity', 'required');

            if($this->session->userdata('user_designation') != 'Supervisor')
            {
                $this->form_validation->set_rules('supervisor_name', 'Supervisor name', 'required');
            }
            if (isset($_POST['verify'])) {
                $this->form_validation->set_rules('rate[]', 'Rate', 'required');
            }

            if ( $this->form_validation->run() == false ) {
                $this->session->set_flashdata( 'error', 'Sorry,  Error while adding Material details.' );
                redirect( base_url( 'admin/MaterialLog/editEntry/') );
            }else{

                $material_quantity = $this->input->post('quantity');
                $material_quantity_rate = $this->input->post('rate');

                $valid_quantity = true;
                $valid_quantity_rate = true;
                if(!empty($material_quantity)){
                    foreach ($material_quantity as  $value) {
                        if($value <= 0){
                          $valid_quantity = false;
                        }
                    }
                }else{
                    $valid_quantity = false;
                }

                if($this->session->userdata('user_designation') == 'admin')
                {
                    // rate field only accessible to admin
                    if(!empty($material_quantity_rate)){
                        foreach ($material_quantity_rate as $value) {
                           if($value <= 0){
                              $valid_quantity_rate = false;
                            }
                        }
                    }else{
                        $valid_quantity_rate = false;
                    }
                }    
                if($valid_quantity == true && $valid_quantity_rate == true){

                    $challan_image=$_FILES['challan_file']['name'];
                    if (!empty($challan_image)) {
                        $result = uploadStaffFile('uploads/materialLog/challan/', 'challan_file');
                        if ($result['flag'] == 1) {
                            $challan_image = $result['filePath'];
                        } else {
                            $fileError[$fileName] = $result['error'];
                        }
                    }
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
                    if($this->session->userdata('user_designation') == 'Supervisor')
                    {
                        $supervisor_name = $this->session->userdata('id');
                    }
                    else
                    {
                        $supervisor_name = $this->input->post('supervisor_name');
                    }
                    $material_category = $this->input->post('material_category');
                    $material_name = $this->input->post('material_name');
                    $quantity = $this->input->post('quantity');
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
                        if(trim($data['result']->challan_image) != "")
                        {
                            if (file_exists(ROOT_PATH.'/uploads/materialLog/challan/'.$data['result']->challan_image))
                            {
                                unlink('./uploads/materialLog/challan/'.$data['result']->challan_image);
                            }
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
                        'company_id' => $this->session->userdata('company_id'),
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
                        $isExistingMaterial=$this->input->post('isExistingMaterial');

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
                                    if(trim($data['result_detail'][$cntDetail]->material_image) != "")
                                    {
                                        if (file_exists(ROOT_PATH.'/uploads/materialLog/material_image/'.$data['result_detail'][$cntDetail]->material_image))
                                        {
                                            unlink('./uploads/materialLog/material_image/'.$data['result_detail'][$cntDetail]->material_image);
                                        }
                                    } 
                                }
                            }
                            else if(!empty($data['result_detail'][$cntDetail]))
                            {
                                if($isExistingMaterial[$cntDetail] == "true"){
                                    $uploaded_material_img=$data['result_detail'][$cntDetail]->material_image;

                                }
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
                        if($this->session->userdata('user_designation') == 'admin')
                        {
                            // $this->send_log_mail($id);
                            $this->load->library("sendMailLog");
                            $this->sendmaillog->send_entryLog_mail($id,$this->session->userdata('id'));
                        }
                        $this->session->set_flashdata('success', 'Material Log Updated Successfully!');
                        redirect(base_url('admin/MaterialLog/index'));
                    }
                }else{
                    if($valid_quantity == false){
                        $this->session->set_flashdata('error', 'Please enter quantity more than zero');
                    }
                    if($valid_quantity_rate == false){
                        $this->session->set_flashdata('error', 'Please enter quantity rate more than zero');
                    }
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
            $getCategoryByProjectId = $this->MaterialCategory_model->get_active_material_category_byProject($supplier_id,$project_id,$this->session->userdata('company_id'));
        }
        
        // $data['material_category'] = $this->MaterialCategory_model->get_active_material_category();
        $data['material_category'] = $getCategoryByProjectId;

        $data['material'] = $this->Material_model->get_active_material();
        // $data['supplier'] = $this->Supplier_model->get_active_supplier();
        $data['supplier'] = $getSupplierByProjectId ;
        
        if($this->session->userdata('user_designation') == 'Supervisor'){
            $data['projects'] = $this->foreman->get_project_foremanId($this->session->userdata('id'));
        }
        else
        {
            $data['projects'] = $this->project->get_active_projects();
        }
        // $data['supervisor'] = $this->MaterialLog_model->getProjectSupervisor();

        $data['title'] = 'Edit Material Entry';
        $data['description'] = 'Edit Material Entry';
        $data['page'] = 'MaterialLog/materialLog_edit';
        $this->load->view('includes/template', $data);
    }
    public function send_log_mail($entry_log_id){
        
        $result = $this->MaterialLog_model->get_materiallog_detail($entry_log_id,$this->session->userdata('company_id'));
        
        $amount="";
        if($this->session->userdata('user_designation') == 'admin')
        {
            $receiverDetails=array("user_email"=>$result->supervisor_email,
                                   "user_name" =>  $result->supervisor_name
                                );

            $content = "<p>New material entry has been approved by admin.</p>";
            $amount="<p><b>Total amount: </b>".$result->total_rate."</p>";
        }
        else
        {
            $receiverDetails = $this->MaterialLog_model->get_company_admin($this->session->userdata('company_id'));
            $content = "<p>New material entry has been maid by supervisor.</p>";
        }
       
        // $result_detail = $this->MaterialLog_model->get_materiallog_detail_by_id($entry_log_id);
        // echo "<pre>";
        // print_r($result);
        // print_r($receiverDetails);
        // exit;
        if(count($receiverDetails) > 0)
        {
            $subject = "Material Entry | The Hajiri App";
            
            $content .= "<p><b><u>Material Entry Detail:</u></b></p>";
            
            $content .= "<p><b>Challan Date: </b>".$result->challan_date."</p>";
            $content .= "<p><b>Challan No: </b>".$result->challan_no."</p>";
            $content .= $amount;
            $content .= "<p>For more detail:</p>";
            $content .= "<p><a href='" . base_url('admin/MaterialLog/entryDetail/').$result->id. "' >Please click here</a></p>";
            $content .= "<p>Regards,</p>";
            $content .= "<p>Team Aasaan</p>";
            $content .= "<p>http://www.aasaan.co/</p>";
            $content .= "<img src='" . base_url('assets/admin/images/aasaan-footer-logo.jpg') . "' height='80' width='250'/>";

            
            $this->load->library("PHPMailer_Library");
            $mail = $this->phpmailer_library->load();
            $mail->IsSMTP();                              // send via SMTP
            // $mail->Host = "ssl://smtp.zoho.com";
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;                       // turn on SMTP authentication
            // $mail->Username = "hajiri@aasaan.co";        // SMTP username
            $mail->Username = "info.emailtest1@gmail.com";
            // $mail->Password = "hajiriaasaan"; // SMTP password
            $mail->Password = "rwnzucezczusfezs";
            $webmaster_email = "hajiri@aasaan.co";       //Reply to this email ID
                                          // Recipient's name
            $mail->From = $webmaster_email;
            // $mail->Port = 465;
            $mail->Port = 587;
            $mail->FromName = "The Hajiri App";
            if($this->session->userdata('user_designation') == 'admin')
            {
                foreach ($receiverDetails as $key => $value) {
                    $mail->AddAddress($value['user_email'],$value['user_name']);
                }    
            }
            else
            {
                foreach ($receiverDetails as $key => $value) {
                    $mail->AddAddress($value->user_email,$value->user_name);
                }
            }
            $mail->AddAddress("hinal.webpatriot@gmail.com",'aa');
            // $mail->AddReplyTo($webmaster_email,"The Hajiri App");
            $mail->WordWrap = 50;                         // set word wrap
            $mail->IsHTML(true);                          // send as HTML
            $mail->Subject = $subject;
            $mail->Body = $content;  
              
            if(!$mail->Send()){
                return false;
            } else {
                return true;
            }
        }
    }
    public function getFilterDetailAjax($id) {
        
        $getProjectSupervisor = array();
        $getSupplierByProjectId = array();
        
        $getProjectSupervisor = $this->MaterialLog_model->getProjectSupervisor($id);
        $getSupplierByProjectId = $this->Supplier_model->getProjectSupplier($id);
        $getMaterialByProjectId = $this->Material_model->getProjectMaterial($id);
        
        echo json_encode([
            'success'=> true, 
            'getProjectSupervisor' => $getProjectSupervisor,
            'getProjectSupplier' => $getSupplierByProjectId,
            'getProjectMaterial' => $getMaterialByProjectId
        ]);  exit();
    }
    public function materialLogDatatable()
    {
        // sorting column array
        $columns = array( 
                            0 =>'material_entry_log.challan_no',
                            1 =>'material_entry_log.challan_date',
                            2 => 'supervisor_name',
                            3 => 'supplier_name',
                            4 => 'status'
                        );
        $limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = $columns[$this->input->post('order')[0]['column']];
        $dir = $this->input->post('order')[0]['dir'];
  
        $totalData = $this->MaterialLog_model->allMaterialLog_count();
            
        $totalFiltered = $totalData; 
        // $where=null;
        $where = '(material_entry_log.challan_date between "'.$this->input->post('dateStartRange').'"  and  "'.$this->input->post('dateEndRange').'")';
        if(!empty($this->input->post('search')['value']))
        {            
            if($where != null){
                $where.= ' AND ';
            }
            $where .= '(suppliers.name LIKE "'.$this->input->post('search')['value'].'%" or ';
            $where .= 'material_entry_log.challan_no LIKE "'.$this->input->post('search')['value'].'%" or ';
            
            $where .= 'material_entry_log.status LIKE "'.$this->input->post('search')['value'].'%" or ';
            $where .= 'concat(user.user_name," ",user.user_last_name) LIKE "'.$this->input->post('search')['value'].'%" )';// supervisor_name
            
        }

        
        if(!empty($this->input->post('project')))
        {   
            if($where == null)
            $where .= 'material_entry_log.project_id = "'.$this->input->post('project').'"';
            else
            $where .= ' AND material_entry_log.project_id = "'.$this->input->post('project').'"';
        }
        if(!empty($this->input->post('material')))
        {   
            if($where == null)
            $where .= 'material_entry_logdetail.material_id = "'.$this->input->post('material').'"';
            else
            $where .= ' AND material_entry_logdetail.material_id = "'.$this->input->post('material').'"';
        }
        if(!empty($this->input->post('supplier')))
        {   
            if($where == null)
            $where .= 'material_entry_log.supplier_id = "'.$this->input->post('supplier').'"';
            else
            $where .= ' AND material_entry_log.supplier_id = "'.$this->input->post('supplier').'"';
        }
        if(!empty($this->input->post('supervisor')))
        {   
            if($where == null)
            $where .= 'material_entry_log.receiver_id = "'.$this->input->post('supervisor').'"';
            else
            $where .= ' AND material_entry_log.receiver_id = "'.$this->input->post('supervisor').'"';
        }
        if(!empty($this->input->post('status')))
        {   
            if($where == null)
            $where .= 'material_entry_log.status = "'.$this->input->post('status').'"';
            else
            $where .= ' AND material_entry_log.status = "'.$this->input->post('status').'"';
        }
    
        if($where == null)
        {            
            $materialLogs = $this->MaterialLog_model->allMaterialLog($limit,$start,$order,$dir);
        }
        else {                

            $materialLogs =  $this->MaterialLog_model->materialLog_custom_search($limit,$start,$where,$order,$dir);

            $totalFiltered = $this->MaterialLog_model->materialLog_custom_search_count($where);
        }

        $data = array();
        if(!empty($materialLogs))
        {   
            foreach ($materialLogs as $materialLog)
            {   
                
                $nestedData['challan_no'] = $materialLog->challan_no;
                $nestedData['challan_date'] = $materialLog->challan_date;
                $nestedData['supervisor_name'] = $materialLog->supervisor_name;
                
                $nestedData['supplier_name'] = $materialLog->supplier_name;
                $nestedData['status'] = $materialLog->status;

                
                //Edit Action                   
               
                $nestedData['action'] = '<a class="btn btn-sm btn-primary" href="'.base_url('admin/MaterialLog/editEntry/') . $materialLog->id.'" title="Edit material entry">
                                            <i class="glyphicon glyphicon-pencil"></i> </a>  ';


                if(isset($materialLog->status) && $materialLog->status !== 'Approved') { 
                     $nestedData['action'] .= '<button class="btn btn-sm btn-danger" title="Delete material entry" onclick="material_entry_log_delete('.$materialLog->id.')">
                        <i class="glyphicon glyphicon-trash"></i> 
                    </button>';
                } 
                    


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
}