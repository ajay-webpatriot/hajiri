
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo (isset($title) ? $title : ''); ?>
            <small></small>
        </h1>
    </section>
	<ol class="breadcrumb margin-bottom">
		<li><a href="<?php echo base_url('admin'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
		<li class="active"><?php echo (isset($title) ? $title : ''); ?></li>
	</ol>
    <section class="content row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><?php echo (isset($title) ? $title : ''); ?></h3>
                    <div class="box-tools pull-right">
                        <!-- Add button -->
                        <?php 
                            if($limit->wLimit > 0 || $planId->id == 3 || $this->session->userdata('user_designation') == 'Superadmin'){
                        ?>
                        <button class="btn btn-info" id="add">
                            <i class="glyphicon glyphicon-plus"></i> 
                            Add project
                            <?php if($planId->id != 3 && $this->session->userdata('user_designation') == 'admin')echo '('.$limit->wLimit.')'; ?>
                        </button>
                        <?php 
                            }
                        ?>
                    </div>
                    <br/>
                    <div id="addProj">
                        <br/>
                        <br/>
                        <form action="" id="add-project" class="form-horizontal" method="POST">
                            <?php if( $this->session->userdata('user_designation') == 'Superadmin' ) {?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="company_id" class="col-sm-3 control-label">Company:</label>
                                        <div class="col-sm-9">
                                            <select name="company_id" class="form-control company_id">
                                                <option value="">--Select Company--</option>
                                                <?php 
                                                    foreach( $companies as $company ){ 
                                                        $selected = '';
                                                        if( isset($_POST['company_id']) && $company->compnay_id == $_POST['company_id'] ){
                                                            $selected = 'selected="selected"';
                                                        }
                                                        echo '<option value="'.$company->compnay_id.'" '.$selected.' >'.$company->company_name.'</option>';
                                                    }
                                                ?>
                                            </select>
                                            <span class="error"><?php echo (form_error('company_id')) ? form_error('company_id') : ''; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                            <?php }else{
                                echo '<div class="col-md-6 col-md-offset-2">';
                            } ?>
                                                       
                                <div class="form-group">
                                    <label for="projectName" class="col-sm-3 control-label">Project name:</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="projectName" placeholder='Project name' class="form-control" required>
                                        <span class="error"><?php echo (form_error('projectName')) ? form_error('projectName') : ''; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <input type="submit" name="submit" value="Add" class="btn btn-block btn-success" />
                            </div>
                            <div class="clearfix"></div>
                            <hr/>
                        </form>
                    </div>
                    <br/>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive">
                    <?php if ($this->session->flashdata('success') != ''): ?>
                        <div class="alert alert-success alert-dismissable">
                            <i class="fa fa-check"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <b>Success!</b> 
                            <?php echo $this->session->flashdata('success'); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($this->session->flashdata('error') != ''): ?>
                        <div class="alert alert-danger alert-dismissable">
                            <i class="fa fa-check"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <b>Error!</b> 
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>
                    <table id="table" class="table table-striped table-bordered display responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <?php if( $this->session->userdata('user_designation') == 'Superadmin' ) {?>
                                <th>Company name</th>
                                <?php } ?>   
                                <th>Project name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($projects)) {
                                foreach ($projects as $project) {
                                    if ($project->status == "2") {
                                        $status = '<a class="btn btn-sm btn-danger btn-xs" href="javascript:void(0)" title="Status" data-status="' . $project->status . '" onclick="change_status(' . "'" . $project->project_id . "'" . ')">Inactive</a>';
                                    } else {
                                        $status = '<a class="btn btn-sm btn-success btn-xs" href="javascript:void(0)" title="Status" data-status="' . $project->status . '" onclick="change_status(' . "'" . $project->project_id . "'" . ')">Active</a>';
                                    }
                                    ?>
							<tr>
                                <?php 
                                    if( $this->session->userdata('user_designation') == 'Superadmin' ){  ?>
                                        <td><?php echo (isset($project->company_name)) ? $project->company_name : ''; ?></td>
                                        <?php } ?> 
                                <td><?php echo (isset($project->project_name)) ? $project->project_name : ''; ?></td>
                                <td><?php echo $status; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="<?php echo '#editModal'.$project->project_id; ?>" onclick="checkStatus('<?php echo $project->project_id; ?>')" title="Edit category" >
                                                <i class="glyphicon glyphicon-pencil"></i>
                                    </button> 
                                </td>

                                 <!-- edit Project modal -->

                                <div class="modal fade" id="<?php echo 'editModal'.$project->project_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
                                    <div class="modal-dialog modal-sm" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="btn btn-danger close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="exampleModalLabel">
                                                    Edit Project
                                                </h4>
                                            </div>
                                            <div class="modal-body col-md-12" style="background: white;">
                                                <form action="" class="mark-present" method="POST">
                                                     <?php if( $this->session->userdata('user_designation') == 'Superadmin' ) {?>
                                                            <div class="form-group">
                                                               
                                                                <div class="col-sm-12">
                                                                    <select name="company_id" class="form-control company_id">
                                                                        <option value="">--Select Company--</option>
                                                                        <?php 
                                                                            foreach( $companies as $company ){ 
                                                                                $selected = '';
                                                                                if( isset($project->company_id) && $company->compnay_id == $project->company_id ){
                                                                                    $selected = 'selected="selected"';
                                                                                }
                                                                                echo '<option value="'.$company->compnay_id.'" '.$selected.' >'.$company->company_name.'</option>';
                                                                            }
                                                                        ?>
                                                                    </select>
                                                                    <span class="error"><?php echo (form_error('company_id')) ? form_error('company_id') : ''; ?></span>
                                                                </div>
                                                            </div>
                                                            <?php } ?>
                                                    <div class="form-group col-md-12">
                                                        <label for="projectName" class="col-sm-3 control-label">Name:</label>
                                                        <div class="col-sm-9">
                                                            <input name="projectName" value="<?php echo (isset($project->project_name)) ? $project->project_name : ''; ?>"  id="<?php echo 'cateName'.$project->project_id; ?>" placeholder="Project name" class="form-control" type="text" required />
                                                            <input type="text" name="project_id" value="<?php echo (isset($project->project_id)) ? $project->project_id : ''; ?>" class='hidden'>
                                                        </div>
                                                    </div>
                                                    <div class="form-group activeSelect<?php echo $project->project_id; ?> col-md-12">
                                                        <label for="status" class="col-sm-3 control-label">Status:</label>
                                                        <div class="col-sm-9">
                                                            <select name="status" class="form-control activeSel<?php echo $project->project_id; ?>">
                                                                <option value="1" <?php echo ($project->status == "1") ? 'selected="selected"' : ''; ?>>Active</option>
                                                                <option value="2" <?php echo ($project->status == "2") ? 'selected="selected"' : ''; ?>>In Active</option>
                                                            </select>
                                                            <span class="error"><?php echo (form_error('status')) ? form_error('status') : ''; ?></span>
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="modal-footer col-md-12">
                                                    <input type="submit" class="btn btn-success" name="edit" value="Submit" />
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                            </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                        <div id="divLoading"> 
                        </div>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- /.content -->

<script type="text/javascript">
    $(document).ready(function() {
        $('.alert-success').fadeOut(7000); //remove suucess message

        // DataTable
       $('#table').DataTable({
        columnDefs: [
           { orderable: false, targets: -1 },
           { orderable: false, targets: -2 }

        ]
        });

       

       jQuery('#add-project').submit(function (event) {
            var exp = /^[a-zA-Z ]+$/;
            var project = jQuery("[name='projectName']");
            var error = 0;
            if (project.val() == '') {
                project.css({'border': '1px solid red', });
                project.next().text("Please enter project name");
                error = 1;
            } else {
                if (project.val().match(exp)) {
                    $chk = check_projectName(project);
                    if ($chk == 1) {
                        error = 1;
                    }else{
                        project.css({'border': '1px solid green', });
                        project.next().text("");
                    }
                } else {
                    project.css({'border': '1px solid red', });
                    project.next().text("Please enter valid name");
                    error = 1;
                }
            }
            if (error > 0) {
                event.preventDefault();
            }
        });
        
        $("#addProj").hide();

        $("#add").click(function(){
            $("#addProj").slideToggle('slow','swing');
        });

        jQuery('[name="projectName"]').on('change', function () {
            var project = $(this);
            check_projectName(project);
        });

    }); /*End of document ready*/

    function checkStatus(id){
        if(jQuery('.activeSel'+id).val() == 1){
            $('.deleteSelect'+id).addClass('hidden');
        }
        jQuery('.activeSel'+id).change(function() { 
            if(jQuery('.activeSel'+id).val() == 1){
                $('.deleteSelect'+id).addClass('hidden');
            }else{
                $('.deleteSelect'+id).removeClass('hidden');
            }
        });
    }
    function check_projectName(project){
        var base_url = "<?php echo base_url(); ?>";
        if (project.val()) {
            $.ajax({
                url: base_url + 'admin/Project/ajax_project_check/',
                type: 'POST',
                dataType: 'json',
                data: {project: project.val()},
                success: function (data) {
                    if (data > 0) {
                        project.css({'border': '2px solid #FF0000'});
                        project.next('span.error').html('This project name already exists.');
                        $(':input[type="submit"]').prop('disabled', true);
                        return 1;
                    } else {
                        project.css({'border': '1px solid #c5c5c5'});
                        project.next('span.error').html('');
                        $(':input[type="submit"]').prop('disabled', false);
                    }
                }
            });
        }
    }



</script>
