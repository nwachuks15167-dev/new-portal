<?php

session_start();

require_once "../classes/Admin.php";

$admin = new Admin();

if(isset($_GET['id']))
{
    $id = $_GET['id'];

    if($admin->restoreProduct($id))
    {
        $_SESSION['success'] = "Product restored successfully!";
    }
    else
    {
        $_SESSION['error'] = "Unable to restore product!";
    }

    header("Location: manage_product.php");
    exit();
}

?>