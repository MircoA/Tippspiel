<?php
header("Location: addseason.php");

include 'connect.php';

// escape variables for security
$id = mysqli_real_escape_string($con, $_POST['id']);
$name = mysqli_real_escape_string($con, $_POST['name']);
$tendency = mysqli_real_escape_string($con, $_POST['tendency']);
$points = mysqli_real_escape_string($con, $_POST['points']);

mysqli_query($con,"UPDATE season SET name = '$name', right_tendency = $tendency, bonus_points = $points WHERE id = $id");

// echo $id . '<br>' . $name . '<br>' .$tendency. '<br>' .$points;

mysqli_close($con);
?>