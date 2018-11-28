<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

        <header class="main-header">
            <!-- Logo -->
            <!-- <a href="http://aasaan.co/hajiri/" class="logo">
                mini logo for sidebar mini 50x50 pixels
                <span class="logo-mini">
                    <img src="<?php echo base_url('assets/admin/images/square_50_logo_aasaan.png') ?>" width="100%">
                </span>
                logo for regular state and mobile devices
                <span class="logo-lg">
                    <img src="<?php echo base_url('assets/admin/images/logo_aasaan_menu.png') ?>" width="100%">
                </span>
            </a> -->
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <a href="<?php echo base_url('admin') ?>" title='Hajiri landig page'>
                    <h3> The hajiri app</h3>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- Messages: style can be found in dropdown.less-->
                        <!-- Notifications: style can be found in dropdown.less -->
                       
                        <!-- Tasks: style can be found in dropdown.less -->
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <?php $defaultadmin = base_url('assets/admin/dist/img/user2-160x160.png'); ?>
                                <img src="<?php echo ($this->session->userdata('image')) ? base_url() . 'uploads/user/' . $this->session->userdata('image') : $defaultadmin; ?>" class="user-image">
                                <span class="hidden-xs"><?php echo ($this->session->userdata('name')) ? $this->session->userdata('name') : ' '; ?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header">
                                    <img src="<?php echo ($this->session->userdata('image')) ? base_url() . 'uploads/user/' . $this->session->userdata('image') : $defaultadmin; ?>" class="img-circle" alt="User Image">

                                    <p>
                                        <?php echo ($this->session->userdata('name')) ? $this->session->userdata('name') : ' '; ?>

                                    </p>
                                </li>
                                <!-- Menu Body -->
                                <li class="">
                                    <div class="col-xs-12 noPadding userDropDown">
                                        <div class="col-xs-6 text-center noPadding">
                                            <a href="<?php echo base_url('admin/profile'); ?>" class="btn btn-success btn-block">Profile</a>
                                        </div><!-- 
                                        <div class="col-xs-6 text-center">
                                            <a href="<?php echo base_url('admin/changePassword'); ?>" class="btn btn-warning" style="padding: 6px 4px; color: #fff; color: #fff !important;">Change Password</a>
                                        </div> -->
                                        <div class="col-xs-6 text-center noPadding">
                                            <a href="<?php echo base_url('admin/logout'); ?>" class="btn btn-danger btn-block">Logout</a>
                                        </div>
                                    </div>
                                    <!-- /.row -->
                                </li>
                       
                        </ul>
                    </li>
                    <!-- Control Sidebar Toggle Button -->

                    </ul>
                </div>
            </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->