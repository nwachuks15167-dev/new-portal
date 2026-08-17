<?php
session_start();

require_once "../classes/admin.php";
require_once "../classes/database.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin")
{
    header("Location: ../login.php");
    exit();
}

$admin = new Admin();

$id = $_GET['id'];

$user = $admin->getUserById($id);

if(isset($_POST['update']))
{
    $admin->editUser(
        $id,
        $_POST['username'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['age'],
        $_POST['role']
    );

    header("Location: manage_user.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
</head>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family:Arial,sans-serif;
            background:#f4f6f9;
        }

        .container{
            width:80%;
            margin:50px auto;
            background:white;
            padding:20px;
            border-radius:5px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }

        h2{
            text-align:center;
            margin-bottom:20px;
        }

        form{
            display:flex;
            flex-direction:column;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        select{
            padding:10px;
            margin-bottom:15px;
            border:1px solid #ccc;
            border-radius:5px;
        }

        input[type="submit"]{
            padding:10px;
            background:#2c3e50;
            color:white;
            border:none;
            border-radius:5px;
            cursor:pointer;
        }

        input[type="submit"]:hover{
            background:#34495e;
        }

        </style>

<body>

<h2>Edit User</h2>

<form method="POST">

Username
<input type="text" name="username"
value="<?php echo $user['username']; ?>">

Email
<input type="email" name="email"
value="<?php echo $user['email']; ?>">

Phone
<input type="text" name="phone"
value="<?php echo $user['phone']; ?>">

Age
<input type="number" name="age"
value="<?php echo $user['age']; ?>">

Role

<select name="role">

<option value="user"
<?php if($user['role']=="user") echo "selected"; ?>>

User

</option>

<option value="admin"
<?php if($user['role']=="admin") echo "selected"; ?>>

Admin

</option>

</select>

<input type="submit"
name="update"
value="Update User">

</form>

</body>