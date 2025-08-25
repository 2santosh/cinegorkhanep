<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$staff_id = $_SESSION['staff_id'];

?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <?php include("dist/_partials/head.php"); ?>
    <title>Loans</title>
</head>
<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <!-- Navbar -->
    <?php include("dist/_partials/nav.php"); ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php include("dist/_partials/loan.php"); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Loans</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="pages_manage_clients.php">iBank Loans</a></li>
                <li class="breadcrumb-item active">Give Loans</li>
              </ol>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Select any action option to manage your clients</h3>
              </div>
              <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                <table id="example1" class="table table-hover table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Loan ID</th>
                      <th>Client Name</th>
                      <th>Original Loan Amount</th>
                      <th>Interest Rate</th>
                      <th>Start Date</th>
                      <th>Due Date</th>
                      <th>Total Payments Made</th>
                      <th>Pending Amount</th>
                      <th>Days Until Due</th>
                      <th>Interest Accrued</th>
                      <th>Total Amount Due</th>
                     
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    // Fetch data from the query
                    $sql = "SELECT p.loan_id, l.name, p.loan_amount AS original_loan_amount,
                    l.interest_rate, l.start_date, l.due_date, 
                    COALESCE(SUM(p.payment_amt), 0) AS total_payments_made, p.loan_amount - COALESCE(SUM(p.payment_amt), 0) AS pending_amount,
                    DATEDIFF(l.due_date, NOW()) AS days_until_due, ROUND(p.loan_amount * (l.interest_rate / 100) * DATEDIFF(NOW(), l.start_date) / 365, 2) AS interest_accrued, (p.loan_amount + ROUND(p.loan_amount * (l.interest_rate / 100) * DATEDIFF(NOW(), l.start_date) / 365, 2)) AS total_amount_due FROM LoanPayments p LEFT JOIN Loans l ON p.loan_id = l.loan_id GROUP BY p.loan_id";
                    $result = $mysqli->query($sql);
                    $cnt = 1;
                    while ($row = $result->fetch_assoc()) {
                    ?>
                      <tr>
                        <td><?php echo $cnt; ?></td>
                        <td><?php echo $row['loan_id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['original_loan_amount']; ?></td>
                        <td><?php echo $row['interest_rate']; ?></td>
                        <td><?php echo $row['start_date']; ?></td>
                        <td><?php echo $row['due_date']; ?></td>
                        <td><?php echo $row['total_payments_made']; ?></td>
                        <td><?php echo $row['pending_amount']; ?></td>
                        <td><?php echo $row['days_until_due']; ?></td>
                        <td><?php echo $row['interest_accrued']; ?></td>
                        <td><?php echo $row['total_amount_due']; ?></td>
                        </td>
                      </tr>
                    <?php $cnt = $cnt + 1;
                    } ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <?php include("dist/_partials/footer.php"); ?>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->

  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- DataTables -->
  <script src="plugins/datatables/jquery.dataTables.js"></script>
  <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="dist/js/demo.js"></script>
  <!-- page script -->
  <script>
    $(function() {
      $("#example1").DataTable();
      $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
      });
    });
  </script>
</body>

</html>
