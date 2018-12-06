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
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="box-title" style="padding-left: 10px;">General Details :</h4>
                        </div>
                    </div>
                    <form action="<?php echo base_url('admin/MaterialIssue/addIssueLog');?>" id="addIssueLog" class="form-horizontal validateDontSubmit" method="POST" enctype="multipart/form-data" autocomplete="off">
                        <div class="box-body">
                           <!-- Issue Date start -->
                            <div class="form-group">
                                <label for="date" class="col-sm-3 control-label">Issue Date <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <input name="issueDate" id="date" placeholder="Issue Date" class="form-control datepicker-material" type="text" value="<?php echo (isset($_POST['issueDate'])) ? $_POST['issueDate'] : ''; ?>" required>
                                    <span class="error"><?php echo (form_error('issueDate')) ? form_error('issueDate') : ''; ?></span>
                                </div>
                            </div>
                            <!-- Issue Date end -->
                            <div class="form-group">
                                <label for="date" class="col-sm-3 control-label">Issue No <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <input name="issueNo" id="issueNo" placeholder="Issue No" class="form-control" type="text" value="<?php echo (isset($_POST['issueNo'])) ? $_POST['issueNo'] : ''; ?>" required>
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
                                            ?>
                                          <option value="<?php echo $value->project_id; ?>"><?php echo $value->project_name; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="error"><?php echo form_error('project_name') ?></span>
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

                                                if( isset($_POST['materialCategory']) && $Category->id == $_POST['materialCategory'] ){
                                                    $selected = 'selected="selected"';
                                                }
                                                ?>
                                                <option value="<?php echo $Category->id; ?>"><?php echo $Category->category; ?></option>
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
                                        <option value="">Material Name </option>
                                        <?php 
                                        // if($materialNames != ''){
                                        // foreach ($materialNames as $singlematerialName) {
                                            ?>
                                            <!-- <option value="<?php //echo $singlematerialName->id; ?>"><?php //echo $singlematerialName->name; ?></option> -->
                                        <?php
                                            //} 
                                        //} ?>
                                    </select>
                                    <span class="error empty_material_error"><?php echo form_error('MaterialName') ?></span>
                                </div>
                            </div>
                            <span class="totalQuantity col-md-12 col-md-offset-3" id="" ></span>

                            <div class="form-group">
                                <label for="IssueQuantity" class="col-sm-3 control-label">Quantity <font color="red">*</font></label>
                                <div class="col-sm-6">
                                    <input name="IssueQuantity" placeholder="Quantity" class="form-control issueQuantity" type="number" min="1" value="<?php echo (isset($_POST['IssueQuantity'])) ? $_POST['IssueQuantity'] : ''; ?>" required>
                                    <span class="error QuantityError"><?php echo (form_error('IssueQuantity')) ? form_error('IssueQuantity') : ''; ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <span><b class="unit"></b></span>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label for="Issuefile" class="col-sm-3 control-label">Material Image </label>
                                <div class="col-sm-9">
                                    <input type="file" id="issuefile" accept="image/*" name="Issuefile"  />
                                    <span class="error"><?php echo (form_error('material_file')) ? form_error('material_file') : ''; ?></span>
                                </div>
                            </div>  
                            
                            <div class="form-group">
                                <label for="ConsumtionPlace" class="col-sm-3 control-label">consumption Place </label>
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="radio" name="sites" value="Insite" checked>
                                            <label for="WithinSite" class="control-label">Within Site</label>
                                        </div>

                                        <div class="col-md-8">
                                            <input type="radio" id="OutsideButton" name="sites" value="outsite">
                                            <label for="OutsideSite" class="control-label">Outside Site</label>

                                            
                                        </div>
                                        <div class="col-md-12" id="OutsideSiteMenuHide" style="display:none;">
                                            <select class="form-control" name="Projects">
                                                <?php
                                                if($ActiveProjects  != ''){
                                                    foreach($ActiveProjects as $SingleActiveProject){
                                                ?>
                                                <option value="<?php echo $SingleActiveProject->project_id; ?>"><?php echo $SingleActiveProject->project_name; ?></option>
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
                                <label for="quantity" class="col-sm-3 control-label">Issue Comment </label>
                                <div class="col-sm-6">
                                   <textarea name="issueComment" id="" cols="30" rows="5"></textarea>
                                </div>
                            </div> 
                            <?php
                            if($this->session->userdata('user_designation') == 'Superadmin' || $this->session->userdata('user_designation') == 'admin'){
                            ?> 
                            <div class="form-group">
                                <label for="quantity" class="col-sm-3 control-label">Verify Comment:</label>
                                <div class="col-sm-6">
                                   <textarea name="verifyComment" id="" cols="30" rows="5"></textarea>
                                </div>
                            </div> 
                            <?php
                            }?>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <input type="submit" id="" name="submit" class="btn btn-primary" value="Save">
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

    $("input[type='radio']").change(function(){
        if($(this).val()=="outsite")
        {
            $("#OutsideSiteMenuHide").removeAttr('style');
        }
        else
        {
            $("#OutsideSiteMenuHide").attr("style", "display: none;"); 
        }
    });
    $(document).on("change",".materialName",function(){
        
        $('.QuantityError').html('');
        var material_id = $(".materialName").val();
        var project_id = $(".project_name").val();
        $('.totalQuantity').attr('id', 0);
        
        if(material_id != '' && project_id != ''){
            $.ajax({
                type:"GET",
                url: "<?php echo base_url('admin/MaterialIssue/getMaterialIssueQuantity/'); ?>?material_id="+material_id+"&project_id="+project_id,
                dataType: "json",
                success: function(data) {
                    if(data.status == true){
                        // var id = data;
                        $('.totalQuantity').removeClass('totalQuantityClassError');
                        $('.totalQuantity').addClass('totalQuantityClass');
                        $('.totalQuantity').html('Available quantity '+data.quantity+' bags');
                        $('.totalQuantity').attr('id', data.quantity);
                    }else{
                         // var id = data;
                        $('.totalQuantity').removeClass('totalQuantityClass');
                        $('.totalQuantity').addClass('totalQuantityClassError');
                        $('.totalQuantity').html('No issue quantity available');
                    }
                }
            });
        }
            var unit_measurement = $(this).find('option:selected', this).attr('data-unit');
            $(".unit").html(unit_measurement);
    });
         
        $(document).on('submit','.validateDontSubmit',function (){
            // quantity validation
            var totalQuantity = $('.totalQuantity').attr('id');
            var issueQuantity = $('.issueQuantity').val();
            
            var status = false;
            if(Number(totalQuantity) >= Number(issueQuantity)){
                status =  true;
            }else{
                $('.QuantityError').show();

                if(totalQuantity == 0){
                    $('.QuantityError').html('Quantity not available in selected category and project.');
                }else{
                    $('.QuantityError').html('Quantity not available more than '+totalQuantity);
                }
                status =  false;
            }
            $('.totalQuantity').attr('id', 0);
            return status;
        });

        $(document).on("change",".project_name",function(){
            var project_id = $('.project_name').val();
            var material_category = $('.material_category').val();
            $('.empty_material_error').html("");
            getMaterial(project_id, material_category);
        });

        $(document).on("change",".material_category",function(){
            var project_id = $('.project_name').val();
            var material_category = $('.material_category').val();
            $('.empty_material_error').html("");
            getMaterial(project_id, material_category);
        });
        
        // get material by project and category
        function getMaterial(project_id, material_category){

            var projectMaterialOption ="<option value=''>Material Name</option>";
            $('.totalQuantity').html('');

            if(project_id && material_category) {   
                $.ajax({
                    url: "<?php echo base_url().'admin/MaterialIssue/getProjectMaterialAjax/'?>?project_id="+project_id+'&category_id='+material_category,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {
                        if(data.status == true){
                            $.each(data.material, function(key, value) {
                                projectMaterialOption+='<option  value="'+ value.id +'">'+ value.name +'</option>';
                            });
                        }else{
                           $('.empty_material_error').html("Material not found in selected category and project.");
                        }
                        $('.materialName').html(projectMaterialOption);
                    }
                });
            }
            else{
                $('.materialName').html(projectMaterialOption);
            }
        }

</script>