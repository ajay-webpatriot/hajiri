<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class workerRegister extends CI_Controller {

    public $data;
	public function __construct() {
        parent::__construct();
        $this->load->model('Register_model', 'register');        
        $this->load->model('labour_model', 'labour');        
        $this->load->model('Category_model', 'category');
        $this->load->model('Foreman_model', 'foreman');
        $this->load->model('Companies_model', 'company');
        $this->load->model('project_model', 'project');
        $this->load->model('plan_model', 'plan');
        $this->load->model('Weeklyof_model', 'weekOff');
        $this->load->model('Holidays_model', 'holiday');
        checkAdmin();
    }

    public function index() {
       
        $this->breadcrumbcomponent->add('Dashboard', base_url('/admin'));
        $this->breadcrumbcomponent->add('All Worker', base_url('/admin/workerRegister'));
        $this->data['menu_title'] = 'Worker register';
        $data['breadcrumb'] = $this->breadcrumbcomponent->output();
        $data = $this->data;
        //$data['project'] = $this->register->get_all_where('project',$this->session->userdata('company_id'));
    		if($this->session->userdata('user_designation') == 'admin')
    			$data['projects'] = $this->project->get_active_projects();
    		if($this->session->userdata('user_designation') == 'Supervisor'){
    			$data['projects'] = $this->foreman->get_project_foremanId( $this->session->userdata('id'));
    		}
        $data['Category'] = $this->category->get_all_category($this->session->userdata('company_id'));
        $data['planId'] = $this->plan->get_PlanId($this->session->userdata('company_id'));
        $data['limit'] = $this->plan->get_limit('pp.no_of_worker - count(w.company_id) AS wLimit',$this->session->userdata('company_id'),'worker','w. status != 0');
        $data['title'] = 'Worker register';
        $data['description'] = 'All worker list';
        $data['page'] = 'register/worker_register';
        $this->load->view('includes/template', $data);
    }

    public function workerDatatable()
        {
            $project = $this->register->get_all_where('project',$this->session->userdata('company_id'));
            if ($project) {
                $option = '';
               foreach ($project as $data) {
                    $option .= "<option  value='" . $data->id . "'>" . $data->name . "</option>";
               }
           }
            $columns = array( 
                                0 =>'labour_name', 
                                1 =>'category_name',
                                2=> 'worker_due_wage',
                            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
      
            $totalData = $this->register->allworker_count();
                
            $totalFiltered = $totalData; 
                
            if(empty($this->input->post('search')['value']))
            {            
                $posts = $this->register->allworker($limit,$start,$order,$dir);
            }
            else {
                $search = $this->input->post('search')['value']; 

                $posts =  $this->register->worker_search($limit,$start,$search,$order,$dir);

                $totalFiltered = $this->register->worker_search_count($search);
            }
            if(!empty($this->input->post('category'))){
                $search = $this->input->post('category'); 

                $posts =  $this->register->worker_col_search($limit,'category_name',$start,$search,$order,$dir);

                $totalFiltered = $this->register->worker_custom_search_count($search,'category_name');

            }

            $data = array();
            if(!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $workerId = base_url('admin/workerRegister/worker/') . $post->worker_id;

                    $nestedData['worker_id'] = $post->worker_id;
                    $nestedData['labour_name'] = '<p class="capitalize">'.ucwords(strtolower($post->labour_name)).' '.ucwords(strtolower($post->labour_last_name)).'</p>';
                    $nestedData['category_name'] = $post->category_name;
                    $nestedData['status'] = $post->status;
                    $nestedData['worker_due_wage'] = $post->worker_due_wage;
                    $nestedData['wageId'] = $post->wageId;
                    $nestedData['paymentDate'] = $post->paymentDate;
                    $nestedData['Paid'] = $post->Paid;
                    if (in_array("2", $this->session->userdata('permissions'))){
                        if ($post->status != "2"){
                            $markPresent = 
                               
                                   '<button class="btn btn-sm btn-success" data-toggle="modal" data-target="#absentModal'.$post->worker_id.'" title="Mark present" >
                                       Mark present 
                                   </button>
                                   <div class="modal fade" id="absentModal'.$post->worker_id.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">

                                   <div class="modal-dialog modal-sm" role="document">
                                       <div class="modal-content">
                                           <div class="modal-header">
                                               <button type="button" class="btn btn-danger close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                               <h4 class="modal-title" id="exampleModalLabel">
                                                   '.ucwords(strtolower($post->labour_name)).' '.ucwords(strtolower($post->labour_last_name)).' <span class="categoryName">'.$post->category_name.'</span>
                                               </h4>
                                           </div>
                                           <div class="modal-body container-fluid">
                                               <form action="" class="makePayment" method="POST" enctype="multipart/form-data">
                                                   
                                                   <div class="form-group">
                                                       <label>Select date</label>
                                                       <input name="date" id="date'.$post->worker_id.'" placeholder="Select Date" class="form-control datepicker col-xs-12" autocomplete="off" type="text" required readonly>
                                                       <span class="add-on"><i class="fa fa-calendar"></i></span>
                                                       <span class="error"><?php echo (form_error("date")) ? form_error("date") : " "; ?></span>
                                                   </div>
                                                   <div class="form-group">
                                                       <label>Select Project</label>
                                                       <select name="pid" id="pid'.$post->worker_id.'" class="form-control project col-xs-12" required>
                                                           '.$option.'
                                                       </select>
                                                   </div>
                                                   <div class="form-group">
                                                       <label>Add reason for absent</label>
                                                       <textarea class="form-control"  placeholder="Reason for present" name="presentReason" id="reason'.$post->worker_id.'" required></textarea>
                                                       <span class="error"><?php echo (form_error("presentReason")) ? form_error("presentReason") : " "; ?></span>
                                                   </div>

                                           </div>
                                           <div class="modal-footer">
                                                   <button type="button" class="btn btn-success"  onclick="worker_present('.$post->worker_id.')">
                                                       Submit
                                                   </button>
                                                   <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                               </form>
                                           </div>
                                       </div>
                                   </div>
                               </div>';

                            $paidLeave = '
                                <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#paidLeaveModal'.$post->worker_id.'" title="Paid leave" >
                                       Paid Leave 
                                   </button>
           
                               <div class="modal fade" id="paidLeaveModal'.$post->worker_id.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
                                   <div class="modal-dialog modal-sm" role="document">
                                       <div class="modal-content">
                                           <div class="modal-header">
                                               <button type="button" class="btn btn-danger close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="exampleModalLabel">
                                                   '.ucwords(strtolower($post->labour_name)).' '.ucwords(strtolower($post->labour_last_name)).' <span class="categoryName">'.$post->category_name.'</span>
                                               </h4>
                                           </div>
                                           <div class="modal-body container-fluid">
                                               <form action="" class="makePayment" method="POST" enctype="multipart/form-data">
                                                   <div class="form-group">
                                                       <label>Select date</label>
                                                       <input name="date" id="paidDate'.$post->worker_id.'" placeholder="Select Date" class="form-control datepicker  col-xs-12" autocomplete="off" type="text" required readonly >
                                                       <span class="add-on"><i class="fa fa-calendar"></i></span>
                                                           <span class="error"><?php echo (form_error("date")) ? form_error("date") : " "; ?></span>
                                                   </div>
           
                                           </div>
                                           <div class="modal-footer">
                                                   <button type="button" class="btn btn-success"  onclick="paidLeave('.$post->worker_id.')">
                                                       Submit
                                                   </button>
                                                   <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                               </form>
                                           </div>
                                       </div>
                                   </div>
                               </div>
                            ';

                            
                        }else{
                            $markPresent = '<button class="btn btn-sm btn-default disabled" title="Mark present" >
                                                       Mark present 
                                                   </button>';
                            $paidLeave = ' <button class="btn btn-sm btn-default disabled" title="Mark present" >
                                                       Paid Leave
                                                   </button>';
                            
                        }
                    }else{
                        $markPresent = '';
                        $paidLeave = '';
                    }

                    if (in_array("3", $this->session->userdata('permissions'))){
                        if ($post->status != "2"){
                            $makePayment = '
                                <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#paymentModal'.$post->worker_id.'" title="Make payment" onclick="fetchHajiri('.$post->worker_id.')">
                                                           Make payment 
                               </button>
                               <div class="modal fade" id="paymentModal'.$post->worker_id.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
                               <div class="modal-dialog modal-sm" role="document">
                                   <div class="modal-content">
                                       <div class="modal-header">
                                           <button type="button" class="btn btn-danger close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                           <h4 class="modal-title" id="exampleModalLabel">
                                                   '.ucwords(strtolower($post->labour_name)).' '.ucwords(strtolower($post->labour_last_name)).' <span class="categoryName">'.$post->category_name.'</span>
                                               </h4>  
                                       </div>
                                       <div class="modal-body container-fluid">
                                           <form action="" class="makePayment" method="POST" enctype="multipart/form-data">
                                               <div class="col-xs-12">
                                                   <div class="col-xs-8">
                                                       <p>Daily Hajiri</p>
                                                       <p>Due amount</p>
                                                       <p>Last withdrawal</p>
                                                       <p class="error">'.((isset($post->paymentDate)) ? date( 'd-m-Y', strtotime(($post->paymentDate))) : '').'</p>
                                                   </div>
                                                   <div class="col-xs-4">
                                                       <p><span id="hajiri'.$post->worker_id.'"></span></p>
                                                       <p>'.((isset($post->worker_due_wage)) ? $post->worker_due_wage : '') .'</p>
                                                       <p><span id="paid'.$post->worker_id.'"></span></p>
                                                       <p></p>
                                                   </div>
                                               </div>
                                               <div class="col-xs-12">
                                                   <div class="form-group">
                                                       <label>Select project</label>
                                                       <select name="projectId" id="projectId'.$post->worker_id.'" class="col-xs-12 form-control project" required>
                                                           '.$option.'
                                                       </select>
                                                   </div>
                                                   <div class="form-group">
                                                       <input type="text" name="wageId" id="wageId'.$post->worker_id.'" value="'.((isset($post->wageId)) ? $post->wageId : '').'" required class="hidden">
                                                       <label>Add amount </label>
                                                       <input type="number" class="form-control"  placeholder="Amount" name="amount" id="amount'.$post->worker_id.'" required>
                                                       <span class="error"><?php echo (form_error("amount")) ? form_error("amount") : " "; ?></span>
                                                   </div>
                                               </div>
                                       </div>
                                       <div class="modal-footer">
                                               <button type="button" class="btn btn-success"  onclick="makePayment('.$post->worker_id.')">
                                                   Submit
                                               </button>
                                               <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                           </form>
                                       </div>
                                   </div>
                               </div>
                                </div>
                            ';
                        }else{
                            $makePayment = '<button class="btn btn-sm btn-default disabled" title="Make payment " >
                                                       Make payment  
                                                   </button>';
                        }
                    }else{
                        $makePayment = '';
                    }

                    $nestedData['action'] = '
                        <a class="btn btn-sm btn-primary" href="'.$workerId.'" title="Edit">
                           <i class="glyphicon glyphicon-pencil"></i> 
                        </a>
                       ';
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

    public function worker() {
        $fileError = array();
        if (isset($_POST['submit_add'])) {
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('first_name', 'First name', 'trim|required|regex_match[/[a-zA-Z]/]');
            $this->form_validation->set_rules('last_name', 'Last name', 'trim|regex_match[/[a-zA-Z]/]');
            $this->form_validation->set_rules('contact', 'Contact No.', 'trim|regex_match[/^[0-9]{10}$/]|min_length[10]');
            $this->form_validation->set_rules('daily_wage', 'Daily Wage', 'trim|required');
            $this->form_validation->set_rules('cid', 'Category', 'trim|required');
            $this->form_validation->set_rules('status', 'status', 'trim|required');
            $this->form_validation->set_rules('due_amount', 'Opening Amount', 'trim|numeric');
            if ($this->form_validation->run() == TRUE) {
                $wType = $this->input->post('wage_type');
                if($wType == NULL){
                    $wType = 1;
                }
                if($this->input->post('status') == 2){
                  $status = $this->input->post('delete');
                }else{
                  $status = $this->input->post('status');
                }
                $data = array(
                    'company_id' => $this->input->post('company_id'),
                    'labour_name' => ucfirst($this->input->post('first_name')),
                    'labour_last_name' => ucfirst($this->input->post('last_name')),
                    'worker_contact' => $this->input->post('contact'),
                    'category_id' => $this->input->post('cid'),
                    'labour_join_date' => date('Y-m-d'),
                    'status' => $status,
                    'worker_wage_type' => $wType,
                );
                if( $this->input->post('worker_id') > 0 ){
                  // Check if worker for a particular company with added name already exist
                  $sql = "SELECT worker_id FROM `worker` WHERE UPPER(labour_name) like UPPER('" . $this->input->post('first_name') ."') AND UPPER(labour_last_name) like UPPER('" . $this->input->post('last_name') . "') AND worker_id != ".$this->input->post('worker_id')." AND status != 0 AND company_id = ".$this->input->post('company_id');
                  $workerRecord = $this->db->query($sql)->row();
                  if( !empty( $workerRecord ) ) {
                    $this->session->set_flashdata('error', 'Worker with name <strong>'.$this->input->post('first_name').' '.$this->input->post('last_name').'</strong> already exist.');
                    redirect(base_url('admin/workerRegister/worker/'.$this->input->post('worker_id')));
                  }
                    $labour_id = $this->labour->update('worker', array('worker_id' => $this->input->post('worker_id')), $data);
                    if ($labour_id >= 0) {
                        $wageData = array(
                            'worker_id' => $this->input->post('worker_id'),
                            'worker_wage' => $this->input->post('daily_wage'),
                            'worker_opening_wage' => $this->input->post('due_amount'),
                            'worker_wage_type' => $wType,
                        );
                        $wage_id = $this->labour->update('worker_wage', array('worker_wage_id' => $this->input->post('wage_id')), $wageData);
                         if ($wage_id >= 0) {
                            $this->session->set_flashdata('success', 'Worker data updated successfully! ');
                            redirect(base_url('admin/workerRegister/'));
                        }
                        else {
                            $this->session->set_flashdata('error', 'Failed To update Wage data.');
                            redirect(base_url('admin/workerRegister/worker/'.$this->input->post('worker_id')));
                        }
                    } else {
                        $this->session->set_flashdata('error', 'Failed To Update Worker data.');
                        redirect(base_url('admin/workerRegister/worker/'.$this->input->post('worker_id')));
                    }
                }
                
                else{
                    // Check if worker for a particular company with added name already exist
                  $sql = "SELECT worker_id FROM `worker` WHERE UPPER(labour_name) like UPPER('" . $this->input->post('first_name') ."') AND UPPER(labour_last_name) like UPPER('" . $this->input->post('last_name') . "') AND status != 0 AND company_id = ".$this->input->post('company_id');
                  $workerRecord = $this->db->query($sql)->row();
                  if( !empty( $workerRecord ) ) {
                    $this->session->set_flashdata('error', 'Worker with name <strong>'.$this->input->post('first_name').' '.$this->input->post('last_name').'</strong> already exist.');
                    redirect(base_url('admin/workerRegister/worker'));
                  }
                    $labour_id = $this->labour->save('worker', $data);
                    if ($labour_id) {
                        $wageData = array(
                            'worker_id' => $labour_id,
                            'worker_wage' => $this->input->post('daily_wage'),
                            'worker_opening_wage' => $this->input->post('due_amount'),
                            'wage_start_date' => date('Y-m-d'),
                            'worker_wage_type' => $wType,
                        );
                        $wage_id = $this->labour->save('worker_wage', $wageData);
                         if ($wage_id) {
                           
                            // --- Generate and upload QR code -----
                            $this->load->library('ciqrcode');

                            $qrcode_date_time = date('dmY_His');
                            $image_name = date('Y_m_d_H_i_s') . '.jpg';
                            $params['data'] = $qrcode_date_time;
                            $params['level'] = 'H';
                            $params['size'] = 10;
                            $params['savename'] = FCPATH . 'uploads/barcodes/' . $image_name;
                            $this->ciqrcode->generate($params);
                            $qrCode = array(
                                'worker_qrcode'         => $qrcode_date_time, 
                                'worker_qrcode_image'   => $image_name, 
                                'worker_wage'           => $wage_id,
                            );

                            $worker_id = $this->labour->update('worker', array('worker_id' => $labour_id), $qrCode);

                            $this->session->set_flashdata('success', 'Worker Added Successfully! ');
                            redirect(base_url('admin/workerRegister/worker'));
                        }
                        else {
                            $this->labour->delete('worker','worker_id','$labour_id');
                            $this->session->set_flashdata('error', 'Failed To Add Wage');
                            redirect(base_url('admin/workerRegister/worker'));
                        }
                    } else {
                        $this->session->set_flashdata('error', 'Failed To Add Worker');
                        redirect(base_url('admin/workerRegister/worker'));
                    }
                }
            }
        }
        $data = $this->data;
        $data['results'] = '';
        $data['planId'] = $this->plan->get_PlanId($this->session->userdata('company_id'));
        $data['limit'] = $this->plan->get_limit('pp.no_of_worker - count(w.company_id) AS wLimit',$this->session->userdata('company_id'),'worker','w. status != 0');
        if( $this->uri->segment(4) > 0 ){
            $wageId = $data['results'] = $this->labour->get_by_id($this->uri->segment(4));
            $data['wage'] = $this->labour->get_by_wage_id($wageId->worker_wage);
            $data['menu_title'] = 'Edit worker';
            $data['title'] = 'Edit Worker';
        }else{
            $data['menu_title'] = 'Add worker';
            $data['title'] = 'Add Worker';
        }
        $data['company'] = $this->labour->get_all_company('company');
        //$data['category'] = $this->category->get_all_category();
        $data['projects'] = $this->labour->get_all_project('project');
        $data['fileError'] = $fileError;
        $data['description'] = 'Add worker';
        $data['page'] = 'labour/worker';
        $this->load->view('includes/template', $data);
    }

	public function ajax_bulk_present(){
      $formData = $this->input->post('formData');
      $counter = 1;
      $error = 0;
      foreach ($formData as $value) {
		if($counter == 2){
			$id = $value;
		}elseif($counter == 4){
			$date = $value;
		}elseif($counter == 6){
			$project = $value;			
		}elseif($counter == 8){
			$reason = $value;
            //echo '<br/> id=> '.$id.'Date=> '.$date.'project=> '.$project.' reason =>'.$reason;
            $this->ajax_present($id,$date,$project,$reason); 
            $counter = $counter - 8;
		}			
		$counter++;
      }
    }

    public function ajax_present($id,$presentDate,$project,$reason) {
        
        $wage = $this->register->get_wage($id);
        $add_date = $date = $presentDate;
        $checkAttendance = $this->register->checkAttendance($id,date("Y-m-d", strtotime($date)));

        if($checkAttendance == 0){
            if($wage->worker_wage_type == 1){
                
                $m = date("m", strtotime($date));
                $y = date("Y", strtotime($date));
                $noOfDays = cal_days_in_month(CAL_GREGORIAN, $m,$y);
                $noOfWeekOff = $this->weekOff->countWeekOff('company_id = '.$this->session->userdata('company_id').' AND status = 1');
                $days = $noOfDays - $noOfWeekOff ;
                $wage->worker_wage = round(($wage->worker_wage / $days),1);

                $searchDate = $presentDate;
                $condition = 0;
                $ispresent = 0;
                $holidayDateArray = array();
                do 
                {
                    $prev_date = date('Y-m-d', strtotime($searchDate .' -1 day'));

                    $checkAttendancesql = $this->register->checkAttendanceHoliday($id, $prev_date);
                    $dd = date('l', strtotime($prev_date));
                    
                    if ($checkAttendancesql == 0)
                    {
                        $isweekoff = 0;
                        $day = date('l', strtotime($prev_date));
                        $getweekoffsql =  $this->weekOff->get_datatables();
                        foreach($getweekoffsql as $weekoffrow){
                            if ($weekoffrow->day == $day) 
                            {
                                $isweekoff = 1;
                            }
                        }
                        if ($isweekoff == 1)
                        {
                            $searchDate = $prev_date;
                        }
                        else
                        {
                            $holidayrow = $this->holiday->countHoliday($prev_date);
                            if ($holidayrow == 0)
                            {
                                $condition = 1;
                            }
                            else
                            {
                                $searchDate = $prev_date;
                                $holidayDateArray[] = $prev_date;
                            }
                        }
                    }
                    else
                    {
                        $condition = 1;
                        $ispresent = 1;
                    }
                  
                } while ($condition == 0);
                
                if ($ispresent == 1)
                {
                    foreach($holidayDateArray as $holidayDate)
                    {
                        $checkholidayattendancerow = $this->register->checkAttendance($id, $holidayDate);

                        if ($checkholidayattendancerow != 0) 
                        {
                            $status = $this->register->checkAttendanceStatus($id, $holidayDate);
                            if ($status == 2) 
                            {   
                                $updateData = array(
                                                'status'        => 4, 
                                                'amount'        => $wage->worker_wage,
                                                'project_id'    => $project,
                                                'hajiri'        => 1,
                                                'user_id'       => $this->session->userdata('id'),
                                            );
                                $date = date("Y-m-d", strtotime($holidayDate));
                                $where = "worker_id = ".$id." AND attendance_date_time LIKE '".$date."%'";
                                $updateattendancesql = $this->register->update('attendance',$updateData,$where);
                        
                                if($updateattendancesql > 0)
                                {                                    
                                    $labourwagerow = $this->register->get_wage($id);                                    
                                    $total_amount = $wage->worker_total_wage;
                                    $due_amount = $wage->worker_due_wage;
                                    $total_amount = $total_amount + $wage->worker_wage;
                                    $due_amount = $due_amount + $wage->worker_wage;
                                    $updateData = array(
                                                    'worker_total_wage' => $total_amount, 
                                                    'worker_due_wage' => $due_amount, 
                                                );
                                    $where = 'worker_wage_id = '.$wage->worker_wage_id;
                                    $updatelabour = $this->register->update('worker_wage',$updateData,$where);
                                }
                            }
                        } 
                        else
                        {
                            $holidayDate = date('Y-m-d His', strtotime($holidayDate));
                            $data = array(
                                'user_id' => $this->session->userdata('id'),
                                'status' => 4,
                                'project_id' => $project,
                                'worker_id' => $id,
                                'attendance_date_time' => date("Y-m-d h:i:s", strtotime($date)),
                                
                                'amount' => $wage->worker_wage,
                                'reason_for_absent' => $reason,
                                'attendance_mark_status' => 1,
                            );
                            $attendance_id = $this->register->save('attendance', $data);
                            
                    
                            if($attendance_id)
                            {
                                $labourwagerow = $this->register->get_wage($id);
                                    
                                $total_amount = $wage->worker_total_wage;
                                $due_amount = $wage->worker_due_wage;
                                $total_amount = $total_amount + $wage->worker_wage;
                                $due_amount = $due_amount + $wage->worker_wage;

                                $updateData = array(
                                                'worker_total_wage' => $total_amount, 
                                                'worker_due_wage' => $due_amount, 
                                            );
                                $where = 'worker_wage_id = '.$wage->worker_wage_id;
                                $updatelabour = $this->register->update('worker_wage',$updateData,$where);
								
                                if($updatelabour){
									//Update balance
									$balanceMonth = date('mY', strtotime($presentDate));

									$balanceData = $this->register->get_balance($id,$balanceMonth);
									if($balanceData){
										$balanceDataArray = array('balance' => $balanceData->balance + $wage->worker_wage);
										$balanceUpdate = $this->register->abscent('balance', $balanceDataArray, array('balance_id' => $balanceData->balance_id ));
									}
									 echo json_encode(array('alertType' => 'success', 'msg' => 'Worker marked present successfully.' ));
                    }else{
                      echo json_encode(array('alertType' => 'success', 'msg' => 'Worker marked present successfully.' ));
                    }

                }else{
                  echo json_encode(array('alertType' => 'error', 'msg' => 'Error occured while marking attendance.' ));
                }
                        }
                    }
                }else{
                    $data = array(
                        'user_id' => $this->session->userdata('id'),
                        'status' => 1,
                        'project_id' => $project,
                        'worker_id' => $id,
                        'attendance_date_time' => date("Y-m-d h:i:s", strtotime($date)),
                        'amount' => $wage->worker_wage,
                        'reason_for_absent' => $reason,
                        'attendance_mark_status' => 1,
                    );
                    $attendance_id = $this->register->save('attendance', $data);
                    if($attendance_id)
                    {
                        $total_amount = $wage->worker_total_wage;
                        $due_amount = $wage->worker_due_wage;
                        $total_amount = $total_amount + $wage->worker_wage;
                        $due_amount = $due_amount + $wage->worker_wage;

                        $updateData = array(
                                        'worker_total_wage' => $total_amount, 
                                        'worker_due_wage' => $due_amount, 
                                    );
                        $where = 'worker_wage_id = '.$wage->worker_wage_id;
                        $updatelabour = $this->register->update('worker_wage',$updateData,$where);
						if($updatelabour){
							//Update balance
							$balanceMonth = date('mY', strtotime($presentDate));

							$balanceData = $this->register->get_balance($id,$balanceMonth);
							if($balanceData){
								$balanceDataArray = array('balance' => $balanceData->balance + $wage->worker_wage);
								$balanceUpdate = $this->register->abscent('balance', $balanceDataArray, array('balance_id' => $balanceData->balance_id ));
							}
                  echo json_encode(array('alertType' => 'success', 'msg' => 'Worker marked present successfully.' ));
                    }else{
                      echo json_encode(array('alertType' => 'success', 'msg' => 'Worker marked present successfully.' ));
                    }

                }else{
                  echo json_encode(array('alertType' => 'error', 'msg' => 'Error occured while marking attendance.' ));
                }
              }

            }else{

                $data = array(
                    'user_id' => $this->session->userdata('id'),
                    'status' => 1,
                    'project_id' => $project,
                    'worker_id' => $id,
                   'attendance_date_time' => date("Y-m-d h:i:s", strtotime($date)),
                    'amount' => $wage->worker_wage,
                    'reason_for_absent' => $reason,
                    'attendance_mark_status' => 1,
                );
                $attendance_id = $this->register->save('attendance', $data);
                if($attendance_id)
                {
                    $total_amount = $wage->worker_total_wage;
                    $due_amount = $wage->worker_due_wage;
                    $total_amount = $total_amount + $wage->worker_wage;
                    $due_amount = $due_amount + $wage->worker_wage;

                    $updateData = array(
                                    'worker_total_wage' => $total_amount, 
                                    'worker_due_wage' => $due_amount, 
                                );
                    $where = 'worker_wage_id = '.$wage->worker_wage_id;
                    $updatelabour = $this->register->update('worker_wage',$updateData,$where);
                    if($updatelabour){
						//Update balance
						$balanceMonth = date('mY', strtotime($presentDate));

						$balanceData = $this->register->get_balance($id,$balanceMonth);
						if($balanceData){
							$balanceDataArray = array('balance' => $balanceData->balance + $wage->worker_wage);
							$balanceUpdate = $this->register->abscent('balance', $balanceDataArray, array('balance_id' => $balanceData->balance_id ));
						}
                    echo json_encode(array('alertType' => 'success', 'msg' => 'Worker marked present successfully.' ));
                    }else{
                      echo json_encode(array('alertType' => 'success', 'msg' => 'Worker marked present successfully.' ));
                    }

                }else{
                  echo json_encode(array('alertType' => 'error', 'msg' => 'Error occured while marking attendance.' ));
                }
            }
        }else{
          echo json_encode(array('alertType' => 'warning', 'msg' => 'Worker attendance is already marked.' ));
        }
    }
	
	public function ajax_bulk_paid_leave(){
      $formData = $this->input->post('formData');
      $counter = 1;
      $error = 0;
      foreach ($formData as $value) {
		if($counter == 2){
			$id = $value;
		}elseif($counter == 4){
			$date = $value;
		}elseif($counter == 6){
			$project = $value;
            $this->ajax_paidLeave($id,$date,$project); 
            $counter = $counter - 6;
		}			
		$counter++;
      }
    }

    public function ajax_paidLeave($id,$date,$project) {
        
        $wage = $this->register->get_wage($id);
        $checkAttendance = $this->register->checkAttendance($id,date("Y-m-d", strtotime($date)));

        if($checkAttendance == 0){
            if($wage->worker_wage_type == 1){
                
                $m = date("m", strtotime($date));
                $y = date("Y", strtotime($date));
                $noOfDays = cal_days_in_month(CAL_GREGORIAN, $m,$y);
                $noOfWeekOff = $this->weekOff->countWeekOff('company_id = '.$this->session->userdata('company_id').' AND status = 1');
                $days = $noOfDays - $noOfWeekOff ;
                $wage->worker_wage = round(($wage->worker_wage / $days),1);

                $data = array(
                    'user_id' => $this->session->userdata('id'),
                    'status' => 3,
                    'worker_id' => $id,
				            'project_id' => $project,
                   'attendance_date_time' => date("Y-m-d h:i:s", strtotime($date)),
                    'amount' => $wage->worker_wage,
                    'attendance_mark_status' => 1,
                );
                $attendance_id = $this->register->save('attendance', $data);
                if($attendance_id)
                {
                    $total_amount = $wage->worker_total_wage;
                    $due_amount = $wage->worker_due_wage;
                    $total_amount = $total_amount + $wage->worker_wage;
                    $due_amount = $due_amount + $wage->worker_wage;

                    $updateData = array(
                                    'worker_total_wage' => $total_amount, 
                                    'worker_due_wage' => $due_amount, 
                                );
                    $where = 'worker_wage_id = '.$wage->worker_wage_id;
                    $updatelabour = $this->register->update('worker_wage',$updateData,$where);
                    if($updatelabour){
						//Update balance
						$balanceMonth = date('mY', strtotime($date));

						$balanceData = $this->register->get_balance($id,$balanceMonth);
						if($balanceData){
							$balanceDataArray = array('balance' => $balanceData->balance + $wage->worker_wage);
							$balanceUpdate = $this->register->abscent('balance', $balanceDataArray, array('balance_id' => $balanceData->balance_id ));
						}
              echo json_encode(array('alertType' => 'success', 'msg' => 'Worker marked present successfully.' ));

                    }else{
                      echo json_encode(array('alertType' => 'warning', 'msg' => 'Worker attendance is already marked.' ));
                    }

                }else{
                  echo json_encode(array('alertType' => 'Error', 'msg' => 'Error occured while marking attendance.' ));
                }
            }
            else{

                $data = array(
                    'user_id' => $this->session->userdata('id'),
                    'status' => 3,
                    'worker_id' => $id,
					         'project_id' => $project,
                   'attendance_date_time' => date("Y-m-d h:i:s", strtotime($date)),
                    'amount' => $wage->worker_wage,
                    'attendance_mark_status' => 3,
                );
                $attendance_id = $this->register->save('attendance', $data);
                if($attendance_id)
                {
                    $total_amount = $wage->worker_total_wage;
                    $due_amount = $wage->worker_due_wage;
                    $total_amount = $total_amount + $wage->worker_wage;
                    $due_amount = $due_amount + $wage->worker_wage;

                    $updateData = array(
                                    'worker_total_wage' => $total_amount, 
                                    'worker_due_wage' => $due_amount, 
                                );
                    $where = 'worker_wage_id = '.$wage->worker_wage_id;
                    $updatelabour = $this->register->update('worker_wage',$updateData,$where);
                    if($updatelabour){
						//Update balance
						$balanceMonth = date('mY', strtotime($date));

						$balanceData = $this->register->get_balance($id,$balanceMonth);
						if($balanceData){
							$balanceDataArray = array('balance' => $balanceData->balance + $wage->worker_wage);
							$balanceUpdate = $this->register->abscent('balance', $balanceDataArray, array('balance_id' => $balanceData->balance_id ));
						} 
            echo json_encode(array('alertType' => 'success', 'msg' => 'Worker marked present successfully.' ));
            }else{
            echo json_encode(array('alertType' => 'success', 'msg' => 'Worker marked present successfully.' ));
            }
          }else{
            echo json_encode(array('alertType' => 'Error', 'msg' => 'Error occured while marking attendance.' ));
          }
        }
        }else{
          echo json_encode(array('alertType' => 'warning', 'msg' => 'Worker attendance is already marked.' ));
        }
    }

	public function ajax_bulk_make_payment(){
      $formData = $this->input->post('formData');
      $counter = 1;
      $error = 0;
      foreach ($formData as $value) {
		if($counter == 2){
			$id = $value;
		}elseif($counter == 4){
			$amount = $value;
		}elseif($counter == 6){
			$project = $value;
            //echo '<br/> id=> '.$id.'Amount=> '.$amount.'project=> '.$project;
            $this->ajax_makePayment($id,$amount,$project); 
            $counter = $counter - 6;
		}			
		$counter++;
      }
    }

    public function ajax_makePayment($id,$amount,$project){
        $pid = $project;
        $worker_id = $id;

        $wage = $this->register->get_wage($id);

        $due_amount = $wage->worker_due_wage - ($amount);

        $updateData = 
			array(
                'worker_due_wage' => $due_amount, 
            );
        $where = 'worker_wage_id = '.$wage->worker_wage_id;
        $updatelabour = $this->register->update('worker_wage',$updateData,$where);

        if($updatelabour){
            $paymentData = array(
                'project_id' => $pid, 
                'worker_id' => $worker_id, 
                'user_id' => $this->session->userdata('id'),
                'payment_date_time' => date('Y-m-d h:i:s'), 
                'payment_amount' => $amount, 
            );
            $addPayment = $this->register->save('payment',$paymentData);
            if($addPayment){
				//Update balance
				$balanceMonth = date('mY');

				$balanceData = $this->register->get_balance($worker_id,$balanceMonth);
				if($balanceData){
					$balanceDataArray = array('balance' => $balanceData->balance - $amount);
					$balanceUpdate = $this->register->abscent('balance', $balanceDataArray, array('balance_id' => $balanceData->balance_id ));
				}       
            echo json_encode(array('alertType' => 'success', 'msg' => 'Payment made successfully.' ));
            }else{
              echo json_encode(array('alertType' => 'error', 'msg' => 'Error occured while making payment.' ));
            }
        }else{
          echo json_encode(array('alertType' => 'error', 'msg' => 'Error occured while making payment.' ));
        }

    }

    public function ajax_fetchHajiri() {
        $worker_id = $this->input->post('worker_id');
        $wage = $this->register->get_wage($this->input->post('worker_id'));

        $lastWithDrawal = $this->register->lastRecord($worker_id);
        $response['amount'] = (isset($lastWithDrawal)) ? $lastWithDrawal->amount : '0'; 

        if($wage->worker_wage_type == 1){
            $date = date('Y-m-d');
            $m = date("m", strtotime($date));
            $y = date("Y", strtotime($date));
            $noOfDays = cal_days_in_month(CAL_GREGORIAN, $m,$y);
            $noOfWeekOff = $this->weekOff->countWeekOff('company_id = '.$this->session->userdata('company_id').' AND status = 1');
            $days = $noOfDays - $noOfWeekOff ;
            $wage->worker_wage = round(($wage->worker_wage / $days),1);
        }
        if($wage->worker_wage > 0){
            $response['hajiri'] = $wage->worker_wage;
            echo json_encode($response);
        }else{
            $response['hajiri'] = 0;
            echo json_encode($response);
        }
    }

    public function ajax_delete($id) {
        $where = 'worker_id = '.$id;
        $data = array(
            'status' => 0,
        );
        $worker_id = $this->register->abscent('worker',$data,$where);
        
        $this->session->set_flashdata('success', 'Worker deleted present successfully');
    }


    public function ajax_change_status($id) {
        $where = 'worker_id = '.$id;
        $result = $this->labour->get_by_id($id);
        if ($result->status == "1") {
            $data = array(
                'status' => 2,
            );
        }else{
            $data = array(
                'status' => 1,
            );
        }
        $worker_id = $this->register->abscent('worker',$data,$where);
        
        $this->session->set_flashdata('success', 'Worker status changed successfully');
    }

    public function ajax_get_categoryList($id) {
        $prjlist = "";
        $category = $this->category->get_all_category($id);
        if (!empty($category)) {
            $prjlist .= '<select name="category" class="form-control">';
            $prjlist .= '<option value = "">--Select Category--</option>';
            foreach ($category as $project) {
                $prjlist .= "<option value = '" . $project->id . "'>" . $project->category . "</option>";
            }
            $prjlist .= '</select>';
        } else {
            $prjlist .= '<select name="category" class="form-control">';
            $prjlist .= '<option value = "">--No Category Available--</option>';
            $prjlist .= '</select>';
        }
        $result['projectlist'] = $prjlist;
        echo json_encode($result);
    }

}
?>