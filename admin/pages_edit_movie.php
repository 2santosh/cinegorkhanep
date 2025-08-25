<?php
session_start();
include('conf/config.php'); // Database connection
include('conf/checklogin.php'); // Login check
check_login();
$admin_id = $_SESSION['admin_id']; // Current admin ID

// Initialize variables
$err = $success = '';
$movie = null;

// Get movie_id from URL
if (isset($_GET['movie_id']) && !empty($_GET['movie_id'])) {
    $movie_id = intval($_GET['movie_id']);

    // Fetch existing movie details
    $ret = "SELECT * FROM movies WHERE movie_id = ?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('i', $movie_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $movie = $res->fetch_object();
    $stmt->close();

    if (!$movie) {
        // Redirect if movie not found
        header("Location: pages_manage_movies.php");
        exit();
    }
} else {
    // Redirect if movie_id is not provided
    header("Location: pages_manage_movies.php");
    exit();
}

// Handle Update Movie Action
if (isset($_POST['update_movie'])) {
    $title = $_POST['title'];
    $genre_id = intval($_POST['genre_id']);
    $description = $_POST['description'];
    $release_year = intval($_POST['release_year']);
    $duration = intval($_POST['duration']);
    $language = $_POST['language'];
    $rental_price = floatval($_POST['rental_price']);
    $stock = intval($_POST['stock']);
    $trailer_url = $_POST['trailer_url'];
    $new_video_type = $_POST['type']; // 'local' or 'youtube'
    $new_video_url = $movie->video_url; // Default to current video URL

    // Handle Poster Upload
    $poster_url = $movie->poster_url; // Default to current poster
    if (isset($_FILES['poster_file']) && $_FILES['poster_file']['error'] == 0) {
        $allowed_img = ['jpg', 'jpeg', 'png', 'gif'];
        $img_file_name = $_FILES['poster_file']['name'];
        $img_file_tmp = $_FILES['poster_file']['tmp_name'];
        $img_ext = strtolower(pathinfo($img_file_name, PATHINFO_EXTENSION));

        if (in_array($img_ext, $allowed_img)) {
            $new_poster_name = uniqid('poster_') . '.' . $img_ext;
            $upload_poster_path = 'uploads/posters/' . $new_poster_name;
            
            // Create directory if it doesn't exist
            if (!file_exists('uploads/posters/')) {
                mkdir('uploads/posters/', 0777, true);
            }
            
            if (move_uploaded_file($img_file_tmp, $upload_poster_path)) {
                $poster_url = $upload_poster_path;
                // Delete old poster if it was a local file and different
                if ($movie->poster_url && file_exists($movie->poster_url) && $movie->poster_url != $poster_url) {
                    unlink($movie->poster_url);
                }
            } else {
                $err = "Failed to move uploaded poster file. Check directory permissions.";
            }
        } else {
            $err = "Invalid poster file type. Allowed: jpg, jpeg, png, gif.";
        }
    }

    // Handle Video Upload / YouTube Link
    if ($new_video_type == 'local') {
        if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] == 0) {
            $allowed_video = ['mp4', 'mov', 'avi', 'mkv'];
            $video_file_name = $_FILES['video_file']['name'];
            $video_file_tmp = $_FILES['video_file']['tmp_name'];
            $video_ext = strtolower(pathinfo($video_file_name, PATHINFO_EXTENSION));

            if (in_array($video_ext, $allowed_video)) {
                $new_video_name = uniqid('movie_') . '.' . $video_ext;
                $upload_video_path = 'uploads/' . $new_video_name;
                
                // Create directory if it doesn't exist
                if (!file_exists('uploads/')) {
                    mkdir('uploads/', 0777, true);
                }
                
                if (move_uploaded_file($video_file_tmp, $upload_video_path)) {
                    $new_video_url = $upload_video_path;
                    // Delete old local video if it exists and type was local
                    if ($movie->type == 'local' && file_exists($movie->video_url) && $movie->video_url != $new_video_url) {
                        unlink($movie->video_url);
                    }
                } else {
                    $err = "Failed to move uploaded video file. Check directory permissions.";
                }
            } else {
                $err = "Invalid video file type. Allowed: mp4, mov, avi, mkv.";
            }
        } else {
            // No new file uploaded, retain old video URL if type remains local
            if ($movie->type == 'local' && $new_video_type == 'local') {
                $new_video_url = $movie->video_url;
            } else if ($movie->type == 'youtube' && $new_video_type == 'local') {
                // Changing from YouTube to local, but no file provided
                $err = "Please select a video file when changing to local upload type.";
            }
        }
    } else if ($new_video_type == 'youtube') {
        $new_video_url = $_POST['youtube_link'];
        if (empty($new_video_url)) {
            $err = "Please enter a YouTube link.";
        } else {
            // If changing from local to YouTube, delete old local file
            if ($movie->type == 'local' && file_exists($movie->video_url)) {
                unlink($movie->video_url);
            }
        }
    }

    if (empty($err)) {
        // Update movie in database
        $query = "UPDATE movies SET title = ?, genre_id = ?, description = ?, release_year = ?, duration = ?, language = ?, poster_url = ?, trailer_url = ?, video_url = ?, type = ?, rental_price = ?, stock = ? WHERE movie_id = ?";
        $stmt = $mysqli->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param('sisiiissdsiii', $title, $genre_id, $description, $release_year, $duration, $language, $poster_url, $trailer_url, $new_video_url, $new_video_type, $rental_price, $stock, $movie_id);
            
            if ($stmt->execute()) {
                $success = "Movie details updated successfully!";
                // Refresh movie object to reflect changes
                $refresh_stmt = $mysqli->prepare("SELECT * FROM movies WHERE movie_id = ?");
                $refresh_stmt->bind_param('i', $movie_id);
                $refresh_stmt->execute();
                $movie = $refresh_stmt->get_result()->fetch_object();
                $refresh_stmt->close();
            } else {
                $err = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $err = "Database preparation error: " . $mysqli->error;
        }
    }
}

// Fetch genres for dropdown
$genres_query = "SELECT genre_id, genre_name FROM genres ORDER BY genre_name ASC";
$genres_result = $mysqli->query($genres_query);
$genres = $genres_result->fetch_all(MYSQLI_ASSOC);

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
                            <h1><i class="fas fa-edit"></i> Edit Movie: <?php echo htmlspecialchars($movie->title); ?></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="pages_manage_movies.php">Manage Movies</a></li>
                                <li class="breadcrumb-item active">Edit Movie</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Display error/success messages -->
                            <?php if (!empty($err)): ?>
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h5><i class="icon fas fa-ban"></i> Error!</h5>
                                    <?php echo $err; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($success)): ?>
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h5><i class="icon fas fa-check"></i> Success!</h5>
                                    <?php echo $success; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- general form elements -->
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Update Movie Details</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form role="form" method="post" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="title"><i class="fas fa-film"></i> Movie Title</label>
                                                <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($movie->title); ?>" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="genre_id"><i class="fas fa-tag"></i> Genre</label>
                                                <select name="genre_id" id="genre_id" class="form-control" required>
                                                    <?php foreach ($genres as $genre) { ?>
                                                        <option value="<?php echo $genre['genre_id']; ?>" <?php if ($movie->genre_id == $genre['genre_id']) echo 'selected'; ?>>
                                                            <?php echo htmlspecialchars($genre['genre_name']); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="description"><i class="fas fa-align-left"></i> Description</label>
                                            <textarea name="description" id="description" class="form-control" rows="4" required><?php echo htmlspecialchars($movie->description); ?></textarea>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <label for="release_year"><i class="fas fa-calendar-alt"></i> Release Year</label>
                                                <input type="number" name="release_year" id="release_year" class="form-control" value="<?php echo htmlspecialchars($movie->release_year); ?>" required min="1900" max="<?php echo date('Y'); ?>">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="duration"><i class="fas fa-hourglass-half"></i> Duration (minutes)</label>
                                                <input type="number" name="duration" id="duration" class="form-control" value="<?php echo htmlspecialchars($movie->duration); ?>" required min="1">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="language"><i class="fas fa-language"></i> Language</label>
                                                <input type="text" name="language" id="language" class="form-control" value="<?php echo htmlspecialchars($movie->language); ?>" required>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="stock"><i class="fas fa-box"></i> Stock</label>
                                                <input type="number" name="stock" id="stock" class="form-control" value="<?php echo htmlspecialchars($movie->stock); ?>" required min="0">
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="rental_price"><i class="fas fa-dollar-sign"></i> Rental Price ($)</label>
                                                <input type="number" step="0.01" name="rental_price" id="rental_price" class="form-control" value="<?php echo htmlspecialchars($movie->rental_price); ?>" required min="0">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="trailer_url"><i class="fab fa-youtube"></i> Trailer URL</label>
                                                <input type="url" name="trailer_url" id="trailer_url" class="form-control" value="<?php echo htmlspecialchars($movie->trailer_url); ?>" placeholder="e.g., https://www.youtube.com/watch?v=...">
                                            </div>
                                        </div>

                                        <!-- Poster Image Section -->
                                        <div class="form-group">
                                            <label for="poster_file"><i class="fas fa-image"></i> Movie Poster</label>
                                            <?php if ($movie->poster_url && file_exists($movie->poster_url)) { ?>
                                                <div class="mb-2">
                                                    <img src="<?php echo htmlspecialchars($movie->poster_url); ?>" alt="Current Poster" style="max-width: 150px; border-radius: 8px;">
                                                    <small class="text-muted ml-2">Current Poster</small>
                                                </div>
                                            <?php } else { ?>
                                                <div class="mb-2">
                                                    <small class="text-muted">No poster uploaded yet.</small>
                                                </div>
                                            <?php } ?>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" name="poster_file" class="custom-file-input" id="poster_file" accept="image/*">
                                                    <label class="custom-file-label" for="poster_file">Choose new poster (optional)</label>
                                                </div>
                                            </div>
                                        </div>

                                        <hr> <!-- Separator for video section -->

                                        <!-- Video Upload Type Section -->
                                        <div class="form-group">
                                            <label for="type"><i class="fas fa-cloud-upload-alt"></i> Video Upload Type</label>
                                            <select name="type" id="type" class="form-control" required>
                                                <option value="local" <?php if ($movie->type == 'local') echo 'selected'; ?>>Upload Video File</option>
                                                <option value="youtube" <?php if ($movie->type == 'youtube') echo 'selected'; ?>>YouTube Link</option>
                                            </select>
                                        </div>

                                        <div class="form-group" id="local_file" style="<?php echo ($movie->type == 'local') ? 'display:block;' : 'display:none;'; ?>">
                                            <label for="video_file"><i class="fas fa-file-upload"></i> Choose Video File</label>
                                            <?php if ($movie->type == 'local' && $movie->video_url && file_exists($movie->video_url)) { ?>
                                                <div class="mb-2">
                                                    <small class="text-muted">Current Video: <?php echo htmlspecialchars(basename($movie->video_url)); ?></small>
                                                </div>
                                            <?php } else if ($movie->type == 'local') { ?>
                                                <div class="mb-2">
                                                    <small class="text-muted">No local video file found.</small>
                                                </div>
                                            <?php } ?>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" name="video_file" class="custom-file-input" id="video_file" accept="video/*">
                                                    <label class="custom-file-label" for="video_file">Choose new video (optional)</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group" id="youtube_link" style="<?php echo ($movie->type == 'youtube') ? 'display:block;' : 'display:none;'; ?>">
                                            <label for="youtube_link_input"><i class="fab fa-youtube"></i> YouTube Video Link</label>
                                            <input type="url" name="youtube_link" id="youtube_link_input" class="form-control" value="<?php echo htmlspecialchars($movie->video_url); ?>" placeholder="https://youtube.com/watch?v=...">
                                        </div>
                                    </div>
                                    <!-- /.card-body -->

                                    <div class="card-footer">
                                        <button type="submit" name="update_movie" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                                        <a href="pages_manage_movies.php" class="btn btn-default">Cancel</a>
                                    </div>
                                </form>
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

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
    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.js"></script>
    <!-- bs-custom-file-input -->
    <script src="plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize bs-custom-file-input
            bsCustomFileInput.init();
            
            // Initial state based on current movie type
            var initialType = $('#type').val();
            if (initialType === 'local') {
                $('#local_file').show();
                $('#youtube_link').hide();
            } else {
                $('#local_file').hide();
                $('#youtube_link').show();
            }

            // Handle change event for upload type
            $('#type').change(function() {
                if ($(this).val() == 'local') {
                    $('#local_file').show();
                    $('#youtube_link').hide();
                } else {
                    $('#local_file').hide();
                    $('#youtube_link').show();
                }
            });
        });
    </script>
</body>
</html>