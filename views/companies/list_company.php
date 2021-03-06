<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper row">
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
    <section class="content">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><?php echo (isset($title) ? $title : ''); ?></h3>
                    <div class="box-tools pull-right">
                        <!-- Add button -->
                        <a href="<?php echo base_url('admin/companies/addEditCompany');   ?>">  
							<button class="btn btn-success">
								<i class="glyphicon glyphicon-plus"></i> Add company
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
                    <table id="table" class="table table-striped table-bordered display responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Company name</th>
                                <th>Email</th>
                                <th>Contact no</th>
                                <th>Status</th>
                                <th style="width:100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($companies)) {
                                $count=0;
                                foreach ($companies as $company) {
                                    $count =$count + 1;
									if ($company->status == "2") {
                                        $status = '<a class="btn btn-sm btn-danger btn-xs" title="Status">Inactive</a>';
                                    } else {
                                        $status = '<a class="btn btn-sm btn-success btn-xs" title="Status">Active</a>';
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo $company->company_name; ?></td>
                                        <td><?php echo $company->company_email; ?></td>
                                        <td><?php echo $company->company_contact_no; ?></td>
                                        <td><?php echo $status; ?></td>
                                        <td> 
                                            <a class="btn btn-sm btn-primary" href="<?php echo base_url('admin/companies/addEditcompany/') . $company->compnay_id; ?>" title="Edit">
                                                <i class="glyphicon glyphicon-pencil"></i> </a>
                                            <a class="btn btn-sm btn-danger" href="<?php echo base_url('admin/companies/deletecompany/') . $company->compnay_id; ?>" title="Delete" >
                                                <i class="glyphicon glyphicon-trash"></i> </a>

                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
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
           {
                "targets": [0],
                "searchable": true,
                "sortable":false,
                "type": "string"
            },
            {
                "targets": [1],
                "searchable": true,
                "type": "string"
            },
            {
                "targets": [2],
                "searchable": true,
                "type": "string"
            },
            {
                "targets": [3],
                "sortable":false,
                "searchable": false,
                "type": "string"
            },

            {
                "targets": [4],
                "sortable":false,
                "searchable": false,
                "type": "string"
            }

        ]
        });
    });
    $(document).ready(function () {
        $('.alert-success').fadeOut(3000);
    });

</script>



