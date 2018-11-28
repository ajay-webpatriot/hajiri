<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo (!empty($title) ? ucwords($title) : ''); ?>
            <small><?php echo (!empty($description) ? $description : ''); ?></small>
        </h1>
        <?php echo (!empty($breadcrumb) ? $breadcrumb : ''); ?>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8 col-offset-2">
                <!-- Horizontal Form -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo (!empty($description) ? $description : ''); ?></h3>
                    </div>
                    <!-- /.box-header -->
                    <?php if ($this->session->flashdata('error')) { ?>
                        <div class="alert alert-danger">
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
                    <form action="" id="add-labour" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Name:</label>

                                <div class="col-sm-9">
                                    <input name="name" placeholder="Name" class="form-control" type="text" value="<?php echo (isset($_POST['name'])) ? $_POST['name'] : ''; ?>">
                                    <span class="error"><?php echo (form_error('name')) ? form_error('name') : ''; ?></span>
                                </div>
                            </div>
                           
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Date:</label>
                                <div class="col-sm-9">
                                    <input name="date" id="date" placeholder="Select Date" class="form-control datepicker" type="text" value="<?php echo (isset($_POST['date'])) ? $_POST['date'] : ''; ?>">
                                    <span class="error"><?php echo (form_error('date')) ? form_error('date') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="pid" class="col-sm-3 control-label">Status:</label>
                                <div class="col-sm-9">
                                    
                                     <input type="checkbox" name="status" value="1" > Active
                                    
                                </div>
                            </div>


                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <input type="submit" id="btnSave" name="submit" class="btn btn-primary" value="Save">
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
    jQuery(document).ready(function () {
        //For Date Picker
        $( ".datepicker" ).datepicker({
        format: 'dd-mm-yyyy'
        });

        $("[data-mask]").inputmask();
        jQuery('#add-labour').submit(function (event) {
            var exp = /^[a-zA-Z ]+$/;
            var phone_pattern = /^\d{10}$/;
            var aadhar_pattern = /^([0-9]{4}-){2}[0-9]{4}$/;
            var labour_name = jQuery("[name='name']");
            var pid = jQuery("select[name='pid']");
            var category = jQuery("select[name='category']");
            var user_id = jQuery("select[name='user_id']");
            var contact = jQuery("[name='contact']");
            var aadhar_id = jQuery("[name='aadhar_id']");
            var daily_wage = jQuery("[name='daily_wage']");
            var total_amount = jQuery("[name='total_amount']");
            var paid_amount = jQuery("[name='paid_amount']");
            var due_amount = jQuery("[name='due_amount']");

            var error = 0;
            if (labour_name.val() == '') {
                labour_name.css({'border': '1px solid red', });
                labour_name.next().text("Please enter labour name");
                error = 1;
            } else {
                if (labour_name.val().match(exp)) {
                    labour_name.css({'border': '1px solid green', });
                    labour_name.next().text("");
                } else {
                    labour_name.css({'border': '1px solid red', });
                    labour_name.next().text("Please enter valid labour name");
                    error = 1;
                }
            }
            if (category.val() == '') {
                category.css({'border': '1px solid red', });
                category.next().text("Please select labour category");
                error = 1;
            } else {
                category.css({'border': '1px solid green', });
                category.next().text("");
            }
            if (pid.val() == '') {
                pid.css({'border': '1px solid red', });
                pid.next().text("Please select project");
                error = 1;
            } else {
                pid.css({'border': '1px solid green', });
                pid.next().text("");
            }
            if (user_id.val() == '') {
                user_id.css({'border': '1px solid red', });
                user_id.next().text("Please select project manager");
                error = 1;
            } else {
                user_id.css({'border': '1px solid green', });
                user_id.next().text("");
            }
            if (contact.val() == '') {
                contact.css({'border': '1px solid red', });
                contact.next().text("Please enter contact number");
                error = 1;
            } else {
                if (contact.val().match(phone_pattern)) {
                    contact.css({'border': '1px solid green', });
                    contact.next().text("");
                } else {
                    contact.css({'border': '1px solid red', });
                    contact.next().text("Please enter valid contact number");
                    error = 1;
                }
            }
            if (aadhar_id.val() == '') {
                aadhar_id.css({'border': '1px solid red', });
                aadhar_id.next().text("Please enter aadhar number");
                error = 1;
            } else {
                if (aadhar_id.val().match(aadhar_pattern)) {
                    aadhar_id.css({'border': '1px solid green', });
                    aadhar_id.next().text("");
                } else {
                    aadhar_id.css({'border': '1px solid red', });
                    aadhar_id.next().text("Please enter valid aadhar number e.g.(xxxx-xxxx-xxxx)");
                    error = 1;
                }
            }
            if (daily_wage.val() == '') {
                daily_wage.css({'border': '1px solid red', });
                daily_wage.next().text("Please enter daily wage");
                error = 1;
            } else {
                daily_wage.css({'border': '1px solid green', });
                daily_wage.next().text("");
            }
            if (total_amount.val() == '') {
                total_amount.css({'border': '1px solid red', });
                total_amount.next().text("Please enter total amount");
                error = 1;
            } else {
                total_amount.css({'border': '1px solid green', });
                total_amount.next().text("");
            }
            if (paid_amount.val() == '') {
                paid_amount.css({'border': '1px solid red', });
                paid_amount.next().text("Please enter paid amount");
                error = 1;
            } else {
                paid_amount.css({'border': '1px solid green', });
                paid_amount.next().text("");
            }
            if (due_amount.val() == '') {
                due_amount.css({'border': '1px solid red', });
                due_amount.next().text("Please enter due amount");
                error = 1;
            } else {
                due_amount.css({'border': '1px solid green', });
                due_amount.next().text("");
            }
            if (error > 0) {
                event.preventDefault();
            }
        });
    });
</script>