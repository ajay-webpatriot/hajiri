<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Labourimport_model extends CI_Model {
   
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
   
    public function save( $table, $data ) {
        $this->db->insert( $table, $data );
        return $this->db->insert_id();
    }
	
	public function update( $table, $where, $data ) {
        return $this->db->update( $table, $data, $where );
    }

    public function getAllRecords( $table, $where ) {

        $this->db->select( '*' );
        $this->db->from( $table );
        $this->db->where( $where );
        $query = $this->db->get();
        return $query->result();
    }

}
