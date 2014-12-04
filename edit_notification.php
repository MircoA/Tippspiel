<?php
header("Location: usercontrol.php");

include 'admin/connect.php';

// escape variables for security
$id = mysqli_real_escape_string($con, $_POST['user_id']);
$note = mysqli_real_escape_string($con, $_POST['notification']);

if ($note != '') {
	mysqli_query($con,"UPDATE user SET notification = 1 WHERE id = $id");	
} else {
	mysqli_query($con,"UPDATE user SET notification = 0 WHERE id = $id");
}

mysqli_close($con);
?>