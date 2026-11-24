<?php require_once __DIR__ . '/auth_guard.php'; ?>
                <!-- /*!
* Author Name: Matiur Rahaman.
* GitHub link: https://github.com/matiur25580

*/ -->
                <?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../connection/connect.php';

$formId = filter_input(INPUT_GET, 'form_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$formId) {
    http_response_code(400);
    exit('Invalid order identifier.');
}

// Admin order changes require CSRF validation and update the status plus its remark in one transaction.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    admin_require_csrf_post();
    $status = $_POST['status'] ?? '';
    $remark = trim($_POST['remark'] ?? '');
    $allowedStatuses = ['in process', 'closed', 'rejected'];

    if (!in_array($status, $allowedStatuses, true) || $remark === '' || mb_strlen($remark) > 5000) {
        http_response_code(422);
        exit('Please provide a valid status and a message of at most 5000 characters.');
    }

    mysqli_begin_transaction($db);
    try {
        $orderCheck = mysqli_prepare($db, 'SELECT o_id FROM users_orders WHERE o_id = ? LIMIT 1');
        if (!$orderCheck) throw new RuntimeException('Unable to prepare order lookup.');
        mysqli_stmt_bind_param($orderCheck, 'i', $formId);
        mysqli_stmt_execute($orderCheck);
        $orderResult = mysqli_stmt_get_result($orderCheck);
        $orderExists = $orderResult && mysqli_fetch_assoc($orderResult);
        mysqli_stmt_close($orderCheck);
        if (!$orderExists) throw new RuntimeException('Order not found.');

        $remarkStmt = mysqli_prepare($db, 'INSERT INTO remark (frm_id, status, remark) VALUES (?, ?, ?)');
        if (!$remarkStmt) throw new RuntimeException('Unable to prepare order remark.');
        mysqli_stmt_bind_param($remarkStmt, 'iss', $formId, $status, $remark);
        if (!mysqli_stmt_execute($remarkStmt)) throw new RuntimeException('Unable to save order remark.');
        mysqli_stmt_close($remarkStmt);

        $updateStmt = mysqli_prepare($db, 'UPDATE users_orders SET status = ? WHERE o_id = ?');
        if (!$updateStmt) throw new RuntimeException('Unable to prepare order update.');
        mysqli_stmt_bind_param($updateStmt, 'si', $status, $formId);
        if (!mysqli_stmt_execute($updateStmt)) throw new RuntimeException('Unable to update order status.');
        mysqli_stmt_close($updateStmt);

        mysqli_commit($db);
        header('Location: all_orders.php', true, 303);
        exit;
    } catch (Throwable $error) {
        mysqli_rollback($db);
        error_log('Admin order update failed: ' . $error->getMessage());
        http_response_code($error->getMessage() === 'Order not found.' ? 404 : 500);
        exit('Unable to update this order. Please try again.');
    }
}
?>
                <!-- /*!
 * Author Name: Matiur Rahaman.
 * GitHub link: https://github.com/matiur25580
 
 */ -->

                <script language="javascript" type="text/javascript">
function f2() {
    window.close();
}
ser

function f3() {
    window.print();
}
                </script>

                <head>
                    <!-- /*!
* Author Name: Matiur Rahaman.
* GitHub link: https://github.com/matiur25580

*/ -->

                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <meta name="description" content="">
                    <meta name="author" content="">
                    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
                    <title>Order Update</title>
                    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
                    <link href="css/helper.css" rel="stylesheet">
                    <link href="css/style.css" rel="stylesheet">
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

                    <style type="text/css" rel="stylesheet">
                    .indent-small {
                        margin-left: 5px;
                    }

                    .form-group.internal {
                        margin-bottom: 0;
                    }

                    .dialog-panel {
                        margin: 10px;
                    }

                    .datepicker-dropdown {
                        z-index: 200 !important;
                    }

                    .panel-body {
                        background: #e5e5e5;
                        background: -moz-radial-gradient(center, ellipse cover, #e5e5e5 0%, #ffffff 100%);
                        background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, #e5e5e5), color-stop(100%, #ffffff));
                        background: -webkit-radial-gradient(center, ellipse cover, #e5e5e5 0%, #ffffff 100%);
                        background: -o-radial-gradient(center, ellipse cover, #e5e5e5 0%, #ffffff 100%);
                        background: -ms-radial-gradient(center, ellipse cover, #e5e5e5 0%, #ffffff 100%);
                        background: radial-gradient(ellipse at center, #e5e5e5 0%, #ffffff 100%);
                        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#e5e5e5', endColorstr='#ffffff', GradientType=1);
                        font: 600 15px "Open Sans", Arial, sans-serif;
                    }

                    label.control-label {
                        font-weight: 600;
                        color: #777;
                    }








                    table {
                        width: 650px;
                        border-collapse: collapse;
                        margin: auto;
                        margin-top: 50px;
                    }


                    tr:nth-of-type(odd) {
                        background: #eee;
                    }

                    th {
                        background: #004684;
                        color: white;
                        font-weight: bold;
                    }

                    td,
                    th {
                        padding: 10px;
                        border: 1px solid #ccc;
                        text-align: left;
                        font-size: 14px;
                    }
                    </style>
                </head>

                <body>
                    <!-- /*!
* Author Name: Matiur Rahaman.
* GitHub link: https://github.com/matiur25580

*/ -->


                    <div style="margin-left:50px;">
                        <form name="updateticket" id="updatecomplaint" method="post"><?php echo admin_csrf_input(); ?>




                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td><b>Form Number</b></td>
                                    <td><?php echo (int) $formId; ?></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>

                                    <td>&nbsp;</td>
                                </tr>

                                <tr>
                                    <td><b>Status</b></td>
                                    <td><select name="status" required="required">
                                            <option value="">Select Status</option>
                                            <option value="in process">On the way</option>
                                            <option value="closed">Delivered</option>
                                            <option value="rejected">Cancelled</option>

                                        </select></td>
                                </tr>

                                <!-- /*!
* Author Name: Matiur Rahaman.
* GitHub link: https://github.com/matiur25580

*/ -->

                                <tr>
                                    <td><b>Message</b></td>
                                    <td><textarea name="remark" cols="50" rows="10" required="required"></textarea></td>
                                </tr>

                                <!-- /*!
* Author Name: Matiur Rahaman.
* GitHub link: https://github.com/matiur25580

*/ -->


                                <tr>
                                    <td><b>Action</b></td>
                                    <td><input type="submit" name="update" class="btn btn-primary" value="Submit">

                                        <input name="Submit2" type="submit" class="btn btn-danger" value="Close this window " onClick="return f2();" style="cursor: pointer;" />
                                    </td>
                                </tr>



                                <!-- /*!
* Author Name: Matiur Rahaman.
* GitHub link: https://github.com/matiur25580

*/ -->





                            </table>
                        </form>
                    </div>

                </body>
                <!-- /*!
* Author Name: Matiur Rahaman.
* GitHub link: https://github.com/matiur25580

*/ -->

                </html>