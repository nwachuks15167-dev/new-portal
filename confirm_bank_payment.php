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

$order_id = $_POST['order_id'];

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

// Make sure this is Bank Transfer
if($data['payment_method'] != "Bank Transfer")
{
    $_SESSION['error'] =
        "This is not a Bank Transfer order.";
    header("Location: order_history.php");
    exit();
}

// Confirm Bank Transfer payment
if(!$order->confirmBankTransferPayment($order_id, $user_id))
{
    $_SESSION['error'] =
        "Payment could not be confirmed.";
    header("Location: bank_transfer.php?order_id=".$order_id);
    exit();
}

// Reduce product stock
if(!$order->reduceProductQuantity($order_id))
{
    $_SESSION['error'] =
        "Product quantity could not be updated.";
    header("Location: order_history.php");
    exit();
}

// Clear user's My Products
if(!$order->clearUserProducts($user_id))
{
    $_SESSION['error'] =
        "Products could not be cleared.";
    header("Location: order_history.php");
    exit();
}

// Success
$_SESSION['success'] =
    "Bank payment confirmed successfully. Your order is being processed.";
header("Location: order_success.php?order_id=".$order_id);
exit();

?>