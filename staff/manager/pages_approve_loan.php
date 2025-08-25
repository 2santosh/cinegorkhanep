<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();

if (isset($_GET['loan_id'])) {
    $loan_id = $_GET['loan_id'];

    // Fetch loan details
    $fetch_loan_query = "SELECT * FROM loans WHERE loan_id = ?";
    $stmt_fetch_loan = $mysqli->prepare($fetch_loan_query);
    $stmt_fetch_loan->bind_param('i', $loan_id);
    $stmt_fetch_loan->execute();
    $result_loan = $stmt_fetch_loan->get_result();
    $loan_data = $result_loan->fetch_assoc();

    // Check if loan data exists
    if ($loan_data) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Check if the form was submitted
            if (isset($_POST['action'])) {
                $action = $_POST['action'];
                if ($action === "Approve") { // Check for "Approve" action
                    // Insert loan data into loanpayments table
                    $insert_loan_payment_query = "INSERT INTO loanpayments (loan_id, name, loan_amount, loan_type, client_id, payment_amt, interest_rate, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt_insert_loan_payment = $mysqli->prepare($insert_loan_payment_query);

                    // Check if the prepare statement succeeded
                    if ($stmt_insert_loan_payment) {
                        // Extract loan data into variables for binding
                        $name = $loan_data['name'];
                        $loan_amount = $loan_data['loan_amount'];
                        $loan_type = $loan_data['loan_type'];
                        $client_id = $loan_data['client_id'];
                        $payment_amt = $loan_data['loan_amount']; // Initially same as loan_amount
                        $interest_rate = $loan_data['interest_rate'];
                        $start_date = $loan_data['start_date'];
                        $end_date = $loan_data['end_date'];

                        // Bind parameters for the insert statement
                        $stmt_insert_loan_payment->bind_param(
                            'isdsdssss',
                            $loan_id,
                            $name,
                            $loan_amount,
                            $loan_type,
                            $client_id,
                            $payment_amt,
                            $interest_rate,
                            $start_date,
                            $end_date
                        );

                        // Execute the insert statement
                        $stmt_insert_loan_payment->execute();

                        // Update loan status to "approved"
                        $update_loan_status_query = "UPDATE loans SET status = 'approved' WHERE loan_id = ?";
                        $stmt_update_loan_status = $mysqli->prepare($update_loan_status_query);
                        $stmt_update_loan_status->bind_param('i', $loan_id);
                        $stmt_update_loan_status->execute();

                        // Redirect back to the loans page after approval
                        header("Location: dashboard.php");
                        exit();
                    } else {
                        // Handle prepare statement error
                        die('Error: ' . $mysqli->error);
                    }
                } elseif ($action === "Reject") { // Check for "Reject" action
                    // Delete loan record after rejection
                    $delete_loan_query = "DELETE FROM loans WHERE loan_id = ?";
                    $stmt_delete_loan = $mysqli->prepare($delete_loan_query);
                    $stmt_delete_loan->bind_param('i', $loan_id);
                    $stmt_delete_loan->execute();

                    // Redirect back to the loans page after rejection
                    header("Location: pages_manage_loans.php");
                    exit();
                }
            }
        }
    } else {
        // Handle the case where loan data is not found
        echo "Loan data not found.";
    }
} else {
    // Handle the case where loan_id is not set
    echo "Loan ID not provided.";
}
?>


<!DOCTYPE html>
<html>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<?php include("dist/_partials/head.php"); ?>
<title>Approve or Reject Loan</title>
<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <!-- Navbar -->
    <?php include("dist/_partials/nav.php"); ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php include("dist/_partials/manager.php"); ?>
    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Approve or Reject Loan</h1>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Small boxes (Stat box) -->
          <div class="row">
            <div class="col-lg-6">
              <div class="card">
                <div class="card-body">
                  <form method="post">
                    <div class="form-group">
                      <input type="submit" class="btn btn-success" name="action" value="Approve">
                      <input type="submit" class="btn btn-danger" name="action" value="Reject">
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- /.row -->
        </div><!-- /.container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <?php include("dist/_partials/footer.php"); ?>
  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.min.js"></script>
</body>
</html>
