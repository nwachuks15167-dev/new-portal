<?php

require_once "classes/Product.php";

$product = new Product();

$id = $_GET['id'];

$productData = $product->getProductById($id);

if(!$productData)
{
    echo "Product not found";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($productData['product_name']); ?></title>

    <link rel="stylesheet" href="includes/product_details.css">
</head>

<body>

<div class="product-card">

    <img 
        src="uploads/<?php echo $productData['image']; ?>" 
        alt="<?php echo htmlspecialchars($productData['product_name']); ?>" 
    >

    <h3>
        <?php echo htmlspecialchars($productData['product_name']); ?>
    </h3>

    <p class="price">
        ₦<?php echo number_format($productData['price']); ?>
    </p>

    <p class="meta">
        Quantity:
        <?php echo $productData['quantity']; ?>
    </p>

    <p class="meta">
        Category:
        <?php echo htmlspecialchars($productData['category']); ?>
    </p>

    <p class="meta">
        Description:
        <?php echo htmlspecialchars($productData['description']); ?>
    </p>

    <p class="meta">
        Created:
        <?php echo $productData['created_at']; ?>
    </p>


<?php
$product_id = (int) $productData['id'];
$stock = (int) $productData['quantity'];
?>


<form action="add_to_my_product.php" method="POST">

    <input
        type="hidden"
        name="product_id"
        value="<?php echo (int) $productData['id']; ?>"
    >

    <label for="quantity">Quantity:</label>

    <input
        type="number"
        id="quantity"
        name="quantity"
        value="1"
        min="1"
        max="<?php echo (int) $productData['quantity']; ?>"
        required
    >

    <button type="submit" class="btn add-btn">
        Add to My Products
    </button>

</form>

    <br>

    <a href="products.php" class="btn">
        Back to Products
    </a>

</div>

</body>
</html>
