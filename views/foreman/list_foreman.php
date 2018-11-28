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
                        <a href="<?php echo base_url('admin/foreman/addForeman'); ?>">  
                            <button class="btn btn-info">
                                <i class="glyphicon glyphicon-plus"></i> 
                                Add Supervisor 
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
                    <table id="table123" class="table table-striped table-bordered display responsive nowrap" cellspacing="0" width="100%">
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
                            if (!empty($foremans)) {
                                foreach ($foremans as $foreman) {
                                        if ($foreman->status == "2") {
                                            $status = '<a class="btn btn-sm btn-danger btn-xs" href="#" title="Status" data-status="' . $foreman->status . '" onclick="change_status(' . "'" . $foreman->user_id . "'" . ')">Inactive</a>';
                                        } else {
                                            $status = '<a class="btn btn-sm btn-success btn-xs" href="#" title="Status" data-status="' . $foreman->status . '" onclick="change_status(' . "'" . $foreman->user_id . "'" . ')">Active</a>';
                                        }
                                    ?>
                                    <tr> <?php if( $this->session->userdata('user_designation') == 'Superadmin' ) {?>
                                        <td><?php echo $foreman->company_name; ?></td>
                                        <?php } ?>
                                        <td><?php echo $foreman->user_name.' '.$foreman->user_last_name; ?></td>
                                        <td><?php echo $foreman->user_email; ?></td>
                                        <td> <?php echo $status; ?></td>
                                        <td><a class="btn btn-sm btn-primary" href="<?php echo base_url('admin/foreman/editForeman/') . $foreman->user_id; ?>" title="Edit">
                                                <i class="glyphicon glyphicon-pencil"></i> </a>
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
        jQuery("#table123").DataTable({
            columnDefs: [
               { orderable: false, targets: -1 },
               { orderable: false, targets: -2 },
            ],
        });
        jQuery('.alert-success').fadeOut(3000); //remove suucess message
    });
    

</script>



