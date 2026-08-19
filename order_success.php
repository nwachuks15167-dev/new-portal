<?php

session_start();

?>

<!DOCTYPE html>
<html>
<head>
<title>Order Successful</title>

<link rel="stylesheet" href="includes/order_success.css">

</head>
<body>
<div class="success-container">
    <div class="success-card">
        <div class="success-icon">
            ✔
        </div>
        <h1>Order Successful</h1>

        <?php
        if(isset($_SESSION['success']))
        {
        ?>

        <div class="success-message">

        <?php
        echo $_SESSION['success'];
        unset($_SESSION['success']);
        ?>

    </div>

    <?php
    }

    ?>

        <p>
            Thank you for shopping with us.
            <br>
            Your order has been placed successfully.
        </p>

        <div class="buttons">
            <a href="order_history.php" class="btn orders">
                View My Orders
            </a> 

            <a href="dashboard.php" class="btn dashboard">
                Back to Dashboard
            </a>
        </div>

    </div>

</div>

</body>