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
            <!-- left column -->
            <div class="col-md-8 col-offset-2">
                <!-- general form elements -->
                <div class="box box-primary">
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
                    <form role="form" action="" id="profile-form" method="POST" enctype="multipart/form-data">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Name</label>
                                <input value="<?php echo (empty($results->company_name)) ? '' : $results->company_name; ?>" name="name" placeholder="Enter Company Name" type="text" class="form-control"/> 
                                <p class="help-block error"><?php echo form_error('name'); ?></p> 
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Company Type</label>
                                <input value="<?php echo (empty($results->company_type)) ? '' : $results->company_type; ?>" name="company_type" placeholder="Enter Company Type" type="text" class="form-control"/> 
                                <p class="help-block error"><?php echo form_error('company_type'); ?></p> 
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Address:</label>
                                <input value="<?php echo (empty($results->company_address)) ? '' : $results->company_address; ?>" name="company_address" placeholder="Enter Address." type="text" class="form-control"/> 
                                <p class="help-block error"><?php echo form_error('company_address'); ?></p> 
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Pincode:</label>
                                <input value="<?php echo (empty($results->company_pincode)) ? '' : $results->company_pincode; ?>" name="company_pincode" placeholder="Enter Pincode." type="text" class="form-control" maxlength="6"/> 
                                <p class="help-block error"><?php echo form_error('company_pincode'); ?></p> 
                            </div>

                             <div class="form-group">
                                <label for="exampleInputEmail1">City:</label>
                                <input value="<?php echo (empty($results->company_city)) ? '' : $results->company_city; ?>" name="company_city" placeholder="Enter City Name." type="text" class="form-control"/> 
                                <p class="help-block error"><?php echo form_error('company_city'); ?></p> 
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">State:</label>
                                <input value="<?php echo (empty($results->company_state)) ? '' : $results->company_state; ?>" name="company_state" placeholder="Enter State Name." type="text" class="form-control"/> 
                                <p class="help-block error"><?php echo form_error('company_state'); ?></p> 
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Country:</label>
                                <input value="<?php echo (empty($results->company_country)) ? '' : $results->company_country; ?>" name="company_country" placeholder="Enter Country Name." type="text" class="form-control"/> 
                                <p class="help-block error"><?php echo form_error('company_country'); ?></p> 
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Company Email:</label>
                                <input value="<?php echo (empty($results->company_email)) ? '' : $results->company_email; ?>" name="company_email" placeholder="Enter Company Email." type="text" class="form-control"/> 
                                <p class="help-block error"><?php echo form_error('company_email'); ?></p> 
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Contact Number:</label>
                                <input value="<?php echo (empty($results->company_contact_no)) ? '' : $results->company_contact_no; ?>" name="company_contact_no" placeholder="Enter Contact Number." type="text" class="form-control" maxlength="10"/> 
                                <p class="help-block error"><?php echo form_error('company_contact_no'); ?></p> 
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Website:</label>
                                <input value="<?php echo (empty($results->company_website)) ? '' : $results->company_website; ?>" name="company_website" placeholder="Enter Website." type="text" class="form-control"/> 
                                <p class="help-block error"><?php echo form_error('company_website'); ?></p> 
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">GST Number:</label>
                                <input value="<?php echo (empty($results->company_gst)) ? '' : $results->company_gst; ?>" name="company_gst" placeholder="Enter GST Number." type="text" class="form-control"/> 
                                <p class="help-block error"><?php echo form_error('company_gst'); ?></p> 
                            </div>

                             <div class="form-group">
                                <label for="exampleInputEmail1">Company PAN:</label>
                                <input value="<?php echo (empty($results->company_pan)) ? '' : $results->company_pan; ?>" name="company_pan" placeholder="Enter Company PAN Number." type="text" class="form-control"/> 
                                <p class="help-block error"><?php echo form_error('company_pan'); ?></p> 
                            </div>
                           
                           <div class="form-group">
                                <label for="status" class="col-sm-3 control-label">Status:</label>
                                <div class="col-sm-9">
                                    <select name="status" class="form-control">
                                        <option value="">--Select Status--</option>
                                        <option value="1" <?php echo (isset($_POST['status']) && $_POST['status'] == "1") ? 'selected="selected"' : ''; ?>>Active</option>
                                        <option value="0" <?php echo (isset($_POST['status']) && $_POST['status'] == "0") ? 'selected="selected"' : ''; ?>>In Active</option>
                                    </select>
                                    <span class="error"><?php echo (form_error('status')) ? form_error('status') : ''; ?></span>
                                </div>
                            </div>


                            <div class="row">
                                <?php
                                $associatedFileNames = array('company_logo_image');
                                foreach ($associatedFileNames as $key => $fileName) {
                                    ?>

                                    <div class="form-group col-sm-6"> 
                                        <label for="inputEmail3" class="col-sm-2 control-label">Profile Photo</label> 
                                        <div class="col-sm-9"> 
                                            <input type="file" id="<?php echo $fileName ?>" name="<?php echo $fileName ?>" />
                                            <p class="help-block error"><?php echo array_key_exists($fileName, $fileError) ? $fileError[$fileName] : ''; ?></p>

                                        </div> 
                                    </div>
                                    <div class="form-group col-sm-6"> 
                                        <label for="inputEmail3" class="col-sm-2 control-label">Profile Photo</label> 
                                        <div class="col-sm-9"> 
                                            <?php $url = (empty($results->company_logo_image)) ? base_url('assets/') . 'admin/images/defuser.png' : base_url() . 'uploads/user/' . $results->company_logo_image;
                                            ?>
                                            <img src="<?php echo $url; ?>" height="100" width="100"/>
                                        </div> 
                                    </div>
                                <?php } ?>
                            </div>   
                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <input type="submit" name="submit" class="btn btn-primary" value="Update Company Profile">
                        </div>
                    </form>
                </div>
                <!-- /.box -->


            </div>
            <!--/.col (left) -->

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
        $('#profile-form').submit(function (event) {
            var exp = /^[a-zA-Z ]+$/;
            var phone_pattern = /[0-9]{10}/;
            var uname = jQuery("[name='name']");
            var email = jQuery("[name='email']");
            var contact = jQuery("[name='contact']");

            var error = 0;
            if (uname.val() == '') {
                uname.css({'border': '1px solid red', });
                uname.next().text("Please enter name");
                error = 1;
            } else {
                if (uname.val().match(exp)) {
                    uname.css({'border': '1px solid green', });
                    uname.next().text("");
                } else {
                    uname.css({'border': '1px solid red', });
                    uname.next().text("Please enter valid name");
                    error = 1;
                }

            }
            if (email.val() == '') {
                email.css({'border': '1px solid red', });
                email.next().text("Please enter email");
                error = 1;
            }
            if (email.val() != '') {
                var atpos = email.val().indexOf("@");
                var dotpos = email.val().lastIndexOf(".");
                if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= email.val().length) {
                    email.css({'border': '1px solid red', });
                    email.next().text("Please enter valid email address");
                    error = 1;
                } else {
                    email.css({'border': '1px solid green', });
                    email.next().text("");
                }
            }
            if (contact.val() == '') {
                contact.css({'border': '1px solid red', });
                contact.next().text("Please enter contact number");
                error = 1;
            } else {
                if (contact.val().match(phone_pattern)) {
                    contact.css({'border': '1px solid green', });
                    contact.next().text("");
                } else {
                    contact.css({'border': '1px solid red', });
                    contact.next().text("Please enter valid contact number");
                    error = 1;
                }
            }
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
                            email.css({'border': '1px solid #FF0000'});
                            email.next('p.error').html('This email is already exists.');
                             error = 1;
                        } else {
                            email.css({'border': '1px solid green'});
                            email.next('p.error').html('');
                        }
                    }
                });
            }
            if (error > 0) {
                event.preventDefault();
            }
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