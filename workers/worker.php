<?php
	$XMLParams=simplexml_load_file("../params.xml");
	$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
	switch ($_POST['Method'])
	{
		case 'SelectDolgnost':
			$r=$m->query('SELECT Dolgnost FROM ManualDolgnost');
			while($res=$r->fetch_assoc())
				echo '<option value="'.$res['Dolgnost'].'">'.$res['Dolgnost'].'</option>';
		break;
		
		case 'AddEdit':
			switch($_POST['WorkDialogAddEditStatus'])
			{
				case 'Add':
					$m->query("INSERT INTO workers (Num, FIO, DolgnostID, Placement, Phone, Phone1, Adress, Note, SmartCartNum) VALUES ('".$_POST['Num']."', '".$_POST['FIO']."', (SELECT id FROM manualdolgnost WHERE Dolgnost='".$_POST['Dolgnost']."' LIMIT 1), STR_TO_DATE('".$_POST['Placement']."','%d.%m.%Y') , '".$_POST['Phone']."' , '".$_POST['Phone1']."' , '".$_POST['Adress']."' , '".$_POST['Note']."', ".($_POST["SmartCartNum"]==""?"NULL" : "'".$_POST["SmartCartNum"]."'").")");
				break;
				case 'Edit':
					$AuthPass="NULL"; if($_POST["AuthPass"]!="") $AuthPass=" '".$_POST["AuthPass"]."' ";
					$m->query('UPDATE workers SET '.
						"Num='".$_POST['Num']."' , ".
						"FIO='".$_POST['FIO']."' , ".
						"DolgnostID=(SELECT id FROM ManualDolgnost WHERE Dolgnost='".$_POST['Dolgnost']."' LIMIT 1) , ".
						"Placement=STR_TO_DATE('".$_POST['Placement']."','%d.%m.%Y'), ".
						"Phone='".$_POST['Phone']."' , ".
						"Phone1='".$_POST['Phone1']."' , ".
						"Adress='".$_POST['Adress']."' , ".
						"Note='".$_POST['Note']."', ".
						"AuthPass=".$AuthPass.", ".
						"SmartCartNum=".($_POST["SmartCartNum"]==""?"NULL" : "'".$_POST["SmartCartNum"]."'")." ".
						"WHERE id=".$_POST['id']
					);
				break;
			};
			
		break;
		
		case 'EditStart':
			$StatusOnline='Отсутствует';
			$res=$m->query('SELECT * FROM workersdidlayn WHERE timestop IS NULL AND idworker='.$_POST['id']);
			if($res->num_rows>0)
				$StatusOnline='На производстве';
			$res=$m->query("SELECT w.Num, w.FIO, m.Dolgnost, DATE_FORMAT(w.Placement, '%d.%m.%Y') as Placement, w.Phone, w.Phone1, w.Adress, w.Note, w.AuthPass, w.fired, w.SmartCartNum FROM workers as w, ManualDolgnost as m WHERE m.id=w.DolgnostID AND w.id=".$_POST['id'].' ');
			$r=$res->fetch_assoc();
			//Определение, отмечен сотрудник в других таблицах
			$WorkerCountSet=0;
			$res=$m->query("SELECT COUNT(*) FROM naryad WHERE LaserWork='".$r["FIO"]."' OR SgibkaWork='".$r["FIO"]."' OR SvarkaCompliteWork='".$r["FIO"]."' OR SborkaCompliteWork='".$r["FIO"]."' OR ColorCompliteWork='".$r["FIO"]."' OR UpakCompliteWork='".$r["FIO"]."' OR ShptCompliteWork='".$r["FIO"]."' ");
			$WorkerCountSet+=(int) $res->fetch_row()[0]; $res->close();
			$res=$m->query("SELECT COUNT(*) FROM paymentsworkers WHERE idWorker='".$_POST['id']."' ");
			$WorkerCountSet+=(int) $res->fetch_row()[0]; $res->close();
			$flagEditFIO=true; if($WorkerCountSet>0) $flagEditFIO=false;
			
			$data=Array(
				'Num'=>$r["Num"],
				"flagEditFIO"=>$flagEditFIO,
				'FIO'=>$r['FIO'],
				'Dolgnost'=>$r['Dolgnost'],
				'Placement'=>$r['Placement'],
				'Phone'=>$r['Phone'],
				'Phone1'=>$r['Phone1'],
				'Adress'=>$r['Adress'],
				'Note'=>$r['Note'],
				"AuthPass"=>$r["AuthPass"],
				"Fired"=>$r["fired"],
				"SmartCartNum"=>$r["SmartCartNum"],
				'StatusOnline'=>$StatusOnline
			);
			echo json_encode($data);
		break;
		
		case 'Select':
			$where=' 1=1 ';
			if(isset($_POST['Where']))
				$where=$where.$_POST['Where'];
			//$res=$m->query('SELECT w.*, m.Dolgnost FROM workers as w, manualdolgnost as m WHERE'.$where.' ORDER BY FIO');
			$res=$m->query(
				"SELECT t1.*,t2.OnEnterprise FROM (SELECT w.*, m.Dolgnost FROM workers as w, manualdolgnost as m WHERE w.DolgnostID=m.id AND ".$where.") t1
				LEFT JOIN
					(SELECT COUNT(*) AS OnEnterprise, w1.idworker FROM workersdidlayn w1 WHERE w1.timestop IS NULL GROUP BY w1.idworker) t2
				ON t1.id=t2.idworker
				ORDER BY t1.FIO"
			);
			$a=array(); $i=0;
			while($r=$res->fetch_assoc())
			{
				$Num=""; if($r['Num']!=null) $Num=$r['Num'];
				$a[$i]=array(
					"id"=>$r['id'],
					"Num"=>$Num,
					"FIO"=>$r['FIO'],
					"Dolgnost"=>$r['Dolgnost'],
					"SmartCartNum"=>$r['SmartCartNum'],
					"Fired"=>$r["fired"],
					"OnEnterprise"=>$r["OnEnterprise"]
				);
				$i++;
			};
			echo json_encode($a);
		break;
		
		//Изменение статуса сотрудника на производстве
		case 'UpdateDidlayn':
			switch ($_POST['status'])
			{
				case 'Отсутствует':
					$m->query('UPDATE workersdidlayn SET timestop=Now() WHERE idworker='.$_POST['id'].' AND timestop IS NULL');
					$m->query('INSERT INTO workersdidlayn (idworker , timestart) VALUES ('.$_POST['id'].' , NOW())');
					echo 'На производстве';
				break;
				case 'На производстве':
					$m->query('UPDATE workersdidlayn SET timestop=Now() WHERE idworker='.$_POST['id'].' AND timestop IS NULL');
					echo 'Отсутствует';
				break;
			};
		break;
		//-----------------------------------------------------------------
		
		//Отображение сотрудников онлайн
		case 'SelectOnline':
			$aDolgnost=array(
				array("dolgnost"=>"ст. Инженер","id"=>13),
				array("dolgnost"=>"Инженеры","id"=>2),
				array("dolgnost"=>"ст. Сгибщик","id"=>10),
				array("dolgnost"=>"Сгибщики","id"=>5),
				array("dolgnost"=>"ст. Сварщик","id"=>8),
				array("dolgnost"=>"Сварщики","id"=>3),
				array("dolgnost"=>"Рамочники","id"=>15),
				array("dolgnost"=>"ст. Сборщик","id"=>9),
				array("dolgnost"=>"Сборщики","id"=>4),
				array("dolgnost"=>"ст. Маляр","id"=>11),
				array("dolgnost"=>"Маляры","id"=>6),
				array("dolgnost"=>"ст. Упаковщик","id"=>12),
				array("dolgnost"=>"Упаковщики","id"=>7)
			);
			for($i=0; $i<count($aDolgnost);$i++)
			{
				$res=$m->query("SELECT w.FIO, DATE_FORMAT(d.timestart,'%d.%m.%Y') AS DateStart FROM workers w, workersdidlayn d WHERE w.id=d.idworker AND d.timestop IS NULL AND w.DolgnostID=".$aDolgnost[$i]["id"]);
				if($res->num_rows>0)
					echo '<p><h1 class="h1">'.$aDolgnost[$i]["dolgnost"].'</h1></p>';
				while($r=$res->fetch_assoc())
					echo '<p class="LeftMenu">'.$r['FIO'].'</p>';
			}
		break;
		//----------------------------------------------
		
		// Удаление
		case 'Remove':
			$m->query('DELETE FROM workersdidlayn WHERE idworker='.$_POST['id']);
			$m->query('DELETE FROM workers WHERE id='.$_POST['id']);
			echo 'ok';
		break;
		
		//Максимальный +1 идентификационный номер
		case "WorkerMaxNum":
			$d=$m->query("SELECT MAX(Num)+1 AS MaxNum FROM workers w");
			$r=$d->fetch_assoc();
			echo $r["MaxNum"];
		break;
		
		//Отображение списка сотрудников для увольнения
		case "FireNotFireSelect":
			$d=$m->query("SELECT w.id, w.Num, w.FIO, m.Dolgnost FROM workers as w, manualdolgnost as m WHERE w.Fired=0 AND w.DolgnostID=m.id ORDER BY FIO"); $i=0; $a=array();
			while($r=$d->fetch_assoc())
			{
				$a[$i]=array(
					"id"=>$r["id"],
					"Num"=>$r["Num"],
					"FIO"=>$r["FIO"],
					"Dolgnost"=>$r["Dolgnost"]
				);
				$i++;
			};
			echo json_encode($a);
		break;
		
		//Уволить/Восстановить сотрудника
		case"FireEdit":
			$er="ok";
			switch((int) $_POST["Fired"])
			{
				case 0:
					$m->query("UPDATE workers w SET w.fired=1, w.AuthPass='', w.SmartCartNum='' WHERE w.id=".$_POST["id"]) or die($er="Ошибка выполнения");
					$m->query("UPDATE workersdidlayn w SET w.timestop=NOW() WHERE w.timestop IS NULL AND w.idworker=".$_POST["id"]) or die($er="Ошибка выполнения");
				break;
				case 1:
					$m->query("UPDATE workers w SET w.fired=0 WHERE w.id=".$_POST["id"]) or die($er="Ошибка выполнения");
				break;
			};
			echo $er;
		break;
		case "RfidEditSave":
			$WhereWorkerID=$_POST["idWorker"]=="" ? "":" AND id<>".$_POST["idWorker"];
			$SmartCartNum=$_POST["SmartCartNum"];
			if($SmartCartNum!="")
			{
				$d=$m->query("SELECT FIO FROM workers WHERE SmartCartNum='$SmartCartNum' $WhereWorkerID");
				if($d->num_rows>0)
				{
					$r=$d->fetch_assoc();
					echo "Карта зарегестрирована на ".$r["FIO"];
				}
				else
					echo "ok";
			}
			else echo "ok";
			/*
			else
				echo "Пустое значение № карты";*/
		break;
	};
?>