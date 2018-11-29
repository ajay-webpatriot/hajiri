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
    <section class="content row">
        <div class="col-md-12">
            <!-- Filter portion start -->
            <div class="box">
                <div class="box-body table-responsive">
                    <div class="filters col-md-12">
                        <br/>
                        <div class="col-md-1">
                            <h4>Filters:</h4><br/>
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
                        
                    </div>
                </div>
            </div> 
            <!-- Filter portion end -->
            <div class="box">
                <div class="box-header">
                    <!-- <h3 class="box-title"><?php echo (isset($title) ? $title : ''); ?></h3> -->
                    <div class="box-tools pull-right">
                        <!-- Add button -->
                        
                        <a href="<?php echo base_url('admin/material/addMaterial'); ?>">  
                            <button class="btn btn-info">
                                <i class="glyphicon glyphicon-plus"></i> 
                                Add Material 
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

                    
                    <table id="tableMaterial" class="table table-striped table-bordered display responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Material Name</th>
                                <th>Material Category</th>
                                <th>Material Unit</th>
                                <th>Project Name</th>
                                <th>Status</th>
                                <th style="width:150px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            /*if (!empty($materials)) {
                                foreach ($materials as $material) {
                                        if ($material->status == "0") {
                                            $status = '<a class="btn btn-sm btn-danger btn-xs" href="#" title="Status" data-status="' . $material->status . '" onclick="change_status(' . "'" . $material->id . "'" . ')">Inactive</a>';
                                        } else {
                                            $status = '<a class="btn btn-sm btn-success btn-xs" href="#" title="Status" data-status="' . $material->status . '" onclick="change_status(' . "'" . $material->id . "'" . ')">Active</a>';
                                        }
                                    ?>
                                    <tr> 
                                       
                                        <td><?php echo $material->name; ?></td>
                                        <td><?php echo $material->category_name; ?></td>
                                        <td><?php echo $material->unit_measurement; ?></td>
                                        <td><?php echo $material->project_name; ?></td>
                                        <td><?php echo $status; ?></td>
                                        <td><a class="btn btn-sm btn-primary" href="<?php echo base_url('admin/material/editMaterial/') . $material->id; ?>" title="Edit material">
                                                <i class="glyphicon glyphicon-pencil"></i> </a>

                                                <button class="btn btn-sm btn-danger" title="Delete material" onclick="material_delete('<?php echo $material->id; ?>')">
                                                <i class="glyphicon glyphicon-trash"></i> 
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }*/
                            ?>
                        </tbody>
                        <div id="divLoading"></div> 
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- /.content -->

<script type="text/javascript">
    jQuery(function ($) {
        // jQuery("#tableMaterial").DataTable({
        //     columnDefs: [
        //        { orderable: false, targets: -1 },
        //        { orderable: false, targets: -2 },
        //     ],
        // });
        // DataTable
        var table = $('#tableMaterial').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "drawCallback": function( settings ) {    
            },
            "ajax":{
                "url": "<?php echo base_url('admin/Material/materialDatatable') ?>",
                "dataType": "json",
                "type": "POST",
                "data":function(data) {
                    data.project = $('.project').val();
                    data.<?php echo $this->security->get_csrf_token_name(); ?> = "<?php echo $this->security->get_csrf_hash(); ?>";
                },
            },
            "columns": [
                      { "data": "name" },
                      { "data": "category_name" },
                      { "data": "unit_measurement" },
                      { "data": "project_name" },
                      { "data": "status" },
                      { "data": "action" },
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
        
        
        $('.project').change(function () {
            table.draw();
        });
        jQuery('.alert-success').fadeOut(3000); //remove suucess message
    });


    // Delete materials
    function material_delete(id) {
        
        if (id !== '' ) {
            if (confirm('Are you sure to delete material?')) {
                 $.ajax({
                    url: "<?php echo base_url().'admin/Material/ajax_delete/' ?>"+id,
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

    // update status
    function change_status(id) {
        if (confirm('Are you sure to change status?')) { // ajax change status
            jQuery.ajax({
                url: "<?php echo site_url('admin/material/ajax_change_status') ?>/" + id,
                type: "POST",
                dataType: "JSON",
                success: function (data) { // jQuery("#table").DataTable();
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Error Changing Status');
                },
                beforeSend: function () {
                    jQuery("div#divLoading").addClass('show');
                },
                complete: function () {
                    jQuery("div#divLoading").removeClass('show');
                },
            });
        }
    }

</script>



