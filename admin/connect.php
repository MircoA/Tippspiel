<?php

    $con = mysqli_connect("localhost","root","mirco161090","bundesliga");
	// Check connection
	if (mysqli_connect_errno()) {
  		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

?>