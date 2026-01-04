<?php
include 'database.php';

function logActivity($user_id, $action){
    global $conn;
    if($user_id <= 0) return; // Prevent logging invalid user ID
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $action);
    $stmt->execute();
}
?>
