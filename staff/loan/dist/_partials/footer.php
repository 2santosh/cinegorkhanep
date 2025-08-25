<?php
// Prepare and execute the query to fetch system settings
$ret = "SELECT * FROM `iB_SystemSettings` LIMIT 1";
$stmt = $mysqli->prepare($ret);
$stmt->execute();
$res = $stmt->get_result();

// Check if there are any system settings found
if ($res->num_rows > 0) {
    // Fetch the first row of system settings
    $sys = $res->fetch_object();
?>
    <footer class="main-footer">
        <strong>&copy; 2024-<?php echo date('Y'); ?> SBS Bank.</strong>
        All rights reserved.
    </footer>
<?php
} else {
    // No system settings found, display a default footer
?>
    <footer class="main-footer">
        <strong>&copy; <?php echo date('Y'); ?> SBS Bank.</strong>
        All rights reserved.
    </footer>
<?php
}
?>
