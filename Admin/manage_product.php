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

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    color: #333;
    padding: 30px;
}

.container {
    width: 100%;
    max-width: 1400px;
    margin: auto;
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
}

h2 {
    font-size: 26px;
    color: #222;
    margin-bottom: 25px;
    border-left: 5px solid #2563eb;
    padding-left: 12px;
}

/* Table container */
.container {
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 1050px;
    border-collapse: collapse;
    text-align: left;
}

th {
    background: #1e293b;
    color: white;
    padding: 15px 12px;
    font-size: 14px;
    font-weight: 600;
    white-space: nowrap;
}

td {
    padding: 13px 12px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
    vertical-align: middle;
}

tr:hover td {
    background: #f8fafc;
}

td img {
    width: 55px;
    height: 55px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #eee;
}

/* Status labels */
.status {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.available {
    background: #dcfce7;
    color: #15803d;
}

.deleted {
    background: #fee2e2;
    color: #dc2626;
}

/* Action buttons */
.btn {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    font-weight: bold;
    margin: 3px;
    transition: 0.2s;
}

.edit {
    background: #dbeafe;
    color: #1d4ed8;
}

.edit:hover {
    background: #2563eb;
    color: white;
}

.delete {
    background: #fee2e2;
    color: #dc2626;
}

.delete:hover {
    background: #dc2626;
    color: white;
}

.restore {
    background: #dcfce7;
    color: #15803d;
}

.restore:hover {
    background: #16a34a;
    color: white;
}

/* Mobile */
@media (max-width: 768px) {
    body {
        padding: 12px;
    }

    .container {
        padding: 15px;
    }

    h2 {
        font-size: 22px;
    }
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
<th>Status</th>
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
    <?php if($row['is_deleted'] == 1) { ?>

        <span class="status deleted">Deleted</span>

    <?php } else { ?>

        <span class="status available">Available</span>

    <?php } ?>
</td>

<td>
    <a class="btn edit"
       href="edit_product.php?id=<?php echo $row['id']; ?>">
       Edit
    </a>

    <?php if($row['is_deleted'] == 1) { ?>

        <a class="btn restore"
           href="restore_product.php?id=<?php echo $row['id']; ?>">
           Restore
        </a>

    <?php } else { ?>

        <a class="btn delete"
           href="delete_product.php?id=<?php echo $row['id']; ?>">
           Delete
        </a>

    <?php } ?>
</td>

</tr>

<?php

}

?>

</table>

</div>

</body>

</html>