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
                    
					
					<div class="filters col-md-12">
						<br/>
						<div class="col-md-1">
	                        <h4>Filters:</h4>
	                    </div>
                        <label class="col-md-1 control-label">Project:</label>
                        <div class="col-md-3">
                            <select class="form-control project" name="project">
                                <option value="">Select Project </option>
                                <?php 
                                    foreach ($projects as $proj) {
                                ?>
                                <option value="<?php echo $proj->project_id; ?>"><?php echo $proj->project_name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
						
						
						<label class="col-md-1 control-label">Supplier:</label>
						<div class="col-md-3">
							<select class="form-control supplier" name="supplier">
								<option value="">Select Supplier</option>
							</select>
						</div>	
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
                    					
                    <table id="invoiceTable" class="tableFilter table table-striped table-hover table-bordered display responsive nowrap" cellspacing="0" width="100%">
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
        var table = $('#invoiceTable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "drawCallback": function( settings ) {    
            },
            "ajax":{
                "url": "<?php echo base_url('admin/MaterialInvoice/invoiceEntryDatatable') ?>",
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

        $('.project').change(function () {
            var optionHTML="<option value=''>Supplier Name</option>";
            var project_id = $(this).val();
            var ele=this;
            if(project_id) {   
                $.ajax({
                    url: "<?php echo base_url().'admin/MaterialInvoice/getSupplierAjax/'?>"+project_id,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {
                        // $('select[name="city"]').empty();
                        $.each(data, function(key, value) {
                            optionHTML+='<option  value="'+ value.id +'">'+ value.name +'</option>';
                        });
                        $(".supplier").html(optionHTML);
                    }
                });
            }else{
               $(".supplier").html(optionHTML);
            }
        });
        $('.supplier').change(function () {
            table.draw();
        });
        
		//Add money button positioning
		
     
        $('.alert-success').fadeOut(5000); //remove suucess message 

            
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

   
</script>




