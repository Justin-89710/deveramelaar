<?php
//Start Login Session
session_start();

//Get the user id from session
$id = $_SESSION['id'];

//Connect to database
$db = new SQLite3('../database/database.db');

//Check if database connection is successful
if (!$db) {
    die("Connection failed: " . $db->connect_error);
}

//Show Errors that happen in the server
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

//Check if user is logged in and set all the session variables
if (!isset($_SESSION['loggedin'])) {
    //If user is not logged in send user to login page
    header("location: ../home/home.php");
} elseif (isset($_SESSION['loggedin'])) {
    //get info for the account page from database
    $result = $db->query("SELECT * FROM Login WHERE ID='$id'");
    $row = $result->fetchArray();
    $username = $row['username'];
    $email = $row['email'];
    $profielfoto = $row['profielfoto'];
    $bio = $row['bio'];
    $rank = $row['rank'];
    $post = $row['posts'];
} else {
    //if there is something wrong show wats wrong in the variable error
    $error = error_log();
}

// change rank into text
if ($rank == 0) {
    //if rank is 0 show that the user is a Collector
    $rank = "Collector";
} elseif ($rank == 1) {
    //if rank is 1 show that the user is an Admin
    $rank = "Admin";
} else {
    //if rank is none of the above show that the user is a Visitor
    $rank = "Visitor";
}

// search function
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
    <title><?php echo $username?>'s Account</title>
    <!-- Icon Logo for web -->
    <link rel="icon" href="../afbeeldingen/logo.png">
    <!-- Nav style sheet -->
    <link rel="stylesheet" href="../nav/nav.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<!-- section for the page -->
<section class="h-100 gradient-custom-2" style="min-height: 100vh">
    <!-- Nav -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(145,145,145,0.5);">
        <div class="container-fluid">
            <a class="navbar-brand" href="../home/home.php">
                <!-- logo -->
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
                <!-- Search form -->
                <form method="post" class="d-flex">
                    <input type="text" class="form-control me-2" name="searchinput" placeholder="Search">
                    <button type="submit" class="btn btn-outline-light" name="searchbutton">Search</button>
                </form>
                <!-- Search Results -->
                <div class="search-results" style="background-color: rgba(145,145,145,0.5);">
                    <div class="container">
                        <?php
                        if ($searchresult !== null) { // if result is not empty
                            while ($searchrow = $searchresult->fetchArray()) {
                                $searchname = $searchrow['username']; // get name of person you are searching for
                                $searchprofilepic = $searchrow['profielfoto']; // get profile picture of user you're searching for
                                $searchid = $searchrow['ID']; // get id of person you're searching for
                                ?>
                                <div class="profile-item">
                                    <a class="hover" href="../acount/profile.php?id=<?php echo $searchid ?>" class="profile-name">
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
                <!-- dropdown for when logged in -->
                <?php
                // check if user is logged in
                if (isset($_SESSION['loggedin'])) {
                    // if user is logged in show dropdown
                    echo "<div class='dropdown'>
                          <button class='btn btn-outline-light dropdown-toggle' type='button' id='dropdownMenuButton1' data-bs-toggle='dropdown' aria-expanded='false'>
                          $username
                          </button>
                            <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton1'>
                                <li><a class='dropdown-item' href='../acount/acount.php'>Profile</a></li>
                                <li><a class='dropdown-item' href='../Post/Post.php'>Post</a></li>
                                <li><a class='dropdown-item' href='../acount/logout.php'>Logout</a></li>
                                ";
                    // if rank is admin show admin button
                    if ($rank == "Admin") {
                        echo "<li><a class='dropdown-item' href='../acount/Admin.php'>Admin</a></li>";
                    }"
                            </ul>
                        </div>";
                }
                ?>
            </div>
        </div>
    </nav>
    <!-- end nav -->

    <!-- profile page -->
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-lg-9 col-xl-7">
                <div class="card" style="background-color: transparent; border-bottom-left-radius: 1em; border-bottom-right-radius: 1em;">
                    <div class="rounded-top text-white d-flex flex-row" style="background-color: rgba(145,145,145,0.5); height:200px;">
                        <div class="ms-4 mt-5 d-flex flex-column" style="width: 150px;">
                            <img src="../afbeeldingen/<?php echo $profielfoto ?>"
                                 alt="Generic placeholder image" class="img-fluid img-thumbnail mt-4 mb-2"
                                 style="width: 150px; z-index: 1">
                            <a href="../acount/edit.php" style="z-index: 1;">
                            <button type="button" class="btn btn-outline-dark" data-mdb-ripple-color="dark"
                                    style="z-index: 1;">
                                    Edit profile
                            </button>
                            </a>
                        </div>
                        <div class="ms-3" style="margin-top: 130px;">
                            <h5><?php echo $username?></h5>
                            <p><?php echo $rank ?></p>
                        </div>
                    </div>
                    <div class="p-4 text-black" style="background-color: #f8f9fa;">
                        <div class="d-flex justify-content-end text-center py-1">
                            <div>
                                <p class="mb-1 h5"><?php echo $post ?></p>
                                <p class="small text-muted mb-0">Photos</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 text-black" style="background-color: white; border-bottom-left-radius: 1em; border-bottom-right-radius: 1em;">
                        <div class="mb-5">
                            <p class="lead fw-normal mb-1">About</p>
                            <div class="p-4" style="background-color: #f8f9fa;">
                                <p class="font-italic mb-1"><?php echo $bio ?></p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <p class="lead fw-normal mb-0">Recent photos</p>
                            <p class="mb-0"><a href="Posts.php?id=<?php echo $id?>" class="text-muted hover">Show all</a></p>
                        </div>
                        <div class="container">
                        <div class="row">
                            <?php
                            // get the 4 most recent posts from the user
                            $result = $db->query("SELECT * FROM Posts WHERE userID='$id' ORDER BY ID DESC LIMIT 4");
                            while ($row = $result->fetchArray()) {
                                $title = $row['title'];
                                $info = $row['info'];
                                $afbeelding1 = $row['afbeelding1'];
                                $carid = $row['ID'];
                                ?>
                            <div class="col-md-4">
                            <div class="card mb-4" style="max-height: 350px; min-height: 350px; overflow: hidden;">
                                <a href="../acount/inpost.php?id=<?php echo $carid ?>"><img style="padding: 1em; border-radius: 1em; max-height: 100%;" class="card-img-top" src="../afbeeldingen/<?php echo $afbeelding1 ?>" alt="..." /></a>
                                <div class="card-body" style="height: 150px; overflow: auto;">
                                    <div class="small text-muted"><?php echo $username ?></div>
                                    <h2 class="card-title h4"><?php echo $title ?></h2>
                                    <p class="card-text"><?php echo $info ?></p>
                                    <a class="btn btn-outline-dark" href="../acount/inpost.php?id=<?php echo $carid ?>">Read more →</a>
                                </div>
                            </div>
                            </div>
                            <?php
                            }
                            ?>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- bootstrap script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<!-- fonts script -->
<script src="https://kit.fontawesome.com/2a8f5c1a81.js" crossorigin="anonymous"></script>
<!-- nav script -->
<script src="../nav/nav.js"></script>
</body>
</html>
