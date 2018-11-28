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
		<li><a href="<?php echo base_url('admin/manager'); ?>"> Managers</a></li>
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
                    ?>
                    <!-- form start -->
                    <form action="" id="add-manager" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">First Name:</label>
                                <div class="col-sm-9">
                                    <input name="name" placeholder="First Name" class="form-control" type="text" value="<?php echo (isset($_POST['name'])) ? $_POST['name'] : ''; ?>" required>
                                    <span class="error"><?php echo (form_error('name')) ? form_error('name') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Last Name:</label>
                                <div class="col-sm-9">
                                    <input name="lname" placeholder="Last Name" class="form-control" type="text" value="<?php echo (isset($_POST['lname'])) ? $_POST['lname'] : ''; ?>">
                                    <span class="error"><?php echo (form_error('lname')) ? form_error('lname') : ''; ?></span>
                                </div>
                            </div>
							
							<?php if( $this->session->userdata('user_designation') == 'Superadmin' ) {?>
							<div class="form-group">
                                <label for="company_id" class="col-sm-3 control-label">Company:</label>
                                <div class="col-sm-9">
                                    <select name="company_id" class="form-control company_id" required>
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
							<?php } else { ?>
								<input type="hidden" name="company_id" class="form-control company_id" value="<?php echo $this->session->userdata('company_id'); ?>">
							<?php }?>
							
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Email Address:</label>
                                <div class="col-sm-9">
                                    <input name="email" placeholder="Email Address" class="form-control" type="text" value="<?php echo (isset($_POST['email'])) ? $_POST['email'] : ''; ?>" required>
                                    <span class="error"><?php echo (form_error('email')) ? form_error('email') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Password:</label>
                                <div class="col-sm-9">
                                    <input name="password" placeholder="Password" class="form-control" type="password" value="<?php echo (isset($_POST['password'])) ? $_POST['password'] : ''; ?>" required>
                                    <span class="error"><?php echo (form_error('password')) ? form_error('password') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Retype Password:</label>
                                <div class="col-sm-9">
                                    <input name="conf_password" placeholder="Retype Password" class="form-control" type="password" value="" required>
                                    <span class="error"><?php echo (form_error('conf_password')) ? form_error('conf_password') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Contact Number:</label>
                                <div class="col-sm-9">
                                    <input name="contact" placeholder="Contact Number" class="form-control" type="tel" pattern="[0-9]{10}" value="<?php echo (isset($_POST['contact'])) ? $_POST['contact'] : ''; ?>" required>
                                    <span class="error"><?php echo (form_error('contact')) ? form_error('contact') : ''; ?></span>
                                </div>
                            </div>
							<div class="form-group">
                                <label for="pid" class="col-sm-3 control-label">Project: </label>
                                <div class="col-sm-9">
                                   <div class="projectlist checkBoxList"> </div>
                                    <span class="error"><?php echo (form_error('pid[]')) ? form_error('pid[]') : ''; ?></span>
                                    <span class="text-danger">Please restart the app to see changes.</span>
                                </div>
                            </div>							
							<div class="form-group">
                                <label for="pid" class="col-sm-3 control-label">Set permissions:</label>
                                <div class="col-sm-9">
                                    <div class="pluginList checkBoxList"></div>
                                    <span class="error"><?php echo (form_error('plugin')) ? form_error('plugin') : ''; ?></span>
                                    <span class="text-danger">Please restart the app to see changes.</span>
                                </div>
                            </div>
							<div class="form-group">
                                <label for="access" class="col-sm-3 control-label">Set Access:</label>
                                <div class="col-sm-9">
                                    <select class='form-control' name='access' required>
										<option value='0'>Web access </option>
										<option value='1'>Android access </option>
										<option value='2'>Web and Android access </option>
									</select>
                                    <span class="error"><?php echo (form_error('access')) ? form_error('access') : ''; ?></span>
                                    <span class="text-danger">Please ask user to restart the app to see changes.</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $associatedFileNames = array('image');
                                foreach ($associatedFileNames as $key => $fileName) {
                                    ?>
                                    <label for="<?php echo $fileName ?>" class="col-sm-3 control-label">Profile photo:</label>

                                    <div class="col-sm-9">
                                        <input type="file" id="<?php echo $fileName; ?>" name="<?php echo $fileName; ?>"/>
                                        <span class="help-block error"><?php echo array_key_exists($fileName, $fileError) ? $fileError[$fileName] : ''; ?></span>

                                    </div>
                                <?php } ?>
                            </div>
                            <?php if( $this->session->userdata('user_designation') == 'Superadmin' ) {?>
                            <div class="form-group">
                                <label for="status" class="col-sm-3 control-label">Status:</label>
                                <div class="col-sm-9">
                                    <select name="status" class="form-control" required>
                                        <option value="1" <?php echo (isset($_POST['status']) && $_POST['status'] == "1") ? 'selected="selected"' : ''; ?>>Active</option>
                                        <option value="0" <?php echo (isset($_POST['status']) && $_POST['status'] == "0") ? 'selected="selected"' : ''; ?>>In Active</option>
                                    </select>
                                    <span class="error"><?php echo (form_error('status')) ? form_error('status') : ''; ?></span>
                                </div>
                            </div>
                            <?php } else { ?>
                                <input type="hidden" name="status" value="1">
                            <?php }?>
                        </div>
                        <!-- /.box-body -->
                        <?php 
                            if($limit->wLimit > 0 || $planId->id == 3){
                        ?>
                        <div class="box-footer">
                            <input type="submit" id="btnSave" name="submit" class="btn btn-primary" value="Save">
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
    jQuery(document).ready(function ($) {
        var emailchk = 0;
        var numberchk = 0;
		<?php if( $this->session->userdata('user_designation') != 'Superadmin' ) {?>
			loadProjectAndPlugin()
		<?php } ?>
        $("[data-mask]").inputmask();
        /*jQuery('#add-manager').submit(function (event) {
            var exp = /^[a-zA-Z ]+$/;
            var phone_pattern = /^\d{10}$/;
            var aadhar_pattern = /^([0-9]{4}-){2}[0-9]{4}$/;
            var uname = jQuery("[name='name']");
            var email = jQuery("[name='email']");
            var password = jQuery("[name='password']");
            var conf_password = jQuery("[name='conf_password']");
            var contact = jQuery("[name='contact']");
            var aadhar_id = jQuery("[name='aadhar_id']");
            var organization_name = jQuery("[name='organization_name']");
            var status = jQuery("[name='status']");
            var error = 0;
            if (uname.val() == '') {
                uname.css({'border': '1px solid red', });
                uname.next('span.error').text("Please enter name");
                error = 1;
            } else {
                if (uname.val().match(exp)) {
                    uname.css({'border': '1px solid green', });
                    uname.next('span.error').text("");
                } else {
                    uname.css({'border': '1px solid red', });
                    uname.next('span.error').text("Please enter valid name");
                    error = 1;
                }
            }
            if (email.val() == '') {
                email.css({'border': '1px solid red', });
                email.next('span.error').text("Please enter email");
                error = 1;
            }
            if (email.val() != '') {
                var atpos = email.val().indexOf("@");
                var dotpos = email.val().lastIndexOf(".");
                if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= email.val().length) {
                    email.css({'border': '1px solid red', });
                    email.next('span.error').text("Please enter valid email address");
                    error = 1;
                } else {

                    email.css({'border': '1px solid green', });
                    email.next('span.error').text("");
                    var base_url = "<?php echo base_url(); ?>";
                    $.ajax({
                        url: base_url + 'admin/manager/ajax_email_check/',
                        type: 'POST',
                        dataType: 'json',
                        data: {email: email.val()},
                        success: function (data) {
                            if (data.status == true) {
                                email.css({'border': '1px solid #FF0000'});
                                email.next('span.error').html('This email is already exists.');
                                error = 1;
                            } else {
                                email.css({'border': '1px solid #c5c5c5'});
                                email.next('span.error').html('');
                            }
                        }
                    });
                }
            }
            if (password.val() == '') {
                password.css({'border': '1px solid red', });
                password.next('span.error').text("Please enter password");
                error = 1;
            } else {
                password.css({'border': '1px solid green', });
                password.next('span.error').text("");
            }
            if (conf_password.val() == '') {
                conf_password.css({'border': '1px solid red', });
                conf_password.next('span.error').text("Please enter confirm password");
                error = 1;
            } else {
                conf_password.css({'border': '1px solid green', });
                conf_password.next('span.error').text("");
            }
            if (contact.val() == '') {
                contact.css({'border': '1px solid red', });
                contact.next('span.error').text("Please enter contact number");
                error = 1;
            } else {
                if (contact.val().match(phone_pattern)) {
                    contact.css({'border': '1px solid green', });
                    contact.next('span.error').text("");
                } else {
                    contact.css({'border': '1px solid red', });
                    contact.next('span.error').text("Please enter valid contact number");
                    error = 1;
                }
            }
            if (aadhar_id.val() != '') {
                if (aadhar_id.val().match(aadhar_pattern)) {
                    aadhar_id.css({'border': '1px solid green', });
                    aadhar_id.next('span.error').text("");
                } else {
                    aadhar_id.css({'border': '1px solid red', });
                    aadhar_id.next('span.error').text("Please enter valid aadhar number e.g.(xxxx-xxxx-xxxx)");
                    error = 1;
                }
            }
            if (organization_name.val() == '') {
                organization_name.css({'border': '1px solid red', });
                organization_name.next('span.error').text("Please enter organization name");
                error = 1;
            } else {
                organization_name.css({'border': '1px solid green', });
                organization_name.next('span.error').text("");
            }
            if (password.val() != conf_password.val()) {
                conf_password.css({'border': '1px solid red', });
                conf_password.next('span.error').text("Confirm password dosen't match with password");
                error = 1;
            }
            if (status.val() == '') {
                status.css({'border': '1px solid red', });
                status.next().text("Please select status");
                error = 1;
            } else {
                status.css({'border': '1px solid green', });
                status.next().text("");
            }
            if (error == 0) {
                $(this).submit();
            }
        });*/
        $('#add-manager').submit(function (event) {
            var exp = /^[a-zA-Z ]+$/;
            var phone_pattern = /^\d{10}$/;
            var aadhar_pattern = /^([0-9]{4}-){2}[0-9]{4}$/;
            var uname = jQuery("[name='name']");
            var email = jQuery("[name='email']");
            var password = jQuery("[name='password']");
            var conf_password = jQuery("[name='conf_password']");
            var contact = jQuery("[name='contact']");
            var aadhar_id = jQuery("[name='aadhar_id']");
            var organization_name = jQuery("[name='organization_name']");
            var status = jQuery("[name='status']");
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

            if (password.val() == '') {
                password.css({'border': '1px solid red', });
                password.next('span.error').text("Please enter password");
                error = 1;
            } else {
                password.css({'border': '1px solid green', });
                password.next('span.error').text("");
            }
            if (conf_password.val() == '') {
                conf_password.css({'border': '1px solid red', });
                conf_password.next('span.error').text("Please enter confirm password");
                error = 1;
            } else {
                conf_password.css({'border': '1px solid green', });
                conf_password.next('span.error').text("");
            }
            if (contact.val() == '') {
                contact.css({'border': '1px solid red', });
                contact.next('span.error').text("Please enter contact number");
                error = 1;
            } else {
                if (contact.val().match(phone_pattern)) {
                    contact.css({'border': '1px solid green', });
                    contact.next('span.error').text("");
                } else {
                    contact.css({'border': '1px solid red', });
                    contact.next('span.error').text("Please enter valid contact number");
                    error = 1;
                }
            }
            if (aadhar_id.val() != '') {
                if (aadhar_id.val().match(aadhar_pattern)) {
                    aadhar_id.css({'border': '1px solid green', });
                    aadhar_id.next('span.error').text("");
                } else {
                    aadhar_id.css({'border': '1px solid red', });
                    aadhar_id.next('span.error').text("Please enter valid aadhar number e.g.(xxxx-xxxx-xxxx)");
                    error = 1;
                }
            }
            if (organization_name.val() == '') {
                organization_name.css({'border': '1px solid red', });
                organization_name.next('span.error').text("Please enter organization name");
                error = 1;
            } else {
                organization_name.css({'border': '1px solid green', });
                organization_name.next('span.error').text("");
            }
            if (password.val() != conf_password.val()) {
                conf_password.css({'border': '1px solid red', });
                conf_password.next('span.error').text("Confirm password dosen't match with password");
                error = 1;
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

        //Email input field change check for dublicate email address
        jQuery('[name="email"]').on('change', function () {
             var email = $(this);
            var atpos = email.val().indexOf("@");
            var dotpos = email.val().lastIndexOf(".");
            if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= email.val().length) {
                email.css({'border': '1px solid red', });
                email.next().text("Please enter valid email address");
                error = 1;
            }else{        
                var userid = jQuery('[name="userid"]').val();
                var base_url = "<?php echo base_url(); ?>";
                if (email.val()) {
                    $.ajax({
                        url: base_url + 'admin/Foreman/ajax_email_check_onupdate/',
                        type: 'POST',
                        dataType: 'json',
                        data: {email: email.val(), userid: userid},
                        success: function (data) {
                            if (data > 0) {
                                email.css({'border': '2px solid #FF0000'});
                                email.next('span.error').html('This email already exists.');
                                $(':input[type="submit"]').prop('disabled', true);
                                emailchk = 1;
                            } else {
                                email.css({'border': '1px solid #c5c5c5'});
                                email.next('span.error').html('');
                                emailchk = 0;
                                if(emailchk == 0 && numberchk == 0){
                                    $(':input[type="submit"]').prop('disabled', false);
                                }
                            }
                        }
                    });
                }
            }
        });

        jQuery('[name="contact"]').on('change', function () {
            var email = $(this);
            var userid = jQuery('[name="userid"]').val();
            var base_url = "<?php echo base_url(); ?>";
            if (email.val()) {
                $.ajax({
                    url: base_url + 'admin/Foreman/ajax_number_check_onupdate/',
                    type: 'POST',
                    dataType: 'json',
                    data: {email: email.val(), userid: userid},
                    success: function (data) {
                        if (data > 0) {
                            email.css({'border': '2px solid #FF0000'});
                            email.next('span.error').html('This contact already exists.');
                            $(':input[type="submit"]').prop('disabled', true);
                            numberchk = 1;
                        } else {
                            email.css({'border': '1px solid #c5c5c5'});
                            email.next('span.error').html('');
                            numberchk = 0;
                            if(emailchk == 0 && numberchk == 0){
                                $(':input[type="submit"]').prop('disabled', false);
                            }
                        }
                    }
                });
            }
        });

        $('.company_id').on('change', function () {
			var company_id = $(this).val();
            
            if (company_id) {
                loadProjectAndPlugin();
            } else {
                $('.projectlist').html(prjlist);
            }
        });
    });
    function loadProjectAndPlugin(){
        var company_id = $('.company_id').val();
        $.ajax({
            url: "<?php echo site_url('admin/Foreman/ajax_get_projectList') ?>/" + company_id,
            type: "POST",
            dataType: "JSON",
            success: function (data) {
                $('.projectlist').html(data.projectlist);
                $('#organization_name').val(data.organization)
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error Getting Project list');
            }
        });
        $.ajax({
            url: "<?php echo site_url('admin/Foreman/ajax_get_pluginList') ?>/" + company_id,
            type: "POST",
            dataType: "JSON",
            success: function (data) {
                $('.pluginList').html(data.pluginList);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error Getting Plugin list');
            }
        });
    }
</script>