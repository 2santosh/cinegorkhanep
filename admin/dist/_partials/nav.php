<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <?php
                $stmt = $mysqli->prepare("SELECT count(*) FROM admin_notifications"); // Make sure table exists
                $stmt->execute();
                $stmt->bind_result($ntf);
                $stmt->fetch();
                $stmt->close();
                ?>
                <span class="badge badge-danger navbar-badge"><?= $ntf ?></span>
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <?php
                $stmt = $mysqli->prepare("SELECT * FROM admin_notifications ORDER BY created_at DESC"); 
                $stmt->execute();
                $res = $stmt->get_result();

                while ($row = $res->fetch_object()) {
                    $notification_time = $row->created_at;
                ?>
                    <div class="dropdown-item">
                        <div class="media">
                            <div class="media-body">
                                <h3 class="dropdown-item-title">
                                    <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                                </h3>
                                <p class="text-sm"><?= $row->notification_details ?></p>
                                <p class="text-sm text-muted">
                                    <i class="far fa-clock mr-1"></i><?= date("d-M-Y :: h:i", strtotime($notification_time)) ?>
                                </p>
                            </div>
                        </div>
                        <a href="pages_dashboard.php?Clear_Notifications=<?= $row->notification_id ?>" class="float-right text-sm text-danger">
                            <i class="fas fa-trash"></i> Clear
                        </a>
                        <hr>
                    </div>
                <?php } ?>
            </div>
        </li>
    </ul>
</nav>
