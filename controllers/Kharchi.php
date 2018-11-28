<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Kharchi extends CI_Controller {

    public $data;
	public function __construct() {
        parent::__construct();
		$this->data['menu_title'] = 'kharchi';
        $this->load->model('Kharchi_model', 'kharchi');    
        $this->load->model('Admin_model', 'admin');    
        $this->load->model('Foreman_model', 'foreman');
        $this->load->model('project_model', 'project');
        checkAdmin();
    }

    public function index() {
       
        $data = $this->data;
		$data['credit'] = $this->kharchi->kharchiStats('status = 1 AND debit_credit_status = 0');
		$data['debit'] = $this->kharchi->kharchiStats('status = 1 AND debit_credit_status = 1');
		if (isset($_POST['addKharchiSubmit'])) {
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('addKharchiTitle', 'Kharchi Title', 'trim|required');
            $this->form_validation->set_rules('addKharchiProject', 'Kharchi project', 'trim|required');
            $this->form_validation->set_rules('addKharchAmount', 'Kharchi Amount', 'trim|required');
            $this->form_validation->set_rules('addKharchDate', 'Kharchi Date', 'trim|required');
            if ($this->form_validation->run() == true) {

                $data = array(
                    'status' => '0',
					'company_id' => $this->session->userdata('company_id'),
					'supervisor_id' => $this->session->userdata('id'),
                    'project_id' => $this->input->post('addKharchiProject'),
                    'title' => $this->input->post('addKharchiTitle'),
                    'amount' => $this->input->post('addKharchAmount'),
					'debit_credit_status' => 1,
                    'date_time' => date('Y-m-d H:i:s', strtotime( $this->input->post('addKharchDate') ) ),
                );
                $associatedFileNames = array('image');
				foreach ($associatedFileNames as $fileName) {
					if (!empty($_FILES[$fileName]['name'])) {
						$result = uploadStaffFile('uploads/kharchi/', $fileName);
						if ($result['flag'] == 1) {
							$data[$fileName] = $result['filePath'];
						} else {
							$fileError[$fileName] = $result['error'];
						}
					}
				} 
                if (empty($fileError)) { 
                    $id = $this->input->post('kharchiEditId');
                    $result = $this->foreman->save('kharchi', $data);
                    if ($result) {
                        $this->session->set_flashdata('success', 'Kharchi added Successfully.');
                        $data['menu_title'] = '';
						header("Refresh:0");
                    } else {
                        $data['menu_title'] = '';
                        $this->session->set_flashdata('error', 'Sorry, Kharchi Not added.');
						header("Refresh:0");
                    } 
                } else {
                    $data['menu_title'] = '';
                    $this->session->set_flashdata('error', 'Sorry, Kharchi Not added.');
					header("Refresh:0");
                }
            }
		}//End of Add kharchi form submit

		if (isset($_POST['editKharchiSubmit'])) {
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('kharchiTitle', 'Kharchi Title', 'trim|required');
            $this->form_validation->set_rules('kharchiProject', 'Kharchi project', 'trim|required');
            $this->form_validation->set_rules('kharchiAmount', 'Kharchi Amount', 'trim|required');
            $this->form_validation->set_rules('kharchiDate', 'Kharchi Date', 'trim|required');
            if ($this->form_validation->run() == true) {
                $data = array(
                    'status' => $this->input->post('kharchiStatus'),
                    'project_id' => $this->input->post('kharchiProject'),
                    'title' => $this->input->post('kharchiTitle'),
                    'amount' => $this->input->post('kharchiAmount'),
                    'date_time' => date('Y-m-d H:i:s', strtotime( $this->input->post('kharchiDate') ) ),
                );
                $associatedFileNames = array('image');
				foreach ($associatedFileNames as $fileName) {
					if (!empty($_FILES[$fileName]['name'])) {
						$result = uploadStaffFile('uploads/kharchi/', $fileName);
						if ($result['flag'] == 1) {
							$data[$fileName] = $result['filePath'];
						} else {
							$fileError[$fileName] = $result['error'];
						}
					}
				} 
                if (empty($fileError)) { 
                    $id = $this->input->post('kharchiEditId');
                    $result = $this->admin->updateRecord('kharchi', $id, 'kharachi_id', $data);
                    if ($result) {
                        $this->session->set_flashdata('success', 'Kharchi Data Successfully Updated');
                        $data['menu_title'] = '';
						header("Refresh:0");
                    } else {
                        $data['menu_title'] = '';
                        $this->session->set_flashdata('error', ' Kharchi Data Not Updated');
						header("Refresh:0");
                    } 
                } else {
                    $data['menu_title'] = '';
                    $this->session->set_flashdata('error', 'Sorry, Kharchi Data Not Updated');
						header("Refresh:0");
                }
            }
		}//End of Edit kharchi form submit
        
		if (isset($_POST['editCreditSubmit'])) {
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('creditSupervisor', 'Supervisor', 'trim|required');
            $this->form_validation->set_rules('creditProject', 'Kharchi project', 'trim|required');
            $this->form_validation->set_rules('creditAmount', 'Kharchi Amount', 'trim|required');
            $this->form_validation->set_rules('creditDate', 'Kharchi Date', 'trim|required');
            if ($this->form_validation->run() == true) {

                $data = array(
                    'project_id' => $this->input->post('creditProject'),
                    'supervisor_id' => $this->input->post('creditSupervisor'),
                    'admin_id' => $this->session->userdata('id'),
                    'amount' => $this->input->post('creditAmount'),
                    'date_time' => date('Y-m-d H:i:s', strtotime( $this->input->post('creditDate') ) ),
                );
 
                $id = $this->input->post('creditEditId');
                $result = $this->admin->updateRecord('kharchi', $id, 'kharachi_id', $data);
                if ($result) {
                    $this->session->set_flashdata('success', 'Credit Data Successfully Updated');
                    $data['menu_title'] = '';
					header("Refresh:0");
                } else {
                    $data['menu_title'] = '';
                    $this->session->set_flashdata('error', ' Credit Data Not Updated');
					header("Refresh:0");
                } 
            }
		}//End of Edit Credit form submit
        
		if( $this->session->userdata('user_designation') == 'Supervisor' ){
			$data['supervisorProjects'] = $this->foreman->get_allForemanProjects($this->session->userdata('id'));
		}else{
			$data['projects'] = $this->project->get_datatables();
		}
        $data['supervisor'] = $this->foreman->get_activeForeman();
        $data['title'] = 'Kharchi';
        $data['page'] = 'kharchi/kharchi_contractor';
        $this->load->view('includes/template', $data);
    }

    public function kharchiDatatable()
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

	public function addMoney() {
        $date = date("Y-m-d", strtotime( $this->input->post('date')) );
		$data 
			=
			array(
			'company_id' => $this->session->userdata('company_id'),
			'admin_id' => $this->session->userdata('id'),
			'supervisor_id' => $this->input->post('supervisor'),
			'project_id' => $this->input->post('project'),
			'amount' => $this->input->post('amount'),
			'date_time' => $date,
			'status' => '1',
			'debit_credit_status' => '0',
			'update_flag' => '0',
		);

		$addMoney_id = $this->foreman->save('kharchi', $data);
		if($addMoney_id){
			$this->session->set_flashdata('success', 'Money added to supervisor account successfully.');
		}else{
			$this->session->set_flashdata('error', 'Error in adding money.');
		}
	}

	public function getKharchiDetails(){
		$id = $this->input->post('id');
		$where = array('kharachi_id' => $id);
		$details = $this->kharchi->kharchiDetails($where,'kharchi');
		$details->date_time = date('d-m-Y', strtotime($details->date_time));
		$details->log = '';
		if($details->update_flag == 1){
			$where = array('kharchi_id' => $id);
			$updateLog = $this->kharchi->kharchiLogDetails($where,'kharchi_log');
			$details->log = $updateLog->kharchi_log_description.' on '.date('d-m-Y', strtotime($updateLog->kharchi_log_date_time));
		}
		echo json_encode($details);
	}

    public function supervisor_project(){
        $supervisorId = $this->input->post('supervisorId');
        $assignedProjects = $this->foreman->get_allForemanProjectsName($supervisorId);
        $option='';
        foreach($assignedProjects as $proj){
            $option .= "<option value='$proj->project_id'>$proj->project_name</option>";
        }
        echo json_encode($assignedProjects);
    }

}
?>