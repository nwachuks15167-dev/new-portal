<?php

session_start();

if(!isset($_SESSION['user_id']))
{
    header("Location: login.php");
    exit;
}

require_once "classes/User.php";

$user = new User();

$userdata = $user->getUserById($_SESSION['user_id']);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Profile</title>

    <link rel="stylesheet" href="includes/sidebar.css">

    <link rel="stylesheet" href="includes/profile.css">

</head>

<body>
    
<?php include "includes/sidebar.php"; ?>

<div class="main-content" id="mainContent">

    <h1 style="text-align:center;">Profile</h1>

    <div class="profile-card">
        <img src="uploads/<?php echo $userdata['profile_pic']; ?>" alt="Profile Picture">

        <h2>
            <?php echo $userdata['username']; ?>
        </h2>

        <div class="info">

            <p>
                <strong>Email:</strong>
                <?php echo $userdata['email']; ?>
            </p>

            <p>
                <strong>Phone:</strong>
                <?php echo $userdata['phone']; ?>
            </p>

            <p>
                <strong>Age:</strong>
                <?php echo $userdata['age']; ?>
            </p>

            <?php if(isset($userdata['created_at'])) { ?>

            <p>
                <strong>Joined:</strong>
                <?php echo $userdata['created_at']; ?>
            </p>

            <?php } ?>

        </div>

        <div class="buttons">

            <a href="dashboard.php" class="btn dashboard">
                Dashboard
            </a>

            <a href="edit_profile.php" class="btn edit">
                Edit Profile
            </a>

        </div>

    </div>

</div>

<script src="includes/sidebar.js"></script>

</body>
</html>