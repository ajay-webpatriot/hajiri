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
        <li><a href="<?php echo base_url('admin/materialInvoice'); ?>">Material Invoice</a></li>
        <li class="active"><?php echo (isset($title) ? $title : ''); ?></li>
    </ol>
    <section class="content container-fluid">

        <div class="col-md-12">
			<div class="box">
                <div class="box-body table-responsive">
                    
					
					<div class="filters col-md-12">
						<br/>
						<div class="col-md-12">
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
								<option value="">All Supplier</option>
							</select>
						</div>	
                        <label class="col-md-1 control-label">Date:</label>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="daterange" />
                        </div>
					</div>
				</div>
			</div>
            <!-- form start -->
            <form action="<?php echo base_url('admin/MaterialInvoice/addInvoiceDetail');?>" id="add-challan" class="form-horizontal" method="POST">
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
                                    <th></th>
                                    <th>Challan No</th>
                                    <th>
                                        Entry Date
                                    </th> 
                                    <th>
                                        Amount
                                    </th>
                                </tr>
                            </thead>
                        </table>
                        <div class="box-tools pull-left">
                            <!-- Add button -->
                              
                                <a href="javascript:void(0);" id="addChallanEntry" class="btn btn-info">
                                    <i class="glyphicon glyphicon-plus"></i> 
                                    Add Challan
                                </a>
                            
                        </div>
                    </div>
                </div>
            </form>
            <!-- form end-->
        </div>
		
		
    </section>
</div>

<!-- /.content -->

<script type="text/javascript">
    var rows_selected = null;
    var absentId = [];
    var fHajiriId = [];

    var tableInvoice="";
    var dateStartRange="";
    var dateEndRange="";
    $(document).ready(function() {
		$('input[name="daterange"]').daterangepicker({
            opens: 'left',
            startDate: moment().subtract(6, 'days'),
            endDate: new Date()
          }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            dateStartRange=start.format('YYYY-MM-DD');
            dateEndRange=end.format('YYYY-MM-DD');
            tableInvoice.draw();
        });

        // set date during initialization

        dateStartRange=moment($('input[name="daterange"]').val().split(" - ")[0]).format('YYYY-MM-DD');
        dateEndRange=moment($('input[name="daterange"]').val().split(" - ")[1]).format('YYYY-MM-DD');

        // DataTable
        tableInvoice = $('#invoiceTable').DataTable({
            "order": [[ 1, "desc" ]],
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
                    data.project = $('.project').val();
                    data.supplier = $('.supplier').val();
                    data.dateStartRange=dateStartRange;
                    data.dateEndRange=dateEndRange;
                    data.<?php echo $this->security->get_csrf_token_name(); ?> = "<?php echo $this->security->get_csrf_hash(); ?>";
                },
            },
            "columns": [
                      { "data": "id" },
                      { "data": "challan_no" },
                      { "data": "challan_date" },
                      { "data": "amount" }
            ],
            columnDefs: [
               {
                    "targets": [0],
                    "visible": true,
                    "searchable": false,
                    "sortable":false,
                    "type": "string"
                },
				{
                    "targets": [1],
                    "visible": true,
                    "searchable": true,
                    "sortable":true,
                    "type": "string"
                },
                {
                    "targets": [2],
                    "visible": true,
                    "searchable": true,
                    "sortable":true,
                    "type": "string"
                },
                {
                    "targets": [3],
                    "visible": true,
                    "searchable": true,
                    "sortable":true,
                    "type": "string"
                }
            ]
        });
           

        $('.project').change(function () {
            var optionHTML="<option value=''>All Supplier</option>";
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
            tableInvoice.draw();
        });
        $('.supplier').change(function () {
            tableInvoice.draw();
        });
        
		//Add money button positioning
		
     
        $('.alert-success').fadeOut(5000); //remove suucess message 

        $("#addChallanEntry").click(function(){
            if($('[name="log_ids[]"]:checked').length > 0)
            {
                if(window.location.href.indexOf('?invoiceId') > -1)
                {
                    var url = window.location.search;
                    
                    url = url.replace("?invoiceId=", '');
                    $("#add-challan").attr("action","<?=base_url('admin/MaterialInvoice/editInvoiceDetail/')?>"+url);
                }
                $("#add-challan").submit();
            }
            else
            {
                alert("select atleast one checkbox");
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
    
   
</script>




