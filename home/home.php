<?php
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

if (!isset($_SESSION['loggedin'])) {
    $sessionname = "Bezoeker";
    $sessionemail = null;
    $sessionprofielfoto = "default.png";
    $sessionbio = "Ik ben een bezoeker!";
    $sessionrank = null;
} elseif (isset($_SESSION['loggedin'])) {
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

// ceck if user is logged in
if (!isset($_SESSION['loggedin'])) {
    $sessionrank = null;
} else {
    $sessionrank = $_SESSION['rank'];
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

$searchresult = null;
if (isset($_POST['searchbutton'])) {
    $search = $_POST['searchinput'];
    $searchquery = "SELECT * FROM Login WHERE username LIKE '%$search%'";
    $searchresult = $db->query($searchquery);
}

// get al Posts
$result = $db->query("SELECT * FROM Posts");
// count how many posts there are
$numberofposts = 0;
while ($row = $result->fetchArray()) {
    $numberofposts++;
}
// randamize a number
$randomnumber = rand(1, $numberofposts);
// get the post with the random number
$result = $db->query("SELECT * FROM Posts WHERE ID='$randomnumber'");
$row = $result->fetchArray();
$mainposttitle = $row['title'];
$mainposttext = $row['info'];
$mainpostimage = $row['afbeelding1'];
$mainpostuserID = $row['userID'];

// get username from user ID
$result = $db->query("SELECT * FROM Login WHERE ID='$mainpostuserID'");
$row = $result->fetchArray();
$mainpostusername = $row['username'];
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home</title>
    <link rel="stylesheet" href="../nav/nav.css">
    <link rel="icon" href="../afbeeldingen/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<section class="h-100">
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
    <div style="height: 75px;"></div>
    <div class="container">
        <div class="row">
            <!-- Blog entries-->
            <div class="col-lg-8" style="background-color: rgba(145,145,145,0.7); padding: 2em; border-radius: 2em; margin-bottom: 1em;">
                <!-- Featured blog post-->
                <div class="card mb-4">
                    <a href="../acount/inpost.php?id=<?php echo $randomnumber ?>"><img style="padding: 1em; border-radius: 1em;" class="card-img-top" src="../afbeeldingen/<?php echo $mainpostimage ?>" alt="..." /></a>
                    <div class="card-body">
                        <div class="small text-muted"><?php echo $mainpostusername ?></div>
                        <h2 class="card-title"><?php echo $mainposttitle ?></h2>
                        <p class="card-text"><?php echo $mainposttext ?></p>
                        <a class="btn btn-outline-dark" href="../acount/inpost.php?id=<?php echo $randomnumber ?>">Read more →</a>
                    </div>
                </div>
                <!-- Nested row for non-featured blog posts-->
                <div class="row">
                    <!-- First Column -->
                    <div class="col-lg-6">
                        <?php
                        $result = $db->query("SELECT * FROM Posts ORDER BY ID DESC");

                        // Display the first column of posts
                        $counter = 0; // Initialize a counter to keep track of the number of posts displayed
                        while ($row = $result->fetchArray()) {
                            if ($counter % 2 == 0) { // Check if it's an even-numbered post
                                $afbeelding1 = $row['afbeelding1'];
                                $title = $row['title'];
                                $info = $row['info'];
                                $userID = $row['userID'];
                                $postID = $row['ID'];

                                // get username from user ID
                                $userResult = $db->query("SELECT * FROM Login WHERE ID='$userID'");
                                $userRow = $userResult->fetchArray();
                                $username = $userRow['username'];
                                ?>
                                <div class="card mb-4" style="overflow: hidden;">
                                    <a href="../acount/inpost.php?id=<?php echo $postID ?>"><img style="padding: 1em; border-radius: 1em; max-height: 100%;" class="card-img-top" src="../afbeeldingen/<?php echo $afbeelding1 ?>" alt="..." /></a>
                                    <div class="card-body" style="height: 150px; overflow: auto;">
                                        <div class="small text-muted"><?php echo $username ?></div>
                                        <h2 class="card-title h4"><?php echo $title ?></h2>
                                        <p class="card-text"><?php echo $info ?></p>
                                    </div>
                                </div>
                                <?php
                            }
                            $counter++; // Increment the counter
                        }
                        ?>
                    </div>

                    <!-- Second Column -->
                    <div class="col-lg-6">
                        <?php
                        // Reset the result set pointer to the beginning
                        $result->reset();

                        // Display the second column of posts
                        $counter = 0; // Reset the counter
                        while ($row = $result->fetchArray()) {
                            if ($counter % 2 == 1) { // Check if it's an odd-numbered post
                                $afbeelding1 = $row['afbeelding1'];
                                $title = $row['title'];
                                $info = $row['info'];
                                $userID = $row['userID'];
                                $postID = $row['ID'];

                                // get username from user ID
                                $userResult = $db->query("SELECT * FROM Login WHERE ID='$userID'");
                                $userRow = $userResult->fetchArray();
                                $username = $userRow['username'];
                                ?>
                                <div class="card mb-4" style="overflow: hidden;">
                                    <a href="../acount/inpost.php?id=<?php echo $postID ?>"><img style="padding: 1em; border-radius: 1em; max-height: 100%;" class="card-img-top" src="../afbeeldingen/<?php echo $afbeelding1 ?>" alt="..." /></a>
                                    <div class="card-body" style="height: 150px; overflow: auto;">
                                        <div class="small text-muted"><?php echo $username ?></div>
                                        <h2 class="card-title h4"><?php echo $title ?></h2>
                                        <p class="card-text"><?php echo $info ?></p>
                                    </div>
                                </div>
                                <?php
                            }
                            $counter++; // Increment the counter
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Search widget -->
            <div class="col-lg-4">
                <div class="card mb-4" style="display: flex; align-content: center; justify-content: center; background-color: rgba(145,145,145,1); border-bottom-left-radius: 1em; border-bottom-right-radius: 1em;"">
                    <div class="card-header">Search</div>
                    <div class="card-body" style=" background-color: white; border-bottom-left-radius: 1em; border-bottom-right-radius: 1em;"">
                        <form method="GET" action="">
                            <div class="input-group">
                                <input class="form-control" type="text" name="search_term" placeholder="Enter name of car or brand..." aria-label="Enter search term..." aria-describedby="button-search" />
                                <button class="btn btn-outline-dark" id="button-search" type="submit">Go!</button>
                            </div>
                        </form>
                    </div>

                    <!-- Display search results with a scrollable section -->
                    <div class="col-lg-8" style="max-height: 400px; overflow-y: scroll; width: 100%;">
                        <?php
                        if(isset($_GET['search_term'])) {
                            $search_term = $_GET['search_term'];
                            $search_query = "SELECT * FROM Posts WHERE title LIKE '%$search_term%' OR merk LIKE '%$search_term%'";
                            $search_result = $db->query($search_query);

                            if($search_result) {
                                while ($row = $search_result->fetchArray()) {
                                    // Display search results
                                    $afbeelding1 = $row['afbeelding1'];
                                    $title = $row['title'];
                                    $info = $row['info'];
                                    $userID = $row['userID'];
                                    $postID = $row['ID'];

                                    // get username from user ID
                                    $userResult = $db->query("SELECT * FROM Login WHERE ID='$userID'");
                                    $userRow = $userResult->fetchArray();
                                    $username = $userRow['username'];
                                    ?>
                                    <!-- Blog post -->
                                    <div class="card mb-4">
                                        <a href="../acount/inpost.php?id=<?php echo $postID ?>"><img class="card-img-top" src="../afbeeldingen/<?php echo $afbeelding1 ?>" alt="..." /></a>
                                        <div class="card-body">
                                            <div class="small text-muted"><?php echo $username ?></div>
                                            <h2 class="card-title h4"><?php echo $title ?></h2>
                                            <p class="card-text"><?php echo $info ?></p>
                                            <a class="btn btn-outline-dark" href="../acount/inpost.php?id=<?php echo $postID ?>">Read more →</a>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo "No results found.";
                            }
                        }
                        ?>
                    </div>

        </div>
                <!-- Categories widget-->
                <div class="card mb-4" style="background-color: rgba(145,145,145,1); border-bottom-left-radius: 1em; border-bottom-right-radius: 1em;"">
                    <div class="card-header">Categories</div>
                    <div class="card-body" style="border-bottom-left-radius: 1em; border-bottom-right-radius: 1em; background-color: white;">
                        <div class="row">
                            <div class="col-sm-6">
                                <ul class="list-unstyled mb-0">
                                    <li><a style="color: black; text-decoration: none" class="hover" href="category.php?id=1">Old Timer</a></li>
                                    <li><a style="color: black; text-decoration: none" class="hover" href="category.php?id=2">Sports Car</a></li>
                                    <li><a style="color: black; text-decoration: none" class="hover" href="category.php?id=3">SUV</a></li>
                                </ul>
                            </div>
                            <div class="col-sm-6">
                                <ul class="list-unstyled mb-0">
                                    <li><a style="color: black; text-decoration: none" class="hover" href="category.php?id=4">Super Car</a></li>
                                    <li><a style="color: black; text-decoration: none" class="hover" href="category.php?id=5">Hyper Car</a></li>
                                    <li><a style="color: black; text-decoration: none" class="hover" href="category.php?id=6">Muscle Car</a></li>
                                </ul>
                            </div>
                            <div class="col-sm-6">
                                <ul class="list-unstyled mb-0">
                                    <li><a style="color: black; text-decoration: none" class="hover" href="category.php?id=7">Tuner Car</a></li>
                                    <li><a style="color: black; text-decoration: none" class="hover" href="category.php?id=8">Trucks</a></li>
                                    <li><a style="color: black; text-decoration: none" class="hover" href="category.php?id=9">Other</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
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
