<?php

session_start();

require_once "classes/Order.php";

if(!isset($_SESSION['user_id']))
{
    header("Location: login.php");
    exit();
}

$order = new Order();

$user_id = $_SESSION['user_id'];

$orders = $order->getUserOrders($user_id);

$total = 0;

?>

<!DOCTYPE html>

<html>

<head>

<title>My Order History</title>

 <link rel="stylesheet" href="includes/sidebar.css">

    <link rel="stylesheet" href="includes/order_history.css">

</head>

<body>
    <?php include "includes/sidebar.php"; ?>

<div class="main-content" id="mainContent">

<h1>My Order History</h1>

<?php
if(mysqli_num_rows($orders) > 0)
{
while($row = mysqli_fetch_assoc($orders))
{

?>

<div class="order-card">

<div class="order-header">

<p>
<strong>Order ID:</strong>
#<?php echo $row['id']; ?>
</p>

<p>
<strong>Date:</strong>
<?php echo $row['created_at']; ?>
</p>

<p>
<strong>Payment:</strong>
<?php echo $row['payment_method']; ?>
</p>

<p>
<strong>Payment Status:</strong>
<span class="status">
<?php echo $row['payment_status']; ?>
</span>
</p>

<p>
<strong>Order Status:</strong>
<span class="status">
<?php echo $row['order_status']; ?>
</span>
</p>

</div>

<p>
<strong>Delivery Address:</strong>
<?php echo htmlspecialchars($row['address']); ?>
</p>

<div class="order-products">
<?php
$items = $order->getOrderProducts($row['id']);
while($item = mysqli_fetch_assoc($items))
{
    $subtotal =
        $item['price'] * $item['quantity'];
        $total += $subtotal;
?>

<div class="product-item">

    <img
        src="uploads/<?php echo $item['image']; ?>"
        class="product-image"
        alt="<?php echo $item['product_name']; ?>"
    >

    <div class="product-details">

        <h3>
            <?php echo $item['product_name']; ?>
        </h3>

        <p class="product-price">
            ₦<?php echo number_format($item['price']); ?>
        </p>

        <p class="product-info">
            Quantity:
            <?php echo $item['quantity']; ?>
        </p>

        <p class="subtotal">
            Subtotal:
            ₦<?php echo number_format($subtotal); ?>
        </p>

    </div>

</div>

<?php

}

?>

</div>
<a
    href="delete_order.php?id=<?php echo $row['id']; ?>"
    onclick="return confirm('Delete this order history?')"
    class="delete-btn"
>
    Delete
</a>

</div>

<?php
}
}

else

{

?>

<h3 style="text-align:center;">
You have no order history yet.
</h3>

<?php
}
?>

</div>

<script src="includes/sidebar.js"></script>

</body>

</html>