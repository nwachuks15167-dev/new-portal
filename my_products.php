
<?php
session_start();

require_once "classes/Product.php";

$product = new Product();

$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $isLoggedIn ? (int) $_SESSION['user_id'] : 0;

// Handle quantity and remove actions
if (isset($_GET['increase']) || isset($_GET['decrease']) || isset($_GET['remove'])) {

    $id = (int) (
        $_GET['increase'] ?? $_GET['decrease'] ?? $_GET['remove']
    );

    if ($id < 1) {
        $_SESSION['error'] = "Invalid product.";
        header("Location: my_products.php");
        exit();
    }

    // Guest cart: use the session
    if (!$isLoggedIn) {

        if (isset($_GET['remove'])) {
            unset($_SESSION['my_products'][$id]);
            $_SESSION['success'] = "Product removed.";
        } elseif (isset($_SESSION['my_products'][$id])) {

            $row = $product->getProductById($id);

            if (!$row) {
                $_SESSION['error'] = "Product not found.";
            } elseif (isset($_GET['increase'])) {

                $quantity = (int) $_SESSION['my_products'][$id];
                $stock = (int) $row['quantity'];

                if ($quantity < $stock) {
                    $_SESSION['my_products'][$id] = $quantity + 1;
                    $_SESSION['success'] = "Quantity increased.";
                } else {
                    $_SESSION['error'] = "Maximum stock reached.";
                }

            } else {
                $quantity = (int) $_SESSION['my_products'][$id];

                if ($quantity > 1) {
                    $_SESSION['my_products'][$id] = $quantity - 1;
                    $_SESSION['success'] = "Quantity decreased.";
                } else {
                    $_SESSION['error'] = "Quantity cannot go below 1.";
                }
            }
        }

    } else {

        // Logged-in cart: use the database
        if (isset($_GET['remove'])) {
            if ($product->deletemyproduct($user_id, $id)) {
                $_SESSION['success'] = "Product removed.";
            } else {
                $_SESSION['error'] = "Unable to remove product.";
            }

        } elseif (isset($_GET['increase'])) {
            if ($product->increaseQuantity($user_id, $id)) {
                $_SESSION['success'] = "Quantity increased.";
            } else {
                $_SESSION['error'] = "Maximum stock reached or product not found.";
            }

        } else {
            if ($product->decreaseQuantity($user_id, $id)) {
                $_SESSION['success'] = "Quantity decreased.";
            } else {
                $_SESSION['error'] = "Quantity cannot go below 1.";
            }
        }
    }

    header("Location: my_products.php");
    exit();
}

// Load the correct cart
$cart = [];

if ($isLoggedIn) {

    $result = $product->getProductsByUser($user_id);

    if ($result === false) {
        die("Unable to load your products.");
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $cart[] = $row;
    }

} else {

    $guest_products = $_SESSION['my_products'] ?? [];

    foreach ($guest_products as $product_id => $quantity) {

        $row = $product->getProductById((int) $product_id);

        if (!$row) {
            continue;
        }

        $row['product_id'] = (int) $product_id;
        $row['quantity'] = (int) $quantity;

        $cart[] = $row;
    }
}

$total = 0;
$productCount = count($cart);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Products</title>
    <link rel="stylesheet" href="includes/sidebar.css">
    <link rel="stylesheet" href="includes/my_product.css">
</head>

<body>

<?php if ($isLoggedIn) { ?>
    <?php include "includes/sidebar.php"; ?>
<?php } else { ?>
    <nav class="guest-nav">
        <a href="index.php">Home</a> 
        <a href="products.php">Products</a> 
        <a href="login.php">Login</a>
    </nav>
<?php } ?>

<div class="main-content <?php echo $isLoggedIn ? 'logged-in-view' : 'guest-view'; ?>" id="mainContent">

    <h1 style="text-align:center;">My Products</h1>

    <?php if (isset($_SESSION['success'])) { ?>
        <div class="success-message">
            <?php
            echo htmlspecialchars($_SESSION['success']);
            unset($_SESSION['success']);
            ?>
        </div>
    <?php } ?>

    <?php if (isset($_SESSION['error'])) { ?>
        <div class="error-message">
            <?php
            echo htmlspecialchars($_SESSION['error']);
            unset($_SESSION['error']);
            ?>
        </div>
    <?php } ?>

    <div class="products-list">

        <?php if ($productCount > 0) { ?>

            <?php foreach ($cart as $row) {

                $product_id = (int) $row['product_id'];
                $quantity = (int) $row['quantity'];
                $price = (float) $row['price'];

                $subtotal = $price * $quantity;
                $total += $subtotal;
            ?>

                <div class="product-card">

                    <img
                        src="uploads/<?php echo htmlspecialchars($row['image'] ?? ''); ?>"
                        alt="<?php echo htmlspecialchars($row['product_name']); ?>"
                    >

                    <h3>
                        <?php echo htmlspecialchars($row['product_name']); ?>
                    </h3>

                    <p class="price">
                        ₦<?php echo number_format($price, 2); ?>
                    </p>

                    <p>
                        <strong>Category:</strong>
                        <?php echo htmlspecialchars($row['category'] ?? ''); ?>
                    </p>

                    <div class="quantity-box">

                        <a
                            href="my_products.php?decrease=<?php echo $product_id; ?>"
                            class="qty-btn"
                        >−</a>

                        <span class="quantity">
                            <?php echo $quantity; ?>
                        </span>

                        <a
                            href="my_products.php?increase=<?php echo $product_id; ?>"
                            class="qty-btn"
                        >+</a>

                    </div>

                    <p>
                        <strong>Subtotal:</strong>
                        ₦<?php echo number_format($subtotal, 2); ?>
                    </p>

                    <a
                        href="my_products.php?remove=<?php echo $product_id; ?>"
                        class="delete"
                        onclick="return confirm('Remove this product?')"
                    >Remove Product</a>

                </div>

            <?php } ?>

        <?php } else { ?>

            <h2 class="empty">No products added yet.</h2>

        <?php } ?>

    </div>

    <?php if ($productCount > 0) { ?>

        <div class="checkout-box">

            <h2>Total: ₦<?php echo number_format($total, 2); ?></h2>

            <form action="checkout.php" method="POST">
                <button type="submit">Checkout All Products</button>
            </form>

        </div>

    <?php } ?>

</div>

<?php if ($isLoggedIn) { ?>
    <script src="includes/sidebar.js"></script>
<?php } ?>

</body>
</html>
