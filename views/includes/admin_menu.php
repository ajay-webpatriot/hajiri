
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

				<?php if (in_array("9", $this->session->userdata('permissions'))){ ?>

				<li class="<?php echo ((($menu_title == 'Entry Log')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/materialLog'); ?>"><i class="fa fa-address-book-o"></i>Entry Log</a></li>
				<li class="<?php echo ((($menu_title == 'Issue Log')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/materialIssue'); ?>"><i class="fa fa-address-book-o"></i>Issue Log</a></li>
				<li class="<?php echo ((($menu_title == 'Invoice Log')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/materialInvoice'); ?>"><i class="fa fa-address-book-o"></i>Invoice Log</a></li>

				<?php
				}
				?>
			</ul>
		</li>
		<?php if (in_array("7", $this->session->userdata('permissions'))){ ?>
			<li class="<?php echo ((($menu_title == 'Kharchi')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/kharchi'); ?>"><i class="fa fa-money"></i> <span>Kharchi</span></a></li>
		<?php } ?>
		<li class="treeview <?php echo ( $menu_title == 'YearlyHolidays'  ? 'active' : '' ); ?>">
			<a href="#">
				<i class="fa fa-calendar-check-o"></i>
				<span>Holiday Calendar</span>
				<span class="pull-right-container">
					<i class="fa fa-angle-left pull-right"></i>
				</span>
			</a>
			<ul class="treeview-menu <?php echo ( $menu_title == 'YearlyHolidays' || $menu_title == 'WeeklyOff' ? 'menu open' : ''); ?>"
				<?php echo ( $menu_title == 'YearlyHolidays' || $menu_title == 'WeeklyOff' ? "style='display:block;'" : "" ); ?>>
				<li class="<?php echo ( $menu_title == 'YearlyHolidays' ? 'active' : '' ); ?>"><a href="<?php echo base_url('/admin/YearlyHolidays'); ?>"><i class="fa fa-calendar"></i>Yearly Holidays</a></li>
				<li class="<?php echo ( $menu_title == 'WeeklyOff' ? 'active' : '' ); ?>"><a href="<?php echo base_url('/admin/WeeklyOff/editWeeklyOff'); ?>"><i class="fa fa-calendar-o"></i>Weekly Off</a></li>
			</ul>
		</li>	

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
			<a href="#">
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

		<li class="treeview <?php echo ( ( $menu_title == 'Company' || $menu_title == 'Manager' || $menu_title == 'Project' || $menu_title == 'Foreman' ) ? 'active' : ''); ?>">
				<a href="#">
					<i class="fa fa-info-circle"></i>
					<span>Company Details</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu <?php echo ( ( $menu_title == 'Company' || $menu_title == 'Manager' || $menu_title == 'Project' || $menu_title == 'Foreman' ) ? 'menu open' : ''); ?>"
					<?php echo ( ( $menu_title == 'Company' || $menu_title == 'Manager' || $menu_title == 'Project' || $menu_title == 'Foreman' ) ? "style='display:block;'" : ""); ?>>
					<li class="<?php echo ((($menu_title == 'Company')) ? 'active' : ''); ?>"><a href="<?php echo base_url('admin/companies/editProfile/'); ?>"><i class="fa fa-address-card-o"></i>Profile</a></li>
					<li class="<?php echo ((($menu_title == 'Manager')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/manager'); ?>"><i class="fa fa-user-secret"></i>Admin</a></li>
					<li class="<?php echo ((($menu_title == 'Foreman')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/Foreman'); ?>"><i class="fa fa-user-o"></i>Supervisors</a></li>
					<li class="<?php echo ((($menu_title == 'Project')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/Project'); ?>"><i class="fa fa-building-o"></i>Projects</a></li>
				</ul>
		</li>
		<?php if (in_array("9", $this->session->userdata('permissions'))){ ?>
		<li class="treeview <?php echo ( ( $menu_title == 'Material Management' || $menu_title == 'Material Category' || $menu_title == 'Material' || $menu_title == 'Supplier' ) ? 'active' : ''); ?>">
				<a href="#">
					<i class="fa fa-info-circle"></i>
					<span>Material Management</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu <?php echo ( ( $menu_title == 'Material Management' || $menu_title == 'Material Category' || $menu_title == 'Material' || $menu_title == 'Supplier' ) ? 'menu open' : ''); ?>"
					<?php echo ( ( $menu_title == 'Material Management' || $menu_title == 'Material Category' || $menu_title == 'Materials' || $menu_title == 'Suppliers' ) ? "style='display:block;'" : ""); ?>>
					<li class="<?php echo ((($menu_title == 'Material Category')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/MaterialCategory'); ?>"><i class="fa fa-user-o"></i>Category</a></li>
					<li class="<?php echo ((($menu_title == 'Material')) ? 'active' : ''); ?>"><a href="<?php echo base_url('admin/material'); ?>"><i class="fa fa-address-card-o"></i>Material</a></li>
					<li class="<?php echo ((($menu_title == 'Supplier')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/supplier'); ?>"><i class="fa fa-user-secret"></i>Supplier</a></li>
				</ul>
		</li>
		<?php
		}?>
		<?php if ($this->session->userdata('plan_id') == 2){ ?>
			<li class="<?php echo ((($menu_title == 'Invoice')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/Report/invoice'); ?>"><i class="fa fa-file-text"></i>Genrate Invoice</a></li>
			<li class="<?php echo ((($menu_title == 'Upgrade')) ? 'active' : ''); ?>">
				<a href="<?php echo base_url('/admin/upgrade'); ?>">
					<i class="fa fa-check-square"></i>
					Upgrade
					<br/>
					<span style="color: red;">
						Expiry: <?php echo date("d-m-Y", strtotime($this->session->userdata('due_date'))); ?>
					</span>
				</a>
			</li>
		<?php } ?>
    </ul>

    

