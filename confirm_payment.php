<?php

session_start();

require_once "classes/Order.php";

if(!isset($_SESSION['user_id']))
{
    header("Location: login.php");
    exit();
}

if(!isset($_POST['order_id']))
{
    header("Location: checkout.php");
    exit();
}

$order_id = $_POST['order_id'];

$user_id = $_SESSION['user_id'];

$order = new Order();


// Get order
$data = $order->getOrderById($order_id);

if(!$data)
{
    die("Order not found.");
}


// Check ownership
if($data['user_id'] != $user_id)
{
    die("Unauthorized order.");
}


// Prevent duplicate payment
if($data['payment_status'] == "Paid")
{
    header(
        "Location: order_success.php?order_id=".$order_id
    );

    exit();
}


// Reduce ONLY the quantity ordered
if(!$order->reduceProductQuantity($order_id))
{
    die("Product quantity could not be updated.");
}


// Mark payment as paid
if(!$order->updateOrderStatus(
    $order_id,
    "Paid",
    "Processing"
))
{
    die("Payment could not be confirmed.");
}


// Remove products from My Products
if(!$order->clearUserProducts($user_id))
{
    die("Products could not be cleared.");
}


// Success
header(
    "Location: order_success.php?order_id=".$order_id
);

exit();

?>