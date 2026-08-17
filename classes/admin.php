<?php

require_once "User.php";

class Admin extends User
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllUsers()
    {
        $sql = "SELECT * FROM users ORDER BY id DESC";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }

    public function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE id='$id'";
        return mysqli_query($this->conn, $sql);
    }

    public function getAllProducts()
    {
        $sql = "SELECT * FROM products ORDER BY id DESC";
        return mysqli_query($this->conn,$sql);
    }

    public function getProductOwner($user_id)
    {
        $sql = "SELECT username
            FROM users
            WHERE id='$user_id'";
        $result = mysqli_query($this->conn,$sql);
        return mysqli_fetch_assoc($result);
    }

    public function getUsersWithProduct($product_id)
    {
        $sql = "SELECT users.username
            FROM user_products
            INNER JOIN users
            ON user_products.user_id = users.id
            WHERE user_products.product_id='$product_id'";
        return mysqli_query($this->conn,$sql);
    }

    public function deleteProduct($id)
    {
        $sql = "DELETE FROM products WHERE id='$id'";
        return mysqli_query($this->conn, $sql);
    }

    public function getTotalUsers()
    {
        $sql = "SELECT COUNT(*) AS total FROM users";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    public function getTotalProducts()
    {
        $sql = "SELECT COUNT(*) AS total FROM products";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    public function getUserById($id)
    {
        $sql = "SELECT * FROM users WHERE id='$id'";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function getProductById($id)
    {
        $sql = "SELECT * FROM products WHERE id='$id'";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function editUser($id, $username, $email, $phone, $age, $role)
    {
        $sql = "UPDATE users SET username='$username', email='$email', phone='$phone', age='$age', role='$role' WHERE id='$id'";
        return mysqli_query($this->conn, $sql);
    }

    public function addProduct($user_id, $product_name, $price, $description, $image, $quantity, $category, $status)
    {
    $sql = "INSERT INTO products
            (user_id, product_name, price, description,
             image, quantity, category, status)

            VALUES
            ('$user_id', '$product_name', '$price', '$quantity', '$category', '$description', '$image', '$status')";

            return mysqli_query($this->conn, $sql);
    }

}