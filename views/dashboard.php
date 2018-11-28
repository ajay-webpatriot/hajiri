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

    <!-- Main content -->
    <?php 
        $role = $this->session->userdata('user_designation');
        if ( $role == 'admin' )
            require_once "admin_dashboard.php";
        else if( $role == 'Superadmin')
            require_once "superadmin_dashboard.php";
		else if( $role == 'Supervisor')
            require_once "supervisor_dashboard.php";

    ?>

    <!-- /.content -->
</div>
<!-- /.content-wrapper -->