<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Kharchi_model extends CI_Model {

    var $a = 'attendance';
    var $k = 'kharchi';
    var $c = 'worker_category';
    var $wage = 'worker_wage';
    var $p = 'project';
    var $u = 'user';
    var $pay = 'payment';
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

/******************* Kharchi datatable  *********************/
    function allKharchi_count()
    {   
        $company_id = $this->session->userdata('company_id');
        $this->db->select($this->k . '.*');
        $this->db->from($this->k);
        $this->db->where($this->k .'. company_id  = '.$company_id);
        $this->db->where($this->k .'. status != 2');
		if( $this->session->userdata('user_designation') == 'Supervisor' ){
			$this->db->where($this->k .'. supervisor_id  = '. $this->session->userdata('id'));	
		}
        $this->db->where($this->k .'. date_time LIKE "'.date('Y-m').'%"');
        $this->db->order_by($this->k . '. kharachi_id', "ASC");
        $query = $this->db->get();
    
        return $query->num_rows();  

    }
    
    function allKharchi($limit,$start,$col,$dir)
    {   
        
        $company_id = $this->session->userdata('company_id');
        $this->db->select($this->k . '.*');
        $this->db->from($this->k);
        $this->db->where($this->k .'. company_id  = '.$company_id);
		if( $this->session->userdata('user_designation') == 'Supervisor' ){
			$this->db->where($this->k .'. supervisor_id  = '. $this->session->userdata('id') );	
		}
        $this->db->where($this->k .'. status != 2');
        $this->db->where($this->k .'. date_time LIKE "'.date('Y-m').'%"');
        $this->db->order_by($this->k . '. kharachi_id', "ASC");
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

    function kharchi_search($limit,$start,$search,$col,$dir)
    {
		$company_id = $this->session->userdata('company_id');
		$this->db->select($this->k . '.*');
		$this->db->from($this->k);
		$this->db->where($this->k .'. company_id  = '.$company_id);
		if( $this->session->userdata('user_designation') == 'Supervisor' ){
			$this->db->where($this->k .'. supervisor_id  = '. $this->session->userdata('id'));
		}
		$this->db->where($this->k .'. status != 2');
		if( $this->session->userdata('user_designation') == 'Supervisor' ){
			$this->db->where($this->k .'. supervisor_id  = '. $this->session->userdata('id'));	
		}
		$this->db->where($this->k .'. date_time LIKE "'.date('Y-m').'%"');
		$this->db->order_by($this->k . '. kharachi_id', "ASC"); 
		$this->db->like('title',$search);
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


    function kharchi_custom_search($limit,$start,$search,$col,$dir)
    {
		$company_id = $this->session->userdata('company_id');
        $this->db->select($this->k . '.*');
		$this->db->from($this->k);
		$this->db->where($this->k .'. company_id  = '.$company_id);
		if( $this->session->userdata('user_designation') == 'Supervisor' ){
			$this->db->where($this->k .'. supervisor_id  = '. $this->session->userdata('id'));
		}
		$this->db->where($this->k .'. status != 2');
        $this->db->where($search);
		$this->db->order_by($this->k . '. kharachi_id', "ASC"); 
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

    function kharchi_search_count($search)
    {
		$company_id = $this->session->userdata('company_id');   
        $this->db->select($this->k . '.*');
		$this->db->from($this->k);
		$this->db->where($this->k .'. company_id  = '.$company_id);
		if( $this->session->userdata('user_designation') == 'Supervisor' ){
			$this->db->where($this->k .'. supervisor_id  = '. $this->session->userdata('id'));
		}
		$this->db->where($this->k .'. status != 2');
		$this->db->where($this->k .'. date_time LIKE "'.date('Y-m').'%"');
		$this->db->order_by($this->k . '. kharachi_id', "ASC"); 
		$this->db->like('title',$search);
        $query = $this->db->get();
    
        return $query->num_rows();
    }

    function kharchi_custom_search_count($search)
    {
		$company_id = $this->session->userdata('company_id');   
        $this->db->select($this->k . '.*');
		$this->db->from($this->k);
		$this->db->where($this->k .'. company_id  = '.$company_id);
		if( $this->session->userdata('user_designation') == 'Supervisor' ){
			$this->db->where($this->k .'. supervisor_id  = '. $this->session->userdata('id'));
		}
		$this->db->where($this->k .'. status != 2');
		$this->db->where($this->k .'. date_time LIKE "'.date('Y-m').'%"');
        $this->db->where($search);
		$this->db->order_by($this->k . '. kharachi_id', "ASC"); 
        $query = $this->db->get();
    
        return $query->num_rows();
    }
	function kharchiStats($where){
		$this->db->select('sum(amount) AS amount');
		$this->db->from('kharchi');
		$this->db->where($where);
		$this->db->where('company_id',$this->session->userdata('company_id'));
		if($this->session->userdata('user_designation') == 'Supervisor')
			$this->db->where('supervisor_id',$this->session->userdata('id'));
        $query = $this->db->get();
        return $query->row();
	}
	function kharchiDetails($where,$table){
		$this->db->select($table . '.*');
		$this->db->from($table);
		$this->db->where($where);
        $query = $this->db->get();
        return $query->row();
	}
	function kharchiLogDetails($where,$table){
		$this->db->select($table . '.kharchi_log_description');
		$this->db->select($table . '.kharchi_log_date_time');
		$this->db->from($table);
		$this->db->where($where);
        $query = $this->db->get();
        return $query->row();
	}
}

?>