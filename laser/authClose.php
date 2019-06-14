<?php
	session_start();
	unset($_SESSION["PassLaser"]);
	unset($_SESSION["FIOLaser"]);
	session_destroy();
	header("location: index.php");
?>