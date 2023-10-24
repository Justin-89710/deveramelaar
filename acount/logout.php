<?php
// start session
session_start();

//end session
session_destroy();

//redirect to home page
header("Location: ../home/home.php");
?>