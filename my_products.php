<?php

session_start();

//include "includes/sidebar.php";

require_once "classes/Product.php";

$product = new Product();

$user_id = $_SESSION['user_id'];

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

$result = $product->getProductsByUser($user_id);

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
    $total = 0;
$productCount = mysqli_num_rows($result);

if($productCount > 0)
    {
        while($row = mysqli_fetch_assoc($result))
        {

    ?>
        <div class="product-card">
            <img
            src="uploads/<?php echo $row['image']; ?>"
            alt="Product Image">
            <h3>
                <?php echo $row['product_name']; ?>
            </h3>
            <p class="price">
                ₦<?php echo number_format($row['price']); ?>
            </p>

            <p>
                <strong>Category:</strong>
                <?php echo $row['category']; ?>
            </p>

            <p class="description">
                <?php echo $row['description']; ?>
            </p>

            <div class="quantity-box">
                <a
                href="my_products.php?decrease=<?php echo $row['product_id']; ?>"
                class="qty-btn">

                −

                </a>

                <span class="quantity">
                    <?php echo $row['quantity']; ?>
                </span>

                <a
                href="my_products.php?increase=<?php echo $row['product_id']; ?>"
                class="qty-btn">

                +

                </a>
            </div>

            <div class="buttons">

                <a
                href="delete_my_product.php?id=<?php echo $row['product_id']; ?>"
                class="delete"
                onclick="return confirm('Remove this product?')">

                Remove Product

                </a>

            </div>
        </div>

    <?php
        }
    }
    else
    {
        echo "<h2 class='empty'>No products added yet.</h2>";
    }

    ?>

    </div>

    <?php if($productCount > 0){ ?>
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