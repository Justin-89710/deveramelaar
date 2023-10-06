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
    $sessionrank = "User";
} elseif ($sessionrank == 1) {
    $sessionrank = "Admin";
} elseif ($sessionrank == null) {
    $sessionrank = "Bezoeker";
} else {
    $sessionrank = "Error";
}

// get id from url
$id = $_GET['id'];

// get al info from post
$result = $db->query("SELECT * FROM Posts WHERE ID='$id'");
$row = $result->fetchArray();
$userid = $row['userID'];
$img1 = $row['afbeelding1'];
$img2 = $row['afbeelding2'];
$img3 = $row['afbeelding3'];
$img4 = $row['afbeelding4'];
$img5 = $row['afbeelding5'];
$title = $row['title'];
$info = $row['info'];
$merk = $row['merk'];
$model = $row['model'];
$bouwjaar = $row['bouwjaar'];
$kmstand = $row['kmstand'];
$kleur = $row['kleur'];
$vermogen = $row['vermogen'];
$category = $row['category'];

// convert category to text
if ($category == 1) {
    $category = "oldtimer";
} elseif ($category == 2) {
    $category = "sportcar";
} elseif ($category == 3) {
    $category = "SUV";
} elseif ($category == 4) {
    $category = "Supercar";
} elseif ($category == 5) {
    $category = "Hypercar";
} elseif ($category == 6) {
    $category = "musel car";
} elseif ($category == 7) {
    $category = "tuner car";
} elseif ($category == 8) {
    $category = "truks";
} elseif ($category == 9) {
    $category = "Anders";
} else {
    $category = "Error";
}

// get al info from user
$result = $db->query("SELECT * FROM Login WHERE ID='$userid'");
$row = $result->fetchArray();
$username = $row['username'];
$profielfoto = $row['profielfoto'];

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
    <title><?php echo $title ?> By <?php echo $username?></title>
    <style>
        .gradient-custom-2 {
            background: #1f2029;
        }
        .flex{
            display: flex;
            flex-wrap: wrap;
        }
        /* flex child*/
        .flex > *{
            flex: 1 1 50%;
        }
    </style>
    <link rel="stylesheet" href="../nav/nav.css">
    <link rel="icon" href="../afbeeldingen/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<section class="h-100" style="min-height: 100vh; background: #e7e7e7">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #1f2029;">
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
                <div class="search-results">
                    <div class="container">
                        <?php
                        if ($searchresult !== null) {
                            while ($searchrow = $searchresult->fetchArray()) {
                                $searchname = $searchrow['username'];
                                $searchprofilepic = $searchrow['profielfoto'];
                                $searchid = $searchrow['ID'];
                                ?>
                                <div class="profile-item">
                                    <a href="../acount/profile.php?id=<?php echo $searchid ?>" class="profile-name">
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
            <div class="col col-lg-9 col-xl-7" style="width: 95%">
                <div class="card">
                    <div class="rounded-top text-white d-flex flex-row" style="background-color: #1f2029; height:200px;">
                        <div class="ms-4 mt-5 d-flex flex-column" style="width: 150px;">
                            <img src="../afbeeldingen/<?php echo $profielfoto ?>"
                                 alt="Generic placeholder image" class="img-fluid img-thumbnail mt-4 mb-2"
                                 style="width: 150px; z-index: 1">
                        </div>
                        <div class="ms-3" style="margin-top: 130px;">
                            <a href="profile.php?id=<?php echo $userid ?>" style="color: white;">
                            <h5><?php echo $username?></h5>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-4 text-black" style="width: 100%">
                        <div class="mb-5">
                            <div class="p-4" style="background-color: #f8f9fa;">
                                <div class="row">
                                    <?php
                                    $images = array($img1, $img2, $img3, $img4, $img5);
                                    $numImages = count(array_filter($images)); // Count the non-null images

                                    // Determine the column width based on the number of images
                                    $colWidth = ($numImages > 1) ? "col-md-6" : "col-md-12";

                                    foreach ($images as $image) {
                                        if (!empty($image)) {
                                            echo "<div class='$colWidth'>
                            <img src='../afbeeldingen/$image' alt='Image' class='img-fluid mb-4 rounded' style='width: 100%; height: auto;'>
                        </div>";
                                        }
                                    }
                                    ?>
                                    <div class="col-md-12">
                                        <hr>
                                        <div class="col-md-6">
                                            <h1><?php echo $title ?></h1>
                                            <h4><?php echo $info ?></h4>
                                        </div>
                                    <div class="col-md-6">
                                        <p><b>Merk:</b> <?php echo $merk ?></p>
                                        <p><b>Model:</b> <?php echo $model ?></p>
                                        <p><b>Bouwjaar:</b> <?php echo $bouwjaar ?></p>
                                        <p><b>Km stand:</b> <?php echo $kmstand ?></p>
                                        <p><b>Kleur:</b> <?php echo $kleur ?></p>
                                        <p><b>Vermogen:</b> <?php echo $vermogen ?></p>
                                        <p><b>Categorie:</b> <?php echo $category ?></p>
                                    </div>
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
