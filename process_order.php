
<?php

session_start();

require_once "classes/Order.php";

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = "checkout.php";
    header("Location: login.php");
    exit();
}

if (!isset($_POST['checkout'])) {
    header("Location: checkout.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$address = trim($_POST['address'] ?? '');
$total_amount = (float) ($_POST['total_amount'] ?? 0);
$payment_method = $_POST['payment_method'] ?? '';

if ($address === '' || $total_amount <= 0) {
    $_SESSION['error'] = "Please check your delivery address and order total.";
    header("Location: checkout.php");
    exit();
}

if (!in_array($payment_method, ['Bank Transfer', 'Cash on Delivery'], true)) {
    $_SESSION['error'] = "Please select a valid payment method.";
    header("Location: checkout.php");
    exit();
}

$order = new Order();

// Create the order
$order_id = $order->createOrder(
    $user_id,
    $address,
    $total_amount,
    $payment_method
);

if (!$order_id) {
    $_SESSION['error'] = "Unable to place order.";
    header("Location: checkout.php");
    exit();
}

// Save the order products
if (!$order->saveOrderItems($order_id, $user_id)) {
    $_SESSION['error'] = "Unable to save order products.";
    header("Location: checkout.php");
    exit();
}

// BANK TRANSFER
if ($payment_method === "Bank Transfer") {
    // Keep the cart until the transfer workflow
    // has handled the order successfully.
    header("Location: bank_transfer.php?order_id=" . $order_id);
    exit();
}

// CASH ON DELIVERY
if ($payment_method === "Cash on Delivery") {

    $order->updateOrderStatus(
        $order_id,
        "pending",
        "Processing"
    );

    // Reduce stock
    if (!$order->reduceProductQuantity($order_id)) {
        $_SESSION['error'] = "Product quantity could not be updated.";
        header("Location: checkout.php");
        exit();
    }

    // Clear the logged-in user's database cart
    if (!$order->clearUserProducts($user_id)) {
        $_SESSION['error'] = "Products could not be cleared.";
        header("Location: checkout.php");
        exit();
    }

    // Clear the session cart after successful order processing
    unset($_SESSION['my_products']);

    header("Location: order_success.php?order_id=" . $order_id);
    exit();
}

?>