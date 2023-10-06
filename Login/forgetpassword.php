<?php
// start session
session_start();

// connect to database
$db = new SQLite3('../database/database.db');

// check if connection is successful
if (!$db) {
    die("Connection failed: " . $db->connect_error);
}

if (isset($_POST['submit'])){
    //get email
    $email = $_POST['email'];

    //check if email exists
    $stmt = $db->prepare("SELECT * FROM Login WHERE email = :email");
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray();

    //generate random 4 digit code
    $code = rand(1000, 9999);

    //send mail with this code
    $to = $email;
    $subject = "Wachtwoord vergeten";
    $message = "Uw code is: " . $code;
    $headers = "From: Team De Verzamelaar";

    mail($to, $subject, $message, $headers);

    //store code in session
    $_SESSION['code'] = $code;

    //store email in session
    $_SESSION['email'] = $email;

    //send to code page
    header("Location: code.php");
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

        <title>Wachtwoord vergeten</title>

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
                                                <h4 class="mb-4 pb-3" style="color: white">Forget Password</h4>
                                                <div class="form-group">
                                                    <input type="email" name="email" class="form-style" placeholder="Your Email" id="logemail" autocomplete="off">
                                                    <i class="input-icon uil uil-at"></i>
                                                </div>
                                                <input type="submit" value="submit" name="submit" class="btn mt-4">
                                                <p class="mb-0 mt-4 text-center"><a href="login.php" class="link">Already have an account? Login!</a></p>
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