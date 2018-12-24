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
            <!-- form start -->
            <form action="<?php echo base_url('admin/MaterialInvoice/addInvoiceDetail');?>" id="add-challan" class="form-horizontal" method="POST">
			<div class="box">
                <div class="box-body table-responsive">
                    
					
					<div class="filters col-md-12">
						<br/>
						<div class="col-md-12">
	                        <h4>Filters:</h4>
	                    </div>
                        <label class="col-md-1 control-label">Project:</label>
                        <div class="col-md-3">
                            <?php
                            $disabledProj="";
                            if($this->session->userdata("challan_project")){
                                $disabledProj="disabled='disabled'";
                            ?>
                                <input type="hidden" name="project" value="<?=$this->session->userdata("challan_project")?>">
                            <?php    
                            }
                            ?>
                            <select <?=$disabledProj?> class="form-control project" name="project">
                                <option value="">Select Project </option>
                                <?php 
                                    foreach ($projects as $proj) {
                                        $selected="";
                                        if($this->session->userdata("challan_project") && $this->session->userdata("challan_project") == $proj->project_id)
                                        {
                                            $selected = 'selected="selected"';
                                        }
                                ?>
                                <option <?=$selected?> value="<?php echo $proj->project_id; ?>"><?php echo $proj->project_name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
						
						
						<label class="col-md-1 control-label">Supplier:</label>
						<div class="col-md-3">
                            <?php
                            $disabledSup="";
                            if($this->session->userdata("challan_supplier")){
                                $disabledSup="disabled='disabled'";
                            ?>
                                <input type="hidden" name="supplier" value="<?=$this->session->userdata("challan_supplier")?>">
                            <?php    
                            }
                            ?>
							<select <?=$disabledSup?> class="form-control supplier" name="supplier">
								<option value="">Select Supplier</option>
                                <?php
                                if(count($suppliers) >0){
                                    foreach ($suppliers as $supp) {
                                        $selected="";
                                        if($this->session->userdata("challan_supplier") && $this->session->userdata("challan_supplier") == $supp->id)
                                            {
                                                $selected = 'selected="selected"';
                                            }
                                    ?>
                                    <option <?=$selected?> value="<?php echo $supp->id; ?>"><?php echo $supp->name; ?></option>
                                <?php }
                                } ?>
							</select>
						</div>	

                        <div class="box-tools pull-left">
                            <!-- Add button -->
                              
                                <a href="javascript:void(0);" id="filterChallan" class="btn btn-info">
                                    <i class="fa fa-filter"></i> 
                                    Filter Challan
                                </a>
                                <a style="display: none;" href="<?=base_url('admin/MaterialInvoice/clearChallan')?>" id="clearChallan" class="btn btn-info">
                                    <i class="fa fa-trash"></i> 
                                    Clear
                                </a>
                            
                        </div>
                        <!-- <label class="col-md-1 control-label">Date:</label>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="daterange" />
                        </div> -->
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
    // var dateStartRange="";
    // var dateEndRange="";
    $(document).ready(function() {
        if(window.location.href.indexOf('?invoiceId') == -1)
        {
            // visible clear button only when adding invoice
            $("#clearChallan").show();
        }
        tableInvoice = $('#invoiceTable').DataTable({
                "order": [[ 1, "desc" ]],
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "deferLoading": ($('.project').val() != "" && $('.supplier').val() != "")?null:0,
                "drawCallback": function( settings ) {    
                },
                "language": {
                    "emptyTable": "<font color='red'>Please select project and supplier name.</font>"
                  },
                // data:[],
                "ajax":{
                    "url": "<?php echo base_url('admin/MaterialInvoice/invoiceEntryDatatable') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":function(data) {
                        data.project = $('.project').val();
                        data.supplier = $('.supplier').val();
                        // data.dateStartRange=dateStartRange;
                        // data.dateEndRange=dateEndRange;
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
		$(document).on("click","#filterChallan",function(){

            var errorMsg="";
            if($('.project').val().trim() == "")
            {
                errorMsg="Please Select project name.\n";
            }
            else if($('.supplier').val().trim() == "")
            {
                errorMsg="Please Select supplier name.\n";
            }
            if(errorMsg != "")
            {
                alert(errorMsg);
            }
            else
            {
                tableInvoice.draw();
            }           
        });   

        $('.project').change(function () {
            var optionHTML="<option value=''>Select Supplier</option>";
            var project_id = $(this).val();
            var ele=this;
            if(project_id) {   
                $.ajax({
                    url: "<?php echo base_url().'admin/MaterialInvoice/getSupplierAjax/'?>"+project_id,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {
                        // $('select[name="city"]').empty();
                        $.each(data.getProjectSupplier, function(key, value) {
                            optionHTML+='<option  value="'+ value.id +'">'+ value.name +'</option>';
                        });
                        $(".supplier").html(optionHTML);
                    }
                });
            }else{
               $(".supplier").html(optionHTML);
            }
            // tableInvoice.draw();
        });
        $('.supplier').change(function () {
            // tableInvoice.draw();
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




