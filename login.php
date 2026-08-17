<?php
session_start();

require_once "classes/User.php";

$user = new User();

$message = "";

if(isset($_POST['login']))
{
    $loggedInUser = $user->login($_POST['email'], $_POST['password']);

if($loggedInUser)
{
    if($loggedInUser['role'] == "admin")
    {
        header("Location: Admin/admin_dashboard.php");
    }
    else
    {
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
