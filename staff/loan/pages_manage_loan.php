<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$staff_id = $_SESSION['staff_id'];

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle loan payment
if (isset($_POST['pay_loan'])) {
    $tr_code = $_POST['tr_code'];
    $loan_id = $_GET['loan_id'];
    $loan_amount = $_POST['loan_amount'];
    $client_id = $_GET['client_id'];
    $client_name = $_POST['client_name'];
    $client_national_id = $_POST['client_national_id'];
    $transaction_amt = $_POST['transaction_amt'];

    // Insert loan payment transaction
    $query = "INSERT INTO loanpayments (loan_id, name, loan_amount, tr_code, client_id, borrower_national_id, payment_amt) VALUES (?,?,?,?,?,?,?)";
    $stmt = $mysqli->prepare($query);
    // Bind parameters
    $stmt->bind_param('isdsidd', $loan_id, $client_name, $loan_amount, $tr_code, $client_id, $client_national_id, $transaction_amt);

    $stmt->execute();

    // Check for errors
    if ($stmt->error) {
        echo "Error: " . $stmt->error; // Output any errors that occurred during execution
    } else {
        // Check if transaction was successful
        if ($stmt->affected_rows > 0) {
            $success = "Loan payment successful";
            
            // Decrease loan amount by payment amount
            $new_loan_amount = $loan_amount - $transaction_amt;
            $update_query = "UPDATE loans SET loan_amount = ? WHERE loan_id = ?";
            $update_stmt = $mysqli->prepare($update_query);
            $update_stmt->bind_param('di', $new_loan_amount, $loan_id);
            $update_stmt->execute();

            if ($update_stmt->error) {
                echo "Error updating loan amount: " . $update_stmt->error;
            } else {
                $success .= " Loan amount updated.";
            }
        } else {
            $err = "Failed to process loan payment. Please try again later.";
        }
    }
}
?>


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
        <?php
        $loan_id = $_GET['loan_id'];
        $ret = "SELECT * FROM  loans WHERE loan_id = ?";
        $stmt = $mysqli->prepare($ret);
        $stmt->bind_param('i', $loan_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_object();
        ?>
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Make Loan Payment</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="pages_loans.php">iBank Finances</a></li>
                                <li class="breadcrumb-item"><a href="pages_loans.php">Loans</a></li>
                                <li class="breadcrumb-item active"><?php echo $row->name; ?></li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <!-- left column -->
                        <div class="col-md-12">
                            <!-- general form elements -->
                            <div class="card card-purple">
                                <div class="card-header">
                                    <h3 class="card-title">Fill All Fields</h3>
                                </div>
                                <!-- form start -->
                                <form method="post" enctype="multipart/form-data" role="form">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label for="exampleInputEmail1">Client Name</label>
                                                <input type="text" readonly name="client_name" value="<?php echo $row->name; ?>" required class="form-control">
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label for="exampleInputEmail1">Loan Amount</label>
                                                <input type="text" readonly name="loan_amount" value="<?php echo $row->loan_amount; ?>" required class="form-control">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="exampleInputPassword1">Transaction Code</label>
                                                <?php
                                                // Generate random transaction code
                                                $tr_code = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, 10);
                                                ?>
                                                <input type="text" name="tr_code" readonly value="<?php echo $tr_code; ?>" required class="form-control">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label for="exampleInputPassword1">Client National ID No.</label>
                                                <input type="text"  name="client_national_id" required class="form-control">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="exampleInputPassword1">Amount to Pay ($)</label>
                                                <input type="text" name="transaction_amt" required class="form-control">
                                            </div>
                                        </div>

                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" name="pay_loan" class="btn btn-success">Pay Loan</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /.card -->
                        </div><!-- /.container-fluid -->
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
    <!-- bs-custom-file-input -->
    <script src="plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            bsCustomFileInput.init();
        });
    </script>
</body>

</html>
