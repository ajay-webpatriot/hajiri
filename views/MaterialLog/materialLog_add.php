<style type="text/css">
    .unit,.amountLabel{
        vertical-align: -webkit-baseline-middle !important;
    }
    .amountLabel{
        display: block;
        padding: 6px 12px;
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
        <li><a href="<?php echo base_url('admin/materialLog'); ?>">Material Log</a></li>
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
                    <form action="" id="add-labour" class="form-horizontal" method="POST" enctype="multipart/form-data" autocomplete="off">
                        <div class="box-body">
                            <h4 class="box-title">General Details :</h4>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Challan Date <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <input name="challan_date" id="date" placeholder="Challan Date" class="form-control datepicker-material" type="text" value="<?php echo (isset($_POST['date'])) ? $_POST['date'] : date('Y-m-d'); ?>" required>
                                    <span class="error"><?php echo (form_error('challan_date')) ? form_error('challan_date') : ''; ?></span>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Challan No <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <input name="challan_no" placeholder="Challan No" class="form-control" type="text" value="<?php echo (isset($_POST['challan_no'])) ? $_POST['challan_no'] : ''; ?>" >
                                    <span class="error"><?php echo (form_error('challan_no')) ? form_error('challan_no') : ''; ?></span>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Challan Image <font color="red"> </font></label>
                                <div class="col-sm-9">
                                    <input type="file" accept="image/*" id="challan_file" name="challan_file"  />
                                    <span class="error"><?php echo (form_error('challan_image')) ? form_error('challan_image') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="project_name" class="col-sm-3 control-label">Project Name <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <select class="form-control project_name" name="project_name" required>
                                        <option value="">Project Name </option>
                                        <?php 
                                        foreach ($projects as $proj) {
                                            ?>
                                            <option value="<?php echo $proj->project_id; ?>"><?php echo $proj->project_name; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="error"><?php echo form_error('project_name') ?></span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="supplier_name" class="col-sm-3 control-label">Supplier Name <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <select class="form-control supplier_name" name="supplier_name" required>
                                        <option value="">Supplier Name </option>
                                        <?php /*
                                        foreach ($supplier as $supp) {
                                            ?>
                                            <option value="<?php echo $supp->id; ?>"><?php echo $supp->name; ?></option>
                                        <?php } */ ?>
                                    </select>
                                    <span class="error"><?php echo form_error('supplier_name') ?></span>
                                </div>
                            </div>
                            <?php
                            if($this->session->userdata('user_designation') != "Supervisor")
                            { ?>  
        
                                <div class="form-group">
                                    <label for="supervisor_name" class="col-sm-3 control-label">Supervisor Name <font color="red">*</font></label>
                                    <div class="col-sm-9">
                                        <select class="form-control supervisor_name" name="supervisor_name" required>
                                            <option value="">Supervisor Name </option>
                                           
                                        </select>
                                        <span class="error"><?php echo form_error('supervisor_name') ?></span>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>    
                            <h4 class="box-title">Add Material Details :</h4>
                            <div class="addMaterialDetail">
                                <hr>
                                <div class='remove_button_div pull-right' style="padding-bottom: 5px;display: none;">
                                    <button class='fa fa-times' type='button' id='remove'
                                        onclick="removeEntryClone(this);" title="Remove material details" >
                                        <i class="icon-minus-2 "></i>
                                    </button>
                                </div>
                                <div class="form-group">
                                    <label for="material_category" class="col-sm-3 control-label">Material Category <font color="red">*</font></label>
                                    <div class="col-sm-9">
                                        <select class="form-control material_category" name="material_category[]" required>
                                            <option value="">Material Category</option>
                                            <?php /*
                                            foreach ($material_category as $proj) {
                                                ?>
                                                <option value="<?php echo $proj->id; ?>"><?php echo $proj->category; ?></option>
                                            <?php } */?>
                                        </select>
                                        <span class="error"><?php echo form_error('material_category') ?></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="material_NAME" class="col-sm-3 control-label">Material Name <font color="red">*</font></label>
                                    <div class="col-sm-9">
                                        <select class="form-control material_name" name="material_name[]" required>
                                            <option value="">Material Name</option>
                                            
                                        </select>
                                        <span class="error material-error"><?php echo form_error('material_name') ?></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="quantity" class="col-sm-3 control-label">Quantity <font color="red">*</font></label>
                                    <div class="col-sm-6">
                                        <input name="quantity[]" min="1" placeholder="Quantity" class="form-control quantity" type="number" value="<?php echo (isset($_POST['quantity'][0])) ? $_POST['quantity'][0] : ''; ?>" required>
                                        <span class="error"><?php echo (form_error('quantity')) ? form_error('quantity') : ''; ?></span>
                                    </div>
                                    <div class="col-sm-3">
                                        <span><b class="unit"></b></span>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label for="title" class="col-sm-3 control-label">Material Image </label>
                                    <div class="col-sm-9">
                                        <input type="file" id="material_file" accept="image/*" name="material_file[]"  />
                                        <span class="error"><?php echo (form_error('material_file')) ? form_error('material_file') : ''; ?></span>
                                    </div>
                                </div>  

                            </div>    
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <input type="button" id="add_more" class="btn btn-success" value="Add more">
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

    $(document).ready(function () {
       $("#add_more").click(function(){

        var $clone = $('.addMaterialDetail:last').clone();
        $clone.find('input').val('');
        $clone.find('select').val('');
        // $clone.find('.amountLabel').html('');
        $clone.find('.material-error').html('');
        $clone.find('img').remove();
        $clone.find('.remove_button_div').show();
        
        $clone.insertAfter($('[class^="addMaterialDetail"]').last());
    });
        // load material name using ajax
        $(document).on("change",".material_category",function(){ 

            
            var project_id = $('.project_name').val();
            var optionHTML="<option value=''>Material Name</option>";
            var category_id = $(this).val();
            var ele=this;
            $(ele).parents(".form-group").next().find('.material-error').html('');
            if(category_id && project_id !== '') {   
                $.ajax({
                    url: "<?php echo base_url().'admin/MaterialLog/getmaterialAjax/'?>?category_id="+category_id+"&project_id="+project_id,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {
                        if(data.status == true){
                            $.each(data.material, function(key, value) {
                                optionHTML+='<option  data-unit="'+value.unit_measurement+'"  value="'+ value.id +'">'+ value.name +'</option>';
                            });
                        }else{
                            $(ele).parents(".form-group").next().find('.material-error').html("Material not found in selected category");
                        }
                        $(ele).parents(".form-group").next().find("select").html(optionHTML);
                    }
                });
            }else{
                $(ele).parents(".form-group").next().find("select").html(optionHTML);
            }
        }); 
        
        $(document).on("change",".material_name",function(){
            var unit_measurement = $(this).find('option:selected', this).attr('data-unit');
            $(this).parents(".form-group").next().find(".unit").html(unit_measurement);
        });

        // load supervisor name using ajax
        $(document).on("change",".project_name",function(){

            var optionHTML="<option value=''>Supervisor Name</option>";
            var projectSupplierOption ="<option value=''>Supplier Name</option>";
            $('.material_category').html("<option value=''>Material Category</option>");
            $('.material_name').html("<option value=''>Material Name</option>");

            var project_id = $(this).val();
            var ele=this;
            if(project_id) {   
                $.ajax({
                    url: "<?php echo base_url().'admin/MaterialLog/getSupervisorAjax/'?>"+project_id,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {
                        // $('select[name="city"]').empty();
                        $.each(data.getProjectSupervisor, function(key, value) {
                            optionHTML+='<option  value="'+ value.user_id +'">'+ value.supervisor_name +'</option>';
                        });

                        $.each(data.getProjectSupplier, function(key, value) {
                            projectSupplierOption+='<option  value="'+ value.id +'">'+ value.name +'</option>';
                        });

                        $('.supervisor_name').html(optionHTML);
                        $('.supplier_name').html(projectSupplierOption);
                    }
                });
            }else{
                $('.supplier_name').html(projectSupplierOption);
                $('.supervisor_name').html(optionHTML);
            }
        }); 
        $(document).on("change",".supplier_name",function(){
            var CategoryOption ="<option value=''>Material Category</option>";
            $('.material_name').html("<option value=''>Material Name</option>");
            var supplier_id = $(this).val();
            var project_id = $(".project_name").val();
            var projectCategoryOption ="<option value=''>Material Category</option>";
            var ele=this;
            if(supplier_id) { 
                $.ajax({
                    url: "<?php echo base_url().'admin/MaterialLog/getSupplierCategoryAjax/'?>"+supplier_id+"/"+project_id,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {
                        $.each(data.getProjectCategory, function(key, value) {
                            projectCategoryOption+='<option  value="'+ value.id +'">'+ value.category +'</option>';
                        });
                        $('.material_category').html(projectCategoryOption);
                    }
                });
            }else{
                $('.material_category').html(projectCategoryOption);
            }
        });
    });
    // Calculate total amount
    function rate_change_fun(ele)
    {
        var quantity=$(ele).parents(".form-group").prev().find(".quantity").val();
        var rate=$(ele).val();
        var totalRate=quantity*rate;
        
        $(ele).parents(".form-group").next().find(".amountLabel").html(totalRate);
        $(ele).parents(".form-group").next().find(".amount").val(totalRate);
    }
    function quantity_change_fun(ele)
    {
        var quantity=$(ele).val();
        var rate=$(ele).parents(".form-group").next().find(".rate").val();
        var totalRate=quantity*rate;
        
        $(ele).parents(".form-group").next().next().find(".amountLabel").html(totalRate);
        $(ele).parents(".form-group").next().next().find(".amount").val(totalRate);
    }
</script>