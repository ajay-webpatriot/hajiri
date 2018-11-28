<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo (!empty($title) ? ucwords($title) : ''); ?>
            <small><?php echo (!empty($description) ? $description : ''); ?></small>
        </h1>
        <?php //echo (!empty($breadcrumb) ? $breadcrumb : ''); ?>
    </section>
	<ol class="breadcrumb margin-bottom0">
		<li><a href="<?php echo base_url('admin'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
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
                    ?> <?php //print_r( $results->company_name ); exit;?>
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
                                <label class="col-sm-3 control-label for="exampleInputEmail1">Address:</label>
								<div class="col-sm-9">
									<textarea name="company_address" placeholder="Enter Address" class="form-control"><?php echo (empty($results->company_address)) ? '' : $results->company_address ?> </textarea> 
									<p class="help-block error"><?php echo form_error('company_address'); ?></p> 
								</div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="exampleInputEmail1">Pincode:</label>
								<div class="col-sm-9">
									<input value="<?php echo (empty($results->company_pincode)) ? '' : $results->company_pincode; ?>" name="company_pincode" placeholder="Enter Pincode." type="number" class="form-control" maxlength="6"/> 
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
                                    <div class="form-group col-sm-6">
                                        <label for="<?php echo $fileName ?>" class="col-lg-4 control-label">Company logo:</label>

                                        <div class="col-lg-8">
                                            
                                            <div class="">
                                                <div class="panel-body" align="center">
                                                    <input type="file" name="<?php echo $fileName; ?>" id="upload_image" accept="image/x-png,image/jpeg,image/jpg"/>
                                                    <input type="text" name="company_logo" value="" class="hide" id='imageName'>
                                                    <br />
                                                    <div id="uploaded_image"></div>
                                                </div>
                                            </div>
                                            <div id="uploadimageModal" class="modal" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Upload & Crop Image</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-12 text-center">
                                                                      <div id="image_demo" style=" margin-top:30px"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button class="btn btn-success crop_image">Crop & Upload Image</button>
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                    <?php if (!empty($results->company_logo_image)) { ?>
                                        <div class="form-group col-sm-12">
                                            <label for="<?php echo $fileName ?>" class="col-lg-2 control-label"></label>
                                            <div class="col-lg-8">
                                                <img src=" <?php echo $url = (empty($result->company_logo_image)) ? base_url() . 'uploads/user/' . $results->company_logo_image : base_url('assets/') . 'admin/pages/img/defmanager.png';?>" width="100px" class="image"/>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <input type="submit" id="btnSave" name="profileSubmit" class="btn btn-primary" value="Save">
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
        //Check file selected is image
        $('INPUT[type="file"]').change(function () {
            var ext = this.value.match(/\.(.+)$/)[1];
            switch (ext) {
                case 'jpg':
                case 'jpeg':
                case 'png':
                    break;
                default:
                    alert('This is not an allowed file type.');
                    this.value = '';
            }
        });
        //Image crop plugin
        var base_url = "<?php echo base_url(); ?>";

        $image_crop = $('#image_demo').croppie({
        enableExif: true,
        viewport: {
          width:200,
          height:200,
          type:'square' //circle
        },
        boundary:{
          width:300,
          height:300
        }
      });

      $('#upload_image').on('change', function(){
        var reader = new FileReader();
        reader.onload = function (event) {
          $image_crop.croppie('bind', {
            url: event.target.result
          }).then(function(){
            console.log('jQuery bind complete');
          });
        }
        reader.readAsDataURL(this.files[0]);
        $('#uploadimageModal').modal('show');
      });

      $('.crop_image').click(function(event){
        event.preventDefault();
        $image_crop.croppie('result', {
          type: 'canvas',
          size: 'viewport'
        }).then(function(response){
          $.ajax({
            url: base_url + 'admin/uploadImage/',
            type: "POST",
            data:{"image": response},
            success:function(data)
            {
              $('#uploadimageModal').modal('hide');
              $('#uploaded_image').html('<img src="'+base_url+'uploads/user/'+data+'" class="img-thumbnail" />');
              $('#imageName').val(data);
            }
          });
        })
      });// End of image crop plugin 


        $('#frmCompany').submit(function (event) {
			boolReturn = true;
			
            var exp = /^[a-zA-Z ]+$/;
            var phone_pattern = /[0-9]{10}/;
            var uname = jQuery("[name='company_name']");
            var email = jQuery("[name='company_email']");
            var contact = jQuery("[name='company_contact_no']");
            var gst = jQuery("[name='company_gst']");
            var pan = jQuery("[name='company_pan']");

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
            if (gst.val() != '' && gst.val().length != 15) {
                gst.css({'border': '1px solid red', });
                gst.next().text("Please enter correct GST number");
                boolReturn = false;
            }
            if (pan.val() != '' && pan.val().length != 10) {
                pan.css({'border': '1px solid red', });
                pan.next().text("Please enter correct PAN number");
                boolReturn = false;
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
			return boolReturn;
			
        });
    });
    jQuery(document).ready(function ($) {
        jQuery('[name="email"]').on('blur', function () {
            var email = $(this);
            var userid = "<?php echo $this->session->userdata('id') ?>";
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