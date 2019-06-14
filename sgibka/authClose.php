<?php
	session_start();
	unset($_SESSION["PassSgibka"]);
	unset($_SESSION["FIOSgibka"]);
	session_destroy();
	header("location: index.php");
?>