<?php require_once __DIR__ . '/auth_guard.php'; ?>
                <!-- /*!
* Author Name: Matiur Rahaman.
* GitHub link: https://github.com/matiur25580

*/ -->
                <?php
include("../connection/connect.php");
error_reporting(0);
session_start();

mysqli_query($db,"DELETE FROM restaurant WHERE rs_id = '".$_GET['res_del']."'");
header("location:all_restaurant.php");  

?>
                <!-- /*!
* Author Name: Matiur Rahaman.
* GitHub link: https://github.com/matiur25580

*/ -->