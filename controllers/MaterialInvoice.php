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
        $this->load->model('project_model', 'project');        
        checkAdmin();
    }

    public function index(){
        $data = $this->data;
        $data['materialInvoice'] = $this->Invoice_model->getMaterialInvoice();
        $data['title'] = 'Material Invoice';
        $data['materialLog']=array();
        $data['page'] = 'MaterialInvoice/materialInvoice_list';
        $this->load->view('includes/template', $data);
    }
    public function issueInvoice()
    {
        $data = $this->data;
        $data['title'] = 'Material Invoice';
        $data['projects'] = $this->project->get_active_projects();
        $data['page'] = 'MaterialInvoice/materialInvoiceEntry_list';
        $this->load->view('includes/template', $data);
    }
    public function getSupplierAjax($id) { 
       $result = $this->Supplier_model->getProjectSupplier($id);
       echo json_encode($result);
       exit;
    }
    public function invoiceDatatable()
    {
        $columns = array( 
                            0 =>'kharachi_id', 
                            2 =>'title',
                            3 =>'date_time',
                        );

        $limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = $columns[$this->input->post('order')[0]['column']];
        $dir = $this->input->post('order')[0]['dir'];
  
        $totalData = $this->kharchi->allKharchi_count();
            
        $totalFiltered = $totalData; 
        $where = null;
        if(!empty($this->input->post('search')['value']))
        {            
            $where .= 'kharchi.title LIKE "'.$this->input->post('search')['value'].'%"';
        }
        if(!empty($this->input->post('project')))
        {   
            if($where == null)
            $where .= 'kharchi.project_id = "'.$this->input->post('project').'"';
            else
            $where .= ' AND kharchi.project_id = "'.$this->input->post('project').'"';
        }
        if(!empty($this->input->post('supervisor')))
        {   
            if($where == null)
            $where .= 'kharchi.supervisor_id = "'.$this->input->post('supervisor').'"';
            else
            $where .= ' AND kharchi.supervisor_id = "'.$this->input->post('supervisor').'"';
        }
        if(!empty($this->input->post('date')))
        {   
            if($where == null)
             $where .= 'kharchi.date_time LIKE "'.date('Y-m', strtotime($this->input->post('date'))).'%"';
            else
             $where .= ' AND kharchi.date_time LIKE "'.date('Y-m', strtotime($this->input->post('date'))).'%"';
        }
    
        if($where == null)
        {            
            $posts = $this->kharchi->allKharchi($limit,$start,$order,$dir);
        }
        else {                

            $posts =  $this->kharchi->kharchi_custom_search($limit,$start,$where,$order,$dir);

            $totalFiltered = $this->kharchi->kharchi_custom_search_count($where);
        }

        $data = array();
        if(!empty($posts))
        {   
            $debitImg = base_url('assets/admin/images/debit.png');
            $creditImg = base_url('assets/admin/images/credit.png');
            foreach ($posts as $post)
            {   
                $nestedData['id'] = $post->kharachi_id;
                if($post->debit_credit_status == 0)
                    $nestedData['Kharchi_type'] = '<img src="'.$creditImg.'" class="tableIcon" />';
                else if($post->debit_credit_status == 1)
                    $nestedData['Kharchi_type'] = '<img src="'.$debitImg.'"  class="tableIcon"/>';
                $nestedData['Kharchi_details'] = '<p class="capitalize">'.ucwords(strtolower($post->title)).' </p>';
                $nestedData['date'] = date('d-m-Y',strtotime($post->date_time));
                $nestedData['amount'] = $post->amount;
                if($post->status == 0)
                    $nestedData['status'] = 'Pending';
                else if($post->status == 1)
                    $nestedData['status'] = 'Approved';
                //Edit Action                   
                if($post->debit_credit_status == 0){
                    if( $this->session->userdata('user_designation') == 'admin' )
                        $nestedData['action'] = "<button class='btn btn-success'  onClick='editCredit($post->kharachi_id)'  data-toggle='modal' data-target='#editCredit' title='Edit Credit' >Edit Credit</button>";
                    else
                        $nestedData['action'] = "";
                }
                else if($post->debit_credit_status == 1){
                    if($post->status == 0){
                        $nestedData['action'] = "<button class='btn btn-success editKharchi' onClick='editKharchi($post->kharachi_id)'  data-toggle='modal' data-target='#editKharchi' title='Edit Kharchi' >Edit Kharchi</button>";
                    }else if($post->status == 1)
                    $nestedData['action'] = "";
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