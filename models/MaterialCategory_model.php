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
        $this->db->where($default.".company_id", $this->session->userdata('company_id'));
        $this->db->where($default.".is_deleted", '0');
        $this->db->order_by($default . '.'.$order_by, "ASC");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_active_material_category() {
        $default = 'categories';
        $this->db->select('name AS category, id,approximate_estimate_ratio,status');
        $this->db->from($default);
        $this->db->where($default.".status", 1);
        $this->db->where($default.".company_id", $this->session->userdata('company_id'));
        $this->db->where($default.".is_deleted", '0');
        $this->db->order_by($default . '.name', "ASC");
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_active_material_category_byProject($supplier_id,$project_id,$company_id) {
        
       // $sql = "select name AS category, id,approximate_estimate_ratio,status FROM categories where id IN ( SELECT DISTINCT category_id FROM `materials` INNER JOIN material_projects on materials.id = material_projects.material_id WHERE material_projects.project_id = '".$project_id."') AND status = 1 AND is_deleted = '0'";
        $sql = "select c.name AS category, c.id, c.approximate_estimate_ratio, c.status  from categories as c inner join supplier_categories sc on c.id = sc.category_id where c.company_id='".$company_id."' and sc.supplier_id = '".$supplier_id."' AND c.is_deleted = '0'  and (select count(*) from materials m inner join material_projects mp on m.id=mp.material_id where m.category_id=c.id and m.status='1' and mp.project_id='".$project_id."' and m.is_deleted='0') > 0";
// and (select count(*) from materials m inner join material_projects mp on m.id=mp.material_id where m.category_id=c.id and m.status='1' and m.is_deleted='0') > 0 
        // echo $sql;exit;
        return $this->db->query( $sql, '' )->result_array();
    }
    
    public function checkCategory($id) {
        $w = 'materials';
        $this->db->select('count(id) AS id');
        $this->db->from($w);
        $this->db->where($w.".category_id", $id);
        $this->db->where($w.".is_deleted",'0');
        // $this->db->where($w.".status", 1);
        $query = $this->db->get();
        return $result = $query->row();
    }
    public function save($table, $data) {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    public function update($table, $where, $data) {
        return $this->db->update($table, $data, $where);
        // return $this->db->affected_rows();
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
    public function caheckIsExistCategory($id){
        $default = 'categories';
        $this->db->select('name');
        $this->db->from($default);
        $this->db->where($default . '.id', $id);
        $query = $this->db->get();
        return $query->row();
    }
}

?>