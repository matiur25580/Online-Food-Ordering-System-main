<?php require_once __DIR__ . '/auth_guard.php'; ?>
                <!-- /*!
* Author Name: Matiur Rahaman.
* GitHub link: https://github.com/matiur25580

*/ -->
                <?php
include("../connection/connect.php");
error_reporting(0);
session_start();

mysqli_query($db,"DELETE FROM res_category WHERE c_id = '".$_GET['cat_del']."'");
header("location:add_category.php");  

?>
                <!-- /*!
* Author Name: Matiur Rahaman.
* GitHub link: https://github.com/matiur25580

*/ -->