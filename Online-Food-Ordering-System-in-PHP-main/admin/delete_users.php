                <!-- /*!
* Author Name: Matiur Rahaman.
* GitHub link: https://github.com/matiur25580

*/ -->
                <?php
include("../connection/connect.php");
error_reporting(0);
session_start();

mysqli_query($db,"DELETE FROM users WHERE u_id = '".$_GET['user_del']."'");
header("location:all_users.php");  

?>
                <!-- /*!
* Author Name: Matiur Rahaman.
* GitHub link: https://github.com/matiur25580

*/ -->