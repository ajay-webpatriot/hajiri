<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper row">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo (isset($title) ? $title : ''); ?>
            <small></small>
        </h1>
    </section>
	<ol class="breadcrumb margin-bottom0">
		<li><a href="<?php echo base_url('admin'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
		<li class="active"><?php echo (isset($title) ? $title : ''); ?></li>
	</ol>
    <section class="content">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><?php echo (isset($title) ? $title : ''); ?></h3>
                    <div class="box-tools pull-right">
                        <!-- Add button -->
                        <button class="btn btn-info" id="add">
                            <i class="glyphicon glyphicon-plus"></i> 
                            Add yearly holiday
                        </button>
                    </div>
                    <br/>
                    <div id="addYH">
                        <br/>
                        <br/>
                        <form action="" id="add-holiday" class="form-horizontal" method="POST">
                            <div class="col-md-4 col-md-offset-2">
                                <input name="name" placeholder="Name" class="form-control" type="text" value="<?php echo (isset($_POST['name'])) ? $_POST['name'] : ''; ?>">
                                    <span class="error"><?php echo (form_error('name')) ? form_error('name') : ''; ?></span>
                            </div>
                            <div class="col-md-2">
                                <input name="date" id="date" placeholder="Select Date" class="form-control datepicker" type="text" value="<?php echo (isset($_POST['date'])) ? $_POST['date'] : ''; ?>">
                                    <span class="error"><?php echo (form_error('date')) ? form_error('date') : ''; ?></span>
                            </div>
                            <div class="col-md-4">
                                <input type="submit" name="submit" value="submit" class="btn btn-success" />
                            </div>
                            <div class="col-md-12">
                                <hr/>
                            </div>
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
                     <?php if ($this->session->flashdata('warning') != ''): ?>
                        <div class="alert alert-warning alert-dismissable">
                            <i class="fa fa-check"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <b>Warning!</b> 
                            <?php echo $this->session->flashdata('warning'); ?>
                        </div>
                    <?php endif; ?>
                    <table id="table" class="table table-striped table-hover table-bordered display responsive nowrap" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Holiday Name</th>
                                <th>Date</th> 
                                <th width="80px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            if (!empty($holidays)) {
                                $count=0;
                                foreach ($holidays as $holidays) {
                                    ?>
                                    <tr>
                                        <td><?php echo (isset($holidays->holiday_name)) ? $holidays->holiday_name : ''; ?></td>

                                        <td><?php echo (isset($holidays->holiday_date)) ? date("d-m-Y", strtotime($holidays->holiday_date))  : ''; ?></td>
                                        <td> 
                                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="<?php echo '#editModal'.$holidays->holiday_id; ?>" title="Edit category" >
                                                <i class="glyphicon glyphicon-pencil"></i>
                                            </button>
                                            <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete" onclick="delete_holidays('<?php echo $holidays->holiday_id; ?>')">
                                                <i class="glyphicon glyphicon-trash"></i> 
                                            </a>

                                            <!-- edit category modal -->

                                            <div class="modal fade" id="<?php echo 'editModal'.$holidays->holiday_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
                                                <div class="modal-dialog modal-sm" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="btn btn-danger close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="exampleModalLabel">
                                                                Edit yearly holiday
                                                            </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="" class="mark-present" method="POST">
                                                                <div class="form-group">
                                                                    <input name="name" placeholder="Name" class="form-control" type="text" value="<?php echo (isset($holidays->holiday_name)) ? $holidays->holiday_name : ''; ?>">
                                                                    <span class="error"><?php echo (form_error('name')) ? form_error('name') : ''; ?></span>
                                                                    <input type="text" name="hId" value="<?php echo (isset($holidays->holiday_id)) ? $holidays->holiday_id : ''; ?>" class='hidden'>
                                                                       
                                                                </div>
                                                                <div class="form-group">
                                                                    <input name="date" id="date" placeholder="Date" class="form-control datepicker" type="text" value="<?php echo (isset($holidays->holiday_date)) ? date("d-m-Y", strtotime($holidays->holiday_date))  : ''; ?>">
                                                                        <span class="error"><?php echo (form_error('date')) ? form_error('date') : ''; ?></span>
                                                                </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                                <input type="submit" class="btn btn-success" name="submit" value="submit" />
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                        <div id="divLoading"> 
                        </div>
                        <tfoot>
                            <tr>
                                <tr>
                                
                                <th>Holiday Name</th>
                                <th>Date</th> 
                                <th>Action</th>
                            </tr>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- /.content -->

<script type="text/javascript">
    $(function () {
        //For Date Picker
        var date = new Date();
        date.setDate(date.getDate());

        $( ".datepicker" ).datepicker({
        format: 'dd-mm-yyyy',
        startDate: date,
        autoclose: true
        });

        $('.alert-success').fadeOut(3000); //remove suucess message

        $("#table").DataTable({
            columnDefs: [
               { orderable: false, targets: -1 }
            ]
        });

        $("#addYH").hide();

        $("#add").click(function(){
            $("#addYH").slideToggle("slow");
        });
    });

    jQuery(document).ready(function () {
        $("[data-mask]").inputmask();
        jQuery('#add-holiday').submit(function (event) {
            var exp = /^[a-zA-Z ]+$/;
            var holiday_name = jQuery("[name='name']");
            var date = jQuery("[name='date']");
            var error = 0;
            if (holiday_name.val() == '') {
                holiday_name.css({'border': '1px solid red', });
                holiday_name.next().text("Please enter holiday name");
                error = 1;
            } else {
                if (holiday_name.val().match(exp)) {
                    holiday_name.css({'border': '1px solid green', });
                    holiday_name.next().text("");
                } else {
                    holiday_name.css({'border': '1px solid red', });
                    holiday_name.next().text("Please enter valid holiday name");
                    error = 1;
                }
            }
            if (date.val() == '') {
                date.css({'border': '1px solid red', });
                date.next().text("Please select date.");
                error = 1;
            } else {
                holiday_name.css({'border': '1px solid green', });
                holiday_name.next().text("");
            }
            if (error > 0) {
                event.preventDefault();
            }
        });
    });

    function delete_holidays(id) {
        if (confirm('Are you sure delete this data?')) {
            // ajax delete data to database
            $.ajax({
                url: "<?php echo site_url('admin/YearlyHolidays/ajax_delete') ?>/" + id,
                type: "POST",
                dataType: "JSON",
                success: function (data)
                {
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error deleting data');
                },
                beforeSend: function () {
                    $("div#divLoading").addClass('show');
                },
                complete: function () {
                    $("div#divLoading").removeClass('show');
                },
            });

        }
    }

</script>



