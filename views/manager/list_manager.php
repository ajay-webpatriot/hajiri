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
                        <?php 
                            if($limit->wLimit > 0 || $planId->id == 3 || $this->session->userdata('user_designation') == 'Superadmin'){
                        ?>
                        <a href="<?php echo base_url('admin/manager/addManager'); ?>">  
                            <button class="btn btn-info">
                                <i class="glyphicon glyphicon-plus"></i> 
                                Add Admin 
                                <?php 
                                    if($planId->id != 3 && $this->session->userdata('user_designation') == 'admin'){
                                        echo '('.$limit->wLimit.')'; 
                                    }
                                ?>
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
                    <table id="table" class="table table-striped table-bordered display responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <?php if( $this->session->userdata('user_designation') == 'Superadmin' ) {?>
                                <th>Company name</th>
                                <?php } ?>
                                <th>Name</th>
                                <th>Email</th>
								<th>Status</th>
                                <th style="width:150px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            
                            if (!empty($managers)) {
                                foreach ($managers as $manager) {
                                    if ($manager->status == "2") {
                                        $status = '<a class="btn btn-sm btn-danger btn-xs" href="javascript:void(0)" title="Status" data-status="' . $manager->status . '" )">Inactive</a>';
                                    } else {
                                        $status = '<a class="btn btn-sm btn-success btn-xs" href="javascript:void(0)" title="Status" data-status="' . $manager->status . '")">Active</a>';
                                    }
									$manager->created_at = date('d-m-Y', strtotime('201-02-03') );
                                    $managerdate = date('d-m-Y', strtotime($manager->created_at));
                                    if ($managerdate == date('d-m-Y')) {
                                        echo "<tr style='background:green;color:#fff;'>";
                                    } else {
                                        echo "<tr>";
                                    } ?>
                                        <?php if( $this->session->userdata('user_designation') == 'Superadmin' ) {?>
                                        <td><?php echo $manager->company_name; ?></td>
                                        <?php } ?>
                                        <td><?php echo $manager->user_name.' '.$manager->user_last_name;  ?></td>
                                        <td><?php echo $manager->user_email; ?></td>
                                        <td> <?php echo $status; ?></td>
                                        <td>
                                            <a class="btn btn-sm btn-primary" href="<?php echo base_url('admin/manager/editManager/') . $manager->user_id; ?>" title="Edit">
                                                <i class="glyphicon glyphicon-pencil"></i> 
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                        <div id="divLoading"> 
                        </div>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- /.content -->

<script type="text/javascript">
    $(function () {
        $("#table").DataTable({
            columnDefs: [
               { orderable: false, targets: -1 }
            ],
        });
    });

    var table;
    var base_url = '<?php echo base_url(); ?>';

    function delete_manager(id) {
        if (confirm('Are you sure delete this data?')) {
            // ajax delete data to database
            $.ajax({
                url: "<?php echo site_url('admin/Manager/ajax_delete') ?>/" + id,
                type: "POST",
                dataType: "JSON",
                success: function (data)
                {
                    $('.alert-success').html("Manager Deleted Successfully");
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error deleting data');
                },
                beforeSend: function () {
                    $("div#divLoading").addClass('show');
                },
                complete: function () {
                    $("div#divLoading").removeClass('show');
                },
            });

        }
    }
    function change_status(id) {
        if (confirm('Are you sure to change status?')) { // ajax change status
            jQuery.ajax({
                url: "<?php echo site_url('admin/Manager/ajax_change_status') ?>/" + id,
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
    function delete_manager(id) {
        if (confirm('Are you sure to delete user?')) { // ajax change status
            jQuery.ajax({
                url: "<?php echo site_url('admin/Manager/ajax_delete_manager') ?>/" + id,
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

    $(document).ready(function () {
        $('.alert-success').fadeOut(3000); //remove suucess message
        //datatables
    });

</script>



