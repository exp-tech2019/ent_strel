<?php
	$XMLParams=simplexml_load_file("../params.xml");
	$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
	switch($_POST["Method"])
	{
		case "PropNalogLoad":
			$d=$m->query("SELECT * FROM nalogcalc WHERE DoorType='".$_POST["DoorType"]."'");
			$a=array(); $i=0;
			if($d->num_rows>0)
			while($r=$d->fetch_assoc())
			{
				$a[$i]=array(
					"Material"=>$r["Material"],
					"Unit"=>$r["Unit"],
					"CalcType"=>$r["CalcType"],
					"Count"=>$r["Count"],
					"Price"=>$r["Price"]
				);
				$i++;
			};
			echo json_encode($a);
		break;
		case "PropNalogSave":
			$m->autocommit(FALSE);
			$m->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
			$er="";
			
			try
			{
				//Для начала удалим записи по текущему типу дверей
				$m->query("DELETE FROM nalogcalc WHERE DoorType='".$_POST["DoorType"]."'");
				$i=0;
				while(isset($_POST["MaterialArr"][$i]))
				{
					if($_POST["MaterialArr"][$i]!="" & $_POST["UnitArr"][$i]!="" & $_POST["CalcTypeArr"][$i]!="" & $_POST["CountArr"][$i]!="" & $_POST["PriceArr"][$i]!="")
						$m->query("INSERT INTO nalogcalc (DoorType, Material, Unit, CalcType, Count, Price) VALUES(".
							"'".$_POST["DoorType"]."', ".
							"'".$_POST["MaterialArr"][$i]."', ".
							"'".$_POST["UnitArr"][$i]."', ".
							"".($_POST["CalcTypeArr"][$i]=="на ед. площади"?"1":"0").", ".
							"".$_POST["CountArr"][$i].", ".
							"".str_replace(",",".",$_POST["PriceArr"][$i])." ".
							" )"
						) or die($er=$er.mysqli_error($m));
					$i++;
				};
			}
			catch (mysqli_sql_exception $e) {$er=$er.$e->errorMessage();};
			
			if($er=="")
			{
				$m->commit(); echo "ok";
			}
			else
			{
				echo $er; $m->rollback();
			};
		break;
		//------------Зарплата---------------
		case 'PropPrlLoad':
			$d=$m->query("SELECT * FROM payrolldoorsize WHERE DoorType='".$_POST["DoorType"]."' AND Step='".$_POST["Step"]."'");
			$a=array(); $OutArr=array(); $i=0;
			if($d)
				while($r=$d->fetch_assoc()){
					$a[$i]=array(
						"HSign"=>$r["HSign"],
						"HVal"=>$r["HVal"]==null?"":$r["HVal"],
						"WSign"=>$r["WSign"],
						"WVal"=>$r["WVal"]==null?"":$r["WVal"],
						"S"=>$r["S"],
						"SSign"=>$r["SSign"],
						"SVal"=>$r["SVal"]==null?"":$r["SVal"],
						"Open"=>$r["Open"],
						"Framug"=>$r["Framug"],
						"Sum"=>$r["Sum"]
						);
					$i++;
				};
			$d->close();
			$OutArr["Size"]=$a;
			//Постоянные значения
			$a=array(); $i=0;
			$d=$m->query("SELECT * FROM PayrollConstant WHERE DoorType='".$_POST["DoorType"]."' AND Step='".$_POST["Step"]."'");
			if($d)
				while($r=$d->fetch_assoc()){
					$a[$i]=array(
						"Name"=>$r["Name"],
						"Sum"=>$r["Sum"]
					);
					$i++;
				}
			$OutArr["Const"]=$a;
			$d=$m->query("SELECT * FROM PayrollConstruct WHERE DoorType='".$_POST["DoorType"]."' AND Step='".$_POST["Step"]."'");
			if($d)
				{
					$r=$d->fetch_assoc();
					$a=array(
						"Frame"=>$r["Frame"],
						"FrameCount"=>$r["FrameCount"],
						"FrameSum"=>$r["FrameSum"],
				
						"Dovod"=>$r["Dovod"],
						"DovodPreparation"=>$r["DovodPreparation"],
						"DovodSum"=>$r["DovodSum"],
				
						"Nalichnik"=>$r["Nalichnik"],
						"NalichnikSum"=>$r["NalichnikSum"],
				
						"Window"=>$r["Window"],
						"WindowCount"=>$r["WindowCount"],
						"WindowMore"=>$r["WindowMore"],
						"WindowSum"=>$r["WindowSum"],
				
						"Framuga"=>$r["Framuga"],
						"FramugaSum"=>$r["FramugaSum"],

						"Petlya"=>$r["Petlya"],
						"PetlyaCount"=>$r["PetlyaCount"],
						"PetlyaMore"=>$r["PetlyaMore"],
						"PetlyaSum"=>$r["PetlyaSum"],

						"PetlyaWork"=>$r["PetlyaWork"],
						"PetlyaWorkCount"=>$r["PetlyaWorkCount"],
						"PetlyaWorkMore"=>$r["PetlyaWorkMore"],
						"PetlyaWorkSum"=>$r["PetlyaWorkSum"],

						"PetlyaStvorka"=>$r["PetlyaStvorka"],
						"PetlyaStvorkaCount"=>$r["PetlyaStvorkaCount"],
						"PetlyaStvorkaMore"=>$r["PetlyaStvorkaMore"],
						"PetlyaStvorkaSum"=>$r["PetlyaStvorkaSum"],

						"Stiffener"=>$r["Stiffener"],
						"StiffenerW"=>$r["StiffenerW"],
						"StiffenerSum"=>$r["StiffenerSum"],

						"M2"=>$r["M2"],
						"M2Sum"=>$r["M2Sum"],

						"Antipanik"=>$r["Antipanik"],
						"AntipanikSum"=>$r["AntipanikSum"],

						"Otboynik"=>$r["Otboynik"],
						"OtboynikSum"=>$r["OtboynikSum"],

						"Wicket"=>$r["Wicket"],
						"WicketSum"=>$r["WicketSum"],

						"BoxLock"=>$r["BoxLock"],
						"BoxLockSum"=>$r["BoxLockSum"],

						"Otvetka"=>$r["Otvetka"],
						"OtvetkaSum"=>$r["OtvetkaSum"],

						"Isolation"=>$r["Isolation"],
						"IsolationSum"=>$r["IsolationSum"],

						"Grid"=>$r["Grid"],
						"GridCount"=>$r["GridCount"],
						"GridSum"=>$r["GridSum"]
					);
					$OutArr["Constructor"]=$a;
				};

			$OutArr["Step"]=$_POST["Step"];
			
			echo json_encode($OutArr);
			break;
			case 'PropPrlSave':
				$i=0; $er="";
				$m->autocommit(FALSE);
				$m->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
				$m->query("DELETE FROM payrolldoorsize WHERE DoorType='".$_POST["DoorType"]."' AND Step='".$_POST["Step"]."'") or die($er=$er." ошибка: ".mysqli_error($m));
				$m->query("DELETE FROM PayrollConstant WHERE DoorType='".$_POST["DoorType"]."' AND Step='".$_POST["Step"]."'") or die($er=$er." ошибка: ".mysqli_error($m));
				$m->query("DELETE FROM PayrollConstruct WHERE DoorType='".$_POST["DoorType"]."' AND Step='".$_POST["Step"]."'") or die($er=$er." ошибка: ".mysqli_error($m));
				while (isset($_POST["HSign"][$i])) {
					$HSign=0;
					switch ($_POST["HSign"][$i]) {
						case 'n/a': $HSign=0; break;
						case '<': $HSign=1; break;
						case '>': $HSign=2; break;
						case '=': $HSign=3; break;
					};
					$WSign=0;
					switch ($_POST["WSign"][$i]) {
						case 'n/a': $WSign=0; break;
						case '<': $WSign=1; break;
						case '>': $WSign=2; break;
						case '=': $WSign=3; break;
					};
					$SSign=0;
					switch ($_POST["SSign"][$i]) {
						case 'n/a': $SSign=0; break;
						case '<': $SSign=1; break;
						case '>': $SSign=2; break;
						case '=': $SSign=3; break;
					};
					$m->query("INSERT INTO payrolldoorsize (DoorType, Step, HSign, HVal, WSign, WVal, S, SSign, SVal, Open, Framug, Sum) VALUES(".
						"'".$_POST["DoorType"]."', ".
						"'".$_POST["Step"]."', ".
						"".$HSign.", ".
						"".($HSign==0?"null":$_POST["HVal"][$i]).", ".
						"".$WSign.", ".
						"".($WSign==0?"null":$_POST["WVal"][$i]).", ".
						"".$_POST["S"][$i].", ".
						"".$SSign.", ".
						"".($SSign==0?"null":$_POST["SVal"][$i]).", ".
						"'".$_POST["Open"][$i]."', ".
						"".$_POST["Framug"][$i].", ".
						"".str_replace(",", ".", $_POST["Sum"][$i]) ." ".
						")") or die($er=$er."Таблица: PayrollDoorSize Сторка:".($i+1)." ошибка: ".mysqli_error($m));
					$i++;
				};
				
				//Постоянные значения
				$i=0;
				while(isset($_POST["ConstName"][$i]))
				{
					$m->query("INSERT INTO PayrollConstant (DoorType, Step, Name, Sum) VALUES(".
						"'".$_POST["DoorType"]."', ".
						"'".$_POST["Step"]."', ".
						"'".$_POST["ConstName"][$i]."', ".
						"".str_replace(",", ".", $_POST["ConstSum"][$i]) ." ".
					")") or die($er=$er."Таблица: PayrollConstant Сторка:".($i+1)." ошибка: ".mysqli_error($m));
					$i++;
				};
				//---Сохранение таблицы Конструкция двери---
				$m->query("INSERT INTO PayrollConstruct ".
					"( DoorType, Step, Frame, FrameCount, FrameSum, Dovod, DovodPreparation, DovodSum, Nalichnik, NalichnikSum, Window, WindowCount, WindowMore, WindowSum, Framuga, FramugaSum, Petlya, PetlyaCount, PetlyaMore, PetlyaSum, PetlyaWork, PetlyaWorkCount, PetlyaWorkMore, PetlyaWorkSum, PetlyaStvorka, PetlyaStvorkaCount, PetlyaStvorkaMore, PetlyaStvorkaSum, Stiffener, StiffenerW, StiffenerSum, M2, M2Sum, Antipanik, AntipanikSum, Otboynik, OtboynikSum, Wicket, WicketSum, BoxLock, BoxLockSum, Otvetka, OtvetkaSum, Isolation, IsolationSum, Grid, GridCount, GridSum) VALUE ('".$_POST["DoorType"]."', '".$_POST["Step"]."',".
						"".$_POST["ConstrFrame"]." , ".
						"".$_POST["ConstrFrameCount"]." , ".
						"".$_POST["ConstrFrameSum"]." , ".

						"".$_POST["ConstrDovod"]." , ".
						"".$_POST["ConstrDovodPreparation"]." , ".
						"".$_POST["ConstrDovodSum"]." , ".

						"".$_POST["ConstrNalichnik"]." , ".
						"".$_POST["ConstrNalichnikSum"]." , ".

						"".$_POST["ConstrWindow"]." , ".
						"".$_POST["ConstrWindowCount"]." , ".
						"".$_POST["ConstrWindowMore"]." , ".
						"".$_POST["ConstrWindowSum"]." , ".

						"".$_POST["ConstrFramuga"]." , ".
						"".$_POST["ConstrFramugaSum"]." , ".
						//Навесы
						"".$_POST["ConstrPetlya"]." , ".
						"".$_POST["ConstrPetlyaCount"]." , ".
						"".$_POST["ConstrPetlyaMore"]." , ".
						"".$_POST["ConstrPetlyaSum"]." , ".
						//Навесы на рабочей створке
						"".$_POST["ConstrWorkPetlya"]." , ".
						"".$_POST["ConstrWorkPetlyaCount"]." , ".
						"".$_POST["ConstrWorkPetlyaMore"]." , ".
						"".$_POST["ConstrWorkPetlyaSum"]." , ".
						//Навесы на второй створке
						"".$_POST["ConstrStvorkaPetlya"]." , ".
						"".$_POST["ConstrStvorkaPetlyaCount"]." , ".
						"".$_POST["ConstrStvorkaPetlyaMore"]." , ".
						"".$_POST["ConstrStvorkaPetlyaSum"]." , ".
						//Ребра жесткости
						"".$_POST["ConstrStiffener"]." , ".
						"".$_POST["ConstrStiffenerW"]." , ".
						"".$_POST["ConstrStiffenerSum"].",  ".
						//Площадь двери
						"".$_POST["ConstructM2"].", ".
						"".$_POST["ConstructM2Sum"].", ".
						//Антипаника
						"".$_POST["Antipanik"].", ".
						"".$_POST["AntipanikSum"].", ".
						//Отбойник
						"".$_POST["Otboynik"].", ".
						"".$_POST["OtboynikSum"].", ".
						//Калитка
						"".$_POST["Wicket"].", ".
						"".$_POST["WicketSum"].", ".
						//Врезка замка
						"".$_POST["BoxLock"].", ".
						"".$_POST["BoxLockSum"].", ".
						//Отвветка
						"".$_POST["Otvetka"].", ".
						"".$_POST["OtvetkaSum"].", ".
						//Утепление
						"".$_POST["Isolation"].", ".
						"".$_POST["IsolationSum"]." , ".
						//Вент. решетка
						"".$_POST["Grid"]." , ".
						"".$_POST["GridCount"]." , ".
						"".$_POST["GridSum"]." ".
					")");

				if($er=="")
				{
					$m->commit();

				}
				else
				{
					echo $er; $m->rollback();
				};
			break;
		//------Зарплата ИТР---------
		//Подгрзка списка сотрудников
		case "PrlTasksWorkersLoad":
			$idDolgnost=isset($_POST["idDolgnost"]) ? "WHERE DolgnostID=".$_POST["idDolgnost"] : "";
			$d=$m->query("SELECT id, DolgnostID, FIO FROM Workers ".$idDolgnost);
			$a=array(); $i=0;
			if($d->num_rows>0)
				while($r=$d->fetch_assoc()){
					$a[$i]=array(
						"id"=>$r["id"],
						"idDolgnost"=>$r["DolgnostID"],
						"FIO"=>$r["FIO"],
					);
					$i++;
				};
			echo json_encode($a);
		break;
		//Сохраняем задание
		case "PrlTasksSave":
			$idTask=$_POST["idTask"];
			$er="";

			$m->autocommit(FALSE);
			$m->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
			if($idTask=="")
			{
				$Note=$_POST["Note"];
				$Ch1=$_POST["Ch1"];
				$Ch1Inp=$_POST["Ch1Inp"]==""? "NULL": $_POST["Ch1Inp"];
				$Ch2=$_POST["Ch2"];
				$Ch3=$_POST["Ch3"];
				$Ch4=$_POST["Ch4"];
				$Ch4Inp=$_POST["Ch4Inp"]==""? "NULL": "STR_TO_DATE('%d.%m.%Y', '".$_POST["Ch4Inp"]."')";
				$m->query("INSERT INTO PayrollTasks (Note, Ch1, Ch1Inp, Ch2, Ch3, Ch4, Ch4Inp) VALUES ('$Note', $Ch1, $Ch1Inp, $Ch2, $Ch3, $Ch4, $Ch4Inp)") or die($er=$er.mysqli_error($m));
				$idTask=$m->insert_id;
			}
			else
			{
				$Note=$_POST["Note"];
				$Ch1=$_POST["Ch1"];
				$Ch1Inp=$_POST["Ch1Inp"]==""? "NULL": $_POST["Ch1Inp"];
				$Ch2=$_POST["Ch2"];
				$Ch3=$_POST["Ch3"];
				$Ch4=$_POST["Ch4"];
				$Ch4Inp=$_POST["Ch4Inp"]==""? "NULL": "STR_TO_DATE('".$_POST["Ch4Inp"]."','%d.%m.%Y')";
				$m->query("UPDATE PayrollTasks SET Note='$Note', Ch1=$Ch1, Ch1Inp=$Ch1Inp, Ch2=$Ch2, Ch3=$Ch3, Ch4=$Ch4, Ch4Inp=$Ch4Inp WHERE id=$idTask") or die($er=$er.mysqli_error($m));
				$m->query("DELETE FROM PayrollTasksWorkers WHERE idTask=$idTask") or die($er=$er.mysqli_error($m));
			};
			//Заполняем таблицу сотрудников
			$idDolgnost=$_POST["idDolgnost"];
			$idWorker=$_POST["idWorker"];
			$Cost=$_POST["Cost"];
			for($i=0;$i<count($idDolgnost) & $er=="";$i++)
			{
				$idDolgnost1=$idDolgnost[$i];
				$idWorker1=$idWorker[$i];
				$Cost1=$Cost[$i];
				$m->query("INSERT INTO PayrollTasksWorkers (idTask, idDolgnost, idWorker, Cost) VALUES ($idTask, $idDolgnost1, $idWorker1, $Cost1)") or die($er=$er.mysqli_error($m));
			};
			//Завершение транзакции
			if($er=="")
			{
				$m->commit(); echo "ok";
			}
			else
			{
				echo $er; $m->rollback();
			};
		break;
		case "PrlTasksSelect":
			$idTask=(isset($_POST["id"]) ? "AND t.id=".$_POST["id"]:"");
			$d=$m->query("SELECT t1.*, DATE_FORMAT(t1.Ch4Inp,'%d.%m.%Y') AS Ch4InpDate, w.FIO
	FROM (SELECT t.*, d.id AS idDolgnost, d.Dolgnost, tw.idWorker, tw.Cost FROM payrolltasks t, payrolltasksworkers tw, manualdolgnost d WHERE t.id=tw.idTask AND tw.idDolgnost=d.id $idTask) t1
LEFT JOIN workers w
ON t1.idWorker=w.id");
			$a=array(); $i=0;
			if($d->num_rows>0)
				while($r=$d->fetch_assoc()){
					$a[$i]=array(
						"idTask"=>$r["id"],
						"Note"=>$r["Note"],
						"Ch1"=>$r["Ch1"],
						"Ch1Inp"=>$r["Ch1Inp"],
						"Ch2"=>$r["Ch2"],
						"Ch3"=>$r["Ch3"],
						"Ch4"=>$r["Ch4"],
						"Ch4Inp"=>$r["Ch4InpDate"],
						"idDolgnost"=>$r["idDolgnost"],
						"Dolgnost"=>$r["Dolgnost"],
						"idWorker"=>$r["idWorker"],
						"FIO"=>$r["FIO"],
						"Cost"=>$r["Cost"]
					);
					$i++;
				};
			echo json_encode($a);
		break;
		case "PrlTasksDelete":
			$er="";
			$m->autocommit(FALSE);
			$m->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
			$m->query("DELETE FROM payrolltasks WHERE id=".$_POST["id"]) or die($er=$er.mysqli_error($m));
			$m->query("DELETE FROM payrolltasksworkers WHERE idTask=".$_POST["id"]) or die($er=$er.mysqli_error($m));
			if($er=="")
			{
				$m->commit(); echo "ok";
			}
			else
			{
				echo $er; $m->rollback();
			};
		break;
			//----------------Справочники------------------
		case "ManDolgnostSelect":
			$d=$m->query("SELECT * FROM ManualDolgnost ORDER BY id");
			$a=array(); $i=0;
			while ($r=$d->fetch_assoc()) {
				$a[$i]=array(
						"id"=>$r["id"],
						"Dolgnost"=>$r["Dolgnost"]
					);
				$i++;
			};
			echo json_encode($a);
		break;
		case "ManDolgnostEditSave":
			$Name=$_POST["Name"];
			$m->query("UPDATE ManualDolgnost SET Dolgnost='".$Name."' WHERE id=".$_POST["id"]);
			echo "ok";
		break;
		case 'ManDolgnostDelete':
			//Определим, есть ли сотрудники с такой должность
			$d=$m->query("SELECT COUNT(*) AS DolgnostCount FROM Workers WHERE DolgnostID=".$_POST["id"]);
			$r=$d->fetch_assoc();
			if((int)$r["DolgnostCount"]==0)
			{
				$m->query("DELETE FROM ManualDolgnost WHERE id=".$_POST["id"]);
				echo "ok";
			}
			else
				echo "Удаление невозможно. Некоторым сотрудникам назначена эта должность!";
		break;
		case "ManDolgnostAdd":
			$Dolgnost=$_POST["Dolgnost"];
			$m->query("INSERT INTO ManualDolgnost (Dolgnost) VALUES ('$Dolgnost')");
		break;
	};
	$m->close();
?>