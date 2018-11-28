
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
				<div class="box-body">
					<div class="form-inline">
						<div class="form-group">
							<label  for="company">Company</label>
							<select class='form-control' id='company'>
								<?php 
                                    foreach ($company as $data) {
                                ?>
                                <option value="<?php echo $data->id; ?>"><?php echo $data->name; ?></option>
                                <?php } ?>
							</select>
						</div>
						<div class="form-group">
							<label  for="fromDate">From date</label>
							<input type='text' class='datepicker' id='fromDate' />
						</div>
						<div class="form-group">
							<label  for="toDate">To date</label>
							<input type='text' class='datepicker' id='toDate' />
						</div>
						<div class="form-group">
							<button class='btn btn-success' id='submit' onClick='sendSmsData()'>Submit</button>
						</div>
					</div><!-- end of inline form -->
					<div class='col-md-12'>
						<hr/>
						<table class="table table-hover table-responsive">
							<thead>
								<tr>
									<th>Total Present</th>
									<th>Total SMS</th>
									<th>SMS Sent</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td id='present'> </td>
									<td id='sms'></td>
									<td id='smsSent'></td>
								</tr>
							</tbody>
						</table>
					<div>
				</div>
			</div>
		</div>
	</section>
</div>


<script type="text/javascript">
    $(document).ready(function() {
		$( ".datepicker" ).datepicker({
			defaultDate: new Date(),
			format: 'dd-mm-yyyy',
			endDate: '+0d',
			autoclose: true,
		});
	});
	function sendSmsData() {
		if($('#fromDate').val() != '' && $('#toDate').val() != ''){
		   $.ajax({
				url: "<?php echo base_url().'admin/hajiriSms/sendSmsData' ?>",
				type:'POST',
				dataType: 'json',
				async: false,
				cache: false,
				data:  {
					'company'	: $('#company').val(),
					'from'		: $('#fromDate').val(),
					'to'		: $('#toDate').val(),
				},
				success: function(data, xhr, textStatus) {
					$('#present').html(data.present);
					$('#sms').html(data.sms);
					$('#smsSent').html(data.sent);		
				},
				complete: function(data, xhr, textStatus) {
				} ,
				beforeSend: function () {
				},
			});
		}else{
			alert('Enter date.');
		}
    }
</script>