<?php
declare(strict_types=1);

require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../connection/connect.php';

// This popup is read-only. Order status changes are handled by order_update.php.
$orderId = filter_input(INPUT_GET, 'newform_id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);
if (!$orderId) {
    http_response_code(400);
    exit('Invalid order identifier.');
}

$sql = 'SELECT u.u_id, u.f_name, u.l_name, u.email, u.phone, u.status AS user_status, u.date AS registered_at
        FROM users_orders AS o
        INNER JOIN users AS u ON u.u_id = o.u_id
        WHERE o.o_id = ?
        LIMIT 1';
$stmt = mysqli_prepare($db, $sql);
if (!$stmt) {
    error_log('Admin user profile lookup prepare failed: ' . mysqli_error($db));
    http_response_code(500);
    exit('Unable to load this customer profile.');
}
mysqli_stmt_bind_param($stmt, 'i', $orderId);
if (!mysqli_stmt_execute($stmt)) {
    error_log('Admin user profile lookup failed: ' . mysqli_stmt_error($stmt));
    mysqli_stmt_close($stmt);
    http_response_code(500);
    exit('Unable to load this customer profile.');
}
$result = mysqli_stmt_get_result($stmt);
$customer = $result ? mysqli_fetch_assoc($result) : null;
mysqli_stmt_close($stmt);

if (!$customer) {
    http_response_code(404);
    exit('Customer profile not found.');
}

function profile_escape($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Profile</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <main class="container" style="padding: 24px;">
        <div class="card">
            <div class="card-header">
                <h2 class="m-b-0">Customer Profile</h2>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr><th>Customer ID</th><td><?= profile_escape($customer['u_id']) ?></td></tr>
                        <tr><th>First Name</th><td><?= profile_escape($customer['f_name']) ?></td></tr>
                        <tr><th>Last Name</th><td><?= profile_escape($customer['l_name']) ?></td></tr>
                        <tr><th>Email</th><td><?= profile_escape($customer['email']) ?></td></tr>
                        <tr><th>Phone</th><td><?= profile_escape($customer['phone']) ?></td></tr>
                        <tr><th>Registered</th><td><?= profile_escape($customer['registered_at']) ?></td></tr>
                        <tr><th>Account Status</th><td><?= (int) $customer['user_status'] === 1 ? 'Active' : 'Blocked' ?></td></tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-secondary" onclick="window.close()">Close</button>
            </div>
        </div>
    </main>
</body>
</html>
