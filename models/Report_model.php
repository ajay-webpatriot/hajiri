<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model {

	public function getMonthlyAttendance( $data ){
			$sql = " SELECT A.attendance_id, DAY( A.attendance_date_time ) attendance_day, A. hajiri, A.status, A.worker_id, A.amount, W.labour_name, W.labour_last_name, WC.category_id, WC.category_name, WW.worker_wage, WW.worker_wage_type
				FROM attendance A
				JOIN worker W ON W.worker_id = A.worker_id
				JOIN worker_category WC ON WC.category_id = W.category_id
				JOIN ( SELECT * FROM worker_wage WHERE wage_end_date IS NULL ) WW ON WW.worker_id = W.worker_id
				WHERE 
					A.project_id = ? 
					AND WC.category_id LIKE ?
					AND MONTH( A.attendance_date_time ) = ? AND YEAR( A.attendance_date_time ) = ?
					AND A.status IN ( 1, 3 )
					AND A.user_id IN ( SELECT user_id 
										FROM user 
										WHERE company_id =  ? )
				Order BY A.attendance_id ASC ";
			return $this->db->query( $sql, array( $data['project_id'], $data['category_id'], $data['month'], $data['year'], $data['company_id'] ) )->result_array();
	}
	
	public function getHolidays( $data, $noOfDays ){
		$where = array( 'company_id' => $data['company_id'], 'holiday_date >= ' => $data['year'] . '-' . $data['month'] . '-01', 'holiday_date <= ' => $data['year'] . '-' . $data['month']. '-' . $noOfDays, 'status' => 1);
		
		$this->db->select( 'GROUP_CONCAT( Day( holiday_date ) ) holiday_day , GROUP_CONCAT( holiday_name) holiday_name' );
        $this->db->from('holidays');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->row();
		
	}
	
	public function getWeekOff( $where ){
		
		$this->db->select( 'GROUP_CONCAT( day ) days' );
        $this->db->from( 'week_off_days' );
        $this->db->where( $where );
        $query = $this->db->get();
        return $query->row();
		
	}
    // ,sum(P.payment_amount) advance_payment
	public function getMonthlyPayment( $data ){
		$sql = " SELECT P.*, DAY( P.payment_date_time ) payment_day, WC.category_id, WC.category_name,sum(P.payment_amount) advance_payment
				FROM payment P
				JOIN worker W ON W.worker_id = P.worker_id
				JOIN worker_category WC ON WC.category_id = W.category_id
				WHERE 
					P.project_id = ? 
					AND WC.category_id LIKE ?
					AND MONTH( P.payment_date_time ) = ? AND YEAR( P.payment_date_time ) = ? 
					AND P.user_id IN ( SELECT user_id 
										FROM user 
										WHERE company_id =  ? )
										GROUP BY P.worker_id , payment_day
										";

        return $this->db->query( $sql, array( $data['project_id'], $data['category_id'], $data['month'], $data['year'], $data['company_id'] ) )->result_array();
	}
	
	public function getMonthlyLabourData( $data ){
		$strSql ='';
		if( isset( $data['labour_id'] ) ){
			$strSql = ' AND A.worker_id = '.$data['labour_id'];
		}
		
		$sql = " SELECT A.attendance_id, DAY( A.attendance_date_time ) attendance_day, A. hajiri, A.status, A.worker_id, A.amount, W.labour_name, W.labour_last_name, WC.category_id, WC.category_name, WW.worker_wage,WW.worker_wage_type, P.project_id, P.project_name
				FROM attendance A
				JOIN worker W ON W.worker_id = A.worker_id ".$strSql." 
				JOIN worker_category WC ON WC.category_id = W.category_id
				JOIN ( SELECT * FROM worker_wage WHERE worker_id = ? ORDER BY worker_wage_id DESC LIMIT 1 ) WW ON WW.worker_id = W.worker_id 
				JOIN project P ON P.project_id = A.project_id 
				WHERE
					MONTH( A.attendance_date_time ) = ? AND YEAR( A.attendance_date_time ) = ?
					AND A.status IN ( 1, 3 )
					AND A.user_id IN ( SELECT user_id 
										FROM user 
										WHERE company_id =  ? )
				Order BY A.attendance_id ASC ";
        return $this->db->query( $sql, array( $data['labour_id'], $data['month'], $data['year'], $data['company_id'] ) )->result_array();
	}
	
	public function getMonthlyLabourPaymentData( $data ){
		$sql = " SELECT SUM( payment_amount ) payment_amount
				FROM payment P
				JOIN worker W ON W.worker_id = P.worker_id AND P.worker_id = ?
				WHERE
					MONTH( P.payment_date_time ) = ? AND YEAR( P.payment_date_time ) = ? 
					AND P.user_id IN ( SELECT user_id 
										FROM user 
										WHERE company_id =  ? )";
        return $this->db->query( $sql, array( $data['labour_id'], $data['month'], $data['year'], $data['company_id'] ) )->row()->payment_amount;
		//echo $this->db->last_query(); exit;
	}
	
	public function getOpeningBalance( $data ) {  
		/* prev month */
        $time = mktime(0, 0, 0, $data['month'], 1, $data['year']);
        $prev_year = date('Y', strtotime('-1 month', $time));
        $prev_month = date('m', strtotime('-1 month', $time));
		
		$numOfWeekOff = 0;
		$noOfDays = date("t", strtotime( $prev_year . '-' . $prev_month ) );
		if ( !empty( $data['week_offs'] ) ) {
			$fromDate = date( 'Y-m-01 ',strtotime( $prev_year.'-'.$prev_month.'-01' ) );
			$toDate = date( 'Y-m-d ',strtotime( $prev_year.'-'.$prev_month.'-'.$noOfDays ) );
			for ( $i = 0; $i <= ((strtotime($toDate) - strtotime($fromDate)) / 86400); $i++ ) {
				//echo date('l',strtotime($fromDate) + ($i * 86400)).'<br>';
				if( in_array( date('l',strtotime($fromDate) + ($i * 86400)), $data['week_offs'] ) ) {
						$numOfWeekOff++;
				}    
			}
		}
		
		$sql = " SELECT sum( A.amount )amount, WW.worker_wage, WW.worker_wage_type
				FROM attendance A
				JOIN ( SELECT * FROM worker_wage WHERE wage_end_date IS NULL ) WW ON WW.worker_id = A.worker_id
				WHERE 
					A.project_id = ? 
					AND MONTH( A.attendance_date_time ) = ? and YEAR( A.attendance_date_time ) = ?
					AND A.status IN ( 1, 3 )
					AND A.user_id IN ( SELECT user_id 
										FROM user 
										WHERE company_id =  ? )
				GROUP BY A.worker_id ";
        $result = $this->db->query( $sql, array( $data['project_id'], $prev_month, $prev_year, $data['company_id'] ) )->result_array();

		$expense_amount = 0;
		foreach( $result as $id => $worker ){ 
			if( $worker['worker_wage_type'] == 1 && $numOfWeekOff > 0 ){
				$worker['amount'] += ( $numOfWeekOff * $worker['worker_wage'] );
			}
			$expense_amount += $worker['amount'];
		}
		
		$sql = " SELECT sum( P.payment_amount ) payment_amount
				FROM payment P
				WHERE 
					P.project_id = ? 
					AND MONTH( P.payment_date_time ) = ? and YEAR( P.payment_date_time ) = ?
					AND P.user_id IN ( SELECT user_id 
										FROM user 
										WHERE company_id =  ? ) ";
        $payment_amount = $this->db->query( $sql, array( $data['project_id'], $prev_month, $prev_year, $data['company_id'] ) )->row()->payment_amount;
		
		return $expense_amount - $payment_amount;
	}
	
	public function getKharachiData( $data ){
		$sql = " SELECT K.*, DAY( K.date_time ) kharchi_day, ( S.user_name ) supervisor_name , ( S.user_last_name ) supervisor_last_name, A.user_name, A.user_last_name
				FROM kharchi K 
				JOIN user S ON S.user_id = K.supervisor_id 
				JOIN user A ON A.user_id = K.admin_id
				WHERE K.company_id = ? 
					AND K.project_id = ?  
					AND K.supervisor_id LIKE ?
					AND MONTH( K.date_time ) = ? AND YEAR( K.date_time ) = ? 
					AND ( K.status = 1 OR ( K.status = 0 AND K.debit_credit_status = 0 ) ) ";
        return $this->db->query( $sql, array( $data['company_id'], $data['project_id'], $data['supervisor_id'], $data['month'], $data['year'] ) )->result_array();
		//echo $this->db->last_query(); exit;
	}
	
	public function getSupervisorOpeningBalance( $data ){
		$sql = " SELECT K.supervisor_id, SUM(K.amount) credit_amount, ( S.user_name ) supervisor_name , ( S.user_last_name ) supervisor_last_name
				FROM kharchi K 
				JOIN user S ON S.user_id = K.supervisor_id 
				WHERE K.company_id = ? 
					AND K.project_id = ?  
					AND K.supervisor_id LIKE ?
					AND DATE( K.date_time ) < ? 
					AND K.debit_credit_status = 0  
				GROUP BY supervisor_id";
        $creditArray = $this->db->query( $sql, array( $data['company_id'], $data['project_id'], $data['supervisor_id'], $data['year'].'-'.$data['month'].'-01' ) )->result_array();
		
		$sql = " SELECT K.supervisor_id, SUM(K.amount) debit_amount, ( S.user_name ) supervisor_name , ( S.user_last_name ) supervisor_last_name
				FROM kharchi K 
				JOIN user S ON S.user_id = K.supervisor_id 
				WHERE K.company_id = ? 
					AND K.project_id = ?  
					AND K.supervisor_id LIKE ?
					AND DATE( K.date_time ) < ? 
					AND K.status = 1 AND K.debit_credit_status = 1 
				GROUP BY supervisor_id";
        $debitArray = $this->db->query( $sql, array( $data['company_id'], $data['project_id'],$data['supervisor_id'], $data['year'].'-'.$data['month'].'-01' ) )->result_array();
		
		foreach( $debitArray as $debit ){
			$flag = 1;
			foreach( $creditArray as $key => $credit ){
				if( $credit['supervisor_id'] == $debit['supervisor_id'] ){
					$creditArray[$key]['credit_amount'] = $credit['credit_amount'] -  $debit['debit_amount'];
					$flag = 0;
					break;
				}
			}
			if( $flag == 1 ){
				$creditArray[] = $debit;
			}
		}
		return $creditArray;
	}
	
	public function getResult($table) {
        $this->db->select('*');
        $this->db->from($table);
        $query = $this->db->get();
        return $query->result();
    }

    public function getWhereLimit($table, $where, $limit, $order_by = 'DESC') {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($where);
        $this->db->limit($limit);
        $this->db->order_by('qrcode_id', $order_by);
        $query = $this->db->get();
        return $query->result();
    }

    public function getWhereResult($table, $col, $val, $is_today = 'no', $labour_ids = array()) {
       
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($col, $val);
        if ($is_today != 'no') {
          $this->db->where("labour_join_date", date('Y-m-d'));
        }
        $query = $this->db->get();
       return $query->result();
    }

    public function getWhereResultSpecific($table, $col, $labour_ids = array(), $is_today = 'no') {
        $this->db->select('*');
        $this->db->from($table);
        if (is_array($labour_ids) && !empty($labour_ids)) {
            $this->db->where_in($col, $labour_ids);
        }
        if ($is_today != 'no') {
            $this->db->where("joindate", date('Y-m-d'));
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function getWhereRow($table, $col, $val) {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($col, $val);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_labours($company_id) {
        $this->db->select('*');
        $this->db->from('worker');
        $this->db->where('company_id', $company_id);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_where($table, $select, $where = array()) {
        $this->db->select($select);
        $this->db->from($table);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_all_managers($table) {
         $user_id = $this->session->userdata('id');
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($table . '.user_designation', 'admin');
        $this->db->where($table . '.user_id', $user_id);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_all_project($table) {
        $company_id = $this->session->userdata('company_id');
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($table . '.status', 1);
        $this->db->where($table . '.company_id', $company_id);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_all_labour($table) {
         $company_id = $this->session->userdata('company_id');
        $this->db->select('*');
        $this->db->from($table);
       // $this->db->where($table . '.status', 1);
        $this->db->where($table . '.company_id', $company_id);
        $query = $this->db->get();
        return $query->result();
    }
    public function getMaxAttendance($data){
    	$sql = " SELECT t.total_hajiri , t.worker_id, t.user_id, t.project_id, t.hajiri, t.attendance_date_time 
    			FROM ( 
    				SELECT SUM(hajiri) as total_hajiri , attendance.worker_id, attendance.user_id, attendance.project_id, attendance.hajiri, attendance.attendance_date_time 
    				FROM attendance 
    				INNER JOIN worker ON worker.worker_id = attendance.worker_id 
    				WHERE MONTH( attendance.attendance_date_time ) = ? AND YEAR( attendance.attendance_date_time ) = ? AND attendance.hajiri > 0
    				GROUP BY attendance.worker_id, attendance.project_id 
    				ORDER BY attendance.worker_id,`total_hajiri` DESC , attendance.project_id DESC ) t GROUP BY t.worker_id";
		return $query = $this->db->query( $sql, array( $data['month'], $data['year'] ) )->result_array();
    }
    public function getMonthlyPaymentBylabour( $data ){
    	$sql = "SELECT DAY(payment_date_time) payment_day, payment_date_time, sum(P.payment_amount) payment_amount
				FROM payment P
				WHERE 
					P.worker_id = ? 
					AND MONTH( P.payment_date_time ) = ? AND YEAR( P.payment_date_time ) = ? 
					GROUP BY  DAY( P.payment_date_time )";

        return $this->db->query( $sql, array( $data['labour_id'], $data['month'], $data['year']) )->result_array();
	}


	public function getOpeningBalanceByWorker( $data ) {  
	 	/* prev month */
        $time = mktime(0, 0, 0, $data['month'], 1, $data['year']);
        $prev_year = date('Y', strtotime('-1 month', $time));
        $prev_month = date('m', strtotime('-1 month', $time));
		
		$numOfWeekOff = 0;

		$noOfDays = date("t", strtotime( $prev_year . '-' . $prev_month ) );
		if ( !empty( $data['week_offs'] ) ) {
			$fromDate = date( 'Y-m-01 ',strtotime( $prev_year.'-'.$prev_month.'-01' ) );
			$toDate = date( 'Y-m-d ',strtotime( $prev_year.'-'.$prev_month.'-'.$noOfDays ) );
			for ( $i = 0; $i <= ((strtotime($toDate) - strtotime($fromDate)) / 86400); $i++ ) {
				//echo date('l',strtotime($fromDate) + ($i * 86400)).'<br>';
				if( in_array( date('l',strtotime($fromDate) + ($i * 86400)), $data['week_offs'] ) ) {
						$numOfWeekOff++;
				}    
			}
		}

		$sql = "SELECT sum( A.amount )amount,sum(A.hajiri)hajiri, WW.worker_wage, WW.worker_wage_type
				FROM attendance A
				JOIN ( SELECT * FROM worker_wage WHERE wage_end_date IS NULL ) WW ON WW.worker_id = A.worker_id
				WHERE 
					A.worker_id = ? 
					AND MONTH( A.attendance_date_time ) = ? and YEAR( A.attendance_date_time ) = ?
					AND A.status IN ( 1, 3 )
					AND A.user_id IN ( SELECT user_id 
										FROM user 
										WHERE company_id =  ? )
				GROUP BY A.worker_id ";
        $result = $this->db->query( $sql, array( $data['labour_id'], $prev_month, $prev_year, $data['company_id'] ) )->result_array();
        $expense_amount = 0;
		
		foreach( $result as $id => $worker ){ 

			if( $worker['worker_wage_type'] == 1 && $numOfWeekOff > 0 ){
				 
				$workingdays = $noOfDays - $numOfWeekOff;

				$dailywage = $worker['worker_wage'] / $workingdays;

				$total_day =  number_format($worker['hajiri'] , 2) + $numOfWeekOff ;
				  
				$amount = ( number_format($total_day , 2) * number_format($dailywage , 2));

				$worker['amount'] = round($amount);
			}
			$expense_amount += $worker['amount'];
		}
		 
		 
		$sql = " SELECT sum( P.payment_amount ) payment_amount
				FROM payment P
				WHERE 
					P.worker_id = ? 
					AND MONTH( P.payment_date_time ) = ? and YEAR( P.payment_date_time ) = ?
					AND P.user_id IN ( SELECT user_id 
										FROM user 
										WHERE company_id =  ? ) ";

        $payment_amount = $this->db->query( $sql, array( $data['labour_id'], $prev_month, $prev_year, $data['company_id'] ) )->row()->payment_amount;

        return $expense_amount - $payment_amount; 
	}
}
