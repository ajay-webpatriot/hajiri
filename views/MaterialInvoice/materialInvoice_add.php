<style type="text/css">
    .day_txt{
        vertical-align: -webkit-baseline-middle !important;
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
        <li><a href="<?php echo base_url('admin/MaterialInvoice'); ?>">Material Invoice</a></li>
        <!-- <li><a href="<?php echo base_url('admin/MaterialInvoice/issueInvoice/'); ?>">Material Entry Invoice</a></li> -->
        
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
                    <form action="" id="add-labour" class="form-horizontal formInvoiceSubmit" method="POST" autocomplete="off">
                        <div class="box-body">
                            <h4 class="box-title">Issue Invoice :</h4>
                            <div class="form-group">
                                <label for="project_name" class="col-sm-3 control-label">Project Name <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <select disabled="disabled" class="form-control invoice_project_name" name="project_name" required>
                                        <option value="">Project Name </option>
                                        <?php 
                                        foreach ($projects as $proj) {
                                            $selected="";
                                            if($this->session->userdata("challan_project") && $this->session->userdata("challan_project") == $proj->project_id)
                                            {
                                                $selected = 'selected="selected"';
                                            }
                                            ?>
                                            <option <?=$selected?> value="<?php echo $proj->project_id; ?>"><?php echo $proj->project_name; ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php
                                    if($this->session->userdata("challan_project")){
                                    ?>
                                        <input type="hidden" name="project_name" value="<?=$this->session->userdata("challan_project")?>">
                                    <?php    
                                    }
                                    ?>
                                    <span class="error"><?php echo form_error('project_name') ?></span>
                                </div>
                            </div>
                            <?php
                            if($this->session->userdata('user_designation') != "Supervisor")
                            { ?>  
        
                                <div class="form-group">
                                    <label for="supervisor_name" class="col-sm-3 control-label">Supervisor Name <font color="red">*</font></label>
                                    <div class="col-sm-9">
                                        <select class="form-control supervisor_name" name="supervisor_name" required>
                                            <option value="">Supervisor Name </option>
                                            <?php
                                            if(count($supervisors) >0){
                                                foreach ($supervisors as $supervisor) {
                                                    
                                                ?>
                                                <option value="<?php echo $supervisor->user_id; ?>"><?php echo $supervisor->supervisor_name; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                        <span class="error"><?php echo form_error('supervisor_name') ?></span>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                            <div class="form-group">
                                <label for="supplier_name" class="col-sm-3 control-label">Supplier Name <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <select disabled="disabled" class="form-control supplier_name" name="supplier_name" required>
                                       <option value="">Supplier Name </option>
                                        <?php 
                                        if(count($suppliers) >0){
                                        foreach ($suppliers as $supp) {
                                                $selected="";
                                                if($this->session->userdata("challan_supplier") && $this->session->userdata("challan_supplier") == $supp->id)
                                                    {
                                                        $selected = 'selected="selected"';
                                                    }
                                            ?>
                                            <option <?=$selected?> value="<?php echo $supp->id; ?>"><?php echo $supp->name; ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                    <?php
                                    if($this->session->userdata("challan_supplier")){
                                    ?>
                                        <input type="hidden" name="supplier_name" value="<?=$this->session->userdata("challan_supplier")?>">
                                    <?php    
                                    }
                                    ?>
                                    <span class="error"><?php echo (form_error('supplier_name')) ? form_error('supplier_name') : ''; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Invoice Date <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <input name="invoice_date" id="invoice_date" placeholder="Invoice Date" class="form-control datepicker-material" type="text" value="<?php echo (isset($_POST['invoice_date'])) ? $_POST['invoice_date'] : date('Y-m-d'); ?>" required>
                                    <span class="error"><?php echo (form_error('invoice_date')) ? form_error('invoice_date') : ''; ?></span>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">Invoice No <font color="red">*</font></label>
                                <div class="col-sm-9">
                                    <input name="invoice_no" placeholder="Invoice No" class="form-control" type="text" value="<?php echo (isset($_POST['invoice_no'])) ? $_POST['invoice_no'] : ''; ?>" required>
                                    <span class="error"><?php echo (form_error('invoice_no')) ? form_error('invoice_no') : ''; ?></span>
                                </div>
                            </div> 
                            
                            <h4 class="box-title">Challan Details :</h4>
                            <div class="challanDetail">
                                <hr>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Challan No</label>
                                    <label class="col-sm-2 control-label">Date</label>
                                    <label class="col-sm-2 control-label">Amount</label>
                                    
                                </div>
                                <?php
                                $total_amount=0;

                                if(count($challanData) > 0)
                                {
                                    
                                    foreach ($challanData as $key => $value) {
                                        # code...
                                    $total_amount+=$value->total_rate;
                                 ?>
                                    <div class="form-group">
                                        <div class="col-sm-2 control-label">
                                            <?=$value->challan_no?>
                                            <input type="hidden" class="challan_log_id" name="challan_log_id[]" value="<?=$value->id?>">        
                                        </div>
                                        <div class="col-sm-2 control-label"><?=$value->challan_date?></div>
                                        <div class="col-sm-2 control-label"><i class="fa fa-rupee" style="font-size: 13px;color: #808080;"  aria-hidden="true"></i><?=$value->total_rate?>/-
                                            <input type="hidden" name="total_rate[]" class="total_rate" value="<?=$value->total_rate?>">
                                        </div>
                                        <div class="col-sm-1" style="padding-top: 7px;" >
                                            <?php
                                            if($value->challan_image != "")
                                            {
                                                $image =  ROOT_PATH.'/uploads/materialLog/challan/'.$value->challan_image;
                                                if(file_exists($image)){
                                                    ?>
                                                    <a href="<?=base_url('admin/MaterialInvoice/DownloadChallan/').$value->id?>"<i class="fa fa-file-o"  aria-hidden="true"></i>
                                                    </a>
                                            <?php
                                                }
                                            }
                                            ?>
                                            <i class="fa fa-trash" onclick="deleteChallan(this)" aria-hidden="true"></i>
                                        </div>
                                        
                                    </div>

                                 <?php   
                                        
                                    }
                                }
                                ?>
                                <div class="col-sm-6 pull-right">
                                    <a href="<?php echo base_url('admin/MaterialInvoice/issueInvoice/'); ?>" class="btn btn-primary">Add More</a>
                                </div>
                                <span class="error challanError"><?php echo (form_error('challan_log_id[]')) ? form_error('challan_log_id[]') : ''; ?></span>
                                <div class="form-group">
                                    <div class="col-sm-2 control-label"></div>
                                    <div class="col-sm-2 control-label"></div>
                                    <div class="col-sm-2 control-label amount"><i class="fa fa-rupee" style="font-size: 13px;color: #808080;"  aria-hidden="true"></i><?=$total_amount?>/-
                                        
                                    </div>
                                    <input type="hidden" value="<?=$total_amount?>" id="amount_without_tax" name="amount_without_tax">
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-2 control-label"></div>
                                    <div class="col-sm-2 control-label">Tax details</div>
                                    <div class="col-sm-2 control-label"><i class="fa fa-rupee" style="font-size: 13px;color: #808080;"  aria-hidden="true"></i>0/-</div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Total invoice amount <font color="red">*</font></label>
                                    <div class="col-sm-3">
                                        <input id="total_amount" name="total_amount" class="form-control" type="number" value="<?php echo (isset($_POST['total_amount'])) ?  $_POST['total_amount']: $total_amount; ?>" required>
                                        <span class="error totalAmountError"><?php echo (form_error('total_amount')) ? form_error('total_amount') : ''; ?></span>
                                    </div>
                                </div>
                                <!-- <div class="form-group" style="color: red;">
                                    <div class="col-sm-6">Amount should be greater than or equal to sum of challan amount <br/>Amount Should be inclusive of all taxes</div>
                                </div> -->
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Payment Cycle <font color="red">*</font></label>
                                    <div class="col-sm-3">
                                        <input onkeyup="displayDate(this)" id="payment_cycle" name="payment_cycle" class="form-control" type="number" value="<?php echo (isset($_POST['payment_cycle'])) ? $_POST['payment_cycle'] : ''; ?>" required>
                                        <span class="error"><?php echo (form_error('payment_cycle')) ? form_error('payment_cycle') : ''; ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <span><b class="day_txt" >Days</b></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Payment Due <font color="red">*</font></label>
                                    <div class="col-sm-3">
                                        <input readonly="" name="payment_date" id="payment_date" placeholder="Date" class="form-control" type="text" value="<?php echo (isset($_POST['payment_date'])) ? $_POST['payment_date'] : ''; ?>" required>
                                        <span class="error"><?php echo (form_error('payment_date')) ? form_error('payment_date') : ''; ?></span>
                                    </div>
                                </div> 
                                  

                            </div>    
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <input type="submit" id="btnSave" name="submit" class="btn btn-primary" value="Save">
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

<script type="text/javascript">

    $(document).ready(function () {

        $(document).on("change",".invoice_project_name",function(){

            var projectSupplierOption ="<option value=''>Supplier Name</option>";
            var projectSupervisorOption ="<option value=''>Supervisor Name</option>";
            

            var project_id = $(this).val();
            var ele=this;
            if(project_id) {   
                $.ajax({
                    url: "<?php echo base_url().'admin/MaterialInvoice/getFilterAjax/'?>"+project_id,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {
                        
                        $.each(data.getProjectSupplier, function(key, value) {
                            projectSupplierOption+='<option  value="'+ value.id +'">'+ value.name +'</option>';
                        });

                        $('.supplier_name').html(projectSupplierOption);

                        $.each(data.getProjectSupervisor, function(key, value) {
                            projectSupervisorOption+='<option  value="'+ value.user_id +'">'+ value.supervisor_name +'</option>';
                        });
                        $('.supervisor_name').html(projectSupervisorOption);
                    }
                });
            }else{
                $('.supplier_name').html(projectSupplierOption);
                $('.supervisor_name').html(projectSupervisorOption);
            }
        }); 
    });
</script>