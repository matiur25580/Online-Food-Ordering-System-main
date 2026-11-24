<!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580
<!DOCTYPE html>
<html lang="en">
<?php

session_start(); 
error_reporting(0); 
include("connection/connect.php"); 
// Registration validates submitted identity details, checks uniqueness, and stores only a password hash.
if (isset($_POST['submit'])) {
    $username = trim($_POST['username'] ?? '');
    $firstName = trim($_POST['firstname'] ?? '');
    $lastName = trim($_POST['lastname'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['cpassword'] ?? '';
    $address = trim($_POST['address'] ?? '');

    if ($username === '' || $firstName === '' || $lastName === '' || $email === '' || $phone === '' || $password === '' || $confirmPassword === '') {
        $message = "All fields must be Required!";
    } elseif ($password !== $confirmPassword) {
        echo "<script>alert('Password not match');</script>";
    } elseif (strlen($password) < 8) {
        echo "<script>alert('Password must be at least 8 characters');</script>";
    } elseif (strlen($phone) < 10) {
        echo "<script>alert('Invalid phone number!');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email address please type a valid email!');</script>";
    } else {
        $check = mysqli_prepare($db, "SELECT username, email FROM users WHERE username = ? OR email = ? LIMIT 1");
        if ($check) {
            mysqli_stmt_bind_param($check, "ss", $username, $email);
            mysqli_stmt_execute($check);
            $result = mysqli_stmt_get_result($check);
            $existing = $result ? mysqli_fetch_assoc($result) : null;
            mysqli_stmt_close($check);

            if ($existing && strcasecmp($existing['username'], $username) === 0) {
                echo "<script>alert('Username Already exists!');</script>";
            } elseif ($existing && strcasecmp($existing['email'], $email) === 0) {
                echo "<script>alert('Email Already exists!');</script>";
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $insert = mysqli_prepare($db, "INSERT INTO users (username, f_name, l_name, email, phone, password, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if ($insert) {
                    mysqli_stmt_bind_param($insert, "sssssss", $username, $firstName, $lastName, $email, $phone, $passwordHash, $address);
                    if (mysqli_stmt_execute($insert)) {
                        mysqli_stmt_close($insert);
                        header("Location: login.php");
                        exit;
                    }
                    mysqli_stmt_close($insert);
                }
                $message = "Registration could not be completed. Please try again.";
            }
        } else {
            $message = "Registration could not be completed. Please try again.";
        }
    }
}
?>
<!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580


<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="#">
    <title>Registration</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580

<body>
    <!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580

    <div style=" background-image: url('images/img/pimg.jpg');">
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

                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <div class="page-wrapper">

            <div class="container">
                <ul>


                </ul>
            </div>
            <!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580

            <section class="contact-page inner-page">
                <div class="container ">
                    <div class="row ">
                        <div class="col-md-12">
                            <div class="widget">
                                <div class="widget-body">
                                    <!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580

                                    <form action="" method="post">
                                        <div class="row">
                                            <div class="form-group col-sm-12">
                                                <label for="exampleInputEmail1">User-Name</label>
                                                <input class="form-control" type="text" name="username" id="example-text-input">
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="exampleInputEmail1">First Name</label>
                                                <input class="form-control" type="text" name="firstname" id="example-text-input">
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="exampleInputEmail1">Last Name</label>
                                                <input class="form-control" type="text" name="lastname" id="example-text-input-2">
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="exampleInputEmail1">Email Address</label>
                                                <input type="text" class="form-control" name="email" id="exampleInputEmail1" aria-describedby="emailHelp">
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="exampleInputEmail1">Phone number</label>
                                                <input class="form-control" type="text" name="phone" id="example-tel-input-3">
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="exampleInputPassword1">Password</label>
                                                <input type="password" class="form-control" name="password" id="exampleInputPassword1">
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="exampleInputPassword1">Confirm password</label>
                                                <input type="password" class="form-control" name="cpassword" id="exampleInputPassword2">
                                            </div>
                                            <div class="form-group col-sm-12">
                                                <label for="exampleTextarea">Delivery Address</label>
                                                <textarea class="form-control" id="exampleTextarea" name="address" rows="3"></textarea>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-sm-4">
                                                <p> <input type="submit" value="Register" name="submit" class="btn theme-btn"> </p>
                                            </div>
                                        </div>
                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>
                </div>
            </section>
            <!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580


            <?php include "include/footer.php" ?>

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
<!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580

</html>