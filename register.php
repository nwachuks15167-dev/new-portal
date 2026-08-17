<?php
session_start();
require_once "classes/User.php";

$user  = new User(); // auto connects to database.php


if (isset($_POST['register'])) {
    $result = $user->register(
        $_POST['username'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['age'],
        $_POST['password'],
        $_FILES['profile_pic']
    );

    if ($result === true) {
        header("Location: login.php?registered=1");
        exit();
    } else {
        $error = $result;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>

    <link rel="stylesheet" href="includes/register.css">
    
</head>
<body>
<div class="card">
    <h2>Create Account</h2>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="Enter username" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="Enter email" required>
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" placeholder="Enter phone" required>
        </div>
        <div class="form-group">
            <label>Age</label>
            <input type="number" name="age" placeholder="Enter age" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password" required>
        </div>
        <div class="form-group">
            <label>Profile Picture</label>
            <input type="file" name="profile_pic" accept="image/*" required>
        </div>
        <button type="submit" name="register" class="btn">Register</button>
    </form>

    <div class="link">
        Already have an account? <a href="login.php">Login here</a>
    </div>
</div>
</body>
</html>