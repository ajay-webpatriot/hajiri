<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MaterialLog_model extends CI_Model {

    var $table1 = 'material_entry_log';
    var $table2 = 'material_entry_logdetail';
    var $table3 = 'materials';
    var $table4 = 'suppliers';
    var $table5 = 'categories';
    var $table6 = 'user';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    public function materialLogsave($data){
    	$this->db->insert($this->table1 , $data);
        return $this->db->insert_id();
    }
    public function materialLogDetailsave($data){
    	$this->db->insert($this->table2, $data);
        return $this->db->insert_id();
    }
    public function delete($table, $col_name, $value) {
        $this->db->where($col_name, $value);
        $this->db->delete($table);
    }
    public function getMaterialLog(){

        $this->db->select($this->table1 . '.*,'
            .$this->table4.'.name as supplier_name,concat('.$this->table6.'.user_name," ",'.$this->table6.'.user_last_name) as supervisor_name');

        $this->db->select('(select group_concat('.$this->table3.'.name separator ",") from '.$this->table3.' join '.$this->table2.' where '.$this->table3.'.id='.$this->table2.'.material_id and '.$this->table1.'.id='.$this->table2.'.material_entry_log_id) as material_name');
        $this->db->select('(select group_concat('.$this->table5.'.name separator ",") from '.$this->table3.' join '.$this->table2.'  join '.$this->table5.' where '.$this->table3.'.id='.$this->table2.'.material_id and '.$this->table5.'.id='.$this->table3.'.category_id and '.$this->table1.'.id='.$this->table2.'.material_entry_log_id) as category_name');
        
        $this->db->from($this->table1);
        $this->db->join($this->table4, $this->table4.'.id = '.$this->table1.'.supplier_id');
        $this->db->join($this->table6, $this->table1.'.receiver_id = '.$this->table6.'.user_id');
        $this->db->where($this->table1.".status != ", "Deleted");
        $this->db->order_by($this->table1 . '.id', "ASC");
        $query = $this->db->get();
        return $query->result();
    }
    public function getMaterialByCategory($id){
        $result = $this->db->where("category_id",$id)->get($this->table3)->result();
        return $result;
    }
    public function getProjectSupervisor($id)
    {

        $this->db->select("user.user_id,CONCAT(user.user_name,' ',user.user_last_name) as supervisor_name");
        $this->db->from("user");
        $this->db->join("user_project",'user_project.user_id = user.user_id');
        $this->db->where('user.user_designation', "Supervisor");
        $this->db->where('user_project.project_id', $id);
        
        $this->db->where("user.status", 1);
        $this->db->where("user_project.status", 1);
        $this->db->order_by('supervisor_name', "ASC");
        $query = $this->db->get();
        return $query->result();
    }
    public function get_materiallog_by_id($id){
        $this->db->select($this->table1 . '.*');

        $this->db->from($this->table1);
       // $this->db->join($this->table5, '.id = .name', 'inner')
        $this->db->where($this->table1 . '.id', $id);
        $query = $this->db->get();
        return $query->row();
    }
    public function get_materiallog_detail_by_id($id){


        $this->db->select($this->table2 . '.*,'.$this->table5.'.id as category_id');
        
        $this->db->from($this->table1);
        $this->db->join($this->table2,$this->table2.'.material_entry_log_id = '.$this->table1.'.id');
        $this->db->join($this->table3,$this->table3.'.id = '.$this->table2.'.material_id');
        $this->db->join($this->table5,$this->table5.'.id = '.$this->table3.'.category_id');
        $this->db->where($this->table1 . '.id', $id);
        $this->db->order_by($this->table2 . '.id', "ASC");
        $query = $this->db->get();
        // echo $this->db->last_query();
        // echo "<pre>" ;
        // print_r($query->result());exit;
        return $query->result();
    }
    public function update($table, $where, $data) {
        $this->db->update($table, $data, $where);
        return $this->db->affected_rows();
    }
    public function getMaterialEntryQuantitybyProjectId($project_id, $material_id){

        $this->db->select('SUM('.$this->table2.'.quantity) as entryQuantity');
        $this->db->from($this->table1);
        $this->db->join($this->table2, $this->table1. '.id = '. $this->table2.'.material_entry_log_id');
        $this->db->where($this->table1 . '.project_id', $project_id);
        $this->db->where($this->table1 . '.status !=', 'Deleted');
        $query = $this->db->get();
       return  $result = $query->row();
    }
}

?>