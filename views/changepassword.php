<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo (!empty($title) ? ucwords($title) : ''); ?>
        </h1>
        <?php echo (!empty($breadcrumb) ? $breadcrumb : ''); ?>
    </section>
    <ol class="breadcrumb margin-bottom0">
        <li><a href="<?php echo base_url('admin'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="<?php echo base_url('admin/profile'); ?>">Profile</a></li>
        <li class="active"><?php echo (isset($title) ? $title : ''); ?></li>
    </ol>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-8 col-offset-2">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Change Paswword</h3>
                    </div>
                    <!-- /.box-header -->
                    <?php
                    if ($this->session->flashdata('warningMsg')) { ?>
                        <div class="alert alert-warning">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close" style="color:black; text-decoration: none;">x</a>
                            <strong>Warning ! </strong> <?php echo $this->session->flashdata('warningMsg'); ?>
                        </div>
                        <?php
                    }
                     if ($this->session->flashdata('errorMsg')) { ?>
                        <div class="alert alert-danger">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close" style="color:black; text-decoration: none;">x</a>
                            <strong>Error ! </strong> <?php echo $this->session->flashdata('errorMsg'); ?>
                        </div>
                        <?php
                    }
                    if ($this->session->flashdata('successMsg')) {
                        ?>
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close" style="color:black;text-decoration: none;">x</a>
                            <strong>Success ! </strong> <?php echo $this->session->flashdata('successMsg'); ?>
                        </div>
                        <?php
                    }
                    ?>
                    <!-- form start -->
                    <form action="" id="change-password" method="POST" enctype="multipart/form-data">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Old Password
                                    <span class="required" style="color:#F83A18;"> * </span></label>
                                <input type="password" name="oldpassword" class="form-control" value="<?php echo set_value('oldpassword'); ?>" placeholder="Enter Old Password">
                                <span class="error"> <?php echo (form_error('oldpassword')) ? form_error('oldpassword') : $custom_error; ?></span>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">New Password
                                    <span class="required" style="color:#F83A18;"> * </span></label>
                                <input type="password" name="password" class="form-control" value="<?php echo set_value('password'); ?>" placeholder="Enter Password">
                                <span class="help-block error"> <?php echo form_error('password'); ?></span>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Retype Password
                                    <span class="required" style="color:#F83A18;"> * </span></label>
                                <input type="password" name="conf_password" class="form-control" value="" placeholder="Retype Password">
                                <span class="help-block error"> <?php echo form_error('conf_password'); ?></span>
                            </div>

                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" name="changepassword" value="Change Password">

                        </div>
                    </form>

                </div>
                <!-- /.box -->


            </div>
            <!--/.col (left) -->

        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script type="text/javascript">
    // When the document is ready
    $(document).ready(function () {
        $('.alert-success').fadeOut(2000); //remove suucess message
    });
    $(document).ready(function () {
        $('#change-password').submit(function (event) {
            var error = 0;
            var oldpassword = $('[name="oldpassword"]');
            var password = $('[name="password"]');
            var conf_password = $('[name="conf_password"]');
            if (oldpassword.val() == "") {
                oldpassword.css({"border": "1px solid red"});
                oldpassword.next().text("Please enter old password");
                error = 1;
            } else {
                oldpassword.css({"border": "1px solid green"});
                oldpassword.next().text("");
            }
            if (password.val() == "") {
                password.css({"border": "1px solid red"});
                password.next().text("Please enter password");
                error = 1;
            } else {
                password.css({"border": "1px solid green"});
                password.next().text("");
            }
            if (conf_password.val() == "") {
                conf_password.css({"border": "1px solid red"});
                conf_password.next().text("Please enter confirm password");
                error = 1;
            } else {
                conf_password.css({"border": "1px solid green"});
                conf_password.next().text("");
            }
            if (password.val() != conf_password.val()) {
                conf_password.css({'border': '1px solid red', });
                conf_password.next().text("Confirm password dosen't match with password");
                error = 1;
            }
            if (error > 0) {
                event.preventDefault();
            }
        });
    });
</script>