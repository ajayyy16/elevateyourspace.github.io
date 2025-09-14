<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

require 'db_connection.php';

$input = json_decode(file_get_contents('php://input'), true);
$orderId = $input['order_id'] ?? '';

// Verify order belongs to user
$stmt = $conn->prepare("
    SELECT status FROM orders 
    WHERE order_id = ? AND user_id = ?
");
$stmt->bind_param("si", $orderId, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

// Check if order can be cancelled
if ($order['status'] != 'pending' && $order['status'] != 'processing') {
    echo json_encode(['success' => false, 'message' => 'Order cannot be cancelled at this stage']);
    exit();
}

// Update order status
$update = $conn->prepare("
    UPDATE orders SET status = 'cancelled' 
    WHERE order_id = ? AND user_id = ?
");
$update->bind_param("si", $orderId, $_SESSION['user_id']);

if ($update->execute()) {
    echo json_encode(['success' => true, 'message' => 'Order cancelled successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to cancel order']);
}