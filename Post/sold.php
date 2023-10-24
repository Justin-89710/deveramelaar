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

// set besteld to 1 in Post
$id = $_GET['id'];
$db->exec("UPDATE Posts SET besteld='1' WHERE ID='$id'");
header("Location: ../home/home.php");
?>