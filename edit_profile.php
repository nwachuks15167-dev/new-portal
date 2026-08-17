<?php

session_start();

if(!isset($_SESSION['user_id']))
{
    header("Location: login.php");
    exit;
}

require_once "classes/User.php";

$user = new User();

$userData = $user->getUserById($_SESSION['user_id']);

$message = "";

if(isset($_POST['update']))
{
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $age = $_POST['age'];
    $profile_pic = $userData['profile_pic'];

    if(!empty($_FILES['profile_pic']['name']))
    {
        $profile_pic = time() . "_" . $_FILES['profile_pic']['name'];

        move_uploaded_file(
            $_FILES['profile_pic']['tmp_name'],
            "uploads/" . $profile_pic
        );
    }

    $result = $user->updateUser(
        $_SESSION['user_id'],
        $username,
        $email,
        $phone,
        $age,
        $profile_pic,
        $userData['password']
    );
    
    if($result)
    {
        $_SESSION['username'] = $username;
        $message = "Profile updated successfully.";
        $userData = $user->getUserById($_SESSION['user_id']);
    }
    else
    {
        $message = "Failed to update profile.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>

    <style>

        body{
            font-family: Arial, sans-serif;
            background:#f4f6f9;
        }

        .container{
            width:500px;
            margin:40px auto;
            background:white;
            padding:25px;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,.1);
        }

        h2{
            text-align:center;
        }

        input{
            width:100%;
            padding:10px;
            margin-bottom:15px;
        }

        button{
            width:100%;
            padding:12px;
            background:#3498db;
            color:white;
            border:none;
            cursor:pointer;
        }

        img{
            width:120px;
            height:120px;
            border-radius:50%;
            display:block;
            margin:10px auto;
        }

        .message{
            color:green;
            text-align:center;
            margin-bottom:15px;
        }

    </style>

</head>

<body>

<div class="container">

    <h2>Edit Profile</h2>

    <?php if($message){ ?>
        <div class="message">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <img src="uploads/<?php echo $userData['profile_pic']; ?>" alt="Profile Picture">

    <form method="POST" enctype="multipart/form-data">

        <label>Username</label>
        <input
            type="text"
            name="username"
            value="<?php echo $userData['username']; ?>"
            required
        >

        <label>Email</label>
        <input
            type="email"
            name="email"
            value="<?php echo $userData['email']; ?>"
            required
        >

        <label>Phone</label>
        <input
            type="text"
            name="phone"
            value="<?php echo $userData['phone']; ?>"
            required
        >

        <label>Age</label>
        <input
            type="number"
            name="age"
            value="<?php echo $userData['age']; ?>"
            required
        >

        <label>New Profile Picture</label>
        <input type="file" name="profile_pic">

        <button type="submit" name="update">
            Update Profile
        </button>

    </form>

    <br>

    <a href="profile.php">Back to Profile</a>

</div>

</body>
</html>