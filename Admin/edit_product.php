<?php
session_start();

require_once "../classes/Database.php";
require_once "../classes/Product.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin")
{
    header("Location: ../login.php");
    exit();
}

$product = new Product();

$id = $_GET['id'];

$data = $product->getProductById($id);

if(isset($_POST['update']))
{
    $name = $_POST['product_name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $data['image'];
    $quantity = $_POST['quantity'];
    $category = $_POST['category'];

    // Check if admin uploads another image
    if($_FILES['image']['name'] != "")
    {
        $image = time()."_".$_FILES['image']['name'];

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../uploads/".$image
        );
    }

    $product->updateProduct($id,$name,$price,$description,$image,$quantity,$category);

    header("Location: manage_product.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
</head>

<style>
    *{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:Arial, Helvetica, sans-serif;
    background:#f4f6f9;
}

.container{
    width:550px;
    margin:50px auto;
    background:#ffffff;
    padding:35px;
    border-radius:12px;
    box-shadow:0 8px 20px rgba(0,0,0,0.15);
}

h2{
    text-align:center;
    color:#1e3a8a;
    margin-bottom:30px;
}

label{
    display:block;
    margin-top:18px;
    margin-bottom:8px;
    font-weight:bold;
    color:#333;
}

input[type=text],
input[type=number],
input[type=file],
select,
textarea{

    width:100%;
    padding:12px;
    border:1px solid #ccc;
    border-radius:6px;
    font-size:15px;
    transition:.3s;
}

input:focus,
select:focus,
textarea:focus{

    border-color:#1e3a8a;
    outline:none;
    box-shadow:0 0 6px rgba(30,58,138,.3);
}

textarea{

    resize:vertical;
}

.image-box{

    text-align:center;
    margin:15px 0;
}

.product-image{

    width:180px;
    height:180px;
    object-fit:cover;
    border-radius:10px;
    border:3px solid #ddd;
    box-shadow:0 4px 10px rgba(0,0,0,.15);
}

.btn{

    width:100%;
    margin-top:25px;
    padding:14px;
    border:none;
    border-radius:6px;
    background:#1e3a8a;
    color:white;
    font-size:17px;
    cursor:pointer;
    font-weight:bold;
    transition:.3s;
}

.btn:hover{

    background:#163172;
    transform:translateY(-2px);
}

</style>

<body>

<h2>Edit Product</h2>

<form method="POST" enctype="multipart/form-data">

<label>Product Name</label><br>
<input
type="text"
name="product_name"
value="<?php echo $data['product_name']; ?>"
required>

<br><br>

<label>Price</label><br>
<input
type="number"
name="price"
value="<?php echo $data['price']; ?>"
required>

<br><br>

<label>Quantity</label><br>
<input
type="number"
name="quantity"
value="<?php echo $data['quantity']; ?>"
required>

<br><br>

<label>Category</label><br>

<select name="category">

<option
<?php if($data['category']=="Electronics") echo "selected"; ?>>
Electronics
</option>

<option
<?php if($data['category']=="Fashion") echo "selected"; ?>>
Fashion
</option>

<option
<?php if($data['category']=="Food") echo "selected"; ?>>
Food
</option>

<option
<?php if($data['category']=="Furniture") echo "selected"; ?>>
Furniture
</option>

<option
<?php if($data['category']=="Books") echo "selected"; ?>>
Books
</option>

</select>

<br><br>

<label>Description</label><br>

<textarea
name="description"
rows="5"><?php echo $data['description']; ?></textarea>

<br><br>

<label>Current Image</label><br>

<img
src="../uploads/<?php echo $data['image']; ?>"
width="150">

<br><br>

<label>Choose New Image (Optional)</label><br>

<input
type="file"
name="image">

<br><br>

<button type="submit" name="update">
Update Product
</button>

</form>

</body>
</html>