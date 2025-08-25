<?php
session_start();
include('conf/config.php'); // Database connection
include('conf/checklogin.php'); // Login check
check_login();
$admin_id = $_SESSION['admin_id']; // Current admin ID

// Handle Add New Genre Action
if (isset($_POST['add_genre'])) {
    $genre_name = $_POST['genre_name'];
    $description = $_POST['description'];

    $query = "INSERT INTO genres (genre_name, description) VALUES (?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ss', $genre_name, $description);
    $stmt->execute();

    if ($stmt) {
        $success = "Movie genre added successfully!";
        // Optionally add an admin log entry here
    } else {
        $err = "Failed to add movie genre. Please try again.";
    }
    $stmt->close();
}

// Handle Edit Genre Action
if (isset($_POST['update_genre'])) {
    $genre_id = intval($_POST['genre_id']);
    $genre_name = $_POST['genre_name'];
    $description = $_POST['description'];

    $query = "UPDATE genres SET genre_name = ?, description = ? WHERE genre_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ssi', $genre_name, $description, $genre_id);
    $stmt->execute();

    if ($stmt) {
        $success = "Movie genre updated successfully!";
        // Optionally add an admin log entry here
    } else {
        $err = "Failed to update movie genre. Please try again.";
    }
    $stmt->close();
}

// Handle Delete Genre Action
if (isset($_GET['delete_genre'])) {
    $genre_id = intval($_GET['delete_genre']);

    // Check if there are any movies associated with this genre before deleting
    $check_movies_query = "SELECT COUNT(*) FROM movies WHERE genre_id = ?";
    $stmt_check = $mysqli->prepare($check_movies_query);
    $stmt_check->bind_param('i', $genre_id);
    $stmt_check->execute();
    $stmt_check->bind_result($movie_count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($movie_count > 0) {
        $err = "Cannot delete genre: There are " . $movie_count . " movies associated with this genre. Please reassign or delete those movies first.";
    } else {
        $adn = "DELETE FROM genres WHERE genre_id = ?";
        $stmt = $mysqli->prepare($adn);
        $stmt->bind_param('i', $genre_id);
        $stmt->execute();
        $stmt->close();

        if ($stmt) {
            $info = "Movie genre deleted successfully!";
            // Optionally add an admin log entry here
        } else {
            $err = "Failed to delete movie genre. Please try again.";
        }
    }
}


// Fetch all genres for display
$ret = "SELECT * FROM genres ORDER BY genre_name ASC";
$stmt = $mysqli->prepare($ret);
$stmt->execute();
$res = $stmt->get_result();
$genres = $res->fetch_all(MYSQLI_ASSOC); // Fetch all results as an associative array
$stmt->close();

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
                            <h1><i class="fas fa-tags"></i> Manage Movie Genres</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Manage Genres</li>
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
                                    <h3 class="card-title">All Defined Movie Genres</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addGenreModal">
                                            <i class="fas fa-plus"></i> Add New Genre
                                        </button>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="genresTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">#</th>
                                                <th style="width: 25%;">Genre Name</th>
                                                <th style="width: 50%;">Description</th>
                                                <th style="width: 20%;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $cnt = 1;
                                            foreach ($genres as $genre) { ?>
                                                <tr>
                                                    <td><?php echo $cnt; ?></td>
                                                    <td><?php echo htmlspecialchars($genre['genre_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($genre['description']); ?></td>
                                                    <td>
                                                        <button class="btn btn-warning btn-sm edit-genre-btn"
                                                            data-toggle="modal"
                                                            data-target="#editGenreModal"
                                                            data-genre-id="<?php echo $genre['genre_id']; ?>"
                                                            data-genre-name="<?php echo htmlspecialchars($genre['genre_name']); ?>"
                                                            data-genre-description="<?php echo htmlspecialchars($genre['description']); ?>"
                                                            title="Edit Genre">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <a href="pages_manage_genres.php?delete_genre=<?php echo $genre['genre_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this genre? All associated movies must be unlinked first.');" title="Delete Genre">
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
                                                <th>Genre Name</th>
                                                <th>Description</th>
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

        <!-- Add Genre Modal -->
        <div class="modal fade" id="addGenreModal" tabindex="-1" role="dialog" aria-labelledby="addGenreModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addGenreModalLabel">Add New Movie Genre</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="add_genre_name"><i class="fas fa-tag"></i> Genre Name</label>
                                <input type="text" name="genre_name" id="add_genre_name" class="form-control" placeholder="e.g., Action, Comedy" required>
                            </div>
                            <div class="form-group">
                                <label for="add_description"><i class="fas fa-align-left"></i> Description</label>
                                <textarea name="description" id="add_description" class="form-control" rows="3" placeholder="A brief description of the genre"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="add_genre" class="btn btn-primary"><i class="fas fa-plus"></i> Add Genre</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.modal -->

        <!-- Edit Genre Modal -->
        <div class="modal fade" id="editGenreModal" tabindex="-1" role="dialog" aria-labelledby="editGenreModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editGenreModalLabel">Edit Movie Genre</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <input type="hidden" name="genre_id" id="edit_genre_id">
                            <div class="form-group">
                                <label for="edit_genre_name"><i class="fas fa-tag"></i> Genre Name</label>
                                <input type="text" name="genre_name" id="edit_genre_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_description"><i class="fas fa-align-left"></i> Description</label>
                                <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="update_genre" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
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
            // Initialize DataTables for the genres table
            $("#genresTable").DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });

            // Populate Edit Genre Modal when Edit button is clicked
            $('.edit-genre-btn').on('click', function() {
                var genreId = $(this).data('genre-id');
                var genreName = $(this).data('genre-name');
                var genreDescription = $(this).data('genre-description');

                $('#edit_genre_id').val(genreId);
                $('#edit_genre_name').val(genreName);
                $('#edit_description').val(genreDescription);
            });

            // Clear Add Genre Modal fields when modal is hidden
            $('#addGenreModal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset(); // Reset form fields
            });

            // Clear Edit Genre Modal fields when modal is hidden (optional, but good practice)
            $('#editGenreModal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset(); // Reset form fields
            });
        });
    </script>
</body>
</html>
