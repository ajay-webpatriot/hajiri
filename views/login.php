<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo (defined('WEBSITE_NAME')) ? WEBSITE_NAME : 'Education'; ?> | Attendance and payments</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="<?php echo base_url('assets/admin/bootstrap/css/bootstrap.min.css'); ?>">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="<?php echo base_url('assets/admin/dist/css/AdminLTE.min.css'); ?>">
        <!-- iCheck -->
        <link rel="stylesheet" href="<?php echo base_url('assets/admin/plugins/iCheck/square/blue.css'); ?>">

        <link rel="apple-touch-icon" sizes="57x57" href="<?php echo base_url('assets/admin/images/fav/apple-icon-57x57.png'); ?>">
        <link rel="apple-touch-icon" sizes="60x60" href="<?php echo base_url('assets/admin/images/fav/apple-icon-60x60.png'); ?>">
        <link rel="apple-touch-icon" sizes="72x72" href="<?php echo base_url('assets/admin/images/fav/apple-icon-72x72.png'); ?>">
        <link rel="apple-touch-icon" sizes="76x76" href="<?php echo base_url('assets/admin/images/fav/apple-icon-76x76.png'); ?>">
        <link rel="apple-touch-icon" sizes="114x114" href="<?php echo base_url('assets/admin/images/fav/apple-icon-114x114.png'); ?>">
        <link rel="apple-touch-icon" sizes="120x120" href="<?php echo base_url('assets/admin/images/fav/apple-icon-120x120.png'); ?>">
        <link rel="apple-touch-icon" sizes="144x144" href="<?php echo base_url('assets/admin/images/fav/apple-icon-144x144.png'); ?>">
        <link rel="apple-touch-icon" sizes="152x152" href="<?php echo base_url('assets/admin/images/fav/apple-icon-152x152.png'); ?>">
        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url('assets/admin/images/fav/apple-icon-180x180.png'); ?>">
        <link rel="icon" type="image/png" sizes="192x192"  href="<?php echo base_url('assets/admin/images/fav/android-icon-192x192.png'); ?>">
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url('assets/admin/images/fav/favicon-32x32.png'); ?>">
        <link rel="icon" type="image/png" sizes="96x96" href="<?php echo base_url('assets/admin/images/fav/favicon-96x96.png'); ?>">
        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/admin/images/fav/favicon-16x16.png'); ?>">
        <link rel="manifest" href="<?php echo base_url('assets/admin/images/fav/manifest.json'); ?>">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="<?php echo base_url('assets/admin/images/fav/ms-icon-144x144.png'); ?>">
        <meta name="theme-color" content="#ffffff">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <style>.error{color:red;}</style>
    <body class="hold-transition login-page">
        <div class='col-xs-12 login-logo'>
            <img src="<?php echo base_url('assets/admin/images/aasaan_logo_black.png') ?>">
        </div>
        <div class="col-md-6">
            <img class='botImage' src="<?php echo base_url('assets/admin/images/bots.png') ?>">
        </div>
        <div class="col-md-6">
            <div class="login-box">
                <div class="userIcon">
                    <img src="<?php echo base_url('assets/admin/images/user_icon.png') ?>">
                    
                </div>
                <!-- /.login-logo -->
                <div class="login-box-body">
                    <?php
                    if ($this->session->flashdata('error')) {
                        ?>
                        <div class="alert alert-danger">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close" style="color:black">&times;</a>
                            <strong>Error!</strong> <?php echo $this->session->flashdata('error'); ?>
                        </div>
                        <?php
                        $this->session->unset_userdata('error');
                    }
                    ?>
                    <?php
                    if ($this->session->flashdata('success')) {
                        ?>
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close" style="color:black">&times;</a>
                            <strong>Success!</strong> <?php echo $this->session->flashdata('success'); ?>
                        </div>
                        <?php
                        $this->session->unset_userdata('error');
                    }
                    ?>
                    <form action="" id="login-form" method="post" enctype="multipart/form-data">
                        <div class="form-group has-feedback">
                            <input type="email" class="form-control" placeholder="Email" value="<?php echo (isset($_POST['username']) ? $_POST['username'] : ''); ?>" name="username">
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            <span class="error"><?php echo (form_error('username')) ? form_error('username') : ''; ?></span>
                        </div>
                        <div class="form-group has-feedback">
                            <input type="password" class="form-control" autocomplete="off" placeholder="Password" name="password">
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            <span class="error"><?php echo (form_error('password')) ? form_error('password') : ''; ?></span>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <input type="submit" class="btn btn-success btn-block btn-flat" name="submit" value="LOGIN"/>
                                <p class="fp">Forgot Password ?</p>
                            </div>
                        </div>
                    </form>
                    <div class="fp-div hide">
                        <div class="form-group has-feedback">
                            <input type="email" name="recEmail" placeholder="Enter registered email id" class="form-control" id='recEmail'>
                            <span class="error"></span>
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <button  class="btn btn-info btn-block btn-flat" name="Recover" id='recover'>
                                Recover
                            </button>
                        </div>
                    </div>

                    <div class="otp-div hide">
                        <div class="form-group has-feedback">
                            <div class="alert alert-info" role="alert">
                                <p>We have sent OTP to your register email id.</p>
                            </div>
                        </div>
                        <div class="form-group has-feedback">
                            <input type="number" name="otp" id='otp' placeholder="Enter OTP" class="form-control" min='1000' max='9999'>
                            <span class="error"></span>
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <p>Didn't recived OTP?</p>
                            <button  class="btn btn-info btn-block btn-flat" name="Resend" id="otpResend">
                                Resend 
                                <span id="countdown"></span>
                            </button>
                        </div>
                        <div class="form-group has-feedback">
                            <button  class="btn btn-flat btn-block btn-success" name="Otp" id="otpSubmit">
                                Submit
                            </button>
                        </div>
                    </div>

                    <div class="cp-div hide">
                        <form id="frmRecover">
                            <div class="form-group has-feedback">
                                <input type="password" name="password" placeholder="New password" id="pass" class="form-control" minLength="6" required>
                                <span class="error"></span>
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            </div>
                            <div class="form-group has-feedback">
                                <input type="password" placeholder="Confirm new password" id="passC" class="form-control" minLength="6" required>
                                <span class="error"></span>
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            </div>
                            <div class="form-group has-feedback">
                                <input type="submit"  class="btn btn-flat btn-block btn-success" name="submit" id="npSubmit" value="Update password">
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /.login-box-body -->
                <div class="userIcon">
                    <img src="<?php echo base_url('assets/admin/images/line.png') ?>">
                    
                </div>
            </div>
            <!-- /.login-box -->
        </div>
        <div class="col-xs-12 copyRight">
            <strong>Copyright &copy;<?php echo (date('Y') - 1 ) . '-' . date('Y'); ?> <a href="<?php echo base_url();?>">Aasaan Tech Pvt. Ltd. </a> </strong> All rights
        reserved.
        </div>
        <!-- jQuery 2.2.3 -->
        <script src="<?php echo base_url('assets/admin/plugins/jQuery/jquery-2.2.3.min.js'); ?>"></script>
        <!-- Bootstrap 3.3.6 -->
        <script src="<?php echo base_url('assets/admin/bootstrap/js/bootstrap.min.js'); ?>"></script>
        <!-- iCheck -->
        <script src="<?php echo base_url('assets/admin/plugins/iCheck/icheck.min.js'); ?>"></script>
        <script>
            var OTP;
            $(function () {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%' // optional
                });
            });
            $(document).ready(function () {
            var base_url = "<?php echo base_url(); ?>";

                $('.fp').click(function (){
                    $('#login-form').addClass("hide");
                    $('.fp-div').removeClass("hide");
                });

                $('#recover').click(function (){
                    //Check if input is valid email
                    $('#recover').text('Checking..');
                    $("#recover").addClass('disabled');
                    var emailParam = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
                    if (emailParam.test($('#recEmail').val())){
                        OTP = Math.floor(1000 + Math.random() * 9000);                      
                        $.ajax({
                            url: base_url + 'admin/ajax_email_check/',
                            type: 'POST',
                            dataType: 'json',
                            data: {email: $('#recEmail').val()},
                            success: function (data) {
                                if (data.status == true) {
                                    $.ajax({
                                        url: base_url + 'admin/ajax_forgot_pass/',
                                        type: 'POST',
                                        dataType: 'json',
                                        data: {email: $('#recEmail').val(), otp: OTP},
                                        success: function (data) {
                                            if (data.status == true) {
                                                $('.fp-div').addClass("hide");
                                                $('.otp-div').removeClass("hide");
                                                $("#otpResend").addClass('disabled');
                                                countDown();
                                            } else {
                                                $('#recover').text('Recover');
                                                $("#recover").removeClass('disabled');
                                                $('#recEmail').next().text('Error in sending OTP via Email.');
                                            }
                                        }
                                    });
                                } else {
                                    $('#recover').text('Recover');
                                    $("#recover").removeClass('disabled');
                                    $('#recEmail').css({'border': '1px solid red', });
                                    $('#recEmail').next().text('This email id is not registerd.');
                                }
                            }
                        });              
                    }
                    else{
                        $('#recEmail').css({'border': '1px solid red', });
                        $('#recEmail').next().text("Please enter valid email");
                        $('#recover').text('Recover');
                        $("#recover").removeClass('disabled')
                    }
                });
                $('#otpResend').click(function (){ 
                    OTP = Math.floor(1000 + Math.random() * 9000); 
                    $('#otpResend').text('Sending..');
                    $("#otpResend").addClass('disabled'); 
                    $.ajax({
                        url: base_url + 'admin/ajax_forgot_pass/',
                        type: 'POST',
                        dataType: 'json',
                        data: {email: $('#recEmail').val(), otp: OTP},
                        success: function (data) {
                            if (data.status == true) {
                                countDown();
                                $('#otpResend').text('Resend');
                                $("#otpResend").addClass('disabled');
                            } else {
                                $('#otpResend').text('Resend');
                                $("#otpResend").removeClass('disabled'); 
                                $('#recEmail').next().text('Error in sending OTP via Email.');
                            }
                        }
                    });
                });
                $('#otpSubmit').click(function (){
                    $('#otpSubmit').text('Checking..');
                    $("#otpSubmit").addClass('disabled');
                    if(OTP == $('#otp').val()){
                        $('.otp-div').addClass("hide");
                        $('.cp-div').removeClass("hide");
                    }else{
                        $('#otpSubmit').text('Submit');
                        $("#otpSubmit").removeClass('disabled');
                        $('#otp').css({'border': '1px solid red', });
                        $('#otp').next().text('Error, OTP entered is invalid.');
                    }
                });

                $('#frmRecover').on('submit', function(e){
                    e.preventDefault();
                    $('#npSubmit').text('Checking..');
                    $("#npSubmit").addClass('disabled');
                    if($('#pass').val() == $('#passC').val()){
                        $.ajax({
                            url: base_url + 'admin/ajax_update_pass/',
                            type: 'POST',
                            dataType: 'json',
                            data: {password: $('#pass').val(), email: $('#recEmail').val()},
                            success: function (data) {
                                if (data.status == true) {
                                    location.reload();
                                } else {
                                    $('#passC').next().text('Error in updating password. Please try again in sometime.');
                                }
                            }
                        });
                    }else{
                        $('#npSubmit').text('Update Password');
                        $("#npSubmit").removeClass('disabled');
                        $('#pass').css({'border': '1px solid red', });
                        $('#passC').css({'border': '1px solid red', });
                        $('#passC').next().text('Password do not match.');
                    }
                });

                $('#login-form').submit(function (event) {
                    var username = $('[name="username"]');
                    var password = $('[name="password"]');
                    var error = 0;
                    if (username.val() == '') {
                        username.css({'border': '1px solid red', });
                        username.next().next().text("Please enter Email id");
                        error = 1;
                    } else {
                        username.css({'border': '1px solid green', });
                        username.next().next().text("");
                    }
                    if (password.val() == '') {
                        password.css({'border': '1px solid red', });
                        password.next().next().text("Please enter password");
                        error = 1;
                    } else {
                        password.css({'border': '1px solid green', });
                        password.next().next().text("");
                    }
                    if (error > 0) {
                        event.preventDefault();
                    }
                });
            });
            function countDown(){
                var counter = 60;
                var interval = setInterval(function() {
                    counter--;
                    // Display 'counter' wherever you want to display it.
                    $('#countdown').html(counter);
                        if (counter == 0) {
                            $("#otpResend").removeClass('disabled');
                            $('#countdown').html('');
                            clearInterval(interval);
                        }
                }, 1000);
            }
        </script>
    </body>
</html>
