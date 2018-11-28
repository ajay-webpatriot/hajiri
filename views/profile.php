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
        <li class="active"><?php echo (isset($title) ? $title : ''); ?></li>
    </ol>

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
                                <label for="exampleInputEmail1" class="col-sm-2 noPadding control-label">First Name:</label>
                                <div class="col-sm-10">
                                <input value="<?php echo (empty($results->user_name)) ? '' : $results->user_name; ?>" name="name" placeholder="Enter First Name" type="text" class="form-control"/> 
                                <p class="help-block error"><?php echo form_error('name'); ?></p> 
                            </div>
                            </div>
                             <div class="form-group">
                                <label for="exampleInputEmail1" class="col-sm-2 noPadding control-label">Last Name:</label>
                                <div class="col-sm-10">
                                <input value="<?php echo (empty($results->user_last_name)) ? '' : $results->user_last_name; ?>" name="lname" placeholder="Enter Last Name" type="text" class="form-control"/> 
                                <p class="help-block error"><?php echo form_error('lname'); ?></p> 
                            </div>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="col-sm-2 noPadding control-label">Email:</label>
                                <div class="col-sm-10">
                                    <input value="<?php echo (empty($results->user_email)) ? '' : $results->user_email; ?>" name="email" placeholder="Enter Email" type="text" class="form-control"/> 
                                    <p class="help-block error"><?php echo form_error('email'); ?></p> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="contact" class="col-sm-2 noPadding control-label">Contact No:</label>
                                <div class="col-sm-10">
                                    <input value="<?php echo (empty($results->user_contact)) ? '' : $results->user_contact; ?>" name="contact" placeholder="Enter contact no." type="text" class="form-control"/> 
                                    <p class="help-block error"><?php echo form_error('contact'); ?></p> 
                                </div>
                            </div>
                            <div class="row">
                                <?php
                                $associatedFileNames = array('user_profile_image');
                                foreach ($associatedFileNames as $key => $fileName) {
                                    ?>

                                    <div class="form-group col-sm-12"> 
                                        <label for="inputEmail3" class="col-sm-2 control-label noPadding">Profile Photo:</label> 
                                        <div class="col-md-6">
                                            
                                            <div class="">
                                                <div class="panel-body" align="center">
                                                    <input type="file" name="<?php echo $fileName; ?>" id="upload_image" accept="image/x-png,image/jpeg,image/jpg"/>
                                                    <input type="text" name="profile_image" value="" class="hide" id='imageName'>
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
                                    <div class="form-group col-sm-12"> 
                                        <label for="inputEmail3" class="col-sm-2 control-label"></label> 
                                        <div class="col-sm-9"> 
                                            <?php $url = (empty($results->user_profile_image)) ? base_url('assets/') . 'admin/images/defuser.png' : base_url() . 'uploads/user/' . $results->user_profile_image;
                                            ?>
                                            <img src="<?php echo $url; ?>" width="100"/>
                                        </div> 
                                    </div>
                                <?php } ?>
                            </div>   
                        </div>
                        <!-- /.box-body -->
                        <div class="col-xs-6 text-center">
                            
                        </div>

                        <div class="box-footer">
                            <input type="submit" name="submit" class="btn btn-primary" value="Update Profile">
                            <a href="<?php echo base_url('admin/changePassword'); ?>" class="btn btn-warning" style="padding: 6px 4px; color: #fff; color: #fff !important;">Change Password</a>
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
        $('#profile-form').submit(function (event) {
            var exp = /^[a-zA-Z ]+$/;
            var phone_pattern = /[0-9]{10}/;
            var uname = jQuery("[name='name']");
            var lname = jQuery("[name='lname']");
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
			if (lname.val() == '') {
                lname.css({'border': '1px solid red', });
                lname.next().text("Please enter Last name");
                error = 1;
            } else {
                if (lname.val().match(exp)) {
                    lname.css({'border': '1px solid green', });
                    lname.next().text("");
                } else {
                    lname.css({'border': '1px solid red', });
                    lname.next().text("Please enter valid Last name");
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