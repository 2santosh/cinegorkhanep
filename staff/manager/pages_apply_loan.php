<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$staff_id = $_SESSION['staff_id'];

// Check if form is submitted
if (isset($_POST['submit'])) {
    // Handle form data
    $client_id = $_POST['client_id'];
    $loan_amount = $_POST['loan_amount'];
    $interest_rate = $_POST['interest_rate'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $loan_type = $_POST['loan_type'];

    // Fetch client's name based on client_id
    $stmt_client = $mysqli->prepare("SELECT name FROM ib_clients WHERE client_id = ?");
    if (!$stmt_client) {
        die("Error in preparing statement: " . $mysqli->error);
    }

    $stmt_client->bind_param("i", $client_id);
    $stmt_client->execute();

    if (!$stmt_client->execute()) {
        die("Error in executing statement: " . $stmt_client->error);
    }

    $stmt_client->store_result();
    $stmt_client->bind_result($client_name);
    $stmt_client->fetch();

    $stmt_client->close();


    // Handle file uploads
    $loan_document_name = $_FILES['loan_document']['name'];
    $loan_document_temp = $_FILES['loan_document']['tmp_name'];
    $deposit_document_name = $_FILES['deposit_document']['name'];
    $deposit_document_temp = $_FILES['deposit_document']['tmp_name'];

    // Move uploaded files to desired directory
    $loan_document_path = './../uploads/' . $loan_document_name;
    $deposit_document_path = './../uploads/' . $deposit_document_name;
    move_uploaded_file($loan_document_temp, $loan_document_path);
    move_uploaded_file($deposit_document_temp, $deposit_document_path);

    // Prepare and execute SQL query to insert data into database
    $stmt = $mysqli->prepare("INSERT INTO loans (client_id, name, loan_amount, interest_rate, start_date, end_date, loan_type, loan_document, deposit_document) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error in preparing statement: " . $mysqli->error);
    }

    $stmt->bind_param("isddsssss", $client_id, $client_name, $loan_amount, $interest_rate, $start_date, $end_date, $loan_type, $loan_document_path, $deposit_document_path);

    if (!$stmt->execute()) {
        die("Error in executing statement: " . $stmt->error);
    }

    echo "Loan application submitted successfully.";

    $stmt->close();
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
        <?php include("dist/_partials/manager.php"); ?>

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

            <!-- Loan Application Form -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Loan Application Form</h3>
                </div>
                <div class="card-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <label for="client_id">Client ID:</label><br>
                        <input type="text" id="client_id" name="client_id" required><br>

                        <label for="loan_amount">Loan Amount:</label><br>
                        <input type="number" id="loan_amount" name="loan_amount" required><br>

                        <label for="interest_rate">Interest Rate:</label><br>
                        <input type="number" step="0.01" id="interest_rate" name="interest_rate" required><br>

                        <label for="start_date">Start Date:</label><br>
                        <input type="date" id="start_date" name="start_date" required><br>

                        <label for="end_date">End Date:</label><br>
                        <input type="date" id="end_date" name="end_date" required><br>

                        <label for="loan_type">Loan Type:</label><br>
                        <select id="loan_type" name="loan_type">
                            <option value="personal">Personal</option>
                            <option value="business">Business</option>
                            <option value="education">Education</option>
                            <option value="mortgage">Mortgage</option>
                            <option value="other">Other</option>
                        </select><br>

                        <label for="loan_document">Loan Document:</label><br>
                        <input type="file" id="loan_document" name="loan_document" required><br>

                        <label for="deposit_document">Deposit Document:</label><br>
                        <input type="file" id="deposit_document" name="deposit_document" required><br>

                        <input type="submit" name="submit" value="Submit">
                    </form>
                </div>
            </div>
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