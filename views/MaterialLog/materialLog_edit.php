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
                    <form action="" id="add-labour" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <div class="box-body">
                            <h4 class="box-title">General Details :</h4>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Challan Date:</label>
                                <div class="col-sm-9">
                                    <input name="challan_date" id="date" placeholder="Challan Date" class="form-control datepicker" type="text" value="<?php echo (isset($result->challan_date)) ? $result->challan_date : ''; ?>" required>
                                    <span class="error"><?php echo (form_error('challan_date')) ? form_error('challan_date') : ''; ?></span>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Challan No:</label>
                                <div class="col-sm-9">
                                    <input name="challan_no" placeholder="Challan No" class="form-control" type="text"value="<?php echo (isset($result->challan_no)) ? $result->challan_no : ''; ?>" required>
                                    <span class="error"><?php echo (form_error('challan_date')) ? form_error('challan_date') : ''; ?></span>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Challan Image:</label>
                                <div class="col-sm-9">
                                    <input type="file" accept="image/*" id="challan_file" name="challan_file"  />
                                    <span class="error"><?php echo (form_error('challan_image')) ? form_error('challan_image') : ''; ?></span>
                                    <br/>
                                    <?php
                                    if($result->challan_image != "")
                                    {
                                        $image =  ROOT_PATH.'/uploads/materialLog/challan/'.$result->challan_image;
                                        if(file_exists($image)){
                                            ?>
                                            <img src="<?=base_url('uploads/')?>materialLog/challan/<?=$result->challan_image?>" width="100px" class="image"/>
                                            <?php
                                        }
                                    }

                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="project_name" class="col-sm-3 control-label">Project Name:</label>
                                <div class="col-sm-9">
                                    <select class="form-control project_name" name="project_name" required>
                                        <option value="">Project Name </option>
                                        <?php 
                                        foreach ($projects as $proj) {
                                            $selected = "";
                                                if( isset( $result->project_id ) && $proj->project_id == $result->project_id ){
                                                    $selected = 'selected="selected"';
                                                }
                                            ?>
                                            <option <?=$selected?> value="<?php echo $proj->project_id; ?>"><?php echo $proj->project_name; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="error"><?php echo form_error('project_name') ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="supplier_name" class="col-sm-3 control-label">Supplier Name:</label>
                                <div class="col-sm-9">
                                    <select class="form-control supplier_name" name="supplier_name" required>
                                        <option value="">Supplier Name </option>
                                        <?php 
                                        foreach ($supplier as $supp) {
                                            $selected = '';
                                            if( isset( $result->supplier_id ) && $supp->id == $result->supplier_id ){
                                                $selected = 'selected="selected"';
                                            }
                                            ?>
                                            <option <?=$selected?> value="<?php echo $supp->id; ?>"><?php echo $supp->name; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="error"><?php echo form_error('supplier_name') ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="supervisor_name" class="col-sm-3 control-label">Supervisor Name:</label>
                                <div class="col-sm-9">
                                    <select class="form-control supervisor_name" name="supervisor_name"required>
                                        <option value="">Supervisor Name </option>
                                       <?php
                                       $supervisors = $this->MaterialLog_model->getProjectSupervisor($result->project_id);

                                        foreach ($supervisors as $supervisor) {
                                            $selected = '';
                                            
                                            if( isset( $result->receiver_id ) && $supervisor->user_id == $result->receiver_id ){
                                                $selected = 'selected="selected"';
                                            }
                                            echo '<option value="'.$supervisor->user_id.'" '.$selected.' >'.$supervisor->supervisor_name.'</option>';
                                        }
                                       ?>
                                    </select>
                                    <span class="error"><?php echo form_error('supervisor_name') ?></span>
                                </div>
                            </div>
                            <h4 class="box-title">Add Material Details :</h4>
                            <?php
                            foreach ($result_detail as $key => $value) {
                                # code...
                                ?>
                                <div class="addMaterialDetail">
                                    <hr>
                                    <div class="form-group">
                                        <label for="material_category" class="col-sm-3 control-label">Material Category:</label>
                                        <div class="col-sm-9">
                                            <select class="form-control material_category" name="material_category[]" required>
                                                <option value="">Material Category</option>
                                                <?php 
                                            /*echo "<pre>";
                                            print_r($result);
                                            exit();*/
                                            foreach ($material_category as $proj) {
                                              $selected = '';
                                              if( isset( $value->category_id ) && $proj['id'] == $value->category_id ){
                                                $selected = 'selected="selected"';
                                            }
                                            echo '<option value="'.$proj['id'].'" '.$selected.' >'.$proj['category'].'</option>';
                                        } ?>
                                    </select>
                                    <span class="error"><?php echo form_error('material_category') ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="material_NAME" class="col-sm-3 control-label">Material Name:</label>
                                <div class="col-sm-9">
                                    <select class="form-control material_name" name="material_name[]" required>

                                        <option value="">Material Name</option>
                                        <?php
                                        $materials = $this->MaterialLog_model->getMaterialByCategory($value->category_id);

                                        foreach ($materials as $key => $mat) {
                                            $selected = '';
                                            $selected_unit_measurement = '';
                                            if( isset( $value->material_id ) && $mat->id == $value->material_id ){
                                                $selected = 'selected="selected"';
                                                $selected_unit_measurement = $mat->unit_measurement;
                                            }
                                            echo '<option data-unit="'.$mat->unit_measurement.'" value="'.$mat->id.'" '.$selected.' >'.$mat->name.'</option>';
                                        }
                                        ?>


                                    </select>
                                    <span class="error"><?php echo form_error('material_name') ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="quantity" class="col-sm-3 control-label">Quantity:</label>
                                <div class="col-sm-6">
                                    <input name="quantity[]" min="1" placeholder="Quantity" onkeyup="quantity_change_fun(this)" onchange="quantity_change_fun(this)" class="form-control quantity" type="number" value="<?php echo (isset($value->quantity)) ? $value->quantity : ''; ?>" required>
                                    <span class="error"><?php echo (form_error('quantity')) ? form_error('quantity') : ''; ?></span>
                                </div>
                                <div class="col-sm-2">
                                    <span><b class="unit"><?=$selected_unit_measurement?></b></span>
                                </div>
                            </div>
                            <?php
                        if($this->session->userdata('user_designation') == 'Superadmin' || $this->session->userdata('user_designation') == 'admin'){
                        ?> 
                            <div class="form-group">
                                <label for="rate" class="col-sm-3 control-label">Rate:</label>
                                <div class="col-sm-9">
                                    <input name="rate[]" min="1" onkeyup="rate_change_fun(this)" onchange="rate_change_fun(this)" placeholder="Rate" class="form-control rate" type="number" value="<?php echo (isset($value->rate)) ? $value->rate : ''; ?>" required>
                                   
                                </div>
                             </div>
                                <div class="form-group">
                                    <label for="amount" class="col-sm-3 control-label">Amount:</label>
                                    <div class="col-sm-9">
                                        <span class="amountLabel"><?php echo (isset($value->total_rate)) ? $value->total_rate : ''; ?></span>
                                        <input name="amount[]" placeholder="Amount" class="form-control amount" type="hidden" value="<?php echo (isset($value->total_rate)) ? $value->total_rate : ''; ?>" required>
                                    </div>
                                </div> 
                            <?php
                            }?>    
                                <div class="form-group">
                                    <label for="title" class="col-sm-3 control-label">Material Image:</label>
                                    <div class="col-sm-9">
                                        <input type="file" id="material_file" accept="image/*" name="material_file[]"  />
                                        <span class="error"><?php echo (form_error('material_file')) ? form_error('material_file') : ''; ?></span>
                                        <br/>
                                        <?php
                                        if($value->material_image != "")
                                        {   
                                            $material_image =  ROOT_PATH.'/uploads/materialLog/material_image/'.$value->material_image;
                                            if(file_exists($material_image)){ ?>
                                                <img src="<?=base_url('uploads/')?>materialLog/material_image/<?=$value->material_image?>" width="100px" class="image"/>
                                                <?php
                                            }
                                        }?>
                                    </div>
                                </div>  
                    </div>
                    <?php } ?>
                    <!-- comment -->
                    <div class="form-group">
                        <label for="title" class="col-sm-3 control-label">Comment:</label>
                        <div class="col-sm-9">
                           <textarea rows="4" cols="50" name="comment"><?=$result->comment?></textarea>
                        </div>
                    </div> 
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <input type="button" id="add_more" class="btn btn-success" value="Add more">
                        <?php
                        if(!$this->session->userdata('user_designation') != 'Superadmin' && $this->session->userdata('user_designation') != 'admin')
                        {
                        ?>
                        <input type="submit" id="btnSave" name="submit" class="btn btn-primary" value="Save">
                        <?php
                        }
                        ?>
                        <?php
                        if( $this->session->userdata('user_designation') != 'Superadmin' || $this->session->userdata('user_designation') != 'admin'){
                        ?>
                        <input type="submit" id="btnverify" name="verify" class="btn btn-primary" value="Verify & Submit">
                        <?php
                        }
                        ?>
                        <a href="<?php echo base_url('admin/materialLog'); ?>" id="btnClose" name="close" class="btn btn-primary" >Close</a>

                        <a id="btndelete" name="delete" href="<?php echo base_url('admin/materialLog/ajax_delete/'.$result->id); ?>" class="btn btn-danger" >Delete</a>
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
         
        // projectByOption($('.project_name').val());

        var status="<?=$result->status?>";

       if(status == "Approved")
       {
            $('input[type=text]').attr("disabled",true);
            $('input[type=number]').attr("disabled",true);
            $('textarea').attr("disabled",true);
            $('select').attr("disabled",true);
            $("#add_more").hide();
            $("#btnSave").hide();
            $("#btnverify").hide();
            $("#btnClose").show();
            

            $('input[type=file]').hide();
        
       }
       else
       {
            $("#btnClose").hide();
            
       } 
       $("#add_more").click(function(){

        var $clone = $('.addMaterialDetail:last').clone();
        $clone.find('input').val('');
        $clone.find('select').val('');
        $clone.find('img').remove();
        $clone.insertAfter($('[class^="addMaterialDetail"]').last());
    });


        // load material name using ajax
        $(document).on("change",".material_category",function(){

            var optionHTML="<option value=''>Material Name</option>";
            var category_id = $(this).val();
            var ele=this;
            if(category_id) {   
                $.ajax({
                    url: "<?php echo base_url().'admin/MaterialLog/getmaterialAjax/'?>"+category_id,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {
                        // $('select[name="city"]').empty();
                        $.each(data, function(key, value) {
                            optionHTML+='<option data-unit="'+value.unit_measurement+'" value="'+ value.id +'">'+ value.name +'</option>';
                        });
                        $(ele).parents(".form-group").next().find("select").html(optionHTML);
                    }
                });
            }else{
                $(ele).parents(".form-group").next().find("select").html(optionHTML);
            }
        }); 

        // display unit measurement beside quantity textbox
        $(document).on("change",".material_name",function(){

            var unit_measurement = $(this).find('option:selected', this).attr('data-unit');
            $(this).parents(".form-group").next().find(".unit").html(unit_measurement);
        });


        // load supervisor name using ajax
        $(document).on("change",".project_name",function(){ 
            var project_id = $(this).val();
            projectByOption(project_id);
        }); 
        $(document).on("change",".supplier_name",function(){
            
            $('.material_name').html("<option value=''>Material Name</option>");
            var CategoryOption ="<option value=''>Material Category</option>";
            var supplier_id = $(this).val();
            var projectCategoryOption ="<option value=''>Material Category</option>";
            var ele=this;
            if(supplier_id) { 
                $.ajax({
                    url: "<?php echo base_url().'admin/MaterialLog/getSupplierCategoryAjax/'?>"+supplier_id,
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

    function projectByOption(project_id){
        var optionHTML="<option value=''>Supervisor Name</option>";
        var projectSupplierOption ="<option value=''>Supplier Name</option>";
         
        if(project_id != '' && project_id != undefined){
            $.ajax({
                url: "<?php echo base_url().'admin/MaterialLog/getSupervisorAjax/'?>"+project_id,
                type: "GET",
                dataType: "json",
                success:function(data) {
                    
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
            $('.supervisor_name').html(optionHTML);
            $('.supplier_name').html(projectSupplierOption);
        }
    }
    
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