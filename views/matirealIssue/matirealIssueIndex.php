<div class="content-wrapper">
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
        <div class="box">
            <div class="box-header">

                <h3 class="box-title"><?php echo (isset($title) ? $title : ''); ?></h3>

                <div class="box-tools pull-right">
                    <a href="<?php echo base_url('admin/MaterialIssue/addIssueLog/');   ?>">  
                        <button class="btn btn-info">
                            <i class="glyphicon glyphicon-plus"></i> 
                            <?php echo (isset($title) ? $title : ''); ?>
                        </button>
                    </a>
                </div>

            </div>

            <div class="box-body">
                <table id="Issuetable" class="table table-striped table-hover table-bordered  table-responsive" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Issue No</th> 
                            <th>Issue Date</th>
                            <th>Issue By</th>
                            <th>Category</th>
                            <th>Material Name</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($materialIssue != ''){

                        
                            foreach ($materialIssue as $value) { 
                        ?>
                                <tr>
                                    <td><?php echo $value->issue_no ; ?></td>
                                    <td><?php echo $value->date ; ?></td>
                                    <td><?php echo $value->issue_by_name; ?></td>
                                    <td><?php echo $value->category_name; ?></td>
                                    <td><?php echo $value->material_name; ?></td>
                                    <td><?php echo $value->quantity; ?></td>
                                    <td><?php echo $value->status; ?></td>
                                    
                                    <td> 

                                    <a class="btn btn-sm btn-primary" href="<?php echo base_url('admin/MaterialIssue/editIssueLog/') . $value->id; ?>" title="Edit material entry">
                                        <i class="glyphicon glyphicon-pencil"></i> </a>  
                                        <?php if($value->status !== 'Verified' ) { ?>
                                            <button class="btn btn-sm btn-danger" title="Delete material entry" onclick="material_issue_log_delete('<?php echo $value->id; ?>')">
                                                <i class="glyphicon glyphicon-trash"></i> 
                                            </button>
                                        <?php } ?>
                                    </td>
                                </tr>
                        <?php 
                            } 
                                } 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<script>
$(function () {
    $("#Issuetable").DataTable({
        "order": [[ 0, "desc" ]]
    });
});

// Delete material issue log
    function material_issue_log_delete(id) {

        if (id !== '' ) {
            if (confirm('Are you sure to delete material issue log?')) {
               $.ajax({
                url: "<?php echo base_url().'admin/MaterialIssue/ajax_delete/' ?>"+id,
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