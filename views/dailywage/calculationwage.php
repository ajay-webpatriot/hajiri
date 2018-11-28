<style>
    .table-responsive{
        max-height: 350px;
    }
</style>
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
                        <h3 class="box-title"><?php // echo (!empty($description) ? $description : '');                         ?></h3>
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
                    <form action="" id="import-labour" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="user_id" class="col-sm-3 control-label">Select Method: </label>
                                <div class="col-sm-9">
                                    <select name="method_id" class="form-control Manager" id="method_id">
                                       <option value="">-Select-</option>
                                        <?php
                                        if ($users) {
                                            foreach ($users as $user) {
                                                echo "<option " . (isset($selected_method) && ($selected_method == $user->method_id) ? "selected='selected'" : "") . " value='" . $user->method_id . "'>" . $user->method_name . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <span class="error"><?php echo (form_error('user_id')) ? form_error('user_id') : ''; ?></span>
                                </div>
                            </div>

                            <div class="form-group" id="days" style="display: none;">
                                <label for="user_id" class="col-sm-3 control-label">Enter Days:</label>
                                <div class="col-sm-9">
                                    <input type="number" name="day" id="day" value="<?php echo $days;?>" class="form-control">
                                   <font color="red"> <span id="days_error" style="display: none;">Please Enter Valid Days (1-30 days )</span></font>
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
            <!-- /.col -->

            
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script>
    $(document).ready(function () {
        var method ="<?php echo $selected_method; ?>";
       // alert(method);
        if(method ==3){
            $('#days').show();
            $('#btnSave').show();
        }
        jQuery('.alert-danger').fadeOut(3000); //remove suucess message
        jQuery('#import-labour').submit(function (event) {
            var user_id = jQuery("[name='method_id']");
            var days = $('#day').val();
            if (user_id.val() == '')
            {
                user_id.css({'border': '1px solid red', });
                user_id.next().text("Please select Method");
                event.preventDefault();
            } else {
                user_id.css({'border': '1px solid green', });
                user_id.next().text("");
            }
        });

        $("#method_id").change(function(){
          $('#btnSave').show();
         var method =$(this).val();
         var user_id = jQuery("[name='method_id']");
         user_id.css({'border': '1px solid green', });
                user_id.next().text("");
         if(method == 3){
            $('#days').show();
            $('#btnSave').hide();
         }else{
            $('#days').hide();
         }
        }); 

         $("#day").keyup(function(){
           var day =$(this).val();
           if(day >30 || day == 0){
             $('#days_error').show();
              $('#btnSave').hide();
           }else{
            $('#days_error').hide();
            $('#btnSave').show();
           }
         });
    });
</script>