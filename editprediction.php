<!-- Uebermittlung von Tipps an die Datenbank -->
<?php

//Verbindungsaufbau
include 'admin/connect.php';

//Zeitzone zum vergleichen waehlen (Tippabgaben nur bis zum Anstoss zulaessig)
date_default_timezone_set('Europe/Berlin');

//Datumsformat
setlocale (LC_ALL, 'de_de');

// escape variables for security
$home_tipp = mysqli_real_escape_string($con, $_POST['home_tipp']);
$away_tipp = mysqli_real_escape_string($con, $_POST['away_tipp']);
$game_id = mysqli_real_escape_string($con, $_POST['game_id']);
$user_id = mysqli_real_escape_string($con, $_POST['user_id']);
$matchday_id = mysqli_real_escape_string($con, $_POST['matchday_id']);

//Abfangen von ungueltigen Tipps (leere Felder, Rest durch Eingabefeld begrenzt)
if ($home_tipp == '' && $away_tipp == '') {
	die('Unvollst&auml;ndiger oder fehlerhafter Tipp.');
}

//Abfrage eines moeglicherweise vorhandenen Tipps zum entrpechenden Spiel
$prediction = mysqli_query($con, "SELECT * FROM prediction WHERE game_id = $game_id AND user_id = $user_id");
$_prediction = mysqli_fetch_array($prediction, MYSQL_ASSOC);

//Anstoszeit des Spiels
$time = mysqli_query($con, "SELECT timestamp FROM game WHERE id = $game_id");
$_time = mysqli_fetch_array($time, MYSQL_ASSOC);

//Tipp vorhanden und nicht Spiel nicht angestossen, dann aktualisieren
if (mysqli_num_rows($prediction) != 0 && strtotime($_time[timestamp]) > time()) {
	mysqli_query($con, "UPDATE prediction SET home_prediction = $home_tipp, away_prediction = $away_tipp WHERE id = $_prediction[id]");
} else if (strtotime($_time[timestamp]) > time()) { //Tipp nicht vorhanden und nicht Spiel nicht angestossen, dann neuen Eintrag erstellen
	mysqli_query($con, "INSERT INTO prediction (home_prediction, away_prediction, game_id, user_id) VALUES ('$home_tipp', '$away_tipp', '$game_id', '$user_id')");
}

?>