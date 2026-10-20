
<?php
session_start();

require_once "classes/Product.php";

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in user's ID
$user_id = (int) $_SESSION['user_id'];

// Get the product ID from the URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT) || (int) $_GET['id'] < 1) {
    $_SESSION['error'] = "Invalid product ID.";
    header("Location: my_products.php");
    exit();
}

$product_id = (int) $_GET['id'];

// Create the Product object
$product = new Product();

// Delete the product from this user's database cart
if ($product->deletemyproduct($user_id, $product_id)) {
    $_SESSION['success'] = "Product removed from My Products successfully.";
} else {
    $_SESSION['error'] = "Unable to remove product. Please try again.";
}

// Return to My Products
header("Location: my_products.php");
exit();
?>
