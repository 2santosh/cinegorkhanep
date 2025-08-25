<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();

if (isset($_POST['add_loan_rate'])) {
    // Capture form data
    $loan_type = $_POST['loan_type'];
    $interest_rate = $_POST['interest_rate'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $loan_amount = $_POST['loan_amount'];
    $loan_document = $_POST['loan_document'];
    $deposit_document = $_POST['deposit_document'];

    // Insert data into rateLoans table
    $insert_loan_rate_query = "INSERT INTO rateLoans (loan_type, interest_rate, start_date, end_date, loan_amount, loan_document, deposit_document) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert_loan_rate = $mysqli->prepare($insert_loan_rate_query);
    
    // Bind parameters
    $stmt_insert_loan_rate->bind_param(
        'sissdss',
        $loan_type,
        $interest_rate,
        $start_date,
        $end_date,
        $loan_amount,
        $loan_document,
        $deposit_document
    );

    // Execute query
    $stmt_insert_loan_rate->execute();

    // Redirect back to the page after adding loan rate
    header("Location: pages_loan_interest_table.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<?php include("dist/_partials/head.php"); ?>

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
                            <h1>Add Loan Interest Rate</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="pages_manage_loans.php">iBank Loans</a></li>
                                <li class="breadcrumb-item active">Add Interest Rate</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
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
                                        <div class="col-md-4 form-group">
                                            <label for="loan_type">Loan Type</label>
                                            <input type="text" name="loan_type" required class="form-control" id="loan_type">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="interest_rate">Interest Rate (%)</label>
                                            <input type="text" name="interest_rate" required class="form-control" id="interest_rate">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="start_date">Start Date</label>
                                            <input type="date" name="start_date" required class="form-control" id="start_date">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <label for="end_date">End Date</label>
                                            <input type="date" name="end_date" required class="form-control" id="end_date">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="loan_amount">Loan Amount</label>
                                            <input type="text" name="loan_amount" required class="form-control" id="loan_amount">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="loan_document">Loan Document</label>
                                            <input type="text" name="loan_document" required class="form-control" id="loan_document">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <label for="deposit_document">Deposit Document</label>
                                            <input type="text" name="deposit_document" required class="form-control" id="deposit_document">
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" name="add_loan_rate" class="btn btn-success">Add Interest Rate</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
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
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
</body>

</html>
