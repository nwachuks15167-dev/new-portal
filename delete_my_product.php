<?php

session_start();

require_once "classes/product.php";

if (!isset($_SESSION['user_id']))
{
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']))
    {
        die("No product selected.");
    }

$user_id = $_SESSION['user_id'];
$product_id = $_GET['id'];

$product = new product();

if ($product->deletemyproduct($user_id, $product_id))
    {
        header("location: my_products.php");
        exit();
    }
    else
        {
            echo "Failed to remove product.";
        }

?>