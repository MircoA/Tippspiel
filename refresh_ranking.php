<!-- Aktualisiert die Tipptabelle / Aufruf der Seite mit den entsprechenden Parametern -->
<?php

	//Auslesen der uebermittelten Infos
	$start = $_POST['start'];
	$end = $_POST['end'];
	$season = $_POST['season'];

	//Abfangen von ungueltigen Angaben (Negative Spanne)
	if ($start > $end) {
		die('Ung&uuml;ltige Spietagsangabe');
	}

	//Entsprechender neuer Aufruf der Seite
	header("Location: ranking.php?season=$season&start=$start&end=$end");

?>