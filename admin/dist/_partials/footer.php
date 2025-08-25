<?php
$ret = "SELECT * FROM `system_settings` LIMIT 1";
$stmt = $mysqli->prepare($ret);
$stmt->execute();
$res = $stmt->get_result();
$sys = $res->fetch_object();
?>
<footer class="main-footer">
    <strong>&copy; 2024-<?php echo date('Y'); ?> <?php echo isset($sys->site_name) ? htmlspecialchars($sys->site_name) : 'CineGorkha'; ?>.</strong>
    <?php echo isset($sys->footer_text) ? htmlspecialchars($sys->footer_text) : 'All rights reserved.'; ?>
</footer>
