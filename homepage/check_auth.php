<?php
// check_auth.php
session_start();
header('Content-Type: application/json');

echo json_encode([
    'authenticated' => isset($_SESSION['user_id'])
]);