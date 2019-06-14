<?php
	ini_set("max_input_vars", "5000");
	session_start();
	$XMLParams=simplexml_load_file("../params.xml");
	$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
	
	switch($_POST['method'])
	{
		case "GetVersion":
			echo "6.1";
		break;
		
		case 'OrderAddMaxBlank':
			$d=$m->query("SELECT MAX(Blank)+1 as num FROM oreders WHERE YEAR(BlankDate)=YEAR(NOW())");
			$r=$d->fetch_assoc();
			echo $r["num"]!=null ? $r["num"] : 1;
		break;
		
		case "OrderSave":
			$er="";
			$idOrder=$_POST["idOrder"];
			$m->autocommit(FALSE);
			$m->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
			//Обрабатываем запрос к таблице Oreders
			$ShetDate="null";
			if($_POST['ShetDate']!="") $ShetDate="STR_TO_DATE('".$_POST['ShetDate']."', '%d.%m.%Y')";
			if($idOrder=="")
			{
				try
				{
					$m->query ("INSERT INTO oreders (Blank, BlankDate, Shet, ShetDate, Srok, Zakaz, Contact, Note, Manager)".
					" VALUES ('".$_POST['Blank']."' , STR_TO_DATE('".$_POST['BlankDate']."', '%d.%m.%Y') , '".$_POST['Shet']."' , ".$ShetDate." , '".$_POST['Srok']."' , '".$_POST['Zakaz']."' , '".$_POST['Contact']."' , '".$_POST['Note']."' , '".$_SESSION["AutorizeFIO"]."')")
					or die($er=$er.mysqli_error($m));
					$idOrder=$m->insert_id;
				}
				catch (mysqli_sql_exception $e) {$er=$er.$e->errorMessage();};
			}
			else
			{
				try
				{
					$m->query('UPDATE oreders SET  '.
					"Blank='".$_POST["Blank"]."' , ".
					"BlankDate=STR_TO_DATE('".$_POST['BlankDate']."', '%d.%m.%Y')  , ".
					"Shet='".$_POST["Shet"]."' , ".
					"ShetDate=STR_TO_DATE('".$_POST['ShetDate']."', '%d.%m.%Y')  , ".
					"Srok='".$_POST["Srok"]."' , ".
					"Zakaz='".$_POST["Zakaz"]."' , ".
					"Contact='".$_POST["Contact"]."' , ".
					"Note='".$_POST["Note"]."' , ".
					"status=".$_POST["status"].", ".
					"UserBlock=null ".
					'WHERE id='.$_POST['idOrder']) or die($er=$er.mysqli_error($m));
				}
				catch (mysqli_sql_exception $e){$er=$er.$e->errorMessage();};
			};
			
			//Работаем с таблицей
			$i=0;

			$CostDoors=array();
			while($i<count($_POST['OrderDialogTableTDNameArr']) & $er=="")
			{
				$DoorID=-1;
				if(isset($_POST["OrderDialogTableTDIDArr"][$i]))
					$DoorID=$_POST["OrderDialogTableTDIDArr"][$i];
				$H=$_POST['OrderDialogTableTDHArr'][$i]!="" ? $_POST['OrderDialogTableTDHArr'][$i] : "NULL";
				$W=$_POST['OrderDialogTableTDWArr'][$i]!="" ? $_POST['OrderDialogTableTDWArr'][$i] : "NULL";
				$S=($_POST['OrderDialogTableTDSArr'][$i]!="" & $_POST['OrderDialogTableTDSArr'][$i]!="Равн.")? $_POST['OrderDialogTableTDSArr'][$i]:"null";
				$SEqual=($_POST['OrderDialogTableTDSArr'][$i]=="Равн.")? "1":"0";
				switch($_POST['OrderDialogTableTDStatusArr'][$i])
				{
					case "Load"://Изменим номер п/п
						$m->query("UPDATE orderDoors SET NumPP=".$_POST['OrderDialogTableTDNumArr'][$i]." WHERE id=$DoorID" )
							or die($er=$er.mysqli_error($m));
					break;
					case "Add":
						try
						{
							$m->query('INSERT INTO orderDoors (idOrder, NumPP, name, H, W, S, SEqual, Open, Nalichnik, Dovod, RAL , Note, Markirovka, Count, Shtild, '.
								'WorkPetlya, WorkWindowCh, WorkWindowNoFrame, WorkWindowH, WorkWindowW, WorkWindowGain, WorkWindowGlass, WorkWindowGlassType, WorkWindowGrid, WorkWindowCh1, WorkWindowNoFrame1, WorkWindowH1, WorkWindowW1, WorkWindowGain1, WorkWindowGlass1, WorkWindowGlassType1, WorkWindowGrid1, WorkWindowCh2, WorkWindowNoFrame2, WorkWindowH2, WorkWindowW2, WorkWindowGain2, WorkWindowGlass2, WorkWindowGlassType2, WorkWindowGrid2, WorkUpGridCh, WorkDownGridCh, StvorkaCh, StvorkaPetlya, StvorkaWindowCh, StvorkaWindowNoFrame, StvorkaWindowH, StvorkaWindowW, StvorkaWindowGain, StvorkaWindowGlass, StvorkaWindowGlassType, StvorkaWindowGrid, StvorkaWindowCh1, StvorkaWindowNoFrame1, StvorkaWindowH1, StvorkaWindowW1, StvorkaWindowGain1, StvorkaWindowGlass1, StvorkaWindowGlassType1, StvorkaWindowGrid1, StvorkaWindowCh2, StvorkaWindowNoFrame2, StvorkaWindowH2, StvorkaWindowW2, StvorkaWindowGain2, StvorkaWindowGlass2, StvorkaWindowGlassType2, StvorkaWindowGrid2, StvorkaUpGridCh,StvorkaDownGridCh, FramugaCh, FramugaH, FramugaWindowCh, FramugaWindowNoFrame, FramugaWindowH, FramugaWindowW, FramugaWindowGain, FramugaWindowGlass, FramugaWindowGlassType, FramugaWindowGrid, FramugaUpGridCh, FramugaDownGridCh, Antipanik, Otboynik, Wicket, BoxLock, Otvetka, Isolation, '.
								'CostLaser, CostSgibka, CostSvarka, CostFrame, CostMdf, CostSborka, CostSborkaMdf, CostColor, CostUpak, CostShpt '.
								') values('.$idOrder." , ".
									" ".$_POST['OrderDialogTableTDNumArr'][$i]." , ".
									" '".$_POST['OrderDialogTableTDNameArr'][$i]."' , ".
									$H." , ".
									$W." , ".
									$S." , ".
									$SEqual." , '".
									$_POST['OrderDialogTableTDOpenArr'][$i]."' , '".
									$_POST['OrderDialogTableTDNalichnikArr'][$i]."' , '".
									$_POST['OrderDialogTableTDDovodArr'][$i]."' , '".
									$_POST['OrderDialogTableTDRALArr'][$i]."' , '".
									$_POST['OrderDialogTableTDNoteArr'][$i]."' , '".
									$_POST['OrderDialogTableTDMarkirovkaArr'][$i]."' , ".
									$_POST['OrderDialogTableTDCountArr'][$i]." , '".
									$_POST['OrderDialogTableTDShtildArr'][$i]."' , ".
									//--Рабочая створка
									$_POST['OrderDialogTableTDConstructWorkPetlya'][$i].", ".
									//Окно
									$_POST['OrderDialogTableTDConstructWorkWindowCh'][$i].", ".
									$_POST['OrderDialogTableTDConstructWorkWindowNoFrame'][$i].", ".
									$_POST['OrderDialogTableTDConstructWorkWindowH'][$i].", ".
									$_POST['OrderDialogTableTDConstructWorkWindowW'][$i].", ".
									$_POST['OrderDialogTableTDConstructWorkWindowGain'][$i].", ".
									$_POST['OrderDialogTableTDConstructWorkWindowGlass'][$i].", ".
									" '".$_POST['OrderDialogTableTDConstructWorkWindowGlassType'][$i]."', ".
									$_POST['OrderDialogTableTDConstructWorkWindowGrid'][$i].", ".
									//Окно 1
									$_POST['OrderDialogTableTDConstructWorkWindowCh1'][$i].", ".
									$_POST['OrderDialogTableTDConstructWorkWindowNoFrame1'][$i].", ".
									$_POST['OrderDialogTableTDConstructWorkWindowH1'][$i].", ".
									$_POST['OrderDialogTableTDConstructWorkWindowW1'][$i].", ".
									$_POST['OrderDialogTableTDConstructWorkWindowGain1'][$i].", ".
									$_POST['OrderDialogTableTDConstructWorkWindowGlass1'][$i].", ".
									" '".$_POST['OrderDialogTableTDConstructWorkWindowGlassType1'][$i]."', ".
									$_POST['OrderDialogTableTDConstructWorkWindowGrid1'][$i].", ".
									//Окно 2
									$_POST['OrderDialogTableTDConstructWorkWindowCh2'][$i].", ".
									$_POST['OrderDialogTableTDConstructWorkWindowNoFrame2'][$i].", ".
									$_POST['OrderDialogTableTDConstructWorkWindowH2'][$i].", ".
									$_POST['OrderDialogTableTDConstructWorkWindowW2'][$i].", ".
									$_POST['OrderDialogTableTDConstructWorkWindowGain2'][$i].", ".
									$_POST['OrderDialogTableTDConstructWorkWindowGlass2'][$i].", ".
									" '".$_POST['OrderDialogTableTDConstructWorkWindowGlassType2'][$i]."', ".
									$_POST['OrderDialogTableTDConstructWorkWindowGrid2'][$i].", ".
									//Вент решетка
									$_POST['OrderDialogTableTDConstructWorkUpGridCh'][$i].", ".
									$_POST['OrderDialogTableTDConstructWorkDownGridCh'][$i].", ".
									//--Вторая створка
									$_POST['OrderDialogTableTDConstructStvorkaCh'][$i].", ".
									$_POST['OrderDialogTableTDConstructStvorkaPetlya'][$i].", ".
									//Окно
									$_POST['OrderDialogTableTDConstructStvorkaWindowCh'][$i].", ".
									$_POST['OrderDialogTableTDConstructStvorkaWindowNoFrame'][$i].", ".
									$_POST['OrderDialogTableTDConstructStvorkaWindowH'][$i].", ".
									$_POST['OrderDialogTableTDConstructStvorkaWindowW'][$i].", ".
									$_POST['OrderDialogTableTDConstructStvorkaWindowGain'][$i].", ".
									$_POST['OrderDialogTableTDConstructStvorkaWindowGlass'][$i].", ".
									" '".$_POST['OrderDialogTableTDConstructStvorkaWindowGlassType'][$i]."', ".
									$_POST['OrderDialogTableTDConstructStvorkaWindowGrid'][$i].", ".
									//Окно 1
									$_POST['OrderDialogTableTDConstructStvorkaWindowCh1'][$i].", ".
									$_POST['OrderDialogTableTDConstructStvorkaWindowNoFrame1'][$i].", ".
									$_POST['OrderDialogTableTDConstructStvorkaWindowH1'][$i].", ".
									$_POST['OrderDialogTableTDConstructStvorkaWindowW1'][$i].", ".
									$_POST['OrderDialogTableTDConstructStvorkaWindowGain1'][$i].", ".
									$_POST['OrderDialogTableTDConstructStvorkaWindowGlass1'][$i].", ".
									" '".$_POST['OrderDialogTableTDConstructStvorkaWindowGlassType1'][$i]."', ".
									$_POST['OrderDialogTableTDConstructStvorkaWindowGrid1'][$i].", ".
									//Окно 2
									$_POST['OrderDialogTableTDConstructStvorkaWindowCh2'][$i].", ".
									$_POST['OrderDialogTableTDConstructStvorkaWindowNoFrame2'][$i].", ".
									$_POST['OrderDialogTableTDConstructStvorkaWindowH2'][$i].", ".
									$_POST['OrderDialogTableTDConstructStvorkaWindowW2'][$i].", ".
									$_POST['OrderDialogTableTDConstructStvorkaWindowGain2'][$i].", ".
									$_POST['OrderDialogTableTDConstructStvorkaWindowGlass2'][$i].", ".
									" '".$_POST['OrderDialogTableTDConstructStvorkaWindowGlassType2'][$i]."', ".
									$_POST['OrderDialogTableTDConstructStvorkaWindowGrid2'][$i].", ".
									//Вент решетка
									$_POST['OrderDialogTableTDConstructStvorkaUpGridCh'][$i].", ".
									$_POST['OrderDialogTableTDConstructStvorkaDownGridCh'][$i].", ".
									//--Фрамуга
									$_POST['OrderDialogTableTDConstructFramugaCh'][$i].", ".
									$_POST['OrderDialogTableTDConstructFramugaH'][$i].", ".
									$_POST['OrderDialogTableTDConstructFramugaWindowCh'][$i].", ".
									$_POST['OrderDialogTableTDConstructFramugaWindowNoFrame'][$i].", ".
									$_POST['OrderDialogTableTDConstructFramugaWindowH'][$i].", ".
									$_POST['OrderDialogTableTDConstructFramugaWindowW'][$i].", ".
									$_POST['OrderDialogTableTDConstructFramugaWindowGain'][$i].", ".
									$_POST['OrderDialogTableTDConstructFramugaWindowGlass'][$i].", ".
									" '".$_POST['OrderDialogTableTDConstructFramugaWindowGlassType'][$i]."', ".
									$_POST['OrderDialogTableTDConstructFramugaWindowGrid'][$i].", ".
									$_POST['OrderDialogTableTDConstructFramugaUpGridCh'][$i].", ".
									$_POST['OrderDialogTableTDConstructFramugaDownGridCh'][$i].", ".
									//Дополнительно
									$_POST["Antipanik"][$i]." , ".
									$_POST["Otboynik"][$i]." , ".
									$_POST["Wicket"][$i]." , ".
									$_POST["BoxLock"][$i]." , ".
									$_POST["Otvetka"][$i]." , ".
									$_POST["Isolation"][$i]." , ".
									//--Зарплата
									$_POST["CostLaser"][$i].", ".
									$_POST["CostSgibka"][$i].", ".
									$_POST["CostSvarka"][$i].", ".
									$_POST["CostFrame"][$i].", ".
									$_POST["CostMdf"][$i].", ".
									$_POST["CostSborka"][$i].", ".
									$_POST["CostSborkaMdf"][$i].", ".
									$_POST["CostColor"][$i].", ".
									$_POST["CostUpak"][$i].", ".
									$_POST["CostShpt"][$i]." ".
								")"
							) or die($er=$er.mysqli_error($m));
						}
						catch (mysqli_sql_exception $e) {$er=$er.$e->errorMessage();};
					break;
					case "Edit":
						try
						{
							$StatusTD=$_POST["OrderDialogTableTDWorkStatusArr"][$i];

							$d=$m->query("SELECT CostLaser, CostSgibka, CostSvarka, CostFrame, CostMdf, CostSborka, CostColor, CostSborkaMdf, CostUpak, CostShpt FROM OrderDoors WHERE id=$DoorID");
							$r=$d->fetch_assoc();
							$CostOld=array(
								"CostLaser"=>$r["CostLaser"],
								"CostSgibka"=>$r["CostSgibka"],
								"CostSvarka"=>$r["CostSvarka"],
								"CostFrame"=>$r["CostFrame"],
								"CostMdf"=>$r["CostMdf"],
								"CostSborka"=>$r["CostSborka"],
								"CostColor"=>$r["CostColor"],
								"CostSborkaMdf"=>$r["CostSborkaMdf"],
								"CostUpak"=>$r["CostUpak"],
								"CostShpt"=>$r["CostShpt"]
							);
							$d->close();
							switch ($StatusTD)
							{
								case "Start":case "Work":
									$m->query("UPDATE orderDoors SET ".
										"NumPP=".$_POST['OrderDialogTableTDNumArr'][$i].", ".
										"name='".$_POST['OrderDialogTableTDNameArr'][$i]."' , ".
										"H=".$H." , ".
										"W=".$W." , ".
										"S=".$S." , ".
										"SEqual=".$SEqual." , ".
										"Open='".$_POST['OrderDialogTableTDOpenArr'][$i]."' , ".
										"Nalichnik='".$_POST['OrderDialogTableTDNalichnikArr'][$i]."' , ".
										"Dovod='".$_POST['OrderDialogTableTDDovodArr'][$i]."' , ".
										"RAL='".$_POST['OrderDialogTableTDRALArr'][$i]."' , ".
										"Note='".$_POST['OrderDialogTableTDNoteArr'][$i]."' , ".
										"Markirovka='".$_POST['OrderDialogTableTDMarkirovkaArr'][$i]."' , ".
										"Count=".$_POST["OrderDialogTableTDCountArr"][$i].", ".
										"Shtild='".$_POST['OrderDialogTableTDShtildArr'][$i]."', ".
										//--Рабочая створка
										"WorkPetlya=".$_POST['OrderDialogTableTDConstructWorkPetlya'][$i].", ".
										//Окно
										"WorkWindowCh=".$_POST['OrderDialogTableTDConstructWorkWindowCh'][$i].", ".
										"WorkWindowNoFrame=".$_POST['OrderDialogTableTDConstructWorkWindowNoFrame'][$i].", ".
										"WorkWindowH=".$_POST['OrderDialogTableTDConstructWorkWindowH'][$i].", ".
										"WorkWindowW=".$_POST['OrderDialogTableTDConstructWorkWindowW'][$i].", ".
										"WorkWindowGain=".$_POST['OrderDialogTableTDConstructWorkWindowGain'][$i].", ".
										"WorkWindowGlass=".$_POST['OrderDialogTableTDConstructWorkWindowGlass'][$i].", ".
										"WorkWindowGlassType='".$_POST['OrderDialogTableTDConstructWorkWindowGlassType'][$i]."', ".
										"WorkWindowGrid=".$_POST['OrderDialogTableTDConstructWorkWindowGrid'][$i].", ".
										//Окно 1
										"WorkWindowCh1=".$_POST['OrderDialogTableTDConstructWorkWindowCh1'][$i].", ".
										"WorkWindowNoFrame1=".$_POST['OrderDialogTableTDConstructWorkWindowNoFrame1'][$i].", ".
										"WorkWindowH1=".$_POST['OrderDialogTableTDConstructWorkWindowH1'][$i].", ".
										"WorkWindowW1=".$_POST['OrderDialogTableTDConstructWorkWindowW1'][$i].", ".
										"WorkWindowGain1=".$_POST['OrderDialogTableTDConstructWorkWindowGain1'][$i].", ".
										"WorkWindowGlass1=".$_POST['OrderDialogTableTDConstructWorkWindowGlass1'][$i].", ".
										"WorkWindowGlassType1='".$_POST['OrderDialogTableTDConstructWorkWindowGlassType1'][$i]."', ".
										"WorkWindowGrid1=".$_POST['OrderDialogTableTDConstructWorkWindowGrid1'][$i].", ".
										//Окно 2
										"WorkWindowCh2=".$_POST['OrderDialogTableTDConstructWorkWindowCh2'][$i].", ".
										"WorkWindowNoFrame2=".$_POST['OrderDialogTableTDConstructWorkWindowNoFrame2'][$i].", ".
										"WorkWindowH2=".$_POST['OrderDialogTableTDConstructWorkWindowH2'][$i].", ".
										"WorkWindowW2=".$_POST['OrderDialogTableTDConstructWorkWindowW2'][$i].", ".
										"WorkWindowGain2=".$_POST['OrderDialogTableTDConstructWorkWindowGain2'][$i].", ".
										"WorkWindowGlass2=".$_POST['OrderDialogTableTDConstructWorkWindowGlass2'][$i].", ".
										"WorkWindowGlassType2='".$_POST['OrderDialogTableTDConstructWorkWindowGlassType2'][$i]."', ".
										"WorkWindowGrid2=".$_POST['OrderDialogTableTDConstructWorkWindowGrid2'][$i].", ".
										//Вент решетки
										"WorkUpGridCh=".$_POST['OrderDialogTableTDConstructWorkUpGridCh'][$i].", ".
										"WorkDownGridCh=".$_POST['OrderDialogTableTDConstructWorkDownGridCh'][$i].", ".
										//--Вторая створка
										"StvorkaCh=".$_POST['OrderDialogTableTDConstructStvorkaCh'][$i].", ".
										"StvorkaPetlya=".$_POST['OrderDialogTableTDConstructStvorkaPetlya'][$i].", ".
										//Окно
										"StvorkaWindowCh=".$_POST['OrderDialogTableTDConstructStvorkaWindowCh'][$i].", ".
										"StvorkaWindowNoFrame=".$_POST['OrderDialogTableTDConstructStvorkaWindowNoFrame'][$i].", ".
										"StvorkaWindowH=".$_POST['OrderDialogTableTDConstructStvorkaWindowH'][$i].", ".
										"StvorkaWindowW=".$_POST['OrderDialogTableTDConstructStvorkaWindowW'][$i].", ".
										"StvorkaWindowGain=".$_POST['OrderDialogTableTDConstructStvorkaWindowGain'][$i].", ".
										"StvorkaWindowGlass=".$_POST['OrderDialogTableTDConstructStvorkaWindowGlass'][$i].", ".
										"StvorkaWindowGlassType='".$_POST['OrderDialogTableTDConstructStvorkaWindowGlassType'][$i]."', ".
										"StvorkaWindowGrid=".$_POST['OrderDialogTableTDConstructStvorkaWindowGrid'][$i].", ".
										//Окно 1
										"StvorkaWindowCh1=".$_POST['OrderDialogTableTDConstructStvorkaWindowCh1'][$i].", ".
										"StvorkaWindowNoFrame1=".$_POST['OrderDialogTableTDConstructStvorkaWindowNoFrame1'][$i].", ".
										"StvorkaWindowH1=".$_POST['OrderDialogTableTDConstructStvorkaWindowH1'][$i].", ".
										"StvorkaWindowW1=".$_POST['OrderDialogTableTDConstructStvorkaWindowW1'][$i].", ".
										"StvorkaWindowGain1=".$_POST['OrderDialogTableTDConstructStvorkaWindowGain1'][$i].", ".
										"StvorkaWindowGlass1=".$_POST['OrderDialogTableTDConstructStvorkaWindowGlass1'][$i].", ".
										"StvorkaWindowGlassType1='".$_POST['OrderDialogTableTDConstructStvorkaWindowGlassType1'][$i]."', ".
										"StvorkaWindowGrid1=".$_POST['OrderDialogTableTDConstructStvorkaWindowGrid1'][$i].", ".
										//Окно 2
										"StvorkaWindowCh2=".$_POST['OrderDialogTableTDConstructStvorkaWindowCh2'][$i].", ".
										"StvorkaWindowNoFrame2=".$_POST['OrderDialogTableTDConstructStvorkaWindowNoFrame2'][$i].", ".
										"StvorkaWindowH2=".$_POST['OrderDialogTableTDConstructStvorkaWindowH2'][$i].", ".
										"StvorkaWindowW2=".$_POST['OrderDialogTableTDConstructStvorkaWindowW2'][$i].", ".
										"StvorkaWindowGain2=".$_POST['OrderDialogTableTDConstructStvorkaWindowGain2'][$i].", ".
										"StvorkaWindowGlass2=".$_POST['OrderDialogTableTDConstructStvorkaWindowGlass2'][$i].", ".
										"StvorkaWindowGlassType2='".$_POST['OrderDialogTableTDConstructStvorkaWindowGlassType2'][$i]."', ".
										"StvorkaWindowGrid2=".$_POST['OrderDialogTableTDConstructStvorkaWindowGrid2'][$i].", ".
										//Вент решетка
										"StvorkaUpGridCh=".$_POST['OrderDialogTableTDConstructStvorkaUpGridCh'][$i].", ".
										"StvorkaDownGridCh=".$_POST['OrderDialogTableTDConstructStvorkaDownGridCh'][$i].", ".
										//--Фрамуга
										"FramugaCh=".$_POST['OrderDialogTableTDConstructFramugaCh'][$i].", ".
										"FramugaH=".$_POST['OrderDialogTableTDConstructFramugaH'][$i].", ".
										//Окно
										"FramugaWindowCh=".$_POST['OrderDialogTableTDConstructFramugaWindowCh'][$i].", ".
										"FramugaWindowNoFrame=".$_POST['OrderDialogTableTDConstructFramugaWindowNoFrame'][$i].", ".
										"FramugaWindowH=".$_POST['OrderDialogTableTDConstructFramugaWindowH'][$i].", ".
										"FramugaWindowW=".$_POST['OrderDialogTableTDConstructFramugaWindowW'][$i].", ".
										"FramugaWindowGain=".$_POST['OrderDialogTableTDConstructFramugaWindowGain'][$i].", ".
										"FramugaWindowGlass=".$_POST['OrderDialogTableTDConstructFramugaWindowGlass'][$i].", ".
										"FramugaWindowGlassType='".$_POST['OrderDialogTableTDConstructFramugaWindowGlassType'][$i]."', ".
										"FramugaWindowGrid=".$_POST['OrderDialogTableTDConstructFramugaWindowGrid'][$i].", ".
										//Вент решетки
										"FramugaUpGridCh=".$_POST['OrderDialogTableTDConstructFramugaUpGridCh'][$i].", ".
										"FramugaDownGridCh=".$_POST['OrderDialogTableTDConstructFramugaDownGridCh'][$i].", ".
										//Дополнительно
										"Antipanik=".$_POST["Antipanik"][$i].", ".
										"Otboynik=".$_POST["Otboynik"][$i].", ".
										"Wicket=".$_POST["Wicket"][$i].", ".
										"BoxLock=".$_POST["BoxLock"][$i].", ".
										"Otvetka=".$_POST["Otvetka"][$i].", ".
										"Isolation=".$_POST["Isolation"][$i].", ".
										//--Зарплата
										"CostLaser=".$_POST['CostLaser'][$i].", ".
										"CostSgibka=".$_POST['CostSgibka'][$i].", ".
										"CostSvarka=".$_POST['CostSvarka'][$i].", ".
										"CostFrame=".$_POST['CostFrame'][$i].", ".
										"CostMdf=".$_POST['CostMdf'][$i].", ".
										"CostSborka=".$_POST['CostSborka'][$i].", ".
										"CostSborkaMdf=".$_POST['CostSborkaMdf'][$i].", ".
										"CostColor=".$_POST['CostColor'][$i].", ".
										"CostUpak=".$_POST['CostUpak'][$i].", ".
										"CostShpt=".$_POST['CostShpt'][$i]." ".
										"WHERE id=$DoorID"
									)
									or die($er=$er.mysqli_error($m));
									break;
								case "Complite":
									$m->query("UPDATE orderDoors SET ".
										"Shtild='".$_POST['OrderDialogTableTDShtildArr'][$i]."', ".
										//Зарплата
										"CostLaser=".$_POST['CostLaser'][$i].", ".
										"CostSgibka=".$_POST['CostSgibka'][$i].", ".
										"CostSvarka=".$_POST['CostSvarka'][$i].", ".
										"CostFrame=".$_POST['CostFrame'][$i].", ".
										"CostMdf=".$_POST['CostMdf'][$i].", ".
										"CostSborka=".$_POST['CostSborka'][$i].", ".
										"CostSborkaMdf=".$_POST['CostSborkaMdf'][$i].", ".
										"CostColor=".$_POST['CostColor'][$i].", ".
										"CostUpak=".$_POST['CostUpak'][$i].", ".
										"CostShpt=".$_POST['CostShpt'][$i]." ".
										"WHERE id=$DoorID"
									)
									or die($er=$er.mysqli_error($m));
									break;
							};
							if($StatusTD=="Work" || $StatusTD=="Complite")
								$CostDoors[]=array(
									"idDoor"=>$DoorID,
									"CostLaser"=>$_POST["CostLaser"][$i],
									"CostSgibka"=>$_POST["CostSgibka"][$i],
									"CostSvarka"=>$_POST["CostSvarka"][$i],
									"CostFrame"=>$_POST["CostFrame"][$i],
									"CostSborka"=>$_POST["CostSborka"][$i],
									"CostMdf"=>$_POST["CostMdf"][$i],
									"CostSborkaMdf"=>$_POST["CostSborkaMdf"][$i],
									"CostColor"=>$_POST["CostColor"][$i],
									"CostUpak"=>$_POST["CostUpak"][$i],
									"CostShpt"=>$_POST["CostShpt"][$i]
								);
							/*
							if($StatusTD=="Work" || $StatusTD=="Complite")
							{
								$StepsArr=array("CostLaser", "CostSgibka", "CostSvarka", "CostFrame", "CostSborka", "CostColor", "CostUpak", "CostShpt", "CostMdf", "CostSborkaMdf");
								$StepNum=1;
								foreach ($StepsArr as $CostName)
								{
									//if($CostOld[$CostName]!=$_POST[$CostName][$i])
									{
										$Cost=$_POST[$CostName][$i];
										$m->query("Update NaryadComplite nc SET nc.Cost=$Cost WHERE nc.Step=$StepNum AND nc.idNaryad IN (SELECT n.id FROM Naryad n WHERE n.idDoors=$DoorID)") or die($er=$er.mysqli_error($m));
									};
									$StepNum++;
								};
							};
							*/
						}
						catch (mysqli_sql_exception $e) {$er=$er.$e->errorMessage();};
					break;
					case "Del":
						try
						{
							$m->query("DELETE FROM orderDoors WHERE id=".$_POST["OrderDialogTableTDIDArr"][$i]) or die($er=$er.mysqli_error($m));
						}
						catch (mysqli_sql_exception $e) {$er=$er.$e->errorMessage();};
					break;
				};
				$i++;
			};
			//Создаем таск на изменение стоимости
			//Делаем проверку изменилась стоимость работ, тогда обновим стоимость работ для нарядов
			$arr=array();
			$arr["DialogGUID"]=$_POST["DialogGUID"];
			$arr["Status"]="Add";
			$arr["DoorList"]=$CostDoors;
			$IISCon=curl_init();
			curl_setopt($IISCon, CURLOPT_URL, "http://localhost:8282/api/SavePayment");
			curl_setopt($IISCon,CURLOPT_POST,1);
			curl_setopt($IISCon, CURLOPT_HTTPHEADER,array('Content-Type:application/json'));
			curl_setopt($IISCon,CURLOPT_POSTFIELDS,json_encode($arr));
			curl_setopt($IISCon,CURLOPT_RETURNTRANSFER,1);
			curl_exec($IISCon);
			curl_close($IISCon);

			if($er=="")
			{
				$m->commit(); echo "ok";
			}
			else
			{
				echo $er; $m->rollback();
			};
		break;
		
		case "OrederClose":
			$m->query("UPDATE oreders SET UserBlock=null WHERE id=".$_POST["idOrder"]);
		break;
		
		case 'OrderAdd':
			$er="ok";
			$i=1;
			$m->query("START TRANSACTION") or die ($er="Ошибка начала транзакции");
			if($er=="ok")
			{
				$ShetDate="null";
				if($_POST['ShetDate']!="") $ShetDate="STR_TO_DATE('".$_POST['ShetDate']."', '%d.%m.%Y')";
				
				$m->query ("INSERT INTO oreders (Blank, BlankDate, Shet, ShetDate, Srok, Zakaz, Contact, Note, Manager)".
				" VALUES ('".$_POST['Blank']."' , STR_TO_DATE('".$_POST['BlankDate']."', '%d.%m.%Y') , '".$_POST['Shet']."' , ".$ShetDate." , '".$_POST['Srok']."' , '".$_POST['Zakaz']."' , '".$_POST['Contact']."' , '".$_POST['Note']."' , '".$_SESSION["AutorizeFIO"]."')")
				or die ($er=$er."Ошибка добавления в таблицу orders<br>");
				
				$d=$m->query("SELECT MAX(id) as num FROM oreders");
				$r=$d->fetch_assoc();
				$max=$r["num"];
				$d->close();
			
				$i=1;
				while($i<count($_POST['OrderDialogTableTDNameArr']))
					if($_POST['OrderDialogTableTDNameArr'][$i]!='')
					{
						$S="null";
						if($_POST['OrderDialogTableTDSArr'][$i]!="") $S=$_POST['OrderDialogTableTDSArr'][$i];
						$m->query('INSERT INTO orderDoors (idOrder, NumPP, name, H, W, S, Open, Nalichnik, Dovod, RAL , Note, Markirovka, Count, Shtild, '.
						'WorkPetlya, WorkWindowCh, WorkWindowH, WorkWindowW, WorkWindowGrid, WorkWindowGlass, WorkWindowGlassType, StvorkaCh, StvorkaPetlya, StvorkaWindowCh, StvorkaWindowH, StvorkaWindowW, StvorkaWindowGrid, StvorkaWindowGlass, StvorkaWindowGlassType, FramugaCh, FramugaWindowCh, FramugaWindowH, FramugaWindowW, FramugaWindowGrid, FramugaWindowGlass, FramugaWindowGlassType, '.
						'CostLaser, CostSgibka, CostSvarka, CostFrame, CostSborka, CostColor, CostUpak, CostShpt '.
						') values('.$max." , ".
							" ".$i." , ".
							" '".$_POST['OrderDialogTableTDNameArr'][$i]."' , ".
							$_POST['OrderDialogTableTDHArr'][$i]." , ".
							$_POST['OrderDialogTableTDWArr'][$i]." , ".
							$S." , '".
							$_POST['OrderDialogTableTDOpenArr'][$i]."' , '".
							$_POST['OrderDialogTableTDNalichnikArr'][$i]."' , '".
							$_POST['OrderDialogTableTDDovodArr'][$i]."' , '".
							$_POST['OrderDialogTableTDRALArr'][$i]."' , '".
							$_POST['OrderDialogTableTDNoteArr'][$i]."' , '".
							$_POST['OrderDialogTableTDMarkirovkaArr'][$i]."' , ".
							$_POST['OrderDialogTableTDCountArr'][$i]." , '".
							$_POST['OrderDialogTableTDShtildArr'][$i]."' , ".
							//Рабочая створка
							$_POST['OrderDialogTableTDConstructWorkPetlya'][$i].", ".
							$_POST['OrderDialogTableTDConstructWorkWindowCh'][$i].", ".
							$_POST['OrderDialogTableTDConstructWorkWindowH'][$i].", ".
							$_POST['OrderDialogTableTDConstructWorkWindowW'][$i].", ".
							$_POST['OrderDialogTableTDConstructWorkWindowGrid'][$i].", ".
							$_POST['OrderDialogTableTDConstructWorkWindowGlass'][$i].", ".
							" '".$_POST['OrderDialogTableTDConstructWorkWindowGlassType'][$i]."', ".
							//Вторая створка
							$_POST['OrderDialogTableTDConstructStvorkaCh'][$i].", ".
							$_POST['OrderDialogTableTDConstructStvorkaPetlya'][$i].", ".
							$_POST['OrderDialogTableTDConstructStvorkaWindowCh'][$i].", ".
							$_POST['OrderDialogTableTDConstructStvorkaWindowH'][$i].", ".
							$_POST['OrderDialogTableTDConstructStvorkaWindowW'][$i].", ".
							$_POST['OrderDialogTableTDConstructStvorkaWindowGrid'][$i].", ".
							$_POST['OrderDialogTableTDConstructStvorkaWindowGlass'][$i].", ".
							" '".$_POST['OrderDialogTableTDConstructStvorkaWindowGlassType'][$i]."', ".
							//Фрамуга
							$_POST['OrderDialogTableTDConstructFramugaCh'][$i].", ".
							$_POST['OrderDialogTableTDConstructFramugaWindowCh'][$i].", ".
							$_POST['OrderDialogTableTDConstructFramugaWindowH'][$i].", ".
							$_POST['OrderDialogTableTDConstructFramugaWindowW'][$i].", ".
							$_POST['OrderDialogTableTDConstructFramugaWindowGrid'][$i].", ".
							$_POST['OrderDialogTableTDConstructFramugaWindowGlass'][$i].", ".
							" '".$_POST['OrderDialogTableTDConstructFramugaWindowGlassType'][$i]."', ".
							$_POST["CostLaser"][$i].", ".
							$_POST["CostSgibka"][$i].", ".
							$_POST["CostSvarka"][$i].", ".
							$_POST["CostFrame"][$i].", ".
							$_POST["CostSborka"][$i].", ".
							$_POST["CostColor"][$i].", ".
							$_POST["CostUpak"][$i].", ".
							$_POST["CostShpt"][$i]." ".
						")") or die ($er=$er."Ошибка добавления записи в таблицу OrderDoors");
					
						$i++;
					};
			};
			if($er=="ok")
			{
				$m->query("COMMIT") or die($er="Ошибка применения транзакции");
			}
			else
				$m->query("ROLLBACK") or die($er=$er."Ошибка отката транзакции");
			echo $er;
		break;
		
		case 'select':
			$where="";
			if(isset($_POST["Where"])) $where=$_POST["Where"];
			//$result=$m->query("SELECT o.*, sum(d.Count) as DoorsCount, DATE_FORMAT(o.BlankDate,'%d.%m.%Y') as bd , DATE_FORMAT(o.ShetDate,'%d.%m.%Y') as sd  FROM oreders AS o, orderdoors AS d WHERE o.id=d.idOrder AND ".$where." GROUP BY o.Blank ORDER BY o.Blank");
			/*$result=$m->query("SELECT o.*, (SELECT SUM(Count) FROM orderdoors WHERE idOrder=o.id) as DoorsCount, DATE_FORMAT(o.BlankDate,'%d.%m.%y') as bd , DATE_FORMAT(o.ShetDate,'%d.%m.%y') as sd FROM oreders o ".
				"LEFT JOIN (SELECT * FROM orderdoors ) d ".
				"ON o.id=d.idOrder WHERE 1=1 ".$where." GROUP BY d.idOrder ORDER BY o.Blank DESC");*/
			$result=$m->query("SELECT o.*, SUM(od.Count) AS DoorsCount, DATE_FORMAT(o.BlankDate,'%d.%m.%y') as bd , DATE_FORMAT(o.ShetDate,'%d.%m.%y') as sd FROM oreders o ".
				"LEFT JOIN OrderDoors od ".
				"ON o.id=od.idOrder ".
				"WHERE 1=1 $where ".
				"GROUP BY o.id ".
				"ORDER BY o.BlankDate DESC, o.Blank DESC"
				);
			$i=0;
			$a=array();
			while ($line = $result->fetch_assoc())
			{
				$color='Start';
				switch($line['status'])
				{
					case -1: $color='Cancel'; break;
					case 1: $color='Work'; break;
					case 2: $color='Complite'; break;
					case 3: $color='Complite'; break;
				};
				$Srok=$line['Srok'];
				$colorAlertTD="";
				//Предупреждение срока ДО. Начинает гореть красным заранее 4 дня до просрочки
				/*
				if($line['bd']!=null & $line['Srok']!="" & $line['status']!=3)
				{
					list( $day,$month, $year) =split("[/.-]",$line['bd']);
					$md=mktime(0,0,0,(int)$month,(int)$day+(int)$line['Srok']-4,(int)$year);
					if($md-time()<=0) $colorAlertTD="Alert";
					$md=mktime(0,0,0,(int)$month,(int)$day+(int)$line['Srok'],(int)$year);
					$Srok=floor( ($md-time())/24/60/60 );
				};
				*/
				//--------------------------------------------------------
				$Shet=""; if($line['Shet']!=null) $Shet=$line['Shet'];
				$ShetDate=""; if($line['sd']!=null & $line['sd']!="00.00.0000") $ShetDate=$line['sd'];
				$Manager=""; if($line['Manager']!=null) $Manager=$line['Manager'];
				$zakazSTR=""; $zakazSTR=$line['Zakaz'];
				$a[$i]=array(
					"Color"=>$color,
					"ColorAlertTD"=>$colorAlertTD,
					"id"=>$line['id'],
					"Blank"=>$line['Blank'],
					"BlankDate"=>$line['bd'],
					"Shet"=>$Shet,
					"ShetDate"=>$ShetDate,
					"Srok"=>$Srok,
					"DoorsCount"=>$line["DoorsCount"],
					"Zakaz"=>$zakazSTR,
					"Manager"=>$Manager,
					"UserBlock"=>$line["UserBlock"]
				);
				$i++;
			};
			echo json_encode($a);
			$result->close();;
		break;
		
		case 'EditStart':
			$arDoors=array();
			$result=$m->query('SELECT o.*, (SELECT count(*) FROM naryad n WHERE o.id=n.idDoors) as NaryadCount, (SELECT count(*) FROM naryad n WHERE o.id=n.idDoors AND n.UpakCompliteFlag=1) as NaryadCompliteCount, (SELECT count(*) FROM naryad n WHERE o.id=n.idDoors AND n.ShptCompliteFlag=1) as NaryadShptCount FROM orderdoors o WHERE idOrder='.$_POST['id']." ORDER BY NumPP");
			$i=0;
			while ($line = $result->fetch_assoc())
			{
				$H=$line['H']!=null ? $line['H'] : "";
				$W=$line['W']!=null ? $line['W'] : "";
				$S=($line['S']!=null)?$line['S']:($line['SEqual']==1?"Равн.":"");
				//--Рабочая створка
				$WorkPetlya=""; if($line["WorkPetlya"]!=null) $WorkPetlya=$line["WorkPetlya"];
				//Окно
				$WorkWindowCh="false"; if($line["WorkWindowCh"]==1) $WorkWindowCh="true";
				$WorkWindowNoFrame="false"; if($line["WorkWindowNoFrame"]==1) $WorkWindowNoFrame="true";
				$WorkWindowH=""; if($line["WorkWindowH"]!=null) $WorkWindowH=$line["WorkWindowH"];
				$WorkWindowW=""; if($line["WorkWindowW"]!=null) $WorkWindowW=$line["WorkWindowW"];
				$WorkWindowGain="false"; if($line["WorkWindowGain"]==1) $WorkWindowGain="true";
				$WorkWindowGlass="false"; if($line["WorkWindowGlass"]==1) $WorkWindowGlass="true";
				$WorkWindowGrid="false"; if($line["WorkWindowGrid"]==1) $WorkWindowGrid="true";
				//Окно 1
				$WorkWindowCh1="false"; if($line["WorkWindowCh1"]==1) $WorkWindowCh1="true";
				$WorkWindowNoFrame1="false"; if($line["WorkWindowNoFrame1"]==1) $WorkWindowNoFrame1="true";
				$WorkWindowH1=""; if($line["WorkWindowH1"]!=null) $WorkWindowH1=$line["WorkWindowH1"];
				$WorkWindowW1=""; if($line["WorkWindowW1"]!=null) $WorkWindowW1=$line["WorkWindowW1"];
				$WorkWindowGain1="false"; if($line["WorkWindowGain1"]==1) $WorkWindowGain1="true";
				$WorkWindowGlass1="false"; if($line["WorkWindowGlass1"]==1) $WorkWindowGlass1="true";
				$WorkWindowGrid1="false"; if($line["WorkWindowGrid1"]==1) $WorkWindowGrid1="true";
				//Окно 2
				$WorkWindowCh2="false"; if($line["WorkWindowCh2"]==1) $WorkWindowCh2="true";
				$WorkWindowNoFrame2="false"; if($line["WorkWindowNoFrame2"]==1) $WorkWindowNoFrame2="true";
				$WorkWindowH2=""; if($line["WorkWindowH2"]!=null) $WorkWindowH2=$line["WorkWindowH2"];
				$WorkWindowW2=""; if($line["WorkWindowW2"]!=null) $WorkWindowW2=$line["WorkWindowW2"];
				$WorkWindowGain2="false"; if($line["WorkWindowGain2"]==1) $WorkWindowGain2="true";
				$WorkWindowGlass2="false"; if($line["WorkWindowGlass2"]==1) $WorkWindowGlass2="true";
				$WorkWindowGrid2="false"; if($line["WorkWindowGrid2"]==1) $WorkWindowGrid2="true";
				//Вент Решетки
				$WorkUpGridCh="false"; if($line["WorkUpGridCh"]==1) $WorkUpGridCh="true";
				$WorkDownGridCh="false"; if($line["WorkDownGridCh"]==1) $WorkDownGridCh="true";
				//--Вторая створка
				$StvorkaCh="false"; if($line["StvorkaCh"]==1) $StvorkaCh="true";
				$StvorkaPetlya=""; if($line["StvorkaPetlya"]!=null) $StvorkaPetlya=$line["StvorkaPetlya"];
				//Окно
				$StvorkaWindowCh="false"; if($line["StvorkaWindowCh"]==1) $StvorkaWindowCh="true";
				$StvorkaWindowNoFrame="false"; if($line["StvorkaWindowNoFrame"]==1) $StvorkaWindowNoFrame="true";
				$StvorkaWindowH=""; if($line["StvorkaWindowH"]!=null) $StvorkaWindowH=$line["StvorkaWindowH"];
				$StvorkaWindowW=""; if($line["StvorkaWindowW"]!=null) $StvorkaWindowW=$line["StvorkaWindowW"];
				$StvorkaWindowGain="false"; if($line["StvorkaWindowGain"]==1) $StvorkaWindowGain="true";
				$StvorkaWindowGlass="false"; if($line["StvorkaWindowGlass"]==1) $StvorkaWindowGlass="true";
				$StvorkaWindowGrid="false"; if($line["StvorkaWindowGrid"]==1) $StvorkaWindowGrid="true";
				//Окно 1
				$StvorkaWindowCh1="false"; if($line["StvorkaWindowCh1"]==1) $StvorkaWindowCh1="true";
				$StvorkaWindowNoFrame1="false"; if($line["StvorkaWindowNoFrame1"]==1) $StvorkaWindowNoFrame1="true";
				$StvorkaWindowH1=""; if($line["StvorkaWindowH1"]!=null) $StvorkaWindowH1=$line["StvorkaWindowH1"];
				$StvorkaWindowW1=""; if($line["StvorkaWindowW1"]!=null) $StvorkaWindowW1=$line["StvorkaWindowW1"];
				$StvorkaWindowGain1="false"; if($line["StvorkaWindowGain1"]==1) $StvorkaWindowGain1="true";
				$StvorkaWindowGlass1="false"; if($line["StvorkaWindowGlass1"]==1) $StvorkaWindowGlass1="true";
				$StvorkaWindowGrid1="false"; if($line["StvorkaWindowGrid1"]==1) $StvorkaWindowGrid1="true";
				//Окно 2
				$StvorkaWindowCh2="false"; if($line["StvorkaWindowCh2"]==1) $StvorkaWindowCh2="true";
				$StvorkaWindowNoFrame2="false"; if($line["StvorkaWindowNoFrame2"]==1) $StvorkaWindowNoFrame2="true";
				$StvorkaWindowH2=""; if($line["StvorkaWindowH2"]!=null) $StvorkaWindowH2=$line["StvorkaWindowH2"];
				$StvorkaWindowW2=""; if($line["StvorkaWindowW2"]!=null) $StvorkaWindowW2=$line["StvorkaWindowW2"];
				$StvorkaWindowGain2="false"; if($line["StvorkaWindowGain2"]==1) $StvorkaWindowGain2="true";
				$StvorkaWindowGlass2="false"; if($line["StvorkaWindowGlass2"]==1) $StvorkaWindowGlass2="true";
				$StvorkaWindowGrid2="false"; if($line["StvorkaWindowGrid2"]==1) $StvorkaWindowGrid2="true";
				//Вент решетки
				$StvorkaUpGridCh="false"; if($line["StvorkaUpGridCh"]==1) $StvorkaUpGridCh="true";
				$StvorkaDownGridCh="false"; if($line["StvorkaDownGridCh"]==1) $StvorkaDownGridCh="true";
				//--Фрамуга
				$FramugaCh="false"; if($line["FramugaCh"]==1) $FramugaCh="true";
				$FramugaWindowCh="false"; if($line["FramugaWindowCh"]==1) $FramugaWindowCh="true";
				$FramugaWindowNoFrame="false"; if($line["FramugaWindowNoFrame"]==1) $FramugaWindowNoFrame="true";
				$FramugaWindowH=""; if($line["FramugaWindowH"]!=null) $FramugaWindowH=$line["FramugaWindowH"];
				$FramugaWindowW=""; if($line["FramugaWindowW"]!=null) $FramugaWindowW=$line["FramugaWindowW"];
				$FramugaWindowGain="false"; if($line["FramugaWindowGain"]==1) $FramugaWindowGain="true";
				$FramugaWindowGlass="false"; if($line["FramugaWindowGlass"]==1) $FramugaWindowGlass="true";
				$FramugaWindowGrid="false"; if($line["FramugaWindowGrid"]==1) $FramugaWindowGrid="true";
				$FramugaUpGridCh="false"; if($line["FramugaUpGridCh"]==1) $FramugaUpGridCh="true";
				$FramugaDownGridCh="false"; if($line["FramugaDownGridCh"]==1) $FramugaDownGridCh="true";
				//--Дополнительно
				$Antipanik=$line["Antipanik"]!=null ? $line["Antipanik"] : 0;
				$Otboynik=$line["Otboynik"]!=null ? $line["Otboynik"] : 0;
				$Wicket=$line["Wicket"]!=null ? $line["Wicket"] : 0;
				$BoxLock=$line["BoxLock"]!=null ? $line["BoxLock"] : 0;
				$Otvetka=$line["Otvetka"]!=null ? $line["Otvetka"] : 0;
				$Isolation=$line["Isolation"]!=null ? $line["Isolation"] : 0;
				//--Зарплата
				$CostLaser="0"; if($line["CostLaser"]!=null) $CostLaser=$line["CostLaser"];
				$CostSgibka="0"; if($line["CostSgibka"]!=null) $CostSgibka=$line["CostSgibka"];
				$CostSvarka="0"; if($line["CostSvarka"]!=null) $CostSvarka=$line["CostSvarka"];
				$CostFrame="0"; if($line["CostFrame"]!=null) $CostFrame=$line["CostFrame"];
				$CostMdf="0"; if($line["CostMdf"]!=null) $CostMdf=$line["CostMdf"];
				$CostSborka="0"; if($line["CostSborka"]!=null) $CostSborka=$line["CostSborka"];
				$CostSborkaMdf="0"; if($line["CostSborkaMdf"]!=null) $CostSborkaMdf=$line["CostSborkaMdf"];
				$CostColor="0"; if($line["CostColor"]!=null) $CostColor=$line["CostColor"];
				$CostUpak="0"; if($line["CostUpak"]!=null) $CostUpak=$line["CostUpak"];
				$CostShpt="0"; if($line["CostShpt"]!=null) $CostShpt=$line["CostShpt"];
				
				$arDoors[$i]=array(
					$line['idOrder'],//0
					"NumPP"=>$line['NumPP'],
					"name"=>$line['name'],//1
					"H"=>$H,//2
					"W"=>$W,//3
					"S"=>$S,//4
					"Open"=>$line['Open'],//5
					"Nalichnik"=>$line['Nalichnik'],//6
					"Dovod"=>$line['Dovod'],//7
					"RAL"=>$line['RAL'],//8
					"Note"=>$line['Note'],//9
					"Markirovka"=>$line['Markirovka'],//10
					"Count"=>$line['Count'],//11
					"Shtild"=>$line['Shtild'],//12
					"id"=>$line["id"],//13
					//--Рабочая створка
					"WorkPetlya"=>$WorkPetlya,//14
					//Окно
					"WorkWindowCh"=>$WorkWindowCh,//15
					"WorkWindowNoFrame"=>$WorkWindowNoFrame,//16
					"WorkWindowH"=>$WorkWindowH,//17
					"WorkWindowW"=>$WorkWindowW,//18
					"WorkWindowGain"=>$WorkWindowGain,//19
					"WorkWindowGlass"=>$WorkWindowGlass,//20
					"WorkWindowGlassType"=>$line['WorkWindowGlassType'],//21
					"WorkWindowGrid"=>$WorkWindowGrid,//19
					//Окно 1
					"WorkWindowCh1"=>$WorkWindowCh1,//15
					"WorkWindowNoFrame1"=>$WorkWindowNoFrame1,//16
					"WorkWindowH1"=>$WorkWindowH1,//17
					"WorkWindowW1"=>$WorkWindowW1,//18
					"WorkWindowGain1"=>$WorkWindowGain1,//19
					"WorkWindowGlass1"=>$WorkWindowGlass1,//20
					"WorkWindowGlassType1"=>$line['WorkWindowGlassType1'],//21
					"WorkWindowGrid1"=>$WorkWindowGrid1,//19
					//Окно 2
					"WorkWindowCh2"=>$WorkWindowCh2,//15
					"WorkWindowNoFrame2"=>$WorkWindowNoFrame2,//16
					"WorkWindowH2"=>$WorkWindowH2,//17
					"WorkWindowW2"=>$WorkWindowW2,//18
					"WorkWindowGain2"=>$WorkWindowGain2,//19
					"WorkWindowGlass2"=>$WorkWindowGlass2,//20
					"WorkWindowGlassType2"=>$line['WorkWindowGlassType2'],//21
					"WorkWindowGrid2"=>$WorkWindowGrid2,//19
					//Вент рештка
					"WorkUpGridCh"=>$WorkUpGridCh,
					"WorkDownGridCh"=>$WorkDownGridCh,
					//--Вторая створка
					"StvorkaCh"=>$StvorkaCh,//22
					"StvorkaPetlya"=>$StvorkaPetlya,//23
					//Окно
					"StvorkaWindowCh"=>$StvorkaWindowCh,//24
					"StvorkaWindowNoFrame"=>$StvorkaWindowNoFrame,//25
					"StvorkaWindowH"=>$StvorkaWindowH,//26
					"StvorkaWindowW"=>$StvorkaWindowW,//27
					"StvorkaWindowGain"=>$StvorkaWindowGain,//29
					"StvorkaWindowGlass"=>$StvorkaWindowGlass,//30
					"StvorkaWindowGlassType"=>$line['StvorkaWindowGlassType'],//31
					"StvorkaWindowGrid"=>$StvorkaWindowGrid,//29
					//Окно 1
					"StvorkaWindowCh1"=>$StvorkaWindowCh1,//24
					"StvorkaWindowNoFrame1"=>$StvorkaWindowNoFrame1,//25
					"StvorkaWindowH1"=>$StvorkaWindowH1,//26
					"StvorkaWindowW1"=>$StvorkaWindowW1,//27
					"StvorkaWindowGain1"=>$StvorkaWindowGain1,//29
					"StvorkaWindowGlass1"=>$StvorkaWindowGlass1,//30
					"StvorkaWindowGlassType1"=>$line['StvorkaWindowGlassType1'],//31
					"StvorkaWindowGrid1"=>$StvorkaWindowGrid1,//29
					//Окно 2
					"StvorkaWindowCh2"=>$StvorkaWindowCh2,//24
					"StvorkaWindowNoFrame2"=>$StvorkaWindowNoFrame2,//25
					"StvorkaWindowH2"=>$StvorkaWindowH2,//26
					"StvorkaWindowW2"=>$StvorkaWindowW2,//27
					"StvorkaWindowGain2"=>$StvorkaWindowGain2,//29
					"StvorkaWindowGlass2"=>$StvorkaWindowGlass2,//30
					"StvorkaWindowGlassType2"=>$line['StvorkaWindowGlassType2'],//31
					"StvorkaWindowGrid2"=>$StvorkaWindowGrid2,//29
					//Вент решетка
					"StvorkaUpGridCh"=>$StvorkaUpGridCh,
					"StvorkaDownGridCh"=>$StvorkaDownGridCh,
					//--Фрамуга
					"FramugaCh"=>$FramugaCh,//32
					"FramugaH"=>$line["FramugaH"]==null ? "" : $line["FramugaH"],//33
					//Окно
					"FramugaWindowCh"=>$FramugaWindowCh,//34
					"FramugaWindowNoFrame"=>$FramugaWindowNoFrame,//35
					"FramugaWindowH"=>$FramugaWindowH,//36
					"FramugaWindowW"=>$FramugaWindowW,//37
					"FramugaWindowGain"=>$FramugaWindowGain,//38
					"FramugaWindowGlass"=>$FramugaWindowGlass,//39
					"FramugaWindowGlassType"=>$line['FramugaWindowGlassType'],//40
					"FramugaWindowGrid"=>$FramugaWindowGrid,//38
					//Вент рештки
					"FramugaUpGridCh"=>$FramugaUpGridCh,
					"FramugaDownGridCh"=>$FramugaDownGridCh,
					//Дополнительно
					"Antipanik"=>$Antipanik,
					"Otboynik"=>$Otboynik,
					"Wicket"=>$Wicket,
					"BoxLock"=>$BoxLock,
					"Otvetka"=>$Otvetka,
					"Isolation"=>$Isolation,
					//--Зарплата
					"CostLaser"=>$CostLaser,//41
					"CostSgibka"=>$CostSgibka,//42
					"CostSvarka"=>$CostSvarka,//43
					"CostFrame"=>$CostFrame,//44
					"CostMdf"=>$CostMdf,
					"CostSborka"=>$CostSborka,//45
					"CostSborkaMdf"=>$CostSborkaMdf,
					"CostColor"=>$CostColor,//46
					"CostUpak"=>$CostUpak,//47
					"CostShpt"=>$CostShpt,//48
					"NaryadCount"=>$line["NaryadCount"],//49
					"NaryadCompliteCount"=>$line["NaryadCompliteCount"],//50-45
					"NaryadShptCount"=>$line["NaryadShptCount"]
				);
				$i++;
			};
			$result->close();
			$result=$m->query("SELECT * , DATE_FORMAT(BlankDate,'%d.%m.%Y') as bd , DATE_FORMAT(ShetDate,'%d.%m.%Y') as sd  FROM oreders WHERE id=".$_POST['id']);
			$line = $result->fetch_assoc();
			
			$json_data = array (
				"DialogGUID"=>getGUID(),
				'Blank'=>$line['Blank'],
				'BlankDate'=>$line['bd'],
				'Shet'=>$line['Shet'],
				'ShetDate'=>$line['sd'],
				'Dostavka'=>$line['Dostavka'],
				'Montag'=>$line['Montag'],
				'Srok'=>$line['Srok'],
				'Zakaz'=>$line['Zakaz'],
				'Contact'=>$line['Contact'],
				'NoteZakaz'=>$line['Note'],
				"Status"=>$line["status"],
				"Manager"=>$line["Manager"],
				'Status'=>$line['status'],
				"UserBlock"=>$line["UserBlock"],
				"Doors"=>$arDoors
				);
				$result->close();
			echo json_encode($json_data);
			//Поставим блокировку на редактировании
			if($_SESSION["AutorizeType"]!=4)
				$m->query("UPDATE oreders SET UserBlock='".$_SESSION["AutorizeFIO"]."' WHERE id=".$_POST['id']);
		break;
		
		case 'OrderEdit':
			$er="ok";
			
			//Для начала делаем проверку не находится заказ в статусе на выполнении
			$d=$m->query("SELECT status FROM oreders WHERE (status=1 OR status=2 OR status=3) AND id=".$_POST['idOrder']) or die ($er="Ошибка выполнения SQL запроса<br>");
			if($d->num_rows==0)
			{
				$d->close();
				$m->query("START TRANSACTION") or die($er=$er."Ошибка начала транзакции<br>");
				
				$m->query('DELETE FROM orderdoors WHERE idOrder='.$_POST['idOrder']) or die ($er=$er."Ошибка удаления дверей из таблицы<br>");
				$i=1;
				if(isset($_POST['OrderDialogTableTDNameArr']))
					while($i<count($_POST['OrderDialogTableTDNameArr']))
						if(isset( $_POST['OrderDialogTableTDNameArr'][$i]))
						{
							$S="NULL";
							if($_POST['OrderDialogTableTDSArr'][$i]!="") $S=$_POST['OrderDialogTableTDSArr'][$i];
							$m->query('INSERT INTO orderDoors (idOrder, NumPP, name, H, W, S, Open, Nalichnik, Dovod, RAL , Note, Markirovka, Count, Shtild, '.
								'WorkPetlya, WorkWindowCh, WorkWindowNoFrame, WorkWindowH, WorkWindowW, WorkWindowGrid, WorkWindowGlass, WorkWindowGlassType, StvorkaCh, StvorkaPetlya, StvorkaWindowCh, StvorkaWindowNoFrame, StvorkaWindowH, StvorkaWindowW, StvorkaWindowGrid, StvorkaWindowGlass, StvorkaWindowGlassType, FramugaCh, FramugaWindowCh, FramugaWindowNoFrame, FramugaWindowH, FramugaWindowW, FramugaWindowGrid, FramugaWindowGlass, FramugaWindowGlassType, '.
								'CostLaser, CostSgibka, CostSvarka, CostFrame, CostSborka, CostColor, CostUpak, CostShpt'.
								' ) values('.$_POST['idOrder']." , ".
								" ".$i." , ".
								" '".$_POST['OrderDialogTableTDNameArr'][$i]."' , ".
								$_POST['OrderDialogTableTDHArr'][$i]." , ".
								$_POST['OrderDialogTableTDWArr'][$i]." , ".
								$S." , '".
								$_POST['OrderDialogTableTDOpenArr'][$i]."' , '".
								$_POST['OrderDialogTableTDNalichnikArr'][$i]."' , '".
								$_POST['OrderDialogTableTDDovodArr'][$i]."' , '".
								$_POST['OrderDialogTableTDRALArr'][$i]."' , '".
								$_POST['OrderDialogTableTDNoteArr'][$i]."' , '".
								$_POST['OrderDialogTableTDMarkirovkaArr'][$i]."' , ".
								$_POST['OrderDialogTableTDCountArr'][$i]." , '".
								$_POST['OrderDialogTableTDShtildArr'][$i]."', ".
								//Рабочая створка
								$_POST['OrderDialogTableTDConstructWorkPetlya'][$i].", ".
								$_POST['OrderDialogTableTDConstructWorkWindowCh'][$i].", ".
								$_POST['OrderDialogTableTDConstructWorkWorkWindowNoFrame'][$i].", ".
								$_POST['OrderDialogTableTDConstructWorkWindowH'][$i].", ".
								$_POST['OrderDialogTableTDConstructWorkWindowW'][$i].", ".
								$_POST['OrderDialogTableTDConstructWorkWindowGrid'][$i].", ".
								$_POST['OrderDialogTableTDConstructWorkWindowGlass'][$i].", ".
								" '".$_POST['OrderDialogTableTDConstructWorkWindowGlassType'][$i]."', ".
								//Вторая створка
								$_POST['OrderDialogTableTDConstructStvorkaCh'][$i].", ".
								$_POST['OrderDialogTableTDConstructStvorkaPetlya'][$i].", ".
								$_POST['OrderDialogTableTDConstructStvorkaWindowCh'][$i].", ".
								$_POST['OrderDialogTableTDConstructStvorkaWindowNoFrame'][$i].", ".
								$_POST['OrderDialogTableTDConstructStvorkaWindowH'][$i].", ".
								$_POST['OrderDialogTableTDConstructStvorkaWindowW'][$i].", ".
								$_POST['OrderDialogTableTDConstructStvorkaWindowGrid'][$i].", ".
								$_POST['OrderDialogTableTDConstructStvorkaWindowGlass'][$i].", ".
								" '".$_POST['OrderDialogTableTDConstructStvorkaWindowGlassType'][$i]."', ".
								//Фрамуга
								$_POST['OrderDialogTableTDConstructFramugaCh'][$i].", ".
								$_POST['OrderDialogTableTDConstructFramugaWindowCh'][$i].", ".
								$_POST['OrderDialogTableTDConstructFramugaWindowNoFrame'][$i].", ".
								$_POST['OrderDialogTableTDConstructFramugaWindowH'][$i].", ".
								$_POST['OrderDialogTableTDConstructFramugaWindowW'][$i].", ".
								$_POST['OrderDialogTableTDConstructFramugaWindowGrid'][$i].", ".
								$_POST['OrderDialogTableTDConstructFramugaWindowGlass'][$i].", ".
								" '".$_POST['OrderDialogTableTDConstructFramugaWindowGlassType'][$i]."', ".
								//Зарплата
								" '".$_POST['CostLaser'][$i]."', ".
								" '".$_POST['CostSgibka'][$i]."', ".
								" '".$_POST['CostSvarka'][$i]."', ".
								" '".$_POST['CostFrame'][$i]."', ".
								" '".$_POST['CostSborka'][$i]."', ".
								" '".$_POST['CostColor'][$i]."', ".
								" '".$_POST['CostUpak'][$i]."', ".
								" '".$_POST['CostShpt'][$i]."' ".
							")") or die($er=$er."Ошибка добавления измененных дверей<br>");
						$i++;
						};
				$m->query('UPDATE oreders SET  '.
					"Blank='".$_POST["Blank"]."' , ".
					"BlankDate=STR_TO_DATE('".$_POST['BlankDate']."', '%d.%m.%Y')  , ".
					"Shet='".$_POST["Shet"]."' , ".
					"ShetDate=STR_TO_DATE('".$_POST['ShetDate']."', '%d.%m.%Y')  , ".
					"Srok='".$_POST["Srok"]."' , ".
					"Zakaz='".$_POST["Zakaz"]."' , ".
					"Contact='".$_POST["Contact"]."' , ".
					"Note='".$_POST["Note"]."' , ".
					"status=".$_POST["status"]." ".
					'WHERE id='.$_POST['idOrder']) or die($er=$er."Ошибка изменения данных в таблице заказа<br>");
				
				if($er=="ok")
					{ $m->query("COMMIT") or die ($er=$er."Ошибка применения транзакции<br>");}
					else $m->query("ROLLBACK") or die ($er=$er."Ошибка отката транзакции<br>");
			}
			else
			{
				//$er="Нельзя редактировать заказ со статусами: НА ВЫПОЛНЕНИИ, ВЫПОЛНЕН";
				$m->query('UPDATE oreders SET  '.
				"status=".$_POST["status"]." ".
				'WHERE id='.$_POST['idOrder']) or die($er=$er."Ошибка изменения данных в таблице заказа<br>");
			};
			echo $er;
		break;
		
		case 'Delete':
			$er="ok";
			//Определяем не находится заказ на стадии выполнения или выполненн
			$d=$m->query("SELECT status FROM oreders WHERE status=0 AND id=".$_POST["id"]);
			if($d->num_rows==1)
			{
				$d->close();
				$m->query("START TRANSACTION") or die ($er=$er."Ошибка начала транзакции, ");
				$m->query("INSERT INTO trashorders (Blank, BlankDate, Shet, ShetDate, Dostavka, Montag, Srok, Zakaz, Contact, Note) SELECT o.Blank, o.BlankDate, o.Shet, o.ShetDate, o.Dostavka, o.Montag, o.Srok, o.Contact, o.Zakaz, o.Note FROM oreders o WHERE id=".$_POST["id"]) or die ($er=$er."Ошибка переноса заказа, ");
				$d=$m->query("SELECT MAX(id) as num FROM trashorders");
				$r=$d->fetch_assoc();
				$max=$r["num"];
				$d->close();
				$m->query("INSERT INTO trashorderdoors (idOrder, name, H, W, S, Open, Nalichnik, Dovod, RAL, Note, Markirovka, Count, Shtild) SELECT ".$max.", o.name, o.W, o.H, o.S, o.Open, o.Nalichnik, o.Dovod, o.RAL, o.Note, o.Markirovka, o.Count, o.Shtild FROM orderdoors o WHERE o.idOrder=".$_POST["id"]) or die ($er=$er."Ошибка переноса дверей, ");
				$m->query("DELETE FROM oreders WHERE id=".$_POST["id"]) or die ($er=$er."Ошибка удаление из аблицы заказа, ");
				$m->query("DELETE FROM orderdoors WHERE idOrder=".$_POST["id"]) or die ($er=$er."Ошибка удаление из аблицы двери, ");
			}
			else $er="Нельзя удалить со статусом: выполняется или выполненн";
			if($er=="ok")
					{ $m->query("COMMIT") or die ($er=$er."Ошибка применения транзакции<br>");}
					else $m->query("ROLLBACK") or die ($er=$er."Ошибка отката транзакции<br>");
			echo $er;
		break;
		
		case "OderCopy":
			$er="";
			$m->autocommit(FALSE);
			$m->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
			
			try
			{
				$m->query(
					"INSERT oreders (Blank, BlankDate, Shet, ShetDate, Dostavka, Srok, Montag, Zakaz, Contact, Note, Manager, status) ".
					"SELECT (SELECT MAX(o1.Blank)+1 FROM oreders o1), o.BlankDate, o.Shet, o.ShetDate, o.Dostavka, o.Srok, o.Montag, o.Zakaz, o.Contact, o.Note, '".$_SESSION["AutorizeFIO"]."', 0 FROM oreders o WHERE id=".$_POST["idOld"]
				) or die ($er=$er." ".mysqli_error($m));
				$NewIdOrder=$m->insert_id;
				$m->query(
					"INSERT INTO orderdoors (".
						"idOrder, NumPP, name, H, W, S, Open, Nalichnik, Dovod, Note, RAL, Markirovka, Count, Shtild, Petlya, PetlyaCount, Zamok, Cilindr, Hand, Window, ".
						"WorkPetlya, WorkWindowCh, WorkWindowNoFrame, WorkWindowH, WorkWindowW, WorkWindowGrid, WorkWindowGlass, WorkWindowGlassType, ".
						"StvorkaCh, StvorkaPetlya, StvorkaWindowCh, StvorkaWindowNoFrame, StvorkaWindowH, StvorkaWindowW, StvorkaWindowGrid, StvorkaWindowGlass, StvorkaWindowGlassType, ".
						"FramugaCh, FramugaWindowCh, FramugaWindowNoFrame, FramugaWindowH, FramugaWindowW, FramugaWindowGrid, FramugaWindowGlass, FramugaWindowGlassType ".
					") ".
					"SELECT ".
						$NewIdOrder.", o.NumPP, o.name, o.H, o.W, o.S, o.Open, o.Nalichnik, o.Dovod, o.Note, o.RAL, o.Markirovka, o.Count, o.Shtild, o.Petlya, o.PetlyaCount, o.Zamok, o.Cilindr, o.Hand, o.Window, ".
						"WorkPetlya, WorkWindowCh, WorkWindowNoFrame, WorkWindowH, WorkWindowW, WorkWindowGrid, WorkWindowGlass, WorkWindowGlassType, ".
						"StvorkaCh, StvorkaPetlya, StvorkaWindowCh, StvorkaWindowNoFrame, StvorkaWindowH, StvorkaWindowW, StvorkaWindowGrid, StvorkaWindowGlass, StvorkaWindowGlassType, ".
						"FramugaCh, FramugaWindowCh, FramugaWindowNoFrame, FramugaWindowH, FramugaWindowW, FramugaWindowGrid, FramugaWindowGlass, FramugaWindowGlassType ".
					"FROM orderdoors o WHERE o.idOrder=".$_POST["idOld"]
				) or die ($er=$er." ".mysqli_error($m));
			}
			catch (mysqli_sql_exception $e) {$er=$er." ".$er->errorMessage();};
			if($er=="")
			{
				$m->commit();
				$d=$m->query("SELECT Blank FROM oreders WHERE id=".$NewIdOrder);
				$BlankNew="";
				if($d->num_rows>0)
				{
					$r=$d->fetch_assoc();
					$BlankNew=$r["Blank"];
				};
				echo "Новый заказ имеет номер:".$BlankNew;
			}
			else
			{
				echo "При копировании возникли ошибки: ".$er; $m->rollback();
			};
		break;
		
		case 'OrdersPrint':
			include("../mpdf53/mpdf.php");		
			$mpdf = new mPDF('utf-8', 'A4', '8', '', 10, 10, 7, 7, 10, 10); /*задаем формат, отступы и.т.д.*/
			$mpdf->charset_in = 'UTF8';
			$mpdf->list_indent_first_level = 0; 
			$html='<table>'.$_POST['Table'].'</table>';
			$mpdf->WriteHTML($html, 2); /*формируем pdf*/
			$mpdf->AddPage();
			$mpdf->Output('OrdersList.pdf' , 'F');
		break;
		
		//Печать маркировки
		case 'OrdersPrintMarkirovka':
			include("../mpdf53/mpdf.php");		
			$SizePage=$XMLParams->Orders->Markirovka->TypePrint=="StandartA4" ? "A4" : array($XMLParams->Orders->Markirovka->SizeW, $XMLParams->Orders->Markirovka->SizeH);
			$mpdf = new mPDF('utf-8', $SizePage, '8', '', 5, 5, 5, 2, 5, 5); /*задаем формат, отступы и.т.д.*/
			$mpdf->charset_in = 'UTF8';
			$mpdf->list_indent_first_level = 0; 

			$aDoorPos=$_POST["aDoorPos"];
			$aName=$_POST["aName"];
			$aH=$_POST["aH"];
			$aW=$_POST["aW"];
			$aS=$_POST["aS"];
			$aOpen=$_POST["aOpen"];
			$aMarkirovka=$_POST["aMarkirovka"];
			$aRAL=$_POST["aRAL"];
			$aShtild=$_POST["aShtild"];
			$aCount=$_POST["aCount"];

			$i=0; $html=""; $p=1; $NumInOrder=1;
			while(isset($aName[$i]))
			{
				for($NumPP=1;$NumPP<=$aCount[$i];$NumPP++)
				{
					$Shtild=$aShtild[$i];
					if(is_numeric($Shtild))
					{
						$ShtildS=strval((int)$Shtild+$NumPP-1);
						//Теперь добавим знак 0 в начале строки если не хватает
						while(strlen($ShtildS)<strlen(strval($aShtild[$i])))
							$ShtildS="0".$ShtildS;
					};
					$Num=$_POST["OrderShet"]."/".$aDoorPos[$i]."/";
					switch($XMLParams->Orders->Markirovka->TypePrint)
					{
						case "Special":
							$mpdf->AddPage();
							$Pg="<p style='font-size:11.5pt; font-weight:bold'>Поставка от ".$_POST["OrderZakaz"].
								"<br>Наряд: ".$Num.$NumPP." № ".(string)($NumInOrder++)."<br>"."<br>Счет ".$_POST["OrderShet"]." от ".$_POST["OrderShetDate"].
								//"<br>Марка: ".$aName[$i].";".
								"<br>Открывание: ".$aOpen[$i].
								"<br>Размеры: ".$aH[$i]." x ".$aW[$i].( ($aS[$i]!="") ? " x ".$aS[$i] : "").
								"<br>Цвет: ".$aRAL[$i].
								($aShtild[$i]!=""?"Шильда: ".$ShtildS."<br>" : "").
								"<br>Марк.: ".$aMarkirovka[$i]."</p>";
							$mpdf->WriteHTML($Pg, 2);
						break;
						case 'StandartA4':
							$Pg="<table style='border:solid 2px black; width:100%; margin:20px; font-size:24pt;'><tr><td>".
								"Тип: ".$aName[$i]."<br>".
								"Размер: ".$aH[$i]." x ".$aW[$i].( ($aS[$i]!="") ? " x ".$aS[$i] : "")."<br>".
								"Открывание: ".$aOpen[$i]."<br>".
								"<span style='font-size:28pt;'> Марк.: ".$aMarkirovka[$i]."</span>".
								"</td><td style='border:solid 1px black'><b>".$_POST["OrderBlank"]."<br>".$aDoorPos[$i]."</b></td></tr></table>";
							$mpdf->WriteHTML($Pg, 2);
							$p++;
							if($p>5 )
							{
								$mpdf->AddPage();
								$html="";
								$p=1;
							};
							break;
					};
				};
				$i++;
			};
			$mpdf->Output('Markirovka.pdf' , 'F');
			echo "ok";
		break;
		
		case "SetNoSetStatusCancel":
			$er=true;
			switch($_POST["Operation"])
			{
				case "Set":
					$m->query("UPDATE oreders SET status=-1 WHERE id=".$_POST["id"]) or die ($er="Ошибка установки статуса");
				break;
				case "NoSet":
					//Опрежделим статус до установки значения отменен
					$idOrder=$_POST["id"];
					//Определим количество возможных нарядов в пределах заказа
					//$d=$m->query("SELECT SUM(o1.Count) FROM oreders o, orderdoors o1 WHERE o.id=o1.idOrder AND o.id=".$idOrder)  or die ($er=$er."Ошибка:".mysqli_error());;
					$d=$m->query("SELECT count(*) FROM naryad n, orderdoors o WHERE n.idDoors=o.id AND o.idOrder=".$idOrder)  or die ($er=$er."Ошибка:".mysqli_error());;
					$NaryadMaxCount=$d->fetch_row()[0];
					$d->close();
					if($NaryadMaxCount==0)
					{
						$m->query("UPDATE oreders SET status=0 WHERE id=".$idOrder)  or die ($er=$er."Ошибка:".mysqli_error());
					}
					else
					{
						$m->query("UPDATE oreders SET status=1 WHERE id=".$idOrder)  or die ($er=$er."Ошибка:".mysqli_error());
						//Определяем кол-во упакованных нарядов
						$d=$m->query("SELECT COUNT(*) FROM oreders o, orderdoors o1, naryad n WHERE o.id=o1.idOrder AND o1.id=n.idDoors AND n.UpakCompliteWork IS NOT NULL AND o.id=".$idOrder)  or die ($er=$er."Ошибка:".mysqli_error());
						$NaryadUpakCount=$d->fetch_row()[0];
						$d->close();
						if((int) $NaryadMaxCount==(int)$NaryadUpakCount)
							$m->query("UPDATE oreders SET status=2 WHERE id=".$idOrder)  or die ($er=$er."Ошибка:".mysqli_error());
						//Определяем кол-во отгруженных нарядов
						$d=$m->query("SELECT COUNT(*) FROM oreders o, orderdoors o1, naryad n WHERE o.id=o1.idOrder AND o1.id=n.idDoors AND n.ShptComplite IS NOT NULL AND o.id=".$idOrder)  or die ($er=$er."Ошибка:".mysqli_error());
						$NaryadUpakCount=$d->fetch_row()[0];
						$d->close();
						if((int) $NaryadMaxCount==(int)$NaryadUpakCount)
							$m->query("UPDATE oreders SET status=3 WHERE id=".$idOrder)  or die ($er=$er."Ошибка:".mysqli_error());
					};
				break;
			};
			echo $er;
		break;
		
		case 'PDF':
			include("../mpdf53/mpdf.php");		
			$mpdf = new mPDF('utf-8', 'A4-L', '8', '', 10, 10, 7, 7, 10, 10); /*задаем формат, отступы и.т.д.*/
			$mpdf->charset_in = 'UTF8';
			$mpdf->list_indent_first_level = 0; 
			
			$html='<h3 style="text-align:center">Заказ № '.$_POST['Blank'].' от '.$_POST['BlankDate'].' Счет № '.$_POST['Shet'].' от '.$_POST['ShetDate'];
			$html=$html.'<table border=1 cellspacing=0 cellpading=0 style="text-align:center;">';
			$html=$html.'<tr style="background-color:#b5ffbc;">'.
				'<td rowspan=2>№</td>'.
				'<td rowspan=2>Наименование</td>'.
				'<td colspan=2>Размер по коробке</td>'.
				'<td rowspan=2>Размер раб. ств.</td>'.
				'<td rowspan=2>Открывание</td>'.
				'<td rowspan=2>Наличник</td>'.
				'<td rowspan=2>Доводчик</td>'.
				'<td rowspan=2>RAL окрас</td>'.
				'<td rowspan=2>Примечание</td>'.
				'<td rowspan=2>Маркировка</td>'.
				'<td rowspan=2>Кол-во</td>'.
				'<td rowspan=2>Шильда</td>'.
				'<td rowspan=2></td>'.
			'</tr>'.
			'<tr style="background-color:#b5ffbc;">'.
				'<td>Высота</td>'.
				'<td>Ширина</td>'.
			'</tr>';
			
			$i=0; $CountSum=0;
			if(isset($_POST['OrderDialogTableTDNameArr']))
			while($i<count($_POST['OrderDialogTableTDNameArr']))
				if(isset( $_POST['OrderDialogTableTDNameArr'][$i]))
				{
					$html=$html.'<tr>'.
					'<td>'.$_POST['OrderDialogTableTDNumArr'][$i].'</td>'.
					'<td>'.$_POST['OrderDialogTableTDNameArr'][$i]."</td>".
					'<td>'.$_POST['OrderDialogTableTDHArr'][$i].'</td>'.
					'<td>'.$_POST['OrderDialogTableTDWArr'][$i].'</td>'.
					'<td>'.$_POST['OrderDialogTableTDSArr'][$i].'</td>'.
					'<td>'.$_POST['OrderDialogTableTDOpenArr'][$i].'</td>'.
					'<td>'.$_POST['OrderDialogTableTDNalichnikArr'][$i].'</td>'.
					'<td>'.$_POST['OrderDialogTableTDDovodArr'][$i].'</td>'.
					'<td>'.$_POST['OrderDialogTableTDRALArr'][$i].'</td>'.
					'<td>'.$_POST['OrderDialogTableTDNoteArr'][$i].'</td>'.
					'<td>'.$_POST['OrderDialogTableTDMarkirovkaArr'][$i]."</td>".
					'<td>'.$_POST['OrderDialogTableTDCountArr'][$i].'</td>'.
					'<td>'.$_POST['OrderDialogTableTDShtildArr'][$i]."</td> ".
					'</tr>';
					$CountSum+=(int)$_POST['OrderDialogTableTDCountArr'][$i];
					$i++;
				};
			$html=$html."<b><tr><td colspan=11>Итого</td><td>".$CountSum."</td><td></td></tr></b>";
			$html=$html.'</table>';
			$html=$html.'<table>'.
			'<tr><td>Срок изготовления</td><td>'.$_POST['Srok'].'</td></tr>'.
			'<tr><td>Заказчик</td><td>'.$_POST['Zakaz'].'</td></tr>'.
			'<tr><td>Контакты</td><td>'.$_POST['Contact'].'</td></tr>'.
			'<tr><td>Ответственный</td><td>'.$_POST['Manager'].'</td></tr>'.
			'<tr><td colspan=2>Примечание</td></tr>'.
			'<tr><td colspan=2>'.$_POST['Note'].'</td></tr>'.
			'</table>';
			
			$mpdf->WriteHTML($html, 2); /*формируем pdf*/
			$mpdf->Output('Zakaz'.$_POST['Blank'].'.pdf' , 'F');
		break;
		
		case 'selectTrash':				
			$result=$m->query("SELECT o.* , DATE_FORMAT(o.BlankDate,'%d.%m.%Y') as bd , DATE_FORMAT(o.ShetDate,'%d.%m.%Y') as sd  FROM trashorders AS o ORDER BY o.Blank");
			$i=0;
			$a=array();
			while ($line = $result->fetch_assoc())
			{
				$color='white';
				$a[$i]=array(
					"id"=>$line['id'],
					"Blank"=>$line['Blank'],
					"BlankDate"=>$line['bd'],
					"Shet"=>$line['Shet'],
					"ShetDate"=>$line['sd'],
					"Srok"=>$line['Srok'],
					"Zakaz"=>$line['Zakaz'],
					"Contact"=>$line['Contact']
				);
				$i++;
			}
			$result->close();
			echo json_encode($a);
		break;
		
		case 'deleteTrash':
			$er="ok";
			$m->query("START TRANSACTION") or die ($er=$er."Ошибка начала транзакции, ");
			$m->query('DELETE FROM trashorderdoors WHERE idOrder='.$_POST['id']) or die ($er=$er."Ошибка удаления заказа");
			$m->query('DELETE FROM trashorders WHERE id='.$_POST['id']) or die ($er=$er."Ошибка удаления списка дверей");
			if($er=="ok")
				{ $m->query("COMMIT") or die ($er=$er."Ошибка применения транзакции<br>");}
				else $m->query("ROLLBACK") or die ($er=$er."Ошибка отката транзакции<br>");
			echo $er;
		break;
		
		case 'recoveryTrash':
			$er="ok";
				$m->query("START TRANSACTION") or die ($er=$er."Ошибка начала транзакции, ");
				//Определяем макс номер заказа в таблице oreders
				$d=$m->query("SELECT MAX(Blank)+1 as BlankNew FROM oreders");
				$r=$d->fetch_assoc();
				$BlankNew=$r["BlankNew"];
				$d->close();
				
				$m->query("INSERT INTO oreders (Blank, BlankDate, Shet, ShetDate, Dostavka, Montag, Srok, Zakaz, Contact, Note, status) SELECT ".$BlankNew.", o.BlankDate, o.Shet, o.ShetDate, o.Dostavka, o.Montag, o.Srok, o.Contact, o.Zakaz, o.Note, 0 FROM trashorders o WHERE id=".$_POST["id"]) or die ($er=$er."Ошибка переноса заказа, ");
				$d=$m->query("SELECT MAX(id) as num FROM oreders");
				$r=$d->fetch_assoc();
				$max=$r["num"];
				$d->close();
				$m->query("INSERT INTO orderdoors (idOrder, name, H, W, S, Open, Nalichnik, Dovod, RAL, Note, Markirovka, Count, Shtild) SELECT ".$max.", o.name, o.W, o.H, o.S, o.Open, o.Nalichnik, o.Dovod, o.RAL, o.Note, o.Markirovka, o.Count, o.Shtild FROM trashorderdoors o WHERE o.idOrder=".$_POST["id"]) or die ($er=$er."Ошибка переноса дверей, ");
				$m->query("DELETE FROM trashorders WHERE id=".$_POST["id"]) or die ($er=$er."Ошибка удаление из аблицы заказа, ");
				$m->query("DELETE FROM trashorderdoors WHERE idOrder=".$_POST["id"]) or die ($er=$er."Ошибка удаление из аблицы двери, ");
			
			if($er=="ok")
					{ $m->query("COMMIT") or die ($er=$er."Ошибка применения транзакции<br>");}
					else $m->query("ROLLBACK") or die ($er=$er."Ошибка отката транзакции<br>");
			if($er=="ok") $er="Заказ восстановлен под номером: ".$BlankNew;
			echo $er;
		break;
		//Стадии выолнения двери в процентах
		case "OrderDoorProcessing":
			//Опеределяем всего дверей
			$d=$m->query("SELECT o.Count as DoorCount FROM orderdoors o WHERE id=".$_POST["idDoor"]);
			$r=$d->fetch_assoc();
			$DoorCount=$r["DoorCount"];
			$d->close();
			//Выполненно лазером
			$d=$m->query("SELECT COUNT(*) as CountLaser FROM naryad n, orderdoors o WHERE n.idDoors=o.id AND n.LaserCompliteFlag=1 AND o.id=".$_POST["idDoor"]);
			$r=$d->fetch_assoc();
			$CountLaser=$r["CountLaser"];
			$CountLaserPersent=round($r["CountLaser"]/$DoorCount*100);
			$d->close();
			//Выполненно Сгибкой
			$d=$m->query("SELECT COUNT(*) as CountSgibka FROM naryad n, orderdoors o WHERE n.idDoors=o.id AND n.SgibkaCompliteFlag=1 AND o.id=".$_POST["idDoor"]);
			$r=$d->fetch_assoc();
			$CountSgibka=$r["CountSgibka"];
			$CountSgibkaPersent=round($r["CountSgibka"]/$DoorCount*100);
			$d->close();
			//Выполненно Сваркой
			$d=$m->query("SELECT COUNT(*) as CountSvarka FROM naryad n, orderdoors o WHERE n.idDoors=o.id AND n.SvarkaCompliteFlag=1 AND o.id=".$_POST["idDoor"]);
			$r=$d->fetch_assoc();
			$CountSvarka=$r["CountSvarka"];
			$CountSvarkaPersent=round($r["CountSvarka"]/$DoorCount*100);
			$d->close();
			//Выполненно МДФ
			$d=$m->query("SELECT COUNT(*) as CountMdf FROM naryad n, orderdoors o WHERE n.idDoors=o.id AND n.MdfCompliteFlag=1 AND o.id=".$_POST["idDoor"]);
			$r=$d->fetch_assoc();
			$CountMdf=$r["CountMdf"];
			$CountMdfPersent=round($r["CountMdf"]/$DoorCount*100);
			$d->close();
			//Выполненно Сборкой
			$d=$m->query("SELECT COUNT(*) as CountSborka FROM naryad n, orderdoors o WHERE n.idDoors=o.id AND n.SborkaCompliteFlag=1 AND o.id=".$_POST["idDoor"]);
			$r=$d->fetch_assoc();
			$CountSborka=$r["CountSborka"];
			$CountSborkaPersent=round($r["CountSborka"]/$DoorCount*100);
			$d->close();
			//Выполненно Покраской
			$d=$m->query("SELECT COUNT(*) as CountColor FROM naryad n, orderdoors o WHERE n.idDoors=o.id AND n.ColorCompliteFlag=1 AND o.id=".$_POST["idDoor"]);
			$r=$d->fetch_assoc();
			$CountColor=$r["CountColor"];
			$CountColorPersent=round($r["CountColor"]/$DoorCount*100);
			$d->close();
			//Выполненно Сборкой МДФ
			$d=$m->query("SELECT COUNT(*) as CountSborkaMdf FROM naryad n, orderdoors o WHERE n.idDoors=o.id AND n.SborkaMdfCompliteFlag=1 AND o.id=".$_POST["idDoor"]);
			$r=$d->fetch_assoc();
			$CountSborkaMdf=$r["CountSborkaMdf"];
			$CountSborkaMdfPersent=round($r["CountSborkaMdf"]/$DoorCount*100);
			$d->close();
			//Выполненно Упаковкой
			$d=$m->query("SELECT COUNT(*) as CountUpak FROM naryad n, orderdoors o WHERE n.idDoors=o.id AND n.UpakCompliteFlag=1 AND o.id=".$_POST["idDoor"]);
			$r=$d->fetch_assoc();
			$CountUpak=$r["CountUpak"];
			$CountUpakPersent=round($r["CountUpak"]/$DoorCount*100);
			$d->close();
			
			//Формируем массив
			$a=array(
				"CountLaserPersent"=>$CountLaserPersent,
				"CountLaser"=>$CountLaser,
				"CountSgibkaPersent"=>$CountSgibkaPersent,
				"CountSgibka"=>$CountSgibka,
				"CountSvarkaPersent"=>$CountSvarkaPersent,
				"CountSvarka"=>$CountSvarka,
				"CountMdfPersent"=>$CountMdfPersent,
				"CountMdf"=>$CountMdf,
				"CountSborkaPersent"=>$CountSborkaPersent,
				"CountSborka"=>$CountSborka,
				"CountColorPersent"=>$CountColorPersent,
				"CountColor"=>$CountColor,
				"CountSborkaMdfPersent"=>$CountSborkaMdfPersent,
				"CountSborkaMdf"=>$CountSborkaMdf,
				"CountUpakPersent"=>$CountUpakPersent,
				"CountUpak"=>$CountUpak
			);
			
			echo json_encode($a);
		break;
		//Определение максимального номера Шильды
		case "SelectShtildMax":
			$MaxNum=1;
			$MaxNumStr="1";
			$MaxNumLen=0;
			//Вычисляем маскимальный предыдущий номер
			$d=$m->query("SELECT MAX(shtild) as m, o.Count FROM orderdoors o WHERE Shtild REGEXP '^-?[0-9]+$'");
			if($d->num_rows>0)
			{
				$r=$d->fetch_assoc();
				if($r["m"]!=null) $MaxNum=(int)$r["m"];
				$MaxNumLen=strlen($r["m"]);
				$MaxNumStr=$r["m"];
			};
			$d->close();
			//Для максимального предыдущего номера узнаем количество и суммируем
			$d=$m->query("SELECT o.Count FROM orderdoors o WHERE o.Shtild='".$MaxNumStr."' ");
			if($d->num_rows>0)
			{
				$r=$d->fetch_assoc();
				$MaxNum+=(int)$r["Count"];
			};
			$d->close();
			$MaxNumStr=(string) $MaxNum;
			//Допоисываем 0 в начале
			while($MaxNumLen>strlen($MaxNumStr))
				$MaxNumStr="0".$MaxNumStr;
			
			echo $MaxNumStr;
		break;
		
		//-------------------------Изменение стоимости выполненных работ-------------------------------
		case "EditWorkSumLoad":
			$d=$m->query("SELECT o.id, o.NumPP, o.name, o.H, o.W, o.S, o.SEqual, o.Count, o.WorkWindowCh, o.WorkWindowNoFrame, o.StvorkaWindowCh, o.StvorkaWindowNoFrame, o.FramugaWindowCh, o.FramugaWindowNoFrame, o.CostLaser, o.CostSgibka, o.CostFrame, o.CostMdf, o.CostSvarka, o.CostSborka, o.CostSborkaMdf, o.CostUpak, o.CostColor, o.CostShpt FROM orderdoors o, oreders o1 WHERE o1.id=o.idOrder AND ".($XMLParams->Global->ViewNumOrder=="Blank"? "o1.Blank=".$_POST["Blank"] : "o1.Shet='".$_POST["Blank"]."'"));
			$a=array(); $i=0;
			while($r=$d->fetch_assoc())
			{
				$a[$i]=array(
					"id"=>$r["id"],
					"NumPP"=>$r["NumPP"],
					"name"=>$r["name"],
					"Size"=>$r["H"]." x ".$r["W"].($r["S"]!=null || $r["SEqual"]==1? ($r["S"]!=null? " x ".$r["S"] : " x Равн.") : ""),
					"Count"=>$r["Count"],
					"FrameComplite"=>(($r["WorkWindowCh"]==1 & $r["WorkWindowNoFrame"]==0) || ($r["StvorkaWindowCh"]==1 & $r["StvorkaWindowNoFrame"]==0) || ($r["FramugaWindowCh"]==1 & $r["FramugaWindowNoFrame"]==0))? 1:0, 
					"CostLaser"=>$r["CostLaser"],
					"CostSgibka"=>$r["CostSgibka"],
					"CostFrame"=>$r["CostFrame"],
					"CostMdf"=>$r["CostMdf"],
					"CostSvarka"=>$r["CostSvarka"],
					"CostSborka"=>$r["CostSborka"],
					"CostSborkaMdf"=>$r["CostSborkaMdf"],
					"CostColor"=>$r["CostColor"],
					"CostUpak"=>$r["CostUpak"],
					"CostShpt"=>$r["CostShpt"],
					"CostAll"=>$r["CostLaser"]+$r["CostSgibka"]+$r["CostFrame"]+$r["CostMdf"]+$r["CostSvarka"]+$r["CostSborka"]+$r["CostSborkaMdf"]+$r["CostColor"]+$r["CostUpak"]+$r["CostShpt"],
					"CostAllOnCount"=>($r["CostLaser"]+$r["CostSgibka"]+$r["CostFrame"]+$r["CostMdf"]+$r["CostSvarka"]+$r["CostSborka"]+$r["CostSborkaMdf"]+$r["CostColor"]+$r["CostUpak"]+$r["CostShpt"])*$r["Count"]
				);
				$i++;
			};
			echo json_encode($a);
		break;
		case "EditWorkSumCompite":
			$idDoor=$_POST["idDoor"];
			$CostLaserOld=$_POST["CostLaserOld"];
			$CostLaser=$_POST["CostLaser"];
			$CostSgibkaOld=$_POST["CostSgibkaOld"];
			$CostSgibka=$_POST["CostSgibka"];
			$CostSvarkaOld=$_POST["CostSvarkaOld"];
			$CostSvarka=$_POST["CostSvarka"];
			$CostFrameOld=array(); if(isset($_POST["CostFrameOld"])) $CostFrameOld=$_POST["CostFrameOld"];
			$CostFrame=array(); if(isset($_POST["CostFrame"])) $CostFrame=$_POST["CostFrame"];
			$CostMdfOld=array(); if(isset($_POST["CostMdfOld"])) $CostMdfOld=$_POST["CostMdfOld"];
			$CostMdf=array(); if(isset($_POST["CostMdf"])) $CostMdf=$_POST["CostMdf"];
			$CostSborkaOld=$_POST["CostSborkaOld"];
			$CostSborka=$_POST["CostSborka"];
			$CostSborkaMdfOld=array(); if(isset($_POST["CostSborkaMdfOld"])) $CostSborkaMdfOld=$_POST["CostSborkaMdfOld"];
			$CostSborkaMdf=array(); if(isset($_POST["CostSborkaMdf"])) $CostSborkaMdf=$_POST["CostSborkaMdf"];
			$CostColorOld=$_POST["CostColorOld"];
			$CostColor=$_POST["CostColor"];
			$CostUpakOld=$_POST["CostUpakOld"];
			$CostUpak=$_POST["CostUpak"];
			$CostShptOld=$_POST["CostShptOld"];
			$CostShpt=$_POST["CostShpt"];
			$i=0; $er=""; $html="";
			//$m->autocommit(FALSE);
			//$m->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
			$er="";
			while(isset($idDoor[$i]) & $er=="")
			{
				//Для начала определим для какой позиции в на какой стадии изменилась стоимость
				$EditDoor=false;
				$EditLaser=false;
				$EditSgibka=false;
				$EditSvarka=false;
				$EditFrame=false;
				$EditMdf=false;
				$EditSborka=false;
				$EditSborkaMdf=false;
				$EditColor=false;
				$EditUpak=false;
				$EditShpt=false;
				if((float)$CostLaser[$i]!=(float)$CostLaserOld[$i]) $EditLaser=true;
				if((float)$CostSgibka[$i]!=(float)$CostSgibkaOld[$i]) $EditSgibka=true;
				if((float)$CostSvarka[$i]!=(float)$CostSvarkaOld[$i]) $EditSvarka=true;
				if(isset($CostFrame[$i])) if((float)$CostFrame[$i]!=(float)$CostFrameOld[$i]) $EditFrame=true;
				if(isset($CostMdf[$i])) if((float)$CostMdf[$i]!=(float)$CostMdfOld[$i]) $EditMdf=true;
				if((float)$CostSborka[$i]!=(float)$CostSborkaOld[$i]) $EditSborka=true;
				if(isset($CostSborkaMdf[$i])) if((float)$CostSborkaMdf[$i]!=(float)$CostSborkaMdfOld[$i]) $EditSborkaMdf=true;
				if((float)$CostColor[$i]!=(float)$CostColorOld[$i]) $EditColor=true;
				if((float)$CostUpak[$i]!=(float)$CostUpakOld[$i]) $EditUpak=true;
				if((float)$CostShpt[$i]!=(float)$CostShptOld[$i]) $EditShpt=true;
				if($EditLaser || $EditSgibka || $EditSvarka || $EditFrame || $EditMdf || $EditSborka || $EditSborkaMdf || $EditColor || $EditUpak || $EditShpt) $EditDoor=true;
				$FlagEditAll=true;
				if($EditDoor || $FlagEditAll)
				{
					$FrameS=""; if(isset($CostFrame[$i])) if($CostFrame[$i]!="") $FrameS=", CostFrame=".$CostFrame[$i];
					$MdfS=""; if(isset($CostMdf[$i])) if($CostMdf[$i]!="") $MdfS=", CostMdf=".$CostMdf[$i];
					$SborkaMdfS=""; if(isset($CostSborkaMdf[$i])) if($CostSborkaMdf[$i]!="") $SborkaMdfS=", CostSborkaMdf=".$CostSborkaMdf[$i];
					$m->query("UPDATE orderdoors SET CostLaser=".$CostLaser[$i].", CostSgibka=".$CostSgibka[$i].", CostSvarka=".$CostSvarka[$i].$FrameS.$MdfS.", CostSborka=".$CostSborka[$i].$SborkaMdfS.", CostColor=".$CostColor[$i].", CostUpak=".$CostUpak[$i].", CostShpt=".$CostShpt[$i]." WHERE id=".$idDoor[$i]) or die($er=$er." Ошибка изменения позиции".mysqli_error($m));
					//Определим id наряда
					$d=$m->query("SELECT id FROM Naryad WHERE idDoors=".$idDoor[$i]);
					if($d->num_rows>0)
					{
						while(($r=$d->fetch_assoc()) & $er=="")
						{
							if($EditLaser || $FlagEditAll) $m->query("UPDATE NaryadComplite SET Cost=".$CostLaser[$i]." WHERE Step=1 AND idNaryad=".$r["id"]) or die($er=$er." Ошибка изменения стадии".mysqli_error($m));
							if($EditSgibka || $FlagEditAll) $m->query("UPDATE NaryadComplite SET Cost=".$CostSgibka[$i]." WHERE Step=2 AND idNaryad=".$r["id"]) or die($er=$er." Ошибка изменения стадии".mysqli_error($m));
							if($EditSvarka || $FlagEditAll) $m->query("UPDATE NaryadComplite SET Cost=".$CostSvarka[$i]." WHERE Step=3 AND idNaryad=".$r["id"]) or die($er=$er." Ошибка изменения стадии".mysqli_error($m));
							if(isset($CostFrame[$i]))
							if($CostFrame[$i]!="")
							if($EditFrame || $FlagEditAll)
								$m->query("UPDATE NaryadComplite SET Cost=".$CostFrame[$i]." WHERE Step=4 AND idNaryad=".$r["id"]) or die($er=$er." Ошибка изменения стадии".mysqli_error($m));
							if(isset($CostMdf[$i]))
							if($CostMdf[$i]!="")
							if($EditMdf || $FlagEditAll)
								$m->query("UPDATE NaryadComplite SET Cost=".$CostMdf[$i]." WHERE Step=9 AND idNaryad=".$r["id"]) or die($er=$er." Ошибка изменения стадии".mysqli_error($m));
							if($EditSborka || $FlagEditAll) $m->query("UPDATE NaryadComplite SET Cost=".$CostSborka[$i]." WHERE Step=5 AND idNaryad=".$r["id"]) or die($er=$er." Ошибка изменения стадии".mysqli_error($m));
							if(isset($CostSborkaMdf[$i]))
							if($CostSborkaMdf[$i]!="")
							if($EditSborkaMdf || $FlagEditAll)
								$m->query("UPDATE NaryadComplite SET Cost=".$CostSborkaMdf[$i]." WHERE Step=10 AND idNaryad=".$r["id"]) or die($er=$er." Ошибка изменения стадии".mysqli_error($m));
							if($EditColor || $FlagEditAll) $m->query("UPDATE NaryadComplite SET Cost=".$CostColor[$i]." WHERE Step=6 AND idNaryad=".$r["id"]) or die($er=$er." Ошибка изменения стадии".mysqli_error($m));
							if($EditUpak || $FlagEditAll) $m->query("UPDATE NaryadComplite SET Cost=".$CostUpak[$i]." WHERE Step=7 AND idNaryad=".$r["id"]) or die($er=$er." Ошибка изменения стадии".mysqli_error($m));
							if($EditShpt || $FlagEditAll) $m->query("UPDATE NaryadComplite SET Cost=".$CostShpt[$i]." WHERE Step=8 AND idNaryad=".$r["id"]) or die($er=$er." Ошибка изменения стадии".mysqli_error($m));
						};
						$d->close();
					};
				};
				$i++;
			};
			if($er=="")
			{
				$m->commit();
			}
			else
			{
				$m->rollback();
				echo $er;
			};
		break;
		case "EditWorkSumCompitePrint":
			$html="<h2>Изменение стоимости работ заказа №".$_POST["OrderNum"]."</h2>";
			$html=$html."<table><tr>".
				"<td border='1' rowspan=2>п/п</td>".
				"<td border='1' rowspan=2>Тип</td>".
				"<td border='1' rowspan=2>Размер</td>".
				"<td border='1' rowspan=2>Кол-во</td>".
				"<td border='1' colspan=2>Лазер</td>".
				"<td border='1' colspan=2>Сгибка</td>".
				"<td border='1' colspan=2>Сварка</td>".
				"<td border='1' colspan=2>Рамка</td>".
				"<td border='1' colspan=2>Сборка</td>".
				"<td border='1' colspan=2>Покраска</td>".
				"<td border='1' colspan=2>Упаковка</td>".
				"<td border='1' colspan=2>Отгрузка</td>".
				"<td border='1' colspan=2>Итого</td>".
				"<td border='1' colspan=2>Всего</td>".
				"</tr>";
			$html=$html."<tr>".
				"<td>Исходное</td><td>Изменение</td>".
				"<td>Исходное</td><td>Изменение</td>".
				"<td>Исходное</td><td>Изменение</td>".
				"<td>Исходное</td><td>Изменение</td>".
				"<td>Исходное</td><td>Изменение</td>".
				"<td>Исходное</td><td>Изменение</td>".
				"<td>Исходное</td><td>Изменение</td>".
				"<td>Исходное</td><td>Изменение</td>".
				"<td>Исходное</td><td>Изменение</td>".
				"<td>Исходное</td><td>Изменение</td>".
				"</tr>";
			$i=0;
			while(isset($_POST["Pos"][$i])){
				$SumAll=0; $SumAllOld=0;
				$SumAllOld=$_POST["CostLaserOld"][$i]+$_POST["CostSgibkaOld"][$i]+$_POST["CostSvarkaOld"][$i]+
					(isset($_POST["CostFrameOld"][$i])?$_POST["CostFrameOld"][$i]:0)+
					$_POST["CostSborkaOld"][$i]+$_POST["CostColorOld"][$i]+$_POST["CostUpakOld"][$i]+$_POST["CostShptOld"][$i];
				$SumAll=$_POST["CostLaser"][$i]+$_POST["CostSgibka"][$i]+$_POST["CostSvarka"][$i]+
					(isset($_POST["CostFrame"][$i])?$_POST["CostFrame"][$i]:0)+
					$_POST["CostSborka"][$i]+$_POST["CostColor"][$i]+$_POST["CostUpak"][$i]+$_POST["CostShpt"][$i];
				$html=$html."<tr>".
					"<td>".$_POST["Pos"][$i]."</td>".
					"<td>".$_POST["Name"][$i]."</td>".
					"<td>".$_POST["Size"][$i]."</td>".
					"<td>".$_POST["Count"][$i]."</td>".
					"<td>".$_POST["CostLaserOld"][$i]."</td><td>".$_POST["CostLaser"][$i]."</td>".
					"<td>".$_POST["CostSgibkaOld"][$i]."</td><td>".$_POST["CostSgibka"][$i]."</td>".
					"<td>".$_POST["CostSvarkaOld"][$i]."</td><td>".$_POST["CostSvarka"][$i]."</td>".
					"<td>".(isset($_POST["CostFrameOld"][$i])?$_POST["CostFrameOld"][$i]:"")."</td><td>".(isset($_POST["CostFrame"][$i])?$_POST["CostFrame"][$i]:"")."</td>".
					"<td>".$_POST["CostSborkaOld"][$i]."</td><td>".$_POST["CostSborka"][$i]."</td>".
					"<td>".$_POST["CostColorOld"][$i]."</td><td>".$_POST["CostColor"][$i]."</td>".
					"<td>".$_POST["CostUpakOld"][$i]."</td><td>".$_POST["CostUpak"][$i]."</td>".
					"<td>".$_POST["CostShptOld"][$i]."</td><td>".$_POST["CostShpt"][$i]."</td>".
					"<td>".$SumAllOld."</td><td>".$SumAll."</td>".
					"<td>".($SumAllOld*$_POST["Count"][$i])."</td><td>".($SumAll*$_POST["Count"][$i])."</td>".
					"</tr>";
				$i++;
			};
			include("../mpdf53/mpdf.php");
			$mpdf = new mPDF('utf-8', 'A4-L', '8', '', 10, 10, 7, 7, 10, 10); /*задаем формат, отступы и.т.д.*/
			$mpdf->charset_in = 'UTF8';
			$mpdf->list_indent_first_level = 0;
			$mpdf->WriteHTML($html."</table>", 2); /*формируем pdf*/
			$mpdf->Output('CostChange.pdf' , 'F');
		break;
		
		//---------------------- Спецификация ----------------------------
		case "SpecificLoad":
			$d=$m->query("
				SELECT spec.*, m.Name AS MaterialName, m.Unit AS MaterialUnit FROM 
					(SELECT s.id, s.idTypeDoor, s.idGroup, s.idMaterial, s.Count, s.CountEd, s.Step, g.Name AS GroupName FROM stockmaterialgroup g, specificconstruct s WHERE s.idGroup=g.id) spec
				LEFT JOIN stockmaterial m
				ON spec.idMaterial=m.id
				WHERE spec.idTypeDoor=(SELECT DoorType.id FROM manualtypedoors DoorType WHERE DoorType.Name='".$_POST["TypeDoor"]."')
				ORDER BY spec.idGroup
			");
			$a=array(); $i=0;
			while($r=$d->fetch_assoc())
			{
				$a[$i]=array(
					"idTypeDoor"=>$r["idTypeDoor"],
					"idGroup"=>$r["idGroup"],
					"idMaterial"=>$r["idMaterial"],
					"Count"=>$r["Count"],
					"CountEd"=>$r["CountEd"],
					"Step"=>$r["Step"],
					"GroupName"=>$r["GroupName"],
					"MaterialName"=>$r["MaterialName"],
					"MaterialUnit"=>$r["MaterialUnit"]
				);
				$i++;
			};
			echo json_encode($a);
		break;
		case "SpecificLoadList":
			$d=$m->query("SELECT s.*, m.Name AS MaterialName, m.idGroup AS MaterialGroup FROM specificList s, stockmaterial m WHERE s.idMaterial=m.id AND idDoor=".$_POST["idDoor"]);
			$a=array(); $i=0;
			if($d->num_rows>0)
				while($r=$d->fetch_assoc())
				{
					$a[$i]=array(
						"id"=>$r["id"],
						"idMaterial"=>$r["idMaterial"],
						"Count"=>$r["Count"],
						"MaterialName"=>$r["MaterialName"],
						"MaterialGroup"=>$r["MaterialGroup"]
					);
					$i++;
				};
				echo json_encode($a);
		break;
		case "SpecificMateriaList":
			$d=$m->query("SELECT Name, id FROM stockmaterial  WHERE idGroup=".$_POST["idGroup"]." ORDER BY Name");
			$a=array(); $i=0;
			while($r=$d->fetch_assoc()){
				$a[$i]=array(
					"idMaterial"=>$r["id"],
					"NameMaterial"=>$r["Name"]
				);
				$i++;
			};
			echo json_encode($a);
		break;
		case "SpecificSave":
			$er="";
			$idSpecific=$_POST["idSpecific"];
			$idMaterial=$_POST["idMaterial"];
			$Status=$_POST["Status"];
			$CountEd=$_POST["CountEd"];
			$idDoor=$_POST["idDoor"];
			
			for($i=0;$i<count($idSpecific);$i++){
				if($Status[$i]=="Del" & $idSpecific[$i]!="") $m->query("DELETE FROM SpecificList WHERE id=".$idSpecific[$i]) or die($er="hh");
				if($Status[$i]!="Del" & $idSpecific[$i]!="") $m->query("UPDATE SpecificList SET idMaterial=".($idMaterial[$i]!=""?$idMaterial[$i]:"null").", Count=".($CountEd[$i]!=""?$CountEd[$i]:"null")." WHERE id=".$idSpecific[$i]) or die($er="dsd");
			
				if($idSpecific[$i]=="" & $idMaterial[$i]!="") $m->query("INSERT INTO SpecificList (idDoor, idMaterial, Count) VALUES(".$idDoor.", ".($idMaterial[$i]!=""?$idMaterial[$i]:"null").", ".($CountEd[$i]!=""?$CountEd[$i]:"null").")") or die($er="df");
			};
			echo $er;
		break;
		//----------------------------------------------------------------
		case "SelectTypeDoors":
			$d=$m->query("SELECT Name, ValueNull FROM manualtypedoors ORDER BY Name");
			$aTypeDoors=array(); $i=0;
			while($r=$d->fetch_assoc())
			{
				$aTypeDoors[$i]=array(
					"Name"=>$r["Name"],
					"ValueNull"=>$r["ValueNull"]!=null ? $r["ValueNull"] : 0
				);
				$i++;
			};
			$d=$m->query("SELECT Name FROM manualopendoor ORDER BY Name");
			$aOpenDoor=array(); $i=0;
			while($r=$d->fetch_assoc())
			{
				$aOpenDoor[$i]=$r["Name"];
				$i++;
			};
			$d=$m->query("SELECT Name FROM manualnalichnikdoor ORDER BY Name");
			$aTypeNalichnikDoor=array(); $i=0;
			while($r=$d->fetch_assoc())
			{
				$aTypeNalichnikDoor[$i]=$r["Name"];
				$i++;
			};
			$d=$m->query("SELECT Name FROM manualdovoddoor ORDER BY Name");
			$aTypeDovodDoor=array(); $i=0;
			while($r=$d->fetch_assoc())
			{
				$aTypeDovodDoor[$i]=$r["Name"];
				$i++;
			};
			$a=array(
				"TypeDoor"=>$aTypeDoors,
				"TypeNalichnikDoor"=>$aTypeNalichnikDoor,
				"TypeOpenDoor"=>$aOpenDoor,
				"TypeDovodDoor"=>$aTypeDovodDoor
			);
			echo json_encode($a);
		break;
		//-------------Контрагент-------------
		case "ContragentListSelect":
			//$d=$m->query("SELECT id, Alias, INN FROM ordercontragents WHERE ".$_POST["Where"]." ORDER BY Alias");
			$d=$m->query("SELECT o.id, o.Alias, o.INN, c.Phone1 FROM ordercontragents o
  				LEFT JOIN ordercontragentscontacts c
  				ON o.id=c.idContragent
  				WHERE ".$_POST["Where"]." GROUP BY o.id ORDER BY Alias");
			$a=array(); $i=0;
			if($d->num_rows>0)
				while($r=$d->fetch_assoc())
				{
					$a[$i]=array("id"=>$r["id"],"Alias"=>$r["Alias"], "INN"=>$r["INN"], "Phone1"=>$r["Phone1"]);
					$i++;
				};
			echo json_encode($a);
		break;
		case 'ContragentImport':
			$d=$m->query("SELECT * FROM ordercontragents WHERE Alias='".$_POST["Zakaz"]."' OR Name='".$_POST["Zakaz"]."'");
			echo $d->num_rows;
		break;
		case 'ContragentEditStart':
			$aContact=array(); $i=0;
			$d=$m->query("SELECT * FROM ordercontragentscontacts WHERE idContragent=".$_POST["id"]." ORDER BY FIO");
			if($d->num_rows>0)
				while($r=$d->fetch_assoc()){
					$aContact[$i]=array(
						"id"=>$r["id"],
						"FIO"=>$r["FIO"],
						"Post"=>$r["Post"],
						"Phone1"=>$r["Phone1"],
						"Phone2"=>$r["Phone2"],
						"eMail"=>$r["eMail"],
						"Note"=>$r["Note"],
						"Agent"=>$r["Agent"]
						);
					$i++;
				};
			$d=$m->query("SELECT * FROM ordercontragents WHERE id=".$_POST["id"]);
			$r=$d->fetch_assoc();
			echo json_encode(
					array(
						"Alias"=>$r["Alias"],
						"Name"=>$r["Name"],
						"AdressUrid"=>$r["AdressUrid"],
						"AdressFact"=>$r["AdressFact"],
						"AdressShpt"=>$r["AdressShpt"],
						"INN"=>$r["INN"],
						"KPP"=>$r["KPP"],
						"OKPO"=>$r["OKPO"],
						"OGRN"=>$r["OGRN"],
						"Note"=>$r["Note"],
						"Contacts"=>$aContact
					)
				);
		break;
		case 'ContragentSave':
			$er="";
			$m->autocommit(FALSE);
			$m->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
			try
			{
				if($_POST["id"]=="")
				{
					$m->query("INSERT INTO ordercontragents (Alias, Name, AdressUrid, AdressFact, AdressShpt, INN, KPP, OKPO, OGRN, Note) VALUES(".
						"'".$_POST["Alias"]."', ".
						"'".$_POST["Name"]."', ".
						"'".$_POST["AdressUrid"]."', ".
						"'".$_POST["AdressFact"]."', ".
						"'".$_POST["AdressShpt"]."', ".
						"'".$_POST["INN"]."', ".
						"'".$_POST["KPP"]."', ".
						"'".$_POST["OKPO"]."', ".
						"'".$_POST["OGRN"]."', ".
						"'".$_POST["Note"]."' ".
						")") or die($er=$er.mysqli_error($m));
				}
				else
				{
					$m->query("UPDATE ordercontragents SET ".
						"Alias='".$_POST["Alias"]."', ".
						"Name='".$_POST["Name"]."', ".
						"AdressUrid='".$_POST["AdressUrid"]."', ".
						"AdressFact='".$_POST["AdressFact"]."', ".
						"AdressShpt='".$_POST["AdressShpt"]."', ".
						"INN='".$_POST["INN"]."', ".
						"KPP='".$_POST["KPP"]."', ".
						"OKPO='".$_POST["OKPO"]."', ".
						"OGRN='".$_POST["OGRN"]."', ".
						"Note='".$_POST["Note"]."' ".
						"WHERE id=".$_POST["id"]) or die($er=$er.mysqli_error($m));
				};
			}
			catch (mysqli_sql_exception $e){$er=$er.$e->errorMessage();};
			$idContragent=$_POST["id"]!=""?$_POST["id"]:$m->insert_id;
			$i=0;
			if(isset($_POST["ContactStatus"]))
			while($i<count($_POST["ContactStatus"]) & $er=="")
			{
				try
				{
					switch ($_POST["ContactStatus"][$i]) {
						case 'Add':
							$m->query("INSERT INTO ordercontragentscontacts (idContragent, FIO, Post, Phone1, Phone2, eMail, Note, Agent) VALUES(".
								"".$idContragent.", ".
								"'".$_POST["ContactFIO"][$i]."', ".
								"'".$_POST["ContactPost"][$i]."', ".
								"'".$_POST["ContactPhone1"][$i]."', ".
								"'".$_POST["ContactPhone2"][$i]."', ".
								"'".$_POST["ContacteMail"][$i]."', ".
								"'".$_POST["ContactNote"][$i]."', ".
								"".$_POST["ContactAgent"][$i]." ".
								")"
							) or die($er=$er.mysqli_error($m));
						break;
						case 'Edit':
							$m->query("UPDATE ordercontragentscontacts SET ".
								"FIO='".$_POST["ContactFIO"][$i]."', ".
								"Post='".$_POST["ContactPost"][$i]."', ".
								"Phone1='".$_POST["ContactPhone1"][$i]."', ".
								"Phone2='".$_POST["ContactPhone2"][$i]."', ".
								"eMail='".$_POST["ContacteMail"][$i]."', ".
								"Note='".$_POST["ContactNote"][$i]."', ".
								"Agent=".$_POST["ContactAgent"][$i]." ".
								"WHERE id=".$_POST["ContactID"][$i]
							) or die($er=$er.mysqli_error($m));
						break;
						case "Del":
							$m->query("DELETE FROM ordercontragentscontacts WHERE id=".$_POST["ContactID"][$i]) or die($er=$er.mysqli_error($m));
						break;
					}
				}
				catch (mysqli_sql_exception $e){$er=$er.$e->errorMessage();};
				$i++;
			};
			if($er=="")
			{

				$m->commit(); echo json_encode(array("Status"=>"ok", "idContragent"=>$idContragent));
			}
			else
			{
				echo json_encode(array("Status"=>"err", "ErrMsg"=>$er)); $m->rollback();
			};
		break;
		//Передать изделие из трубы в производство
		case "OrderTubeInProduction":
			$er="";
			$m->autocommit(FALSE);
			$m->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
			switch ($_POST["Status"]) {
				case 'Start':
                    $idDoor=$_POST["idDoor"];

					$idDoor=$_POST["idDoor"];
					//Определим начальный номер позиции
					$d=$m->query("SELECT od.id, od.count FROM OrderDoors od WHERE od.idOrder=(SELECT od1.idOrder FROM OrderDoors od1 WHERE od1.id=$idDoor) ORDER BY od.NumPP");
					$FirstNumInOrder=0;
					while($r=$d->fetch_assoc())
						if($r["id"]!=$idDoor)
						{
							$FirstNumInOrder+=(int)$r["count"];
						}
						else
							 break;
					$FirstNumInOrder++;

					$d=$m->query("SELECT o1.idOrder, o.Blank, o.Shet, o1.id, o1.NumPP, o1.name, o1.Count, o1.CostLaser, o1.CostSgibka, o1.CostSvarka, o1.CostFrame, o1.CostMdf, o1.CostSborka, o1.CostColor, o1.CostSborkaMdf, o1.CostShpt, o1.CostUpak, o1.WorkWindowCh, o1.WorkWindowNoFrame, o1.StvorkaWindowCh, o1.StvorkaWindowNoFrame, o1.FramugaWindowCh, o1.FramugaWindowNoFrame FROM oreders o, orderdoors o1 WHERE o.id=o1.idOrder AND o1.id=".$_POST["idDoor"]);
					$r=$d->fetch_assoc();
					$idOrder=$r["idOrder"];

					if($XMLParams->Global->ViewNumOrder=="Shet" & $r["Shet"]=="")$er="Не заполненно поле СЧЕТ, заполните и сохраните.";
					if($r["CostSvarka"]==0 || $r["CostSborka"]==0 || $r["CostColor"]==0 || $r["CostUpak"]==0 || ($XMLParams->Orders->ShptAllowZero=="0" & $r["CostShpt"]==0)) $er=$er." Не заполненно стоимость работ ";
					if($_SESSION["AutorizeType"]!=1 & $_SESSION["AutorizeType"]!=2) $er=$er." Не хватает прав чтобы передать в производство";

					if($er=="")
					{
						//Создадим Таск для склада, если уставновлен такой параметр
						if($XMLParams->Enterprise->StockNaryadComplite=="Enable") {
							$m->query("INSERT INTO stn_NaryadCompliteTasks (idDoor, idNaryad, DateCreate, Step, Type) VALUES($idDoor, -1, NOW(), 1,'WriteOf')") or die($m->error);
							$m->query("INSERT INTO stn_NaryadCompliteTasks (idDoor, idNaryad, DateCreate, Step, Type) VALUES($idDoor, -1, NOW(), 2,'WriteOf')") or die($m->error);
						};
						//Продолжим отправку на производство
						$NaryadNum=($XMLParams->Global->ViewNumOrder=="Blank"?$r["Blank"]:$r["Shet"])."/".$r["NumPP"]."/";
						$CostLaser=(float)$r['CostLaser'];
						$CostSgibka=(float)$r['CostSgibka'];
						$CostSvarka=(float)$r['CostSvarka'];
						$CostFrame=(float)$r['CostFrame'];
						$CostMdf=(float)$r['CostMdf'];
						$CostSborka=(float)$r['CostSborka'];
						$CostColor=(float)$r['CostColor'];
						$CostSborkaMdf=(float)$r['CostSborkaMdf'];
						$CostUpak=(float)$r['CostUpak'];
						$CostShpt=(float)$r['CostShpt'];

						$Frame="null";
						if(($r["WorkWindowCh"] & !$r["WorkWindowNoFrame"]) || ($r["StvorkaWindowCh"] & !$r["StvorkaWindowNoFrame"]) || ($r["FramugaWindowCh"] & !$r["FramugaWindowNoFrame"])) $Frame="0";
						$MdfDoor=strpos($r["name"],"МДФ")>-1? "0": "NULL";
						$SborkaMdf=strpos($r["name"],"МДФ")>-1? "0": "NULL";
						$Svarka=0;
						$Pos=1;
						while ($Pos<=(int)$r["Count"] & $er=="") {
							$NumInOrder=$FirstNumInOrder+$Pos-1;
							$m->query("INSERT INTO naryad (idDoors, NumInOrder, Num, NumPP, LaserCompliteFlag, SgibkaCompliteFlag, SvarkaCompliteFlag, FrameCompliteFlag, MdfCompliteFlag, SborkaCompliteFlag, SborkaMdfCompliteFlag, ColorCompliteFlag, UpakCompliteFlag, ShptCompliteFlag) VALUES (".$_POST["idDoor"].", $NumInOrder, '".$NaryadNum."', ".$Pos.", 1,1,0,$Frame,$MdfDoor,0,$SborkaMdf,0,0,0)") or die($er=$er.mysqli_error($m));
							$idNaryad=$m->insert_id;
							$m->query("INSERT INTO NaryadComplite (idNaryad, Step, Cost, DateComplite) VALUES ($idNaryad, 1, $CostLaser, NOW())") or die($er=$er.mysqli_error($m));
							$m->query("INSERT INTO NaryadComplite (idNaryad, Step, Cost, DateComplite) VALUES ($idNaryad, 2, $CostSgibka, NOW())") or die($er=$er.mysqli_error($m));
							$m->query("INSERT INTO NaryadComplite (idNaryad, Step, Cost) VALUES ($idNaryad, 3, $CostSvarka)") or die($er=$er.mysqli_error($m));
							if($Frame=="0")
								$m->query("INSERT INTO NaryadComplite (idNaryad, Step, Cost) VALUES ($idNaryad, 4, $CostFrame)") or die($er=$er.mysqli_error($m));
							if($MdfDoor=="0")
								$m->query("INSERT INTO NaryadComplite (idNaryad, Step, Cost) VALUES ($idNaryad, 9, $CostMdf)") or die($er=$er.mysqli_error($m));
							$m->query("INSERT INTO NaryadComplite (idNaryad, Step, Cost) VALUES ($idNaryad, 5, $CostSborka)") or die($er=$er.mysqli_error($m));
							if($SborkaMdf=="0")
								$m->query("INSERT INTO NaryadComplite (idNaryad, Step, Cost) VALUES ($idNaryad, 10, $CostSborkaMdf)") or die($er=$er.mysqli_error($m));
							$m->query("INSERT INTO NaryadComplite (idNaryad, Step, Cost) VALUES ($idNaryad, 6, $CostColor)") or die($er=$er.mysqli_error($m));
							$m->query("INSERT INTO NaryadComplite (idNaryad, Step, Cost) VALUES ($idNaryad, 7, $CostUpak)") or die($er=$er.mysqli_error($m));
							$m->query("INSERT INTO NaryadComplite (idNaryad, Step, Cost) VALUES ($idNaryad, 8, $CostShpt)") or die($er=$er.mysqli_error($m));

							$Pos++;
						};
						//Изменим статус заказа
						$m->query("UPDATE oreders SET status=1 WHERE id=".$idOrder) or die($er=$er.mysqli_error($m));
					};
					break;
				//Вернуть из производства
				case 'Work':
                    $idDoor=$_POST["idDoor"];

					//Проверим не прошли наряды этап сварки
					$d=$m->query("SELECT COUNT(*) as C FROM naryad n WHERE (n.SvarkaCompliteFlag=1 OR n.FrameCompliteFlag=1 OR n.SborkaCompliteFlag=1 OR n.ColorCompliteFlag=1 OR n.UpakCompliteFlag=1 OR n.ShptCompliteFlag=1) AND n.idDoors=".$_POST["idDoor"]);
					$r=$d->fetch_assoc();
					if($r["C"]==0)
					{
						$idDoor=$_POST["idDoor"];
						//Создадим Таск для склада
						if($XMLParams->Enterprise->StockNaryadComplite=="Enable") {
							$m->query("INSERT INTO stn_NaryadCompliteTasks (idDoor, idNaryad, DateCreate, Step, Type) VALUES($idDoor, -1, NOW(), 1,'Rollback')") or die($m->error);
							$m->query("INSERT INTO stn_NaryadCompliteTasks (idDoor, idNaryad, DateCreate, Step, Type) VALUES($idDoor, -1, NOW(), 2,'Rollback')") or die($m->error);;
						};
						//Продолжим отправку на производство
						$d=$m->query("DELETE FROM NaryadComplite WHERE idNaryad IN (SELECT id FROM Naryad n WHERE idDoors=$idDoor)");
						$m->query("DELETE FROM naryad WHERE idDoors=$idDoor") or die($er=mysql_error($m));
						$d=$m->query("SELECT o.id FROM oreders o, orderdoors o1 WHERE o1.idOrder=o.id AND o1.id=$idDoor");
						$r=$d->fetch_assoc();
						$idOrder=$r["id"];
						$d=$m->query("SELECT count(*) AS CountDoor FROM orderdoors WHERE idOrder=".$idOrder);
						$r=$d->fetch_assoc();
						if($r["CountDoor"]==1)
							$m->query("UPDATE oreders o SET o.status=0 WHERE o.id=".$idOrder) or die($er=mysql_error($m));
					}
					else
						$er="Двери прошли дальнейшие стадии";
					break;
			};

			if($er=="")
			{

				$m->commit();
				echo "ok";
			}
			else
			{
				echo $er; $m->rollback();
			};
		break;
		//Печать нарядов
		case "PrintNaryad":
			include("../mpdf53/mpdf.php");
			$mpdf = new mPDF('utf-8', 'A5', '8', '', 10, 10, 7, 7, 10, 10); /*задаем формат, отступы и.т.д.*/
			$mpdf->charset_in = 'UTF8';
			$mpdf->list_indent_first_level = 0;
			$a=array(); $c=0;
			//Загружаем данные
			$idOrder=$_POST["idOrder"];
			$d=$m->query("SELECT o.Blank, o.BlankDate, o.Shet, o.ShetDate, o.Zakaz, o1.id, o1.NumPP, o1.Count, o1.name, o1.H, o1.W, o1.S, o1.SEqual, o1.Open, o1.Nalichnik, o1.Dovod, o1.RAL, o1.Zamok, o1.Shtild, o1.Note as DoorNote, o1.WorkWindowCh, o1.WorkWindowNoFrame, o1.StvorkaWindowCh, o1.StvorkaWindowNoFrame, o1.FramugaWindowCh, o1.FramugaWindowNoFrame, o1.CostLaser, o1.CostSgibka, o1.CostSvarka, o1.CostFrame, o1.CostSborka, o1.CostColor, o1.CostUpak, o1.CostShpt, (SELECT SUM(od2.Count) FROM oreders o2, orderdoors od2 WHERE o2.id=o.id AND o2.id=od2.idOrder) AS DoorAllCount FROM oreders o, orderDoors o1 WHERE o.id=$idOrder AND o.id=o1.idOrder ORDER BY o1.NumPP");
			if(file_exists("OrderNaryad.pdf") ) unlink("OrderNaryad.pdf");
			$NumInOrder=1;
			while($r=$d->fetch_assoc())
			{
				//Определим нужна рамка
				$Frame=false;
				if(($r["WorkWindowCh"] & !$r["WorkWindowNoFrame"]) || ($r["StvorkaWindowCh"] & !$r["StvorkaWindowNoFrame"]) || ($r["FramugaWindowCh"] & !$r["FramugaWindowNoFrame"])) $Frame=true;
				//Определим размеры двери
				$Size=$r["H"]." x ".$r["W"].($r["S"]!=null?" x ".$r["S"] : (($r["SEqual"]!=0 & $r["SEqual"]!=NULL)? " x Равн.":""));
				//Опеределим номер: заказ/счет / позиция
				$Num=($XMLParams->Global->ViewNumOrder=="Blank"?$r["Blank"]:$r["Shet"])."/".$r["NumPP"]."/";
				//Комерческая информация
				$Commercie=$XMLParams->Enterprise->ViewCommercieWhenPrint=="true"?"<b>Счет № : </b>".$r["Shet"]."<br><b>Заказчик : </b>".$r["Zakaz"] : "";

				$imgOut="pdfOut/naryadImg_".$r["id"]."jpg";
				if(file_exists($imgOut) ) unlink($imgOut);
				copy ( "http://".$XMLParams->Global->HostName."/enterprise/naryadimg.php?idDoor=".$r["id"], $imgOut );
				for($NumPP=1;$NumPP<=(int)$r["Count"];$NumPP++)
				{
					$mpdf->AddPage();

					$html="";
					$f = fopen("../enterprise/printNaryadForm.html", "r");
					$a_in=array(
					"#imgOut","#Commercie","#NaryadID", "#NumInOrder", "#Num","#Name","#Size","#Open","#Nalichnik","#Dovod","#RAL","#Zamok", "#Shtild","#DoorNote","#Svarka", "#Frame","#Sborka","#Color","#Upak","#Shpt","#NaryadNote", "#Master", "#DoorAllCount"
					);
					$Svarka="__________________________  Стоимость: ".$r["CostSvarka"];
					$Frame="";
					if($Frame)
						$Frame="&nbsp;&nbsp; Рамка : &nbsp;<span>__________________________  Стоимость: ".$r["CostFram"]."</span><br><br>";
					$Sborka="__________________________  Стоимость: ".$r["CostSborka"];
					$Color="__________________________  Стоимость: ".$r["CostColor"];
					$Upak="__________________________  Стоимость: ".$r["CostUpak"];
					$Shpt="__________________________  Стоимость: ".$r["CostShpt"];
					//------Обработка штильды----------------
					$Shtild="";
					if($r["Shtild"]!="" & is_numeric($r["Shtild"]))
					{
						$Shtild=(int)$r["Shtild"]+(int)$NumPP-1;
						//Если число 0007, тогда необходимо комписировать 0 в начале
						$s_new=strval($Shtild);
						for($j=0;$j<strlen($r["Shtild"]);$j++)
							if(strlen($r["Shtild"])>strlen($s_new))
								$s_new="0".$s_new;
						$Shtild=$s_new;
					};
					if((int)$Shtild==(int)$Num) $Shtild=$r["Shtild"]; //Проверка если номер штилбды не число тогад выводим его знач
					//-----------------------------------------------------
					$a_replace=array(
						$imgOut, $Commercie,$r["id"], $NumInOrder, $Num.$NumPP, $r["name"] , $Size , $r["Open"], $r["Nalichnik"],$r["Dovod"],"RAL ".$r["RAL"],$r["Zamok"],$Shtild,$r["DoorNote"] ,$Svarka,$Frame,$Sborka,$Color,$Upak, $Shpt, "", "", $r["DoorAllCount"]
					);
					while(!feof($f))
						$html=$html.str_replace($a_in,$a_replace,fgets($f));
					fclose($f);


					$mpdf->WriteHTML($html, 2); /*формируем pdf*/
					$NumInOrder++;
				};
			};
			$mpdf->Output('OrderNaryad.pdf' , 'F');
			echo "ok";
		break;
		case "SummaryPrint":
			$idOrder=$_POST["idOrder"];
			include("../mpdf53/mpdf.php");		
			$mpdf = new mPDF('utf-8', 'A4', '8', '', 10, 10, 7, 7, 10, 10); /*задаем формат, отступы и.т.д.*/
			$mpdf->charset_in = 'UTF8';
			$mpdf->list_indent_first_level = 0; 
			$d=$m->query("SELECT o1.id as idDoor, o1.NumPP AS DoorPos, o1.Name, o1.H, o1.W, o1.S, o1.SEqual, o1.Count, n.id as NaryadID, n.* FROM 
				(SELECT od.id, od.NumPP, od.Name, od.H, od.W, od.S, od.SEqual, od.Count FROM oreders o, orderdoors od WHERE o.id=od.idOrder AND o.id=$idOrder) o1
				LEFT JOIN naryad n
				ON o1.id=n.idDoors
				ORDER BY o1.NumPP");
			$html=""; $idDoor=""; $NumPP=""; $Name=""; $DoorSize="";
			$LaserCompliteFlag=0;
			$SgibkaCompliteFlag=0;
			$SvarkaCompliteFlag=0;
			$FrameCompliteFlag=0;
			$SborkaCompliteFlag=0;
			$ColorCompliteFlag=0;
			$UpakCompliteFlag=0;
			$ShptCompliteFlag=0;
			$CountSum=0;
			$LaserCompliteFlagSum=0;
			$SgibkaCompliteFlagSum=0;
			$SvarkaCompliteFlagSum=0;
			$FrameCompliteFlagSum=0;
			$SborkaCompliteFlagSum=0;
			$ColorCompliteFlagSum=0;
			$UpakCompliteFlagSum=0;
			$ShptCompliteFlagSum=0;
			$html="<table>
					<tr>
						<td>Позиция</td>
						<td>Наименование</td>
						<td>Размеры</td>
						<td>Кол-во</td>
						<td>Лазер</td>
						<td>Сгибка</td>
						<td>Сварка</td>
						<td>Рамка</td>
						<td>Сборка</td>
						<td>Покраска</td>
						<td>Упаковка</td>
						<td>Отгрузка</td>
					</tr>
				";
			while($r=$d->fetch_assoc())
			{
				if($idDoor!=$r["idDoor"])
				{
					if($idDoor!="")
						$html=$html."<tr>".
							"<td>".$NumPP."</td>".
							"<td>".$Name."</td>".
							"<td>".$DoorSize."</td>".
							"<td>".$Count."</td>".
							"<td>".$LaserCompliteFlag."</td>".
							"<td>".$SgibkaCompliteFlag."</td>".
							"<td>".$SvarkaCompliteFlag."</td>".
							"<td>".$FrameCompliteFlag."</td>".
							"<td>".$SborkaCompliteFlag."</td>".
							"<td>".$ColorCompliteFlag."</td>".
							"<td>".$UpakCompliteFlag."</td>".
							"<td>".$ShptCompliteFlag."</td>".
							"</tr>";
					$LaserCompliteFlag=0;
					$SgibkaCompliteFlag=0;
					$SvarkaCompliteFlag=0;
					$FrameCompliteFlag=0;
					$SborkaCompliteFlag=0;
					$ColorCompliteFlag=0;
					$UpakCompliteFlag=0;
					$ShptCompliteFlag=0;
					$idDoor=$r["idDoor"];
					$NumPP=$r["DoorPos"];
					$Name=$r["Name"];
					$DoorSize=$r["H"]."x".$r["W"];
					$Count=$r["Count"];
					$CountSum+=$Count;
				};
				if(isset($r["NaryadID"]))
				{
					$LaserCompliteFlag+=$r["LaserCompliteFlag"]==1 ? 1 : 0;
					$SgibkaCompliteFlag+=$r["SgibkaCompliteFlag"]==1 ? 1 : 0;
					$SvarkaCompliteFlag+=$r["SvarkaCompliteFlag"]==1 ? 1 : 0;
					$FrameCompliteFlag+=$r["FrameCompliteFlag"]==1 ? 1 : 0;
					$SborkaCompliteFlag+=$r["SborkaCompliteFlag"]==1 ? 1 : 0;
					$ColorCompliteFlag+=$r["ColorCompliteFlag"]==1 ? 1 : 0;
					$UpakCompliteFlag+=$r["UpakCompliteFlag"]==1 ? 1 : 0;
					$ShptCompliteFlag+=$r["ShptCompliteFlag"]==1 ? 1 : 0;
					//Всего
					$LaserCompliteFlagSum+=$r["LaserCompliteFlag"]==1 ? 1 : 0;
					$SgibkaCompliteFlagSum+=$r["SgibkaCompliteFlag"]==1 ? 1 : 0;
					$SvarkaCompliteFlagSum+=$r["SvarkaCompliteFlag"]==1 ? 1 : 0;
					$FrameCompliteFlagSum+=$r["FrameCompliteFlag"]==1 ? 1 : 0;
					$SborkaCompliteFlagSum+=$r["SborkaCompliteFlag"]==1 ? 1 : 0;
					$ColorCompliteFlagSum+=$r["ColorCompliteFlag"]==1 ? 1 : 0;
					$UpakCompliteFlagSum+=$r["UpakCompliteFlag"]==1 ? 1 : 0;
					$ShptCompliteFlagSum+=$r["ShptCompliteFlag"]==1 ? 1 : 0;
				};
			};
			if($idDoor!="")
				$html=$html."<tr>".
						"<td>".$NumPP."</td>".
						"<td>".$Name."</td>".
						"<td>".$DoorSize."</td>".
						"<td>".$Count."</td>".
						"<td>".$LaserCompliteFlag."</td>".
						"<td>".$SgibkaCompliteFlag."</td>".
						"<td>".$SvarkaCompliteFlag."</td>".
						"<td>".$FrameCompliteFlag."</td>".
						"<td>".$SborkaCompliteFlag."</td>".
						"<td>".$ColorCompliteFlag."</td>".
						"<td>".$UpakCompliteFlag."</td>".
						"<td>".$ShptCompliteFlag."</td>".
					"</tr>";
			//Выведем всего
			$html=$html."<tr>".
					"<td colspan=3>Всего</td>".
					"<td>".$CountSum."</td>".
					"<td>".$LaserCompliteFlagSum."</td>".
					"<td>".$SgibkaCompliteFlagSum."</td>".
					"<td>".$SvarkaCompliteFlagSum."</td>".
					"<td>".$FrameCompliteFlagSum."</td>".
					"<td>".$SborkaCompliteFlagSum."</td>".
					"<td>".$ColorCompliteFlagSum."</td>".
					"<td>".$UpakCompliteFlagSum."</td>".
					"<td>".$ShptCompliteFlagSum."</td>".
				"</tr>";

			$html=$html."</table>";
			$mpdf->WriteHTML($html, 2); /*формируем pdf*/
			$mpdf->Output('OrderSummary.pdf' , 'F');
		break;
	};
	$m->close();
	//mysql_close($link);

function getGUID(){
	if (function_exists('com_create_guid')){
		return com_create_guid();
	}else{
		mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
		$charid = strtoupper(md5(uniqid(rand(), true)));
		$hyphen = chr(45);// "-"
		$uuid = //chr(123)// "{"
			substr($charid, 0, 8).$hyphen
			.substr($charid, 8, 4).$hyphen
			.substr($charid,12, 4).$hyphen
			.substr($charid,16, 4).$hyphen
			.substr($charid,20,12);
			//.chr(125);// "}"
		return $uuid;
	}
}
?>