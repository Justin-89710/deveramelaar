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

// check for admin rank
if ($_SESSION['rank'] != 1) {
    header("Location: ../home/home.php");
}

// change rank
if (isset($_POST['submitrank'])) {
    // get id
    $id = $_POST['id'];
    // get rank
    $rank = $_POST['rank'];
    // update rank
    $db->exec("UPDATE Login SET rank='$rank' WHERE ID='$id'");
    // redirect to admin page
    header("Location: Admin.php");
}
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Panel</title>
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
        <th scope="col">Gebruikersnaam</th>
        <th scope="col">Email</th>
        <th scope="col">Rank</th>
        <th scope="col">Profielfoto</th>
        <th scope="col">Bio</th>
        <th scope="col">Verwijder</th>
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
        echo "<td><a href='deleteuser.php?id=" . $row['ID'] . "'><button class='btn btn-danger'>Verwijder</button></a></td>";
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
    <button type="submit" name="submitrank" class="btn btn-primary">Verander rank</button>
</form>
<!-- bootstrap js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<!-- font awesome -->
<script src="https://kit.fontawesome.com/2a8f5c1a81.js" crossorigin="anonymous"></script>
</body>
</html>
