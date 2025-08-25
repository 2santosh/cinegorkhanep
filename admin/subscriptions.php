<?php
session_start();
include('conf/config.php'); // Database connection
include('conf/checklogin.php'); // Login check
check_login();
$admin_id = $_SESSION['admin_id']; // Current admin ID

// Handle Add New Subscription Action
if (isset($_POST['add_subscription'])) {
    $user_id = intval($_POST['user_id']);
    $plan_name = $_POST['plan_name'];
    $price = floatval($_POST['price']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];

    // Fetch user name for logging/display if needed
    $stmt_user = $mysqli->prepare("SELECT name FROM users WHERE user_id = ?");
    $stmt_user->bind_param('i', $user_id);
    $stmt_user->execute();
    $stmt_user->bind_result($user_name);
    $stmt_user->fetch();
    $stmt_user->close();

    $query = "INSERT INTO subscriptions (user_id, plan_name, price, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('isdsss', $user_id, $plan_name, $price, $start_date, $end_date, $status);
    $stmt->execute();

    if ($stmt) {
        $success = "Subscription added successfully for " . htmlspecialchars($user_name) . "!";
        // Optionally add an admin log entry here
    } else {
        $err = "Failed to add subscription. Please try again.";
    }
    $stmt->close();
}

// Handle Edit Subscription Action
if (isset($_POST['update_subscription'])) {
    $subscription_id = intval($_POST['subscription_id']);
    $user_id = intval($_POST['user_id']); // User can be changed, though usually not for subscriptions
    $plan_name = $_POST['plan_name'];
    $price = floatval($_POST['price']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];

    $query = "UPDATE subscriptions SET user_id = ?, plan_name = ?, price = ?, start_date = ?, end_date = ?, status = ? WHERE subscription_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('isdsssi', $user_id, $plan_name, $price, $start_date, $end_date, $status, $subscription_id);
    $stmt->execute();

    if ($stmt) {
        $success = "Subscription updated successfully!";
        // Optionally add an admin log entry here
    } else {
        $err = "Failed to update subscription. Please try again.";
    }
    $stmt->close();
}

// Handle Delete Subscription Action
if (isset($_GET['delete_subscription'])) {
    $subscription_id = intval($_GET['delete_subscription']);

    $adn = "DELETE FROM subscriptions WHERE subscription_id = ?";
    $stmt = $mysqli->prepare($adn);
    $stmt->bind_param('i', $subscription_id);
    $stmt->execute();
    $stmt->close();

    if ($stmt) {
        $info = "Subscription record deleted successfully!";
        // Optionally add an admin log entry here
    } else {
        $err = "Failed to delete subscription record. Please try again.";
    }
}


// Fetch all subscriptions for display
$ret = "SELECT s.*, u.name as user_name, u.email as user_email
        FROM subscriptions s
        JOIN users u ON s.user_id = u.user_id
        ORDER BY s.start_date DESC";
$stmt = $mysqli->prepare($ret);
$stmt->execute();
$res = $stmt->get_result();
$subscriptions = $res->fetch_all(MYSQLI_ASSOC); // Fetch all results as an associative array
$stmt->close();

// Fetch all users for the 'Add/Edit Subscription' modals
$users_query = "SELECT user_id, name, email FROM users ORDER BY name ASC";
$users_result = $mysqli->query($users_query);
$users = $users_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<?php include("dist/_partials/head.php"); ?>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include("dist/_partials/nav.php"); ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include("dist/_partials/sidebar.php"); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-credit-card"></i> Manage Subscriptions</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Manage Subscriptions</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">All User Subscriptions</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSubscriptionModal">
                                            <i class="fas fa-plus"></i> Add New Subscription
                                        </button>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="subscriptionsTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>User Name</th>
                                                <th>Plan Name</th>
                                                <th>Price</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $cnt = 1;
                                            foreach ($subscriptions as $sub) { ?>
                                                <tr>
                                                    <td><?php echo $cnt; ?></td>
                                                    <td><?php echo htmlspecialchars($sub['user_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($sub['plan_name']); ?></td>
                                                    <td>$<?php echo number_format($sub['price'], 2); ?></td>
                                                    <td><?php echo date("d-M-Y", strtotime($sub['start_date'])); ?></td>
                                                    <td><?php echo date("d-M-Y", strtotime($sub['end_date'])); ?></td>
                                                    <td>
                                                        <?php
                                                        if ($sub['status'] == 'active') {
                                                            echo '<span class="badge badge-success">Active</span>';
                                                        } elseif ($sub['status'] == 'expired') {
                                                            echo '<span class="badge badge-danger">Expired</span>';
                                                        } else {
                                                            echo '<span class="badge badge-warning">Canceled</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-warning btn-sm edit-subscription-btn"
                                                            data-toggle="modal"
                                                            data-target="#editSubscriptionModal"
                                                            data-sub-id="<?php echo $sub['subscription_id']; ?>"
                                                            data-user-id="<?php echo $sub['user_id']; ?>"
                                                            data-plan-name="<?php echo htmlspecialchars($sub['plan_name']); ?>"
                                                            data-price="<?php echo htmlspecialchars($sub['price']); ?>"
                                                            data-start-date="<?php echo htmlspecialchars($sub['start_date']); ?>"
                                                            data-end-date="<?php echo htmlspecialchars($sub['end_date']); ?>"
                                                            data-status="<?php echo htmlspecialchars($sub['status']); ?>"
                                                            title="Edit Subscription">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <a href="pages_manage_subscriptions.php?delete_subscription=<?php echo $sub['subscription_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this subscription record?');" title="Delete Subscription">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php $cnt++;
                                            } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>#</th>
                                                <th>User Name</th>
                                                <th>Plan Name</th>
                                                <th>Price</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Add Subscription Modal -->
        <div class="modal fade" id="addSubscriptionModal" tabindex="-1" role="dialog" aria-labelledby="addSubscriptionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addSubscriptionModalLabel">Add New Subscription</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="add_user_id"><i class="fas fa-user"></i> Select User</label>
                                <select name="user_id" id="add_user_id" class="form-control" required>
                                    <option value="">-- Select User --</option>
                                    <?php foreach ($users as $user) { ?>
                                        <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['name']); ?> (<?php echo htmlspecialchars($user['email']); ?>)</option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="add_plan_name"><i class="fas fa-clipboard-list"></i> Plan Name</label>
                                <input type="text" name="plan_name" id="add_plan_name" class="form-control" placeholder="e.g., Premium, Basic" required>
                            </div>
                            <div class="form-group">
                                <label for="add_price"><i class="fas fa-dollar-sign"></i> Price</label>
                                <input type="number" step="0.01" name="price" id="add_price" class="form-control" placeholder="e.g., 9.99" required min="0">
                            </div>
                            <div class="form-group">
                                <label for="add_start_date"><i class="fas fa-calendar-alt"></i> Start Date</label>
                                <input type="date" name="start_date" id="add_start_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="add_end_date"><i class="fas fa-calendar-check"></i> End Date</label>
                                <input type="date" name="end_date" id="add_end_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="add_status"><i class="fas fa-info-circle"></i> Status</label>
                                <select name="status" id="add_status" class="form-control" required>
                                    <option value="active">Active</option>
                                    <option value="expired">Expired</option>
                                    <option value="canceled">Canceled</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="add_subscription" class="btn btn-primary"><i class="fas fa-plus"></i> Add Subscription</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.modal -->

        <!-- Edit Subscription Modal -->
        <div class="modal fade" id="editSubscriptionModal" tabindex="-1" role="dialog" aria-labelledby="editSubscriptionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editSubscriptionModalLabel">Edit Subscription</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <input type="hidden" name="subscription_id" id="edit_subscription_id">
                            <div class="form-group">
                                <label for="edit_user_id"><i class="fas fa-user"></i> Select User</label>
                                <select name="user_id" id="edit_user_id" class="form-control" required>
                                    <option value="">-- Select User --</option>
                                    <?php foreach ($users as $user) { ?>
                                        <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['name']); ?> (<?php echo htmlspecialchars($user['email']); ?>)</option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_plan_name"><i class="fas fa-clipboard-list"></i> Plan Name</label>
                                <input type="text" name="plan_name" id="edit_plan_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_price"><i class="fas fa-dollar-sign"></i> Price</label>
                                <input type="number" step="0.01" name="price" id="edit_price" class="form-control" required min="0">
                            </div>
                            <div class="form-group">
                                <label for="edit_start_date"><i class="fas fa-calendar-alt"></i> Start Date</label>
                                <input type="date" name="start_date" id="edit_start_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_end_date"><i class="fas fa-calendar-check"></i> End Date</label>
                                <input type="date" name="end_date" id="edit_end_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_status"><i class="fas fa-info-circle"></i> Status</label>
                                <select name="status" id="edit_status" class="form-control" required>
                                    <option value="active">Active</option>
                                    <option value="expired">Expired</option>
                                    <option value="canceled">Canceled</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="update_subscription" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.modal -->

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
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="plugins/datatables/jquery.dataTables.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTables for the subscriptions table
            $("#subscriptionsTable").DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });

            // Populate Edit Subscription Modal when Edit button is clicked
            $('.edit-subscription-btn').on('click', function() {
                var subId = $(this).data('sub-id');
                var userId = $(this).data('user-id');
                var planName = $(this).data('plan-name');
                var price = $(this).data('price');
                var startDate = $(this).data('start-date');
                var endDate = $(this).data('end-date');
                var status = $(this).data('status');

                $('#edit_subscription_id').val(subId);
                $('#edit_user_id').val(userId);
                $('#edit_plan_name').val(planName);
                $('#edit_price').val(price);
                $('#edit_start_date').val(startDate);
                $('#edit_end_date').val(endDate);
                $('#edit_status').val(status);
            });

            // Clear Add Subscription Modal fields when modal is hidden
            $('#addSubscriptionModal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset(); // Reset form fields
            });

            // Clear Edit Subscription Modal fields when modal is hidden (optional)
            $('#editSubscriptionModal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset(); // Reset form fields
            });
        });
    </script>
</body>
</html>
