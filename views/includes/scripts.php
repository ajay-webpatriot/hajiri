<!-- jQuery 2.2.3 
<script src="<?php// echo base_url('assets/admin/plugins/jQuery/jquery-2.2.3.min.js'); ?>"></script>
 jQuery UI 1.11.4 
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>-->
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo base_url('assets/admin/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/admin/bootstrap/js/bootstrap-datepicker.js'); ?>"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/v/dt/dt-1.10.18/datatables.min.js"></script>
<script src="<?php echo base_url('assets/admin/plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/admin/plugins/datatables/checkbox.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/admin/plugins/datatables/dataTables.bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/admin/plugins/datatables/dataTables.select.min.js'); ?>"></script>

<!-- SlimScroll -->
<script src="<?php echo base_url('assets/admin/plugins/slimScroll/jquery.slimscroll.min.js'); ?>"></script>
<!-- FastClick -->
<script src="<?php echo base_url('assets/admin/plugins/fastclick/fastclick.js'); ?>"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url('assets/admin/dist/js/app.min.js'); ?>"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url('assets/admin/dist/js/demo.js'); ?>"></script>
<!-- InputMask -->
<script src="<?php echo base_url('assets/admin/plugins/input-mask/jquery.inputmask.js'); ?>"></script>
<script src="<?php echo base_url('assets/admin/plugins/input-mask/jquery.inputmask.date.extensions.js'); ?>"></script>
<script src="<?php echo base_url('assets/admin/plugins/input-mask/jquery.inputmask.extensions.js'); ?>"></script>
<!-- select 2 js -->
<script src="<?php echo base_url('assets/admin/plugins/select2/select2.js'); ?>"></script>
<!-- Latest compiled and minified JavaScript -->


<!-- daterangepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="<?php echo base_url('assets/admin/plugins/daterangepicker/daterangepicker.js'); ?>"></script>


<!-- BEGIN PAGE LEVEL PLUGINS -->
<script>
  $(function () {
    $("#example1").DataTable();
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false
    });
    $('.dataTables_wrapper').find('.col-sm-6').first().remove();
    $('.dataTables_wrapper').find('.col-sm-6').addClass('col-md-4 addRow');
	//$('.dataTables_wrapper').find('.col-sm-6').first().addClass('col-md-4 addRow');
		$('<div class="col-sm-6 addMoneyCol"></div>').insertAfter('.addRow');
		$('.add-kharchi').appendTo('.addMoneyCol');
  });
</script>