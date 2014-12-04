<?php

	$start = $_POST['start'];
	$end = $_POST['end'];
	$season = $_POST['season'];

	if ($start > $end) {
		die('Ung&uuml;ltige Spietagsangabe');
	}

	header("Location: ranking.php?season=$season&start=$start&end=$end");

?>