<?php
session_start();

if(!isset($_SESSION['user_id']))
{
    header("Location: login.php");
    exit;
}

require_once "classes/Order.php";
require_once "classes/Product.php";
require_once "classes/User.php";

$user_id = $_SESSION['user_id'];

$user = new User();
$product = new Product();
$Order = new Order();

$totalorders = $Order->getTotalOrders($user_id);

$userData = $user->getUserById($_SESSION['user_id']);

$totalProducts = $product->countProducts();

$myProducts = $product->countUserProducts($_SESSION['user_id']);

$recentProducts = $product->getLatestProducts();
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard</title>

<link rel="stylesheet" href="includes/sidebar.css">
<link rel="stylesheet" href="includes/dashboard.css">

</head>

<body>
<?php include "includes/sidebar.php"; ?>

<div class="main-content" id="mainContent">

    <h1 class="dashboard-title">Dashboard</h1>


    <!-- SUMMARY CARDS -->

    <div class="stats">

        <div class="card">
            <div class="card-icon">🛍️</div>
            <h3>Total Products</h3>
            <h1><?php echo $totalProducts['total']; ?></h1>
            <p>Products available</p>
        </div>

        <div class="card">
            <div class="card-icon">📦</div>
            <h3>My Products</h3>
            <h1><?php echo $myProducts['total']; ?></h1>
            <p>Products in my list</p>
        </div>

        <div class="card">
            <div class="card-icon">🧾</div>
            <h3>Total Orders</h3>
            <h1>
                <?php echo $totalorders['total_orders'] ?? 0; ?>
            </h1>
            <p>Orders placed</p>
        </div>

    </div>


    <!-- CATEGORY -->

    <div class="category-container">

        <h2>Browse Products by Category</h2>

        <div class="category-cards">

            <a href="products.php?category=Electronics"
               class="category-card">

                <h3>Electronics</h3>
                <p>View Electronics</p>

            </a>

            <a href="products.php?category=Furniture"
               class="category-card">

                <h3>Furniture</h3>
                <p>View Furniture</p>

            </a>

            <a href="products.php?category=device"
               class="category-card">

                <h3>Device</h3>
                <p>View Devices</p>

            </a>

        </div>

    </div>


    <!-- RECENT PRODUCTS -->

    <div class="products">

        <h2>Recent Products</h2>

        <div class="table-container">

            <table>

                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Category</th>
                    </tr>
                </thead>

                <tbody>

                    <?php while($row = mysqli_fetch_assoc($recentProducts)) { ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($row['product_name']); ?>
                        </td>

                        <td>
                            ₦<?php echo number_format($row['price']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['category']); ?>
                        </td>

                    </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script src="includes/sidebar.js"></script>

</body>
</html>

