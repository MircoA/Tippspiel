<?php

$matchday_id = $_POST['matchday_id'];
$season_id = $_POST['season_id'];

// echo $matchday_id;

header("Location: matchday.php?matchday_id=$matchday_id&season_id=$season_id");

?>