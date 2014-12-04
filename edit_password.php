<!-- Uebermitteln eines geanderten Passworts an die Datenbank -->
<?php

//Verbindungsaufbau
include 'admin/connect.php';

// escape variables for security
$id = mysqli_real_escape_string($con, $_POST['user_id']);
$old_pwd = mysqli_real_escape_string($con, $_POST['old_pwd']);
$new_pwd1 = mysqli_real_escape_string($con, $_POST['new_pwd1']);
$new_pwd2 = mysqli_real_escape_string($con, $_POST['new_pwd2']);

//Pruefsumme des alten Passworts aus der Datenbank holen
$pw = mysqli_query($con, "SELECT password FROM user WHERE id = $id");
$_pw = mysqli_fetch_array( $pw, MYSQL_ASSOC);

//Ueberprufung ob eingegebenes altes Passwort richtig
if ($_pw[password] != md5($old_pwd)) {
	die('Falsches Passwort');
}

//Ueberpruefen ob beide neuen Passwoerter identisch sind
if ($new_pwd1 != $new_pwd2) {
	die('Passw&ouml;rter stimmen nicht &uuml;berein');
	exit();
}

//Eintragen der Aenderung
mysqli_query($con,"UPDATE user SET password = MD5('$new_pwd1') WHERE id = $id");

//Verbindung beenden
mysqli_close($con);

//Redirection auf Ausgangsseite
header("Location: usercontrol.php");
?>