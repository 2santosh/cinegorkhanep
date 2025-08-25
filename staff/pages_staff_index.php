<?php
session_start();
include('manager/conf/config.php'); // get configuration file

if (isset($_POST['login'])) {
  $email = $_POST['email'];
  $password = sha1(md5($_POST['password'])); // double encrypt to increase security
  $stmt = $mysqli->prepare("SELECT email, password, staff_id, staff_position FROM iB_staff WHERE email=? AND password=?"); // sql to log in user
  $stmt->bind_param('ss', $email, $password); // bind fetched parameters
  $stmt->execute(); // execute bind
  $stmt->bind_result($email, $password, $staff_id, $staff_position); // bind result
  $rs = $stmt->fetch();
  $_SESSION['staff_id'] = $staff_id; // assign session to staff id

  if ($rs) { // if it's successful
    switch ($staff_position) {
      case 'Manager':
        header("location: manager/dashboard.php");
        break;
      case 'CSD':
        header("location: csd/dashboard.php");
        break;
      case 'Loan':
          header("location: loan/dashboard.php");
          break;
      case 'Cash':
            header("location: cash/dashboard.php");
            break;
      default:
        header("location: ./../index.php");
        break;
    }
  } else {
    $err = "Access Denied. Please Check Your Credentials";
  }
}

/* Persist System Settings On Brand */
$ret = "SELECT * FROM `iB_SystemSettings` ";
$stmt = $mysqli->prepare($ret);
$stmt->execute(); // ok
$res = $stmt->get_result();
while ($auth = $res->fetch_object()) {
?>
  <!DOCTYPE html>
  <html>
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <?php include("manager/dist/_partials/head.php"); ?>
  <?php include("header.php"); ?>
  <body class="hold-transition login-page">
    <div class="login-box">
      <div class="login-logo">
        <p><?php echo $auth->sys_name; ?></p>
      </div>
      <!-- /.login-logo -->
      <div class="card">
        <div class="card-body login-card-body">
          <p class="login-box-msg">Log In To Start Staff Session</p>

          <form method="post">
            <div class="input-group mb-3">
              <input type="email" name="email" class="form-control" placeholder="Email">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-envelope"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="password" name="password" class="form-control" placeholder="Password">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-8">
                <div class="icheck-primary">
                  <input type="checkbox" id="remember">
                  <label for="remember">
                    Remember Me
                  </label>
                </div>
              </div>
              <!-- /.col -->
              <div class="col-4">
                <button type="submit" name="login" class="btn btn-success btn-block">Log In</button>
              </div>
              <!-- /.col -->
            </div>
          </form>
        </div>
        <!-- /.login-card-body -->
      </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>

  </body>
  <?php include("footer.php"); ?>
  </html>
<?php
} ?>
