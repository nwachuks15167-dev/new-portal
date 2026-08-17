<?php

session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin")
{
    header("Location: ../login.php");
    exit();
}

require_once "../classes/admin.php";

$admin = new Admin();

$users = $admin->getAllUsers();

?>

<!DOCTYPE html>

<html>

<head>

<title>Manage Users</title>

<style>

body{
    font-family:Arial;
    background:#f4f6f9;
}

.container{
    width:95%;
    margin:auto;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
}

table th,
table td{
    border:1px solid #ddd;
    padding:12px;
    text-align:center;
}

table th{
    background:#3498db;
    color:white;
}

.btn{
    padding:8px 12px;
    color:white;
    text-decoration:none;
    border-radius:5px;
}

.edit{
    background:green;
}

.delete{
    background:red;
}

</style>

</head>

<body>

<div class="container">

<h2>Manage Users</h2>

<table>

<tr>

<th>ID</th>
<th>Username</th>
<th>Email</th>
<th>Phone</th>
<th>Age</th>
<th>Role</th>
<th>Action</th>

</tr>

<?php while($row = mysqli_fetch_assoc($users)) { ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['username']; ?></td>

<td><?php echo $row['email']; ?></td>

<td><?php echo $row['phone']; ?></td>

<td><?php echo $row['age']; ?></td>

<td><?php echo $row['role']; ?></td>

<td>

<a class="btn edit"
href="edit_user.php?id=<?php echo $row['id']; ?>">

Edit

</a>

<a class="btn delete"
href="delete_user.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Delete this user?')">

Delete

</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</body>

</html>