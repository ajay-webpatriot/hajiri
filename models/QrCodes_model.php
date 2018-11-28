<?php


class QrCodes_model extends CI_Model {

    var $a = 'attendance';
    var $w = 'worker';
    var $c = 'worker_category';
    var $wage = 'worker_wage';
    var $p = 'project';
    var $u = 'user';
    var $pay = 'payment';

    public function getLabours( $data ) {
        $this->db->select('W.*, WC.category_id, WC.category_name');
        $this->db->from( 'worker W' );
        $this->db->join('worker_category WC', "WC.category_id = W.category_id");
		if( !$data['all_labour'] ) {
			if ( is_array( $data['labour_ids'] ) && !empty( $data['labour_ids'] ) ) {
				$this->db->where_in('W.worker_id', $data['labour_ids'] );
			}
			if ( !empty( $data['join_date'] ) ) {
				$this->db->where( "W.labour_join_date", $data['join_date'] );
			}
		}
		$this->db->where( "W.company_id", $data['company_id'] );
        $query = $this->db->get();
        return $query->result();
		//echo $this->db->last_query(); exit;
    }

    public function get_labours($where) {
        $this->db->select('*');
        $this->db->from('worker');
        $this->db->where($where);
        $this->db->where('status = 1');
        $query = $this->db->get();
        return $query->result();
    }
/*************************** Datatable data ***************************/

    function allworker_count()
    {   
        $company_id = $this->session->userdata('company_id');
        
        $this->db->select($this->w . '.worker_id');
        $this->db->select($this->w . '.labour_name');
        $this->db->select($this->w . '.labour_last_name');
        $this->db->select($this->c . '.category_name');
        $this->db->select($this->w . '.labour_join_date');
        $this->db->from($this->w);
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->w .'. company_id  = '.$company_id);
        $this->db->where($this->w .'. status = 1');
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
        $this->db->select($this->w . '.labour_join_date');
        $this->db->from($this->w);
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->w .'. company_id  = '.$company_id);
        $this->db->where($this->w .'. status = 1');
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
        $this->db->select($this->w . '.labour_join_date');
        $this->db->from($this->w);
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->w .'. company_id  = '.$company_id);
        $this->db->where($this->w .'. status = 1');
        $this->db->group_by($this->w . '. worker_id');
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
        $this->db->select($this->w . '.labour_join_date');
        $this->db->from($this->w);
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->w .'. company_id  = '.$company_id);
        $this->db->where($this->w .'. status = 1');
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
        $this->db->select($this->w . '.labour_join_date');
        $this->db->from($this->w);
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->w .'. company_id  = '.$company_id);
        $this->db->where($this->w .'. status = 1');
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
        $this->db->select($this->w . '.labour_join_date');
        $this->db->from($this->w);
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->w .'. company_id  = '.$company_id);
        $this->db->where($this->w .'. status = 1');
        $this->db->group_by($this->w . '. worker_id');
        $this->db->order_by($this->w . '. status', "ASC");
        $this->db->like($column,$search);
        $query = $this->db->get();
    
        return $query->num_rows();
    }

    function attendance_where_search($limit,$where,$start,$col,$dir)
    {
        $company_id = $this->session->userdata('company_id');
        $this->db->select($this->w . '.worker_id');
        $this->db->select($this->w . '.labour_name');
        $this->db->select($this->w . '.labour_last_name');
        $this->db->select($this->c . '.category_name');
        $this->db->select($this->w . '.labour_join_date');
        $this->db->from($this->w);
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->w .'. company_id  = '.$company_id);
        $this->db->where($this->w .'. status = 1');
        $this->db->where($where);
        $this->db->group_by($this->w . '. worker_id');
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
        $this->db->select($this->w . '.worker_id');
        $this->db->select($this->w . '.labour_name');
        $this->db->select($this->w . '.labour_last_name');
        $this->db->select($this->c . '.category_name');
        $this->db->select($this->w . '.labour_join_date');
        $this->db->from($this->w);
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->w .'. company_id  = '.$company_id);
        $this->db->where($this->w .'. status = 1');
        $this->db->where($where);
        $this->db->group_by($this->w . '. worker_id');
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
    function attendance_where_search_count($where)
    {
        $company_id = $this->session->userdata('company_id');
        $this->db->select($this->w . '.worker_id');
        $this->db->select($this->w . '.labour_name');
        $this->db->select($this->w . '.labour_last_name');
        $this->db->select($this->c . '.category_name');
        $this->db->select($this->w . '.labour_join_date');
        $this->db->from($this->w);
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->w .'. company_id  = '.$company_id);
        $this->db->where($this->w .'. status = 1');
        $this->db->where($where);
        $query = $this->db->get();
    
        return $query->num_rows();
    }

    function attendance_search_count($search)
    {
        $company_id = $this->session->userdata('company_id');
        $this->db->select($this->w . '.worker_id');
        $this->db->select($this->w . '.labour_name');
        $this->db->select($this->w . '.labour_last_name');
        $this->db->select($this->c . '.category_name');
        $this->db->select($this->w . '.labour_join_date');
        $this->db->from($this->w);
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->w .'. company_id  = '.$company_id);
        $this->db->where($this->w .'. status = 1');
        $this->db->like($this->w .'.labour_name',$search);
        $query = $this->db->get();
    
        return $query->num_rows();
    }

    function attendance_where_text_search_count($where,$search)
    {
        $company_id = $this->session->userdata('company_id');
        $this->db->select($this->w . '.worker_id');
        $this->db->select($this->w . '.labour_name');
        $this->db->select($this->w . '.labour_last_name');
        $this->db->select($this->c . '.category_name');
        $this->db->select($this->w . '.labour_join_date');
        $this->db->from($this->w);
        $this->db->join($this->c, $this->w .'. `category_id` = '.$this->c .'. category_id');
        $this->db->where($this->w .'. company_id  = '.$company_id);
        $this->db->where($this->w .'. status = 1');
        $this->db->like($this->w .'.labour_name',$search);
        $query = $this->db->get();
    
        return $query->num_rows();
    }


}
