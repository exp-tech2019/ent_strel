<?php
	session_start();
	ini_set("max_execution_time", "2000");
	$XMLParams=simplexml_load_file("../params.xml");
	$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
	switch ($_POST['Method'])
	{
		case "WorksSelectList":
			$a=array();
			$d=$m->query("CALL SelectPayments3 ('".$_POST["DateWith"]."' , '".$_POST["DateBy"]."')" );
			$i=0;
			if($d)
				while($r=$d->fetch_assoc())
				{
					$SumWith=$r["SumWith"]!=null?(float)$r["SumWith"] : 0;
					$SumPlus=$r["SumPlus"]!=null?(float)$r["SumPlus"] : 0;
					$SumMinus=$r["SumMinus"]!=null?(-1)*((float)$r["SumMinus"]) : 0;
					$a[$i]=array(
						"idWorker"=>$r["idWorker"],
						"FIO"=>$r["FIO"],
						"Dolgnost"=>$r["Dolgnost"],
						"SumWith"=>$SumWith,
						"SumPlus"=>$SumPlus+(float)$r["Cost"],
						"SumMinus"=>$SumMinus,
						"SumEnd"=>$SumWith+$SumPlus+(float)$r["Cost"]-$SumMinus
					);
					$i++;
				};
			echo json_encode($a);
		break;
		
		//Отображение списка сотрудников
		case "SelectWorkers":
			$d=$m->query("SELECT FIO FROM workers WHERE fired<>1 ORDER BY FIO");
			$a=array(); $i=0;
			while($r=$d->fetch_assoc())
			{
				$a[$i]=$r["FIO"]; $i++;
			};
			echo json_encode($a);
		break;
		
		case "SelectWorkerBalance":
			$d=$m->query("SELECT SUM(
    (SELECT SUM(nc.Cost) FROM naryadcomplite nc WHERE nc.idWorker=w.id AND nc.DateComplite IS NOT NULL) +
    (SELECT SUM(p.Sum) FROM paymentsworkers p WHERE p.idWorker=w.id)
  ) AS Balance
  FROM workers w WHERE w.FIO='".$_POST["FIO"]."'");
			if($d->num_rows>0)
			{
				$r=$d->fetch_assoc();
				$balance=$r["Balance"]!=null ? $r["Balance"] : 0;
				echo $balance;
			};
		break;
		//Вывод должности сотрудника
		case "SelectWorkerDolgnost":
			$d=$m->query("SELECT m.Dolgnost FROM workers w, manualdolgnost m WHERE w.FIO='".$_POST["FIO"]."' AND m.id=w.DolgnostID");
			if($d->num_rows>0)
			{
				$r=$d->fetch_assoc();
				echo $r["Dolgnost"];
			};
		break;
		
		case "PayamentsWorksSave":
			$er="";
			//Определим id сотрудника
			$d=$m->query("SELECT id FROM workers WHERE FIO='".$_POST["FIO"]."'") or die ($er=$er."Ошибка определения id сотрудника");
			if($d->num_rows>0)
			{
				$r=$d->fetch_assoc();
				$id=$r["id"];
				$m->query("INSERT INTO paymentsworkers (idWorker, DatePayment, Sum, Note, Accountant) VALUES(".$id.", STR_TO_DATE('".$_POST["DatePayment"]."', '%d.%m.%Y'), ".$_POST["Sum"].", '".$_POST["Note"]."', '".$_SESSION["AutorizeFIO"]."')") or die($er=$er."Ошибка добавления платежа");
			};
			echo $er;
		break;
		
		case "EditStart":
			$d=$m->query("SELECT w.FIO, d.Dolgnost, DATE_FORMAT(p.DatePayment, '%d.%m.%Y') as DatePayment, p.Sum, p.Note, p.Accountant FROM paymentsworkers p, workers w, manualdolgnost d WHERE p.idWorker=w.id AND w.DolgnostID=d.id AND p.id=".$_POST["id"]);
			$r=$d->fetch_assoc();
			$a=array(
				"id"=>$_POST["id"],
				"FIO"=>$r["FIO"],
				"Dolgnost"=>$r["Dolgnost"],
				"DatePayment"=>$r["DatePayment"],
				"Sum"=>$r["Sum"],
				"Note"=>$r["Note"],
				"Accountant"=>$r["Accountant"]
			);
			echo json_encode($a);
		break;
		case "EditSave":
			//Определяемся со знаком суммы +/-
			$Sum=(float)$_POST["Sum"];
			if($_POST["PlusMinus"]=="Выплаты") $Sum=(-1)*$Sum;
			$er="ok";
			$m->query("UPDATE paymentsworkers SET DatePayment=STR_TO_DATE('".$_POST["DatePayment"]."', '%d.%m.%Y') , Sum=".$Sum." , Note='".$_POST["Note"]."' WHERE id=".$_POST["id"]) or die($er="Произошла ошшибка сохранения записи");
			echo $er;
		break;
		
		case "Delete":
			$er="ok";
			$m->query("DELETE FROM paymentsworkers WHERE id=".$_POST["id"]) or die($er="Ошибка удаления");
			echo $er;
		break;
		
		//Печать расходно-кассового ордера
		case "PrintRKO":
			$html="";
			if(file_exists("rko.pdf") ) unlink("rko.pdf");
			$f = fopen("rko.htm", "r");
			$a_in=array( "#PaymentDate", "#PaymentSum", "#FIO", "#PaymentDolgnost", "#Note", "#PaymentStr");
			$a_replace=array($_POST["DatePayment"],$_POST["Sum"],$_POST["FIO"],$_POST["Dolgnost"],$_POST["Note"], num2str((int)$_POST["Sum"]));
			while(!feof($f)) 
				$html=$html.str_replace($a_in,$a_replace,fgets($f));
			fclose($f);
			include("../mpdf53/mpdf.php");		
			$mpdf = new mPDF('utf-8', 'A4', '8', '', 10, 10, 7, 7, 10, 10); 
			$mpdf->charset_in = 'UTF8';
			$mpdf->list_indent_first_level = 0; 
			$mpdf->WriteHTML($html, 2); 
			//$mpdf->AddPage();
			$mpdf->Output('rko.pdf' , 'F');
			echo "ok";
		break;
		
		case "PrintWorkersList":
			$i=0;
			$html="<table border=1 cellspacing=0 cellpadding=6><tr><td>ФИО</td><td>Должность</td><td>На начало периода</td><td>Заработанно (тек. мес.)</td><td>Выплачено (тек. мес.)</td><td>На конец периода</td></tr>";
			$FIO=$_POST["aFIO"];
			$Dolgnost=$_POST["aDolgnost"];
			$Column1=$_POST["aColumn1"];
			$Column2=$_POST["aColumn2"];
			$Column3=$_POST["aColumn3"];
			$Column4=$_POST["aColumn4"];
			//Вывод списка без итога
			for($i=0;$i<count($FIO);$i++)
			{
				$html=$html."<tr><td>".$FIO[$i]."</td><td>".$Dolgnost[$i]."</td><td>".$Column1[$i]."</td><td>".$Column2[$i]."</td><td>".$Column3[$i]."</td><td>".$Column4[$i]."</td></tr>";
			};
			//Вывод Итог
			$html=$html."<tr bgcolor=lightgray><b><td colspan=2>Итог</td><td>".$_POST["Itog1"]."</td><td>".$_POST["Itog2"]."</td><td>".$_POST["Itog3"]."</td><td>".$_POST["Itog4"]."</td></b></tr>";

			$html=$html."</table>";
			include("../mpdf53/mpdf.php");		
			$mpdf = new mPDF('utf-8', 'A4', '8', '', 10, 10, 7, 7, 10, 10); 
			$mpdf->charset_in = 'UTF8';
			$mpdf->list_indent_first_level = 0; 
			$mpdf->WriteHTML($html, 2); 
			//$mpdf->AddPage();
			$mpdf->Output('PaymentsWorkersList.pdf' , 'F');
			echo "ok";
		break;
		
		case "WorkerOneSelect":
			$d=$m->query("CALL SelectPaymentsWorkerOne2 (".$_POST["idWorker"].", '".$_POST["FIO"]."', STR_TO_DATE('".$_POST["DateWith"]."','%d.%m.%Y'), DATE_ADD(STR_TO_DATE('".$_POST["DateBy"]."','%d.%m.%Y'), INTERVAL 1 DAY)  ) ");
			$a=array();
			$i=0;
			$debetSum=0; $kredetSum=0;
			$SumWith=0; $Earned=0; $Paid=0; $EndSum=0;
			while($r=$d->fetch_assoc())
			{
				if($r["Note"]=="Остаток на начало периода") $SumWith=(float)$r["Sum"];//Остаток на начало месяца
				if($r["Note"]!="Остаток на начало периода" & ($r["idPayment"]==null || $r["Sum"]>0)) $Earned+=(float) $r["Sum"];//Заработано
				if($r["Note"]!="Остаток на начало периода" & ($r["idPayment"]!=null & (float)$r["Sum"]<=0)) $Paid+=(float) $r["Sum"];//Выплаченно
				$debet="";
				$kredet="";
				if($r["Sum"]>0)
				{
					$debet=$r["Sum"];
					$debetSum+=(float) $r["Sum"];
				}
				else 
				{
					$kredet=(-1)*(float)$r["Sum"];
					$kredetSum+=(float) $r["Sum"];
				};
				$a[$i]=array(
					"idPayment"=>$r["idPayment"],
					"DatePayment"=>$r["DatePayment"],
					"Note"=>$r["Note"]==null ? "":$r["Note"],
					"Debet"=>$debet,
					"kredet"=>$kredet,
					"Accountant"=>$r["Accountant"]
				);
				$i++;
			};
			$aReturn=array(
				"SumWith"=>$SumWith,
				"Earned"=>$Earned,
				"Paid"=>$Paid>0? $Paid : (-1)*$Paid,
				"SumEnd"=>$SumWith+$Earned+$Paid,
				"DebetSum"=>$debetSum,
				"KeredetSum"=>(-1)*$kredetSum,
				"Lines"=>$a
			);
			echo json_encode($aReturn);
		break;
		//Печать таблицы из диалога
		case "WorkerOnePrint":
			$html="<p>ФИО: ".$_POST["FIO"]."</p>";
			$html=$html."<p>Должность: ".$_POST["Dolgnost"]."</p>";
			$html=$html."<p>Период: ".$_POST["With"]." - ".$_POST["By"]."</p>";
			$i=0;
			if(isset($_POST["Date"]))
			{
			$aDate=$_POST["Date"];
			$aNote=$_POST["Note"];
			$aDebet=$_POST["Debet"];
			$aKredet=$_POST["Kredet"];
			$aAttantion=$_POST["Attantion"];
			$html=$html."<table cellspacing=0 cellpadding=2 rules='all' bordercolor='green'>";
			$html=$html."<tr><td style='border:solid 0.5px gray'>Дата</td><td style='border:solid 0.5px gray'>Описание</td><td style='border:solid 0.5px gray'>Начисления</td><td style='border:solid 0.5px gray'>Выплаты</td><td style='border:solid 0.5px gray'>Выплатил</td></tr>";
			while(isset($aDate[$i]))
			{
				$html=$html."<tr>".
					"<td style='border:solid 0.5px gray'>".$aDate[$i]."</td>".
					"<td style='border:solid 0.5px gray'>".$aNote[$i]."</td>".
					"<td style='border:solid 0.5px gray'>".$aDebet[$i]."</td>".
					"<td style='border:solid 0.5px gray'>".$aKredet[$i]."</td>".
					"<td style='border:solid 0.5px gray'>".(isset($aAttantion[$i]) & $aAttantion[$i]!=null & $aAttantion[$i]!="null" ? $aAttantion[$i] : "")."</td>".
				"</tr>";
				$i++;
			};
			};
			$html=$html."</table>";
			include("../mpdf53/mpdf.php");		
			$mpdf = new mPDF('utf-8', 'A4', '8', '', 10, 10, 7, 7, 10, 10); 
			$mpdf->charset_in = 'UTF8';
			$mpdf->list_indent_first_level = 0; 
			$mpdf->WriteHTML($html, 2); 
			//$mpdf->AddPage();
			$mpdf->Output('OnePrint.pdf' , 'F');
			echo "ok";
		break;
		//Обнуление платежей
		case "SumNull":
			//$_SESSION["AutorizeFIO"]
			$idHistory="";
			$m->autocommit(FALSE);
			$m->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
			$er="";
			try
			{
				$m->query("INSERT INTO paymentssumnullhistory (Date, Manager) values (Now(), '".$_SESSION["AutorizeFIO"]."')")
				or die($er=$er." ".mysqli_error($m));
				$idHistory=$m->insert_id;
				$aSum=array("LaserSum","SgibkaSum","SvarkaSum","FrameSum","SborkaSum","ColorSum","UpakSum","ShptSum");
				$aWorker=array("LaserWork","SgibkaWork","SvarkaCompliteWork","FrameCompliteWork","SborkaCompliteWork","ColorCompliteWork","UpakCompliteWork","ShptCompliteWork");
				$aCaption=array("Не распределен(лазер)","Не распределен(сгибка)","Не распределен(сварка)","Не распределен(рамщик)","Не распределен(маляр)","Не распределен(упаковка)","Не распределен(отгрузка)");
				$aStep=array("Лазер","Сгибка","Сварка","Рамка","Сборка","Покраска","Упаковка","Отгрузка");
				for($i=0;$i<7;$i++)
				{
					$m->query("INSERT INTO paymentssumnullhistorylist (idHistory, idNaryad, Step, Summ, Complite) SELECT ".$idHistory.", n.id, '".$aStep[$i]."', n.".$aSum[$i].", 0 FROM naryad n WHERE n.".$aSum[$i]."<>0 AND n.".$aWorker[$i]."='".$aCaption[$i]."'")
					or die($er=$er." ".mysqli_error($m));
					$m->query("UPDATE naryad n SET n.".$aSum[$i]."=0 WHERE n.".$aSum[$i]."<>0 AND n.".$aWorker[$i]."='".$aCaption[$i]."'")
					or die($er=$er." ".mysqli_error($m));
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
		case "SumNullHistory":
			$d=$m->query("
SELECT h.id, DATE_FORMAT(h.Date,'%d.%m.%Y') as Date, h.Manager,lt.C, lt.s FROM paymentssumnullhistory h
LEFT join
(SELECT l.idHistory, count(*) as C, SUM(l.Summ) as s FROM paymentssumnullhistorylist l GROUP BY l.idHistory) lt
ON h.id=lt.idHistory		
WHERE h.Date BETWEEN STR_TO_DATE('".$_POST["DateWith"]."', '%d.%m.%Y') AND STR_TO_DATE('".$_POST["DateBy"]."', '%d.%m.%Y')
ORDER BY h.Date	
			");
			$a=array(); $i=0;
			while($r=$d->fetch_assoc())
			{
				$a[$i]=array(
					"id"=>$r["id"],
					"Date"=>$r["Date"],
					"Manager"=>$r["Manager"],
					"Count"=>$r["C"],
					"Sum"=>$r["s"]
				);
				$i++;
			};
			echo json_encode($a);
		break;
		case "SumNullHistoryList":
			$d=$m->query("SELECT p.*, n.Num, n.NumPP FROM paymentssumnullhistorylist p, naryad n WHERE n.id=p.idNaryad AND p.idHistory=".$_POST["idHistory"]);
			$i=0; $a=array();
			while($r=$d->fetch_assoc())
			{
				$a[$i]=array(
					"id"=>$r["id"],
					"idNaryad"=>$r["idNaryad"],
					"NaryadNum"=>$r["Num"]."/".$r["NumPP"],
					"Step"=>$r["Step"],
					"Summ"=>$r["Summ"],
					"Complite"=>$r["Complite"]
				);
				$i++;
			};
			echo json_encode($a);
		break;
		case "SumNullEditWorker":
			$step="1";
			switch($_POST["Step"]) {
				case "Лазер": $step="2 , 14"; break;
				case "Сгибка": $step="2 , 5 , 10"; break;
				case "Сварка": $step="2 , 3"; break;
				case "Рамка": $step="2 , 3 , 15"; break;
				case "Сборка": $step="2 , 4"; break;
				case "Покраска": $step="2 , 6 , 11"; break;
				case "Упаковка": $step="2 , 7 , 12"; break;
				case "Отгрузка": $step="2 , 7 , 12"; break;
			};
			$d=$m->query("SELECT FIO FROM workers WHERE DolgnostID IN (".$step.") ORDER BY FIO");
			$i=0; $a=array();
			while($r=$d->fetch_assoc())
			{
				$a[$i]=$r["FIO"];
				$i++;
			};
			echo json_encode($a);
		break;
		case "SumNullEditSave":
			$er="";
			$Complite=""; $CompliteSum=""; $CompliteWorker="";
			switch($_POST["Step"]){
				case "Лазер": $Complite="LaserDate"; $CompliteSum="LaserSum"; $CompliteWorker="LaserWork"; break;
				case "Сгибка": $Complite="SgibkaDate"; $CompliteSum="SgibkaSum"; $CompliteWorker="SgibkaWork"; break;
				case "Сварка": $Complite="SvarkaComplite"; $CompliteSum="SvarkaSum"; $CompliteWorker="SvarkaCompliteWork"; break;
				case "Рамка": $Complite="FrameComplite"; $CompliteSum="FrameSum"; $CompliteWorker="FrameCompliteWork"; break;
				case "Сборка": $Complite="SborkaComplite"; $CompliteSum="SborkaSum"; $CompliteWorker="SborkaCompliteWork"; break;
				case "Покраска": $Complite="ColorComplite"; $CompliteSum="ColorSum"; $CompliteWorker="ColorCompliteWork"; break;
				case "Упаковка": $Complite="UpakComplite"; $CompliteSum="UpakSum"; $CompliteWorker="UpakCompliteWork"; break;
				case "Отгрузка": $Complite="ShptComplite"; $CompliteSum="ShptSum"; $CompliteWorker="ShptCompliteWork"; break;
			};
			
			$m->autocommit(FALSE);
			$m->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
			$er="";
			try
			{
				$m->query("UPDATE naryad SET ".$CompliteWorker."='".$_POST["Worker"]."', ".$Complite."=STR_TO_DATE('".$_POST["Date"]."','%d.%m.%Y') , ".$CompliteSum."=".$_POST["Summ"]." WHERE id=".$_POST["NaryadID"]);
				$m->query("UPDATE paymentssumnullhistorylist SET Complite=1 WHERE id=".$_POST["HistoryListID"]);
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
		//-----------------------Заказы---------------------
		case 'OrderSelect':
			$DateWith=$_POST["DateWith"];
			$DateBy=$_POST["DateBy"];
			$d=$m->query("CALL PaymentsOrderByDateOrder ('".$DateWith."', '".$DateBy."')");
			$a=array(); $i=0;
			while($r=$d->fetch_assoc())
			{
				$CostPlain=(float)$r["CostLaser"]+(float)$r["CostSgibka"]+(float)$r["CostSvarka"]+(float)$r["CostFrame"]+(float)$r["CostSborka"]+(float)$r["CostColor"]+(float)$r["CostUpak"]+(float)$r["CostShpt"]+(float)$r["CostMdf"]+(float)$r["CostSborkaMdf"];
				$a[$i]=array(
					"id"=>$r["id"],
					"Blank"=>$r["Blank"],
					"BlankDate"=>$r["BlankDate"],
					"Shet"=>$r["Shet"],
					"CostLaser"=>$r["CostLaser"],
					"CostSgibka"=>$r["CostSgibka"],
					"CostSvarka"=>$r["CostSvarka"],
					"CostFrame"=>$r["CostFrame"],
					"CostSborka"=>$r["CostSborka"],
					"CostColor"=>$r["CostColor"],
					"CostUpak"=>$r["CostUpak"],
					"CostShpt"=>$r["CostShpt"],
					"CostMdf"=>$r["CostMdf"],
					"CostSborkaMdf"=>$r["CostSborkaMdf"],
					"CostPlain"=>$CostPlain,
					"CostNaryadComplite"=>$r["CostNaryadComplite"]
					);
				$i++;
			};
			echo json_encode($a);
			break;
			case "OrderSelectMore":
				$idOrder=$_POST["idOrder"];
				$d=$m->query("SELECT nc.Step, SUM(COALESCE(nc.Cost,0)) AS Cost FROM orderdoors od, Naryad n, NaryadComplite nc WHERE od.idOrder=$idOrder AND od.id=n.idDoors AND n.id=nc.idNaryad AND nc.DateComplite IS NOT NULL GROUP BY nc.Step");
				$CostLaser=0;
				$CostSgibka=0;
				$CostLaser=0;
				$CostSvarka=0;
				$CostFrame=0;
				$CostSborka=0;
				$CostColor=0;
				$CostUpak=0;
				$CostShpt=0;
				$CostSborkaMdf=0;
				$CostMdf=0;
				while($r=$d->fetch_assoc())
					switch ($r["Step"]) {
						case 1: $CostLaser=(float)$r["Cost"]; break;
						case 2: $CostSgibka=(float)$r["Cost"]; break;
						case 3: $CostSvarka=(float)$r["Cost"]; break;
						case 4: $CostFrame=(float)$r["Cost"]; break;
						case 5: $CostSborka=(float)$r["Cost"]; break;
						case 6: $CostColor=(float)$r["Cost"]; break;
						case 7: $CostUpak=(float)$r["Cost"]; break;
						case 8: $CostShpt=(float)$r["Cost"]; break;
						case 9: $CostSborkaMdf=(float)$r["Cost"]; break;
						case 10: $CostMdf=(float)$r["Cost"]; break;
					};
				echo json_encode(array(
					"CostLaser"=>$CostLaser,
					"CostSgibka"=>$CostSgibka,
					"CostSvarka"=>$CostSvarka,
					"CostFrame"=>$CostFrame,
					"CostSborka"=>$CostSborka,
					"CostColor"=>$CostColor,
					"CostUpak"=>$CostUpak,
					"CostShpt"=>$CostShpt,
					"CostSborkaMdf"=>$CostSborkaMdf,
					"CostMdf"=>$CostMdf
					));
			break;
	};
	$m->close();
	
	/**
 * Возвращает сумму прописью
 * @author runcore
 * @uses morph(...)
 */
function num2str($num) {
    $nul='ноль';
    $ten=array(
        array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
        array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
    );
    $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
    $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
    $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
    $unit=array( // Units
        array('копейка' ,'копейки' ,'копеек',	 1),
        array('рубль'   ,'рубля'   ,'рублей'    ,0),
        array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
        array('миллион' ,'миллиона','миллионов' ,0),
        array('миллиард','милиарда','миллиардов',0),
    );
    //
    list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub)>0) {
        foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
            if (!intval($v)) continue;
            $uk = sizeof($unit)-$uk-1; // unit key
            $gender = $unit[$uk][3];
            list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
            // mega-logic
            $out[] = $hundred[$i1]; # 1xx-9xx
            if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
            else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
            // units without rub & kop
            if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
        } //foreach
    }
    else $out[] = $nul;
    $out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
    $out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
    return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}

/**
 * Склоняем словоформу
 * @ author runcore
 */
function morph($n, $f1, $f2, $f5) {
    $n = abs(intval($n)) % 100;
    if ($n>10 && $n<20) return $f5;
    $n = $n % 10;
    if ($n>1 && $n<5) return $f2;
    if ($n==1) return $f1;
    return $f5;
}
?>
