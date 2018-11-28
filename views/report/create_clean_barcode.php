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
        <li class="active"><?php echo (isset($title) ? $title : ''); ?></li>
    </ol>
    <section class="content">
        <div class="row">
            <div class="col-md-8 col-offset-2">
                <div class="box box-info">
                    <?php if ($this->session->flashdata('error')) { ?>
                        <div class="alert alert-danger alert-dismiss">
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
                    <form action="" id="create_clean_barcode" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <div class="box-body">
                            <h1><?php echo $msg; ?></h1>
							
							<?php if( $this->session->userdata('user_designation') == 'Superadmin' ) {?>
							<div class="form-group">
                                <label for="company_id" class="col-sm-3 control-label">Company:</label>
                                <div class="col-sm-9">
                                    <select name="company_id" class="form-control">
                                        <!--<option value="">--Select Company--</option>-->
										<?php 
											foreach( $companies as $company ){ 
												$selected = '';
												if( isset($_POST['company_id']) && $company->compnay_id == $_POST['company_id'] ){
													$selected = 'selected="selected';
												}
												echo '<option value="'.$company->compnay_id.'" '.$selected.' >'.$company->company_name.'</option>';
											}
										?>
                                    </select>
                                    <span class="error"><?php echo (form_error('company_id')) ? form_error('company_id') : ''; ?></span>
                                </div>
                            </div>
							<?php } ?>
													
                            <div class="form-group">
                                <label for="count" class="col-sm-3 control-label">Number of blank QR codes:</label>
                                <div class="col-sm-9">
                                    <input name="count" placeholder="Enter number" class="form-control" type="number" value="<?php echo (isset($_POST['count'])) ? $_POST['count'] : ''; ?>">
                                    <span class="error"><?php echo (form_error('count')) ? form_error('count') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="qr_code" class="col-sm-3 control-label">QR Type:</label>
                                <div class="col-sm-9">
                                    <select name="qr_code" class="form-control">
                                        <option value="0" >QR Stamp</option>
                                        <option value="1" >QR ID Card</option>
                                    </select>
                                    <span class="error"><?php echo (form_error('qr_code')) ? form_error('qr_code') : ''; ?></span>
                                </div>
                            </div>
                            <div class="box-footer">
                                <input type="submit" id="btnSave" name="submit" class="btn btn-primary" value="Generate">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
    </section>
</div>


<script>
    jQuery(document).ready(function () {
        $('.alert-dismiss').fadeOut(5000);
        jQuery('#create_clean_barcode').submit(function (event) {
            var count = jQuery("[name='count']");
            var error = 0;
            if (count.val() == '') {
                count.css({'border': '1px solid red', });
                count.next().text("Please Enter amount");
                error = 1;
            } else {
                count.css({'border': '1px solid green', });
                count.next().text("");
            }
            if (error > 0) {
                event.preventDefault();
            }
        });
    });
</script>