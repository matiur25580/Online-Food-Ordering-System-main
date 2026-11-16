                <!-- /*!
* Author Name: Matiur Rahaman.
* GitHub link: https://github.com/matiur25580

*/ -->
                <!DOCTYPE html>
                <html lang="en">
                <?php
include("../connection/connect.php");
error_reporting(0);
session_start();
if (isset($_POST['submit'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = mysqli_prepare($db, "SELECT adm_id, password FROM admin WHERE username = ? LIMIT 1");
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
                $upgrade = mysqli_prepare($db, "UPDATE admin SET password = ? WHERE adm_id = ?");
                if ($upgrade) {
                    mysqli_stmt_bind_param($upgrade, "si", $newHash, $row['adm_id']);
                    mysqli_stmt_execute($upgrade);
                    mysqli_stmt_close($upgrade);
                }
            }
        }
        if ($valid) {
            session_regenerate_id(true);
            $_SESSION['adm_id'] = $row['adm_id'];
            header("Location: dashboard.php");
            exit;
        }
    }
    echo "<script>alert('Invalid Username or Password!');</script>";
}
?>
                <!-- /*!
* Author Name: Matiur Rahaman.
* GitHub link: https://github.com/matiur25580

*/ -->

                <head>
                    <meta charset="UTF-8">
                    <title>Admin Login</title>

                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">

                    <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900'>
                    <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Montserrat:400,700'>
                    <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>

                    <link rel="stylesheet" href="css/login.css">


                </head>

                <body>

                    <!-- /*!
* Author Name: Matiur Rahaman.
* GitHub link: https://github.com/matiur25580

*/ -->

                    <div class="container">
                        <div class="info">
                            <h1>Admin Panel </h1>
                        </div>
                    </div>
                    <div class="form">
                        <div class="thumbnail"><img src="images/manager.png" /></div>
                        <span style="color:red;"><?php echo $message; ?></span>
                        <span style="color:green;"><?php echo $success; ?></span>
                        <form class="login-form" action="index.php" method="post">
                            <input type="text" placeholder="Username" name="username" />
                            <input type="password" placeholder="Password" name="password" />
                            <input type="submit" name="submit" value="Login" />

                        </form>
                        <!-- /*!
* Author Name: Matiur Rahaman.
* GitHub link: https://github.com/matiur25580

*/ -->

                    </div>
                    <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
                    <script src='js/index.js'></script>
                </body>

                </html>
                <!-- /*!
* Author Name: Matiur Rahaman.
* GitHub link: https://github.com/matiur25580

*/ -->