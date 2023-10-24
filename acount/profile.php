<?php
//start session
session_start();

//connect to db
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
    // set session variables if user is not logged in
    $sessionname = "Visitor";
    $sessionemail = null;
    $sessionprofielfoto = "default.png";
    $sessionbio = "I am a visitor!";
    $sessionrank = null;
} elseif (isset($_SESSION['loggedin'])) {
    // set session variables if user is logged in
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

// get posts and name and rank and profile picture form user
$result = $db->query("SELECT * FROM Login WHERE ID='$id'");
$row = $result->fetchArray();
$username = $row['username'];
$email = $row['email'];
$profielfoto = $row['profielfoto'];
$bio = $row['bio'];
$rank = $row['rank'];
$post = $row['posts'];

// check if user has posts
if ($post === null){
    $post = 0;
}

// change rank into text
if ($rank == 0) {
    $rank = "Collector";
} elseif ($rank == 1) {
    $rank = "Admin";
} elseif ($rank == null){
    $rank = "Visitor";
} else {
    $rank = "Error";
}

// search for users
$searchresult = null;
if (isset($_POST['searchbutton'])) {
    $search = $_POST['searchinput'];
    $searchquery = "SELECT * FROM Login WHERE username LIKE '%$search%'";
    $searchresult = $db->query($searchquery);
}

// get al users posts
$result = $db->query("SELECT * FROM Posts WHERE userID='$id' ORDER BY ID DESC LIMIT 4");
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $username?></title>
    <!-- web icon -->
    <link rel="icon" href="../afbeeldingen/logo.png">
    <!-- Nav CSS -->
    <link rel="stylesheet" href="../nav/nav.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<section class="h-100" style="min-height: 100vh;">
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
                <div class="search-results" style="background-color: rgba(145,145,145,0.5);">
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
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-lg-9 col-xl-7">
                <div class="card">
                    <div class="rounded-top text-white d-flex flex-row" style="background-color: rgba(145,145,145,0.5); height:200px;">
                        <div class="ms-4 mt-5 d-flex flex-column" style="width: 150px;">
                            <img src="../afbeeldingen/<?php echo $profielfoto ?>"
                                 alt="Generic placeholder image" class="img-fluid img-thumbnail mt-4 mb-2"
                                 style="width: 150px; z-index: 1">
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
                                <p class="small text-muted mb-0">Cars</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 text-black">
                        <div class="mb-5">
                            <p class="lead fw-normal mb-1">About</p>
                            <div class="p-4" style="background-color: #f8f9fa;">
                                <p class="font-italic mb-1"><?php echo $bio ?></p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <p class="lead fw-normal mb-0">Recent photos</p>
                            <p class="mb-0"><a href="Posts.php?id=<?php echo $id ?>" class="text-muted hover">Show all</a></p>
                        </div>
                        <!-- show the 4 most recent posts user-->
                        <div class="row">
                            <?php
                            while ($row = $result->fetchArray()) {
                                $carid = $row['ID'];
                                echo "<div class='col-lg-3 col-md-6 mb-4 mb-lg-0' style='width: 50%'>
                                      <div class='card rounded shadow-sm border-0'>
                                      <div class='card-body p-4'>
                                      <a href='inpost.php?id=$carid' style='color: black; text-decoration: none;'>
                                      <img src='../afbeeldingen/" . $row['afbeelding1'] . "' alt='' class='img-fluid d-block mx-auto mb-3' style='width: 100%;'>
                                      <h5>" . $row['title'] . "</h5>
                                      <p class='small text-muted font-italic'>
                                      " . $row['info'] . "
                                      </p>
                                  </a>
                            </div>
                       </div>
                </div>";
                            }
                            ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<!-- Font Awesome JS -->
<script src="https://kit.fontawesome.com/2a8f5c1a81.js" crossorigin="anonymous"></script>
<!-- nav script -->
<script src="../nav/nav.js"></script>
</body>
</html>
