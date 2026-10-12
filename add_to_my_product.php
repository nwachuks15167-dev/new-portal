<?php

session_start();

$product_id = $_GET['id'] ?? null;

if ($product_id === null) {
    header("Location: index.php");
    exit();
}

// Guest user
if (!isset($_SESSION['user_id'])) {

    $_SESSION['guest_products'] ??= [];

    $_SESSION['guest_products'][] = (int)$product_id;

    header("Location: my_products.php");
    exit();
}

// Logged-in user
require_once "classes/Product.php";

$product = new Product();

$user_id = $_SESSION['user_id'];

$result = $product->addToMyProducts($user_id, $product_id);

if ($result) {
    $_SESSION['success'] = "Product added successfully.";
} else {
    $_SESSION['error'] = "Product could not be added.";
}

header("Location: my_products.php");
exit();

?>