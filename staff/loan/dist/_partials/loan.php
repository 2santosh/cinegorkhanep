<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo 
    and load this 
    page with logged in user instance
  -->
  <?php
  $staff_id = $_SESSION['staff_id'];
  $ret = "SELECT * FROM  iB_staff  WHERE staff_id = ? ";
  $stmt = $mysqli->prepare($ret);
  $stmt->bind_param('i', $staff_id);
  $stmt->execute(); // Execute the query
  $res = $stmt->get_result();
  while ($row = $res->fetch_object()) {
    //set automatically logged in user default image if they have not updated their pics
    if ($row->profile_pic == '') {
      $profile_picture = "<img src='./../img/user_icon.png' class='elevation-2' alt='User Image'>";
    } else {
      $profile_picture = "<img src='./../img/$row->profile_pic' class='elevation-2' alt='User Image'>";
    }

    /* Persisit System Settings On Brand */
    $ret = "SELECT * FROM `iB_SystemSettings` ";
    $stmt = $mysqli->prepare($ret);
    $stmt->execute(); // Execute the query
    $res = $stmt->get_result();
    while ($sys = $res->fetch_object()) {
  ?>

      <a href="dashboard.php" class="brand-link">
        <img src="./../img/<?php echo $sys->sys_logo; ?>" alt="iBanking Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"><?php echo $sys->sys_name; ?></span>
      </a>

  <?php } ?>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <?php echo $profile_picture; ?>
      </div>
      <div class="info">
        <a href="pages_account.php" class="d-block"><?php echo $row->name; ?></a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->

        <li class="nav-item has-treeview">
          <a href="dashboard.php" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="pages_loans.php" class="nav-link">
            <i class="fas fa-cart-arrow-down nav-icon"></i>
            <p>Loans</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="pages_apply_loan.php" class="nav-link">
            <i class="nav-icon fas fa-hand-holding-usd"></i>
            <p>Apply Loan</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="pages_paid_loans.php" class="nav-link">
            <i class="nav-icon fas fa-money-check-alt"></i>
            <p>Paid Loans</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="pages_pending_loans.php" class="nav-link">
            <i class="nav-icon fas fa-exclamation-circle"></i>
            <p>Pending Loans</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="pages_loan_interest_table.php" class="nav-link">
            <i class="nav-icon fas fa-exclamation-circle"></i>
            <p>Interest Rate</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="pages_loan_interest.php" class="nav-link">
            <i class="nav-icon fas fa-percent"></i>
            <p>Loan Interest</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="./../pages_logout.php" class="nav-link">
            <i class="nav-icon fas fa-power-off"></i>
            <p>Log Out</p>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
  <?php } ?>
</aside>
