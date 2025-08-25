<html>
<title>MovieStream SuperAdmin</title>

<head>
    <link rel="stylesheet" type="text/css" href="css/index.css">
    <link rel="shortcut icon" href="img/logoTitle.jpg">
</head>

<body>
    <?php include 'header.php' ?>

    <div class="index_container">
        <!-- Featured Movies Slider -->
        <div class="slider">
            <div class="slideimg">
                <img src="movies/featured1.jpg" alt="Featured Movie 1">
                <img src="movies/featured2.jpg" alt="Featured Movie 2">
                <img src="movies/featured3.jpg" alt="Featured Movie 3">
                <img src="movies/featured4.jpg" alt="Featured Movie 4">
            </div>
        </div><br>

        <!-- Latest Announcements -->
        <div class="newsroom">
            <marquee class="marquee_news" scrolldelay="20">
                <p class="newsfeed">
                    <span>🔥 New Release: "Inception" now streaming in HD.</span>
                    <span>🎥 Upcoming: Avengers Marathon Weekend.</span>
                    <span>⚡ System Update: New Admin tools added for better movie management.</span>
                </p>
            </marquee>
        </div><br><br>

        <!-- Admin Updates -->
        <div class="news_events">
            <h4>SuperAdmin | Updates | Notices</h4><br>
            <ul>
                <p>Manage movie catalog, upload posters, and update streaming links.</p><br>
                <p>View registered users and subscription analytics in real time.</p><br>
                <p>Schedule upcoming releases and manage promotions.</p>
            </ul>
        </div>

        <!-- Admin Services -->
        <div id="iservices" class="online_services">
            <h4>SuperAdmin Controls</h4>
            <ul>
                <a href="admin/add_movie.php">
                    <li>Add New Movie</li>
                </a>
                <a href="admin/manage_users.php">
                    <li>Manage Users</li>
                </a><br>
                <a href="admin/manage_categories.php">
                    <li>Manage Categories</li>
                </a>
                <a href="admin/view_reports.php">
                    <li>View Reports & Analytics</li>
                </a><br>
                <a href="admin/settings.php">
                    <li>System Settings</li>
                </a>
            </ul>
        </div>

        <!-- About Section -->
        <div id="aboutus" class="about">
            <span>About MovieStream</span><br><br>
            <p>MovieStream SuperAdmin portal allows administrators to manage movies, categories, users, and system updates. 
            Built for ultimate control over the streaming platform, it ensures a smooth entertainment experience for viewers worldwide.</p>
        </div>

        <!-- Disclaimer -->
        <div class="disclaimer">
            <span>Disclaimer !!</span><br><br>
            <p>All movies hosted on this platform are licensed for streaming. Unauthorized distribution or sharing is prohibited.</p>
            <p>SuperAdmins must ensure that uploaded content complies with copyright laws.</p>
            <p>MovieStream is not responsible for pirated uploads. Only upload legal and verified content.</p>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>
