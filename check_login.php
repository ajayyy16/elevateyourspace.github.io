<?php
session_start();

// Debugging: Print session data
header('Content-Type: application/json');
echo json_encode([
    "logged_in" => isset($_SESSION['user_id']),
    "session_data" => $_SESSION
]);
?>
