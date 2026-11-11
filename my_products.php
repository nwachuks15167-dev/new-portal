
<?php
session_start();

require_once "classes/Product.php";

$product = new Product();

$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $isLoggedIn ? (int) $_SESSION['user_id'] : 0;

// Handle AJAX quantity updates
if (isset($_POST['ajax'])) {

    header("Content-Type: application/json");

    $id = (int) ($_POST['product_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($id < 1 || !in_array($action, ['increase', 'decrease'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request.'
        ]);
        exit();
    }

    // Guest cart
    if (!$isLoggedIn) {

        if (!isset($_SESSION['my_products'][$id])) {
            echo json_encode([
                'success' => false,
                'message' => 'Product not found in cart.'
            ]);
            exit();
        }

        $row = $product->getProductById($id);

        if (!$row) {
            echo json_encode([
                'success' => false,
                'message' => 'Product not found.'
            ]);
            exit();
        }

        $quantity = (int) $_SESSION['my_products'][$id];
        $stock = (int) $row['quantity'];

        if ($action === 'increase') {

            if ($quantity >= $stock) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Maximum stock reached.'
                ]);
                exit();
            }

            $quantity++;

        } else {

            if ($quantity <= 1) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Quantity cannot go below 1.'
                ]);
                exit();
            }

            $quantity--;
        }

        $_SESSION['my_products'][$id] = $quantity;

    } else {

        // Logged-in cart
        if ($action === 'increase') {
            $updated = $product->increaseQuantity($user_id, $id);
        } else {
            $updated = $product->decreaseQuantity($user_id, $id);
        }

        if (!$updated) {
            echo json_encode([
                'success' => false,
                'message' => 'Unable to update quantity. Check available stock.'
            ]);
            exit();
        }

        // Get the updated quantity
        $result = $product->getProductsByUser($user_id);

        $quantity = 0;

        while ($row = mysqli_fetch_assoc($result)) {

            if ((int) $row['product_id'] === $id) {
                $quantity = (int) $row['quantity'];
                break;
            }
        }
    }

    echo json_encode([
        'success' => true,
        'quantity' => $quantity
    ]);

    exit();
}

// Handle remove action
if (isset($_GET['remove'])) {

    $id = (int) $_GET['remove'];

    if ($id < 1) {
        $_SESSION['error'] = "Invalid product.";
    }

    // Guest cart
    elseif (!$isLoggedIn) {

        unset($_SESSION['my_products'][$id]);
        $_SESSION['success'] = "Product removed.";

    } else {

        // Logged-in cart
        if ($product->deletemyproduct($user_id, $id)) {
            $_SESSION['success'] = "Product removed.";
        } else {
            $_SESSION['error'] = "Unable to remove product.";
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

                <div class="product-card"
                    data-id="<?php echo $product_id; ?>"
                    data-price="<?php echo $price; ?>">

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

                        <button type="button"
                                class="qty-btn update-btn"
                                data-action="decrease"
                                data-id="<?php echo $product_id; ?>">
                            −
                        </button>

                        <span class="quantity" id="quantity-<?php echo $product_id; ?>">
                            <?php echo $quantity; ?>
                        </span>

                        <button type="button"
                                class="qty-btn update-btn"
                                data-action="increase"
                                data-id="<?php echo $product_id; ?>">
                            +
                        </button>

                    </div>

                    <p>
                        <strong>Subtotal:</strong>
                        ₦<span id="subtotal-<?php echo $product_id; ?>">
                            <?php echo number_format($subtotal, 2); ?>
                        </span>
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

            <h2>
                Total: ₦<span id="cartTotal">
                    <?php echo number_format($total, 2); ?>
                </span>
            </h2>

            <form action="checkout.php" method="POST">
                <button type="submit">Checkout All Products</button>
            </form>

        </div>

    <?php } ?>

</div>

<?php if ($isLoggedIn) { ?>
    <script src="includes/sidebar.js"></script>
<?php } ?>


<script>
document.addEventListener("click", function(e) {

    const button = e.target.closest(".update-btn");

    if (!button) {
        return;
    }

    const action = button.dataset.action;
    const productId = button.dataset.id;

    button.disabled = true;

    const formData = new FormData();

    formData.append("ajax", "1");
    formData.append("action", action);
    formData.append("product_id", productId);

    fetch("my_products.php", {
        method: "POST",
        body: formData
    })

    .then(response => response.json())

    .then(data => {

        if (!data.success) {
            alert(data.message);
            return;
        }

        // Update the quantity
        document.getElementById("quantity-" + productId).textContent =
            data.quantity;

        // Get the product card and price
        const card = document.querySelector(
            '.product-card[data-id="' + productId + '"]'
        );

        const price = Number(card.dataset.price);

        // Calculate the new subtotal
        const subtotal = price * data.quantity;

        document.getElementById("subtotal-" + productId).textContent =
            subtotal.toLocaleString("en-NG", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        // Calculate the new cart total
        let total = 0;

        document.querySelectorAll(".product-card").forEach(card => {

            const productPrice = Number(card.dataset.price);

            const quantity = Number(
                card.querySelector(".quantity").textContent
            );

            total += productPrice * quantity;

        });

        document.getElementById("cartTotal").textContent =
            total.toLocaleString("en-NG", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

    })

    .catch(error => {
        alert("Something went wrong. Please try again.");
    })

    .finally(() => {
        button.disabled = false;
    });

});
</script>

</body>
</html>
