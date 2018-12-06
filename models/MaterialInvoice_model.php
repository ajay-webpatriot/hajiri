<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MaterialInvoice_model extends CI_Model {

    var $table_invoice = 'material_invoice';
    var $table_invoice_detail = 'material_invoice_detail';
    var $table_supplier = 'suppliers';
    var $table_entry = 'material_entry_log';
    var $table_entry_detail = 'material_entry_logdetail';
    // var $table_material='materials';
    // var $table_category = 'categories';
    var $table_project = 'project';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    public function save($table, $data) {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }
    public function delete($table, $col_name, $value) {
        $this->db->where($col_name, $value);
        $this->db->delete($table);
    }
    public function getMaterialInvoice(){

        $this->db->select($this->table_invoice . '.*,'
            .$this->table_supplier.'.name as supplier_name,');
        
        $this->db->from($this->table_invoice);
        $this->db->join($this->table_supplier, $this->table_supplier.'.id = '.$this->table_invoice.'.supplier_id');
        $this->db->where($this->table_invoice.".status != ", "Deleted");
        $this->db->where($this->table_invoice.".company_id", $this->session->userdata('company_id'));
        $this->db->where($this->table_invoice.".is_deleted", '0');
        $this->db->order_by($this->table_invoice . '.id', "ASC");
        $query = $this->db->get();
        return $query->result();
    }
    // material invoice date table query start
    function allMaterialInvoice_count()
    {   
        $this->db->select($this->table_invoice . '.*,'
            .$this->table_supplier.'.name as supplier_name,');

        $this->db->from($this->table_invoice);
        $this->db->join($this->table_supplier, $this->table_supplier.'.id = '.$this->table_invoice.'.supplier_id');
        $this->db->where($this->table_invoice.".status != ", "Deleted");
        $this->db->where($this->table_invoice.".company_id", $this->session->userdata('company_id'));
        $this->db->where($this->table_invoice.".is_deleted", '0');
        $query = $this->db->get();
        return $query->num_rows();  

    }
    public function allMaterialInvoice($limit,$start,$col,$dir)
    {
        $this->db->select($this->table_invoice . '.*,'
            .$this->table_supplier.'.name as supplier_name,');

        $this->db->from($this->table_invoice);
        $this->db->join($this->table_supplier, $this->table_supplier.'.id = '.$this->table_invoice.'.supplier_id');
        $this->db->where($this->table_invoice.".status != ", "Deleted");
        $this->db->where($this->table_invoice.".company_id", $this->session->userdata('company_id'));
        $this->db->where($this->table_invoice.".is_deleted", '0');
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
    public function materialInvoice_custom_search($limit,$start,$search,$col,$dir)
    {
        $this->db->select($this->table_invoice . '.*,'
            .$this->table_supplier.'.name as supplier_name,');

        $this->db->from($this->table_invoice);
        $this->db->join($this->table_supplier, $this->table_supplier.'.id = '.$this->table_invoice.'.supplier_id');
        $this->db->where($this->table_invoice.".status != ", "Deleted");
        $this->db->where($this->table_invoice.".company_id", $this->session->userdata('company_id'));
        
        $this->db->where($this->table_invoice.".is_deleted", '0');
        $this->db->where($search);
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

    public function materialInvoice_custom_search_count($search)
    {
        $this->db->select($this->table_invoice . '.*,'
            .$this->table_supplier.'.name as supplier_name,');

        $this->db->from($this->table_invoice);
        $this->db->join($this->table_supplier, $this->table_supplier.'.id = '.$this->table_invoice.'.supplier_id');
        $this->db->where($this->table_invoice.".status != ", "Deleted");
        $this->db->where($this->table_invoice.".company_id", $this->session->userdata('company_id'));
        
        $this->db->where($this->table_invoice.".is_deleted", '0');
        $this->db->where($search);
       
        $query = $this->db->get();


        return $query->num_rows();
    }
    // material invoice date table query end
    public function update($table, $where, $data) {
        $this->db->update($table, $data, $where);
        return $this->db->affected_rows();
    }
    // invoice entry data table query start
    function allMaterialInvoiceEntry_count()
    {   
        $this->db->select($this->table_entry . '.*,'
            .$this->table_supplier.'.name as supplier_name,sum('
            .$this->table_entry_detail.'.total_rate) as total_rate');

        $this->db->from($this->table_entry);
        $this->db->join($this->table_entry_detail, $this->table_entry.'.id = '.$this->table_entry_detail.'.material_entry_log_id');
        $this->db->join($this->table_supplier, $this->table_supplier.'.id = '.$this->table_entry.'.supplier_id');
        $this->db->where($this->table_entry.".company_id", $this->session->userdata('company_id'));
        $this->db->where($this->table_entry.".status = ", "Approved");
        $this->db->where($this->table_entry.".is_deleted", '0');
        $this->db->group_by($this->table_entry.'.id');

        $query = $this->db->get();
    
        return $query->num_rows();  

    }
    public function allMaterialInvoiceEntry($limit,$start,$col,$dir){

        $this->db->select($this->table_entry . '.*,'
            .$this->table_supplier.'.name as supplier_name,sum('
            .$this->table_entry_detail.'.total_rate) as total_rate');

        $this->db->from($this->table_entry);
        $this->db->join($this->table_entry_detail, $this->table_entry.'.id = '.$this->table_entry_detail.'.material_entry_log_id');
        $this->db->join($this->table_supplier, $this->table_supplier.'.id = '.$this->table_entry.'.supplier_id');
        $this->db->where($this->table_entry.".company_id", $this->session->userdata('company_id'));
        $this->db->where($this->table_entry.".status = ", "Approved");
        $this->db->where($this->table_entry.".is_deleted", '0');

        $this->db->limit($limit,$start);
        $this->db->group_by($this->table_entry.'.id');
        $this->db->order_by($col,$dir);
        $query = $this->db->get();
        // echo "<pre>";
        // print_r($query->result());exit;
        if($query->num_rows()>0)
        {
            return $query->result(); 
        }
        else
        {
            return null;
        }
    }
    public function materialInvoiceEntry_custom_search($limit,$start,$search,$col,$dir)
    {
        $this->db->select($this->table_entry . '.*,'
            .$this->table_supplier.'.name as supplier_name,sum('
            .$this->table_entry_detail.'.total_rate) as total_rate');

        $this->db->from($this->table_entry);
        $this->db->join($this->table_entry_detail, $this->table_entry.'.id = '.$this->table_entry_detail.'.material_entry_log_id');
        $this->db->join($this->table_supplier, $this->table_supplier.'.id = '.$this->table_entry.'.supplier_id');
        $this->db->where($this->table_entry.".company_id", $this->session->userdata('company_id'));
        $this->db->where($this->table_entry.".status = ", "Approved");
        $this->db->where($this->table_entry.".is_deleted", '0');
        $this->db->where($search);
        $this->db->limit($limit,$start);
        $this->db->group_by($this->table_entry.'.id');
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

    public function materialInvoiceEntry_custom_search_count($search)
    {
        $this->db->select($this->table_entry . '.*,'
            .$this->table_supplier.'.name as supplier_name,sum('
            .$this->table_entry_detail.'.total_rate) as total_rate');

        $this->db->from($this->table_entry);
        $this->db->join($this->table_entry_detail, $this->table_entry.'.id = '.$this->table_entry_detail.'.material_entry_log_id');
        $this->db->join($this->table_supplier, $this->table_supplier.'.id = '.$this->table_entry.'.supplier_id');
        $this->db->where($this->table_entry.".company_id", $this->session->userdata('company_id'));
        $this->db->where($this->table_entry.".status = ", "Approved");
        $this->db->where($this->table_entry.".is_deleted", '0');
        $this->db->where($search);
        $this->db->group_by($this->table_entry.'.id');
       
        $query = $this->db->get();


        return $query->num_rows();
    }
    // invoice entry data table query end
    public function SelectedChallanDetails($logIds)
    {
        $this->db->select($this->table_entry . '.id,'.$this->table_entry . '.challan_no,'.$this->table_entry . '.challan_image,'.$this->table_entry . '.challan_date,sum('.$this->table_entry_detail.'.total_rate) as total_rate');

        $this->db->from($this->table_entry);
        $this->db->join($this->table_entry_detail, $this->table_entry.'.id = '.$this->table_entry_detail.'.material_entry_log_id');
        $this->db->where($this->table_entry.".company_id", $this->session->userdata('company_id'));
        $this->db->where($this->table_entry.".status != ", "Deleted");
        $this->db->where($this->table_entry.".is_deleted", '0');
        $this->db->where_in($this->table_entry.".id", $logIds);
        $this->db->group_by($this->table_entry.'.id');
       
        $query = $this->db->get();
        return $query->result(); 
    }
    public function getInvoiceById($id)
    {
        $this->db->select($this->table_invoice . '.*');
        $this->db->from($this->table_invoice);
        $this->db->where($this->table_invoice.".status != ", "Deleted");
        $this->db->where($this->table_invoice.".company_id", $this->session->userdata('company_id'));
        $this->db->where($this->table_invoice.".id", $id);
        $this->db->where($this->table_invoice.".is_deleted", '0');
        $this->db->order_by($this->table_invoice . '.id', "ASC");
        $query = $this->db->get();
        return $query->row();
    }
    public function getChallanById($id)
    {
        // $this->db->select($this->table_entry . '.id,'.$this->table_entry . '.challan_no,'.$this->table_entry . '.challan_image,'.$this->table_entry . '.challan_date,sum('.$this->table_entry_detail.'.total_rate) as total_rate');
        $this->db->select($this->table_invoice_detail . '.material_entry_log_id');

        $this->db->from($this->table_entry);
        // $this->db->join($this->table_entry_detail, $this->table_entry.'.id = '.$this->table_entry_detail.'.material_entry_log_id');
        $this->db->join($this->table_invoice_detail, $this->table_entry.'.id = '.$this->table_invoice_detail.'.material_entry_log_id');
        
        $this->db->where($this->table_entry.".company_id", $this->session->userdata('company_id'));
        $this->db->where($this->table_entry.".status != ", "Deleted");
        $this->db->where($this->table_entry.".is_deleted", '0');
        $this->db->group_by($this->table_entry.'.id');

        $this->db->where($this->table_invoice_detail.".invoice_id", $id);
        
        $query = $this->db->get();
        return $query->result();
    }
}

?>