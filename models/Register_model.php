<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Register_model extends CI_Model {

    var $a = 'attendance';
    var $w = 'worker';
    var $c = 'worker_category';
    var $wage = 'worker_wage';
    var $p = 'project';
    var $u = 'user';
    var $pay = 'payment';
    var $table1 = 'user';
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function allattendance_count()
    {   
        $company_id = $this->session->userdata('company_id');
        
        $this->db->select($this->a . '.attendance_id');
        $this->db->from($this->a);
        $this->db->join($this->w, $this->a .'. `worker_id` = '.$this->w .'. worker_id');
        $this->db->join($this->p, $this->a .'. `project_id` = '.$this->p .'. project_id');
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->a .'. worker_id  IN (select worker_id from worker where 
            company_id = '.$company_id.')');
		if ($this->session->userdata('user_designation') == 'Supervisor'){
			$this->db->where($this->a.'.project_id IN (select project_id from user_project where user_id = '.$this->session->userdata("id").')');
		}
        $this->db->where($this->a .'. status IN (1,3)');
        $this->db->order_by($this->a . '. attendance_date_time', "DESC");
        //$this->db->like('attendance_date_time',date("Y-m-d"));
        $query = $this->db->get();
    
        return $query->num_rows();  

    }
    
    function allattendance($limit,$start,$col,$dir)
    {   
        $company_id = $this->session->userdata('company_id');
        $this->db->select($this->a . '.attendance_id');
        $this->db->select($this->a . '.hajiri');
        $this->db->select($this->a . '.amount');
        $this->db->select($this->p . '.project_name');
        $this->db->select($this->w . '.labour_name');
        $this->db->select($this->w . '.labour_last_name');
        $this->db->select($this->c . '.category_name');
        $this->db->select($this->w . '.worker_contact');
        $this->db->select($this->a . '.attendance_date_time AS date');
        $this->db->from($this->a);
        $this->db->join($this->w, $this->a .'. `worker_id` = '.$this->w .'. worker_id');
        $this->db->join($this->p, $this->a .'. `project_id` = '.$this->p .'. project_id');
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->a .'. worker_id  IN (select worker_id from worker where 
            company_id = '.$company_id.')');
        if ($this->session->userdata('user_designation') == 'Supervisor'){
			$this->db->where($this->a.'.project_id IN (select project_id from user_project where user_id = '.$this->session->userdata("id").')');
		}
        $this->db->where($this->a .'. status IN (1,3)');
        $this->db->order_by($this->a . '. attendance_date_time', "DESC");
        //$this->db->like('attendance_date_time',date("Y-m-d"));
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

    function attendance_search($limit,$start,$search,$col,$dir)
    {
        $company_id = $this->session->userdata('company_id');
        $this->db->select($this->a . '.attendance_id');
        $this->db->select($this->a . '.hajiri');
        $this->db->select($this->a . '.amount');
        $this->db->select($this->p . '.project_name');
        $this->db->select($this->w . '.labour_name');
        $this->db->select($this->w . '.labour_last_name');
        $this->db->select($this->c . '.category_name');
        $this->db->select($this->w . '.worker_contact');
        $this->db->select($this->a . '.attendance_date_time AS date');
        $this->db->from($this->a);
        $this->db->join($this->w, $this->a .'. `worker_id` = '.$this->w .'. worker_id');
        $this->db->join($this->p, $this->a .'. `project_id` = '.$this->p .'. project_id');
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->a .'. worker_id  IN (select worker_id from worker where 
            company_id = '.$company_id.')');
		if ($this->session->userdata('user_designation') == 'Supervisor'){
			$this->db->where($this->a.'.project_id IN (select project_id from user_project where user_id = '.$this->session->userdata("id").')');
		}
        $this->db->where($this->a .'. status IN (1,3)');
        $this->db->order_by($this->a . '. attendance_date_time', "DESC");
        $this->db->like($this->w .'.labour_name',$search);
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


    function attendance_col_search($limit,$column,$start,$search,$col,$dir)
    {
        $company_id = $this->session->userdata('company_id');
        $this->db->select($this->a . '.attendance_id');
        $this->db->select($this->a . '.hajiri');
        $this->db->select($this->a . '.amount');
        $this->db->select($this->p . '.project_name');
        $this->db->select($this->w . '.labour_name');
        $this->db->select($this->w . '.labour_last_name');
        $this->db->select($this->c . '.category_name');
        $this->db->select($this->w . '.worker_contact');
        $this->db->select($this->a . '.attendance_date_time AS date');
        $this->db->from($this->a);
        $this->db->join($this->w, $this->a .'. `worker_id` = '.$this->w .'. worker_id');
        $this->db->join($this->p, $this->a .'. `project_id` = '.$this->p .'. project_id');
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->a .'. worker_id  IN (select worker_id from worker where 
            company_id = '.$company_id.')');
        if ($this->session->userdata('user_designation') == 'Supervisor'){
			$this->db->where($this->a.'.project_id IN (select project_id from user_project where user_id = '.$this->session->userdata("id").')');
		}
        $this->db->where($this->a .'. status IN (1,3)');
        $this->db->order_by($this->a . '. attendance_date_time', "DESC");
        $this->db->like($column,$search);
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

    function attendance_where_search($limit,$where,$start,$col,$dir)
    {
        $company_id = $this->session->userdata('company_id');
        $this->db->select($this->a . '.attendance_id');
        $this->db->select($this->a . '.hajiri');
        $this->db->select($this->a . '.amount');
        $this->db->select($this->p . '.project_name');
        $this->db->select($this->w . '.labour_name');
        $this->db->select($this->w . '.labour_last_name');
        $this->db->select($this->c . '.category_name');
        $this->db->select($this->w . '.worker_contact');
        $this->db->select($this->a . '.attendance_date_time AS date');
        $this->db->from($this->a);
        $this->db->join($this->w, $this->a .'. `worker_id` = '.$this->w .'. worker_id');
        $this->db->join($this->p, $this->a .'. `project_id` = '.$this->p .'. project_id');
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->a .'. worker_id  IN (select worker_id from worker where 
            company_id = '.$company_id.')');
        if ($this->session->userdata('user_designation') == 'Supervisor'){
			$this->db->where($this->a.'.project_id IN (select project_id from user_project where user_id = '.$this->session->userdata("id").')');
		}
        $this->db->where($this->a .'. status IN (1,3)');
        $this->db->where($where);
        $this->db->order_by($this->a . '. attendance_date_time', "DESC");
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

    function attendance_where_text_search($limit,$where,$search,$start,$col,$dir)
    {
        $company_id = $this->session->userdata('company_id');
        $this->db->select($this->a . '.attendance_id');
        $this->db->select($this->a . '.hajiri');
        $this->db->select($this->a . '.amount');
        $this->db->select($this->p . '.project_name');
        $this->db->select($this->w . '.labour_name');
        $this->db->select($this->w . '.labour_last_name');
        $this->db->select($this->c . '.category_name');
        $this->db->select($this->w . '.worker_contact');
        $this->db->select($this->a . '.attendance_date_time AS date');
        $this->db->from($this->a);
        $this->db->join($this->w, $this->a .'. `worker_id` = '.$this->w .'. worker_id');
        $this->db->join($this->p, $this->a .'. `project_id` = '.$this->p .'. project_id');
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->a .'. worker_id  IN (select worker_id from worker where 
            company_id = '.$company_id.')');
        if ($this->session->userdata('user_designation') == 'Supervisor'){
			$this->db->where($this->a.'.project_id IN (select project_id from user_project where user_id = '.$this->session->userdata("id").')');
		}
        $this->db->where($this->a .'. status IN (1,3)');
        $this->db->where($where);
        $this->db->order_by($this->a . '. attendance_date_time', "DESC");
        $this->db->limit($limit,$start);
        $this->db->order_by($col,$dir);
        $this->db->like($this->w .'.labour_name',$search);
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

    function attendance_search_count($search)
    {
        $company_id = $this->session->userdata('company_id');
        $this->db->select($this->a . '.attendance_id');
        $this->db->from($this->a);
        $this->db->join($this->w, $this->a .'. `worker_id` = '.$this->w .'. worker_id');
        $this->db->join($this->p, $this->a .'. `project_id` = '.$this->p .'. project_id');
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->a .'. worker_id  IN (select worker_id from worker where 
            company_id = '.$company_id.')');
		if ($this->session->userdata('user_designation') == 'Supervisor'){
			$this->db->where($this->a.'.project_id IN (select project_id from user_project where user_id = '.$this->session->userdata("id").')');
		}
        $this->db->where($this->a .'. status IN (1,3)');
        $this->db->order_by($this->a . '. attendance_date_time', "DESC");
        $this->db->like($this->w .'.labour_name',$search);
        $query = $this->db->get();
    
        return $query->num_rows();
    }
    function attendance_where_search_count($where)
    {
        $company_id = $this->session->userdata('company_id');
        $this->db->select($this->a . '.attendance_id');
        $this->db->from($this->a);
        $this->db->join($this->w, $this->a .'. `worker_id` = '.$this->w .'. worker_id');
        $this->db->join($this->p, $this->a .'. `project_id` = '.$this->p .'. project_id');
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->a .'. worker_id  IN (select worker_id from worker where 
            company_id = '.$company_id.')');
        $this->db->where($where);
		if ($this->session->userdata('user_designation') == 'Supervisor'){
			$this->db->where($this->a.'.project_id IN (select project_id from user_project where user_id = '.$this->session->userdata("id").')');
		}
        $this->db->where($this->a .'. status IN (1,3)');
        $this->db->order_by($this->a . '. attendance_date_time', "DESC");
        $query = $this->db->get();
    
        return $query->num_rows();
    }
    function attendance_where_text_search_count($where,$search)
    {
        $company_id = $this->session->userdata('company_id');
        $this->db->select($this->a . '.attendance_id');
        $this->db->from($this->a);
        $this->db->join($this->w, $this->a .'. `worker_id` = '.$this->w .'. worker_id');
        $this->db->join($this->p, $this->a .'. `project_id` = '.$this->p .'. project_id');
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->a .'. worker_id  IN (select worker_id from worker where 
            company_id = '.$company_id.')');
		if ($this->session->userdata('user_designation') == 'Supervisor'){
			$this->db->where($this->a.'.project_id IN (select project_id from user_project where user_id = '.$this->session->userdata("id").')');
		}
        $this->db->where($where);
        $this->db->where($this->a .'. status IN (1,3)');
        $this->db->order_by($this->a . '. attendance_date_time', "DESC");
        $this->db->like($this->w .'.labour_name',$search);
        $query = $this->db->get();
    
        return $query->num_rows();
    }

    function attendance_custom_search_count($search, $column)
    {
        $company_id = $this->session->userdata('company_id');
        $this->db->select($this->a . '.attendance_id');
        $this->db->from($this->a);
        $this->db->join($this->w, $this->a .'. `worker_id` = '.$this->w .'. worker_id');
        $this->db->join($this->p, $this->a .'. `project_id` = '.$this->p .'. project_id');
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->a .'. worker_id  IN (select worker_id from worker where 
            company_id = '.$company_id.')');
        if ($this->session->userdata('user_designation') == 'Supervisor'){
			$this->db->where($this->a.'.project_id IN (select project_id from user_project where user_id = '.$this->session->userdata("id").')');
		}
        $this->db->where($this->a .'. status IN (1,3)');
        $this->db->order_by($this->a . '. attendance_date_time', "DESC");
        $this->db->like($column,$search);
        $query = $this->db->get();
    
        return $query->num_rows();
    }
/******************* Worker datatable  *********************/
    function allworker_count()
    {   
        $company_id = $this->session->userdata('company_id');
        
        $this->db->select($this->w . '.worker_id');
        $this->db->select($this->w . '.labour_name');
        $this->db->select($this->w . '.labour_last_name');
        $this->db->select($this->c . '.category_name');
        $this->db->select($this->w . '.status');
        $this->db->select($this->wage . '.worker_due_wage');
        $this->db->select($this->wage . '.`worker_wage_id` AS wageId');
        $this->db->select($this->pay . '.payment_date_time AS paymentDate');
        $this->db->select($this->pay . '.payment_amount AS Paid');
        $this->db->from($this->w);
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->join($this->wage, $this->w .'. `worker_wage` = '.$this->wage .'. worker_wage_id');
        $this->db->join($this->pay, $this->pay .'. `worker_id` = '.$this->w .'. worker_id', 'left');
        $this->db->where($this->w .'. company_id  = '.$company_id);
        $this->db->where($this->w .'. status != 0');
        $this->db->group_by($this->w . '. worker_id');
        $this->db->order_by($this->w . '. status', "ASC");
        $query = $this->db->get();
    
        return $query->num_rows();  

    }
    
    function allworker($limit,$start,$col,$dir)
    {   
        $company_id = $this->session->userdata('company_id');
        
        $this->db->select($this->w . '.worker_id');
        $this->db->select($this->w . '.labour_name');
        $this->db->select($this->w . '.labour_last_name');
        $this->db->select($this->c . '.category_name');
        $this->db->select($this->w . '.status');
        $this->db->select($this->wage . '.worker_due_wage');
        $this->db->select($this->wage . '.`worker_wage_id` AS wageId');
        $this->db->select($this->pay . '.payment_date_time AS paymentDate');
        $this->db->select($this->pay . '.payment_amount AS Paid');
        $this->db->from($this->w);
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->join($this->wage, $this->w .'. `worker_wage` = '.$this->wage .'. worker_wage_id');
        $this->db->join($this->pay, $this->pay .'. `worker_id` = '.$this->w .'. worker_id', 'left');
        $this->db->where($this->w .'. company_id  = '.$company_id);
        $this->db->where($this->w .'. status != 0');
        $this->db->group_by($this->w . '. worker_id');
        $this->db->order_by($this->w . '. status', "ASC");
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

    function worker_search($limit,$start,$search,$col,$dir)
    {
       $company_id = $this->session->userdata('company_id');
        
        $this->db->select($this->w . '.worker_id');
        $this->db->select($this->w . '.labour_name');
        $this->db->select($this->w . '.labour_last_name');
        $this->db->select($this->c . '.category_name');
        $this->db->select($this->w . '.status');
        $this->db->select($this->wage . '.worker_due_wage');
        $this->db->select($this->wage . '.`worker_wage_id` AS wageId');
        $this->db->select($this->pay . '.payment_date_time AS paymentDate');
        $this->db->select($this->pay . '.payment_amount AS Paid');
        $this->db->from($this->w);
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->join($this->wage, $this->w .'. `worker_wage` = '.$this->wage .'. worker_wage_id');
        $this->db->join($this->pay, $this->pay .'. `worker_id` = '.$this->w .'. worker_id', 'left');
        $this->db->where($this->w .'. company_id  = '.$company_id);
        $this->db->where($this->w .'. status != 0');
        $this->db->group_by($this->w . '. worker_id');
        $this->db->order_by($this->w . '. status', "ASC");
        $this->db->like('labour_name',$search);
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


    function worker_col_search($limit,$column,$start,$search,$col,$dir)
    {
        $company_id = $this->session->userdata('company_id');
        
        $this->db->select($this->w . '.worker_id');
        $this->db->select($this->w . '.labour_name');
        $this->db->select($this->w . '.labour_last_name');
        $this->db->select($this->c . '.category_name');
        $this->db->select($this->w . '.status');
        $this->db->select($this->wage . '.worker_due_wage');
        $this->db->select($this->wage . '.`worker_wage_id` AS wageId');
        $this->db->select($this->pay . '.payment_date_time AS paymentDate');
        $this->db->select($this->pay . '.payment_amount AS Paid');
        $this->db->from($this->w);
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->join($this->wage, $this->w .'. `worker_wage` = '.$this->wage .'. worker_wage_id');
        $this->db->join($this->pay, $this->pay .'. `worker_id` = '.$this->w .'. worker_id', 'left');
        $this->db->where($this->w .'. company_id  = '.$company_id);
        $this->db->where($this->w .'. status != 0');
        $this->db->group_by($this->w . '. worker_id');
        $this->db->order_by($this->w . '. status', "ASC");
        $this->db->like($column,$search);
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

    function worker_search_count($search)
    {
        $company_id = $this->session->userdata('company_id');
        
        $this->db->select($this->w . '.worker_id');
        $this->db->select($this->w . '.labour_name');
        $this->db->select($this->w . '.labour_last_name');
        $this->db->select($this->c . '.category_name');
        $this->db->select($this->w . '.status');
        $this->db->select($this->wage . '.worker_due_wage');
        $this->db->select($this->wage . '.`worker_wage_id` AS wageId');
        $this->db->select($this->pay . '.payment_date_time AS paymentDate');
        $this->db->select($this->pay . '.payment_amount AS Paid');
        $this->db->from($this->w);
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->join($this->wage, $this->w .'. `worker_wage` = '.$this->wage .'. worker_wage_id');
        $this->db->join($this->pay, $this->pay .'. `worker_id` = '.$this->w .'. worker_id', 'left');
        $this->db->where($this->w .'. company_id  = '.$company_id);
        $this->db->where($this->w .'. status != 0');
        $this->db->group_by($this->w . '. worker_id');
        $this->db->order_by($this->w . '. status', "ASC");
         $this->db->like('labour_name',$search);
        $query = $this->db->get();
    
        return $query->num_rows();
    }

    function worker_custom_search_count($search, $column)
    {
        $company_id = $this->session->userdata('company_id');
        
        $this->db->select($this->w . '.worker_id');
        $this->db->select($this->w . '.labour_name');
        $this->db->select($this->w . '.labour_last_name');
        $this->db->select($this->c . '.category_name');
        $this->db->select($this->w . '.status');
        $this->db->select($this->wage . '.worker_due_wage');
        $this->db->select($this->wage . '.`worker_wage_id` AS wageId');
        $this->db->select($this->pay . '.payment_date_time AS paymentDate');
        $this->db->select($this->pay . '.payment_amount AS Paid');
        $this->db->from($this->w);
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->join($this->wage, $this->w .'. `worker_wage` = '.$this->wage .'. worker_wage_id');
        $this->db->join($this->pay, $this->pay .'. `worker_id` = '.$this->w .'. worker_id', 'left');
        $this->db->where($this->w .'. company_id  = '.$company_id);
        $this->db->where($this->w .'. status != 0');
        $this->db->group_by($this->w . '. worker_id');
        $this->db->order_by($this->w . '. status', "ASC");
        $this->db->like($column,$search);
        $query = $this->db->get();
    
        return $query->num_rows();
    }



    public function abscent($table,$data, $where) {
        $this->db->update($table, $data, $where);
        return $this->db->affected_rows();
    }

    public function attendanceDetail($id){
        $this->db->select('*');
        $this->db->from('attendance');
        $this->db->where('attendance_id',$id);
        $query = $this->db->get();
        return $result = $query->row();
    }
    public function lastRecord($id) {
        $this->db->select('payment_amount AS amount');
        $this->db->from($this->pay);
        $this->db->where('worker_id',$id);
        $this->db->order_by($this->pay . '. payment_id', "DESC");
        $query = $this->db->get();
        return $query->row();
    }

    public function checkAttendance($id,$date) {
        $where = 'worker_id = '.$id.' AND attendance_date_time LIKE "'.$date.'%"';
        $this->db->select('*');
        $this->db->from('attendance');
        $this->db->where($where);
        $this->db->where('status',1);
        return $this->db->count_all_results();
    }

    public function checkAttendanceHoliday($id,$date) {
        $where = 'worker_id = '.$id.' AND attendance_date_time LIKE "'.$date.'%"';
        $this->db->select('*');
        $this->db->from('attendance');
        $this->db->where($where);
        $this->db->where('status IN ("1" , "3")');
        return $this->db->count_all_results();
    }

    public function checkAttendanceStatus($id,$date) {
        $where = 'worker_id = '.$id.' AND attendance_date_time LIKE "'.$date.'%"';
        $this->db->select('status');
        $this->db->from('attendance');
        $this->db->where($where);
        $this->db->where('status',1);
        $query = $this->db->get();
        return $result = $query->row();
    }

    public function update($table,$data,$where){
        $this->db->update($table, $data, $where);
        return $this->db->affected_rows();
    }
    public function checkAttendance2($id,$date) {
        $where = 'worker_id = '.$id.' AND attendance_date_time LIKE "'.$date.'%"';
        $data = array('status' => 0, );
        $this->db->update('attendance', $data, $where);
        return $this->db->affected_rows();
    }

    public function save($table, $data) {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    public function get_all_where($table,$id) {
        $this->db->select('project_id AS id, project_name AS name');
        $this->db->from($table);
        $this->db->where('company_id  = '.$id);
        $this->db->where('status',1);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_wage($id) {
        $this->db->select('*');
        $this->db->from('worker_wage');
        $this->db->where('worker_id',$id);
        $query = $this->db->get();
        return $result = $query->row();
    }
	
    public function get_balance($id, $month) {
        $this->db->select('balance');
        $this->db->select('balance_id');
        $this->db->from('balance');
        $this->db->where('worker_id',$id);
        $this->db->where('month',$month);
        $query = $this->db->get();
        return $result = $query->row();
    }

	/**************** Send sms attendance wise ***********************/
	function allSmsAttendanceCount($category,$where)
    {   
        $company_id = $this->session->userdata('company_id');
        
        $this->db->select('*');
        $this->db->from($this->a);
		
		$this->db->join($this->w, $this->w .' . `worker_id` = '.$this->a .' . worker_id');
		$this->db->join($this->c, $this->w .' . `category_id` = '.$this->c .' . category_id');
        $this->db->where($this->a .'. worker_id  IN (select worker_id from worker where 
            company_id = '.$company_id.')');
        $this->db->where($this->a .'. status IN (1,3)');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->num_rows();  
    }

	/**************** Send sms number wise ***********************/
	function allSmsNumberCount($category,$where)
    {   
        $company_id = $this->session->userdata('company_id');
        
        $this->db->select('*');
        $this->db->from($this->a);
		$this->db->join($this->w, $this->w .' . `worker_id` = '.$this->a .' . worker_id');
		$this->db->join($this->c, $this->w .' . `category_id` = '.$this->c .' . category_id');
        $this->db->where($this->a .'. worker_id  IN (select worker_id from worker where 
            company_id = '.$company_id.')');
        $this->db->where($this->a .'. status IN (1,3)');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->num_rows();  
    }
	function allSmsNumberData($category,$where)
    {   
        $company_id = $this->session->userdata('company_id');
        
        $this->db->select('attendance.hajiri as hajiri, attendance.amount as amount, CONCAT( worker.labour_name, " ", worker.labour_last_name) AS name, worker.worker_contact as contact, worker_wage.worker_due_wage AS due');
        $this->db->from($this->a);
		$this->db->join($this->w, $this->w .'.worker_id = '.$this->a .'.worker_id');
		$this->db->join($this->c, 'worker_category.category_id = '.$this->w .'.category_id ');
		$this->db->join('worker_wage','worker_wage.worker_id = '.$this->a .'.worker_id');
        $this->db->where($this->a .'. worker_id  IN (select worker_id from worker where 
            company_id = '.$company_id.')');
        $this->db->where($this->a .'. status IN (1,3)');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result();  
    }

    function get_activeUser() {

        $this->db->select($this->table1 . '.*,');
        if( $this->session->userdata('user_designation') == 'Superadmin' ){
            $this->db->select('c.company_name');
        }
        $this->db->from($this->table1);
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

}

?>