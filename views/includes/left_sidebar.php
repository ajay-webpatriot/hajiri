<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <?php $role = $this->session->userdata('user_designation'); ?>
        
        
        <!-- search form -->
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <?php 
        	if ( $role == 'admin' )
        		require_once "admin_menu.php";
        	else if ( $role == 'Supervisor' )
        		require_once "supervisor_menu.php";
			else
        		require_once "superadmin_menu.php";
        ?>
    </section>
    <!-- /.sidebar -->
</aside>