<?php
session_start();
include 'database.php';
include 'activity_log.php';
if(isset($_SESSION['user_id'])){
    logActivity($_SESSION['user_id'], "Logged out");
}
session_destroy();
header("Location: login.php");
exit;
?>
