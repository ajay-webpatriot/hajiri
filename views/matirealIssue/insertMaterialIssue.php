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
                

                    <form action="<?php echo base_url('admin/MaterialIssue/addIssueLog');?>" id="addIssueLog" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <div class="box-body">
                           <!-- Issue Date start -->
                            <div class="form-group">
                                <label for="date" class="col-sm-3 control-label">Issue Date:</label>
                                <div class="col-sm-9">
                                    <input name="issueDate" id="date" placeholder="Issue Date" class="form-control datepicker" type="text" value="<?php echo (isset($_POST['issueDate'])) ? $_POST['issueDate'] : ''; ?>" required>
                                    <span class="error"><?php echo (form_error('issueDate')) ? form_error('issueDate') : ''; ?></span>
                                </div>
                            </div>
                            <!-- Issue Date end -->
                            <div class="form-group">
                                <label for="date" class="col-sm-3 control-label">Issue No:</label>
                                <div class="col-sm-9">
                                    <input name="issueNo" id="issueNo" placeholder="Issue No" class="form-control" type="text" value="<?php echo (isset($_POST['issueNo'])) ? $_POST['issueNo'] : ''; ?>" required>
                                    <span class="error"><?php echo (form_error('issueNo')) ? form_error('issueNo') : ''; ?></span>
                                </div>
                            </div>
                            

                            <div class="form-group">
                                    <label for="materialCategory" class="col-sm-3 control-label">Material Category:</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="MaterialCategory" name="materialCategory" required>
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
                                <label for="MaterialName" class="col-sm-3 control-label">Material Name:</label>
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
                                    <span class="error"><?php echo form_error('supervisor_name') ?></span>
                                </div>
                            </div>
                               
                            <div class="form-group">
                                <label for="IssueQuantity" class="col-sm-3 control-label">Quantity:</label>
                                <div class="col-sm-6">
                                    <input name="IssueQuantity" placeholder="Quantity" class="form-control" type="number" min="1" value="<?php echo (isset($_POST['IssueQuantity'])) ? $_POST['IssueQuantity'] : ''; ?>" required>
                                    <span class="error"><?php echo (form_error('IssueQuantity')) ? form_error('IssueQuantity') : ''; ?></span>
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
                                </div>
                            </div>  
                            
                            <div class="form-group">
                                <label for="ConsumtionPlace" class="col-sm-3 control-label">Consumption Place:</label>
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
                                <label for="quantity" class="col-sm-3 control-label">Issue Comment:</label>
                                <div class="col-sm-6">
                                   <textarea name="issueComment" id="" cols="30" rows="5"></textarea>
                                </div>
                            </div> 
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
       
    $("#MaterialCategory").change(function(){
        var selectCatId = $("#MaterialCategory option:selected").val();
        var ele=this;
        var optionHTML="<option value=''>Material Name</option>";
        console.log(selectCatId);
        $.ajax({
            type:"GET",
            url: "<?php echo base_url('admin/MaterialIssue/materialIssueNames/'); ?>"+selectCatId,
            dataType: "json",
            success: function(data) {
                console.log('============');
                $.each(data, function(key, value) {
                    console.log(value);
                   
                    optionHTML+='<option  data-unit="'+value.unit_measurement+'"  value="'+ value.id +'">'+ value.name +'</option>';
                });
                $(ele).parents(".form-group").next().find("select").html(optionHTML);
            }
        });

        $('#MaterialNames').html();
    });

    $(document).on("change",".materialName",function(){
            var unit_measurement = $(this).find('option:selected', this).attr('data-unit');
            $(this).parents(".form-group").next().find(".unit").html(unit_measurement);
        });
        
        // load material name using ajax
        // $(document).on("change",".material_category",function(){

        //     var optionHTML="<option value=''>Material Name</option>";
        //     var category_id = $(this).val();
        //     var ele=this;
        //     if(category_id) {   
        //         $.ajax({
        //             url: "<?php //echo base_url().'admin/MaterialLog/getmaterialAjax/'?>"+category_id,
        //             type: "GET",
        //             dataType: "json",
        //             success:function(data) {
        //                 // $('select[name="city"]').empty();
        //                 $.each(data, function(key, value) {
        //                     optionHTML+='<option  data-unit="'+value.unit_measurement+'"  value="'+ value.id +'">'+ value.name +'</option>';
        //                 });
        //                 $(ele).parents(".form-group").next().find("select").html(optionHTML);
        //             }
        //         });
        //     }else{
        //         $(ele).parents(".form-group").next().find("select").html(optionHTML);
        //     }
        // }); 
        
    //     $(document).on("change",".material_name",function(){
    //         var unit_measurement = $(this).find('option:selected', this).attr('data-unit');
    //         $(this).parents(".form-group").next().find(".unit").html(unit_measurement);
    //     });
    // });

</script>