<?php
require_once "database.php";

class product extends Database{
   
    public function __construct()
    {
        parent::__construct();
    }

    public function getallproduct()
    {
        $sql = "SELECT *
            FROM products
            ORDER BY id DESC";
        $result = mysqli_query($this->conn, $sql);
        if(!$result)
        {
            return false;
        }
        return $result;
    }

    public function addproduct($user_id, $product_name, $price, $description, $image, $quantity, $category)
    {
        $sql = "INSERT INTO products
        (user_id, product_name, price, description, image, quantity, category)
        VALUES
        ('$user_id', '$product_name', '$price', '$description', '$image', '$quantity', '$category')";
        return mysqli_query($this->conn, $sql);
    }

    public function addToMyProducts($user_id, $product_id)
    {
    // Check if product already exists in user's personal products
        $check = mysqli_query(
        $this->conn,
        "SELECT * FROM user_products
         WHERE user_id='$user_id'
         AND product_id='$product_id'");

        if(mysqli_num_rows($check) > 0)
        {
            return false;
        }

    // Add product to user's personal page
        $sql = "INSERT INTO user_products
            (user_id, product_id, quantity)
            VALUES
            ('$user_id','$product_id',1)";

        return mysqli_query($this->conn,$sql);
    }

    public function getproductbyid($id)
    {
        $sql    = "SELECT * FROM products WHERE id='$id'";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function getProductsByUser($user_id)
    {
        $sql = "SELECT
                products.*,
                user_products.product_id,
                user_products.quantity
            FROM user_products
            INNER JOIN products
            ON user_products.product_id = products.id
            WHERE user_products.user_id='$user_id'
            ORDER BY user_products.id DESC";

        return mysqli_query($this->conn,$sql);
    }

    public function updateproduct($id, $product_name, $price, $description, $image, $quantity, $category)
    {
        $sql = "UPDATE products
                SET product_name = '$product_name',
                    price = '$price',
                    description = '$description',
                    image = '$image',
                    quantity = '$quantity',
                    category = '$category'

                    WHERE id='$id'";
                    return mysqli_query($this->conn, $sql);
    }

    public function countProducts()
    {
        $sql = "SELECT COUNT(*) AS total FROM products";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function countUserProducts($user_id)
    {
        $sql = "SELECT COUNT(*) AS total
            FROM user_products
            WHERE user_id = '$user_id'";
        $result = mysqli_query($this->conn, $sql);

        return mysqli_fetch_assoc($result);
    }

    public function getLatestProducts($limit = 5)
    {
        $sql = "SELECT *FROM products ORDER BY created_at DESC LIMIT $limit";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }

    public function deleteproduct($id)
    {
        $sql = "DELETE FROM products WHERE id='$id'";
        if (mysqli_query($this->conn, $sql)) {
            return true;
        } else {
            return "Delete failed: " . mysqli_error($this->conn);
        }
    }

    public function deletemyproduct($user_id, $product_id)
    {
        $sql = "DELETE FROM user_products
                WHERE user_id = '$user_id'
                AND product_id = '$product_id'";

        return mysqli_query($this->conn, $sql);
    }

    public function increaseQuantity($user_id, $product_id)
    {
        // Get stock from products table
        $product = mysqli_query($this->conn,
            "SELECT quantity
             FROM products
             WHERE id='$product_id'");
        $productData = mysqli_fetch_assoc($product);

        if(!$productData)
        {
            return false;
        }
        $stock = (int)$productData['quantity'];

        // Get current cart quantity
        $cart = mysqli_query($this->conn,
            "SELECT quantity
             FROM user_products
             WHERE user_id='$user_id'
             AND product_id='$product_id'");
        $cartData = mysqli_fetch_assoc($cart);
        if(!$cartData)
        {
            return false;
        }

        $cartQuantity = (int)$cartData['quantity'];
        // Stop when cart quantity reaches available stock
        if($cartQuantity >= $stock)
        {
            return false;
        }

         // Increase by 1
        $update = "UPDATE user_products
               SET quantity = quantity + 1
               WHERE user_id='$user_id'
               AND product_id='$product_id'";

        return mysqli_query($this->conn, $update);
    }

    public function decreaseQuantity($user_id, $product_id)
    {
        $sql = "SELECT quantity
            FROM user_products
            WHERE user_id='$user_id'
            AND product_id='$product_id'";

        $result = mysqli_query($this->conn, $sql);

        if(!$result)
        {
            return false;
        }
        $user_product = mysqli_fetch_assoc($result);

        // Product was not found
        if(!$user_product)
        {
            return false;
        }

        // Only decrease if quantity is greater than 1
        if((int)$user_product['quantity'] > 1)
        {
            $update = "UPDATE user_products
                   SET quantity = quantity - 1
                   WHERE user_id='$user_id'
                   AND product_id='$product_id'";

            return mysqli_query($this->conn, $update);
        }

        return false;
    }

    public function getProductsByCategory($category)
    {
        $category = mysqli_real_escape_string($this->conn, $category);

        $sql = "SELECT *
            FROM products
            WHERE category = '$category'
            ORDER BY id DESC";

        return mysqli_query($this->conn, $sql);
    }

    public function searchProducts($search)
    {
        $search = mysqli_real_escape_string($this->conn, $search);

        $sql = "SELECT *
            FROM products
            WHERE product_name LIKE '%$search%'
            OR category LIKE '%$search%'
            OR description LIKE '%$search%'
            ORDER BY id DESC";

        return mysqli_query($this->conn, $sql);
    }

}