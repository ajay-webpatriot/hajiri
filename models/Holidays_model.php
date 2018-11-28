<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Holidays_model extends CI_Model {

    var $table1 = 'holidays';
   

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function get_datatables() {
        $company_id = $this->session->userdata('company_id');
        $this->db->select($this->table1 . '.*,');
        $this->db->from($this->table1);
        $this->db->where($this->table1 .".company_id", $company_id);
        $this->db->where($this->table1 .".status", 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_all() {
        $this->db->select($this->table1 . '.*,');
        $this->db->from($this->table1);
        return $this->db->count_all_results();
    }

    public function get_by_id($id) { //$table3
        $this->db->select($this->table1 . '.*,');
        $this->db->from($this->table1);
        // $this->db->select($this->table3 . '.worker_wage AS daily_wage,');
         //$this->db->join($this->table1, "$this->table1.worker_wage=$this->table3.worker_wage_id");
        $this->db->where($this->table1 . '.holiday_id', $id);

        $query = $this->db->get();
        return $query->row();
    }

    public function save($table, $data) {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    public function update($table, $where, $data) {
        $this->db->update($table, $data, $where);
        return $this->db->affected_rows();
    }

    public function delete($table, $col_name, $value) {
        $this->db->where($col_name, $value);
        $this->db->delete($table);
    }

    public function countHoliday($date) {
        $company_id = $this->session->userdata('company_id');
        $this->db->select('*');
        $this->db->from('holidays');
        $this->db->where('holiday_date',$date);
        $this->db->where($this->table1 .".company_id", $company_id);
        $this->db->where('status',1);
        return $this->db->count_all_results();
    }

    public function get_all_where($table) {
        $this->db->select('*');
        $this->db->from($table);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_all_labours($table){
         $this->db->select('id,name');
        $this->db->from($table);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_all_project($table) {
        $this->db->select('*');
        $this->db->from($table);
//        $this->db->where("$table.approved_status", 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_row_where($table, $where) {
        $this->db->select('*');
        $this->db->from($table);
//        $this->db->where("$table.approved_status", 1);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_all_managers($table) {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($table . '.login_as', 'admin');
        //        $this->db->where("$table.approved_status", 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_all_list() {
        $this->db->select($this->table1 . '.*,');
        $this->db->from($this->table1);
//        $this->db->where("$table.approved_status", 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function checkDate($date,$company_id) {
        $this->db->select('count(*) AS count,');
        $this->db->from($this->table1);
        $this->db->where($this->table1 . '.holiday_date', $date);
        $this->db->where($this->table1 . '.company_id', $company_id);
        $this->db->where($this->table1 . '.status', 1);
        $query = $this->db->get();
        return $query->row();
    }

    public function checkUpdateDate($date, $id,$company_id) {
        $this->db->select('count(*) AS count,');
        $this->db->from($this->table1);
        $this->db->where($this->table1 . '.holiday_id != '.$id);
        $this->db->where($this->table1 . '.holiday_date', $date);
        $this->db->where($this->table1 . '.company_id', $company_id);
        $this->db->where($this->table1 . '.status', 1);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_labour_pdf($id,$month) {
        $this->db->select('labour.*');
        $this->db->select('attendance.working_hr');
        $this->db->select('attendance.date');
        $this->db->select('attendance.halfday');
        $this->db->select('project.name AS PROJECT_NAME');
        $this->db->select('user.organization_name');
        $this->db->from('labour');
        $this->db->join('attendance','attendance.lid=labour.id');
        $this->db->join('project','project.id=labour.pid');
        $this->db->join('user','user.id=labour.user_id');
        $this->db->where('labour.id', $id);
        $this->db->where('attendance.month',$month);
        $query = $this->db->get();
        return $query->result();
    }

}
