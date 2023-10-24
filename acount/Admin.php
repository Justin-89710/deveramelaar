<?php
//start Login Session
session_start();

// Connect to database
$db = new SQLite3('../database/database.db');

//check if with the database connection is successful
if (!$db) {
    die("Connection failed: " . $db->connect_error);
}

// show server side errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// check for admin rank
if ($_SESSION['rank'] != 1) {
    // if not send to home
    header("Location: ../home/home.php");
}

// change rank of user
if (isset($_POST['submitrank'])) {
    // get id of user
    $id = $_POST['id'];
    // get rank of user
    $rank = $_POST['rank'];
    // update rank of user
    $db->exec("UPDATE Login SET rank='$rank' WHERE ID='$id'"); // set the rank of the chosen user in the database
}

// get all posts
$result = $db->query("SELECT * FROM Posts");
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Panel</title>
    <!-- Logo for page -->
    <link rel="icon" href="../afbeeldingen/logo.png">
    <!-- bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<h1>Admin Panel</h1>
<!-- show all users with bootstrap -->
<table class="table">
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col">username</th>
        <th scope="col">Email</th>
        <th scope="col">Rank</th>
        <th scope="col">Profile Picture</th>
        <th scope="col">Bio</th>
        <th scope="col">Delete</th>
    </tr>
    </thead>
    <tbody>
    <?php
    // get all users
    $result = $db->query("SELECT * FROM Login");
    while ($row = $result->fetchArray()) {
        // show all users
        echo "<tr>";
        echo "<th scope='row'>" . $row['ID'] . "</th>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['rank'] . "</td>";
        echo "<td>" . $row['profielfoto'] . "</td>";
        echo "<td>" . $row['bio'] . "</td>";
        echo "<td><a href='deleteuser.php?id=" . $row['ID'] . "'><button class='btn btn-danger'>Delete</button></a></td>";
        echo "</tr>";
    }
    ?>
    </tbody>
</table>
<!-- change rank -->
<form method="post">
    <div class="mb-3">
        <label for="id" class="form-label">ID</label>
        <input type="text" class="form-control" id="id" name="id">
    </div>
    <div class="mb-3">
        <label for="rank" class="form-label">Rank</label>
        <input type="text" class="form-control" id="rank" name="rank">
    </div>
    <button type="submit" name="submitrank" class="btn btn-primary">Change Rank</button>
</form>
<!-- Posts -->
<h1>Posts</h1>
<!-- show all posts with bootstrap -->
<?php
// get all posts
$result = $db->query("SELECT * FROM Posts");
// put all posts in a table
echo "<table class='table'>";
echo "<thead>";
echo "<tr>";
echo "<th scope='col'>ID</th>";
echo "<th scope='col'>Title</th>";
echo "<th scope='col'>Description</th>";
echo "<th scope='col'>Image 1</th>";
echo "<th scope='col'>Image 2</th>";
echo "<th scope='col'>Image 3</th>";
echo "<th scope='col'>Image 4</th>";
echo "<th scope='col'>Image 5</th>";
echo "<th scope='col'>Delete</th>";
echo "</tr>";
// show all posts
while ($row = $result->fetchArray()) {
    echo "<tr>";
    echo "<th scope='row'>" . $row['ID'] . "</th>";
    echo "<td>" . $row['title'] . "</td>";
    echo "<td>" . $row['info'] . "</td>";
    echo "<td> <img src='../afbeeldingen/" . $row['afbeelding1'] . "' alt='' style='max-width: 40px;'></td>";
    echo "<td> <img src='../afbeeldingen/" . $row['afbeelding2'] . "' alt='' style='max-width: 40px;'></td>";
    echo "<td><img src='../afbeeldingen/" . $row['afbeelding3'] . "' alt='' style='max-width: 40px;'></td>";
    echo "<td><img src='../afbeeldingen/" . $row['afbeelding4'] . "' alt='' style='max-width: 40px;'></td>";
    echo "<td><img src='../afbeeldingen/" . $row['afbeelding5'] . "' alt='' style='max-width: 40px;'></td>";
    echo "<td><a href='deletepost.php?id=" . $row['ID'] . "'><button class='btn btn-danger'>Delete</button></a></td>";
    echo "</tr>";
}
echo "</thead>";
echo "<tbody>";
?>

<!-- show all the ebstellingen -->
<h1>orders</h1>
<!-- show all bestellingen with bootstrap -->
<table class="table">
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col">Username</th>
        <th scope="col">Post ID</th>
        <th scope="col">Price</th>
        <th scope="col">Message</th>
        <th scope="col">Delete</th>
    </tr>
    </thead>
    <tbody>
    <?php
    // get all bestellingen
    $result = $db->query("SELECT * FROM Bestellingen");
    while ($row = $result->fetchArray()) {
        // show all bestellingen
        echo "<tr>";
        echo "<th scope='row'>" . $row['ID'] . "</th>";
        echo "<td>" . $row['unb'] . "</td>";
        echo "<td>" . $row['postid'] . "</td>";
        echo "<td>" . $row['price'] . "</td>";
        echo "<td>" . $row['message'] . "</td>";
        echo "<td><a href='deletebestelling.php?id=" . $row['ID'] . "'><button class='btn btn-danger'>Delete</button></a></td>";
        echo "</tr>";
    }
    ?>
    </tbody>
</table>
<!-- bootstrap js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<!-- font awesome -->
<script src="https://kit.fontawesome.com/2a8f5c1a81.js" crossorigin="anonymous"></script>
</body>
</html>
