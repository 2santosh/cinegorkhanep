<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();


?>
<!-- Log on to codeastro.com for more projects! -->
<!DOCTYPE html>
<html>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<?php include("dist/_partials/head.php"); ?>

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
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Loans</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="pages_loans.php">iBank Finances</a></li>
                <li class="breadcrumb-item active">Loans</li>
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
                <h3 class="card-title">Select any loan account to manage</h3>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example1" class="table table-hover table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Loan ID</th>
                        <th>Client ID</th>
                        <th>Client Name</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Interest Rate (%)</th>
                        <th>Loan Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Loan Amount (USD)</th>
                        <th>Last Instalment</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      // Fetch all loans with related data
                     $ret = "SELECT l.loan_id, l.client_id, l.loan_amount, l.interest_rate, l.start_date, l.end_date, l.status, l.created_at,
                              c.name AS name, c.phone AS client_phone, c.email AS client_email, c.address AS client_address,
                              p.payment_id, p.loan_amount AS payment_amount, p.payment_date,
                              l.loan_document, l.loan_type, l.deposit_document
                              FROM loans l
                              INNER JOIN ib_clients c ON l.client_id = c.client_id
                              LEFT JOIN loanpayments p ON l.loan_id = p.loan_id
                              ORDER BY l.loan_id DESC";
                      $stmt = $mysqli->prepare($ret);
                      $stmt->execute();
                      $res = $stmt->get_result();
                      $cnt = 1;
                      while ($row = $res->fetch_object()) {
                      ?>
                        <tr>
                          <td><?php echo $cnt; ?></td>
                          <td><?php echo $row->loan_id; ?></td>
                          <td><?php echo $row->client_id; ?></td>
                          <td><?php echo $row->name; ?></td>
                          <td><?php echo $row->client_phone; ?></td>
                          <td><?php echo $row->client_email; ?></td>
                          <td><?php echo $row->interest_rate; ?></td>
                          <td><?php echo $row->loan_type; ?></td>
                          <td><?php echo date('Y-m-d', strtotime($row->start_date)); ?></td>
                          <td><?php echo date('Y-m-d', strtotime($row->end_date)); ?></td>
                          <td><?php echo $row->status; ?>
                          <td><?php echo number_format($row->loan_amount, 2); ?></td>
                          <td><?php echo $row->payment_amount; ?></td>
                          </td>
                          <td>
                            <a class="btn btn-success btn-sm" href="pages_manage_loan.php?loan_id=<?php echo $row->loan_id; ?>&client_id=<?php echo $row->client_id; ?>&name=<?php echo $row->name; ?>">
                              <i class="fas fa-money-check-alt"></i>
                              Payment Loan
                            </a>
                          </td>


                        </tr>
                      <?php $cnt = $cnt + 1;
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
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