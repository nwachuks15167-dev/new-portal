<?php

session_start();

require_once "../classes/admin.php";

$admin = new Admin();

$products = $admin->getAllProducts();

?>

<!DOCTYPE html>

<html>

<head>

<title>Manage Products</title>

<style>

body{
    font-family:Arial;
    background:#f4f4f4;
    margin:0;
    padding:30px;
}

.container{
    width:95%;
    margin:auto;
}

h2{
    text-align:center;
    margin-bottom:20px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
}

table th{
    background:#343a40;
    color:#fff;
    padding:15px;
}

table td{
    padding:12px;
    border-bottom:1px solid #ddd;
    text-align:center;
}

img{
    width:80px;
    height:80px;
    object-fit:cover;
}

.btn{
    padding:8px 15px;
    color:white;
    text-decoration:none;
    border-radius:5px;
    margin:2px;
    display:inline-block;
}

.edit{
    background:#007bff;
}

.delete{
    background:red;
}

.approve{
    background:green;
}

.reject{
    background:orange;
}

</style>

</head>

<body>

<div class="container">

<h2>Manage Products</h2>

<table>

<tr>

<th>ID</th>

<th>Image</th>

<th>Product</th>

<th>Price</th>

<th>Quantity</th>

<th>Category</th>

<th>Owner</th>

<th>Users</th>

<th>Action</th>

</tr>

<?php

while($row = mysqli_fetch_assoc($products))
{

?>

<tr>

<td>

<?php echo $row['id']; ?>

</td>

<td>

<img src="../uploads/<?php echo $row['image']; ?>">

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

<?php echo $row['category']; ?>

</td>

<td>

<?php

$owner = $admin->getProductOwner($row['user_id']);

if($owner)
{
    echo $owner['username'];
}
else
{
    echo "Unknown User";
}

?>

</td>

<td>

<?php

$users = $admin->getUsersWithProduct($row['id']);

if(mysqli_num_rows($users) > 0)
{

    while($user = mysqli_fetch_assoc($users))
    {

        echo $user['username']."<br>";

    }

}
else
{
    echo "No User";
}

?>

</td>


<td>

<a class="btn edit"
href="edit_product.php?id=<?php echo $row['id']; ?>">

Edit

</a>

<a class="btn delete"
href="delete_product.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Delete Product?')">

Delete

</a>

</td>

</tr>

<?php

}

?>

</table>

</div>

</body>

</html>