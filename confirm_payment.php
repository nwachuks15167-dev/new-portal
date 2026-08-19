<?php

session_start();

require_once "classes/Order.php";


// Make sure user is logged in
if(!isset($_SESSION['user_id']))
{
    header("Location: login.php");
    exit();
}


// Make sure form was submitted
if(!isset($_POST['confirm_payment']))
{
    header("Location: order_history.php");
    exit();
}


// Make sure order ID exists
if(!isset($_POST['order_id']))
{
    $_SESSION['error'] = "Invalid order.";

    header("Location: order_history.php");
    exit();
}


$order_id = (int)$_POST['order_id'];

$user_id = $_SESSION['user_id'];

$order = new Order();


// Get order
$data = $order->getOrderById($order_id);

if(!$data)
{
    $_SESSION['error'] = "Order not found.";

    header("Location: order_history.php");
    exit();
}


// Check ownership
if($data['user_id'] != $user_id)
{
    $_SESSION['error'] = "Unauthorized order.";

    header("Location: order_history.php");
    exit();
}


// Make sure this is Cash on Delivery
if($data['payment_method'] != "Cash on Delivery")
{
    $_SESSION['error'] =
        "This is not a Cash on Delivery order.";

    header("Location: order_history.php");
    exit();
}


// Confirm COD payment
if($order->confirmCashOnDelivery($order_id, $user_id))
{
    $_SESSION['success'] =
        "Payment confirmed successfully. Your order has been marked as delivered.";

    header("Location: order_history.php");
    exit();
}
else
{
    $_SESSION['error'] =
        "Unable to confirm payment. Please try again.";

    header("Location: order_history.php");
    exit();
}

?>