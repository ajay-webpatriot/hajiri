<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo (!empty($title) ? ucwords($title) : ''); ?>
            <small><?php echo (!empty($description) ? $description : ''); ?></small>
        </h1>
    </section>
    <ol class="breadcrumb margin-bottom0">
        <li><a href="<?php echo base_url('admin'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active"><?php echo (isset($title) ? $title : ''); ?></li>
    </ol>


    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8 col-offset-2">
                <!-- Horizontal Form -->
                <div class="box box-info">
                    <!-- /.box-header -->
                    <?php if ($this->session->flashdata('error')) { ?>
                        <div class="alert alert-danger alert-dismiss">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close" style="color:black">&times;</a>
                            <strong>Error!</strong> <?php echo $this->session->flashdata('error'); ?>
                        </div>
                        <?php
                    }
                    if ($this->session->flashdata('success')) {
                        ?>
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close" style="color:black">&times;</a>
                            <strong>Success!</strong> <?php echo $this->session->flashdata('success'); ?>
                        </div>
                        <?php
                    }
                    ?>
                    <!-- form start -->
                    <form action="" id="invoice" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <!--<div class="box-body">
                           <div class="form-group">
                                <label for="year" class="col-sm-3 control-label">Select Company:</label>
                                <div class="col-sm-9">
                                    <?php
                                    echo form_dropdown('company_id', $companies, '' , 'class="form-control"');
                                    ?>
                                    <span class="error"><?php echo form_error('companies') ?></span>
                                </div>
                            </div>
                        </div> -->
                        <!-- /.box-body -->
                        <input type="hidden" name="company_id" class="form-control company_id" value="<?php echo $this->session->userdata('company_id'); ?>">
                        <div class="box-footer">
                            <input type="submit" id="btnSave" name="submit" class="btn btn-primary" value="Generate Invoice">
                        </div>
                        <!-- /.box-footer -->
                    </form>
                </div>
                <!-- /.box -->
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script>
    jQuery(document).ready(function(){
        $('.alert-dismiss').fadeOut(5000);
        $('#invoice').submit(function(event){
            var error=0;
            var companies =$("select[name='company_id']");
           if (companies.val() == '') {
                companies.css({'border': '1px solid red', });
                companies.next().text("Please select project");
                error = 1;
            } else {
                companies.css({'border': '1px solid green', });
                companies.next().text("");
            }
            if (error > 0) {
                event.preventDefault();
            }
        });
    });
</script>