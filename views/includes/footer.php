<style>
    .error p{
        color:#F83A18;
    }
    .error {
        color:#F83A18;
    }
</style>

<footer class="main-footer col-md-12">
    <div class="col-md-3">
        <img src="<?php echo base_url('assets/admin/images/aasaan_logo_black.png') ?>" width="100%">
    </div>
    <div class="col-md-4 col-md-offset-3">
        <strong>Copyright &copy;<?php echo (date('Y') - 1 ) . '-' . date('Y'); ?> <a href="<?php echo base_url();?>">Aasaan Tech Pvt. Ltd. </a> </strong> All rights
        reserved.
    </div>
</footer>

<?php $this->load->view('includes/scripts'); ?>
</body>
<script type="text/javascript">
    // When the document is ready
    $(document).ready(function () {
        $('.alert-success').fadeOut(3000); //remove suucess message
        $('.datepicker').datepicker({
            startDate:new Date(),
            format: "mm/dd/yyyy"
        });

        $('.datepicker-material').datepicker({
            format: "mm/dd/yyyy"
        });
    });
</script>
<script src="<?php echo base_url('assets/admin/dist/js/croppie.js'); ?>"></script>

</html>








