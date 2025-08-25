<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('conf/config.php');

$err = '';

// Handle login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = sha1(md5($_POST['password'])); // double encrypt (keep your old method)

    // Use the 'admins' table from your SQL
    $stmt = $mysqli->prepare("SELECT admin_id, email, password FROM admins WHERE email=? AND password=?");
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $stmt->bind_result($admin_id, $db_email, $db_password);
    $rs = $stmt->fetch();

    if ($rs) {
        $_SESSION['admin_id'] = $admin_id;
        header("Location: dashboard.php");
        exit;
    } else {
        $err = "Access Denied. Please check your credentials";
    }
}

// Fetch system settings from the 'system_settings' table
$ret = "SELECT * FROM system_settings LIMIT 1";
$stmt = $mysqli->prepare($ret);
$stmt->execute();
$res = $stmt->get_result();
$auth = $res->fetch_object();

?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<?php include("dist/_partials/head.php"); ?>
<?php include("header.php"); ?>
<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <p><?php echo htmlspecialchars($auth->sys_name); ?></p>
    </div>
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Log In To Start Administrator Session</p>

        <?php if ($err): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
        <?php endif; ?>

        <form method="post">
          <div class="input-group mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8">
              <button type="submit" name="login" class="btn btn-danger btn-block">Log In as Admin</button>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>

  <script src="plugins/jquery/jquery.min.js"></script>
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="dist/js/adminlte.min.js"></script>
</body>
<?php include("footer.php"); ?>
</html>
