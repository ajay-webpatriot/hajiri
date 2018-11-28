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
                        <button class="btn btn-info" id="addCategory" data-toggle="modal" data-target="#addCategoryModel">
                            <i class="glyphicon glyphicon-plus"></i> 
                            Add category
                        </button>
                    </div>
                    <!-- Add category modal -->
                    <div class="modal fade" id="addCategoryModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
                        <div class="modal-dialog modal-sm" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="btn btn-danger close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="exampleModalLabel">
                                        Add Category
                                    </h4>
                                </div>
                                <form action="" class="mark-present" method="POST">
                                    <div class="modal-body">
                                        
                                            <div class="form-group">
                                                <input name="categoryName" value="" id="materialCategory" placeholder="Category name" class="form-control" type="text" required />
                                                   
                                            </div>
                                             <div class="form-group">
                                                <input name="approximate_estimate_ratio" value="" id="approximate_estimate_ratio" placeholder="Approximate estimate ratio" class="form-control" type="text" required />
                                                   
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                            <input type="submit" class="btn btn-success" name="submit" value="submit" />
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>    
                    <br/>
                    
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
                    <?php if ($this->session->flashdata('error') != ''): ?>
                        <div class="alert alert-error alert-dismissable">
                            <i class="fa fa-check"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <b>Error!</b> 
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>
                    <table id="tableMaterialCategory" class="tableFilter table table-striped table-hover table-bordered display responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>
                                    Category
                                </th>
                                <th>
                                    Approximate estimate ratio
                                </th>
                                <th>
                                    Status
                                </th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($Category)) {
                                foreach ($Category as $value) {

                                    if ($value->status == "0") {
                                            $status = '<a class="btn btn-sm btn-danger btn-xs" href="#" title="Status" data-status="' . $value->status . '" onclick="change_status(' . "'" . $value->id . "'" . ')">Inactive</a>';
                                        } else {
                                            $status = '<a class="btn btn-sm btn-success btn-xs" href="#" title="Status" data-status="' . $value->status . '" onclick="change_status(' . "'" . $value->id . "'" . ')">Active</a>';
                                        }
                                    ?>
                                    <tr>
                                        <td><?php echo (isset($value->category)) ? $value->category : ''; ?></td>
                                        <td><?php echo (isset($value->approximate_estimate_ratio)) ? $value->approximate_estimate_ratio : ''; ?></td>
                                        <td><?php echo $status; ?></td>
                                        <td> 

                                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="<?php echo '#editModal'.$value->id; ?>" title="Edit category" >
                                                <i class="glyphicon glyphicon-pencil"></i>
                                            </button>  
                                            <button class="btn btn-sm btn-danger" title="Delete category" onclick="material_category_delete('<?php echo $value->id; ?>')">
                                                <i class="glyphicon glyphicon-trash"></i> 
                                            </button>

                                            <!-- edit category modal -->

                                            <div class="modal fade" id="<?php echo 'editModal'.$value->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
                                                <div class="modal-dialog modal-sm" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="btn btn-danger close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="exampleModalLabel">
                                                                Edit Category
                                                            </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="" class="mark-present" method="POST">
                                                                <div class="form-group">
                                                                    <input name="categoryName" value="<?php echo (isset($value->category)) ? $value->category : ''; ?>" id="<?php echo 'cateName'.$value->id; ?>" placeholder="Category name" class="form-control" type="text" required />
                                                                    <input type="text" name="catId" value="<?php echo (isset($value->id)) ? $value->id : ''; ?>" class='hidden'>
                                                                       
                                                                </div>
                                                                <div class="form-group">
                                                                    <input name="approximate_estimate_ratio" value="<?php echo (isset($value->approximate_estimate_ratio)) ? $value->approximate_estimate_ratio : ''; ?>" id="<?php echo 'approximate_estimate_ratio'.$value->id; ?>" placeholder="Approximate estimate ratio" class="form-control" type="text" required />
                                                                       
                                                                </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                                <input type="submit" class="btn btn-success" name="edit" value="submit" />
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            
                                        </td>

                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                        <div id="divLoading"> </div>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>


<!-- /.content -->

<script type="text/javascript">
    $(document).ready(function() {
        
        $('.alert-success').fadeOut(7000); //remove suucess message
        $('.alert-warning').fadeOut(7000); //remove suucess message
        // DataTable
        var table = $('#tableMaterialCategory').DataTable({
            columnDefs: [
               { orderable: false, targets: -1 }
            ]
        });
        
    }); /*End of document ready*/

    var base_url = '<?php echo base_url(); ?>';
    

    // Delete category
    function material_category_delete(id) {
        
        if (id !== '' ) {
            if (confirm('Are you sure to delete category?')) {
                 $.ajax({
                    url: "<?php echo base_url().'admin/MaterialCategory/ajax_delete/' ?>"+id,
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
                url: "<?php echo site_url('admin/MaterialCategory/ajax_change_status') ?>/" + id,
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




