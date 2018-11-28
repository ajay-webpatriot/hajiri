<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Loguser extends CI_Controller {

    public $data;
	public function __construct() {
        parent::__construct();
		$this->data['menu_title'] = 'Loguser';
        $this->load->model('loguser_model', 'loguser');
        checkAdmin();
    }

    public function index() {
        $this->breadcrumbcomponent->add('Dashboard', base_url('/admin'));
        $this->breadcrumbcomponent->add('All Loguser', base_url('/admin/loguser'));
        $data['breadcrumb'] = $this->breadcrumbcomponent->output();
        
		$data = $this->data;
		$data['logusers'] = $this->loguser->get_datatables();
        $data['title'] = 'Loguser List';
        $data['description'] = 'All loguser list';
        $data['page'] = 'loguser/list_loguser';
        $this->load->view('includes/template', $data);
    }

}
