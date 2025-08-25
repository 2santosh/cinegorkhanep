<?php
/* Persisit System Settings On Brand */
$ret = "SELECT * FROM `iB_SystemSettings` ";
$stmt = $mysqli->prepare($ret);
$stmt->execute(); //ok
$res = $stmt->get_result();
while ($sys = $res->fetch_object()) {
?>
  <footer class="main-footer">
    <strong>&copy; 2024-<?php echo date('Y'); ?> SBS Bank.</strong>
    All rights reserved.
  </footer>
<?php } ?>