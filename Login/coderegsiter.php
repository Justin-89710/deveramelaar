<?php
// start session
session_start();

// connect to database
$db = new SQLite3('../database/database.db');

// check if connection is successful
if (!$db) {
    die("Connection failed: " . $db->connect_error);
}

// show server erors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// get code from session
$code = $_SESSION['code'];

// show session email
// check if code is correct
if (isset($_POST['submit'])) {
    $codeinput = $_POST['code'];
    if ($codeinput == $code) {
        // make new user
        $email = $_SESSION['email'];
        $name = $_SESSION['name'];
        $password = $_SESSION['password'];
        // hash password
        $hassword = password_hash($password, PASSWORD_DEFAULT);
        // insert user into database
        $db->exec("INSERT INTO Login (email, username, password, profielfoto, bio ,rank) VALUES ('$email', '$name', '$hassword', 'default.jpg', 'Dit is mijn bio!', 0)");
        // set profile picture
        // login the user
        $result = $db->query("SELECT * FROM Login WHERE email='$email'");
        $row = $result->fetchArray();
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $row['ID'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['email'] = $row['email'];
        if ($row['profile_pic'] == null) {
            $db->exec("UPDATE Login SET profielfoto = 'default.jpg' WHERE email='$email'");
        }
        if ($row['bio'] == null) {
            $db->exec("UPDATE Login SET bio = 'Dit is mijn bio!' WHERE email='$email'");
        }
        if (isset($error)){
            echo "<div class='alert alert-danger' role='alert'>" . $error . "</div>";
        } else {
            header("Location: ../home/home.php");
        }
    } else {
        $error = "Code is ongeldig.";
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

    <title>Email Check</title>

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
                                            <h4 class="mb-4 pb-3" style="color: white">Email Check</h4>
                                            <div class="form-group">
                                                <input type="text" name="code" class="form-style" placeholder="Your Code" id="logemail" autocomplete="off">
                                                <i class="input-icon uil uil-at"></i>
                                            </div>
                                            <input type="submit" value="submit" name="submit" class="btn mt-4">
                                            <p class="mb-0 mt-4 text-center" style="color: white">You got a mail with a 4 diget code please enter that code here!</p>
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