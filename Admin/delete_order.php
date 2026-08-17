<?php

session_start();

require_once "../classes/Order.php";

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}

if(!isset($_GET['id']))
{
    header("Location: order_history.php");
    exit();
}

$order_id = $_GET['id'];

$order = new Order();

$deleted = $order->deleteOrder($order_id);

if($deleted)
{
    $_SESSION['success'] = "Order deleted successfully.";
}
else
{
    $_SESSION['error'] = "Unable to delete order.";
}

header("Location: order_history.php");
exit();

?>