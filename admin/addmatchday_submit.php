<?php

include 'connect.php';

// escape variables for security
$season_id = mysqli_real_escape_string($con, $_POST['season_id']);

$update_season = "UPDATE season SET matchdays = matchdays + 1 WHERE id = $season_id ";

if (!mysqli_query($con,$update_season)) {
  die('Error: ' . mysqli_error($con));
}

$create_matchday = "INSERT INTO matchday (season_id, matchday_number) SELECT $season_id, matchdays as matchday_number FROM season WHERE id = $season_id ";

if (!mysqli_query($con,$create_matchday)) {
  die('Error: ' . mysqli_error($con));
}

$matchday_id = mysqli_insert_id($con);

mysqli_close($con);

header("Location: addgame.php?matchday_id=$matchday_id");
?>