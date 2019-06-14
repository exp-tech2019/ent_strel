<?php
	$XMLParams=simplexml_load_file("../params.xml");
	$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
	switch ($_POST['Method'])
	{
		//Отображение сотрудников онлайн
		case 'SelectOnline':
			$aDolgnost=array(
				array("dolgnost"=>"ст. Инженер","id"=>13),
				array("dolgnost"=>"Инженеры","id"=>2),
				array("dolgnost"=>"ст. Сгибщик","id"=>10),
				array("dolgnost"=>"Сгибщики","id"=>5),
				array("dolgnost"=>"ст. Сварщик","id"=>8),
				array("dolgnost"=>"Сварщики","id"=>3),
				array("dolgnost"=>"Рамщики","id"=>15),
				array("dolgnost"=>"ст. Сборщик","id"=>9),
				array("dolgnost"=>"Сборщики","id"=>4),
				array("dolgnost"=>"ст. Маляр","id"=>11),
				array("dolgnost"=>"Маляры","id"=>6),
				array("dolgnost"=>"ст. Упаковщик","id"=>12),
				array("dolgnost"=>"Упаковщики","id"=>7)
			);
			for($i=0; $i<count($aDolgnost);$i++)
			{
				$res=$m->query("SELECT w.FIO, DATE_FORMAT(d.timestart,'%d.%m.%Y') AS DateStart FROM workers w, workersdidlayn d WHERE w.id=d.idworker AND w.fired<>1 AND d.timestop IS NULL AND w.DolgnostID=".$aDolgnost[$i]["id"]);
				if($res->num_rows>0)
					echo '<p><h1 class="h1">'.$aDolgnost[$i]["dolgnost"].'</h1></p>';
				while($r=$res->fetch_assoc())
					echo '<p class="LeftMenu">'.$r['FIO'].'</p>';
			}
		break;
		//----------------------------------------------

		//Выбор заказов на выполнении
		case 'SelectOrders':
			$result=$m->query("SELECT o.* , DATE_FORMAT(o.BlankDate,'%d.%m.%Y') as bd , DATE_FORMAT(o.ShetDate,'%d.%m.%Y') as sd  FROM oreders AS o, orderdoors AS d WHERE o.id=d.idOrder AND o.status=1 GROUP BY o.Blank ORDER BY o.BlankDate");
			while ($line = $result->fetch_assoc())
			{
				$color='white';
				switch($line['status'])
				{
					case 1: $color='yellow'; break;
					case 2: $color='lightgreen'; break;
				};
				echo '<tr bgcolor='.$color.' onmouse id=OrderTableTR'.$line['id'].' onClick="OrderEditFunction('.$line['id'].')">'.
				'<td></td>'.
				'<td>'.$line['Blank'].'</td>'.
				'<td>'.$line['bd'].'</td>'.
				'<td>'.$line['Shet'].'</td>'.
				'<td>'.$line['sd'].'</td>'.
				'<td>'.$line['Srok'].'</td>'.
				'<td>'.$line['Zakaz'].'</td>'.
				'<td>'.$line['Contact'].'</td>'.
				'</tr>';
			}
			mysql_free_result($result);
		break;

		//--------------------Лазер--------------------
		case 'LaserSelect':
		/*
		$r=$m->query("SELECT ".($XMLParams->Global->ViewNumOrder=="Blank"? "o1.Blank" : "o1.Shet as Blank").", o.name, o.H, o.W, o.S, o.SEqual, n.id, n.idDoors, n.LaserWork, n.LaserDate FROM naryad n, orderdoors o, oreders o1 WHERE o1.id=o.idOrder AND o.id=n.idDoors AND DATE_FORMAT( n.LaserDate,'%d.%m.%Y')=DATE_FORMAT( NOW(),'%d.%m.%Y')");
			while($d=$r->fetch_assoc())
				echo '<tr>'.
					'<td>'.$d['Blank'].'</td>'.
					'<td>'.$d['name'].'</td>'.
					'<td>'.$d['H'].'</td>'.
					'<td>'.$d['W'].'</td>'.
					'<td>'.$d['LaserWork'].'</td>'.
					'<td>'.$d['LaserDate'].'</td>'.
					'</tr>';
					*/
		break;

		//--------------------Сгибка--------------------
		case 'SgibkaSelect':
		/*
		$r=$m->query("SELECT ".($XMLParams->Global->ViewNumOrder=="Blank"? "o1.Blank" : "o1.Shet as Blank").", o.name, o.H, o.W, o.S, o.SEqual, n.id, n.idDoors, n.SgibkaWork, n.SgibkaDate FROM naryad n, orderdoors o, oreders o1 WHERE o1.id=o.idOrder AND o.id=n.idDoors AND DATE_FORMAT( n.SgibkaDate,'%d.%m.%Y')=DATE_FORMAT( NOW(),'%d.%m.%Y') ORDER BY o1.Blank, o.NumPP, n.NumPP");
			while($d=$r->fetch_assoc())
				echo '<tr>'.
					'<td>'.$d['Blank'].'</td>'.
					'<td>'.$d['name'].'</td>'.
					'<td>'.$d['H'].'</td>'.
					'<td>'.$d['W'].'</td>'.
					'<td>'.$d['SgibkaWork'].'</td>'.
					'<td>'.$d['SgibkaDate'].'</td>'.
					'</tr>';
					*/
		break;

		//--------------Наряды------------------------
		case 'NaryadTempListSelect':
		/*
			$d=$m->query("SELECT * FROM 
				(select nc1.idNaryad, w1.FIO AS LaserFIO, nc1.DateComplite AS LaserComplite, w2.FIO AS SgibkaFIO, nc2.DateComplite AS SgibkaComplite, nc3.idWorker, nc3.DateComplite FROM naryadcomplite nc1, naryadcomplite nc2, naryadcomplite nc3, workers w1, workers w2 WHERE nc1.idNaryad=nc2.idNaryad AND nc2.idNaryad=nc3.idNaryad AND nc1.Step=1 AND nc2.Step=2 AND nc3.Step=3 AND nc1.idWorker=w1.id AND nc2.idWorker=w2.id) NaryadC
				RIGHT JOIN
				(SELECT od.name, od.H, od.W, od.S, od.SEqual, n.id AS idNaryad, n.Num, n.NumPP, n.AlertStatus FROM OrderDoors od, Naryad n WHERE n.SgibkaCompliteFlag=1 AND n.SvarkaCompliteFlag=0 AND n.SborkaCompliteFlag=0 AND n.ColorCompliteFlag=0 AND n.UpakCompliteFlag=0 AND n.ShptCompliteFlag=0 AND n.idDoors=od.id) NaryadN
				ON NaryadC.idNaryad=NaryadN.idNaryad
				".($XMLParams->Enterprise->SkipPurposeWelder=="0"?" WHERE NaryadC.idWorker IS NULL ":"")."
				ORDER BY NaryadN.Num, NaryadN.NumPP");
			$a=array(); $i=0;
			while($r=$d->fetch_assoc()){
				$a[$i]=Array(
					'id'=>$r['idNaryad'],
					'Blank'=>$r['Num'].$r['NumPP'],
					'name'=>$r['name'],
					'H'=>$r['H'],
					'W'=>$r['W'],
					'S'=>($r['S']!=null || $r["SEqual"]==1 ? ($r["S"]!=""? $r["S"] : "Равн.") : ""),
					'LaserDate'=>$r['LaserComplite'],
					'LaserWork'=>$r['LaserFIO'],
					'SgibkaDate'=>$r["SgibkaComplite"],
					'SgibkaWork'=>$r["SgibkaFIO"],
					'AlertStatus'=>$r['AlertStatus']
				);
				$i++;
			}
			echo json_encode($a);
			*/
		break;

		case 'NarydEditStart':
			$r=$m->query("SELECT n.NumInOrder, n.Num, n.NumPP, o1.name, o1.H, o1.W, o1.S, o1.SEqual, o1.Nalichnik, o1.RAL, o1.Zamok, o1.Note as DoorNote, o1.Shtild, n.Master, n.Note, o1.CostLaser, o1.CostSgibka, o1.CostSvarka, o1.CostFrame, o1.CostMdf, o1.CostSborka, o1.CostColor, o1.CostSborkaMdf, o1.CostUpak, o1.CostShpt, n.LaserCompliteFlag, n.SgibkaCompliteFlag, n.SvarkaCompliteFlag, n.FrameCompliteFlag, n.MdfCompliteFlag, n.SborkaCompliteFlag, n.ColorCompliteFlag, n.SborkaMdfCompliteFlag, n.UpakCompliteFlag, DATE_FORMAT(n.ShptAllowDate, '%d.%m.%Y %H:%i:%S') AS ShptAllowDate, n.ShptAllowWork, n.ShptCompliteFlag FROM naryad n, oreders o, orderdoors o1 WHERE n.idDoors=o1.id AND o1.idOrder=o.id AND n.id=".$_POST['id']);
			$d=$r->fetch_assoc();
			//------Обработка шильды----------------
			$Shtild="";
			if($d["Shtild"]!="" & is_int((int)$d["Shtild"]))
			{
				$Shtild=(int)$d["Shtild"]+(int)$d["NumPP"]-1;
				//Если число 0007, тогда необходимо комписировать 0 в начале
				$s_new=strval($Shtild);
				for($i=0;$i<strlen($d["Shtild"]);$i++)
					if(strlen($d["Shtild"])>strlen($s_new))
						$s_new="0".$s_new;
				$Shtild=$s_new;
			};
			if((int)$Shtild==(int)$d["NumPP"]) $Shtild=$d["Shtild"]; //Проверка если номер шилбды не число тогда выводим его знач
			//-----------------------------------------------------
			$aConstruct=array(
				'Blank'=>$d['Num'].$d["NumPP"],
				'name'=>$d['name'],
				"NumInOrder"=>$d["NumInOrder"],
				'H'=>$d['H']!=null ? $d["H"] : "",
				'W'=>$d['W']!=null ? $d["W"] : "",
				'S'=>($d['S']!=null?$d['S']:"").($d['SEqual']=="1"?"Равн.":""),
				'Nalichnik'=>$d['Nalichnik'],
				'RAL'=>$d['RAL'],
				'Zamok'=>$d['Zamok'],
				'Shtild'=>$Shtild,
				"Master"=>$d["Master"],
				'Note'=>$d['Note'],

				"DoorNote"=>$d["DoorNote"],
				"CostLaser"=>$d["CostLaser"],
				"CostSgibka"=>$d["CostSgibka"],
				"CostSvarka"=>$d["CostSvarka"],
				"CostFrame"=>$d["CostFrame"],
				"CostMdf"=>$d["CostMdf"],
				"CostSborka"=>$d["CostSborka"],
				"CostColor"=>$d["CostColor"],
				"CostSborkaMdf"=>$d["CostSborkaMdf"],
				"CostUpak"=>$d["CostUpak"],
				"CostShpt"=>$d["CostShpt"],

				"LaserCompliteFlag"=>$d["LaserCompliteFlag"],
				"SgibkaCompliteFlag"=>$d["SgibkaCompliteFlag"],
				"SvarkaCompliteFlag"=>$d["SvarkaCompliteFlag"],
				"FrameCompliteFlag"=>$d["FrameCompliteFlag"],
				"MdfCompliteFlag"=>$d["MdfCompliteFlag"],
				"SborkaCompliteFlag"=>$d["SborkaCompliteFlag"],
				"ColorCompliteFlag"=>$d["ColorCompliteFlag"],
				"SborkaMdfCompliteFlag"=>$d["SborkaMdfCompliteFlag"],
				"UpakCompliteFlag"=>$d["UpakCompliteFlag"],
				"ShptAllowDate"=>$d["ShptAllowDate"],
				"ShptAllowWork"=>$d["ShptAllowWork"],
				"ShptCompliteFlag"=>$d["ShptCompliteFlag"]
				);
			//Выводим список выполнения
			$d=$m->query("SELECT nc.id, nc.Step, nc.idWorker, w.FIO, nc.Cost, DATE_FORMAT(nc.DateAppointment,'%d.%m.%Y') AS DateAppointment, DATE_FORMAT(nc.DateComplite,'%d.%m.%Y %H:%i:%S') AS DateComplite FROM NaryadComplite nc LEFT JOIN  Workers w ON nc.idWorker=w.id WHERE nc.idNaryad=".$_POST['id']);
			$aComplite=array(); $i=0;
			while($r=$d->fetch_assoc())
			{
				$aComplite[$i]=array(
						"id"=>$r["id"],
						"Step"=>$r["Step"],
						"idWorker"=>$r["idWorker"],
						"FIO"=>$r["FIO"],
						"Cost"=>$r["Cost"],
						"DateAppointment"=>$r["DateAppointment"],
						"DateComplite"=>$r["DateComplite"]
					);
				$i++;
			};

			echo json_encode(Array(
				"Construct"=>$aConstruct,
				"Complite"=>$aComplite
			));
		break;

		case 'NaryadTempRemove':
			$m->query('UPDATE Naryad SET AlertStatus=1 WHERE id='.$_POST['id']);
			echo 'ok';
		break;

		case 'NaryadSelectWorks':
			$sSelectWork='';
			$sSelectDate='';
			$sIdDolgnost=0;
			switch($_POST['typeSelect'])
			{
				case 'Svarka':$sSelectWork='SvarkaWork'; $sSelectDate='SvarkaDate'; $sIdDolgnost='DolgnostID=3 OR DolgnostID=8 OR DolgnostID=2 OR DolgnostID=17 OR DolgnostID=18 OR DolgnostID=19'; break;
				case 'Frame':$sSelectWork='SvarkaWork'; $sSelectDate='SvarkaDate'; $sIdDolgnost='DolgnostID=3 OR DolgnostID=8 OR DolgnostID=2 OR DolgnostID=17 OR DolgnostID=18 OR DolgnostID=19'; break;
				//case 'Frame':$sSelectWork='FrameCompliteWork'; $sSelectDate='FrameComplite'; $sIdDolgnost='DolgnostID=15'; break;
				case 'Color':$sSelectWork='ColorCompliteWork'; $sSelectDate='ColorComplite'; $sIdDolgnost='DolgnostID=11 OR DolgnostID=2 OR DolgnostID=17 OR DolgnostID=18 OR DolgnostID=19'; break;
				case 'Sborka':$sSelectWork='SborkaCompliteWork'; $sSelectDate='SborkaComplite'; $sIdDolgnost='DolgnostID=4 OR DolgnostID=9 OR DolgnostID=2 OR DolgnostID=17 OR DolgnostID=18 OR DolgnostID=19'; break;
				case 'Upak':$sSelectWork='UpakCompliteWork'; $sSelectDate='UpakComplite'; $sIdDolgnost='DolgnostID=12 OR DolgnostID=2 OR DolgnostID=17 OR DolgnostID=18 OR DolgnostID=19'; break;
				case 'Shpt':$sSelectWork='ShptCompliteWork'; $sSelectDate='ShptComplite'; $sIdDolgnost='DolgnostID=7 OR DolgnostID=12 OR DolgnostID=2 OR DolgnostID=17 OR DolgnostID=18 OR DolgnostID=19'; break;
			};
			$d=$m->query('SELECT Num, FIO FROM Workers WHERE fired<>1 AND '.$sIdDolgnost." ORDER BY FIO");
			$a=array();
			$i=0;
			while($r=$d->fetch_assoc())
			{
				$SQLstr="";
				switch($_POST["typeSelect"])
				{
					case "Svarka":$SQLstr='SELECT count(*) as c FROM naryad WHERE (SvarkaCompliteWork="'.$r['FIO'].'" AND DATE_FORMAT(SvarkaComplite,"%d.%m.%Y") =DATE_FORMAT(Now(),"%d.%m.%Y") ) OR (SvarkaCompliteWork="'.$r['FIO'].'" AND SvarkaComplite IS NULL)'; break;
					case "Frame": $SQLstr='SELECT count(*) as c FROM naryad WHERE (FrameCompliteWork="'.$r['FIO'].'" AND DATE_FORMAT(FrameComplite,"%d.%m.%Y") =DATE_FORMAT(Now(),"%d.%m.%Y") ) '; break;
					case "Color": $SQLstr='SELECT count(*) as c FROM naryad WHERE (ColorCompliteWork="'.$r['FIO'].'" AND DATE_FORMAT(ColorComplite,"%d.%m.%Y") =DATE_FORMAT(Now(),"%d.%m.%Y") ) '; break;
					case "Sborka": $SQLstr='SELECT count(*) as c FROM naryad WHERE (SborkaCompliteWork="'.$r['FIO'].'" AND DATE_FORMAT(SborkaComplite,"%d.%m.%Y") =DATE_FORMAT(Now(),"%d.%m.%Y") ) '; break;
					case "Upak": $SQLstr='SELECT count(*) as c FROM naryad WHERE (UpakCompliteWork="'.$r['FIO'].'" AND DATE_FORMAT(UpakComplite,"%d.%m.%Y") =DATE_FORMAT(Now(),"%d.%m.%Y") ) '; break;
					case "Shpt": $SQLstr='SELECT count(*) as c FROM naryad WHERE (ShptCompliteWork="'.$r['FIO'].'" AND DATE_FORMAT(ShptComplite,"%d.%m.%Y") =DATE_FORMAT(Now(),"%d.%m.%Y") ) '; break;
				};
				$d1=$m->query($SQLstr);
				$r1=$d1->fetch_assoc();
				$a[$i]=array('FIO'=>$r['Num'].' - '.$r['FIO'].' ('.$r1['c'].')');

				$i++;
			};
			echo json_encode($a);
		break;
		//Сохранение карточки наряда после редактирования
		case 'NaryadSave':
			$er="ok";
			$m->query("START TRANSACTION") or die ($er=$er."Ошибка начала транзакции");
			$EditNaryad=false;
			//Обработаем наряды на выполнении
			if(isset($_POST["Step"]))
			{
				$idNaryad=$_POST["id"];
				$Step=$_POST["Step"];
				$Status=$_POST["Status"];
				$idNC=$_POST["idNaryadComplite"];
				$Cost=$_POST["Cost"];
				$idWorker=$_POST["idWorker"];
				$DateApp=$_POST["DateAppointment"];
				$DateComplite=$_POST["DateComplite"];
				$Steps=array(
					"Svarka"=>"3",
					"Frame"=>"4",
					"Mdf"=>"9",
					"Sborka"=>"5",
					"Color"=>"6",
					"SborkaMdf"=>"10",
					"Upak"=>"7",
					"Shpt"=>"8"
					);
				//Таски для склада, если нужно
				if($XMLParams->Enterprise->StockNaryadComplite=="Enable") {
					$i = -1;
					while (isset($Status[++$i])) {
						$StepOne = $Steps[$Step[$i]];
						switch ($idWorker[$i]) {
							case "":
								if ($Status[$i] != "Add")
									$m->query("INSERT INTO stn_NaryadCompliteTasks (idDoor, idNaryad, DateCreate, Step, Type) VALUES(-1, $idNaryad, NOW(), $StepOne, 'Rollback')");
								break;
							default:
								$m->query("INSERT INTO stn_NaryadCompliteTasks (idDoor, idNaryad, DateCreate, Step, Type) VALUES(-1, $idNaryad, NOW(), $StepOne, 'WriteOf')");
								break;
						};
					};
				};
				//Производим сохранение наряда
				$i=0;
				while(isset($Status[$i]) & $er=="ok")
				{
					switch ($Status[$i]) {
						case 'Edit':
							$m->query("UPDATE NaryadComplite SET idWorker=".($idWorker[$i]==""? "NULL":$idWorker[$i]).", Cost=".$Cost[$i].", DateAppointment=".($DateApp[$i]==""?"NULL":"STR_TO_DATE('".$DateApp[$i]."','%d.%m.%Y %H:%i:%S')").", DateComplite=".($DateComplite[$i]==""?"NULL":"STR_TO_DATE('".$DateComplite[$i]."','%d.%m.%Y %H:%i:%S')")." WHERE id=".$idNC[$i]) or die($er=$er."Ошибка изменения выполнения наряда:".mysqli_error());
								$EditNaryad=true;
							break;
						case "Add":
							$m->query("INSERT INTO NaryadComplite (idNaryad, Step, idWorker, Cost, DateAppointment, DateComplite) VALUES(".($_POST["id"].", ".$Steps[$Step[$i]]).", ".($idWorker[$i]==""?"NULL":$idWorker[$i]).", ".$Cost[$i].", ".($DateApp[$i]==""?"NULL":"STR_TO_DATE('".$DateApp[$i]."','%d.%m.%Y %H:%i:%S')").", ".($DateComplite[$i]==""?"NULL":"STR_TO_DATE('".$DateComplite[$i]."','%d.%m.%Y %H:%i:%S')").")") or die($er=$er."Ошибка добавления выполнения наряда:".mysqli_error());
								$EditNaryad=true;
							break;
						default:
							# code...
							break;
					};
					$i++;
				};
			};

			//Произведем действия для таблицы Naryad
			$m->query("UPDATE Naryad SET ShptAllowWork='".$_POST["ShptAllowWork"]."', ShptAllowDate=".($_POST["ShptAllowDate"]==""?"NULL":"STR_TO_DATE('".$_POST["ShptAllowDate"]."','%d.%m.%Y %H:%i:%S')").", Master='".$_POST["Master"]."', Note='".$_POST["Note"]."', SvarkaCompliteFlag=".$_POST["SvarkaCompliteFlag"].", FrameCompliteFlag=".$_POST["FrameCompliteFlag"].", MdfCompliteFlag=".$_POST["MdfCompliteFlag"].", SborkaCompliteFlag=".$_POST["SborkaCompliteFlag"].", ColorCompliteFlag=".$_POST["ColorCompliteFlag"].", SborkaMdfCompliteFlag=".$_POST["SborkaMdfCompliteFlag"].", UpakCompliteFlag=".$_POST["UpakCompliteFlag"].", ShptCompliteFlag=".$_POST["ShptCompliteFlag"]." WHERE id=".$_POST["id"]) or die($er=$er."Ошибка изменения наряда:".mysqli_error());

			//Делаем проверку, все наряды упакованны. Если все, тогда устанавливаем статус заказа =3
			//Для уменьшения нагрузки проверяем были изменения в наряде
			if(/*$EditNaryad &*/ $er=="ok")
				if($_POST["UpakCompliteFlag"]=="1" || $_POST["ShptCompliteFlag"]=="1")
				{
					//Определим id заказа
					$d=$m->query("SELECT o.id FROM oreders o, orderdoors o1, naryad n WHERE o.id=o1.idOrder AND o1.id=n.idDoors AND n.id=".$_POST["id"])  or die ($er=$er."Ошибка:".mysqli_error());
					$idOrder=$d->fetch_row()[0];
					$d->close();
					//Определим количество возможных нарядов в пределах заказа
					$d=$m->query("SELECT SUM(o1.Count) FROM oreders o, orderdoors o1 WHERE o.id=o1.idOrder AND o.id=".$idOrder)  or die ($er=$er."Ошибка:".mysqli_error());;
					$NaryadMaxCount=$d->fetch_row()[0];
					$d->close();
					//Если все наряды упакованы - статус=2
					if($_POST["UpakCompliteFlag"]=="1" & $_POST["ShptCompliteFlag"]=="0")
					{
						//Определяем кол-во упакованных нарядов
						$d=$m->query("SELECT COUNT(*) FROM oreders o, orderdoors o1, naryad n WHERE o.id=o1.idOrder AND o1.id=n.idDoors AND n.UpakCompliteFlag=1 AND o.id=".$idOrder)  or die ($er=$er."Ошибка:".mysqli_error());
						$NaryadUpakCount=$d->fetch_row()[0];
						$d->close();
						if((int) $NaryadMaxCount==(int)$NaryadUpakCount)
							$m->query("UPDATE oreders SET status=2 WHERE id=".$idOrder)  or die ($er=$er."Ошибка:".mysqli_error());
					};
					//Если все наряды отгружены - статусу=3
					if($_POST["ShptCompliteFlag"]=="1")
					{
						//Определяем кол-во отгруженных нарядов
						$d=$m->query("SELECT COUNT(*) FROM oreders o, orderdoors o1, naryad n WHERE o.id=o1.idOrder AND o1.id=n.idDoors AND n.ShptCompliteFlag=1 AND o.id=".$idOrder)  or die ($er=$er."Ошибка:".mysqli_error());
						$NaryadUpakCount=$d->fetch_row()[0];
						$d->close();
						if((int) $NaryadMaxCount==(int)$NaryadUpakCount)
							$m->query("UPDATE oreders SET status=3 WHERE id=".$idOrder)  or die ($er=$er."Ошибка:".mysqli_error());
					};
				}
				//Если наряд находится в работе тогда присвеим статус = 1
				else
				{
					$idNaryad=$_POST["id"];
					$d=$m->query("UPDATE oreders o SET o.status=1 WHERE o.id=(SELECT od.idOrder FROM orderdoors od, naryad n WHERE od.id=n.idDoors AND n.id=$idNaryad LIMIT 1)")  or die ($er=$er."Ошибка:".mysqli_error());
				};

			//Начисление з/п Мастерам после отгрузки двери
			if($XMLParams->Enterprise->PayrollMasters->Power=="1" & $_POST["ShptCompliteFlag"]==1)
				$m->query("CALL PayrollMasters (".$_POST["id"].", ".$XMLParams->Enterprise->PayrollMasters->SummMainMaster." , ".$XMLParams->Enterprise->PayrollMasters->SumMaster.")") or die ($er=$er."Ошибка:".mysqli_error());


			if($er=="ok")
			{
				$m->query("COMMIT")  or die ($er=$er."Ошибка фиксирования транзакции:".mysqli_error());
			}
			else
				$m->query("ROLLBACK")  or die ($er=$er."Ошибка отката транзакции:".mysqli_error());
			echo $er;
		break;

		//--------Список наряда-------------------------------
		case 'NaryadListSelect':
			$d=$m->query('SELECT n.id, n.Num, n.NumPP, o.Shet, n.NumInOrder, o1.name , o1.H , o1.W, o1.S, o1.SEqual, o.status, n.Note, n.SvarkaCompliteFlag, n.FrameCompliteFlag, n.MdfCompliteFlag, n.SborkaCompliteFlag, n.ColorCompliteFlag, n.SborkaMdfCompliteFlag, n.UpakCompliteFlag, n.ShptCompliteFlag FROM naryad as n, oreders as o, orderdoors as o1 WHERE n.idDoors=o1.id AND o1.idOrder=o.id AND (n.SgibkaCompliteFlag=1 OR n.SvarkaCompliteFlag=1 OR n.SvarkaCompliteFlag=2 OR n.SborkaCompliteFlag=1 OR n.SborkaMdfCompliteFlag=1 OR n.ColorCompliteFlag=1 OR n.UpakCompliteFlag=1) '.$_POST["WHERE"]." LIMIT 500");
			$a=array();
			$i=0;
			while($r=$d->fetch_assoc())
			{
				$SgibkaFlag=(int)$r["SvarkaCompliteFlag"];
				$SvarkaFlag=(int)$r["SvarkaCompliteFlag"];
				$MdfFlag=(int)$r["MdfCompliteFlag"];
				$SborkaFlag=(int)$r["SborkaCompliteFlag"];
				$ColorFlag=(int)$r["ColorCompliteFlag"];
				$SborkaMdfFlag=(int)$r["SborkaMdfCompliteFlag"];
				$UpakFlag=(int)$r["UpakCompliteFlag"];
				$ShptFlag=(int)$r["ShptCompliteFlag"];

				$sNote="";
				$sColor='#00BFFF';
				$Step="Гибка";

				//Определим шаг, цвет и определим пропущенную стадию
				$Alert="";
				if($SgibkaFlag==1)
				{
					$sColor='#00BFFF';
					$Step="Гибка";
				};
				if($SvarkaFlag==2)
				{
					$sColor='#00BFFF';
					$Step="Сварка наз";
				};
				if($SvarkaFlag==1)
				{
					$sColor='#00FF00';
					$Step="Сварка";
				}
				else
					if($SborkaFlag==1 || $ColorFlag==1 || $UpakFlag==1 || $ShptFlag==1)
						$Alert="сварка; ";

				if($SborkaFlag==1)
				{
					$sColor='#FFFF00';
					$Step="Сборка";
				}
				else
					if($ColorFlag==1 || $UpakFlag==1 || $ShptFlag==1)
						$Alert=$Alert."сборка; ";
				if($ColorFlag==1)
				{
					$sColor='#FFA500';
					$Step="Покраска";
				}
				else
					if($SborkaMdfFlag==1 || $UpakFlag==1 || $ShptFlag==1)
						$Alert=$Alert."покарска; ";
				if(strpos($r["name"], "МДФ")>-1 & $MdfFlag==0 & $UpakFlag==1)
					$Alert=$Alert."МДФ цех; ";
				if($SborkaMdfFlag==1)
				{
					$sColor='#999900';
					$Step="Сборка МДФ";
				}
				else
					if(strpos($r["name"], "МДФ")>-1 & $SborkaMdfFlag==0 & ($UpakFlag==1 || $ShptFlag==1))
						$Alert=$Alert."сборка МДФ; ";
				if($UpakFlag==1)
				{
					$sColor='#FF6347';
					$Step="Упаковка";
				}
				else
					if( $ShptFlag==1)
						$Alert=$Alert."упаковка; ";
				if($ShptFlag==1)
				{
					$sColor='green';
					$Step="Погрузка";
				};
				//Выведем примечание
				$NoteNaryad='';
					if(!isset($r['Note']))
						if($r['Note']!=null)
							$NoteNaryad=$r['Note'];
				if(strlen($NoteNaryad)>10) $NoteNaryad=substr($NoteNaryad,0,10).' ...';

				$a[$i]=array(
					'id'=>$r['id'],
					'Blank'=>$r['Num'].$r["NumPP"],
					"Shet"=>$r["Shet"],
					"NumInOrder"=>($r["NumInOrder"]!=null?$r["NumInOrder"]:""),
					'Name'=>$r['name'],
					'W'=>$r['W']!=null ? $r['W'] : "",
					'H'=>$r['H']!=null ? $r['H'] : "",
					'S'=>$r['S']!=null?$r["S"] : ($r["SEqual"]==1? "Равн.":""),
					"Status"=>$r["status"],
					"OrderStatus"=>$r["status"],
					"Step"=>$Step,
					"ColorTR"=>$sColor,
					"NaryadNote"=>$r["Note"],
					"NaryadNoteS"=>$NoteNaryad,
					"Alert"=>$Alert!="" ? "Пропущенно: ".$Alert : ""
				);
				$i++;
			};
			echo json_encode($a);
		break;
		//Печать одинраного наряда
		case "PrintNaryad":
			$d=$m->query("SELECT o.Zakaz, o.Shet, (SELECT SUM(od2.Count) FROM oreders o2, orderdoors od2 WHERE o2.id=o.id AND o2.id=od2.idOrder) AS DoorAllCount, n.id as NaryadID, n.Num, n.NumPP, o1.name, o1.H, o1.W, o1.S, o1.Nalichnik, o1.Dovod, o1.RAL, o1.Zamok, o1.Shtild, o1.Note as DoorNote, o1.WorkWindowCh, o1.StvorkaWindowCh, o1.FramugaWindowCh, n.SvarkaCompliteWork, n.SvarkaSum, n.FrameCompliteWork, n.FrameSum, n.SborkaCompliteWork, n.SborkaSum, n.ColorCompliteWork, n.ColorSum, n.UpakCompliteWork, n.UpakSum, n.ShptCompliteWork, n.ShptSum, n.Master, n.Note as NaryadNote FROM naryad n, oreders o, orderdoors o1 WHERE n.idDoors=o1.id AND o1.idOrder=o.id AND n.id=".$_POST["id"]);
			if(file_exists("naryad.pdf") ) unlink("naryad.pdf");
			if(file_exists("naryadImg.jpg") ) unlink("naryadImg.jpg");
			if($d->num_rows>0)
			{
				$r=$d->fetch_assoc();

				$imgOut="pdfOut/naryadImg".$r["NaryadID"].".jpg";
				if(file_exists($imgOut) ) unlink($imgOut);
				copy ( "http://".$XMLParams->Global->HostName."/enterprise/naryadimg.php?idNaryad=".$r["NaryadID"], $imgOut );

				$html="";
				$f = fopen("printNaryadForm.html", "r");
				$a_in=array(
					"#imgOut","#Commercie","#NaryadID", "#Num","#Name","#Size","#Nalichnik","#Dovod","#RAL","#Zamok", "#Shtild","#DoorNote","#Svarka", "#Frame","#Sborka","#Color","#Upak","#Shpt","#NaryadNote", "#Master", "#DoorAllCount","#BarCode"
				);
				$Commercie=$XMLParams->Enterprise->ViewCommercieWhenPrint=="true"?"<b>Счет № : </b>".$r["Shet"]."<br><b>Заказчик : </b>".$r["Zakaz"] : "";
				$Svarka="__________________________  Стоимость: ".$r["SvarkaSum"]; if($r["SvarkaCompliteWork"]!="") $Svarka=$r["SvarkaCompliteWork"]."  Стоимость: ".$r["SvarkaSum"]."";
				$Frame="";
				if($r["WorkWindowCh"] || $r["StvorkaWindowCh"] || $r["FramugaWindowCh"])
					if($r["FrameCompliteWork"]!="")
					{
						$Frame="&nbsp;&nbsp; Рамка : &nbsp;<span>".$r["FrameCompliteWork"]." Стоимость: ".$r["FrameSum"]."</span><br><br>";
					}
					else
						$Frame="&nbsp;&nbsp; Рамка : &nbsp;<span>__________________________  Стоимость: ".$r["FrameSum"]."</span><br>";
				$Sborka="__________________________  Стоимость: ".$r["SborkaSum"]; if($r["SborkaCompliteWork"]!="") $Sborka=$r["SborkaCompliteWork"]." Стоимость: ".$r["SborkaSum"]."";
				$Color="__________________________  Стоимость: ".$r["ColorSum"]; if($r["ColorCompliteWork"]!="") $Color=$r["ColorCompliteWork"]." Стоимость: ".$r["ColorSum"]."";
				$Upak="__________________________  Стоимость: ".$r["UpakSum"]; if($r["UpakCompliteWork"]!="") $Upak=$r["UpakCompliteWork"]." Стоимость: ".$r["UpakSum"]."";
				$Shpt="__________________________  Стоимость: ".$r["ShptSum"]; if($r["ShptCompliteWork"]!="") $Shpt=$r["ShptCompliteWork"]." Стоимость: ".$r["ShptSum"]."";
				//------Обработка штильды----------------
				$Shtild="";
				if($r["Shtild"]!="" & is_int((int)$r["Shtild"]))
				{
					$Shtild=(int)$r["Shtild"]+(int)$r["NumPP"]-1;
					//Если число 0007, тогда необходимо комписировать 0 в начале
					$s_new=strval($Shtild);
					for($i=0;$i<strlen($r["Shtild"]);$i++)
						if(strlen($r["Shtild"])>strlen($s_new))
							$s_new="0".$s_new;
					$Shtild=$s_new;
				};
				if((int)$Shtild==(int)$r["NumPP"]) $Shtild=$r["Shtild"]; //Проверка если номер штилбды не число тогад выводим его знач
				//-----------------------------------------------------

				$a_replace=array(
					$imgOut,$Commercie,$r["NaryadID"], $r["Num"].$r["NumPP"] , $r["name"] , $r["H"]."x".$r["W"]."x".$r["S"] , $r["Nalichnik"],$r["Dovod"],"RAL ".$r["RAL"],$r["Zamok"],$Shtild,$r["DoorNote"] ,$Svarka,$Frame,$Sborka,$Color,$Upak, $Shpt, $r["NaryadNote"], $r["Master"], $r["DoorAllCount"],barcode::code39((string)$r["NaryadID"])
				);
				while(!feof($f))
					$html=$html.str_replace($a_in,$a_replace,fgets($f));
				fclose($f);

				include("../mpdf53/mpdf.php");
				$mpdf = new mPDF('utf-8', 'A5', '8', '', 10, 10, 7, 7, 10, 10); /*задаем формат, отступы и.т.д.*/
				$mpdf->charset_in = 'UTF8';
				$mpdf->list_indent_first_level = 0;
				$mpdf->WriteHTML($html, 2); /*формируем pdf*/
				//$mpdf->AddPage();
				$mpdf->Output('naryad.pdf' , 'F');
				echo "ok";
			};
		break;
		//Пакетная печать
		case "PrintNaryadPackeg":
			include("../mpdf53/mpdf.php");
			$mpdf = new mPDF('utf-8', 'A5', '8', '', 10, 10, 7, 7, 10, 10); /*задаем формат, отступы и.т.д.*/
			$mpdf->charset_in = 'UTF8';
			$mpdf->list_indent_first_level = 0;

			$d=$m->query("SELECT o.Zakaz, o.Shet, (SELECT SUM(od2.Count) FROM oreders o2, orderdoors od2 WHERE o2.id=o.id AND o2.id=od2.idOrder) AS DoorAllCount, n.id as NaryadID, n.Num, n.NumPP, o1.name, o1.H, o1.W, o1.S, o1.Nalichnik, o1.Dovod, o1.RAL, o1.Zamok, o1.Shtild, o1.Note as DoorNote, o1.WorkWindowCh, o1.StvorkaWindowCh, o1.FramugaWindowCh, n.SvarkaCompliteWork, n.SvarkaSum, n.FrameCompliteWork, n.FrameSum, n.SborkaCompliteWork, n.SborkaSum, n.ColorCompliteWork, n.ColorSum, n.UpakCompliteWork, n.UpakSum, n.ShptCompliteWork, n.ShptSum, n.Note as NaryadNote, n.Master FROM naryad n, oreders o, orderdoors o1 WHERE n.idDoors=o1.id AND o1.idOrder=o.id AND o.status<>-1 AND n.SgibkaDate IS NOT NULL AND n.SvarkaCompliteWork IS NULL");
			if(file_exists("naryad.pdf") ) unlink("naryad.pdf");

			while($r=$d->fetch_assoc())
			{

				$imgOut="pdfOut/naryadImg".$r["NaryadID"].".jpg";
				if(file_exists($imgOut) ) unlink($imgOut);
				copy ( "http://".$XMLParams->Global->HostName."/enterprise/naryadimg.php?idNaryad=".$r["NaryadID"], $imgOut );

				$mpdf->AddPage();

				$html="";
				$f = fopen("printNaryadForm.html", "r");
				$a_in=array(
					"#imgOut","#Commercie","#NaryadID", "#Num","#Name","#Size","#Nalichnik","#Dovod","#RAL","#Zamok", "#Shtild","#DoorNote","#Svarka", "#Frame","#Sborka","#Color","#Upak","#Shpt","#NaryadNote", "#Master", "#DoorAllCount","#BarCode"
				);
				$Commercie=$XMLParams->Enterprise->ViewCommercieWhenPrint=="true"?"<b>Счет № : </b>".$r["Shet"]."<br><b>Заказчик : </b>".$r["Zakaz"] : "";
				$Svarka="__________________________  Стоимость: ".$r["SvarkaSum"]; if($r["SvarkaCompliteWork"]!="") $Svarka=$r["SvarkaCompliteWork"]."  Стоимость: ".$r["SvarkaSum"]."";
				$Frame="";
				if($r["WorkWindowCh"] || $r["StvorkaWindowCh"] || $r["FramugaWindowCh"])
					if($r["FrameCompliteWork"]!="")
					{
						$Frame="&nbsp;&nbsp; Рамка : &nbsp;<span>".$r["FrameCompliteWork"]." Стоимость: ".$r["FrameSum"]."</span><br><br>";
					}
					else
						$Frame="&nbsp;&nbsp; Рамка : &nbsp;<span>__________________________  Стоимость: ".$r["FrameSum"]."</span><br><br>";
				$Sborka="__________________________  Стоимость: ".$r["SborkaSum"]; if($r["SborkaCompliteWork"]!="") $Sborka=$r["SborkaCompliteWork"]." Стоимость: ".$r["SborkaSum"]."";
				$Color="__________________________  Стоимость: ".$r["ColorSum"]; if($r["ColorCompliteWork"]!="") $Color=$r["ColorCompliteWork"]." Стоимость: ".$r["ColorSum"]."";
				$Upak="__________________________  Стоимость: ".$r["UpakSum"]; if($r["UpakCompliteWork"]!="") $Upak=$r["UpakCompliteWork"]." Стоимость: ".$r["UpakSum"]."";
				$Shpt="__________________________  Стоимость: ".$r["ShptSum"]; if($r["ShptCompliteWork"]!="") $Shpt=$r["ShptCompliteWork"]." Стоимость: ".$r["ShptSum"]."";
				//------Обработка штильды----------------
				$Shtild="";
				if($r["Shtild"]!="" & is_int((int)$r["Shtild"]))
				{
					$Shtild=(int)$r["Shtild"]+(int)$r["NumPP"]-1;
					//Если число 0007, тогда необходимо комписировать 0 в начале
					$s_new=strval($Shtild);
					for($i=0;$i<strlen($r["Shtild"]);$i++)
						if(strlen($r["Shtild"])>strlen($s_new))
							$s_new="0".$s_new;
					$Shtild=$s_new;
				};
				if((int)$Shtild==(int)$r["NumPP"]) $Shtild=$r["Shtild"]; //Проверка если номер штилбды не число тогад выводим его знач
				//-----------------------------------------------------

				$a_replace=array(
					$imgOut, $Commercie,$r["NaryadID"], $r["Num"].$r["NumPP"] , $r["name"] , $r["H"]."x".$r["W"]."x".$r["S"] , $r["Nalichnik"],$r["Dovod"],"RAL ".$r["RAL"],$r["Zamok"],$Shtild,$r["DoorNote"] ,$Svarka,$Frame,$Sborka,$Color,$Upak, $Shpt, $r["NaryadNote"], $r["Master"], $r["#DoorAllCount"],barcode::code39((string)$r["NaryadID"])
				);
				while(!feof($f))
					$html=$html.str_replace($a_in,$a_replace,fgets($f));
				fclose($f);


				$mpdf->WriteHTML($html, 2); /*формируем pdf*/

			};
			$mpdf->Output('naryad.pdf' , 'F');
			echo "ok";
		break;

		case "StockSelect":
			$d=$m->query('SELECT n.Num, n.NumPP , o1.name , o1.H , o1.W, o1.S, n.*, DATE_FORMAT(n.UpakComplite,"%d.%m.%Y") as UpakDt FROM naryad as n, oreders as o, orderdoors as o1 WHERE n.idDoors=o1.id AND o1.idOrder=o.id AND n.UpakComplite IS NOT null AND n.ShptComplite IS NULL AND o.status<>-1 ORDER BY o.Blank, o1.NumPP, n.NumPP');
			$a=array();
			$i=0;
			while($r=$d->fetch_assoc())
			{
				$S=""; if($r["S"]!=null) $S="x".$r["S"];
				$a[$i]=array(
					'id'=>$r['id'],
					'Blank'=>$r['Num'].$r["NumPP"],
					'Name'=>$r['name'],
					'W'=>$r['W'],
					'H'=>$r['H'],
					'S'=>$S,
					"ShptAllowDate"=>$r["ShptAllowDate"],
					'UpakDt'=>$r["UpakDt"]
				);
				$i++;
			};
			echo json_encode($a);
		break;
		//----------------------------------------------Склад упакованных дверей------------------------------------------------
		//Отображение списка заказов
		case "StockSelectOrders":
			$d=$m->query("SELECT o.id, ".($XMLParams->Global->ViewNumOrder=="Blank"? "o.Blank" : "o.Shet as Blank").", (SELECT SUM(o2.Count) FROM orderdoors o2 WHERE o2.idOrder=o.id) AS SumDoors, SUM( o1.c) AS SumDoorsUpak FROM oreders o
				INNER JOIN
				(
					SELECT o.idOrder, o.Count, Count(n.id) AS c FROM orderdoors o
					INNER JOIN naryad n
					ON o.id = n.idDoors
					WHERE n.UpakCompliteFlag=1 AND n.ShptCompliteFlag=0
					GROUP BY o.id
				) AS o1
				ON o1.idOrder=o.id
				WHERE o.status<>-1
				GROUP BY o.id
				Order By o.Blank LIMIT 20");
			$a=array(); $i=0;
			while($r=$d->fetch_assoc())
			{
				$a[$i]=array(
					"id"=>$r["id"],
					"Blank"=>$r["Blank"],
					"SumDoors"=>$r["SumDoors"],
					"SumDoorsUpak"=>$r["SumDoorsUpak"]
				);
				$i++;
			};
			$d->close();
			echo json_encode($a);
		break;
		//Список дверей в заказе
		case "StockSelectOrderDoors":
			$d=$m->query("
			SELECT o.id as idDoor, o.NumPP, o.Count, Count(n.id) AS CountNaryad FROM orderdoors o
				INNER JOIN naryad n
				ON o.id = n.idDoors
				WHERE n.UpakCompliteFlag=1 AND n.ShptCompliteFlag=0 AND o.idOrder=".$_POST["idOrder"]."
				GROUP BY o.id
			");
			$a=array(); $i=0;
			while($r=$d->fetch_assoc())
			{
				$a[$i]=array(
					"idDoor"=>$r["idDoor"],
					"NumPP"=>$r["NumPP"],
					"Count"=>$r["Count"],
					"CountNaryad"=>$r["CountNaryad"],
				);
				$i++;
			};
			echo json_encode($a);
		break;
		//Список нардов в позиции
		case "StockSelectOrderDoorsNaryad":
			$d=$m->query("SELECT n.id as idNaryad, n.Num, n.NumPP, o.name, o.H, o.W, o.S, n.ShptAllowDate FROM naryad n, orderdoors o WHERE n.UpakCompliteFlag=1 AND n.ShptCompliteFlag=0 AND n.idDoors=".$_POST["idDoor"]." AND o.id=n.idDoors ORDER BY n.Num, n.NumPP");
			$a=array(); $i=0;
			while($r=$d->fetch_assoc())
			{
				$size=$r["H"]."x".$r["W"]; if($r["S"]!=null) $size=$size."x".$r["S"];
				$a[$i]=array(
					"idNaryad"=>$r["idNaryad"],
					"Num"=>$r["Num"],
					"NumPP"=>$r["NumPP"],
					"Size"=>$size,
					"Name"=>$r["name"],
					"ShptAllowDate"=>$r["ShptAllowDate"]
				);
				$i++;
			};
			echo json_encode($a);
		break;
		//Установка - разрешена/отменена отгрузка
		case "StockAllowStep2":
			session_start();
			$er="ok";
			switch($_POST["TypeSelect"])//1 - всех нарядов в заказе 2- нарядов в позиции 3- нарядов
			{
				case 1:
					if($_POST["Action"]=="Allow")
					{
						$m->query("UPDATE naryad n SET n.ShptAllowDate=NOW(), n.ShptAllowWork='".$_SESSION["AutorizeFIO"]."' WHERE  n.UpakCompliteFlag=1 AND n.ShptCompliteFlag=0 AND n.idDoors IN (SELECT o.id FROM orderdoors o WHERE o.idOrder=".$_POST["id"]." )") or die ($er="Произошла ошибка");
					}
					else
						$m->query("UPDATE naryad n SET n.ShptAllowDate=null	, n.ShptAllowWork=null WHERE n.idDoors IN (SELECT o.id FROM orderdoors o WHERE n.UpakCompliteFlag=1 AND n.ShptCompliteFlag=0 AND o.idOrder=".$_POST["id"]." )") or die ($er="Произошла ошибка");
				break;
				case 2:
					if($_POST["Action"]=="Allow")
					{
						$m->query("UPDATE naryad n SET n.ShptAllowDate=NOW(), n.ShptAllowWork='".$_SESSION["AutorizeFIO"]."' WHERE n.UpakCompliteFlag=1 AND n.ShptCompliteFlag=0 AND n.idDoors=".$_POST["id"]." ") or die ($er="Произошла ошибка");
					}
					else
						$m->query("UPDATE naryad n SET n.ShptAllowDate=null	, n.ShptAllowWork=null WHERE n.UpakComplite IS NOT null AND n.ShptCompliteFlag=0 AND n.idDoors=".$_POST["id"]." ") or die ($er="Произошла ошибка");
				break;
				case 3:
					if($_POST["Action"]=="Allow")
					{
						$m->query("UPDATE naryad n SET n.ShptAllowDate=NOW(), n.ShptAllowWork='".$_SESSION["AutorizeFIO"]."' WHERE n.id=".$_POST["id"]."") or die ($er="Произошла ошибка");
					}
					else
						$m->query("UPDATE naryad n SET n.ShptAllowDate=null	, n.ShptAllowWork=null WHERE n.id=".$_POST["id"]." ") or die ($er="Произошла ошибка");
				break;
			};
			echo $er;
		break;
		//----------------------------------------------------------------------------------------------------------------------
		//Установка статуса отгружено
		case "ShptStatus":
			$er="ok";
			$m->query("UPDATE naryad SET ShptAllowDate=Now(), ShptAllowWork='".$_POST["FIOMaster"]."' WHERE id=".$_POST["id"]) or die ($er="Произошла ошибка SQL");
			echo $er;
		break;

		case "ShptAllowComplite":
			$er="ok";
			session_start();
			$FIO=$_SESSION["AutorizeFIO"];
			if($FIO=="") $er="Error FIO session ";
			$m->query("START TRANSACTION") or die ($er=$er."Ошибка начала транзакции ");
			$i=0; $aID=$_POST["aID"];
			while(isset($aID[$i]) & $er=="ok")
			{
				$m->query("UPDATE naryad SET ShptAllowDate=NOW(), ShptAllowWork='".$FIO."' WHERE idDoors=".$aID[$i]) or die ($er=$er."Error update query ");
				$i++;
			};
			if($er=="ok")
			{
				$m->query("COMMIT")  or die ($er=$er."Ошибка:".mysqli_error());
			}
			else
				$m->query("ROLLBACK")  or die ($er=$er."Ошибка:".mysqli_error());
			echo $er;
		break;
		//------------------Отгруженные двери (новая редакция)-----------------
		case "ShptNewOrdersSelect":
			$OrderBy="n.ShptComplite"; if($_POST["OrderBy"]==1) $OrderBy=$XMLParams->Global->ViewNumOrder=="Blank"? "o.Blank":"o.Shet";
			$Where=$XMLParams->Global->ViewNumOrder=="Blank"? $_POST["Where"]:str_replace("o.Blank","o.Shet",$_POST["Where"]);
			//$d=$m->query("SELECT o.id, ".($XMLParams->Global->ViewNumOrder=="Blank"? "o.Blank" : "o.Shet as Blank").", (SELECT SUM(o1.Count) FROM orderdoors o1 WHERE o1.idOrder=o.id) AS Count, COUNT(nc.DateComplite) AS CountComplite FROM oreders o, orderdoors od, naryad n, NaryadComplite nc WHERE o.id=od.idOrder AND od.id=n.idDoors AND n.ShptCompliteFlag=1 AND n.id=nc.idNaryad ".$Where." GROUP BY o.Blank ORDER BY ".$OrderBy);
			//echo "SELECT o.id, ".($XMLParams->Global->ViewNumOrder=="Blank"? "o.Blank" : "o.Shet as Blank").", (SELECT SUM(o1.Count) FROM orderdoors o1 WHERE o1.idOrder=o.id) AS Count, COUNT(*) AS CountComplite FROM oreders o, orderdoors od, naryad n, NaryadComplite nc WHERE o.id=od.idOrder AND od.id=n.idDoors AND n.id=nc.idNaryad AND n.ShptCompliteFlag=1 ".$Where." GROUP BY o.Blank ORDER BY ".$OrderBy;
			$d=$m->query("SELECT o.id, ".($XMLParams->Global->ViewNumOrder=="Blank"? "o.Blank" : "o.Shet as Blank").", (SELECT SUM(o1.Count) FROM orderdoors o1 WHERE o1.idOrder=o.id) AS Count, COUNT(*) AS CountComplite FROM oreders o, orderdoors od, naryad n, NaryadComplite nc WHERE o.id=od.idOrder AND od.id=n.idDoors AND n.id=nc.idNaryad AND n.ShptCompliteFlag=1 AND nc.Step=8 ".$Where." GROUP BY o.Blank ORDER BY ".$OrderBy);
			$a=array(); $i=0;
			while($r=$d->fetch_assoc())
			{
				$a[$i]=array(
					"id"=>$r["id"],
					"Blank"=>$r["Blank"],
					"Count"=>$r["Count"],
					"CountComplite"=>$r["CountComplite"]
				);
				$i++;
			};
			echo json_encode($a);
		break;
		case "ShptNewDoorsSelect":
			$Where=$XMLParams->Global->ViewNumOrder=="Blank"? $_POST["Where"]:str_replace("o.Blank","o.Shet",$_POST["Where"]);
			$d=$m->query("SELECT od.id, od.NumPP, od.name, od.H, od.W, od.S, od.Count, COUNT(nc.DateComplite) as CountComplite FROM naryad n, NaryadComplite nc,  orderdoors od, oreders o WHERE o.id=od.idOrder AND od.idOrder=".$_POST["idOrder"]." AND od.id=n.idDoors AND n.ShptCompliteFlag=1 AND n.id=nc.idNaryad AND nc.Step=8 ".$Where." GROUP BY od.id ORDER BY od.NumPP DESC");
			$a=array(); $i=0;
			while($r=$d->fetch_assoc())
			{
				$size=$r["H"]."x".$r["W"]; if($r["S"]!=null) $size=$r["H"]."x".$r["W"]."x".$r["S"];
				$a[$i]=array(
					"id"=>$r["id"],
					"NumPP"=>$r["NumPP"],
					"Name"=>$r["name"],
					"Size"=>$size,
					"Count"=>$r["Count"],
					"CountComplite"=>$r["CountComplite"]
				);
				$i++;
			};
			echo json_encode($a);
		break;
		case "ShptNewNaryadsSelect":
			$Where=$XMLParams->Global->ViewNumOrder=="Blank"? $_POST["Where"]:str_replace("o.Blank","o.Shet",$_POST["Where"]);
			$d=$m->query("SELECT n.id,CONCAT(n.Num,n.NumPP) AS Num, (SELECT w.FIO FROM workers w WHERE w.id=nc.idWorker LIMIT 1) AS ShptCompliteWork, DATE_FORMAT(nc.DateComplite,'%d.%m.%y') AS ShptComplite FROM naryad n, NaryadComplite nc, orderdoors od, oreders o WHERE o.id=od.idOrder AND od.id=n.idDoors AND n.ShptCompliteFlag=1 AND nc.Step=8 ".$Where." AND n.idDoors=".$_POST["idDoor"]." AND n.id=nc.idNaryad GROUP BY n.id ORDER BY n.NumPP DESC");
			$a=array(); $i=0;
			while($r=$d->fetch_assoc())
			{
				$a[$i]=array(
					"id"=>$r["id"],
					"Num"=>$r["Num"],
					"ShptCompliteWork"=>$r["ShptCompliteWork"],
					"ShptComplite"=>$r["ShptComplite"]
				);
				$i++;
			};
			echo json_encode($a);
		break;
		//--Отчет для налоговой--
		case "ShptNewNalogReport":
			$CompliteStatus="";
			$CompliteNote="";

			$DoorType="";
			$aCalc=array();
			$Material="";
			$Unit="";
			$CalcType=0;
			$Count=0;
			$Price=0;

			//Формируем массив дверей
			$Where=" 1=1 ";
			if($_POST["Blank"]!="") $Where=$Where.($XMLParams->Global->ViewNumOrder=="Blank"? "AND o.Blank=".$_POST["Blank"]." " : "AND o.Shet='".$_POST["Blank"]."' ");
			if($_POST["DateWith"]!="" & $_POST["DateBy"]!="") $Where=$Where."AND n.ShptComplite BETWEEN STR_TO_DATE('".$_POST["DateWith"]."','%d.%m.%Y') AND DATE_ADD(STR_TO_DATE('".$_POST["DateBy"]."','%d.%m.%Y'), INTERVAL 1 DAY) ";
			if($_POST["DateWith"]=="" || $_POST["DateBy"]=="") $Where=$Where." AND n.ShptComplite IS NOT NULL";
			$d=$m->query("SELECT o.Blank, o.Shet, o.Zakaz, o1.name, o1.H, o1.W, n.ShptComplite FROM oreders o, orderdoors o1, naryad n WHERE o.id=o1.idOrder AND o1.id=n.idDoors AND".$Where);

			require_once '../PHPWord/PHPWord.php';
			$PHPWord = new PHPWord();
			$PHPWord->addParagraphStyle('pStyleLeft', array('align'=>'left', 'spaceAfter'=>100));
			$PHPWord->addParagraphStyle('pStyleRight', array('align'=>'right', 'spaceAfter'=>100));
			//$section = $PHPWord->createSection();
			$styleTable = array('borderSize'=>6, 'borderColor'=>'006699', 'cellMargin'=>0, 'cellMarginBottom'=>0);
			$styleFirstRow = array( 'borderBottomColor'=>'4f4f4f', 'bgColor'=>'d6d2d2');
			$styleCell = array('valign'=>'center');
			$styleCellBTLR = array('valign'=>'center', 'textDirection'=>PHPWord_Style_Cell::TEXT_DIR_BTLR);
			$fontStyle = array('bold'=>true, 'align'=>'center');
			$PHPWord->addTableStyle('myOwnTableStyle', $styleTable, $styleFirstRow);
			if($d->num_rows>0)
			while($r=$d->fetch_assoc())
			{
				if($r["name"]!=$DoorType)
				{
					$DoorType=$r["name"];
					$dCalc=$m->query("SELECT * FROM nalogcalc WHERE DoorType='".$r["name"]."'");
					if($dCalc->num_rows>0)
					{
						$aCalc=array(); $i=0;
						while($rCalc=$dCalc->fetch_assoc())
						{
							$aCalc[$i]=array(
								"Material"=>$rCalc["Material"],
								"Unit"=>$rCalc["Unit"],
								"CalcType"=>$rCalc["CalcType"],
								"Count"=>$rCalc["Count"],
								"Price"=>$rCalc["Price"]
							);
							$i++;
						};
					}
					else
					{
						$CompliteStatus="err"; $CompliteNote="В справочнике нет расчетов под тип: ".$r["name"];
						break;
					};
				};
				$section = $PHPWord->createSection();
				$section->addText("УТВЕРЖДАЮ\t\t",null,"pStyleRight");
				$section->addText("Директор\t\t",null,"pStyleRight");
				$section->addText("________________ Мощинский И. И.",null,"pStyleRight");
				$section->addText("\t\t\tКалькуляция",null,"pStyleLeft");
				$section->addText("Дверь: ".$r["name"],null,"pStyleLeft");
				$section->addText("Дверь металл ".$r["H"]."x".$r["H"],null,"pStyleLeft");
				$section->addText($r["Zakaz"],null,"pStyleLeft");
				$table = $section->addTable('myOwnTableStyle');
				$table->addRow();
				$table->addCell(250,$styleCell)->addText("Материал", $fontStyle);
				$table->addCell($styleCell)->addText("Ед изм", $fontStyle);
				$table->addCell(400)->addText("Кол-во на ед.", $fontStyle);
				$table->addCell($styleCell)->addText("Цена", $fontStyle);
				$table->addCell($styleCell)->addText("Сумма", $fontStyle);
				foreach($aCalc as $v)
				{
					$table->addRow();
					$table->addCell($styleCell)->addText($v["Material"], $fontStyle);
					$table->addCell($styleCell)->addText($v["Unit"], $fontStyle);
					switch((int)$v["CalcType"])
					{
						//за ед изделия
						case 0:
							$table->addCell(400)->addText($v["Count"], $fontStyle);
							$table->addCell($styleCell)->addText($v["Price"], $fontStyle);
							$table->addCell($styleCell)->addText(number_format($v["Count"]*$v["Price"],2,',',' '), $fontStyle);
						break;
						//на ед площади
						case 1:
							$table->addCell(400)->addText($v["Count"]*$r["H"]*$r["W"], $fontStyle);
							$table->addCell($styleCell)->addText($v["Price"], $fontStyle);
							$table->addCell($styleCell)->addText(number_format($v["Count"]*$v["Price"]*$r["H"]*$r["W"],2,',',' '), $fontStyle);
						break;
					};
				};
				$section->addText("Составил: _______________________",null,"pStyleLeft");
			};
			$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
			$objWriter->save('AdvancedTable.docx');

		break;

		//---------Диалог печати---------
		case "PrintDialogOrderLoad":
			$d=$m->query("SELECT o.id, o.".($XMLParams->Global->ViewNumOrder=="Shet"?"Shet":"Blank")." AS Blank, COUNT(*) as CountNaryad FROM oreders o, orderdoors o1, naryad n WHERE o.id=o1.idOrder AND o1.id=n.idDoors AND n.SgibkaCompliteFlag=1 ".($XMLParams->Enterprise->SkipPurposeWelder=="1"?"AND n.SvarkaCompliteFlag=0":"AND n.SvarkaCompliteFlag=2")." GROUP BY o.id ORDER BY o.".($XMLParams->Global->ViewNumOrder=="Shet"?"Shet":"Blank")." ");
			$a = array(); $i=0;
			if($d)
				while($r=$d->fetch_assoc()){
					$a[$i]=array("id"=>$r["id"], "Blank"=>$r["Blank"], "CountNaryad"=>$r["CountNaryad"]);
					$i++;
				};
			echo json_encode($a);
		break;
		//Раскрытие списка дверей/наряда
		case "PrintDialogDoorNaryadLoad":
			$a=array(); $i=0;
			switch($_POST["Type"])
			{
				case "Doors":
					$d=$m->query("SELECT o1.id, o1.NumPP, o1.name, o1.H, o1.W, o1.S, o1.SEqual,  COUNT(*) as CountNaryad FROM orderdoors o1, naryad n WHERE o1.idOrder=".$_POST["id"]." AND o1.id=n.idDoors AND n.SgibkaCompliteFlag=1 ".($XMLParams->Enterprise->SkipPurposeWelder=="1"?"AND n.SvarkaCompliteFlag=0":"AND n.SvarkaCompliteFlag=2")." GROUP BY o1.id ORDER BY o1.NumPP DESC");
					if($d)
						while($r=$d->fetch_assoc()){
							$a[$i]=array(
								"id"=>$r["id"],
								"NumPP"=>$r["NumPP"],
								"Name"=>$r["name"],
								"H"=>$r["H"],
								"W"=>$r["W"],
								"S"=>($r["S"]!=""?" x ".$r["S"]:"").($r["SEqual"]==1?" x Равн.":""),
								"CountNaryad"=>$r["CountNaryad"]
							);
							$i++;
						};
				break;
				case "Naryads":
					$d=$m->query("SELECT n.id, n.Num, n.NumPP FROM naryad n WHERE n.idDoors=".$_POST["id"]." AND n.SgibkaCompliteFlag=1 ".($XMLParams->Enterprise->SkipPurposeWelder=="1"?"AND n.SvarkaCompliteFlag=0":"AND n.SvarkaCompliteFlag=2")." ORDER BY n.NumPP DESC");
					if($d)
						while($r=$d->fetch_assoc()){
							$a[$i]=array(
								"id"=>$r["id"],
								"Num"=>$r["Num"],
								"NumPP"=>$r["NumPP"]
							);
							$i++;
						};
				break;
			};
			echo json_encode($a);
		break;
		case "PrintDialogPrint":
			include("../mpdf53/mpdf.php");
			$mpdf = new mPDF('utf-8', 'A5', '8', '', 10, 10, 7, 7, 10, 10); /*задаем формат, отступы и.т.д.*/
			$mpdf->charset_in = 'UTF8';
			$mpdf->list_indent_first_level = 0;
			$a=array(); $c=0;
			if(isset($_POST["OrderCh"]))
				for($i=0; $i<count($_POST["OrderCh"]); $i++)
				{
					$d=$m->query("SELECT o.Zakaz, o.Shet, (SELECT SUM(od2.Count) FROM oreders o2, orderdoors od2 WHERE o2.id=o.id AND o2.id=od2.idOrder) AS DoorAllCount, n.id as NaryadID, n.NumInOrder, n.Num, n.NumPP, o1.name, o1.H, o1.W, o1.S, o1.SEqual, o1.Open, o1.Nalichnik, o1.Dovod, o1.RAL, o1.Zamok, o1.Shtild, o1.Note as DoorNote, o1.CostSvarka, n.FrameCompliteFlag, o1.CostFrame, o1.CostSborka,  o1.CostColor, o1.CostUpak, o1.CostShpt, n.Note as NaryadNote, n.Master FROM naryad n, oreders o, orderdoors o1 WHERE o.id=".$_POST["OrderCh"][$i]." AND n.idDoors=o1.id AND o1.idOrder=o.id AND o.status<>-1 AND n.SgibkaCompliteFlag=1 ".($XMLParams->Enterprise->SkipPurposeWelder=="1"?"AND n.SvarkaCompliteFlag=0":"AND n.SvarkaCompliteFlag=2"));
					if($d)
						while($r=$d->fetch_assoc()){
							$a[$c]=array(
								"Zakaz"=>$r["Zakaz"],
								"Shet"=>$r["Shet"],
								"DoorAllCount"=>$r["DoorAllCount"],
								"NaryadID"=>$r["NaryadID"],
								"NumInOrder"=>$r["NumInOrder"],
								"Num"=>$r["Num"],
								"NumPP"=>$r["NumPP"],
								"name"=>$r["name"],
								"H"=>$r["H"],
								"W"=>$r["W"],
								"S"=>($r["S"]!=null||$r["SEqual"]==1 ? ($r["S"]!=null ? $r["S"] : "Равн.") : ""),
								"Open"=>$r["Open"],
								"Nalichnik"=>$r["Nalichnik"],
								"Dovod"=>$r["Dovod"],
								"RAL"=>$r["RAL"],
								"DoorNote"=>$r["DoorNote"],
								"Shtild"=>$r["Shtild"],
								"SvarkaSum"=>$r["CostSvarka"],
								"FrameCompliteFlag"=>$r["FrameCompliteFlag"],
								"FrameSum"=>$r["CostFrame"],
								"SborkaSum"=>$r["CostSborka"],
								"ColorSum"=>$r["CostColor"],
								"UpakSum"=>$r["CostUpak"],
								"ShptSum"=>$r["CostShpt"],
								"NaryadNote"=>$r["NaryadNote"],
								"Master"=>$r["Master"]
							);
							$c++;
						};
				};

			if(isset($_POST["DoorCh"]))
				for($i=0; $i<count($_POST["DoorCh"]); $i++)
				{
					$d=$m->query("SELECT o.Zakaz, o.Shet, (SELECT SUM(od2.Count) FROM oreders o2, orderdoors od2 WHERE o2.id=o.id AND o2.id=od2.idOrder) AS DoorAllCount, n.id as NaryadID, n.NumInOrder, n.Num, n.NumPP, o1.name, o1.H, o1.W, o1.S, o1.SEqual, o1.Open, o1.Nalichnik, o1.Dovod, o1.RAL, o1.Zamok, o1.Shtild, o1.Note as DoorNote, o1.CostSvarka, n.FrameCompliteFlag, o1.CostFrame, o1.CostSborka,  o1.CostColor, o1.CostUpak, o1.CostShpt, n.Note as NaryadNote, n.Master FROM naryad n, oreders o, orderdoors o1 WHERE o1.id=".$_POST["DoorCh"][$i]." AND n.idDoors=o1.id AND o1.idOrder=o.id AND o.status<>-1 AND n.SgibkaCompliteFlag=1 ".($XMLParams->Enterprise->SkipPurposeWelder=="1"?"AND n.SvarkaCompliteFlag=0":"AND n.SvarkaCompliteFlag=2"));
					if($d)
						while($r=$d->fetch_assoc()){
							$a[$c]=array(
								"Zakaz"=>$r["Zakaz"],
								"Shet"=>$r["Shet"],
								"DoorAllCount"=>$r["DoorAllCount"],
								"NaryadID"=>$r["NaryadID"],
								"NumInOrder"=>$r["NumInOrder"],
								"Num"=>$r["Num"],
								"NumPP"=>$r["NumPP"],
								"name"=>$r["name"],
								"H"=>$r["H"],
								"W"=>$r["W"],
								"S"=>($r["S"]!=null||$r["SEqual"]==1 ? ($r["S"]!=null ? $r["S"] : "Равн.") : ""),
								"Open"=>$r["Open"],
								"Nalichnik"=>$r["Nalichnik"],
								"Dovod"=>$r["Dovod"],
								"RAL"=>$r["RAL"],
								"DoorNote"=>$r["DoorNote"],
								"Shtild"=>$r["Shtild"],
								"SvarkaSum"=>$r["CostSvarka"],
								"FrameCompliteFlag"=>$r["FrameCompliteFlag"],
								"FrameSum"=>$r["CostFrame"],
								"SborkaSum"=>$r["CostSborka"],
								"ColorSum"=>$r["CostColor"],
								"UpakSum"=>$r["CostUpak"],
								"ShptSum"=>$r["CostShpt"],
								"NaryadNote"=>$r["NaryadNote"],
								"Master"=>$r["Master"]
							);
							$c++;
						};
				};

			if(isset($_POST["NaryadCh"]))
				for($i=0; $i<count($_POST["NaryadCh"]); $i++)
				{
					$d=$m->query("SELECT o.Zakaz, o.Shet, (SELECT SUM(od2.Count) FROM oreders o2, orderdoors od2 WHERE o2.id=o.id AND o2.id=od2.idOrder) AS DoorAllCount, n.id as NaryadID, n.NumInOrder, n.Num, n.NumPP, o1.name, o1.H, o1.W, o1.S, o1.SEqual, o1.Open, o1.Nalichnik, o1.Dovod, o1.RAL, o1.Zamok, o1.Shtild, o1.Note as DoorNote, o1.CostSvarka, n.FrameCompliteFlag, o1.CostFrame, o1.CostSborka,  o1.CostColor, o1.CostUpak, o1.CostShpt, n.Note as NaryadNote, n.Master FROM naryad n, oreders o, orderdoors o1 WHERE n.id=".$_POST["NaryadCh"][$i]." AND n.idDoors=o1.id AND o1.idOrder=o.id AND o.status<>-1 AND n.SgibkaCompliteFlag=1 ".($XMLParams->Enterprise->SkipPurposeWelder=="1"?"AND n.SvarkaCompliteFlag=0":"AND n.SvarkaCompliteFlag=2"));
					if($d)
						while($r=$d->fetch_assoc()){
							$a[$c]=array(
								"Zakaz"=>$r["Zakaz"],
								"Shet"=>$r["Shet"],
								"DoorAllCount"=>$r["DoorAllCount"],
								"NaryadID"=>$r["NaryadID"],
								"NumInOrder"=>$r["NumInOrder"],
								"Num"=>$r["Num"],
								"NumPP"=>$r["NumPP"],
								"name"=>$r["name"],
								"H"=>$r["H"],
								"W"=>$r["W"],
								"S"=>($r["S"]!=null||$r["SEqual"]==1 ? ($r["S"]!=null ? $r["S"] : "Равн.") : ""),
								"Open"=>$r["Open"],
								"Nalichnik"=>$r["Nalichnik"],
								"Dovod"=>$r["Dovod"],
								"RAL"=>$r["RAL"],
								"DoorNote"=>$r["DoorNote"],
								"Shtild"=>$r["Shtild"],
								"SvarkaSum"=>$r["CostSvarka"],
								"FrameCompliteFlag"=>$r["FrameCompliteFlag"],
								"FrameSum"=>$r["CostFrame"],
								"SborkaSum"=>$r["CostSborka"],
								"ColorSum"=>$r["CostColor"],
								"UpakSum"=>$r["CostUpak"],
								"ShptSum"=>$r["CostShpt"],
								"NaryadNote"=>$r["NaryadNote"],
								"Master"=>$r["Master"]
							);
							$c++;
						};
				};
			//Случай когда печатается распределнный наряд
			if(isset($_POST["NaryadOne"]))
				{
					$d=$m->query("SELECT o.Zakaz, o.Shet, (SELECT SUM(od2.Count) FROM oreders o2, orderdoors od2 WHERE o2.id=o.id AND o2.id=od2.idOrder) AS DoorAllCount, n.id as NaryadID, n.NumInOrder, n.Num, n.NumPP, o1.name, o1.H, o1.W, o1.S, o1.SEqual, o1.Open, o1.Nalichnik, o1.Dovod, o1.RAL, o1.Zamok, o1.Shtild, o1.Note as DoorNote, o1.CostSvarka, n.FrameCompliteFlag, o1.CostFrame, o1.CostSborka,  o1.CostColor, o1.CostUpak, o1.CostShpt, n.Note as NaryadNote, n.Master FROM naryad n, oreders o, orderdoors o1 WHERE n.id=".$_POST["NaryadOne"]." AND n.idDoors=o1.id AND o1.idOrder=o.id ");
					if($d)
						while($r=$d->fetch_assoc()){
							$a[$c]=array(
								"Zakaz"=>$r["Zakaz"],
								"Shet"=>$r["Shet"],
								"DoorAllCount"=>$r["DoorAllCount"],
								"NaryadID"=>$r["NaryadID"],
								"NumInOrder"=>$r["NumInOrder"],
								"Num"=>$r["Num"],
								"NumPP"=>$r["NumPP"],
								"name"=>$r["name"],
								"H"=>$r["H"],
								"W"=>$r["W"],
								"S"=>($r["S"]!=null||$r["SEqual"]==1 ? ($r["S"]!=null ? $r["S"] : "Равн.") : ""),
								"Open"=>$r["Open"],
								"Nalichnik"=>$r["Nalichnik"],
								"Dovod"=>$r["Dovod"],
								"RAL"=>$r["RAL"],
								"DoorNote"=>$r["DoorNote"],
								"Shtild"=>$r["Shtild"],
								"SvarkaSum"=>$r["CostSvarka"],
								"FrameCompliteFlag"=>$r["FrameCompliteFlag"],
								"FrameSum"=>$r["CostFrame"],
								"SborkaSum"=>$r["CostSborka"],
								"ColorSum"=>$r["CostColor"],
								"UpakSum"=>$r["CostUpak"],
								"ShptSum"=>$r["CostShpt"],
								"NaryadNote"=>$r["NaryadNote"],
								"Master"=>$r["Master"]
							);
							$c++;
						};
				};
			//'SELECT n.id, n.Num, n.NumPP, o.Shet, n.NumInOrder, o1.name , o1.H , o1.W, o1.S, o1.SEqual, o.status, n.Note, n.SvarkaCompliteFlag, n.FrameCompliteFlag, n.SborkaCompliteFlag, n.ColorCompliteFlag, n.UpakCompliteFlag, n.ShptCompliteFlag FROM naryad as n, oreders as o, orderdoors as o1 WHERE n.idDoors=o1.id AND o1.idOrder=o.id AND (n.SgibkaCompliteFlag=1 OR n.SvarkaCompliteFlag=1 OR n.SvarkaCompliteFlag=2 OR n.SborkaCompliteFlag=1 OR n.ColorCompliteFlag=1 OR n.UpakCompliteFlag=1) '.$_POST["WHERE"]
			if(isset($_POST["PrintList"]))

				{
					$d=$m->query("SELECT o.Zakaz, o.Shet, (SELECT SUM(od2.Count) FROM oreders o2, orderdoors od2 WHERE o2.id=o.id AND o2.id=od2.idOrder) AS DoorAllCount, n.id as NaryadID, n.NumInOrder, n.Num, n.NumPP, o1.name, o1.H, o1.W, o1.S, o1.SEqual, o1.Open, o1.Nalichnik, o1.Dovod, o1.RAL, o1.Zamok, o1.Shtild, o1.Note as DoorNote, o1.CostSvarka, n.FrameCompliteFlag, o1.CostFrame, o1.CostSborka,  o1.CostColor, o1.CostUpak, o1.CostShpt, n.Note as NaryadNote, n.Master FROM naryad n, oreders o, orderdoors o1 WHERE n.idDoors=o1.id AND o1.idOrder=o.id AND (n.SgibkaCompliteFlag=1 OR n.SvarkaCompliteFlag=1 OR n.SvarkaCompliteFlag=2 OR n.SborkaCompliteFlag=1 OR n.ColorCompliteFlag=1 OR n.UpakCompliteFlag=1) ".$_POST["WHERE"]);
					if($d)
						while($r=$d->fetch_assoc()){
							$a[$c]=array(
								"Zakaz"=>$r["Zakaz"],
								"Shet"=>$r["Shet"],
								"DoorAllCount"=>$r["DoorAllCount"],
								"NaryadID"=>$r["NaryadID"],
								"NumInOrder"=>$r["NumInOrder"],
								"Num"=>$r["Num"],
								"NumPP"=>$r["NumPP"],
								"name"=>$r["name"],
								"H"=>$r["H"],
								"W"=>$r["W"],
								"S"=>($r["S"]!=null||$r["SEqual"]==1 ? ($r["S"]!=null ? $r["S"] : "Равн.") : ""),
								"Open"=>$r["Open"],
								"Nalichnik"=>$r["Nalichnik"],
								"Dovod"=>$r["Dovod"],
								"RAL"=>$r["RAL"],
								"DoorNote"=>$r["DoorNote"],
								"Shtild"=>$r["Shtild"],
								"SvarkaSum"=>$r["CostSvarka"],
								"FrameCompliteFlag"=>$r["FrameCompliteFlag"],
								"FrameSum"=>$r["CostFrame"],
								"SborkaSum"=>$r["CostSborka"],
								"ColorSum"=>$r["CostColor"],
								"UpakSum"=>$r["CostUpak"],
								"ShptSum"=>$r["CostShpt"],
								"NaryadNote"=>$r["NaryadNote"],
								"Master"=>$r["Master"]
							);
							$c++;
						};
				};

			
			if(file_exists("naryad.pdf") ) unlink("naryad.pdf");

			for($i=0;$i<count($a); $i++)
			{

				$imgOut="pdfOut/naryadImg".$a[$i]["NaryadID"].".jpg";
				if(file_exists($imgOut) ) unlink($imgOut);
				copy ( "http://".$XMLParams->Global->HostName."/enterprise/naryadimg.php?idNaryad=".$a[$i]["NaryadID"], $imgOut );

				$mpdf->AddPage();

				$html="";
				$f = fopen("printNaryadForm.html", "r");
				$a_in=array(
					"#imgOut","#Commercie","#NaryadID", "#NumInOrder", "#Num", "#Name","#Size","#Open","#Nalichnik","#Dovod","#RAL","#Zamok", "#Shtild","#DoorNote","#Svarka", "#Frame","#Sborka","#Color","#Upak","#Shpt","#NaryadNote", "#Master", "#DoorAllCount"
				);
				$Commercie=$XMLParams->Enterprise->ViewCommercieWhenPrint=="true"?"<b>Счет № : </b>".$a[$i]["Shet"]."<br><b>Заказчик : </b>".$a[$i]["Zakaz"] : "";
				$Svarka="__________________________  Стоимость: ".$a[$i]["SvarkaSum"];
				$Frame="";
				if($a[$i]["FrameCompliteFlag"]!=null)
					$Frame="&nbsp;&nbsp; Рамка : &nbsp;<span>__________________________  Стоимость: ".$a[$i]["FrameSum"]."</span><br><br>";
				$Sborka="__________________________  Стоимость: ".$a[$i]["SborkaSum"];
				$Color="__________________________  Стоимость: ".$a[$i]["ColorSum"];
				$Upak="__________________________  Стоимость: ".$a[$i]["UpakSum"];
				$Shpt="__________________________  Стоимость: ".$a[$i]["ShptSum"];
				//------Обработка штильды----------------
				$Shtild="";
				if($a[$i]["Shtild"]!="" & is_int((int)$a[$i]["Shtild"]))
				{
					$Shtild=(int)$a[$i]["Shtild"]+(int)$a[$i]["NumPP"]-1;
					//Если число 0007, тогда необходимо комписировать 0 в начале
					$s_new=strval($Shtild);
					for($j=0;$j<strlen($a[$i]["Shtild"]);$j++)
						if(strlen($a[$i]["Shtild"])>strlen($s_new))
							$s_new="0".$s_new;
					$Shtild=$s_new;
				};
				if((int)$Shtild==(int)$a[$i]["NumPP"]) $Shtild=$a[$i]["Shtild"]; //Проверка если номер штилбды не число тогад выводим его знач
				//-----------------------------------------------------

				$a_replace=array(
					$imgOut, $Commercie,$a[$i]["NaryadID"], $a[$i]["NumInOrder"], $a[$i]["Num"].$a[$i]["NumPP"] , $a[$i]["name"] , $a[$i]["H"]." x ".$a[$i]["W"]." x ".$a[$i]["S"] , $a[$i]["Open"] , $a[$i]["Nalichnik"],$a[$i]["Dovod"],"RAL ".$a[$i]["RAL"],$a[$i]["Zamok"],$Shtild,$a[$i]["DoorNote"] ,$Svarka,$Frame,$Sborka,$Color,$Upak, $Shpt, $a[$i]["NaryadNote"], $a[$i]["Master"], $a[$i]["DoorAllCount"]
				);
				while(!feof($f))
					$html=$html.str_replace($a_in,$a_replace,fgets($f));
				fclose($f);


				$mpdf->WriteHTML($html, 2); /*формируем pdf*/

			};

			$mpdf->Output('naryad.pdf' , 'F');
			echo "ok";
		break;
		case "SelectWorkers":
			$Step="1=1";
			switch($_POST["Step"])
			{
				case "Laser": $Step="DolgnostID=2 OR DolgnostID=14"; break;
				case "Sgibka": $Step="DolgnostID=2 OR DolgnostID=10"; break;
				case "Svarka": $Step="DolgnostID=2 OR DolgnostID=3"; break;
				case "Frame": $Step="DolgnostID=2 OR DolgnostID=3 OR DolgnostID=15"; break;
				case "Mdf": $Step="DolgnostID=2 OR DolgnostID=21"; break;
				case "Sborka": $Step="DolgnostID=2 OR DolgnostID=4"; break;
				case "SborkaMdf": $Step="DolgnostID=2 OR DolgnostID=22"; break;
				case "Color": $Step="DolgnostID=2 OR DolgnostID=11 OR DolgnostID=6"; break;
				case "Upak": $Step="DolgnostID=2 OR DolgnostID=7 OR DolgnostID=12"; break;
				case "Shpt": $Step="DolgnostID=2 OR DolgnostID=7 OR DolgnostID=12"; break;
			};
			$d=$m->query("SELECT id, Num, FIO FROM workers WHERE (".$Step.") AND fired<>1 ORDER BY FIO");
			$a=array(); $i=0;
			while($r=$d->fetch_assoc())
			{
				$a[$i]=array(
					"id"=>$r["id"],
					"Num"=>$r["Num"],
					"FIO"=>$r["FIO"]
					);
				$i++;
			};
			echo json_encode($a);
		break;
		//Разблокировать / Заблокировать терминалы
		case "TerminalsAlterStatus":
			$NewStatus="";
			switch ($XMLParams->Enterprise->TerminalsStatus) {
				case 'Enable':
					$XMLParams->Enterprise->TerminalsStatus="Disable";
					$NewStatus="Disable";
					break;
				case 'Disable':
					$XMLParams->Enterprise->TerminalsStatus="Enable";
					$NewStatus="Enable";
					break;
			};
			$XMLParams->asXML("../params.xml");
			echo $NewStatus;
		break;
	};
?>
