<!-- Beitritt zu einem Tippspiel an die Datenbank uebermitteln-->
<?php

//Verbindungsaufbau
include 'admin/connect.php';

// escape variables for security
$season_id = mysqli_real_escape_string($con, $_POST['season_id']);
$user_id = mysqli_real_escape_string($con, $_POST['user_id']);

//Eintragen
mysqli_query($con,"INSERT INTO user_in_season (user_id, season_id) VALUES($user_id, $season_id)");

//Verbindung beenden
mysqli_close($con);

//Weiterleitung
header("Location: usercontrol.php");
?>