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
    public function getMaterialIssueQuantitybyProjectId($project_id, $material_id){
        
        $this->db->select('SUM('.$this->table1.'.quantity) as issueQuantity');
        $this->db->from($this->table1);
        $this->db->where($this->table1 . '.project_id', $project_id);
        $this->db->where($this->table1 . '.material_id', $material_id);
        $this->db->where($this->table1 . '.status !=', 'Deleted');
        $this->db->where($this->table1 . '.is_deleted', 0);
        $query = $this->db->get();
        return $result = $query->row();
    }
    public function getMaterialAjax($project_id, $category_id){

         // Sub Query
        $this->db->select('material_id')->from('material_entry_log')->join('material_entry_logdetail', 'material_entry_log.id = material_entry_logdetail.material_entry_log_id')->where('material_entry_log.project_id', $project_id);
        $subQuery =  $this->db->get_compiled_select();
 
        // Main Query
        $query = $this->db->select('id,name')
                 ->from($this->table3)
                 ->where("id IN ($subQuery)", NULL, FALSE)
                 ->where($this->table3.'.category_id', $category_id)
                 ->get();
        return $result = $query->result();

        // select * from materials as m where m.id IN ( SELECT DISTINCT meld.material_id FROM material_entry_log as mel inner JOIN material_entry_logdetail as meld on mel.id = meld.material_entry_log_id where mel.project_id = 90) and m.category_id = 2
    }
    // data table query start
    public function allMaterialIssue_count()
    {   
        $this->db->select($this->table1 . '.*,concat('.$this->table4.'.user_name," ",'.$this->table4.'.user_last_name) as issue_by_name,'.$this->table2.'.name as category_name,'.$this->table3.'.name as material_name');
        $this->db->from($this->table1);
        $this->db->join($this->table3, $this->table1.'.material_id = '.$this->table3.'.id');
        $this->db->join($this->table2, $this->table2.'.id = '.$this->table3.'.category_id');
        $this->db->join($this->table4, $this->table4.'.user_id = '.$this->table1.'.issue_by');
        $this->db->where($this->table1 . '.status != ', 'Deleted');
        
        $query = $this->db->get();
    
        return $query->num_rows();
    }

    public function allMaterialIssue($limit,$start,$col,$dir)
    {   
        $this->db->select($this->table1 . '.*,concat('.$this->table4.'.user_name," ",'.$this->table4.'.user_last_name) as issue_by_name,'.$this->table2.'.name as category_name,'.$this->table3.'.name as material_name');
        $this->db->from($this->table1);
        $this->db->join($this->table3, $this->table1.'.material_id = '.$this->table3.'.id');
        $this->db->join($this->table2, $this->table2.'.id = '.$this->table3.'.category_id');
        $this->db->join($this->table4, $this->table4.'.user_id = '.$this->table1.'.issue_by');
        $this->db->where($this->table1 . '.status != ', 'Deleted');
        $this->db->limit($limit,$start);
        $this->db->order_by($col,$dir);
        $query = $this->db->get();
        
        if($query->num_rows()>0)
        {
            return $query->result(); 
        }
        else
        {
            return null;
        }
    }
    // data table query end
}