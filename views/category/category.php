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
                        <button class="btn btn-info" id="add">
                            <i class="glyphicon glyphicon-plus"></i> 
                            Add category
                        </button>
                    </div>
                    <br/>
                    <div id="addCat">
                        <br/>
                        <br/>
                        <form action="" id="add-category" class="form-horizontal" method="POST">
                            <div class="col-md-8 col-md-offset-2">
                                <input type="text" name="categoryName" placeholder='Category name' class="form-control" required>
                                <span class="error"><?php echo (form_error('categoryName')) ? form_error('categoryName') : ''; ?></span>
                            </div>
                            <div class="col-md-2">
                                <input type="submit" name="submit" value="Add" class="btn btn-success" />
                            </div>
                            <hr/>
                        </form>
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
                    <table id="table" class="tableFilter table table-striped table-hover table-bordered display responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>
                                    Category
                                </th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($Category)) {
                                foreach ($Category as $labour) {
                                    ?>
                                    <tr>
                                         <td><?php echo (isset($labour->category)) ? $labour->category : ''; ?></td>
                                        
                                        <td> 

                                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="<?php echo '#editModal'.$labour->id; ?>" title="Edit category" >
                                                <i class="glyphicon glyphicon-pencil"></i>
                                            </button>  
                                            <button class="btn btn-sm btn-danger" title="Delete worker" onclick="worker_delete('<?php echo $labour->id; ?>')">
                                                <i class="glyphicon glyphicon-trash"></i> 
                                            </button>
                                            <!-- edit category modal -->

                                            <div class="modal fade" id="<?php echo 'editModal'.$labour->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
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
                                                                    <input name="categoryName" value="<?php echo (isset($labour->category)) ? $labour->category : ''; ?>" id="<?php echo 'cateName'.$labour->id; ?>" placeholder="Category name" class="form-control" type="text" required />
                                                                    <input type="text" name="catId" value="<?php echo (isset($labour->id)) ? $labour->id : ''; ?>" class='hidden'>
                                                                       
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
        var table = $('#table').DataTable({
            columnDefs: [
               { orderable: false, targets: -1 }
            ]
        });
        
        $("#addCat").hide();

        $("#add").click(function(){
            $("#addCat").toggle(500);
        });

    }); /*End of document ready*/

    var base_url = '<?php echo base_url(); ?>';
    

    // Delete worker
    function worker_delete(id) {
        
        if (id !== '' ) {
             $.ajax({
                url: "<?php echo base_url().'admin/category/ajax_delete/' ?>"+id,
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

        } /*end of date and reason check if*/
              
    }

</script>




