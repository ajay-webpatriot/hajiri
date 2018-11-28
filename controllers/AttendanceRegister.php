<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AttendanceRegister extends CI_Controller {

    public $data;
	public function __construct() {
        parent::__construct();
		$this->data['menu_title'] = 'Attendance register';
        $this->load->model('Register_model', 'register');        
        $this->load->model('labour_model', 'labour');        
        $this->load->model('Category_model', 'category');
        $this->load->model('Foreman_model', 'foreman');
        $this->load->model('project_model', 'project');
        $this->load->helper('SendSms');
        checkAdmin();
    }

    public function index() {
       
        $this->breadcrumbcomponent->add('Dashboard', base_url('/admin'));
        $this->breadcrumbcomponent->add('All Worker', base_url('/admin/attendance_register'));
        $data['breadcrumb'] = $this->breadcrumbcomponent->output();
        $data = $this->data;
		if($this->session->userdata('user_designation') == 'admin')
			$data['projects'] = $this->project->get_active_projects();
		if($this->session->userdata('user_designation') == 'Supervisor'){
			$data['projects'] = $this->foreman->get_project_foremanId( $this->session->userdata('id'));
		}
        $data['supervisor'] = $this->register->get_activeUser();
        $data['Category'] = $this->category->get_all_category($this->session->userdata('company_id'));
        $data['title'] = 'Attendance register';
        $data['description'] = 'All worker list';
        $data['page'] = 'register/attendance_register';
        $this->load->view('includes/template', $data);

    }

    public function attendanceDatatable()
        {
            $columns = array( 
                                0 =>'labour_name', 
                                1 =>'category_name',
                            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
      
            $totalData = $this->register->allattendance_count();
                
            $totalFiltered = $totalData; 
                
            if(!empty($this->input->post('date')) && !empty($this->input->post('category')) && !empty($this->input->post('project')) && !empty($this->input->post('supervisor')) ){
                $date = date("Y-m-d", strtotime($this->input->post('date'))); 
                $category = $this->input->post('category');

                $where =  'attendance.attendance_date_time LIKE "'.$date.'%" AND worker_category.category_id = "'. $category.'" AND project.project_id = "'.$this->input->post('project').'" AND user_id = '.$this->input->post('supervisor');
                if(empty($this->input->post('search')['value'])){
                    $posts =  $this->register->attendance_where_search($limit,$where,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_search_count($where);
                }
                else{
                    $search = $this->input->post('search')['value'];
                    $posts =  $this->register->attendance_where_text_search($limit,$where,$search,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_text_search_count($where,$search);
                }
            }elseif(!empty($this->input->post('date')) && !empty($this->input->post('category')) && !empty($this->input->post('supervisor')) ){
                $date = date("Y-m-d", strtotime($this->input->post('date'))); 
                $category = $this->input->post('category');

                $where =  'attendance.attendance_date_time LIKE "'.$date.'%" AND worker_category.category_id = "'. $category.'" AND user_id = '.$this->input->post('supervisor');
                if(empty($this->input->post('search')['value'])){
                    $posts =  $this->register->attendance_where_search($limit,$where,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_search_count($where);
                }
                else{
                    $search = $this->input->post('search')['value'];
                    $posts =  $this->register->attendance_where_text_search($limit,$where,$search,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_text_search_count($where,$search);
                }

            }elseif(!empty($this->input->post('date')) && !empty($this->input->post('project'))  && !empty($this->input->post('supervisor')) ){
                $date = date("Y-m-d", strtotime($this->input->post('date'))); 

                $where =  'attendance.attendance_date_time LIKE "'.$date.'%" AND project.project_id = "'.$this->input->post('project').'" AND user_id = '.$this->input->post('supervisor');

               if(empty($this->input->post('search')['value'])){
                    $posts =  $this->register->attendance_where_search($limit,$where,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_search_count($where);
                }
                else{
                    $search = $this->input->post('search')['value'];
                    $posts =  $this->register->attendance_where_text_search($limit,$where,$search,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_text_search_count($where,$search);
                }

            }elseif(!empty($this->input->post('category')) && !empty($this->input->post('project'))  && !empty($this->input->post('supervisor')) ){
                $category = $this->input->post('category');
                $where =  'worker_category.category_id = "'. $category.'" AND project.project_id = "'.$this->input->post('project').'" AND user_id = '.$this->input->post('supervisor');

                if(empty($this->input->post('search')['value'])){
                    $posts =  $this->register->attendance_where_search($limit,$where,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_search_count($where);
                }
                else{
                    $search = $this->input->post('search')['value'];
                    $posts =  $this->register->attendance_where_text_search($limit,$where,$search,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_text_search_count($where,$search);
                }

            }elseif(!empty($this->input->post('date')) && !empty($this->input->post('category')) && !empty($this->input->post('project')) ){
                $date = date("Y-m-d", strtotime($this->input->post('date'))); 
                $category = $this->input->post('category');

                $where =  'attendance.attendance_date_time LIKE "'.$date.'%" AND worker_category.category_id = "'. $category.'" AND project.project_id = "'.$this->input->post('project').'"';
                if(empty($this->input->post('search')['value'])){
                    $posts =  $this->register->attendance_where_search($limit,$where,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_search_count($where);
                }
                else{
                    $search = $this->input->post('search')['value'];
                    $posts =  $this->register->attendance_where_text_search($limit,$where,$search,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_text_search_count($where,$search);
                }
            }elseif(!empty($this->input->post('date')) && !empty($this->input->post('category'))){
                $date = date("Y-m-d", strtotime($this->input->post('date'))); 
                $category = $this->input->post('category');

                $where =  'attendance.attendance_date_time LIKE "'.$date.'%" AND worker_category.category_id = "'. $category.'"';
                if(empty($this->input->post('search')['value'])){
                    $posts =  $this->register->attendance_where_search($limit,$where,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_search_count($where);
                }
                else{
                    $search = $this->input->post('search')['value'];
                    $posts =  $this->register->attendance_where_text_search($limit,$where,$search,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_text_search_count($where,$search);
                }

            }elseif(!empty($this->input->post('date')) && !empty($this->input->post('project'))){
                $date = date("Y-m-d", strtotime($this->input->post('date'))); 

                $where =  'attendance.attendance_date_time LIKE "'.$date.'%" AND project.project_id = "'.$this->input->post('project').'"';

               if(empty($this->input->post('search')['value'])){
                    $posts =  $this->register->attendance_where_search($limit,$where,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_search_count($where);
                }
                else{
                    $search = $this->input->post('search')['value'];
                    $posts =  $this->register->attendance_where_text_search($limit,$where,$search,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_text_search_count($where,$search);
                }

            }elseif(!empty($this->input->post('category')) && !empty($this->input->post('project'))){
                $category = $this->input->post('category');
                $where =  'worker_category.category_id = "'. $category.'" AND project.project_id = "'.$this->input->post('project').'"';

                if(empty($this->input->post('search')['value'])){
                    $posts =  $this->register->attendance_where_search($limit,$where,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_search_count($where);
                }
                else{
                    $search = $this->input->post('search')['value'];
                    $posts =  $this->register->attendance_where_text_search($limit,$where,$search,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_text_search_count($where,$search);
                }

            }elseif(!empty($this->input->post('date')) && !empty($this->input->post('supervisor'))  ){
                $date = date("Y-m-d", strtotime($this->input->post('date'))); 

                $where =  'attendance.attendance_date_time LIKE "'.$date.'%" AND attendance.user_id = '.$this->input->post('supervisor');
                if(empty($this->input->post('search')['value'])){
                    $posts =  $this->register->attendance_where_search($limit,$where,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_search_count($where);
                }
                else{
                    $search = $this->input->post('search')['value'];
                    $posts =  $this->register->attendance_where_text_search($limit,$where,$search,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_text_search_count($where,$search);
                }
            }elseif(!empty($this->input->post('project')) && !empty($this->input->post('supervisor'))  ){

                $where =  'project.project_id = "'.$this->input->post('project').'" AND user_id = '.$this->input->post('supervisor');
                if(empty($this->input->post('search')['value'])){
                    $posts =  $this->register->attendance_where_search($limit,$where,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_search_count($where);
                }
                else{
                    $search = $this->input->post('search')['value'];
                    $posts =  $this->register->attendance_where_text_search($limit,$where,$search,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_text_search_count($where,$search);
                }
            }elseif(!empty($this->input->post('category')) && !empty($this->input->post('supervisor'))  ){
                

                $where =  'worker_category.category_id = "'. $this->input->post('category').'" AND user_id = '.$this->input->post('supervisor');
                if(empty($this->input->post('search')['value'])){
                    $posts =  $this->register->attendance_where_search($limit,$where,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_search_count($where);
                }
                else{
                    $search = $this->input->post('search')['value'];
                    $posts =  $this->register->attendance_where_text_search($limit,$where,$search,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_text_search_count($where,$search);
                }
            }elseif(!empty($this->input->post('project'))){
                $where = array('project.project_id' => $this->input->post('project'), );

                if(empty($this->input->post('search')['value'])){
                    $posts =  $this->register->attendance_where_search($limit,$where,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_search_count($where);
                }
                else{
                    $search = $this->input->post('search')['value'];
                    $posts =  $this->register->attendance_where_text_search($limit,$where,$search,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_text_search_count($where,$search);
                }

            }elseif(!empty($this->input->post('category'))){
                $where = array('worker_category.category_id' => $this->input->post('category'), );

                if(empty($this->input->post('search')['value'])){
                    $posts =  $this->register->attendance_where_search($limit,$where,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_search_count($where);
                }
                else{
                    $search = $this->input->post('search')['value'];
                    $posts =  $this->register->attendance_where_text_search($limit,$where,$search,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_text_search_count($where,$search);
                }

            }
            elseif(!empty($this->input->post('date'))){
                $date = date("Y-m-d", strtotime($this->input->post('date'))); 

                $where =  'attendance.attendance_date_time LIKE "'.$date.'%"';

                if(empty($this->input->post('search')['value'])){
                    $posts =  $this->register->attendance_where_search($limit,$where,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_search_count($where);
                }
                else{
                    $search = $this->input->post('search')['value'];
                    $posts =  $this->register->attendance_where_text_search($limit,$where,$search,$start,$order,$dir);
                    $totalFiltered = $this->register->attendance_where_text_search_count($where,$search);
                }

            }elseif(empty($this->input->post('search')['value']))
            {            
                $posts = $this->register->allattendance($limit,$start,$order,$dir);
            }
            else {
                $search = $this->input->post('search')['value']; 

                $posts =  $this->register->attendance_search($limit,$start,$search,$order,$dir);

                $totalFiltered = $this->register->attendance_search_count($search);
            }

            $data = array();
            if(!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $nestedData['id'] = $post->attendance_id;
                    $nestedData['labour_name'] = '<p class="capitalize">'.ucwords(strtolower($post->labour_name)).' '.ucwords(strtolower($post->labour_last_name)).'</p>';
                    $nestedData['category_name'] = $post->category_name;
                    $nestedData['hajiri_rate'] = $post->hajiri.' | &#8377;'.$post->amount;
                    
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


    public function ajax_absent() {
        $counter = 1;
		$formData = $this->input->post('formData');
		foreach ($formData as $value)  {
			if($counter == 1){
				$id = $value;
				$counter++;
			}elseif($counter == 2){
				$absentReason = $value;
				$counter--;
				$data = array(
					'user_id' => $this->session->userdata('user_id'),
					'status' => 2,
					'absent_date_time' => date('Y-m-d h:i:s'),
					'reason_for_absent' => $absentReason,
				);
				$attendanceDetails = $this->register->attendanceDetail($id);
				$amount = $attendanceDetails->amount;
				$workerId= $attendanceDetails->worker_id;

				$balanceMonth = date('mY', strtotime($attendanceDetails->attendance_date_time));
				$balanceData = $this->register->get_balance($workerId,$balanceMonth);
				if($balanceData){
					$balanceDataArray = array('balance' => $balanceData->balance - $amount);
					$balanceUpdate = $this->register->abscent('balance', $balanceDataArray, array('balance_id' => $balanceData->balance_id ));
				}
				$Wage = $this->register->get_wage($workerId);

				$wageData = array(
						'worker_total_wage' => $Wage->worker_total_wage - $amount, 
						'worker_due_wage' => $Wage->worker_due_wage - $amount, 
				);
				$register_id = $this->register->abscent('attendance', $data, array('attendance_id' => $id) );

				$wageId = $this->register->abscent('worker_wage', $wageData, array('worker_wage_id' => $Wage->worker_wage_id ));
				if($wageId)
                  echo json_encode(array('alertType' => 'success', 'msg' => 'Worker Absent marked successfully.' ));
				else {
					echo json_encode(array('alertType' => 'error', 'msg' => 'Error ocurred while marking absent.' ));
				}
			}/* End of if */
        } /*End of ForEach*/
    }

    public function ajax_change_hajiri() {
        $formData = $this->input->post('formData');
        $counter = 1;
        $error = 0;
        foreach ($formData as $value) {
            if($counter == 6){
                $postAmount = $value;
                $hajiriData = array('hajiri' => $hajiri, 'amount' => $postAmount );

				$attendanceDetails = $this->register->attendanceDetail($id);
				$amount = $attendanceDetails->amount;
				$workerId= $attendanceDetails->worker_id;

				$balanceMonth = date('mY', strtotime($attendanceDetails->attendance_date_time));
				$balanceData = $this->register->get_balance($workerId,$balanceMonth);
				if($balanceData){
					$balanceDataArray = array('balance' => $balanceData->balance - ($amount - $postAmount));
					$balanceUpdate = $this->register->abscent('balance', $balanceDataArray, array('balance_id' => $balanceData->balance_id ));
				}
				$Wage = $this->register->get_wage($workerId);

				$wageData = array(
						'worker_total_wage' => $Wage->worker_total_wage - ($amount - $postAmount), 
						'worker_due_wage' => $Wage->worker_due_wage - ($amount - $postAmount), 
				);
                $absent = $this->labour->update('attendance', array('attendance_id' => $id), $hajiriData);
                $wageId = $this->register->abscent('worker_wage', $wageData, array('worker_wage_id' => $Wage->worker_wage_id ));
				if(!$wageId) {
                   $error = 1;
                } 
                $counter = $counter - 6;
            }else{
                if($counter == 2)
                    $id = $value;
                elseif($counter == 4)
                    $hajiri = $value;
                elseif($counter == 6){
                    $amount = $value;
                }
            }
            $counter++;
        }
        if($error == 0){
            echo json_encode(array('alertType' => 'success', 'msg' => 'Worker Hajiri Updated  successfully.' ));
        }else{
            echo json_encode(array('alertType' => 'error', 'msg' => 'Error occurred while changing Hajiri.' ));
        }
    } /*end of change hajiri multiple*/

	public function checkDate(){
		$your_date =  $this->input->post('date');

		if(date('d') > 20 && date('m',strtotime($your_date)) == date('m') )
			echo true;
		else if(date('d') < 20 && date('m',strtotime($your_date)) <= (date('m') - 1) )
			echo json_encode(false);
		else if(date('d') < 20 && date('m',strtotime($your_date)) >= (date('m') - 1) )
			echo json_encode(true);
		else 
			echo json_encode(false);
	}

	public function sendSmsData(){
		$where = null;
		if(!empty($this->input->post('date')))
        {  
			$date = date('Y-m-d', strtotime($this->input->post('date')) );
            $where .= 'attendance.attendance_date_time LIKE "'.$date.'%"';
        }
		if(!empty($this->input->post('project')))
        {   
			if($where == null)
            $where .= 'attendance.project_id = "'.$this->input->post('project').'"';
			else
			$where .= ' AND attendance.project_id = "'.$this->input->post('project').'"';
        }
		if(!empty($this->input->post('supervisor')))
        {   
			if($where == null)
            $where .= 'attendance.user_id = "'.$this->input->post('supervisor').'"';
			else
			$where .= ' AND attendance.user_id = "'.$this->input->post('supervisor').'"';
        }
			
		if(!empty($this->input->post('category')))
        {   
			if($where == null)
            $where .= 'worker_category.category_id = "'.$this->input->post('category').'"';
			else
			$where .= ' AND worker_category.category_id = "'.$this->input->post('category').'"';
        }
		$totalPresent = $this->register->allSmsAttendanceCount($this->input->post('category'),$where);
		if($where == null)
            $where .= ' worker.worker_contact != ""';
		else
			$where .= ' AND worker.worker_contact != ""';

		$totalNumber = $this->register->allSmsNumberCount($this->input->post('category'),$where);
		$smsData =  new stdClass();
		//$smsData = $this->register->allSmsNumberData($this->input->post('category'),$where);
		$smsData->totalPresent = $totalPresent;
		$smsData->totalNumber = $totalNumber;
		echo json_encode($smsData);
	}

	public function sendSmsHajiri(){
		$where = null;
		if(!empty($this->input->post('date')))
        {  
			$date = date('Y-m-d', strtotime($this->input->post('date')) );
            $where .= 'attendance.attendance_date_time LIKE "'.$date.'%"';
        }
		if(!empty($this->input->post('project')))
        {   
			if($where == null)
            $where .= 'attendance.project_id = "'.$this->input->post('project').'"';
			else
			$where .= ' AND attendance.project_id = "'.$this->input->post('project').'"';
        }
		if(!empty($this->input->post('supervisor')))
        {   
			if($where == null)
            $where .= 'attendance.user_id = "'.$this->input->post('supervisor').'"';
			else
			$where .= ' AND attendance.user_id = "'.$this->input->post('supervisor').'"';
        }
			
		if(!empty($this->input->post('category')))
        {   
			if($where == null)
            $where .= 'worker_category.category_id = "'.$this->input->post('category').'"';
			else
			$where .= ' AND worker_category.category_id = "'.$this->input->post('category').'"';
        }
		if($where == null)
            $where .= ' worker.worker_contact != ""';
		else
			$where .= ' AND worker.worker_contact != ""';

		$smsData =  new stdClass();
		$workerData = $this->register->allSmsNumberData($this->input->post('category'),$where);
		$selectedDate = date('d-m-Y', strtotime($this->input->post('date')));
		$companyName = $this->session->userdata("company_name");
		$smsSentCounter = 0;
		foreach ($workerData as $smsData){
			$number = urlencode($smsData->contact);
			$message_body = urlencode('Namastey! '.$smsData->name.', '.$companyName.' mein '.$selectedDate.' ko aapko '.$smsData->hajiri.' Hajiri rate ke hisaabse Rs. '.$smsData->amount.' mile hai. Aapke khaate mein kul Rs. '.$smsData->due.' jama hai.');
			$smsSent = SendSms($number, $message_body);
			$smsSent = json_decode($smsSent, true);
			if($smsSent['ErrorCode'] == '000'){
				echo ++$smsSentCounter;}
		}
		$smsCountData = array(
			'company_id' => $this->session->userdata("company_id"),
			'user_id' => $this->session->userdata("id"),
			'total_present' => $this->input->post('totalAttendance'),
			'total_sms' => $this->input->post('totalSms'),
			'sms_sent' => $smsSentCounter,
		);
		$saveSmsCount = $this->foreman->save('hajiri_sms', $smsCountData);
		if($saveSmsCount)
			$this->session->set_flashdata("success", $smsSentCounter." Hajiri SMS sent successfully.");
		else
			$this->session->set_flashdata("error"," Error in sending Hajiri SMS.");
	}

}
?>