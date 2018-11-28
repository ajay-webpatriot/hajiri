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
        <?php if($this->session->userdata('user_designation') != 'admin'){ ?>
        <li><a href="<?php echo base_url('admin/companies'); ?>"> Companies</a></li>
        <?php } ?>
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
                    ?> <?php //print_r( $planDetails ); exit;?>
                    <!-- form start -->
                    <form action="" id="frmCompany" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <div class="box-body">
                            <input type="hidden" id="company_id" name="company_id" value="<?php echo (isset($results->compnay_id)) ? $results->compnay_id : ''; ?>">
                            <div class="form-group">
                                <label for="company_name" class="col-sm-3 control-label">Name:</label>
                                <div class="col-sm-9">
                                    <input name="company_name" placeholder="Enter Company Name" class="form-control" type="text" value="<?php echo (isset($results->company_name)) ? $results->company_name : ''; ?>">
                                    <span class="error"><?php echo (form_error('company_name')) ? form_error('company_name') : ''; ?></span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Company Type:</label>
                                <div class="col-sm-9">
                                    <input name="company_type" placeholder="Enter Company Type" class="form-control" type="text" value="<?php echo (isset($results->company_type)) ? $results->company_type : ''; ?>">
                                    <span class="error"><?php echo (form_error('company_type')) ? form_error('company_type') : ''; ?></span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="Company Payment Plan">Payment Plan</label>
                                <div class="col-sm-9">
                                    <select name="company_payment_plan_id" class="form-control">
                                        <?php foreach($pricingPlan as $plan){ ?> 

                                        <option value="<?php echo $plan->id ?>"  <?php echo (isset($results->company_payment_plan_id) && $results->company_payment_plan_id == $plan->id) ? 'selected="selected"' : ''; ?> ><?php echo $plan->name; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="error"><?php echo (form_error('company_payment_plan_id')) ? form_error('company_payment_plan_id') : ''; ?></span> 
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="Company Payment Plan">Set permissions:</label>
                                <div class="col-sm-9 checkBoxList">
                                        <?php 
                                         if ($pluginAssigned != -1){
                                            foreach ($pluginList as $plugin) {
                                               echo "<label><input type='checkbox' name ='plugin[]' " . (in_array($plugin->plugin_id,$plugin_assign_ids) ? "checked" : "") . " value='" . $plugin->plugin_id . "'>" . $plugin->plugin_name . "</label>";
                                            }
                                        }else{
                                        foreach($pluginList as $data){ ?> 
                                            <label>
                                                <input type='checkbox' name ='plugin[]' value="<?php echo $data->plugin_id ?>"><?php echo $data->plugin_name; ?>
                                            </label>
                                        <?php } }?>
                                    <span class="error"><?php echo (form_error('company_payment_plan_id')) ? form_error('company_payment_plan_id') : ''; ?></span> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Address:</label>
                                <div class="col-sm-9">
                                    <textarea name="company_address" placeholder="Enter company address" type="text" class="form-control"> <?php echo (empty($results->company_address)) ? '' : $results->company_address; ?></textarea>
                                    <p class="help-block error"><?php echo form_error('company_address'); ?></p> 
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="exampleInputEmail1">Pincode:</label>
                                <div class="col-sm-9">
                                    <input value="<?php echo (empty($results->company_pincode)) ? '' : $results->company_pincode; ?>" name="company_pincode" placeholder="Enter Pincode." type="text" class="form-control" maxlength="6"/> 
                                    <p class="help-block error"><?php echo form_error('company_pincode'); ?></p> 
                                </div>
                            </div>

                             <div class="form-group">
                                <label class="col-sm-3 control-label" for="exampleInputEmail1">City:</label>
                                <div class="col-sm-9">
                                    <input value="<?php echo (empty($results->company_city)) ? '' : $results->company_city; ?>" name="company_city" placeholder="Enter City Name." type="text" class="form-control"/> 
                                    <p class="help-block error"><?php echo form_error('company_city'); ?></p> 
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="exampleInputEmail1">State:</label>
                                <div class="col-sm-9">
                                    <input value="<?php echo (empty($results->company_state)) ? '' : $results->company_state; ?>" name="company_state" placeholder="Enter State Name." type="text" class="form-control"/> 
                                    <p class="help-block error"><?php echo form_error('company_state'); ?></p> 
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="exampleInputEmail1">Country:</label>
                                <div class="col-sm-9">
                                    <input value="<?php echo (empty($results->company_country)) ? '' : $results->company_country; ?>" name="company_country" placeholder="Enter Country Name." type="text" class="form-control"/> 
                                    <p class="help-block error"><?php echo form_error('company_country'); ?></p> 
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="exampleInputEmail1">Company Email:</label>
                                <div class="col-sm-9">
                                    <input value="<?php echo (empty($results->company_email)) ? '' : $results->company_email; ?>" name="company_email" placeholder="Enter Company Email." type="text" class="form-control"/> 
                                    <p class="help-block error"><?php echo form_error('company_email'); ?></p> 
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="exampleInputEmail1">Contact Number:</label>
                                <div class="col-sm-9">
                                    <input value="<?php echo (empty($results->company_contact_no)) ? '' : $results->company_contact_no; ?>" name="company_contact_no" placeholder="Enter Contact Number." type="text" class="form-control" maxlength="10"/> 
                                    <p class="help-block error"><?php echo form_error('company_contact_no'); ?></p> 
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="exampleInputEmail1">Website:</label>
                                <div class="col-sm-9">
                                    <input value="<?php echo (empty($results->company_website)) ? '' : $results->company_website; ?>" name="company_website" placeholder="Enter Website." type="text" class="form-control"/> 
                                    <p class="help-block error"><?php echo form_error('company_website'); ?></p> 
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="exampleInputEmail1">GST Number:</label>
                                <div class="col-sm-9">
                                    <input value="<?php echo (empty($results->company_gst)) ? '' : $results->company_gst; ?>" name="company_gst" placeholder="Enter GST Number." type="text" class="form-control"/> 
                                    <p class="help-block error"><?php echo form_error('company_gst'); ?></p> 
                                </div>
                            </div>

                             <div class="form-group">
                                <label class="col-sm-3 control-label" for="exampleInputEmail1">Company PAN:</label>
                                <div class="col-sm-9">
                                    <input value="<?php echo (empty($results->company_pan)) ? '' : $results->company_pan; ?>" name="company_pan" placeholder="Enter Company PAN Number." type="text" class="form-control"/> 
                                    <p class="help-block error"><?php echo form_error('company_pan'); ?></p> 
                                </div>
                            </div>

                            <div class="col-sm-offset-1 col-sm-11">
                                <?php
                                $associatedFileNames = array('company_logo_image');
                                foreach ($associatedFileNames as $key => $fileName) {
                                    ?>
                                    <div class="form-group col-sm-12">
                                        <label for="<?php echo $fileName ?>" class="col-lg-2 control-label">Photo:</label>

                                        <div class="col-lg-8">
                                            <input type="file" id="<?php echo $fileName; ?>" name="<?php echo $fileName; ?>"/>
                                            <p class="help-block error"><?php echo array_key_exists($fileName, $fileError) ? $fileError[$fileName] : ''; ?></p>

                                        </div>
                                    </div>
                                    <?php if (!empty($results->company_logo_image)) { ?>
                                        <div class="form-group col-sm-12">
                                            <label for="<?php echo $fileName ?>" class="col-lg-2 control-label">Profile Photo:</label>
                                            <div class="col-lg-8">
                                                <img src=" <?php echo $url = (empty($results->company_logo_image)) ? base_url('assets/') . 'admin/pages/img/defmanager.png' : base_url() . 'uploads/user/' . $results->company_logo_image;
                                        ?>" height="50" class="image"/>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="status" class="col-sm-3 control-label">Status:</label>
                                <div class="col-sm-9">
                                    <select name="status" class="form-control">
                                        <option value="1" <?php echo (isset($results->status) && $results->status == "1") ? 'selected="selected"' : ''; ?>>Active</option>
                                        <option value="2" <?php echo (isset($results->status) && $results->status == "2") ? 'selected="selected"' : ''; ?>>In Active</option>
                                    </select>
                                    <span class="error"><?php echo (form_error('status')) ? form_error('status') : ''; ?></span>
                                </div>
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
<script>
    $(document).ready(function () {
        $('.alert-success').fadeOut(2000); //remove suucess message
    });
    $(document).ready(function () {
        $('#frmCompany').submit(function (event) {
            boolReturn = true;
            
            var exp = /^[a-zA-Z ]+$/;
            var phone_pattern = /[0-9]{10}/;
            var uname = jQuery("[name='company_name']");
            var email = jQuery("[name='company_email']");
            var contact = jQuery("[name='company_contact_no']");

            if (uname.val() == '') {
                uname.css({'border': '1px solid red', });
                uname.next().text("Please enter company name");
                boolReturn = false;
            }
            
            if (email.val() == '') {
                email.css({'border': '1px solid red', });
                email.next().text("Please enter company email");
                boolReturn = false;
            }
            else {
                var atpos = email.val().indexOf("@");
                var dotpos = email.val().lastIndexOf(".");
                if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= email.val().length) {
                    email.css({'border': '1px solid red', });
                    email.next().text("Please enter valid email address");
                    boolReturn = false;
                } else {
                    email.css({'border': '1px solid green', });
                    email.next().text("");
                }
            }
            if (contact.val() == '') {
                contact.css({'border': '1px solid red', });
                contact.next().text("Please enter contact number");
                boolReturn = false;
            } else {
                if (contact.val().match(phone_pattern)) {
                    contact.css({'border': '1px solid green', });
                    contact.next().text("");
                } else {
                    contact.css({'border': '1px solid red', });
                    contact.next().text("Please enter valid contact number");
                    boolReturn = false;
                }
            }
            /*var userid = $('#company_id').val();
            var base_url = "<?php echo base_url(); ?>";
            if (email.val()) {
                boolReturn = $.ajax({
                    url: base_url + 'admin/companies/checkEmailExists/',
                    type: 'POST',
                    dataType: 'json',
                    data: {company_email: email.val(), company_id: userid, ajax_request: 1},
                    success: function (data) {
                        console.log( data );
                        if (data.result == true) {
                            email.css({'border': '1px solid #FF0000'});
                            email.next('p.error').html('This email is already exists.');
                             return false;
                        } else {
                            email.css({'border': '1px solid green'});
                            email.next('p.error').html('');
                            return true;
                        }
                    }
                });
            }
            console.log( boolReturn );
            return false;*/
            return boolReturn;
            
        });
    });
    jQuery(document).ready(function ($) {
        jQuery('[name="email"]').on('blur', function () {
            var email = $(this);
            var userid = "<?php echo $this->session->userdata('id') ?>";
            var base_url = "<?php echo base_url(); ?>";
            if (email.val()) {
                $.ajax({
                    url: base_url + 'admin/ajax_email_check_onupdate/',
                    type: 'POST',
                    dataType: 'json',
                    data: {email: email.val(), userid: userid},
                    success: function (data) {
                        if (data.status == true) {
                            email.css({'border': '2px solid #FF0000'});
                            email.next('p.error').html('This email is already exists.');
                        } else {
                            email.css({'border': '1px solid #c5c5c5'});
                            email.next('p.error').html('');
                        }
                    }
                });
            }
        });
    });
</script>