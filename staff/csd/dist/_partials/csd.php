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
  $stmt->execute(); //ok
  $res = $stmt->get_result();
  while ($row = $res->fetch_object()) {
    //set automatically logged in user default image if they have not updated their pics
    if ($row->profile_pic == '') {
      $profile_picture = "<img src='./../img/user_icon.png' class=' elevation-2' alt='User Image'>
                ";
    } else {
      $profile_picture = "<img src='./../img/$row->profile_pic' class='elevation-2' alt='User Image'>
                ";
    }


    /* Persisit System Settings On Brand */
    $ret = "SELECT * FROM `iB_SystemSettings` ";
    $stmt = $mysqli->prepare($ret);
    $stmt->execute(); //ok
    $res = $stmt->get_result();
    while ($sys = $res->fetch_object()) {
  ?>

      <a href="dashboard.php" class="brand-link">
        <img src="./../img/<?php echo $sys->sys_logo; ?>" alt="iBanking Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"><?php echo $sys->sys_name; ?></span>
      </a>

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
            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

            <li class="nav-item has-treeview">
              <a href="dashboard.php" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                  Dashboard
                </p>
              </a>
            </li>
            <!-- ./DAshboard -->

            <!--Clients -->
           
                <li class="nav-item">
                  <a href="pages_add_client.php" class="nav-link">
                    <i class="fas fa-user-plus nav-icon"></i>
                    <p>Add Client</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages_manage_clients.php" class="nav-link">
                    <i class="fas fa-user-cog nav-icon"></i>
                    <p>Manage Clients</p>
                  </a>
                </li>
              
            <!-- ./ Clients -->

            <!--iBank Accounts-->
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-briefcase"></i>
                <p>
                  Accounts
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="pages_add_acc_type.php" class="nav-link">
                    <i class="far fas fa-plus nav-icon"></i>
                    <p>Add Acc Type</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages_manage_accs.php" class="nav-link">
                    <i class="fas fa-cogs nav-icon"></i>
                    <p>Manage Acc Types</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages_open_acc.php" class="nav-link">
                    <i class="fas fa-lock-open nav-icon"></i>
                    <p>Open iBank Acc</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages_manage_acc_openings.php" class="nav-link">
                    <i class="fas fa-cog nav-icon"></i>
                    <p>Manange Acc Openings</p>
                  </a>
                </li>
              </ul>
            </li>
            <!--./ iBank Acounts-->

          </li>
          <!-- ./Finances -->

          <li class="nav-item">
            <a href="pages_balance_enquiries.php" class="nav-link">
              <i class="nav-icon fas fa-exchange-alt"></i>
              <p>
                Statement
              </p>
            </a>
          </li>
          <!--./Transcactions Engine-->

          <!--Password Resets-->
          <li class="nav-item">
            <a href="pages_manage_reset_pwd.php" class="nav-link">
              <i class="nav-icon fas fa-lock"></i>
              <p>
                Manage Resets
              </p>
            </a>
          </li>
          <!-- ./ Password Resets-->
  
          <!-- Log Out -->
          <li class="nav-item">
            <a href="./../pages_logout.php" class="nav-link">
              <i class="nav-icon fas fa-power-off"></i>
              <p>
                Log Out
              </p>
            </a>
          </li>
          <!-- ./Log Out -->
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
</aside>
<?php
    }
  } ?>