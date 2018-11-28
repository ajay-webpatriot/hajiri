<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Companies_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function get_datatables() {
        $this->db->select('*');
        $this->db->from('company');
        $this->db->where("status != ", 0);
        $this->db->order_by('compnay_id', "desc");
        $query = $this->db->get();
        return $query->result();
    }
	function get_hajiriSmsCompany(){
		$this->db->select('compnay_id AS id, company_name AS name');
        $this->db->from('company');
        $this->db->where(" compnay_id IN ( select company_id from company_plugin_association where plugin_id = 8 )");
		$this->db->order_by('compnay_id', "desc");
        $query = $this->db->get();
        return $query->result();
	}
    function pricingPlan(){
        $this->db->select('id,name');
        $this->db->from('pricing_plans');
        $query = $this->db->get();
        return $query->result();
    }
    function pluginList(){
        $this->db->select('plugin_id,plugin_name');
        $this->db->from('plugin');
        $this->db->where("status",1);
        $query = $this->db->get();
        return $query->result();
    }
    function get_company_plan($company_id){
        $this->db->select(' pp.name,pp.id,cp.plan_id,c.company_name,cp.company_plan_id AS id , cp.plan_type');
        $this->db->from('`company_plan` cp ');
        $this->db->join('pricing_plans pp','cp.plan_id = pp.id');
        $this->db->join('company c','c.compnay_id = cp.company_id');
        $this->db->where("c.compnay_id", $company_id);
        $query = $this->db->get();
        return $query->row();
    }
    function get_company_plugin($company_id){
        $this->db->select('cp.plugin_id AS plugin_id');
        $this->db->from('`company_plugin_association` cp');
        $this->db->join('plugin p','p.plugin_id = cp.plugin_id');
        $this->db->where("cp.company_id", $company_id);
        $this->db->where("p.status", 1);
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_company_detl($company_id) {
        return $this->db->where('compnay_id', $company_id)->get('company')->row();
    }

    public function getAllRecords( $table, $where ) {

        $this->db->select( '*' );
        $this->db->from( $table );
        $this->db->where( $where );
        $query = $this->db->get();
        return $query->result();
    }
	    
    public function companyName($id){
        $this->db->select( 'company_name' );
        $this->db->from( 'company' );
        $this->db->where( 'compnay_id',$id );
        $query = $this->db->get();
        return $query->row();
    }

	public function get_smsCount($id, $from, $to){
        $this->db->select( ' sum(total_present) AS present, sum(total_sms) AS sms, sum(sms_sent) as sent' );
        $this->db->from( 'hajiri_sms' );
        $this->db->where( 'company_id',$id );
        $this->db->where( ' date_time between "'.$from.'" AND "'.$to.'"'  );
        $query = $this->db->get();
        return $query->row();
    }

    public function getRecord( $where ){
        return $this->db->where($where)->get('company')->row();
        //echo $this->db->last_query();
    }
    

    public function save($table, $data) {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }
    
    public function update($table, $where, $data) {
        $this->db->update($table, $data, $where);
        return $this->db->affected_rows();
    }

    public function delete($table, $col_name, $value) {
        $this->db->where($col_name, $value);
        $this->db->delete($table);
        return $this->db->affected_rows();
    }
    public function getInvoiceNo(){
        $this->db->select('lastinvoiceid');
        $this->db->from('invoicelog');
        $query = $this->db->get();
        return $query->row();
    }
    public function insertInvoiceId(){
        $this->db->insert('invoicelog', array('lastinvoiceid' => '0' ));
        // $this->db->insert('user',$data);
            return true;
    }
    public function updateInvoiceId($invoiceId){
        $this->db->update('invoicelog',array('lastinvoiceid' => $invoiceId ));
        return true;
    }
}
