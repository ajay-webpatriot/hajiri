<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class GenerateID_model extends CI_Model {

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
        //echo "getWhereResult";
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($col, $val);
        $query = $this->db->get();
       // /echo sizeof($query->result());
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

    public function get_labours($user_id) {
         $company_id = $this->session->userdata('company_id');
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

}
