<?php

require_once "classes/User.php";

// if (!isset($_SESSION['user_id'])) {

//     header("Location: login.php");

//     exit();

// }

$user = new User();

$userData = null;

if (isset($_SESSION['user_id'])) {

    $userData = $user->getUserById($_SESSION['user_id']);

}

?>


<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar" id="sidebar">

    <div class="sidebar-header">

        <h2>Eloc Stores</h2>

        <button
            type="button"
            class="close-btn"
            onclick="toggleSidebar()">
            ×
        </button>

    </div>


    <?php if ($currentPage == 'dashboard.php') { ?>

        <div class="sidebar-profile">

            <img
                src="uploads/<?php echo htmlspecialchars($userData['profile_pic']); ?>"
                alt="Profile Picture"
            >

            <h3>
                <?php echo htmlspecialchars($userData['username']); ?>
            </h3>

            <h3>
                <?php echo htmlspecialchars($userData['email']); ?>
            </h3>

            <p>Welcome back</p>

        </div>

    <?php } ?>


    <div class="sidebar-menu">

        <a href="dashboard.php">
            <span>🏠</span>
            <span>Dashboard</span>
        </a>


        <a href="profile.php">
            <span>👤</span>
            <span>Profile</span>
        </a>


        <a href="products.php">
            <span>🛍</span>
            <span>Products</span>
        </a>


        <a href="my_products.php">
            <span>📦</span>
            <span>My Products</span>
        </a>


        <a href="order_history.php">
            <span>🧾</span>
            <span>Orders</span>
        </a>


        <a href="logout.php">
            <span>🚪</span>
            <span>Logout</span>
        </a>

    </div>

</div>


<!-- Open Sidebar Button -->

<button
    type="button"
    class="open-btn"
    id="openBtn"
    onclick="toggleSidebar()">

    ☰

</button>

    