<?php

session_start();

require_once "classes/order.php";
$order = new order();
$order_id = $_GET['order_id'];
$data = $order->getOrderById($order_id);

?>

<!DOCTYPE html>
<html>
<head>
<title>Bank Transfer</title>

<link rel="stylesheet" href="includes/bank_transfer.css">

</head>
<body>
<div class="container">
<div class="payment-card">

<h2>Bank Transfer</h2>

<div class="amount">
<p>Total Amount to Pay</p>
<h3>
₦<?php echo number_format($data['total_amount']); ?>
</h3>

</div>

<div class="bank-details">

<p><strong>Bank Name:</strong> Kuda Bank</p>

<p><strong>Account Name:</strong> Eloc Store</p>

<p><strong>Account Number:</strong> 2013610975</p>

</div>

<div class="note">
Please transfer the exact amount shown above to the bank account provided.
After making the transfer, click the button below to confirm your payment.
</div>

<form action="confirm_payment.php" method="POST">

<input
type="hidden"
name="order_id"
value="<?php echo $order_id; ?>">

<button type="submit">

I Have Made Payment

</button>

</form>

</div>

</div>

</body>