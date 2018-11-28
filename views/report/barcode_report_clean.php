<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo (!empty($title) ? ucwords($title) : ''); ?>
            <small><?php echo (!empty($description) ? $description : ''); ?></small>
        </h1>
        <?php echo (!empty($breadcrumb) ? $breadcrumb : ''); ?>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8 col-offset-2">
                <!-- Horizontal Form -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo (!empty($description) ? $description : ''); ?></h3>
                    </div>
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
                    <form action="" id="barcode-report" class="form-horizontal" method="GET" enctype="multipart/form-data">
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <input type="submit" id="btnSave" name="submit" class="btn btn-primary" value="Get Todays blank Barcode">
                        </div>
                        <!-- /.box-footer -->
                    </form>
                </div><!-- /.box -->
            </div>

        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->


<script>
    jQuery(document).ready(function ($) {
        $('.alert-dismiss').fadeOut(5000);
        $("[name='user_id']").change(function () {
            var user_id = $(this).val();
            if (user_id != '') {
                jQuery.ajax({
                    url: "<?php echo site_url('admin/report/ajax_get_labours') ?>/" + user_id,
                    type: "POST",
                    dataType: "JSON",
                    success: function (data) {
                        $("#labour_id").html(data.labours_html);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert('Error while getting data');
                    }
                });
            } else {
                $("#labour_id").html("<option value=''>Select Labours Name</option>");
            }
        });
    });

    jQuery(document).ready(function () {
        jQuery('#barcode-report').submit(function (event) {
            var user_id = jQuery("select[name='user_id']");
            var error = 0;
             if (user_id.val() == ''){
                user_id.css({'border': '1px solid red', });
                user_id.next().text("Please select user");
                error = 1;
            } else {
                user_id.css({'border': '1px solid green', });
                user_id.next().text("");
            }
            if (error > 0) {
                event.preventDefault();
            }
        });
    });
</script>