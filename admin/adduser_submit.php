<?php
header("Location: adduser.php");

include 'connect.php';

// escape variables for security
$name = mysqli_real_escape_string($con, $_POST['name']);
$surname = mysqli_real_escape_string($con, $_POST['surname']);
$username = mysqli_real_escape_string($con, $_POST['username']);
$email = mysqli_real_escape_string($con, $_POST['email']);
$password = mysqli_real_escape_string($con, $_POST['password']);

$sql="INSERT INTO user (name, surname, username, email, password)
VALUES ('$name', '$surname', '$username', '$email', MD5('$password'))";

if (!mysqli_query($con,$sql)) {
  die('Error: ' . mysqli_error($con));
}
echo "user added";

mysqli_close($con);
?>