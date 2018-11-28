<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Plan_model extends CI_Model {

    var $table2 = 'user';
    var $table3 = 'worker_wage';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_PlanId($id) {
        $this->db->select('plan_id AS id');
        $this->db->from('company_plan');
        $this->db->where('company_id  = '.$id);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_limit($select,$id,$joinTable,$where) {
        $this->db->select($select);
        $this->db->from('pricing_plans pp');
        $this->db->join( $joinTable.' w', 'w. `company_id` = '.$id);
        $this->db->where('`id` = (SELECT plan_id from company_plan where company_id = '.$id.')');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_where($table, $col, $id) {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($col, $id);
        $query = $this->db->get();
        return $query->row();
    }
}

?>