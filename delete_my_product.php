<?php

session_start();

$product_id = $_GET['id'] ?? null;

if (!isset($_SESSION['user_id'])) {

    if (isset($_SESSION['guest_products'])) {

        foreach ($_SESSION['guest_products'] as $key => $id) {

            if ($id == $product_id) {

                unset($_SESSION['guest_products'][$key]);

                break;
            }
        }

        $_SESSION['guest_products'] = array_values($_SESSION['guest_products']);
    }

    header("Location: my_products.php");
    exit();
}

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