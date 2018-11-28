<section class="content">
  <!-- Small boxes (Stat box) -->
  <div class="row">
      <!-- ./col -->
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
      <div class="col-md-4  col-xs-6">
          <!-- small box -->
          <button type='button' data-toggle="modal" data-target="#todayac" class="small-box bg-green">
              <div class="inner">
                  <h3><?php echo $todaysStrength; ?></h3>
                  
                  <p>Todays Strength</p>
              </div>
              <div class="icon">
                  <i class="fa fa-users"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </button>
          <div class="modal fade" id="todayac" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
              <div class="modal-dialog modal-md" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title" id="exampleModalLabel">
                              Today's strength &nbsp;&nbsp;&nbsp;
                              <strong><?php echo $todaysStrength; ?></strong>
                          </h4>
                      </div>
                      <div class="modal-body">
                          <?php
                              if ($todayAttendancePW == null)
                                  echo '<div class="alert alert-danger" role="alert">No attendance marked today</div>';
                              else{
                                  $counter = 0;
                                  ?>
                                   <table id='todaysAttendanceTable' class="table table-striped table-hover table-bordered display responsive nowrap">
                                      <thead>
                                          <th>Project name </th>
                                          <th>Hajiri</th>
                                      </thead>
                                      <tbody>
                                  <?php foreach ($todayAttendancePW as $todayac) { 
                                  $counter++;
                                  ?>                                      
                                          <tr>
                                              <td> <?php echo $todayac->name; ?></td>
                                              <td> <?php echo $todayac->count; ?></td>
                                          </tr>
                                      
                                  <?php
                                  } ?>
                                  <tbody>
                              </table>
                          <?php    }                                  
                          ?>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      </div>
                  </div>
              </div>
          </div> 
      </div>

       <div class="col-md-4   col-xs-6">
          <!-- small box -->
          <button type='button' data-toggle="modal" data-target="#average" class="small-box bg-yellow ">
              <div class="inner">
                  <h3><?php echo round($averageAttendance/7); ?></h3>

                  <p>Last 7 Days Average</p>
              </div>
              <div class="icon">
                  <i class="fa fa-bar-chart"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </button>
          
          <div class="modal fade" id="average" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
              <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title" id="exampleModalLabel">
                              7 day average &nbsp;&nbsp;&nbsp;
                              <strong><?php echo round($averageAttendance/7); ?></strong>
                          </h4>
                      </div>
                      <div class="modal-body">
                          <canvas id="averageChart" width="400" height="200"></canvas>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      
      <div class="col-md-4   col-xs-6">
        <!-- small box -->
        <button type='button' data-toggle="modal" data-target="#attendanceTillDate"  class="small-box bg-red">
            <div class="inner">
                <h3><?php echo round($attendanceTillDate->total, 1); ?></h3>
                <p>Attendance till date</p>
            </div>
            <div class="icon">
                <i class="fa fa-check-square-o"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </button>

        <div class="modal fade" id="attendanceTillDate" tabindex="-1" role="dialog" 
          aria-labelledby="exampleModalLabel">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="exampleModalLabel">
                            Attendance till date&nbsp;&nbsp;&nbsp;
                            <strong> <?php echo round($attendanceTillDate->total, 1); ?> </strong>
                        </h4>
                    </div>
                    <div class="modal-body">
                          <?php
                              if ($attendanceProjectWise == null)
                                  echo '<div class="alert alert-danger" role="alert">No attendance marked till date.</div>';
                              else{
                                  $counter = 0;
                                  ?>
                                   <table class="table table-striped table-hover table-bordered display responsive nowrap">
                                      <thead>
                                          <th>Project name </th>
                                          <th>Hajiri</th>
                                      </thead>
                                      <tbody>
                                  <?php foreach ($attendanceProjectWise as $data) { 
                                  $counter++;
                                  ?>                                      
                                          <tr>
                                              <td> <?php echo $data->pName; ?></td>
                                              <td> <?php echo round($data->total, 1); ?></td>
                                          </tr>
                                      
                                  <?php
                                  } ?>
                                  <tbody>
                              </table>
                          <?php    }                                  
                          ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
      </div>
  </div>

  <div class="row ">
    <div class="col-md-4 col-xs-6">
      <!-- small box -->
      <button type='button' data-toggle="modal" data-target="#expenseTillDate" class="small-box bg-maroon">
          <div class="inner">
              <h3>
                  &#8377;
                  <?php 
                       foreach ($workerExpense as $tp) {
                          echo number_format($tp);
                      }
                  ?>
              </h3>

              <p>Worker expense till date</p>
          </div>
          <div class="icon">
              <i class="fa fa-line-chart"></i>
          </div>
          <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
      </button>

      <div class="modal fade" id="expenseTillDate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
          <div class="modal-dialog modal-sm" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="exampleModalLabel">
                          Expense till date &nbsp;&nbsp;&nbsp;
                          <strong>&#8377;
                          <?php 
                              foreach ($workerExpense as $tp) {
                                  echo number_format($tp);
                              }
                          ?>
                          </strong>
                      </h4>
                  </div>
                  <div class="modal-body">
                          <?php
                              if ($wExpProj == null)
                                  echo '<div class="alert alert-danger" role="alert">No attendance marked till date.</div>';
                              else{
                                  $counter = 0;
                                  ?>
                                   <table class="table table-striped table-hover table-bordered display responsive nowrap">
                                      <thead>
                                          <th>Category name </th>
                                          <th>Amount &#8377;</th>
                                      </thead>
                                      <tbody>
                                  <?php foreach ($wExpProj as $data) { 
                                  $counter++;
                                  ?>                                      
                                          <tr>
                                              <td> <?php echo $data->pName; ?></td>
                                              <td>&#8377; <?php echo number_format(round($data->total, 1)); ?></td>
                                          </tr>
                                      
                                  <?php
                                  } ?>
                                  <tbody>
                              </table>
                          <?php    }                                  
                          ?>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
              </div>
          </div>
      </div>
    </div>

    <div class="col-md-4   col-xs-6">
      <!-- small box -->
      <button type='button' data-toggle="modal" data-target="#paymentDue" class="small-box bg-purple">
        <div class="inner">
              <h3>
                  &#8377; 
                  <?php 
                     
                      echo number_format($totalDue->total);
                  ?>
              </h3>

              <p>Total payment due</p>
          </div>
          <div class="icon">
              <i class="fa fa-bank"></i>
          </div>
      <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
      </button>

      <div class="modal fade" id="paymentDue" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
          <div class="modal-dialog modal-sm" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="exampleModalLabel">
                          Total payment due &nbsp;&nbsp;&nbsp;
                          <strong>&#8377;
                          <?php 
                              echo number_format($totalDue->total);
                          ?>
                          </strong>
                      </h4>
                  </div>
                  <div class="modal-body">
                          <?php
                              if ($totalDue->total == null)
                                  echo '<div class="alert alert-danger" role="alert">No attendance marked till date.</div>';
                              else{
                                  $counter = 0;
                                  ?>
                                   <table class="table table-striped table-hover table-bordered display responsive nowrap">
                                      <thead>
                                          <th>Category name </th>
                                          <th>Amount &#8377;</th>
                                      </thead>
                                      <tbody>
                                  <?php foreach ($totalDuePW as $data) { 
                                  $counter++;
                                  ?>                                      
                                          <tr>
                                              <td> <?php echo $data->pName; ?></td>
                                              <td>&#8377; <?php echo number_format(round($data->total, 1)); ?></td>
                                          </tr>
                                      
                                  <?php
                                  } ?>
                                  <tbody>
                              </table>
                          <?php    }                                  
                          ?>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
              </div>
          </div>
      </div>
    </div>

    <!-- ./col -->
            <div class="col-md-4   col-xs-6">
                <!-- small box -->
                <button type='button' data-toggle="modal" data-target="#todayscost" class="small-box bg-teal">
                    <div class="inner">
                        <h3>
                                &#8377; 
                            <?php 
                                foreach ($todaysCost as $tc) {
                                    if($tc == null)
                                        echo 0;
                                    else 
                                        echo number_format($tc);
                                }
                            ?>
                        </h3>

                        <p>Today's expense</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </button>

                <div class="modal fade" id="todayscost" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="exampleModalLabel">
                                    Today's expense &nbsp;&nbsp;&nbsp; <strong> &#8377;
                                    <?php 
                                        foreach ($todaysCost as $tc) {
                                            if($tc == null)
                                                echo 0;
                                            else 
                                                echo $tc;
                                        }
                                    ?>
                                    </strong>
                                </h4>
                            </div>
                            <div class="modal-body">
                                <?php
                                    if ($todaysCost == null)
                                        echo '<div class="alert alert-danger" role="alert">No attendance marked today</div>';
                                        else{
                                            
                                            ?>
                                                <table class="table table-hover">
                                                <thead>
                                                    <th>Project name </th>
                                                    <th>&#8377; Expense</th>
                                                </thead>
                                                <tbody>
                                            <?php foreach ($todayscostPW as $todayac) { 
                                            
                                            ?>                                      
                                                    <tr>
                                                        <td> <?php echo $todayac->pName; ?></td>
                                                        <td>&#8377; <?php echo number_format($todayac->total); ?></td>
                                                    </tr>
                                                
                                            <?php
                                            } ?>
                                            <tbody>
                                        </table>
                                    <?php    }                                  
                                    ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

  </div> <!-- end of row -->
  
</section>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"  type="text/javascript"></script>
  <script>
      $(function () {
        $("#todaysAttendanceTable").DataTable({
            columnDefs: [
               { orderable: false, targets: -1 }
            ]
        });
        var speedCanvas = document.getElementById("averageChart");

        Chart.defaults.global.defaultFontFamily = "Sensation";
        Chart.defaults.global.defaultFontSize = 18;

        var speedData = {
          labels: [
              <?php  
                  for ($x = -6; $x <= 0; $x++) {
                    $date = date('d-m-Y', strtotime($x.' days'));
                      echo '"'.$date.'", ';
                  } 
              ?>
          ],
          datasets: [{
            label: "Hajiri",
            data: [
                <?php  
                    $found = 0;
                for ($x = -6; $x <= 0; $x++) {
                  $date = date('d-m-Y', strtotime($x.' days'));
                  foreach ($avgAttendanceProjectwise as $aap) { 
                    if(date('d-m-Y', strtotime($aap->date)) == $date){
                      echo '"'.$aap->count.'", '; 
                      $found = 1;
                      break;
                    }
                  }
                  if($found != 1)
                    echo '"0", ';
                } 
                ?>
            ],
            backgroundColor:[
                "rgba(255, 99, 132, 0.5)",
                "rgba(255, 159, 64, 0.5)",
                "rgba(255, 205, 86, 0.5)",
                "rgba(75, 192, 192, 0.5)",
                "rgba(54, 162, 235, 0.5)",
                "rgba(153, 102, 255, 0.5)",
                "rgba(201, 203, 207, 0.5)"
            ],
            borderColor:[
                "rgb(255, 99, 132)",
                "rgb(255, 159, 64)",
                "rgb(255, 205, 86)",
                "rgb(75, 192, 192)",
                "rgb(54, 162, 235)",
                "rgb(153, 102, 255)",
                "rgb(201, 203, 207)"
            ],
            borderWidth: 2
          }]
        };

        var chartOptions = {
          legend: {
            display: false,
          },
          scales: {
            xAxes: [{
              gridLines: {
                display: false,
                color: "black"
              },
              scaleLabel: {
                display: true,
                labelString: "Last 7 days",
                fontColor: "red"
              }
            }],
            yAxes: [{
              ticks: {
                min: 0,
              },
              scaleLabel: {
                display: true,
                labelString: "No. of workers presents per day",
                fontColor: "green"
              }
            }]
          }
        };

        var lineChart = new Chart(speedCanvas, {
          type: 'bar',
          data: speedData,
          options: chartOptions
        });
      });
  
  </script>