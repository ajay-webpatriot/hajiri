<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Foreman_model extends CI_Model {

    var $table1 = 'user';
    var $table2 = 'project';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function get_datatables() {

        $this->db->select($this->table1 . '.*,');
        if( $this->session->userdata('user_designation') == 'Superadmin' ){
            $this->db->select('c.company_name');
        }
        $this->db->from($this->table1);
        $this->db->where($this->table1 . '.user_designation', "Supervisor");
		if( $this->session->userdata('user_designation') != 'Superadmin' ){
			$company_id = $this->session->userdata('company_id');
			$this->db->where($this->table1 .".company_id", $company_id);
		}else{
            $this->db->join('company c','c.compnay_id = '.$this->table1.'.company_id');
        }
        $this->db->where($this->table1 . '.status != 0');
        $this->db->order_by($this->table1 . '.user_id', "desc");
        $query = $this->db->get();
        return $query->result();
    }

	function get_activeForeman() {

        $this->db->select($this->table1 . '.*,');
        if( $this->session->userdata('user_designation') == 'Superadmin' ){
            $this->db->select('c.company_name');
        }
        $this->db->from($this->table1);
        $this->db->where($this->table1 . '.user_designation', "Supervisor");
		if( $this->session->userdata('user_designation') != 'Superadmin' ){
			$company_id = $this->session->userdata('company_id');
			$this->db->where($this->table1 .".company_id", $company_id);
		}else{
            $this->db->join('company c','c.compnay_id = '.$this->table1.'.company_id');
        }
        $this->db->where($this->table1 . '.status = 1');
        $this->db->order_by($this->table1 . '.user_id', "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function count_all() {
        $this->db->select($this->table1 . '.*,');
        $this->db->from($this->table1);
        $this->db->where($this->table1 . '.login_as', "foreman");
        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        $this->db->select($this->table1 . '.*,');
        $this->db->from($this->table1);
        $this->db->where($this->table1 . '.user_id', $id);
        $this->db->where($this->table1 . '.user_designation', "Supervisor");
        $query = $this->db->get();
        return $query->row();
    }

    public function save($table, $data) {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    public function updateprojectid($table, $where, $id) {
        $this->db->set("user_id", "CONCAT( user_id, '," . $id . "' )", false);
        $this->db->where($where);
        $this->db->update($table);
        return $this->db->affected_rows();
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
        $this->db->where($this->table1 . '.login_as', "foreman");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_row_where($table, $where) {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($this->table1 . '.login_as', "foreman");
        $this->db->where($where);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_all_list() {
        $this->db->select($this->table1 . '.*,');
        $this->db->from($this->table1);
        $this->db->where($this->table1 . '.login_as', "foreman");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_status($table, $id) {
        $this->db->select($table . '.status,');
        $this->db->select($table . '.user_name,');
        $this->db->select($table . '.user_email,');
        $this->db->from($table);
        $this->db->where('user_id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function checkUniqueEmail($table, $where) {
        $this->db->select('count(*) as count');
        $this->db->from($table);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->row();
    }

    public function checkUniqueEmailUpdate($table, $where) {
        $this->db->select('count(*) as count');
        $this->db->from($table);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_all_managers($table) {
        $company_id = $this->session->userdata('company_id');
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($this->table1 . '.user_designation', "admin");
        $this->db->where($this->table1 .".company_id", $company_id);
        $query = $this->db->get();
        return $query->result();
    }

     public function get_all_plugin($id) {
        
        $this->db->select('p.plugin_id, p.plugin_name');
        $this->db->from('plugin p');
        $this->db->join('company_plugin_association cp', 'cp.plugin_id = p.plugin_id');
        $this->db->where('cp'. '.company_id', $id);
        $this->db->where('p'. '.status', 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_where($table, $col, $id) {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($col, $id);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_where_set($table, $where = array()) {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_projectDetails($company_id) {

        $this->db->select('project' . '.*,');
        $this->db->select('company' . '.company_name AS ORG_NAME');
        $this->db->from('project');
        $this->db->join('company', "project.company_id = company.compnay_id");
        $this->db->where('project' .".company_id", $company_id);
        $this->db->where('project' .".status", 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_Formanprojects($table,$pids) {
        $this->db->select($table . '.project_name,');
		$this->db->select($table . '.project_start_date,');
		$this->db->select($table . '.project_end_date');
		$this->db->from($table);
		$this->db->where_in('project_id',$pids);
		$query = $this->db->get();
		return $query->result();
    }

	public function get_project_foremanId($id){
		$this->db->select('*');
		$this->db->from('project');
		$this->db->where('project_id IN ( select project_id from user_project where user_id = '.$id.') AND STATUS = 1');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_allForemanProjects($user_id) {
        $this->db->select('*');
		$this->db->from('project AS p');
		$this->db->where('project_id IN ( SELECT project_id FROM `user_project` where user_id = '.$user_id.' AND status = 1 )');
		$query = $this->db->get();
		return $query->result();
    }

   public function get_allForemanProjectsName($user_id) {
        $this->db->select('p.project_id,p.project_name');
        $this->db->from('project AS p');
        $this->db->join('user_project', "p.project_id = user_project.project_id");
        $this->db->where('user_project.user_id = '.$user_id.' AND user_project.status = 1 ');
        $query = $this->db->get();
        return $query->result();
    }
}
