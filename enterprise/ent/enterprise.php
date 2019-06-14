<?php
	$m=new mysqli('localhost','root','Rfhkcjy:bd2010','ent');
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
		$r=$m->query("SELECT o1.Blank, o.name, o.H, o.W, n.id, n.idDoors, n.LaserWork, n.LaserDate FROM naryad n, orderdoors o, oreders o1 WHERE o1.id=o.idOrder AND o.id=n.idDoors AND DATE_FORMAT( n.LaserDate,'%d.%m.%Y')=DATE_FORMAT( NOW(),'%d.%m.%Y')");
			while($d=$r->fetch_assoc())
				echo '<tr>'.
					'<td>'.$d['Blank'].'</td>'.
					'<td>'.$d['name'].'</td>'.
					'<td>'.$d['H'].'</td>'.
					'<td>'.$d['W'].'</td>'.
					'<td>'.$d['LaserWork'].'</td>'.
					'<td>'.$d['LaserDate'].'</td>'.
					'</tr>';
		break;
		
		//--------------------Сгибка--------------------
		case 'SgibkaSelect':
		$r=$m->query("SELECT o1.Blank, o.name, o.H, o.W, n.id, n.idDoors, n.SgibkaWork, n.SgibkaDate FROM naryad n, orderdoors o, oreders o1 WHERE o1.id=o.idOrder AND o.id=n.idDoors AND DATE_FORMAT( n.SgibkaDate,'%d.%m.%Y')=DATE_FORMAT( NOW(),'%d.%m.%Y') ORDER BY o1.Blank, o.NumPP, n.NumPP");
			while($d=$r->fetch_assoc())
				echo '<tr>'.
					'<td>'.$d['Blank'].'</td>'.
					'<td>'.$d['name'].'</td>'.
					'<td>'.$d['H'].'</td>'.
					'<td>'.$d['W'].'</td>'.
					'<td>'.$d['SgibkaWork'].'</td>'.
					'<td>'.$d['SgibkaDate'].'</td>'.
					'</tr>';
		break;
		
		//--------------Наряды------------------------
		case 'NaryadTempListSelect':
			$r=$m->query("SELECT n.id , n.Num, n.NumPP , o1.name , o1.H, o1.W,o1.S , DATE_FORMAT(n.LaserDate,'%d.%m.%Y') AS LaserDate , n.LaserWork , DATE_FORMAT(n.SgibkaDate,'%d.%m.%Y') AS SgibkaDate , n.SgibkaWork , AlertStatus FROM oreders o, orderdoors o1, naryad n WHERE n.idDoors=o1.id AND o1.idOrder=o.id AND n.SgibkaWork IS NOT NULL AND n.SvarkaCompliteWork IS NULL  ORDER BY o.Blank, o1.NumPP, n.NumPP");
			$a=Array();
			$i=0;
			while($d=$r->fetch_assoc())
			{	
				$a[$i]=Array(
					'id'=>$d['id'],
					'Blank'=>$d['Num'].$d['NumPP'],
					'name'=>$d['name'],
					'H'=>$d['H'],
					'W'=>$d['W'],
					'S'=>$d['S'],
					'LaserDate'=>$d['LaserDate'],
					'LaserWork'=>$d['LaserWork'],
					'SgibkaDate'=>$d['SgibkaDate'],
					'SgibkaWork'=>$d['SgibkaWork'],
					'AlertStatus'=>$d['AlertStatus']
				);
				$i++;
			};
			echo json_encode($a);
		break;
		
		case 'NarydEditStart':
			$r=$m->query("SELECT n.Num, n.NumPP, o1.name, o1.H, o1.W, o1.S, o1.Nalichnik, o1.RAL, o1.Zamok, n.*, DATE_FORMAT(n.LaserDate,'%d.%m.%Y %H:%i:%S') AS LaserDt, DATE_FORMAT(n.SgibkaDate,'%d.%m.%Y %H:%i:%S') AS SgibkaDt, DATE_FORMAT(n.SvarkaEdit,'%d.%m.%Y %H:%i:%S') AS SvarkaDtEdit, DATE_FORMAT(n.SvarkaComplite,'%d.%m.%Y %H:%i:%S') AS SvarkaDt, DATE_FORMAT(n.SborkaComplite,'%d.%m.%Y %H:%i:%S') AS SborkaDt, DATE_FORMAT(n.ColorComplite,'%d.%m.%Y %H:%i:%S') AS ColorDt, DATE_FORMAT(n.UpakComplite,'%d.%m.%Y %H:%i:%S') AS UpakDt, DATE_FORMAT(n.ShptComplite,'%d.%m.%Y %H:%i:%S') AS ShptDt FROM naryad n, oreders o, orderdoors o1 WHERE n.idDoors=o1.id AND o1.idOrder=o.id AND n.id=".$_POST['id']);
			$d=$r->fetch_assoc();
			//-----Скрипт определяет идентификационный номер сотрудника
			$aPunkt=array("SgibkaWork", "SvarkaCompliteWork", "SborkaCompliteWork", "ColorCompliteWork", "UpakCompliteWork", "ShptCompliteWork");
			$aWorkNumResult=array("SgibkaWork"=>"", "SvarkaCompliteWork"=>"", "SborkaCompliteWork"=>"", "ColorCompliteWork"=>"", "UpakCompliteWork"=>"", "ShptCompliteWork"=>"");
			for($i=0; $i<count($aPunkt);$i++)
			{
				$r=$m->query("SELECT Num FROM workers WHERE FIO='".$d[$aPunkt[$i]]."' ");
				if($r->num_rows>0)
				{
					$d1=$r->fetch_assoc();
					$aWorkNumResult[$aPunkt[$i]]=$d1["Num"];
				};
				$r->close();
			};
			//---------------------------------------------------------------------------------------------
			echo json_encode(Array(
				'Blank'=>$d['Num'].$d["NumPP"],
				'name'=>$d['name'],
				'H'=>$d['H'],
				'W'=>$d['W'],
				'S'=>$d['S'],
				'Nalichnik'=>$d['Nalichnik'],
				'RAL'=>$d['RAL'],
				'Zamok'=>$d['Zamok'],
				'LaserWork'=>$d['LaserWork'],
				'LaserDate'=>$d['LaserDt'],
				'LaserSum'=>$d['LaserSum'],
				'SgibkaWorkNum'=>$aWorkNumResult["SgibkaWork"],
				'SgibkaWork'=>$d['SgibkaWork'],
				'SgibkaDate'=>$d['SgibkaDt'],
				'SgibkaSum'=>$d['SgibkaSum'],
				'SvarkaDateEdit'=>$d['SvarkaDtEdit'],
				'SvarkaDate'=>$d['SvarkaDt'],
				'SvarkaCompliteWorkNum'=>$aWorkNumResult["SvarkaCompliteWork"],
				'SvarkaWork'=>$d['SvarkaCompliteWork'],
				'SvarkaSum'=>$d['SvarkaSum'],
				'SborkaDate'=>$d['SborkaDt'],
				'SborkaCompliteWorkNum'=>$aWorkNumResult["SborkaCompliteWork"],
				'SborkaWork'=>$d['SborkaCompliteWork'],
				'SborkaSum'=>$d['SborkaSum'],
				'ColorDate'=>$d['ColorDt'],
				'ColorCompliteWorkNum'=>$aWorkNumResult["ColorCompliteWork"],
				'ColorWork'=>$d['ColorCompliteWork'],
				'ColorSum'=>$d['ColorSum'],
				'UpakDate'=>$d['UpakDt'],
				'UpakCompliteWorkNum'=>$aWorkNumResult["UpakCompliteWork"],
				'UpakWork'=>$d['UpakCompliteWork'],
				'UpakSum'=>$d['UpakSum'],
				'ShptDate'=>$d["ShptDt"],
				'ShptCompliteWorkNum'=>$aWorkNumResult["ShptCompliteWork"],
				"ShptWork"=>$d["ShptCompliteWork"],
				"ShptSum"=>$d["ShptSum"],
				'Note'=>$d['Note']
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
				case 'Svarka':$sSelectWork='SvarkaWork'; $sSelectDate='SvarkaDate'; $sIdDolgnost='DolgnostID=3 OR DolgnostID=8'; break;
				case 'Color':$sSelectWork='ColorCompliteWork'; $sSelectDate='ColorComplite'; $sIdDolgnost='DolgnostID=11'; break;
				case 'Sborka':$sSelectWork='SborkaCompliteWork'; $sSelectDate='SborkaComplite'; $sIdDolgnost='DolgnostID=4 OR DolgnostID=9'; break;
				case 'Upak':$sSelectWork='UpakCompliteWork'; $sSelectDate='UpakComplite'; $sIdDolgnost='DolgnostID=12'; break;
				case 'Shpt':$sSelectWork='ShptCompliteWork'; $sSelectDate='ShptComplite'; $sIdDolgnost='DolgnostID=7 OR DolgnostID=12 OR DolgnostID=6 OR DolgnostID=11'; break;
			};
			$d=$m->query('SELECT Num, FIO FROM Workers WHERE '.$sIdDolgnost);
			$a=array();
			$i=0;
			while($r=$d->fetch_assoc())
			{
				$SQLstr="";
				switch($_POST["typeSelect"])
				{
					case "Svarka":$SQLstr='SELECT count(*) as c FROM naryad WHERE (SvarkaCompliteWork="'.$r['FIO'].'" AND DATE_FORMAT(SvarkaComplite,"%d.%m.%Y") =DATE_FORMAT(Now(),"%d.%m.%Y") ) OR (SvarkaCompliteWork="'.$r['FIO'].'" AND SvarkaComplite IS NULL)'; break;
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
			$SvarkaCompliteWork="NULL";
			$SvarkaWorkEdit="NULL";
			if($_POST["SvarkaWork"]!="")
			{
				$SvarkaWorkEdit="STR_TO_DATE('".$_POST["SvarkaEdit"]."','%d.%m.%Y %H:%i:%S')";
				$SvarkaCompliteWork="'".$_POST["SvarkaWork"]."'";
			};
			$SvarkaComplite="null";
			if($_POST["SvarkaComplite"]!="")
				$SvarkaComplite="STR_TO_DATE('".$_POST["SvarkaComplite"]."','%d.%m.%Y %H:%i:%S')";
			$SborkaComplite="NULL";
			$SborkaCompliteWork="NULL";
			if($_POST["SborkaComplite"]!="")
			{
				$SborkaComplite="STR_TO_DATE('".$_POST["SborkaComplite"]."','%d.%m.%Y %H:%i:%S')";
				$SborkaCompliteWork="'".$_POST["SborkaCompliteWork"]."'";
			};
			$ColorComplite="NULL";
			$ColorCompliteWork="NULL";
			if($_POST["ColorComplite"]!="")
			{
				$ColorComplite="STR_TO_DATE('".$_POST["ColorComplite"]."','%d.%m.%Y %H:%i:%S')";
				$ColorCompliteWork="'".$_POST["ColorCompliteWork"]."'";
			};
			$UpakComplite="NULL";
			$UpakCompliteWork="NULL";
			if($_POST["UpakComplite"]!="")
			{
				$UpakComplite="STR_TO_DATE('".$_POST["UpakComplite"]."','%d.%m.%Y %H:%i:%S')";
				$UpakCompliteWork="'".$_POST["UpakCompliteWork"]."'";
			};
			$ShptComplite="NULL";
			$ShptCompliteWork="NULL";
			if($_POST["ShptComplite"]!="")
			{
				$ShptComplite="STR_TO_DATE('".$_POST["ShptComplite"]."','%d.%m.%Y %H:%i:%S')";
				$ShptCompliteWork="'".$_POST["ShptCompliteWork"]."'";
			};
			
			//Суммы зарплат
			$LaserSum="0"; if($_POST["LaserSum"]!="") $LaserSum=str_replace(',' , '.' , $_POST["LaserSum"]);
			$SgibkaSum="0"; if($_POST["SgibkaSum"]!="") $SgibkaSum=str_replace(',' , '.' , $_POST["SgibkaSum"]);
			$SvarkaSum="0"; if($_POST["SvarkaSum"]!="") $SvarkaSum=str_replace(',' , '.' , $_POST["SvarkaSum"]);
			$SborkaSum="0"; if($_POST["SborkaSum"]!="") $SborkaSum=str_replace(',' , '.' , $_POST["SborkaSum"]);
			$ColorSum="0"; if($_POST["ColorSum"]!="") $ColorSum=str_replace(',' , '.' , $_POST["ColorSum"]);
			$UpakSum="0"; if($_POST["UpakSum"]!="") $UpakSum=str_replace(',' , '.' , $_POST["UpakSum"]);
			$ShptSum="0"; if($_POST["ShptSum"]!="") $ShptSum=str_replace(',' , '.' , $_POST["ShptSum"]);
			
			$m->query("START TRANSACTION") or die ($er=$er."Ошибка начала транзакции");
			
			$m->query('UPDATE Naryad SET SvarkaCompliteWork='.$SvarkaCompliteWork.', SvarkaEdit='.$SvarkaWorkEdit.', Note="'.$_POST['Note'].'", SvarkaComplite='.$SvarkaComplite.', SborkaComplite='.$SborkaComplite.', SborkaCompliteWork='.$SborkaCompliteWork.', ColorComplite='.$ColorComplite.', ColorCompliteWork='.$ColorCompliteWork.', UpakComplite='.$UpakComplite.', UpakCompliteWork='.$UpakCompliteWork.', ShptComplite='.$ShptComplite.', ShptCompliteWork='.$ShptCompliteWork.', LaserSum='.$LaserSum.', SgibkaSum='.$SgibkaSum.', SvarkaSum='.$SvarkaSum.', SborkaSum='.$SborkaSum.', ColorSum='.$ColorSum.', UpakSum='.$UpakSum.', ShptSum='.$ShptSum.' WHERE id='.$_POST['id']) or die ($er=$er."Ошибка:".mysqli_error());
			
			//Делаем проверку, все наряды упакованны. Если все, тогда устанавливаем статус заказа =3
			if($_POST["UpakComplite"]!="")
			{
				//Определим id заказа
				$d=$m->query("SELECT o.id FROM oreders o, orderdoors o1, naryad n WHERE o.id=o1.idOrder AND o1.id=n.idDoors AND n.id=".$_POST["id"])  or die ($er=$er."Ошибка:".mysqli_error());;
				$idOrder=$d->fetch_row()[0];
				$d->close();
				//Определим количество возможных нарядов в пределах заказа
				$d=$m->query("SELECT SUM(o1.Count) FROM oreders o, orderdoors o1 WHERE o.id=o1.idOrder AND o.id=".$idOrder)  or die ($er=$er."Ошибка:".mysqli_error());;
				$NaryadMaxCount=$d->fetch_row()[0];
				$d->close();
				//Определяем кол-во упакованных нарядов
				$d=$m->query("SELECT COUNT(*) FROM oreders o, orderdoors o1, naryad n WHERE o.id=o1.idOrder AND o1.id=n.idDoors AND n.UpakCompliteWork IS NOT NULL AND o.id=".$idOrder)  or die ($er=$er."Ошибка:".mysqli_error());
				$NaryadUpakCount=$d->fetch_row()[0];
				$d->close();
				if((int) $NaryadMaxCount==(int)$NaryadUpakCount)
					$m->query("UPDATE oreders SET status=2 WHERE id=".$idOrder)  or die ($er=$er."Ошибка:".mysqli_error());
				
			};
			if($er=="ok")
			{
				$m->query("COMMIT")  or die ($er=$er."Ошибка:".mysqli_error());
			}
			else
				$m->query("ROLLBACK")  or die ($er=$er."Ошибка:".mysqli_error());
			echo $er;
		break;
		
		//--------Список наряда-------------------------------
		case 'NaryadListSelect':
			$d=$m->query('SELECT n.Num, n.NumPP , o1.name , o1.H , o1.W, n.*, DATE_FORMAT(n.SvarkaComplite,"%d.%m.%Y") as SvarkaDt, DATE_FORMAT(n.SborkaComplite,"%d.%m.%Y") as SborkaDt, DATE_FORMAT(n.ColorComplite,"%d.%m.%Y") as ColorDt, DATE_FORMAT(n.UpakComplite,"%d.%m.%Y") as UpakDt, DATE_FORMAT(n.ShptComplite,"%d.%m.%Y") as ShptDt FROM naryad as n, oreders as o, orderdoors as o1 WHERE n.idDoors=o1.id AND o1.idOrder=o.id AND n.SvarkaCompliteWork IS NOT null AND n.UpakComplite IS NULL');
			$a=array();
			$i=0;			
			while($r=$d->fetch_assoc())
			{
				$s='Сварка';
				$sNote=$r['SvarkaCompliteWork'];
				$sColor='blue';
				if($r['SvarkaDt']!=null)
				{
					$sNote=$r['SvarkaCompliteWork'].'  '.$r['SvarkaDt'];
					$sColor='green';
				};
				if($r['SborkaDt']!=null)
				{
					$s="Сборка";
					$sNote=$r['SborkaCompliteWork'].'  '.$r['SborkaDt'];
					$sColor='green';
				};
				if($r['ColorDt']!=null)
				{
					$s="Покраска";
					$sNote=$r['ColorCompliteWork'].'  '.$r['ColorDt'];
					$sColor='green';
				};
				if($r['UpakDt']!=null)
				{
					$s="Сборка";
					$sNote=$r['UpakCompliteWork'].'  '.$r['UpakDt'];
					$sColor='green';
				};
				if($r['ShptDt']!=null)
				{
					$s="Погрузка";
					$sNote=$r['ShptCompliteWork'].'  '.$r['ShptDt'];
					$sColor='green';
				};
				$NoteNaryad='';
				if($r['Note']!=null)
					$NoteNaryad=$r['Note'];
				if(strlen($NoteNaryad)>10) $NoteNaryad=substr($NoteNaryad,0,10).' ...';
				$a[$i]=array(
					'id'=>$r['id'],
					'Blank'=>$r['Num'].$r["NumPP"],
					'Name'=>$r['name'],
					'W'=>$r['W'],
					'H'=>$r['H'],
					'Work'=>$s,
					'Note'=>$sNote,
					'Color'=>$sColor,
					'NoteNaryad'=>$NoteNaryad,
					'NoteNaryadTitle'=>$r['Note']
				);
				$i++;
			};
			echo json_encode($a);
		break;
		case "PrintNaryad":
			$d=$m->query("SELECT  n.Num, n.NumPP, o1.name, o1.H, o1.W, o1.S, o1.Nalichnik, o1.RAL, o1.Zamok, o1.Note as DoorNote, n.SvarkaCompliteWork, n.SvarkaSum, n.SborkaCompliteWork, n.SborkaSum, n.ColorCompliteWork, n.ColorSum, n.UpakCompliteWork, n.UpakSum, n.Note as NaryadNote FROM naryad n, oreders o, orderdoors o1 WHERE n.idDoors=o1.id AND o1.idOrder=o.id AND n.id=".$_POST["id"]);
			if(file_exists("naryad.pdf") ) unlink("naryad.pdf");
			if($d->num_rows>0)
			{
				$r=$d->fetch_assoc();
				$html="";
				$f = fopen("printNaryadForm.html", "r");
				$a_in=array(
					"#Num","#Name","#Size","#Nalichnik","#RAL","#Zamok","#DoorNote","#Svarka","#Sborka","#Color","#Upak","#NaryadNote"
				);
				$Svarka="__________________________"; if($r["SvarkaCompliteWork"]!="") $Svarka=$r["SvarkaCompliteWork"]." (".$r["SvarkaSum"].")";
				$Sborka="__________________________"; if($r["SborkaCompliteWork"]!="") $Sborka=$r["SborkaCompliteWork"]." (".$r["SborkaSum"].")";
				$Color="__________________________"; if($r["ColorCompliteWork"]!="") $Color=$r["ColorCompliteWork"]." (".$r["ColorSum"].")";
				$Upak="__________________________"; if($r["UpakCompliteWork"]!="") $Upak=$r["UpakCompliteWork"]." (".$r["UpakSum"].")";
				
				$a_replace=array(
					$r["Num"].$r["NumPP"] , $r["name"] , $r["H"]."x".$r["W"]."x".$r["S"] , $r["Nalichnik"],$r["RAL"],$r["Zamok"],$r["DoorNote"] ,$Svarka,$Sborka,$Color,$Upak, $r["NaryadNote"]
				);
				while(!feof($f)) 
					$html=$html.str_replace($a_in,$a_replace,fgets($f));
				fclose($f);
				
				include("../mpdf53/mpdf.php");		
				$mpdf = new mPDF('utf-8', 'A5', '8', '', 10, 10, 7, 7, 10, 10); /*задаем формат, отступы и.т.д.*/
				$mpdf->charset_in = 'UTF8';
				$mpdf->list_indent_first_level = 0; 
				$mpdf->WriteHTML($html, 2); /*формируем pdf*/
				$mpdf->AddPage();
				$mpdf->Output('naryad.pdf' , 'F');
				echo "ok";
			};
		break;
	};
?>