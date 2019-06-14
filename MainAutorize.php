<?php
	session_start();
	switch($_POST["Method"])
	{
		case "Autification":
			if($_POST["Login"]!="" & $_POST["Pass"]!="")
			{
				$XMLParams=simplexml_load_file("params.xml");
				$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB)
					or die('Не удалось соединиться: ' . mysql_error());
				$d=$m->query("SELECT id, FIO, Type FROM logins WHERE Login='".$_POST["Login"]."'  AND Pass='".$_POST["Pass"]."'");
				if($d->num_rows>0)
				{
					$r=$d->fetch_assoc();
					$_SESSION["AutorizeFIO"]=$r["FIO"];
					$_SESSION["AutorizeLogin"]=$_POST["Login"];
					$_SESSION["AutorizeType"]=$r["Type"];
					//Авторизация для склада
					$_SESSION["idLogin"]=$r["id"];
					$_SESSION["FIOLogin"]=$r["FIO"];
					echo "ok";
				}
				else
					echo "Логин или пароль не верны";
			}
			else
				echo "Логин или пароль не верны";
		break;
		case "GetSession":
			$a=array (
				"FIO"=>$_SESSION["AutorizeFIO"],
				"Login"=>$_SESSION["AutorizeLogin"],
				"Type"=>$_SESSION["AutorizeType"]
			);
			echo json_encode($a);
		break;
		case "Exit":
			unset($_SESSION["AutorizeFIO"]);
			unset($_SESSION["AutorizeLogin"]);
			unset($_SESSION["AutorizeType"]);
			echo "ok";
		break;
	};
?>