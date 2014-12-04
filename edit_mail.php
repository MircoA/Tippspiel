<!-- Uebermitteln einer geanderten eMail-Adresse an die Datenbank -->
<?php
//Redirection auf Ausgangsseite
header("Location: usercontrol.php");

//Verbindungsaufbau
include 'admin/connect.php';

// escape variables for security
$id = mysqli_real_escape_string($con, $_POST['user_id']);
$mail = mysqli_real_escape_string($con, $_POST['mail']);

//Eintragen der Aenderung
mysqli_query($con,"UPDATE user SET email = '$mail' WHERE id = $id");

//Verbindung beenden
mysqli_close($con);
?>