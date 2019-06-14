<?php
	$XMLParams=simplexml_load_file("../params.xml");
	$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
	switch ($_POST['Method'])
	{
		//Вкладка общее
		case "SelectGeneralPayments":
			$RangeWith=$_POST["RangeWith"];
			$RangeBy=$_POST["RangeBy"];
			$d=$m->query("SELECT 
-1*(SELECT COALESCE(SUM(Sum),0) FROM paymentsworkers WHERE DatePayment BETWEEN STR_TO_DATE('$RangeWith','%d.%m.%Y') AND STR_TO_DATE('$RangeBy','%d.%m.%Y')) AS Payment,
(SELECT COALESCE(SUM(Cost),0) FROM NaryadComplite WHERE DateComplite BETWEEN STR_TO_DATE('$RangeWith','%d.%m.%Y') AND date_add(STR_TO_DATE('$RangeBy','%d.%m.%Y'), interval 1 day)) AS Naryad");
			$r=$d->fetch_assoc();
			echo json_encode(array(
				"Payment"=>$r["Payment"],
				"Naryad"=>$r["Naryad"]
				));
		break;
		//Кол-во выполненных дверей
		case "SelectGeneralDoorCount":
			$RangeWith=$_POST["RangeWith"];
			$RangeBy=$_POST["RangeBy"];
			//Дверей поступило в производство
			$d=$m->query("SELECT COALESCE(SUM(od.Count),0) AS DoorCount FROM oreders o, orderdoors od WHERE o.BlankDate BETWEEN STR_TO_DATE('$RangeWith','%d.%m.%Y') AND STR_TO_DATE('$RangeBy','%d.%m.%Y') AND o.id=od.idOrder");
			$r=$d->fetch_assoc();
			$DoorCount=$r["DoorCount"];
			$d->close();
			//Дверей на выполнении
			$d=$m->query("SELECT COUNT(*) AS NaryadCount FROM Naryad WHERE UpakCompliteFlag=0");
			$r=$d->fetch_assoc();
			$NaryadCount=$r["NaryadCount"];
			$d->close();
			//Разбивка по стадиям
			$d=$m->query("SELECT Step, COUNT(*) AS DoorCount FROM NaryadComplite WHERE DateComplite BETWEEN STR_TO_DATE('$RangeWith','%d.%m.%Y') AND date_add(STR_TO_DATE('$RangeBy','%d.%m.%Y'), interval 1 day) GROUP BY Step");
			$a=array(0,0,0,0,0,0,0,0,0,0);
			if($d->num_rows>0)
				while($r=$d->fetch_assoc())
					$a[(int)$r["Step"]-1]=$r["DoorCount"];
			echo json_encode(array(
				"DoorCount"=>$DoorCount,
				"NaryadCount"=>$NaryadCount,
				"NaryadStep"=>$a
			));
		break;
		//Сотрудники
		case "SelectGeneralWorkers":
			$RangeWith=$_POST["RangeWith"];
			$RangeBy=$_POST["RangeBy"];
			//Всего сотрудников
			$d=$m->query("SELECT COALESCE(COUNT(*),0) AS WorkerCount FROM Workers WHERE fired=0");
			$r=$d->fetch_assoc();
			$WorkerCount=$r["WorkerCount"];
			$d->close();
			//Сотрудников на производстве
			$ProductionTeamCount=0;
			$d=$m->query("SELECT w.Num, w.FIO, d.Dolgnost FROM Workers w, manualdolgnost d, workersdidlayn wd WHERE w.DolgnostID=d.id AND w.id=wd.idworker AND w.fired=0 AND wd.timestop IS NULL GROUP BY w.id ORDER BY d.Dolgnost");
			$a=array(); $i=0;
			if($d->num_rows>0)
				while($r=$d->fetch_assoc())
				{
					$a[$i]=array(
						"Num"=>$r["Num"],
						"FIO"=>$r["FIO"],
						"Dolgnost"=>$r["Dolgnost"]
					);
					$i++;
					$ProductionTeamCount++;
				};
			echo json_encode(array(
				"WorkerCount"=>$WorkerCount,
				"ProductionTeamCount"=>$ProductionTeamCount,
				"ProductionTeam"=>$a
			));
		break;
		//Производство
		case "SelectEntTypeDoors":
			$RangeWith=$_POST["RangeWith"];
			$RangeBy=$_POST["RangeBy"];
			$d=$m->query("SELECT * FROM manualtypedoors ORDER BY Name");
			$a=array(); $i=0;
			while($r=$d->fetch_assoc())
			{
				$a[$i]=array(
					"Name"=>$r["Name"],
					"Step"=>array(
						"Laser"=>0,
						"Sgibka"=>0,
						"Mdf"=>0,
						"Svarka"=>0,
						"Frame"=>0,
						"Sborka"=>0,
						"Color"=>0,
						"SborkaMdf"=>0,
						"Upak"=>0,
						"Shpt"=>0
					)
				);
				$i++;
			};
			$d->close();
			$d=$m->query("SELECT od.name, nc.Step FROM orderdoors od, naryad n, naryadcomplite nc WHERE od.id=n.idDoors AND n.id=nc.idNaryad AND nc.DateComplite BETWEEN STR_TO_DATE('$RangeWith','%d.%m.%Y') AND date_add(STR_TO_DATE('$RangeBy','%d.%m.%Y'), interval 1 day)");
			if($d->num_rows>0)
				while($r=$d->fetch_assoc())
					for($i=0;$i<count($a);$i++)
					{
						if($a[$i]["Name"]==$r["name"])
						{
							switch((int)$r["Step"])
							{
								case 1: $a[$i]["Step"]["Laser"]++; break;
								case 2: $a[$i]["Step"]["Sgibka"]++; break;
								case 3: $a[$i]["Step"]["Svarka"]++; break;
								case 4: $a[$i]["Step"]["Frame"]++; break;
								case 5: $a[$i]["Step"]["Sborka"]++; break;
								case 6: $a[$i]["Step"]["Color"]++; break;
								case 7: $a[$i]["Step"]["Upak"]++; break;
								case 8: $a[$i]["Step"]["Shpt"]++; break;
								case 9: $a[$i]["Step"]["Mdf"]++; break;
								case 10: $a[$i]["Step"]["SborkaMdf"]++; break;
							};
							break;
						};
					};
			echo json_encode($a);
		break;
		case "SelectEntOrders":
			$RangeWith=$_POST["RangeWith"];
			$RangeBy=$_POST["RangeBy"];
			$a=array();
			$d=$m->query("SELECT o.Blank, o.Shet, od.name, nc.Step FROM oreders o, orderdoors od, naryad n, naryadcomplite nc WHERE o.id=od.idOrder AND od.id=n.idDoors AND n.id=nc.idNaryad AND nc.DateComplite BETWEEN STR_TO_DATE('$RangeWith','%d.%m.%Y') AND date_add(STR_TO_DATE('$RangeBy','%d.%m.%Y'), interval 1 day)");
			if($d->num_rows>0)
				while($r=$d->fetch_assoc())
				{
					$flagFind=false;
					$i=0;
					while($i<count($a))
					{
						if($a[$i]["Blank"]==$r["Blank"])
						{
							$flagFind=true;
							break;
						};
						$i++;
					};
					switch ($flagFind) {
						case false:
							$a[count($a)]=array(
								"Blank"=>$r["Blank"],
								"Shet"=>$r["Shet"],
								"Step"=>array(
									"Laser"=>(int)$r["Step"]==1 ? 1:0,
									"Sgibka"=>(int)$r["Step"]==2 ? 1:0,
									"Mdf"=>(int)$r["Step"]==9 ? 1:0,
									"Svarka"=>(int)$r["Step"]==3 ? 1:0,
									"Frame"=>(int)$r["Step"]==4 ? 1:0,
									"Sborka"=>(int)$r["Step"]==5 ? 1:0,
									"Color"=>(int)$r["Step"]==6 ? 1:0,
									"SborkaMdf"=>(int)$r["Step"]==10 ? 1:0,
									"Upak"=>(int)$r["Step"]==7 ? 1:0,
									"Shpt"=>(int)$r["Step"]==8 ? 1:0
								)
							);
							break;
						case true:
							switch((int)$r["Step"])
							{
								case 1: $a[$i]["Step"]["Laser"]++; break;
								case 2: $a[$i]["Step"]["Sgibka"]++; break;
								case 3: $a[$i]["Step"]["Svarka"]++; break;
								case 4: $a[$i]["Step"]["Frame"]++; break;
								case 5: $a[$i]["Step"]["Sborka"]++; break;
								case 6: $a[$i]["Step"]["Color"]++; break;
								case 7: $a[$i]["Step"]["Upak"]++; break;
								case 8: $a[$i]["Step"]["Shpt"]++; break;
								case 9: $a[$i]["Step"]["Mdf"]++; break;
								case 10: $a[$i]["Step"]["SborkaMdf"]++; break;
							};
							break;
					};
				};
			echo json_encode($a);
		break;
		//Платежи
		case "SelectPaymentsWorkers":
			$RangeWith=$_POST["RangeWith"];
			$RangeBy=$_POST["RangeBy"];
			$d=$m->query("CALL SelectPayments3 ('$RangeWith', '$RangeBy')");
			$a=array();
			while($r=$d->fetch_assoc())
			{
				$flagFind=false;
				$i=0;
				while($i<count($a))
				{
					if($a[$i]["Dolgnost"]==$r["Dolgnost"])
					{
						$flagFind=true;
						break;
					}
					$i++;
				};
				switch ($flagFind) {
					case false:
						$a[count($a)]=array(
							"Dolgnost"=>$r["Dolgnost"],
							"SumWith"=>$r["SumWith"],
							"Cost"=>$r["Cost"],
							"SumPlus"=>$r["SumPlus"],
							"SumMinus"=>$r["SumMinus"]
						);
						break;
					case true:
						$a[$i]["SumWith"]+=(float)$r["SumWith"];
						$a[$i]["Cost"]+=(float)$r["Cost"];
						$a[$i]["SumPlus"]+=(float)$r["SumPlus"];
						$a[$i]["SumMinus"]+=(float)$r["SumMinus"];
						break;
				};
			};
			echo json_encode($a);
		break;

		case "SelectPaymentsOrders":
			$RangeWith=$_POST["RangeWith"];
			$RangeBy=$_POST["RangeBy"];
			$d=$m->query("SELECT o.Blank, o.Shet, od.name, nc.Step, nc.Cost FROM oreders o, orderdoors od, Naryad n, NaryadComplite nc WHERE o.id=od.idOrder AND od.id=n.idDoors AND n.id=nc.idNaryad AND nc.DateComplite BETWEEN STR_TO_DATE('$RangeWith','%d.%m.%Y') AND date_add(STR_TO_DATE('$RangeBy','%d.%m.%Y'), interval 1 day)");


			$a=array();
			$aTypeDoors=array();
			$aOrders=array();
			while($r=$d->fetch_assoc())
			{
				$flagFind=false;
				$i=0;
				while($i<count($aTypeDoors))
				{
					if($aTypeDoors[$i]["Name"]==$r["name"])
					{
						$flagFind=true;
						break;
					}
					$i++;
				};
				switch ($flagFind) {
					case false:
						$aTypeDoors[count($aTypeDoors)]=array(
							"Name"=>$r["name"],
							"Step"=>array(
								"Laser"=>(int)$r["Step"]==1 ? (float) $r["Cost"]:0,
								"Sgibka"=>(int)$r["Step"]==2 ? (float) $r["Cost"]:0,
								"Mdf"=>(int)$r["Step"]==9 ? (float) $r["Cost"]:0,
								"Svarka"=>(int)$r["Step"]==3 ? (float) $r["Cost"]:0,
								"Frame"=>(int)$r["Step"]==4 ? (float) $r["Cost"]:0,
								"Sborka"=>(int)$r["Step"]==5 ? (float) $r["Cost"]:0,
								"Color"=>(int)$r["Step"]==6 ? (float) $r["Cost"]:0,
								"SborkaMdf"=>(int)$r["Step"]==10 ? (float) $r["Cost"]:0,
								"Upak"=>(int)$r["Step"]==7 ? (float) $r["Cost"]:0,
								"Shpt"=>(int)$r["Step"]==8 ? (float) $r["Cost"]:0
							)
						);
						break;
					case true:
						switch((int)$r["Step"])
						{
							case 1: $aTypeDoors[$i]["Step"]["Laser"]+=(float)$r["Cost"]; break;
							case 2: $aTypeDoors[$i]["Step"]["Sgibka"]+=(float)$r["Cost"]; break;
							case 3: $aTypeDoors[$i]["Step"]["Svarka"]+=(float)$r["Cost"]; break;
							case 4: $aTypeDoors[$i]["Step"]["Frame"]+=(float)$r["Cost"]; break;
							case 5: $aTypeDoors[$i]["Step"]["Sborka"]+=(float)$r["Cost"]; break;
							case 6: $aTypeDoors[$i]["Step"]["Color"]+=(float)$r["Cost"]; break;
							case 7: $aTypeDoors[$i]["Step"]["Upak"]+=(float)$r["Cost"]; break;
							case 8: $aTypeDoors[$i]["Step"]["Shpt"]+=(float)$r["Cost"]; break;
							case 9: $aTypeDoors[$i]["Step"]["Mdf"]+=(float)$r["Cost"]; break;
							case 10: $aTypeDoors[$i]["Step"]["SborkaMdf"]+=(float)$r["Cost"]; break;
						};
						break;
				};
			//
				$flagFind=false;
				$i=0;
				while($i<count($aOrders))
				{
					if($aOrders[$i]["Blank"]==$r["Blank"])
					{
						$flagFind=true;
						break;
					}
					$i++;
				};
				switch ($flagFind) {
					case false:
						$aOrders[count($aOrders)]=array(
							"Blank"=>$r["Blank"],
							"Shet"=>$r["Shet"],
							"Step"=>array(
								"Laser"=>(int)$r["Step"]==1 ? (float) $r["Cost"]:0,
								"Sgibka"=>(int)$r["Step"]==2 ? (float) $r["Cost"]:0,
								"Mdf"=>(int)$r["Step"]==9 ? (float) $r["Cost"]:0,
								"Svarka"=>(int)$r["Step"]==3 ? (float) $r["Cost"]:0,
								"Frame"=>(int)$r["Step"]==4 ? (float) $r["Cost"]:0,
								"Sborka"=>(int)$r["Step"]==5 ? (float) $r["Cost"]:0,
								"Color"=>(int)$r["Step"]==6 ? (float) $r["Cost"]:0,
								"SborkaMdf"=>(int)$r["Step"]==10 ? (float) $r["Cost"]:0,
								"Upak"=>(int)$r["Step"]==7 ? (float) $r["Cost"]:0,
								"Shpt"=>(int)$r["Step"]==8 ? (float) $r["Cost"]:0
							)
						);
						break;
					case true:
						switch((int)$r["Step"])
						{
							case 1: $aOrders[$i]["Step"]["Laser"]+=(float)$r["Cost"]; break;
							case 2: $aOrders[$i]["Step"]["Sgibka"]+=(float)$r["Cost"]; break;
							case 3: $aOrders[$i]["Step"]["Svarka"]+=(float)$r["Cost"]; break;
							case 4: $aOrders[$i]["Step"]["Frame"]+=(float)$r["Cost"]; break;
							case 5: $aOrders[$i]["Step"]["Sborka"]+=(float)$r["Cost"]; break;
							case 6: $aOrders[$i]["Step"]["Color"]+=(float)$r["Cost"]; break;
							case 7: $aOrders[$i]["Step"]["Upak"]+=(float)$r["Cost"]; break;
							case 8: $aOrders[$i]["Step"]["Shpt"]+=(float)$r["Cost"]; break;
							case 9: $aOrders[$i]["Step"]["Mdf"]+=(float)$r["Cost"]; break;
							case 10: $aOrders[$i]["Step"]["SborkaMdf"]+=(float)$r["Cost"]; break;
						};
						break;
				};
			};
			echo json_encode(array(
				"TypeDoors"=>$aTypeDoors,
				"Orders"=>$aOrders
				));
		break;
	};
	$m->close();
?>