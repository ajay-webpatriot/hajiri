<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ini_set('max_execution_time', 0); 
ini_set('memory_limit','2048M');

class qr_codes extends CI_Controller {

    public $data;
    public function __construct() {
        parent::__construct();
        $this->data['menu_title'] = 'Generate_idcards';
        $this->load->model('QrCodes_model', 'qrcode');
        $this->load->model('Category_model', 'category');
        $this->load->model('Companies_model', 'company');
        $this->load->model('GenerateID_model', 'report');
    }

    /* Generate Id Card */
    function index() {
       
        $data = $this->data;
        if ( isset( $_POST['qrStamp'] ) ) {
            $this->generate_qr_stamp();
        }elseif ( isset( $_POST['qrIdCard'] ) ) {
            $this->generate_qr_id_card();
        }elseif(isset( $_GET['submit'] )){
            if($_GET['submit'] == 'qrStamp')
                $this->generate_qr_stamp();
            elseif($_GET['submit'] == 'qrIdCard')
                $this->generate_qr_id_card();
        }

        $data['Category'] = $this->category->get_all_category($this->session->userdata('company_id'));

        $data['title'] = 'Generate QR Codes';
        $data['description'] = '';
        $data['companies'] = $this->company->getAllRecords('company', array( 'status' => 1 ), 'company_name ASC' );
        if( $this->session->userdata('user_designation') != 'Superadmin' ) {
            $data['labours'] = $this->company->getAllRecords('worker', array( 'company_id' => $this->session->userdata('company_id'), 'status' => 1 ), 'labour_name ASC' );
        }
        $data['page'] = 'qr_codes/qr_id_cards';
        $this->load->view('includes/template', $data);
    }

    public function workerDatatable()
        {
            
            $columns = array( 
                                0 =>'worker_id', 
                                1 =>'labour_name', 
                                2 =>'labour_last_name', 
                                3 =>'category_name',
                            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
      
            $totalData = $this->qrcode->allworker_count();
                
            $totalFiltered = $totalData; 
                
            if(!empty($this->input->post('date')) && !empty($this->input->post('category')) && !empty($this->input->post('search')['value'])){
                $date = date("Y-m-d", strtotime($this->input->post('date'))); 
                $category = $this->input->post('category');                
				$search = $this->input->post('search')['value'];

                $where =  'worker.labour_join_date LIKE "'.$date.'%" AND worker_category.category_name = "'. $category.'" AND labour_name like "%'.$search.'%" OR labour_last_name like "%'.$search.'%"';
                $posts =  $this->qrcode->attendance_where_search($limit,$where,$start,$order,$dir);
                $totalFiltered = $this->qrcode->attendance_where_search_count($where);
                
            }elseif(!empty($this->input->post('date')) && !empty($this->input->post('category')) ){
                $date = date("Y-m-d", strtotime($this->input->post('date'))); 
                $category = $this->input->post('category');

                $where =  'worker.labour_join_date LIKE "'.$date.'%" AND worker_category.category_name = "'. $category.'"';

                $posts =  $this->qrcode->attendance_where_search($limit,$where,$start,$order,$dir);
                $totalFiltered = $this->qrcode->attendance_where_search_count($where);
            }elseif(!empty($this->input->post('date')) && !empty($this->input->post('search')['value'])){
                $date = date("Y-m-d", strtotime($this->input->post('date'))); 
				$search = $this->input->post('search')['value'];

                $where =  'worker.labour_join_date LIKE "'.$date.'%" AND labour_name like "%'.$search.'%" OR labour_last_name like "%'.$search.'%"';
                $posts =  $this->qrcode->attendance_where_search($limit,$where,$start,$order,$dir);
                $totalFiltered = $this->qrcode->attendance_where_search_count($where);
                
            }elseif(!empty($this->input->post('category')) && !empty($this->input->post('search')['value'])){
                $category = $this->input->post('category');                
				$search = $this->input->post('search')['value'];

                $where =  'worker_category.category_name = "'. $category.'" AND labour_name like "%'.$search.'%" OR labour_last_name like "%'.$search.'%"';
                $posts =  $this->qrcode->attendance_where_search($limit,$where,$start,$order,$dir);
                $totalFiltered = $this->qrcode->attendance_where_search_count($where);
                
            }
            elseif(!empty($this->input->post('category'))){
                $search = $this->input->post('category'); 

                $posts =  $this->qrcode->worker_col_search($limit,'category_name',$start,$search,$order,$dir);

                $totalFiltered = $this->qrcode->worker_custom_search_count($search,'category_name');

            }elseif(!empty($this->input->post('date'))){
                $search = date("Y-m-d", strtotime($this->input->post("date") )); 

                $posts =  $this->qrcode->worker_col_search($limit,'labour_join_date',$start,$search,$order,$dir);

                $totalFiltered = $this->qrcode->worker_custom_search_count($search,'labour_join_date');

            }elseif(empty($this->input->post('search')['value']))
            {            
                $posts = $this->qrcode->allworker($limit,$start,$order,$dir);
            }
            else {
                $search = $this->input->post('search')['value']; 

                $posts =  $this->qrcode->worker_search($limit,$start,$search,$order,$dir);

                $totalFiltered = $this->qrcode->worker_search_count($search);
            }

            $data = array();
            if(!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $nestedData['worker_id'] = $post->worker_id;
                    $nestedData['labour_name'] = '<p class="capitalize">'.ucwords(strtolower($post->labour_name)).' </p>';
                    $nestedData['labour_last_name'] = '<p class="capitalize"> '.ucwords(strtolower($post->labour_last_name)).'</p>';
                    $nestedData['category_name'] = $post->category_name;
                    $data[] = $nestedData;

                }
            }
              
            $json_data = array(
                        "draw"            => intval($this->input->post('draw')),  
                        "recordsTotal"    => intval($totalData),  
                        "recordsFiltered" => intval($totalFiltered), 
                        "data"            => $data   
                        );
                
            echo json_encode($json_data); 
    }
    
    function generate_qr_id_card(){
        
        $requestData = array();
        if(isset( $_GET['submit'] )){
            $requestData['company_id'] = (isset($_GET['company_id']) ? $_GET['company_id'] : 0);
            $requestData['labour_ids'] = (isset($_GET['labour_id']) ? $_GET['labour_id'] : array());
            $requestData['join_date'] = $requestData['all_labour'] = '';
        }else{
            $requestData['company_id'] = $this->input->post('company_id') ? $this->input->post('company_id') : $this->session->userdata('company_id');
            $requestData['all_labour'] = $this->input->post('all_labour');
            $requestData['join_date'] = ( $this->input->post('join_date') ? Date( 'Y-m-d', strtotime( $this->input->post('join_date') ) ) : '' );
            $requestData['labour_ids'] = ( $this->input->post('labour_id') ? $this->input->post('labour_id') : array() );
        }
        
        $companyInfo = $this->db->where('compnay_id', $requestData['company_id'])->get('company')->row_array();
        
        $BARCODES = $this->qrcode->getLabours( $requestData );
        
        if ( empty( $BARCODES ) ) {
            $this->session->set_flashdata('error', 'No data found.');
            echo json_encode( array('message'=>'No data found', 'status' =>200 ) ); 
            redirect(base_url('admin/qr_codes'));
        }
        $pdfWidth = count( $BARCODES ) > 1 ? '100%' : '50%'; 
        $BARCODES = array_chunk($BARCODES, 2);
        ob_start();
        ?>

        <style>
       
            @page {size: a4 portrait;margin:2em 0 0 0;padding:0.0;}
            .first {font-size: 20px; width:<?php echo $pdfWidth; ?>;}
            .main-container {border: 1px solid #000000;float: left;}
            .width50{width:50%;}
            .header-image{width: 100px;}
            .qr-image{height: 150px; width:150px;}
            .labour-details div{font-size: 14px, font-weight: 300; height: 30px;}
            .name{text-transform: capitalize !important;}
            .marginTop{display: block; width: 100%; height: 2em;}
        </style>
        <table class="first">
            <?php if ($BARCODES) { 
                 $count = 0;
                 foreach ($BARCODES AS $key => $BARCODE) { 
                    ?>
                <tr>
                    <?php foreach ($BARCODE AS $key =>$B) { ?>
                    <?php 

                    if(($count % 2) == 0) {?>
                    <td style="width:5%;"></td>
                    <?php }?>
                    <td style="width:45%; margin:0 5% !important; border: 1px solid #000000;"> 
                        <table style="width:100%; padding: 0px !important; margin: 0 !important;">
                            <tr>
                                <td style="width:5%"></td>
                                <td style="width:30%; margin: 5px 0 0 0;" valign="top"> <?php if( !empty( $companyInfo['company_logo_image'] ) && file_exists( 'uploads/user/'.$companyInfo['company_logo_image'] ) ) { ?><img class="header-image" src="<?php echo base_url('uploads/user/').$companyInfo['company_logo_image']; ?>">  <?php } ?></td>
                                <td style="width:63%; text-align:center;"> 
                                    <span style="font-size: 15px, font-weight: 600; text-transform: uppercase; width: 100%;"> 
                                        <u><?php echo $companyInfo['company_name']; ?></u> 
                                    </span>
                                    <br>
                                    <span style="font-size: 16px, font-weight: 300; width: 100%;"> Hajiri ID-card </span> 
                                </td>
                                <td style="width:20%"></td>
                            </tr>
                        </table>
                        <table style="width:100%; padding: 0px !important; margin: 0 !important;">
                            <tr>
                                <td style="width:40%" valign="top"> <?php if( !empty( $B->worker_qrcode_image ) && file_exists( 'uploads/barcodes/'.$B->worker_qrcode_image ) ) { ?> <img class="qr-image" src="<?php echo base_url('uploads/barcodes/').$B->worker_qrcode_image; ?>"> <?php } ?></td>
                                <td class="labour-details" valign="top" style="width:60%, "> 
                                    <div class="name" style=" margin-top: 10px;"><?php echo $B->labour_name.' '.$B->labour_last_name; ?></div>
                                    <div><?php echo $B->category_name; ?> </div>
                                    <div><?php echo $B->worker_contact; ?></div>
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
                            echo '<div class="marginTop"></div>';
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
        $this->dompdf->set_option('isHtml5ParserEnabled', true);
        $this->dompdf->render();
        $this->dompdf->stream("Hajiri QR ID cards.pdf", array("Attachment" => True));
    }

    function generate_qr_stamp(){
        $requestData = array();
        if(isset( $_GET['submit'] )){
            $requestData['company_id'] = (isset($_GET['company_id']) ? $_GET['company_id'] : 0);
            $requestData['labour_ids'] = (isset($_GET['labour_id']) ? $_GET['labour_id'] : array());
            $requestData['join_date'] = $requestData['all_labour'] = '';
        }else{
            $requestData['company_id'] = $this->input->post('company_id') ? $this->input->post('company_id') : $this->session->userdata('company_id');
            $requestData['all_labour'] = $this->input->post('all_labour');
            $requestData['join_date'] = ( $this->input->post('join_date') ? Date( 'Y-m-d', strtotime( $this->input->post('join_date') ) ) : '' );
            $requestData['labour_ids'] = ( $this->input->post('labour_id') ? $this->input->post('labour_id') : array() );
        }
        
        $companyInfo = $this->db->where('compnay_id', $requestData['company_id'])->get('company')->row_array();
        
        $BARCODES = $this->qrcode->getLabours( $requestData );
        
        if ( empty( $BARCODES ) ) {
            $this->session->set_flashdata('error', 'No data found.');
            echo json_encode( array('message'=>'No data found', 'status' =>200 ) ); 
            redirect(base_url('admin/qr_codes'));
        }
        $BARCODES = array_chunk($BARCODES, 4);
        ob_start();
        ?>
        <style>
            @page {size: a4 portrait;margin:2em 0 0 0;padding:0.0;}
            .first {font-family: arial;font-size: 16px;}
            .div_td {border: 1px solid #000000;float: left;}
            .div_td p {text-align: center;margin-top: 0px; font-size: 14px;margin-bottom: 5px; text-transform: capitalize;}
            .aasaanLogo{height:10px; width:100px; margin-top:20px;}
            .qrCode{width: 96%;}
            .qr-image{height: 160px; width:160px;}
            .name{text-transform: capitalize !important;}
        </style>
        <div class="first">
            <?php if ($BARCODES) {  $count = 0;?>
                <?php foreach ($BARCODES AS $BARCODE) { ?>
                    <?php foreach ($BARCODE AS $B) { $count++;?>
                        <div class="div_td" style="width: 25%;">
                            <center><img src='<?php echo base_url('assets/admin/images/aasaan_pdf_logo.png'); ?>' class='aasaanLogo' /></center>
                            <?php $url = base_url('uploads/barcodes/') . $B->worker_qrcode_image; ?>
                            <center><img src="<?php echo $url; ?>" class='qr-image'></center>
                            
                            <p class="name"><?php echo substr($B->labour_name,0,15)." ".substr($B->labour_last_name,0,15)."<br>"; ?></p>
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
        //print_r($requestData);
        $this->load->library('Dom_pdf');
        $this->dompdf->load_html($contents);
        $this->dompdf->render();
        $this->dompdf->stream("Hajiri QR stamps.pdf", array("Attachment" => True));
    
    }

    public function ajax_get_categoryList($id) {
        $prjlist = "";
        $category = $this->category->get_all_category($id);
        if (!empty($category)) {
            $prjlist .= '<select name="category" class="form-control category">';
            $prjlist .= '<option value = "">Select Category</option>';
            foreach ($category as $data) {
                $prjlist .= "<option value = '" . $data->id . "'>" . $data->category . "</option>";
            }
            $prjlist .= '</select>';
        } else {
            $prjlist .= '<select name="category" class="form-control">';
            $prjlist .= '<option value = "">--No Category Available--</option>';
            $prjlist .= '</select>';
        }
        $result['projectlist'] = $prjlist;
        echo json_encode($result);
    }

    public function ajax_get_labours($company_id) {
        $where = 'company_id = "' .$company_id.'"';
        $labours = $this->qrcode->get_labours($where);
       
        $labours_html = '<option value="">Select Workers</option>';
        if ($labours) {
            foreach ($labours as $labour) {
                $labours_html .= '<option value="' . $labour->worker_id . '">' . $labour->labour_name .' '. $labour->labour_last_name . '</option>';
            }
        }
        echo json_encode(array("status" => true, "labours_html" => $labours_html));
    }

    function ajax_get_filter_workers(){
        if($this->input->post('date') != '' && $this->input->post('category_id') != ''){
            $joinDate = date("Y-m-d", strtotime($this->input->post('date')));
            $where = "company_id = '". $this->input->post('company_id') . "' AND category_id = '".$this->input->post('category_id') . "' AND labour_join_date = '" . $joinDate . "'";
        }elseif($this->input->post('date') != ''){
            $joinDate = date("Y-m-d", strtotime($this->input->post('date')));
            $where = "company_id = '". $this->input->post('company_id') . "' AND labour_join_date = '" . $joinDate . "'";
        }elseif($this->input->post('category_id') != ''){
            $joinDate = date("Y-m-d", strtotime($this->input->post('date')));
            $where = "company_id = '". $this->input->post('company_id') . "' AND category_id = '" . $this->input->post('category_id') . "'";
        }
        $filter = $this->qrcode->get_labours($where);
       
        $filter_html = '<option value="">Select Workers</option>';
        if ($filter) {
            foreach ($filter as $labour) {
                $filter_html .= '<option value="' . $labour->worker_id . '">' . $labour->labour_name .' '. $labour->labour_last_name . '</option>';
            }
        }
        echo json_encode(array("status" => true, "labours_html" => $filter_html));
    }

}
