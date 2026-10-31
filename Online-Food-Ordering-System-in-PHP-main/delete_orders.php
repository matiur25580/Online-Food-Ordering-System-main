    <!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580
include("connection/connect.php"); //connection to db
error_reporting(0);
session_start();


// sending query
mysqli_query($db,"DELETE FROM users_orders WHERE o_id = '".$_GET['order_del']."'"); 
header("location:your_orders.php"); 

?>
    <!--  Author Name: Matiur Rahaman.
                        GitHub link: https://github.com/matiur25580
