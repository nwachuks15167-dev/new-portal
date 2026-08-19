<?php

session_start();

session_destroy();

header("Location: index.php");
exit();

  {
        $sql = "SELECT
                products.*,
                GROUP_CONCAT(users.username SEPARATOR ', ') AS usernames
            FROM products
            LEFT JOIN user_products
                ON products.id = user_products.product_id
            LEFT JOIN users
                ON user_products.user_id = users.id
            GROUP BY products.id";

            return mysqli_query($this->conn, $sql);
    }

/*

$totalSpent = $Order->getTotalSpent($user_id);

<div class="card">
            <div class="card-icon">💰</div>
            <h3>Total Spent</h3>
            <h1>
                ₦<?php echo number_format($totalSpent['total_spent'] ?? 0); ?>
            </h1>
            <p>Amount spent on orders</p>
        </div>


public function getTotalSpent($user_id)
    {
        $sql = "SELECT SUM(total_amount) AS total_spent
            FROM orders
            WHERE user_id = '$user_id'
            AND payment_status = 'paid'";

        $result = mysqli_query($this->conn, $sql);

        $row = mysqli_fetch_assoc($result);

        return $row['total'];
    }



<!-- All Users -->
    <h3>All Users</h3>
    <div class="users-grid">
        <?php // while ($u = mysqli_fetch_assoc($all_users)): ?>
            <div class="user-card">
                <img src="uploads/<?= htmlspecialchars($u['profile_pic']) ?>"
                     alt="profile"
                     onerror="this.src='uploads/placeholder.jpg'">
                <h4><?=// htmlspecialchars($u['username']) ?></h4>
                <p><?= //htmlspecialchars($u['email']) ?></p>
                <p>Age: <?=// htmlspecialchars($u['age']) ?></p>
            </div>
        <?php// endwhile; ?>
    </div>


<?php

session_start();

require_once "classes/User.php";

$user = new User();

$message = "";

if(isset($_POST['login']))
{
    if(
        $user->login(
            $_POST['email'],
            $_POST['password']
        )
    )
    {
        header("Location: dashboard.php");
        exit();
    }
    else
    {
        $message = "Invalid Email or Password";
    }
}
?>

<!DOCTYPE html>

<html>
<head>
    <title>Login</title>
</head>
<body>

<h2>Login</h2>

<p><?php echo $message; ?></p>

<form method="POST">

```
<input type="email"
       name="email"
       placeholder="Email"
       required><br><br>

<input type="password"
       name="pwd"
       placeholder="Password"
       required><br><br>

<button type="submit" name="login">
    Login
</button>
```

</form>

</body>
</html>

/*<?php

require_once "Database.php";

class User extends Database
{
    public function __construct()
    {
        parent::__construct();
    }

    // Register User
    public function register($username, $email, $phone, $age, $pwd, $image)
    {
        // Check if email exists
        $check = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($this->conn, $check);

        if(mysqli_num_rows($result) > 0)
        {
            return "Email already exists";
        }

        // Hash password
        $hashed_pwd = password_hash($pwd, PASSWORD_DEFAULT);

        // Upload image
        $image_name = $image['name'];
        $tmp_name = $image['tmp_name'];

        $new_image_name = time() . "_" . $image_name;

        move_uploaded_file($tmp_name,"uploads/" . $new_image_name);

        // Insert user
        $sql = "INSERT INTO users
                (username, email, phone, age, pwd, profile_pic)
                VALUES
                ('$username',
                 '$email',
                 '$phone',
                 '$age',
                 '$hashed_pwd',
                 '$new_image_name')";

        if(mysqli_query($this->conn, $sql))
        {
            return true;
        }

        return false;
    }

    // Login User
    public function login($email, $pwd)
    {
        $sql = "SELECT * FROM users
                WHERE email='$email'";

        $result = mysqli_query($this->conn, $sql);

        if(mysqli_num_rows($result) > 0)
        {
            $user = mysqli_fetch_assoc($result);

            if(password_verify($pwd, $user['pwd']))
            {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                return true;
            }
        }

        return false;
    }

    // Get User By ID
    public function getUserById($id)
    {
        $sql = "SELECT * FROM users WHERE id='$id'";

        $result = mysqli_query($this->conn, $sql);

        return mysqli_fetch_assoc($result);
    }
}
?>*/

?>