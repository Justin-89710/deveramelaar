<?php
// start session
session_start();

// connect to database
$db = new SQLite3('../database/database.db');

// check if connection is successful
if (!$db) {
    die("Connection failed: " . $db->connect_error);
}

// change password
if (isset($_POST['submit'])) {
    // get password
    $password = $_POST['newpassword'];
    // check if password is valid
    if (strlen($password) < 8) {
        $error = "Wachtwoord moet minimaal 8 tekens lang zijn.";
    } elseif (!preg_match("#[0-9]+#", $password)) {
        $error = "Wachtwoord moet minimaal 1 nummer bevatten.";
    } elseif (!preg_match("#[A-Z]+#", $password)) {
        $error = "Wachtwoord moet minimaal 1 hoofdletter bevatten.";
    } elseif (!preg_match("#[a-z]+#", $password)) {
        $error = "Wachtwoord moet minimaal 1 kleine letter bevatten.";
    } else {
        // check if password is the same as the confirmation password
        if ($_POST['newpassword'] != $_POST['confirmpassword']) {
            $error = "Wachtwoorden komen niet overeen.";
        } else {
            // hash password
            $password = password_hash($password, PASSWORD_DEFAULT);
            // get email
            $email = $_SESSION['email'];
            // update password
            $db->exec("UPDATE Login SET password='$password' WHERE email='$email'");
            // redirect to login page
            header("Location: login.php");
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Icon Logo -->

    <link rel="icon" href="../afbeeldingen/logo.png">

    <title>New Password</title>

    <!-- Stylesheets -->

    <link rel="stylesheet" href="style.css">

    <!-- Bootstrap CSS -->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>
<div class="section">
    <div class="container">
        <div class="row full-height justify-content-center">
            <div class="col-12 text-center align-self-center py-5">
                <div class="section pb-5 pt-5 pt-sm-2 text-center">
                    <label for="reg-log"></label>
                    <div class="card-3d-wrap mx-auto">
                        <div class="card-3d-wrapper">
                            <div class="card-front">
                                <div class="center-wrap">
                                    <div class="section text-center">
                                        <form method="POST">
                                            <h4 class="mb-4 pb-3" style="color: white">New Password</h4>
                                            <div class="form-group">
                                                <input type="password" name="newpassword" class="form-style" placeholder="New Password" id="logemail" autocomplete="off">
                                                <i class="input-icon uil uil-at"></i>
                                            </div>
                                            <div class="form-group">
                                                <input type="password" name="confirmpassword" class="form-style" placeholder="Confirm Password" id="logemail" autocomplete="off">
                                                <i class="input-icon uil uil-at"></i>
                                            </div>
                                            <?php
                                            if (isset($error)) {
                                                echo "<div class='alert alert-danger' role='alert'>" . $error . "</div>";
                                            }
                                            ?>
                                            <input type="submit" value="submit" name="submit" class="btn mt-4">
                                            <p class="mb-0 mt-4 text-center" style="color: white">Put in your new password and your done! Make sure you put the same password in twice!</p>
                                    </div>

                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>