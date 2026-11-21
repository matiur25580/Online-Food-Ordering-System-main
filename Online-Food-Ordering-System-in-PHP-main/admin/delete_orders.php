<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../connection/connect.php';

admin_require_csrf_post();
$id = filter_input(INPUT_POST, 'order_del', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) {
    http_response_code(400);
    exit('Invalid record identifier.');
}

$stmt = mysqli_prepare($db, 'DELETE FROM users_orders WHERE o_id = ?');
if (!$stmt) {
    error_log('Admin delete prepare failed: ' . mysqli_error($db));
    http_response_code(500);
    exit('Unable to complete the request.');
}
mysqli_stmt_bind_param($stmt, 'i', $id);
if (!mysqli_stmt_execute($stmt)) {
    error_log('Admin delete execution failed: ' . mysqli_stmt_error($stmt));
    mysqli_stmt_close($stmt);
    http_response_code(500);
    exit('Unable to complete the request.');
}
mysqli_stmt_close($stmt);
header('Location: all_orders.php', true, 303);
exit;
