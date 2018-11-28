<style>
div#table_length {
    margin-top: -5%;
}
div.dataTables_filter label input {
    margin-left: 32px;
}
</style><!-- Content Wrapper. Contains page content -->
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
            <div class="box">
                
                <div class="box-body">
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
                    <?php if ($this->session->flashdata('error') != ''): ?>
                        <div class="alert alert-error alert-dismissable">
                            <i class="fa fa-check"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <b>Error!</b> 
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="filtersWR col-md-4">
                        <label class="col-md-3 control-label">Category:</label>
                        <div class="col-md-9">
                            <select class="form-control category" name="category">
                                <option value="">All Category </option>
                                <?php 
                                    foreach ($Category as $proj) {
                                ?>
                                <option value="<?php echo $proj->category; ?>"><?php echo $proj->category; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="filtersWR col-md-4">
                        <label class="col-md-4 control-label">Join Date:</label>
                        <div class="col-md-8">
                            <input type="text" name="date" class="datepicker form-control" autocomplete="off">
                        </div>
                    </div>
                    <br/>
                    <div class="hidden" id="qrCodeActionButton">
                        <button class="btn btn-success" data-toggle="modal" data-target="#qrCodeModal"  id="generate">Generate QR Codes</button>
                    </div>
                    <table id="table" class="tableFilter table table-striped table-hover table-bordered display responsive nowrap" cellspacing="0" width="100%" style="margin-top: 50px !important;" >
                        <thead>
                            <tr>
                               <th></th>
                                <th>
                                    First name
                                </th> 
                                <th>
                                    Last name
                                </th> 
                                <th>
                                    Category name
                                </th>
                            </tr>
                        </thead>
                    </table>
                    
                    <div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog" aria-labelledby="qrCodeModal">
                            <div class="modal-dialog modal-md" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="btn btn-danger close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title col-md-4" id="exampleModalLabel">
                                            Generate QR Code
                                        </h4>
                                    </div>
                                    <div class="modal-body">
                                        <form id="frmHajiri" action="" method="POST">
                                            <div class="row bulkDetailsHeader">
                                                <div class="col-md-4">First name</div>
                                                <div class="col-md-4">Last name</div>
                                                <div class="col-md-4">Category</div>
                                            </div>
                                            <div class="workerDetails"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <form action="" id="frm_qr_id_cards" class="form-horizontal" method="POST" enctype="multipart/form-data">
                                            <select name="labour_id[]" id="labour_id" class="hidden" multiple required>
                                            </select>

                                            <input type="submit" id="btn_getidcards" name="qrStamp" class="btn btn-success" value="Generate QR Stamp">
                                            <input type="submit" id="btnSave" name="qrIdCard" class="btn btn-warning" value="Generate QR ID Cards">
                                        </form>
                                        </form>    
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- /.content -->

<script type="text/javascript">
    $(document).ready(function() {
        $( ".datepicker" ).datepicker({
            defaultDate: new Date(),
            format: 'dd-mm-yyyy',
            endDate: '+0d',
            autoclose: true
        });

        $('.category').change(function () {
            table.draw();
        });
        $('.datepicker').change(function () {
            table.draw();
        });
        $('.alert-success').fadeOut(7000); //remove suucess message
        // DataTable
        var table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "select"    : true,
			"searching": true, 
            "select": {
                style: 'multi'
            },
            "drawCallback": function( settings ) {
                $( ".datepicker" ).datepicker({
                    defaultDate: new Date(),
                    format: 'dd-mm-yyyy',
                    endDate: '+0d',
                    autoclose: true
                });
            },
            "ajax":{
                "url": "<?php echo base_url('admin/qr_codes/workerDatatable') ?>",
                "dataType": "json",
                "type": "POST",
                "data":function(data) {
                    data.category = $('.category').val();
                    data.date = $('.datepicker').val();
                    data.<?php echo $this->security->get_csrf_token_name(); ?> = "<?php echo $this->security->get_csrf_hash(); ?>";
                },
            },
            "columns": [
                      { "data": "worker_id" },
                      { "data": "labour_name" },
                      { "data": "labour_last_name" },
                      { "data": "category_name" },
                   ],
            "columnDefs": [
                {
                    "targets": [0],
                    'checkboxes': {
                       'selectRow': true
                    },
                    "visible": true,
                    "searchable": false,
                    "sortable":false,
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
                    "sortable":true,
                    "searchable": true,
                    "type": "string"
                },
            ]
        });
        $('.dataTables_wrapper').find('.row').first().removeClass('row');
        $('.dataTables_wrapper').find('.col-sm-6').removeClass('col-sm-6').addClass('col-md-4');
        
        table
        .on( 'select', function ( e, dt, type, indexes ) {
            var rowData = table.column(0).checkboxes.selected();
            $('#qrCodeActionButton').removeClass('hidden');
        } )
        .on('user-select', function (e, dt, type, cell, originalEvent) {
        //       alert( table.rows('.selected').data().length +' row(s) selected' );
        })
        .on( 'deselect', function ( e, dt, type, indexes ) {
            var rowData = table.column(0).checkboxes.selected();
            if(table.column(0).checkboxes.selected().length == 0){
                $('#qrCodeActionButton').addClass('hidden');
            }
        } );

        $('#generate').click( function () {
            $('.workerDetails').html('');
            var wrokerData =  table.rows('.selected').data();
            for (var i=0; i < wrokerData.length; i++){
                
               $('.workerDetails').append("<div class='row table-striped'>  <div class='col-md-4'> " + wrokerData[i]['labour_name'] + "</div> <div class='col-md-4'> " + wrokerData[i]['labour_last_name'] + "</div><div class='col-md-4'> " + wrokerData[i]['category_name'] + "</div> </div>");

               $('#labour_id').append("<option value='" + wrokerData[i]['worker_id'] + "' selected> " + wrokerData[i]['worker_id'] +" </option>");
            }
        } );

    }); /*End of document ready*/
</script>

