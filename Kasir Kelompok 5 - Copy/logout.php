<?php
session_start();

// Menghancurkan session dan logout
session_destroy();
header("Location: login.php");
exit;
?>
