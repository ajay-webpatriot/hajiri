<div class="content-wrapper">
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
        <!-- Filter portion start -->
        <div class="box">
            <div class="box-body table-responsive">
                <div class="filters col-md-12">
                    <br/>
                    <div class="col-md-12">
                        <h4>Filters:</h4><br/>
                    </div>
                    
                    <div class="col-md-12" style="padding-bottom: 2%;">
                        <label class="col-md-1 control-label">Date:</label>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="daterange" value="01/01/2018 - 01/15/2018" />
                        </div>

                        <label class="col-md-1 control-label">Project:</label>
                        <div class="col-md-3">
                            <select class="form-control projectIssue" name="projectIssue">
                                <option value="">All Project </option>
                                <?php 
                                    foreach ($projects as $proj) {
                                ?>
                                <option value="<?php echo $proj->project_id; ?>"><?php echo $proj->project_name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <label class="col-md-1 control-label">Material:</label>
                        <div class="col-md-3">
                            <select class="form-control materialIssue" name="project">
                                <option value="">All Material </option>
                                
                            </select>
                        </div>
                        
                    </div>
                    <div class="col-md-12">
                        <label class="col-md-1 control-label">Supervisor Name:</label>
                        <div class="col-md-3">
                            <select class="form-control supervisorIssue" name="supervisorIssue">
                                <option value="">All Supervisor </option>
                                
                            </select>
                        </div>
                        <label class="col-md-1 control-label">Supplier Name:</label>
                        <div class="col-md-3">
                            <select class="form-control supplierIssue" name="supplierIssue">
                                <option value="">All Supplier </option>
                                
                            </select>
                        </div>
                        <label class="col-md-1 control-label">Status:</label>
                        <div class="col-md-3">
                            <select class="form-control statusIssue" name="statusIssue">
                                <option value="">All Status </option>
                                <option value="Verified">Verified</option>
                                <option value="Pending">Pending</option>
                                
                            </select>
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
                    <a href="<?php echo base_url('admin/MaterialIssue/addIssueLog/');   ?>">  
                        <button class="btn btn-info">
                            <i class="glyphicon glyphicon-plus"></i> 
                            <?php echo (isset($title) ? $title : ''); ?>
                        </button>
                    </a>
                </div>

            </div>
            
            <div class="box-body">
                <table id="Issuetable" class="table table-striped table-hover table-bordered  table-responsive" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Issue No</th> 
                            <th>Issue Date</th>
                            <th>Issue By</th>
                            <th>Category</th>
                            <th>Material Name</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($materialIssue != ''){

                        
                            foreach ($materialIssue as $value) { 
                        ?>
                                <tr>
                                    <td><?php echo $value->issue_no ; ?></td>
                                    <td><?php echo $value->date ; ?></td>
                                    <td><?php echo $value->issue_by_name; ?></td>
                                    <td><?php echo $value->category_name; ?></td>
                                    <td><?php echo $value->material_name; ?></td>
                                    <td><?php echo $value->quantity; ?></td>
                                    <td><?php echo $value->status; ?></td>
                                    
                                    <td> 

                                    <a class="btn btn-sm btn-primary" href="<?php echo base_url('admin/MaterialIssue/editIssueLog/') . $value->id; ?>" title="Edit material issue">
                                        <i class="glyphicon glyphicon-pencil"></i> </a>  
<<<<<<< HEAD
                                        <button class="btn btn-sm btn-danger" title="Delete material issue" onclick="material_issue_log_delete('<?php echo $value->id; ?>')">
                                            <i class="glyphicon glyphicon-trash"></i> 
                                        </button>
=======
                                        <?php if($value->status !== 'Verified' ) { ?>
                                            <button class="btn btn-sm btn-danger" title="Delete material entry" onclick="material_issue_log_delete('<?php echo $value->id; ?>')">
                                                <i class="glyphicon glyphicon-trash"></i> 
                                            </button>
                                        <?php } ?>
>>>>>>> cb822bde0a86db6110c1026c2b05ce63bb220cf3
                                    </td>
                                </tr>
                        <?php 
                            } 
                                } 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<script>
$(function () {
    // $("#Issuetable").DataTable({
    //     "order": [[ 0, "desc" ]]
    // });
    $('input[name="daterange"]').daterangepicker({
        opens: 'left',
        startDate: moment().subtract(6, 'days'),
        endDate: new Date()
      }, function(start, end, label) {
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        dateStartRange=start.format('YYYY-MM-DD');
        dateEndRange=end.format('YYYY-MM-DD');

        tableIssue.draw();
    });

    // set date during initialization
    dateStartRange=moment($('input[name="daterange"]').val().split(" - ")[0]).format('YYYY-MM-DD');
    dateEndRange=moment($('input[name="daterange"]').val().split(" - ")[1]).format('YYYY-MM-DD');


    // DataTable
    tableIssue = $('#Issuetable').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "drawCallback": function( settings ) {    
        },
        "ajax":{
            "url": "<?php echo base_url('admin/MaterialIssue/materialIssueDatatable') ?>",
            "dataType": "json",
            "type": "POST",
            "data":function(data) {
                data.project = $('.projectIssue').val();
                data.material = $('.materialIssue').val();
                data.supplier = $('.supplierIssue').val();
                data.supervisor = $('.supervisorIssue').val();
                data.status = $('.statusIssue').val();
                data.dateStartRange=dateStartRange;
                data.dateEndRange=dateEndRange;
                data.<?php echo $this->security->get_csrf_token_name(); ?> = "<?php echo $this->security->get_csrf_hash(); ?>";
            },
        },
        "columns": [
                  { "data": "issue_no" },
                  { "data": "date" },
                  { "data": "issue_by_name" },
                  { "data": "category_name" },
                  { "data": "material_name" },
                  { "data": "quantity" },
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
                "searchable": false,
                "sortable":false,
                "type": "string"
            }
        ]
    });

    $('.projectIssue').change(function () {
        var optionHTML="<option value=''>All Supervisor</option>";
        var projectSupplierOption ="<option value=''>All Supplier</option>";
        var projectMaterialOption ="<option value=''>All Material</option>";

        var project_id = $(this).val();
        var ele=this;
        if(project_id) {   
            $.ajax({
                url: "<?php echo base_url().'admin/MaterialLog/getFilterDetailAjax/'?>"+project_id,
                type: "GET",
                dataType: "json",
                success:function(data) {
                    // $('select[name="city"]').empty();
                    $.each(data.getProjectSupervisor, function(key, value) {
                        optionHTML+='<option  value="'+ value.user_id +'">'+ value.supervisor_name +'</option>';
                    });

                    $.each(data.getProjectSupplier, function(key, value) {
                        projectSupplierOption+='<option  value="'+ value.id +'">'+ value.name +'</option>';
                    });

                    $.each(data.getProjectMaterial, function(key, value) {
                        projectMaterialOption+='<option  value="'+ value.id +'">'+ value.name +'</option>';
                    });

                    $('.supervisorIssue').html(optionHTML);
                    $('.supplierIssue').html(projectSupplierOption);
                    $('.materialIssue').html(projectMaterialOption);
                }
            });
        }else{
            $('.supplierIssue').html(projectSupplierOption);
            $('.supervisorIssue').html(optionHTML);
            $('.materialIssue').html(projectMaterialOption);
        }

        tableIssue.draw();
    });

    $('.supervisorIssue').change(function () {
        tableIssue.draw();
    });

    $('.supplierIssue').change(function () {
        tableIssue.draw();
    });

    $('.materialIssue').change(function () {
        tableIssue.draw();
    });
    $('.statusIssue').change(function () {
        tableIssue.draw();
    });
});

// Delete material issue log
    function material_issue_log_delete(id) {

        if (id !== '' ) {
            if (confirm('Are you sure to delete material issue log?')) {
               $.ajax({
                url: "<?php echo base_url().'admin/MaterialIssue/ajax_delete/' ?>"+id,
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
       } /*end of date and reason check if*/

   }
</script>