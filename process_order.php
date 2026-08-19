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


// Create order
$order_id = $order->createOrder(
    $user_id,
    $address,
    $total_amount,
    $payment_method
);

// Check if order was created
if(!$order_id)
{
    $_SESSION['error'] = "Unable to place order.";
    header("Location: checkout.php");
    exit();
}

// Save products into order_items
if(!$order->saveOrderItems($order_id, $user_id))
{
    $_SESSION['error'] = "Unable to save order products.";
    header("Location: checkout.php");
    exit();
}

// BANK TRANSFER
if($payment_method == "Bank Transfer")
{
    header(
        "Location: bank_transfer.php?order_id=".$order_id
    );
    exit();
}

// CASH ON DELIVERY
if($payment_method == "Cash on Delivery")
{
    $order->updateOrderStatus(
        $order_id,
        "pending",
        "Processing"
    );
    // Reduce stock
    if(!$order->reduceProductQuantity($order_id))
    {
        $_SESSION['error'] =
            "Product quantity could not be updated.";
        header("Location: checkout.php");
        exit();
    }
    // Clear user's My Products
    if(!$order->clearUserProducts($user_id))
    {
        $_SESSION['error'] =
            "Products could not be cleared.";
        header("Location: checkout.php");
        exit();
    }
    header(
        "Location: order_success.php?order_id=".$order_id
    );

    exit();
}

?>