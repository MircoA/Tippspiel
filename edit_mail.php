<?php
header("Location: usercontrol.php");

include 'admin/connect.php';

// escape variables for security
$id = mysqli_real_escape_string($con, $_POST['user_id']);
$mail = mysqli_real_escape_string($con, $_POST['mail']);

mysqli_query($con,"UPDATE user SET email = '$mail' WHERE id = $id");

mysqli_close($con);
?>