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
    <section class="content">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><?php echo (isset($title) ? $title : ''); ?></h3>
                    <div class="box-tools pull-right">
                        <!-- Add button -->
                        <!--<a href="<?php //echo base_url('admin/labour/addLabour');   ?>">  <button class="btn btn-success"><i class="glyphicon glyphicon-plus"></i> Add</button></a>-->
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
                                <th>Sr.No.</th>
                                <th>Company Name</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Category</th>
                                <th>Contact No.</th>
                                <th style="width:150px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($labours)) {
                                $count=0;
                                foreach ($labours as $labour) {
                                    $count =$count + 1;
                                    ?>
                                    <tr>
                                        <td><?php echo $count;?></td>
                                        <td><?php echo (isset($labour->company_name)) ? $labour->company_name : ''; ?></td>
                                        <td><?php echo (isset($labour->labour_name)) ? $labour->labour_name : ''; ?></td>
                                        <td><?php echo (isset($labour->labour_last_name)) ? $labour->labour_last_name : ''; ?></td>
                                         <td><?php echo (isset($labour->category_name)) ? $labour->category_name : ''; ?></td>
                                        <td><?php echo (isset($labour->worker_contact)) ? $labour->worker_contact : ''; ?></td>
                                       
                                        <td> 
                                            <a class="btn btn-sm btn-primary" href="<?php echo base_url('admin/labour/editLabour/') . $labour->worker_id; ?>" title="Edit">
                                                <i class="glyphicon glyphicon-pencil"></i> </a>
                                            <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete" onclick="delete_labour('<?php echo $labour->worker_id; ?>')">
                                                <i class="glyphicon glyphicon-trash"></i> </a>

                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                        <div id="divLoading"> </div>
                        <tfoot>
                            <tr>
                                <th>Sr.No.</th>
                                <th>Company Name</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Category</th>
                                <th>Contact No.</th>
                                <th style="width:150px;">Action</th>
                            </tr>
                        </tfoot>
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
            "order": [[ 4, "desc" ]]
        });
    });
    var table;
    var base_url = '<?php echo base_url(); ?>';

    function delete_labour(id) {
        if (confirm('Are you sure delete this data?')) {
            // ajax delete data to database
            $.ajax({
                url: "<?php echo site_url('admin/Labour/ajax_delete') ?>/" + id,
                type: "POST",
                dataType: "JSON",
                success: function (data)
                {
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

    var table;
    $(document).ready(function () {
        $('.alert-success').fadeOut(3000); //remove suucess message
        //datatables
    });

</script>



