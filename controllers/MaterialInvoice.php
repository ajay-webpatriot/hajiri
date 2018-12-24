<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class materialInvoice extends CI_Controller {

    public $data;
	public function __construct() {
        parent::__construct();
		$this->data['menu_title'] = 'Invoice Log';
        $this->load->model('Material_model', 'Material_model');
        $this->load->model('MaterialInvoice_model', 'Invoice_model');
        $this->load->model('MaterialCategory_model', 'MaterialCategory_model');
        $this->load->model('Supplier_model', 'Supplier_model');
        $this->load->model('Admin_model', 'admin');
        $this->load->model('MaterialLog_model');
        $this->load->model('project_model', 'project');
        $this->load->model('Foreman_model', 'foreman');        
        checkAdmin();
    }

    public function index(){
        $data = $this->data;
        $this->session->unset_userdata("log_ids");
        $this->session->unset_userdata("challan_supplier");
        $this->session->unset_userdata("challan_project");
        // $data['materialInvoice'] = $this->Invoice_model->getMaterialInvoice();
        if($this->session->userdata('user_designation') == 'Supervisor'){
            $data['projects'] = $this->foreman->get_project_foremanId($this->session->userdata('id'));
        }
        else
        {
            $data['projects'] = $this->project->get_active_projects();
        }
        $data['title'] = 'Material Invoice';
        $data['materialLog']=array();
        $data['page'] = 'MaterialInvoice/materialInvoice_list';
        $this->load->view('includes/template', $data);
    }
    public function clearChallan()
    {
        $this->session->unset_userdata("log_ids");
        $this->session->unset_userdata("challan_supplier");
        $this->session->unset_userdata("challan_project");

        redirect(base_url('admin/MaterialInvoice/issueInvoice'));
    }
    public function issueInvoice()
    {
        $data = $this->data;
        $data['title'] = 'Material Entry Invoice';
        if($this->session->userdata('user_designation') == 'Supervisor'){
            $data['projects'] = $this->foreman->get_project_foremanId($this->session->userdata('id'));
        }
        else
        {
            $data['projects'] = $this->project->get_active_projects();
        }

        $suppliers=array();
        if($this->session->userdata("challan_project") && $this->session->userdata("challan_supplier"))
        {
            $suppliers = $this->Supplier_model->getProjectSupplier($this->session->userdata("challan_project"));
        }
        $data['suppliers']=$suppliers;
        
        $data['page'] = 'MaterialInvoice/materialInvoiceEntry_list';
        $this->load->view('includes/template', $data);
    }
    public function addInvoiceDetail(){
        $data = $this->data;
        $suppliers=array();
        $supervisors=array();

        if (isset($_POST['submit'])){
            $this->form_validation->set_rules('invoice_date', 'Date', 'trim|required');
            $this->form_validation->set_rules('invoice_no', 'Invoice no', 'trim|required');
            $this->form_validation->set_rules('project_name', 'Project name', 'trim|required');
            $this->form_validation->set_rules('supplier_name', 'Supplier name', 'trim|required');
            $this->form_validation->set_rules('challan_log_id[]', 'Challan no', 'trim|required');
            $this->form_validation->set_rules('total_amount', 'Invoice amount', 'trim|required|numeric');
            $this->form_validation->set_rules('payment_cycle', 'Payment cycle', 'trim|required|numeric');
            $this->form_validation->set_rules('payment_date', 'Payment date', 'trim|required');
            if($this->session->userdata('user_designation') != 'Supervisor')
            {
                $this->form_validation->set_rules('supervisor_name', 'Supervisor name', 'required');
            }
            if ( $this->form_validation->run() == TRUE) {
                $challan_log_id=$this->input->post('challan_log_id[]');

                $invoice_date=date_format(date_create($this->input->post('invoice_date')),'Y-m-d');
                $payment_date=date_format(date_create($this->input->post('payment_date')),'Y-m-d');

                // if($this->session->userdata('user_designation') == 'admin'){
                //     $invoiceStatus="Verified";
                // }
                // else
                {
                    $invoiceStatus="Issued";
                } 
                if($this->session->userdata('user_designation') == 'Supervisor')
                {
                    $supervisor_name = $this->session->userdata('id');
                }
                else
                {
                    $supervisor_name = $this->input->post('supervisor_name');
                } 
                $invoice_data=array(
                                'supplier_id'  => $this->input->post('supplier_name'),
                                'project_id'  => $this->input->post('project_name'),
                                'supervisor_id'   => $supervisor_name,
                                'company_id'   => $this->session->userdata('company_id'),
                                'invoice_no' => $this->input->post('invoice_no'),
                                'invoice_date' => $invoice_date,
                                'total_amount' => $this->input->post('total_amount'),
                                'payment_cycle' => $this->input->post('payment_cycle'),
                                'payment_due_date' => $payment_date,
                                'company_id' => $this->session->userdata('company_id'),
                                'status' => $invoiceStatus
                            );

                $invoice_id = $this->Invoice_model->save('material_invoice', $invoice_data);

                if($invoice_id){
                    foreach ($challan_log_id as  $value) {
                        $invoice_detail=array(
                                'invoice_id' => $invoice_id,
                                'material_entry_log_id'   => $value,
                            );

                        $this->Invoice_model->save('material_invoice_detail',$invoice_detail);
                    }
                    if($this->session->userdata('user_designation') == 'Supervisor')
                    {
                        // $this->send_invoice_mail($invoice_id);
                        $this->load->library("sendMailLog");
                        $this->sendmaillog->send_invoice_mail($invoice_id,$this->session->userdata('id'));
                    }
                    $this->session->set_flashdata('success', 'Material Invoice Added Successfully!');
                        redirect(base_url('admin/MaterialInvoice/index'));
                }else{
                    $this->session->set_flashdata('error', 'Sorry,  Error while adding invoice details.');
                }
                
            }
        }
        else if($this->input->post('log_ids[]'))
        {
            $log_ids=$this->input->post('log_ids[]');
            // if($this->session->userdata('log_ids')){
            //     $session_log_ids=$this->session->userdata('log_ids');
            //     $this->session->set_userdata("log_ids",array_unique(array_merge($log_ids,$session_log_ids)));
            // }
            // else
            {
                $this->session->set_userdata("log_ids", $log_ids);
            }

            // set session to set selected project and supplier of challan
            $this->session->set_userdata("challan_project",$this->input->post('project'));
            $this->session->set_userdata("challan_supplier",$this->input->post('supplier'));
            
            $suppliers = $this->Supplier_model->getProjectSupplier($this->input->post('project'));
            $supervisors=$this->MaterialLog_model->getProjectSupervisor($this->input->post('project'));
        }

        $data['log_ids']=$this->session->userdata('log_ids');
        $data['challanData']=$this->Invoice_model->SelectedChallanDetails($this->session->userdata('log_ids'));
        if($this->session->userdata('user_designation') == 'Supervisor'){
            $data['projects'] = $this->foreman->get_project_foremanId($this->session->userdata('id'));
        }
        else
        {
            $data['projects'] = $this->project->get_active_projects();
        }
        $data['suppliers']=$suppliers;
        $data['supervisors'] = $supervisors;

        $data['title'] = 'Add Invoice';
        $data['page'] = 'MaterialInvoice/materialInvoice_add';
        $this->load->view('includes/template', $data);
    }
    public function editInvoiceDetail($id){

        if (isset($_POST['submit'])){
            $this->form_validation->set_rules('invoice_date', 'Date', 'trim|required');
            $this->form_validation->set_rules('invoice_no', 'Invoice no', 'trim|required');
            $this->form_validation->set_rules('project_name', 'Project name', 'trim|required');
            $this->form_validation->set_rules('supplier_name', 'Supplier name', 'trim|required');
            $this->form_validation->set_rules('challan_log_id[]', 'Challan no', 'trim|required');
            $this->form_validation->set_rules('total_amount', 'Invoice amount', 'trim|required|numeric');
            $this->form_validation->set_rules('payment_cycle', 'Payment cycle', 'trim|required|numeric');
            $this->form_validation->set_rules('payment_date', 'Payment date', 'trim|required');
            if($this->session->userdata('user_designation') != 'Supervisor')
            {
                $this->form_validation->set_rules('supervisor_name', 'Supervisor name', 'required');
            }
            if ( $this->form_validation->run() == TRUE) {
                $challan_log_id=$this->input->post('challan_log_id[]');

                $invoice_date=date_format(date_create($this->input->post('invoice_date')),'Y-m-d');
                $payment_date=date_format(date_create($this->input->post('payment_date')),'Y-m-d');
                if($this->session->userdata('user_designation') == 'admin'){
                    $invoiceStatus="Verified";
                }
                else
                {
                    $invoiceStatus="Issued";
                }
                if($this->session->userdata('user_designation') == 'Supervisor')
                {
                    $supervisor_name = $this->session->userdata('id');
                }
                else
                {
                    $supervisor_name = $this->input->post('supervisor_name');
                }    
                $invoice_data=array(
                                'supplier_id'  => $this->input->post('supplier_name'),
                                'project_id'  => $this->input->post('project_name'),
                                'supervisor_id'   => $supervisor_name,
                                'company_id'   => $this->session->userdata('company_id'),
                                'invoice_no' => $this->input->post('invoice_no'),
                                'invoice_date' => $invoice_date,
                                'total_amount' => $this->input->post('total_amount'),
                                'payment_cycle' => $this->input->post('payment_cycle'),
                                'payment_due_date' => $payment_date,
                                'company_id' => $this->session->userdata('company_id'),
                                'status' => $invoiceStatus
                            );

                $this->Invoice_model->update('material_invoice', array('id' => $id), $invoice_data);

                if($id){

                    $this->Invoice_model->delete('material_invoice_detail','invoice_id', $id);
                    foreach ($challan_log_id as  $value) {
                        $invoice_detail=array(
                                'invoice_id' => $id,
                                'material_entry_log_id'   => $value,
                            );

                        $this->Invoice_model->save('material_invoice_detail',$invoice_detail);
                    }
                    if($this->session->userdata('user_designation') == 'admin')
                    {
                        // $this->send_invoice_mail($id);
                        $this->load->library("sendMailLog");
                        $this->sendmaillog->send_invoice_mail($id,$this->session->userdata('id'));
                    }
                    $this->session->set_flashdata('success', 'Material Invoice Updated Successfully!');
                        redirect(base_url('admin/MaterialInvoice/index'));
                }else{
                    $this->session->set_flashdata('error', 'Sorry,  Error while updating invoice details.');
                }
                
            }
        }
        else if($this->input->post('log_ids[]'))
        {
            $log_ids=$this->input->post('log_ids[]');
            // if($this->session->userdata('log_ids')){
            //     $session_log_ids=$this->session->userdata('log_ids');
            //     $this->session->set_userdata("log_ids",array_unique(array_merge($log_ids,$session_log_ids)));
            // }
            // else
            {
                $this->session->set_userdata("log_ids", $log_ids);
            }
            
            
            
        }
        
        $existingChallan=$this->Invoice_model->getChallanById($id);
        $existingChallanArray=array();
        foreach ($existingChallan as $key => $value) {
            array_push($existingChallanArray, $value->material_entry_log_id);
        }
        if(count($existingChallanArray) > 0)
        {
            if($this->session->userdata('log_ids')){
                $session_log_ids=$this->session->userdata('log_ids');
                $this->session->set_userdata("log_ids",array_unique(array_merge($existingChallanArray,$session_log_ids)));
            }
            else{
                $this->session->set_userdata("log_ids", $existingChallanArray);
            }
        }

        $diff=array_diff($this->session->userdata('log_ids'),$existingChallanArray);

        // echo "<pre>";
        // print_r($existingChallanArray);
        // print_r($this->session->userdata('log_ids'));
        // print_r($diff);
        // exit;
        // print_r($log_ids);
        // print_r($this->session->userdata('log_ids'));exit;

        $data = $this->data;
        $challanData=$this->Invoice_model->SelectedChallanDetails($this->session->userdata('log_ids'));
        $data['invoiceDetail']=$this->Invoice_model->getInvoiceById($id);
        $data['allChallanData']=$challanData;
        $data['existingChallan']=$existingChallanArray;
        $data['newChallan']=$diff;

        $suppliers = array();
        if(isset($data['invoiceDetail']->project_id) && !empty($data['invoiceDetail']->project_id)){
            $suppliers = $this->Supplier_model->getProjectSupplier($data['invoiceDetail']->project_id);
        }

        $data['suppliers'] = $suppliers;

        // set session to set selected project and supplier of challan
        $this->session->set_userdata("challan_project",$data['invoiceDetail']->project_id);
        $this->session->set_userdata("challan_supplier",$data['invoiceDetail']->supplier_id);

        $data['supervisors'] = $this->MaterialLog_model->getProjectSupervisor($data['invoiceDetail']->project_id);
        $data['log_ids']=$this->session->userdata('log_ids');
        if($this->session->userdata('user_designation') == 'Supervisor'){
            $data['projects'] = $this->foreman->get_project_foremanId($this->session->userdata('id'));
        }
        else
        {
            $data['projects'] = $this->project->get_active_projects();
        }

        $data['title'] = 'Edit Invoice';
        $data['page'] = 'MaterialInvoice/materialInvoice_edit';
        $this->load->view('includes/template', $data);
    }
    public function invoiceDetail($id){

        $existingChallan=$this->Invoice_model->getChallanById($id);
        $existingChallanArray=array();
        foreach ($existingChallan as $key => $value) {
            array_push($existingChallanArray, $value->material_entry_log_id);
        }

        $data = $this->data;
        $challanData=$this->Invoice_model->SelectedChallanDetails($existingChallanArray);
        $data['invoiceDetail']=$this->Invoice_model->getInvoiceById($id);
        $data['allChallanData']=$challanData;
        $data['existingChallan']=$existingChallanArray;

        $suppliers = array();
        if(isset($data['invoiceDetail']->project_id) && !empty($data['invoiceDetail']->project_id)){
            $suppliers = $this->Supplier_model->getProjectSupplier($data['invoiceDetail']->project_id);
        }

        $data['suppliers'] = $suppliers;
        $data['supervisors'] = $this->MaterialLog_model->getProjectSupervisor($data['invoiceDetail']->project_id);
        $data['log_ids']=$this->session->userdata('log_ids');
        if($this->session->userdata('user_designation') == 'Supervisor'){
            $data['projects'] = $this->foreman->get_project_foremanId($this->session->userdata('id'));
        }
        else
        {
            $data['projects'] = $this->project->get_active_projects();
        }

        $data['title'] = 'Invoice Detail';
        $data['page'] = 'MaterialInvoice/materialInvoice_detail';
        $this->load->view('includes/template', $data);
    }
    public function send_invoice_mail($invoice_log_id){
        
        $result = $this->Invoice_model->get_materialInvoice_detail($invoice_log_id,$this->session->userdata('company_id'));
        
        if($this->session->userdata('user_designation') == 'admin')
        {
            $receiverDetails=array("user_email"=>$result->supervisor_email,
                                   "user_name" =>  $result->supervisor_name
                                );

            $content = "<p>New material invoice has been approved by admin.</p>";
        }
        else
        {
            $receiverDetails = $this->MaterialLog_model->get_company_admin($this->session->userdata('company_id'));
            $content = "<p>New material invoice has been maid by supervisor.</p>";
        }
        // $result_detail = $this->MaterialLog_model->get_materiallog_detail_by_id($entry_log_id);
        // echo "<pre>";
        // print_r($result);
        // print_r($receiverDetails);
        // exit;
        if(count($receiverDetails) > 0)
        {
            $subject = "Material Invoice | The Hajiri App";
            
            $content .= "<p><b><u>Material Invoice Detail:</u></b></p>";
            
            $content .= "<p><b>Invoice Date: </b>".$result->invoice_date."</p>";
            $content .= "<p><b>Invoice No: </b>".$result->invoice_no."</p>";
            $content .= "<p><b>Total Amount: </b>".$result->total_amount."</p>";
            
            $content .= "<p>For more detail:</p>";
            $content .= "<p><a href='" . base_url('admin/MaterialInvoice/invoiceDetail/').$result->id. "' >Please click here</a></p>";
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
    public function DownloadChallan($image)
    {
        $detail=$this->MaterialLog_model->get_materiallog_by_id($image);
        $image=$detail->challan_image;
        $this->load->helper('download');

        $imagePath="./uploads/materialLog/challan/".$image;
        $data = file_get_contents(ROOT_PATH.'/uploads/materialLog/challan/'.$image);
        force_download($image,$data);
    }
    public function getFilterAjax($id) { 
       $result = $this->Supplier_model->getProjectSupplier($id);

       $getProjectSupervisor = array();
        
        $getProjectSupervisor = $this->MaterialLog_model->getProjectSupervisor($id);
        
        echo json_encode([
            'success'=> true, 
            'getProjectSupervisor' => $getProjectSupervisor,
            'getProjectSupplier' => $result
        ]);  exit();
       // echo json_encode($result);
       // exit;
    }
    public function getSupplierAjax($id) {
        
        $getSupplierByProjectId = array();
        
        $getSupplierByProjectId = $this->Supplier_model->getProjectSupplier($id);
        
        echo json_encode([
            'success'=> true, 
            'getProjectSupplier' => $getSupplierByProjectId
        ]);  exit();
    }
    public function ajax_delete($id) {

        $materialInvoiceStatus = array(
                    'status'   => "Deleted",
                    'is_deleted'   => "1",
                );
        
        $materialLogId = $this->Invoice_model->update('material_invoice', array('id' => $id), $materialInvoiceStatus);
        
        redirect(base_url('admin/MaterialInvoice/index'));
        // $this->MaterialLog_model->delete('material_entry_log', 'id', $id);
        // $this->MaterialLog_model->delete('material_entry_logdetail', 'material_entry_log_id', $id);
        $this->session->set_flashdata('success', 'Material Invoice Deleted Successfully');
    }
    public function invoiceEntryDatatable()
    {
        
        // sorting column array
        $columns = array( 
                            1 =>'material_entry_log.challan_no',
                            2 =>'material_entry_log.challan_date',
                            3 => 'amount'
                        );
        $limit = $this->input->post('length');
        $start = $this->input->post('start');
        // $order = $columns[$this->input->post('order')[0]['column']];
        $order = $columns[1];
        $dir = $this->input->post('order')[0]['dir'];
  
        $totalData = $this->Invoice_model->allMaterialInvoiceEntry_count();
            
        $totalFiltered = $totalData; 
        $where=null;
        // $where = '(material_entry_log.challan_date between "'.$this->input->post('dateStartRange').'"  and  "'.$this->input->post('dateEndRange').'")';
        if(!empty($this->input->post('search')['value']))
        {            
            if($where != null){
                $where.= ' AND ';
            }
            $where .= '( material_entry_log.challan_no LIKE "'.$this->input->post('search')['value'].'%" or ';
            
            $where .= 'total_rate LIKE "'.$this->input->post('search')['value'].'%")';            
        }

        
        if(!empty($this->input->post('project')))
        {   
            if($where == null)
            $where .= 'material_entry_log.project_id = "'.$this->input->post('project').'"';
            else
            $where .= ' AND material_entry_log.project_id = "'.$this->input->post('project').'"';
        }
        if(!empty($this->input->post('supplier')))
        {   
            if($where == null)
            $where .= 'material_entry_log.supplier_id = "'.$this->input->post('supplier').'"';
            else
            $where .= ' AND material_entry_log.supplier_id = "'.$this->input->post('supplier').'"';
        }
    
        if($where == null)
        {            
            $materialLogs = $this->Invoice_model->allMaterialInvoiceEntry($limit,$start,$order,$dir);
        }
        else {                

            $materialLogs =  $this->Invoice_model->materialInvoiceEntry_custom_search($limit,$start,$where,$order,$dir);

            $totalFiltered = $this->Invoice_model->materialInvoiceEntry_custom_search_count($where);
        }

        $data = array();
        if(!empty($materialLogs))
        {   
            foreach ($materialLogs as $materialLog)
            {   
                $checkedFlag="";
                if($this->session->userdata('log_ids'))
                {
                    if(in_array($materialLog->id, $this->session->userdata('log_ids')))
                    {
                        $checkedFlag="checked='checked'";
                    }
                }
                $nestedData['id'] = '<input '.$checkedFlag.' type="checkbox" name="log_ids[]" class="log_ids" value="'.$materialLog->id.'">';

                $nestedData['challan_no'] = $materialLog->challan_no;
                $nestedData['challan_date'] = $materialLog->challan_date;
                $nestedData['amount'] = $materialLog->total_rate;
                // $nestedData['category_name'] = "";
                // $nestedData['material_name'] = "";
                // $nestedData['quantity'] = "";


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
    public function materialInvoiceDatatable()
    {

        
        // sorting column array
        $columns = array( 
                            0 =>'invoice_no',
                            1 =>'invoice_date',
                            2 =>'payment_due_date',
                            3 =>'suppliers.name',
                            4 => 'total_amount',
                            5 => 'status'
                        );
        $limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = $columns[$this->input->post('order')[0]['column']];
        $dir = $this->input->post('order')[0]['dir'];
  
        $totalData = $this->Invoice_model->allMaterialInvoice_count();
       
        $totalFiltered = $totalData; 
        $where=null;
        $where = '(material_invoice.invoice_date between "'.$this->input->post('invoiceStartRange').'"  and  "'.$this->input->post('invoiceEndRange').'" or material_invoice.payment_due_date between "'.$this->input->post('paymentStartRange').'"  and  "'.$this->input->post('paymentEndRange').'")';
        if(!empty($this->input->post('search')['value']))
        {            
            if($where != null){
                $where.= ' AND ';
            }
            $where .= '( invoice_no LIKE "'.$this->input->post('search')['value'].'%" or ';
            $where .= 'invoice_date LIKE "'.$this->input->post('search')['value'].'%" or ';
            $where .= 'payment_due_date LIKE "'.$this->input->post('search')['value'].'%" or ';
            $where .= 'suppliers.name LIKE "'.$this->input->post('search')['value'].'%" or ';
            $where .= 'total_amount LIKE "'.$this->input->post('search')['value'].'%" or ';
            $where .= 'material_invoice.status LIKE "'.$this->input->post('search')['value'].'%")';            
        }

        
        if(!empty($this->input->post('project')))
        {   
            if($where == null)
            $where .= 'material_invoice.project_id = "'.$this->input->post('project').'"';
            else
            $where .= ' AND material_invoice.project_id = "'.$this->input->post('project').'"';
        }
        if(!empty($this->input->post('supplier')))
        {   
            if($where == null)
            $where .= 'material_invoice.supplier_id = "'.$this->input->post('supplier').'"';
            else
            $where .= ' AND material_invoice.supplier_id = "'.$this->input->post('supplier').'"';
        }
    
        if($where == null)
        {            
            $materialInvoices = $this->Invoice_model->allMaterialInvoice($limit,$start,$order,$dir);
        }
        else {                

            $materialInvoices =  $this->Invoice_model->materialInvoice_custom_search($limit,$start,$where,$order,$dir);

            $totalFiltered = $this->Invoice_model->materialInvoice_custom_search_count($where);
        }

        $data = array();
        if(!empty($materialInvoices))
        {   
            foreach ($materialInvoices as $materialInvoice)
            {   
                
                

                $nestedData['invoice_no'] = $materialInvoice->invoice_no;
                $nestedData['invoice_date'] = $materialInvoice->invoice_date;
                $nestedData['payment_due_date'] = $materialInvoice->payment_due_date;
                $nestedData['supplier_name'] = $materialInvoice->supplier_name;
                $nestedData['total_amount'] = $materialInvoice->total_amount;
                $nestedData['status'] = $materialInvoice->status;
                $nestedData['action']='<a class="btn btn-sm btn-primary" href="'.base_url('admin/MaterialInvoice/editInvoiceDetail/') . $materialInvoice->id.'" title="Edit material invoice">
                                            <i class="glyphicon glyphicon-pencil"></i> </a>  
                                            <button class="btn btn-sm btn-danger" title="Delete material invoice" onclick="material_invoice_delete('.$materialInvoice->id.')">
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
}