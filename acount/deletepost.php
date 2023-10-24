<?php
// start session
session_start();

// connect to database
$db = new SQLite3('../database/database.db');

// check if connection is successful
if (!$db) {
    die("Connection failed: " . $db->connect_error);
}

// show server errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// check for admin rank
if ($_SESSION['rank'] != 1) {
    header("Location: ../home/home.php");
}

// get id from url
$id = $_GET['id'];

// delete user
$db->exec("DELETE FROM Posts WHERE ID='$id'");

// redirect to admin page
header("Location: Admin.php");
?>