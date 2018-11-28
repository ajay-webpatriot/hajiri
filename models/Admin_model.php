<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {
    
    public function getCount($table, $where){
		$this->db->select( '*' );
        $this->db->from( $table );
        $this->db->where( $where );
        return $this->db->count_all_results();
	}
     
	 public function count_all($table) {
         $company_id = $this->session->userdata('company_id');
        $this->db->select($table . '.*,');
        $this->db->from($table);
         $this->db->where($table.'.company_id',$company_id);
        return $this->db->count_all_results();
    }
     public function count_all_manager($table) {
       // echo "In Count Manager";
         $company_id = $this->session->userdata('company_id');

        $this->db->select($table . '.*,');
        $this->db->from($table);
        $this->db->where($table.'.user_designation','admin');
        $this->db->where($table.'.company_id',$company_id);
        return $this->db->count_all_results();
    }
     public function count_all_foreman($table) {
         $company_id = $this->session->userdata('company_id');
        $this->db->select($table . '.*,');
        $this->db->from($table);
        $this->db->where($table.'.user_designation','Supervisor');
        $this->db->where($table.'.company_id',$company_id);
        return $this->db->count_all_results();
    }
        public function getLogin($table, $username, $password) {
        $this->db->select("*");
        $this->db->from('user');
        $this->db->where('user_email', $username);
        $this->db->where('password', md5( $password ) );
        $this->db->where('portal_access != 1' );
        $this->db->where('status', 1 );
        $query = $this->db->get();
        return $result = $query->row();
    }
     function updateRecord($table, $id, $col, $data) {
        return $this->db->where($col, $id)->update($table, $data);
    }

    public function update_password($table, $data, $where) {
        return $this->db->where('id', $where)->update($table, $data);
        //return $this->db->update($table,$data,$where);
    }

    public function get_where_data_results($id) {
        return $this->db->where('user_id', $id)->get('user')->row();
    }

    public function get_company_detl($id) {
        $company_id = $this->session->userdata('company_id');
        return $this->db->where('compnay_id', $company_id)->get('company')->row();
    }

    public function get_company_name($id) {
        return $this->db->where('compnay_id', $id)->get('company')->row();
    }

      function getwhere($table, $col, $id) {
        return $this->db->where($col, $id)->get($table)->row();
    }
	public function checkUniqueUpdate($table,$whereCol,$whereVal,$col,$colVal){
		$where = array(
			$col		=> $colVal,
		);
		$this->db->select('*');
        $this->db->from($table);
        $this->db->where($where);
        $this->db->where($whereCol.' != "'.$whereVal.'"');
        $query = $this->db->get();
        return $query->row();
	}
    public function checkUniqueEmailUpdate($table, $where) {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->row();
    }

     public function get_all_where($table,$select,$where) {
        $this->db->select($select);
        $this->db->from($table);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result();
    }
    /************ Admin controllers ******************/


    public function count_all_strength($table) {
        $companyId =  $this->session->userdata('company_id');
        $this->db->select($table . '.id,');
        $this->db->from($table);
        $this->db->where($table.'.user_id  IN (select user_id from user where 
            company_id = '.$companyId.')');
        $this->db->where($table.'.attendance_date_time LIKE "'.date("Y-m-d").'%"');
        $this->db->where($table.'.status = 1');
		if ($this->session->userdata('user_designation') == 'Supervisor'){
			$this->db->where('attendance.project_id IN (select project_id from user_project where user_id = '.$this->session->userdata("id").')');
		}
        return $this->db->count_all_results();
    }
    
    public function todayap() {
        $this->db->select('count(p.project_name) AS count,p.project_name AS name, p.project_id');
        $this->db->from('attendance a');
        $this->db->join('worker b', 'a.worker_id=b.worker_id');
        $this->db->join('project p', 'p.project_id = a.project_id');
        $this->db->where('`a`.`attendance_date_time` LIKE "'.date("Y-m-d").'%"');
        $this->db->where('a.status = 1');
		if ($this->session->userdata('user_designation') == 'Supervisor'){
			$this->db->where('a.project_id IN (select project_id from user_project where user_id = '.$this->session->userdata("id").')');
		}
        $this->db->where('p.company_id = '.$this->session->userdata('company_id'));
        $this->db->group_by('p.project_name');
        $query = $this->db->get();
        return $result = $query->result();
    }

    public function todayac() {
        $this->db->select('count(c.category_name) AS count,c.category_name AS name');
        $this->db->from('attendance a');
        $this->db->join('worker b', 'a.worker_id=b.worker_id');
        $this->db->join('worker_category c', 'c.category_id=b.category_id');
        $this->db->where('`a`.`attendance_date_time` LIKE "'.date("Y-m-d").'%"');
        $this->db->where('a.status = 1');
		if ($this->session->userdata('user_designation') == 'Supervisor'){
			$this->db->where('a.project_id IN (select project_id from user_project where user_id = '.$this->session->userdata("id").')');
		}
        $this->db->group_by('c.category_name');
        $query = $this->db->get();
        return $result = $query->result();
    }

    public function average_attendance($table) {
        $companyId =  $this->session->userdata('company_id');
        $startDate = date('Y-m-d', strtotime('-6 days'));
        $endDate = date('Y-m-d');
        $this->db->select($table . '.id,');
        $this->db->from($table);
        $this->db->where($table.'.user_id  IN (select user_id from user where 
            company_id = '.$companyId.')');
        $this->db->where($table.'.attendance_date_time BETWEEN "'.$startDate.'%" AND "'.$endDate.'"');
        $this->db->where($table.'.status in (1,3)');
		if ($this->session->userdata('user_designation') == 'Supervisor'){
			$this->db->where('attendance.project_id IN (select project_id from user_project where user_id = '.$this->session->userdata("id").')');
		}
        $this->db->where('status = 1');
        return $this->db->count_all_results();
    }

    public function totalExpense($select,$id,$table) {
        $this->db->select($select);
        $this->db->from($table);
        $this->db->where($table.'.worker_id IN (SELECT worker_id from worker WHERE company_id = '.$id.' AND worker.status = 1)');
        $query = $this->db->get();
        return $result = $query->row();
    }

    public function total_expense_cat_wise($select,$company_id){
        $this->db->select($select);
        $this->db->from('`worker_wage` ww ');
        $this->db->join('worker w', ' w.worker_id = ww.worker_id');
        $this->db->join('worker_category cat', ' cat.category_id = w.category_id');
        $this->db->where('w.company_id = '.$company_id.' and w.status = 1');
        $this->db->group_by('cat.category_name');
        $query = $this->db->get();
        return $result = $query->result();
    }

    public function total($select,$id,$table) {
        $this->db->select($select);
        $this->db->from($table);
        $this->db->where($table.'.user_id IN (SELECT user_id from user WHERE company_id = '.$id.')');
        $this->db->where($table.'.project_id != ""');
        if($table != 'payment')
        $this->db->where($table.'.status = 1');
		if ($this->session->userdata('user_designation') == 'Supervisor'){
			$this->db->where('attendance.project_id IN (select project_id from user_project where user_id = '.$this->session->userdata("id").')');
		}
        $query = $this->db->get();
        return $result = $query->row();
    }

    public function total_wise($select,$joinTable,$joinClause,$id,$table){
        $this->db->select($select);
        $this->db->from($table.' a');
        $this->db->join($joinTable, $joinClause);
        $this->db->where('a.user_id IN (SELECT user_id from user WHERE company_id = '.$id.')');
        if($table != 'payment')
        $this->db->where('a.status',1);
        $this->db->where('a.project_id != ""');
		if ($this->session->userdata('user_designation') == 'Supervisor'){
			$this->db->where('a.project_id IN (select project_id from user_project where user_id = '.$this->session->userdata("id").')');
		}
        $this->db->group_by('p.project_name');
        $query = $this->db->get();
        return $result = $query->result();
    }


    public function todays_cost($select,$id,$table) {
        $this->db->select($select);
        $this->db->from($table.' a');
        $this->db->where('a.user_id IN (SELECT user_id from user WHERE company_id = '.$id.')');
        $this->db->where('`a.`attendance_date_time` LIKE "'.date("Y-m-d").'%"');
        $query = $this->db->get();
        return $result = $query->row();
    }

    public function totalCost_wise($select,$joinTable,$joinClause,$id,$table){
        $this->db->select($select);
        $this->db->from($table.' a');
        $this->db->join($joinTable, $joinClause);
        $this->db->where('a.user_id IN (SELECT user_id from user WHERE company_id = '.$id.')');
        $this->db->where('`a.`attendance_date_time` LIKE "'.date("Y-m-d").'%"');
        $this->db->where('a.project_id != ""');
        $this->db->group_by('p.project_name');
        $query = $this->db->get();
        return $result = $query->result();
    }
    
    public function avgap($id) {
        $startDate = date('Y-m-d', strtotime('-7 days'));
        $endDate = date('Y-m-d', strtotime('1 days'));
        $this->db->select('count(a.attendance_id) as count');
        $this->db->select('a.attendance_date_time AS date');
        $this->db->from('attendance a');
        $this->db->join('project p', 'p.project_id = a.project_id');
        $this->db->where('a.user_id IN (SELECT user_id from user WHERE company_id = '.$id.')');
        $this->db->where("`a.`attendance_date_time` BETWEEN '".$startDate."%' AND '".$endDate."%'");
        $this->db->group_by('a.attendance_date_time');
        $this->db->order_by('a.attendance_date_time', 'ASC');
        $query = $this->db->get();
        return $result = $query->result();
    }
    public function planId($table, $col, $id) {
        $this->db->select('plan_id, due_date');
        $this->db->from($table);
        $this->db->where($col, $id);
        $query = $this->db->get();
        return $query->row();
    }

    /*
    public function avgap() {
        $startDate = date('Y-m-d', strtotime('-6 days'));
        $endDate = date('Y-m-d');
        $this->db->select('count(labour.category) as count');
        $this->db->select('date');
        $this->db->from('labour');
        $this->db->join('attendance', 'labour.id = attendance.lid');
        $this->db->where('attendance.user_id',$this->session->userdata('id'));
        $this->db->where("attendance.date BETWEEN '".$startDate."' AND '".$endDate."'");
        $this->db->group_by('date');
        $query = $this->db->get();
        return $result = $query->result();
    }*/

/*
    public function total_attendance_payment($table) {
        $this->db->select('sum(amount) as tap');
        $this->db->from($table);
        $this->db->where($table.'.user_id',$this->session->userdata('id'));
        $query = $this->db->get();
        return $result = $query->row();
    }



    public function apw() {
        //select count(p.id) as count, name from project p inner join attendance b on p.id=b.pid where b.user_id=129 GROUP by name
        $this->db->select('count(project.id) as count');
        $this->db->select('project.name as name');
        $this->db->from('project');
        $this->db->join('attendance', 'project.id = attendance.pid');
        $this->db->where('attendance.user_id',$this->session->userdata('id'));
        $this->db->group_by('project.name');
        $query = $this->db->get();
        return $result = $query->result();
    }


    public function wetd() {
        $this->db->select('sum(amount) as count');
        $this->db->select('project.name as name');
        $this->db->from('project');
        $this->db->join('attendance', 'project.id = attendance.pid');
        $this->db->where('attendance.user_id',$this->session->userdata('id'));
        $this->db->group_by('project.name');
        $query = $this->db->get();
        return $result = $query->result();
    }

    public function todayscost() {
        $this->db->select('sum(amount) as count');
        $this->db->select('project.name as name');
        $this->db->from('project');
        $this->db->join('attendance', 'project.id = attendance.pid');
        $this->db->where('attendance.date', date("Y-m-d"));
        $this->db->where('attendance.user_id',$this->session->userdata('id'));
        $this->db->group_by('project.name');
        $query = $this->db->get();
        return $result = $query->result();
    }
*/
}
