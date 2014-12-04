<?php

include 'connect.php';

// escape variables for security
$home_team_id = mysqli_real_escape_string($con, $_POST['home_team_id']);
$away_team_id = mysqli_real_escape_string($con, $_POST['away_team_id']);
$matchday_id = mysqli_real_escape_string($con, $_POST['matchday_id']);
$timestamp = mysqli_real_escape_string($con, $_POST['timestamp']);

if ($home_team_id == $away_team_id) {
	die('Error: Ung&uuml;ltige Paarung');
} else {

	$add_game="INSERT INTO game (home_team_id, away_team_id, matchday_id, timestamp)
	VALUES ('$home_team_id', '$away_team_id', '$matchday_id', '$timestamp')";

	if (!mysqli_query($con,$add_game)) {
	  die('Error: ' . mysqli_error($con));
	}

	mysqli_query($con, "UPDATE matchday SET games = games + 1, status = 0 WHERE id = $matchday_id");
}

header("Location: addgame.php?matchday_id=$matchday_id");

mysqli_close($con);
?>