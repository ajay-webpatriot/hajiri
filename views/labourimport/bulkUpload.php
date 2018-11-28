<style>
    .table-responsive{
        max-height: 350px;
    }
</style>
<?php 

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo (!empty($title) ? ucwords($title) : ''); ?> UI 2
        </h1>
    </section>
    <ol class="breadcrumb margin-bottom0">
        <li><a href="<?php echo base_url('admin'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active"><?php echo (isset($title) ? $title : ''); ?></li>
    </ol>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8 col-offset-2">
                <!-- Horizontal Form -->
                <div class="box box-info">
                    <div class="box-header with-border">
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
										<select name="company_id" class="form-control Manager" id="company_id" required >
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
                            <div class="form-group">
                                <?php
                                $associatedFileNames = array('image');
                                foreach ($associatedFileNames as $key => $fileName) {
                                    ?>
                                    <label for="<?php echo $fileName ?>" class="col-sm-3 control-label">Import File:</label>

                                    <div class="col-sm-9">
                                        <input style="padding-top:5px;" type="file" id="<?php echo $fileName; ?>" name="<?php echo $fileName; ?>" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required />
                                        <span class="help-block error"><?php echo array_key_exists($fileName, $fileError) ? $fileError[$fileName] : ''; ?></span>

                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <input type="submit" id="btnSave" name="submit" class="btn btn-primary" value="Upload" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                             <a class='btn btn-warning' href="<?php echo base_url();?>assets/admin/sample/Sample_bulk_upload_hajiri.xlsx" download="Sample_bulk_upload_hajiri.xlsx"> Download sample excel sheet</a>
                        </div>
                        <!-- /.box-footer -->
                    </form>
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->

            <?php
            if ( $skipRows != "" ) {
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
								<?php if (!empty($skippedData)) { ?>
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

													foreach ($skippedData as $skippedD) {

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
											<p>Total Added Rows :<b style="font-size: 16px;"> <?php echo $insertRows; ?></b></p>
											<p>Total Failed Rows : <b style="font-size: 16px;"><?php echo $skipRows; ?></b></p>
											<p>Total Rows : <b style="font-size: 16px;"><?php echo ($skipRows + $insertRows); ?></b></p>
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
        jQuery('.alert-danger').fadeOut(3000); //remove suucess message
		var bar = $('.bar');
		var percent = $('.percent');
		var status = $('#status');

		$('#import-labour').ajaxForm({
			alert('enetered');
			beforeSend: function() {
				status.empty();
				var percentVal = '0%';
				bar.width(percentVal);
				percent.html(percentVal);
			},
			uploadProgress: function(event, position, total, percentComplete) {
				var percentVal = percentComplete + '%';
				bar.width(percentVal);
				percent.html(percentVal);
			},
			complete: function(xhr) {
				status.html(xhr.responseText);
			}
		});
    });
</script>