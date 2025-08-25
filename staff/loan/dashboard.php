<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$staff_id = $_SESSION['staff_id'];

// Clear notifications and alert user that they are cleared
if (isset($_GET['Clear_Notifications'])) {
  $id = intval($_GET['Clear_Notifications']);
  $adn = "DELETE FROM  iB_notifications  WHERE notification_id = ?";
  $stmt = $mysqli->prepare($adn);
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $stmt->close();

  if ($stmt) {
    $info = "Notifications Cleared";
  } else {
    $err = "Try Again Later";
  }
}

// Queries to fetch loan-related data from the database
$result_loans = $mysqli->query("SELECT count(*) FROM loans")->fetch_assoc()['count(*)'];
$result_rateloans = $mysqli->query("SELECT count(*) FROM rateloans")->fetch_assoc()['count(*)'];
$result_loanpayments = $mysqli->query("SELECT count(*) FROM loanpayments")->fetch_assoc()['count(*)'];

// Query to fetch loan dues
$result_loan_dues = $mysqli->query("SELECT SUM(due_amount) AS total_due FROM loanpayments")->fetch_assoc()['total_due'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include("dist/_partials/head.php"); ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
  <div class="wrapper">
    <!-- Navbar -->
    <?php include("dist/_partials/nav.php"); ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php include("dist/_partials/loan.php"); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <!-- Add content header here if needed -->
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Info boxes -->
          <div class="row">
            <!-- iBank Clients -->
            <div class="col-12 col-sm-6 col-md-4">
              <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Total Loans</span>
                  <span class="info-box-number"><?php echo $result_loans; ?></span>
                </div>
              </div>
            </div>

            <!-- iBank Acc types -->
            <div class="col-12 col-sm-6 col-md-4">
              <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-briefcase"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Total Rate Loans</span>
                  <span class="info-box-number"><?php echo $result_rateloans; ?></span>
                </div>
              </div>
            </div>

            <!-- iBank Accounts -->
            <div class="col-12 col-sm-6 col-md-4">
              <div class="info-box mb-3">
                <span class="info-box-icon bg-purple elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Total Loan Payments</span>
                  <span class="info-box-number"><?php echo $result_loanpayments; ?></span>
                </div>
              </div>
            </div>
          </div>

          <!-- Loan Dues -->
          <div class="row">
            <div class="col-md-12">
              <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-money-check-alt"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Total Loan Dues</span>
                  <span class="info-box-number"><?php echo $result_loan_dues; ?></span>
                </div>
              </div>
            </div>
          </div>

        </div>
        <!--/. container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <?php include("dist/_partials/footer.php"); ?>
  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.js"></script>

  <!-- OPTIONAL SCRIPTS -->
  <script src="dist/js/demo.js"></script>

  <!-- PAGE PLUGINS -->
  <!-- jQuery Mapael -->
  <script src="plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
  <script src="plugins/raphael/raphael.min.js"></script>
  <script src="plugins/jquery-mapael/jquery.mapael.min.js"></script>
  <script src="plugins/jquery-mapael/maps/usa_states.min.js"></script>
  <!-- ChartJS -->
  <script src="plugins/chart.js/Chart.min.js"></script>

  <!-- PAGE SCRIPTS -->
  <script src="dist/js/pages/dashboard2.js"></script>

  <!-- Load Canvas JS -->
  <script src="plugins/canvasjs.min.js"></script>
  <!-- Load Few Charts -->
  <script>
    // JavaScript code for rendering charts
  </script>
</body>
</html>
