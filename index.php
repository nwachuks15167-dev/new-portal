<?php
session_start();

require_once "classes/Product.php";

$product = new Product();

$user_id = $_SESSION['user_id'] ?? null;

$page = isset($_GET['page']) ? $_GET['page'] : 1;

if ($page >= 3 && !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$products_per_page = 8;

$start = ($page - 1) * $products_per_page;

$products = $product->getallproduct();

$total_products = mysqli_num_rows($products);

$total_pages = ceil($total_products / $products_per_page);

mysqli_data_seek($products, $start);
?>

<!DOCTYPE html>
<html>
<head>
    <title>MyProject</title>
    <link rel="stylesheet" href="includes/index.css">
</head>
<body>

<!-- ===========================
     NAVBAR
=========================== -->
<div class="navbar">
    <h1>ELOC STORES</h1>
    <div class="nav-links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</div>

<!-- ===========================
     HERO SECTION
=========================== -->
<div class="hero">
    <h2>Welcome to ELOC STORES</h2>
    <p>A simple and powerful platform to manage your account and products</p>

    <div class="hero-buttons">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php" class="btn-primary">Go to Dashboard</a>
        <?php else: ?>
            <a href="register.php" class="btn-primary">Get Started</a>
            <a href="login.php"    class="btn-secondary">Login</a>
            <a href="products.php" class="btn view-products">View Products</a>
        <?php endif; ?>
    </div>
</div>

 <div class="products-section" id="products">

    <h3>Our Products</h3>
    <p>Explore our available products</p>

    <div class="products-grid">

        <?php

        $count = 0;

        while ($row = mysqli_fetch_assoc($products)) {

            if ($count >= $products_per_page) {
                break;
            }

        ?>

            <div class="product-card">

                <div class="product-image">

                    <img
                        src="uploads/<?php echo $row['image']; ?>"
                        alt="<?php echo htmlspecialchars($row['product_name']); ?>"
                    >

                </div>

                <div class="product-info">

                    <h3>
                        <?php echo htmlspecialchars($row['product_name']); ?>
                    </h3>

                    <p class="price">
                        ₦<?php echo number_format($row['price']); ?>
                    </p>

                    <p class="category">
                        <?php echo htmlspecialchars($row['category']); ?>
                    </p>

                    <a
                        href="product_details.php?id=<?php echo $row['id']; ?>"
                        class="view-btn">
                        View Product
                    </a>

                    <?php if (isset($_SESSION['user_id'])): ?>

                        <a
                            href="add_to_my_product.php?id=<?php echo $row['id']; ?>"
                            class="add-btn">
                            Add Product
                        </a>

                    <?php endif; ?>

                </div>

            </div>

        <?php

            $count++;

        }

        ?>

    </div>


    <!-- PAGINATION -->

    <div class="pagination">

        <?php if ($page > 1): ?>

            <a href="index.php?page=<?php echo $page - 1; ?>#products">
                Previous
            </a>

        <?php endif; ?>


        <?php if ($page < $total_pages): ?>

            <a href="index.php?page=<?php echo $page + 1; ?>#products">
                Next Page
            </a>

        <?php endif; ?>

    </div>

</div>

<!-- ===========================
     FEATURES SECTION
=========================== -->
<div class="features">
    <h3>What We Offer</h3>
    <p>Everything you need in one place</p>



    <div class="features-grid">
        <div class="feature-card">
            <div class="icon">👤</div>
            <h4>User Accounts</h4>
            <p>Create your personal account with a profile picture and manage your details easily.</p>
        </div>

        <div class="feature-card">
            <div class="icon">🛒</div>
            <h4>Browse Products</h4>
            <p>Explore all available products and add your favourites to your personal page.</p>
        </div>

        <div class="feature-card">
            <div class="icon">📋</div>
            <h4>My Products</h4>
            <p>Keep track of all the products you have saved in one personal page.</p>
        </div>

        <div class="feature-card">
            <div class="icon">🔒</div>
            <h4>Secure Login</h4>
            <p>Your password is safely encrypted and your session is fully protected.</p>
        </div>
    </div>

</div>

<!-- ===========================
     CALL TO ACTION
=========================== -->
<div class="cta">
    <h3>Ready to get started?</h3>
    <p>Create your free account today and start exploring</p>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="dashboard.php">Go to Dashboard</a>
    <?php else: ?>
        <a href="register.php">Create Account</a>
    <?php endif; ?>
</div>

<!-- ===========================
     FOOTER
=========================== -->
<div class="footer">
    <p>&copy; <?= date('Y') ?> MyProject. All rights reserved.</p>
    <p><a href="login.php">Login</a> | <a href="register.php">Register</a></p>
</div>

</body>
</html>