<?php

include 'connect.php';
 
$user = mysqli_query($con, "SELECT * FROM user WHERE notification = 1");

while ($_user = mysqli_fetch_array($user, MYSQL_ASSOC)) {

	//$games = mysqli_query($con, "SELECT matchday.matchday_number as number, h.name as home_name, a.name as away_name FROM game LEFT JOIN team h ON (game.home_team_id = h.id) LEFT JOIN team a ON (game.away_team_id = a.id) LEFT JOIN matchday ON (matchday.id = game.matchday_id) WHERE NOT EXISTS (SELECT id FROM prediction WHERE prediction.game_id = game.id AND prediction.user_id =" . $_user['id'] . ") AND game.status = 0 AND DATE(timestamp) < DATE_SUB(DATE(NOW()), Interval -20 day) ORDER BY timestamp ASC"); // -2
	$games = mysqli_query($con, "SELECT matchday.season_id as season_id, matchday.id as number, matchday.name as name, h.name as home_name, a.name as away_name FROM game LEFT JOIN team h ON (game.home_team_id = h.id) LEFT JOIN team a ON (game.away_team_id = a.id) LEFT JOIN matchday ON (matchday.id = game.matchday_id) WHERE NOT EXISTS (SELECT id FROM prediction WHERE prediction.game_id = game.id AND prediction.user_id = $_user[id]) AND game.status = 0 AND DATE(timestamp) < DATE_SUB(DATE(NOW()), Interval -2 day) AND EXISTS (SELECT * FROM user_in_season WHERE user_in_season.user_id = $_user[id] AND user_in_season.season_id = matchday.season_id) ORDER BY timestamp ASC");
	// An welche Adresse sollen die Mails gesendet werden?
	$zieladresse = $_user['email'];

	// Welche Adresse soll als Absender angegeben werden?
	// (Manche Hoster lassen diese Angabe vor dem Versenden der Mail ueberschreiben)
	$absenderadresse = 'augias';

	// Welcher Absendername soll verwendet werden?
	$absendername = 'LS3 Tippspiel';

	// Welchen Betreff sollen die Mails erhalten?
	$betreff = 'Ausstehende Tipps';

	/**
	 * Ende Konfiguration
	 */

		$header = array();
		$header[] = "From: ".mb_encode_mimeheader($absendername, "utf-8", "Q")." <".$absenderadresse.">";
		$header[] = "MIME-Version: 1.0";
		$header[] = "Content-type: text/plain; charset=utf-8";
		$header[] = "Content-transfer-encoding: 8bit";
	
	  $mailtext = "Hallo " . $_user['name'] . ",\n\n" . "innerhalb der nächsten zwei Tage fehlen von dir noch Tipps für die folgenden Spiele:\n";

		$send = false;

		$number = '';

		while ($_games = mysqli_fetch_array($games, MYSQL_ASSOC)) {

			if ($number == '') {

				$season_name = mysqli_query($con, "SELECT name FROM season WHERE id = $_games[season_id]");
				$_season_name = mysqli_fetch_array($season_name, MYSQL_ASSOC);

				$number = $_games['number'];
				$mailtext = $mailtext . "\n" . $_games['name'] . ' ('. $_season_name['name'] . "):\n";	
			}
			if ($number != $_games['number']) {

				$season_name = mysqli_query($con, "SELECT name FROM season WHERE id = $_games[season_id]");
				$_season_name = mysqli_fetch_array($season_name, MYSQL_ASSOC);

				$mailtext = $mailtext . "\n\n" . $_games['name'] . ' ('. $_season_name['name'] . "):\n";		
			}

			$mailtext = $mailtext . "\n\t" . utf8_encode($_games['home_name']) . " - " . utf8_encode($_games['away_name']); 	


			$number = $_games['number'];
			$send = true;
		}

		if ($send == true) {

			$mailtext = $mailtext . "\n\n" . "Solltest du keine weiteren Hinweise über ausstehende Tipps erhalten wollen, so kannst du dies in der Kontoverwaltung abstellen.";

			mail(
				$zieladresse, 
				mb_encode_mimeheader($betreff, "utf-8", "Q"), 
				$mailtext,
				implode("\n", $header)
				) or die("Die Mail konnte nicht versendet werden.");
		}
	}
?>
