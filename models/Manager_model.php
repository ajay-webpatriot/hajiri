<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Manager_model extends CI_Model {

    var $table1 = 'user';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function get_datatables() {
        $role = $this->session->userdata('user_designation');
        $this->db->select($this->table1 . '.*,');
        if ( $role != 'admin' ){
            $this->db->select('c.company_name,');
        }
        $this->db->from($this->table1);
        if ( $role == 'admin' ){
            $company_id = $this->session->userdata('company_id');
            $this->db->where($this->table1 . '.company_id = '.$company_id);
        }else{
            $this->db->join('company c','c.compnay_id = '.$this->table1.'.company_id');
        }
        $this->db->where($this->table1 . '.status != 0');
        $this->db->where($this->table1 . '.user_designation', "admin");
        $this->db->order_by($this->table1 . '.user_id', "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function count_all() {
        $this->db->select($this->table1 . '.*,');
        $this->db->from($this->table1);
        $this->db->where($this->table1 . '.user_designation', "admin");
        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        $this->db->select($this->table1 . '.*,');
        $this->db->from($this->table1);
        $this->db->where($this->table1 . '.user_id', $id);
        $this->db->where($this->table1 . '.user_designation', "admin");
        $query = $this->db->get();
        return $query->row();
    }

    public function save($table, $data) {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    public function update($table, $where, $data) {
        return $this->db->update($table, $data, $where);
        //return $this->db->affected_rows();
    }

    public function delete($table, $col_name, $value) {
        $this->db->where($col_name, $value);
        $this->db->delete($table);
    }

    public function get_all_where($table) {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($this->table1 . '.user_designation', "admin");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_row_where($table, $where) {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($this->table1 . '.user_designation', "admin");
        $this->db->where($where);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_all_list() {
        $this->db->select($this->table1 . '.*,');
        $this->db->from($this->table1);
        $this->db->where($this->table1 . '.user_designation', "admin");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_where($select, $where = array()) {
        $this->db->select($select);
        $this->db->from($this->table1);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_status($table, $id) {
        $this->db->select($table . '.status,');
        $this->db->select($table . '.user_name AS name,');
        $this->db->select($table . '.user_email,');
        $this->db->from($table);
        $this->db->where('user_id', $id);
        $query = $this->db->get();
        return $query->row();
    }
    public function checkUniqueEmail($table, $where) {
        $this->db->select('count(user_id) AS count');
        $this->db->from($table);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->row();
    }
     public function checkUniqueEmailUpdate($table, $where) {
        $this->db->select('count(user_id) AS count');
        $this->db->from($table);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->row();
    }

}
