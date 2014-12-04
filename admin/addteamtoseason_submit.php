<?php

include 'connect.php';

// escape variables for security
$season_id = mysqli_real_escape_string($con, $_POST['season_id']);
$team_id = mysqli_real_escape_string($con, $_POST['team_id']);


$add_season_to_team = "INSERT INTO season_has_team (season_id, team_id) VALUES ('$season_id', '$team_id')";

if (!mysqli_query($con,$add_season_to_team)) {
  die('Error: ' . mysqli_error($con));
}

$update_season = "UPDATE season SET teams = teams + 1 WHERE id = $season_id ";

if (!mysqli_query($con,$update_season)) {
  die('Error: ' . mysqli_error($con));
}

header("Location: addteamtoseason.php?season_id=$season_id");
?>