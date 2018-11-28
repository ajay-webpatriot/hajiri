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
                        <div class="alert alert-danger alert-dismiss">
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
 
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#home">QR ID Card</a></li>
                        <li><a  href="<?php echo base_url(); ?>admin/report/labour_barcode_report">QR Stamp</a></li>
                    </ul>

                    <!-- form start -->
                    <form action="" id="barcode-report" class="form-horizontal" method="GET" enctype="multipart/form-data">
                        <div class="box-body">
                            <div class="form-group" style="display: none;">
                                <label for="users" class="col-sm-3 control-label">Project Manager:</label>
                                <div class="col-sm-9">
                                    <select name="user_id" class="form-control">
                                       
                                        <?php if ($users) { ?>
                                            <?php foreach ($users as $user) { ?>
                                                <option value="<?php echo $user->user_id; ?>"><?php echo $user->user_name; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                    <span class="error"><?php echo form_error('user_id') ?></span>
                                </div>
                            </div>
                            <div class="form-group all_labours" id="all_labours">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" value="yes" name="is_today"> Get All Labour's Id-Cards
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" >
                                <div class="col-sm-12">
                                    <label for="users" class="col-sm-3 control-label">Particular Labour's:</label>
                                    <div class="checkbox">
                                        <label>
                                            <input type="radio" value="1" name="particular_labour"> Yes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="radio" value="0" name="particular_labour" checked="checked"> No
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group labours-selection-div" style="display: none;">
                                <label for="labours" class="col-sm-3 control-label">Labours Name:</label>
                                <div class="col-sm-9">
                                    <select name="labour_id[]" id="labour_id" class="form-control" multiple="multiple">
                                        <option value="">Select Labours Name:</option>
                                    </select>
                                    <span class="error"><?php echo form_error('labour_id') ?></span>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <input type="submit" id="btnSave" name="submit" class="btn btn-primary" value="Generate ID Cards">
                        </div>
                        <!-- /.box-footer -->
                    </form>
                </div><!-- /.box -->
            </div>

        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->


<script>
    jQuery(document).ready(function ($) {
       
        $('.alert-dismiss').fadeOut(5000);
       // $("[name='user_id']").change(function () {
            var user_id = "<?php  echo  $this->session->userdata('id'); ?>";
           
            if (user_id != '') {
                jQuery.ajax({
                    url: "<?php echo site_url('admin/report/ajax_get_labours') ?>/" + user_id,
                    type: "POST",
                    dataType: "JSON",
                    success: function (data) {
                        $("#labour_id").html(data.labours_html);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert('Error while getting data');
                    }
                });
            } else {
                $("#labour_id").html("<option value=''>Select Labours Name</option>");
            }
       // });
    });

    jQuery(document).ready(function ($) {
        $("input[name=particular_labour]:radio").change(function () {
            if ($(this).val() == '1') {
                $('.labours-selection-div').show();
                 $('.all_labours').hide();
            } else {
                $('.labours-selection-div').hide();
                $("#labour_id").val('');
                 $('.all_labours').show();
            }
        });
    });
      jQuery(document).ready(function () {
        jQuery('#barcode-report').submit(function (event) {
            var exp = /^[a-zA-Z ]+$/;
            var phone_pattern = /[0-9]{10}/;
            var aadhar_pattern = /[0-9]/;
            
            var user_id = jQuery("select[name='user_id']");
            var labour_id = jQuery("select[name='labour_id[]']");
            var particular_labour = jQuery("radio[name='particular_labour']");

            var error = 0;
             if (user_id.val() == ''){
                user_id.css({'border': '1px solid red', });
                user_id.next().text("Please select user");
                error = 1;
            } else {
                user_id.css({'border': '1px solid green', });
                user_id.next().text("");
            }
            if (labour_id.val() == ''){
                labour_id.css({'border': '1px solid red', });
                labour_id.next().text("Please select labour");
                error = 1;
            } else {
                labour_id.css({'border': '1px solid green', });
                labour_id.next().text("");
            }
            
            if (particular_labour.val() == '') {
                particular_labour.css({'border': '1px solid red', });
                particular_labour.next().text("Please select Particular labour's");
                error = 1;
            } else {
                    particular_labour.css({'border': '1px solid green', });
                    particular_labour.next().text("");
            }
            if (error > 0) {
                event.preventDefault();
            }
        });
    });
</script>