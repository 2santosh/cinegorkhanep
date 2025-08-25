<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <?php
// Get logged-in admin info
$admin = null;
if(isset($_SESSION['admin_id'])) {
    $stmt = $mysqli->prepare("SELECT * FROM admins WHERE admin_id = ?");
    $stmt->bind_param('i', $_SESSION['admin_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    $admin = $res->fetch_object();
}

// Fallback profile picture
$profile_picture = "<img src='./../img/user_icon.png' class='img-circle elevation-2' alt='Admin Image'>";
if($admin && !empty($admin->profile_pic)) {
    $profile_picture = "<img src='./../img/{$admin->profile_pic}' class='img-circle elevation-2' alt='Admin Image'>";
}

// Get system settings
$sys = null;
$stmt = $mysqli->prepare("SELECT * FROM system_settings LIMIT 1");
$stmt->execute();
$res = $stmt->get_result();
$sys = $res->fetch_object();
$sys_name = $sys->sys_name ?? 'Movie System';
$sys_logo = $sys->sys_logo ?? 'default_logo.png';
?>

    <!-- Brand Logo -->
    <a href="dashboard.php" class="brand-link">
        <img src="./../img/<?php echo $sys_logo; ?>" alt="Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light"><?php echo $sys_name; ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Admin Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <?php echo $profile_picture; ?>
            </div>
            <div class="info">
                <a href="account.php" class="d-block"><?php echo $admin->name ?? 'Admin'; ?></a>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Movies -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-film"></i>
                        <p>
                            Movies
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="add_movie.php" class="nav-link">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Add Movie</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="manage_movies.php" class="nav-link">
                                <i class="fas fa-cogs nav-icon"></i>
                                <p>Manage Movies</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="movie_rentals.php" class="nav-link">
                                <i class="fas fa-dollar-sign nav-icon"></i>
                                <p>Rentals</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Genres -->
                <li class="nav-item">
                    <a href="manage_genres.php" class="nav-link">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>Genres</p>
                    </a>
                </li>

                <!-- Users -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Users
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="manage_users.php" class="nav-link">
                                <i class="fas fa-cogs nav-icon"></i>
                                <p>Manage Users</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="subscriptions.php" class="nav-link">
                                <i class="fas fa-file-invoice-dollar nav-icon"></i>
                                <p>Subscriptions</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Playlists -->
                <li class="nav-item">
                    <a href="manage_playlists.php" class="nav-link">
                        <i class="nav-icon fas fa-list"></i>
                        <p>Playlists</p>
                    </a>
                </li>

                <!-- Settings -->
                <li class="nav-item">
                    <a href="system_settings.php" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>System Settings</p>
                    </a>
                </li>

                <!-- Log Out -->
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="nav-icon fas fa-power-off"></i>
                        <p>Log Out</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>