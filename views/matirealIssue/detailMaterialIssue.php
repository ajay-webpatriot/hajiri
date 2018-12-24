<style type="text/css">
    .totalQuantityClass{
        padding-bottom: 10px;
        color: green;
    }
    .totalQuantityClassError{
        padding-bottom: 10px;
        color: red;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo (!empty($title) ? ucwords($title) : ''); ?>
        </h1>
    </section>
    <ol class="breadcrumb margin-bottom0">
        <li><a href="<?php echo base_url('admin'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="<?php echo base_url('admin/MaterialIssue'); ?>">Material Issue</a></li>
        <li class="active"><?php echo (isset($title) ? $title : ''); ?></li>
    </ol>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8">
                <!-- Horizontal Form -->
                <div class="box box-info">

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
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="box-title" style="padding-left: 10px;">General Details :</h4>
                        </div>
                    </div>
                    
                    <form action="" id="addIssueLog" class="form-horizontal validateDontSubmit" method="POST" enctype="multipart/form-data" autocomplete="off">
                        <div class="box-body">

                            <?php
                                $date = '';
                                if(isset($result->date) && !empty($result->date)){
                                    $date = date("m/d/Y", strtotime($result->date));
                                }
                            ?>
                            
                            <div class="form-group">
                                <label for="date" class="col-sm-3 control-label">Issue Date <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <input name="issueDate" id="date" placeholder="Issue Date" class="form-control datepicker-material" type="text" value="<?php echo (isset($date)) ? $date : ''; ?>" required>
                                    <span class="error"><?php echo (form_error('issueDate')) ? form_error('issueDate') : ''; ?></span>
                                </div>
                            </div>
                            <!-- Issue Date end -->
                            <div class="form-group">
                                <label for="date" class="col-sm-3 control-label">Issue No <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <input name="issueNo" id="issueNo" placeholder="Issue No" class="form-control" type="text" value="<?php echo (isset($result->issue_no)) ? $result->issue_no : ''; ?>" required>
                                    <span class="error"><?php echo (form_error('issueNo')) ? form_error('issueNo') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="project_name" class="col-sm-3 control-label">Project Name <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <select class="form-control project_name" name="project_name" required>
                                        <option value="">Project Name </option>
                                        <?php 
                                        foreach($ActiveProjects as $value){
                                            $selected = '';
                                                if( isset($result->project_id) && $value->project_id == $result->project_id ){
                                                    $selected = 'selected="selected"';
                                                }
                                            ?>
                                          <option <?=$selected?> value="<?php echo $value->project_id; ?>"><?php echo $value->project_name; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="error"><?php echo form_error('project_name') ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="supervisor_name" class="col-sm-3 control-label">Supervisor Name <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <select class="form-control supervisor_name" name="supervisor_name" required>
                                        <option value="">Supervisor Name </option>
                                        <?php
                                           

                                            foreach ($supervisors as $supervisor) {
                                                $selected = '';
                                                
                                                if( isset( $result->supervisor_id ) && $supervisor->user_id == $result->supervisor_id ){
                                                    $selected = 'selected="selected"';
                                                }
                                                echo '<option '.$selected.' value="'.$supervisor->user_id.'" '.$selected.' >'.$supervisor->supervisor_name.'</option>';
                                            }
                                           ?>
                                    </select>
                                    <span class="error"><?php echo form_error('supervisor_name') ?></span>
                                </div>
                            </div>
                               
                            <div class="form-group">
                                    <label for="materialCategory" class="col-sm-3 control-label">Material Category <font color="red">*</font></label>
                                    <div class="col-sm-9">
                                        <select class="form-control material_category" id="MaterialCategory" name="materialCategory" required>
                                            <option value="">Material Category</option>
                                            <?php 
                                            if($materialCategory != ''){

                                            foreach ($materialCategory as $Category) {
                                                $selected = '';
                                                if( isset($result->category_id) && $Category->id == $result->category_id ){
                                                    $selected = 'selected="selected"';
                                                }
                                                ?>
                                                <option <?=$selected?> value="<?php echo $Category->id; ?>"><?php echo $Category->category; ?></option>
                                            <?php 
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="error"><?php echo form_error('materialCategory') ?></span>
                                    </div>
                                </div>
                            <div class="form-group selectclass">
                                <label for="MaterialName" class="col-sm-3 control-label">Material Name <font color="red">*</font></label>
                                <div class="col-sm-9" id="MaterialNames">
                                    <select class="form-control materialName" name="MaterialName" required>
                                        <option value="">Material Name</option>
                                        <?php 
                                        $materials = array();
                                        $materials = $this->MaterialLog_model->getMaterialByCategory($result->category_id, $result->project_id,$this->session->userdata('company_id'));

                                        foreach ($materials as $key => $mat){
                                            $selected = '';
                                            $selected_unit_measurement = '';
                                            if( isset( $result->material_id ) && $mat->id == $result->material_id ){
                                                $selected = 'selected="selected"';
                                                $selected_unit_measurement = $mat->unit_measurement;
                                            }
                                            echo '<option data-unit="'.$mat->unit_measurement.'" value="'.$mat->id.'" '.$selected.' >'.$mat->name.'</option>';
                                        }
                                        ?>
                                    </select>
                                    <span class="error empty_material_error"><?php echo form_error('MaterialName') ?></span>
                                </div>
                            </div>

                            <span class="totalQuantity col-md-12 col-md-offset-3" id="<?php echo $totalQuantity; ?>" ></span>
                               
                            <div class="form-group">
                                <label for="IssueQuantity" class="col-sm-3 control-label">Quantity <font color="red">*</font></label>
                                <div class="col-sm-6">
                                    <input name="IssueQuantity" placeholder="Quantity" class="form-control issueQuantity" type="number" min="1" value="<?php echo (isset($result->quantity)) ? $result->quantity : ''; ?>" required>
                                    <span class="error QuantityError"><?php echo (form_error('IssueQuantity')) ? form_error('IssueQuantity') : ''; ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <span><b class="unit"></b></span>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label for="Issuefile" class="col-sm-3 control-label">Material Image:</label>
                                <div class="col-sm-9">
                                    <input type="file" id="issuefile" accept="image/*" name="Issuefile"  />
                                    <span class="error"><?php echo (form_error('material_file')) ? form_error('material_file') : ''; ?></span>
                                    <br/>

                                    <?php
                                        if($result->material_image != "")
                                        {
                                            ?>
                                            <img onclick="openImageModel(this)" src="<?=base_url('uploads/')?>MaterialIssue/<?=$result->material_image?>" width="100px" class="image"/>
                                            <?php
                                        }?>
                                </div>
                            </div>  
                            
                            <div class="form-group">
                                <label for="ConsumtionPlace" class="col-sm-3 control-label">Consumtion Place:</label>
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="radio" name="sites" <?php echo ($result->consumption_place=='Insite')?'checked':'' ?> value="Insite" checked>
                                            <label for="WithinSite" class="control-label">Within Site</label>
                                        </div>

                                        <div class="col-md-8">
                                            <input type="radio" id="OutsideButton" <?php echo ($result->consumption_place=='outsite')?'checked':'' ?> name="sites" value="outsite">
                                            <label for="OutsideSite" class="control-label">Outside Site</label>
                                            <?php

                                            if($result->consumption_place=='outsite')
                                            {
                                                $style="display:block";
                                            }
                                            else
                                            {
                                                $style="display:none";
                                            }
                                            ?>
                                            
                                        </div>
                                        <div class="col-md-12" id="OutsideSiteMenuHide" style="<?=$style?>">
                                            <select class="form-control" name="Projects">
                                                <?php
                                                if($ActiveProjects  != ''){
                                                    foreach($ActiveProjects as $SingleActiveProject){

                                                       if( isset( $result->consumption_outsite_project_id ) && $SingleActiveProject->project_id == $result->consumption_outsite_project_id ){
                                                            $selected = 'selected="selected"';
                                                        }  
                                                ?>
                                                <option <?=$selected?> value="<?php echo $SingleActiveProject->project_id; ?>"><?php echo $SingleActiveProject->project_name; ?></option>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label for="quantity" class="col-sm-3 control-label">Issue Comment:</label>
                                <?php if($this->session->userdata('user_designation') == 'admin'){
                                ?> 
                                <div class="col-sm-6">
                                   <textarea name="issueComment" id="" cols="30" rows="5" disabled="disabled"><?=$result->issue_comment?></textarea>
                                </div>
                            <?php } else{ ?>
                                    <div class="col-sm-6">
                                   <textarea name="issueComment" id="" cols="30" rows="5"><?=$result->issue_comment?></textarea>
                                </div>
                            <?php } ?>
                            </div> 
                            <div class="form-group">
                                <label for="quantity" class="col-sm-3 control-label">Verify Comment:</label>
                                <div class="col-sm-6">
                                   <textarea name="verifyComment" id="" cols="30" rows="5"><?=$result->verify_comment?></textarea>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            
                            <a href="<?php echo base_url('admin/MaterialIssue'); ?>" id="btnClose" name="close" class="btn btn-primary" >Close</a>
                           
                        </div>
                        <!-- /.box-footer -->
                    </form>
                    <input type="hidden" class="project_name" value="<?php echo $result->project_id; ?>">
                </div>
                <!-- /.box -->
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<!-- The image Modal -->
<div id="imageModal" class="imageModal">
  <span class="closeImageModel">&times;</span>
  <img class="image-modal-content" id="modelImg01">
  <div id="imageCaption"></div>
</div>
<script type="text/javascript">

    $(document).ready(function () {
        
            $('input[type=text]').attr("disabled",true);
            $('input[type=number]').attr("disabled",true);
            $('textarea').attr("disabled",true);
            $('select').attr("disabled",true);
            $('input[type=radio]').attr("disabled",true);
            
            $("#btnClose").show();

            $('input[type=file]').hide();
        
    });
    
</script>