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
        	 <form action="" id="add-foreman" class="form-horizontal" method="POST" enctype="multipart/form-data">
        		<input type="email" name="email" class="form-control email">
				<button type="button" id="btn-filter" class="btn btn-primary">Filter</button>
			</form>
			<table class="table table-striped table-bordered display responsive nowrap dataTable no-footer" id="posts">
                <thead>
                    <th> Worker name</th> 
                    <th>Category name</th>
                    <th>Due amount</th>
                    <th>Action</th>
                </thead>				
           </table>

        </div>
    </section>
</div>
<!-- /.content -->


<script>
	var table;
    $(document).ready(function () {
        table = $('#posts').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax":{
		     "url": "<?php echo base_url('admin/DataTable/posts') ?>",
		     "dataType": "json",
		     "type": "POST",
		     "data":function(data) {
							data.email = $('.email').val();
						    data.<?php echo $this->security->get_csrf_token_name(); ?> = "<?php echo $this->security->get_csrf_hash(); ?>";
						},
		    },
	    "columns": [
		          { "data": "labour_name" },
		          { "data": "category_name" },
		          { "data": "worker_due_wage" },
		          { "data": "action" },
		       ]	 

	    });
	    $('#btn-filter').click(function(){ //button filter event click
            var categoryName = $('.email').val();
            table.draw();
	    });
    });
</script>



