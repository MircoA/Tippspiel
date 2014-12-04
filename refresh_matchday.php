<!-- Aktualisiert die Spieltagsuebersicht / Aufruf der Seite mit den entsprechenden Parametern -->
<?php

	//Liest POST-Infos aus
	$matchday_id = $_POST['matchday_id'];
	$season_id = $_POST['season_id'];

	//Leitet entsprechend weiter
	header("Location: matchday.php?matchday_id=$matchday_id&season_id=$season_id");

?>