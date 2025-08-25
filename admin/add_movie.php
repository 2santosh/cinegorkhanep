<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();

// Fetch genres for dropdown
$genres = [];
$genre_stmt = $mysqli->prepare("SELECT * FROM genres ORDER BY genre_name ASC");
$genre_stmt->execute();
$genre_res = $genre_stmt->get_result();
while($g = $genre_res->fetch_object()){
    $genres[] = $g;
}

// Handle form submission
if(isset($_POST['upload_movie'])) {

    $title = $_POST['title'];
    $description = $_POST['description'];
    $genre_id = $_POST['genre_id'];
    $type = $_POST['type']; // 'local' or 'youtube'
    $video_url = '';

    if($type == 'local') {
        if(isset($_FILES['video_file']) && $_FILES['video_file']['error'] == 0) {
            $allowed = ['mp4', 'mov', 'avi', 'mkv'];
            $file_name = $_FILES['video_file']['name'];
            $file_tmp = $_FILES['video_file']['tmp_name'];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if(in_array($ext, $allowed)) {
                $new_file_name = uniqid('movie_') . '.' . $ext;
                $upload_path = 'uploads/' . $new_file_name;
                if(move_uploaded_file($file_tmp, $upload_path)) {
                    $video_url = $upload_path;
                } else {
                    $err = "Failed to move uploaded file.";
                }
            } else {
                $err = "Invalid file type. Allowed: mp4, mov, avi, mkv.";
            }
        } else {
            $err = "Please select a video file to upload.";
        }
    } else if($type == 'youtube') {
        $video_url = $_POST['youtube_link'];
        if(empty($video_url)) {
            $err = "Please enter a YouTube link.";
        }
    }

    if(!isset($err)) {
        $query = "INSERT INTO movies (title, description, genre_id, type, video_url) VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ssiss', $title, $description, $genre_id, $type, $video_url);
        $stmt->execute();

        if($stmt){
            $success = "Movie uploaded successfully!";
        } else {
            $err = "Database error. Please try again.";
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
    <?php include("dist/_partials/nav.php"); ?>
    <?php include("dist/_partials/sidebar.php"); ?>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"><h1>Upload Movie</h1></div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <?php if(isset($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>
                <?php if(isset($err)) { echo "<div class='alert alert-danger'>$err</div>"; } ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary">
                            <div class="card-header"><h3 class="card-title"><i class="fas fa-video"></i> Add New Movie</h3></div>
                            <form role="form" method="post" enctype="multipart/form-data">
                                <div class="card-body">

                                    <div class="form-group">
                                        <label>Movie Title</label>
                                        <input type="text" name="title" class="form-control" required placeholder="Enter movie title">
                                    </div>

                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control" rows="4" placeholder="Enter movie description"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Genre</label>
                                        <select name="genre_id" class="form-control" required>
                                            <option value="">Select Genre</option>
                                            <?php foreach($genres as $g): ?>
                                                <option value="<?php echo $g->genre_id; ?>"><?php echo $g->genre_name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Upload Type</label>
                                        <select name="type" id="type" class="form-control" required>
                                            <option value="local">Upload Video File</option>
                                            <option value="youtube">YouTube Link</option>
                                        </select>
                                    </div>

                                    <div class="form-group" id="local_file">
                                        <label>Choose Video File</label>
                                        <input type="file" name="video_file" class="form-control">
                                    </div>

                                    <div class="form-group" id="youtube_link" style="display:none;">
                                        <label>YouTube Video Link</label>
                                        <input type="url" name="youtube_link" class="form-control" placeholder="https://youtube.com/watch?v=...">
                                    </div>

                                </div>

                                <div class="card-footer">
                                    <button type="submit" name="upload_movie" class="btn btn-primary"><i class="fas fa-upload"></i> Upload Movie</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script src="plugins/jquery/jquery.min.js"></script>
        <script>
        $(document).ready(function(){
            $('#type').change(function(){
                if($(this).val() == 'local'){
                    $('#local_file').show();
                    $('#youtube_link').hide();
                } else {
                    $('#local_file').hide();
                    $('#youtube_link').show();
                }
            });
        });
        </script>
    </div>

    <?php include("dist/_partials/footer.php"); ?>
</div>
</body>
</html>
