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
                                <th>Category</th>
                                <th>Material Name</th>
                                <th>Supplier Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach ($materialLog as $value) { ?>
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
                                <?php  } ?>
                            </tbody>
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
                "order": [[ 0, "desc" ]]
            });
        });
        var table;
        var base_url = '<?php echo base_url(); ?>';


        var table;
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