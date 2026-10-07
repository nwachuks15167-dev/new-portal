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

if(isset($_POST['change_password']))
{
    $success = $user->changePassword(
        $_SESSION['user_id'],
        $_POST['new_password'],
        $_POST['confirm_password']
    );

    if($success)
    {
        $message = "Password changed successfully";
    }
    else
    {
        $error = "Password change failed";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
<link rel="stylesheet" href="includes/edit_profile.css">

       
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

    <form method="POST">

    <h3>Change Password</h3>

    <input type="password" name="new_password"
           placeholder="New Password">

    <input type="password" name="confirm_password"
           placeholder="Confirm Password">

    <button type="submit" name="change_password">
        Change Password
    </button>

</form>

    <br>

    <a href="profile.php">Back to Profile</a>

</div>

</body>
</html>