<style type="text/css">#table_filter{margin: 10px 0 0 20px;}</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo (isset($title) ? $title : ''); ?>
            <small></small>
        </h1>
    </section>
    <ol class="breadcrumb margin-bottom0">
        <li><a href="<?php echo base_url('admin'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active"><?php echo (isset($title) ? $title : ''); ?></li>
    </ol>
    <section class="content container-fluid">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><?php echo (isset($title) ? $title : ''); ?></h3>
                    <div class="box-tools pull-right">
                        <?php 
                            if($limit->wLimit > 0 || $planId->id == 3){
                        ?>
                        <!-- Add button -->
                        <a href="<?php echo base_url('admin/workerRegister/worker/');   ?>">  
                            <button class="btn btn-info">
                                <i class="glyphicon glyphicon-plus"></i> 
                                Add worker 
                                <?php if($planId->id != 3)echo '('.$limit->wLimit.')'; ?>
                            </button>
                        </a>
                        <?php 
                            }
                        ?>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive">
                    <?php if ($this->session->flashdata('success') != ''): ?>
                        <div class="alert alert-success alert-dismissable">
                            <i class="fa fa-check"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <b>Success!</b> 
                            <?php echo $this->session->flashdata('success'); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($this->session->flashdata('warning') != ''): ?>
                        <div class="alert alert-warning alert-dismissable">
                            <i class="fa fa-check"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <b>Warning!</b> 
                            <?php echo $this->session->flashdata('warning'); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($this->session->flashdata('error') != ''): ?>
                        <div class="alert alert-error alert-dismissable">
                            <i class="fa fa-check"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <b>Error!</b> 
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>

                   <div class="filters col-md-12" id='fixme'>
                        <h4>Filters:</h4>
                        <label class="col-md-1 control-label">Category:</label>
                        <div class="col-md-3">
                            <select class="form-control category" name="category">
                                <option value="">All Category </option>
                                <?php 
                                    foreach ($Category as $proj) {
                                ?>
                                <option value="<?php echo $proj->category; ?>"><?php echo $proj->category; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                     
                        <div id="actionButton" class='col-md-8'>
                            <?php if (in_array("2", $this->session->userdata('permissions'))){ ?>
                                <button class="btn btn-sm btn-success present" data-toggle="modal" data-target="#absentModal" title="Mark present" >
                                   Mark present 
                                </button>
                                <button class="btn btn-sm btn-warning paidLeave" data-toggle="modal" data-target="#paidLeaveModal" title="Paid leave" >
                                   Paid Leave 
                                </button>
                            <?php }else{?>
                                <button class="btn btn-sm btn-default disabled" title="Mark present" >
                                   Mark present 
                                </button>
                                <button class="btn btn-sm btn-default disabled" title="Mark present" >
                                   Paid Leave
                                </button>
                            <?php }
                            if (in_array("3", $this->session->userdata('permissions'))){
                            ?>
                                <button class="btn btn-sm btn-info makePayment" data-toggle="modal" data-target="#paymentModal" title="Make payment">
                                   Make payment 
                                </button>
                            <?php }else{ ?>  
								<button class="btn btn-sm btn-default disabled" title="Make payment" >
                                   Make Payment
                                </button>
							<?php } ?>
                        </div> <!-- End of acton button Div -->
                    </div>

                        <?php if (in_array("2", $this->session->userdata('permissions'))){ ?>
                            <div class="modal fade" id="absentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="btn btn-danger close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title col-md-4" id="exampleModalLabel">
                                                Mark Present
                                            </h4>
                                            <div class="col-md-7" style="text-align: right;">
                                            <input type="checkbox" id="presentAll">
                                                    Mark All Present
                                        </div>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row rowHeader">
                                                <div class="col-md-12">
                                                    <div class="col-md-3">Select date:</div>
                                                    <div class="col-md-4">Select project:</div>
                                                    <div class="col-md-5">Present reason:</div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="col-md-3">
                                                        <input id="dateProjSelectAll" placeholder="Select Date" class="form-control datepicker col-xs-12" type="text" readonly>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select class="form-control" id='presentProjSelectAll'>
                                                            <option >Select project</option>
															<?php
																foreach ($projects as $data) {
																	echo "<option  value='" . $data->project_id . "'>" . $data->project_name . "</option>";
																}
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-5 makePayment">
                                                        <textarea class="form-control" id='presentReasonSelectAll'  maxlength='160' placeholder="Reason for present"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row bulkDetailsHeader">
                                                <div class="presentAlert"></div>
                                                <div class="col-md-3">Name</div>
                                                <div class="col-md-3">Date</div>
                                                <div class="col-md-3">Project</div>
                                                <div class="col-md-3">Reason</div>
                                            </div>

                                            <div class="presentDetails"></div>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-success" id='markPresentSubmit'>Submit</button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
							<!--------------------- Paid Leave modal -------------------->
							<div class="modal fade" id="paidLeaveModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
                                <div class="modal-dialog modal-md" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="btn btn-danger close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title col-md-4" id="exampleModalLabel">
                                                Paid leave
                                            </h4>
                                            <div class="col-md-7" style="text-align: right;">
                                            <input type="checkbox" id="paidLeaveAll">
                                            Mark all Paid Leave 
                                        </div>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row rowHeader">
                                                <div class="col-md-12">
                                                    <div class="col-md-6">Select date:</div>
                                                    <div class="col-md-6">Select project:</div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="col-md-6">
                                                        <input id="paidLeaveDateAll" placeholder="Select Date" class="form-control datepicker col-xs-12" type="text" readonly>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="form-control" id='paidLeaveProjectAll'>
                                                            <option >Select project</option>
															<?php
                                                                foreach ($projects as $data) {
                                                                    echo "<option  value='" . $data->project_id . "'>" . $data->project_name . "</option>";
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row bulkDetailsHeader">
                                                <div class="paidLeaveAlert"></div>
                                                <div class="col-md-4">Name</div>
                                                <div class="col-md-4">Date</div>
                                                <div class="col-md-4">Project</div>
                                            </div>
                                            <div class="paidLeaveDetails"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-success" id='paidLeaveSubmit'>Submit</button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }?>
						<?php 
						if (in_array("3", $this->session->userdata('permissions'))){
                            ?>
							<!--------------------- Make Payment modal -------------------->
							<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
                                <div class="modal-dialog modal-md" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="btn btn-danger close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title col-md-4" id="exampleModalLabel">
                                                Make Payment
                                            </h4>
                                            <div class="col-md-7" style="text-align: right;">
                                            <input type="checkbox" id="makePaymentAll">
                                            Make Payment All
                                        </div>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row rowHeader">
                                                <div class="col-md-12">
                                                    <div class="col-md-6">Enter amount:</div>
                                                    <div class="col-md-6">Select project:</div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="col-md-6">
                                                        <input id="makePaymentAmountAll" placeholder="Enter amount" class="form-control col-xs-12" type="number" >
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="form-control" id='makePaymentProjectAll'>
                                                            <option >Select project</option>
															<?php
																foreach ($projects as $data) {
																	echo "<option  value='" . $data->project_id . "'>" . $data->project_name . "</option>";
																}
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row bulkDetailsHeader">
                                                <div class="paymentAlert"></div>
                                                <div class="col-md-4">Name</div>
                                                <div class="col-md-4">Amount</div>
                                                <div class="col-md-4">Project</div>
                                            </div>
                                            <div class="makePaymentDetails"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-success" id='makePaymentSubmit'>Submit</button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
						<?php } ?>
                    <table id="table" class="WR tableFilter table table-striped table-hover table-bordered display responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Worker name</th> 
                                <th>Category name</th>
                                <th>Due amount</th>
                                <th>Action</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- /.content -->

<script type="text/javascript"> 
    window.onscroll = function() {myFunction()};    
    var navbar = document.getElementById("fixme");
    var sticky = navbar.offsetTop;

    function myFunction() {
      if (window.pageYOffset >= sticky+50) {
        navbar.classList.remove("filterScroll");
        navbar.classList.add("filterFixed");
        $('.box').addClass('divContent');
      } else {
        navbar.classList.remove("filterFixed");
        navbar.classList.add("filterScroll")
        $('.box').removeClass('divContent');
      }
    }
	
    $(document).ready(function() {
        //Mark present
        $(".rowHeader").hide();
		
        $( ".datepicker" ).datepicker({
            defaultDate: new Date(),
            format: 'dd-mm-yyyy',
			<?php 
				if( date('d') > 20){
					$current = (date('d') - 1);
					echo "startDate: '-".$current."d',";
				}else{
					echo "startDate: '-".date('01-m-Y', strtotime('-1 MONTH'))."',";
				}
			?>
            endDate: '+0d',
            autoclose: true
        });

        $('.category').change(function () {
            var categoryName = $('.category').val();
            table.draw();

        });
        $('.alert').fadeOut(7000); //remove suucess message
        // DataTable
        var table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "serverSide": true,
            "select": true,
            "responsive": true,
            'select': {
                'style': 'multi'
            },
            "drawCallback": function( settings ) {
                $( ".datepicker" ).datepicker({
                    defaultDate: new Date(),
                    format: 'dd-mm-yyyy',
					<?php 
						if( date('d') > 20){
							$current = (date('d') - 1);
							echo "startDate: '-".$current."d',";
						}else{
							echo "startDate: '".date('01-m-Y', strtotime('-1 MONTH'))."',";
						}
					?>
                    endDate: '+0d',
                    autoclose: true
                });
                if(table.column(0).checkboxes.selected().length == 0){
                    $('.present').attr("disabled", true);
                    $('.paidLeave').attr("disabled", true);
                    $('.makePayment').attr("disabled", true);
                }
            },
            "ajax":{
                "url": "<?php echo base_url('admin/workerRegister/workerDatatable') ?>",
                "dataType": "json",
                "type": "POST",
                "data":function(data) {
                    data.category = $('.category').val();
                    data.<?php echo $this->security->get_csrf_token_name(); ?> = "<?php echo $this->security->get_csrf_hash(); ?>";
                },
            },
			"columns": [
				{ "data": "worker_id" },
				{ "data": "labour_name" },
				{ "data": "category_name" },
				{ "data": "worker_due_wage" },
				{ "data": "action" },
				{ "data": "status" },
			],
            columnDefs: [
            {
                "targets": [0],
                'checkboxes': {
                    'selectRow': true
                },
                "visible": true,
                "searchable": false,
                "sortable":false,
                "type": "string"
            },
            {
                "targets": [1],
                "visible": true,
                "searchable": true,
                "type": "string"
            },
            {
                "targets": [2],
                "visible": true,
                "searchable": true,
                "type": "string"
            },
            {
                "targets": [3],
                "visible": true,
                "sortable":false,
                "searchable": true,
                "type": "num"
            },

            {
                "targets": [4],
                "visible": true,
                "sortable":false,
                "searchable": false,
                "type": "string"
            },
            {
                "targets": [5],
                "visible": false,
                "sortable":false,
                "searchable": false,
                "type": "string"
            }
        ]
        });
        
        table
        .on( 'select', function ( e, dt, type, indexes ) {
            var rowData = table.column(0).checkboxes.selected();
            $('.present').attr("disabled", false);
            $('.paidLeave').attr("disabled", false);
            $('.makePayment').attr("disabled", false);
        } )
        .on( 'deselect', function ( e, dt, type, indexes ) {
            var rowData = table.column(0).checkboxes.selected();
            if(table.column(0).checkboxes.selected().length == 0){
				$('.present').attr("disabled", true);
				$('.paidLeave').attr("disabled", true);
				$('.makePayment').attr("disabled", true);
            }
        });

        /************ Mark present *********************/

        $('#presentAll').on('click', function(){
            if ( $(this).is(':checked') ) {
                $(".rowHeader").slideDown("slow");
            } 
            else {
                $(".rowHeader").slideUp("slow");
            }
        });
        
        $('.present').click(function(){
            //--- Hide multi select and reset its value
            $('#presentAll').prop('checked', false);
            $(".rowHeader").slideUp("slow");
            $('#dateProjSelectAll').val('');
            $('#presentProjSelectAll').val('');
            $('#presentReasonSelectAll').val('');

            var presentData = [];
            $('.presentDetails').html('');
            var presentData =  table.rows('.selected').data();
            for (var i=0; i < presentData.length; i++){
                $('.presentDetails').append("<div class='row table-striped'> <input type='text' name='id' id='worker_id' class='hidden' value='" + presentData[i]['worker_id'] + "' /> <div class='col-md-3'> " + presentData[i]['labour_name'] + "</div>  <div class='col-md-3'><input type='text' placeholder='Select date' class='form-control date' name='dates' readonly id='date"+presentData[i]['worker_id']+"' /> </div> <div class='col-md-3'><select class='form-control projectChangeList' name='projects' id='project"+presentData[i]['worker_id']+"'><option value='' selected>Select project</option></select></div> <div class='col-md-3 makePayment'><textarea class='form-control' name='reasons' placeholder='Present reason' maxlength='160' id='presentReason"+presentData[i]['worker_id']+"' ></textarea></div></div>");
            }
            <?php 
                foreach ($projects as $data) { ?>
                    $('.projectChangeList').append("<option  value='<?php echo $data->project_id ?>'> <?php echo $data->project_name ?></option>");
            <?php }
            ?>
            $( ".date" ).datepicker({
                defaultDate: new Date(),
                format: 'dd-mm-yyyy',
                <?php 
                    if( date('d') > 20){
                        $current = (date('d') - 1);
                        echo "startDate: '-".$current."d',";
                    }else{
                        echo "startDate: '-".date('01-m-Y', strtotime('-1 MONTH'))."',";
                    }
                ?>
                endDate: '+0d',
                autoclose: true
            });
        });
        
        $('#presentProjSelectAll').on('change',function(){
            var presentId =  table.rows('.selected').data();
            for (var i=0; i < presentId.length; i++){                
                $('#project'+presentId[i]['worker_id']).val($('#presentProjSelectAll').val());
            }
        });

        $('#dateProjSelectAll').on('change',function(){
            var presentId =  table.rows('.selected').data();
            for (var i=0; i < presentId.length; i++){                
                $('#date'+presentId[i]['worker_id']).val($('#dateProjSelectAll').val());
            }
        });

        $('#presentReasonSelectAll').on('input',function(){
            var presentId =  table.rows('.selected').data();
            for (var i=0; i < presentId.length; i++){                
                $('#presentReason'+presentId[i]['worker_id']).val($('#presentReasonSelectAll').val());
            }
        });

        $('#markPresentSubmit').click(function(e){
            var fMarkPresentId = [];
            var Id =  table.rows('.selected').data();
            var error = 0;
            var counter = 0;
            for (var i=0; i < Id.length; i++){
                fMarkPresentId.push(counter,Id[i]['worker_id']);
                counter++;
                if($('#date'+Id[i]['worker_id']).val() != ''){
                    $('#date'+Id[i]['worker_id']).css("border","1px solid green");
                    fMarkPresentId.push(counter,$('#date'+Id[i]['worker_id']).val());
                    counter++;
                }else{
                    $('#date'+Id[i]['worker_id']).css("border","1px solid red");
                    error = 1;
                }
                if($('#project'+Id[i]['worker_id']).val() != ''){
                    $('#project'+Id[i]['worker_id']).css("border","1px solid green");
                    fMarkPresentId.push(counter,$('#project'+Id[i]['worker_id']).val());
                    counter++;
                }else{
                    $('#project'+Id[i]['worker_id']).css("border","1px solid red");
                    error = 1;
                }
                if($('#presentReason'+Id[i]['worker_id']).val() != ''){
                    $('#presentReason'+Id[i]['worker_id']).css("border","1px solid green");
                    fMarkPresentId.push(counter,$('#presentReason'+Id[i]['worker_id']).val());
                    counter++;
                }else{
                    $('#presentReason'+Id[i]['worker_id']).css("border","1px solid red");
                    error = 1;
                }
            }
            if(error == 0){
                worker_mark_present(fMarkPresentId);
            }else
                alert('Kindly, fill all details');
        }); 

		
        /************ Paid leave *********************/

        $('#paidLeaveAll').on('click', function(){
            if ( $(this).is(':checked') ) {
                $(".rowHeader").slideDown("slow");
            } 
            else {
                $(".rowHeader").slideUp("slow");
            }
        });
        
        $('.paidLeave').click(function(){
            //--- Hide multi select and reset its value
            $('#paidLeaveAll').prop('checked', false);
            $(".rowHeader").slideUp("slow");
            $('#paidLeaveDateAll').val('');
            $('#paidLeaveProjectAll').val('');

            var paidLeaveData = [];
            $('.paidLeaveDetails').html('');
            var paidLeaveData =  table.rows('.selected').data();
            for (var i=0; i < paidLeaveData.length; i++){
                $('.paidLeaveDetails').append("<div class='row table-striped'> <input type='text' name='id' id='paidLeaveWorkerId' class='hidden' value='" + paidLeaveData[i]['worker_id'] + "' /> <div class='col-md-4'> " + paidLeaveData[i]['labour_name'] + "</div>  <div class='col-md-4'><input type='text' placeholder='Select date' class='form-control date' name='dates' readonly id='paidLeaveDate"+paidLeaveData[i]['worker_id']+"' /> </div> <div class='col-md-4'><select class='form-control projectChangeList' name='projects' id='paidLeaveProject"+paidLeaveData[i]['worker_id']+"'><option value='' selected>Select project</option></select></div> </div>");
            }
            <?php 
                foreach ($projects as $data) { ?>
                    $('.projectChangeList').append("<option  value='<?php echo $data->project_id ?>'> <?php echo $data->project_name ?></option>");
            <?php }
            ?>
            $( ".date" ).datepicker({
                defaultDate: new Date(),
                format: 'dd-mm-yyyy',
                <?php 
                    if( date('d') > 20){
                        $current = (date('d') - 1);
                        echo "startDate: '-".$current."d',";
                    }else{
                        echo "startDate: '-".date('01-m-Y', strtotime('-1 MONTH'))."',";
                    }
                ?>
                endDate: '+0d',
                autoclose: true
            });
        });
        
        $('#paidLeaveProjectAll').on('change',function(){
            var presentId =  table.rows('.selected').data();
            for (var i=0; i < presentId.length; i++){                
                $('#paidLeaveProject'+presentId[i]['worker_id']).val($('#paidLeaveProjectAll').val());
            }
        });

        $('#paidLeaveDateAll').on('change',function(){
            var presentId =  table.rows('.selected').data();
            for (var i=0; i < presentId.length; i++){                
                $('#paidLeaveDate'+presentId[i]['worker_id']).val($('#paidLeaveDateAll').val());
            }
        });

        $('#paidLeaveSubmit').click(function(e){
            var fPaidLeaveId = [];
			var Id = '';
            var Id =  table.rows('.selected').data();
            var error = 0;
            var counter = 0;
            for (var i=0; i < Id.length; i++){
                fPaidLeaveId.push(counter,Id[i]['worker_id']);
                counter++;
                if($('#paidLeaveDate'+Id[i]['worker_id']).val() != ''){
                    $('#paidLeaveDate'+Id[i]['worker_id']).css("border","1px solid green");
                    fPaidLeaveId.push(counter,$('#paidLeaveDate'+Id[i]['worker_id']).val());
                    counter++;
                }else{
                    $('#paidLeaveDate'+Id[i]['worker_id']).css("border","1px solid red");
                    error = 1;
                }
                if($('#paidLeaveProject'+Id[i]['worker_id']).val() != ''){
                    $('#paidLeaveProject'+Id[i]['worker_id']).css("border","1px solid green");
                    fPaidLeaveId.push(counter,$('#paidLeaveProject'+Id[i]['worker_id']).val());
                    counter++;
                }else{
                    $('#paidLeaveProject'+Id[i]['worker_id']).css("border","1px solid red");
                    error = 1;
                }
            }
            if(error == 0){
                paidLeave(fPaidLeaveId);
            }else
                alert('Kindly, fill all details');
        }); 

		/************ Make Payment *********************/

        $('#makePaymentAll').on('click', function(){
            if ( $(this).is(':checked') ) {
                $(".rowHeader").slideDown("slow");
            } 
            else {
                $(".rowHeader").slideUp("slow");
            }
        });
        
        $('.makePayment').click(function(){
            //--- Hide multi select and reset its value
            $('#makePaymentAll').prop('checked', false);
            $(".rowHeader").slideUp("slow");
            $('#makePaymentAmountAll').val('');
            $('#makePaymentProjectAll').val('');
            
            var makePaymentData = [];
            $('.makePaymentDetails').html('');
            var makePaymentData =  table.rows('.selected').data();
            for (var i=0; i < makePaymentData.length; i++){
                $('.makePaymentDetails').append("<div class='row table-striped'> <input type='text' name='id' id='makePaymentWorkerId' class='hidden' value='" + makePaymentData[i]['worker_id'] + "' /> <div class='col-md-4'> " + makePaymentData[i]['labour_name'] + "</div>  <div class='col-md-4'><input type='number' placeholder='Add amount' class='form-control' name='amount' id='makePaymentAmount"+makePaymentData[i]['worker_id']+"' /> </div> <div class='col-md-4'><select class='form-control projectChangeList' name='projects' id='makePaymentProject"+makePaymentData[i]['worker_id']+"'><option value='' selected>Select project</option></select></div> </div>");
            }
            <?php 
                foreach ($projects as $data) { ?>
                    $('.projectChangeList').append("<option  value='<?php echo $data->project_id ?>'> <?php echo $data->project_name ?></option>");
            <?php }
            ?>
        });
        
        $('#makePaymentProjectAll').on('change',function(){
            var presentId =  table.rows('.selected').data();
            for (var i=0; i < presentId.length; i++){                
                $('#makePaymentProject'+presentId[i]['worker_id']).val($('#makePaymentProjectAll').val());
            }
        });

        $('#makePaymentAmountAll').on('change',function(){
            var presentId =  table.rows('.selected').data();
            for (var i=0; i < presentId.length; i++){                
                $('#makePaymentAmount'+presentId[i]['worker_id']).val($('#makePaymentAmountAll').val());
            }
        });

        $('#makePaymentSubmit').click(function(e){
            var fmakePaymentId = [];
			var Id = '';
            var Id =  table.rows('.selected').data();
            var error = 0;
            var counter = 0;
            for (var i=0; i < Id.length; i++){
                fmakePaymentId.push(counter,Id[i]['worker_id']);
                counter++;
                if($('#makePaymentAmount'+Id[i]['worker_id']).val() != ''){
                    $('#makePaymentAmount'+Id[i]['worker_id']).css("border","1px solid green");
                    fmakePaymentId.push(counter,$('#makePaymentAmount'+Id[i]['worker_id']).val());
                    counter++;
                }else{
                    $('#makePaymentAmount'+Id[i]['worker_id']).css("border","1px solid red");
                    error = 1;
                }
                if($('#makePaymentProject'+Id[i]['worker_id']).val() != ''){
                    $('#makePaymentProject'+Id[i]['worker_id']).css("border","1px solid green");
                    fmakePaymentId.push(counter,$('#makePaymentProject'+Id[i]['worker_id']).val());
                    counter++;
                }else{
                    $('#makePaymentProject'+Id[i]['worker_id']).css("border","1px solid red");
                    error = 1;
                }
            }
            if(error == 0){
                makePayment(fmakePaymentId);
            }else
                alert('Kindly, fill all details');
        }); 

    }); /*End of document ready*/

    var base_url = '<?php echo base_url(); ?>';

    function worker_mark_present(formData) {
        $.ajax({
            url: "<?php echo base_url().'admin/workerRegister/ajax_bulk_present' ?>",
            type:'POST',
            dataType: 'json',
            data:  {
                'formData' : formData,
            },
            success: function(data, textStatus, xhr) {
                if(data.alertType == 'success'){
                    $('.presentAlert').html('<div class="alert alert-success alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Success!</b> '+data.msg+'</div>');
                }else if(data.alertType == 'warning'){
                    $('.presentAlert').html('<div class="alert alert-warning alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Warning!</b> '+data.msg+' </div>');
                }else{
                    $('.presentAlert').html('<div class="alert alert-error alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Error!</b> '+data.msg+' </div>');
                }
                $('.alert').fadeOut(7000); //remove suucess message
            },
        });        
    }

    function paidLeave(formData) {
		$.ajax({
			url: "<?php echo base_url().'admin/workerRegister/ajax_bulk_paid_leave' ?>",
			type:'POST',
			dataType: 'json',
			data:  {
				'formData' : formData,
			},
			success: function(data, textStatus, xhr) {
                if(data.alertType == 'success'){
                    $('.paidLeaveAlert').html('<div class="alert alert-success alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Success!</b> '+data.msg+'</div>');
                }else if(data.alertType == 'warning'){
                    $('.paidLeaveAlert').html('<div class="alert alert-warning alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Warning!</b> '+data.msg+' </div>');
                }else{
                    $('.paidLeaveAlert').html('<div class="alert alert-error alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Error!</b> '+data.msg+' </div>');
                }
                $('.alert').fadeOut(7000); //remove suucess message
			},
		});
    }


    function makePayment(formData) {
        $.ajax({
            url: "<?php echo base_url().'admin/workerRegister/ajax_bulk_make_payment' ?>",
            type:'POST',
            dataType: 'json',
            data:  {
                'formData' : formData,
            },
            success: function(data, textStatus, xhr) {
                if(data.alertType == 'success'){
                    $('.paymentAlert').html('<div class="alert alert-success alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Success!</b> '+data.msg+'</div>');
                }else{
                    $('.paymentAlert').html('<div class="alert alert-error alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Error!</b> '+data.msg+' </div>');
                }
                $('.alert').fadeOut(7000); //remove suucess message
            },
            complete: function(data, xhr, textStatus) {
                
            } ,
            beforeSend: function () {
                $("div#divLoading").addClass('show');
            },
        });  
    }
</script>