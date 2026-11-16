<!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login || Code Camp BD</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">

    <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900|RobotoDraft:400,100,300,500,700,900'>
    <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>

    <link rel="stylesheet" href="css/login.css">

    <style type="text/css">
    #buttn {
        color: #fff;
        background-color: #5c4ac7;
    }
    </style>

    <!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

</head>
<!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580

<body>
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
    <div style=" background-image: url('images/img/pimg.jpg');">

        <?php
include("connection/connect.php"); 
error_reporting(0); 
session_start(); 
if (isset($_POST['submit'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = mysqli_prepare($db, "SELECT u_id, password FROM users WHERE username = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);
        $valid = false;
        if ($row) {
            $stored = (string) $row['password'];
            $valid = password_verify($password, $stored);
            if (!$valid && hash_equals(strtolower($stored), md5($password))) {
                $valid = true;
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $upgrade = mysqli_prepare($db, "UPDATE users SET password = ? WHERE u_id = ?");
                if ($upgrade) {
                    mysqli_stmt_bind_param($upgrade, "si", $newHash, $row['u_id']);
                    mysqli_stmt_execute($upgrade);
                    mysqli_stmt_close($upgrade);
                }
            }
        }
        if ($valid) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $row['u_id'];
            header("Location: index.php");
            exit;
        }
    }
    $message = "Invalid Username or Password!";
}
?>

        <!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580

        <div class="pen-title">
            < </div>

                <div class="module form-module">
                    <div class="toggle">

                    </div>
                    <div class="form">
                        <h2>Login to your account</h2>
                        <span style="color:red;"><?php echo $message; ?></span>
                        <span style="color:green;"><?php echo $success; ?></span>
                        <form action="" method="post">
                            <input type="text" placeholder="Username" name="username" />
                            <input type="password" placeholder="Password" name="password" />
                            <input type="submit" id="buttn" name="submit" value="Login" />
                        </form>
                    </div>

                    <div class="cta">Not registered?<a href="registration.php" style="color:#5c4ac7;"> Create an account</a></div>
                </div>
                <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>


                <!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580


                <div class="container-fluid pt-3">
                    <p></p>
                </div>



                <?php include "include/footer.php" ?>

                <!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580


</body>

</html>