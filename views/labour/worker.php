<style>
    .switch {
      position: relative;
      display: inline-block;
      width: 43px;
      height: 17px;
    }

    .switch input {display:none;}

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: green;
      -webkit-transition: .4s;
      transition: .4s;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 10px;
      width: 10px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      -webkit-transition: .4s;
      transition: .4s;
    }

    input:checked + .slider {
      background-color: #2196F3;
    }

    input:focus + .slider {
      box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
      -webkit-transform: translateX(26px);
      -ms-transform: translateX(26px);
      transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
      border-radius: 34px;
    }

    .slider.round:before {
      border-radius: 50%;
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
        <li><a href="<?php echo base_url('admin/workerRegister'); ?>">Worker Register</a></li>
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
                            <input type="hidden" id="worker_id" name="worker_id" value="<?php echo (isset($results->worker_id)) ? $results->worker_id : ''; ?>">
                            <input type="hidden" id="wage_id" name="wage_id" value="<?php echo (isset($wage->worker_wage_id)) ? $wage->worker_wage_id : ''; ?>">
                            <div class="form-group">
                                <label class="col-sm-3" id='monthly' style="text-align: right;">Monthly Wage</label>
                                <div class="col-sm-2" style="text-align: center;">
                                    <label class="switch">
                                        <input type="checkbox" name="wage_type" id='switch' value='<?php echo $wage->worker_wage_type; ?>' 
                                            <?php 
                                                if(isset($wage->worker_wage_type)){
                                                    if($wage->worker_wage_type == 0) 
                                                    echo 'checked';
                                                }
                                            ?>
                                        >
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <label class="col-sm-3" id='daily'>Daily Wage</label>
                            </div>
                            <?php if( $this->session->userdata('user_designation') == 'Superadmin' ) {?>
                            <div class="form-group">
                                <label for="company_id" class="col-sm-3 control-label">Company:</label>
                                <div class="col-sm-9">
                                    <select name="company_id" class="form-control company_id">
                                        <option value="">--Select Company--</option>
                                        <?php 
                                            foreach( $companies as $company ){ 
                                                $selected = '';
                                                if( isset($_POST['company_id']) && $company->compnay_id == $_POST['company_id'] ){
                                                    $selected = 'selected="selected"';
                                                }
                                                echo '<option value="'.$company->compnay_id.'" '.$selected.' >'.$company->company_name.'</option>';
                                            }
                                        ?>
                                    </select>
                                    <span class="error"><?php echo (form_error('company_id')) ? form_error('company_id') : ''; ?></span>
                                </div>
                            </div>
                            <?php } else{
                            ?>
                            <input type="text" name="company_id" value="<?php echo $this->session->userdata('company_id'); ?>" hidden>
                            <?php }?>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">First name:</label>

                                <div class="col-sm-9">
                                    <input name="first_name" placeholder="First name" class="form-control" type="text" value="<?php echo (isset($results->labour_name)) ? $results->labour_name : ''; ?>" required>
                                    <span class="error"><?php echo (form_error('first_name')) ? form_error('first_name') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Last name:</label>

                                <div class="col-sm-9">
                                    <input name="last_name" placeholder="Last name" class="form-control" type="text" value="<?php echo (isset($_POST['labour_last_name'])) ? $_POST['labour_last__name'] : ''; ?><?php echo (isset($results->labour_last_name)) ? $results->labour_last_name : ''; ?>">
                                    <span class="error"><?php echo (form_error('last_name')) ? form_error('last_name') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Contact Number:</label>
                                <div class="col-sm-9">
                                    <input name="contact" placeholder="Contact Number" class="form-control" type="text" value="<?php echo (isset($_POST['worker_contact'])) ? $_POST['worker_contact'] : ''; ?><?php echo (isset($results->worker_contact)) ? $results->worker_contact : ''; ?>">
                                    <span class="error"><?php echo (form_error('contact')) ? form_error('contact') : ''; ?></span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label" id='wage'>Daily Wage:</label>
                                <div class="col-sm-9">
                                    <input name="daily_wage" id='wageInput' placeholder="Daily Wage" class="form-control" type="text" value="<?php echo (isset($_POST['daily_wage'])) ? $_POST['daily_wage'] : ''; ?><?php echo (isset($wage->worker_wage)) ? $wage->worker_wage : ''; ?>">
                                    <span class="error"><?php echo (form_error('daily_wage')) ? form_error('daily_wage') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Opening Amount:</label>
                                <div class="col-sm-9">
                                    <input name="due_amount" placeholder="Opening Amount" class="form-control" type="text" value="<?php echo (isset($wage->worker_opening_wage)) ? $wage->worker_opening_wage : ''; ?>">
                                    <span class="error"><?php echo (form_error('due_amount')) ? form_error('due_amount') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category" class="col-sm-3 control-label">Category:</label>
                                <div class="col-sm-9">
                                    <select name="cid" class="form-control category" required>

                                    </select>
                                    <span class="error"><?php echo (form_error('category')) ? form_error('category') : ''; ?></span>
                                </div>
                            </div>
                            <?php if(isset($results->worker_id)){ ?>
                                <div class="form-group" id='activeSelect'>
                                    <label for="status" class="col-sm-3 control-label">Status:</label>
                                    <div class="col-sm-9">
                                        <select name="status" class="form-control activeSel">
                                            <option value="">--Select Status--</option>
                                            <option value="1" <?php echo (isset($results->status) && $results->status == "1") ? 'selected="selected"' : ''; ?>>Active</option>
                                            <option value="2" <?php echo (isset($results->status) && $results->status == "2") ? 'selected="selected"' : ''; ?>>In Active</option>
                                        </select>
                                        <span class="error"><?php echo (form_error('status')) ? form_error('status') : ''; ?></span>
                                    </div>
                                </div>

                                <div class="form-group" id="deleteSelect">
                                    <label for="status" class="col-sm-3 control-label">Delete worker:</label>
                                    <div class="col-sm-9">
                                        <select name="delete" class="form-control">
                                            <option value="0" <?php echo ($results->status == "0") ? 'selected="selected"' : ''; ?>>Yes</option>
                                        <option value="2" <?php echo ($results->status == "2") ? 'selected="selected"' : ''; ?>>No</option>
                                        </select>
                                        <span class="error"><?php echo (form_error('status')) ? form_error('status') : ''; ?></span>
                                    </div>
                                </div>
                            <?php } 
                            else{ ?>
                            <input type="text" value="1" name="status" class="hidden" />
                            <?php } ?>
                        </div>
                        <!-- /.box-body -->
                        <?php 
                            if($limit->wLimit > 0 || $results != '' || $planId->id == 3){
                        ?>
                        <div class="box-footer">
                            <input type="submit" id="btnSave" name="submit_add" class="btn btn-primary" value="Save">
                        </div>
                        <?php } ?>
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
        if(jQuery('.activeSel').val() == 1){
            $('#deleteSelect').addClass('hidden');
        }
        jQuery('.activeSel').change(function() { 
            if(jQuery('.activeSel').val() == 1){
                $('#deleteSelect').addClass('hidden');
            }else{
                $('#deleteSelect').removeClass('hidden');
            }
        });

        $("[data-mask]").inputmask();
        jQuery('#add-labour').submit(function (event) {
            var exp = /^[a-zA-Z ]+$/;
            var exp2 = /^[a-zA-Z0-9 ]+$/;
            var phone_pattern = /^\d{10}$/;
            var labour_name = jQuery("[name='first_name']");
            var labour_lname = jQuery("[name='last_name']");
            var company_name = jQuery("[name='company']");
            var cid = jQuery("select[name='cid']");
            var category = jQuery("select[name='category']");
            var contact = jQuery("[name='contact']");
            var daily_wage = jQuery("[name='daily_wage']");
            var due_amount = jQuery("[name='due_amount']");

            var error = 0;
             if (company_name.val() == '') {
                company_name.css({'border': '1px solid red', });
                company_name.next().text("Please select a company");
                error = 1;
            } else {
                company_name.css({'border': '1px solid green', });
                company_name.next().text("");
            }
            if (labour_name.val() == '') {
                labour_name.css({'border': '1px solid red', });
                labour_name.next().text("Please enter worker first name");
                error = 1;
            } else {
                if (labour_name.val().match(exp2)) {
                    labour_name.css({'border': '1px solid green', });
                    labour_name.next().text("");
                } else {
                    labour_name.css({'border': '1px solid red', });
                    labour_name.next().text("Please enter valid worker first name");
                    error = 1;
                }
            }
            if (labour_lname.val() == '') {
                labour_lname.css({'border': '1px solid red', });
                labour_lname.next().text("Please enter worker last name");
                error = 1;
            } else {
                if (labour_lname.val().match(exp2)) {
                    labour_lname.css({'border': '1px solid green', });
                    labour_lname.next().text("");
                } else {
                    labour_lname.css({'border': '1px solid red', });
                    labour_lname.next().text("Please enter valid worker last name");
                    error = 1;
                }
            }
            if (category.val() == '') {
                category.css({'border': '1px solid red', });
                category.next().text("Please select Worker category");
                error = 1;
            } else {
                category.css({'border': '1px solid green', });
                category.next().text("");
            }
            if (cid.val() == '') {
                cid.css({'border': '1px solid red', });
                cid.next().text("Please select project");
                error = 1;
            } else {
                cid.css({'border': '1px solid green', });
                cid.next().text("");
            }
            if (contact.val() != '') {
                if (contact.val().match(phone_pattern)) {
                    contact.css({'border': '1px solid green', });
                    contact.next().text("");
                } else {
                    contact.css({'border': '1px solid red', });
                    contact.next().text("Please enter valid contact number");
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
            if (status.val() == '') {
                status.css({'border': '1px solid red', });
                status.next().text("Please select status");
                error = 1;
            } else {
                status.css({'border': '1px solid green', });
                status.next().text("");
            }
            if (error > 0) {
                event.preventDefault();
            }
        });
       
   
        if($("#switch").prop('checked') == true){
            $("#wage").text("Daily wage");
            $('#wageInput').attr("placeholder", "Daily wage");
            $(this).val('0');
        }else{
            $("#wage").text("Monthly wage");
            $('#wageInput').attr("placeholder", "Monthly wage");
            $(this).val('1');
        }

        $('#switch').change(function() {
            if($(this).is(":checked")) {
                $("#wage").text("Daily wage");
                $('#wageInput').attr("placeholder", "Daily wage");
                $(this).val('0');
            }
            else{
                $("#wage").text("Monthly wage");
                $('#wageInput').attr("placeholder", "Monthly wage");
                $(this).val('1');
            }
        });

        $('.company').change(function(){
            var id = $('.company :selected').val();
            if (id) {
                $.ajax({
                    url: "<?php echo site_url('admin/labour/ajax_get_categoryList') ?>/" + id,
                    type: "POST",
                    dataType: "JSON",
                    success: function (data) {
                        $('.category').html(data.projectlist);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert('Error Getting Project list');
                    }
                });
            } 
        });
    
        var role = "<?php echo $this->session->userdata('user_designation'); ?>";
        if (role == 'Superadmin'){
            $('.company_id').on('change', function () {
                var company_id = $(this).val();
                
                if (company_id) {
                    $.ajax({
                        url: "<?php echo site_url('admin/labour/ajax_get_categoryList') ?>/" + company_id,
                        type: "POST",
                        dataType: "JSON",
                        success: function (data) {
                            $('.category').html(data.projectlist);
                            selected();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            alert('Error Getting Project list');
                        }
                    });
                }
            });
        }else{
            $.ajax({
                url: "<?php echo site_url('admin/labour/ajax_get_categoryList') ?>/<?php echo $this->session->userdata('company_id'); ?>",
                type: "POST",
                dataType: "JSON",
                success: function (data) {
                    $('.category').html(data.projectlist);
                    selected();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Error Getting Category list');
                }
            });
        }
    });


    function selected(){
        $cat_id = null;
         <?php 
            if(isset($results->category_id))
                $cat_id = $results->category_id;
            else
                $cat_id = '-1';
        ?>
        
        if($cat_id != '-1'){
            var num = <?php echo $cat_id; ?>;
            $(".category option").each(function(){
                if($(this).val()==num){
                    $(this).attr("selected","selected");    
                }
            });
        }
    }
</script>