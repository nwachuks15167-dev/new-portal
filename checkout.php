
<?php
session_start();

require_once "classes/Product.php";

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = "checkout.php";
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

$product = new Product();
$result = $product->getProductsByUser($user_id);

$cart = [];
$total = 0;

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $quantity = (int) $row['quantity'];
        $price = (float) $row['price'];

        $row['cart_quantity'] = $quantity;
        $row['subtotal'] = $price * $quantity;

        $cart[] = $row;
        $total += $row['subtotal'];
    }
}

if (empty($cart)) {
    header("Location: my_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Checkout | ELOC STORES</title>

    <link rel="stylesheet" href="includes/checkout.css">
</head>

<body>

<div class="checkout-container">

    <header class="checkout-header">
        <div>
            <p class="brand-name">ELOC STORES</p>
            <h1>Checkout</h1>
            <p class="subtitle">Review your order and complete your purchase.</p>
        </div>

        <a href="dashboard.php" class="back-btn">
             Dashboard
        </a>
    </header>

    <div class="checkout-layout">

        <section class="order-section">
            <h2>Your Order</h2>

            <p class="section-description">
                Review your selected products before placing your order.
            </p>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($cart as $item) { ?>

                        <tr>
                            <td>
                                <div class="product-info">
                                    <img
                                        src="uploads/<?php echo htmlspecialchars($item['image']); ?>"
                                        alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                        class="product-image"
                                    >

                                    <span>
                                        <?php echo htmlspecialchars($item['product_name']); ?>
                                    </span>
                                </div>
                            </td>

                            <td>
                                ₦<?php echo number_format($item['price'], 2); ?>
                            </td>

                            <td>
                                <?php echo $item['cart_quantity']; ?>
                            </td>

                            <td class="subtotal">
                                ₦<?php echo number_format($item['subtotal'], 2); ?>
                            </td>
                        </tr>

                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="summary-section">

            <h2>Order Summary</h2>

            <div class="summary-row">
                <span>Products</span>
                <span><?php echo count($cart); ?> item(s)</span>
            </div>

            <div class="summary-row">
                <span>Delivery</span>
                <span>To be arranged</span>
            </div>

            <div class="total-row">
                <span>Grand Total</span>
                <strong>
                    ₦<?php echo number_format($total, 2); ?>
                </strong>
            </div>

            <form action="process_order.php" method="POST">

                <input
                    type="hidden"
                    name="total_amount"
                    value="<?php echo $total; ?>"
                >

                <div class="form-group">
                    <label for="address">Delivery Address</label>

                    <textarea
                        id="address"
                        name="address"
                        rows="4"
                        required
                        placeholder="Enter your complete delivery address"
                    ></textarea>
                </div>

                <div class="form-group">
                    <label for="payment_method">Payment Method</label>

                    <select
                        id="payment_method"
                        name="payment_method"
                        required
                    >
                        <option value="">Choose payment method</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cash on Delivery">Cash on Delivery</option>
                    </select>
                </div>

                <button type="submit" name="checkout" class="place-order-btn">
                    Place Order
                </button>

                <p class="secure-note">
                    Please confirm your delivery details before ordering.
                </p>

            </form>

        </aside>

    </div>

    <footer class="checkout-footer">
        &copy; <?php echo date('Y'); ?> ELOC STORES. All rights reserved.
    </footer>

</div>

</body>
</html>
