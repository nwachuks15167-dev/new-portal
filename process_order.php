<?php

session_start();

require_once "classes/Order.php";

if(!isset($_SESSION['user_id']))
{
    header("Location: login.php");
    exit();
}

if(!isset($_POST['checkout']))
{
    header("Location: checkout.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$address = trim($_POST['address']);
$total_amount = $_POST['total_amount'];
$payment_method = $_POST['payment_method'];

$order = new Order();

// Create Order
$order_id = $order->createOrder($user_id, $address, $total_amount, $payment_method);

// Check if order was created
if(!$order_id)
{
    $_SESSION['error'] = "Unable to place order.";
    header("Location: checkout.php");
    exit();
}

// Save every product in cart into order_items
$order->saveOrderItems($order_id, $user_id);

// Redirect depending on payment method
if($payment_method == "Bank Transfer")
{
    header("Location: bank_transfer.php?order_id=".$order_id);
   exit();
}

if($payment_method == "Cash on Delivery")
{
    // Optional: update payment status
    $order->updateOrderStatus($order_id,"pending","Processing");
     // Now reduce stock
    $order->reduceProductQuantity($order_id);
    // Now clear My Products
    $order->clearUserProducts($user_id);
    header("Location: order_success.php?order_id=".$order_id);
    exit();
}

?>