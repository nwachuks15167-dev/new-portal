<?php
session_start();

require_once "../classes/Product.php";

$product = new Product();

$id = $_GET['id'];

$product->deleteProduct($id);

header("Location: manage_product.php");