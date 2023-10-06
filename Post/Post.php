<?php
session_start();

$db = new SQLite3('../database/database.db');

if (!$db) {
    die("Connection failed: " . $db->connect_error);
}

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

if (isset($_POST['submit'])) {
    $id = $_SESSION['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $userID = $_SESSION['id'];
    $merk = $_POST['merk'];
    $model = $_POST['model'];
    $bouwjaar = $_POST['bouwjaar'];
    $kmstand = $_POST['kmstand'];
    $kleur = $_POST['kleur'];
    $vermogen = $_POST['vermogen'];
    $category = $_POST['category'];

    // Define an array to store the uploaded image filenames
    $imageNames = array();

    // Maximum file size (5MB)
    $maxsize = 5242880;

    // compres images
    function compressImage($source, $destination, $quality)
    {
        // Check if the source file exists
        if (!file_exists($source)) {
            return false;
        }

        $info = getimagesize($source);
        $mime = $info['mime'];

        if ($mime == 'image/jpeg' || $mime == 'image/jpg' || $mime == 'image/png') {
            // Create an image resource based on the MIME type
            if ($mime == 'image/jpeg' || $mime == 'image/jpg') {
                $image = imagecreatefromjpeg($source);
            } elseif ($mime == 'image/png') {
                $image = imagecreatefrompng($source);
            }

            // Attempt to compress and save the image as JPEG
            if (imagejpeg($image, $destination, $quality)) {
                return $destination;
            } else {
                return false; // Failed to compress and save the image
            }
        } else {
            return false; // Unsupported image type
        }
    }




    for ($i = 1; $i <= 5; $i++) {
        $imageKey = 'afbeelding' . $i;
        $imageName = $_FILES[$imageKey]['name'];
        $image_tmp = $_FILES[$imageKey]['tmp_name'];

        // Check if the image is not too big
        if ($_FILES[$imageKey]['size'] > $maxsize) {
            echo "File $imageKey is too large. Max file size is 5MB.<br>";
        } else {
            // Generate a unique filename for each image
            $uniqueName = uniqid() . '_' . $imageName;
            $destination = "../afbeeldingen/$uniqueName";

            // Compress the image
            $destination = compressImage($image_tmp, $destination, 30);

            // Upload the image to the server
            if (move_uploaded_file($image_tmp, $destination)) {
                // Store the filename in the array
                $imageNames[] = $uniqueName;
            }
        }
    }

    // Insert the data into the database
    $image1 = isset($imageNames[0]) ? $imageNames[0] : "";
    $image2 = isset($imageNames[1]) ? $imageNames[1] : "";
    $image3 = isset($imageNames[2]) ? $imageNames[2] : "";
    $image4 = isset($imageNames[3]) ? $imageNames[3] : "";
    $image5 = isset($imageNames[4]) ? $imageNames[4] : "";

    $stmt = $db->prepare("INSERT INTO Posts (title, info, afbeelding1, afbeelding2, afbeelding3, afbeelding4, afbeelding5, userID, merk, model, bouwjaar, kmstand, kleur, vermogen, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bindParam(1, $title);
    $stmt->bindParam(2, $content);
    $stmt->bindParam(3, $image1);
    $stmt->bindParam(4, $image2);
    $stmt->bindParam(5, $image3);
    $stmt->bindParam(6, $image4);
    $stmt->bindParam(7, $image5);
    $stmt->bindParam(8, $userID);
    $stmt->bindParam(9, $merk);
    $stmt->bindParam(10, $model);
    $stmt->bindParam(11, $bouwjaar);
    $stmt->bindParam(12, $kmstand);
    $stmt->bindParam(13, $kleur);
    $stmt->bindParam(14, $vermogen);
    $stmt->bindParam(15, $category);
    $stmt->execute();

    // update post count
    $result = $db->query("SELECT * FROM Login WHERE ID='$id'");
    $row = $result->fetchArray();
    $post = $row['posts'];
    $post++;
    $db->exec("UPDATE Login SET posts='$post' WHERE ID='$id'");
}

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
    <title>Post</title>
    <link rel="icon" href="../afbeeldingen/logo.png">
    <!-- bootstrap css -->
    <link rel="stylesheet" href="../nav/nav.css">
    <style>
        .gradient-custom-2 {
            background: #1f2029;
            color: white;
        }
        html {
            scroll-behavior: smooth;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<section class="h-100 gradient-custom-2" style="min-height: 100vh; background-color: #c4c3ca;">
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
    <div class="container my-5" style="background-color: white; padding: 3em; border-radius: 1em; box-shadow: #69707a; color: black;">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <h1 class="mb-3">Post!</h1>
                <p class="mb-5">Add a car to your online collection!</p>
                <form method="post" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="your-name" class="form-label">Name of car</label>
                            <input type="text" class="form-control" id="your-name" name="title" required>
                        </div>
                        <div class="col-md-6">
                            <label for="your-email" class="form-label">Merk</label>
                            <input type="text" class="form-control" id="your-email" name="merk" required>
                        </div>
                        <div class="col-12">
                            <label for="your-message" class="form-label">Info over auto</label>
                            <textarea class="form-control" id="your-message" name="content" rows="5" required></textarea>
                        </div>
                        <div class="col-12">
                            <label for="your-picture" class="form-label">Category</label>
                            <select class="form-select" aria-label="Default select example" name="category">
                                <option selected>Open this select menu</option>
                                <option value="1">oldtimer</option>
                                <option value="2">sportcar</option>
                                <option value="3">SUV</option>
                                <option value="4">Supercar</option>
                                <option value="5">Hypercar</option>
                                <option value="6">musel car</option>
                                <option value="7">tuner car</option>
                                <option value="8">truks</option>
                                <option value="9">Anders</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="your-email" class="form-label">Model</label>
                            <input type="text" class="form-control" id="your-email" name="model" required>
                        </div>
                        <div class="col-md-6">
                            <label for="your-email" class="form-label">Bouwjaar</label>
                            <input type="text" class="form-control" id="your-email" name="bouwjaar" required>
                        </div>
                        <div class="col-md-6">
                            <label for="your-email" class="form-label">Km stand</label>
                            <input type="text" class="form-control" id="your-email" name="kmstand" required>
                        </div>
                        <div class="col-md-6">
                            <label for="your-email" class="form-label">Kleur</label>
                            <input type="text" class="form-control" id="your-email" name="kleur" required>
                        </div>
                        <div class="col-md-6">
                            <label for="your-email" class="form-label">Vermogen</label>
                            <input type="text" class="form-control" id="your-email" name="vermogen" required>
                        </div>
                        <div class="col-12">
                            <label for="your-picture" class="form-label">Afbeelding 1</label>
                            <input type="file" class="form-control" id="your-picture" name="afbeelding1" required>
                        </div>
                        <div class="col-12">
                            <label for="your-picture" class="form-label">Afbeelding 2 (optional)</label>
                            <input type="file" class="form-control" id="your-picture" name="afbeelding2">
                        </div>
                        <div class="col-12">
                            <label for="your-picture" class="form-label">Afbeelding 3 (optional)</label>
                            <input type="file" class="form-control" id="your-picture" name="afbeelding3">
                        </div>
                        <div class="col-12">
                            <label for="your-picture" class="form-label">Afbeelding 4 (optional)</label>
                            <input type="file" class="form-control" id="your-picture" name="afbeelding4">
                        </div>
                        <div class="col-12">
                            <label for="your-picture" class="form-label">Afbeelding 5 (optional)</label>
                            <input type="file" class="form-control" id="your-picture" name="afbeelding5">
                        </div>
                        <div class="col-12">
                            <div class="row">
                                <button type="submit" class="btn btn-outline-dark w-100 fw-bold" style="width: 100%" name="submit">Send</button>
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
