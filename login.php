<?php
session_start();

require_once "classes/User.php";
require_once "classes/Product.php";

$user = new User();
$product = new Product();

$message = "";

if(isset($_POST['login']))
{
    $loggedInUser = $user->login($_POST['email'], $_POST['password']);

if ($loggedInUser)
{
    // Set the logged-in user's session
    $_SESSION['user_id'] = (int) $loggedInUser['id'];
    $_SESSION['username'] = $loggedInUser['username'];
    $_SESSION['role'] = $loggedInUser['role'];

    // Transfer guest cart for regular users
    if (
        $loggedInUser['role'] !== "admin"
        && !empty($_SESSION['my_products'])
    ) {
        $guestCart = $_SESSION['my_products'];

        if ($product->mergeSessionCart(
            $_SESSION['user_id'],
            $guestCart
        )) {
            // Remove guest cart only after successful transfer
            unset($_SESSION['my_products']);
        } else {
            // Keep the guest cart if transfer fails
            $_SESSION['error'] =
                "Some cart products could not be transferred. Please check stock and try again.";

            header("Location: dashboard.php");
            exit();
        }
    }

    // Return to the page the user originally requested
    if (isset($_SESSION['redirect_after_login'])) {
        $redirect = $_SESSION['redirect_after_login'];
        unset($_SESSION['redirect_after_login']);

        header("Location: " . $redirect);
        exit();
    }

    // Normal login redirects
    if ($loggedInUser['role'] === "admin") {
        header("Location: Admin/admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }

    exit();
}
else
{
    $message = "Invalid Email or Password";
}
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
   <link rel="stylesheet" href="includes/login.css">
</head>
<body>
<div class="card">
    <h2>Welcome Back</h2>

    <?php if (isset($_GET['registered'])): ?>
        <div class="success">Registration successful! Please login.</div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="error"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="Enter email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password" required>
        </div>
        <button type="submit" name="login" class="btn">Login</button>
    </form>

    <div class="link">
        Don't have an account? <a href="register.php">Register here</a>
    </div>
</div>
</body>
</html>
