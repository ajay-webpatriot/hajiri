
	<div class="user-panel companyLogo">
        <div class="image">
            <?php 
            	$defaultadmin = base_url('assets/admin/dist/img/user2-160x160.png'); 
            	if($this->session->userdata('company_logo') != ''){
            		$companyLogo = base_url("uploads/user/".$this->session->userdata('company_logo'));
            ?>
            		<img src="<?php echo($companyLogo) ? $companyLogo : $defaultadmin; ?>" alt="Manager Image" />
            <?php } ?>
        </div>
        <div class="info">
            <p><?php echo ($this->session->userdata('company_name')) ? $this->session->userdata('company_name') : 'Company name'; ?></p>
        </div>
    </div>

	<ul class="sidebar-menu">
		
		<li class="<?php echo ((($menu_title == 'Admin')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin'); ?>"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
		
		<li class="treeview <?php echo ( ( $menu_title == 'Register') || ($menu_title == 'Attendance register') || ($menu_title == 'Worker register') ? 'active' : ''); ?>">
			<a href="#">
				<i class="fa fa-book"></i>
				<span>Register</span>
				<span class="pull-right-container">
					<i class="fa fa-angle-left pull-right"></i>
				</span>
			</a>
			<ul class="treeview-menu <?php echo ( ( $menu_title == 'Attendance register' || $menu_title == 'Worker register') ? 'menu open' : ''); ?>"
				<?php echo ( ( $menu_title == 'Attendance register' || $menu_title == 'Worker register' ) ? "style='display:block;'" : ""); ?>>
				<li class="<?php echo ((($menu_title == 'Attendance register')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/attendanceRegister'); ?>"><i class="fa fa-check-square"></i>Attendance register</a></li>
				<li class="<?php echo ((($menu_title == 'Worker register')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/workerRegister'); ?>"><i class="fa fa-address-book-o"></i>Worker register</a></li>
			</ul>
		</li>	
		
		<?php if (in_array("7", $this->session->userdata('permissions'))){ ?>
			<li class="<?php echo ((($menu_title == 'Kharchi')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/kharchi'); ?>"><i class="fa fa-money"></i> <span>Kharchi</span></a></li>
		<?php } ?>
		<?php if (in_array("6", $this->session->userdata('permissions'))){ ?>
			<li class="treeview <?php echo ( ( $menu_title == 'monthlyPayment' || $menu_title == 'monthlyLabour' || $menu_title == 'Kharchi' || $menu_title == 'monthlyAttendance' ) ? 'active' : ''); ?>">
				<a href="#">
					<i class="fa  fa-file-text-o"></i>
					<span>Reports</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu <?php echo ( ( $menu_title == 'monthlyPayment' || $menu_title == 'Attendance' || $menu_title == 'Kharchi' || $menu_title == 'monthlyLabour' ) ? 'menu open' : ''); ?>"
					<?php echo ( ( $menu_title == 'Payment' || $menu_title == 'Attendance' || $menu_title == 'Kharchi' || $menu_title == 'Workerwise' ) ? "style='display:block;'" : ""); ?>>
					<li class="<?php echo ((($menu_title == 'monthlyPayment')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/Report/monthly_payment_report'); ?>"><i class="fa fa-money"></i>Payment</a></li>
					<li class="<?php echo ((($menu_title == 'monthlyAttendance')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/Report/monthly_attendance_report'); ?>"><i class="fa fa-check-square-o"></i>Attendance</a></li>
					<?php if (in_array("7", $this->session->userdata('permissions'))){ ?>
					<li class="<?php echo ((($menu_title == 'Kharchi')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/Report/kharchi_report'); ?>"><i class="fa fa-file-text"></i>Kharchi</a></li>
					<?php } ?>
					<li class="<?php echo ((($menu_title == 'monthlyLabour')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/Report/monthly_labour_report'); ?>"><i class="fa fa-user-circle-o"></i>Workerwise</a></li>
				</ul>
			</li>
		<?php }?>
		<li class="treeview <?php echo ( ( $menu_title == 'Qr Stamp' ||  $menu_title == 'Generate_idcards' || $menu_title == 'Labourimport' ) ? 'active' : ''); ?>">
			<a href="">
				<i class="fa fa-qrcode"></i>
				<span>QR Codes</span>
				<span class="pull-right-container">
					<i class="fa fa-angle-left pull-right"></i>
				</span>
			</a>
			<ul class="treeview-menu <?php echo ( ( $menu_title == 'Qr Stamp' ||  $menu_title == 'Generate_idcards' || $menu_title == 'Labourimport' ) ? 'menu open' : ''); ?>"
				<?php echo ( ( $menu_title == 'Qr Stamp' ||  $menu_title == 'Generate_idcards' || $menu_title == 'Clean Barcode' ) ? "style='display:block;'" : ""); ?>>
				<li class="<?php echo ( $menu_title == 'Qr Stamp' ||  $menu_title == 'Generate_idcards' ) ? 'active' : ''; ?>"><a href="<?php echo base_url('/admin/qr_codes'); ?>"><i class="fa fa-download"></i>Generate QR code</a></li>
				<li class="<?php echo ( $menu_title == 'Labourimport' ) ? 'active' : ''; ?>"><a href="<?php echo base_url('/admin/report/create_clean_qrcode'); ?>"><i class="fa fa-file-o"></i>Blank QR Code</a></li>
			</ul>
		</li>
		<?php if (in_array("5", $this->session->userdata('permissions'))){ ?>
			<li class="treeview <?php echo ( ( $menu_title == 'Worker setting') || ($menu_title == 'Edit worker') || ($menu_title == 'Add worker') || ($menu_title == 'Bulk worker upload') || ($menu_title == 'category' ) ? 'active' : ''); ?>">
				<a href="#">
					<i class="fa fa-cogs"></i>
					<span>Worker setting</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>			
				<ul class="treeview-menu <?php echo ( ( $menu_title == 'Add worker') || ($menu_title == 'Edit worker')  || ($menu_title == 'Bulk worker upload') || ($menu_title == 'category') ? 'menu open' : ''); ?>">
					<li class="<?php echo ((($menu_title == 'Add worker')  ) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/workerRegister/worker'); ?>">
					<i class="fa fa-user-plus"></i>Add worker</a></li>
					
					<li class="<?php echo ((($menu_title == 'category')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/category'); ?>"><i class="fa fa-list"></i>Add worker category</a></li>
        
					<li class="<?php echo ((($menu_title == 'Bulk worker upload')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/labourimport/importLabourFile'); ?>"><i class="fa fa-cloud-upload"></i>Bulk worker upload</a></li>
				</ul>
			</li>
		<?php } ?>

    </ul>

