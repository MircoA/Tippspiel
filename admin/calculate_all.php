<?php

include 'connect.php';


//Alle Spiele fuer die noch keine Punkte vergeben wurden
$games = mysqli_query($con, "SELECT * FROM game WHERE status = 1");

while ($_games = mysqli_fetch_array($games, MYSQL_ASSOC)) {

	$season = mysqli_query($con, "SELECT season.right_tendency AS tendency, season.bonus_points AS points FROM matchday, season WHERE matchday.id = $_games[matchday_id] AND season.id = matchday.season_id");
	$_season = mysqli_fetch_array($season, MYSQL_ASSOC);

	//Zu vergebende Punkte fuer diese Saison
	$bonus_points = $_season['points'];
	$right_tendency = $_season['tendency'];

	//Tendenz zwischen den Teams (mit VZ, +: Sieg Heim, -:Sieg Gast)
	$diff = $_games['home_score'] - $_games['away_score'];

	//Statistik: Hier alle Statistik-Variablen auf 0 setzen
	mysqli_query($con, "UPDATE prediction SET status = 1, exact = 0, difference = 0, goal_number = 0, tendency = 0, points = 0 WHERE game_id = $_games[id]");

	//Anzahl der Tore
	$total_goals = $_games['home_score'] + $_games['away_score'];

	//Unentschieden?
	if ($diff == 0) {
		// echo 'Diff = ' . $diff . '<br>';

		//Wetten mit richtiger Tendenz
		$bets = mysqli_query($con, "SELECT * FROM prediction WHERE game_id = $_games[id] AND (home_prediction - away_prediction) = 0"); 
	} else {
		// echo 'Diff = ' . $diff . '<br>';

		//Wetten mit richtiger Tendenz, Sortierung wohl ueberfluessig...
		$bets = mysqli_query($con, "SELECT * FROM prediction WHERE game_id = $_games[id] AND SIGN($diff) = SIGN(home_prediction - away_prediction)");
	}
	
	//Punkte-Matrix
	$dim = 41;
	$point_system = array_fill(0, $dim, array_fill(0, $dim, 0));

	//Erste Schleife ueber Wetten zum Bestimmen der Anzahl der 'gleichen' Wetten
	while ($_bets = mysqli_fetch_array($bets, MYSQL_ASSOC)) { //Tabelle schreiben

		// echo $_bets[user_id];

		$user_diff = abs(abs($diff) - abs($_bets['home_prediction'] - $_bets['away_prediction']));

		// echo ', Abweichung von Tordifferenz: ' . $user_diff;

		// $user_gdiff = abs($_bets[home_prediction] - $_games[home_score]) + abs($_games[away_score] - $_bets[away_prediction]);
		$user_gdiff = abs($total_goals - $_bets['home_prediction'] - $_bets['away_prediction']);

		// echo ', Abweichung Tore: ' . $user_gdiff . '<br>';

		$point_system[$user_diff][$user_gdiff] += 1.0;
	}

	//Zaehler
	$count = 0.0;

	//Punktesumme zur Korrektur
	$sum = 0.0;

	//Generiert Gewichte
	for ($i=0; $i < $dim; $i++) { 
		for ($j=0; $j < $dim; $j++) { 

			if ($point_system[$i][$j] != 0) {
				$count = $count + $point_system[$i][$j];
				$sum = $sum + (double)($point_system[$i][$j] / $count);
				$point_system[$i][$j] = (double)(1.0 / $count);
			}
		}	
	}

	//TODO: Wie kann man die zweite Abfrage umgehen?
	//Unentschieden?
	if ($diff == 0) {

		//Wetten mit richtiger Tendenz
		$bets = mysqli_query($con, "SELECT * FROM prediction WHERE game_id = $_games[id] AND (home_prediction - away_prediction) = 0"); 
	} else {

		//Wetten mit richtiger Tendenz, Sortierung wohl ueberfluessig...
		$bets = mysqli_query($con, "SELECT * FROM prediction WHERE game_id = $_games[id] AND SIGN($diff) = SIGN(home_prediction - away_prediction)");
	}

	//Punkte vergeben
	while ($_bets = mysqli_fetch_array($bets, MYSQL_ASSOC)) { //Tabelle schreiben

		// echo $_bets[user_id] . ': ';

		$user_diff = abs(abs($diff) - abs($_bets['home_prediction'] - $_bets['away_prediction']));
		// $user_gdiff = abs($_bets[home_prediction] - $_games[home_score]) + abs($_games[away_score] - $_bets[away_prediction]);
		// $user_gdiff = abs($_bets[home_prediction] - $_games[home_score] - $_games[away_score] + $_bets[away_prediction]);
		$user_gdiff = abs($total_goals - $_bets['home_prediction'] - $_bets['away_prediction']);

		//Punkte die gutgeschrieben werden
		$tmp = ($bonus_points * $point_system[$user_diff][$user_gdiff] / $sum + $right_tendency);

		//Datenbank aktualisieren
		mysqli_query($con, "UPDATE prediction SET status = 1, points = $tmp WHERE id = $_bets[id]");

		$tendency = 1;
		$goal_number = 0;
		$difference = 0;
		$exact = 0;

		//Statistik: Hier Statsiktiken setzen
		if ($user_diff == 0) {
			$difference = 1;
		}

		if ($user_gdiff == 0) {
			$goal_number = 1;
		}

		if ($difference == 1 && $goal_number == 1) {
			$exact = 1;
		}

		mysqli_query($con, "UPDATE prediction SET tendency = $tendency, goal_number = $goal_number, exact = $exact, difference = $difference WHERE id = $_bets[id]");

		// echo $points . ' ' . $point_system[$user_diff][$user_gdiff] . ' ' . $sum;
		// echo ', Punkte: ' . ($bonus_points * $point_system[$user_diff][$user_gdiff] / $sum) . '<br>';


	}

	// print_r($point_system);
	// echo '<br>';
	// print_r($count);
	// echo '<br>';
	// print_r($sum);
	// echo '<br>';

	//Setzte Status auf 2 (d.h. berechnet, bei Aenderung geht es zurueck auf 1)
	mysqli_query($con, "UPDATE game SET status = 2 WHERE id = $_games[id]");

	//alle Spiele des Tages berechnet?
	mysqli_query($con, "UPDATE matchday SET status = 2 WHERE id = $_games[matchday_id] AND NOT EXISTS (SELECT id FROM game WHERE matchday_id = $_games[matchday_id] AND status != 2)");
	// echo '---------------------<br>';
}
?>