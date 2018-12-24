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
        $this->load->model('Material_model');
        $this->load->model('Admin_model', 'admin');
        $this->load->model('Foreman_model', 'foreman');        
        checkAdmin();
    }
    public function index() {
        if($this->session->userdata('user_designation') == 'Supervisor'){
            $data['projects'] = $this->foreman->get_project_foremanId($this->session->userdata('id'));
        }
        else
        {
            $data['projects'] = $this->Project_model->get_active_projects();
        }
        $data['title'] = 'Material Issue';
        $data['menu_title'] = 'Issue Log';
        $data['materialIssue'] = $this->MaterialIssueModel->getIssueLog();
        $data['page'] = 'matirealIssue/matirealIssueIndex';

        $this->load->view('includes/template', $data);
    }
    public function addIssueLog(){
        if($this->input->post()){
            $Issuefile = $_FILES["Issuefile"];
            
            // $this->form_validation->set_rules('issueNo', 'Issue No', 'trim|required');    
            $this->form_validation->set_rules('issueDate', 'Date', 'trim|required');
            $this->form_validation->set_rules('MaterialName', 'Material Name', 'trim|required');
            $this->form_validation->set_rules('IssueQuantity', 'Quantity', 'required');
            $this->form_validation->set_rules('materialCategory', 'Material Category', 'required');
            $this->form_validation->set_rules('sites', 'Consumption Place', 'required');
            $this->form_validation->set_rules('project_name', 'Project Name', 'required');
            if($this->session->userdata('user_designation') != 'Supervisor')
            {
                $this->form_validation->set_rules('supervisor_name', 'Supervisor name', 'required');
            }
            if ($this->form_validation->run() == false ) {
                
                $this->session->set_flashdata( 'error', 'Sorry,  Error while adding Material Issue details.' );
                // redirect( base_url( 'admin/MaterialIssue/addIssueLog/') );
            }else{

                // auto generate issue no work start
                $issueData=$this->MaterialIssueModel->getMaxIssueDetail();
                $materialData=$this->Material_model->get_by_id($this->input->post('MaterialName'));
                $projectData=$this->Project_model->get_by_id($this->input->post('project_name'));
                if(count($issueData) > 0)
                {
                    $issueNo=substr($issueData->issue_no,4)+1;    
                }
                else
                {
                    $issueNo=1;
                }
                $autoIssueNum=strtoupper(substr($projectData->project_name,0,2)).strtoupper(substr($materialData->name, 0, 2)).str_pad($issueNo, 3, '0', STR_PAD_LEFT);
                // auto generate issue no work end

                $material_quantity = $this->input->post('IssueQuantity');

                $valid_quantity = true;

                
                if($material_quantity <= 0){
                  $valid_quantity = false;
                }
                
                if($valid_quantity == true){


                    $issue_image=$Issuefile['name'];
                    if (!empty($issue_image)) {
                        $Issuefile = $_FILES["Issuefile"]['name'];
                        $fileResult = uploadStaffFile('uploads/MaterialIssue/', 'Issuefile');
                        $issue_image=$fileResult['filePath'];
                    }
                    // if($this->session->userdata('user_designation') == 'admin')
                    // {
                    //     $log_status = "Verified";
                    //     $verify_comment = $this->input->post('verifyComment');
                    // }
                    // else 
                    {
                        $log_status = "Issued";
                        $verify_comment = "";
                    }
                    
                    $createdate = date_create($this->input->post('issueDate'));
                    $date = date_format($createdate,'Y-m-d');

                    if($this->session->userdata('user_designation') == 'Supervisor')
                    {
                        $supervisor_name = $this->session->userdata('id');
                    }
                    else
                    {
                        $supervisor_name = $this->input->post('supervisor_name');
                    }

                    $materialIssueArr = array(
                        'issue_no' => $autoIssueNum,
                        'date' => $date,
                        'issue_by'=>$this->session->userdata('id'),
                        'supervisor_id' =>$supervisor_name,
                        'material_id'   => $this->input->post('MaterialName'),
                        'project_id'   => $this->input->post('project_name'),
                        'company_id' => $this->session->userdata('company_id'),
                        'quantity' => $this->input->post('IssueQuantity'),
                        'material_image'   =>  $issue_image,
                        'consumption_place'   => $this->input->post('sites'),
                        'consumption_outsite_project_id'   => ($this->input->post('sites') == "outsite")?$this->input->post('Projects'):'',
                        'issue_comment'   => $this->input->post('issueComment'),
                        'status'=>$log_status,
                        'verify_comment' => $verify_comment
                    );
                    
                    $materialIssueId = $this->MaterialIssueModel->save($materialIssueArr);
                    
                    if($materialIssueId){
                        if($this->session->userdata('user_designation') == 'Supervisor')
                        {
                            // $this->send_issue_mail($materialIssueId);
                            $this->load->library("sendMailLog");
                            $this->sendmaillog->send_issueLog_mail($materialIssueId,$this->session->userdata('id'));
                        }
                        $this->session->set_flashdata('success', 'Material Issue Added Successfully!');
                        redirect(base_url('admin/MaterialIssue'));
                    }
                }else{
                    $this->session->set_flashdata('error', 'Please enter quantity more than zero');
                }
            }
        }
        // $data['materialCategory'] = $this->MaterialCategory_model->get_active_material_category();
        if($this->session->userdata('user_designation') == 'Supervisor'){
            $data['ActiveProjects'] = $this->foreman->get_project_foremanId($this->session->userdata('id'));
        }
        else
        {
            $data['ActiveProjects'] = $this->Project_model->get_active_projects();
        }
        $data['materialCategory'] = $this->MaterialCategory_model->get_active_material_category();
        $data['title'] = 'Add Material Issue';
        $data['menu_title'] = 'Issue Log';
        $data['page'] = 'matirealIssue/insertMaterialIssue';
        $this->load->view('includes/template', $data);
    }
    public function editIssueLog($id)
    {
        $data = $this->data;
        $data['result'] = $this->MaterialIssueModel->get_materialIssue_by_id($id);

        $issue = 0;
        $entry = 0;
        $totalQuantity = 0;

        if(isset($data['result']->material_id) && isset($data['result']->project_id)){
            $project_id = $data['result']->project_id;
            $material_id = $data['result']->material_id;
            $result = $this->MaterialIssueModel->getMaterialIssueQuantitybyProjectId($project_id, $material_id,$this->session->userdata('company_id'));
            $entryResult = $this->MaterialLog_model->getMaterialEntryQuantitybyProjectId($project_id, $material_id,$this->session->userdata('company_id'));
            
             // if(count($issue) > 0){
            if($result->issueQuantity > 0){
                $issue = $result->issueQuantity;
            }
            // if(count($entryResult) > 0){
            if($entryResult->entryQuantity > 0){
                $entry = $entryResult->entryQuantity;
            }
        }
       
        $totalQuantity =  $entry - $issue;
        $data['totalQuantity'] = $totalQuantity;
         
        if (isset( $_POST['submit']) || isset($_POST['verify'])){

            $Issuefile = $_FILES["Issuefile"];
            
            // $this->form_validation->set_rules('issueNo', 'Issue No', 'trim|required');    
            $this->form_validation->set_rules('issueDate', 'Date', 'trim|required');
            $this->form_validation->set_rules('MaterialName', 'Material Name', 'trim|required');
            $this->form_validation->set_rules('IssueQuantity', 'Quantity', 'required');
            $this->form_validation->set_rules('materialCategory', 'Material Category', 'required');
            $this->form_validation->set_rules('sites', 'Consumption Place', 'required');
            $this->form_validation->set_rules('project_name', 'Project Name', 'required');
            if($this->session->userdata('user_designation') != 'Supervisor')
            {
                $this->form_validation->set_rules('supervisor_name', 'Supervisor name', 'required');
            }
            // $this->form_validation->set_rules('issueComment', 'Issue Comment', 'required');  
            
            if($this->form_validation->run() == false ){
                $this->session->set_flashdata( 'error', 'Sorry,  Error while adding Material Issue details.' );
                redirect( base_url( 'admin/MaterialIssue/editIssueLog/') );
            }else{

                $material_quantity = $this->input->post('IssueQuantity');
                $valid_quantity = true;
                $quantity_invalid = true; 

                if($material_quantity <= 0){
                    $valid_quantity = false;
                }else if( $material_quantity > $totalQuantity){
                    $quantity_invalid = false;
                }
                 
                if($valid_quantity == true && $quantity_invalid == true){

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
                        if(trim($data['result']->material_image) != "")
                        {
                            if(file_exists(ROOT_PATH.'/uploads/MaterialIssue/'.$data['result']->material_image))
                            {
                                unlink('./uploads/MaterialIssue/'.$data['result']->material_image);
                            }
                        }    
                    }
                    else if(!empty($data['result']))
                    {
                        $uploaded_material_img=$data['result']->material_image;
                    }

                    $createdate = date_create($this->input->post('issueDate'));
                    $date = date_format($createdate,'Y-m-d');
                    if($this->session->userdata('user_designation') == 'Supervisor')
                    {
                        $supervisor_name = $this->session->userdata('id');
                    }
                    else
                    {
                        $supervisor_name = $this->input->post('supervisor_name');
                    }

                    $materialIssueArr = array(
                        // 'issue_no' => $this->input->post('issueNo'),
                        'date' => $date,
                        'issue_by'=>$this->session->userdata('id'),
                        'supervisor_id' => $supervisor_name,
                        'material_id'   => $this->input->post('MaterialName'),
                        'project_id'   => $this->input->post('project_name'),
                        'company_id' => $this->session->userdata('company_id'),
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
                        if($this->session->userdata('user_designation') == 'admin')
                        {
                            // $this->send_issue_mail($id);
                            $this->load->library("sendMailLog");
                            $this->sendmaillog->send_issueLog_mail($id,$this->session->userdata('id'));
                        }
                        $this->session->set_flashdata('success', 'Material Issue updated Successfully!');
                        redirect(base_url('admin/MaterialIssue'));
                    // }
                }else{
                    if($valid_quantity == false){
                        $this->session->set_flashdata('error', 'Please enter quantity more than zero.');
                    } else{
                        $this->session->set_flashdata('error', 'Invalid your entered quantity.');
                    }
                }
            }
        }
        $data['materialCategory'] = $this->MaterialCategory_model->get_active_material_category();
        if($this->session->userdata('user_designation') == 'Supervisor'){
            $data['ActiveProjects'] = $this->foreman->get_project_foremanId($this->session->userdata('id'));
        }
        else
        {
            $data['ActiveProjects'] = $this->Project_model->get_active_projects();
        }
        $data['supervisors'] = $this->MaterialLog_model->getProjectSupervisor($data['result']->project_id);

        $data['title'] = 'Material Issue edit Data';
        $data['menu_title'] = 'Issue Log';
        $data['page'] = 'matirealIssue/editMaterialIssue';
        $this->load->view('includes/template', $data);
    }
    public function issueDetail($id)
    {
        $data = $this->data;
        $data['result'] = $this->MaterialIssueModel->get_materialIssue_by_id($id);

        $issue = 0;
        $entry = 0;
        $totalQuantity = 0;

        if(isset($data['result']->material_id) && isset($data['result']->project_id)){
            $project_id = $data['result']->project_id;
            $material_id = $data['result']->material_id;
            $result = $this->MaterialIssueModel->getMaterialIssueQuantitybyProjectId($project_id, $material_id,$this->session->userdata('company_id'));
            $entryResult = $this->MaterialLog_model->getMaterialEntryQuantitybyProjectId($project_id, $material_id,$this->session->userdata('company_id'));
            
             // if(count($issue) > 0){
            if($result->issueQuantity > 0){
                $issue = $result->issueQuantity;
            }
            // if(count($entryResult) > 0){
            if($entryResult->entryQuantity > 0){
                $entry = $entryResult->entryQuantity;
            }
        }
       
        $totalQuantity =  $entry - $issue;
        $data['totalQuantity'] = $totalQuantity;
         
        $data['materialCategory'] = $this->MaterialCategory_model->get_active_material_category();
        if($this->session->userdata('user_designation') == 'Supervisor'){
            $data['ActiveProjects'] = $this->foreman->get_project_foremanId($this->session->userdata('id'));
        }
        else
        {
            $data['ActiveProjects'] = $this->Project_model->get_active_projects();
        }
        $data['supervisors'] = $this->MaterialLog_model->getProjectSupervisor($data['result']->project_id);

        $data['title'] = 'Material Issue Detail';
        $data['menu_title'] = 'Issue Log Detail';
        $data['page'] = 'matirealIssue/detailMaterialIssue';
        $this->load->view('includes/template', $data);
    }
    public function send_issue_mail($issue_log_id){
        
        $result = $this->MaterialIssueModel->get_materialIssue_detail($issue_log_id,$this->session->userdata('company_id'));
        
        if($this->session->userdata('user_designation') == 'admin')
        {
            $receiverDetails=array("user_email"=>$result->supervisor_email,
                                   "user_name" =>  $result->supervisor_name
                                );

            $content = "<p>New material issue has been approved by admin.</p>";
        }
        else
        {
            $receiverDetails = $this->MaterialLog_model->get_company_admin($this->session->userdata('company_id'));
            $content = "<p>New material issue has been maid by supervisor.</p>";
        }
        // $result_detail = $this->MaterialLog_model->get_materiallog_detail_by_id($entry_log_id);
        // echo "<pre>";
        // print_r($result);
        // print_r($receiverDetails);
        // exit;
        if(count($receiverDetails) > 0)
        {
            $subject = "Material Issue | The Hajiri App";
            
            $content .= "<p><b><u>Material Issue Detail:</u></b></p>";
            
            $content .= "<p><b>Issue Date: </b>".$result->date."</p>";
            $content .= "<p><b>Issue No: </b>".$result->issue_no."</p>";
            
            $content .= "<p>For more detail:</p>";
            $content .= "<p><a href='" . base_url('admin/MaterialIssue/issueDetail/').$result->id. "' >Please click here</a></p>";
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
            foreach ($receiverDetails as $key => $value) {
                $mail->AddAddress($value->user_email,$value->user_name);
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
        
        $issue = $this->MaterialIssueModel->getMaterialIssueQuantitybyProjectId($project_id, $material_id,$this->session->userdata('company_id'));
       
        $entry = $this->MaterialLog_model->getMaterialEntryQuantitybyProjectId($project_id, $material_id,$this->session->userdata('company_id'));
        
        $entryQuantity = 0;
        $issueQuantity = 0;
        
        $entryQuantity = isset($entry->entryQuantity)? $entry->entryQuantity : 0;
        $issueQuantity = isset($issue->issueQuantity)? $issue->issueQuantity : 0;
        
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
                $material = $this->MaterialLog_model->getMaterialByCategory($category_id, $project_id,$this->session->userdata('company_id'));
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

    public function getFilterDetailAjax($id) {
        
        $getMaterialByProjectId = array();
        $getCompanyAdmin= array();

        $getMaterialByProjectId = $this->Material_model->getProjectMaterial($id);
        $getCompanyAdmin= $this->MaterialIssueModel->companyAdminSupervisor();


        echo json_encode([
            'success'=> true, 
            'getProjectSupervisor' => $getCompanyAdmin,
            'getProjectMaterial' => $getMaterialByProjectId
        ]);  exit();
    }
    public function getFilterSupervisorAjax($id) {
        
        $getProjectSupervisor = array();
        
        $getProjectSupervisor = $this->MaterialLog_model->getProjectSupervisor($id);
        
        echo json_encode([
            'success'=> true, 
            'getProjectSupervisor' => $getProjectSupervisor
        ]);  exit();
    }
    public function materialIssueDatatable()
    {
        // sorting column array
        $columns = array( 
                            0 =>'material_issue_log.issue_no',
                            1 =>'material_issue_log.date',
                            2 => 'issue_by_name',
                            3 => 'category_name',
                            4 => 'material_name',
                            5 => 'quantity',
                            6 => 'status'
                        );
        $limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = $columns[$this->input->post('order')[0]['column']];
        $dir = $this->input->post('order')[0]['dir'];
  
        $totalData = $this->MaterialIssueModel->allMaterialIssue_count();
            
        $totalFiltered = $totalData; 
        $where=null;
       
        $where = '(material_issue_log.date between "'.$this->input->post('dateStartRange').'"  and  "'.$this->input->post('dateEndRange').'")';
        if(!empty($this->input->post('search')['value']))
        {            
            if($where != null){
                $where.= ' AND ';
            }
            $where .= '(material_issue_log.issue_no LIKE "'.$this->input->post('search')['value'].'%" or ';
            
            $where .= 'material_issue_log.status LIKE "'.$this->input->post('search')['value'].'%" or ';
            $where .= 'categories.name LIKE "'.$this->input->post('search')['value'].'%" or ';
            $where .= 'materials.name LIKE "'.$this->input->post('search')['value'].'%" or ';
            $where .= 'material_issue_log.quantity LIKE "'.$this->input->post('search')['value'].'%" or ';
             $where .= 'material_issue_log.status LIKE "'.$this->input->post('search')['value'].'%" or ';
            $where .= 'concat(user.user_name," ",user.user_last_name) LIKE "'.$this->input->post('search')['value'].'%" )';// supervisor_name            
        }

        
        if(!empty($this->input->post('project')))
        {   
            if($where == null)
            $where .= 'material_issue_log.project_id = "'.$this->input->post('project').'"';
            else
            $where .= ' AND material_issue_log.project_id = "'.$this->input->post('project').'"';
        }
        if(!empty($this->input->post('material')))
        {   
            if($where == null)
            $where .= 'material_issue_log.material_id = "'.$this->input->post('material').'"';
            else
            $where .= ' AND material_issue_log.material_id = "'.$this->input->post('material').'"';
        }
        if(!empty($this->input->post('supervisor')))
        {   
            if($where == null)
            $where .= 'material_issue_log.issue_by = "'.$this->input->post('supervisor').'"';
            else
            $where .= ' AND material_issue_log.issue_by = "'.$this->input->post('supervisor').'"';
        }
        if(!empty($this->input->post('status')))
        {   
            if($where == null)
            $where .= 'material_issue_log.status = "'.$this->input->post('status').'"';
            else
            $where .= ' AND material_issue_log.status = "'.$this->input->post('status').'"';
        }
    
        if($where == null)
        {            
            $issues = $this->MaterialIssueModel->allMaterialIssue($limit,$start,$order,$dir);
        }
        else {                

            $issues =  $this->MaterialIssueModel->materialIssue_custom_search($limit,$start,$where,$order,$dir);

            $totalFiltered = $this->MaterialIssueModel->materialIssue_custom_search_count($where);
        }

        $data = array();
        if(!empty($issues))
        {   
            foreach ($issues as $issue)
            {   
                
                $nestedData['issue_no'] = $issue->issue_no;
                $nestedData['date'] = $issue->date;
                $nestedData['issue_by_name'] = $issue->issue_by_name;
                
                $nestedData['category_name'] = $issue->category_name;
                $nestedData['material_name'] = $issue->material_name;
                $nestedData['quantity'] = $issue->quantity;
                $nestedData['status'] = $issue->status;

                
                //Edit Action                   
               
                $nestedData['action'] = '<a class="btn btn-sm btn-primary" href="'.base_url('admin/MaterialIssue/editIssueLog/') . $issue->id.'" title="Edit material issue">
                                        <i class="glyphicon glyphicon-pencil"></i> </a>  
                                        ';

                if($issue->status !== 'Verified') { 
                    $nestedData['action'] .= '<button class="btn btn-sm btn-danger" title="Delete material issue" onclick="material_issue_log_delete('.$issue->id.')">
                                            <i class="glyphicon glyphicon-trash"></i> 
                                        </button>';
                }     
                else if($issue->status == 'Verified' && $this->session->userdata('user_designation') == 'admin') { 
                    $nestedData['action'] .= '<button class="btn btn-sm btn-danger" title="Delete material issue" onclick="material_issue_log_delete('.$issue->id.')">
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