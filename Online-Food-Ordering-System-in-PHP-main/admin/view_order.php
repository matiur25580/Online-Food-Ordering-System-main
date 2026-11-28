<?php
declare(strict_types=1);

require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../connection/connect.php';

$orderId = filter_input(INPUT_GET, 'user_upd', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);
if (!$orderId) {
    http_response_code(400);
    exit('Invalid order identifier.');
}

$sql = 'SELECT o.o_id, o.title, o.quantity, o.price, o.address, o.date, o.status,
               u.username
        FROM users_orders AS o
        INNER JOIN users AS u ON u.u_id = o.u_id
        WHERE o.o_id = ?
        LIMIT 1';
$stmt = mysqli_prepare($db, $sql);
if (!$stmt) {
    error_log('Admin order lookup prepare failed: ' . mysqli_error($db));
    http_response_code(500);
    exit('Unable to load this order.');
}
mysqli_stmt_bind_param($stmt, 'i', $orderId);
if (!mysqli_stmt_execute($stmt)) {
    error_log('Admin order lookup failed: ' . mysqli_stmt_error($stmt));
    mysqli_stmt_close($stmt);
    http_response_code(500);
    exit('Unable to load this order.');
}
$result = mysqli_stmt_get_result($stmt);
$order = $result ? mysqli_fetch_assoc($result) : null;
mysqli_stmt_close($stmt);

if (!$order) {
    http_response_code(404);
    exit('Order not found.');
}

function order_escape($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$statusLabels = [
    '' => 'Dispatch',
    'NULL' => 'Dispatch',
    'in process' => 'On the way',
    'closed' => 'Delivered',
    'rejected' => 'Cancelled',
];
$status = (string) ($order['status'] ?? '');
$statusLabel = $statusLabels[$status] ?? 'Unknown';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Order</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <main class="container" style="padding: 24px;">
        <div class="card card-outline-primary">
            <div class="card-header">
                <h2 class="m-b-0 text-white">View Order #<?= (int) $order['o_id'] ?></h2>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr><th>Username</th><td><?= order_escape($order['username']) ?></td></tr>
                        <tr><th>Item</th><td><?= order_escape($order['title']) ?></td></tr>
                        <tr><th>Quantity</th><td><?= (int) $order['quantity'] ?></td></tr>
                        <tr><th>Price</th><td>$<?= order_escape($order['price']) ?></td></tr>
                        <tr><th>Address</th><td><?= order_escape($order['address']) ?></td></tr>
                        <tr><th>Date</th><td><?= order_escape($order['date']) ?></td></tr>
                        <tr><th>Status</th><td><?= order_escape($statusLabel) ?></td></tr>
                    </tbody>
                </table>
                <div class="d-flex flex-wrap" style="gap: 8px;">
                    <a class="btn btn-primary" href="order_update.php?form_id=<?= (int) $order['o_id'] ?>">Update Order Status</a>
                    <a class="btn btn-info" href="userprofile.php?newform_id=<?= (int) $order['o_id'] ?>">View Customer Details</a>
                    <button type="button" class="btn btn-secondary" onclick="window.close()">Close</button>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
