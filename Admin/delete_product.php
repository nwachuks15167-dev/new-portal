
<?php

session_start();

require_once "../classes/Product.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$product = new Product();

$product_id = $_POST['id'] ?? 0;

if ($product->deleteProduct($product_id)) {

    $_SESSION['success'] = "Product deleted successfully.";

} else {

    $_SESSION['error'] = "This product has existing orders or could not be deleted.";

}

header("Location: manage_product.php");
exit();

?>
