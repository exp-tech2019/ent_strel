<?php
	$XMLParams=simplexml_load_file("../params.xml");
	ini_set("max_execution_time", "2000");
	$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
	switch ($_POST['Method'])
	{
		case 'Select':
			echo '<tr class=head>'.
				'<th>Номер '.($XMLParams->Global->ViewNumOrder=="Blank" ? "заказа" : "счета").'</th>'.
				'<th>Дата</th>'.
				'<th>Кол-во дверей</th>'.
				'<th>Выполненно</th>'.
				'</tr>';
			
			$r=$m->query('SELECT o.id, '.($XMLParams->Global->ViewNumOrder=="Blank"? "o.Blank" : "o.Shet as Blank").', DATE_FORMAT(BlankDate, "%d.%m.%Y") AS BlankDate, COUNT(o1.id) as Count, SUM(o1.Count) as Sum FROM oreders o, orderdoors o1 WHERE o.id=o1.idOrder AND o.status<>-1 AND o.status<>2 AND o.status<>3 GROUP BY o.id ');
			while($d=$r->fetch_assoc())
			{
				$r1=$m->query('SELECT COUNT( n.id) AS Count FROM orderdoors d, naryad n WHERE n.idDoors=d.id AND n.LaserCompliteFlag=1 AND d.idOrder='.$d['id']);
				$r1->data_seek(0);
				$countComplite=$r1->fetch_row()[0];
				if($d['Sum']>$countComplite)
				echo '<tr id=OrderTRHeader'.$d['id'].' class=row onClick="SelectDoors('.$d['id'].')">'.
					'<td>'.$d['Blank'].'</td>'.
					'<td style="padding:2px;">'.$d['BlankDate'].'</td>'.
					'<td style="text-align:center">'.$d['Sum'].'</td>'.
					'<td style="text-align:center" id=OrderDoorComplite'.$d['id'].'>'.$countComplite.'</td>'.
					'</tr>'.
					'<tr><td colspan=4 style="display:none" id=OrderTR'.$d['id'].'></td></tr>';
			};
		break;
		
		case 'SelectDoors':
			echo '<table id=DoorsTable'.$_POST['id'].'>'.
				'<tr class=head1>'.
				'<th>№</th>'.
				'<th>Наименование</td>'.
				'<th>Высота</th>'.
				'<th>Ширина</th>'.
				'<th>Количество</th>'.
				'<th>Выполненно</th>'.
				'<th></th>'.
				'</tr>';
			$r=$m->query('SELECT o.NumPP, o.id, o.name, o.H, o.W, o.Count FROM orderdoors o WHERE o.idOrder='.$_POST['id'].'  AND (o.CostSvarka<>0 AND o.CostSborka<>0 AND o.CostColor<>0 AND o.CostUpak<>0 '.($XMLParams->Orders->ShptAllowZero=="1"?"":"AND o.CostShpt<>0").')  ORDER BY o.id');
			while($d=$r->fetch_assoc())
			{
				$r1=$m->query("SELECT COUNT(n.id) FROM orderdoors o, naryad n WHERE o.id=n.idDoors AND o.id=".$d['id']." AND n.LaserCompliteFlag=1 ");
				$r1->data_seek(0);
				$countComplite=$r1->fetch_row()[0];
				if($d['Count']>$countComplite)
				echo '<tr id=DoorTr'.$d['id'].' class=row1>'.
					'<td>'.$d["NumPP"].'</td>'.
					'<td>'.$d['name'].'</td>'.
					'<td>'.$d['H'].'</td>'.
					'<td>'.$d['W'].'</td>'.
					'<td Type=All id=DoorTDCount'.$d['id'].'>'.$d['Count'].'</td>'.
					'<td Type=Complite id=DoorTDComplite'.$d['id'].'>'.$countComplite.'</td>'.
					'<td><input id=InpDoor'.$d['id'].' maxlength=100 value=1> <button onclick="AddNarayd('.$d['id'].')">></button><button onclick="AddNaryadAll('.$d['id'].',this)">>></button></td>'.
					'</tr>';
			};
			echo '</table>';
			
		break;
		
		case 'AddNaryad':
			session_start();
			if(isset($_SESSION["FIOLaser"]))
			if($_SESSION["FIOLaser"]!="")
			{
				$r=$m->query("SELECT id FROM workers WHERE FIO='".$_SESSION["FIOLaser"]."'");
				$r->data_seek(0);
				$idWorker=$r->fetch_row()[0];
				$r->close();
				
				$countInp=$_POST['Count'];//Кол-во выполненых нарядов

				//Определим начальный номер двери в заказе
				$idDoors=$_POST["id"];
				$FirstNumInOrder=0;
				$d=$m->query("SELECT od.id, od.Count FROM oreders o, orderdoors od WHERE o.id=od.idOrder AND o.id IN (SELECT od1.idOrder FROM orderdoors od1 WHERE od1.id=$idDoors) ORDER BY od.NumPP");
				while($r=$d->fetch_assoc())
					if($r["id"]!=$idDoors)
					{
						$FirstNumInOrder+=(int)$r["Count"];
					}
					else
						break;
				//Значение 0 может быть для первой позиции, в этом случае мы присваеваем = 1
				$FirstNumInOrder+=1;
				$d->close();
			
				$d=$m->query("SELECT n.id, n.NumInOrder FROM naryad n WHERE n.idDoors=".$_POST['id']);
				$NaryadList=array(); $i=0;
				while($r=$d->fetch_assoc())
				{
					$NaryadList[$i]=$r["NumInOrder"];
					$i++;
				};
				$d->close();
			
				$r=$m->query('Select count FROM orderdoors WHERE id='.$_POST['id']);
				$r->data_seek(0);
				$count=$r->fetch_row()[0];
				$r->close();
			
				if($countInp<=$count-count($NaryadList))
				{
					//Определяем номер заявки
					$d=$m->query("SELECT ".($XMLParams->Global->ViewNumOrder=="Blank"? "o1.Blank" : "o1.Shet as Blank").", o.NumPP, o.CostLaser, o.CostSgibka, o.CostSvarka, o.CostFrame, o.CostSborka, o.CostColor, o.CostUpak, o.CostShpt, o.WorkWindowCh, o.WorkWindowNoFrame, o.S, o.StvorkaWindowCh, o.StvorkaWindowNoFrame, o.FramugaCh, o.FramugaWindowCh, o.FramugaWindowNoFrame FROM orderdoors o, oreders o1 WHERE o.idOrder=o1.id AND o.id=".$_POST["id"]);
					$r=$d->fetch_assoc();
					$Blank=$r["Blank"];
					$NumPP=$r["NumPP"];
					$CostLaser=$r["CostLaser"];
					$CostSgibka=$r["CostSgibka"];
					$CostSvarka=$r["CostSvarka"];
					$CostFrame=$r["CostFrame"];
					$CostSborka=$r["CostSborka"];
					$CostColor=$r["CostColor"];
					$CostUpak=$r["CostUpak"];
					$CostShpt=$r["CostShpt"];
					$Frame=false; 
					if(((bool)$r["WorkWindowCh"] & !(bool)$r["WorkWindowNoFrame"]) || ($r["S"]!=null & (bool)$r["StvorkaWindowCh"] & !(bool)$r["StvorkaWindowNoFrame"]) || ((bool)$r["FramugaCh"] & (bool)$r["FramugaWindowCh"] & !(bool)$r["FramugaWindowNoFrame"])) $Frame=true;
					$d->close();

					$m->query("START TRANSACTION") or die ($er="Ошибка начала транзакции");
					$er="ok";
					for($i=1;$i<=$countInp;$i++)
					{
						if($er=="ok")
							try
							{
								//Опеределяем номер наряда
								$NumNaryad=1;
								$NaryadOldArr=array(); $c=0;
								$d=$m->query("SELECT n.NumPP FROM naryad n, orderdoors o WHERE n.idDoors=o.id AND o.id=".$_POST["id"]);
								while($r=$d->fetch_assoc())
									$NaryadOldArr[$c++]=(int)$r["NumPP"];
								$d->close();
								for($ii=1;$ii<500;$ii++)
									if(!in_array($ii,$NaryadOldArr))
									{
										$NumNaryad=$ii;
										break;
									};
								//Определим номер новой двери
								$NumInOrder=0;
								for($ii=$FirstNumInOrder; $ii<=$FirstNumInOrder+$count-1; $ii++)
									if(!in_array($ii,$NaryadList))
									{
										$NumInOrder=$ii;
										break;
									};
								if($NumInOrder==0) $er="Ошибка формирования номера двери";

								$m->query("INSERT INTO naryad (idDoors, NumInOrder, Num, NumPP, LaserCompliteFlag, SgibkaCompliteFlag, SvarkaCompliteFlag, FrameCompliteFlag, SborkaCompliteFlag, ColorCompliteFlag, UpakCompliteFlag, ShptCompliteFlag) values(".$_POST['id'].", $NumInOrder, '".$Blank."/".$NumPP."/', ".$NumNaryad.", 1, 0, 0, ".($Frame?"0":"NULL").", 0, 0, 0, 0)") or die($er=$er."\nINSERT Naryad: ".mysqli_error($m));
								//Добваим позиции для выполнения
								$idNaryad=$m->insert_id;
								$m->query("INSERT INTO NaryadComplite (idNaryad, Step, idWorker, Cost, DateComplite) VALUES (".$idNaryad.", 1, ".$idWorker.", ".$CostLaser.", NOW())") or die($er=$er."\nINSERT NaryadComplite Laser: ".mysqli_error($m));
								$m->query("INSERT INTO NaryadComplite (idNaryad, Step, idWorker, Cost, DateComplite) VALUES (".$idNaryad.", 2, NULL, ".$CostSgibka.", NULL)") or die($er=$er."\nINSERT NaryadComplite Sgibka: ".mysqli_error($m));
								$m->query("INSERT INTO NaryadComplite (idNaryad, Step, idWorker, Cost, DateComplite) VALUES (".$idNaryad.", 3, NULL, ".$CostSvarka.", NULL)") or die($er=$er."\nINSERT NaryadComplite Svarka: ".mysqli_error($m));
								if($Frame)
									$m->query("INSERT INTO NaryadComplite (idNaryad, Step, idWorker, Cost, DateComplite) VALUES (".$idNaryad.", 4, NULL, ".$CostFrame.", NULL)") or die($er=$er."\nINSERT NaryadComplite Frame: ".mysqli_error($m));
								$m->query("INSERT INTO NaryadComplite (idNaryad, Step, idWorker, Cost, DateComplite) VALUES (".$idNaryad.", 5, NULL, ".$CostSborka.", NULL)") or die($er=$er."\nINSERT NaryadComplite Sborka: ".mysqli_error($m));
								$m->query("INSERT INTO NaryadComplite (idNaryad, Step, idWorker, Cost, DateComplite) VALUES (".$idNaryad.", 6, NULL, ".$CostColor.", NULL)") or die($er=$er."\nINSERT NaryadComplite Color: ".mysqli_error($m));
								$m->query("INSERT INTO NaryadComplite (idNaryad, Step, idWorker, Cost, DateComplite) VALUES (".$idNaryad.", 7, NULL, ".$CostUpak.", NULL)") or die($er=$er."\nINSERT NaryadComplite Upak: ".mysqli_error($m));
								$m->query("INSERT INTO NaryadComplite (idNaryad, Step, idWorker, Cost, DateComplite) VALUES (".$idNaryad.", 8, NULL, ".$CostShpt.", NULL)") or die($er=$er."\nINSERT NaryadComplite Shpt: ".mysqli_error($m));
								//Изменим статус заказа В РАБОТЕ
								$m->query("UPDATE oreders SET status=1 WHERE id=(SELECT idOrder FROM orderdoors WHERE id=".$_POST['id']."  LIMIT 1)") or die($er=$er."\nINSERT NaryadComplite Oreders: ".mysqli_error($m));
							}
							catch (mysqli_sql_exception $e) {$er=$er.$e->errorMessage();};
					};
					if($er=="ok")
					{
						$m->commit(); echo "ok";
					}
					else
					{
						echo $er; $m->rollback();
					};
				}
				else
					echo 'Введенное количество превышает доступное';
			}
			else echo "Сессия не инициирована";
		break;
		
		case 'NaryadSelect':
			session_start();
			$r=$m->query("SELECT id FROM workers WHERE FIO='".$_SESSION["FIOLaser"]."'");
			$r->data_seek(0);
			$idWorker=$r->fetch_row()[0];
			$r->close();
			//Отображаем записи если: (наряд выполнен сегодня или статус alert=1)
			//а также изделие не согнуто - для этоого состояния кнопка удалить активна
			$r=$m->query("SELECT n.Num, n.NumPP, o.name, o.H, o.W, n.id, n.idDoors, n.LaserWork, n.LaserDate, n.AlertStatus, n.SgibkaDate FROM NaryadComplite nc, naryad n, orderdoors o, oreders o1 WHERE o1.id=o.idOrder AND o.id=n.idDoors AND n.id=nc.idNaryad AND (DATE_FORMAT( nc.DateComplite,'%d.%m.%Y')=DATE_FORMAT( NOW(),'%d.%m.%Y') OR n.AlertStatus=1)  AND nc.idWorker='".$idWorker."' ORDER BY n.Num ,n.NumPP");
			while($d=$r->fetch_assoc())
			{
				$bg='white';
				if($d['AlertStatus']==1)
					$bg='red';
				
				$ButtonStr=""; if($d["SgibkaDate"]==null) $ButtonStr='<button onclick="NaryadDelete('.$d['id'].')">х</button>';
				echo '<tr bgcolor='.$bg.' id=NaryadTableTR'.$d['id'].'>'.
					'<td>'.$d['Num'].$d["NumPP"].'</td>'.
					'<td>'.$d['name'].'</td>'.
					'<td>'.$d['H'].'</td>'.
					'<td>'.$d['W'].'</td>'.
					'<td>'.$d['LaserWork'].'</td>'.
					'<td>'.$d['LaserDate'].'</td>'.
					'<td>'.$ButtonStr.'</td>'.
					'</tr>';
			};
		break;
		
		case 'NaryadDelete':
			//Определяем id заказа
			$d=$m->query("SELECT COUNT(*) as c, o.id FROM naryad n, oreders o, orderdoors o1 WHERE o.id=o1.idOrder AND o1.id=n.idDoors AND o.id=(SELECT o1.id FROM naryad n, orderdoors o, oreders o1 WHERE o1.id=o.idOrder AND o.id=n.idDoors AND n.id=".$_POST['id']." LIMIT 1)");
			$r=$d->fetch_assoc();
			$CountNaryad=(int) $r["c"];
			$d->close();

			$er="ok";
			$m->query("START TRANSACTION") or die ($er="Ошибка начала транзакции");
			if($er=="ok")
				try
				{
					if($CountNaryad==1)
						$m->query("UPDATE oreders SET status=0 WHERE id=".$r["id"]) or die($er=$er."\UPDATE orders status: ".mysqli_error($m));
					$m->query('DELETE FROM naryad WHERE id='.$_POST['id']) or die($er=$er."\DELETE Naryad: ".mysqli_error($m));
					$m->query('DELETE FROM NaryadComplite WHERE idNaryad='.$_POST['id']) or die($er=$er."\DELETE NaryadComplite: ".mysqli_error($m));
				}
				catch (mysqli_sql_exception $e) {$er=$er.$e->errorMessage();};
				if($er=="ok")
				{
					$m->commit(); echo "ok";
				}
				else
				{
					echo $er; $m->rollback();
				};
		break;
	};
	$m->close();
?>