<!-- Uebermitteln einer geanderten Notifikationseinstellung an die Datenbank 
	1: Benarichtigungen erhalten
	0: keine Benachrichtigungen
-->
<?php
//Redirection auf Ausgangsseite
header("Location: usercontrol.php");

//Verbindungsaufbau
include 'admin/connect.php';

// escape variables for security
$id = mysqli_real_escape_string($con, $_POST['user_id']);
$note = mysqli_real_escape_string($con, $_POST['notification']);

//Eintragen der Aenderung
if ($note != '') {
	mysqli_query($con,"UPDATE user SET notification = 1 WHERE id = $id");	
} else {
	mysqli_query($con,"UPDATE user SET notification = 0 WHERE id = $id");
}

//Verbindung beenden
mysqli_close($con);
?>