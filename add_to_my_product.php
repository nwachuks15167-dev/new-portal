<?php

session_start();

require_once "classes/Product.php";

$product = new Product();

$user_id = $_SESSION['user_id'];

$product_id = $_GET['id'];

$result = $product->addtomyproducts($user_id,$product_id);

if($result)
{
    $_SESSION['success'] = "Product added successfully.";
}
else
{
    $_SESSION['error'] = "Product could not be added.";
}
header("Location: my_products.php");

exit();

?>