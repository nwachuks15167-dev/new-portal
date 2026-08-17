<?php

session_start();

require_once "../classes/Order.php";

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}

if($_SESSION['role'] != "admin")
{
    header("Location: ../dashboard.php");
    exit();
}

$order = new Order();

$orders = $order->getAllOrders();

?>

<!DOCTYPE html>

<html>

<head>

<title>All Order History</title>

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}
body {
    font-family: Arial, sans-serif;
    background: #f4f6f8;
    color: #333;
    padding: 30px;
}
.container {
    width: 95%;
    max-width: 1200px;
    margin: auto;
}
.container h1 {
    text-align: center;
    margin-bottom: 30px;
    font-size: 30px;
    color: #222;
}
.order-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    border: 1px solid #e5e5e5;
}
.order-header {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #ddd;
    margin-bottom: 20px;
}
.order-header p,
.order-card > p {
    font-size: 15px;
    line-height: 1.6;
}
.order-header strong,
.order-card > p strong {
    color: #222;
}
.order-card > p {
    background: #f8f9fa;
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}
.products {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-top: 20px;
}
.product {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    transition: 0.3s ease;
}
.product:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
}
.product img {
    width: 100%;
    height: 220px;
    object-fit: contain;
    display: block;
    margin: 0 auto 15px;
    border-radius: 8px;
    background: #f7f7f7;
}
.product h3 {
    font-size: 18px;
    margin-bottom: 10px;
    color: #222;
}
.product p {
    font-size: 14px;
    margin: 7px 0;
    color: #555;
}
.product p:nth-of-type(1) {
    font-weight: bold;
    color: #16803c;
}
.product p:nth-of-type(3) {
    font-weight: bold;
    color: #222;
}
.status {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    background: #e8f5e9;
    color: #16803c;
    font-size: 13px;
    font-weight: bold;
}
.total {
    margin-top: 25px;
    padding: 18px;
    background: #f1f3f5;
    border-radius: 8px;
    text-align: right;
    font-size: 20px;
    font-weight: bold;
    color: #222;
}
.delete-btn {
    display: inline-block;
    margin-top: 18px;
    padding: 10px 18px;
    background: #dc3545;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: 0.3s;
}
.delete-btn:hover {
    background: #b02a37;
}
.back {
    display: block;
    width: fit-content;
    margin: 30px auto;
    padding: 12px 22px;
    background: #222;
    color: white;
    text-decoration: none;
    border-radius: 7px;
    transition: 0.3s;
}
.back:hover {
    background: #444;
}
.container > h3 {
    background: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
}
@media (max-width: 1000px) {
    .products {
        grid-template-columns: repeat(3, 1fr);
    }
}
@media (max-width: 750px) {
    .order-header {
        grid-template-columns: 1fr;
    }
    .products {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 500px) {
    body {
        padding: 15px;
    }
    .container {
        width: 100%;
    }
    .order-card {
        padding: 15px;
    }
    .products {
        grid-template-columns: 1fr;
    }
    .product img {
        height: 250px;
    }
    .total {
        text-align: center;
    }

}

</style>
</head>
<body>
<div class="container">
<h1>All Customer Orders</h1>

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
<strong>Customer:</strong>
<?php echo htmlspecialchars($row['username']); ?>
</p>

<p>
<strong>Email:</strong>
<?php echo htmlspecialchars($row['email']); ?>
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

<div class="products">
<?php
$order_items = $order->getOrderProducts($row['id']);
while($item = mysqli_fetch_assoc($order_items))
{
?>

<div class="product">

<img
src="../uploads/<?php echo $item['image']; ?>"
alt="Product">

<h3>
<?php echo htmlspecialchars($item['product_name']); ?>
</h3>

<p>
Price:
₦<?php echo number_format($item['price']); ?>
</p>

<p>
Quantity:
<?php echo $item['quantity']; ?>
</p>

<p>
Subtotal:
₦<?php
echo number_format(
    $item['price'] * $item['quantity']
);
?>
</p>

</div>
<?php
}
?>
</div>

<div class="total">
Order Total:
₦<?php echo number_format($row['total_amount']); ?>
</div>
<a
    href="delete_order.php?id=<?php echo $row['id']; ?>"
    onclick="return confirm('Delete this order?')"
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
No orders have been placed yet.
</h3>

<?php
}
?>

<a href="admin_dashboard.php" class="back">
Back to Admin Dashboard
</a>

</div>
</body>
</html>