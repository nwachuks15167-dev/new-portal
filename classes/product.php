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
        $stock = $productData['quantity'];

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

        $cartQuantity = $cartData['quantity'];
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
        if($user_product['quantity'] > 1)
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

    public function mergeSessionCart($user_id, $session_cart)
{
    if (empty($session_cart)) {
        return true;
    }

    mysqli_begin_transaction($this->conn);

    try {
        foreach ($session_cart as $product_id => $quantity) {
            $product_id = (int) $product_id;
            $quantity = (int) $quantity;
            $user_id = (int) $user_id;

            if ($product_id < 1 || $quantity < 1) {
                throw new Exception("Invalid product or quantity.");
            }

            // Check the product and available stock
            $stmt = mysqli_prepare(
                $this->conn,
                "SELECT quantity FROM products WHERE id = ?"
            );

            mysqli_stmt_bind_param($stmt, "i", $product_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if (!$product) {
                throw new Exception("Product not found.");
            }

            $stock = (int) $product['quantity'];

            // Check whether this user already has the product
            $stmt = mysqli_prepare(
                $this->conn,
                "SELECT quantity FROM user_products
                 WHERE user_id = ? AND product_id = ?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ii",
                $user_id,
                $product_id
            );

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $existing = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            $old_quantity = $existing
                ? (int) $existing['quantity']
                : 0;

            $new_quantity = $old_quantity + $quantity;

            // Do not allow the cart quantity to exceed stock
            if ($new_quantity > $stock) {
                throw new Exception(
                    "There is not enough stock for product ID $product_id."
                );
            }

            if ($existing) {
                // Update the existing cart row
                $stmt = mysqli_prepare(
                    $this->conn,
                    "UPDATE user_products
                     SET quantity = ?
                     WHERE user_id = ? AND product_id = ?"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "iii",
                    $new_quantity,
                    $user_id,
                    $product_id
                );
            } else {
                // Add a new cart row
                $stmt = mysqli_prepare(
                    $this->conn,
                    "INSERT INTO user_products
                     (user_id, product_id, quantity)
                     VALUES (?, ?, ?)"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "iii",
                    $user_id,
                    $product_id,
                    $quantity
                );
            }

            if (!mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                throw new Exception("Unable to save cart product.");
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_commit($this->conn);
        return true;

    } catch (Throwable $e) {
        mysqli_rollback($this->conn);
        return false;
    }
}


public function addQuantityToMyProducts($user_id, $product_id, $quantity)
{
    $user_id = (int) $user_id;
    $product_id = (int) $product_id;
    $quantity = (int) $quantity;

    if ($user_id < 1 || $product_id < 1 || $quantity < 1) {
        return false;
    }

    $this->conn->begin_transaction();

    try {
        $sql = "SELECT quantity
                FROM products
                WHERE id = ?
                FOR UPDATE";

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $productData = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$productData) {
            throw new Exception("Product not found.");
        }

        $stock = (int) $productData['quantity'];

        $sql = "SELECT quantity
                FROM user_products
                WHERE user_id = ? AND product_id = ?
                FOR UPDATE";

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $existing = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        $current = $existing ? (int) $existing['quantity'] : 0;

        if (($current + $quantity) > $stock) {
            throw new Exception("Selected quantity exceeds stock.");
        }

        if ($existing) {
            $newQuantity = $current + $quantity;

            $sql = "UPDATE user_products
                    SET quantity = ?
                    WHERE user_id = ? AND product_id = ?";

            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param(
                $stmt,
                "iii",
                $newQuantity,
                $user_id,
                $product_id
            );
        } else {
            $sql = "INSERT INTO user_products
                    (user_id, product_id, quantity)
                    VALUES (?, ?, ?)";

            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param(
                $stmt,
                "iii",
                $user_id,
                $product_id,
                $quantity
            );
        }

        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            throw new Exception("Unable to save quantity.");
        }

        mysqli_stmt_close($stmt);
        $this->conn->commit();

        return true;

    } catch (Throwable $e) {
        $this->conn->rollback();
        return false;
    }
}


}