<?php
	$XMLParams=simplexml_load_file("../params.xml");
	ini_set("max_execution_time", "2000");
	$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
	switch ($_POST['Method'])
	{
		case 'Select':
			
			$d=$m->query('SELECT o.id, '.($XMLParams->Global->ViewNumOrder=="Blank"? "o.Blank" : "o.Shet as Blank").', DATE_FORMAT(BlankDate, "%d.%m.%Y") AS BlankDate, COUNT(n.id) as CountComplite FROM oreders o, orderdoors o1, naryad n WHERE o.id=o1.idOrder AND n.idDoors=o1.id AND o.status<>-1 AND n.SgibkaCompliteFlag=0 GROUP BY o.id ');
			while($r=$d->fetch_assoc())
			{
				echo '<tr id=OrderTRHeader'.$r['id'].' class=row onClick="SelectDoors('.$r['id'].')">'.
					'<td>'.$r['Blank'].'</td>'.
					'<td style="padding:2px;">'.$r['BlankDate'].'</td>'.
					'<td style="text-align:right" id=OrderDoorComplite'.$r['id'].'>'.$r["CountComplite"].'</td>'.
					'</tr>'.
					'<tr><td colspan=3 style="display:none" id=OrderTR'.$r['id'].'></td></tr>';
			};
		break;
		
		case 'SelectDoors':
			echo '<table id=DoorsTable'.$_POST['id'].'>'.
				'<tr class=head1>'.
				'<th>№ наряда</th>'.
				'<th>Наименование</td>'.
				'<th>Высота</th>'.
				'<th>Ширина</th>'.
				'<th>Выполненно</th>'.
				'<th></th>'.
				'</tr>';
			$r=$m->query('SELECT n.id, n.Num, n.NumPP, o.name, o.H, o.W, n.AlertStatus FROM orderdoors o, naryad n WHERE (n.SgibkaCompliteFlag=0 OR (n.SgibkaCompliteFlag=1 AND n.AlertStatus=true))AND n.idDoors=o.id AND o.idOrder='.$_POST['id']." ORDER BY n.Num, n.NumPP");
			while($d=$r->fetch_assoc())
			{
				$bgColor='white';
				if($d['AlertStatus']==1)
					$bgColor='Red';
				echo '<tr bgColor='.$bgColor.' id=DoorTr'.$d['id'].' class=row1>'.
					'<td>'.$d["Num"].$d["NumPP"]."</td>".
					'<td>'.$d['name'].'</td>'.
					'<td>'.$d['H'].'</td>'.
					'<td>'.$d['W'].'</td>'.
					'<td><button onclick="AddNarayd('.$d['id'].')">Выполненно</button></td>'.
					'</tr>';
			};
			echo '</table>';
			
		break;
		
		case 'AddNaryad':
			session_start();
			if(isset($_SESSION["FIOSgibka"]))
			if(!empty($_POST['id']))
			{
				//Опеределим id сотрудника
				$r=$m->query("SELECT id FROM workers WHERE FIO='".$_SESSION["FIOSgibka"]."'");
				$r->data_seek(0);
				$idWorker=$r->fetch_row()[0];
				$r->close();

				$er="ok";
				$m->query("START TRANSACTION") or die ($er="Ошибка начала транзакции");
				if($er=="ok")
					try
					{
						$m->query("UPDATE naryad SET SgibkaCompliteFlag=1 WHERE id=".$_POST['id']) or die($er=$er."\nОбновление таблицы наряда: ".mysqli_error($m));
						$m->query("UPDATE NaryadComplite SET idWorker=".$idWorker.", DateComplite=NOW() WHERE idNaryad=".$_POST["id"]." AND Step=2") or die($er=$er."\nОбновление таблицы выполнения наряда: ".mysqli_error($m));
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
			};
		break;
		
		case 'NaryadSelect':
			session_start();
			$r=$m->query("SELECT id FROM workers WHERE FIO='".$_SESSION["FIOSgibka"]."'");
			$r->data_seek(0);
			$idWorker=$r->fetch_row()[0];
			$r->close();

			$r=$m->query("SELECT n.Num, n.NumPP, o.name, o.H, o.W, n.id, n.idDoors, n.SgibkaWork, n.SgibkaDate, n.SvarkaCompliteWork, n.AlertStatus FROM NaryadComplite nc, naryad n, orderdoors o, oreders o1 WHERE o1.id=o.idOrder AND o.id=n.idDoors AND n.id=nc.idNaryad AND ((TO_DAYS( NOW())-TO_DAYS( nc.DateComplite) BETWEEN 0 AND 1) OR n.AlertStatus=1) AND n.SgibkaCompliteFlag=1 AND nc.idWorker=".$idWorker." AND nc.Step=2 ORDER BY n.Num, n.NumPP,  AlertStatus DESC ");
			while($d=$r->fetch_assoc())
			{
				$bgColor='White';
				if($d['AlertStatus']==1)
					$bgColor='Red';
				$BtnStr=""; if($d["SvarkaCompliteWork"]==null) $BtnStr='<button onclick="NaryadDelete('.$d['id'].')">х</button>';
				
				echo '<tr bgColor='.$bgColor.' id=NaryadTableTR'.$d['id'].'>'.
					'<td>'.$d['Num'].$d["NumPP"].'</td>'.
					'<td>'.$d['name'].'</td>'.
					'<td>'.$d['H'].'</td>'.
					'<td>'.$d['W'].'</td>'.
					'<td>'.$d['SgibkaWork'].'</td>'.
					'<td>'.$d['SgibkaDate'].'</td>'.
					'<td>'.$BtnStr.'</td>'.
					'</tr>';
			};
		break;
		
		case 'NaryadDelete':
			$er="ok";
			$m->query("START TRANSACTION") or die ($er="Ошибка начала транзакции");
			if($er=="ok")
				try
				{
					$m->query('UPDATE naryad SET SgibkaCompliteFlag=0 , AlertStatus=1 WHERE id='.$_POST['id']) or die($er=$er."\nОбновление таблицы наряда: ".mysqli_error($m));
					$m->query("UPDATE NaryadComplite SET idWorker=NULL, DateComplite=NULL WHERE Step=2 AND idNaryad=".$_POST['id']) or die($er=$er."\nОбновление таблицы выполнения наряда: ".mysqli_error($m));
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
		
		case 'SelectWorkerOnline':
			$r=$m->query("SELECT w.FIO FROM workers w, workersdidlayn d WHERE w.id=d.idworker AND d.timestop IS NULL AND (w.DolgnostID=5 OR w.DolgnostID=10)");
			while($d=$r->fetch_assoc())
				echo '<option name="'.$d['FIO'].'">'.$d['FIO'].'</option>';
		break;
	};
	$m->close();
?>