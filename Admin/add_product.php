<?php

session_start();

require_once "../classes/Product.php";

$product = new Product();

if(isset($_POST['submit']))
{
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $category = $_POST['category'];
    

    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    $new_image = time() . "_" . $image;

    move_uploaded_file($tmp, "../uploads/" . $new_image);

    $user_id = $_SESSION['user_id'];

    $status = "approved";

    $result = $product->addProduct($user_id, $product_name, $price, $description, $new_image, $quantity, $category, $status);

    $message = "Product Added Successfully";
    
}
?>




<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>

     <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background: #fff;
            padding: 25px;
            width: 400px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0,0,0,0.1);
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #218838;
        }

        .message {
            text-align: center;
            margin-top: 10px;
            color: green;
        }
    </style>
</head>

<body>

<div class="form-container">

    <?php if(isset($message)) { echo "<p class='message'>$message</p>"; } ?>
    
    <h2>Add Product</h2>

    <form method="POST" enctype="multipart/form-data">

        <label>Product Name</label>
        <input type="text" name="product_name" required>

        <label>Description</label>
        <textarea name="description" required></textarea>

        <label>Price</label>
        <input type="number" name="price" required>

        <label>Quantity</label>
        <input type="number" name="quantity" required>

        <label>Category</label>
        <input type="text" name="category" required>

        <label>Image</label>
        <input type="file" name="image" required>

        <button type="submit" name="submit">Add Product</button>

    </form>

<a href="manage_product.php" class="btn">Manage Products</a><br>
<a href="admin_dashboard.php" class="btn">Back to Dashboard</a>

</div>

</body>
</html>