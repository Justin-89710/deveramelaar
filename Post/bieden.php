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

// check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    // set user variables if user is not logged in
    $sessionname = "Visitor";
    $sessionemail = null;
    $sessionprofielfoto = "default.png";
    $sessionbio = "I am a visitor!";
    $sessionrank = null;
} elseif (isset($_SESSION['loggedin'])) {
    // set user variables if user is logged in
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
    $sessionrank = "Collector";
} elseif ($sessionrank == 1) {
    $sessionrank = "Admin";
} elseif ($sessionrank == null) {
    $sessionrank = "Visitor";
} else {
    $sessionrank = "Error";
}

// get id from url
$id = $_GET['id'];

//search
$searchresult = null;
if (isset($_POST['searchbutton'])) {
    $search = $_POST['searchinput'];
    $searchquery = "SELECT * FROM Login WHERE username LIKE '%$search%'";
    $searchresult = $db->query($searchquery);
}
?>

<!doctype html>
<html lang="en" class="gradient-custom-2">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bieden</title>
    <link rel="icon" href="../afbeeldingen/logo.png">
    <!-- bootstrap css -->
    <link rel="stylesheet" href="../nav/nav.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<section class="h-100 gradient-custom-2" style="min-height: 100vh;">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(145,145,145,0.5);">
        <div class="container-fluid">
            <a class="navbar-brand" href="../home/home.php">
                <img src="../afbeeldingen/logo.png" alt="" class="logo">
            </a>
            <button class="navbar-toggler first-button" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <div class="animated-icon1"><span></span><span></span><span></span></div>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="../home/home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../askadmin.php">Ask The Admin</a>
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
                                    <a href="../acount/profile.php?id=<?php echo $searchid ?>" class="profile-name hover">
                                        <img src="../afbeeldingen/<?php echo $searchprofilepic ?>" alt="Profile Picture" class="profile-picture">
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
                                <li><a class='dropdown-item' href='../acount/acount.php'>Profile</a></li>
                                <li><a class='dropdown-item' href='../Post/Post.php'>Post</a></li>
                                <li><a class='dropdown-item' href='../acount/logout.php'>Logout</a></li>
                                ";if ($sessionrank == "Admin") {
                        echo "<li><a class='dropdown-item' href='../acount/Admin.php'>Admin</a></li>";
                    }"
                            </ul>
                        </div>";
                }
                ?>
            </div>
        </div>
    </nav>
    <div class="container my-5" style="background-color: rgba(145,145,145,0.5); padding: 3em; border-radius: 1em; box-shadow: #69707a; color: black;">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <?php
                // get id from url
                $id = $_GET['id'];
                // get info from the post with the id
                $result = $db->query("SELECT * FROM Posts WHERE ID='$id'");
                $row = $result->fetchArray();
                $title = $row['title'];
                $userid = $row['userID'];

                // put info form the form in db in tabel bestellingen
                if (isset($_POST['submit'])) {
                    $name = $_POST['name'];
                    $price = $_POST['price'];
                    $message = $_POST['message'];
                    $db->exec("INSERT INTO Bestellingen (unb, price, message, postID) VALUES ('$name', '$price', '$message', '$id')");

                    // get mail from userid
                    $result = $db->query("SELECT * FROM Login WHERE ID='$userid'");
                    $row = $result->fetchArray();
                    $email = $row['email'];

                    // send mail
                    $to = $email;
                    $subject = "New bid on $title";
                    $msg = "You have a new bid on $title";
                    $body = "From: $name\n Message: $message\n Price: $price";
                    mail($to, $subject, $body);
                    header("Location: ../home/home.php");
                }
                ?>
                <h1 class="mb-3" style="color: white;">Bid!</h1>
                <p class="mb-5" style="color: white;">Bid on <?php echo $title ?></p>
                <form method="post" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="your-name" class="form-label" style="color: white;">Your full name</label>
                            <input type="text" class="form-control" id="your-name" name="name" style="background: transparent;" required>
                        </div>
                        <div class="col-md-6">
                            <label for="your-email" class="form-label" style="color: white;">Your price</label>
                            <input type="text" class="form-control" id="your-email" name="price" style="background: transparent;" required>
                        </div>
                        <div class="col-12">
                            <label for="your-message" class="form-label" style="color: white;">Message</label>
                            <textarea class="form-control" id="your-message" name="message" rows="5" style="background: transparent;" required></textarea>
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
<!-- nav script -->
<script src="../nav/nav.js"></script>
</body>
</html>