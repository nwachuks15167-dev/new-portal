<?php
session_start();

require_once __DIR__ . "/classes/Product.php";

$product = new Product();

$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search != '') {
    $products = $product->searchProducts($search);
}
elseif ($category != '') {
    $products = $product->getProductsByCategory($category);
}
else {
    $products = $product->getallproduct();
};


?>

<!DOCTYPE html>
<html>
<head>
    <title>Products</title>

     <link rel="stylesheet" href="includes/sidebar.css">

    <link rel="stylesheet" href="includes/product.css">

</head>

<body>

    <?php include "includes/sidebar.php"; ?>
    
<div class="main-content" id="mainContent">

    <h2 style="text-align:center;">All Products</h2>

        <form action="products.php" method="GET" class="search-form">
            <input 
                type="text"
                name="search"
                placeholder="Search for a product..."
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
            >

            <button type="submit">
                Search
            </button>

        </form>

      <div class="products-list">

    <?php while ($row = mysqli_fetch_assoc($products)) { ?>

        <div class="card">

            <img
                src="uploads/<?php echo htmlspecialchars($row['image']); ?>"
                alt="<?php echo htmlspecialchars($row['product_name']); ?>"
            >

            <h3>
                <?php echo htmlspecialchars($row['product_name']); ?>
            </h3>

            <p class="price">
                ₦<?php echo number_format($row['price']); ?>
            </p>

            <!-- View Product -->
            <a
                href="product_details.php?id=<?php echo (int)$row['id']; ?>"
                class="view-btn">
                View Product
            </a>

            <!-- Add to My Products -->
            <form action="add_to_my_product.php" method="POST">

                <input
                    type="hidden"
                    name="product_id"
                    value="<?php echo (int)$row['id']; ?>"
                >

                <input type="hidden" name="quantity" value="1">

                <button type="submit" class="btn add-btn">
                    Add to My Products
                </button>

            </form>

        </div>

    <?php } ?>

</div>

</div>

<script src="includes/sidebar.js"></script>

</body>
</html>