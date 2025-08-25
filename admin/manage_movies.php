<?php
session_start();
include('conf/config.php'); // Database connection
include('conf/checklogin.php'); // Login check
check_login();
$admin_id = $_SESSION['admin_id']; // Current admin ID for logging or specific actions

// Handle Delete Movie Action
if (isset($_GET['delete_movie'])) {
    $movie_id = intval($_GET['delete_movie']);

    // First, retrieve the video_url to delete the actual file if it's local
    $stmt_fetch = $mysqli->prepare("SELECT video_url, type, poster_url FROM movies WHERE movie_id = ?");
    $stmt_fetch->bind_param('i', $movie_id);
    $stmt_fetch->execute();
    $stmt_fetch->bind_result($video_url_to_delete, $video_type, $poster_url);
    $stmt_fetch->fetch();
    $stmt_fetch->close();

    // If it's a local file, attempt to delete it
    if ($video_type == 'local' && file_exists($video_url_to_delete)) {
        unlink($video_url_to_delete); // Delete the actual file
    }
    
    // Delete poster file if it exists
    if (!empty($poster_url) && file_exists($poster_url)) {
        unlink($poster_url);
    }

    // Now, delete the movie record from the database
    $adn = "DELETE FROM movies WHERE movie_id = ?";
    $stmt = $mysqli->prepare($adn);
    $stmt->bind_param('i', $movie_id);
    $stmt->execute();
    $stmt->close();

    if ($stmt) {
        $info = "Movie Deleted Successfully";
        // Optionally add an admin log entry here
    } else {
        $err = "Failed to delete movie. Please try again.";
    }
}

// Fetch all movies for display
$ret = "SELECT m.*, g.genre_name 
        FROM movies m 
        JOIN genres g ON m.genre_id = g.genre_id 
        ORDER BY m.created_at DESC";
$stmt = $mysqli->prepare($ret);
$stmt->execute();
$res = $stmt->get_result();
$movies = $res->fetch_all(MYSQLI_ASSOC); // Fetch all results as an associative array
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
                            <h1><i class="fas fa-film"></i> Manage Movies</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Manage Movies</li>
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
                                    <h3 class="card-title">All Uploaded Movies</h3>
                                    <div class="card-tools">
                                        <a href="pages_upload_movie.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add New Movie</a>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <?php if (isset($info)): ?>
                                        <div class="alert alert-success alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                            <h5><i class="icon fas fa-check"></i> Success!</h5>
                                            <?php echo $info; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($err)): ?>
                                        <div class="alert alert-danger alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                            <h5><i class="icon fas fa-ban"></i> Error!</h5>
                                            <?php echo $err; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <table id="moviesTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Poster</th>
                                                <th>Title</th>
                                                <th>Genre</th>
                                                <th>Release Year</th>
                                                <th>Duration (min)</th>
                                                <th>Type</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $cnt = 1;
                                            foreach ($movies as $movie) { ?>
                                                <tr>
                                                    <td><?php echo $cnt; ?></td>
                                                    <td>
                                                        <?php if (!empty($movie['poster_url']) && file_exists($movie['poster_url'])): ?>
                                                            <img src="<?php echo $movie['poster_url']; ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" style="width: 60px; height: 80px; object-fit: cover;">
                                                        <?php else: ?>
                                                            <div style="width: 60px; height: 80px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-film text-muted"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($movie['title']); ?></td>
                                                    <td><?php echo htmlspecialchars($movie['genre_name']); ?></td>
                                                    <td><?php echo $movie['release_year']; ?></td>
                                                    <td><?php echo $movie['duration']; ?></td>
                                                    <td><?php echo ucfirst($movie['type']); ?></td>
                                                    <td>
                                                        <button class="btn btn-success btn-sm play-movie-btn"
                                                            data-toggle="modal"
                                                            data-target="#moviePlayerModal"
                                                            data-video-url="<?php echo htmlspecialchars($movie['video_url']); ?>"
                                                            data-video-type="<?php echo htmlspecialchars($movie['type']); ?>"
                                                            data-movie-title="<?php echo htmlspecialchars($movie['title']); ?>"
                                                            title="Play Movie">
                                                            <i class="fas fa-play"></i> Play
                                                        </button>
                                                        <a href="pages_edit_movie.php?movie_id=<?php echo $movie['movie_id']; ?>" class="btn btn-warning btn-sm" title="Edit Movie">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <a href="pages_manage_movies.php?delete_movie=<?php echo $movie['movie_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this movie?');" title="Delete Movie">
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
                                                <th>Poster</th>
                                                <th>Title</th>
                                                <th>Genre</th>
                                                <th>Release Year</th>
                                                <th>Duration (min)</th>
                                                <th>Type</th>
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

        <!-- Movie Player Modal -->
        <div class="modal fade" id="moviePlayerModal" tabindex="-1" role="dialog" aria-labelledby="moviePlayerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="moviePlayerModalLabel">Play Movie</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div id="videoPlayerContent" class="embed-responsive embed-responsive-16by9">
                            <!-- Video player will be loaded here dynamically -->
                            <div class="d-flex justify-content-center align-items-center h-100 bg-dark">
                                <div class="text-center text-white">
                                    <i class="fas fa-spinner fa-spin fa-3x"></i>
                                    <p class="mt-2">Loading video player...</p>
                                </div>
                            </div>
                        </div>
                    </div>
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
    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="plugins/datatables/jquery.dataTables.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $("#moviesTable").DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true
            });

            // Handle Play Movie button click
            $(document).on('click', '.play-movie-btn', function() {
                var videoUrl = $(this).data('video-url');
                var videoType = $(this).data('video-type');
                var movieTitle = $(this).data('movie-title');
                
                // Update modal title
                $('#moviePlayerModalLabel').text('Playing: ' + movieTitle);
                
                loadVideoPlayer(videoUrl, videoType);
            });

            // Function to load video player based on type
            function loadVideoPlayer(videoUrl, videoType) {
                var playerContent = $('#videoPlayerContent');
                playerContent.empty(); // Clear previous content
                
                // Show loading state
                playerContent.html('<div class="d-flex justify-content-center align-items-center h-100 bg-dark">' +
                                    '<div class="text-center text-white">' +
                                    '<i class="fas fa-spinner fa-spin fa-3x"></i>' +
                                    '<p class="mt-2">Loading video player...</p>' +
                                    '</div></div>');

                // Small delay to allow the UI to update
                setTimeout(function() {
                    if (videoType === 'youtube') {
                        loadYouTubePlayer(videoUrl, playerContent);
                    } else if (videoType === 'local') {
                        loadLocalVideoPlayer(videoUrl, playerContent);
                    } else {
                        playerContent.html('<div class="d-flex justify-content-center align-items-center h-100 bg-dark">' +
                                            '<div class="text-center text-white">' +
                                            '<i class="fas fa-exclamation-triangle fa-3x"></i>' +
                                            '<p class="mt-2">Unsupported video type: ' + videoType + '</p>' +
                                            '</div></div>');
                    }
                }, 100);
            }

            // Function to load YouTube player
            function loadYouTubePlayer(youtubeUrl, container) {
                // Extract YouTube video ID using improved regex
                var videoId = null;
                
                // Handle various YouTube URL formats
                var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
                var match = youtubeUrl.match(regExp);
                
                if (match && match[2].length == 11) {
                    videoId = match[2];
                } else if (youtubeUrl.includes('youtube.com/embed/')) {
                    // Handle embed URLs
                    var parts = youtubeUrl.split('youtube.com/embed/');
                    if (parts.length > 1) {
                        videoId = parts[1].split('?')[0].split('/')[0];
                    }
                } else if (youtubeUrl.includes('youtu.be/')) {
                    // Handle shortened URLs
                    var parts = youtubeUrl.split('youtu.be/');
                    if (parts.length > 1) {
                        videoId = parts[1].split('?')[0].split('/')[0];
                    }
                }
                
                if (videoId) {
                    var iframeHtml = '<iframe class="embed-responsive-item" ' +
                                     'src="https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0&modestbranding=1" ' +
                                     'frameborder="0" ' +
                                     'allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" ' +
                                     'allowfullscreen></iframe>';
                    container.html(iframeHtml);
                } else {
                    container.html('<div class="d-flex justify-content-center align-items-center h-100 bg-dark">' +
                                    '<div class="text-center text-white">' +
                                    '<i class="fas fa-exclamation-triangle fa-3x"></i>' +
                                    '<p class="mt-2">Invalid YouTube URL: ' + youtubeUrl + '</p>' +
                                    '</div></div>');
                }
            }

            // Function to load local video player
            function loadLocalVideoPlayer(videoUrl, container) {
                var videoHtml = '<video class="embed-responsive-item" controls autoplay style="width: 100%; height: 100%;">' +
                                '<source src="' + videoUrl + '" type="video/mp4">' +
                                'Your browser does not support the video tag.' +
                                '</video>';
                container.html(videoHtml);
                
                // Add event listener to handle video errors
                container.find('video').on('error', function() {
                    container.html('<div class="d-flex justify-content-center align-items-center h-100 bg-dark">' +
                                    '<div class="text-center text-white">' +
                                    '<i class="fas fa-exclamation-triangle fa-3x"></i>' +
                                    '<p class="mt-2">Error loading video. The file may not exist or is corrupted.</p>' +
                                    '</div></div>');
                });
            }

            // Stop video playback when modal is closed
            $('#moviePlayerModal').on('hidden.bs.modal', function () {
                var playerContent = $('#videoPlayerContent');
                // Remove iframe or video to stop playback
                playerContent.find('iframe, video').remove();
                // Reset to loading state
                playerContent.html('<div class="d-flex justify-content-center align-items-center h-100 bg-dark">' +
                                    '<div class="text-center text-white">' +
                                    '<i class="fas fa-spinner fa-spin fa-3x"></i>' +
                                    '<p class="mt-2">Loading video player...</p>' +
                                    '</div></div>');
            });
        });
    </script>
</body>
</html>