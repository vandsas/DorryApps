<?php
$files = array();
$packages = $conn->query("SELECT * FROM `packages` order by rand() ");
while ($row = $packages->fetch_assoc()) {
  if (!is_dir(base_app . 'uploads/package_' . $row['id']))
    continue;
  $fopen = scandir(base_app . 'uploads/package_' . $row['id']);
  foreach ($fopen as $fname) {
    if (in_array($fname, array('.', '..')))
      continue;
    $files[] = validate_image('uploads/package_' . $row['id'] . '/' . $fname);
  }
}

$getData = $conn->query("SELECT MONTHNAME(date_created) as `bulan`, count(*) as `total_data` FROM `book_list` WHERE status = 1 OR status = 0 OR status = 3 GROUP BY MONTHNAME(date_created) ORDER BY MONTH(date_created) ASC");

if ($getData) {
  // Menyimpan hasil query dalam variabel
  $data = array();
  while ($row = mysqli_fetch_assoc($getData)) {
    $bulan = $row['bulan'];
    $totalData = $row['total_data'];
    $data[$bulan] = $totalData;
  }
}
$myData = json_encode($data);

$getUser = $conn->query("SELECT count(*) FROM `users`")->fetch_assoc()['count(*)'];

$getPackage = $conn->query("SELECT count(*) FROM `packages`")->fetch_assoc()['count(*)'];

$getOrders = $conn->query("SELECT count(*) FROM `book_list` JOIN packages ON book_list.package_id = packages.id WHERE book_list.package_id LIKE packages.id")->fetch_assoc()['count(*)'];

?>


<div class="row">
  <div class="col-sm-6 col-md-4">
    <div class="card card-stats card-primary card-round" style="background-color:#1B6B93;">
      <div class="card-body">
        <div class="row">
          <div class="col-5">
            <div class="icon-big text-center">
              <i class="fas fa-user fa-4x" style="color: #fff;"></i>
            </div>
          </div>
          <div class="col-7 col-stats">
            <div class="numbers text-white">
              <p class="card-category">Total Users</p>
              <h4 class="card-title"><?php echo $getUser ?></h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-4">
    <div class="card card-stats card-info card-round" style="background-color:#4FC0D0">
      <div class="card-body">
        <div class="row">
          <div class="col-5">
            <div class="icon-big text-center">
              <i class="fas fa-box-open fa-4x" style="color: #fff;"></i>
            </div>
          </div>
          <div class="col-7 col-stats">
            <div class="numbers text-white">
              <p class="card-category">Total Package</p>
              <h4 class="card-title"><?php echo $getPackage ?></h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-4">
    <div class="card card-stats card-warning card-round" style="background-color:#79fa52">
      <div class="card-body ">
        <div class="row">
          <div class="col-5">
            <div class="icon-big text-center">
              <i class="fab fa-blogger-b fa-4x" style="color: #fff;"></i>
            </div>
          </div>
          <div class="col-7 col-stats">
            <div class="numbers text-white">
              <p class="card-category">Total Orders</p>
              <h4 class="card-title"><?php echo $getOrders ?></h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<hr>
<h5 class="text-white">Monthly Data Orders</h5>
<hr>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<div class="container white-background">
  <div id="chart" style="background-color: #fff"></div>
  <script>
    var data = <?php echo $myData; ?>;
    var categories = Object.keys(data);

    var newData = categories.map(function(month) {
      return data[month];
    });
    var options = {
      series: [{
        name: "Orders",
        data: newData
      }],
      chart: {
        height: 350,
        type: 'line',
        zoom: {
          enabled: false
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'straight'
      },
      title: {
        text: '',
        align: 'left'
      },
      grid: {
        row: {
          colors: ['#f3f3f3', 'transparent'],
          opacity: 0.5
        },
      },
      xaxis: {
        categories: categories,
      }
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
  </script>
  </div>
  <hr>
  <hr>
  <h5 class="text-white">Most Popular Orders</h5>
  <hr>
  <div class="container white-background card card-outline card-primary">
	<div class="card-header">
		<!-- <div class="card-tools">
			<a href="?page=packages/manage" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div> -->
	</div>
	<div class="card-body">
        <div class="container-fluid">
        <table class="table table-stripped text-dark">
            <colgroup>
                <col width="5%">
                <col width="70%">
                <col width="15">
            </colgroup>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name Package</th>
                    <th>Total Orders</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i=1;
                    $qry = $conn->query("SELECT p.title AS judul, COUNT(b.package_id) AS pesanan FROM packages p JOIN book_list b ON p.id = b.package_id GROUP BY p.title ORDER BY pesanan DESC;");
                    while($row = $qry->fetch_assoc()):
                ?>
                    <tr>
                        <td class="text-center"><?php echo $i++; ?></td>
                        <td><?php echo $row['judul'] ?></td>
                        <td><?php echo $row['pesanan'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
		</div>
	</div>
</div>