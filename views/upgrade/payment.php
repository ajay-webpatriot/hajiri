<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo (!empty($title) ? ucwords($title) : ''); ?>
            <small><?php echo (!empty($description) ? $description : ''); ?></small>
        </h1>
    <script src="https://www.cashfree.com/assets/cashfree.sdk.v1.2.js" type="text/javascript"></script>
       
    </section>
    <ol class="breadcrumb margin-bottom0">
		<li><a href="<?php echo base_url('admin'); ?>"> Dashboard</a></li>
		<li><a href="<?php echo base_url('admin/upgrade'); ?>"> Select Plan</a></li>
		<li class="active"><?php echo (isset($title) ? $title : ''); ?></li>
	</ol>

    <!-- Main content -->
       <?php
          $mode = "PROD";

          $appId = "2547bbdf90b3fd9f29254fe97452";
          $secretKey = "f917b449a343fb2631b893ec5dd689ff0b3345dc";
          if ($amount == 49) {
          	$orderId = 'ADM-'.date('dmY-His');
          }else{
          	$orderId = 'ADY-'.date('dmY-His');
          }
          $orderAmount = $amount;
          $customerName = $this->session->userdata('company_name');
          $customerPhone = "9821117266";
          $customerEmail = $this->session->userdata('user_email');
          $returnUrl = base_url("/admin/upgrade/payment");
          $notifyUrl = base_url("/admin/upgrade/payment");
          $paymentModes = "";

          $tokenData = "appId=".$appId."&orderId=".$orderId."&orderAmount=".$orderAmount."&returnUrl=".$returnUrl."&paymentModes=".$paymentModes;
          $token = hash_hmac('sha256', $tokenData, $secretKey, true);
          $paymentToken = base64_encode($token);
          
        ?>
    <section class="content">
      <div class="row">
      	<div class="col-md-12">
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
      	</div>
      	<div class="col-md-12">
	        <h2>Upgrade to Advance user</h2>
	        <p><b>Bought 1 item worth INR <?php echo $orderAmount;?>. <br/> Order Id is <?php echo $orderId;?></b></p>
	    </div>
      </div>
      <div class="row">
      	<?php 
      		if (isset($_POST["orderAmount"])) {
    				$orderId = $_POST["orderId"];
    				$orderAmount = $_POST["orderAmount"];
    				$referenceId = $_POST["referenceId"];
    				$txStatus = $_POST["txStatus"];
    				$paymentMode = $_POST["paymentMode"];
    				$txMsg = $_POST["txMsg"];
    				$txTime = $_POST["txTime"];
    				$signature = $_POST["signature"];
    				$data = $orderId.$orderAmount.$referenceId.$txStatus.$paymentMode.$txMsg.$txTime;
    				$hash_hmac = hash_hmac('sha256', $data, $secretKey, true) ;
    				$computedSignature = base64_encode($hash_hmac);
  				if ($signature == $computedSignature) {
  			?>
            <div class="col-md-6 col-xs-12">
              <div class="box col-xs-12">
                <div class="col-xs-12">
                  <div class="tick">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                      <circle class="path circle" fill="none" stroke="#73AF55" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
                      <polyline class="path check" fill="none" stroke="#73AF55" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 "/>
                    </svg>
                  </div>
                  <h3>Welcome to Hajiri Advance, you can now</h3>
                  <ul>
                    <li>
                      Assign supervisors and add upto 50 workers
                    </li>
                    <li>
                      Add multiple project's and admin ID's
                    </li>
                    <li>
                      Set holiday and leave calender. 
                    </li>
                    <li style="color: red">
                      Your subscription is valid untill <?php echo $this->session->flashdata('dueDate'); ?>
                    </li>
                  </ul>
                  <div class="alert alert-info">
                    <p><strong>Info!</strong> Log out and Login again for upgrade to take effect.</p>
                  </div>
                </div>
              </div>
            </div>

        <?php		
  				} else {
  					echo "Rejected";
  				}
  			}else{
        	?>
          	<div id="cf-iframe-container"/></div>
      	<?php }?>
      </div>
      <script type="text/javascript">
        (function() {
          var data = {};
          data.orderId = "<?php echo $orderId;?>";
          data.orderAmount = "<?php echo $orderAmount;?>";
          data.customerName = "<?php echo $customerName;?>";
          data.customerPhone = "<?php echo $customerPhone;?>";
          data.customerEmail = "<?php echo $customerEmail;?>";
          data.returnUrl = "<?php echo $returnUrl;?>";
          data.appId = "<?php echo $appId;?>";
          data.paymentModes = "<?php echo $paymentModes;?>";
          data.paymentToken = "<?php echo $paymentToken;?>";

          var config = {};
          config.mode = "<?php echo $mode; ?>";
          config.layout = {view: "inline", width: "640", container: "cf-iframe-container"};
          CashFree.init(config);

          var callback = function (event) { 
            console.log(event);
          };

          CashFree.makePayment(data, callback);
        })();
      </script>

    <!-- /.content -->
</div>
<!-- /.content-wrapper -->