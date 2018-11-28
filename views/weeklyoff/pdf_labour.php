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
                    <form action="" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="lid" class="col-sm-3 control-label">Labour Name:</label>
                                <div class="col-sm-9">
                                    <select name="lid" class="form-control">
                                        <option value="">--Select Labour--</option>
                                        <?php
                                        if ($labours) {
                                            foreach ($labours as $labour) {
                                                echo "<option " . (($_POST['lid'] == $labour->id) ? "selected='selected'" : "") . " value='" . $labour->id . "'>" . $labour->name . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <span class="error"><?php echo (form_error('lid')) ? form_error('lid') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Month:</label>
                                <div class="col-sm-9">
                                    <select name="month" class="form-control">
                                        <option value="">--Select Month--</option>
                                       <?php for ($m=1; $m<=12; $m++) {                   
                                        echo "<option " . (($_POST['month'] == $m) ? "selected='selected'" : "") . " value='" . $m . "'>" .date('F', mktime(0,0,0,$m)). "</option>";
                                        } ?>
                                    </select>
                                    <span class="error"><?php echo (form_error('month')) ? form_error('month') : ''; ?></span>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <input type="submit" id="btnSave" name="submit" class="btn btn-primary" value="Generate PDF">
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
