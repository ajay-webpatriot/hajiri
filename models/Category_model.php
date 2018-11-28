<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model {

    var $table2 = 'user';
    var $table3 = 'worker_wage';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_category($company_id) {
        $role = $this->session->userdata('user_designation');
        $default = 'worker_category';
        $this->db->select('category_name AS category, category_id AS id');
        $this->db->from($default);
        $this->db->where($default.".company_id", $company_id);
        $this->db->where($default.".status", 1);
        $this->db->order_by($default . '. category_id', "ASC");
        $query = $this->db->get();
        return $query->result();
    }

    public function checkCategory($id) {
        $w = 'worker';
        $this->db->select('count(worker_id) AS id');
        $this->db->from($w);
        $this->db->where($w.".category_id", $id);
        $this->db->where($w.".status", 1);
        $query = $this->db->get();
        return $result = $query->row();
    }
}

?>