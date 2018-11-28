<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class HajiriSms extends CI_Controller {

    public $data;
	public function __construct() {
        parent::__construct();
		$this->data['menu_title'] = 'Hajiri SMS';
        $this->load->model('Companies_model', 'company');
        $this->load->model('Register_model', 'register');
        checkAdmin();
    }

    public function index() {
        $this->breadcrumbcomponent->add('Dashboard', base_url('/admin'));
        $this->breadcrumbcomponent->add('Hajiri SMS', base_url('/admin/Hajiri SMS'));

		$data = $this->data;
        $data['breadcrumb'] = $this->breadcrumbcomponent->output();
        $data['company'] = $this->company->get_hajiriSmsCompany();
        $data['title'] = 'Hajiri SMS';
        $data['description'] = '';
        $data['page'] = 'hajiriSms/hajiri_superAdmin';
        $this->load->view('includes/template', $data);
    }

	public function sendSmsData(){
		$company_id = $this->input->post('company');
		$fromDate = date("Y-m-d", strtotime($this->input->post('from')) )." 00:00:00";
		$toDate = date("Y-m-d", strtotime($this->input->post('to') ))." 23:59:59";
		$data = $this->company->get_smsCount($company_id,$fromDate,$toDate);
		echo json_encode($data);
	}
}
