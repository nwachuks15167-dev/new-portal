<?php

session_start();

require_once "classes/Product.php";

$product = new Product();

$product_id = (int) ($_POST['product_id'] ?? 0);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

// Validate input
if ($product_id < 1 || !$quantity || $quantity < 1) {
    $_SESSION['error'] = "Please select a valid product and quantity.";
    header("Location: products.php");
    exit();
}

// Get product details
$row = $product->getProductById($product_id);

if (!$row) {
    $_SESSION['error'] = "Product not found.";
    header("Location: products.php");
    exit();
}

$stock = (int) $row['quantity'];

// Check stock
if ($quantity > $stock) {
    $_SESSION['error'] = "Selected quantity exceeds available stock.";
    header("Location: products.php");
    exit();
}

// Guest visitor
if (!isset($_SESSION['user_id'])) {

    if (!isset($_SESSION['my_products'])) {
        $_SESSION['my_products'] = [];
    }

    $current = (int) ($_SESSION['my_products'][$product_id] ?? 0);

    if (($current + $quantity) > $stock) {
        $_SESSION['error'] = "Selected quantity exceeds available stock.";
        header("Location: products.php");
        exit();
    }

    $_SESSION['my_products'][$product_id] = $current + $quantity;

} else {

    // Logged-in user
    $user_id = (int) $_SESSION['user_id'];

    if (!$product->addQuantityToMyProducts($user_id, $product_id, $quantity)) {
        $_SESSION['error'] = "Unable to add the selected quantity.";
        header("Location: products.php");
        exit();
    }
}

$_SESSION['success'] = "Product added to My Products successfully.";

header("Location: my_products.php");
exit();
