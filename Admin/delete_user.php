<?php
session_start();

require_once "../classes/Database.php";
require_once "../classes/Admin.php";

$admin = new Admin();

$id = $_GET['id'];

$admin->deleteUser($id);

header("Location: manage_user.php");