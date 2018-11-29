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
		<li><a href="<?php echo base_url('admin/supplier'); ?>"> Suppliers</a></li>
		<li class="active"><?php echo (isset($title) ? $title : ''); ?></li>
	</ol>
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
                    <form action="" id="add-supplier" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <div class="box-body">

                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Company Name <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <input name="company_name" placeholder="Company Name" class="form-control" type="text" value="<?php echo (isset($_POST['company_name'])) ? $_POST['company_name'] : ''; ?>" required="required">
                                    <span class="error"><?php echo (form_error('company_name')) ? form_error('company_name') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Supplier Name <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <input name="supplier_name" placeholder="Supplier Name" class="form-control" type="text" value="<?php echo (isset($_POST['supplier_name'])) ? $_POST['supplier_name'] : ''; ?>" required="required">
                                    <span class="error"><?php echo (form_error('supplier_name')) ? form_error('supplier_name') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Contact Number <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <input name="contact_number" placeholder="Contact Number" class="form-control" type="text" value="<?php echo (isset($_POST['contact_number'])) ? $_POST['contact_number'] : ''; ?>" required="required" onkeypress="return isNumber(event)" minlength ="10" maxlength="12">
                                    <span class="error"><?php echo (form_error('contact_number')) ? form_error('contact_number') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">GST No</label>
                                <div class="col-sm-9">
                                    <input name="gst_number" placeholder="GST No" class="form-control" type="text" value="<?php echo (isset($_POST['gst_number'])) ? $_POST['gst_number'] : ''; ?>">
                                    <span class="error"><?php echo (form_error('gst_number')) ? form_error('gst_number') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Address <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    
                                    <textarea name="address" required="required" placeholder="Address" class="form-control"><?php echo (isset($_POST['address'])) ? $_POST['address'] : ''; ?></textarea> 
                                   
                                    <span class="error"><?php echo (form_error('address')) ? form_error('address') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Email Id <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <input name="email_id" placeholder="Email Id" class="form-control" type="email" value="<?php echo (isset($_POST['email_id'])) ? $_POST['email_id'] : ''; ?>" required="required">
                                    <span class="error"><?php echo (form_error('email_id')) ? form_error('email_id') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Assign Project <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    
                                    <select placeholder="Select Project" class="form-control project" name="project_id[]" multiple="multiple" required="required">
                                        
                                        <?php 
                                            foreach ($projects as $proj) {
                                        ?>
                                        <option value="<?php echo $proj->project_id; ?>"><?php echo $proj->project_name; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="error"><?php echo (form_error('project_id')) ? form_error('project_id') : ''; ?></span>
                                </div>
                                    <!-- <input name="unit_measurement" placeholder="Unit of Measurement" class="form-control" type="text" value="<?php echo (isset($_POST['unit_measurement'])) ? $_POST['unit_measurement'] : ''; ?>" >
                                    <span class="error"><?php echo (form_error('unit_measurement')) ? form_error('unit_measurement') : ''; ?></span> -->
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Assign Material Category <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    
                                    <select placeholder="Select Material Category" class="form-control category" name="category_id[]" multiple="multiple" required="required">
                                        
                                        <?php 
                                            foreach ($Categories as $cat) {
                                        ?>
                                        <option value="<?php echo $cat->id; ?>"><?php echo $cat->category; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="error"><?php echo (form_error('category_id')) ? form_error('category_id') : ''; ?></span>
                                </div>
                                    <!-- <input name="unit_measurement" placeholder="Unit of Measurement" class="form-control" type="text" value="<?php echo (isset($_POST['unit_measurement'])) ? $_POST['unit_measurement'] : ''; ?>" >
                                    <span class="error"><?php echo (form_error('unit_measurement')) ? form_error('unit_measurement') : ''; ?></span> -->
                                
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

<script type="text/javascript">
    jQuery(function ($) {
        $('.project').select2({
            placeholder: "Select Project",
            allowClear: true
        });

        $('.category').select2({
            placeholder: "Select Material Category",
            allowClear: true
        });
    });
    //only number keypress
    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
</script>
