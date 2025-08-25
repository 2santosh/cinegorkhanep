<?php
session_start();
include('conf/config.php'); // Database connection
include('conf/checklogin.php'); // Login check
check_login();
$admin_id = $_SESSION['admin_id']; // Current admin ID

// Handle Add New Rental Action
if (isset($_POST['add_rental'])) {
    $movie_id = intval($_POST['movie_id']);
    $user_id = intval($_POST['user_id']);
    $rental_code = 'TRX-' . uniqid(); // Generate a unique rental code

    // Fetch movie details to get title and rental_price
    $stmt_movie = $mysqli->prepare("SELECT title, rental_price, stock FROM movies WHERE movie_id = ?");
    $stmt_movie->bind_param('i', $movie_id);
    $stmt_movie->execute();
    $stmt_movie->bind_result($movie_title, $rental_price, $current_stock);
    $stmt_movie->fetch();
    $stmt_movie->close();

    // Fetch user details to get user_name
    $stmt_user = $mysqli->prepare("SELECT name FROM users WHERE user_id = ?");
    $stmt_user->bind_param('i', $user_id);
    $stmt_user->execute();
    $stmt_user->bind_result($user_name);
    $stmt_user->fetch();
    $stmt_user->close();

    // Check if movie details and user details were found and if stock is available
    if (!$movie_title || !$user_name) {
        $err = "Selected movie or user not found.";
    } elseif ($current_stock <= 0) {
        $err = "Movie is out of stock.";
    } else {
        // Insert new rental record
        $query_rental = "INSERT INTO rentals (rental_code, movie_id, title, user_id, user_name, rental_price) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_rental = $mysqli->prepare($query_rental);
        $stmt_rental->bind_param('sisids', $rental_code, $movie_id, $movie_title, $user_id, $user_name, $rental_price);
        $stmt_rental->execute();

        if ($stmt_rental) {
            // Decrement movie stock
            $query_update_stock = "UPDATE movies SET stock = stock - 1 WHERE movie_id = ?";
            $stmt_update_stock = $mysqli->prepare($query_update_stock);
            $stmt_update_stock->bind_param('i', $movie_id);
            $stmt_update_stock->execute();
            $stmt_update_stock->close();

            $success = "Movie rented successfully! Rental Code: " . $rental_code;
            // Optionally add an admin log entry here
        } else {
            $err = "Failed to add rental. Please try again.";
        }
        $stmt_rental->close();
    }
}

// Handle Delete Rental Action
if (isset($_GET['delete_rental'])) {
    $rental_id = intval($_GET['delete_rental']);

    // Get movie_id from the rental before deleting to increment stock back
    $stmt_fetch_rental = $mysqli->prepare("SELECT movie_id FROM rentals WHERE rental_id = ?");
    $stmt_fetch_rental->bind_param('i', $rental_id);
    $stmt_fetch_rental->execute();
    $stmt_fetch_rental->bind_result($deleted_movie_id);
    $stmt_fetch_rental->fetch();
    $stmt_fetch_rental->close();

    $adn = "DELETE FROM rentals WHERE rental_id = ?";
    $stmt = $mysqli->prepare($adn);
    $stmt->bind_param('i', $rental_id);
    $stmt->execute();
    $stmt->close();

    if ($stmt) {
        // Increment movie stock back since the rental is deleted (assuming it's 'returned')
        if ($deleted_movie_id) {
            $query_increment_stock = "UPDATE movies SET stock = stock + 1 WHERE movie_id = ?";
            $stmt_increment_stock = $mysqli->prepare($query_increment_stock);
            $stmt_increment_stock->bind_param('i', $deleted_movie_id);
            $stmt_increment_stock->execute();
            $stmt_increment_stock->close();
        }
        $info = "Rental Record Deleted Successfully and Stock Incremented.";
        // Optionally add an admin log entry here
    } else {
        $err = "Failed to delete rental record. Please try again.";
    }
}


// Fetch all rentals for display
$ret = "SELECT r.*, m.title as movie_title, u.name as user_name 
        FROM rentals r
        JOIN movies m ON r.movie_id = m.movie_id
        JOIN users u ON r.user_id = u.user_id
        ORDER BY r.rental_date DESC";
$stmt = $mysqli->prepare($ret);
$stmt->execute();
$res = $stmt->get_result();
$rentals = $res->fetch_all(MYSQLI_ASSOC); // Fetch all results as an associative array
$stmt->close();

// Fetch all users for the 'Add Rental' modal
$users_query = "SELECT user_id, name FROM users ORDER BY name ASC";
$users_result = $mysqli->query($users_query);
$users = $users_result->fetch_all(MYSQLI_ASSOC);

// Fetch all movies for the 'Add Rental' modal
$movies_query = "SELECT movie_id, title, rental_price, stock FROM movies ORDER BY title ASC";
$movies_result = $mysqli->query($movies_query);
$all_movies = $movies_result->fetch_all(MYSQLI_ASSOC); // Using a different variable name to avoid conflict
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
                            <h1><i class="fas fa-handshake"></i> Manage Movie Rentals</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Manage Rentals</li>
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
                                    <h3 class="card-title">All Movie Rental Records</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addRentalModal">
                                            <i class="fas fa-plus"></i> Add New Rental
                                        </button>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="rentalsTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Rental Code</th>
                                                <th>Movie Title</th>
                                                <th>User Name</th>
                                                <th>Rental Price</th>
                                                <th>Rental Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $cnt = 1;
                                            foreach ($rentals as $rental) { ?>
                                                <tr>
                                                    <td><?php echo $cnt; ?></td>
                                                    <td><?php echo htmlspecialchars($rental['rental_code']); ?></td>
                                                    <td><?php echo htmlspecialchars($rental['movie_title']); ?></td>
                                                    <td><?php echo htmlspecialchars($rental['user_name']); ?></td>
                                                    <td>$<?php echo number_format($rental['rental_price'], 2); ?></td>
                                                    <td><?php echo date("d-M-Y h:m:s", strtotime($rental['rental_date'])); ?></td>
                                                    <td>
                                                        <a href="pages_manage_rentals.php?delete_rental=<?php echo $rental['rental_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this rental record? This will return the movie to stock.');" title="Delete Rental">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </a>
                                                        <!-- Optionally add an "Edit Rental" or "Return Rental" button here -->
                                                    </td>
                                                </tr>
                                            <?php $cnt++;
                                            } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>#</th>
                                                <th>Rental Code</th>
                                                <th>Movie Title</th>
                                                <th>User Name</th>
                                                <th>Rental Price</th>
                                                <th>Rental Date</th>
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

        <!-- Add Rental Modal -->
        <div class="modal fade" id="addRentalModal" tabindex="-1" role="dialog" aria-labelledby="addRentalModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addRentalModalLabel">Add New Movie Rental</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="user_id"><i class="fas fa-user"></i> Select User</label>
                                <select name="user_id" id="user_id_select" class="form-control" required>
                                    <option value="">-- Select User --</option>
                                    <?php foreach ($users as $user) { ?>
                                        <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="movie_id"><i class="fas fa-film"></i> Select Movie</label>
                                <select name="movie_id" id="movie_id_select" class="form-control" required>
                                    <option value="">-- Select Movie --</option>
                                    <?php foreach ($all_movies as $movie_option) { ?>
                                        <option 
                                            value="<?php echo $movie_option['movie_id']; ?>" 
                                            data-price="<?php echo htmlspecialchars($movie_option['rental_price']); ?>"
                                            data-stock="<?php echo htmlspecialchars($movie_option['stock']); ?>"
                                            <?php echo ($movie_option['stock'] <= 0) ? 'disabled class="text-danger"' : ''; ?>>
                                            <?php echo htmlspecialchars($movie_option['title']); ?>
                                            (Stock: <?php echo htmlspecialchars($movie_option['stock']); ?>)
                                            <?php echo ($movie_option['stock'] <= 0) ? '(Out of Stock)' : ''; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-dollar-sign"></i> Rental Price:</label>
                                <input type="text" id="display_rental_price" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-cubes"></i> Available Stock:</label>
                                <input type="text" id="display_available_stock" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="add_rental" id="add_rental_submit_btn" class="btn btn-primary"><i class="fas fa-plus"></i> Add Rental</button>
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
            // Initialize DataTables for the rentals table
            $("#rentalsTable").DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });

            // Function to update rental price, stock, and button state
            function updateRentalFormDetails() {
                var selectedOption = $('#movie_id_select').find('option:selected');
                var rentalPrice = selectedOption.data('price');
                var stock = selectedOption.data('stock');
                var addRentalBtn = $('#add_rental_submit_btn');

                if (selectedOption.val() !== "") {
                    $('#display_rental_price').val('$' + parseFloat(rentalPrice).toFixed(2));
                    $('#display_available_stock').val(stock);

                    if (stock <= 0) {
                        $('#display_available_stock').css('color', 'red').val('Out of Stock');
                        addRentalBtn.prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
                    } else {
                        $('#display_available_stock').css('color', '').val(stock); // Reset color
                        addRentalBtn.prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
                    }
                } else {
                    $('#display_rental_price').val('');
                    $('#display_available_stock').val('');
                    $('#display_available_stock').css('color', ''); // Reset color
                    addRentalBtn.prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
                }
            }

            // Handle movie selection in the "Add Rental" modal
            $('#movie_id_select').change(updateRentalFormDetails);

            // Trigger on load to set initial values and button state
            updateRentalFormDetails();

            // Clear modal fields when modal is hidden
            $('#addRentalModal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset(); // Reset form fields
                $('#movie_id_select').val(''); // Clear movie selection
                $('#user_id_select').val('');  // Clear user selection
                updateRentalFormDetails(); // Reset display and button state
            });
        });
    </script>
</body>
</html>
