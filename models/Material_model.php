<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Material_model extends CI_Model {

    var $table1 = 'materials';
    var $table2 = 'categories';
    var $table3 = 'material_projects';
    var $table4 = 'project';
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_material() {
        $this->db->select($this->table1 . '.*,'. $this->table2.'.name as category_name');
        $this->db->from($this->table1);
        $this->db->join($this->table2, $this->table2.'.id = '.$this->table1.'.category_id');
        // $this->db->where($this->table1.".status", 1);
        // $this->db->where($this->table2.".status", 1);
        $this->db->order_by($this->table1 . '.id', "ASC");
        $query = $this->db->get();
        return $query->result();
    }
    public function get_active_material() {
        $this->db->select($this->table1 . '.*,'. $this->table2.'.name as category_name');
        $this->db->from($this->table1);
        $this->db->join($this->table2, $this->table2.'.id = '.$this->table1.'.category_id');
        $this->db->where($this->table1.".status", 1);
        $this->db->where($this->table2.".status", 1);
        $this->db->order_by($this->table1 . '.id', "ASC");
        $query = $this->db->get();
        return $query->result();
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
    public function get_by_id($id) {

        $this->db->select($this->table1 . '.*,'. $this->table3.'.project_id');
        $this->db->from($this->table1);
        $this->db->join($this->table2, $this->table2.'.id = '.$this->table1.'.category_id');
        $this->db->join($this->table3, $this->table3.'.material_id = '.$this->table1.'.id');
        $this->db->join($this->table4, $this->table4.'.project_id = '.$this->table3.'.project_id');
        // $this->db->where($this->table1.".status", 1);
        // $this->db->where($this->table2.".status", 1);
        $this->db->where($this->table1 . '.id', $id);
        $query = $this->db->get();
        return $query->row();
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