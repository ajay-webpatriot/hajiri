
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo (!empty($title) ? ucwords($title) : ''); ?>
            <small><?php echo (!empty($description) ? $description : ''); ?></small>
        </h1>
    </section>
        <ol class="breadcrumb margin-bottom0">
			<li><a href="<?php echo base_url('admin'); ?>"> Dashboard</a></li>
			<li class="active"><?php echo (isset($title) ? $title : ''); ?></li>
		</ol>

    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-6 plans" id='advance'>
			<h2>Hajiri Advance</h2>
			<div class="bgWhite col-md-12">
				<div class="planImage">
					<img src="<?php echo base_url('assets/admin/images/advance_screen_2.jpg'); ?>">
				</div>
				<div class="planDetails">
					<ul>
						<li>Assign supervisors and multiple projects and admin.</li>
						<li>Centralized web portal to control multiple projects and generate reports in multiple formats</li>
						<li>Payment to multiple workers directly into their bank accounts.</li>
					</ul>
				</div>
				<div class="col-md-6 month">
					<p class="strike muted"> &#8377; <span>149</span>/Month</p>
					<p class="blue"> &#8377; <span>59</span>/Month</p>
					<form action="" method="POST" >
						<input type="text" name="amount" value='59' class="hidden">
						<input type="submit" name='submit' class="btn btn-flat btn-info" value="MONTHLY PLAN">
					</form>
				</div>
				<div class="col-md-6 yearly">
					<p class="strike muted"> &#8377; <span>1800</span>/Month</p>
					<p class="blue"> &#8377; <span>590</span>/Month</p>
					<form action="" method="POST" >
						<input type="text" name="amount" value='590' class="hidden">
						<input type="submit" name='submit' class="btn btn-flat btn-info" value="YEARLY PLAN">
					</form>
				</div>
			</div>
		</div>
		<div class="col-md-6 plans" id='enterprise'>
				<h2>Hajiri Enterprise</h2>
			<div class="bgWhite col-md-12">
				<div class="planImage">
					<img src="<?php echo base_url('assets/admin/images/Enterprise.jpg'); ?>">
				</div>
				<h3>Construction, industries, housekeeping, warehouse, mass worker developement agencies.</h3>
				<div class="planDetails">
					<p>Unlimited workers and projects</p>
					<p>Unlimited supervisors and admin login</p>
					<p>Unlimited reports</p>
					<p>Petty cash manager</p>
					<p>Direct bank payments for multiple workers</p>
					<p>ESIC and PF modules.</p>
				</div>
				<div class="proceed">
					<button class="btn btn-flat btn-success" id='entBtn'>PROCEED</button>
				</div>
			</div>
		</div>
      </div>
  	</section>

  </div>

	<script type="text/javascript">
		jQuery(function ($) {
			$('#entBtn').click(function(){
				jQuery.ajax({
	                url: "<?php echo site_url('admin/upgrade/enterprise_inquiry') ?>/" ,
	                type: "POST",
	                dataType: "JSON",
	                success: function (data) { 
	                    $('#entBtn').addClass('disabled');
	                    $('#entBtn').text('inquiry sent');
	                	if (data == 'True') 
	                		alert('inquiry sent successfully');
                		else
                			alert('Error sending inquiry');
	                },
	                error: function (jqXHR, textStatus, errorThrown) {
	                    alert('Error sending inquiry. Please try again latter.');
	                },
	                beforeSend: function () {
	                   $('#entBtn').toggleClass('btn-success','btn-default');
	                   $('#entBtn').addClass('disabled');
	                	$('#entBtn').text('Processing ..');
	                    jQuery("div#divLoading").addClass('show');
	                },
	                complete: function () {
	                   $('#entBtn').text('inquiry sent');
	                   $('#entBtn').addClass('disabled');
	                },
	            });
			});
		});
	</script>