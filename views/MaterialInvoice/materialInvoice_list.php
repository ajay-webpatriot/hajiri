<style type="text/css">#table_filter{margin: 10px 0 0 20px;}</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
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
    <section class="content container-fluid">
        <div class="col-md-12">
            <!-- Filter portion start -->
            <div class="box">
                <div class="box-body table-responsive">
                    <div class="filters col-md-12">
                        <br/>
                        <div class="col-md-12">
                            <h4>Filters:</h4><br/>
                        </div>
                        
                        <div class="col-md-12" style="padding-bottom: 2%;">
                            <label class="col-md-1 control-label">Project:</label>
                            <div class="col-md-3">
                                <select class="form-control projectInvoice" name="projectInvoice">
                                    <option value="">All Project </option>
                                    <?php 
                                        foreach ($projects as $proj) {
                                    ?>
                                    <option value="<?php echo $proj->project_id; ?>"><?php echo $proj->project_name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            
                            <label class="col-md-1 control-label">Invoice Date:</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="invoiceRange" />
                            </div>
                            
                        </div>
                        <div class="col-md-12">
                            <label class="col-md-1 control-label">Supplier name:</label>
                            <div class="col-md-3">
                                <select class="form-control supplierInvoice" name="supplierInvoice">
                                    <option value="">All Supplier </option>
                                    
                                </select>
                            </div>
                            <label class="col-md-1 control-label">Payment Date:</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="paymentRange" />
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <!-- Filter portion end -->
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><?php echo (isset($title) ? $title : ''); ?></h3>
                    <div class="box-tools pull-right">
                        <!-- Add button -->
                        <a href="<?php echo base_url('admin/MaterialInvoice/issueInvoice/');   ?>">  
                            <button class="btn btn-info">
                                <i class="glyphicon glyphicon-plus"></i> 
                                Issue Invoice
                            </button>
                        </a>
                    </div>
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
                    <table id="table" class="tableFilter table table-striped table-hover table-bordered display responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Invoice No</th> 
                                <th>Invoice Date</th>
                                <th>Payment Due Date</th>
                                <th>Supplier Name</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            /*foreach ($materialInvoice as $value) { ?>
                                <tr>
                                    <td><?php echo $value->invoice_no ; ?></td>
                                    <td><?php echo $value->invoice_date ; ?></td>
                                    <td><?php echo $value->payment_due_date; ?></td>
                                    <td><?php echo $value->supplier_name; ?></td>
                                    <td><?php echo $value->total_amount; ?></td>
                                    <td><?php echo $value->status; ?></td>
                                    <td> 

                                        <a class="btn btn-sm btn-primary" href="<?php echo base_url('admin/MaterialInvoice/editInvoiceDetail/') . $value->id; ?>" title="Edit material invoice">
                                            <i class="glyphicon glyphicon-pencil"></i> </a>  
                                            <button class="btn btn-sm btn-danger" title="Delete material invoice" onclick="material_invoice_delete('<?php echo $value->id; ?>')">
                                                <i class="glyphicon glyphicon-trash"></i> 
                                            </button>
                                        </td>
                                    </tr>
                                <?php  } 
                            */
                                ?> 
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- /.content -->

    <script type="text/javascript">
        var tableInvoice="";
        var invoiceStartRange="";
        var invoiceEndRange="";
        var paymentStartRange="";
        var paymentEndRange="";
        $(function () {
            // $("#table").DataTable({
            //     "order": [[ 0, "desc" ]]
            // });
            $('input[name="invoiceRange"]').daterangepicker({
                opens: 'left',
                startDate: moment().subtract(6, 'days'),
                endDate: new Date()
              }, function(start, end, label) {
                
                invoiceStartRange=start.format('YYYY-MM-DD');
                invoiceEndRange=end.format('YYYY-MM-DD');
                tableInvoice.draw();
            });
            $('input[name="paymentRange"]').daterangepicker({
                opens: 'left',
                startDate: moment().subtract(6, 'days'),
                endDate: new Date()
              }, function(start, end, label) {
                
                paymentStartRange=start.format('YYYY-MM-DD');
                paymentEndRange=end.format('YYYY-MM-DD');
                tableInvoice.draw();
            });

            // set date during initialization
            invoiceStartRange=moment($('input[name="invoiceRange"]').val().split(" - ")[0]).format('YYYY-MM-DD');
            invoiceEndRange=moment($('input[name="invoiceRange"]').val().split(" - ")[1]).format('YYYY-MM-DD');

            paymentStartRange=moment($('input[name="paymentRange"]').val().split(" - ")[0]).format('YYYY-MM-DD');
            paymentEndRange=moment($('input[name="paymentRange"]').val().split(" - ")[1]).format('YYYY-MM-DD');

            // DataTable
            tableInvoice = $('#table').DataTable({
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "drawCallback": function( settings ) {    
                },
                "ajax":{
                    "url": "<?php echo base_url('admin/MaterialInvoice/materialInvoiceDatatable') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":function(data) {
                        data.project = $('.projectInvoice').val();
                        data.supplier = $('.supplierInvoice').val();
                        data.invoiceStartRange=invoiceStartRange;
                        data.invoiceEndRange=invoiceEndRange;
                        data.paymentStartRange=paymentStartRange;
                        data.paymentEndRange=paymentEndRange;
                        data.<?php echo $this->security->get_csrf_token_name(); ?> = "<?php echo $this->security->get_csrf_hash(); ?>";
                    },
                },
                "columns": [
                          { "data": "invoice_no" },
                          { "data": "invoice_date" },
                          { "data": "payment_due_date" },
                          { "data": "supplier_name" },
                          { "data": "total_amount" },
                          { "data": "status" },
                          { "data": "action" }
                ],
                columnDefs: [
                    {
                        "targets": [0],
                        "visible": true,
                        "searchable": true,
                        "sortable":true,
                        "type": "string"
                    },
                    {
                        "targets": [1],
                        "visible": true,
                        "searchable": true,
                        "sortable":true,
                        "type": "string"
                    },
                    {
                        "targets": [2],
                        "visible": true,
                        "searchable": true,
                        "sortable":true,
                        "type": "string"
                    },
                    {
                        "targets": [3],
                        "visible": true,
                        "searchable": true,
                        "sortable":true,
                        "type": "string"
                    },
                    {
                        "targets": [4],
                        "visible": true,
                        "searchable": true,
                        "sortable":true,
                        "type": "string"
                    },
                    {
                        "targets": [5],
                        "visible": true,
                        "searchable": true,
                        "sortable":true,
                        "type": "string"
                    },
                    {
                        "targets": [6],
                        "visible": true,
                        "searchable": false,
                        "sortable":false,
                        "type": "string"
                    }
                ]
            });

            $(document).on("change",".projectInvoice",function(){

                var projectSupplierOption ="<option value=''>Supplier Name</option>";
                

                var project_id = $(this).val();
                var ele=this;
                if(project_id) {   
                    $.ajax({
                        url: "<?php echo base_url().'admin/MaterialInvoice/getSupplierAjax/'?>"+project_id,
                        type: "GET",
                        dataType: "json",
                        success:function(data) {
                            
                            $.each(data, function(key, value) {
                                projectSupplierOption+='<option  value="'+ value.id +'">'+ value.name +'</option>';
                            });

                            $('.supplierInvoice').html(projectSupplierOption);
                        }
                    });
                }else{
                    $('.supplierInvoice').html(projectSupplierOption);
                    
                }

                tableInvoice.draw();
            }); 
            $('.supplierInvoice').change(function () {
                tableInvoice.draw();
            });
        });

        $(document).ready(function () {
        $('.alert-success').fadeOut(3000); //remove suucess message
        //datatables
    });

    // Delete material invoice
    function material_invoice_delete(id) {

        if (id !== '' ) {
            if (confirm('Are you sure to delete material invoice?')) {
               $.ajax({
                url: "<?php echo base_url().'admin/MaterialInvoice/ajax_delete/' ?>"+id,
                type:'POST',
                dataType: 'json',
                success: function(data, textStatus, xhr) {
                    location.reload();
                },
                complete: function(xhr, textStatus) {
                    location.reload();
                    $("div#divLoading").removeClass('show');
                } ,
                beforeSend: function () {
                    $("div#divLoading").addClass('show');
                },
            });
           }     
       } 

   }
</script>