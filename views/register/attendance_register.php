<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo (isset($title) ? $title : ''); ?> 
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
                    <h3 class="box-title"><?php echo (isset($title) ? $title : ''); ?> for <span class="titleDate"><?php echo date('M-d-Y') ?></span></h3>
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
                    <?php if ($this->session->flashdata('error') != ''): ?>
                        <div class="alert alert-danger alert-dismissable">
                            <i class="fa fa-check"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <b>Error!</b> 
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="filters col-md-12" id='fixme'>
                        <h4>Filters:</h4>
						<div class='col-md-12'>
                        <label class="col-md-1 control-label">Project:</label>
							<div class="col-md-3">
								<select class="form-control project" name="project">
									<option value="">All Project </option>
									<?php 
										foreach ($projects as $proj) {
									?>
									<option value="<?php echo $proj->project_id; ?>"><?php echo $proj->project_name; ?></option>
									<?php } ?>
								</select>
							</div>
							<label class="col-md-1 control-label">Category:</label>
							<div class="col-md-3">
								<select class="form-control category" name="category">
									<option value="">All Category </option>
									<?php 
										foreach ($Category as $proj) {
									?>
									<option value="<?php echo $proj->id; ?>"><?php echo $proj->category; ?></option>
									<?php } ?>
								</select>
							</div>
							<label class="col-md-1 control-label">Date:</label>
							<div class="col-md-3">
								<input type="text" name="date"  placeholder="Attendance date" 
										class="form-control datepicker titleDatePicker" value="">
										 <span class="add-on"><i class="fa fa-calendar"></i></span>
							</div>
						</div>
						<div class='margin-top col-md-12'>
							<?php if ($this->session->userdata('user_designation') == 'admin') { ?>
							<label class="col-md-1 control-label">User:</label>
							<div class="col-md-3">
								<select class="form-control supervisor" name="supervisor">
									<option value="">All User </option>
									<?php 
										foreach ($supervisor as $proj) {
									?>
									<option value="<?php echo $proj->user_id; ?>"><?php echo $proj->user_name.' '.$proj->user_last_name; ?></option>
									<?php } ?>
								</select>
							</div>
							<?php } ?>
							<div id="actionButton" class='col-md-8'>
								<?php if (in_array("2", $this->session->userdata('permissions'))){ ?>
									<button class="btn btn-sm btn-danger absent" data-toggle="modal" data-target="#absentModal" title="Mark Absent" >
										Mark Absent 
									</button>
                                
								<?php }else{ ?>
									<button class="btn btn-sm btn-default disabled" title="Mark Absent" >
									   Mark Absent 
								   </button>
								<?php } ?>
								<?php if (in_array("4", $this->session->userdata('permissions'))){ ?>
									<button class="btn btn-sm btn-info hajiri" data-toggle="modal" data-target="#hajiriModal" title="Change Hajiri" >
										Change Hajiri
									</button>    
								<?php }else{ ?>
									<button class="btn btn-sm btn-default disabled" data-toggle="modal" title="Change Hajiri" >
										Change Hajiri
									</button> 
								<?php } ?>
                                <?php if (in_array("8", $this->session->userdata('permissions'))){ ?>
                                    <button type='button' data-toggle="modal" data-target="#sendSmsModal" class="btn btn-sm btn-primary" id='sendSms' onClick='sendSmsData()'> Send SMS </button>   
                                <?php }?>
							</div>
						</div>
                    </div>
                         <!-- End of acton button Div -->
                        <?php if (in_array("2", $this->session->userdata('permissions'))){ ?>
                            <div class="modal fade" id="absentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
                                <div class="modal-dialog modal-md" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="btn btn-danger close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title col-md-4" id="exampleModalLabel">
												Reason for Absent
											</h4>
											<div class="col-md-7" style="text-align: right;">
												<input type="checkbox" id="selectAllAbsent">
													Change all Reason
											</div>
                                        </div>
                                        <div class="modal-body">
                                            <div class="absentAlert"></div>
                                        <form id="frmAbsent" action="" method="POST">
											<div class="row rowHeader">
                                                <div class="col-md-2">
                                                    
                                                </div>
                                                <div class="col-md-2">Absent Reason:</div>
                                                <div class="col-md-8">
                                                    <textarea class="form-control" id="resaonAll" max-length='150' placeholder="Absent reason for all" readonly></textarea>
                                                </div>
                                            </div>
                                            <div class="row bulkDetailsHeader">
                                                <div class="col-md-4">Name</div>
                                                <div class="col-md-8">Absent Reason</div>
                                            </div>
											<div class='absentDetails'></div>
                                        </div>
                                        <div class="modal-footer">
                                            <!-- input type="submit" name="absent" class="btn btn-success absent" value="Submit" -->
                                        </form>
											<button type='button' class="btn btn-success" id='submit'>Submit</button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }?>

                        <div class="modal fade" id="hajiriModal" tabindex="-1" role="dialog" aria-labelledby="hajiriModal">
                            <div class="modal-dialog modal-md" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="btn btn-danger close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title col-md-4" id="exampleModalLabel">
                                            Change Hajiri
                                        </h4>
                                        <div class="col-md-7" style="text-align: right;">
                                            <input type="checkbox" id="selectAll">
                                                    Change all Hajiri
                                        </div>
                                    </div>
                                    <div class="modal-body">
                                        <div class="changeHajiriAlert"></div>
                                        <form id="frmHajiri" action="" method="POST">
                                            <div class="row rowHeader">
                                                <div class="col-md-3">
                                                    
                                                </div>
                                                <div class="col-md-1">Hajiri:</div>
                                                <div class="col-md-3">
                                                    <input type='number' min='0.1' max='99' step='.01' id='hajiriAll' class="form-control" value="" placeholder='Hajiri' readonly>
                                                </div>
                                                <div class="col-md-2">Amount:</div>
                                                <div class="col-md-3">
                                                    <input type='number' min='1' step='.01' id='amountAll' class="form-control" value="" placeholder='Amount' readonly>
                                                </div>
                                            </div>
                                            <div class="row bulkDetailsHeader">
                                                <div class="col-md-4">Name</div>
                                                <div class="col-md-3">Hajiri</div>
                                                <div class="col-md-5">Amount</div>
                                            </div>
                                            <div class="HajiriDetails"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="submit" name="hajiri" class="btn btn-success absent" value="Submit">
                                        </form>    
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                       
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (in_array("8", $this->session->userdata('permissions'))){ ?>
							
							<!-- Send SMS Modal -->
							<div class="modal fade" id="sendSmsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
							  <div class="modal-dialog" role="document">
								<div class="modal-content">
								  <div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title" id="myModalLabel">Send hajiri SMS for <span class='hajiriDate'></span></h4>
								  </div>
								  <div class="modal-body">
									<table class="table table-hover table-responsive">
										<thead>
											<tr>
												<th>Hajiri Type</th>
												<th>Count</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>Total number of SMS to be sent </td>
												<td class='totalAttendance'></td>
												<input type='text' id='totalAttendanceInput' class='hidden' readonly />
											</tr>
											<tr>
												<td>Workers with invalid contact details </td>
												<td class='totalSms'></td>
												<input type='text' id='totalSmsInput' class='hidden' readonly />
											</tr>
										</tbody>
									</table>
								  </div>
								  <div class="modal-footer">
									<button type="button" class="btn btn-success sendSms" onClick='sendSms()'>Send Sms</button>
									<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								  </div>
								</div>
							  </div>
							</div>
						<?php } ?>

                        <table id="table" class="tableFilter table table-striped table-hover table-bordered display responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                   <th></th>
                                    <th>
                                        Worker name
                                    </th> 
                                    <th>
                                        Category
                                    </th>
                                    <th>
                                        Hajiri | Amount
                                    </th>
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
    var rows_selected = null;
    var absentId = [];
    var fHajiriId = [];
	$('.absent').attr("disabled", true);
	$('.hajiri').attr("disabled", true);
    // When the user scrolls the page, execute myFunction 
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
	var table = '';
	
    $(document).ready(function() {
		$('.titleDatePicker').on("change", function() {
            $('.titleDate').html($('.titleDatePicker').val());
        })
        $( ".datepicker" ).datepicker({
           "endDate"        : '+0d',
           "autoclose"      : true,
           "format"         : 'M-dd-yyyy',
        }).datepicker("setDate", "0");
        // DataTable
        table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "select": true,
            "responsive": true,
            "drawCallback": function( settings ) {
            },
            'select': {
                'style': 'multi'
            },
            "ajax":{
                "url": "<?php echo base_url('admin/attendanceRegister/attendanceDatatable') ?>",
                "dataType": "json",
                "type": "POST",
                "data":function(data) {
                    data.date =  $('.datepicker').val();
                    data.category = $('.category').val();
                    data.project = $('.project').val();
                    data.supervisor = $('.supervisor').val();
                    data.<?php echo $this->security->get_csrf_token_name(); ?> = "<?php echo $this->security->get_csrf_hash(); ?>";
                },
            },
            "columns": [
                      { "data": "id" },
                      { "data": "labour_name" },
                      { "data": "category_name" },
                      { "data": "hajiri_rate" },
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
                    "sortable":false,
                    "type": "string"
                },
                {
                    "targets": [2],
                    "visible": true,
                    "searchable": true,
                    "sortable":false,
                    "type": "string"
                },
                {
                    "targets": [3],
                    "visible": true,
                    "searchable": false,
                    "sortable":false,
                    "type": "string"
                }

            ]
        });
        
        table
        .on( 'select', function ( e, dt, type, indexes ) {
            var rowData = table.column(0).checkboxes.selected();
            $('.absent').attr("disabled", false);
            $('.hajiri').attr("disabled", false);
        } )
        .on('user-select', function (e, dt, type, cell, originalEvent) {
        //       alert( table.rows('.selected').data().length +' row(s) selected' );
        })
        .on( 'deselect', function ( e, dt, type, indexes ) {
            var rowData = table.column(0).checkboxes.selected();
            if(table.column(0).checkboxes.selected().length == 0){
               $('.absent').attr("disabled", true);
				$('.hajiri').attr("disabled", true);
            }
        } );
        
		$('#submit').click(function(e){
			var fAbsentId = [];
			var Id =  table.rows('.selected').data();
			var error = 0;
			for (var i=0; i < Id.length; i++){
				if($('#reason'+Id[i]['id']).val() != ''){
					$('#reason'+Id[i]['id']).css("border","1px solid green");
					fAbsentId.push(Id[i]['id'],$('#reason'+Id[i]['id']).val());
				}else{
					$('#reason'+Id[i]['id']).css("border","1px solid red");
					error = 1;
				}
			}
			if(error == 0)
				worker_absent(fAbsentId);
			else
				alert('Kindly, fill all details');
		});
        // Handle form submission event
        $('#frmAbsent').on('submit', function(e){
			var form = this;
			var fAbsentId = [];
			// Iterate over all selected checkboxes
			var form_list = $(form).serializeArray();
			jQuery.each( form_list, function( i, field ) {
				fAbsentId.push(field.name,field.value);
			});
			worker_absent(fAbsentId);
			e.preventDefault();
        });  

        // Handle Change hajiri event
        $('#frmHajiri').on('submit', function(e){
			var form = this;
			var fHajiriId = [];
			// Iterate over all selected checkboxes
			e.preventDefault();
			var form_list = $(form).serializeArray();
            
			jQuery.each( form_list, function( i, field ) {
				fHajiriId.push(field.name,field.value);
			});
			worker_change_hajiri(fHajiriId);
        });  

		//Mark Absent
        $('.absent').click(function(e){
			markAbsent($('.datepicker').val(),e);
        });

        //Change hajiri
        $('.hajiri').click(function(e){
			changeHajiri($('.datepicker').val(),e);
        });

        $(".rowHeader").hide();
        $('#selectAll').on('click', function(){
            if ( $(this).is(':checked') ) {
                $(".rowHeader").slideDown("slow");
                $(".HajiriDetails :input").prop("readonly", true);
                $("#hajiriAll").prop("readonly", false);
                $("#amountAll").prop("readonly", false);
            } 
            else {
                $(".rowHeader").slideUp("slow");
                $(".HajiriDetails :input").prop("readonly", false);
                $("#hajiriAll").prop("readonly", true);
                $("#amountAll").prop("readonly", true);

            }
        });
		//Absent select All
		$('#selectAllAbsent').on('click', function(){
            if ( $(this).is(':checked') ) {
                $(".rowHeader").slideDown("slow");
                $(".absentDetails :input").prop("readonly", true);
                $("#resaonAll").prop("readonly", false);
            } 
            else {
                $(".rowHeader").slideUp("slow");
                $(".absentDetails :input").prop("readonly", false);
                $("#reasonAll").prop("readonly", true);
            }
        });
		$('#resaonAll').bind('input propertychange', function() {
            var hajiriId =  table.rows('.selected').data();
            for (var i=0; i < hajiriId.length; i++){
                $('#reason'+hajiriId[i]['id']).val($('#resaonAll').val());
            }
        });

		// Hajiri all
        $('#hajiriAll').on('input',function(){
            if( $(this).val() != ''){
                $("#amountAll").prop("readonly", true);
                var hajiriId =  table.rows('.selected').data();
                for (var i=0; i < hajiriId.length; i++){
                    var addy = hajiriId[i]['hajiri_rate'];
                    var amount = addy.match("&#8377;(.*)");
                    amount = parseFloat(amount[1]).toFixed(2);
                    var hajiri = parseFloat(addy.substr(0, addy.indexOf('|'))).toFixed(2);

                    var perHajiri = amount/hajiri;
                    $('#rate'+hajiriId[i]['id']).val($('#hajiriAll').val());
                    $('#amount'+hajiriId[i]['id']).val( parseFloat( [perHajiri * $('#hajiriAll').val()] ).toFixed(2));
                }
            }else{
                $("#amountAll").prop("readonly", false);

            }
        });

        $('#amountAll').on('input',function(){
            if( $(this).val() != ''){
                $("#hajiriAll").prop("readonly", true);
                var hajiriId =  table.rows('.selected').data();
                for (var i=0; i < hajiriId.length; i++){
                    var addy = hajiriId[i]['hajiri_rate'];
                    var amount = addy.match("&#8377;(.*)");
                    amount = parseFloat(amount[1]).toFixed(2);
                    var hajiri = parseFloat(addy.substr(0, addy.indexOf('|'))).toFixed(2);
                    
                    var perHajiri = amount/hajiri;
                    $('#amount'+hajiriId[i]['id']).val($('#amountAll').val());
                    $('#rate'+hajiriId[i]['id']).val( parseFloat([$('#amountAll').val() / perHajiri] ).toFixed(2) );
                }
            }else{
                $("#hajiriAll").prop("readonly", false);
            }
        });  

        $('.datepicker').change(function () {
            table.draw();
        });
        $('.project').change(function () {
            table.draw();
        });
        $('.category').change(function () {
            table.draw();
        });
        $('.supervisor').change(function () {
            table.draw();
        });
        
     
        $('.alert-success').fadeOut(5000); //remove suucess message

        jQuery('.mark-absent').submit(function (event) {
            var absentReason = jQuery("[name='absentReason']");
            event.preventDefault();
        });    

    });

    function worker_absent(formData) {
        $.ajax({
            url: "<?php echo base_url().'admin/attendanceRegister/ajax_absent' ?>",
            type:'POST',
            dataType: 'json',
            data:  {
                'formData' : formData,
            },
            success: function(data, textStatus, xhr) {
                if(data.alertType == 'success'){
                    $('.absentAlert').html('<div class="alert alert-success alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Success!</b> '+data.msg+'</div>');
                }else if(data.alertType == 'warning'){
                    $('.absentAlert').html('<div class="alert alert-warning alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Warning!</b> '+data.msg+' </div>');
                }else{
                    $('.absentAlert').html('<div class="alert alert-error alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Error!</b> '+data.msg+' </div>');
                }
            },
        });
    }

    function worker_change_hajiri(formData) {        
        $.ajax({
            url: "<?php echo base_url().'admin/attendanceRegister/ajax_change_hajiri' ?>",
            type:'POST',
            dataType: 'json',
            data:  {
                'formData' : formData,
            },
            success: function(data, textStatus, xhr) {
               if(data.alertType == 'success'){
                    $('.changeHajiriAlert').html('<div class="alert alert-success alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Success!</b> '+data.msg+'</div>');
                }else if(data.alertType == 'warning'){
                    $('.changeHajiriAlert').html('<div class="alert alert-warning alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Warning!</b> '+data.msg+' </div>');
                }else{
                    $('.changeHajiriAlert').html('<div class="alert alert-error alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Error!</b> '+data.msg+' </div>');
                }
            },
        });
    }

    function calcHajiri(id,hajiri,amount,type){
        var perHajiri = amount/hajiri;
            if(type == 0){
                $('#amount'+id).val( parseFloat( [perHajiri * $('#rate'+id).val()] ).toFixed(2));
            }else{
                $('#rate'+id).val( parseFloat([$('#amount'+id).val() / perHajiri] ).toFixed(2) );
            }
    }
	function markAbsent(date, event) {
        $.ajax({
            url: "<?php echo base_url().'admin/attendanceRegister/checkDate' ?>",
            type:'POST',
            dataType: 'json',
			async: false,
			cache: false,
            data:  {
                'date' : date,
            },
            success: function(data, xhr, textStatus) {
				if(data == true){
					var absentData = [];
					$('.absentDetails').html('');
					var Id =  table.rows('.selected').data();
					for (var i=0; i < Id.length; i++){
						$('.absentDetails').append("<div class='row table-striped'><input type='text' name='id' class='hidden' value='" + Id[i]['id'] + "' />  <div class='col-md-4'> " + Id[i]['labour_name'] + "</div><div class='col-md-8'>  <textarea class='form-control' max-length='150' id='reason" + Id[i]['id'] + "'  placeholder='Reason for absent' name='absentReason' required></textarea> </div> </div>");
					}
				}else{
					event.stopPropagation();
					alert('Cannot make changes in selected date');
				}
            },
            complete: function(data, xhr, textStatus) {
            } ,
            beforeSend: function () {
            },
        });
    }
	function changeHajiri(date, event) {
        $.ajax({
            url: "<?php echo base_url().'admin/attendanceRegister/checkDate' ?>",
            type:'POST',
            dataType: 'json',
			async: false,
			cache: false,
            data:  {
                'date' : date,
            },
            success: function(data, xhr, textStatus) {
				if(data == true){
					var hajiriData = [];
					$('.HajiriDetails').html('');
					var hajiriId =  table.rows('.selected').data();
					for (var i=0; i < hajiriId.length; i++){
						var addy = hajiriId[i]['hajiri_rate'];
						var amount = addy.match("&#8377;(.*)");
						amount = parseFloat(amount[1]).toFixed(2);
						var hajiri = parseFloat(addy.substr(0, addy.indexOf('|'))).toFixed(2);
					   $('.HajiriDetails').append("<div class='row table-striped'><input type='text' name='id' class='hidden' value='" + hajiriId[i]['id'] + "' />  <div class='col-md-4'> " + hajiriId[i]['labour_name'] + "</div><div class='col-md-3'>  <input type='number' step='any' step='.01' min='0.1' max='99' name='hajiri' class='form-control' onInput='calcHajiri("+hajiriId[i]['id'] +","+hajiri+","+amount+",0)' id='rate"+hajiriId[i]['id']+"' value='"+hajiri+"' required /> </div> <div class='col-md-5'>  <input type='number' step='any' min='1'  step='.01' name='amount' class='form-control' onInput='calcHajiri("+hajiriId[i]['id'] +","+hajiri+","+amount+",1)' id='amount"+hajiriId[i]['id']+"' value='"+amount+"' required/> </div> </div>");
					}
				}else{
					event.stopPropagation();
					alert('Cannot make changes in selected date');
				}
            },
            complete: function(data, xhr, textStatus) {
            } ,
            beforeSend: function () {
            },
        });
    }
	
	function sendSmsData() {
       $.ajax({
            url: "<?php echo base_url().'admin/attendanceRegister/sendSmsData' ?>",
            type:'POST',
            dataType: 'json',
			async: false,
			cache: false,
            data:  {
                'project' : $('.project').val(),
                'category' : $('.category').val(),
                'supervisor' : $('.supervisor').val(),
                'date' : $('.datepicker').val(),
            },
            success: function(data, xhr, textStatus) {
				if(data.totalNumber > 0){
					$('.hajiriDate').html($('.datepicker').val())
					$('.sendSms').removeClass('disabled');
					$('.totalAttendance').html(data.totalPresent);
					$('#totalAttendanceInput').val(data.totalPresent);
					$('.totalSms').html((data.totalPresent - data.totalNumber));
					$('#totalSmsInput').val(data.totalNumber);
				}else{
					$('.sendSms').addClass('disabled');
					$('.totalAttendance').html('0');
					$('.totalSms').html('0');
				}
            },
            complete: function(data, xhr, textStatus) {
            } ,
            beforeSend: function () {
            },
        });
    }

	function sendSms() {
		$('.sendSms').addClass('disabled');
		$.ajax({
            url: "<?php echo base_url().'admin/attendanceRegister/sendSmsHajiri' ?>",
            type:'POST',
            dataType: 'json',
			async: false,
			cache: false,
            data:  {
                'project' : $('.project').val(),
                'category' : $('.category').val(),
                'supervisor' : $('.supervisor').val(),
                'date' : $('.datepicker').val(),
				'totalAttendance' : $('#totalAttendanceInput').val(),
				'totalSms' : $('#totalSmsInput').val(),
            },
            success: function(data, xhr, textStatus) {
            },
            complete: function(data, xhr, textStatus) {
                location.reload();
            } ,
            beforeSend: function () {
            },
		});
    }
</script>