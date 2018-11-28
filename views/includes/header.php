<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo (defined('WEBSITE_NAME') ? WEBSITE_NAME : 'AdminLTE'); ?> | <?php echo (!empty($title) ? ucfirst($title) : 'Dashboard'); ?></title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <?php $this->load->view('includes/styles'); ?>

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

        <!--jQuery 2.2.3--> 
        <script src="<?php echo base_url('assets/admin/plugins/jQuery/jquery-2.2.3.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/admin/plugins/jQuery/jquery-form.min.js'); ?>"></script>
        <!--jQuery UI 1.11.4--> 
        <!-- script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script -->
    </head>
    <!-- END HEAD -->
    <style>
        #divLoading{display : none;}
        #divLoading.show{
            display : block;
            position : fixed;
            z-index: 100;
            background-image : url('http://loadinggif.com/images/image-selection/3.gif');
            background-color:#666;
            opacity : 0.4;
            background-repeat : no-repeat;
            background-position : center;
            left : 0;
            bottom : 0;
            right : 0;
            top : 0;
        }
        #loadinggif.show{
            left : 50%;
            top : 50%;
            position : absolute;
            z-index : 101;
            width : 32px;
            height : 32px;
            margin-left : -16px;
            margin-top : -16px;
        }
		.margin-bottom0{
			margin-bottom: 0px;
		}
    </style>