<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Loguser_model extends CI_Model {

    var $table1 = 'log';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function get_datatables() {
        $user_id = $this->session->userdata('id');
        $this->db->select($this->table1 . '.*,');
        $this->db->from($this->table1);
        $this->db->where($this->table1 .".user_id", $user_id);
        $this->db->order_by($this->table1 . '.log_date_time', "asc");
        $query = $this->db->get();
        return $query->result();
    }

  
}
