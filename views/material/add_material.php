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
		<li><a href="<?php echo base_url('admin/material'); ?>"> Materials</a></li>
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
                    <form action="" id="add-material" class="form-horizontal validateSubmit" method="POST" enctype="multipart/form-data">
                        <div class="box-body">
                            
                            <div class="form-group">
                                <label for="category_id" class="col-sm-3 control-label">Project <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <select class="form-control project" name="project_id" required="required">
                                        <option value="">--Select Project--</option>
                                        <?php 
                                        $selected = "";
                                        if(count($projects) > 0){
                                            foreach ($projects as $proj) {

                                                    if( isset($_POST['project_id']) && $proj->project_id == $_POST['project_id'] ){
                                                        $selected = 'selected="selected"';
                                                    }
                                            ?>
                                            <option <?=$selected?> value="<?php echo $proj->project_id; ?>"><?php echo $proj->project_name; ?></option>
                                            <?php } 
                                        }
                                        ?>
                                    </select>
                                    <span class="error"><?php echo (form_error('project_id')) ? form_error('project_id') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category_id" class="col-sm-3 control-label">Material Category <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <select name="category_id" class="form-control category_id" required>
                                        <option value="">--Select Category--</option>
                                        <?php 
                                            foreach( $Categories as $Category ){ 
                                                $selected = '';
                                                if( isset($_POST['category_id']) && $Category->id == $_POST['category_id'] ){
                                                    $selected = 'selected="selected"';
                                                }
                                                echo '<option value="'.$Category->id.'" '.$selected.' >'.$Category->category.'</option>';
                                            }
                                        ?>
                                    </select>
                                    <span class="error"><?php echo (form_error('category_id')) ? form_error('category_id') : ''; ?></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Material Name <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <input name="material_name" placeholder="Material Name" class="form-control" type="text" value="<?php echo (isset($_POST['material_name'])) ? $_POST['material_name'] : ''; ?>" >
                                    <span class="error"><?php echo (form_error('material_name')) ? form_error('material_name') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Unit of Measurement <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <input name="unit_measurement" placeholder="Unit of Measurement" class="form-control" type="text" value="<?php echo (isset($_POST['unit_measurement'])) ? $_POST['unit_measurement'] : ''; ?>" required="required">
                                    <span class="error"><?php echo (form_error('unit_measurement')) ? form_error('unit_measurement') : ''; ?></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">HSN Code </label>
                                <div class="col-sm-9">
                                    <input name="hsn_code" placeholder="HSN Code" class="form-control" type="text" value="<?php echo (isset($_POST['hsn_code'])) ? $_POST['hsn_code'] : ''; ?>">
                                    <span class="error"><?php echo (form_error('hsn_code')) ? form_error('hsn_code') : ''; ?></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Bound Range <font color="red">*</font></label>
                                <div class="col-sm-3">
                                    <input name="bound_start_range" placeholder="" class="form-control bound_start_range" type="number" min="1" value="<?php echo (isset($_POST['bound_start_range'])) ? $_POST['bound_start_range'] : ''; ?>" required="required">
                                    
                                </div>
                                <label for="title" class="col-sm-1 control-label">To</label>
                                <div class="col-sm-3">
                                    <input name="bound_end_range" placeholder="" class="form-control bound_end_range" type="number" min="1" value="<?php echo (isset($_POST['bound_end_range'])) ? $_POST['bound_start_range'] : ''; ?>" required="required">
                                </div>
                                <div class="col-sm-12 col-sm-offset-3">
                                    <span class="error bond_range_Error"><?php echo (form_error('bound_start_range') || form_error('bound_end_range')) ? form_error('bound_end_range') : ''; ?></span>
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
