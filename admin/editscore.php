<?php

include 'connect.php';

// escape variables for security
$home_score = mysqli_real_escape_string($con, $_POST['home_score']);
$away_score = mysqli_real_escape_string($con, $_POST['away_score']);
$game_id = mysqli_real_escape_string($con, $_POST['game_id']);

$game = mysqli_query($con, "SELECT * FROM game WHERE id = $game_id");
$_game = mysqli_fetch_array($game, MYSQL_ASSOC);

mysqli_query($con, "UPDATE game SET home_score = $home_score, away_score = $away_score, status = 1 WHERE id = $game_id");

//Setzt Spietagsstatus auf 3, d.h. aktueller Spieltag
mysqli_query($con, "UPDATE matchday SET status = 3 WHERE id = $_game[matchday_id]");

//Existieren Spiele ohne Ergebnis?
$test = mysqli_query($con, "SELECT id FROM game WHERE status = 0 AND matchday_id = $_game[matchday_id]");

//Wenn nein, setzte Matchday Status auf 1
if (mysqli_num_rows($test) == 0) {
	mysqli_query($con, "UPDATE matchday SET status = 1 WHERE id = $_game[matchday_id]");
}

// header("location:javascript://history.go(-1)");
header("Location: addgame.php?matchday_id=$_game[matchday_id]");

?>