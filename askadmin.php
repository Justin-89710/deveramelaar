<?php
// start session
session_start();

// connect to database
$db = new SQLite3('database/database.db');

// check if connection is made
if (!$db) {
    die("Connection failed: " . $db->connect_error);
}

// error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    // if not logged in set session variables to null
    $sessionname = "Bezoeker";
    $sessionemail = null;
    $sessionprofielfoto = "default.png";
    $sessionbio = "Ik ben een bezoeker!";
    $sessionrank = null;
} elseif (isset($_SESSION['loggedin'])) {
    // if logged in get session variables
    $sesionid = $_SESSION['id'];
    $result = $db->query("SELECT * FROM Login WHERE ID='$sesionid'");
    $row = $result->fetchArray();
    $sessionname = $row['username'];
    $sessionemail = $row['email'];
    $sessionprofielfoto = $row['profielfoto'];
    $sessionbio = $row['bio'];
    $sessionrank = $row['rank'];
} else {
    $error = "Error";
}

// change rank into text
if ($sessionrank == 0) {
    $sessionrank = "Verzamelaar";
} elseif ($sessionrank == 1) {
    $sessionrank = "Admin";
} elseif ($sessionrank == null) {
    $sessionrank = "Bezoeker";
} else {
    $sessionrank = "Error";
}

// send mail to the mail of people who have rank 1
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $mailFrom = $_POST['mail'];
    $message = $_POST['message'];
    $reden = $_POST['reden'];

    // get mail from admin
    $result = $db->query("SELECT * FROM Login WHERE rank='1'");
    $row = $result->fetchArray();

    // send mail
    $mailTo = "$row[email]";
    $headers = "From: ".$mailFrom;
    $txt = "You have recieved an e-mail from ".$name;
    $body = "Name: ".$name."\n\n".$message."\n\n".$reden;
    mail($mailTo, $txt, $body, $headers);
    header("Location: home/home.php");
}

// search
$searchresult = null;
if (isset($_POST['searchbutton'])) {
    $search = $_POST['searchinput'];
    $searchquery = "SELECT * FROM Login WHERE username LIKE '%$search%'";
    $searchresult = $db->query($searchquery);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contact the admin!</title>
    <link rel="icon" href="afbeeldingen/logo.png">
    <link rel="stylesheet" href="nav/nav.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<section class="h-100" style="min-height: 100vh;">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(145,145,145,0.5);">
        <div class="container-fluid">
            <a class="navbar-brand" href="home/home.php">
                <img src="afbeeldingen/logo.png" alt="" class="logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="home/home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="askadmin.php">Ask The Admin</a>
                    </li>
                </ul>
                <form method="post" class="d-flex">
                    <input type="text" class="form-control me-2" name="searchinput" placeholder="Search">
                    <button type="submit" class="btn btn-outline-light" name="searchbutton">Search</button>
                </form>
                <br>
                <!-- Search Results -->
                <div class="search-results" style="background: rgba(145,145,145,0.5);">
                    <div class="container">
                        <?php
                        if ($searchresult !== null) {
                            while ($searchrow = $searchresult->fetchArray()) {
                                $searchname = $searchrow['username'];
                                $searchprofilepic = $searchrow['profielfoto'];
                                $searchid = $searchrow['ID'];
                                ?>
                                <div class="profile-item">
                                    <a href="acount/profile.php?id=<?php echo $searchid ?>" class="profile-name hover">
                                        <img src="afbeeldingen/<?php echo $searchprofilepic ?>" alt="Profile Picture" class="profile-picture">
                                        <?php echo $searchname ?></a>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <!-- spacer of 50px -->
                <div style="width: 20px;"></div>
                <?php
                if (isset($_SESSION['loggedin'])) {
                    echo "<div class='dropdown'>
                          <button class='btn btn-outline-light dropdown-toggle' type='button' id='dropdownMenuButton1' data-bs-toggle='dropdown' aria-expanded='false'>
                          $sessionname
                          </button>
                            <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton1'>
                                <li><a class='dropdown-item' href='acount/acount.php'>Profile</a></li>
                                <li><a class='dropdown-item' href='Post/Post.php'>Post</a></li>
                                <li><a class='dropdown-item' href='acount/logout.php'>Logout</a></li>
                                ";if ($sessionrank == "Admin") {
                        echo "<li><a class='dropdown-item' href='acount/Admin.php'>Admin</a></li>";
                    }"
                            </ul>
                        </div>";
                }
                ?>
            </div>
        </div>
    </nav>
<div class="container my-5" style="background-color: rgba(145,145,145,0.5); padding: 3em; border-radius: 1em; box-shadow: #69707a; color: white">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <h1 class="mb-3">Ask the Admin</h1>
            <p class="mb-5">If you want to be abel to post and show your collection ask the admin and send a picture of your prised possession!</p>
            <form method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="your-name" class="form-label">Your Name</label>
                        <input type="text" class="form-control" id="your-name" name="name" style="background: transparent;" required>
                    </div>
                    <div class="col-md-6">
                        <label for="your-email" class="form-label">Your Email</label>
                        <input type="email" class="form-control" id="your-email" name="mail" style="background: transparent;" required>
                    </div>
                    <div class="col-12">
                        <label for="your-message" class="form-label">Your Message</label>
                        <textarea class="form-control" id="your-message" name="message" rows="5" style="background: transparent;" required></textarea>
                    </div>
                    <!-- input for picture -->
                    <div class="col-12">
                        <label for="your-picture" class="form-label">Your reason</label>
                        <!-- dropdown -->
                        <select class="form-select" aria-label="Default select example" name="reden" style="background: transparent; color: white;">
                            <option selected>Open this select menu</option>
                            <option value="I want to post my collection">I want to post my collection</option>
                            <option value="I have a question/ bug">I have a question/ bug</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="row">
                                <button type="submit" class="btn btn-outline-light w-100 fw-bold" style="width: 100%" name="submit">Send</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</section>
<!-- bootstrap js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<!-- font awesome -->
<script src="https://kit.fontawesome.com/2a8f5c1a81.js" crossorigin="anonymous"></script>
</body>
</html>