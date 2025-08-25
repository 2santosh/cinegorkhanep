<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('conf/config.php');
include('conf/checklogin.php');
check_login();

$admin_id = $_SESSION['admin_id'];
$err = '';
$success = '';

// Update logged-in user account
if (isset($_POST['update_account'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    $query = "UPDATE admins SET name=?, email=? WHERE admin_id=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ssi', $name, $email, $admin_id);
    $stmt->execute();

    if ($stmt) {
        $success = "Account Updated";
    } else {
        $err = "Please Try Again Or Try Later";
    }
}

// Change password
if (isset($_POST['change_password'])) {
    $password = sha1(md5($_POST['password']));

    $query = "UPDATE admins SET password=? WHERE admin_id=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('si', $password, $admin_id);
    $stmt->execute();

    if ($stmt) {
        $success = "Password Updated";
    } else {
        $err = "Please Try Again Or Try Later";
    }
}

// Fetch admin details
$ret = "SELECT * FROM admins WHERE admin_id=?";
$stmt = $mysqli->prepare($ret);
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$res = $stmt->get_result();
$admin = $res->fetch_object();

// Default profile picture if none
$profile_picture = $admin->profile_pic ? "./../img/{$admin->profile_pic}" : "./../img/user_icon.png";
?>
<!DOCTYPE html>
<html lang="en">
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
                    <div class="col-sm-6">
                        <h1><?php echo htmlspecialchars($admin->name); ?> Profile</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Profile</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- Profile Sidebar -->
                    <div class="col-md-3">
                        <div class="card card-purple card-outline">
                            <div class="card-body box-profile text-center">
                                <img src="<?php echo $profile_picture; ?>" class="img-fluid" alt="Profile Picture">
                                <h3 class="profile-username text-center"><?php echo htmlspecialchars($admin->name); ?></h3>
                                <p class="text-muted text-center">@Admin CineGorkha</p>

                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Email: </b> <span class="float-right"><?php echo htmlspecialchars($admin->email); ?></span>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Number: </b> <span class="float-right"><?php echo htmlspecialchars($admin->number); ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Management Tabs -->
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#update_Profile" data-toggle="tab">Update Profile</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#Change_Password" data-toggle="tab">Change Password</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <?php if ($err): ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
                                <?php endif; ?>
                                <?php if ($success): ?>
                                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                                <?php endif; ?>

                                <div class="tab-content">
                                    <!-- Update Profile Tab -->
                                    <div class="tab-pane active" id="update_Profile">
                                        <form method="post" class="form-horizontal">
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Name</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($admin->name); ?>">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Email</label>
                                                <div class="col-sm-10">
                                                    <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($admin->email); ?>">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Number</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" readonly value="<?php echo htmlspecialchars($admin->number); ?>">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="offset-sm-2 col-sm-10">
                                                    <button type="submit" name="update_account" class="btn btn-outline-success">Update Account</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Change Password Tab -->
                                    <div class="tab-pane" id="Change_Password">
                                        <form method="post" class="form-horizontal">
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">New Password</label>
                                                <div class="col-sm-10">
                                                    <input type="password" name="password" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="offset-sm-2 col-sm-10">
                                                    <button type="submit" name="change_password" class="btn btn-outline-success">Change Password</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include("dist/_partials/footer.php"); ?>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<script src="dist/js/demo.js"></script>
</body>
</html>
