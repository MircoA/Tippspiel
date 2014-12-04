<?php

include 'admin/connect.php';

// escape variables for security
$id = mysqli_real_escape_string($con, $_POST['user_id']);
$old_pwd = mysqli_real_escape_string($con, $_POST['old_pwd']);
$new_pwd1 = mysqli_real_escape_string($con, $_POST['new_pwd1']);
$new_pwd2 = mysqli_real_escape_string($con, $_POST['new_pwd2']);

$pw = mysqli_query($con, "SELECT password FROM user WHERE id = $id");
$_pw = mysqli_fetch_array( $pw, MYSQL_ASSOC);

if ($_pw[password] != md5($old_pwd)) {
	die('Falsches Passwort');
}

if ($new_pwd1 != $new_pwd2) {
	die('Passw&ouml;rter stimmen nicht &uuml;berein');
	exit();
}

mysqli_query($con,"UPDATE user SET password = MD5('$new_pwd1') WHERE id = $id");

mysqli_close($con);

header("Location: usercontrol.php");
?>