<!-- Abmelden -->
<?php

	//Sitzung beenden
	session_start();
 	session_destroy();

	$hostname = $_SERVER['HTTP_HOST'];
	$path = dirname($_SERVER['PHP_SELF']);

	//Weiterleitung zur Startseite
 	header('Location: http://'.$hostname.($path == '/' ? '' : $path).'/index.php');
?>