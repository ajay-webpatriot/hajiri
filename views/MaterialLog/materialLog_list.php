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
            <!-- Filter portion start -->
            <div class="box">
                <div class="box-body table-responsive">
                    <div class="filters col-md-12">
                        <br/>
                        <div class="col-md-12">
                            <h4>Filters:</h4><br/>
                        </div>
                        
                        <div class="col-md-12" style="padding-bottom: 2%;">
                            <label class="col-md-1 control-label">Project:</label>
                            <div class="col-md-3">
                                <select class="form-control projectEntry" name="projectEntry">
                                    <option value="">All Project </option>
                                    <?php 
                                        foreach ($projects as $proj) {
                                    ?>
                                    <option value="<?php echo $proj->project_id; ?>"><?php echo $proj->project_name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="materialFilter" style="display: none;">
                                <label class="col-md-1 control-label">Material:</label>
                                <div class="col-md-3">
                                    <select class="form-control materialEntry" name="project">
                                        <option value="">All Material </option>
                                        
                                    </select>
                                </div>
                            </div>
                            <label class="col-md-1 control-label">Date:</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="daterange" />
                            </div>
                            
                        </div>
                        <div class="col-md-12">
                            <div class="supervisorFilter" style="display: none;">
                                <label class="col-md-1 control-label">Supervisor Name:</label>
                                <div class="col-md-3">
                                    <select class="form-control supervisorEntry" name="supervisorEntry">
                                        <option value="">All Supervisor </option>
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="supplierFilter" style="display: none;">
                                <label class="col-md-1 control-label">Supplier Name:</label>
                                <div class="col-md-3">
                                    <select class="form-control supplierEntry" name="supplierEntry">
                                        <option value="">All Supplier </option>
                                        
                                    </select>
                                </div>
                            </div>
                            <label class="col-md-1 control-label">Status:</label>
                            <div class="col-md-3">
                                <select class="form-control statusEntry" name="statusEntry">
                                    <option value="">All Status </option>
                                    <option value="Approved">Approved</option>
                                    <option value="Pending">Pending</option>
                                    
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <!-- Filter portion end -->
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><?php echo (isset($title) ? $title : ''); ?></h3>
                    <div class="box-tools pull-right">
                        <!-- Add button -->
                        <a href="<?php echo base_url('admin/materialLog/Entry/');   ?>">  
                            <button class="btn btn-info">
                                <i class="glyphicon glyphicon-plus"></i> 
                                Material Entry
                            </button>
                        </a>
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
                    <table id="table" class="WR tableFilter table table-striped table-hover table-bordered display responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Challan No</th> 
                                <th>Entry Date</th>
                                <th>Received By</th>
                                <th>Supplier Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            /* foreach ($materialLog as $value) { ?>
                                <tr>
                                    <td><?php echo $value->challan_no ; ?></td>
                                    <td><?php echo $value->challan_date ; ?></td>
                                    <td><?php echo $value->supervisor_name; ?></td>
                                    <td><?php echo $value->category_name; ?></td>
                                    <td><?php echo $value->material_name; ?></td>
                                    <td><?php echo $value->supplier_name; ?></td>
                                    <td><?php echo $value->status; ?></td>
                                    <td> 

                                        <a class="btn btn-sm btn-primary" href="<?php echo base_url('admin/MaterialLog/editEntry/') . $value->id; ?>" title="Edit material entry">
                                            <i class="glyphicon glyphicon-pencil"></i> </a> 
                                            <?php if(isset($value->status) && $value->status !== 'Approved') { ?> 
                                                <button class="btn btn-sm btn-danger" title="Delete material entry" onclick="material_entry_log_delete('<?php echo $value->id; ?>')">
                                                    <i class="glyphicon glyphicon-trash"></i> 
                                                </button>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php  } */
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- /.content -->

    <script type="text/javascript">
        var tableEntry="";
        var dateStartRange="";
        var dateEndRange="";

        $(function () {

            // $("#table").DataTable({
            //     "order": [[ 0, "desc" ]]
            // });
            $('input[name="daterange"]').daterangepicker({
                opens: 'left',
                startDate: moment().subtract(6, 'days'),
                endDate: new Date()
              }, function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
                dateStartRange=start.format('YYYY-MM-DD');
                dateEndRange=end.format('YYYY-MM-DD');
                tableEntry.draw();
            });

            // set date during initialization

            dateStartRange=moment($('input[name="daterange"]').val().split(" - ")[0]).format('YYYY-MM-DD');
            dateEndRange=moment($('input[name="daterange"]').val().split(" - ")[1]).format('YYYY-MM-DD');
            
            // DataTable
            tableEntry = $('#table').DataTable({
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "drawCallback": function( settings ) {    
                },
                "ajax":{
                    "url": "<?php echo base_url('admin/MaterialLog/materialLogDatatable') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":function(data) {
                        data.project = $('.projectEntry').val();
                        data.material = $('.materialEntry').val();
                        data.supplier = $('.supplierEntry').val();
                        data.supervisor = $('.supervisorEntry').val();
                        data.status = $('.statusEntry').val();
                        data.dateStartRange=dateStartRange;
                        data.dateEndRange=dateEndRange;
                        data.<?php echo $this->security->get_csrf_token_name(); ?> = "<?php echo $this->security->get_csrf_hash(); ?>";
                    },
                },
                "columns": [
                          { "data": "challan_no" },
                          { "data": "challan_date" },
                          { "data": "supervisor_name" },
                          { "data": "supplier_name" },
                          { "data": "status" },
                          { "data": "action" }
                ],
                columnDefs: [
                    {
                        "targets": [0],
                        "visible": true,
                        "searchable": true,
                        "sortable":true,
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
                    },
                    {
                        "targets": [4],
                        "visible": true,
                        "searchable": true,
                        "sortable":true,
                        "type": "string"
                    },
                    {
                        "targets": [5],
                        "visible": true,
                        "searchable": false,
                        "sortable":false,
                        "type": "string"
                    }
                ]
            });
            
            $('.projectEntry').change(function () {
                var optionHTML="<option value=''>All Supervisor</option>";
                var projectSupplierOption ="<option value=''>All Supplier</option>";
                var projectMaterialOption ="<option value=''>All Material</option>";

                var project_id = $(this).val();
                var ele=this;
                if(project_id) {   
                    $.ajax({
                        url: "<?php echo base_url().'admin/MaterialLog/getFilterDetailAjax/'?>"+project_id,
                        type: "GET",
                        dataType: "json",
                        success:function(data) {
                            // $('select[name="city"]').empty();
                            if(data.getProjectSupervisor.length > 0)
                            {
                                $.each(data.getProjectSupervisor, function(key, value) {
                                    optionHTML+='<option  value="'+ value.user_id +'">'+ value.supervisor_name +'</option>';
                                });

                                $(".supervisorFilter").show();
                            }
                            else
                            {
                                $(".supervisorFilter").hide();
                            }
                            

                            if(data.getProjectSupplier.length > 0)
                            {
                                $.each(data.getProjectSupplier, function(key, value) {
                                    projectSupplierOption+='<option  value="'+ value.id +'">'+ value.name +'</option>';
                                });

                                $(".supplierFilter").show();
                            }
                            else
                            {
                                $(".supplierFilter").hide();
                            }
                                
                            if(data.getProjectSupplier.length > 0)
                            {    
                                $.each(data.getProjectMaterial, function(key, value) {
                                    projectMaterialOption+='<option  value="'+ value.id +'">'+ value.name +'</option>';
                                });
                                $(".materialFilter").show();
                            }
                            else
                            {
                                $(".materialFilter").hide();   
                            }    
                            $('.supervisorEntry').html(optionHTML);
                            $('.supplierEntry').html(projectSupplierOption);
                            $('.materialEntry').html(projectMaterialOption);
                        }
                    });
                }else{
                    $('.supplierEntry').html(projectSupplierOption);
                    $('.supervisorEntry').html(optionHTML);
                    $('.materialEntry').html(projectMaterialOption);

                    $(".supervisorFilter").hide();
                    $(".supplierFilter").hide();
                    $(".materialFilter").hide();
                }

                tableEntry.draw();
            });

            $('.supervisorEntry').change(function () {
                tableEntry.draw();
            });

            $('.supplierEntry').change(function () {
                tableEntry.draw();
            });

            $('.materialEntry').change(function () {
                tableEntry.draw();
            });
            $('.statusEntry').change(function () {
                tableEntry.draw();
            });
        });
        var base_url = '<?php echo base_url(); ?>';


        $(document).ready(function () {
        $('.alert-success').fadeOut(3000); //remove suucess message
        //datatables
    });   
    // Delete material entry log
    function material_entry_log_delete(id) {

        if (id !== '' ) {
            if (confirm('Are you sure to delete material entry log?')) {
               $.ajax({
                url: "<?php echo base_url().'admin/MaterialLog/ajax_delete/' ?>"+id,
                type:'POST',
                dataType: 'json',
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
       } /*end of date and reason check if*/

   }
</script>