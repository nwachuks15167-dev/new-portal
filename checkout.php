<?php

session_start();

require_once "classes/Product.php";

$product = new Product();

$user_id = $_SESSION['user_id'];

$products = $product->getProductsByUser($user_id);

$total = 0;

?>

<!DOCTYPE html>
<html>
<head>

<title>Checkout</title>
<link rel="stylesheet" href="includes/checkout.css">
</head>
<body>

<h2>Checkout</h2>
<table border="1" cellpadding="10">
<tr>
<th>Image</th>
<th>Product</th>
<th>Price</th>
<th>Quantity</th>
<th>Total</th>
</tr>

<?php

while($row = mysqli_fetch_assoc($products))
{
 $price = (float)$row['price'];
    $quantity = (int)$row['quantity'];
    $subtotal = $price * $quantity;
    $total += $subtotal;
?>

<tr>
<td>
<img src="uploads/<?php echo $row['image']; ?>">
</td>

<td>
<?php echo $row['product_name']; ?>
</td>

<td>
₦<?php echo number_format($row['price']); ?>
</td>

<td>
<?php echo $row['quantity']; ?>
</td>

<td>
₦<?php echo number_format($subtotal); ?>
</td>

</tr>

<?php
}
?>
</table>

<div class="grand-total">
<h2>
Grand Total
</h2>

<h1>
₦<?php echo number_format($total); ?>
</h1>

</div>

<form action="process_order.php" method="POST">
<input
type="hidden"
name="total_amount"
value="<?php echo $total; ?>">

<label>
Delivery Address
</label>

<textarea
name="address"
required
placeholder="Enter your house address"></textarea>

<label>
Payment Method
</label>

<select
name="payment_method"
required>
<option value="">
Choose Payment
</option>

<option value="Bank Transfer">
Bank Transfer
</option>

<option value="Cash on Delivery">
Cash on Delivery
</option>

</select>
 <button type="submit" name="checkout">
        Place Order
    </button>
</button>
</form>
</div>
</body>

</html>