<?php

session_start();

require_once "../classes/admin.php";

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit;
}

$admin = new Admin();

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')
{
    header("Location: ../dashboard.php");
    exit;
}

$totalproducts = $admin->getTotalProducts();
$totalusers = $admin->getTotalUsers();

?>


<!DOCTYPE html>
<html>
<head>

<title>Admin Dashboard</title>

<style>

body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#f4f6f9;
}

/* Sidebar */

.sidebar{
    width:250px;
    height:100vh;
    background:#2c3e50;
    position:fixed;
}

.sidebar h2{
    color:white;
    text-align:center;
    padding:20px;
}

.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    padding:15px 20px;
}

.sidebar a:hover{
    background:#34495e;
}

/* Main */

.main{
    margin-left:250px;
    padding:20px;
}

.cards{
    display:flex;
    gap:20px;
    flex-wrap:wrap;
}

.card{
    flex:1;
    min-width:250px;
    background:white;
    padding:20px;
    border-radius:10px;
    text-align:center;
    box-shadow:0 0 10px rgba(0,0,0,.1);
}

.card h1{
    color:#3498db;
}

/* Tables */

.table-box{
    background:white;
    padding:20px;
    margin-top:20px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,.1);
}

table{
    width:100%;
    border-collapse:collapse;
}

table th,
table td{
    border:1px solid #ddd;
    padding:10px;
}

table th{
    background:#3498db;
    color:white;
}

</style>

</head>

<body>

<div class="sidebar">

<h2>Admin Panel</h2>

<a href="admin_dashboard.php">
Dashboard
</a>

<a href="manage_user.php">
Manage Users
</a>

<a href="manage_product.php">
Manage Products
</a>

<a href="order_history.php">
Order History   
</a>

<a href="add_product.php">
Add Product
</a>

<a href="../logout.php">
Logout
</a>

</div>

<div class="main">

<h1>
Welcome Admin
</h1>

<div class="cards">

<div class="card">

<h3>Total Users</h3>

<h1>
<?php echo $totalusers; ?>
</h1>

</div>

<div class="card">

<h3>Total Products</h3>

<h1>
<?php echo $totalproducts; ?>
</h1>

</div>

</div>