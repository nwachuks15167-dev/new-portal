<?php
session_start();

require_once __DIR__ . "/classes/Product.php";

$product  = new Product();
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search != '') {
    $product = $product->searchProducts($search);
}
elseif ($category != '') {
    $product = $product->getProductsByCategory($category);
}
else {
    $product = $product->getallproduct();
}


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

</div>

        <div class="products-list">

         <?php
        $total = 0;
        $productCount = mysqli_num_rows($product);

        if($productCount == 0) { ?>

            <div class="no-result">
                <p>No result found.</p>
            </div>

        <?php
        }
        else
        {
        ?>

    <?php
    while($row = mysqli_fetch_assoc($product)) { ?>
        <div class="card">
            <img
                src="uploads/<?php echo $row['image']; ?>"
                alt="<?php echo htmlspecialchars($row['product_name']); ?>"
            >

            <h3>
                <?php echo htmlspecialchars($row['product_name']); ?>
            </h3>

            <p class="price">
                ₦<?php echo number_format($row['price']); ?>
            </p>

            <p class="meta">
                Quantity:
                <?php echo $row['quantity']; ?>
            </p>

            <p class="meta">
                Category:
                <?php echo htmlspecialchars($row['category']); ?>
            </p>

            <p class="meta">
                Description:
                <?php echo htmlspecialchars($row['description']); ?>
            </p>

            <p class="meta">
                Created:
                <?php echo $row['created_at']; ?>
            </p>

            <div class="buttons">

                <a
                    class="btn add"
                    href="add_to_my_product.php?id=<?php echo $row['id']; ?>">
                    Add Product
                </a>

            </div>

        </div>

    <?php } ?>

    <?php } ?>

</div>

</div>

<script src="includes/sidebar.js"></script>

</body>
</html>