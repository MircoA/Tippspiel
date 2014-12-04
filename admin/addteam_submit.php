<?php
header("Location: addteam.php");

include 'connect.php';

// escape variables for security
$name = mysqli_real_escape_string($con, $_POST['name']);
$alias = mysqli_real_escape_string($con, $_POST['alias']);

$create_team = "INSERT INTO team (name, alias) VALUES ('$name', '$alias') ";

if (!mysqli_query($con,$create_team)) {
  die('Error: ' . mysqli_error($con));
}

mysqli_close($con);
?>