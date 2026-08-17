<?php

require_once "Database.php";

class Order extends Database{

    public function __construct()
    {
        parent::__construct();
    }

    public function createOrder($user_id, $address, $total_amount, $payment_method)
    {
        $sql = "INSERT INTO orders
                (user_id,address,total_amount,payment_method,payment_status,order_status)
            VALUES
            ('$user_id','$address','$total_amount','$payment_method','Pending','Pending')";
            mysqli_query($this->conn, $sql);

        return mysqli_insert_id($this->conn);
    }

    public function addOrderItems($user_id,$order_id)
    {
        $sql = "INSERT INTO order_items
        (order_id, product_id, price, quantity)
        SELECT '$order_id',products.id,products.price,1
        FROM user_products
        INNER JOIN products
        ON user_products.product_id = products.id
        WHERE user_products.user_id='$user_id'";

        return mysqli_query($this->conn,$sql);
    }

    public function clearUserProducts($user_id)
    {
        $sql = "DELETE FROM user_products
        WHERE user_id='$user_id'";

        return mysqli_query($this->conn,$sql);
    }

    public function getOrderById($order_id)
    {
        $sql = "SELECT orders.*, users.username
            FROM orders
            INNER JOIN users
            ON orders.user_id = users.id
            WHERE orders.id='$order_id'";

        $result = mysqli_query($this->conn, $sql);
        if(!$result)
        {
            return false;
        }
        return mysqli_fetch_assoc($result);
    }

    public function getUserOrders($user_id)
    {
        $sql = "SELECT * FROM orders
        WHERE user_id='$user_id'
        ORDER BY id DESC";

        return mysqli_query($this->conn,$sql);
    }

    public function getOrderProducts($order_id)
    {
        $sql = "SELECT products.*,order_items.quantity,
        order_items.price FROM order_items
        INNER JOIN products
        ON order_items.product_id = products.id
        WHERE order_items.order_id='$order_id'";

        return mysqli_query($this->conn,$sql);
    }

    public function confirmPayment($order_id)
    {
        $sql = "UPDATE orders
        SET payment_status='Paid',
        order_status='Processing'
        WHERE id='$order_id'";

        return mysqli_query($this->conn,$sql);
    }

    public function updateOrderStatus($order_id,$payment_status,$order_status)
    {
        $sql = "UPDATE orders
        SET payment_status='$payment_status',
        order_status='$order_status'
        WHERE id='$order_id'";

        return mysqli_query($this->conn,$sql);
    }

    public function getAllOrders()
    {
        $sql = "SELECT orders.*,users.username,users.email
        FROM orders INNER JOIN users
        ON orders.user_id = users.id
        ORDER BY orders.id DESC";

        return mysqli_query($this->conn,$sql);
    }

    public function saveOrderItems($order_id, $user_id)
    {
        $sql = "SELECT
                user_products.product_id,
                user_products.quantity,
                products.price
            FROM user_products
            INNER JOIN products
            ON user_products.product_id = products.id
            WHERE user_products.user_id='$user_id'";

        $cart = mysqli_query($this->conn, $sql);
        if(!$cart)
        {
            return false;
        }
        if(mysqli_num_rows($cart) == 0)
        {
            return false;
        }

        while($row = mysqli_fetch_assoc($cart))
        {
        $product_id = (int)$row['product_id'];
        $quantity = (int)$row['quantity'];
        $price = (float)$row['price'];

        $insert = "INSERT INTO order_items
                   (order_id, product_id, quantity, price)
                   VALUES
                   ('$order_id','$product_id','$quantity','$price')";

            if(!mysqli_query($this->conn, $insert))
            {
                return false;
            }
        }
            return true;
    }

    public function reduceProductQuantity($order_id)
    {
        $sql = "SELECT product_id, quantity
            FROM order_items
            WHERE order_id='$order_id'";

        $result = mysqli_query($this->conn, $sql);
        if(!$result)
        {
            return false;
        }

        while($row = mysqli_fetch_assoc($result))
        {
            $product_id = (int)$row['product_id'];
            $quantity = (int)$row['quantity'];

            $update = "UPDATE products
                   SET quantity = quantity - $quantity
                   WHERE id='$product_id'
                   AND quantity >= $quantity";

            if(!mysqli_query($this->conn, $update))
            {
                return false;
            }
        }
            return true;
    }

   public function deleteOrder($order_id)
    {
        // First delete the order items
        $sql1 = "DELETE FROM order_items
             WHERE order_id='$order_id'";

        $result1 = mysqli_query($this->conn, $sql1);
        if(!$result1)
        {
            return false;
        }
        // Then delete the actual order
        $sql2 = "DELETE FROM orders
             WHERE id='$order_id'";

        $result2 = mysqli_query($this->conn, $sql2);
        if(!$result2)
        {
            return false;
        }
        return true;
    }

    public function getTotalOrders($user_id)
{
    $sql = "SELECT COUNT(*) AS total_orders
            FROM orders
            WHERE user_id = '$user_id'";

    $result = mysqli_query($this->conn, $sql);

    return mysqli_fetch_assoc($result);
}

    public function getTotalSpent($user_id)
    {
        $sql = "SELECT COALESCE(SUM(total_amount), 0) AS total
            FROM orders
            WHERE user_id = '$user_id'
            AND payment_status = 'paid'";

        $result = mysqli_query($this->conn, $sql);

        $row = mysqli_fetch_assoc($result);

        return $row['total'];
    }

}