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

// check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    // set user info if user is not logged in
    $sessionname = "Bezoeker";
    $sessionemail = null;
    $sessionprofielfoto = "default.png";
    $sessionbio = "Ik ben een bezoeker!";
    $sessionrank = null;
    $sesionid = null;
} elseif (isset($_SESSION['loggedin'])) {
    // set user info if user is logged in
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
    <title><?php echo $title ?> By <?php echo $username?></title>
    <link rel="stylesheet" href="../nav/nav.css">
    <style>
        .custom-enlarged-image {
            width: 100%; /* Expand to 100% width */
            height: auto; /* Maintain aspect ratio */
            max-height: none; /* Remove max height */
        }
    </style>
    <link rel="icon" href="../afbeeldingen/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<section class="h-100" style="min-height: 100vh;">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(145, 145, 145, 0.5);">
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
                <div class="search-results" style="background-color: rgba(145, 145, 145, 0.5);">
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
            <div class="col col-lg-9 col-xl-7" style="width: 95%">
                <div class="card" style="background-color: rgba(145, 145, 145, 0.5);">
                    <div class="rounded-top text-white d-flex flex-row" style="background-color: rgba(145, 145, 145, 0.5); height:200px;">
                        <div class="ms-4 mt-5 d-flex flex-column" style="width: 150px;">
                            <img src="../afbeeldingen/<?php echo $profielfoto ?>"
                                 alt="Generic placeholder image" class="img-fluid img-thumbnail mt-4 mb-2"
                                 style="width: 150px; z-index: 1">
                        </div>
                        <div class="ms-3" style="margin-top: 130px;">
                            <a href="profile.php?id=<?php echo $userid ?>" style="color: white;" class="hover">
                            <h5><?php echo $username?></h5>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-4 text-black" style="width: 100%; background-color: rgba(145, 145, 145, 0.5)">
                        <div class="mb-5">
                            <div class="p-4" style="background-color: rgba(145, 145, 145, 0.5);">
                                <div class="row">
                                    <!-- Create a Bootstrap Carousel -->
                                    <div id="imageSlider" class="carousel slide" data-ride="carousel">
                                        <div class="carousel-inner">
                                            <?php
                                            $images = array($img1, $img2, $img3, $img4, $img5);
                                            $numImages = count(array_filter($images)); // Count the non-null images

                                            foreach ($images as $index => $image) {
                                                if (!empty($image)) {
                                                    // Determine if it's the first image, and add the 'active' class accordingly
                                                    $activeClass = ($index === 0) ? 'active' : '';
                                                    ?>
                                                    <div class="carousel-item <?php echo $activeClass; ?>">
                                                        <a href="#" data-toggle="modal" data-target="#imageModal<?php echo $index; ?>">
                                                            <img src="../afbeeldingen/<?php echo $image; ?>" alt="Image" class="d-block w-100 custom-image">
                                                        </a>
                                                    </div>

                                                    <!-- Modal for Enlarged Image -->
                                                    <div class="modal fade" id="imageModal<?php echo $index; ?>" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-body">
                                                                    <img src="../afbeeldingen/<?php echo $image; ?>" alt="Image" class="img-fluid custom-enlarged-image">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>

                                        <!-- Add navigation buttons -->
                                        <a class="carousel-control-prev" href="#imageSlider" role="button" data-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="sr-only"></span>
                                        </a>
                                        <a class="carousel-control-next" href="#imageSlider" role="button" data-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="sr-only"></span>
                                        </a>
                                    </div>
                                    <div class="col-md-12" style="background-color: rgba(145,145,145,1); color: white;">
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
                            <!-- button to Bidden page -->
                            <?php
                            // get besteld from Posts in db
                            $result = $db->query("SELECT * FROM Posts WHERE ID='$id'");
                            $row = $result->fetchArray();
                            $besteld = $row['besteld'];
                            // check if besteld is 1
                            if ($besteld == 1) {
                                echo "<div class='row'>
                                <div class='col-md-12'>
                                    <h1 style='color: red; text-align: center;'>Deze auto is al verkocht!</h1>
                                </div>
                            </div>";
                            } else {
                                echo "<div class='row'>
                                <div class='col-md-12'>
                                    <a href='../Post/bieden.php?id=$id'><button class='btn btn-outline-light' style='width: 100%; margin-top: 10px;'>Bieden</button></a>
                                </div>
                            </div>";
                            }
                            ?>
                            <?php
                            // check if this is your post
                            if ($userid == $sesionid) {
                                echo "<div class='row'>
                                <div class='col-md-12'>
                                   <!-- button for putting it on sold -->
                                    <a href='../Post/sold.php?id=$id'><button class='btn btn-outline-light' style='width: 100%; margin-top: 10px;'>Sold</button></a>
                                </div>
                            </div>";
                            ?>
                            <?php
                            } else {
                                echo "";
                            }
                            ?>
                    </div>
                </div>
        </div>
    </div>
</section>
<!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<!-- font awesome -->
<script src="https://kit.fontawesome.com/2a8f5c1a81.js" crossorigin="anonymous"></script>
<script>
    // Initialize the Bootstrap Carousel
    $(document).ready(function () {
        $('#imageSlider').carousel();
    });
</script>
</body>
</html>
