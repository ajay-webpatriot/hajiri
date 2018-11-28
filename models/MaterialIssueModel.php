<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MaterialIssueModel extends CI_Model {

    var $table1 = 'material_issue_log';
    var $table2 = 'categories';
    var $table3 = 'materials';
    var $table4 = 'user';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    public function getIssueLog()
    {   
        $this->db->select($this->table1 . '.*,concat('.$this->table4.'.user_name," ",'.$this->table4.'.user_last_name) as issue_by_name,'.$this->table2.'.name as category_name,'.$this->table3.'.name as material_name');
        $this->db->from($this->table1);
        $this->db->join($this->table3, $this->table1.'.material_id = '.$this->table3.'.id');
        $this->db->join($this->table2, $this->table2.'.id = '.$this->table3.'.category_id');
        $this->db->join($this->table4, $this->table4.'.user_id = '.$this->table1.'.issue_by');
        $this->db->where($this->table1 . '.status != ', 'Deleted');
        $query = $this->db->get();
        $result= $query->result();
        
        return $result;
    }

    public function get_materialIssue_by_id($id){
       
        $this->db->select($this->table1 . '.*,'.$this->table2.'.id as category_id');
        $this->db->from($this->table1);
        $this->db->join($this->table3, $this->table1.'.material_id = '.$this->table3.'.id');
        $this->db->join($this->table2, $this->table2.'.id = '.$this->table3.'.category_id');
        $this->db->join($this->table4, $this->table4.'.user_id = '.$this->table1.'.issue_by');
        $this->db->where($this->table1 . '.id', $id);
        $this->db->where($this->table1 . '.status != ', 'Deleted');
        
        $query = $this->db->get();
        return $query->row();
    }
    public function save($data){
        $this->db->insert($this->table1, $data);
        return $this->db->insert_id();
    }
    public function update($table, $where, $data) {
        $this->db->update($table, $data, $where);
        return $this->db->affected_rows();
    }
       
}