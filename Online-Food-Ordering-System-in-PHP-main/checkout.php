<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php");
session_start();

if (empty($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$item_total = 0;
$cart = $_SESSION["cart_item"] ?? [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
    $submittedToken = $_POST["csrf_token"] ?? "";
    $sessionToken = $_SESSION["csrf_token"] ?? "";
    if (!is_string($submittedToken) || !is_string($sessionToken) ||
        $sessionToken === "" || !hash_equals($sessionToken, $submittedToken)) {
        http_response_code(403);
        exit("Invalid request token.");
    }

    if (!is_array($cart) || $cart === []) {
        http_response_code(400);
        exit("Your cart is empty.");
    }

    mysqli_begin_transaction($db);
    $dishQuery = mysqli_prepare($db, "SELECT title, price FROM dishes WHERE d_id = ?");
    $orderInsert = mysqli_prepare($db, "INSERT INTO users_orders (u_id, title, quantity, price) VALUES (?, ?, ?, ?)");
    if (!$dishQuery || !$orderInsert) {
        mysqli_rollback($db);
        error_log("Unable to prepare checkout statements: " . mysqli_error($db));
        http_response_code(500);
        exit("Unable to place your order right now.");
    }

    $userId = (int) $_SESSION["user_id"];
    $valid = true;
    $total = 0.0;
    foreach ($cart as $item) {
        $dishId = filter_var($item["d_id"] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        $quantity = filter_var($item["quantity"] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 99]]);
        if ($dishId === false || $quantity === false) {
            $valid = false;
            break;
        }

        mysqli_stmt_bind_param($dishQuery, "i", $dishId);
        if (!mysqli_stmt_execute($dishQuery)) {
            $valid = false;
            break;
        }
        mysqli_stmt_bind_result($dishQuery, $dishTitle, $dishPrice);
        if (!mysqli_stmt_fetch($dishQuery)) {
            mysqli_stmt_free_result($dishQuery);
            $valid = false;
            break;
        }
        mysqli_stmt_free_result($dishQuery);

        $price = (string) $dishPrice;
        mysqli_stmt_bind_param($orderInsert, "isis", $userId, $dishTitle, $quantity, $price);
        if (!mysqli_stmt_execute($orderInsert)) {
            $valid = false;
            break;
        }
        $total += (float) $price * $quantity;
    }

    mysqli_stmt_close($dishQuery);
    mysqli_stmt_close($orderInsert);
    if (!$valid) {
        mysqli_rollback($db);
        http_response_code(400);
        exit("Your cart contains an invalid or unavailable item. Please review it and try again.");
    }

    if (!mysqli_commit($db)) {
        mysqli_rollback($db);
        error_log("Checkout transaction commit failed: " . mysqli_error($db));
        http_response_code(500);
        exit("Unable to place your order right now.");
    }

    unset($_SESSION["cart_item"]);
    header("Location: your_orders.php");
    exit;
}
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="#">
    <title>Checkout || Online Food Ordering System - Code Camp BD</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580
    <div class="site-wrapper">
        <header id="header" class="header-scroll top-header headrom">
            <nav class="navbar navbar-dark">
                <div class="container">
                    <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" data-target="#mainNavbarCollapse">&#9776;</button>
                    <a class="navbar-brand" href="index.php"> <img class="img-rounded" src="images/logo.png" alt="" width="18%"> </a>
                    <div class="collapse navbar-toggleable-md  float-lg-right" id="mainNavbarCollapse">
                        <ul class="nav navbar-nav">
                            <li class="nav-item"> <a class="nav-link active" href="index.php">Home <span class="sr-only">(current)</span></a> </li>
                            <li class="nav-item"> <a class="nav-link active" href="restaurants.php">Restaurants <span class="sr-only"></span></a> </li>

                            <?php
						if(empty($_SESSION["user_id"]))
							{
								echo '<li class="nav-item"><a href="login.php" class="nav-link active">Login</a> </li>
							  <li class="nav-item"><a href="registration.php" class="nav-link active">Register</a> </li>';
							}
						else
							{
									
									
										echo  '<li class="nav-item"><a href="your_orders.php" class="nav-link active">My Orders</a> </li>';
									echo  '<li class="nav-item"><a href="logout.php" class="nav-link active">Logout</a> </li>';
							}

						?>
                            <!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <div class="page-wrapper">
            <div class="top-links">
                <div class="container">
                    <ul class="row links">

                        <li class="col-xs-12 col-sm-4 link-item"><span>1</span><a href="restaurants.php">Choose Restaurant</a></li>
                        <li class="col-xs-12 col-sm-4 link-item "><span>2</span><a href="#">Pick Your favorite food</a></li>
                        <li class="col-xs-12 col-sm-4 link-item active"><span>3</span><a href="checkout.php">Order and Pay</a></li>
                    </ul>
                </div>
            </div>

            <div class="container">

                <span style="color:green;">
                    <?php echo $success; ?>
                </span>

            </div>


            <!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580

            <div class="container m-t-30">
                <form action="" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, "UTF-8"); ?>">
                    <div class="widget clearfix">

                        <div class="widget-body">
                            <form method="post" action="#">
                                <div class="row">

                                    <div class="col-sm-12">
                                        <div class="cart-totals margin-b-20">
                                            <div class="cart-totals-title">
                                                <h4>Cart Summary</h4>
                                            </div>
                                            <div class="cart-totals-fields">

                                                <table class="table">
                                                    <tbody>

                                                        <!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580

                                                        <tr>
                                                            <td>Cart Subtotal</td>
                                                            <td> <?php echo "$".$item_total; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Delivery Charges</td>
                                                            <td>Free</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-color"><strong>Total</strong></td>
                                                            <td class="text-color"><strong> <?php echo "$".$item_total; ?></strong></td>
                                                        </tr>
                                                    </tbody>

                                                    <!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580


                                                </table>
                                            </div>
                                        </div>
                                        <div class="payment-option">
                                            <ul class=" list-unstyled">
                                                <li>
                                                    <label class="custom-control custom-radio  m-b-20">
                                                        <input name="mod" id="radioStacked1" checked value="COD" type="radio" class="custom-control-input"> <span class="custom-control-indicator"></span> <span class="custom-control-description">Cash on Delivery</span>
                                                    </label>
                                                </li>
                                                <li>
                                                    <label class="custom-control custom-radio  m-b-10">
                                                        <input name="mod" type="radio" value="paypal" disabled class="custom-control-input"> <span class="custom-control-indicator"></span> <span class="custom-control-description">Paypal <img src="images/paypal.jpg" alt="" width="90"></span> </label>
                                                </li>
                                            </ul>
                                            <p class="text-xs-center"> <input type="submit" onclick="return confirm('Do you want to confirm the order?');" name="submit" class="btn btn-success btn-block" value="Order Now"> </p>
                                        </div>
                            </form>
                        </div>
                    </div>

            </div>
        </div>
        </form>
    </div>
    <!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580
    <?php include "include/footer.php" ?>
    </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/tether.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/animsition.min.js"></script>
    <script src="js/bootstrap-slider.min.js"></script>
    <script src="js/jquery.isotope.min.js"></script>
    <script src="js/headroom.js"></script>
    <script src="js/foodpicky.min.js"></script>
</body>

</html>
<!--  Author Name: Matiur Rahaman.
GitHub link: https://github.com/matiur25580
