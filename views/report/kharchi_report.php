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
        <li><a href="<?php echo base_url('admin'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active"><?php echo (isset($title) ? $title : ''); ?></li>
    </ol>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8 col-offset-2">
                <!-- Horizontal Form -->
                <div class="box box-info">
                    <!-- /.box-header -->
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
                    <!-- form start -->
                    <form action="" id="payment-report" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <div class="box-body">
                            <input type="text" name="company_id" value="<?php echo $this->session->userdata('company_id'); ?>" hidden>
                            
                            <div class="form-group">
                                <label for="project" class="col-sm-3 control-label">Project:</label>
                                <div class="col-sm-9">
                                    <?php
                                    echo form_dropdown('project', $projects, $this->input->get_post('project'), 'class="form-control"');
                                    ?>
                                    <span class="error"><?php echo form_error('project') ?></span>
                                </div>
                            </div>
							<?php if($this->session->userdata('user_designation') != 'Supervisor'){ ?>
							<div class="form-group">
                                <label for="project" class="col-sm-3 control-label">Supervisor:</label>
                                <div class="col-sm-9">
                                    <select class='form-control' name='supervisor'>
										<option value='%'>All Supervisors </option>
										<?php 
											foreach($supervisor as $data){
										?>
											<option value='<?php echo $data->user_id; ?>'> <?php echo $data->user_name.' '.$data->user_last_name; ?> </option>
										<?php } ?>
									</select>
                                    <span class="error"><?php echo form_error('supervisor') ?></span>
                                </div>
                            </div>
							<?php } else{ ?>
							<input type='text' name='supervisor' class='hidden' value='<?php echo $this->session->userdata("id") ?>' />
							<?php } ?>
                            <div class="form-group">
                                <label for="month" class="col-sm-3 control-label">Month:</label>
                                <div class="col-sm-9">
                                    <?php
                                    echo form_dropdown('month', $months, $this->input->get_post('month') ? $this->input->get_post('month') : date('n'), 'class="form-control"');
                                    ?>
                                    <span class="error"><?php echo form_error('month') ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="year" class="col-sm-3 control-label">Year:</label>
                                <div class="col-sm-9">
                                    <?php
                                    echo form_dropdown('year', $years, $this->input->get_post('year') ? $this->input->get_post('year') : date('Y'), 'class="form-control"');
                                    ?>
                                    <span class="error"><?php echo form_error('year') ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="year" class="col-sm-3 control-label">Download:</label>
                                <div class="col-sm-9">
                                    <?php 
                                        $file = array("pdf" => "PDF", "excel" => "Excel");
                                        echo form_dropdown('downloadformat', $file, '','class="form-control"');
                                    ?>
                                    <span class="error"><?php echo form_error('downloadformat') ?></span>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <input type="submit" id="btnSave" name="submit" class="btn btn-primary" value="Generate Report">
                        </div>
                        <!-- /.box-footer -->
                    </form>
                </div>
                <!-- /.box -->
            </div>

        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script>
    jQuery(document).ready(function(){
        $('.alert-dismiss').fadeOut(5000);
        $('#payment-report').submit(function(event){
            var error=0;
            var organization =$("select[name='organization']");
            var project =$("select[name='project']");
            var month =$("select[name='month']");
            var year =$("select[name='year']");
            $("select[name='supervisor']").css({'border': '1px solid green', });
            $("select[name='downloadformat']").css({'border': '1px solid green', });
			if (organization.val() == '') {
                organization.css({'border': '1px solid red', });
                organization.next().text("Please select organization name");
                error = 1;
            } else {
                organization.css({'border': '1px solid green', });
                organization.next().text("");
            }
            if (project.val() == '') {
                project.css({'border': '1px solid red', });
                project.next().text("Please select project");
                error = 1;
            } else {
                project.css({'border': '1px solid green', });
                project.next().text("");
            }
            if (month.val() == ''){
                month.css({'border': '1px solid red', });
                month.next().text("Please select month");
                error = 1;
            } else {
                month.css({'border': '1px solid green', });
                month.next().text("");
            }
            if (year.val() == ''){
                year.css({'border': '1px solid red', });
                year.next().text("Please select year");
                error = 1;
            } else {
                year.css({'border': '1px solid green', });
                year.next().text("");
            }
            if (error > 0) {
                event.preventDefault();
            }
        });
        
        $('select[name="organization"]').on('change', function () {
            var id = $(this).val();
            if (id > 0) {
                $.ajax({
                    url: "<?php echo site_url('admin/report/ajax_get_project_list') ?>/" + id,
                    type: "POST",
                    success: function (data) {
                        $('select[name="project"]').html(data);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert('Error Getting Project list');
                    }
                });
            } else {
                var prjlist = '<select name="project" class="form-control">';
                prjlist += '<option value = "">Select project</option>';
                prjlist += '</select>';
                $('select[name="project"]').html(prjlist);
            }
        });
        
    });
</script>