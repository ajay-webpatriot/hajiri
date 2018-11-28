<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class CalculationDailywage_model extends CI_Model {

    var $table1 = 'worker';
    var $table2 = 'user';
    var $table3 = 'project';
   
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
   
    public function save($table, $data) {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    public function get_all_wagemethod($table,$id) {

        $this->db->select('*');
        $this->db->from($table);
 //       $this->db->where($table . '.user_designation', 'admin');
         //$this->db->where($table . '.user_id', $id);

        //        $this->db->where("$table.approved_status", 1);
        $query = $this->db->get();

        
        return $query->result();
    }


   public function get_selected_method($table,$id) {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($table . '.company_id', $this->session->userdata('company_id'));
        $query = $this->db->get();
        $ret = $query->row();
        if($ret){
        return $ret->method_id;
        }else{
            return 0;
        }
    } 
   
    public function get_days($table,$id) {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($table . '.company_id', $this->session->userdata('company_id'));
        $query = $this->db->get();
        $ret = $query->row();
        if($ret){
        return $ret->custom_value;
        }else{
            return 0;
        }
    } 

     public function delete($table, $col_name, $value) {
        $this->db->where($col_name, $value);
        $this->db->delete($table);
    }

}
