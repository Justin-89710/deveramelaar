<?php
session_start();

// get session info
$id = $_SESSION['id'];

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
    // send user to login page
    header("location: ../home/home.php");
} elseif (isset($_SESSION['loggedin'])) {
    // set session info if user is logged in
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

//get info from database
$result = $db->query("SELECT * FROM Login WHERE ID='$id'");
$row = $result->fetchArray();
$username = $row['username'];
$email = $row['email'];
$profielfoto = $row['profielfoto'];
$bio = $row['bio'];
$rank = $row['rank'];
$post = $row['posts'];

// change rank into text
if ($rank == 0) {
    $rank = "Verzamelaar";
} elseif ($rank == 1) {
    $rank = "Admin";
} else {
    $rank = "Error";
}

// submit new profile picture
if (isset($_POST['submitprofielfoto'])) {
    // get file info
    $file = $_FILES['file'];

    // get file info
    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $fileError = $_FILES['file']['error'];

    // get file extension
    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));

    // allowed file types
    $allowed = array('jpg', 'jpeg', 'png');

    // check if file is allowed
    if (in_array($fileActualExt, $allowed)) {
        // check if there is an error
        if ($fileError === 0) {
            // check if file is not too big
            if ($_FILES['file']['size'] < 10000000) {
                // give file a unique name
                $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                //delete old profile pic
                if ($profielfoto != "default.png") {
                    unlink("../afbeeldingen/" . $profielfoto);
                }
                // set file destination
                $fileDestination = '../afbeeldingen/' . $fileNameNew;
                // move file to destination
                move_uploaded_file($fileTmpName, $fileDestination);
                // update database
                $db->exec("UPDATE Login SET profielfoto = '$fileNameNew' WHERE ID = '$id'");
                header("Location: edit.php");
            } else {
                $error = "Bestand is te groot.";
            }
        } else {
            $error = "Er is een fout opgetreden.";
        }
    } else {
        $error = "Dit bestandstype is niet toegestaan.";
    }
}

// submit new username
if (isset($_POST['submitusername'])) {
    // get username
    $username = $_POST['username'];
    // update database
    $db->exec("UPDATE Login SET username = '$username' WHERE ID = '$id'");
    header("Location: edit.php");
}

// submit new email
if (isset($_POST['submitemail'])) {
    // get email
    $email = $_POST['email'];
    // update database
    $db->exec("UPDATE Login SET email = '$email' WHERE ID = '$id'");
    header("Location: edit.php");
}

// submit new bio
if (isset($_POST['submitbio'])) {
    // get bio
    $bio = $_POST['bio'];
    // update database
    $db->exec("UPDATE Login SET bio = '$bio' WHERE ID = '$id'");
    header("Location: edit.php");
}
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
    <title>Edit <?php echo $username?>'s Profile</title>
    <link rel="icon" href="../afbeeldingen/logo.png">
    <link rel="stylesheet" href="../nav/nav.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .img-account-profile {
            height: 10rem;
        }
        .rounded-circle {
            border-radius: 50% !important;
        }
        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgb(33 40 50 / 15%);
        }
        .card .card-header {
            font-weight: 500;
        }
        .card-header:first-child {
            border-radius: 0.35rem 0.35rem 0 0;
        }
        .card-header {
            padding: 1rem 1.35rem;
            margin-bottom: 0;
            background-color: #1f2029;
            border-bottom: 1px solid rgba(33, 40, 50, 0.125);
            color: white;
        }
        .form-control2, .dataTable-input {
            display: block;
            width: 100%;
            padding: 0.875rem 1.125rem;
            font-size: 0.875rem;
            font-weight: 400;
            line-height: 1;
            color: #69707a;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #c5ccd6;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border-radius: 0.35rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
    </style>
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
<div class="container-xl px-4 mt-4">
    <hr class="mt-0 mb-4">
    <div class="row">
        <div class="col-xl-4">
            <!-- Profile picture card-->
            <div class="card mb-4 mb-xl-0" style="background-color: transparent;">
                <div class="card-header" style="background-color: rgba(145, 145, 145, 0.5);">Profile Picture</div>
                <div class="card-body text-center" style="background: white;">
                    <!-- Profile picture image-->
                    <img class="img-account-profile rounded-circle mb-2" src="../afbeeldingen/<?php echo $profielfoto?>" alt="">
                    <!-- Profile picture help block-->
                    <div class="small font-italic text-muted mb-4">JPG or PNG or GIF than 5 MB</div>
                    <!-- Profile picture upload button-->
                    <form method="post" enctype="multipart/form-data">
                        <!-- input form without the basic html style -->
                        <input type="file" name="file" class="form-control">
                        <br>
                        <button class="btn btn-outline-dark" type="submit" name="submitprofielfoto">Upload new image</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <!-- Account details card-->
            <div class="card mb-4" style="background: transparent;">
                <div class="card-header" style="background-color: rgba(145, 145, 145, 0.5);">Account Details</div>
                <div class="card-body" style="background: #FFFFFF;">
                    <form method="post">
                        <!-- Form Group (username)-->
                        <div class="mb-3">
                            <label class="small mb-1" for="inputUsername">Username</label>
                            <input name="username" class="form-control2" id="inputUsername" type="text" placeholder="Enter your username">
                            <!-- show username -->
                            <p class="small mb-1">Your username is: <?php echo $username?></p>
                            <?php
                            // show when username is changed
                            if (isset($error)) {
                                echo "<div class='alert alert-danger' role='alert'>" . $error . "</div>";
                            }
                            ?>
                            <input type="submit" value="submit" name="submitusername" class="btn btn-outline-dark mt-4">
                        </div>
                        <!-- Form Group (email address)-->
                        <form method="post">
                        <div class="mb-3">
                            <label class="small mb-1" for="inputEmailAddress">Email address</label>
                            <input class="form-control2" id="inputEmailAddress" type="email" placeholder="Enter your email address" name="email">
                            <!-- show email -->
                            <p class="small mb-1" for="inputEmailAddress">Your email is: <?php echo $email?></p>
                            <input type="submit" value="submit" name="submitemail" class="btn btn-outline-dark mt-4">
                        </div>
                        </form>
                        <!-- Form Group (change Bio)-->
                        <form method="post">
                        <div class="mb-3">
                            <label class="small mb-1" for="inputBio">Bio</label>
                            <input name="bio" class="form-control2" id="inputBio" type="text" placeholder="Enter your bio">
                            <!-- show bio -->
                            <p class="small mb-1" for="inputBio">Your bio is: <?php echo $bio?></p>
                            <input type="submit" value="submit" name="submitbio" class="btn btn-outline-dark mt-4">
                        </div>
                        </form>
                        <!-- show rank -->
                        <p class="small mb-1">Your rank is: <?php echo $rank?></p>
                        <br><br>
                        <!-- button for password reset -->
                        <div class="small mb-1">Reset Password</div>
                        <a href="../Login/forgetpassword.php" style="z-index: 1;">
                            <button type="button" class="btn btn-outline-dark" data-mdb-ripple-color="dark"
                                    style="z-index: 1;">
                                Reset Password
                            </button>
                        </a>
                        <?php
                        if ($rank === "Admin") {
                            ?>
                            <br><br>
                            <!-- button for admin panel -->
                            <div class="small mb-1">Admin Panel</div>
                            <a href="../acount/Admin.php" style="z-index: 1;">
                                <button type="button" class="btn btn-outline-dark" data-mdb-ripple-color="dark"
                                        style="z-index: 1;">
                                    Admin Panel
                                </button>
                            </a>
                            <?php
                        }
                        ?>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="https://kit.fontawesome.com/2a8f5c1a81.js" crossorigin="anonymous"></script>
</body>
</html>
