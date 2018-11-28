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
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><?php echo (isset($title) ? $title : ''); ?></h3>
                    <div class="box-tools pull-right">
                        <!-- Add button -->
                        
                        <a href="<?php echo base_url('admin/supplier/addSupplier'); ?>">  
                            <button class="btn btn-info">
                                <i class="glyphicon glyphicon-plus"></i> 
                                Add Supplier 
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
                    <table id="tableSupplier" class="table table-striped table-bordered display responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                
                                <th>Supplier Name</th>
                                <th>Comapny Name</th>
                                <th>Contact Number</th>
                                <th>Status</th>
                                <th style="width:150px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($suppliers)) {
                                foreach ($suppliers as $supplier) {
                                        if ($supplier->status == "0") {
                                            $status = '<a class="btn btn-sm btn-danger btn-xs" href="#" title="Status" data-status="' . $supplier->status . '" onclick="change_status(' . "'" . $supplier->id . "'" . ')">Inactive</a>';
                                        } else {
                                            $status = '<a class="btn btn-sm btn-success btn-xs" href="#" title="Status" data-status="' . $supplier->status . '" onclick="change_status(' . "'" . $supplier->id . "'" . ')">Active</a>';
                                        }
                                    ?>
                                    <tr> 
                                        
                                        <td><?php echo $supplier->name; ?></td>
                                        <td><?php echo $supplier->company_name; ?></td>
                                        <td><?php echo $supplier->contact_number; ?></td>
                                        <td><?php echo $status; ?></td>
                                        <td><a class="btn btn-sm btn-primary" href="<?php echo base_url('admin/supplier/editSupplier/') . $supplier->id; ?>" title="Edit supplier">
                                                <i class="glyphicon glyphicon-pencil"></i> </a>

                                                <button class="btn btn-sm btn-danger" title="Delete supplier" onclick="supplier_delete('<?php echo $supplier->id; ?>')">
                                                <i class="glyphicon glyphicon-trash"></i> 
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
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
        jQuery("#tableSupplier").DataTable({
            columnDefs: [
               { orderable: false, targets: -1 },
               { orderable: false, targets: -2 },
            ],
        });
        jQuery('.alert-success').fadeOut(3000); //remove suucess message
    });


    // Delete suppliers
    function supplier_delete(id) {
        
        if (id !== '' ) {
            if (confirm('Are you sure to delete supplier?')) {
                 $.ajax({
                    url: "<?php echo base_url().'admin/Supplier/ajax_delete/' ?>"+id,
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
                url: "<?php echo site_url('admin/supplier/ajax_change_status') ?>/" + id,
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



