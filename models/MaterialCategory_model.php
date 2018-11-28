<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MaterialCategory_model extends CI_Model {


    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_material_category($order_by='id') {
        $role = $this->session->userdata('user_designation');
        $default = 'categories';
        $this->db->select('name AS category, id,approximate_estimate_ratio,status');
        $this->db->from($default);
        // $this->db->where($default.".status", 1);
        $this->db->order_by($default . '.'.$order_by, "ASC");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_active_material_category() {
        $default = 'categories';
        $this->db->select('name AS category, id,approximate_estimate_ratio,status');
        $this->db->from($default);
        $this->db->where($default.".status", 1);
        $this->db->order_by($default . '.name', "ASC");
        $query = $this->db->get();
        return $query->result();
    }

    public function checkCategory($id) {
        $w = 'materials';
        $this->db->select('count(id) AS id');
        $this->db->from($w);
        $this->db->where($w.".category_id", $id);
        // $this->db->where($w.".status", 1);
        $query = $this->db->get();
        return $result = $query->row();
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
     public function get_status($table, $id) {
        $this->db->select($table . '.status,');
        $this->db->from($table);
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }
}

?>