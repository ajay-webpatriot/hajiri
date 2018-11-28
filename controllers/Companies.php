<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Companies extends CI_Controller {

	public $data;
    public function __construct() {
        parent::__construct();
		$this->data['menu_title'] = 'Company';
        $this->load->model('Companies_model', 'company');
        checkAdmin();
    }

    public function index() {

        $data = $this->data;
        $data['companies'] = $this->company->get_datatables();
        $data['title'] = 'Company List';
        $data['description'] = 'All Company list';
        $data['page'] = 'companies/list_company';
        $this->load->view('includes/template', $data);

    }
	
	public function addEditCompany(){
		
		$fileError = array();
        if ( isset( $_POST['submit'] ) ) {

            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('company_name', 'Name', 'trim|required');
            $this->form_validation->set_rules('company_email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('company_contact_no', 'Contact No.', 'trim|required|regex_match[/^[0-9]{10}$/]|min_length[10]');
            $this->form_validation->set_rules('plugin[]', 'Plugin Name', 'trim|numeric');
        	$this->form_validation->set_rules('company_payment_plan_id', 'Company plan', 'trim|required|numeric');
			
			if ( $this->form_validation->run() == false ) {
				$this->session->set_flashdata( 'error', 'Sorry,  Error while adding company details.' );
				redirect( base_url( 'admin/companies/addEditCompany' . '/' . $company_id) );
			}
		
            $company_id = $this->input->post('company_id') ? $this->input->post('company_id') : '';
			$action = $company_id > 0 ? 'updated' : 'added';
						
			$data = array(
				'company_name' => $this->input->post('company_name'),
				'company_type' => $this->input->post('company_type'),
				'company_payment_plan_id' => $this->input->post('company_payment_plan_id'),
				'company_address' => $this->input->post('company_address'),
				'company_pincode' => $this->input->post('company_pincode'),
				'company_city' => $this->input->post('company_city'),
				'company_state' => $this->input->post('company_state'),
				'company_country' => $this->input->post('company_country'),
				'company_email' => $this->input->post('company_email'),
				'company_contact_no' => $this->input->post('company_contact_no'),
				'company_website' => $this->input->post('company_website'),
				'company_gst' => $this->input->post('company_gst'),
				'company_pan' => $this->input->post('company_pan'),
				'status' => $this->input->post('status')
			);

			$associatedFileNames = array('company_logo_image');
			foreach ($associatedFileNames as $fileName) {
				if (!empty($_FILES[$fileName]['name'])) {
					$result = uploadStaffFile('uploads/user/', $fileName);
					if ($result['flag'] == 1) {
						$data[$fileName] = $result['filePath'];
					} else {
						$fileError[$fileName] = $result['error'];
					}
				}
			}

			if ( !empty( $fileError ) ) {
				$this->session->set_flashdata( 'error', 'Sorry,  Company photo is not ' . $action );
				redirect( base_url( 'admin/companies/addEditCompany' . '/' . $company_id) );
			}
			$company_id = $this->input->post('company_id');

			if( $company_id > 0 ){
				$result = $this->company->update('company', array('compnay_id' => $company_id), $data);
				if ($result){
					$pluginids = $this->input->post('plugin');
					$planId = $this->input->post('company_payment_plan_id');

					$updatePlan = $this->company->update('company_plan', array('company_id' => $company_id), array('plan_id' => $planId, 'create_date' => date('Y-m-d')));

					//For Assign Plug In
					$deletePlugin = $this->company->delete('company_plugin_association', 'company_id',$company_id);
	                foreach ($pluginids  as $pluginid) {
						$pluginAdd = $this->company->save('company_plugin_association', array('company_id'=>$company_id,'plugin_id' => $pluginid));
	                }
	                if($this->input->post('status') == 2){
	                	$inActiveData = array(
							'status' => $this->input->post('status'),
						);
						$inActive = $this->company->update('user', array('company_id' => $company_id), $inActiveData);

	                }
				}
			} else {
				$result = $this->company->save('company', $data);
				if ($result){
					$pluginids = $this->input->post('plugin');
					$planId = $this->input->post('company_payment_plan_id');

					$this->company->save('company_plan', array('company_id'=>$result,'plan_id' => $planId,'create_date' => date('Y-m-d')));
					//For Assign Plug In
	                foreach ($pluginids  as $pluginid) {
						$this->company->save('company_plugin_association', array('company_id'=>$result,'plugin_id' => $pluginid));
	                }
				}
			}
			if( $result ) {
				
				$this->session->set_flashdata('success', 'Company Data Successfully '.$action);
			} else {
				$this->session->set_flashdata('error', 'Sorry,  Data Not '.$action);
			}
			redirect(base_url('admin/companies'));
        }
		
		$this->breadcrumbcomponent->add('Dashboard', base_url('/admin'));
        $this->breadcrumbcomponent->add('Companies', base_url('/admin/companies'));
        $data['breadcrumb'] = $this->breadcrumbcomponent->output();

		$data = $this->data;
		$data['results'] = '';
		$data['title'] = 'Add Company';
		if( $this->uri->segment(4) > 0 ){
			$data['results'] = $this->company->get_company_detl( $this->uri->segment(4) );
			$data['title'] = 'Edit Company';
        	$data['planDetails'] = $this->company->get_company_plan($this->uri->segment(4));
        	$data['pluginAssigned'] = $this->company->get_company_plugin($this->uri->segment(4));
        	$data['plugin_assign_ids'] = array();
	        if (!empty($data['pluginAssigned'])) {
	            foreach ($data['pluginAssigned'] as $value) {
	                $data['plugin_assign_ids'][] = $value->plugin_id;
	            }
	        }
		}else{
			$data['pluginAssigned'] = -1;
		}
    	$data['pricingPlan'] = $this->company->pricingPlan();
    	$data['pluginList'] = $this->company->pluginList();

		$data['fileError'] = $fileError;
        $data['description'] = 'Company Information';
        $data['page'] = 'companies/add_edit_company';
        $this->load->view('includes/template', $data);
	}
	
	public function deleteCompany(){
		if( $this->uri->segment(4) > 0 ){
			$result = $this->company->get_company_detl( $this->uri->segment(4) );
		}
        if( isset( $result ) && count( $result ) > 0 ) {
			//$this->company->delete('company', 'compnay_id', $result->compnay_id);
			$data = [];
			$data['status'] = 0;
        	$inActiveData = array(
				'status' => 0,
			);
			$inActive = $this->company->update('user', array('company_id' => $result->compnay_id), $inActiveData);

			$this->company->update('company', array('compnay_id' => $result->compnay_id), $data);
			$this->session->set_flashdata('success', 'Data Deleted Successfully');
		} else {
			$this->session->set_flashdata('error', 'Sorry,  Data Not Deleted');
		}
        redirect(base_url('admin/companies'));
	}
	
	public function checkEmailExists(){
		$where = ['company_email' => $this->input->post('company_email') ];
		if( $this->input->post('company_id') ){
			$where['compnay_id != '] = $this->input->post('company_id');
		}
		
		if( $this->input->post('ajax_request') ){
			echo json_encode ( [ 'result' =>  $this->company->getRecord( $where ) ? true : false ] );
			exit;
		}
		return $this->company->getRecord( $where ) ? false : true;
	}

	public function editProfile() {
		$company_id = $this->session->userdata('company_id');
        $fileError = array();
        if (isset($_POST['profileSubmit'])) {
            
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('company_name', 'Name', 'trim|required');
            $this->form_validation->set_rules('company_email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('company_contact_no', 'Contact No.', 'trim|required|regex_match[/^[0-9]{10}$/]|min_length[10]');
					
			if ( $this->form_validation->run() == false ) {
				$this->session->set_flashdata( 'error', 'Sorry,  Company information is not valid.' );
				redirect( base_url( 'admin/companies/editProfile/'));
			}
			
			$data = array(
				'company_name' => $this->input->post('company_name'),
				'company_type' => $this->input->post('company_type'),
				'company_address' => $this->input->post('company_address'),
				'company_pincode' => $this->input->post('company_pincode'),
				'company_city' => $this->input->post('company_city'),
				'company_state' => $this->input->post('company_state'),
				'company_country' => $this->input->post('company_country'),
				'company_email' => $this->input->post('company_email'),
				'company_contact_no' => $this->input->post('company_contact_no'),
				'company_website' => $this->input->post('company_website'),
				'company_gst' => $this->input->post('company_gst'),
				'company_pan' => $this->input->post('company_pan'),
				'company_logo_image' => $this->input->post('company_logo'),
			);

            
			$result = $this->company->update('company', array('compnay_id' => $company_id), $data);

                if ($result) {
                    $this->session->set_flashdata('success', 'Profile Updated Successfully. ');
                    $this->session->set_userdata('company_logo', $this->input->post('company_logo'));
                	redirect(base_url('admin/'));
                } else {
                    $this->session->set_flashdata('error', 'Failed To Update Profile.');
					redirect( base_url( 'admin/companies/addEditCompany' . '/' . $company_id) );
            }
            //}
        }
        $this->breadcrumbcomponent->add('Dashboard', base_url('/admin'));
		$data['breadcrumb'] = $this->breadcrumbcomponent->output();
		
        $data = $this->data;
		$data['results'] = $this->company->get_company_detl( $company_id );
        $data['title'] = 'Edit profile';
        $data['fileError'] = $fileError;
        $data['description'] = 'Edit company profile';
        $data['page'] = 'companies/edit_profile';
        $this->load->view('includes/template', $data);
    }

}
