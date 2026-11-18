<?php
include("connection/connect.php");
session_start();

// Only an authenticated customer may cancel their own order.
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method not allowed.');
}

$submittedToken = $_POST['csrf_token'] ?? '';
$sessionToken = $_SESSION['csrf_token'] ?? '';
if (!is_string($submittedToken) || !is_string($sessionToken) ||
    $sessionToken === '' || !hash_equals($sessionToken, $submittedToken)) {
    http_response_code(403);
    exit('Invalid request token.');
}

$orderId = $_POST['order_del'] ?? '';
if (!is_string($orderId) || !ctype_digit($orderId) || (int) $orderId < 1) {
    http_response_code(400);
    exit('Invalid order reference.');
}

$stmt = mysqli_prepare($db, 'DELETE FROM users_orders WHERE o_id = ? AND u_id = ?');
if (!$stmt) {
    error_log('Unable to prepare customer order cancellation: ' . mysqli_error($db));
    http_response_code(500);
    exit('Unable to cancel this order right now.');
}

$userId = (int) $_SESSION['user_id'];
$validatedOrderId = (int) $orderId;
mysqli_stmt_bind_param($stmt, 'ii', $validatedOrderId, $userId);
if (!mysqli_stmt_execute($stmt)) {
    error_log('Customer order cancellation failed: ' . mysqli_stmt_error($stmt));
    mysqli_stmt_close($stmt);
    http_response_code(500);
    exit('Unable to cancel this order right now.');
}

mysqli_stmt_close($stmt);
header('Location: your_orders.php');
exit;
