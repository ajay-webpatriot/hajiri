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
                    <form action="" id="edit-manager" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <div class="box-body">
                            <input type="hidden" name="user_id" value="<?php echo (isset($result->user_id)) ? $result->user_id : ''; ?>">
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Name:</label>
                                <div class="col-sm-9">
                                    <input name="name" placeholder="Name" class="form-control" type="text" value="<?php echo (isset($result->user_name)) ? $result->user_name : ''; ?>">
                                    <span class="error"><?php echo (form_error('name')) ? form_error('name') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Last Name:</label>
                                <div class="col-sm-9">
                                    <input name="lname" placeholder="Last Name" class="form-control" type="text" value="<?php echo (isset($result->user_last_name)) ? $result->user_last_name : ''; ?>">
                                    <span class="error"><?php echo (form_error('lname')) ? form_error('lname') : ''; ?></span>
                                </div>
                            </div>
							
							<?php if( $this->session->userdata('user_designation') == 'Superadmin' ) {?>
							<div class="form-group">
                                <label for="company_id" class="col-sm-3 control-label">Company:</label>
                                <div class="col-sm-9">
                                    <select name="company_id" class="form-control company_id">
										<?php 
											foreach( $companies as $company ){ 
												$selected = '';
												if( isset( $result->company_id ) && $company->compnay_id == $result->company_id ){
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
                                    <input name="email" placeholder="Email Address" class="form-control" type="text" value="<?php echo (isset($result->user_email)) ? $result->user_email : ''; ?>">
                                    <span class="error"><?php echo (form_error('email')) ? form_error('email') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Contact Number:</label>
                                <div class="col-sm-9">
                                    <input name="contact" placeholder="Contact Number" class="form-control" type="text" value="<?php echo (isset($result->user_contact)) ? $result->user_contact : ''; ?>">
                                    <span class="error"><?php echo (form_error('contact')) ? form_error('contact') : ''; ?></span>
                                </div>
                            </div>
							<div class="form-group">
                                <label for="pid" class="col-sm-3 control-label">Project:</label>
                                <div class="col-sm-9">
                                    <div class="projectList checkBoxList"></div>
                                    <span class="error"><?php echo (form_error('pid[]')) ? form_error('pid[]') : ''; ?></span>
                                    <span class="text-danger">Please restart the app to see changes.</span>
                                </div>
                            </div>							
							<div class="form-group">
                                <label for="plugin_id" class="col-sm-3 control-label">Set permissions: </label>
                                <div class="col-sm-9">
                                    <div class="pluginList checkBoxList"></div>
                                    <span class="error"><?php echo (form_error('plugin[]')) ? form_error('plugin[]') : ''; ?></span>
                                    <span class="text-danger">Please restart the app to see changes.</span>
                                </div>
                            </div>
							<div class="form-group">
                                <label for="access" class="col-sm-3 control-label">Set Access:</label>
                                <div class="col-sm-9">
                                    <select class='form-control' name='access'>
										<option value='0' <?php echo ($result->portal_access == 0 ? "selected" : "") ?>>Web access </option>
										<option value='1' <?php echo ($result->portal_access == 1 ? "selected" : "") ?>>Android access </option>
										<option value='2' <?php echo ($result->portal_access == 2 ? "selected" : "") ?>>Web and Android access </option>
									</select>
                                    <span class="error"><?php echo (form_error('access')) ? form_error('access') : ''; ?></span>
                                    <span class="text-danger">Please ask user to restart the app to see changes.</span>
                                </div>
                            </div>
                            <div class="col-sm-offset-1 col-sm-11">
                                <?php
                                $associatedFileNames = array('image');
                                foreach ($associatedFileNames as $key => $fileName) {
                                    ?>
                                    <div class="form-group col-sm-6">
                                        <label for="<?php echo $fileName ?>" class="col-lg-4 control-label">Profile photo:</label>

                                        <div class="col-lg-8">
                                            <input type="file" id="<?php echo $fileName; ?>" name="<?php echo $fileName; ?>"/>
                                            <p class="help-block error"><?php echo array_key_exists($fileName, $fileError) ? $fileError[$fileName] : ''; ?></p>

                                        </div>
                                    </div>
                                    <?php if (!empty($result->user_profile_image)) { ?>
                                        <div class="form-group col-sm-12">
                                            <label for="<?php echo $fileName ?>" class="col-lg-2 control-label"></label>
                                            <div class="col-lg-8">
                                                <img src=" <?php echo $url = (empty($result->user_profile_image)) ? base_url('assets/') . 'admin/pages/img/defmanager.png' : base_url() . 'uploads/user/' . $result->user_profile_image;
                                        ?>" height="100" width="100" class="image"/>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="form-group">
                                <label for="status" class="col-sm-3 control-label">Status:</label>
                                <div class="col-sm-9">
                                    <select name="status" class="form-control activeSel">
                                        <option value="1" <?php echo ($result->status == "1") ? 'selected="selected"' : ''; ?>>Active</option>
                                        <option value="2" <?php echo ($result->status == "2") ? 'selected="selected"' : ''; ?>>In Active</option>
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
    jQuery(document).ready(function () {

        var emailchk = 0;
        var numberchk = 0;
        $("[data-mask]").inputmask();
        loadProjectAndPlugin();

        jQuery('#edit-manager').submit(function (event) {
            var exp = /^[a-zA-Z ]+$/;
            var phone_pattern = /^\d{10}$/;
            var aadhar_pattern = /^([0-9]{4}-){2}[0-9]{4}$/;
            var userid = jQuery('[name="userid"]').val();
            var uname = jQuery("[name='name']");
            var email = jQuery("[name='email']");
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
					$check = checkEmail(email);
					if($check == 1){
						email.css({'border': '1px solid red', });
						email.next().text("This email id already exist.");
						event.preventDefault();
						error = 1;
					}
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
					$check = checkNumber(contact);
					if($check == 1){
						contact.css({'border': '1px solid red', });
						contact.next().text("This Contact no already exist.");
						event.preventDefault();
						error = 1;
					}
                } else {
                    contact.css({'border': '1px solid red', });
                    contact.next().text("Please enter valid contact number");
                    error = 1;
                }
            }
            if (aadhar_id.val() != '') {
                if (aadhar_id.val().match(aadhar_pattern)) {
                    aadhar_id.css({'border': '1px solid green', });
                    aadhar_id.next().text("");
                } else {
                    aadhar_id.css({'border': '1px solid red', });
                    aadhar_id.next().text("Please enter valid aadhar number e.g.(xxxx-xxxx-xxxx)");
                    error = 1;
                }
            }
            if (organization_name.val() == '') {
                organization_name.css({'border': '1px solid red', });
                organization_name.next().text("Please enter organization name");
                error = 1;
            } else {
                organization_name.css({'border': '1px solid green', });
                organization_name.next().text("");
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
		
		
        jQuery('[name="email"]').on('change', function () {
            var email = $(this);
            checkEmail(email);
        });

        jQuery('[name="contact"]').on('change', function () {
            var contact = $(this);
            checkNumber(contact);
        });


        $('.company_id').on('change', function () {
			var company_id = $(this).val();
            
            if (company_id) {
                loadProjectAndPlugin();
            } else {
                var prjlist = '<p>No Projects found </p>';
                $('.projectlist').html(prjlist);
            }
        });
    });

    function loadProjectAndPlugin(){
        var company_id = $('.company_id').val();
        $.ajax({
            url: "<?php echo site_url('admin/Foreman/ajax_get_selected_projectList') ?>",
            type: "POST",
            dataType: "JSON",
            data:  {
                    'user_id'    : <?php echo $result->user_id;?>,
                    'company_id' :  company_id,
                },
            success: function (data) {
                $('.projectList').html(data);
                $('#organization_name').val(data.organization)
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('.projectList').html('Error Getting Project list');
            }
        });
        $.ajax({
            url: "<?php echo site_url('admin/Foreman/ajax_get_selected_pluginList') ?>",
            type: "POST",
            dataType: "JSON",
            data:  {
                    'user_id'    : <?php echo $result->user_id;?>,
                    'company_id' :  company_id,
                },
            success: function (data) {
                $('.pluginList').html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('.pluginList').html('Error Getting Plugin list');
            }
        });
    }

	
    function checkEmail(email){
        var base_url = "<?php echo base_url(); ?>";
        if (email.val()) {
            $.ajax({
                url: base_url + 'admin/Foreman/ajax_email_check_onupdate/',
                type: 'POST',
                dataType: 'json',
                data: {email: email.val()},
                success: function (data) {
                    if (data > 0) {
                        email.css({'border': '2px solid #FF0000'});
                        email.next('span.error').html('This email already exists.');
                        $(':input[type="submit"]').prop('disabled', true);
                        return 1;
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

    function checkNumber(email){
        var base_url = "<?php echo base_url(); ?>";
        if (email.val()) {
            $.ajax({
                url: base_url + 'admin/Foreman/ajax_number_check_onupdate/',
                type: 'POST',
                dataType: 'json',
                data: {email: email.val()},
                success: function (data) {
                    if (data > 0) {
                        email.css({'border': '2px solid #FF0000'});
                        email.next('span.error').html('This contact already exists.');
                        $(':input[type="submit"]').prop('disabled', true);
                        numberchk = 1;
                        return 1;
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
    }
</script>