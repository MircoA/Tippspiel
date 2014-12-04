<?php

include 'connect.php';

$season_id = 1;

$users = mysqli_query($con, "SELECT * FROM user WHERE EXISTS (SELECT id FROM user_in_season WHERE user_in_season.user_id = user.id AND user_in_season.season_id = $season_id)");

while ($_users = mysqli_fetch_array($users, MYSQL_ASSOC)) {

	$datei = fopen("tipps/" . utf8_encode($_users[name]) . utf8_encode($_users[surname]) . '.txt', "w");
	fwrite($datei, "Name " . utf8_encode($_users[name]) . "\n" . "Surname " . utf8_encode($_users[surname]) . "\n" . "Nick " . '"' . utf8_encode($_users[username]) . '"' . "\n");

	$predictions = mysqli_query($con, "SELECT game.*, a.alias as away_alias, h.alias as home_alias, prediction.home_prediction as home_prediction, prediction.away_prediction as away_prediction, matchday.season_id as season_id FROM game LEFT JOIN matchday ON game.matchday_id = matchday.id LEFT JOIN team h ON game.home_team_id = h.id LEFT JOIN team a ON game.away_team_id = a.id LEFT JOIN prediction ON game.id = prediction.game_id AND $_users[id] = prediction.user_id WHERE matchday.season_id = $season_id AND game.status != 0 ORDER BY game.timestamp, game.id");

	while ($_predictions = mysqli_fetch_array($predictions, MYSQL_ASSOC)) {

		fwrite($datei, $_predictions[home_alias] . " " . $_predictions[away_alias] . " ");

		if ($_predictions[home_prediction] == '') {
			fwrite($datei, "-1 -1\n");
		} else {
		 	fwrite($datei, $_predictions[home_prediction] . " " . $_predictions[away_prediction] . "\n");
		}
	}

	fclose($datei);
}

$datei = fopen('tipps/matches.txt', "w");

$games = mysqli_query($con, "SELECT game.*, a.alias as away_alias, h.alias as home_alias, matchday.season_id as season_id FROM game LEFT JOIN matchday ON game.matchday_id = matchday.id LEFT JOIN team h ON game.home_team_id = h.id LEFT JOIN team a ON game.away_team_id = a.id WHERE matchday.season_id = $season_id AND game.status != 0 ORDER BY game.timestamp, game.id");

while ($_games = mysqli_fetch_array($games, MYSQL_ASSOC)) {

	fwrite($datei, $_games[home_alias] . " " . $_games[away_alias] . " ");

	if ($_games[home_score] == '') {
		fwrite($datei, "-1 -1\n");
	} else {
	 	fwrite($datei, $_games[home_score] . " " . $_games[away_score] . "\n");
	}
}

fclose($datei);

?>