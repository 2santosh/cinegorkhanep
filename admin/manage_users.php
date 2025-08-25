<?php
session_start();
include('conf/config.php'); // Database connection
include('conf/checklogin.php'); // Login check
check_login();
$admin_id = $_SESSION['admin_id']; // Current admin ID

// Handle Delete Admin Action
if (isset($_GET['delete_admin'])) {
    $id = intval($_GET['delete_admin']);

    // Prevent an admin from deleting their own account
    if ($id == $admin_id) {
        $err = "You cannot delete your own admin account.";
    } else {
        $adn = "DELETE FROM admins WHERE admin_id = ?";
        $stmt = $mysqli->prepare($adn);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();

        if ($stmt) {
            $info = "Admin Account Deleted Successfully";
            // Optionally add an admin log entry here
        } else {
            $err = "Failed to delete admin account. Please try again later.";
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
        <?php include("dist/_partials/sidebar.php"); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-users-cog"></i> Manage Administrators</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Manage Administrators</li>
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
                                <h3 class="card-title">All CineGorkha Administrators</h3>
                                <div class="card-tools">
                                    <!-- Optional: Add button to add new admin, if you have a separate add admin page -->
                                    <!-- <a href="pages_add_admin.php" class="btn btn-primary btn-sm"><i class="fas fa-user-plus"></i> Add New Admin</a> -->
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-hover table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Profile Pic</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        //fetch all admins
                                        $ret = "SELECT * FROM admins ORDER BY name ASC"; // Assuming 'admins' is your table
                                        $stmt = $mysqli->prepare($ret);
                                        $stmt->execute(); //ok
                                        $res = $stmt->get_result();
                                        $cnt = 1;
                                        while ($row = $res->fetch_object()) {
                                        ?>
                                            <tr>
                                                <td><?php echo $cnt; ?></td>
                                                <td><?php echo htmlspecialchars($row->name); ?></td>
                                                <td><?php echo htmlspecialchars($row->email); ?></td>
                                                <td>
                                                    <?php if ($row->profile_pic) { ?>
                                                        <img src="../img/<?php echo htmlspecialchars($row->profile_pic); ?>" class="img-circle elevation-2" alt="Admin Image" style="width: 40px; height: 40px;">
                                                    <?php } else { ?>
                                                        <img src="../img/default_admin.png" class="img-circle elevation-2" alt="Default Admin Image" style="width: 40px; height: 40px;">
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <!-- Link to view/edit admin details (if you have such a page) -->
                                                    <!-- <a class="btn btn-success btn-sm" href="pages_view_admin.php?admin_id=<?php echo $row->admin_id; ?>">
                                                        <i class="fas fa-eye"></i> View
                                                    </a> -->
                                                    <a class="btn btn-warning btn-sm" href="pages_edit_admin.php?admin_id=<?php echo $row->admin_id; ?>">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a class="btn btn-danger btn-sm" href="pages_manage_admins.php?delete_admin=<?php echo $row->admin_id; ?>" onclick="return confirm('Are you sure you want to delete this admin account? This action cannot be undone.');">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php $cnt = $cnt + 1;
                                        } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Profile Pic</th>
                                            <th>Action</th>
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
