<?php session_start();?>
<html>
    <head>
        <meta charset="utf-8">
	<title>Рабочее место оператора лазера</title>
	<link href="jquery-ui.css" rel="stylesheet">
	<link href="style.css" rel="stylesheet">
	<style>
	        body{
		        font: 62.5% "Trebuchet MS", sans-serif;
		        margin: 50px;
		        background-color:#ebeff2;
	        }
	        #OrderTableDoors{
		        border-spacing: 0px;
	        }
	        #OrderTableDoors tr td{
		        border: solid 1px #a0a1a0;
	        }
	    </style>
    </head>
    <body background-color="ebeff2">
        <script src="external/jquery/jquery.js"></script>
        <script src="jquery-2.1.3.min.js"></script>
        <script src="jquery-ui.js"></script>
        <script src="date.format.js"></script>
        <script src="scripts/jquery.maskedinput.js"></script>
<?php
	if(isset($_SESSION["PassLaser"]) & isset($_SESSION["FIOLaser"])) 
	{
		include "indexAuth.php";
	}
	else
	{
		$Auth=false;
		if(isset($_POST["Pass"]))
		{
			$FIO=SelectFIO($_POST["Pass"]);
			if($FIO!="")
			{
				$_SESSION["PassLaser"]=$_POST["Pass"];
				$_SESSION["FIOLaser"]=$FIO;
				include "indexAuth.php";
			}
			else
				include "auth.php";
		}
		else
			include "auth.php";
	};
	
function SelectFIO($Pass)
{
	$FIO="";
	$XMLParams=simplexml_load_file("../params.xml");
	$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
	$d=$m->query("SELECT FIO FROM workers WHERE (DolgnostID=14 OR DolgnostID=2) AND AuthPass='".$Pass."' ");
	if($d->num_rows>0)
	{
		$r=$d->fetch_assoc();
		$FIO=$r["FIO"];
	};
	return $FIO;
}
?>
    </body>
</html>