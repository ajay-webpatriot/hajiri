<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('max_execution_time', 0); 
ini_set('memory_limit','2048M');

class Report extends CI_Controller {

	public $data;
	public function __construct() {
        parent::__construct();

        require_once APPPATH.'third_party/PHPExcel.php';
         
		$this->data['menu_title'] = 'Report';
		$this->data['month'] = array( '', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
        $this->load->model('Report_model', 'report');
		$this->load->model('Foreman_model', 'foreman');
		$this->load->model('Project_model', 'project');
        $this->load->model('Category_model', 'category');
		$this->load->model('Companies_model', 'company');
    }

    public function index() {
        
    }

    function monthly_attendance_report() {
        $data = $this->data;

        $data['Category'] = $this->category->get_all_category($this->session->userdata('company_id'));

		$this->load->model('manager_model', 'manager');
		//For Getting Project
		$projects = array("" => "Select project ");
		$project_results = array();
		if($this->session->userdata('user_designation') == 'admin')
			$project_results = $this->project->get_datatables();
		if($this->session->userdata('user_designation') == 'Supervisor'){
			$project_results = $this->foreman->get_project_foremanId( $this->session->userdata('id'));
		}
		foreach ($project_results as $key => $value) {
			$projects[$value->project_id] = $value->project_name;
		}
		$data['menu_title'] = 'monthlyAttendance';
        $data['projects'] = $projects;
        //For getting Year
        $years = array("" => "Select year");
        foreach (range(2016, 2050) as $value) {
            $years[$value] = $value;
        }
        $data['years'] = $years;
        //For getting Month
        $months = array("" => "Select month", "1" => "January", "2" => "February", "3" => "March", "4" => "April",
            "5" => "May", "6" => "June", "7" => "July", "8" => "August",
            "9" => "September", "10" => "October", "11" => "November", "12" => "December",
        );
        $data['months'] = $months;
        $data['title'] = 'Monthly attendance report';
        $data['description'] = '';
        $data['page'] = 'report/monthly_attendance_report';
        if (isset($_REQUEST['submit'])) { 
            $this->form_validation->set_data($this->input->get());
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('project', 'Project', 'trim|required|numeric');
            $this->form_validation->set_rules('category', 'Category', 'trim');
            $this->form_validation->set_rules('month', 'Month', 'trim|required|numeric');
            $this->form_validation->set_rules('year', 'Year', 'trim|required|numeric');
            if ($this->form_validation->run() == TRUE) {
                $this->generate_monthly_attendance_register();
            }
        }
        $this->load->view('includes/template', $data);
    }
	
	function generate_monthly_attendance_register(){ 
		if($this->input->get_post('downloadformat') == 'pdf'){ 
			$border = '0';
			$header_colspan = '3';
		}else{
			$border = '1';
			$header_colspan = '6';
		}
		$postData = array();
        $postData['month'] 		= $this->input->get_post('month');
        $postData['year'] 		= $this->input->get_post('year');
        $postData['company_id'] = $this->input->get_post('company_id');
        $postData['project_id'] = $this->input->get_post('project');
        $postData['category_id'] = $this->input->get_post('category');

		if ( empty( $postData['company_id'] ) || empty( $postData['project_id'] ) || empty( $postData['month'] ) || empty( $postData['year'] ) ) {
            $this->session->set_flashdata('error', 'No data found.');
			redirect(base_url('admin/report/monthly_attendance_report'));
		}
		if($postData['category_id'] == '')
			$postData['category_id'] = '%';
		$companyInfo = $this->db->where( 'compnay_id', $postData['company_id'] )->get('company')->row_array();
		$projectInfo = $this->db->where( 'project_id', $postData['project_id'] )->get('project')->row_array();
		$noOfDays = date("t", strtotime( $postData['year'] . '-' . $postData['month'] ) );
		$holidays = $this->report->getHolidays( $postData, $noOfDays );
		$holidays = $holidays ? explode( ',', $holidays->holiday_day ) : array();
		$totalColSpan = $noOfDays + 4;


		// weekOffs calculation  

		$getMaxAttendance = $this->report->getMaxAttendance($postData);
		 
		$weekOffs = $this->report->getWeekOff( array( 'company_id' => $postData['company_id'], 'status' => 1 ) );
		 
		
		$weekOffs = $weekOffs ? explode( ',', $weekOffs->days ) : array();

		$numOfWeekOff = 0; 

		$weekOffArray = array();

		if ( !empty( $weekOffs ) ) {
			$fromDate = date( 'Y-m-01 ',strtotime( $postData['year'].'-'.$postData['month'].'-01' ) );
			$toDate = date( 'Y-m-d ',strtotime( $postData['year'].'-'.$postData['month'].'-'.$noOfDays ) );
			for ( $i = 0; $i <= ((strtotime($toDate) - strtotime($fromDate)) / 86400); $i++ ) {
				//echo date('l',strtotime($fromDate) + ($i * 86400)).'<br>';
				if( in_array( date('l',strtotime($fromDate) + ($i * 86400)), $weekOffs ) ) {
					$weekOffArray[$numOfWeekOff] = date('d',strtotime($fromDate) + ($i * 86400));
					$numOfWeekOff++;
				}    
			}
		}
		 
		$remarksColspan = '';
		if (!$totalColSpan % 2 == 0) {
			$totalColSpan = $totalColSpan + 1;
			$remarksColspan = 2;
			// $totalColSpan += $remarksColspan;
		}
		$results = $this->report->getMonthlyAttendance( $postData );
		if ( empty( $results ) ) {
			$this->session->set_flashdata('error', 'No data found.');
			redirect(base_url('admin/report/monthly_attendance_report'));
		}
		 // echo '<pre>'; print_r( $results ); exit;
		$reportData = array();
		$categoryAverage = array();
		foreach( $results as $result ){
			$worker_id =  $result['worker_id'];
			
			if( isset( $reportData[$worker_id] ) ){
				
				$reportData[$worker_id][$result['attendance_day']]['status'] = $result['status'] == 3 ? 'PL' : 'P';
				$reportData[$worker_id][$result['attendance_day']]['hajiri'] = $result['hajiri'];
				$category_id = $result['category_id'];
				$categoryAverage[$category_id]['hajiri'] += $result['hajiri'];
			} else {
				$reportData[$worker_id] = array();
				$reportData[$worker_id]['labour_name'] = $result['labour_name'];
				$reportData[$worker_id]['labour_last_name'] = $result['labour_last_name'];
				$reportData[$worker_id]['category_name'] = $result['category_name'];
				$reportData[$worker_id]['worker_wage_type'] = $result['worker_wage_type'];
				$reportData[$worker_id][ $result['attendance_day'] ]['status'] = $result['status'] == 3 ? 'PL' : 'P';
				$reportData[$worker_id][ $result['attendance_day'] ]['hajiri'] = $result['hajiri'];
				
				$category_id = $result['category_id'];
				if( isset( $categoryAverage[ $category_id ] ) ){
					$categoryAverage[$category_id]['count'] += 1; 
				} else {
					$categoryAverage[$category_id] = array();
					$categoryAverage[$category_id]['name'] = $result['category_name']; 
					$categoryAverage[$category_id]['count'] = 1; 
					$categoryAverage[$category_id]['hajiri'] = 0; 
				}
				$categoryAverage[$category_id]['hajiri'] += $result['hajiri'];
			}
		}
		//echo'<pre>'; print_r( $categoryAverage ); exit;
		$centerColSpan = $totalColSpan - 10; 
		
		ob_start();
        ?>
        <style type="text/css">
			table,thead,tbody,tfoot,tr,th,td,p { font-family:"Calibri"; font-size:14px }
			a.comment-indicator:hover + comment { background:#ffd; position:absolute; display:block; border:1px solid black; padding:0.5em;  } 
			a.comment-indicator { background:red; display:inline-block; border:1px solid black; width:0.5em; height:0.5em;  } 
			comment { display:none;  } 
			.average-attendance td{ padding: 3px; }
		</style>
	
		<table cellspacing="0" border="<?php echo $border; ?>" style="height:100px;width:100%;">
			<tr>
				<td colspan="4" style="border-top: 2px solid #000000; border-bottom: 2px solid #000000; border-left: 2px solid #000000;" > 
					<?php if( !empty( $companyInfo['company_logo_image'] ) ) { ?><img src='<?php echo base_url('uploads/user/').$companyInfo['company_logo_image']; ?>' style="width:100px; margin:10px;"/> <?php } ?>
				</td>
				<?php if($this->input->get_post('downloadformat') == 'pdf'){ ?>
				<td style="border-top: 2px solid #000000;border-bottom: 2px solid #000000;" width="200px" align="center" valign="middle"></td>
				<?php } ?>
				<td colspan="<?php echo $centerColSpan; ?>" style="border-top: 2px solid #000000;border-bottom: 2px solid #000000;"  height="93" align="center" valign="middle"><b><font size="6" color="#000000"><u><?php echo $companyInfo['company_name']; ?></u></b></font> <br><font size="4"><?php echo $projectInfo['project_name']; ?></font></td>
				<td colspan = "<?php echo $header_colspan; ?>" style="border-top: 2px solid #000000; border-right:2px solid #000; border-bottom: 2px solid #000000; " align="right" >
					<img src='<?php echo base_url('assets/admin/images/aasaan-footer-logo.jpg'); ?>' style="width:200px; margin:10px;"/>
				</td>
			</tr>
			<?php
	            $objPHPExcel = new PHPExcel();

				$styleArray = array(
			        'font' => array(
			            'size'  => 10,
	                    'name'  => 'Arial'
			        ),
			        'alignment' => array(
			            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			        ),
			    );
				$styleArray1 = array(
			        'font' => array(
			            'bold' => true,
			            'size'  => 11,
	                    'name'  => 'Arial'
			        ),
			        'alignment' => array(
			            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			        )
			    );

			    $styleArray2 = array(
			        'font' => array(
			            'bold' => true,
			            'size'  => 12,
	                    'name'  => 'Arial'
			        ),
			        'alignment' => array(
			            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			        )
			    );

			    $styleArray3 = array(
			        'font' => array(
			            'size'  => 10,
	                    'name'  => 'Arial'
			        ),
			        'alignment' => array(
			            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
			            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			        )
			    );

			    $styleArray4 = array(
			        'font' => array(
			            'size'  => 10,
	                    'name'  => 'Arial'
			        ),
			        'alignment' => array(
			            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			        )
			    );
			    $borderStyle = array(
				    'borders' => array(
				        'outline' => array(
				            'style' => PHPExcel_Style_Border::BORDER_THICK,
				            'color' => array('argb' => '000000'),
				        ),
				    ),
				);

	            $company_name = $companyInfo['company_name'];
	            $project_name = $projectInfo['project_name'];
	            $company_logo = $companyInfo['company_logo_image'];

	            $header = $noOfDays + 4;
	            $first = 5;
	           	$last = $header - 8;

	           	//First Line
			    $sheet = $objPHPExcel->getActiveSheet();

			    $firstColString = PHPExcel_Cell::stringFromColumnIndex($first);
			    $StartcenterColString = PHPExcel_Cell::stringFromColumnIndex($first+1);
			    $endCenterColstring = PHPExcel_Cell::stringFromColumnIndex($last-1);
			    $lastColString = PHPExcel_Cell::stringFromColumnIndex($last);

			    $lastImageString = PHPExcel_Cell::stringFromColumnIndex($last + 3);
			    $endColString = PHPExcel_Cell::stringFromColumnIndex($header-1);  
			    
			    $imageExist =  ROOT_PATH.'/uploads/user/'.$company_logo;

		      	$sheet->mergeCells('A1:'.$firstColString.'3');

				if(!empty($company_logo) && file_exists($imageExist)){
				   	$objDrawing = new PHPExcel_Worksheet_Drawing();
					$objDrawing->setName('Logo');
					$objDrawing->setDescription('Logo');
					$objDrawing->setPath('./uploads/user/'.$company_logo);
					$objDrawing->setHeight(30);
					$objDrawing->setOffsetX(50);
					$objDrawing->setOffsetY(10);
					$objDrawing->setWorksheet($sheet);
					// $objDrawing->setOffsetX(50);
					// $objDrawing->setOffsetY(2);
					$objDrawing->setCoordinates('A1');
				}

				$sheet->getStyle('A1:'.$firstColString.'3')->applyFromArray($styleArray1);
				 
				$sheet->getStyle('A1:'.$firstColString.'3')->applyFromArray($borderStyle);

				$sheet->mergeCells($StartcenterColString.'1:'.$endCenterColstring.'3');
			    $sheet->setCellValue($StartcenterColString.'1',$company_name."\n".$project_name);
			    $objPHPExcel->getActiveSheet()->getStyle($StartcenterColString.'1')->getAlignment()->setWrapText(true);
			    $sheet->getStyle($StartcenterColString.'1')->applyFromArray($styleArray1);

			    $sheet->getStyle($StartcenterColString.'1:'.$endCenterColstring.'3')->applyFromArray($borderStyle);

			    $sheet->mergeCells($lastColString.'1:'.$endColString.'3');
	            $objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Logo');
				$objDrawing->setDescription('Logo');
				$objDrawing->setPath('./assets/admin/images/aasaan-footer-logo.jpg');
				$objDrawing->setHeight(36);
				$objDrawing->setWorksheet($sheet);
				$objDrawing->setCoordinates($lastImageString.'1');
				$objDrawing->setOffsetX(50);
				$objDrawing->setOffsetY(10);
				$sheet->getStyle($lastColString.'1:'.$endColString.'3')->applyFromArray($borderStyle);
				$sheet->getStyle($lastColString.'1:'.$endColString.'3')->applyFromArray($styleArray1);
			?>
		</table>
		<table cellspacing="0" border="<?php echo $border; ?>" style="width:100%">
			<tr>
				<td style="border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=<?php echo $totalColSpan; ?> height="20" align="center" valign=bottom>
				<b><font size=4 color="#000000">Attendance register &nbsp; <?php echo $this->data['month'][$postData['month']] .', '. $postData['year']; ?></font></b></td>
			</tr>
			<?php

				//Second Line
				$a = $this->data['month'][$postData['month']] .', '. $postData['year'];
			    $sheet->mergeCells('A4:'.$endColString.'4');
			    $sheet->setCellValue('A4','Attendance register '.$a.'');
			    $sheet->getStyle('A4')->applyFromArray($styleArray1);
			    $sheet->getStyle('A4:'.$endColString.'4')->applyFromArray($borderStyle);
            ?>
			
			<tr>
				<td style="width:3%; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" height="45" align="center" valign=middle><font color="#000000">Sr. no.</font></td>

				<?php 
					
			    	$sheet->setCellValue('A5','Sr. no.');
			    	$sheet->getStyle('A5')->applyFromArray($styleArray);
			    	$sheet->getStyle('A5')->getAlignment()->setWrapText(true);
			    	$sheet->getStyle('A5')->getFont()->setBold(true);
			    	$sheet->getStyle('A5')->applyFromArray($borderStyle);
				?>
				<td style="width:15%; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000"> Name of worker<br>(Designation)</font></td>

				<?php 
					
				    $sheet->setCellValue('B5', "Name of worker \n (Designation)");
				    $sheet->getStyle('B5')->applyFromArray($styleArray);
				    $sheet->getStyle('B5')->getAlignment()->setWrapText(true);
				    $sheet->getColumnDimension('B')->setAutoSize(TRUE);
				    $sheet->getStyle('B5')->getFont()->setBold(true);
				    $sheet->getStyle('B5')->applyFromArray($borderStyle);
				?>	
				<?php $index = 2; $excel_row = 5;
					for($i=1;$i<=$noOfDays;$i++){ ?>
					<td style="border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000"><?php echo $i; ?></font></td>
					<?php 
						
						$Bold = array(
				            "font" => array(
				                "bold" => true,
				            ),
				            'alignment' => array(
			            		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			            		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			        		)
				        );

				        $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($index) . ($excel_row))->applyFromArray($borderStyle);

				        $sheet->setCellValueByColumnAndRow($index++, $excel_row, $i);
				        $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($index) . ($excel_row))->applyFromArray($Bold);


				    ?>
				<?php }	?>
				<td style="width:5%; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000">Total Hajiri</font></td>
				<?php 
					$sheet->setCellValueByColumnAndRow($index, $excel_row, 'Total Hajiri');

				 	$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($index) . ($excel_row))->applyFromArray($borderStyle);
				?>
				<td colspan="<?php echo $remarksColspan; ?>" style="width:10%; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle><font color="#000000">Remarks/<br>Signature</font></td>
				<?php 
				 	$index = $index+1;
				 	$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($index) . ($excel_row))->applyFromArray($borderStyle);

					$sheet->setCellValueByColumnAndRow($index, $excel_row, 'Remarks/Signature');

					$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($index) . ($excel_row))->applyFromArray($Bold);
				?>
			</tr>
			<?php  
				$excel_row = 6;
				$index = 1;
				$tableData = '';
				   // echo "<pre>"; print_r($reportData); die();
				foreach( $reportData as $key => $worker ){
					$totalHajiri = 0;
					$tableData .= '<tr>';
					$tableData .= '<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" height="10" align="center" valign=middle ><font color="#000000">'.$index.'</font></td>';
					$tableData .= '<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; padding: 3px;" align="left" valign=middle><font color="#000000">'.$worker["labour_name"].' '.$worker["labour_last_name"].'<br> '.$worker["category_name"].'</font></td>';

					$sheet->getStyle('A'.$excel_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					// broder
					$sheet->getStyle('A'.$excel_row.'')->applyFromArray($borderStyle);
					
					$sheet->setCellValue('A'.$excel_row,$index);
					$sheet->mergeCells('A'.$excel_row.':A'.($excel_row+1));

					// broder 
					$sheet->getStyle('A'.($excel_row+1).'')->applyFromArray($borderStyle);

					$sheet->getStyle('B'.$excel_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					// broder 
					$sheet->getStyle('B'.$excel_row.'')->applyFromArray($borderStyle);

					$sheet->setCellValue('B'.$excel_row,$worker["labour_name"].' '.$worker["labour_last_name"]."\n".$worker["category_name"]);

				    $sheet->getRowDimension($excel_row)->setRowHeight(20); 
				    $sheet->getRowDimension($excel_row + 1)->setRowHeight(20); 

				    // broder
				    $sheet->getStyle('B'.($excel_row+1).'')->applyFromArray($borderStyle);

					$sheet->getStyle('B'.$excel_row)->getAlignment()->setWrapText(true);
					$sheet->mergeCells('B'.$excel_row.':B'.($excel_row+1));

					$column = 2;
					$count_holidays = 0;
					$count_weekOffs = 0;
					
					for($i=1;$i<=$noOfDays;$i++){
						$status = 'A';
						$hajiri = 0;
						$bgcolor = ' bgcolor="#666666" ';
						$color = ' color="#FFFFFF" ';
						$excelColor = '666666';
						if( isset( $worker[$i] ) ){
							$status = $worker[$i]['status'];
							$bgcolor= "";
							$color = ' color="#000000" ';
							$excelColor = 'FFFFFF';
							 
							// if(fmod($worker[$i]['hajiri'], 1) !== 0.00) $hajiri =  number_format($worker[$i]['hajiri']); else $hajiri =  $worker[$i]['hajiri'];
							if(fmod($worker[$i]['hajiri'], 1) !== 0.00) {
                                 $hajiri =  number_format((float)$worker[$i]['hajiri'] , 1, '.', '');
                            } else{
                                 $hajiri =  $worker[$i]['hajiri'];
                            }
							 
							$totalHajiri += $worker[$i]['hajiri'];
						} elseif($worker['worker_wage_type'] == 1 && in_array($i , $weekOffArray))
						{ 
							$k = array_search( $key , array_column($getMaxAttendance, 'worker_id'));
							$getMaxAttendance[$k]['project_id'];
							
							if(isset($k) && $postData['project_id'] == $getMaxAttendance[$k]['project_id']){
								
								 	$status = 'W';
									$hajiri = '1';
									$bgcolor= "";
									$color = ' color="#000000" ';
									$excelColor = 'FFFFFF';
									$count_weekOffs ++;
							}
						} elseif(in_array( $i, $holidays ) ){
							$status = 'H';
							$hajiri = '-';
							$bgcolor= "";
							$color = ' color="#000000" ';
							$excelColor = 'FFFFFF';
						}

						if(fmod($hajiri, 1) !== 0.00){
                            $fontSize = '12px !important;';
                        }else{
                            $fontSize = '14px !important;';
                        }
                        
						$tableData .= '<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle '.$bgcolor.' ><font '.$color.' >'.$status.'</font><hr><font '.$color.' style="font-size: '.$fontSize.'"  >'.$hajiri.'</font></td>';

							//apply css background color 
							$excelBackgroundArr = array(
						        'fill' => array(
						            'type' => PHPExcel_Style_Fill::FILL_SOLID,
						            'color' => array('rgb' => $excelColor)
						        ),
						        'borders' => array(
								    'outline' => array(
								      // 'style' => PHPExcel_Style_Border::BORDER_THIN,
								    	'style' => PHPExcel_Style_Border::BORDER_THICK,
								      'color' => array('argb' => '000000')
								    )
								  )
						    );

						$sheet->getDefaultStyle()->applyFromArray($styleArray);
				        
						$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($column) . $excel_row)->applyFromArray($excelBackgroundArr);

						$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($column))->setWidth(4);

						$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($column) . ($excel_row+1))->applyFromArray($excelBackgroundArr);

						$sheet->setCellValueByColumnAndRow($column, $excel_row, $status);

						$sheet->setCellValueByColumnAndRow($column, ($excel_row+1), $hajiri);
						$column ++;
					}
					
					if($worker["worker_wage_type"] == '1'){
						
						$k = array_search( $key , array_column($getMaxAttendance, 'worker_id'));
						
						$getMaxAttendance[$k]['project_id'];
						
						if(isset($k) && $postData['project_id'] == $getMaxAttendance[$k]['project_id']){
							
							if(isset($holidays[0]) && !empty($holidays[0])){
								$count_holidays = count($holidays);
							} 
							$totalHajiri = $count_weekOffs + $count_holidays  + $totalHajiri;
						}
					}

					if(fmod($totalHajiri, 1) !== 0.00) $show_total_hajiri = number_format($totalHajiri, 2); else $show_total_hajiri = $totalHajiri;
					 

					$tableData .= '
						<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle sdval="31.5" sdnum="1033;"><font color="#000000">'.$show_total_hajiri.'</font></td>
						<td colspan="'.$remarksColspan.'" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle><font color="#000000"><br></font></td>';
					$tableData .= '</tr>';

					if($noOfDays == 28){
							
						$sheet->setCellValue('AE'.$excel_row,$show_total_hajiri);
						$sheet->mergeCells('AE'.$excel_row.':AE'.($excel_row+1));

						$sheet->getStyle('AE'.$excel_row.'')->applyFromArray($borderStyle);

						$sheet->getColumnDimension('AE')->setWidth(15);
						$sheet->mergeCells('AF'.$excel_row.':AF'.($excel_row+1));
		    			$sheet->setCellValue('AF'.$excel_row,'');
		    			$sheet->getColumnDimension('AF')->setWidth(20);

		    			$sheet->getStyle('AF'.($excel_row+1).'')->applyFromArray($borderStyle);

					}
					if($noOfDays == 29){
							
						$sheet->setCellValue('AF'.$excel_row,number_format($totalHajiri , 2));

						$sheet->getStyle('AF'.$excel_row.'')->applyFromArray($borderStyle);

						$sheet->mergeCells('AF'.$excel_row.':AF'.($excel_row+1));
						$sheet->getColumnDimension('AF')->setWidth(15);

						$sheet->mergeCells('AG'.$excel_row.':AG'.($excel_row+1));
		    			$sheet->setCellValue('AG'.$excel_row,'');
		    			$sheet->getColumnDimension('AG')->setWidth(20);

		    			$sheet->getStyle('AG'.($excel_row+1).'')->applyFromArray($borderStyle);
					}
					if($noOfDays == 30){
							
						$sheet->setCellValue('AG'.$excel_row,number_format($totalHajiri , 2));
						$sheet->getStyle('AG'.$excel_row.':AG'.($excel_row+1))->applyFromArray($borderStyle);
						$sheet->mergeCells('AG'.$excel_row.':AG'.($excel_row+1));
						$sheet->getColumnDimension('AG')->setWidth(15);

						$sheet->mergeCells('AH'.$excel_row.':AH'.($excel_row+1));
		    			$sheet->setCellValue('AH'.$excel_row,'');
		    			$sheet->getColumnDimension('AH')->setWidth(20);
		    			$sheet->getStyle('AH'.$excel_row.':AH'.($excel_row+1))->applyFromArray($borderStyle);
					}
					if($noOfDays == 31){
							
						$sheet->setCellValue('AH'.$excel_row,number_format($totalHajiri , 2));

						$sheet->getStyle('AH'.$excel_row.'')->applyFromArray($borderStyle);

						$sheet->getStyle('AH'.($excel_row+1).'')->applyFromArray($borderStyle);

						$sheet->mergeCells('AH'.$excel_row.':AH'.($excel_row+1));
						$sheet->getColumnDimension('AH')->setWidth(15);

						$sheet->mergeCells('AI'.$excel_row.':AI'.($excel_row+1));
		    			$sheet->setCellValue('AI'.$excel_row,'');
		    			$sheet->getColumnDimension('AI')->setWidth(20);

		    			$sheet->getStyle('AI'.($excel_row).'')->applyFromArray($borderStyle);
		    			$sheet->getStyle('AI'.($excel_row+1).'')->applyFromArray($borderStyle);
					}

					$excel_row=$excel_row+2;
					$index++;
				}
				echo $tableData;
			?>
		</table>
		<br />
		<table class="average-attendance" cellspacing="0" border="<?php echo $border; ?>" style="width:100%">
			<tr>
				<td style="border-top: 2px solid #000000; border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=13 height="25" align="left" valign=bottom><b><u><font size=3 color="#000000">Average monthly attendance</font></u></b></td>
			</tr>
			<?php
			$sheet->mergeCells('A'.$excel_row.':Z'.$excel_row.'');
		    $sheet->setCellValue('A'.$excel_row,'');
		    $excel_row++;
		    $sheet->mergeCells('A'.$excel_row.':Z'.$excel_row.'');
		    $sheet->setCellValue('A'.$excel_row,'');
			// $sheet->mergeCells('A'.$excel_row.':Z'.$excel_row.'');
		 	//$sheet->setCellValue('A'.$excel_row,'Average monthly attendance');
		 	//$sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray2);
		    $excel_row++;
			 ?>
			<tr>
				<td style="width:10%; border-bottom: 2px solid #000000; border-left: 2px solid #000000" colspan=2 height="25" align="left" valign=bottom><font color="#000000">Category</font></td>
				<td style="width:8%; border-bottom: 2px solid #000000; border-right: 2px solid #000000" align="left" valign=bottom><font color="#000000">Attendance</font></td>
				<td style="width:10%; border-bottom: 2px solid #000000" colspan=2 align="left" valign=bottom><font color="#000000">Category</font></td>
				<td style="width:8%; border-bottom: 2px solid #000000; border-right: 2px solid #000000" align="left" valign=bottom><font color="#000000">Attendance</font></td>
				<td style="width:10%; border-bottom: 2px solid #000000" colspan=2 align="left" valign=bottom><font color="#000000">Category</font></td>
				<td style="width:8%; border-bottom: 2px solid #000000; border-right: 2px solid #000000"align="left" valign=bottom><font color="#000000">Attendance</font></td>
				<td style="width:10%; border-bottom: 2px solid #000000;" colspan=2 align="left" valign=bottom ><font color="#000000">Category</font></td>
				<td style="width:8%; border-bottom: 2px solid #000000; border-right: 2px solid #000000" align="left" valign=bottom><font color="#000000">Attendance</font></td>
				<td style="width:10%; border-bottom: 2px solid #000000; border-right: 2px solid #000000" align="left" valign=bottom><font color="#000000"><br></font></td>
			</tr>
			<?php
				$sheet->getStyle('A'.$excel_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sheet->getStyle('A'.$excel_row)->getFont()->setBold(true);


				$sheet->getStyle('A'.($excel_row).':B'.$excel_row)->applyFromArray($borderStyle);
				$sheet->getStyle('A'.($excel_row+1).':B'.($excel_row+1))->applyFromArray($borderStyle);				

				$sheet->mergeCells('A'.$excel_row.':B'.($excel_row+1));
				$sheet->setCellValue('A'.$excel_row,'Average monthly attendance');

		    	$sheet->mergeCells('C'.$excel_row.':E'.$excel_row);
			    $sheet->setCellValue('C'.$excel_row.'','Category');
			    $sheet->getStyle('C'.$excel_row)->getFont()->setBold(true);

			    $sheet->getStyle('C'.($excel_row).':E'.$excel_row)->applyFromArray($borderStyle);

			    $sheet->mergeCells('F'.$excel_row.':H'.$excel_row);
			    $sheet->setCellValue('F'.$excel_row.'','Attendance');
			    $sheet->getStyle('F'.$excel_row)->getFont()->setBold(true);

			    $sheet->getStyle('F'.($excel_row).':H'.$excel_row)->applyFromArray($borderStyle);
				
				$sheet->mergeCells('I'.$excel_row.':K'.$excel_row);
				$sheet->setCellValue('I'.$excel_row.'','Category');
	            $sheet->getStyle('I'.$excel_row)->getFont()->setBold(true);

	            $sheet->getStyle('I'.($excel_row).':K'.$excel_row)->applyFromArray($borderStyle);

	            $sheet->mergeCells('L'.$excel_row.':N'.$excel_row);
			    $sheet->setCellValue('L'.$excel_row.'','Attendance');
			    $sheet->getStyle('L'.$excel_row)->getFont()->setBold(true);

			    $sheet->getStyle('L'.($excel_row).':N'.$excel_row)->applyFromArray($borderStyle);

			    $sheet->mergeCells('O'.$excel_row.':Q'.$excel_row);
			    $sheet->setCellValue('O'.$excel_row.'','Category');
			    $sheet->getStyle('O'.$excel_row)->getFont()->setBold(true);

			    $sheet->getStyle('O'.($excel_row).':Q'.$excel_row)->applyFromArray($borderStyle);

			    $sheet->mergeCells('R'.$excel_row.':T'.$excel_row);
			    $sheet->setCellValue('R'.$excel_row.'','Attendance');
			    $sheet->getStyle('R'.$excel_row)->getFont()->setBold(true);

			    $sheet->getStyle('R'.($excel_row).':T'.$excel_row)->applyFromArray($borderStyle);

			    $sheet->mergeCells('U'.$excel_row.':W'.$excel_row);
			    $sheet->setCellValue('U'.$excel_row.'','Category');
			    $sheet->getStyle('U'.$excel_row)->getFont()->setBold(true);

		    	$sheet->getStyle('U'.($excel_row).':W'.$excel_row)->applyFromArray($borderStyle);

			    $sheet->mergeCells('X'.$excel_row.':Z'.$excel_row);
			    $sheet->setCellValue('X'.$excel_row.'','Attendance');
			    $sheet->getStyle('X'.$excel_row)->getFont()->setBold(true);

			    $sheet->getStyle('X'.($excel_row).':Z'.$excel_row)->applyFromArray($borderStyle);
			    $excel_row++;
		    ?>
			<?php 
				if( $categoryAverage ) {

					$category_array = array_chunk($categoryAverage, 4, true);
					foreach( $category_array as $categories ) {
						$arrayCount = count( $categories );
						if( $arrayCount < 4 ){
							$arrayCount = 4 - $arrayCount;
							$categories += array_fill( -4, $arrayCount, array());
						}
						//echo '<pre>'; print_r($categories); //exit;
			?>
				<tr>
					<?php 
						$totalAverage = 0;
						$i = 0;
						foreach( $categories as $category_id => $category ) { 
							$categoryName = !empty( $category['name'] ) ? $category['name'] : '';
							$average = isset( $category['count'] ) ? $category['hajiri'] / $category['count'] : '';
							$totalAverage += $average;
					?>
						<td style="border-bottom: 2px solid #000000; border-left: 2px solid #000000" colspan=2 height="25" align="left" valign=bottom><font color="#000000"><?php echo $categoryName; ?></font></td>

						<td style="border-bottom: 2px solid #000000; border-right: 2px solid #000000" align="left" valign=bottom><b><font color="#000000"><?php
						if(!empty($average)) echo number_format($average , 2); ?></font></b></td>
					<?php
						if(!empty($average)) $excelAvarage =  number_format($average , 2);
						else $excelAvarage = '';
						switch ($i) 
						{
						    case 0:
							    
							    $sheet->mergeCells('C'.$excel_row.':E'.$excel_row);
				    			$sheet->setCellValue('C'.$excel_row.'',$categoryName);

				    			$sheet->getStyle('C'.($excel_row).':E'.$excel_row)->applyFromArray($borderStyle);

				    			$sheet->mergeCells('F'.$excel_row.':H'.$excel_row);
						        $sheet->setCellValue('F'.$excel_row.'',$excelAvarage);
						        $sheet->getStyle('F'.($excel_row).':H'.$excel_row)->applyFromArray($borderStyle);
								break;
						    case 1:

						    	$sheet->mergeCells('I'.$excel_row.':K'.$excel_row);
								$sheet->setCellValue('I'.$excel_row.'',$categoryName);
								$sheet->getStyle('I'.($excel_row).':K'.$excel_row)->applyFromArray($borderStyle);

						        $sheet->mergeCells('L'.$excel_row.':N'.$excel_row.'');
				                $sheet->setCellValue('L'.$excel_row.'',$excelAvarage);
				                $sheet->getStyle('L'.($excel_row).':N'.$excel_row)->applyFromArray($borderStyle);
				                break;
						    case 2:
						    	$sheet->mergeCells('O'.$excel_row.':Q'.$excel_row);
						        $sheet->setCellValue('O'.$excel_row.'',$categoryName);

						        $sheet->getStyle('O'.($excel_row).':Q'.$excel_row)->applyFromArray($borderStyle);

						        $sheet->mergeCells('R'.$excel_row.':T'.$excel_row.'');
				                $sheet->setCellValue('R'.$excel_row.'',$excelAvarage);
				                $sheet->getStyle('R'.($excel_row).':T'.$excel_row)->applyFromArray($borderStyle);
				                 
						        break;
				            case 3:
			            		$sheet->mergeCells('U'.$excel_row.':W'.$excel_row);
						        $sheet->setCellValue('U'.$excel_row.'',$categoryName);
						        $sheet->getStyle('U'.($excel_row).':W'.$excel_row)->applyFromArray($borderStyle);
						        $sheet->mergeCells('X'.$excel_row.':Z'.$excel_row.'');
				                $sheet->setCellValue('X'.$excel_row.'',$excelAvarage);
				                $sheet->getStyle('X'.($excel_row).':Z'.$excel_row)->applyFromArray($borderStyle);
				                 
				                break;
						    default:
						} 
						$i++;
					} $excel_row++;  ?>
					<td style="border-bottom: 2px solid #000000; border-right: 2px solid #000000" align="left" valign=bottom><b><font color="#000000"><br></font></b></td>
				</tr>
			<?php 	}
				}
			?>
			<tr>
				<td style="border-left: 2px solid #000000" colspan=11 height="30" align="right" valign=bottom><font size=3 color="#000000">Average worker strength: </font></td>
				<td style="border-right: 2px solid #000000; font-size:18px" colspan=2 align="left" valign=bottom><b><font color="#000000"><?php echo number_format($totalAverage , 2); ?></font></b>        </td>
			</tr>

			<?php 
				$sheet->mergeCells('A'.$excel_row.':Z'.$excel_row.'');
			    $sheet->setCellValue('A'.$excel_row.'','');
			    $sheet->getStyle('A'.($excel_row).':Z'.$excel_row)->applyFromArray($borderStyle);
			    $excel_row++; 

			    $sheet->mergeCells('A'.$excel_row.':Q'.$excel_row.'');
			    $sheet->setCellValue('A'.$excel_row.'','Average worker strength: ');
			    $sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray3);
			    $sheet->getStyle('A'.($excel_row).':Q'.$excel_row)->applyFromArray($borderStyle);

			    $sheet->mergeCells('R'.$excel_row.':Z'.$excel_row.'');
			    $sheet->setCellValue('R'.$excel_row.'','Rs.'.number_format($totalAverage , 2));

			     $sheet->getStyle('R'.($excel_row).':Z'.$excel_row)->applyFromArray($borderStyle);

			    $sheet->getStyle('R'.$excel_row.'')->applyFromArray($styleArray1);
			    $sheet->getStyle('R'.$excel_row.'')->getFont()->setBold(true);

			    $excel_row++;
			?>
			<tr>
				<td style="border-left: 2px solid #000000" colspan=11 height="30" align="right" valign=bottom><font size=3 color="#000000">Holidays: </font></td>
				<td style="border-right: 2px solid #000000; font-size:18px" colspan=2 align="left" valign=bottom sdval="3" sdnum="1033;0;#,##0"><b><font color="#000000"><?php echo count($holidays); ?></font></b></td>
			</tr>
			<?php 
				$sheet->mergeCells('A'.$excel_row.':Q'.$excel_row.'');
			    $sheet->setCellValue('A'.$excel_row.'','Holidays: ');
			    $sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray3);
			    $sheet->getStyle('A'.($excel_row).':Q'.$excel_row)->applyFromArray($borderStyle);

			    $sheet->mergeCells('R'.$excel_row.':Z'.$excel_row.'');
			    $sheet->setCellValue('R'.$excel_row.'', count($holidays));
			    $sheet->getStyle('R'.$excel_row.'')->applyFromArray($styleArray1);
			    $sheet->getStyle('R'.$excel_row.'')->getFont()->setBold(true);
			    $sheet->getStyle('R'.($excel_row).':Z'.$excel_row)->applyFromArray($borderStyle);
			    $excel_row++;
		    ?>
			
			<tr>
				<td style="border-left: 2px solid #000000; border-bottom: 1px solid #000000 " colspan=11 height="25" align="right" valign=bottom><font size=3 color="#000000"></font></td>
				<td style="border-right: 2px solid #000000; font-size:18px; border-bottom: 1px solid #000000 " colspan=2 align="center" valign=bottom sdval="3" sdnum="1033;0;#,##0">
					<b>
						<font color="#000000"><br><br>
							<?php echo $this->session->userdata('name'); ?><br>
							<span style="font-size:12px">(self-attested)</span><br>
							<span style="font-size:15px"><?php echo $companyInfo['company_name']; ?></span>
						</font>
					</b>
				</td>			
			</tr>
			<?php
				$name = $this->session->userdata('name');
				$update_row = $excel_row + 4 ;
				$sheet->mergeCells('A'.$excel_row.':Q'.$update_row.'');
			    $sheet->setCellValue('A'.$excel_row.'','');
			    $sheet->mergeCells('R'.$excel_row.':Z'.$update_row.'');
			    $sheet->setCellValue('R'.$excel_row,$name."\n".'(self-attested)'."\n".$companyInfo['company_name']);
			    $sheet->getStyle('R'.$excel_row)->getAlignment()->setWrapText(true);
			    $sheet->getStyle('A'.($excel_row).':Q'.$update_row)->applyFromArray($borderStyle);
			    $sheet->getStyle('R'.($excel_row).':Z'.$update_row)->applyFromArray($borderStyle);

			    $sheet->getStyle('R'.$excel_row)->getFont()->setBold(true);
	            $sheet->getStyle('R'.$excel_row.':Z'.$excel_row.'')->getFont()->setSize(10);
	            $sheet->getStyle('R'.$excel_row.':Z'.$excel_row.'')->applyFromArray($styleArray1);

	            $update_row++;
		    ?>
			<tr>
				<td style="border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=13 align="left" valign=bottom sdnum="1033;16393;[$-4009]DD-MM-YYYY">
					<b><u><font size=3 color="#000000">Disclaimer:</font></u></b>
				</td>
			</tr>
			<?php
				$sheet->mergeCells('A'.$update_row.':Z'.$update_row.'');
			    $sheet->setCellValue('A'.$update_row,'');
			    $sheet->getStyle('A'.($update_row).':Z'.$update_row)->applyFromArray($borderStyle);
			    $update_row++;
				$sheet->mergeCells('A'.$update_row.':Z'.$update_row.'');
			    $sheet->setCellValue('A'.$update_row,'Disclaimer');
			    $sheet->getStyle('A'.$update_row)->applyFromArray($styleArray2);
			    $sheet->getStyle('A'.$update_row)->getFont()->setUnderline(true);
			    $sheet->getStyle('A'.($update_row).':Z'.$update_row)->applyFromArray($borderStyle);

			    $update_row++;
		    ?>
			<tr>
				<td style="border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=13 align="left" valign=bottom>
					<font size=1 color="#000000">1. This is an auto-generated report.</font>
				</td>
			</tr>
			<?php
				$sheet->mergeCells('A'.$update_row.':Z'.$update_row.'');
			    $sheet->setCellValue('A'.$update_row.'','1. This is an auto-generated report.');
			    $sheet->getStyle('A'.$update_row.'')->applyFromArray($styleArray4);
			    $sheet->getStyle('A'.$update_row.'')->getFont()->setSize(8);
			    $sheet->getStyle('A'.($update_row).':Z'.$update_row)->applyFromArray($borderStyle);

			    $update_row++;
		    ?>

			<tr>
				<td style="border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=13 align="left" valign=bottom>
					<font size=1 color="#000000">2. Aasaan does not hold any legal liability for any data generated through this report</font>
				</td>
			</tr>
			<?php
				$sheet->mergeCells('A'.$update_row.':Z'.$update_row.'');
			    $sheet->setCellValue('A'.$update_row.'','2. Aasaan does not hold any legal liability for any data generated through this report');
			    $sheet->getStyle('A'.$update_row.'')->applyFromArray($styleArray4);
			    $sheet->getStyle('A'.$update_row.'')->getFont()->setSize(8);
			    $sheet->getStyle('A'.($update_row).':Z'.$update_row)->applyFromArray($borderStyle);
			    $update_row++;
		    ?>

			<tr>
				<td style="border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=13 align="left" valign=bottom>
					<font size=1 color="#000000">3. The values are purely based on the app operations</font>
				</td>
			</tr>
			<?php
				$sheet->mergeCells('A'.$update_row.':Z'.$update_row.'');
			    $sheet->setCellValue('A'.$update_row.'','3. The values are purely based on the app operations');
			    $sheet->getStyle('A'.$update_row.'')->applyFromArray($styleArray4);
			    $sheet->getStyle('A'.$update_row.'')->getFont()->setSize(8);
			    $sheet->getStyle('A'.($update_row).':Z'.$update_row)->applyFromArray($borderStyle);
			    $update_row++;
		    ?>
			<tr>
				<td style="border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=13 align="right" valign=bottom>
					<font size=1 color="#000000">This report was generated at <?php echo date('H:i'); ?> hours on <?php echo date('d/m/Y'); ?> </font>
				</td>
			</tr>
			<?php
				$c_time = date('H:i');
				$c_date = date('d/m/Y');
				$sheet->mergeCells('A'.$update_row.':Z'.$update_row.'');
			    $sheet->setCellValue('A'.$update_row.'','This report was generated at '.$c_time.' hours on '.$c_date.'');
			    $sheet->getStyle('A'.$update_row.'')->getFont()->setSize(8);
			    $sheet->getStyle('A'.$update_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

			    $sheet->getStyle('A'.($update_row).':Z'.$update_row)->applyFromArray($borderStyle);
			    $update_row++;
			    $sheet->setSelectedCells($StartcenterColString.'1:'.$endCenterColstring.'3');
			?>
		</table>
		<?php 
        $contents = ob_get_contents();
        ob_end_clean();
	    $month = date("F", mktime(0, 0, 0, $postData['month'] , 10));

        if($this->input->get_post('downloadformat') == 'pdf'){
	        $this->load->library('Dom_pdf');
	        $this->dompdf->load_html($contents);
	        $this->dompdf->render();
	        $this->dompdf->stream($month." attendance report ".$projectInfo['project_name'], array("Attachment" => True));
	    }else{
			ob_end_clean();
		    $filename= $month." attendance report ".$projectInfo['project_name'].'.xls';
	        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			// header('Content-Disposition: attachment;filename="01simple.xlsx"');
			header('Content-Disposition: attachment;filename='.$filename);
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');

			// If you're serving to IE over SSL, then the following may be needed
			header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
			header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header ('Pragma: public'); // HTTP/1.0
	  
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');
			exit;
			
			// header('Content-Type: application/vnd.ms-excel');
			// // header('Content-Disposition: attachment;filename=$file');
			// header('Content-Disposition: attachment;filename='.$filename);
			// header('Cache-Control: max-age=0');
			// // If you're serving to IE 9, then the following may be needed
			// header('Cache-Control: max-age=1');

			// // If you're serving to IE over SSL, then the following may be needed
			// header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			// header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
			// header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			// header ('Pragma: public'); // HTTP/1.0

			// echo $contents; die();
	    }
	}
	
	function monthly_labour_report() {
        $data = $this->data;
		$data['menu_title'] = 'monthlyLabour';
         //For Getting project
           //For Getting Labours
           $labours = array("" => "Select worker");
           $labour_results= $this->report->get_all_labour('worker');
           foreach ($labour_results as $key => $value) {
                $labours[$value->worker_id] = $value->labour_name. ' '.$value->labour_last_name;
            }
         $data['labours'] = $labours;
         //For getting Year
        $years = array("" => "Select year");
        foreach (range(2016, 2050) as $value) {
            $years[$value] = $value;
        }

         $data['years'] = $years;
        //For getting Month
        $months = array("" => "Select month", "1" => "January", "2" => "February", "3" => "March", "4" => "April",
            "5" => "May", "6" => "June", "7" => "July", "8" => "August",
            "9" => "September", "10" => "October", "11" => "November", "12" => "December",
        );
        $data['months'] = $months;

        
        $data['title'] = 'Monthly worker detail report';
        $data['description'] = '';
        $data['page'] = 'report/monthly_labour_detail_report';
        if (isset($_REQUEST['submit'])) {
            $this->form_validation->set_data($this->input->get());
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('labour', 'labour', 'trim|required|numeric');
            $this->form_validation->set_rules('month', 'Month', 'trim|required|numeric');
            $this->form_validation->set_rules('year', 'Year', 'trim|required|numeric');
            if ($this->form_validation->run() == TRUE) {

                $this->generate_monthly_labour_report();
            }
        }
        $this->load->view('includes/template', $data);
    }

    function generate_monthly_labour_report() {

		if($this->input->get_post('downloadformat') == 'pdf'){
			$table_class = 'pdf';
			$border = '0';
		}else{
			$table_class = 'excel';
			$border = '1';
		}
		$postData = array();
        $postData['labour_id'] = $this->input->get_post('labour');
        $postData['month'] 		= $this->input->get_post('month');
        $postData['year'] 		= $this->input->get_post('year');
        $postData['company_id'] = $this->input->get_post('company_id');

		if ( empty( $postData['company_id'] ) || empty( $postData['labour_id'] ) || empty( $postData['month'] ) || empty( $postData['year'] ) ) {
            $this->session->set_flashdata('error', 'No data found.');
			redirect(base_url('admin/report/monthly_labour_report'));
		}

		$results = $this->report->getMonthlyLabourData( $postData );
		if ( empty( $results ) ) {
			$this->session->set_flashdata('error', 'No data found.');
			redirect(base_url('admin/report/monthly_labour_report'));
		}
		
		$companyInfo = $this->db->where( 'compnay_id', $postData['company_id'] )->get('company')->row_array();
		//$projectInfo = $this->db->where( 'project_id', $postData['project_id'] )->get('project')->row_array();
		$noOfDays = date("t", strtotime( $postData['year'] . '-' . $postData['month'] ) );
		
		$weekOffs = $this->report->getWeekOff( array( 'company_id' => $postData['company_id'], 'status' => 1 ) );
		
		$weekOffs = $weekOffs ? explode( ',', $weekOffs->days ) : array();

		$numOfWeekOff = 0; 
		$weekOffArray = array();
		if ( !empty( $weekOffs ) ) {
			$fromDate = date( 'Y-m-01 ',strtotime( $postData['year'].'-'.$postData['month'].'-01' ) );
			$toDate = date( 'Y-m-d ',strtotime( $postData['year'].'-'.$postData['month'].'-'.$noOfDays ) );
			for ( $i = 0; $i <= ((strtotime($toDate) - strtotime($fromDate)) / 86400); $i++ ) {
				//echo date('l',strtotime($fromDate) + ($i * 86400)).'<br>';
				if( in_array( date('l',strtotime($fromDate) + ($i * 86400)), $weekOffs ) ) {
					$weekOffArray[$numOfWeekOff] = date('d',strtotime($fromDate) + ($i * 86400));
					$numOfWeekOff++;
				}    
			}
		}

		$postData['week_offs'] = $weekOffs;
		$opening_balance = $this->report->getOpeningBalanceByWorker( $postData );

		$openingBalance = round($opening_balance);

		$holidays = $this->report->getHolidays( $postData, $noOfDays );
		$is_holiday = false;
		if(isset($holidays->holiday_day) && !empty($holidays->holiday_day) && isset($holidays->holiday_name) && !empty($holidays->holiday_name)){
			$is_holiday = true;
		}

		$company_holiday = $holidays ? explode( ',', $holidays->holiday_name ) : array();
		$holidays = $holidays ? explode( ',', $holidays->holiday_day ) : array();
		$holidayarray = array();
		$index = 0;
		
		if($is_holiday){
			foreach($holidays as $item){
				$holidayarray[$index]['holiday_name'] = $company_holiday[$index];
				$holidayarray[$index]['holiday_date'] = $item;
				$index++;
			}
		}
		
		$reportData = array();
		$attendanceData = array();
		
		$firstOfMonth = date( "Y-m-01", strtotime( $postData['year'].'-'.$postData['month'].'-01' ) );

		for($i=1; $i<=$noOfDays;$i++){
			$date = $postData['year'].'-'.$postData['month'].'-'.$i;
			$day = date("w", strtotime( $date ) );
			$weekNumber =  intval( date("W", strtotime($date)) ) - intval( date("W", strtotime($firstOfMonth)) );
			$attendanceData[$weekNumber][$day] = array('day'=>$i);
		}

		$getMonthlyPaymentBylabour = $this->report->getMonthlyPaymentBylabour($postData );
		
		$projectInfoArray = array();
		$paidLeaves = array();
		$projects = array();
		$totalHajiri = 0;
		$totalEarnings = 0;
		$projectNameData = array();
		 
		foreach( $results as $result ){
			$attendance_day =  $result['attendance_day'];
			
			$reportData['worker_id'] =  $result['worker_id'];
			$reportData['labour_name'] =  $result['labour_name'];
			$reportData['labour_last_name'] =  $result['labour_last_name'];
			$reportData['category_id'] =  $result['category_id'];
			$reportData['category_name'] =  $result['category_name'];
			$reportData['worker_wage'] =  $result['worker_wage'];
			$reportData['worker_wage_type'] =  $result['worker_wage_type'];
			
			$attendanceArray = array();
			$attendanceArray['day'] = $attendance_day;
			$attendanceArray['hajiri'] = $result['hajiri'];
			$attendanceArray['project_id'] = $result['project_id'];
			$attendanceArray['project_name'] = $result['project_name'];
			// $attendanceArray['amount'] = $result['amount'];
			$attendanceArray['status'] = $result['status'];
			$attendanceArray['project_alias'] = '';

		 	$key = array_search( $attendanceArray['day'] , array_column($getMonthlyPaymentBylabour, 'payment_day'));
			$attendanceArray['amount'] = '';
			if($key !== false) {
				$attendanceArray['amount'] = $getMonthlyPaymentBylabour[$key]['payment_amount'];
			}


			// $project_name = explode(' ', $result['project_name'] );
			// $alias = '';
			// if(count($project_name) == 1){
			// 	// take project name first letter and last letter
			// 	$firstChr = ucfirst(substr($project_name[0], 0 ,1));
			// 	$lastChr = ucfirst(substr($project_name[0], -1));
			//  	$alias = $firstChr.$lastChr;
			// }else{ 
			// 	// take first letter of Zero and first letter of One
			// 	$arr = array();
			// 	for($i = 0; $i < count($project_name); $i++){
			// 		$char = ucfirst(substr($project_name[$i], 0 , 1)); 
			// 		array_push($arr, $char);
			// 	}
			// 	 $alias = implode("", $arr);
			
			// }
			// $index = 0;
			// while (array_search($alias, array_column($projectNameData, 'alias'))) {
			// 	// if alias already exists
			// 	$index++;
			// 	$alias = $alias.$index;
			// }			
			// $projectNameData[$result['project_id']] = array('project_name' => $result['project_name'], 'alias' => $alias);
			
			$date = $postData['year'].'-'.$postData['month'].'-'.$attendance_day;
			$day = date("w", strtotime( $date ) );
			$weekNumber =  intval( date("W", strtotime($date)) ) - intval( date("W", strtotime($firstOfMonth)) );
			
			if( $result['status'] == 3 ) {
				array_push( $paidLeaves, $attendance_day );
			} else {
				
				$project_id = $result['project_id'];
				if( isset( $projectInfoArray[$project_id] ) ) {
					if($result['hajiri'] > 0){
						$projectInfoArray[$project_id]['present_days'] += $result['hajiri'];
					}
				} else { 
					if($result['hajiri'] > 0){
						$projectInfoArray[$project_id]['present_days'] = $result['hajiri'];
					}else{
						$projectInfoArray[$project_id]['present_days'] = 0;
					}
					
					$projectInfoArray[$project_id]['project_name'] = $result['project_name'];

					$acronym='';
					$projectName = preg_split("/(\s|\-|\.)/", $result['project_name']);
					foreach($projectName as $w) {
						$acronym .= substr($w,0,1);
					}

					if( in_array( $acronym, $projects ) ){
						$projectInfoArray[$project_id]['acronym'] = $acronym.rand(1, 99);
						$attendanceArray['project_alias'] = $projectInfoArray[$project_id]['acronym'];
					} else {
						$projectInfoArray[$project_id]['acronym'] = $acronym;
						$attendanceArray['project_alias'] = $acronym;
					}
				}
			}
			$attendanceData[$weekNumber][$day] = $attendanceArray;
			$totalHajiri += $result['hajiri'];
			$totalEarnings += $result['amount'];
			$monthly_pay = 0;
			if($reportData['worker_wage_type'] == 1){
				$monthly_pay = $result['amount'];
			}
		}

		
		 
		if( isset($attendanceData[5]) ){
			$attendanceData[0] += $attendanceData[5];
			unset( $attendanceData[5] );
		}
		//$reportData['attendance'] = $attendanceData;
		$result = $this->report->getMonthlyLabourPaymentData( $postData );
		$totalPayment = $result ? $result : 0;
		ob_start();
		
        ?>
		<style type="text/css">
			@page {size: a4 portrait;margin:8px;}
			table,thead,tbody,tfoot,tr,th,td,p { font-family:"Calibri"; font-size:14px }
			td{ padding: 3px;}
			a.comment-indicator:hover + comment { background:#ffd; position:absolute; display:block; border:1px solid black; padding:0.5em;  } 
			a.comment-indicator { background:red; display:inline-block; border:1px solid black; width:0.5em; height:0.5em;  } 
			comment { display:none;  } 
		</style>
		<table cellspacing="0" border="<?php echo $border; ?>" style="height:100px;width:100%;">
			<tr>
				<td colspan="4" style="border-top: 2px solid #000000; border-bottom: 2px solid #000000; border-left: 2px solid #000000;" > 
					<?php if( !empty( $companyInfo['company_logo_image'] ) ) { ?><img src='<?php echo base_url('uploads/user/').$companyInfo['company_logo_image']; ?>' style="width:90px; margin:5px;"/> <?php } ?>
				</td>
				
				<td colspan="10" style="border-top: 2px solid #000000;border-bottom: 2px solid #000000;"  height="80" align="center" valign="middle"><b><font size="5" color="#000000"><u><?php echo $companyInfo['company_name']; ?></u></b></font></td>
				
				<td colspan="4" style="border-top: 2px solid #000000; border-right:2px solid #000; border-bottom: 2px solid #000000; " align="right" >
					<img src='<?php echo base_url('assets/admin/images/aasaan-footer-logo.jpg'); ?>' style="width:130px; margin:5px;"/>
				</td>
			</tr>

			<?php
            $objPHPExcel = new PHPExcel();

            $leftTopBorder = 
            	array(
          			'borders' => array(
				    	'left' => array(
				      	// 'style' => PHPExcel_Style_Border::BORDER_THIN
				    		'style' => PHPExcel_Style_Border::BORDER_THICK,
				    ),
				    'top' => array(
				      	// 'style' => PHPExcel_Style_Border::BORDER_THIN
				      	'style' => PHPExcel_Style_Border::BORDER_THICK,
				    ),
			  	)
			);
			$topRightBorder = 
				array(
					'borders' => array(
				    	'top' => array(
				      	// 'style' => PHPExcel_Style_Border::BORDER_THIN
				    		'style' => PHPExcel_Style_Border::BORDER_THICK,
				    ),
				    'right' => array(
				      	// 'style' => PHPExcel_Style_Border::BORDER_THIN
				      	'style' => PHPExcel_Style_Border::BORDER_THICK,
				    ),
			  	)
			);
			$leftBottomBorder = 
				array(
					'borders' => array(
					    'left' => array(
				      	// 'style' => PHPExcel_Style_Border::BORDER_THIN
					    	'style' => PHPExcel_Style_Border::BORDER_THICK,
				    ),
				    'bottom' => array(
				      	// 'style' => PHPExcel_Style_Border::BORDER_THIN
				      	'style' => PHPExcel_Style_Border::BORDER_THICK,
				    ), 
				)
			);
			$rightBottomBorder = 
				array(
					'borders' => array(
					    'right' => array(
				      	// 'style' => PHPExcel_Style_Border::BORDER_THIN
					    	'style' => PHPExcel_Style_Border::BORDER_THICK,
			    	),
				    'bottom' => array(
					      // 'style' => PHPExcel_Style_Border::BORDER_THIN
				    	'style' => PHPExcel_Style_Border::BORDER_THICK,
				    ),
			  	)
			);
			$styleArray = array(
		        'font' => array(
		            'size'  => 10,
                    'name'  => 'Arial'
		        ),
		        'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        )
		    );

		    $styleArray1 = array(
		        'font' => array(
		            'bold' => true,
		            'size'  => 11,
                    'name'  => 'Arial'
		        ),
		        'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        )
		    );

		    $styleArray2 = array(
		        'font' => array(
		            'bold' => true,
		            'size'  => 12,
                    'name'  => 'Arial'
		        ),
		        'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
		        )
		    );

		    $styleArray3 = array(
		        'font' => array(
		            'size'  => 10,
                    'name'  => 'Arial'
		        ),
		        'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
		        )
		    );

		    $styleArray4 = array(
		        'font' => array(
		            'size'  => 10,
                    'name'  => 'Arial'
		        ),
		        'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
		        )
		    );
		    $borderStyle = array(
				    'borders' => array(
				        'outline' => array(
				            'style' => PHPExcel_Style_Border::BORDER_THICK,
				            'color' => array('argb' => '000000'),
				        ),
				    ),
				);

            $company_name = $companyInfo['company_name'];
            $company_logo = $companyInfo['company_logo_image'];
            $lastColumn = 13;
			//First Line
		    $sheet = $objPHPExcel->getActiveSheet();

		    $imageExist =  ROOT_PATH.'/uploads/user/'.$company_logo;
		    $sheet->mergeCells('A1:C3');
		  	$lastColString = PHPExcel_Cell::stringFromColumnIndex($lastColumn);

		    if(!empty($company_logo) && file_exists($imageExist)){
			   	$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Logo');
				$objDrawing->setDescription('Logo');
				$objDrawing->setPath('./uploads/user/'.$company_logo);
				$objDrawing->setHeight(30);
				$objDrawing->setOffsetX(10);
				$objDrawing->setOffsetY(10);
				$objDrawing->setWorksheet($sheet);
				$objDrawing->setCoordinates('A1');
			}
			$sheet->getStyle('A1:C3')->applyFromArray($borderStyle);

		    $sheet->mergeCells('D1:J3');
		    $sheet->setCellValue('D1',$company_name);
		    $objPHPExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setWrapText(true);
		    $sheet->getStyle('D1')->applyFromArray($styleArray1);
		    $sheet->getStyle('D1:J3')->applyFromArray($borderStyle);

		    $sheet->mergeCells('K1:N3');
            $objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Logo');
			$objDrawing->setDescription('Logo');
			$objDrawing->setPath('./assets/admin/images/aasaan-footer-logo.jpg');
			$objDrawing->setHeight(36);
			$objDrawing->setOffsetX(20);
			$objDrawing->setOffsetY(10);
			$objDrawing->setWorksheet($sheet);
			$objDrawing->setCoordinates('K1');
			$sheet->getStyle('K1:N3')->applyFromArray($borderStyle);
			?>
		<!-- </table>
		<table cellspacing="0" border="0" style="width:100%"> -->
			<tr>
				<td style="border-left: 2px solid #000000" colspan="2" height="20" align="left" valign="middle"><font color="#000000">Name :</font></td>
				<td style="" colspan="7" align="left" valign="middle"><b><font color="#000000"><?php echo $reportData['labour_name'].' '.$reportData['labour_last_name']; ?></font></b></td>
				<td style="" colspan="3" align="left" valign="middle"><font color="#000000">Designation:</font></td>
				<td style="" colspan="3" align="left" valign="middle"><b><font color="#000000"><?php echo $reportData['category_name']; ?></font></b></td>
				<td style="" align="left" valign="middle"><font color="#000000"><?php if( $reportData['worker_wage_type'] == 1 ) echo 'Salary:'; else echo 'Wage:'; ?></font></td>
				<td colspan="2" style="border-right: 2px solid #000000;" align="left" valign="middle"><b><font face="Noto Sans Devanagari" color="#000000">Rs. <?php echo $reportData['worker_wage']; if( $reportData['worker_wage_type'] == 1 ) echo ' /month'; else echo ' /days'; ?></font></b></td>
			</tr>

			<?php

			if( $reportData['worker_wage_type'] == 1 ){ 
				$excelSalary = 'Salary: Rs. ';
				$excelWorkerWageType = ' /month';
			} 
			else{
			  	$excelSalary = 'Wage: Rs. '; 
			  	$excelWorkerWageType = ' /days';
			}

				//Second Line
				$sheet->getStyle('A4')->applyFromArray($styleArray);
				$sheet->getStyle('A4')->getFont()->setBold(true);
				$sheet->mergeCells('A4:E5');
			    $sheet->setCellValue('A4','Name: '.$reportData['labour_name'].' '.$reportData['labour_last_name']);
			    $sheet->getStyle('A4:E5')->applyFromArray($borderStyle);
			   
			    $sheet->getStyle('F4')->applyFromArray($styleArray);
			    $sheet->getStyle('F4')->getFont()->setBold(true);
			    $sheet->mergeCells('F4:J5');
				$sheet->setCellValue('F4','Designation: '.$reportData['category_name']);
				$sheet->getStyle('F4:J5')->applyFromArray($borderStyle);
			   	
			 	$sheet->getStyle('K4')->applyFromArray($styleArray);
			 	$sheet->getStyle('K4')->getFont()->setBold(true);
			    $sheet->mergeCells('K4:N5');
			    $sheet->setCellValue('K4',$excelSalary.$reportData['worker_wage'].$excelWorkerWageType);
			    $sheet->getStyle('K4:N5')->applyFromArray($borderStyle);
			?>

			<tr>
				<td style="border-top: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000" colspan="2" rowspan="12" height="184" align="center" valign="bottom"></td>
				<td style="border-top: 2px solid #000000; " colspan="14" align="center" valign="bottom"><b><font size="3" color="#000000">
					 &nbsp; <?php echo $this->data['month'][$postData['month']] .', '. $postData['year']; ?>
				</font></b></td>
				<?php
					$a = $this->data['month'][$postData['month']] .', '. $postData['year'];
				    $sheet->mergeCells('A6:'.$lastColString.'6');
				    $sheet->setCellValue('A6',$a);
				    $sheet->getStyle('A6')->applyFromArray($styleArray1);
				    $sheet->getStyle('A6:'.$lastColString.'6')->applyFromArray($borderStyle);
				?>
				<td style="border-top: 2px solid #000000;border-left: 2px solid #000000;border-right:2px solid #000000" colspan="2" rowspan="12" height="184" align="left" valign="bottom"><u><font color="#000000" style="width: 100%; border-top: 1px solid #000;border-bottom: 1px solid #000;">Holiday details:</font></u><br>
				<b style="padding-bottom:30px;">
					<font color="#000000">
						<?php 
						if($holidays){
							// echo '<br>Holidays: '.implode(',', $holidays).'<br>';
							
							//echo '<br>Holidays: <br />'.$company_holiday.'<br>';
							$i = 8;
							if($is_holiday){ 
								
								foreach( $holidayarray as $holiday ) { 
									echo $holiday['holiday_date'].': '.$holiday['holiday_name'].'<br />';

									$sheet->mergeCells('P'.$i.':Q'.$i);
				    				$sheet->setCellValue('P'.$i.'',$holiday['holiday_date'].': '.$holiday['holiday_name']);
				    				$sheet->getStyle('P'.$i)->applyFromArray($styleArray);
				    				// $sheet->getStyle('P'.$i.':Q'.$i)->applyFromArray($borderStyle);
								    $i++;
								}
							} 
						}?>
						<?php 
							$sheet->mergeCells('P'.$i.':Q'.$i);
						    $sheet->setCellValue('P'.$i,' ');
						    $sheet->getStyle('P'.$i)->applyFromArray($styleArray);
						    $sheet->getStyle('P'.$i)->getFont()->setBold(true);
						    // $sheet->getStyle('P'.$i.':Q'.$i)->applyFromArray($borderStyle);
						 	$i++;
							$sheet->mergeCells('P'.$i.':Q'.$i);
						    $sheet->setCellValue('P'.$i,'Paid Leave');
						    $sheet->getStyle('P'.$i)->applyFromArray($styleArray);
						    // $sheet->getStyle('P'.$i.':Q'.$i)->applyFromArray($borderStyle);
						    $sheet->getStyle('P'.$i)->getFont()->setBold(true);

						if($paidLeaves){
							echo 'Paid Leave: '.implode(',', $paidLeaves);
							/*foreach( $holidays as $holiday ) { 
								echo ': '.$holiday.'<br>';
							}*/
							$i++;
		    				$sheet->mergeCells('P'.$i.':Q'.$i);
		    				$sheet->setCellValue('P'.$i.'',implode(',', $paidLeaves));
		    				// $sheet->getStyle('P'.$i.':Q'.$i)->applyFromArray($borderStyle);
		    				$sheet->getStyle('P'.$i)->applyFromArray($styleArray);
						}?>
					</font></b><br><br>
				</td>
			</tr>
			<tr>
				<td style="border-top: 2px solid #000000; border-bottom: 1px solid #000000;border-right: 1px solid #000000;width: 9%;" colspan="2" align="center" valign="bottom"><font color="#000000">Mon</font></td>
				<td style="border-top: 2px solid #000000;border-bottom: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;width: 9%;" colspan="2" align="center" valign="bottom"><font color="#000000">Tue</font></td>
				<td style="border-top: 2px solid #000000;border-bottom: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;width: 9%;" colspan="2" align="center" valign="bottom"><font color="#000000">Wed</font></td>
				<td style="border-top: 2px solid #000000;border-bottom: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;width: 9%;" colspan="2" align="center" valign="bottom"><font color="#000000">Thu</font></td>
				<td style="border-top: 2px solid #000000;border-bottom: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;width: 9%;" colspan="2" align="center" valign="bottom"><font color="#000000">Fri</font></td>
				<td style="border-top: 2px solid #000000;border-bottom: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;width: 9%;" colspan="2" align="center" valign="bottom"><font color="#000000">Sat</font></td>
				<td style="border-top: 2px solid #000000;border-bottom: 1px solid #000000;border-left: 1px solid #000000;width: 9%;" colspan="2" align="center" valign="bottom"><font color="#000000">Sun</font></td>
			</tr>
			<?php
				$sheet->mergeCells('A7:B7');
			    $sheet->setCellValue('A7','Mon');
			    $sheet->getStyle('A7')->applyFromArray($styleArray);
			    $sheet->getStyle('A7')->getFont()->setBold(true);
			    $sheet->getStyle('A7:B7')->applyFromArray($borderStyle);

			    $sheet->mergeCells('C7:D7');
			    $sheet->setCellValue('C7','Tue');
			    $sheet->getStyle('C7')->applyFromArray($styleArray);
			    $sheet->getStyle('C7')->getFont()->setBold(true);
			    $sheet->getStyle('C7:D7')->applyFromArray($borderStyle);

			    $sheet->mergeCells('E7:F7');
			    $sheet->setCellValue('E7','Wed');
			    $sheet->getStyle('E7')->applyFromArray($styleArray);
			    $sheet->getStyle('E7')->getFont()->setBold(true);
			    $sheet->getStyle('E7:F7')->applyFromArray($borderStyle);

			    $sheet->mergeCells('G7:H7');
			    $sheet->setCellValue('G7','Thu');
			    $sheet->getStyle('G7')->applyFromArray($styleArray);
			    $sheet->getStyle('G7')->getFont()->setBold(true);
			    $sheet->getStyle('G7:H7')->applyFromArray($borderStyle);

			    $sheet->mergeCells('I7:J7');
			    $sheet->setCellValue('I7','Fri');
			    $sheet->getStyle('I7')->applyFromArray($styleArray);
			    $sheet->getStyle('I7')->getFont()->setBold(true);
			    $sheet->getStyle('I7:J7')->applyFromArray($borderStyle);

			    $sheet->mergeCells('K7:L7');
			    $sheet->setCellValue('K7','Sat');
			    $sheet->getStyle('K7')->applyFromArray($styleArray);
			    $sheet->getStyle('K7')->getFont()->setBold(true);
			    $sheet->getStyle('K7:L7')->applyFromArray($borderStyle);

			    $sheet->mergeCells('M7:N7');
			    $sheet->setCellValue('M7','Sun');
			    $sheet->getStyle('M7')->applyFromArray($styleArray);
			    $sheet->getStyle('M7')->getFont()->setBold(true);
			    $sheet->getStyle('M7:N7')->applyFromArray($borderStyle);

			   
			    $sheet->mergeCells('P7:Q7');
			    $sheet->setCellValue('P7','Holiday details');
			    $sheet->getStyle('P7')->applyFromArray($styleArray);
		    	$sheet->getStyle('P7')->getFont()->setBold(true);
		    	// $sheet->getStyle('P7:Q7')->applyFromArray($borderStyle);
		    ?>
			<?php $excel_row = 8; $overtime = 0; $total_PresentDays = 0; $total_absentdays = 0;
			for( $i=0; $i<5; $i++ ) { ?>
				<tr>
				<?php $excelindex = 0;
				for($j=1; $j<=7; $j++) { 
						$k = $j == 7 ? 0 : $j; 						

						$bgcolor = '';
						$excelbgColor = 'FFFFFF';
						$color = '#000000';
						$day ='';

						$hajiri = isset( $attendanceData[$i][$k]['hajiri'] ) ? $attendanceData[$i][$k]['hajiri'] : '';

						if($hajiri > 1){
							$overtime += $hajiri - 1;
						}
						$show_Wo_hajiri = '';
						if( isset( $attendanceData[$i][$k]['day'] ) ) {

							if($reportData['worker_wage_type'] == 1 && in_array($attendanceData[$i][$k]['day'] , $weekOffArray)){
								$bgcolor = 'bgcolor="#D3D3D3"';
								$color = '#000000';
								$excelbgColor = 'FFFFFF';
								$show_Wo_hajiri = 1;
							}elseif( in_array( $attendanceData[$i][$k]['day'], $holidays ) || ( isset( $attendanceData[$i][$k]['status'] ) && $attendanceData[$i][$k]['status'] == 3 ) ) {
								$bgcolor = 'bgcolor="#D3D3D3"';
								$color = '#000000';
								$excelbgColor = 'CCD1D1';
								$show_Wo_hajiri = 1;
							} elseif( !isset($attendanceData[$i][$k]['hajiri']) ) {
								$bgcolor = 'bgcolor="#D3D3D3"';
								$color = '#000000';
								$hajiri = 0;
								$excelbgColor = 'CCD1D1';
							}

							if(isset( $attendanceData[$i][$k]['status'] ) && $attendanceData[$i][$k]['status'] == 1 ){
								$total_PresentDays += $hajiri;
							}
							if(!in_array($attendanceData[$i][$k]['day'] , $paidLeaves) && !in_array( $attendanceData[$i][$k]['day'], $holidays ) && !in_array($attendanceData[$i][$k]['day'] , $weekOffArray) && $reportData['worker_wage_type'] == 1 && !isset( $attendanceData[$i][$k]['hajiri'] )){
								$total_absentdays++;
							}
							if(!$reportData['worker_wage_type'] == 1 && !in_array( $attendanceData[$i][$k]['day'], $holidays ) && !isset( $attendanceData[$i][$k]['hajiri'])){
								$total_absentdays++;
							}
							if($reportData['worker_wage_type'] == 1 && $show_Wo_hajiri == 1){
								$hajiri = $show_Wo_hajiri;
							}
							// if( !in_array($attendanceData[$i][$k]['day'] , $paidLeaves)){
							// 	$total_PresentDays += $hajiri;
							// } 
						}
					?>
					<td style="border-top: 1px solid #000000; border-left: 1px solid #000000" align="left" <?php echo $bgcolor; ?> valign="top" >
						<font size="1" color="<?php echo $color; ?>">
							<?php  if(!empty($hajiri) && fmod($hajiri , 1) !== 0.00) echo number_format($hajiri , 2); else echo $hajiri; ?>
						</font>
					</td>
					<td style="border-top: 1px solid #000000; <?php if($j == 7 && $i !== 4){ echo 'border-right: 3px solid #000000;'; } ?>" align="right" <?php echo $bgcolor; ?> valign="top" >
						<b>
							<font size="1" color="<?php echo $color; ?>">
								<?php if( isset( $attendanceData[$i][$k]['amount'] ) ) echo $attendanceData[$i][$k]['amount']; ?>
							</font>
						</b>
					</td>
					<?php
						//applay backbround color in cell
						$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($excelindex) . $excel_row)->applyFromArray(array('fill' => array(
			            'type' => PHPExcel_Style_Fill::FILL_SOLID,
			            'color' => array('rgb' => $excelbgColor)
			        	)));
						//applay left and top border css in cell
					 	$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($excelindex) . $excel_row)->applyFromArray($leftTopBorder);

					 	//applay css for cell align
					 	$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($excelindex) . $excel_row)->applyFromArray($styleArray4);
					 	//applay padding in cell
						$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($excelindex) . $excel_row)->getAlignment()->setIndent(1);
						$sheet->setCellValueByColumnAndRow($excelindex++, $excel_row, $hajiri);
						//applay top and right border css in cell
						$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($excelindex) . $excel_row)->applyFromArray($topRightBorder);

						//applay backbround color in cell
						$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($excelindex) . $excel_row)->applyFromArray(array('fill' => array(
			            'type' => PHPExcel_Style_Fill::FILL_SOLID,
			            'color' => array('rgb' => $excelbgColor)
			        	)));

						//applay padding in cell
					 	$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($excelindex) . $excel_row)->getAlignment()->setIndent(1);
					 	$excelAttendanceAmount = '';	
						if( isset( $attendanceData[$i][$k]['amount'] ) ){
							$excelAttendanceAmount = $attendanceData[$i][$k]['amount'];
						}

						$sheet->setCellValueByColumnAndRow($excelindex++, $excel_row, $excelAttendanceAmount);
					?>
				<?php }  ?>
				</tr>
				<tr>
				<?php 
					
					$excelindex2 = 0;
				for($j=1; $j<=7; $j++) { 
						$k = $j == 7 ? 0 : $j; 
						$bgcolor = '';
						$excelbgColor = 'FFFFFF';
						$color = '#000000';
						$dayColor = '#000000';
						$day ='';
						// $project = isset( $attendanceData[$i][$k]['project_alias'] ) ? $attendanceData[$i][$k]['project_alias'] : '';
						
						$project_id = isset($attendanceData[$i][$k]['project_id'])? $attendanceData[$i][$k]['project_id'] : '';

						// $attendanceData[$i][$k]['project_id'];
						
						$project = isset($projectInfoArray[$project_id]['acronym'])? $projectInfoArray[$project_id]['acronym'] : '';
						
						if( isset( $attendanceData[$i][$k]['day'] ) ) {
							// if( $reportData['worker_wage_type'] == 1 ) echo ' /month'; else echo ' /days'; 
							if( isset( $attendanceData[$i][$k]['status'] ) && $attendanceData[$i][$k]['status'] == 3 ){
								$project = 'PL';
								$bgcolor = 'bgcolor="#D3D3D3"';
								$color = '#000000';
								$dayColor = '#000000';
								$excelbgColor = 'CCD1D1';
							}elseif( in_array( $attendanceData[$i][$k]['day'], $holidays ) ) {
								$project = 'H';
								$bgcolor = 'bgcolor="#D3D3D3"';
								$color = '#000000';
								$dayColor = '#000000';
								$excelbgColor = 'CCD1D1';
							}elseif( $reportData['worker_wage_type'] == 1 && in_array($attendanceData[$i][$k]['day'] , $weekOffArray)){
								$project = 'WO';
								$bgcolor = 'bgcolor="#D3D3D3"';
								$color = '#000000';
								$dayColor = '#000000';
								$excelbgColor = 'FFFFFF';
							} elseif( !isset($attendanceData[$i][$k]['hajiri']) ) {
								$project = 'A';
								$bgcolor = 'bgcolor="#D3D3D3"';
								$color = '#000000';
								$dayColor = '#000000';
								$excelbgColor = 'CCD1D1';
							}
						}
				?>
					<td style="border-bottom: 1px solid #000000; border-left: 1px solid #000000" align="left" <?php echo $bgcolor; ?> valign="bottom">
						<font size="1" color="<?php echo $color; ?>">
							<?php echo $project; ?>
						</font>
					</td>
					<td style="border-bottom: 1px solid #000000;<?php if($j == 7 && $i !== 4){ echo "border-right: 3px solid #000000;"; } ?>" align="right" <?php echo $bgcolor; ?> valign="bottom" >
						<font color="<?php echo $dayColor; ?>">
							<?php if( isset( $attendanceData[$i][$k]['day'] ) ) echo $attendanceData[$i][$k]['day']; ?>
						</font>
					</td>

					<?php 
						//applay css for cell align left
						$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($excelindex2) . ($excel_row+1))->applyFromArray($styleArray4);
						//applay css for left and bottom border in cell 
						$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($excelindex2) . ($excel_row+1))->applyFromArray($leftBottomBorder);
						//applay backbround color in cell
						$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($excelindex2) . ($excel_row+1))->applyFromArray(array('fill' => array(
			            'type' => PHPExcel_Style_Fill::FILL_SOLID,
			            'color' => array('rgb' => $excelbgColor)
			        	)));

						//applay padding in cell
						$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($excelindex2) . ($excel_row+1))->getAlignment()->setIndent(1);

						$sheet->setCellValueByColumnAndRow($excelindex2++, $excel_row+1, $project);

						//applay css for right and bottom border in cell 
						$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($excelindex2) . ($excel_row+1))->applyFromArray($rightBottomBorder);

						//applay backbround color in cell
						$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($excelindex2) . ($excel_row+1))->applyFromArray(array('fill' => array(
			            'type' => PHPExcel_Style_Fill::FILL_SOLID,
			            'color' => array('rgb' => $excelbgColor)
			        	)));

						//applay padding in cell
						$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($excelindex2) . ($excel_row+1))->getAlignment()->setIndent(1);
						$excelAttendanceDay = '';	
						if( isset( $attendanceData[$i][$k]['day'] ) ){
							$excelAttendanceDay = $attendanceData[$i][$k]['day'];
						}

						$sheet->setCellValueByColumnAndRow($excelindex2++, $excel_row+1, $excelAttendanceDay);

					?>
				<?php } $excel_row = $excel_row + 2; ?>
				</tr>
			<?php } ?>

			<tr>
				<td style="border: 2px solid #000000" colspan="18" height="20" align="left" valign="middle"><b><u><font size="3" color="#000000">Attendance details :</font></u></b></td>
			</tr>
			<?php 
				$sheet->mergeCells('A'.$excel_row.':N'.$excel_row.'');
			    $sheet->setCellValue('A'.$excel_row.'','');
			    $excel_row++;

		     	$sheet->mergeCells('A'.$excel_row.':N'.$excel_row.'');
			    $sheet->setCellValue('A'.$excel_row.'','Attendance details : ');
			    $sheet->getStyle('A'.$excel_row.':N'.$excel_row.'')->applyFromArray($borderStyle);
			    $sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray2);
			    $excel_row++; 
		    ?>
			<tr>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" colspan="16" height="20" align="center" valign="bottom"><b><font color="#000000">Project name</font></b></td>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" colspan="2" align="center" valign="bottom"><b><font color="#000000">Total Hajiri</font></b></td>
			</tr>
			<?php
				$sheet->mergeCells('A'.$excel_row.':K'.$excel_row.'');
			    $sheet->setCellValue('A'.$excel_row.'','Project name');
			    $sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray);
			    $sheet->getStyle('A'.$excel_row.':K'.$excel_row.'')->applyFromArray($borderStyle);
			    $sheet->getStyle('A'.$excel_row)->getFont()->setBold(true);

			    $sheet->mergeCells('L'.$excel_row.':N'.$excel_row.'');
			    $sheet->setCellValue('L'.$excel_row.'','Total Hajiri');
			    $sheet->getStyle('L'.$excel_row.':N'.$excel_row.'')->applyFromArray($borderStyle);
			    $sheet->getStyle('L'.$excel_row.'')->applyFromArray($styleArray);
			    $sheet->getStyle('L'.$excel_row)->getFont()->setBold(true);
			    $excel_row++;
			?>

			<?php 
			$totalPresentDays = 0;
			if( $projectInfoArray ) { 
				foreach( $projectInfoArray as $project ) {
					$totalPresentDays += $project['present_days'];
			?>
				<tr>
					<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" colspan="16" height="18" align="left" valign="middle"><font color="#000000"><?php echo $project['project_name'].' ('.$project['acronym'].')'; ?></font></td>
					<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" colspan="2" align="center" valign="middle" ><font color="#000000"><?php if($project['present_days'] > 0){ echo number_format($project['present_days'] , 2); } else{ echo $project['present_days']; } ?></font></td>
				</tr>
					<?php 

					$sheet->mergeCells('A'.$excel_row.':K'.$excel_row.'');
				    $sheet->setCellValue('A'.$excel_row.'',$project['project_name'].' ('.$project['acronym'].')');
				    $sheet->getStyle('A'.$excel_row.':K'.$excel_row.'')->applyFromArray($borderStyle);
				    $sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray);
				    
					$sheet->mergeCells('L'.$excel_row.':N'.$excel_row.'');
				    $sheet->setCellValue('L'.$excel_row.'',$project['present_days']);
				    $sheet->getStyle('L'.$excel_row.':N'.$excel_row.'')->applyFromArray($borderStyle);
					$sheet->getStyle('L'.$excel_row.'')->applyFromArray($styleArray);
			    	$sheet->getStyle('L'.$excel_row)->getFont()->setBold(true);
				    $excel_row++;
			    ?>
			<?php } 
			}
			// if($reportData['worker_wage_type'] == 1){
			// 	$absentDays = $noOfDays - ( $total_PresentDays + count($paidLeaves) + count($holidayarray));
			// } else{

				$absentDays = $noOfDays - ( $total_PresentDays + count($paidLeaves) + count($holidayarray) );

				//total hajiri monthly worker
				$Count_WeekOffs = 0;
				if($reportData['worker_wage_type'] == 1){
					$totalHajiri = $total_PresentDays + count($paidLeaves) + count($holidayarray) + count($weekOffArray);
					$Count_WeekOffs = count($weekOffArray);

					$workingdays = $noOfDays - count($weekOffArray);

					$dailywage = $reportData['worker_wage'] / $workingdays; 

					$totalEarnings = round($totalHajiri * $dailywage);
 				} 

				if($total_PresentDays > 0){ 
					$total_PresentDays = number_format($total_PresentDays ,2 ); 
				} 
				if($overtime > 0){
					$overtime = number_format($overtime ,2 );
				}if($totalHajiri > 0){
					$totalHajiri = number_format($totalHajiri ,2 ); 
				}
			?>
			<tr>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" colspan="16" height="18" align="right" valign="middle"><font color="#000000">Total number of present days including overtime</font></td> 
				<?php
					$sheet->mergeCells('A'.$excel_row.':K'.$excel_row.'');
				    $sheet->setCellValue('A'.$excel_row.'','Total number of present days including overtime');
					$sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray3);
					$sheet->getStyle('A'.$excel_row.':K'.$excel_row.'')->applyFromArray($borderStyle);
				?>

				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" colspan="2" align="center" valign="middle" ><font color="#000000"><?php echo $total_PresentDays; ?></font></td>
				<?php 
					$sheet->mergeCells('L'.$excel_row.':N'.$excel_row.'');
				    $sheet->setCellValue('L'.$excel_row.'',$total_PresentDays);
					$sheet->getStyle('L'.$excel_row.'')->applyFromArray($styleArray);
					$sheet->getStyle('L'.$excel_row.':N'.$excel_row.'')->applyFromArray($borderStyle);
			    	$sheet->getStyle('L'.$excel_row)->getFont()->setBold(true);
			    	$excel_row++;
				?>
			</tr>
			<?php /* ?>
			<tr>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" colspan="16" height="18" align="right" valign="middle"><font color="#000000">Overtime</font></td>
				<?php 
					$sheet->mergeCells('A'.$excel_row.':K'.$excel_row.'');
				    $sheet->setCellValue('A'.$excel_row.'','Overtime');
					$sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray3);
					$sheet->getStyle('A'.$excel_row.':K'.$excel_row.'')->applyFromArray($borderStyle);
				?>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" colspan="2" align="center" valign="middle" ><b><font color="#000000"><?php echo $overtime; ?></font></b></td>
				<?php 
					$sheet->mergeCells('L'.$excel_row.':N'.$excel_row.'');
				    $sheet->setCellValue('L'.$excel_row.'',$overtime);
					$sheet->getStyle('L'.$excel_row.'')->applyFromArray($styleArray);
					$sheet->getStyle('L'.$excel_row.':N'.$excel_row.'')->applyFromArray($borderStyle);
			    	$sheet->getStyle('L'.$excel_row)->getFont()->setBold(true);
			    	$excel_row++;
				?>
			</tr>
			<?php */ ?>
			<tr>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" colspan="16" height="18" align="right" valign="middle"><font color="#000000">Holidays, Paid leaves and Weekly-offs</font></td>
				<?php 
					$sheet->mergeCells('A'.$excel_row.':K'.$excel_row.'');
				    $sheet->setCellValue('A'.$excel_row.'','Holidays, Paid leaves and Weekly-offs');
					$sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray3);
					$sheet->getStyle('A'.$excel_row.':K'.$excel_row.'')->applyFromArray($borderStyle);

					$count_holidays = 0;
					$count_paidleaves = 0;
					if(!empty($paidLeaves)) $count_paidleaves = count($paidLeaves);
					if(!empty($holidayarray)) $count_holidays = count($holidayarray);

					$count_holidays = $count_holidays + $count_paidleaves;
					if($reportData['worker_wage_type'] == 1){
						$count_holidays = $count_holidays + $count_paidleaves + $Count_WeekOffs;
					}
				?>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" colspan="2" align="center" valign="middle" ><font color="#000000"><?php echo $count_holidays; ?></font></td>
				<?php 

					$sheet->mergeCells('L'.$excel_row.':N'.$excel_row.'');
				    $sheet->setCellValue('L'.$excel_row.'', $count_holidays);
					$sheet->getStyle('L'.$excel_row.'')->applyFromArray($styleArray);
					$sheet->getStyle('L'.$excel_row.':N'.$excel_row.'')->applyFromArray($borderStyle);
			    	$sheet->getStyle('L'.$excel_row)->getFont()->setBold(true);
			    	$excel_row++;
				?>
			</tr>
			<?php /* ?>
			<tr>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" colspan="16" height="18" align="right" valign="middle"><font color="#000000">Paid leaves</font></td>
				<?php 
					$sheet->mergeCells('A'.$excel_row.':K'.$excel_row.'');
				    $sheet->setCellValue('A'.$excel_row.'','Paid leaves');
					$sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray3);
					$sheet->getStyle('A'.$excel_row.':K'.$excel_row.'')->applyFromArray($borderStyle);

					$count_paidleaves = 0;
					if(!empty($paidLeaves)) $count_paidleaves = count($paidLeaves);
				?>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" colspan="2" align="center" valign="middle" ><b><font color="#000000"><?php echo $count_paidleaves; 
				?></font></b></td>
				<?php 

					$sheet->mergeCells('L'.$excel_row.':N'.$excel_row.'');
				    $sheet->setCellValue('L'.$excel_row.'', $count_paidleaves);
					$sheet->getStyle('L'.$excel_row.'')->applyFromArray($styleArray);
					$sheet->getStyle('L'.$excel_row.':N'.$excel_row.'')->applyFromArray($borderStyle);
			    	$sheet->getStyle('L'.$excel_row)->getFont()->setBold(true);
			    	$excel_row++;
				?>
			</tr>
			<?php */ ?>
			<?php /* ?>
			<tr>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" colspan="16" height="18" align="right" valign="middle"><font color="#000000">WO</font></td>
				<?php 
					$sheet->mergeCells('A'.$excel_row.':K'.$excel_row.'');
				    $sheet->setCellValue('A'.$excel_row.'','WO');
					$sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray3);
					$sheet->getStyle('A'.$excel_row.':K'.$excel_row.'')->applyFromArray($borderStyle);
				?>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" colspan="2" align="center" valign="middle" ><font color="#000000"><?php echo $Count_WeekOffs; 
				?></font></td>
				<?php 

					$sheet->mergeCells('L'.$excel_row.':N'.$excel_row.'');
				    $sheet->setCellValue('L'.$excel_row.'', $Count_WeekOffs);
					$sheet->getStyle('L'.$excel_row.'')->applyFromArray($styleArray);
					$sheet->getStyle('L'.$excel_row.':N'.$excel_row.'')->applyFromArray($borderStyle);
			    	$sheet->getStyle('L'.$excel_row)->getFont()->setBold(true);
			    	$excel_row++;
				?>
			</tr>
			<?php  */ ?>

			<tr>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" colspan="16" height="18" align="right" valign="middle"><font color="#000000">Total number of absent days</font></td>
				<?php 
					$sheet->mergeCells('A'.$excel_row.':K'.$excel_row.'');
				    $sheet->setCellValue('A'.$excel_row.'','Total number of absent days');
				    $sheet->getStyle('A'.$excel_row.':K'.$excel_row.'')->applyFromArray($borderStyle);
					$sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray3);
				?>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" colspan="2" align="center" valign="middle" ><font color="#000000"><?php echo $total_absentdays;//echo $absentDays; ?></font></td>
				<?php 
					$sheet->mergeCells('L'.$excel_row.':N'.$excel_row.'');
				    $sheet->setCellValue('L'.$excel_row.'',$total_absentdays);
					$sheet->getStyle('L'.$excel_row.'')->applyFromArray($styleArray);
					$sheet->getStyle('L'.$excel_row.':N'.$excel_row.'')->applyFromArray($borderStyle);
			    	$sheet->getStyle('L'.$excel_row)->getFont()->setBold(true);
			    	$excel_row++;
				?>
			</tr>
			<tr>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" colspan="16" height="18" align="right" valign="middle"><b><font color="#000000">Total attendance</font></b></td>
				<?php 
					$sheet->mergeCells('A'.$excel_row.':K'.$excel_row.'');
				    $sheet->setCellValue('A'.$excel_row.'','Total attendance');
					$sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray3);
					$sheet->getStyle('A'.$excel_row.':K'.$excel_row.'')->applyFromArray($borderStyle);
					$sheet->getStyle('A'.$excel_row)->getFont()->setBold(true);
				?>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" colspan="2" align="center" valign="middle" ><b><font color="#000000"><?php echo $totalHajiri; ?></font></b></td>
				<?php 
					$sheet->mergeCells('L'.$excel_row.':N'.$excel_row.'');
				    $sheet->setCellValue('L'.$excel_row.'',$totalHajiri);
					$sheet->getStyle('L'.$excel_row.'')->applyFromArray($styleArray);
					$sheet->getStyle('L'.$excel_row.':N'.$excel_row.'')->applyFromArray($borderStyle);
			    	$sheet->getStyle('L'.$excel_row)->getFont()->setBold(true);
			    	$excel_row++;
				?>
			</tr>
			<tr>
				<td style="border-top: 1px solid #000000; border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000" colspan="18" height="18" align="left" valign="middle"><b><u><font size="3" color="#000000">Payment details :</font></u></b></td>
			</tr>
			<?php 
				 
				$sheet->mergeCells('A'.$excel_row.':N'.$excel_row.'');
			    $sheet->setCellValue('A'.$excel_row.'','Payment details : ');
			    $sheet->getStyle('A'.$excel_row.':N'.$excel_row.'')->applyFromArray($borderStyle);
			    $sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray2);
			    $excel_row++; 
		    ?>
			<tr>
				<td style="border-top: 2px solid #000000; border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000" colspan="5" height="20" align="center" valign="bottom"><font color="#000000">Opening balance</font></td>
				<td style="border-top: 2px solid #000000; border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000" colspan="5" align="center" valign="bottom"><font color="#000000">Earnings</font></td>
				<td style="border-top: 2px solid #000000; border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000" colspan="5" align="center" valign="bottom"><font color="#000000">Payment</font></td>
				<td style="border-top: 2px solid #000000; border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000" colspan="3" align="center" valign="bottom"><font color="#000000">Closing balance</font></td>
			</tr>
			<?php
				$sheet->mergeCells('A'.$excel_row.':C'.$excel_row.'');
			    $sheet->setCellValue('A'.$excel_row.'','Opening balance ');
			    $sheet->getStyle('A'.$excel_row.':C'.$excel_row.'')->applyFromArray($borderStyle);
			    $sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray);
			
				$sheet->mergeCells('D'.$excel_row.':F'.$excel_row.'');
			    $sheet->setCellValue('D'.$excel_row.'','Earnings');
			    $sheet->getStyle('D'.$excel_row.':F'.$excel_row.'')->applyFromArray($borderStyle);
			    $sheet->getStyle('D'.$excel_row.'')->applyFromArray($styleArray);
			
				$sheet->mergeCells('G'.$excel_row.':I'.$excel_row.'');
			    $sheet->setCellValue('G'.$excel_row.'','Payment');
			    $sheet->getStyle('G'.$excel_row.':I'.$excel_row.'')->applyFromArray($borderStyle);
			    $sheet->getStyle('G'.$excel_row.'')->applyFromArray($styleArray);
			
				$sheet->mergeCells('J'.$excel_row.':N'.$excel_row.'');
			    $sheet->setCellValue('J'.$excel_row.'','Closing balance');
			    $sheet->getStyle('J'.$excel_row.':N'.$excel_row.'')->applyFromArray($borderStyle);
			    $sheet->getStyle('J'.$excel_row.'')->applyFromArray($styleArray);
			    $excel_row++;

			    if( $reportData['worker_wage_type'] == 1 ) {

			        $weekoff_Payment = count($weekOffArray) * $monthly_pay;
			    	$holidays_payment = count($holidayarray) * $monthly_pay;
			    	$paidleaves_payment =  count($paidLeaves) * $monthly_pay;
			    	$overtime_payment = $overtime * $monthly_pay;

			    	
					// $totalEarnings += $weekoff_Payment + $holidays_payment + $paidleaves_payment;
			    } 
			?>
			<tr>
				<td style="border-top: 2px solid #000000; border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000" colspan="5" height="25" align="center" valign="middle" ><b><font color="#000000">Rs. <?php echo $openingBalance; ?></font></b></td>
				<td style="border-top: 2px solid #000000; border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000" colspan="5" align="center" valign="middle" ><b><font color="#000000">Rs. <?php echo $totalEarnings; ?></font></b></td>
				<td style="border-top: 2px solid #000000; border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000" colspan="5" align="center" valign="middle" ><b><font color="#000000">Rs. <?php echo $totalPayment; ?></font></b></td>
				<td style="border-top: 2px solid #000000; border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000" colspan="3" align="center" valign="middle" ><b><font color="#000000">Rs. <?php echo ( $openingBalance + $totalEarnings ) - $totalPayment; ?></font></b></td>
			</tr>
			<?php
				$sheet->mergeCells('A'.$excel_row.':C'.$excel_row.'');
			    $sheet->setCellValue('A'.$excel_row.'','Rs. '.$openingBalance);
			    $sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray);
			    $sheet->getStyle('A'.$excel_row.':C'.$excel_row.'')->applyFromArray($borderStyle);
			
				$sheet->mergeCells('D'.$excel_row.':F'.$excel_row.'');
			    $sheet->setCellValue('D'.$excel_row.'','Rs. '.$totalEarnings);
			    $sheet->getStyle('D'.$excel_row.'')->applyFromArray($styleArray);
			    $sheet->getStyle('D'.$excel_row.':F'.$excel_row.'')->applyFromArray($borderStyle);
			
				$sheet->mergeCells('G'.$excel_row.':I'.$excel_row.'');
			    $sheet->setCellValue('G'.$excel_row.'','Rs. '.$totalPayment);
			    $sheet->getStyle('G'.$excel_row.'')->applyFromArray($styleArray);
			    $sheet->getStyle('G'.$excel_row.':I'.$excel_row.'')->applyFromArray($borderStyle);
			
				$excelClosingBalance = ($openingBalance + $totalEarnings ) - $totalPayment;
				$sheet->mergeCells('J'.$excel_row.':N'.$excel_row.'');
			    $sheet->setCellValue('J'.$excel_row.'','Rs. '.$excelClosingBalance);
			    $sheet->getStyle('J'.$excel_row.'')->applyFromArray($styleArray);
			    $sheet->getStyle('J'.$excel_row.':N'.$excel_row.'')->applyFromArray($borderStyle);
			    $excel_row++;
			?>
			<tr>
				<td style="border-left: 2px solid #000000; border-bottom: 1px solid #000000 " colspan=16 height="25" align="right" valign=bottom><font size=3 color="#000000"></font></td>
				<td style="border-right: 2px solid #000000; font-size:18px; border-bottom: 1px solid #000000 " colspan=2 align="center" valign=bottom >
					<b>
						<font color="#000000"><br><br><br>
							<?php echo $this->session->userdata('name'); ?><br>
							<span style="font-size:12px">(self-attested)</span><br>
							<span style="font-size:15px"><?php echo $companyInfo['company_name']; ?></span>
						</font>
					</b>
				</td>			
			</tr>

			<?php

				$name = $this->session->userdata('name');
				$update_row = $excel_row + 4 ;
				$sheet->mergeCells('A'.$excel_row.':I'.$update_row.'');
			    $sheet->setCellValue('A'.$excel_row.'','');
			    $sheet->getStyle('A'.$excel_row.':I'.$update_row.'')->applyFromArray($borderStyle);

			    $sheet->mergeCells('J'.$excel_row.':N'.$update_row.'');

			    $sheet->setCellValue('J'.$excel_row,$name."\n".'(self-attested)'."\n".$companyInfo['company_name']);
			    $sheet->getStyle('J'.$excel_row.':N'.$update_row.'')->applyFromArray($borderStyle);

			    $sheet->getStyle('J'.$excel_row)->getAlignment()->setWrapText(true);
			    $sheet->getStyle('J'.$excel_row)->getFont()->setBold(true);
	            $sheet->getStyle('J'.$excel_row.':N'.$excel_row.'')->getFont()->setSize(10);
	            $sheet->getStyle('J'.$excel_row.':N'.$excel_row.'')->applyFromArray($styleArray1);

	            $update_row++;
			?>

		    <tr>
				<td style="border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=18 align="left" valign=bottom sdnum="1033;16393;[$-4009]DD-MM-YYYY">
					<b><u><font size=3 color="#000000">Disclaimer:</font></u></b>
				</td>
			</tr>
			<?php
				$sheet->mergeCells('A'.$update_row.':N'.$update_row.'');
			    $sheet->setCellValue('A'.$update_row,'');
			    $update_row++;

				$sheet->mergeCells('A'.$update_row.':N'.$update_row.'');
			    $sheet->setCellValue('A'.$update_row,'Disclaimer');
			    $sheet->getStyle('A'.$update_row)->applyFromArray($styleArray2);
			    $sheet->getStyle('A'.$update_row)->getFont()->setUnderline(true);
			    $sheet->getStyle('A'.$update_row.':N'.$update_row.'')->applyFromArray($borderStyle);

			    $update_row++;
		    ?>
			<tr>
				<td style="border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=18 align="left" valign=bottom>
					<font size=1 color="#000000">1. This is an auto-generated report.</font>
				</td>
			</tr>
			<?php
				$sheet->mergeCells('A'.$update_row.':N'.$update_row.'');
			    $sheet->setCellValue('A'.$update_row.'','1. This is an auto-generated report.');
			    $sheet->getStyle('A'.$update_row.'')->applyFromArray($styleArray4);
			    $sheet->getStyle('A'.$update_row.'')->getFont()->setSize(8);
			    $sheet->getStyle('A'.$update_row.':N'.$update_row.'')->applyFromArray($borderStyle);

			    $update_row++;
		    ?>
			<tr>
				<td style="border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=18 align="left" valign=bottom>
					<font size=1 color="#000000">2. Aasaan does not hold any legal liability for any data generated through this report</font>
				</td>
			</tr>
			<?php
				$sheet->mergeCells('A'.$update_row.':N'.$update_row.'');
			    $sheet->setCellValue('A'.$update_row.'','2. Aasaan does not hold any legal liability for any data generated through this report');
			    $sheet->getStyle('A'.$update_row.'')->applyFromArray($styleArray4);
			    $sheet->getStyle('A'.$update_row.'')->getFont()->setSize(8);
			    $sheet->getStyle('A'.$update_row.':N'.$update_row.'')->applyFromArray($borderStyle);
			    $update_row++;
		    ?>
			<tr>
				<td style="border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=18 align="left" valign=bottom>
					<font size=1 color="#000000">3. The values are purely based on the app operations</font>
				</td>
			</tr>
			<?php
				$sheet->mergeCells('A'.$update_row.':N'.$update_row.'');
			    $sheet->setCellValue('A'.$update_row.'','3. The values are purely based on the app operations');
			    $sheet->getStyle('A'.$update_row.'')->applyFromArray($styleArray4);
			    $sheet->getStyle('A'.$update_row.'')->getFont()->setSize(8);
			    $sheet->getStyle('A'.$update_row.':N'.$update_row.'')->applyFromArray($borderStyle);

			    $update_row++;
		    ?>
			<tr>
				<td style="border-left: 2px solid #000000; border-bottom: 2px solid #000000; border-right: 2px solid #000000" colspan=18 align="right" valign=bottom>
					<font size=1 color="#000000">This report was generated at <?php echo date('H:i'); ?> hours on <?php echo date('d/m/Y'); ?></font>
				</td>
			</tr>
			<?php
				$c_time = date('H:i');
				$c_date = date('d/m/Y');
				$sheet->mergeCells('A'.$update_row.':N'.$update_row.'');
			    $sheet->setCellValue('A'.$update_row.'','This report was generated at '.$c_time.' hours on '.$c_date.'');
			    $sheet->getStyle('A'.$update_row.'')->getFont()->setSize(8);
			    $sheet->getStyle('A'.$update_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			    $sheet->getStyle('A'.$update_row.':N'.$update_row.'')->applyFromArray($borderStyle);
			    $update_row++;
			    $sheet->setSelectedCells('D1:J3');
		    ?>
		</table>
		
		<?php
        $contents = ob_get_contents();
        ob_end_clean();
        $month = date("F", mktime(0, 0, 0, $postData['month'] , 10));

        if($this->input->get_post('downloadformat') == 'pdf'){

	        $this->load->library('Dom_pdf');
	        // Convert to PDF
	        $this->dompdf->load_html($contents);
	        $this->dompdf->render();
	        
	        $this->dompdf->stream($reportData['labour_name']." ".$reportData['labour_last_name']." attendance report ".$month, array("Attachment" => True));
	    } else{
	    	
	   	  ob_end_clean();
		   $filename= $reportData['labour_name']." ".$reportData['labour_last_name']." attendance report ".$month.'.xls';
	        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			// header('Content-Disposition: attachment;filename="01simple.xlsx"');
			header('Content-Disposition: attachment;filename='.$filename);
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');

			// If you're serving to IE over SSL, then the following may be needed
			header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
			header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header ('Pragma: public'); // HTTP/1.0
	  
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');
			exit;

	    }
	}

    function monthly_payment_report() { 
        $this->load->model('manager_model', 'manager');
       
		$data = $this->data;
		//For Getting project
		$projects = array("" => "Select project ");
		$project_results = array();
		if($this->session->userdata('user_designation') == 'admin')
			$project_results = $this->project->get_datatables();
		if($this->session->userdata('user_designation') == 'Supervisor'){
			$project_results = $this->foreman->get_project_foremanId( $this->session->userdata('id'));
		}
		foreach ($project_results as $key => $value) {
			$projects[$value->project_id] = $value->project_name;
		}
        $data['projects'] = $projects;
        $data['Category'] = $this->category->get_all_category($this->session->userdata('company_id'));
        $years = array("" => "Select year");
        foreach (range(2016, 2050) as $value) {
            $years[$value] = $value;
        }
        $data['years'] = $years;
        $months = array("" => "Select month", "1" => "January", "2" => "February", "3" => "March", "4" => "April",
            "5" => "May", "6" => "June", "7" => "July", "8" => "August",
            "9" => "September", "10" => "October", "11" => "November", "12" => "December",
        );
		$data['menu_title'] = 'monthlyPayment';
        $data['months'] = $months;
        $data['title'] = 'Monthly payment report';
        $data['description'] = '';
        $data['page'] = 'report/monthly_payment_report';
        if (isset($_REQUEST['submit'])) {
            $this->form_validation->set_data($this->input->get());
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('project', 'Project', 'trim|required|numeric');
            $this->form_validation->set_rules('month', 'Month', 'trim|required|numeric');
            $this->form_validation->set_rules('year', 'Year', 'trim|required|numeric');
            if ($this->form_validation->run() == TRUE) {
                $this->generate_monthly_payment_report();
            }
        }
        $this->load->view('includes/template', $data);
    }
	
	function generate_monthly_payment_report() {
		if($this->input->get_post('downloadformat') == 'pdf'){
			$border = '0';
		}else{
			$border = '1';
		}
		$postData = array();
        $postData['month'] 		= $this->input->get_post('month');
        $postData['year'] 		= $this->input->get_post('year');
        $postData['company_id'] = $this->input->get_post('company_id');
        $postData['project_id'] = $this->input->get_post('project');
        $postData['category_id'] = $this->input->get_post('category');

		if ( empty( $postData['company_id'] ) || empty( $postData['project_id'] ) || empty( $postData['month'] ) || empty( $postData['year'] ) ) {
            $this->session->set_flashdata('error', 'No data found.');
			redirect(base_url('admin/report/monthly_attendance_report'));
		}
		if($postData['category_id'] == '')
			$postData['category_id'] = '%';
 
		$results = $this->report->getMonthlyAttendance( $postData ); 
		$getMaxAttendance = $this->report->getMaxAttendance($postData); 
		
		if ( empty( $results ) ) {
			$this->session->set_flashdata('error', 'No data found.');
			redirect(base_url('admin/report/monthly_payment_report'));
		}
		
		$companyInfo = $this->db->where( 'compnay_id', $postData['company_id'] )->get('company')->row_array();
		$projectInfo = $this->db->where( 'project_id', $postData['project_id'] )->get('project')->row_array();
		$noOfDays = date("t", strtotime( $postData['year'] . '-' . $postData['month'] ) );
		
		$weekOffs = $this->report->getWeekOff( array( 'company_id' => $postData['company_id'], 'status' => 1 ) );
		$weekOffs = $weekOffs ? explode( ',', $weekOffs->days ) : array();
		$numOfWeekOff = 0; 
		if ( !empty( $weekOffs ) ) {
			$fromDate = date( 'Y-m-01 ',strtotime( $postData['year'].'-'.$postData['month'].'-01' ) );
			$toDate = date( 'Y-m-d ',strtotime( $postData['year'].'-'.$postData['month'].'-'.$noOfDays ) );
			for ( $i = 0; $i <= ((strtotime($toDate) - strtotime($fromDate)) / 86400); $i++ ) {
				if( in_array( date('l',strtotime($fromDate) + ($i * 86400)), $weekOffs ) ) {
					$numOfWeekOff++;
				}    
			}
		}

		$workingdays = $noOfDays - $numOfWeekOff;

		$holidays = $this->report->getHolidays( $postData, $noOfDays );
		$holidays = $holidays ? explode( ',', $holidays->holiday_day ) : array();
				
		$workerData = array();
		foreach( $results as $result ){
			$worker_id =  $result['worker_id'];
			
			if( isset( $workerData[$worker_id] ) ){
				
				if( $result['status'] == 3 ){
					$workerData[$worker_id]['paid_leaves'] += 1;
				} else {
					$workerData[$worker_id]['present_days'] += 1;
				}
				if( $result['hajiri'] > 1 ) {
					$workerData[$worker_id]['overtime'] = $workerData[$worker_id]['overtime'] + ( $result['hajiri'] - 1 );
				}
				$workerData[$worker_id]['total_hajiri'] += $result['hajiri'];
				$workerData[$worker_id]['total_earning'] += $result['amount'];
				
			} else {
				$workerData[$worker_id] = array();
				$workerData[$worker_id]['labour_name'] 		= $result['labour_name'];
				$workerData[$worker_id]['labour_last_name'] = $result['labour_last_name'];
				$workerData[$worker_id]['category_name'] 	= $result['category_name'];
				$workerData[$worker_id]['worker_wage'] 		= $result['worker_wage'];
				$workerData[$worker_id]['worker_wage_type'] = $result['worker_wage_type'];
				
				$workerData[$worker_id]['present_days'] = 0;
				$workerData[$worker_id]['overtime'] = 0;
				$workerData[$worker_id]['paid_leaves'] = 0;
				$workerData[$worker_id]['week_offs'] = $numOfWeekOff;
				if( $result['status'] == 3 ){
					$workerData[$worker_id]['paid_leaves'] = 1;
				} else {
					$workerData[$worker_id]['present_days'] = 1;
				}
				if( $result['hajiri'] > 1 ) {
					$workerData[$worker_id]['overtime'] = $result['hajiri'] - 1;
				}
				$workerData[$worker_id]['total_hajiri'] = $result['hajiri'];
				$workerData[$worker_id]['total_earning'] = $result['amount'];
			}
		}
		$results = $this->report->getMonthlyPayment( $postData );
		$paymentData = array();
		$total_payment = 0;
		if ( !empty( $results ) ) {
			
			foreach( $results as $result ){
				$payment_day =  $result['payment_day'];
				$category_name =  $result['category_name'];
				
				if( isset( $paymentData['payment'][$payment_day] ) ){
					$paymentData['payment'][$payment_day] += $result['advance_payment'];
				} else {
					$paymentData['payment'][$payment_day] = $result['advance_payment'];
				}
				
				$total_payment += $result['advance_payment'];

				//calculate worker advance payment
				if(isset($workerData[$result['worker_id']]['advance_payment']))
					$workerData[$result['worker_id']]['advance_payment'] += $result['advance_payment'];
				else
					$workerData[$result['worker_id']]['advance_payment'] = $result['advance_payment'];
			}
			
		}
		$postData['week_offs'] = $weekOffs;
		$opening_balance = $this->report->getOpeningBalance( $postData );
		
		ob_start();
        ?>
		<style type="text/css">
			table,thead,tbody,tfoot,tr,th,td,p { font-family:"Calibri"; font-size:16px }
			td{ padding: 3px;}
			a.comment-indicator:hover + comment { background:#ffd; position:absolute; display:block; border:1px solid black; padding:0.5em;  } 
			a.comment-indicator { background:red; display:inline-block; border:1px solid black; width:0.5em; height:0.5em;  } 
			comment { display:none;  } 
		</style>
		
		<table cellspacing="0" border="<?php echo $border;?>" style="height:100px; width:100%;">
			<tr>
				<td colspan="3" style="border-top: 2px solid #000000; border-bottom: 2px solid #000000; border-left: 2px solid #000000;"  > 
					<?php if( !empty( $companyInfo['company_logo_image'] ) ) { ?><img src='<?php echo base_url('uploads/user/').$companyInfo['company_logo_image']; ?>' style="width:100px; margin:10px;"/> <?php } ?>
				</td>
				<!-- <td style="border-top: 2px solid #000000;border-bottom: 2px solid #000000;" width="200px" align="center" valign="middle"></td> -->
				
				<td colspan="4" style="border-top: 2px solid #000000;border-bottom: 2px solid #000000;"  height="93" align="center" valign="middle"><font size="5" color="#000000"><u><b><?php echo $companyInfo['company_name']; ?></b></u></font> <br><font size="4"><?php echo $projectInfo['project_name']; ?></font></td>

				<td colspan="5" style="border-top: 2px solid #000000; border-right:2px solid #000; border-bottom: 2px solid #000000; " align="right" >
					<img src='<?php echo base_url('assets/admin/images/aasaan-footer-logo.jpg'); ?>' style="width:200px; margin:10px;"/>
				</td>
			</tr>
			<?php  
            $objPHPExcel = new PHPExcel();

            $styleArray = array(
		        'font' => array(
		            'size'  => 10,
                    'name'  => 'Arial'
		        ),
		        'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        )
		    );

		    $styleArray1 = array(
		        'font' => array(
		            'bold' => true,
		            'size'  => 12,
                    'name'  => 'Arial'
		        ),
		        'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		        )
		    );

		    $styleArray2 = array(
		        'font' => array(
		            'bold' => true,
		            'size'  => 12,
                    'name'  => 'Arial'
		        ),
		        'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
		        )
		    );

		    $styleArray3 = array(
		        'font' => array(
		            'size'  => 10,
                    'name'  => 'Arial'
		        ),
		        'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
		        )
		    );

		    $styleArray4 = array(
		        'font' => array(
		            'size'  => 10,
                    'name'  => 'Arial'
		        ),
		        'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
		        )
		    );
		    $borderStyle = array(
				    'borders' => array(
				        'outline' => array(
				            'style' => PHPExcel_Style_Border::BORDER_THICK,
				            'color' => array('argb' => '000000'),
				        ),
				    ),
				);

            $company_name = $companyInfo['company_name'];
            $project_name = $projectInfo['project_name'];
            $company_logo = $companyInfo['company_logo_image'];


		    //First Line
		    $sheet = $objPHPExcel->getActiveSheet();
		    $imageExist =  ROOT_PATH.'/uploads/user/'.$company_logo;
		      

		    if(!empty($company_logo) && file_exists($imageExist)){
			    $sheet->mergeCells('A1:C3');
			    $objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Logo');
				$objDrawing->setDescription('Logo');
				$objDrawing->setPath('./uploads/user/'.$company_logo);
				$objDrawing->setHeight(36);
				$objDrawing->setOffsetX(20);
				$objDrawing->setOffsetY(10);
				$objDrawing->setWorksheet($sheet);
				$objDrawing->setCoordinates('A1');
			}

			$sheet->getStyle('A1:C3')->applyFromArray($borderStyle);

		    $sheet->mergeCells('D1:H3');
		    $sheet->setCellValue('D1',$company_name."\n".$project_name);
		    $objPHPExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setWrapText(true);
		    $sheet->getStyle('D1')->applyFromArray($styleArray1);
		    $sheet->getStyle('D1:H3')->applyFromArray($borderStyle);

		    $sheet->mergeCells('I1:L3');
            $objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Logo');
			$objDrawing->setDescription('Logo');
			$objDrawing->setPath('./assets/admin/images/aasaan-footer-logo.jpg');
			$objDrawing->setHeight(36);
			$objDrawing->setOffsetX(30);
			$objDrawing->setOffsetY(10);
			$objDrawing->setWorksheet($sheet);
			$objDrawing->setCoordinates('I1');
			$sheet->getStyle('I1:L3')->applyFromArray($borderStyle);
		?>

    
		 <!-- </table> -->
		 
		<!-- <table cellspacing="0" border="0" style="width:100%">   -->
			<tr>
				<td style="border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=12 align="center" valign=bottom>
					<b>
						<font size=4 color="#000000">Salary register &nbsp; <?php echo $this->data['month'][$postData['month']] .', '. $postData['year']; ?></font>
					</b>
				</td>
			</tr>
            
            <?php
			//Second Line
			$a = $this->data['month'][$postData['month']] .', '. $postData['year'];
		    $sheet->mergeCells('A4:L4');
		    $sheet->setCellValue('A4','Salary Register '.$a.'');
		    $sheet->getStyle('A4')->applyFromArray($styleArray1);
		    $sheet->getStyle('A4:L4')->applyFromArray($borderStyle);


		    ?>

			<tr>
				<td style="border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" height="45" align="center" valign=middle><font color="#000000">Sr. no.</font></td>
				<td style="border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000"> Name of Worker</font></td>
				<td style="border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" colspan="5" valign=middle><font color="#000000">Hajiri Entries</font></td>
				<td style="border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000">Hajiri Rate</font></td>
				<td style="border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000">Total Earning</font></td>
				<td style="border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000">Advance <br /> Payments</font></td>
				<td style="border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000">Net <br />Payment</font></td>
				<td style="border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle><font color="#000000">Remarks/<br />Signature</font></td>
			</tr>

			<?php
			//Third Line
		    $sheet->mergeCells('A5:A6');
		    $sheet->setCellValue('A5','Sr. No');
		    $sheet->getStyle('A5')->applyFromArray($styleArray);
		    $sheet->getStyle('A5')->getAlignment()->setWrapText(true);
		    $sheet->getStyle('A5:A6')->applyFromArray($borderStyle);


		    $sheet->mergeCells('B5:B6');
		    $sheet->setCellValue('B5','Name of Worker');
		    $sheet->getStyle('B5')->applyFromArray($styleArray);
		    $sheet->getStyle('B5')->getAlignment()->setWrapText(true);
		    $sheet->getColumnDimension('B')->setAutoSize(TRUE);
		    $sheet->getStyle('B5:B6')->applyFromArray($borderStyle);

			$sheet->mergeCells('C5:G6');
		    $sheet->setCellValue('C5','Hajiri Entries');
		    $sheet->getStyle('C5')->applyFromArray($styleArray);
		    $sheet->getStyle('C5')->getAlignment()->setWrapText(true);
		    $sheet->getStyle('C5:G6')->applyFromArray($borderStyle);


		    $sheet->mergeCells('H5:H6');
		    $sheet->setCellValue('H5','Hajiri Rate');
		    $sheet->getStyle('H5')->applyFromArray($styleArray);
		    $sheet->getStyle('H5')->getAlignment()->setWrapText(true);
		    $sheet->getStyle('H5:H6')->applyFromArray($borderStyle);


		    $sheet->mergeCells('I5:I6');
		    $sheet->setCellValue('I5','Total Earning');
		    $sheet->getStyle('I5')->applyFromArray($styleArray);
		    $sheet->getStyle('I5')->getAlignment()->setWrapText(true);
		    $sheet->getStyle('I5:I6')->applyFromArray($borderStyle);


		    $sheet->mergeCells('J5:J6');
		    $sheet->setCellValue('J5','Advance Payments');
		    $sheet->getStyle('J5')->applyFromArray($styleArray);
		    $sheet->getStyle('J5')->getAlignment()->setWrapText(true);
		    $sheet->getStyle('J5:J6')->applyFromArray($borderStyle);


		    $sheet->mergeCells('K5:K6');
		    $sheet->setCellValue('K5','Net Payment');
		    $sheet->getStyle('K5')->applyFromArray($styleArray);
		    $sheet->getStyle('K5')->getAlignment()->setWrapText(true);
		    $sheet->getStyle('K5:K6')->applyFromArray($borderStyle);


		    $sheet->mergeCells('L5:L6');
		    $sheet->setCellValue('L5','Remarks/Signature');
		    $sheet->getStyle('L5')->applyFromArray($styleArray);
		    $sheet->getStyle('L5')->getAlignment()->setWrapText(true);
		    $sheet->getStyle('L5:L6')->applyFromArray($borderStyle);

		    ?>


			<tr>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" height="39" align="center" valign=middle><font color="#000000"><br></font></td>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="left" valign=middle><font color="#000000"><br></font></td>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000">Present days</font></td>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000">Absent days</font></td>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000">Overtime</font></td>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000">Paid leaves</font></td>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000">Total Hajiri</font></td>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000"><br></font></td>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000"><br></font></td>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000"><br></font></td>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000"><br></font></td>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle><font color="#000000"><br></font></td>
			</tr>

			<?php
			$sheet->mergeCells('A7:A8');
		    $sheet->setCellValue('A7','');
		    $sheet->getStyle('A7:A8')->applyFromArray($borderStyle);

			$sheet->mergeCells('B7:B8');
		    $sheet->setCellValue('B7','');
		    $sheet->getStyle('B7:B8')->applyFromArray($borderStyle);

		    $sheet->mergeCells('C7:C8');
		    $sheet->setCellValue('C7','Present days');
		    $sheet->getStyle('C7')->applyFromArray($styleArray);
		    $sheet->getStyle('C7')->getAlignment()->setWrapText(true);
		    $sheet->getStyle('C7:C8')->applyFromArray($borderStyle);

		    $sheet->mergeCells('D7:D8');
		    $sheet->setCellValue('D7','Absent days');
		    $sheet->getStyle('D7')->applyFromArray($styleArray);
		    $sheet->getStyle('D7')->getAlignment()->setWrapText(true);
		    $sheet->getStyle('D7:D8')->applyFromArray($borderStyle);

		    $sheet->mergeCells('E7:E8');
		    $sheet->setCellValue('E7','Overtime');
		    $sheet->getStyle('E7')->applyFromArray($styleArray);
		    $sheet->getStyle('E7')->getAlignment()->setWrapText(true);
		    $sheet->getStyle('E7:E8')->applyFromArray($borderStyle);

		    $sheet->mergeCells('F7:F8');
		    $sheet->setCellValue('F7','Paid leaves');
		    $sheet->getStyle('F7')->applyFromArray($styleArray);
		    $sheet->getStyle('F7')->getAlignment()->setWrapText(true);
		    $sheet->getStyle('F7:F8')->applyFromArray($borderStyle);

			$sheet->mergeCells('G7:G8');
		    $sheet->setCellValue('G7','Total Hajiri');
		    $sheet->getStyle('G7')->applyFromArray($styleArray);
		    $sheet->getStyle('G7')->getAlignment()->setWrapText(true);

		    $sheet->getStyle('G7:G8')->applyFromArray($borderStyle);

		    $sheet->mergeCells('H7:H8');
		    $sheet->setCellValue('H7','');
		    $sheet->getStyle('H7:H8')->applyFromArray($borderStyle);

		    $sheet->mergeCells('I7:I8');
		    $sheet->setCellValue('I7','');
		    $sheet->getStyle('I7:I8')->applyFromArray($borderStyle);

		    $sheet->mergeCells('J7:J8');
		    $sheet->setCellValue('J7','');
		    $sheet->getStyle('J7:J8')->applyFromArray($borderStyle);

		    $sheet->mergeCells('K7:K8');
		    $sheet->setCellValue('K7','');
		    $sheet->getStyle('K7:K8')->applyFromArray($borderStyle);

		    $sheet->mergeCells('L7:L8');
		    $sheet->setCellValue('L7','');
		    $sheet->getStyle('L7:L8')->applyFromArray($borderStyle);
		?>

			<?php
				$srNo = 1;
				$total_expenses = 0;
				$categoryData = array();
				$excel_row = 9;
				
				foreach( $workerData as $id => $worker ){ 
					$absent_days = '';
					$advancePayment = isset($worker['advance_payment'])?$worker['advance_payment']:'';

					$absent_days = $noOfDays - $worker['week_offs'] - ( $worker['present_days'] + $worker['paid_leaves'] ) ;

					if( $worker['worker_wage_type'] == 1 ){
						
						$key = array_search( $id , array_column($getMaxAttendance, 'worker_id'));
						$getMaxAttendance[$key]['project_id'];
						
					if(isset($key) && $postData['project_id'] == $getMaxAttendance[$key]['project_id']){
						$count_holidays = 0;
						if(isset($holidays[0]) && !empty($holidays[0])){
								$count_holidays = count($holidays);
						}
						
						$worker['total_hajiri'] = $worker['week_offs'] + $count_holidays + $worker['total_hajiri'];

						$absent_days = $noOfDays - $worker['week_offs'] - $count_holidays -( $worker['present_days'] + $worker['paid_leaves']  ) ;
					}
					
						// $worker['total_hajiri'] += $worker['week_offs']; 
						$dailywage = $worker['worker_wage'] / $workingdays; 

						$worker['total_earning'] = round($worker['total_hajiri'] * $dailywage);

						// if( $worker['week_offs'] > 0 ) {

						// 	$days = $noOfDays - $worker['week_offs'];
						// 	// $worker['total_earning'] += round(($worker['worker_wage'] / $days)); 
						// 	$worker['total_earning'] = $worker['total_hajiri']	* round(($worker['worker_wage'] / $days));
						// 	// $worker['total_earning'] += ( $worker['week_offs'] * $worker['worker_wage'] );
						// }
					}
					$total_expenses += $worker['total_earning'];
					
					$category_name =  $worker['category_name'];
					if( isset( $categoryData[$category_name] ) ){
						$categoryData[$category_name] += $worker['total_earning'];
					} else {
						$categoryData[$category_name] = $worker['total_earning'];
					} 


			?>

			<tr>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" height="18" align="center" valign=middle><font color="#000000"><?php echo $srNo; ?></font></td>

				<td width="20%" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="left" valign=middle><font color="#000000"><?php echo $worker['labour_name'].' '.$worker['labour_last_name']; ?><br /><?php echo $category_name; ?></font></td>

				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000"><?php echo $worker['present_days']; ?></font></td>

				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000"><?php echo $absent_days; ?></font></td>
				<?php 
					if($worker['overtime'] > 0) $overtime_format = number_format($worker['overtime'] , 2); 
					else $overtime_format = $worker['overtime']; ?>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000"><?php echo $overtime_format; ?></font></td>

				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000"><?php echo $worker['paid_leaves']; ?></font></td>
				<?php 
					if(fmod($worker['total_hajiri'], 1) !== 0.00) $total_hajiri_format = number_format($worker['total_hajiri'], 2); else $total_hajiri_format = $worker['total_hajiri'];

					// if($worker['total_hajiri'] > 0) $total_hajiri_format = number_format($worker['total_hajiri'], 2);
					// else $total_hajiri_format = $worker['total_hajiri'];
				?>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000"><?php echo $total_hajiri_format; ?></font></td>

				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000"><?php echo $worker['worker_wage']; ?></font></td>

				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000"><?php echo round($worker['total_earning']); ?></font></td>

				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000"><?php echo $advancePayment; ?></font></td>

				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle><font color="#000000"><?php echo round($worker['total_earning'] - $advancePayment); ?></font></td>

				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle><font color="#000000"><br></font></td>

			</tr>

			<?php
            
            $total_earning = round($worker['total_earning']);

            $final_payment = round($worker['total_earning'] - $advancePayment);

   		    $sheet->setCellValueByColumnAndRow(0, $excel_row, $srNo);
   		    $sheet->getDefaultStyle()->applyFromArray($styleArray);
   		    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(0) . ($excel_row))->applyFromArray($borderStyle);

   		    $sheet->setCellValueByColumnAndRow(1, $excel_row, $worker['labour_name'].' '.$worker['labour_last_name']."\n".$category_name);

   		    $sheet->getRowDimension($excel_row)->setRowHeight(40);

   		    $sheet->getStyle('B'.$excel_row)->applyFromArray($borderStyle);

   		    $sheet->getStyle('B'.$excel_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('B'.$excel_row)->getAlignment()->setWrapText(true);
            $sheet->setCellValueByColumnAndRow(2, $excel_row, $worker['present_days']);
            $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . ($excel_row))->applyFromArray($borderStyle);

   		    $sheet->setCellValueByColumnAndRow(3, $excel_row, $absent_days);
   		    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . ($excel_row))->applyFromArray($borderStyle);

   		    $sheet->setCellValueByColumnAndRow(4, $excel_row, $overtime_format);
   		    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(4) . ($excel_row))->applyFromArray($borderStyle);

   		    $sheet->setCellValueByColumnAndRow(5, $excel_row, $worker['paid_leaves']);
   		    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(5) . ($excel_row))->applyFromArray($borderStyle);

   		    $sheet->setCellValueByColumnAndRow(6, $excel_row, $total_hajiri_format);$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(6) . ($excel_row))->applyFromArray($borderStyle);

   		    $sheet->setCellValueByColumnAndRow(7, $excel_row, $worker['worker_wage']);
   		    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(7) . ($excel_row))->applyFromArray($borderStyle);

   		    $sheet->setCellValueByColumnAndRow(8, $excel_row, $total_earning);
   		    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(8) . ($excel_row))->applyFromArray($borderStyle);

   		    $sheet->setCellValueByColumnAndRow(9, $excel_row, $advancePayment);
   		    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(9) . ($excel_row))->applyFromArray($borderStyle);

   		    $sheet->setCellValueByColumnAndRow(10, $excel_row, $final_payment);
   		    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(10) . ($excel_row))->applyFromArray($borderStyle);

   		    $sheet->setCellValueByColumnAndRow(11, $excel_row, '');
   		    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(11) . ($excel_row))->applyFromArray($borderStyle);


			$srNo++;
			$excel_row++;
			}  
			?>
		</table>
		<table cellspacing="0" border="<?php echo $border; ?>" style="width:100%;">
			<tr><th style="border-top: 2px solid #000000; border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000" colspan="16" height="25" align="left" valign=bottom><b><u><font size=3 color="#000000">Payment details:</font></u></b></th>
			</tr>

			<?php
			$sheet->mergeCells('A'.$excel_row.':L'.$excel_row.'');
		    $sheet->setCellValue('A'.$excel_row,'');
		    $excel_row++;
			$sheet->mergeCells('A'.$excel_row.':L'.$excel_row.'');
		    $sheet->setCellValue('A'.$excel_row,'Payment Details');
		    $sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray2);
		    $sheet->getStyle('A'.$excel_row.':L'.$excel_row.'')->applyFromArray($borderStyle);
		    $excel_row++;
		    ?>

			<tr>

				<th colspan="2" style="border-bottom: 2px solid #000000; border-left: 2px solid #000000;">
					<font color="#000000">Date</font>
				</th>
				<th colspan="2" style="border-bottom: 2px solid #000000;" align="left">
					<font color="#000000">Amount</font>
				</th>
				<th colspan="2" style="border-bottom: 2px solid #000000; border-left: 2px solid #000000;">
					<font color="#000000">Date</font>
				</th>
				<th colspan="2" style="border-bottom: 2px solid #000000;" align="left">
					<font color="#000000">Amount</font>
				</th>
				<th colspan="2" style="border-bottom: 2px solid #000000; border-left: 2px solid #000000;">
					<font color="#000000">Date</font>
				</th>
				<th colspan="2" style="border-bottom: 2px solid #000000;" align="left">
					<font color="#000000">Amount</font>
				</th>
				<th colspan="2" style="border-bottom: 2px solid #000000; border-left: 2px solid #000000;">
					<font color="#000000">Date</font>
				</th>
				<th colspan="2" style="border-bottom: 2px solid #000000; border-right: 2px solid #000000;" align="left">
					<font color="#000000">Amount</font>
				</th>
			</tr>

			<?php
		    $sheet->setCellValue('A'.$excel_row.'','Date');
		    $sheet->getStyle('A'.$excel_row)->getFont()->setBold(true);
		    $sheet->getStyle('A'.$excel_row)->applyFromArray($borderStyle);



		    $sheet->mergeCells('B'.$excel_row.':C'.$excel_row.'');
		    $sheet->setCellValue('B'.$excel_row.'','Amount');
		    $sheet->getStyle('B'.$excel_row)->getFont()->setBold(true);
		    $sheet->getStyle('B'.$excel_row.':C'.$excel_row.'')->applyFromArray($borderStyle);

            $sheet->setCellValue('D'.$excel_row.'','Date');
            $sheet->getStyle('D'.$excel_row)->getFont()->setBold(true);
            $sheet->getStyle('D'.$excel_row)->applyFromArray($borderStyle);

			$sheet->mergeCells('E'.$excel_row.':F'.$excel_row.'');
		    $sheet->setCellValue('E'.$excel_row.'','Amount');
		    $sheet->getStyle('E'.$excel_row)->getFont()->setBold(true);
		    $sheet->getStyle('E'.$excel_row.':F'.$excel_row.'')->applyFromArray($borderStyle);

		    $sheet->setCellValue('G'.$excel_row.'','Date');
		    $sheet->getStyle('G'.$excel_row)->getFont()->setBold(true);
		    $sheet->getStyle('G'.$excel_row)->applyFromArray($borderStyle);


		    $sheet->mergeCells('H'.$excel_row.':I'.$excel_row.'');
		    $sheet->setCellValue('H'.$excel_row.'','Amount');
		    $sheet->getStyle('H'.$excel_row)->getFont()->setBold(true);
		    $sheet->getStyle('H'.$excel_row.':I'.$excel_row.'')->applyFromArray($borderStyle);

			$sheet->setCellValue('J'.$excel_row.'','Date');
		    $sheet->getStyle('J'.$excel_row)->getFont()->setBold(true);
		    $sheet->getStyle('J'.$excel_row)->applyFromArray($borderStyle);


		    $sheet->mergeCells('K'.$excel_row.':L'.$excel_row.'');
		    $sheet->setCellValue('K'.$excel_row.'','Amount');
		    $sheet->getStyle('K'.$excel_row)->getFont()->setBold(true);
		    $sheet->getStyle('K'.$excel_row.':L'.$excel_row.'')->applyFromArray($borderStyle);
		    $excel_row++;
		    ?>
		    
			<?php 
				if( isset($paymentData['payment'] )) {
					$payment_arrays = array_chunk($paymentData['payment'], 4, true);

					foreach( $payment_arrays as $payments ) {
						$arrayCount = count( $payments );
						if( $arrayCount < 4 ){
							$arrayCount = 4 - $arrayCount;
							$payments += array_fill( 32, $arrayCount, '');
						}
			?>
					<tr>
						<?php 
						$i = 0;
						foreach( $payments as $payment_day => $payment ) { ?>
							<td colspan="2" style="border-bottom: 2px solid #000000; border-left: 2px solid #000000; width: 25%;" height="20" align="left" valign=bottom><font color="#000000">
								<?php echo $payment_day < 32 ? $payment_day.'-'.$postData['month'].'-'.$postData['year'] : ''; ?></font>
							</td>
							<td colspan="2" style="border-bottom: 2px solid #000000; border-right: 2px solid #000000; width: 25%;" align="left" valign=bottom ><b><font face="Noto Sans Devanagari" color="#000000"><?php echo $payment ? 'Rs. '.$payment : ''; ?></font></b>
							</td>

							<?php
							$final_date = $final_payment = '';
							$final_date = $payment_day < 32 ? $payment_day.'-'.$postData['month'].'-'.$postData['year'] : '';
							$final_payment = $payment ? 'Rs.'.$payment : '';
							switch ($i) 
							{
						    case 0:
						         $sheet->setCellValue('A'.$excel_row.'',$final_date);
						         $sheet->getStyle('A'.$excel_row)->applyFromArray($borderStyle);

						         $sheet->mergeCells('B'.$excel_row.':C'.$excel_row.'');
				                 $sheet->setCellValue('B'.$excel_row.'',$final_payment);
				                 $sheet->getStyle('B'.$excel_row)->getFont()->setBold(true);
				                 $sheet->getStyle('B'.$excel_row.':C'.$excel_row.'')->applyFromArray($borderStyle);
						         break;
						    case 1:
						        $sheet->setCellValue('D'.$excel_row.'',$final_date);
						        $sheet->getStyle('D'.$excel_row)->applyFromArray($borderStyle);
						        $sheet->mergeCells('E'.$excel_row.':F'.$excel_row.'');
				                $sheet->setCellValue('E'.$excel_row.'',$final_payment);
				                $sheet->getStyle('E'.$excel_row)->getFont()->setBold(true);
				                $sheet->getStyle('E'.$excel_row.':F'.$excel_row.'')->applyFromArray($borderStyle);

				                break;
						    case 2:
						        $sheet->setCellValue('G'.$excel_row.'',$final_date);
						        $sheet->getStyle('G'.$excel_row)->applyFromArray($borderStyle);
						        $sheet->mergeCells('H'.$excel_row.':I'.$excel_row.'');
				                $sheet->setCellValue('H'.$excel_row.'',$final_payment);
				                $sheet->getStyle('H'.$excel_row)->getFont()->setBold(true);
				                $sheet->getStyle('H'.$excel_row.':I'.$excel_row.'')->applyFromArray($borderStyle);
						        break;
				            case 3:
						        $sheet->setCellValue('J'.$excel_row.'',$final_date);
						        $sheet->getStyle('J'.$excel_row)->applyFromArray($borderStyle);
						        $sheet->mergeCells('K'.$excel_row.':L'.$excel_row.'');
				                $sheet->setCellValue('K'.$excel_row.'',$final_payment);
				                $sheet->getStyle('K'.$excel_row)->getFont()->setBold(true);
				                $sheet->getStyle('K'.$excel_row.':L'.$excel_row.'')->applyFromArray($borderStyle);
				                break;
						    default:
						        
						    } 						    
						$i++;
                        }?>
					</tr>

					<?php
					$excel_row++;
					?>

					


			<?php 	}
				}
			?>
		</table>
		<table cellspacing="0" border="<?php echo $border; ?>" style="width: 100%;">
			<!-- <tr>
				<td style="border-top: 2px solid #000000; border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=14 height="25" align="left" valign=bottom><b><u><font size=3 color="#000000">Payment details:</font></u></b></td>
			</tr> -->
			<!-- <tr>
				<td style="border-bottom: 2px solid #000000; border-left: 2px solid #000000; width: 12.5%;" width="12.5%"  height="20" align="left" valign=bottom><font color="#000000">Date</font></td>
				<td style="border-bottom: 2px solid #000000; border-right: 2px solid #000000; width: 12.5%;" align="left" valign=bottom ><font color="#000000">Amount</font></td>
				<td style="border-bottom: 2px solid #000000; border-left: 2px solid #000000; width: 12.5%;" width="12.5%" height="20" align="left" valign=bottom><font color="#000000">Date</font></td>
				<td style="border-bottom: 2px solid #000000; border-right: 2px solid #000000; width: 12.5%;" align="left" valign=bottom ><font color="#000000">Amount</font></td>
				<td style="border-bottom: 2px solid #000000; border-left: 2px solid #000000; width: 12.5%;" width="12.5%" height="20" align="left" valign=bottom><font color="#000000">Date</font></td>
				<td style="border-bottom: 2px solid #000000; border-right: 2px solid #000000; width: 12.5%;" align="left" valign=bottom ><font color="#000000">Amount</font></td>
				<td style="border-bottom: 2px solid #000000; border-left: 2px solid #000000; width: 12.5%;" width="12.5%" height="20" align="left" valign=bottom><font color="#000000">Date</font></td>
				<td style="border-bottom: 2px solid #000000; border-right: 2px solid #000000; width: 12.5%;" align="left" valign=bottom ><font color="#000000">Amount</font></td>
			</tr> -->
			<?php 
				/*if( $paymentData['payment'] ) {

					$payment_arrays = array_chunk($paymentData['payment'], 4, true);
					foreach( $payment_arrays as $payments ) {
						$arrayCount = count( $payments );
						if( $arrayCount < 4 ){
							$arrayCount = 4 - $arrayCount;
							$payments += array_fill( 32, $arrayCount, '');
						}
			?>
					<tr>
						<?php foreach( $payments as $payment_day => $payment ) { ?>
							<td style="border-bottom: 2px solid #000000; border-left: 2px solid #000000; width: 12.5%;" width="12.5%" height="20" align="left" valign=bottom><font color="#000000">
								<?php echo $payment_day < 32 ? $payment_day.'-'.$postData['month'].'-'.$postData['year'] : ''; ?></font></td>
							<td style="border-bottom: 2px solid #000000; border-right: 2px solid #000000; width: 12.5%;" align="left" valign=bottom ><b><font face="Noto Sans Devanagari" color="#000000"><?php echo $payment ? 'Rs. '.$payment : ''; ?></font></b></td>
						<?php }?>
					</tr>
			<?php 	}
				} */
			?>
			<tr>
				<td style="border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=16 height="25" align="left" valign=bottom><b><u><font size=3 color="#000000">Cost of worker:</font></u></b></td>
			</tr>

			<?php
			$sheet->mergeCells('A'.$excel_row.':L'.$excel_row.'');
		    $sheet->setCellValue('A'.$excel_row,'');
		    $excel_row++;
			$sheet->mergeCells('A'.$excel_row.':L'.$excel_row.'');
		    $sheet->setCellValue('A'.$excel_row,'Cost Of Worker');
		    $sheet->getStyle('A'.$excel_row)->applyFromArray($styleArray2);
		    $sheet->getStyle('A'.$excel_row.':L'.$excel_row.'')->applyFromArray($borderStyle);
		    $excel_row++;
		    ?>

			<tr>
				<th colspan="2" style="border-bottom: 2px solid #000000; border-left: 2px solid #000000" colspan=2 height="25" align="left" valign=bottom><font color="#000000">Category</font></th>
				<th colspan="2" style="border-bottom: 2px solid #000000; border-right: 2px solid #000000" align="left" valign=bottom><font color="#000000">Amount</font></th>
				<th colspan="2" style="border-bottom: 2px solid #000000; border-left: 2px solid #000000" colspan=2 height="25" align="left" valign=bottom><font color="#000000">Category</font></th>
				<th colspan="2" style="border-bottom: 2px solid #000000; border-right: 2px solid #000000" align="left" valign=bottom><font color="#000000">Amount</font></th>
				<th colspan="2" style="border-bottom: 2px solid #000000; border-left: 2px solid #000000" colspan=2 height="25" align="left" valign=bottom><font color="#000000">Category</font></th>
				<th colspan="2" style="border-bottom: 2px solid #000000; border-right: 2px solid #000000" align="left" valign=bottom><font color="#000000">Amount</font></th>
				<th colspan="2" style="border-bottom: 2px solid #000000; border-left: 2px solid #000000" colspan=2 height="25" align="left" valign=bottom><font color="#000000">Category</font></th>
				<th colspan="2" style="border-bottom: 2px solid #000000; border-right: 2px solid #000000" align="left" valign=bottom><font color="#000000">Amount</font></th>
			</tr>

			<?php
		    $sheet->setCellValue('A'.$excel_row.'','Category');
		    $sheet->getStyle('A'.$excel_row)->getFont()->setBold(true);
		    $sheet->getStyle('A'.$excel_row)->applyFromArray($borderStyle);

		    $sheet->mergeCells('B'.$excel_row.':C'.$excel_row.'');
		    $sheet->setCellValue('B'.$excel_row.'','Amount');
		    $sheet->getStyle('B'.$excel_row)->getFont()->setBold(true);
		    $sheet->getStyle('B'.$excel_row.':C'.$excel_row.'')->applyFromArray($borderStyle);

            $sheet->setCellValue('D'.$excel_row.'','Category');
            $sheet->getStyle('D'.$excel_row)->getFont()->setBold(true);
            $sheet->getStyle('D'.$excel_row)->applyFromArray($borderStyle);

		    $sheet->mergeCells('E'.$excel_row.':F'.$excel_row.'');
		    $sheet->setCellValue('E'.$excel_row.'','Amount');
		    $sheet->getStyle('E'.$excel_row)->getFont()->setBold(true);
		    $sheet->getStyle('E'.$excel_row.':F'.$excel_row.'')->applyFromArray($borderStyle);

		    $sheet->setCellValue('G'.$excel_row.'','Category');
		    $sheet->getStyle('G'.$excel_row)->getFont()->setBold(true);
		    $sheet->getStyle('G'.$excel_row)->applyFromArray($borderStyle);

		    $sheet->mergeCells('H'.$excel_row.':I'.$excel_row.'');
		    $sheet->setCellValue('H'.$excel_row.'','Amount');
		    $sheet->getStyle('H'.$excel_row)->getFont()->setBold(true);
		    $sheet->getStyle('H'.$excel_row.':I'.$excel_row.'')->applyFromArray($borderStyle);

		    $sheet->setCellValue('J'.$excel_row.'','Category');
		    $sheet->getStyle('J'.$excel_row)->getFont()->setBold(true);
		    $sheet->getStyle('J'.$excel_row)->applyFromArray($borderStyle);
		    
		    $sheet->mergeCells('K'.$excel_row.':L'.$excel_row.'');
		    $sheet->setCellValue('K'.$excel_row.'','Amount');
		    $sheet->getStyle('K'.$excel_row)->getFont()->setBold(true);
		    $sheet->getStyle('K'.$excel_row.':L'.$excel_row.'')->applyFromArray($borderStyle);
		    $excel_row++;
		    ?>

			<?php  
				if( $categoryData ) {

					$category_arrays = array_chunk($categoryData, 4, true);
					foreach( $category_arrays as $categories ) {
						$arrayCount = count( $categories );
						if( $arrayCount < 4 ){
							$arrayCount = 4 - $arrayCount;
							$categories += array_fill( 1, $arrayCount, '');
						}
			?>
			<tr>
				<?php 
				$i = 0;
				foreach( $categories as $category => $payment ) { ?>
					<td colspan="2" style="border-bottom: 2px solid #000000; border-left: 2px solid #000000" colspan=2 height="20" align="left" valign=bottom><font color="#000000"> <?php echo !( $category > 0) ? $category : ''; ?></font></td>
					<td colspan="2" style="border-bottom: 2px solid #000000; border-right: 2px solid #000000" align="left" valign=bottom ><b><font face="Noto Sans Devanagari" color="#000000"><?php echo $payment ? 'Rs. '.number_format($payment , 2) : ''; ?></font></b></td>
				<?php 

				$sheet->setCellValueByColumnAndRow($i, $excel_row, $category);
				$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($i) . ($excel_row))->applyFromArray($borderStyle);
				if($payment > 0) $payment = number_format($payment , 2);

				switch ($i) {
				    case "0":
				         $sheet->mergeCells('B'.$excel_row.':C'.$excel_row.'');
		                 $sheet->setCellValue('B'.$excel_row.'',$payment);
		                 $sheet->getStyle('B'.$excel_row)->getFont()->setBold(true);
		                 $sheet->getStyle('B'.$excel_row.':C'.$excel_row.'')->applyFromArray($borderStyle);
				    case "3":
				        $sheet->mergeCells('E'.$excel_row.':F'.$excel_row.'');
		                $sheet->setCellValue('E'.$excel_row.'',$payment);
		                $sheet->getStyle('E'.$excel_row)->getFont()->setBold(true);
		                $sheet->getStyle('E'.$excel_row.':F'.$excel_row.'')->applyFromArray($borderStyle);
				    case "6":
				        $sheet->mergeCells('H'.$excel_row.':I'.$excel_row.'');
		                $sheet->setCellValue('H'.$excel_row.'',$payment);
		                $sheet->getStyle('H'.$excel_row)->getFont()->setBold(true);
		                $sheet->getStyle('H'.$excel_row.':I'.$excel_row.'')->applyFromArray($borderStyle);
		            case "9":
				        $sheet->mergeCells('K'.$excel_row.':L'.$excel_row.'');
		                $sheet->setCellValue('K'.$excel_row.'',$payment);
		                $sheet->getStyle('K'.$excel_row)->getFont()->setBold(true);
		                $sheet->getStyle('K'.$excel_row.':L'.$excel_row.'')->applyFromArray($borderStyle);
				    default:
				}

				$i= $i + 3 ;			    

			    }
			    $col=0;
			    $excel_row++;
			    ?>
			</tr>

			<?php 	}
				}
			?>
			<tr>
				<td style="border-left: 2px solid #000000; border-bottom: 1px solid #000000" colspan=12 height="20" align="right" valign=bottom></td>
				<td style="border-right: 2px solid #000000;  border-bottom: 1px solid #000000;" colspan=4 align="left" valign=bottom sdval="102550"  ></td>
			</tr>

			<?php
			$sheet->mergeCells('A'.$excel_row.':L'.$excel_row.'');
		    $sheet->setCellValue('A'.$excel_row.'','');
		    $excel_row++;
		    ?>

			<tr>
				<td style="border-left: 2px solid #000000; border-bottom: 1px solid #000000; border-right: 1px solid #000000" colspan=12 height="25" align="right" valign=bottom><font size=3 color="#000000">Opening balance</font></td>
				<td style="border-right: 2px solid #000000; border-bottom: 1px solid #000000" colspan=4 align="left" valign=bottom sdval="102550"  ><b><font face="Noto Sans Devanagari" color="#000000">Rs. <?php echo $opening_balance; ?></font></b></td>
			</tr>
            
            <?php
			$sheet->mergeCells('A'.$excel_row.':I'.$excel_row.'');
		    $sheet->setCellValue('A'.$excel_row.'','Opening balance');
		    $sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray3);
		    $sheet->getStyle('A'.$excel_row.':I'.$excel_row.'')->applyFromArray($borderStyle);
		    $sheet->mergeCells('J'.$excel_row.':L'.$excel_row.'');
		    $sheet->setCellValue('J'.$excel_row.'','Rs. '.$opening_balance);
		    $sheet->getStyle('J'.$excel_row.'')->applyFromArray($styleArray4);
		    $sheet->getStyle('J'.$excel_row.'')->getFont()->setBold(true);
		    $sheet->getStyle('J'.$excel_row.':L'.$excel_row.'')->applyFromArray($borderStyle);

		    $excel_row++;
		    if($total_expenses > 0) $total_expenses_format = round($total_expenses);
		    else $total_expenses_format = $total_expenses;
		    ?>

			<tr>
				<td style="border-left: 2px solid #000000; border-bottom: 1px solid #000000; border-right: 1px solid #000000" colspan=12 height="25" align="right" valign=bottom><font size=3 color="#000000">Total expense</font></td>
				<td style="border-right: 2px solid #000000; border-bottom: 1px solid #000000" colspan=4 align="left" valign=bottom sdval="102550"  ><b><font face="Noto Sans Devanagari" color="#000000">Rs. <?php echo $total_expenses_format; ?></font></b></td>
			</tr>

			<?php

			$sheet->mergeCells('A'.$excel_row.':I'.$excel_row.'');
		    $sheet->setCellValue('A'.$excel_row.'','Total expense');
		    $sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray3);
		    $sheet->getStyle('A'.$excel_row.':I'.$excel_row.'')->applyFromArray($borderStyle);
		    $sheet->mergeCells('J'.$excel_row.':L'.$excel_row.'');
		    $sheet->setCellValue('J'.$excel_row.'','Rs. '.$total_expenses_format);
		    $sheet->getStyle('J'.$excel_row.'')->applyFromArray($styleArray4);
		    $sheet->getStyle('J'.$excel_row.'')->getFont()->setBold(true);
		    $sheet->getStyle('J'.$excel_row.':L'.$excel_row.'')->applyFromArray($borderStyle);
		    $excel_row++;
		    ?>

			<tr>
				<td style="border-left: 2px solid #000000; border-bottom: 1px solid #000000; border-right: 1px solid #000000" colspan=12 height="25" align="right" valign=bottom><font size=3 color="#000000">Total payment</font></td>
				<td style="border-right: 2px solid #000000; border-bottom: 1px solid #000000" colspan=4 align="left" valign=bottom sdval="102550"  ><b><font face="Noto Sans Devanagari" color="#000000">Rs. <?php echo $total_payment; ?></font></b></td>
			</tr>

			<?php
			$sheet->mergeCells('A'.$excel_row.':I'.$excel_row.'');
		    $sheet->setCellValue('A'.$excel_row.'','Total payment');
		    $sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray3);
		    $sheet->getStyle('A'.$excel_row.':I'.$excel_row.'')->applyFromArray($borderStyle);

		    $sheet->mergeCells('J'.$excel_row.':L'.$excel_row.'');
		    $sheet->setCellValue('J'.$excel_row.'','Rs. '.$total_payment);
		    $sheet->getStyle('J'.$excel_row.'')->applyFromArray($styleArray4);
		    $sheet->getStyle('J'.$excel_row.'')->getFont()->setBold(true);
		    $sheet->getStyle('J'.$excel_row.':L'.$excel_row.'')->applyFromArray($borderStyle);
		    $excel_row++;
		    ?>

			<tr>
				<td style="border-left: 2px solid #000000; border-bottom: 1px solid #000000; border-right: 1px solid #000000" colspan=12 height="25" align="right" valign=bottom><font size=3 color="#000000">Closing balance</font></td>
				<td style="border-right: 2px solid #000000; border-bottom: 1px solid #000000" colspan=4 align="left" valign=bottom sdval="102550"  ><b><font face="Noto Sans Devanagari" color="#000000">Rs. <?php echo (int)$total_expenses - (int)$total_payment; ?></font></b></td>
			</tr>

			<?php
			$closing_balance = (int)$total_expenses - (int)$total_payment;
			$sheet->mergeCells('A'.$excel_row.':I'.$excel_row.'');
		    $sheet->setCellValue('A'.$excel_row.'','Closing balance');
		    $sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray3);
		    $sheet->getStyle('A'.$excel_row.':I'.$excel_row.'')->applyFromArray($borderStyle);
		    $sheet->mergeCells('J'.$excel_row.':L'.$excel_row.'');
		    $sheet->setCellValue('J'.$excel_row.'','Rs. '.$closing_balance);
		    $sheet->getStyle('J'.$excel_row.'')->applyFromArray($styleArray4);
		    $sheet->getStyle('J'.$excel_row.'')->getFont()->setBold(true);
		    $sheet->getStyle('J'.$excel_row.':L'.$excel_row.'')->applyFromArray($borderStyle);
		    $excel_row++;
		    ?>

			<tr>
				<td style="border-left: 2px solid #000000; border-right: 1px solid #000000; border-bottom: 1px solid #000000 " colspan=12 height="25" align="right" valign=bottom><font size=3 color="#000000"></font></td>
				<td style="border-right: 2px solid #000000; font-size:18px; border-bottom: 1px solid #000000 " colspan=4 align="center" valign=bottom sdval="3" sdnum="1033;0;#,##0">
					<b>
						<font color="#000000"><br><br><br><br>
							<?php echo $this->session->userdata('name'); ?><br>
							<span style="font-size:12px">(self-attested)</span><br>
							<span style="font-size:15px"><?php echo $companyInfo['company_name']; ?></span>
						</font>
					</b>
				</td>			
			</tr>

			<?php
			$name = $this->session->userdata('name');
			$update_row = $excel_row + 4 ;
			$sheet->mergeCells('A'.$excel_row.':I'.$update_row.'');
		    $sheet->setCellValue('A'.$excel_row.'','');
		    $sheet->mergeCells('J'.$excel_row.':L'.$update_row.'');
		    $sheet->setCellValue('J'.$excel_row,$name."\n".'(self-attested)'."\n".$companyInfo['company_name']);
		    $sheet->getStyle('A'.$excel_row.':I'.$update_row.'')->applyFromArray($borderStyle);
		    $sheet->getStyle('J'.$excel_row.':L'.$update_row.'')->applyFromArray($borderStyle);
		    $sheet->getStyle('J'.$excel_row)->getAlignment()->setWrapText(true);
		    $sheet->getStyle('J'.$excel_row)->getFont()->setBold(true);
            $sheet->getStyle('J'.$excel_row.':L'.$excel_row.'')->getFont()->setSize(10);

            $update_row++;
		    ?>


			<tr>
				<td style="border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=16 align="left" valign=bottom sdnum="1033;16393;[$-4009]DD-MM-YYYY">
					<b><u><font size=3 color="#000000">Disclaimer:</font></u></b>
				</td>
			</tr>

			<?php
			$sheet->mergeCells('A'.$update_row.':L'.$update_row.'');
		    $sheet->setCellValue('A'.$update_row,'');
		    $update_row++;
			$sheet->mergeCells('A'.$update_row.':L'.$update_row.'');
		    $sheet->setCellValue('A'.$update_row,'Disclaimer');
		    $sheet->getStyle('A'.$update_row.':L'.$update_row.'')->applyFromArray($borderStyle);
		    $sheet->getStyle('A'.$update_row)->applyFromArray($styleArray2);
		    $sheet->getStyle('A'.$update_row)->getFont()->setUnderline(true);

		    $update_row++;
		    ?>


			<tr>
				<td style="border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=16 align="left" valign=bottom>
					<font size=1 color="#000000">1. This is an auto-generated report.</font>
				</td>
			</tr>

			<?php
			$sheet->mergeCells('A'.$update_row.':L'.$update_row.'');
		    $sheet->setCellValue('A'.$update_row.'','1. This is an auto-generated report.');
		    $sheet->getStyle('A'.$update_row.':L'.$update_row.'')->applyFromArray($borderStyle);
		    $sheet->getStyle('A'.$update_row.'')->applyFromArray($styleArray4);
		    $sheet->getStyle('A'.$update_row.'')->getFont()->setSize(8);

		    $update_row++;
		    ?>

			<tr>
				<td style="border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=16 align="left" valign=bottom>
					<font size=1 color="#000000">2. Aasaan does not hold any legal liability for any data generated through this report </font>
				</td>
			</tr>

			<?php
			$sheet->mergeCells('A'.$update_row.':L'.$update_row.'');
		    $sheet->setCellValue('A'.$update_row.'','2. Aasaan does not hold any legal liability for any data generated through this report');
		    $sheet->getStyle('A'.$update_row.':L'.$update_row.'')->applyFromArray($borderStyle);
		    $sheet->getStyle('A'.$update_row.'')->applyFromArray($styleArray4);
		    $sheet->getStyle('A'.$update_row.'')->getFont()->setSize(8);
		    $update_row++;
		    ?>

			<tr>
				<td style="border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=16 align="left" valign=bottom>
					<font size=1 color="#000000">3. The values are purely based on the app operations</font>
				</td>
			</tr>

			<?php
			$sheet->mergeCells('A'.$update_row.':L'.$update_row.'');
		    $sheet->setCellValue('A'.$update_row.'','3. The values are purely based on the app operations');
		    $sheet->getStyle('A'.$update_row.':L'.$update_row.'')->applyFromArray($borderStyle);
		    $sheet->getStyle('A'.$update_row.'')->applyFromArray($styleArray4);
		    $sheet->getStyle('A'.$update_row.'')->getFont()->setSize(8);
		    $update_row++;
		    ?>

			<tr>
				<td style="border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=16 align="right" valign=bottom>
					<font size=1 color="#000000">This report was generated at <?php echo date('H:i'); ?> hours on <?php echo date('d/m/Y'); ?> </font>
				</td>
			</tr>

			<?php
			$c_time = date('H:i');
			$c_date = date('d/m/Y');
			$sheet->mergeCells('A'.$update_row.':L'.$update_row.'');
		    $sheet->setCellValue('A'.$update_row.'','This report was generated at '.$c_time.' hours on '.$c_date.'');
		    $sheet->getStyle('A'.$update_row.':L'.$update_row.'')->applyFromArray($borderStyle);
		    $sheet->getStyle('A'.$update_row.'')->getFont()->setSize(8);
		    $sheet->getStyle('A'.$update_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $update_row++;
		    $sheet->setSelectedCells('D1:H3');
		    ?>

		</table>
		<?php 
        $contents = ob_get_contents();
        ob_end_clean();
        $month = date("F", mktime(0, 0, 0, $postData['month'] , 10));
        if($this->input->get_post('downloadformat') == 'pdf'){
	        $this->load->library('Dom_pdf');
	        $this->dompdf->load_html($contents);
	        $this->dompdf->render();
	        $this->dompdf->stream($month." payment report ".$projectInfo['project_name'], array("Attachment" => True));
	    } else{
        ob_end_clean();
        $filename =  $month." payment report ".$projectInfo['project_name'].'.xls';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		// header('Content-Disposition: attachment;filename="01simple.xlsx"');
		header('Content-Disposition: attachment;filename='.$filename);
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	    //   	$filename= $month." payment report ".$projectInfo['project_name'].'.xls';
			
			// header('Content-Type: application/vnd.ms-excel');
			// // header('Content-Disposition: attachment;filename=$file');
			// header('Content-Disposition: attachment;filename='.$filename);
			// header('Cache-Control: max-age=0');
			// // If you're serving to IE 9, then the following may be needed
			// header('Cache-Control: max-age=1');

			// // If you're serving to IE over SSL, then the following may be needed
			// header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			// header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
			// header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			// header ('Pragma: public'); // HTTP/1.0

			// echo $contents; die(); 
	    }
	}
	
	function kharchi_report() {
        $this->load->model('manager_model', 'manager');
       
		$data = $this->data;
        $data['supervisor'] = $this->foreman->get_activeForeman();
		//For Getting project
		$projects = array("" => "Select project ");
		if($this->session->userdata('user_designation') == 'admin')
			$project_results = $this->project->get_datatables();
		if($this->session->userdata('user_designation') == 'Supervisor'){
			$project_results = $this->foreman->get_project_foremanId( $this->session->userdata('id'));
		}
		foreach ($project_results as $key => $value) {
			$projects[$value->project_id] = $value->project_name;
		}
		$data['projects'] = $projects;
        $years = array("" => "Select year");
        foreach (range(2016, 2050) as $value) {
            $years[$value] = $value;
        }
        $data['years'] = $years;
        $months = array("" => "Select month", "1" => "January", "2" => "February", "3" => "March", "4" => "April",
            "5" => "May", "6" => "June", "7" => "July", "8" => "August",
            "9" => "September", "10" => "October", "11" => "November", "12" => "December",
        );
        $data['months'] = $months;
        $data['menu_title'] = 'Kharchi';
        $data['title'] = 'Kharchi Report';
        $data['description'] = '';
        $data['page'] = 'report/kharchi_report';
        if (isset($_REQUEST['submit'])) {
			$this->form_validation->set_data($this->input->get());
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('project', 'Project', 'trim|required|numeric');
            $this->form_validation->set_rules('month', 'Month', 'trim|required|numeric');
            $this->form_validation->set_rules('year', 'Year', 'trim|required|numeric');
            if ($this->form_validation->run() == TRUE) {
                $this->generate_kharchi_report();
            }
        }
        $this->load->view('includes/template', $data);
    }
	
	function generate_kharchi_report() {
		if($this->input->get_post('downloadformat') == 'pdf'){
			$table_class = 'pdf';
			$border = '0';
		}else{
			$table_class = 'excel';
			$border = '1';
		}

		$postData = array();
		$postData['project_id'] = $this->input->get_post('project');
		$postData['supervisor_id'] = $this->input->get_post('supervisor');
        $postData['month'] 		= $this->input->get_post('month');
        $postData['year'] 		= $this->input->get_post('year');
        $postData['company_id'] = $this->input->get_post('company_id');

		if ( empty( $postData['company_id'] ) || empty( $postData['month'] ) || empty( $postData['year'] ) ) {
            $this->session->set_flashdata('error', 'No data found.');
			redirect(base_url('admin/report/kharchi_report'));
		}
		
		$results = $this->report->getKharachiData( $postData );
		// echo "<pre>"; print_r($results); die();
		if ( empty( $results ) ) {
			$this->session->set_flashdata('error', 'No data found.');
			redirect(base_url('admin/report/kharchi_report'));
		}
		
		$companyInfo = $this->db->where( 'compnay_id', $postData['company_id'] )->get('company')->row_array();
		$projectInfo = $this->db->where( 'project_id', $postData['project_id'] )->get('project')->row_array();
		
		$supervisorOpeningBalance = $this->report->getSupervisorOpeningBalance( $postData );
		//echo '<pre>'; print_r( $supervisorOpeningBalance );exit;
		ob_start();
		?>
		<style type="text/css">
			table,thead,tbody,tfoot,tr,th,td,p { font-family:"Calibri"; font-size:16px }
			td{ padding: 3px;}
			a.comment-indicator:hover + comment { background:#ffd; position:absolute; display:block; border:1px solid black; padding:0.5em;  } 
			a.comment-indicator { background:red; display:inline-block; border:1px solid black; width:0.5em; height:0.5em;  } 
			comment { display:none;  } 
			.pdf { border:0px; }
			table.excel tr>td { border:1px solid black; }
		</style>
	
		<table cellspacing="0" border="<?php echo $border; ?>" class="<?php echo $table_class; ?>" style="height:100px;width:100%;">
			<tr>
				<td style="border-top: 2px solid #000000; border-bottom: 2px solid #000000; border-left: 2px solid #000000;" colspan="3" > 
					<?php if( !empty( $companyInfo['company_logo_image'] ) ) { ?><img src='<?php echo base_url('uploads/user/').$companyInfo['company_logo_image']; ?>' style="width:100px; margin:10px;"/> <?php } ?>
				</td>
				<!-- <td style="border-top: 2px solid #000000;border-bottom: 2px solid #000000;" width="200px" align="center" valign="middle"></td> -->
				<td  colspan="4" style="border-top: 2px solid #000000;border-bottom: 2px solid #000000;"  height="93" align="center" valign="middle"><font size="5" color="#000000"><u><b><?php echo $companyInfo['company_name']; ?></b></u></font> <br><font size="4"><?php echo $projectInfo['project_name']; ?></font></td>
				<td colspan="3" style="border-top: 2px solid #000000; border-right:2px solid #000; border-bottom: 2px solid #000000; " align="right" >
					<img src='<?php echo base_url('assets/admin/images/aasaan-footer-logo.jpg'); ?>' style="width:200px; margin:10px;"/>
				</td>
			</tr>

			<?php
            $objPHPExcel = new PHPExcel();
			
			$styleArray = array(
		        'font' => array(
		            'size'  => 10,
                    'name'  => 'Arial'
		        ),
		        'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        )
		    );

		    $styleArray1 = array(
		        'font' => array(
		            'bold' => true,
		            'size'  => 11,
                    'name'  => 'Arial'
		        ),
		        'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        )
		    );

		    $styleArray2 = array(
		        'font' => array(
		            'bold' => true,
		            'size'  => 12,
                    'name'  => 'Arial'
		        ),
		        'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
		            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        )
		    );

		    $styleArray3 = array(
		        'font' => array(
		            'size'  => 10,
                    'name'  => 'Arial'
		        ),
		        'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
		            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        )
		    );

		    $styleArray4 = array(
		        'font' => array(
		            'size'  => 10,
                    'name'  => 'Arial'
		        ),
		        'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
		            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        )
		    );
		    $borderStyle = array(
				    'borders' => array(
				        'outline' => array(
				            'style' => PHPExcel_Style_Border::BORDER_THICK,
				            'color' => array('argb' => '000000'),
				        ),
				    ),
				);

            $company_name = $companyInfo['company_name'];
            $project_name = $projectInfo['project_name'];
            $company_logo = $companyInfo['company_logo_image'];


		    //First Line
		    $sheet = $objPHPExcel->getActiveSheet();
		    $imageExist =  ROOT_PATH.'/uploads/user/'.$company_logo;
		      
		    $sheet->mergeCells('A1:D3');
		    if(!empty($company_logo) && file_exists($imageExist)){
			    
			    $objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Logo');
				$objDrawing->setDescription('Logo');
				$objDrawing->setPath('./uploads/user/'.$company_logo);
				$objDrawing->setHeight(45);
				$objDrawing->setOffsetX(10);
				$objDrawing->setOffsetY(2);
				$objDrawing->setWorksheet($sheet);
				$objDrawing->setCoordinates('B1');
			}
			$sheet->getStyle('A1:D3')->applyFromArray($borderStyle);

		    $sheet->mergeCells('E1:I3');
		    $sheet->setCellValue('E1',$company_name."\n".$project_name);
		    $objPHPExcel->getActiveSheet()->getStyle('E1')->getAlignment()->setWrapText(true);
		    $sheet->getStyle('E1')->applyFromArray($styleArray1);
		    $sheet->getStyle('E1:I3')->applyFromArray($borderStyle);

		    $sheet->mergeCells('J1:L3');
            $objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Logo');
			$objDrawing->setDescription('Logo');
			$objDrawing->setPath('./assets/admin/images/aasaan-footer-logo.jpg');
			$objDrawing->setHeight(36);
			$objDrawing->setOffsetX(70);
			$objDrawing->setOffsetY(10);
			$objDrawing->setWorksheet($sheet);
			$objDrawing->setCoordinates('J1');
			$sheet->getStyle('J1:L3')->applyFromArray($borderStyle);
			?>
		<!-- </table>
		<table cellspacing="0" border="1" style="width:100%;"> -->
			<tr>
				<td style="border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000;" colspan="10" align="center" valign="bottom">
					<b>
						<font size="4" color="#000000">Kharchi report for <?php echo $this->data['month'][$postData['month']] .', '. $postData['year']; ?>
						</font>
					</b>
				</td>
			</tr>

			<?php
				//Second Line
				$a = $this->data['month'][$postData['month']] .', '. $postData['year'];
			    $sheet->mergeCells('A4:L4');
			    $sheet->setCellValue('A4','Kharchi Report For '.$a.'');
			    $sheet->getStyle('A4')->applyFromArray($styleArray1);
			    $sheet->getStyle('A4:L4')->applyFromArray($borderStyle);
            ?>

			<tr>
				<td style="width: 3%; border-left: 2px solid #000000; border-right: 1px solid #000000;" height="45" align="center" valign="middle"><b><font color="#000000">Kharchi no.</font></b></td>
				<td style="width: 10%;" align="center" valign="middle"><b><font color="#000000">Issue date</font></b></td>
				<td style="width: 31%; border-left: 1px solid #000000; border-right: 1px solid #000000;" align="center" colspan="3" valign="middle"><b><font color="#000000">Kharchi description</font></b></td>
				<td style="width: 13%;" align="center" valign="middle"><b><font color="#000000">Issued by</font></b></td>
				<td style="width: 13%; border-right: 1px solid #000000; border-left: 1px solid #000000;" align="center" valign="middle"><b><font color="#000000">Approved by</font></b></td>
				<td style="width: 10%; border-right: 1px solid #000000" align="center" valign="middle"><b><font color="#000000">Credit amount</font></b></td>
				<td style="width: 10%; border-right: 1px solid #000000" align="center" valign="middle"><b><font color="#000000">Debit amount</font></b></td>
				<td style="width: 10%; border-right: 2px solid #000000;" align="center" valign="middle"><b><font color="#000000">Closing balance</font></b></td>
			</tr>
			<?php 
			// Table column
			$sheet->mergeCells('A5:A6');
		    $sheet->setCellValue('A5','Kharchi no');
		    $sheet->getStyle('A5')->applyFromArray($styleArray);
		    $sheet->getStyle('A5')->getFont()->setBold(true);
		    $sheet->getStyle('A5:A6')->applyFromArray($borderStyle);

			$sheet->getColumnDimension('A')->setWidth(15);
		    
			$sheet->mergeCells('B5:B6');
		    $sheet->setCellValue('B5','Issue date');
		    $sheet->getStyle('B5')->applyFromArray($styleArray);
		    $sheet->getStyle('B5')->getAlignment()->setWrapText(true);
		    $sheet->getColumnDimension('B')->setAutoSize(TRUE);
		    $sheet->getStyle('B5')->getFont()->setBold(true);
		    $sheet->getStyle('B5:B6')->applyFromArray($borderStyle);

		    $sheet->mergeCells('C5:G6');
		    $sheet->setCellValue('C5','Kharchi description');
		    $sheet->getStyle('C5')->applyFromArray($styleArray);
		    $sheet->getStyle('C5')->getAlignment()->setWrapText(true);
		    $sheet->getStyle('C5')->getFont()->setBold(true);
		    $sheet->getStyle('C5:G6')->applyFromArray($borderStyle);

			$sheet->mergeCells('H5:H6');
		    $sheet->setCellValue('H5','Issued by');
		    $sheet->getStyle('H5')->applyFromArray($styleArray);
		    $sheet->getColumnDimension('H')->setAutoSize(true);
		    $sheet->getStyle('H5')->getFont()->setBold(true);
		    $sheet->getColumnDimension('H')->setWidth(15);
		    $sheet->getStyle('H5:H6')->applyFromArray($borderStyle);

			$sheet->mergeCells('I5:I6');
		    $sheet->setCellValue('I5','Approved by');
		    $sheet->getStyle('I5')->applyFromArray($styleArray);
		    $sheet->getStyle('I5')->getAlignment()->setWrapText(true);
		    $sheet->getColumnDimension('I')->setWidth(15);
		    $sheet->getStyle('I5')->getFont()->setBold(true);
		    $sheet->getStyle('I5:I6')->applyFromArray($borderStyle);
		    
			$sheet->mergeCells('J5:J6');
		    $sheet->setCellValue('J5','Credit amount');
		    $sheet->getStyle('J5')->applyFromArray($styleArray);
		    $sheet->getStyle('J5')->getAlignment()->setWrapText(true);
		    $sheet->getColumnDimension('J')->setWidth(15);
		    $sheet->getStyle('J5')->getFont()->setBold(true);
		    $sheet->getStyle('J5:J6')->applyFromArray($borderStyle);

			$sheet->mergeCells('K5:K6');
		    $sheet->setCellValue('K5','Debit amount');
		    $sheet->getStyle('K5')->applyFromArray($styleArray);
		    $sheet->getStyle('K5')->getAlignment()->setWrapText(true);
		    $sheet->getColumnDimension('K')->setWidth(15);
		    $sheet->getStyle('K5')->getFont()->setBold(true);
		    $sheet->getStyle('K5:K6')->applyFromArray($borderStyle);

			$sheet->mergeCells('L5:L6');
		    $sheet->setCellValue('L5','Closing balance');
		    $sheet->getStyle('L5')->applyFromArray($styleArray);
		    $sheet->getStyle('L5')->getAlignment()->setWrapText(true);
		    $sheet->getStyle('L5')->getFont()->setBold(true);
		    $sheet->getStyle('L5:L6')->applyFromArray($borderStyle);

		    	$excel_row = 7;
				$openingBalance = 0;
				 
				 // echo "<pre>"; print_r($supervisorOpeningBalance); die();
				foreach( $supervisorOpeningBalance as $supervisor ) {
					$creditAmount = '';
					$debitAmount = '';
					if( isset($supervisor['credit_amount']) && $supervisor['credit_amount'] > 0 ) { 
						$creditAmount = 'Rs.'.$supervisor['credit_amount'];
						$openingBalance += $supervisor['credit_amount'];
					} else { 
						if(isset($supervisor['credit_amount'])) {
							$debitAmount = 'Rs.'.$supervisor['credit_amount'];
							$openingBalance -= $supervisor['credit_amount'];
						}
					}
			?>
			<tr>
				<td style="border-bottom: 1px solid #000000; border-right: 1px solid #000000; border-left: 2px solid #000000; border-top: 1px solid #000;" height="29" align="center" valign="middle"><font color="#000000"><br></font></td>
				<td style="border-top: 1px solid #000000; " align="center" valign="middle" sdval="42745" sdnum="1033;0;MM/DD/YYYY"><font color="#000000"><?php echo '01/'.$postData['month'].'/'.$postData['year']; ?></font></td>
				<td style="border-bottom: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;border-top: 1px solid #000;" align="left" colspan="3" valign="middle"><font color="#000000">Opening balance of &nbsp;<?php echo $supervisor['supervisor_name'].' '.$supervisor['supervisor_last_name']; ?></font></td>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; " align="center" valign="middle"><font color="#000000"></font></td>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; " align="center" valign="middle"><font color="#000000"></font></td>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; " align="center" valign="middle" sdval="500" sdnum="1033;0;[$₹]#,##0"><font color="#000000"><?php echo $creditAmount; ?></font></td>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; " align="center" valign="middle"><font color="#000000"><?php echo $debitAmount; ?></font></td>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000;" align="center" valign="middle"><font color="#000000"><br></font></td>
			</tr>
			<?php  

			$sheet->setCellValueByColumnAndRow(0, $excel_row, '');
			$sheet->getDefaultStyle()->applyFromArray($styleArray);
			$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(0) . ($excel_row))->applyFromArray($borderStyle);

		    $sheet->setCellValueByColumnAndRow(1, $excel_row, '01/'.$postData['month'].'/'.$postData['year']);

		    $sheet->mergeCells('C'.$excel_row.':G'.$excel_row.'');
		    $sheet->getStyle('C'.$excel_row.':G'.$excel_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		    $sheet->getStyle('C'.$excel_row.':G'.$excel_row.'')->applyFromArray($borderStyle);

		    $sheet->setCellValueByColumnAndRow(2, $excel_row, 'Opening balance of '.$supervisor['supervisor_name'].' '.$supervisor['supervisor_last_name']);

			$sheet->setCellValueByColumnAndRow(7, $excel_row, '');
			$sheet->getStyle('H'.$excel_row)->applyFromArray($borderStyle);

		    $sheet->getStyle('H'.$excel_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		    $sheet->getStyle('H'.$excel_row)->applyFromArray($borderStyle);

		    $sheet->setCellValueByColumnAndRow(8, $excel_row, '');
		    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(8) . ($excel_row))->applyFromArray($borderStyle);

		    $sheet->setCellValueByColumnAndRow(9, $excel_row, $creditAmount);
		    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(9) . ($excel_row))->applyFromArray($borderStyle);

		    $sheet->setCellValueByColumnAndRow(10, $excel_row, $debitAmount);
		    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(10) . ($excel_row))->applyFromArray($borderStyle);

		    $sheet->setCellValueByColumnAndRow(11, $excel_row, '');
		    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(11) . ($excel_row))->applyFromArray($borderStyle);

		    $excel_row++;

            ?>
			<?php
				}
				$closingBalance = $openingBalance;
				$totalCreditCount = 0;
				$totalDebitCount = 0;
				$supervisorArray = array();
				$excel_row = 7 + count($supervisorOpeningBalance);
				foreach( $results as $result ) { 
					// $issuedBy = $result['supervisor_name'].' '.$result['supervisor_last_name'];
					$approvedBy = '';
					$creditAmount = '';
					$debitAmount = '';
					$credit = 0;
					$debit = 0;
					
					if( $result['debit_credit_status'] == 1 ) {
						$description = $result['title'];
						$issuedBy = $result['supervisor_name'].' '.$result['supervisor_last_name'];
						$approvedBy = $result['user_name'].' '.$result['user_last_name'];
						$debitAmount = $result['amount'];
						$closingBalance -= $debitAmount;
						$totalDebitCount++;
						$debit = 1;
					} else {
						$description = 'Credit to Hajiri account of '.$result['supervisor_name'].' '.$result['supervisor_last_name'];
						$issuedBy = $result['user_name'].' '.$result['user_last_name'];
						$creditAmount = $result['amount'];
						$closingBalance += $creditAmount;
						$totalCreditCount++;
						$credit = 1;
					} 
					
					$supervisorId = $result['supervisor_id'];
					if( isset( $supervisorArray[$supervisorId] ) ){
						
						$supervisorArray[$supervisorId]['creditCount'] += $credit; 
						$supervisorArray[$supervisorId]['credit'] += $creditAmount;
						$supervisorArray[$supervisorId]['debitCount'] += $debit; 
						$supervisorArray[$supervisorId]['debit'] += $debitAmount;
						$supervisorArray[$supervisorId]['closingBalance'] = $credit > 0 ? $supervisorArray[$supervisorId]['closingBalance'] + $creditAmount : $supervisorArray[$supervisorId]['closingBalance'] - $debitAmount;
						
					} else {
						// closing_balance = opening balance of perticular emp + current amount
						$supervisorArray[$supervisorId] = array( 
							'name' => $result['supervisor_name'].' '.$result['supervisor_last_name'], 
							'creditCount'=> $credit, 
							'credit' => $creditAmount, 
							'debitCount' => $debit, 
							'debit' => $debitAmount, 
							'closingBalance' => $credit > 0 ? $creditAmount : $debitAmount );
					}
			?>
			<tr>
				<td style="border-top: 1px solid #000000; border-right: 1px solid #000000; border-left: 2px solid #000000;" height="29" align="center" valign="middle" sdval="11" sdnum="1033;"><font color="#000000"><?php echo $result['kharachi_id']; ?></font></td>
				<td style="border-top: 1px solid #000000;  " align="center" valign="middle"><font color="#000000"><?php echo date('d/m/Y', strtotime( $result['date_time'] ) );?></font></td>
				<td style="border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="left" colspan="3" valign="middle"><font color="#000000"><?php echo $description;//$result['title']; ?></font></td>
				<td style="border-top: 1px solid #000000;border-bottom: 1px solid #000000;  " align="center" valign="middle"><font color="#000000"><?php echo $issuedBy; ?></font></td>
				<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign="middle"><font color="#000000"><?php echo $approvedBy; ?></font></td>
				<td style=" border-top: 1px solid #000000; " align="center" valign="middle" sdval="1000" sdnum="1033;0;[$₹]#,##0"><font color="#000000"><?php if(!empty($creditAmount) && $creditAmount > 0 ){ echo 'Rs.'.$creditAmount; } ?></font></td>
				<td style="border-left: 1px solid #000000;  border-top: 1px solid #000000;" align="center" valign="middle" sdnum="1033;0;[$₹]#,##0"><font color="#000000"><?php if(!empty($debitAmount) && $debitAmount > 0 ){ echo 'Rs.'.$debitAmount; }  ?></font></td>
				<td style="border-top: 1px solid #000000;border-bottom: 1px solid #000000;border-left: 1px solid #000000; border-right: 2px solid #000000;" align="center" valign="middle" sdval="2000" sdnum="1033;0;[$₹]#,##0"><font color="#000000"><?php  if(!empty($closingBalance) ){echo 'Rs.'.$closingBalance; }  ?></font></td>
			</tr>

			<?php

			//table tr
			$creditAmount1 = '';
			$debitAmount1 = '';
			$closingBalance1 = '';
			if(!empty($creditAmount) && $creditAmount > 0 )
			{
				$creditAmount1 = 'Rs.'.$creditAmount; 
			}
			if(!empty($debitAmount) && $debitAmount > 0 )
			{ 
				$debitAmount1 = 'Rs.'.$debitAmount; 
	        }
			if(!empty($closingBalance) )
			{
				$closingBalance1 = 'Rs.'.$closingBalance; 
			}

			$sheet->setCellValueByColumnAndRow(0, $excel_row, $result['kharachi_id']);
			$sheet->getDefaultStyle()->applyFromArray($styleArray);

			$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(0) . ($excel_row))->applyFromArray($borderStyle);

			if(!empty($result['date_time'])){
		   	 $sheet->getColumnDimension('B')->setAutoSize(true);
			}
			$sheet->setCellValueByColumnAndRow(1, $excel_row, date('d/m/Y', strtotime( $result['date_time'] ) ));

			$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . ($excel_row))->applyFromArray($borderStyle);

		    $sheet->mergeCells('C'.$excel_row.':G'.$excel_row.'');
		    $sheet->getStyle('C'.$excel_row.':G'.$excel_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

		    $sheet->getStyle('C'.$excel_row.':G'.$excel_row.'')->applyFromArray($borderStyle);

			$sheet->setCellValueByColumnAndRow(2, $excel_row, $description);

		    if(!empty($issuedBy)){
		   	 $sheet->getColumnDimension('H')->setAutoSize(true);
			}

		    $sheet->setCellValueByColumnAndRow(7, $excel_row, $issuedBy);
		    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(7) . ($excel_row))->applyFromArray($borderStyle);
		     
		    if(!empty($approvedBy)){
		   	 $sheet->getColumnDimension('I')->setAutoSize(true);
			}

		    $sheet->setCellValueByColumnAndRow(8, $excel_row, $approvedBy);
		    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(8) . ($excel_row))->applyFromArray($borderStyle);

		    $sheet->setCellValueByColumnAndRow(9, $excel_row, $creditAmount1);
		    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(9) . ($excel_row))->applyFromArray($borderStyle);

		    $sheet->setCellValueByColumnAndRow(10, $excel_row, $debitAmount1);
		    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(10) . ($excel_row))->applyFromArray($borderStyle);

		    $sheet->setCellValueByColumnAndRow(11, $excel_row, $closingBalance1);
		    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(11) . ($excel_row))->applyFromArray($borderStyle);

		    $excel_row++;
			?>

			<?php }?>

			<tr>
				<td style="border-bottom: 2px solid #000000; border-top: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000;" colspan="10" height="20" align="left" valign="bottom"><b><u><font size="3" color="#000000">Supervisor account summary:</font></u></b></td>
			</tr>
			<tr>
				<td style="border-left: 2px solid #000000" height="20" align="left" valign="bottom"><b><font color="#000000">Sr. no.</font></b></td>
				<td style="" colspan="3" align="left" valign="bottom"  ><b><font color="#000000">Supervisor name</font></b></td>
				<td style="" align="left" valign="bottom"  ><b><font color="#000000">Credit count</font></b></td>
				<td style="" align="left" valign="bottom"  ><b><font color="#000000">Credit</font></b></td>
				<td style="" align="left" valign="bottom"  ><b><font color="#000000">Debit count</font></b></td>
				<td style="" align="left" valign="bottom"  ><b><font color="#000000">Debit</font></b></td>
				<td style="border-right: 2px solid #000000;" colspan="2" align="left" valign="bottom"><b><font color="#000000">Closing balance</font></b></td>
			</tr>
			<?php

				$sheet->mergeCells('A'.$excel_row.':L'.$excel_row.'');
			    $sheet->setCellValue('A'.$excel_row,'');
			    $excel_row++;

				$sheet->mergeCells('A'.$excel_row.':L'.$excel_row.'');
			    $sheet->setCellValue('A'.$excel_row,'Supervisor account summary:');
			    $sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray2);
			    $sheet->getStyle('A'.$excel_row.':L'.$excel_row.'')->applyFromArray($borderStyle);
			    $excel_row++;

				$sheet->setCellValue('A'.$excel_row.'','Sr. no.');
			    $sheet->getStyle('A'.$excel_row)->getFont()->setBold(true);
			     $sheet->getStyle('A'.$excel_row.'')->applyFromArray($borderStyle);


			    $sheet->mergeCells('B'.$excel_row.':C'.$excel_row.'');
			    $sheet->setCellValue('B'.$excel_row.'','Supervisor name');
			    $sheet->getStyle('B'.$excel_row)->getFont()->setBold(true);
			    $sheet->getStyle('B'.$excel_row.':C'.$excel_row.'')->applyFromArray($borderStyle);
				
				$sheet->setCellValue('D'.$excel_row.'','Credit count');
			    $sheet->getStyle('D'.$excel_row)->getFont()->setBold(true);
			    $sheet->getColumnDimension('D')->setAutoSize(TRUE);
			    $sheet->getStyle('D'.$excel_row)->applyFromArray($borderStyle);


			    $sheet->mergeCells('D'.$excel_row.':E'.$excel_row.'');
			    $sheet->setCellValue('D'.$excel_row.'','Credit count');
			    $sheet->getStyle('D'.$excel_row)->getFont()->setBold(true);
			    $sheet->getStyle('D'.$excel_row.':E'.$excel_row.'')->applyFromArray($borderStyle);

			    $sheet->mergeCells('F'.$excel_row.':G'.$excel_row.'');
			    $sheet->setCellValue('F'.$excel_row.'','Credit');
			    $sheet->getStyle('F'.$excel_row)->getFont()->setBold(true);
			    $sheet->getStyle('F'.$excel_row.':G'.$excel_row.'')->applyFromArray($borderStyle);

			    $sheet->mergeCells('H'.$excel_row.':I'.$excel_row.'');
			    $sheet->setCellValue('H'.$excel_row.'','Debit count');
			    $sheet->getStyle('H'.$excel_row)->getFont()->setBold(true);
			    $sheet->getStyle('H'.$excel_row.':I'.$excel_row.'')->applyFromArray($borderStyle);

				$sheet->mergeCells('J'.$excel_row.':K'.$excel_row.'');
			    $sheet->setCellValue('J'.$excel_row.'','Debit');
			    $sheet->getStyle('J'.$excel_row)->getFont()->setBold(true);
			    $sheet->getStyle('J'.$excel_row.':K'.$excel_row.'')->applyFromArray($borderStyle);

				$sheet->setCellValue('L'.$excel_row.'','Closing balance');
			    $sheet->getStyle('L'.$excel_row)->getFont()->setBold(true);
			    $sheet->getColumnDimension('L')->setAutoSize(true);
			    $sheet->getStyle('L'.$excel_row)->applyFromArray($borderStyle);

				$excel_row++;
			


				$srNo = 1;
				foreach( $supervisorArray as $supervisor ) { ?>
			<tr>
				<td style="border-left: 2px solid #000000" height="20" align="left" valign="bottom" sdval="1" sdnum="1033;"><font color="#000000"><?php echo $srNo; ?></font></td>
				<td colspan="3" align="left" valign="bottom"  ><font color="#000000"><?php echo $supervisor['name']; ?></font></td>
				<td align="left" valign="bottom" ><font color="#000000"><?php echo $supervisor['creditCount']; ?></font></td>
				<td align="left" valign="bottom" ><font face="Noto Sans Devanagari" color="#000000"><?php echo $supervisor['credit'] ? 'Rs.'.$supervisor['credit'] : ''; ?></font></td>
				<td align="left" valign="bottom" ><font color="#000000"><?php echo $supervisor['debitCount']; ?></font></td>
				<td align="left" valign="bottom" ><font face="Noto Sans Devanagari" color="#000000"><?php echo $supervisor['debit'] ? 'Rs.'.$supervisor['debit'] : ''; ?></font></td>
				<td style="border-right: 2px solid #000000;" colspan="2" align="left" valign="bottom" ><font face="Noto Sans Devanagari" color="#000000"><?php if(!empty($closingBalance) ){echo 'Rs.'.$closingBalance; } else { echo 'Rs.0.00'; } //echo $supervisor['closingBalance']; ?></font></td>
			</tr>
			<?php

				$supervisor['credit'] ? $supervisorCredit = 'Rs.'.$supervisor['credit'] : $supervisorCredit = '';
				$supervisor['debit'] ? $supervisorDebit = 'Rs.'.$supervisor['debit'] : $supervisorDebit = '';
				$closingBalanceExcel = '';
				 if(!empty($closingBalance)){
				 	$closingBalanceExcel = 'Rs.'.$closingBalance;
				 }


				$sheet->setCellValueByColumnAndRow(0, $excel_row, $srNo);
				$sheet->getDefaultStyle()->applyFromArray($styleArray);
				$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(0) . ($excel_row))->applyFromArray($borderStyle);

				$sheet->mergeCells('B'.$excel_row.':C'.$excel_row.'');
				$sheet->getStyle('B'.$excel_row.':C'.$excel_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

				$sheet->getStyle('B'.$excel_row.':C'.$excel_row.'')->applyFromArray($borderStyle);
				$sheet->setCellValueByColumnAndRow(1, $excel_row, $supervisor['name']);
			    $sheet->mergeCells('D'.$excel_row.':E'.$excel_row.'');
			    $sheet->getStyle('D'.$excel_row.':E'.$excel_row.'')->applyFromArray($borderStyle);
			    $sheet->setCellValueByColumnAndRow(3, $excel_row, $supervisor['creditCount']);
			    $sheet->mergeCells('F'.$excel_row.':G'.$excel_row.'');
			    $sheet->getStyle('F'.$excel_row.':G'.$excel_row.'')->applyFromArray($borderStyle);

			    $sheet->setCellValueByColumnAndRow(5, $excel_row, $supervisorCredit);
			    $sheet->mergeCells('H'.$excel_row.':I'.$excel_row.'');
			    $sheet->getStyle('H'.$excel_row.':I'.$excel_row.'')->applyFromArray($borderStyle);

			    $sheet->setCellValueByColumnAndRow(7, $excel_row, $supervisor['debitCount']);
			    $sheet->mergeCells('J'.$excel_row.':K'.$excel_row.'');
			    $sheet->getStyle('J'.$excel_row.':K'.$excel_row.'')->applyFromArray($borderStyle);

			    $sheet->setCellValueByColumnAndRow(9, $excel_row, $supervisorDebit);
			    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(9) . ($excel_row))->applyFromArray($borderStyle);

			    $sheet->setCellValueByColumnAndRow(11, $excel_row, $closingBalanceExcel);
			    $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(11) . ($excel_row))->applyFromArray($borderStyle);
			    
			    $excel_row++;

				$srNo++;
				} ?>
			
			<tr>
				<td style="border: 2px solid #000000;" colspan="10" height="20" align="left" valign="bottom"><b><u><font size="3" color="#000000">Statement summary</font></u></b></td>
			</tr>
			<?php 
				$sheet->mergeCells('A'.$excel_row.':L'.$excel_row.'');
			    $sheet->setCellValue('A'.$excel_row,'');
			    $excel_row++;

				$sheet->mergeCells('A'.$excel_row.':L'.$excel_row.'');
			    $sheet->setCellValue('A'.$excel_row,'Statement summary:');
			    $sheet->getStyle('A'.$excel_row.'')->applyFromArray($styleArray2);
			    $sheet->getStyle('A'.$excel_row.':L'.$excel_row.'')->applyFromArray($borderStyle);
			    $excel_row++;
			?>
			<tr>
				<td style="border-left: 2px solid #000000" colspan="4" height="25" align="left" valign="bottom"><b><font size="3" color="#000000">Opening balance</font></b></td>
				<td style="" align="left" valign="bottom" sdval="1000"  ><font face="Noto Sans Devanagari" color="#000000">Rs.<?php echo $openingBalance; ?></font></td>
				<td style="" align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
				<td style="" align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
				<td style="" align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
				<td style="" align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
				<td style="border-right: 2px solid #000000" align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
			</tr>
			<?php
				$openingBalanceExcel = '';
				if(!empty($openingBalance)){
					$openingBalanceExcel = 'Rs.'.$openingBalance;
				}
				$sheet->getDefaultStyle()->applyFromArray($styleArray);
				$sheet->mergeCells('A'.$excel_row.':K'.$excel_row.'');
			    $sheet->setCellValueByColumnAndRow(0, $excel_row, 'Opening balance');
			    $sheet->getStyle('A'.$excel_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			    $sheet->getStyle('A'.$excel_row.':K'.$excel_row.'')->applyFromArray($borderStyle);
			   	$sheet->setCellValueByColumnAndRow(11, $excel_row, $openingBalanceExcel);
			   	$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(11) . ($excel_row))->applyFromArray($borderStyle);
			    $excel_row++;
			?>
			<tr>
				<td style="border-left: 2px solid #000000" colspan="4" height="25" align="left" valign="bottom"><b><font size="3" color="#000000">Total debit count</font></b></td>
				<td align="left" valign="bottom" sdval="3" sdnum="1033;"><font size="3" color="#000000"><?php echo $totalDebitCount; ?></font></td>
				<td align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
				<td align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
				<td align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
				<td align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
				<td style="border-right: 2px solid #000000;" align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
			</tr>
			<?php 

				$sheet->getDefaultStyle()->applyFromArray($styleArray);
				$sheet->mergeCells('A'.$excel_row.':K'.$excel_row.'');
			    $sheet->setCellValueByColumnAndRow(0, $excel_row, 'Total debit count');
			    $sheet->getStyle('A'.$excel_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			    $sheet->getStyle('A'.$excel_row.':K'.$excel_row.'')->applyFromArray($borderStyle);
			   	$sheet->setCellValueByColumnAndRow(11, $excel_row, $totalDebitCount);
			   	$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(11) . ($excel_row))->applyFromArray($borderStyle);
			   	$excel_row++;

			?>
			<tr>
				<td style="border-left: 2px solid #000000" colspan="4" height="25" align="left" valign="bottom"><b><font size="3" color="#000000">Total credit count</font></b></td>
				<td align="left" valign="bottom" sdval="4" sdnum="1033;"><font size="3" color="#000000"><?php echo $totalCreditCount; ?></font></td>
				<td align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
				<td align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
				<td align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
				<td align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
				<td style="border-right: 2px solid #000000;" align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
			</tr>
			<?php 
				$sheet->getDefaultStyle()->applyFromArray($styleArray);
				$sheet->mergeCells('A'.$excel_row.':K'.$excel_row.'');
			    $sheet->setCellValueByColumnAndRow(0, $excel_row, 'Total credit count');
			    $sheet->getStyle('A'.$excel_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			    $sheet->getStyle('A'.$excel_row.':K'.$excel_row.'')->applyFromArray($borderStyle);
			   	$sheet->setCellValueByColumnAndRow(11, $excel_row, $totalCreditCount);
			   	$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(11) . ($excel_row))->applyFromArray($borderStyle);
			   	$excel_row++;
			?>
			<tr>
				<td style="border-left: 2px solid #000000" colspan="4" height="25" align="left" valign="bottom"><b><font size="3" color="#000000">Closing balance</font></b></td>
				<td style="" align="left" valign="bottom" sdval="5150"  ><font face="Noto Sans Devanagari" color="#000000">Rs.<?php echo $closingBalance; ?></font></td>
				<td style="" align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
				<td style="" align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
				<td style="" align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
				<td style="" align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
				<td style="border-right: 2px solid #000000;" align="left" valign="bottom"><font size="3" color="#000000"><br></font></td>
			</tr>
			<?php 
				$closingBalanceExcel = '';
				if(!empty($closingBalance)){
					$closingBalanceExcel = 'Rs.'.$closingBalance;
				}
				$sheet->getDefaultStyle()->applyFromArray($styleArray);
				$sheet->mergeCells('A'.$excel_row.':K'.$excel_row.'');
			    $sheet->setCellValueByColumnAndRow(0, $excel_row, 'Closing balance');
			    $sheet->getStyle('A'.$excel_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			    $sheet->getStyle('A'.$excel_row.':K'.$excel_row.'')->applyFromArray($borderStyle);

			   	$sheet->setCellValueByColumnAndRow(11, $excel_row, $closingBalanceExcel);
			   	$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(11) . ($excel_row))->applyFromArray($borderStyle);
			   	$excel_row++;
		   	?>
			<tr>
				<td style="border-top: 2px solid #000000; border-left: 2px solid #000000; border-bottom: 1px solid #000000 " colspan=8 height="25" align="right" valign=bottom><font size=3 color="#000000"></font></td>
				<td style="border-top: 2px solid #000000; border-right: 2px solid #000000; font-size:18px; border-bottom: 1px solid #000000 " colspan=2 align="center" valign=bottom >
					<b>
						<font color="#000000"><br><br><br><br>
							<?php echo $this->session->userdata('name'); ?><br>
							<span style="font-size:12px">(self-attested)</span><br>
							<span style="font-size:15px"><?php echo $companyInfo['company_name']; ?></span>
						</font>
					</b>
				</td>			
			</tr>
			<?php 
				$name = $this->session->userdata('name');
				$update_row = $excel_row + 4 ;
				$sheet->mergeCells('A'.$excel_row.':I'.$update_row.'');
			    $sheet->setCellValue('A'.$excel_row.'','');
			    $sheet->mergeCells('J'.$excel_row.':L'.$update_row.'');
			    $sheet->setCellValue('J'.$excel_row,$name."\n".'(self-attested)'."\n".$companyInfo['company_name']);

			    $sheet->getStyle('J'.$excel_row.':L'.$update_row.'')->applyFromArray($borderStyle);
			    $sheet->getStyle('J'.$excel_row)->getAlignment()->setWrapText(true);
			    $sheet->getStyle('J'.$excel_row)->getFont()->setBold(true);
	            $sheet->getStyle('J'.$excel_row.':L'.$excel_row.'')->getFont()->setSize(10);

	            $update_row++;

	            $sheet->mergeCells('A'.$update_row.':L'.$update_row.'');
			    $sheet->setCellValue('A'.$update_row,'');
			    $sheet->getStyle('A'.$update_row.':L'.$update_row.'')->applyFromArray($borderStyle);

			    $update_row++;
		    ?>
			<tr>
				<td style="border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=10 align="left" valign=bottom sdnum="1033;16393;[$-4009]DD-MM-YYYY">
					<b><u><font size=3 color="#000000">Disclaimer:</font></u></b>
				</td>
			</tr>
			<?php 
				$sheet->mergeCells('A'.$update_row.':L'.$update_row.'');
			    $sheet->setCellValue('A'.$update_row,'Disclaimer');
			    $sheet->getStyle('A'.$update_row)->applyFromArray($styleArray2);
			    $sheet->getStyle('A'.$update_row)->getFont()->setUnderline(true);
			    $sheet->getStyle('A'.$update_row.':L'.$update_row.'')->applyFromArray($borderStyle);

				$update_row++;
			?>
			<tr>
				<td style="border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=10 align="left" valign=bottom>
					<font size=1 color="#000000">1. This is an auto-generated report.</font>
				</td>
			</tr>
			<?php
				$sheet->mergeCells('A'.$update_row.':L'.$update_row.'');
			    $sheet->setCellValue('A'.$update_row.'','1. This is an auto-generated report.');
			    $sheet->getStyle('A'.$update_row.'')->applyFromArray($styleArray4);
			    $sheet->getStyle('A'.$update_row.'')->getFont()->setSize(8);
			    $sheet->getStyle('A'.$update_row.':L'.$update_row.'')->applyFromArray($borderStyle);

			    $update_row++;
		    ?>
			<tr>
				<td style="border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=10 align="left" valign=bottom>
					<font size=1 color="#000000">2. Aasaan does not hold any legal liability for any data generated through this report </font>
				</td>
			</tr>
			<?php
				$sheet->mergeCells('A'.$update_row.':L'.$update_row.'');
			    $sheet->setCellValue('A'.$update_row.'','2. Aasaan does not hold any legal liability for any data generated through this report');
			    $sheet->getStyle('A'.$update_row.'')->applyFromArray($styleArray4);
			    $sheet->getStyle('A'.$update_row.'')->getFont()->setSize(8);
			    $sheet->getStyle('A'.$update_row.':L'.$update_row.'')->applyFromArray($borderStyle);

			    $update_row++;
	     	?>
			<tr>
				<td style="border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=10 align="left" valign=bottom>
					<font size=1 color="#000000">3. The values are purely based on the app operations</font>
				</td>
			</tr>
			<?php
				$sheet->mergeCells('A'.$update_row.':L'.$update_row.'');
			    $sheet->setCellValue('A'.$update_row.'','3. The values are purely based on the app operations');
			    $sheet->getStyle('A'.$update_row.'')->applyFromArray($styleArray4);
			    $sheet->getStyle('A'.$update_row.'')->getFont()->setSize(8);
			    $sheet->getStyle('A'.$update_row.':L'.$update_row.'')->applyFromArray($borderStyle);

			    $update_row++; 
			?>
			<tr>
				<td style="border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000" colspan=10 align="right" valign=bottom>
					<font size=1 color="#000000">This report was generated at <?php echo date('H:i'); ?> hours on <?php echo date('d/m/Y'); ?> </font>
				</td>
			</tr>
			<?php 
				$c_time = date('H:i');
				$c_date = date('d/m/Y');
				$sheet->mergeCells('A'.$update_row.':L'.$update_row.'');
			    $sheet->setCellValue('A'.$update_row.'','This report was generated at '.$c_time.' hours on '.$c_date.'');
			    $sheet->getStyle('A'.$update_row.'')->getFont()->setSize(8);
			    $sheet->getStyle('A'.$update_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			    $sheet->getStyle('A'.$update_row.':L'.$update_row.'')->applyFromArray($borderStyle);
			    $update_row++;
			    $sheet->setSelectedCells('E1:I3');
		    ?>
		</table> 
		<?php if($this->input->get_post('downloadformat') == 'pdf'){ ?>
			<br />
			<table cellspacing="0" border="<?php echo $border; ?>" class="<?php echo $table_class; ?>" style="height:100px;width:100%; page-break-before: always">
				<tr>
					<?php $index = 0; 
					 	foreach ($results as $result) { 
							if(!empty($result['image'])){ 

								if($index == 2){
									echo '</tr><tr>';
									$index= 0;
								} ?>
								<?php 
								$imageExist =  ROOT_PATH.'/uploads/kharchi/'.$result['image'];

			      	 			if(file_exists($imageExist)){ ?>

									<td style="border:1px dotted gray;" align="center" >
										<img src='<?php echo base_url("uploads/kharchi/".$result['image']); ?>' style="width:300px; margin:10px;"/>
										<br />
										Kharchi No : <?php echo $result['kharachi_id'] ?>
									</td>
								<?php } ?>
					<?php $index++; 
							}
						}?>
				</tr>
	 		</table>
		<?php }

		$contents = ob_get_contents();
        ob_end_clean();
        $month = date("F", mktime(0, 0, 0, $postData['month'] , 10));
        if($this->input->get_post('downloadformat') == 'pdf'){
        	$this->load->library('Dom_pdf');
	        // Convert to PDF
	       	$this->dompdf->load_html($contents);
	        $this->dompdf->render();
	        
	        $this->dompdf->stream($month." kharchi report ".$projectInfo['project_name'], array("Attachment" => True));
	    }else{
	    ob_end_clean();
	    $filename= $month." kharchi report ".$projectInfo['project_name'].'.xls';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		// header('Content-Disposition: attachment;filename="01simple.xlsx"');
		header('Content-Disposition: attachment;filename='.$filename);
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
  
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;

	  //     	$filename= $month." kharchi report ".$projectInfo['project_name'].'.xls';
			
			// // header("Content-type: application/vnd.ms-excel");
			// // header("Content-Disposition: attachment; filename=$file");

			// header('Content-Type: application/vnd.ms-excel');
			// // header('Content-Disposition: attachment;filename=$file');
			// header('Content-Disposition: attachment;filename='.$filename);
			// header('Cache-Control: max-age=0');
			// // If you're serving to IE 9, then the following may be needed
			// header('Cache-Control: max-age=1');

			// // If you're serving to IE over SSL, then the following may be needed
			// header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			// header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
			// header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			// header ('Pragma: public'); // HTTP/1.0

			// echo $contents; die();  
	    }
	}

    function labour_barcode_report() {
		
        if (isset($_GET['submit'])) {

            $this->_generate_labour_barcode_report();
        }
		$data['menu_title'] = 'Qr Stamp';
		$data['companies'] = $this->company->get_datatables();
        $data['title'] = 'Generate QR Code';
        $data['description'] = '';
        $data['page'] = 'report/barcode_report';
        $this->load->view('includes/template', $data);
    }

    function _generate_labour_barcode_report() {
        $company_id = $_GET['company_id'];
        $is_today = (isset($_GET['is_today']) ? $_GET['is_today'] : 'no');
        $labour_ids = (isset($_GET['labour_id']) ? $_GET['labour_id'] : array());
        
        if (is_array($labour_ids) && !empty($labour_ids) && !empty($company_id)) {    
            $BARCODES = $this->report->getWhereResultSpecific('worker', 'worker_id', $labour_ids, 'no');
        } else if (!empty($company_id)) {
            $BARCODES = $this->report->getWhereResult('worker', 'company_id', $company_id, $is_today);
        } else {
            
        }
        if (empty($BARCODES)) {
            $this->session->set_flashdata('error', 'No data found.');
            redirect(base_url('admin/report/labour_barcode_report'));
        }
        $BARCODES = array_chunk($BARCODES, 4);
        ob_start();
        ?>
        <style>
            @page {size: a4 portrait;margin:0.0;padding:0.0;}
            first {font-family: arial;font-size: 16px;}
            .div_td {border: 1px solid #000000;float: left;}
            .div_td h3 {text-align: center;margin-top: 5px;margin-bottom: 5px;}
        </style>
        <div class="first">
            <?php if ($BARCODES) { ?>
                <?php foreach ($BARCODES AS $BARCODE) { ?>
                    <?php foreach ($BARCODE AS $B) { ?>
                        <div class="div_td" style="width: 25%;">
                            <center><img src='<?php echo base_url('assets/admin/images/aasaan_pdf_logo.png'); ?>' style="height:10px; width:100px; margin-top:20px;"/></center>
                            <?php $url = base_url('uploads/barcodes/') . $B->worker_qrcode_image; ?>
                            <center><img src="<?php echo $url; ?>" style="width:150px;"></center>
                            
                            <h3><?php echo substr($B->labour_name,0,15)."<br>"; ?></h3>
                        </div>
                    <?php } ?>
                    <div style="clear: both;"></div>
                <?php } ?>
            <?php } ?>
        </div>
        <?php
        $contents = ob_get_contents();
        ob_end_clean();
        $this->load->library('Dom_pdf');
        $this->dompdf->load_html($contents);
        $this->dompdf->render();
        $this->dompdf->stream("qr-report.pdf", array("Attachment" => true));
    }

    public function ajax_get_labours($company_id) {
        $labours = $this->report->get_labours($company_id);
       // print_r($labours);
        $labours_html = '<option value="">Select worker Name</option>';
        if ($labours) {
            foreach ($labours as $labour) {
                $labours_html .= '<option value="' . $labour->worker_id . '">' . $labour->labour_name . '</option>';
            }
        }
        echo json_encode(array("status" => true, "labours_html" => $labours_html));
    }

    function ajax_get_project_list($selected_organization) {
        $html = '<option value="">Select project</option>';
        if ($selected_organization) {
            $project_results = $this->report->get_where('project', 'id,name', "FIND_IN_SET($selected_organization,user_id) > 0");
            foreach ($project_results as $key => $value) {
                $html .= '<option value="' . $value->id . '">' . $value->name . '</option>';
            }
        }
        echo $html;
    }

    function ajax_get_project_n_labour_list($selected_organization) {
        $project_html = '<option value="">Select project</option>';
        if ($selected_organization) {
            $project_results = $this->report->get_where('project', 'id,name', "FIND_IN_SET($selected_organization,user_id) > 0");
            foreach ($project_results as $key => $value) {
                $project_html .= '<option value="' . $value->id . '">' . $value->name . '</option>';
            }
        }
        $labour_html = '<option value="">Select worker</option>';
        if ($selected_organization) {
            $labour_results = $this->report->get_where('labour', 'id,name', array('user_id' => $selected_organization));
            foreach ($labour_results as $key => $value) {
                $labour_html .= '<option value="' . $value->id . '">' . $value->name . '</option>';
            }
        }
        echo json_encode(array('project' => $project_html, 'labour' => $labour_html));
    }

    function labour_barcode_report_clean() {
        if (isset($_GET['submit'])) {
            $this->_generate_labour_barcode_report_clean();
        }
        $data['users'] = $this->report->get_all_managers('user');
        //print_r($data['users']);
        $data['title'] = 'Generate Blank QRcode';
        $data['description'] = '';
        $data['page'] = 'report/barcode_report_clean';
        $this->load->view('includes/template', $data);
    }

    function _generate_labour_barcode_report_clean() {
        $user_id = $this->session->userdata('id');
        $BARCODES = array();
        $BARCODES = $this->report->getWhereResult('blank_qrcode', 'DATE(`qrcode_date_time`)', date('Y-m-d'));

        if (empty($BARCODES)) {
            $this->session->set_flashdata('error', 'No data found.');
            redirect(base_url('admin/report/labour_barcode_report_clean'));
        }
        $BARCODES = array_chunk($BARCODES, 4);
        ob_start();
        ?>
        <style>
            @page {size: a4 portrait;margin:0.0;padding:0.0;}
            first {font-family: arial;font-size: 16px;}
            .div_td {border: 1px solid #000000;float: left;}
            .div_td h3 {text-align: center;margin-top: 5px;margin-bottom: 5px;}
            #pname {vertical-align: bottom;height:30px;margin: 0 auto 5px; width: 90%; border-bottom:1px solid #000000;}
        </style>
        <div class="first">
            <?php if ($BARCODES) { ?>
                <?php foreach ($BARCODES AS $BARCODE) { ?>
                    <?php foreach ($BARCODE AS $B) { ?>
                        <div class="div_td" style="width: 25%;">
                            <center><img src='<?php echo base_url('assets/admin/images/aasaan_pdf_logo.png'); ?>' style="height:10px; width:100px; margin-top:20px;" /></center>
                            <?php $url = base_url('uploads/barcodes/') . $B->qrcode_image; ?>
                            <center><img src="<?php echo $url; ?>" style="width:150px;"></center>
                            <div id="pname"></div>
                        </div>
                    <?php } ?>
                    <div style="clear: both;"></div>
                <?php } ?>
            <?php } ?>
        </div>
        <?php
        $contents = ob_get_contents();
        ob_end_clean();
        $this->load->library('Dom_pdf');
        $this->dompdf->load_html($contents);
        $this->dompdf->render();
        $this->dompdf->stream("clean-qr-report.pdf", array("Attachment" => true));
    }

	
	function generateBlankQrIdCard( $count, $user_id ){
		
		$org_info = $this->db->where('compnay_id', $user_id)->get('company')->row_array();
		$BARCODES = array();
        $BARCODES = $this->report->getWhereLimit('blank_qrcode','qrcode_date_time LIKE "'.date('dmY_H').'%" AND user_id = '.$user_id, $count);
        if (empty($BARCODES)) {
            $this->session->set_flashdata('error', 'No data found.');
            redirect(base_url('admin/report/create_clean_qrcode'));
        }
        $pdfWidth = count( $BARCODES ) > 1 ? '100%' : '50%'; 
        $BARCODES = array_chunk($BARCODES, 2);
         ob_start();
        ?>
        <style>
            
            .div_td {border: 1px solid #000000;float: left;}
            .div_td h3 {text-align: center;margin-top: 5px;margin-bottom: 5px;}
            @page {size: a4 portrait;margin:2em 0 0 0;padding:0.0;}
            .first {font-size: 20px; width:<?php echo $pdfWidth; ?>}
            .main-container {border: 1px solid #000000;float: left;}
            .width50{width:50%;}
            .header-image{width: 100px;}
            .qr-image{height: 150px; width:150px;}
            .labour-details div{font-size: 14px, font-weight: 300; height: 30px;}
            .underline{display: block; width: 100%; border-bottom: 2px solid #000;}
            .name{text-transform: capitalize !important;}
        </style>
        <table class="first">
            <?php if ($BARCODES) { 
                 $count = 0;
                 foreach ($BARCODES AS $key => $BARCODE) { 
                    ?>
                <tr>
                    <?php foreach ($BARCODE AS $key =>$B) { ?>
                    <?php if(($count % 2) == 0) {?>
                    <td style="width:5%;"></td>
                    <?php }?>
                    <td style="width:45%; margin:0 5% !important; border: 1px solid #000000;"> 
                        <table style="width:100%; padding: 0px !important; margin: 0 !important;">
                            <tr>
                                <td style="width:5%"></td>
                                <td style="width:30%; margin: 5px 0 0 0;" valign="top"> <?php if( !empty( $org_info['company_logo_image'] ) && file_exists( 'uploads/user/'.$org_info['company_logo_image'] ) ) { ?><img class="header-image" src="<?php echo base_url('uploads/user/').$org_info['company_logo_image']; ?>">  <?php } ?></td>
                                <td style="width:63%; text-align:center;"> 
                                    <span style="font-size: 15px, font-weight: 600; text-transform: uppercase; width: 100%;"> 
                                        <u><?php echo $org_info['company_name']; ?></u> 
                                    </span>
                                    <br>
                                    <span style="font-size: 16px, font-weight: 300; width: 100%;"> Hajiri ID-card </span> 
                                </td>
                                <td style="width:20%"></td>
                            </tr>
                        </table>
                        <table style="width:100%; padding: 0px !important; margin: 0 !important;">
                            <tr>
                                <td style="width:40%" valign="top"> <?php if( !empty( $B->qrcode_image ) && file_exists( 'uploads/barcodes/'.$B->qrcode_image ) ) { ?> <img class="qr-image" src="<?php echo base_url('uploads/barcodes/').$B->qrcode_image; ?>"> <?php } ?></td>
                                <td class="labour-details" valign="top" style="width:60%, "> 
                                	<div class="underline"></div>
                                	<div class="underline"></div>
                                	<div class="underline"></div>
                                    <div style="font-size:10px; margin-top: 3em 0 0 0 !important; width: 100%;"> *Autogenerated and for company use only</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <?php if(($count % 2) != 0){ ?>
                    <td style="width:5%;"></td>
                    <?php }?>
                    <?php $count++;} 
                        if(($count % 8) == 0){
                            echo '<div style="page-break-after: always;"></div>';
                        }
                    ?>
                </tr>
            <?php 
                } 
            } 
            ?>
        </table>
        
        <?php
        $contents = ob_get_contents();
        ob_end_clean();
        $this->load->library('Dom_pdf');
        $this->dompdf->load_html($contents);
        $this->dompdf->render();
        $this->dompdf->stream("Blank Hajiri QR ID cards.pdf", array("Attachment" => true));
	}

    function _generate_labour_barcode_report_clean_with_count($count, $user_id) {
        $BARCODES = array();
        $BARCODES = $this->report->getWhereLimit('blank_qrcode','qrcode_date_time LIKE "'.date('dmY_H').'%" AND user_id = '.$user_id, $count);
        if (empty($BARCODES)) {
            $this->session->set_flashdata('error', 'No data found.');
            redirect(base_url('admin/report/create_clean_qrcode'));
        }
        $BARCODES = array_chunk($BARCODES, 4);
        ob_start();
        ?>
        <style>
            @page {size: a4 portrait;margin:2em 0 0 0;padding:0.0;}
            first {font-family: arial;font-size: 16px;}
            .div_td {border: 1px solid #000000;float: left;}
            .div_td h3 {text-align: center;margin-top: 5px;margin-bottom: 5px;}
            #pname {vertical-align: bottom;height:30px;margin: 0 auto 5px; width: 90%; border-bottom:1px solid #000000;}
        </style>
        <div class="first">
            <?php if ($BARCODES) {  $count = 0;?>
                <?php foreach ($BARCODES AS $BARCODE) { ?>
                    <?php foreach ($BARCODE AS $B) { $count++;?>
                        <div class="div_td" style="width: 25%;">
                            <center><img src='<?php echo base_url('assets/admin/images/aasaan_pdf_logo.png'); ?>' style="height:10px; width:100px; margin-top:20px;" /></center>
                            <?php $url = base_url('uploads/barcodes/') . $B->qrcode_image; ?>
                            <center><img src="<?php echo $url; ?>" style="width:150px;"></center>
                            <div id="pname"></div>
                        </div>
                    <?php }
                        if(($count % 16) == 0){
                            echo '<div style="page-break-after: always;"></div>';
                        }
                     ?>
                    <div style="clear: both;"></div>
                <?php } ?>

            <?php } ?>
        </div>
        <?php
        $contents = ob_get_contents();
        ob_end_clean();
        $this->load->library('Dom_pdf');
        $this->dompdf->load_html($contents);
        $this->dompdf->render();
        $this->dompdf->stream("Blank Hajiri QR stamps.pdf", array("Attachment" => true));
    }


    public function create_clean_qrcode() {
    	
        $msg = '';
        if (isset($_POST['submit'])) {
            $this->load->library('ciqrcode');
            $barcode_insert_array = array();
            $count = $_POST['count'];

            if($count == 0){
                 redirect(base_url('admin/report/create_clean_qrcode'));
            }
            $todaysdate = date('dmY_His');
			$user_id = $this->input->post('company_id') ? $this->input->post('company_id') : $this->session->userdata('company_id');;
            for ($index = 1; $index <= $count; $index++) {
                $barcode_date_time = $todaysdate . '-' . $index;

                $image_name = date('Y_m_d_H_i_s') . '.jpg';
                $params['data'] = $barcode_date_time;
                
                $params['level'] = 'H';
                $params['size'] = 10;
                $params['savename'] = FCPATH . 'uploads/barcodes/' . $image_name;
                $this->ciqrcode->generate($params);
                sleep(1);
                $barcode_insert_array[] = array(
                    'user_id'=> $user_id,
                    'qrcode_date_time' => $barcode_date_time,
                    'qrcode_image' => $image_name
                );
            }
            $this->db->insert_batch('blank_qrcode', $barcode_insert_array);
			if( $this->input->post('qr_code') ){
				$this->generateBlankQrIdCard( $count, $user_id );
			}else{ 
				$this->_generate_labour_barcode_report_clean_with_count($count, $user_id);
			}
            //$this->session->set_flashdata('success', 'Clean Barcode Sucessfully Created and Saved');
        }

        $data['menu_title'] = 'Labourimport';
		$data['users'] = $this->report->get_all_managers('user');
        $data['title'] = 'Generate Clean QR code';
        $data['msg'] = $msg;
        $data['description'] = 'Generate Clean QR code';
        $data['page'] = 'report/create_clean_barcode';
		$data['companies'] = $this->company->get_datatables();
        $this->load->view('includes/template', $data);
    }
    public function invoice(){
	 	
		$data = $this->data;
		//For Getting project
    	$companies = array("" => "Select project");
        $companies_results = $this->company->get_datatables(); 
        foreach ($companies_results as $key => $value) {
            $company[$value->compnay_id] = $value->company_name;
        }
        $data['companies'] = $company;
		$data['menu_title'] = 'Invoice';
        $data['title'] = 'Generate Invoice';
        $data['description'] = '';
        $data['page'] = 'report/generate_invoice';
        if (isset($_REQUEST['submit'])) {

			$this->form_validation->set_data($this->input->get());
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('company_id', 'Company', 'required');
           	if ($this->form_validation->run() == TRUE) {
                $this->generate_invoice();
            }
        }
        $this->load->view('includes/template', $data);
    }
    public function generate_invoice(){

    	$company_id = $this->input->get_post('company_id');
    	$planInfo =  $this->company->get_company_plan($company_id);
    	$companyInfo  = $this->company->get_company_detl($company_id); 

    	if(!empty($companyInfo->company_gst)) $company_gst = $companyInfo->company_gst;
    	else $company_gst = '';

    	if(!empty($companyInfo->company_pan)) $company_pan = $companyInfo->company_pan;
    	else $company_pan = '';

    	if(!empty($companyInfo->company_contact_no)) $company_contact_no = $companyInfo->company_contact_no;
    	else $company_contact_no = '';

    	if(!empty($companyInfo->company_email)) $company_email = $companyInfo->company_email;
    	else $company_email = '';

    	if($planInfo->plan_type == 0){
    		$planRs = 50;  
    		$plan_type = 'Monthly plan';
    		$invoice_subscription = 'HAJM/';
		}else { 
			$planRs = 500; 
			$plan_type = 'Annual plan';
			$invoice_subscription = 'HAJY/';
		}

		$getInvoiceNo = $this->company->getInvoiceNo();
		if(empty($getInvoiceNo)){
			$insertInvoiceId = $this->company->insertInvoiceId();
			$getInvoiceNo = new StdClass();
			$getInvoiceNo->lastinvoiceid = 0;
		}

		$updateInvoiceId = $this->company->updateInvoiceId($getInvoiceNo->lastinvoiceid + 1);
		$noOfInvoice = sprintf('%02s', $getInvoiceNo->lastinvoiceid + 1);
	  	$month = date('m');
		$year = date('y');

		$invoice_no = 'ATPL/'.$invoice_subscription.$month.$year.$noOfInvoice;
		$order_no = $invoice_subscription.$month.$year.$noOfInvoice;
		
		$cgst = (9 / 100) * $planRs;
		$sgst = (9 / 100) * $planRs;

		$grandTotal = $planRs + $cgst + $sgst;


		ob_start();
    	?>
    	<style type="text/css">
			table,thead,tbody,tfoot,tr,th,td,p { font-family:"Calibri"; font-size:16px }
			td{ padding: 3px; border-bottom: 1px solid #000000;}

			a.comment-indicator:hover + comment { background:#ffd; position:absolute; display:block; border:1px solid black; padding:0.5em;  } 
			a.comment-indicator { background:red; display:inline-block; border:1px solid black; width:0.5em; height:0.5em;  } 
			comment { display:none;  } 
			.pdf { border:0px; }
			table.excel tr>td { border:1px solid black; }
		</style>
		<table cellspacing="0" border="0" class="" style="height:100px;width:100%;">

			<tr>
				<td height="100px" colspan="2" align="left" style="border-top: 2px solid #000000; border-bottom: 2px solid #000000; border-left: 2px solid #000000;" > 
					<img src='<?php echo base_url('assets/admin/images/aasaan-footer-logo.jpg'); ?>' style="width:200px; margin:10px;"/>
				</td>
				<td height="100px" colspan="4" style="padding-right: 20px; border-right: 2px solid #000000; border-top: 2px solid #000000; border-bottom: 2px solid #000000;" align="right" valign="middle"><font size="6" color="#000000"><u><b>Aasaan Tech Pvt. Ltd.</b></u></font><br><font size="4">Parekh Bhuvan, Opp. Dena Bank, Dahanu Road (W), Palghar 401602</font></td>
			</tr>
			<tr height="50">
				<td style="border-bottom: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000;" colspan="6" align="center" valign="center"><b><font size="4" color="#000000"><u>SALES INVOICE</u></font></b>
				</td>
			</tr>

			<tr height="30">
				<td style="border-left: 2px solid #000000; border-right: 1px solid #000000;" align="left" valign="middle"><b><font color="#000000">E-mail ID:</font></b></td>

				<td colspan="2" align="left" valign="middle">care@aasaan.co</td>

				<td style="border-left: 1px solid #000000;" align="left" valign="middle"><b><font color="#000000">GST number:</font></b></td>

				<td colspan="2" style="border-left: 1px solid #000000; border-right: 2px solid #000000;" align="left" valign="middle">27AAPCA6048L1Z4</td>
			</tr>

			<tr height="30" style= "border-bottom: 2px solid #000000;">
				<td style="border-left: 2px solid #000000; border-right: 1px solid #000000;" align="left" valign="middle"><b><font color="#000000">PAN:</font></b></td>

				<td colspan="2" align="left" valign="middle">AAPCA6048L</td>

				<td style="border-left: 1px solid #000000;" align="left" valign="middle"><b><font color="#000000">Contact number:</font></b></td>

				<td colspan="2" style="border-left: 1px solid #000000; border-right: 2px solid #000000;" align="left" valign="middle">91-8369516308</td>
			</tr>

			<tr height="30" style= "border-bottom: 2px solid #000000;">
				<td style="border-left: 2px solid #000000; border-right: 1px solid #000000;" align="left" valign="middle"><b><font color="#000000">CIN:</font></b></td>

				<td colspan="2" align="left" valign="middle">U72900MH2017PTC295223</td>

				<td style="border-left: 1px solid #000000;" align="left" valign="middle"><b><font color="#000000">Date:</font></b></td>

				<td colspan="2" style="border-left: 1px solid #000000; border-right: 2px solid #000000;" align="left" valign="middle">30/05/2018</td>
			</tr>

			<tr height="50">
				<td style="border-bottom: 2px solid #000000; border-top: 2px solid #000000; border-left: 2px solid #000000; border-right: 2px solid #000000;" colspan="6" align="center" valign="center"><b><font size="4" color="#000000"><u>Client details</u></font></b>
				</td>
			</tr>
			<tr height="30" style= "border-bottom: 2px solid #000000;">
				<td style="border-left: 2px solid #000000; border-right: 1px solid #000000;" align="left" valign="middle"><b><font color="#000000">Invoice no:</font></b></td>

				<td colspan="2" align="left" valign="middle"><?php echo $invoice_no; ?></td>

				<td style="border-left: 1px solid #000000;" align="left" valign="middle"><b><font color="#000000">Order no:</font></b></td>

				<td colspan="2" style="border-left: 1px solid #000000; border-right: 2px solid #000000;" align="left" valign="middle"><?php echo $order_no; ?></td>
			</tr>

			<tr height="30" style= "border-bottom: 2px solid #000000;">
				<td style="border-left: 2px solid #000000; border-right: 1px solid #000000;" align="left" valign="middle"><b><font color="#000000">Name of the client:</font></b></td>

				<td colspan="2" align="left" valign="middle"><?php if(!empty($companyInfo->company_name)) echo $companyInfo->company_name; else echo ''; ?></td>

				<td style="border-left: 1px solid #000000;" align="left" valign="middle"><b><font color="#000000">Client admin:</font></b></td>

				<td colspan="2" style="border-left: 1px solid #000000; border-right: 2px solid #000000;" align="left" valign="middle"></td>
			</tr>

			<tr height="30" style= "border-bottom: 2px solid #000000;">
				<td style="border-left: 2px solid #000000; border-right: 1px solid #000000;" align="left" valign="middle"><b><font color="#000000">GST no:</font></b></td>

				<td colspan="2" align="left" valign="middle"><?php echo $company_gst; ?></td>

				<td style="border-left: 1px solid #000000;" align="left" valign="middle"><b><font color="#000000">Contact number:</font></b></td>

				<td colspan="2" style="border-left: 1px solid #000000; border-right: 2px solid #000000;" align="left" valign="middle"><?php echo $company_contact_no ?></td>
			</tr>

			<tr height="30" style= "border-bottom: 2px solid #000000;">
				<td style="border-left: 2px solid #000000; border-right: 1px solid #000000;" align="left" valign="middle"><b><font color="#000000">PAN ID:</font></b></td>

				<td colspan="2" align="left" valign="middle"><?php echo $company_pan; ?></td>

				<td style="border-left: 1px solid #000000;" align="left" valign="middle"><b><font color="#000000">Email ID:</font></b></td>

				<td colspan="2" style="border-left: 1px solid #000000; border-right: 2px solid #000000;" align="left" valign="middle"><?php echo $company_email; ?></td>
			</tr>
			<tr height="50" style="background-color: #95c5d0; ">
				<td style="border-top: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000;" align="center" valign="middle"><b><font color="#000000">Sr. No</font></b></td>

				<td colspan="3" style="width: 50%; border-top: 1px solid #000000;" align="center" valign="middle"><b><font color="#000000">Production  Description</font></b></td>

				<td style="border-left: 1px solid #000000; border-top: 1px solid #000000;" align="center" valign="middle"><b><font color="#000000">SAC Code</font></b></td>

				<td style="border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000;" align="center" valign="middle"><b><font color="#000000">Value of supply</font></b></td>
			</tr>

			<tr height="30">
				<td style="border-left: 2px solid #000000; border-right: 1px solid #000000;" align="center" valign="middle"><b><font color="#000000"></font></b></td>

				<td colspan="3" style="width: 50%;" align="left" valign="middle"><b><font color="#000000"><u><b>The Hajiri app</b></u></font></b></td>

				<td style="border-left: 1px solid #000000;" align="center" valign="middle"><b><font color="#000000"></font></b></td>

				<td style="border-left: 1px solid #000000; border-right: 2px solid #000000;" align="center" valign="middle"><b><font color="#000000"></font></b></td>
			</tr>

			<tr height="30">
				<td style="border-left: 2px solid #000000; border-right: 1px solid #000000;" align="center" valign="middle"><b><font color="#000000"><u>A</u></font></b></td>

				<td colspan="3" style="width: 50%;" align="left" valign="middle"><b><font color="#000000"><b>Hajiri advance module online purchase</b></font></b></td>

				<td style="border-left: 1px solid #000000;" align="center" valign="middle"><font color="#000000">998314</font></td>

				<td style="border-left: 1px solid #000000; border-right: 2px solid #000000;" align="center" valign="middle"><b><font color="#000000"><?php echo 'Rs.'.$planRs; ?></font></b></td>
			</tr>

			<tr height="30">
				<td style="border-left: 2px solid #000000; border-right: 1px solid #000000;" align="center" valign="middle"><b><font color="#000000"></font></b></td>

				<td colspan="3" style="width: 50%;" align="left" valign="middle"><b><font color="#000000"><b><?php echo $plan_type; ?></b></font></b></td>

				<td style="border-left: 1px solid #000000;" align="center" valign="middle"><font color="#000000"></font></td>

				<td style="border-left: 1px solid #000000; border-right: 2px solid #000000;" align="center" valign="middle"><b><font color="#000000"></font></b></td>
			</tr>

			<tr height="30">
				<td style="border-left: 2px solid #000000; border-right: 1px solid #000000;" align="center" valign="middle"><b><font color="#000000"><u>B</u></font></b></td>

				<td colspan="3" style="width: 50%;" align="left" valign="middle"><font color="#000000"><b>Taxes</b></font></td>

				<td style="border-left: 1px solid #000000;" align="center" valign="middle"><font color="#000000"></font></td>

				<td style="border-left: 1px solid #000000; border-right: 2px solid #000000;" align="center" valign="middle"><b><font color="#000000"></font></b></td>
			</tr>

			<tr height="30">
				<td style="border-left: 2px solid #000000; border-right: 1px solid #000000;" align="center" valign="middle"></td>

				<td colspan="3" style="width: 50%;" align="left" valign="middle"><font color="#000000">CGST @ 9%</font></td>

				<td style="border-left: 1px solid #000000;" align="center" valign="middle"><font color="#000000"></font></td>

				<td style="border-left: 1px solid #000000; border-right: 2px solid #000000;" align="center" valign="middle"><b><font color="#000000"><?php echo 'Rs.'.$cgst; ?></font></b></td>
			</tr>

			<tr height="30">
				<td style="border-left: 2px solid #000000; border-right: 1px solid #000000;" align="center" valign="middle"></td>

				<td colspan="3" style="width: 50%;" align="left" valign="middle"><font color="#000000">SGST @ 9%</font></td>

				<td style="border-left: 1px solid #000000;" align="center" valign="middle"><font color="#000000"></font></td>

				<td style="border-left: 1px solid #000000; border-right: 2px solid #000000;" align="center" valign="middle"><b><font color="#000000"><?php echo 'Rs.'.$sgst; ?></font></b></td>
			</tr>

			<tr height="30">
				<td style="border-left: 2px solid #000000; border-right: 1px solid #000000;" align="center" valign="middle"></td>

				<td colspan="3" style="width: 50%;" align="left" valign="middle"><font color="#000000">Grand total</font></td>

				<td style="border-left: 1px solid #000000;" align="center" valign="middle"><font color="#000000"></font></td>

				<td style="border-left: 1px solid #000000; border-right: 2px solid #000000;" align="center" valign="middle"><b><font color="#000000"><?php echo 'Rs.'.$grandTotal; ?></font></b></td>
			</tr>
		</table>
		<table cellspacing="0" border="0" class="" style="height:100px;width:100%; page-break-before: always;">
			<tr height="30">
				<td colspan="6" style="border-top: 2px solid #000000; border-right: 2px solid #000000; border-left: 2px solid #000000;" align="left" valign="middle">Terms and conditions:</td>
			</tr>

			<tr height="30">

				<td style="border-left: 2px solid #000000; border-right: 1px solid #000000;" align="center" valign="middle">1</td>

				<td colspan="5" style="border-right: 2px solid #000000;" align="left" valign="middle"><font color="#000000">The product will be configured as per the project and is expected to run according to the data provided by the client.</font></td>
			</tr>

			<tr height="30">

				<td style="border-left: 2px solid #000000; border-right: 1px solid #000000;" align="center" valign="middle">2</td>

				<td colspan="5" style="border-right: 2px solid #000000;" align="left" valign="middle"><font color="#000000">Product configuration shall commence only after the project information and advance payments are received from client.</font></td>
			</tr>

			<tr height="30">

				<td style="border-left: 2px solid #000000; border-right: 1px solid #000000;" align="center" valign="middle">3</td>

				<td colspan="5" style="border-right: 2px solid #000000;" align="left" valign="middle"><font color="#000000">Training sessions shall be conducted for the users.</font></td>
			</tr>

			<tr height="30">
				<td colspan="6" style="border-top: 1px solid #000000; border-right: 2px solid #000000; border-left: 2px solid #000000;" align="right" valign="middle">Ceritified that the particuler given above are ture and correct</td>
			</tr>

			<tr rowspan="2" height="30">
				<td colspan="6" style="border-right: 2px solid #000000; border-left: 2px solid #000000;" align="right" valign="middle"><b>For Aasaan Tech Pvt. Ltd.</b><br /><img src='<?php echo base_url('assets/admin/images/invoice-signature.jpg'); ?>' style="width:100px; height: 50px; margin:10px;"/><br />Hemil Parekh</td>
			</tr>
		</table>

		<?php $contents = ob_get_contents();
		if(!empty($companyInfo->company_name)) {
			$company_name = $companyInfo->company_name;
		}else{
			$company_name = '';
		}
        ob_end_clean();
		$this->load->library('Dom_pdf');
	    // Convert to PDF
	    $this->dompdf->load_html($contents);
	    $this->dompdf->render();
	    $this->dompdf->stream($company_name."_invoice", array("Attachment" => True));
	}
}