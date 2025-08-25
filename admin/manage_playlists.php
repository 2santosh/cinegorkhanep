<?php
session_start();
include('conf/config.php'); // Database connection
include('conf/checklogin.php'); // Login check
check_login();
$admin_id = $_SESSION['admin_id']; // Current admin ID

// Handle Add New Playlist Action
if (isset($_POST['add_playlist'])) {
    $user_id = intval($_POST['user_id']);
    $name = $_POST['name'];

    $query = "INSERT INTO playlists (user_id, name) VALUES (?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('is', $user_id, $name);
    $stmt->execute();

    if ($stmt) {
        $success = "Playlist '" . htmlspecialchars($name) . "' added successfully!";
        // Optionally add an admin log entry here
    } else {
        $err = "Failed to add playlist. Please try again.";
    }
    $stmt->close();
}

// Handle Edit Playlist Action
if (isset($_POST['update_playlist'])) {
    $playlist_id = intval($_POST['playlist_id']);
    $user_id = intval($_POST['user_id']);
    $name = $_POST['name'];

    $query = "UPDATE playlists SET user_id = ?, name = ? WHERE playlist_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('isi', $user_id, $name, $playlist_id);
    $stmt->execute();

    if ($stmt) {
        $success = "Playlist '" . htmlspecialchars($name) . "' updated successfully!";
        // Optionally add an `admin log` entry here
    } else {
        $err = "Failed to update playlist. Please try again.";
    }
    $stmt->close();
}

// Handle Delete Playlist Action
if (isset($_GET['delete_playlist'])) {
    $playlist_id = intval($_GET['delete_playlist']);

    // Check if there are any movies associated with this playlist before deleting
    $check_movies_query = "SELECT COUNT(*) FROM playlist_movies WHERE playlist_id = ?";
    $stmt_check = $mysqli->prepare($check_movies_query);
    $stmt_check->bind_param('i', $playlist_id);
    $stmt_check->execute();
    $stmt_check->bind_result($movie_count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($movie_count > 0) {
        $err = "Cannot delete playlist: There are " . $movie_count . " movies in this playlist. Please remove movies from the playlist first.";
    } else {
        $adn = "DELETE FROM playlists WHERE playlist_id = ?";
        $stmt = $mysqli->prepare($adn);
        $stmt->bind_param('i', $playlist_id);
        $stmt->execute();
        $stmt->close();

        if ($stmt) {
            $info = "Playlist deleted successfully!";
            // Optionally add an `admin log` entry here
        } else {
            $err = "Failed to delete playlist. Please try again.";
        }
    }
}

// Fetch all playlists for display
$ret = "SELECT p.*, u.name as user_name, u.email as user_email
        FROM playlists p
        JOIN users u ON p.user_id = u.user_id
        ORDER BY p.created_at DESC";
$stmt = $mysqli->prepare($ret);
$stmt->execute();
$res = $stmt->get_result();
$playlists = $res->fetch_all(MYSQLI_ASSOC); // Fetch all results as an associative array
$stmt->close();

// Fetch all users for the 'Add/Edit Playlist' modals
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
                            <h1><i class="fas fa-list-alt"></i> Manage Playlists</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Manage Playlists</li>
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
                                    <h3 class="card-title">All User Playlists</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addPlaylistModal">
                                            <i class="fas fa-plus"></i> Add New Playlist
                                        </button>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="playlistsTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">#</th>
                                                <th style="width: 30%;">Playlist Name</th>
                                                <th style="width: 30%;">User</th>
                                                <th style="width: 20%;">Created At</th>
                                                <th style="width: 15%;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $cnt = 1;
                                            foreach ($playlists as $playlist) { ?>
                                                <tr>
                                                    <td><?php echo $cnt; ?></td>
                                                    <td><?php echo htmlspecialchars($playlist['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($playlist['user_name']); ?> (<?php echo htmlspecialchars($playlist['user_email']); ?>)</td>
                                                    <td><?php echo date("d-M-Y h:m:s", strtotime($playlist['created_at'])); ?></td>
                                                    <td>
                                                        <button class="btn btn-warning btn-sm edit-playlist-btn"
                                                            data-toggle="modal"
                                                            data-target="#editPlaylistModal"
                                                            data-playlist-id="<?php echo $playlist['playlist_id']; ?>"
                                                            data-user-id="<?php echo $playlist['user_id']; ?>"
                                                            data-playlist-name="<?php echo htmlspecialchars($playlist['name']); ?>"
                                                            title="Edit Playlist">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <a href="pages_manage_playlists.php?delete_playlist=<?php echo $playlist['playlist_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this playlist? This will fail if there are movies linked to it.');" title="Delete Playlist">
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
                                                <th>Playlist Name</th>
                                                <th>User</th>
                                                <th>Created At</th>
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

        <!-- Add Playlist Modal -->
        <div class="modal fade" id="addPlaylistModal" tabindex="-1" role="dialog" aria-labelledby="addPlaylistModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPlaylistModalLabel">Add New Playlist</h5>
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
                                <label for="add_playlist_name"><i class="fas fa-list-alt"></i> Playlist Name</label>
                                <input type="text" name="name" id="add_playlist_name" class="form-control" placeholder="e.g., My Favorites, Action Movies" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="add_playlist" class="btn btn-primary"><i class="fas fa-plus"></i> Add Playlist</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.modal -->

        <!-- Edit Playlist Modal -->
        <div class="modal fade" id="editPlaylistModal" tabindex="-1" role="dialog" aria-labelledby="editPlaylistModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPlaylistModalLabel">Edit Playlist</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <input type="hidden" name="playlist_id" id="edit_playlist_id">
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
                                <label for="edit_playlist_name"><i class="fas fa-list-alt"></i> Playlist Name</label>
                                <input type="text" name="name" id="edit_playlist_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="update_playlist" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
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
            // Initialize DataTables for the playlists table
            $("#playlistsTable").DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });

            // Populate Edit Playlist Modal when Edit button is clicked
            $('.edit-playlist-btn').on('click', function() {
                var playlistId = $(this).data('playlist-id');
                var userId = $(this).data('user-id');
                var playlistName = $(this).data('playlist-name');

                $('#edit_playlist_id').val(playlistId);
                $('#edit_user_id').val(userId);
                $('#edit_playlist_name').val(playlistName);
            });

            // Clear Add Playlist Modal fields when modal is hidden
            $('#addPlaylistModal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset(); // Reset form fields
            });

            // Clear Edit Playlist Modal fields when modal is hidden (optional)
            $('#editPlaylistModal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset(); // Reset form fields
            });
        });
    </script>
</body>
</html>
