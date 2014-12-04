<?php

include 'connect.php';

// escape variables for security
$id = mysqli_real_escape_string($con, $_POST['id']);
$season_id = mysqli_real_escape_string($con, $_POST['season_id']);
$name = mysqli_real_escape_string($con, $_POST['name']);
$number = mysqli_real_escape_string($con, $_POST['number']);
$status = mysqli_real_escape_string($con, $_POST['status']);

mysqli_query($con,"UPDATE matchday SET name = '$name', matchday_number = $number, status = $status WHERE id = $id");

header("Location: addmatchday.php?season_id=$season_id");

// echo $id . '<br>' . $season_id . '<br>' .$name. '<br>' .$number . '<br>' .$status;

mysqli_close($con);
?>