<ul class="sidebar-menu">
		
			<li class="<?php echo ((($menu_title == 'Admin')) ? 'active' : ''); ?>">
				<a href="<?php echo base_url('/admin'); ?>"><i class="fa fa-dashboard"></i>
					<span>Dashboard</span>
				</a>
			</li>
			
			<li class="treeview <?php echo ( ( $menu_title == 'Qr Stamp' ||  $menu_title == 'Generate_idcards' || $menu_title == 'Create Clean Barcode' ) ? 'active' : ''); ?>">
				<a href="<?php echo base_url('/admin/report/labour_barcode_report'); ?>">
					<i class="fa fa-qrcode"></i>
					<span>QR Codes</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu <?php echo ( ( $menu_title == 'Qr Stamp' ||  $menu_title == 'Generate_idcards' || $menu_title == 'Clean Barcode' ) ? 'menu open' : ''); ?>"
					<?php echo ( ( $menu_title == 'Qr Stamp' ||  $menu_title == 'Generate_idcards' || $menu_title == 'Clean Barcode' ) ? "style='display:block;'" : ""); ?>>
					<li class="<?php echo ( $menu_title == 'Qr Stamp' ||  $menu_title == 'Generate_idcards' ) ? 'active' : ''; ?>"><a href="<?php echo base_url('/admin/qr_codes'); ?>"><i class="fa fa-download"></i>Generate QR code</a></li>
					<li class="<?php echo ( $menu_title == 'Clean Barcode' ) ? 'active' : ''; ?>"><a href="<?php echo base_url('/admin/report/create_clean_qrcode'); ?>"><i class="fa fa-file-o"></i>Blank QR Code</a></li>
				</ul>
			</li>
			
			<li class="treeview <?php echo ( ( $menu_title == 'Company' || $menu_title == 'Manager' || $menu_title == 'Project' || $menu_title == 'Foreman' ) ? 'active' : ''); ?>">
				<a href="<?php echo base_url('/admin/Companies'); ?>">
					<i class="fa fa-info-circle"></i>
					<span>Company Details</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu <?php echo ( ( $menu_title == 'Company' || $menu_title == 'Manager' || $menu_title == 'Project' || $menu_title == 'Foreman' ) ? 'menu open' : ''); ?>"
					<?php echo ( ( $menu_title == 'Company' || $menu_title == 'Manager' || $menu_title == 'Project' || $menu_title == 'Foreman' ) ? "style='display:block;'" : ""); ?>>
					<li class="<?php echo ((($menu_title == 'Company')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/companies'); ?>"><i class="fa fa-industry"></i>Companies</a></li>
					<li class="<?php echo ((($menu_title == 'Manager')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/manager'); ?>"><i class="fa fa-user-secret"></i>Admin</a></li>
					<li class="<?php echo ((($menu_title == 'Foreman')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/Foreman'); ?>"><i class="fa fa-user-o"></i>Supervisors</a></li>
					<li class="<?php echo ((($menu_title == 'Project')) ? 'active' : ''); ?>"><a href="<?php echo base_url('/admin/Project'); ?>"><i class="fa fa-building-o"></i>Projects</a></li>
				</ul>
			</li>
			<li class="<?php echo ((($menu_title == 'Hajiri SMS')) ? 'active' : ''); ?>">
				<a href="<?php echo base_url('/admin/hajiriSms'); ?>"><i class="fa fa-envelope-open-o"></i>
					<span>Hajiri SMS</span>
				</a>
			</li>
        </ul>