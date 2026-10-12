<?php

session_start();

require_once "classes/Product.php";

$product = new Product();

$user_id = $_SESSION['user_id'] ?? null;

if(isset($_GET['increase']))
{
     $product_id = $_GET['increase'];

    if($product->increaseQuantity($_SESSION['user_id'], $product_id))
    {
        $_SESSION['success'] = "Quantity updated successfully.";
    }
    else
    {
        $_SESSION['error'] = "Maximum stock reached.";
    }
    header("Location: my_products.php");
    exit();
}

if(isset($_GET['decrease']))
{
    $product_id = $_GET['decrease'];
    $product->decreaseQuantity($_SESSION['user_id'], $product_id);
    header("Location: my_products.php");
    exit();
}

$guest_products = $_SESSION['guest_products'] ?? [];

$result = null;

// Logged-in user
if ($user_id !== null) {
    $result = $product->getProductsByUser($user_id);
}

?>

<!DOCTYPE html>
<html>
<head>
    
    <title>My Products</title>
    <link rel="stylesheet" href="includes/sidebar.css">
    <link rel="stylesheet" href="includes/my_product.css">

</head>

<body>

     <?php include "includes/sidebar.php"; ?>

<div class="main-content" id="mainContent">
    <h1 style="text-align:center;">My Products</h1>
    <?php if(isset($_SESSION['success'])){ ?>
        <div class="success-message">
            <?php
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
    <?php } ?>

    <?php if(isset($_SESSION['error'])){ ?>
        <div class="error-message">
            <?php
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>

    <?php } ?>
    <div class="products-list">
<?php

if ($user_id !== null) {

    // Logged-in user's products
    $productCount = mysqli_num_rows($result);

    if ($productCount > 0) {

        while ($row = mysqli_fetch_assoc($result)) {

            $product_id = $row['product_id'];

            ?>

            <div class="product-card">

                <img
                    src="uploads/<?php echo htmlspecialchars($row['image']); ?>"
                    alt="Product Image"
                >

                <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>

                <p class="price">
                    ₦<?php echo number_format($row['price']); ?>
                </p>

                <p>
                    <strong>Category:</strong>
                    <?php echo htmlspecialchars($row['category']); ?>
                </p>

                <p class="description">
                    <?php echo htmlspecialchars($row['description']); ?>
                </p>

                <div class="quantity-box">
                    <a
                        href="my_products.php?decrease=<?php echo $product_id; ?>"
                        class="qty-btn">−</a>

                    <span class="quantity">
                        <?php echo $row['quantity']; ?>
                    </span>

                    <a
                        href="my_products.php?increase=<?php echo $product_id; ?>"
                        class="qty-btn">+</a>
                </div>

                <div class="buttons">
                    <a
                        href="delete_my_product.php?id=<?php echo $product_id; ?>"
                        class="delete"
                        onclick="return confirm('Remove this product?')">
                        Remove Product
                    </a>
                </div>

            </div>

            <?php
        }

    } else {
        echo "<h2 class='empty'>No products added yet.</h2>";
    }

} else {

    // Guest user's products
    $productCount = count($guest_products);

    if ($productCount > 0) {

        foreach ($guest_products as $product_id) {

            $row = $product->getProductById($product_id);

            if (!$row) {
                continue;
            }

            ?>

            <div class="product-card">

                <img
                    src="uploads/<?php echo htmlspecialchars($row['image']); ?>"
                    alt="Product Image"
                >

                <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>

                <p class="price">
                    ₦<?php echo number_format($row['price']); ?>
                </p>

                <p>
                    <strong>Category:</strong>
                    <?php echo htmlspecialchars($row['category']); ?>
                </p>

                <p class="description">
                    <?php echo htmlspecialchars($row['description']); ?>
                </p>

                <div class="buttons">
                    <a
                        href="delete_my_product.php?id=<?php echo $product_id; ?>"
                        class="delete"
                        onclick="return confirm('Remove this product?')">
                        Remove Product
                    </a>
                </div>

            </div>

            <?php
        }

    } else {
        echo "<h2 class='empty'>No products added yet.</h2>";
    }
}

?>

    </div>

    <?php if ($user_id !== null && $productCount > 0) { ?>
    <div class="checkout-box">
        <form action="checkout.php" method="POST">
            <button type="submit">
                Checkout All Products
            </button>
        </form>
    </div>

    <?php } ?>

</div>
<script src="includes/sidebar.js"></script>
</body>
</html>