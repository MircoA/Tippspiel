<?php
header("Location: adduser.php");

include 'connect.php';

// escape variables for security
$id = mysqli_real_escape_string($con, $_POST['id']);
$name = mysqli_real_escape_string($con, $_POST['name']);
$surname = mysqli_real_escape_string($con, $_POST['surname']);
$username = mysqli_real_escape_string($con, $_POST['username']);
$email = mysqli_real_escape_string($con, $_POST['email']);
$pwd = mysqli_real_escape_string($con, $_POST['pwd']);

// echo $id . $alias . $name;

// mysqli_query($con,"UPDATE team SET name='$name', alias=$alias WHERE id = $id");

if ($pwd != '') {
	mysqli_query($con, "UPDATE user SET name='$name', surname='$surname', username='$username', email='$email', password = MD5('$pwd') WHERE id = $id") or die(mysql_error());
}
mysqli_query($con, "UPDATE user SET name='$name', surname='$surname', username='$username', email='$email' WHERE id = $id") or die(mysql_error());

// echo $id . '<br>' . $name . '<br>' .$tendency. '<br>' .$points;

mysqli_close($con);
?>