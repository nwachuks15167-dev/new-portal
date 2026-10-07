<?php
require_once "database.php";

class User extends Database {

    public function __construct()
    {
        parent::__construct();

    }

    // Register User
    public function register($username, $email, $phone, $age, $password, $image)
    {
        // Check if email exists
        $check = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($this->conn, $check);
        if(mysqli_num_rows($result) > 0)
        {
            return "Email already exists";
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Upload image
        $image_name = $image['name'];
        $tmp_name = $image['tmp_name'];
        $new_image_name = time() . "_" . $image_name;
        move_uploaded_file($tmp_name,"uploads/" . $new_image_name);

        // Insert user
        $sql = "INSERT INTO users
                (username, email, phone, age, password, profile_pic)
                VALUES
                ('$username','$email','$phone','$age','$hashed_password','$new_image_name')";

        if(mysqli_query($this->conn, $sql))
        {
            return true;
        }
            return false;
    }

    // Login User
    public function login($email, $password)
    {
        $sql = "SELECT * FROM users
                WHERE email='$email'";
        $result = mysqli_query($this->conn, $sql);

        if(mysqli_num_rows($result) > 0)
        {
            $user = mysqli_fetch_assoc($result);
            if(password_verify($password, $user['password']))
            {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                return $user;
            }
        }
        return false;
    }

    // GET ALL USERS
    public function getAllUsers() {
        $sql    = "SELECT * FROM users";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }

    // GET SINGLE USER BY ID
    public function getUserById($id) {
        $sql    = "SELECT * FROM users WHERE id='$id'";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($result);
    }

    // UPDATE USER
    public function updateUser($id, $username, $email, $phone, $age, $profile_pic, $password) {
        $sql = "UPDATE users 
                SET username='$username', 
                    email='$email', 
                    phone='$phone', 
                    age='$age',
                    profile_pic='$profile_pic'
                WHERE id='$id'";

        if (mysqli_query($this->conn, $sql)) {
            return true;
        } else {
            return "Update failed: " . mysqli_error($this->conn);
        }
    }

    public function changePassword($id, $new, $confirm) {
        if ($new !== $confirm) {
            return "Passwords do not match.";
        }

        $hashed_password = password_hash($new, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password='$hashed_password' WHERE id='$id'";

        if (mysqli_query($this->conn, $sql)) {
            return true;
        } else {
            return "Password change failed: " . mysqli_error($this->conn);
        }
    }

    // DELETE USER
    public function deleteUser($id) {
        $sql = "DELETE FROM users WHERE id='$id'";
        if (mysqli_query($this->conn, $sql)) {
            return true;
        } else {
            return "Delete failed: " . mysqli_error($this->conn);
        }
    }

     // Logout User
    public function logout()
    {
        session_unset();
        session_destroy();

        header("Location: login.php");
        exit;
    }

    
}
?>