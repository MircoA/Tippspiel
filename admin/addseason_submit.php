<?php
header("Location: addseason.php");

include 'connect.php';

// escape variables for security
$name = mysqli_real_escape_string($con, $_POST['name']);

$sql="INSERT INTO season (name, teams)
VALUES ('$name', 0)";

if (!mysqli_query($con,$sql)) {
  die('Error: ' . mysqli_error($con));
}
// echo "season added";

mysqli_close($con);
?>