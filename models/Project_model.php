<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Project_model extends CI_Model {

    var $table1 = 'project';
    var $table2 = 'user';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function get_datatables() {
        $this->db->select('project.project_id, project.project_name, project.company_id, project.status, c.company_name');
        $this->db->from($this->table1);
		if( $this->session->userdata('user_designation') != 'Superadmin' ){
			$company_id = $this->session->userdata('company_id');
            $this->db->where($this->table1 .".company_id", $company_id);
		}
        $this->db->where($this->table1 .".status != 0");
        $this->db->join('company c','c.compnay_id = '.$this->table1.'.company_id');
        $this->db->order_by($this->table1 . '.project_id',"desc");        
        $query = $this->db->get();
        return $query->result();
    }

    function get_active_projects() {
        $this->db->select('project.project_id, project.project_name, project.company_id, project.status, c.company_name');
        $this->db->from($this->table1);
        if( $this->session->userdata('user_designation') != 'Superadmin' ){
            $company_id = $this->session->userdata('company_id');
            $this->db->where($this->table1 .".company_id", $company_id);
        }
        $this->db->where($this->table1 .".status = 1");
        $this->db->join('company c','c.compnay_id = '.$this->table1.'.company_id');
        $this->db->order_by($this->table1 . '.project_id',"desc");        
        $query = $this->db->get();
        return $query->result();
    }

    public function get_by_id($id) {
        $this->db->select($this->table1 . '.*,');
        $this->db->from($this->table1);
        $this->db->where('project_id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_mail_byid($table,$id){
        $this->db->select($table . '.user_email,');
        $this->db->select($table . '.user_name,');
        //$this->db->select($table . '.organization_name,');
        $this->db->from($table);
        $this->db->where('user_id', $id);
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
        $query = $this->db->get();
        return $query->result();
    }
     public function get_all_managers($table) {
        $company_id = $this->session->userdata('company_id');
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($table .".company_id", $company_id);
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_project_details($table1,$table2,$id){
        $this->db->select($table1 . '.*,');
        $this->db->from($table1);
        $this->db->where($table1.'.project_id', $id);
        $query = $this->db->get();
        return $query->row();
    }
	public function getProjectDetailsByCompanyId($company_id) {

        $this->db->select('*');
        $this->db->from('project');
        $this->db->where('company_id', $company_id);
        return $this->db->get()->result();
    }

}
