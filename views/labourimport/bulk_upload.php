<style>
    .table-responsive{
        max-height: 350px;
    }
</style>
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
        <li class="active"><?php echo (isset($title) ? $title : ''); ?> New UI</li>
    </ol>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Horizontal Form -->
				<a href="<?php echo base_url();?>assets/admin/sample/Sample_bulk_upload_hajiri.xlsx" download="Sample_bulk_upload_hajiri.xlsx">
					<div class='downloadSheet col-md-3 col-md-push-9'>
						<div class='col-md-3'><span class='fa fa-cloud-download fa-3x'></span></div>
						<div class='col-md-9'><p>Download sample worker list.xlx</p></div>
					</div>
				</a>
				<div class='clearfix'></div>
                <div class="uploadBox">
                    
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
                    <form action="" id="import-labour" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <div class="box-body">
							<?php if( $this->session->userdata('user_designation') == 'Superadmin' ) {?>
								<div class="form-group">
									<label for="company_id" class="col-sm-3 control-label">Company:</label>
									<div class="col-sm-9">
										<select name="company_id" class="form-control Manager" id="company_id">
										   <option value="">--Select Company--</option>
											<?php
											if ( $companies ) {
												foreach ( $companies as $company ) {
													echo "<option value='" . $company->compnay_id . "'>" . $company->company_name . "</option>";
												}
											}
											?>
										</select>
										<span class="error"><?php echo (form_error('company_id')) ? form_error('company_id') : ''; ?></span>
									</div>
								</div>
							<?php } ?>
							<div class='uploadDiv'>
									<span class='fa fa-cloud-upload fa-4x'></span>
									<p>Click here to select file</p>
									<p class='fileName'></p>
								<div class='progress' id="progress_div">
									<div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="00" aria-valuemin="0" aria-valuemax="100">
										0%
									</div>
								</div>
							</div> <!-- End of uploadDiv -->
                        </div>
						<?php
										$associatedFileNames = array('image');
										foreach ($associatedFileNames as $key => $fileName) {
									?>
										<input type="file" id="<?php echo $fileName; ?>" name="<?php echo $fileName; ?>" class='selectFile hide' />
									<?php } ?>
                        <!-- /.box-body -->
                        <div class="uploadFooter">
                            <input type="submit" id="btnSave" name="submit" class="btn btn-primary" value="Upload ">
                        </div>
                        <!-- /.box-footer -->
                    </form>
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
			
						<br/>
        <?php
	        if ($this->session->flashdata('skip_rows') != '') {
		?>
            <div class="col-md-12">
			  <div class="box box-info">
				<div class="box-header with-border">
				 <h3 class="box-title">Import Summary</h3>

				  <div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>

				  </div>
				  <!-- /.box-tools -->
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<!-- Default box -->
					<?php if (!empty($this->session->flashdata('skippedData')) ) { ?>
						<div class="box">
							<div class="box-header with-border">
								<h4 class="box-title">Failed Rows:</h4>

								<div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
										<i class="fa fa-minus"></i></button>
									<button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
										<i class="fa fa-times"></i></button>
								</div>
							</div>
							<div class="box-body table-responsive">
								<table id="table" class="table table-striped table-bordered display responsive" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>First name</th>
											<th>Last name</th>
											<th>Contact</th>
											<th>Wage/Salary</th>
											<th>Category</th>
											<th>Wage Type</th>
											<th>Opening Amount</th>
										</tr>
									</thead>
									<tbody>
										<?php
											foreach ($this->session->flashdata('skippedData') as $skippedD) {

												$PHPdateValue = $joinDate = $createDate = $updateDate = "";
                                                    
												?>
												<tr>
													<td><?php echo ((isset($skippedD['A'])) ? $skippedD['A'] : ""); ?></td>
													<td><?php echo ((isset($skippedD['B'])) ? $skippedD['B'] : ""); ?></td>
													<td><?php echo ((isset($skippedD['C'])) ? $skippedD['C'] : ""); ?></td>
													<td><?php echo ((isset($skippedD['D'])) ? $skippedD['D'] : ""); ?></td>
													<td><?php echo ((isset($skippedD['E'])) ? $skippedD['E'] : ""); ?></td>
													<td><?php echo ((isset($skippedD['F'])) ? $skippedD['F'] : ""); ?></td>
													<td><?php echo ((isset($skippedD['G'])) ? $skippedD['G'] : ""); ?></td>
												</tr>
										<?php } ?>         
									</tbody>
								</table>
								<?php // }
								?>
							</div>
							<!-- /.box-body -->
							<div class="box-footer">
								<p>Total Added Rows :<b style="font-size: 16px;"> <?php echo $this->session->flashdata('insertRows'); ?></b></p>
								<p>Total Failed Rows : <b style="font-size: 16px;"><?php echo $this->session->flashdata('skip_rows'); ?></b></p>
								<p>Total Rows : <b style="font-size: 16px;"><?php echo ($this->session->flashdata('skip_rows') + $this->session->flashdata('$insertRows')); ?></b></p>
							</div>
							<!-- /.box-footer-->
						</div>
						<!-- /.box -->
					<?php } ?>
				</div>
				<!-- /.box-body -->
			  </div>
			  <!-- /.box -->
			</div>
        <?php } ?>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
    $(document).ready(function () {
        jQuery('.alert').fadeOut(3000); //remove suucess messag
	  
		$('.uploadDiv').click(function(){
			$('.selectFile').click();
		});
		$('input[type=file]').change(function(e){
		  $in=$(this);
		  $('.fileName').text(e.target.files[0].name);
		});
		var bar = $('.progress-bar');
		var percent = $('.progress-bar').next();
		
		$('#import-labour').ajaxForm({
			beforeSubmit: function() {
				document.getElementById("progress_div").style.display="block";
				var percentVal = '0%';
				bar.width(percentVal)
				bar.html(percentVal);
			},
			uploadProgress: function(event, position, total, percentComplete) {
				var percentVal = percentComplete + '%';
				bar.width(percentVal)
				bar.html(percentVal);
			},
			success: function() {
				var percentVal = '100%';
				bar.width(percentVal)
				bar.html(percentVal);
			},
			complete: function(xhr) {
				if(xhr.responseText)
				{
					location.reload();
				}
			}
		});		
	});
</script>