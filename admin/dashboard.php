<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$admin_id = $_SESSION['admin_id'];

// Clear admin logs
if (isset($_GET['Clear_Logs'])) {
    $id = intval($_GET['Clear_Logs']);
    $adn = "DELETE FROM admin_logs WHERE log_id = ?";
    $stmt = $mysqli->prepare($adn);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    if ($stmt) {
        $info = "Admin Logs Cleared";
    } else {
        $err = "Try Again Later";
    }
}

/*
 * Get all dashboard analytics and numeric values from distinct tables
 */

// Return total number of users
$result = "SELECT count(*) FROM users";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($totalUsers);
$stmt->fetch();
$stmt->close();

// Return total number of admins
$result = "SELECT count(*) FROM admins";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($totalAdmins);
$stmt->fetch();
$stmt->close();

// Return total number of genres
$result = "SELECT count(*) FROM genres";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($totalGenres);
$stmt->fetch();
$stmt->close();

// Return total number of movies
$result = "SELECT count(*) FROM movies";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($totalMovies);
$stmt->fetch();
$stmt->close();

// Return total revenue from rentals
$result = "SELECT SUM(rental_price) FROM rentals";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($totalRevenue);
$stmt->fetch();
$stmt->close();

// Return total watch time
$result = "SELECT SUM(total_watch_time) FROM users";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($totalWatchTime);
$stmt->fetch();
$stmt->close();

// Return total number of likes
$result = "SELECT count(*) FROM likes";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($totalLikes);
$stmt->fetch();
$stmt->close();

// Return total number of comments
$result = "SELECT count(*) FROM comments";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($totalComments);
$stmt->fetch();
$stmt->close();

// Return total number of uploads
$result = "SELECT count(*) FROM uploads";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($totalUploads);
$stmt->fetch();
$stmt->close();

// Return total number of playlists
$result = "SELECT count(*) FROM playlists";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($totalPlaylists);
$stmt->fetch();
$stmt->close();

// Return total number of movies in stock
$result = "SELECT SUM(stock) FROM movies";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($totalStock);
$stmt->fetch();
$stmt->close();

// Get count of total watch history sessions
$result = "SELECT count(*) FROM watch_history";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($watchHistoryCount);
$stmt->fetch();
$stmt->close();

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
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">CineGorkha Admin Dashboard</h1>
                        </div><div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div></div></div></div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Users</span>
                                    <span class="info-box-number">
                                        <?php echo $totalUsers; ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-user-shield"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Admins</span>
                                    <span class="info-box-number">
                                        <?php echo $totalAdmins; ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="clearfix hidden-md-up"></div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-film"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Movies</span>
                                    <span class="info-box-number"><?php echo $totalMovies; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-purple elevation-1"><i class="fas fa-layer-group"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Genres</span>
                                    <span class="info-box-number"><?php echo $totalGenres; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-dollar-sign"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Revenue</span>
                                    <span class="info-box-number">
                                        $ <?php echo number_format($totalRevenue, 2); ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-hourglass-half"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Watch Time</span>
                                    <span class="info-box-number"><?php echo $totalWatchTime; ?> mins</span>
                                </div>
                            </div>
                        </div>

                        <div class="clearfix hidden-md-up"></div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-heart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Likes</span>
                                    <span class="info-box-number"><?php echo $totalLikes; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-purple elevation-1"><i class="fas fa-comments"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Comments</span>
                                    <span class="info-box-number"><?php echo $totalComments; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-upload"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Movies Uploaded</span>
                                    <span class="info-box-number"><?php echo $totalUploads; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-list-ul"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Playlists</span>
                                    <span class="info-box-number"><?php echo $totalPlaylists; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-clipboard-list"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Rentals</span>
                                    <span class="info-box-number"><?php echo $watchHistoryCount; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Advanced Analytics</h5>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="chart">
                                                <div id="PieChart" class="col-md-6" style="height: 400px; max-width: 500px; margin: 0px auto;"></div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="col-md-6">
                                            <div class="chart">
                                                <div id="AccountsPerAccountCategories" class="col-md-6" style="height: 400px; max-width: 500px; margin: 0px auto;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-sm-3 col-6">
                                            <div class="description-block border-right">
                                                <h5 class="description-header">$ <?php echo number_format($totalRevenue, 2); ?></h5>
                                                <span class="description-text">TOTAL REVENUE</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 col-6">
                                            <div class="description-block border-right">
                                                <h5 class="description-header"><?php echo $totalLikes; ?></h5>
                                                <span class="description-text">TOTAL LIKES</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 col-6">
                                            <div class="description-block border-right">
                                                <h5 class="description-header"><?php echo $totalComments; ?></h5>
                                                <span class="description-text">TOTAL COMMENTS</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 col-6">
                                            <div class="description-block">
                                                <h5 class="description-header"><?php echo $totalPlaylists; ?></h5>
                                                <span class="description-text">TOTAL PLAYLISTS</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header border-transparent">
                                    <h3 class="card-title">Latest Movie Rentals</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered table-striped m-0">
                                            <thead>
                                                <tr>
                                                    <th>Rental Code</th>
                                                    <th>Movie Title</th>
                                                    <th>User Name</th>
                                                    <th>Rental Price</th>
                                                    <th>Rental Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Get latest rentals
                                                $ret = "SELECT * FROM `rentals` ORDER BY `rentals`.`rental_date` DESC LIMIT 10";
                                                $stmt = $mysqli->prepare($ret);
                                                $stmt->execute();
                                                $res = $stmt->get_result();
                                                while ($row = $res->fetch_object()) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo $row->rental_code; ?></td>
                                                        <td><?php echo $row->title; ?></td>
                                                        <td><?php echo $row->user_name; ?></td>
                                                        <td>$ <?php echo $row->rental_price; ?></td>
                                                        <td><?php echo date("d-M-Y h:m:s ", strtotime($row->rental_date)); ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer clearfix">
                                    <a href="pages_rentals.php" class="btn btn-sm btn-info float-left">View All Rentals</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <aside class="control-sidebar control-sidebar-dark">
            </aside>
        <?php include("dist/_partials/footer.php"); ?>

    </div>
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <script src="dist/js/adminlte.js"></script>

    <script src="dist/js/demo.js"></script>

    <script src="plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
    <script src="plugins/raphael/raphael.min.js"></script>
    <script src="plugins/jquery-mapael/jquery.mapael.min.js"></script>
    <script src="plugins/jquery-mapael/maps/usa_states.min.js"></script>
    <script src="plugins/chart.js/Chart.min.js"></script>

    <script src="dist/js/pages/dashboard2.js"></script>

    <script src="plugins/canvasjs.min.js"></script>
    <script>
        window.onload = function() {
            var Piechart = new CanvasJS.Chart("PieChart", {
                exportEnabled: false,
                animationEnabled: true,
                title: {
                    text: "Movies Per Genre"
                },
                legend: {
                    cursor: "pointer",
                    itemclick: explodePie
                },
                data: [{
                    type: "pie",
                    showInLegend: true,
                    toolTipContent: "{name}: <strong>{y} Movies</strong>",
                    indexLabel: "{name} - {y}",
                    dataPoints: [
                        <?php
                        // Get counts of movies per genre
                        $ret = "SELECT g.genre_name, COUNT(m.movie_id) as movie_count FROM genres g JOIN movies m ON g.genre_id = m.genre_id GROUP BY g.genre_id";
                        $stmt = $mysqli->prepare($ret);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        while ($row = $res->fetch_object()) {
                            echo "{ y: " . $row->movie_count . ", name: \"" . $row->genre_name . "\" },";
                        }
                        $stmt->close();
                        ?>
                    ]
                }]
            });

            var AccChart = new CanvasJS.Chart("AccountsPerAccountCategories", {
                exportEnabled: false,
                animationEnabled: true,
                title: {
                    text: "User Engagement"
                },
                legend: {
                    cursor: "pointer",
                    itemclick: explodePie
                },
                data: [{
                    type: "pie",
                    showInLegend: true,
                    toolTipContent: "{name}: <strong>{y}</strong>",
                    indexLabel: "{name} - {y}",
                    dataPoints: [{
                            y: <?php echo $totalLikes; ?>,
                            name: "Likes",
                            exploded: true
                        }, {
                            y: <?php echo $totalComments; ?>,
                            name: "Comments",
                            exploded: true
                        }, {
                            y: <?php echo $totalPlaylists; ?>,
                            name: "Playlists",
                            exploded: true
                        }
                    ]
                }]
            });

            Piechart.render();
            AccChart.render();
        }

        function explodePie(e) {
            if (typeof(e.dataSeries.dataPoints[e.dataPointIndex].exploded) === "undefined" || !e.dataSeries.dataPoints[e.dataPointIndex].exploded) {
                e.dataSeries.dataPoints[e.dataPointIndex].exploded = true;
            } else {
                e.dataSeries.dataPoints[e.dataPointIndex].exploded = false;
            }
            e.chart.render();

        }
    </script>

</body>

</html>