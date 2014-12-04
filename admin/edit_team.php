<?php
header("Location: addteam.php");

include 'connect.php';

// escape variables for security
$id = mysqli_real_escape_string($con, $_POST['id']);
$name = mysqli_real_escape_string($con, $_POST['name']);
$alias = mysqli_real_escape_string($con, $_POST['alias']);

// echo $id . $alias . $name;

// mysqli_query($con,"UPDATE team SET name='$name', alias=$alias WHERE id = $id");

mysqli_query($con, "UPDATE team SET name='$name', alias='$alias' WHERE id = $id");

// echo $id . '<br>' . $name . '<br>' .$tendency. '<br>' .$points;

mysqli_close($con);
?>