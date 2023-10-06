<?php
// Start the session
session_start();

// Connect to the database
$db = new SQLite3('../database/database.db');

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../home/home.php");
    exit;
}

if (isset($_POST['logsubmit'])) {
    $email = $_POST['logemail'];
    $password = $_POST['logpass'];

    $result = $db->query("SELECT * FROM Login WHERE email='$email'");
    $row = $result->fetchArray();
    $hash = $row['password'];
    $id = $row['ID'];

    if (password_verify($password, $hash)) {
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $row['username'];
        $_SESSION["id"] = $id;
        $_SESSION["email"] = $email;
        $_SESSION["rank"] = $row['rank'];
        header("location: ../home/home.php");
    } else {
        echo '<script>alert("Username or password is incorrect")</script>';
    }
}

// signup
// Make a new user

if (isset($_POST['regsubmit'])) {
    $email = $_POST['regemail'];
    $name = $_POST['regname'];
    $password = $_POST['regpass'];

    // check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email is ongeldig.";
    } else {
        // check if email is already in use
        $result = $db->query("SELECT * FROM Login WHERE email='$email'");
        if ($result->fetchArray()) {
            $error = "Email is al in gebruik.";
        } else {
            // check if username is already in use
            $result = $db->query("SELECT * FROM Login WHERE username='$name'");
            if ($result->fetchArray()) {
                $error = "Username is al in gebruik.";
                //if there is an error, show it
            } elseif (isset($error)) {
                echo "<div class='alert alert-danger' role='alert'>" . $error . "</div>";
            } elseif (strlen($password) < 6) {
                $error = "Wachtwoord moet minimaal 6 tekens lang zijn.";
            } elseif (!preg_match("#[0-9]+#", $password)) {
                $error = "Wachtwoord moet minimaal 1 nummer bevatten.";
            } elseif (!preg_match("#[A-Z]+#", $password)) {
                $error = "Wachtwoord moet minimaal 1 hoofdletter bevatten.";
            } elseif (!preg_match("#[a-z]+#", $password)) {
                $error = "Wachtwoord moet minimaal 1 kleine letter bevatten.";
            } else {
                // generate 4 diget code
                $code = rand(1000, 9999);
                // send email
                $to = $email;
                $subject = "Team de Verzamelaar - Verifieer je email";
                $message = "
                Hier is je code: $code
                voer deze in op de pagina om je email te verifiëren.
                
                Met vriendelijke groet,
                Team de Verzamelaar
                ";
                $headers = "From:
                Team de verzamelaar";
                mail($to, $subject, $message, $headers);
                // save data in session
                $_SESSION['email'] = $email;
                $_SESSION['name'] = $name;
                $_SESSION['password'] = $password;
                $_SESSION["rank"] = $row['rank'];
                $_SESSION['code'] = $code;
                // redirect to code page
                header("Location: coderegsiter.php");
            }
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
    <!-- link to css -->
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS -->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Login</title>
</head>
<body>

<a class="logo" target="_blank">
    <img src="../afbeeldingen/logo.png" alt="" style="width: 100px; height: 100px; border-radius: 0.5em">
</a>

<div class="section">
    <div class="container">
        <div class="row full-height justify-content-center">
            <div class="col-12 text-center align-self-center py-5">
                <div class="section pb-5 pt-5 pt-sm-2 text-center">
                    <h6 class="mb-0 pb-3"><span style="color: white">Log In </span><span style="color: white">Sign Up</span></h6>
                    <input class="checkbox" type="checkbox" id="reg-log" name="reg-log"/>
                    <label for="reg-log"></label>
                    <div class="card-3d-wrap mx-auto">
                        <div class="card-3d-wrapper">
                            <div class="card-front">
                                <div class="center-wrap">
                                    <div class="section text-center">
                                        <form method="POST">
                                        <h4 class="mb-4 pb-3" style="color: white">Log In</h4>
                                        <div class="form-group">
                                            <input type="email" name="logemail" class="form-style" placeholder="Your Email" id="logemail" autocomplete="off">
                                            <i class="input-icon uil uil-at"></i>
                                        </div>
                                        <div class="form-group mt-2">
                                            <input type="password" name="logpass" class="form-style" placeholder="Your Password" id="logpass" autocomplete="off">
                                            <i class="input-icon uil uil-lock-alt"></i>
                                        </div>
                                        <input type="submit" value="submit" name="logsubmit" class="btn mt-4">
                                        <p class="mb-0 mt-4 text-center"><a href="forgetpassword.php" class="link">Forgot your password?</a></p>
                                    </div>
                                </div>
                            </div>
                            </form>
                            <div class="card-back">
                                <div class="center-wrap">
                                    <div class="section text-center">
                                        <form method="post">
                                        <h4 class="mb-4 pb-3" style="color: white">Sign Up</h4>
                                        <div class="form-group">
                                            <input type="text" name="regname" class="form-style" placeholder="Your Full Name" id="logname" autocomplete="off">
                                            <i class="input-icon uil uil-user"></i>
                                        </div>
                                        <div class="form-group mt-2">
                                            <input type="email" name="regemail" class="form-style" placeholder="Your Email" id="logemail" autocomplete="off">
                                            <i class="input-icon uil uil-at"></i>
                                        </div>
                                        <div class="form-group mt-2">
                                            <input type="password" name="regpass" class="form-style" placeholder="Your Password" id="logpass" autocomplete="off">
                                            <i class="input-icon uil uil-lock-alt"></i>
                                        </div>
                                        <input type="submit" value="submit" name="regsubmit" class="btn mt-4">
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
