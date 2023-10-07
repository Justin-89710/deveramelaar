<?php
// start session
session_start();

// connect to database
$db = new SQLite3('../database/database.db');

// check if connection is made
if (!$db) {
    die("Connection failed: " . $db->connect_error);
}

// show server side errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    // if user isn't logged in set these variables
    $sessionname = "Bezoeker";
    $sessionemail = null;
    $sessionprofielfoto = "default.png";
    $sessionbio = "Ik ben een bezoeker!";
    $sessionrank = null;
} elseif (isset($_SESSION['loggedin'])) {
    // if user is logged in set these variables
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

// search
$searchresult = null;
if (isset($_POST['searchbutton'])) {
    $search = $_POST['searchinput'];
    $searchquery = "SELECT * FROM Login WHERE username LIKE '%$search%'";
    $searchresult = $db->query($searchquery);
}

// get id from url
$id = $_GET['id'];

// change id into text
if ($id == 1) {
    $id = "Oldtimers";
} elseif ($id == 2) {
    $id = "Sports Car";
} elseif ($id == 3) {
    $id = "SUV";
} elseif ($id == 4) {
    $id = "Super Car";
} elseif ($id == 5) {
    $id = "Hyper Car";
} elseif ($id == 6) {
    $id = "Muscle car";
} elseif ($id == 7) {
    $id = "Tuner Car";
} elseif ($id == 8) {
    $id = "Trucks";
} elseif ($id == 9) {
    $id = "Other";
} else {
    $id = "Error";
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Category <?php echo $id ?></title>
    <link rel="icon" href="../afbeeldingen/logo.png">
    <link rel="stylesheet" href="../nav/nav.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<section class="h-100" style="min-height: 100vh;">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(145,145,145,0.5);">
        <div class="container-fluid">
            <a class="navbar-brand" href="../home/home.php">
                <img src="../afbeeldingen/logo.png" alt="" class="logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
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
    <!-- show category -->
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 style="text-align: center; margin-top: 5em;">Category: <?php echo $id ?></h1>
            </div>
            <!-- dropdown for other category's -->
            <div class="col-md-12">
                <div class="dropdown" style="text-align: center; margin-top: 2em;">
                    <button class="btn btn-outline-dark dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        Other Category's
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="category.php?id=1">Oldtimers</a></li>
                        <li><a class="dropdown-item" href="category.php?id=2">Sports Car</a></li>
                        <li><a class="dropdown-item" href="category.php?id=3">SUV</a></li>
                        <li><a class="dropdown-item" href="category.php?id=4">Super Car</a></li>
                        <li><a class="dropdown-item" href="category.php?id=5">Hyper Car</a></li>
                        <li><a class="dropdown-item" href="category.php?id=6">Muscle Car</a></li>
                        <li><a class="dropdown-item" href="category.php?id=7">Tuner Car</a></li>
                        <li><a class="dropdown-item" href="category.php?id=8">Trucks</a></li>
                        <li><a class="dropdown-item" href="category.php?id=9">Other</a></li>
                    </ul>
                </div>
        </div>
    </div>
    <?php
    // Get id from URL
    $newid = $_GET['id'];

    $result = $db->query("SELECT * FROM Posts WHERE category= '$newid'");
    $row = $result->fetchArray();
    // Check if there are any records in the result set
    if ($row) {
        ?>
        <!-- spacer of 50px -->
        <div style="height: 100px; width: 100%"></div>
        <div class="container">
            <div class="row">
                <?php
                $result = $db->query("SELECT * FROM Posts WHERE category= '$newid'");
                while ($row2 = $result->fetchArray()) {
                    $afbeelding1 = $row2['afbeelding1'];
                    $title = $row2['title'];
                    $info = $row2['info'];
                    $userID = $row2['userID'];
                    $postID = $row2['ID'];

                    // Get username from user ID
                    $userResult = $db->query("SELECT * FROM Login WHERE ID='$userID'");
                    $userRow = $userResult->fetchArray();
                    $username = $userRow['username'];
                    ?>
                    <div class="col-md-4">
                        <!-- Blog post -->
                        <div class="card mb-4">
                            <a href="../acount/inpost.php?id=<?php echo $postID ?>"><img style="padding: 1em; border-radius: 1em;" class="card-img-top" src="../afbeeldingen/<?php echo $afbeelding1 ?>" alt="..." /></a>
                            <div class="card-body">
                                <div class="small text-muted"><?php echo $username ?></div>
                                <h2 class="card-title h4"><?php echo $title ?></h2>
                                <p class="card-text"><?php echo $info ?></p>
                                <a class="btn btn-outline-dark" href="../acount/inpost.php?id=<?php echo $postID ?>">Read more →</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
    } else {
        echo "<h1 style='text-align: center; margin-top: 5em; color: red'>No posts found of that specific category</h1>";
    }
    ?>
</section>
<!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<!-- font awesome -->
<script src="https://kit.fontawesome.com/2a8f5c1a81.js" crossorigin="anonymous"></script>
</body>
</html>
