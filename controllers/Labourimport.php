<?php

defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('max_execution_time', 0); 
ini_set('memory_limit','2048M');
class Labourimport extends CI_Controller {

    public $data;
	public function __construct() {
        parent::__construct();
		$this->data['menu_title'] = 'Labourimport';
        $this->load->model('Labourimport_model', 'labourimport');
        checkAdmin();
    }

    public function index() {
        
    }

    public function importLabourFile() {
        $fileError = $skipped_data = array();
        $skip_row = $insert_row = $PHPdateValue = "";

        if ( isset( $_POST['submit'] ) ) {
			$company_id = $this->input->post('company_id') ? $this->input->post('company_id') : $this->session->userdata('company_id');
            if ( $company_id ) {
                $associatedFileNames = array('image');
                foreach ( $associatedFileNames as $fileName ) {
                    if ( !empty( $_FILES[$fileName]['name'] ) ) {
                        $result = uploadUserFile( 'uploads/labour/', $fileName );
                        if ( $result['flag'] == 1 && ( $result['extension'] == ".xls" || $result['extension'] == ".xlsx" ) ) {
                            $file = $result['filePath'];
                            $labourXLS = xlsToarray($file);
                            $skip_row = $insert_row = 0;
                            $skipped_data = array();
                            foreach ($labourXLS['rowdata'] as $key => $value) {

                                if ( empty( $value['A'] ) || empty( $value['D'] ) || empty( $value['E'] ) || empty( $value['F'] ) ) { 
									$skipped_data[] = $value;
                                    $skip_row ++;
                                    continue;
								}
								
								$phoneno = "/^((\+){0,1}91(\s){0,1}(\-){0,1}(\s){0,1})?([0-9]{10})$/";
								$aadhar_nu = "/^((\+){0,1}91(\s){0,1}(\-){0,1}(\s){0,1})?([0-9]{10})$/";
								
								if ( !preg_match("/^[a-zA-Z ]+$/", $value['A'] )
									|| !preg_match( '/^[0-9]+(\.[0-9])?$/', $value['D'] )
									|| !preg_match("/^[a-zA-Z ]+$/", $value['E'] )
									|| !preg_match("/^[a-zA-Z ]+$/", $value['F'] )
								) {
                                    if(!empty( $value['B'] )){
                                        if(!preg_match("/^[a-zA-Z ]+$/", $value['B'] )){
                                            $skipped_data[] = $value;
                                            $skip_row ++;
                                            continue;
                                        }
                                    }
                                    if(!empty( $value['C'] )){
                                        if(!preg_match( $phoneno, $value['C'] )){
                                            $skipped_data[] = $value;
                                            $skip_row ++;
                                            continue;
                                        }
                                    }

                                    if(!empty( $value['G'] )){
                                        if(!preg_match( '/^-?[0-9]+(\.[0-9])?$/', $value['G'] ) ){
                                            $skipped_data[] = $value;
                                            $skip_row ++;
                                            continue;
                                        }
                                    }
                                    
									$skipped_data[] = $value;
                                    $skip_row ++;
                                    continue;
                                }
								
								$sql = "SELECT worker_id FROM `worker` WHERE `labour_name`= '" . $value['A'] . "' AND `labour_last_name` = '" . $value['B'] . "' AND company_id = ".$company_id;
								$workerRecord = $this->db->query($sql)->row();
								if( !empty( $workerRecord ) ) {
									$skipped_data[] = $value;
									$skip_row ++;
									continue;
								}
								
								$sql = "SELECT category_id FROM `worker_category` WHERE `category_name`= '" . $value['E'] . "' AND company_id = ".$company_id;
								$workerCategory = $this->db->query($sql)->row();

								if ( empty( $workerCategory ) ) {
									$workerCategoryData = array(
										'company_id' => $company_id,
										'category_name' => $value['E'],
										'status' => 1
									);
									$workerCategoryId = $this->labourimport->save( 'worker_category', $workerCategoryData );
								} else {
									$workerCategoryId = $workerCategory->category_id;
								}
                               

                                $this->load->library('ciqrcode');

                                $barcode_date_time = date('dmY_His') . '-' . $key;
                                $image_name = date('Y_m_d_H_i_s') . '.jpg';
                                $params['data'] = $barcode_date_time;
                                $params['level'] = 'H';
                                $params['size'] = 10;
                                $params['savename'] = FCPATH . 'uploads/barcodes/' . $image_name;
                                $this->ciqrcode->generate($params);

                                $workerData = array(
                                    'company_id'        => $company_id,
                                    'labour_name'       => $value['A'],
                                    'labour_last_name'  => ( ( isset($value['B'] ) ) ? $value['B'] : '' ),
                                    'worker_contact'    => ( ( isset($value['C'] ) ) ? $value['C'] : '' ),
                                    'labour_join_date'  => date('Y-m-d'),
                                    'labour_aadhar'     => '',
                                    'worker_wage'       => 0,
                                    'category_id'       => $workerCategoryId,
                                    'worker_qrcode_image'     => $image_name,
                                    'worker_qrcode'     => $barcode_date_time,
                                    'status'            => 1,
                                    'worker_wage_type'  => strtolower( $value['F'] ) == 'monthly' ? 1 : 0
                                );

                                $labour_id = $this->labourimport->save('worker', $workerData);
                                sleep(1);
                                if ($labour_id) {
									$insert_row ++;
									
									$workerWageData = array(
										'worker_id' 		=> $labour_id,
										'worker_wage' 		=> $value['D'],
										'wage_start_date' 	=> date('Y-m-d'),
										'wage_end_date' 	=> NULL,
										'worker_paid_wage' 	=> 0,
										'worker_due_wage' 	=> 0,
										'worker_total_wage' 	=> 0,
										'worker_opening_wage' 	=> ( isset($value['G'] ) ? $value['G'] : '0' ),
										'worker_wage_type' 	=> strtolower( $value['F'] ) == 'monthly' ? 1 : 0
									);
									
									$worker_wage_id = $this->labourimport->save('worker_wage', $workerWageData);
									
									$where = array( 'worker_id' => $labour_id );
									$workerData = array();
									$workerData['worker_wage'] = $worker_wage_id;
									$worker_wage_id = $this->labourimport->update('worker', $where, $workerData);									
                                }
                            }//foreach end
                            unlink($file); //unlink imported file
                            $this->session->set_flashdata('success', "File Imported Successfully");
                            $this->session->set_flashdata('skippedData',  $skipped_data);
                            $this->session->set_flashdata('skip_rows',  $skip_row);
                            $this->session->set_flashdata('insertRows',  $insert_row);

                        }//if file upload fail
                        else {
                            $fileError[$fileName] = $result['error'];
                            $this->session->set_flashdata('error', "Failed to import file");
                        }
                    }
                }
            }
        }
        $id = $this->session->userdata('id');
		$data['companies'] = $this->labourimport->getAllRecords( 'company', array( 'status' => 1 ) );

        $data['menu_title'] = 'Bulk worker upload';
        $data['title'] = 'Bulk worker upload';
        $data['fileError'] = $fileError;
        $data['skippedData'] = $skipped_data;
        $data['insertRows'] = $insert_row;
        $data['skipRows'] = $skip_row;
        $data['description'] = 'Import Labour List';
        $data['page'] = 'labourimport/import_labour';
        $this->load->view('includes/template', $data);
    }

}
