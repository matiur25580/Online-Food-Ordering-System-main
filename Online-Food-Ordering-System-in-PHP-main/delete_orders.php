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

// Preserve order history and allow cancellation only before restaurant processing starts.
$stmt = mysqli_prepare(
    $db,
    "UPDATE users_orders SET status = 'rejected' WHERE o_id = ? AND u_id = ? AND (status IS NULL OR status = '' OR status = 'pending')"
);
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

$affectedRows = mysqli_stmt_affected_rows($stmt);
mysqli_stmt_close($stmt);
if ($affectedRows === 1) {
    $_SESSION['order_cancel_notice'] = 'Your cancellation was recorded. The order remains in your history.';
} else {
    $_SESSION['order_cancel_notice'] = 'This order is no longer available for cancellation.';
}
header('Location: your_orders.php');
exit;
