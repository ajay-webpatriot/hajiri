<!-- Content Wrapper. Contains page content -->
<?php error_reporting(0); ?>
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
                <div class="box-body table-responsive">
                    <div class="col-md-10 no-padding" style="border-right: 1px solid #ccc">
						<div class="col-md-4 no-padding">
							<div class="col-md-3">
								<img src="<?php echo base_url('assets/admin/images/debit.png'); ?>" class='balanceIcon'>
							</div>
							<div class="col-md-9">
		                        <h4>Credit Balance</h4>
								<p>&#8377; <?php echo number_format($credit->amount != '' ? $credit->amount : '0') ?></p>
							</div>
						</div>
						<div class="col-md-4 ">
	                        <h4 class="text-center">Total Balance</h4>
							<p class="text-center">&#8377; <?php echo number_format(($credit->amount != '' ? $credit->amount : '0') - ($debit->amount != '' ? $debit->amount : '0') ) ?> </p>
						</div>
						<div class="col-md-4">
							<div class="col-md-4">
								<img src="<?php echo base_url('assets/admin/images/credit.png'); ?>" class='balanceIcon'>
							</div>
							<div class="col-md-8">
		                        <h4>Debit Balance</h4>
								<p>&#8377; <?php echo number_format($debit->amount != '' ? $debit->amount : '0'); ?></p>
							</div>
						</div>
					</div>
					<div class="col-md-1">
						
							<?php if( $this->session->userdata('user_designation') == 'admin' ){ ?>
								<button class="btn btn-sm btn-success add-money" data-toggle="modal" data-target="#addMoneyModal" title="Add Money" >
									Add Money
								</button>
								<!------------ Add money model ------------>
								<div class="modal fade" id="addMoneyModal" tabindex="-1" role="dialog" aria-labelledby="addMoneyLabel">
								  <div class="modal-dialog modal-md" role="document">
									<div class="modal-content">
									  <div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title" id="myModalLabel">Add Money</h4>
									  </div>
									  <div class="modal-body">
										<div class="form-horizontal">
											<div class="form-group col-xs-12 formClass" >
												<label for="supervisor" class="col-sm-4 control-label">Supervisor</label>
												<div class="col-sm-8">
													<select id='supervisor' class='form-control supAddMoney' onChange="supProj('.supAddMoney','.projAddMoney')">
														<option>Select Supervisor</option>
													<?php
														foreach ($supervisor as $data) {
															echo "<option  value='" . $data->user_id . "'>" . $data->user_name . " ". $data->user_last_name . "</option>";
														}
													?>
													</select>
												</div>
											</div>
											<div class="form-group col-xs-12 formClass" >
											<label for="supervisor" class="col-sm-4 control-label">Project</label>
											<div class="col-sm-8">
												<select id='project' class='form-control projAddMoney'>
													<option>Select Project</option>
												</select>
											</div>
											</div>
											<div class="form-group col-xs-12 formClass" >
											<label for="amount" class="col-sm-4 control-label">Amount</label>
											<div class="col-sm-8">
												<input type="number" class="form-control" id="amount" placeholder="Enter amount" min='1' max='9999999'>
											</div>
											</div>
											<div class="form-group col-xs-12 formClass" >
											<label for="date" class="col-sm-4 control-label">Date</label>
											<div class="col-sm-8">
												<input type="text" class="form-control datepicker" id="date" placeholder="Select date" readonly>
											</div>
											</div>
										</div>
									  </div>
									  <div class="modal-footer">
										<button type="button" class="btn btn-success" id='addMoneySubmit'>Submit</button>
										<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
									  </div>
									</div>
								  </div>
								</div>
							<?php } ?>
							<?php if( $this->session->userdata('user_designation') == 'Supervisor' ){ ?>
								<button class="btn btn-sm btn-success add-money" data-toggle="modal" data-target="#addKharchiModal" title="Add Kharchi" >
									Add Kharchi
								</button>
								<!------------ Add kharchi model ------------>
								<div class="modal fade" id="addKharchiModal" tabindex="-1" role="dialog">
								  <div class="modal-dialog" role="document">
									<div class="modal-content">
									  <div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title" id="myModalLabel">Add Kharchi</h4>
									  </div>
									  <form role="form" id="add-kharchi-form" method="POST" enctype="multipart/form-data">
										<div class="modal-body">
											<div class="form-horizontal">
											  <div class="form-group col-xs-12 formClass">
												<label for="addKharchiTitle" class="col-sm-4 control-label">Kharchi Title</label>
												<div class="col-sm-8">
													<input type='text' id='addKharchiTitle' name='addKharchiTitle' class='form-control' placeholder='Kharchi title' required />
												</div>
											  </div>
											  <div class="form-group col-xs-12 formClass">
												<label for="addKharchiProject" class="col-sm-4 control-label">Project</label>
												<div class="col-sm-8">
												  <select id='addKharchiProject' name='addKharchiProject' class='form-control'>
													<?php
														foreach ($supervisorProjects as $data) {
															echo "<option  value='" . $data->project_id . "'>" . $data->project_name .  "</option>";
														}
													?>
												  </select>
												</div>
											  </div>
											  <div class="form-group col-xs-12 formClass">
												<label for="addKharchAmount" class="col-sm-4 control-label">Amount</label>
												<div class="col-sm-8">
												  <input type="number" class="form-control" id="addKharchAmount" name='addKharchAmount' placeholder="Enter amount" min='1' max='9999999'>
												</div>
											  </div>
											  <div class="form-group col-xs-12 formClass">
												<label for="addKharchDate" class="col-sm-4 control-label">Date</label>
												<div class="col-sm-8">
												  <input type="text" class="form-control datepicker" id="addKharchDate" name='addKharchDate' placeholder="Select date" readonly>
												</div>
											  </div>
											  <div class="form-group col-xs-12 formClass">
												<label for="kharchiImage" class="col-sm-4 control-label">Kharchi image:</label>
												<div class="col-sm-8">
													<input type="file" class='form-control' id="kharchiImage" name="image"  accept="image/x-png,image/jpeg" />
													<p class="help-block error"> </p> 
												</div>
											  </div>
											</div>
										</div>
										<div class="modal-footer">
											<input type="submit" class="btn btn-success" id='addKharchiSubmit' name='addKharchiSubmit' value='submit'/>
											<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
										</div>
									</form>
									</div>
								  </div>
								</div>
							<?php } ?>
						
					</div>
					<div class="filters col-md-12">
						<br/>
						<div class="col-md-1">
	                        <h4>Filters:</h4>
	                    </div>
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
						<label class="col-md-1 control-label">Month:</label>
						<div class="col-md-2">
							<input type="text" name="date"  placeholder="Kharchi Month" 
									class="form-control monthPicker" value="" autocomplete='false'>
									 <span class="add-on"><i class="fa fa-calendar"></i></span>
						</div>
						<?php if( $this->session->userdata('user_designation') == 'admin' ){ ?>
							<label class="col-md-1 control-label">Supervisor:</label>
							<div class="col-md-3">
								<select class="form-control supervisor" name="supervisor">
									<option value="">All Supervisor </option>
									<?php 
										foreach ($supervisor as $proj) {
									?>
									<option value="<?php echo $proj->user_id; ?>"><?php echo $proj->user_name.' '.$proj->user_last_name; ?></option>
									<?php } ?>
								</select>
							</div>						
						<?php } ?>
					</div>
				</div>
			</div>
            <div class="box">
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
                    					
                    <table id="table" class="tableFilter table table-striped table-hover table-bordered display responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Type</th>
                                <th>
                                    Reason
                                </th> 
                                <th>
                                    Date
                                </th>
								<th>
									Amount
								</th>
                                <th>
                                    Status
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
		<!---------------------------------------   Modals -------------------------------------------->
		<!------------ Edit kharchi model ------------>
		<div class="modal fade" id="editKharchi" tabindex="-1" role="dialog">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Edit Kharchi</h4>
			  </div>
			  <form role="form" id="edit-kharchi-form" method="POST" enctype="multipart/form-data">
				<div class="modal-body">
					<div class="form-horizontal">
						<p class='text-danger'></p>
						<input type='text' name='kharchiEditId' id='kharchiEditId' class='hidden' required/>
						<div class="form-group">
							<label for="title" class="col-sm-4 control-label">Title</label>
							<div class="col-sm-8">
								<input type='text' id='title' class='form-control' name='kharchiTitle' required />
								<p class="help-block error"> </p> 
							</div>	
						</div>
						<div class="form-group">
							<label for="editProject" class="col-sm-4 control-label">Project</label>
							<div class="col-sm-8">
								<select id='editProject' class='form-control' name='kharchiProject' required>
								<?php
								if($this->session->userdata('user_designation') == 'admin')
									$projects = $projects;
								else if ($this->session->userdata('user_designation') == 'Supervisor')
									$projects = $supervisorProjects;
									foreach ($projects as $data) {
										echo "<option  value='" . $data->project_id . "'>" . $data->project_name . " </option>";
									}
								?>
								</select>
								<p class="help-block error"> </p> 
							</div>
						</div>
						<div class="form-group">
							<label for="editAmount" class="col-sm-4 control-label">Amount</label>
							<div class="col-sm-8">
								<input type="number" class="form-control" name='kharchiAmount' id="editAmount" placeholder="Enter amount" min='1' max='9999999' required>
								<p class="help-block error"> </p> 
							</div>
						</div>
						<div class="form-group">
							<label for="editDate" class="col-sm-4 control-label">Date</label>
							<div class="col-sm-8">
								<input type="text" class="form-control datepicker" id="editDate" placeholder="Select date" name='kharchiDate' required readonly>
								<p class="help-block error"> </p> 
							</div>
						</div>
						<div class="form-group">
							<label for="kharchiImage" class="col-sm-4 control-label">Kharchi image:</label>
							<div class="col-sm-8">
								<input type="file" id="kharchiImage" name="image"  accept="image/x-png,image/jpeg" />
								<p class="help-block error"> </p> 
							</div>
						</div>
						<div class="form-group">
							<label for="editImage" class="col-sm-4 control-label"> </label>
							<div class="col-sm-8" id='editImage'>
											  
							</div>
						</div>
						<div class="form-group">
							<label for="status" class="col-sm-4 control-label">Status</label>
							<div class="col-sm-8">
								<select id='kharchiStatus' class='form-control' name='kharchiStatus' required>
									<option value='0'>Pending</option>
									<?php 
										if($this->session->userdata('user_designation') == 'admin'){
									?>
									<option value='1'>Approved</option>
									<?php } ?>
									<option value='2'>Delete</option>
								</select>
								<p class="help-block error"> </p> 
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="submit" class="btn btn-success" id='editKharchiSubmit' value='Submit' name='editKharchiSubmit'>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form>
			</div>
		  </div>
		</div>

		<!------------ Edit Credit model ------------>
		<div class="modal fade" id="editCredit" tabindex="-1" role="dialog">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Edit Credit</h4>
			  </div>
			  <form role="form" id="edit-credit-form" method="POST" enctype="multipart/form-data">
				<div class="modal-body">		
					<div class="form-horizontal">
						<p class='text-danger'></p>
						<input type='text' name='creditEditId' id='creditEditId' class='hidden' required/>
						<div class="form-group">
							<label for="editSupervisor" class="col-sm-4 control-label">Supervisor</label>
							<div class="col-sm-8">
								<select id='editSupervisor' class='form-control supEditCredit' onChange="supProj('.supEditCredit','.projEditCredit')" name='creditSupervisor' required>
								<?php 
									foreach ($supervisor as $proj) {
								?>
								<option value="<?php echo $proj->user_id; ?>"><?php echo $proj->user_name.' '.$proj->user_last_name; ?></option>
								<?php } ?>
								</select>
								<p class="help-block error"> </p> 
							</div>
						</div>
						<div class="form-group">
							<label for="editCreditProject" class="col-sm-4 control-label">Project</label>
							<div class="col-sm-8">
								<select id='editCreditProject' class='form-control projEditCredit' name='creditProject' required>
								<?php
									foreach ($projects as $data) {
										echo "<option  value='" . $data->project_id . "'>" . $data->project_name . " </option>";
									}
								?>
								</select>
								<p class="help-block error"> </p> 
							</div>
						</div>
						<div class="form-group">
							<label for="creditAmount" class="col-sm-4 control-label">Amount</label>
							<div class="col-sm-8">
								<input type="number" class="form-control" name='creditAmount' id="creditAmount" placeholder="Enter amount" min='1' max='9999999' required>
								<p class="help-block error"> </p> 
							</div>
						</div>
						<div class="form-group">
							<label for="creditDate" class="col-sm-4 control-label">Date</label>
							<div class="col-sm-8">
								<input type="text" class="form-control datepicker" id="creditDate" placeholder="Select date" name='creditDate' required readonly>
								<p class="help-block error"> </p> 
							</div>
						</div>
					</div>
				<div>
				<div class="modal-footer">
					<input type="submit" class="btn btn-success" id='editCreditSubmit' value='Submit' name='editCreditSubmit'>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form> 
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
    $(document).ready(function() {
		
		$( ".monthPicker" ).datepicker({
            defaultDate: new Date(),
            format:		"yyyy-mm",
			viewMode:	"months", 
			minViewMode: "months",
            endDate:	'+0d',
            autoclose: true
        }).datepicker("setDate", "0");
		$( ".datepicker" ).datepicker({
			defaultDate: new Date(),
			format: 'dd-mm-yyyy',
			<?php 
				if( date('d') > 10){
					$current = (date('d') - 1);
					echo "startDate: '-".$current."d',";
				}else{
					echo "startDate: '-".date('01-m-Y', strtotime('-1 MONTH'))."',";
				}
			?>
			endDate: '+0d',
			autoclose: true,
		});   
        // DataTable
        var table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "drawCallback": function( settings ) {    
            },
            "ajax":{
                "url": "<?php echo base_url('admin/kharchi/kharchiDatatable') ?>",
                "dataType": "json",
                "type": "POST",
                "data":function(data) {
                    data.date =  $('.monthPicker').val();
                    data.project = $('.project').val();
                    data.supervisor = $('.supervisor').val();
                    data.<?php echo $this->security->get_csrf_token_name(); ?> = "<?php echo $this->security->get_csrf_hash(); ?>";
                },
            },
            "columns": [
                      { "data": "id" },
                      { "data": "Kharchi_type" },
                      { "data": "Kharchi_details" },
                      { "data": "date" },
                      { "data": "amount" },
                      { "data": "status" },
                      { "data": "action" },
            ],
            columnDefs: [
               {
                    "targets": [0],
                    "visible": false,
                    "searchable": false,
                    "sortable":false,
                    "type": "string"
                },
				{
                    "targets": [1],
                    "visible": true,
                    "searchable": false,
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
                    "searchable": true,
                    "sortable":false,
                    "type": "string"
                },
                {
                    "targets": [4],
                    "visible": true,
                    "searchable": false,
                    "sortable":false,
                    "type": "string"
                },
                {
                    "targets": [5],
                    "visible": true,
                    "searchable": false,
                    "sortable":false,
                    "type": "string"
                },
                {
                    "targets": [6],
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
            $('#actionButton').removeClass('hidden');
        } )
        .on('user-select', function (e, dt, type, cell, originalEvent) {
        //       alert( table.rows('.selected').data().length +' row(s) selected' );
        })
        .on( 'deselect', function ( e, dt, type, indexes ) {
            var rowData = table.column(0).checkboxes.selected();
            if(table.column(0).checkboxes.selected().length == 0){
                $('#actionButton').addClass('hidden');
            }
        } );    

        $('.monthPicker').change(function () {
            table.draw();
        });
        $('.project').change(function () {
            table.draw();
        });
        $('.supervisor').change(function () {
            table.draw();
        });
        
		//Add money button positioning
		
     
        $('.alert-success').fadeOut(5000); //remove suucess message 

		//-------------------- Add money form submit ---------------
		$('#addMoneySubmit').click(function(){
			error = 0;
			if( $('#supervisor').val() == '' ){
				error = 1;
				$('#supervisor').css("border","1px solid red");
			}else{
				$('#supervisor').css("border","1px solid green");
			}

			if( $('#project').val() == '' ){
				error = 1;
				$('#project').css("border","1px solid red");
			}else{
				$('#project').css("border","1px solid green");
			}

			if( $('#amount').val() == '' ){
				error = 1;
				$('#amount').css("border","1px solid red");
			}else{
				$('#amount').css("border","1px solid green");
			}

			if( $('#date').val() == '' ){
				error = 1;
				$('#date').css("border","1px solid red");
			}else{
				$('#date').css("border","1px solid green");
			}
			if( error ==1){
				alert('Please fill all the details.');
			}else{
				addMoney();
			}
		});

		/******************** Kharchi Edit form **********************/
		$('#edit-kharchi-form').submit(function (event) {
            var exp = /^[a-zA-Z ]+$/;
            var phone_pattern = /[0-9]{10}/;
            var number = /[0-9]/;
            var title = jQuery("[name='kharchiTitle']");
            var project = jQuery("[name='kharchiProject']");
            var amount = jQuery("[name='kharchiAmount']");
            var date = jQuery("[name='kharchiDate']");
			var submit = jQuery("[name='editKharchiSubmit']");
            var error = 0;
            if (title.val() == '') {
                title.css({'border': '1px solid red', });
                title.next().text("Please enter Kharchi title");
                error = 1;
            } else {
                if (title.val().match(exp)) {
                    title.css({'border': '1px solid green', });
                    title.next().text("");
                } else {
                    title.css({'border': '1px solid red', });
                    title.next().text("Please enter valid kharchi title");
                    error = 1;
                }
            }
			if (project.val() == '') {
                project.css({'border': '1px solid red', });
                project.next().text("Please select project");
                error = 1;
            } else {
                project.css({'border': '1px solid green', });
                project.next().text(" ");
            }
			if (amount.val() == '') {
                amount.css({'border': '1px solid red', });
                amount.next().text("Please enter Kharchi amount");
                error = 1;
            } else {
                if (amount.val().match(number)) {
                    amount.css({'border': '1px solid green', });
                    amount.next().text("");
                } else {
                    amount.css({'border': '1px solid red', });
                    amount.next().text("Please enter valid kharchi amount");
                    error = 1;
                }
			}
			if (date.val() == '') {
                date.css({'border': '1px solid red', });
                date.next().text("Please select date");
                error = 1;
            } else {
                date.css({'border': '1px solid green', });
                date.next().text(" ");
            }
            if (error > 0) {
                event.preventDefault();
            } 
		});

		/******************** Credit Edit form **********************/
		$('#edit-credit-form').submit(function (event) {
            var exp = /^[a-zA-Z ]+$/;
            var phone_pattern = /[0-9]{10}/;
            var number = /[0-9]/;
            var supervisor = jQuery("[name='creditSupervisor']");
            var project = jQuery("[name='creditProject']");
            var amount = jQuery("[name='creditAmount']");
            var date = jQuery("[name='creditDate']");
			var submit = jQuery("[name='editCreditSubmit']");
            var error = 0;
			
			if (supervisor.val() == '') {
                supervisor.css({'border': '1px solid red', });
                supervisor.next().text("Please select supervisor");
                error = 1;
            } else {
                supervisor.css({'border': '1px solid green', });
                supervisor.next().text(" ");
            }
			if (project.val() == '') {
                project.css({'border': '1px solid red', });
                project.next().text("Please select project");
                error = 1;
            } else {
                project.css({'border': '1px solid green', });
                project.next().text(" ");
            }
			if (amount.val() == '') {
                amount.css({'border': '1px solid red', });
                amount.next().text("Please enter Kharchi amount");
                error = 1;
            } else {
                if (amount.val().match(number)) {
                    amount.css({'border': '1px solid green', });
                    amount.next().text("");
                } else {
                    amount.css({'border': '1px solid red', });
                    amount.next().text("Please enter valid kharchi amount");
                    error = 1;
                }
			}
			if (date.val() == '') {
                date.css({'border': '1px solid red', });
                date.next().text("Please select date");
                error = 1;
            } else {
                date.css({'border': '1px solid green', });
                date.next().text(" ");
            }
            if (error > 0) {
                event.preventDefault();
            } 
		});
            
    });
	var base_url = '<?php echo base_url(); ?>';

	function supProj(supClass,projClass) {
        $.ajax({
            url: "<?php echo base_url().'admin/Kharchi/supervisor_project' ?>",
            type:'POST',
            dataType: 'json',
            data:  {
                'supervisorId' : $(supClass).val(),
            },
            success: function(data, textStatus, xhr) {
                $(projClass).html('');
                $.each( data, function( key, val ) {
                	$(projClass).append($('<option>', { 
						value: val.project_id,
						text : val.project_name 
					}));
                });
            },
        });        
    }

    function addMoney() {
        $.ajax({
            url: "<?php echo base_url().'admin/Kharchi/addMoney' ?>",
            type:'POST',
            dataType: 'json',
            data:  {
                'supervisor' : $('#supervisor').val(),
                'project' : $('#project').val(),
                'amount' : $('#amount').val(),
                'date' : $('#date').val(),
            },
            success: function(data, textStatus, xhr) {
                location.reload();
            },
            complete: function(xhr, textStatus) {
                location.reload();
                $("div#divLoading").removeClass('show');
            } ,
            beforeSend: function () {
                $("div#divLoading").addClass('show');
            },
        });        
    }

	function editKharchi(id){
		$.ajax({
            url: "<?php echo base_url().'admin/Kharchi/getKharchiDetails' ?>",
            type:'POST',
            dataType: 'json',
            data:  {
                'id' : id,
            },
            success: function(data, textStatus, xhr) {
                $('#title').val(data.title);
                $('#editProject').val(data.project_id);
                $('#editAmount').val(data.amount);
                $('#editDate').val(data.date_time);
				$('#kharchiEditId').val(data.kharachi_id);
                if(data.log != ''){
					$('.text-danger').html(data.log);
				} 
                $('#kharchiStatus').val(data.status);

				$('#editImage').html("<img src='<?php echo base_url().'uploads/kharchi/' ?>"+data.image+"' />");
            },
            complete: function(xhr, textStatus) {
                //location.reload();
                $("div#divLoading").removeClass('show');
            } ,
            beforeSend: function () {
                $("div#divLoading").addClass('show');
            },
        });
	}

	function editCredit(id){
		$.ajax({
            url: "<?php echo base_url().'admin/Kharchi/getKharchiDetails' ?>",
            type:'POST',
            dataType: 'json',
            data:  {
                'id' : id,
            },
            success: function(data, textStatus, xhr) {
                $('#editCreditProject').val(data.project_id);
                $('#editSupervisor').val(data.supervisor_id);
                $('#creditAmount').val(data.amount);
                $('#creditDate').val(data.date_time);
				$('#creditEditId').val(data.kharachi_id);
            },
            complete: function(xhr, textStatus) {
                //location.reload();
                $("div#divLoading").removeClass('show');
            } ,
            beforeSend: function () {
                $("div#divLoading").addClass('show');
            },
        });
	}
</script>




