<?php
	$XMLParams=simplexml_load_file("../params.xml");
	$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
	switch ($_POST['Method'])
	{
		case "Select":
			$SumAll=0; $CountAll=0;
			$DateWith=$_POST["DateWith"];
			$DateBy=$_POST["DateBy"];

			$a=array(
				1=>array(
					"Step"=>"Лазер",
					"CountComplite"=>0,
					"Cost"=>0,
					"PaymentPlus"=>0,
					"PaymentMinus"=>0,
					"Ostatok"=>0
					),
				2=>array(
					"Step"=>"Сгибка",
					"CountComplite"=>0,
					"Cost"=>0,
					"PaymentPlus"=>0,
					"PaymentMinus"=>0,
					"Ostatok"=>0
					),
				3=>array(
					"Step"=>"Сварка",
					"CountComplite"=>0,
					"Cost"=>0,
					"PaymentPlus"=>0,
					"PaymentMinus"=>0,
					"Ostatok"=>0
					),
				4=>array(
					"Step"=>"Рамка",
					"CountComplite"=>0,
					"Cost"=>0,
					"PaymentPlus"=>0,
					"PaymentMinus"=>0,
					"Ostatok"=>0
					),
				5=>array(
					"Step"=>"Сборка",
					"CountComplite"=>0,
					"Cost"=>0,
					"PaymentPlus"=>0,
					"PaymentMinus"=>0,
					"Ostatok"=>0
					),
				6=>array(
					"Step"=>"Покраска",
					"CountComplite"=>0,
					"Cost"=>0,
					"PaymentPlus"=>0,
					"PaymentMinus"=>0,
					"Ostatok"=>0
					),
				7=>array(
					"Step"=>"Упаковка",
					"CountComplite"=>0,
					"Cost"=>0,
					"PaymentPlus"=>0,
					"PaymentMinus"=>0,
					"Ostatok"=>0
					),
				8=>array(
					"Step"=>"Погрузка",
					"CountComplite"=>0,
					"Cost"=>0,
					"PaymentPlus"=>0,
					"PaymentMinus"=>0,
					"Ostatok"=>0
					),
				9=>array(
					"Step"=>"Прочие",
					"CountComplite"=>0,
					"Cost"=>0,
					"PaymentPlus"=>0,
					"PaymentMinus"=>0,
					"Ostatok"=>0
					),
				10=>array(
					"Step"=>"Не отмечен",
					"CountComplite"=>0,
					"Cost"=>0,
					"PaymentPlus"=>0,
					"PaymentMinus"=>0,
					"Ostatok"=>0
					)
			);
			//Обработаем сколько заработано отмеченными сотрудниками
			if($d=$m->query("SELECT Step, COUNT(*) AS CountComplite, SUM(Cost) AS Cost FROM naryadcomplite WHERE DateComplite BETWEEN str_to_date('$DateWith', '%d.%m.%Y') AND date_add(str_to_date('$DateBy', '%d.%m.%Y'), interval 1 day) AND idWorker IS NOT NULL GROUP BY Step ORDER BY Step") )
			{
				while($r=$d->fetch_assoc())
				{
					$a[(int)$r["Step"]]["CountComplite"]=(int)$r["CountComplite"];
					$a[(int)$r["Step"]]["Cost"]=(float)$r["Cost"];
				};
				$d->close();
			};
			//Расчитаем выполнение без отметки сотрулника
			if($d=$m->query("SELECT COUNT(*) AS CountComplite, SUM(Cost) AS Cost FROM naryadcomplite WHERE DateComplite BETWEEN str_to_date('$DateWith', '%d.%m.%Y') AND date_add(str_to_date('$DateBy', '%d.%m.%Y'), interval 1 day) AND idWorker IS NULL"))
			{
				$r=$d->fetch_assoc();
				$a[10]["CountComplite"]=(int)$r["CountComplite"];
				$a[10]["Cost"]=(float)$r["Cost"];
				$d->close();
			};
			//Посчитаем начисления
			if($d=$m->query("SELECT w.DolgnostID, SUM(p.Sum) AS Sum FROM paymentsworkers p, workers w WHERE p.idWorker=w.id AND p.DatePayment BETWEEN str_to_date('$DateWith', '%d.%m.%Y') AND date_add(str_to_date('$DateBy', '%d.%m.%Y'), interval 1 day) AND p.Sum>0 GROUP BY w.DolgnostID ORDER BY w.DolgnostID"))
			{
				while ($r=$d->fetch_assoc()) 
					switch ((int)$r["DolgnostID"]) {
						case 14:
							$a[1]["PaymentPlus"]+=(float)$r["Sum"];
							break;
						case 5: case 10:
							$a[2]["PaymentPlus"]+=(float)$r["Sum"];
							break;
						case 3:
							$a[3]["PaymentPlus"]+=(float)$r["Sum"];
							break;
						case 15:
							$a[4]["PaymentPlus"]+=(float)$r["Sum"];
							break;
						case 4:
							$a[5]["PaymentPlus"]+=(float)$r["Sum"];
							break;
						case 11: case 6:
							$a[6]["PaymentPlus"]+=(float)$r["Sum"];
							break;
						case 7: case 12:
							$a[7]["PaymentPlus"]+=(float)$r["Sum"];
							break;
						default:
							$a[9]["PaymentPlus"]+=(float)$r["Sum"];
							break;
					}
				$d->close();
			};
			//Посчитаем выплаты
			if($d=$m->query("SELECT w.DolgnostID, SUM(p.Sum) AS Sum FROM paymentsworkers p, workers w WHERE p.idWorker=w.id AND p.DatePayment BETWEEN str_to_date('$DateWith', '%d.%m.%Y') AND date_add(str_to_date('$DateBy', '%d.%m.%Y'), interval 1 day) AND p.Sum<0 GROUP BY w.DolgnostID ORDER BY w.DolgnostID"))
			{
				while ($r=$d->fetch_assoc()) 
					switch ((int)$r["DolgnostID"]) {
						case 14:
							$a[1]["PaymentMinus"]+=(-1)*(float)$r["Sum"];
							break;
						case 5: case 10:
							$a[2]["PaymentMinus"]+=(-1)*(float)$r["Sum"];
							break;
						case 3:
							$a[3]["PaymentMinus"]+=(-1)*(float)$r["Sum"];
							break;
						case 15:
							$a[4]["PaymentMinus"]+=(-1)*(float)$r["Sum"];
							break;
						case 4:
							$a[5]["PaymentMinus"]+=(-1)*(float)$r["Sum"];
							break;
						case 11: case 6:
							$a[6]["PaymentMinus"]+=(-1)*(float)$r["Sum"];
							break;
						case 7: case 12:
							$a[7]["PaymentMinus"]+=(-1)*(float)$r["Sum"];
							break;
						default:
							$a[9]["PaymentMinus"]+=(-1)*(float)$r["Sum"];
							break;
					}
				$d->close();
			};
			
			foreach ($a as &$a1) {
				$a1["Ostatok"]+=$a1["Cost"]+$a1["PaymentPlus"]-$a1["PaymentMinus"];
			};

				/*
				switch ((int)$r["Step"]) {
					case 1: 
						$a["Лазер"]["CountComplite"]=(int)$r["CountComplite"];
						$a["Лазер"]["Cost"]=(float)$r["Cost"];
					break;
					case 2: 
						$a["Сгибка"]["CountComplite"]=(int)$r["CountComplite"];
						$a["Сгибка"]["Cost"]=(float)$r["Cost"];
					break;
					case 3: 
						$a["Сварка"]["CountComplite"]=(int)$r["CountComplite"];
						$a["Сварка"]["Cost"]=(float)$r["Cost"];
					break;
					case 4: 
						$a["Рамка"]["CountComplite"]=(int)$r["CountComplite"];
						$a["Рамка"]["Cost"]=(float)$r["Cost"];
					break;
					case 5: 
						$a["Сборка"]["CountComplite"]=(int)$r["CountComplite"];
						$a["Сборка"]["Cost"]=(float)$r["Cost"];
					break;
					case 6: 
						$a["Покраска"]["CountComplite"]=(int)$r["CountComplite"];
						$a["Покраска"]["Cost"]=(float)$r["Cost"];
					break;
					case 7: 
						$a["Упаковка"]["CountComplite"]=(int)$r["CountComplite"];
						$a["Упаковка"]["Cost"]=(float)$r["Cost"];
					break;
					case 8: 
						$a["Погрузка"]["CountComplite"]=(int)$r["CountComplite"];
						$a["Погрузка"]["Cost"]=(float)$r["Cost"];
					break;
				};
				*/
			/*
			$d=$m->query("CALL ReportSelectCountSum ('".$_POST["DateWith"]."' , '".$_POST["DateBy"]."') ");
			$r=$d->fetch_assoc();
			
			$ItogAll=0;
			$CountWorkLaser=$r["CountWorkLaser"]!=null ? $r["CountWorkLaser"] : 0;
			$SumWorkLaser=$r["SumWorkLaser"]!=null ? $r["SumWorkLaser"] : 0;
			$SumPlusLaser=$r["SumPlusLaser"]!=null ? $r["SumPlusLaser"] : 0;
			$SumMinusLaser=$r["SumMinusLaser"]!=null ? (-1)*$r["SumMinusLaser"] : 0;
			$ItogLaser=$SumWorkLaser+$SumPlusLaser-$SumMinusLaser;
			$ItogAll+=$ItogLaser;
			
			$CountWorkSgibka=$r["CountWorkSgibka"]!=null ? $r["CountWorkSgibka"] : 0;
			$SumWorkSgibka=$r["SumWorkSgibka"]!=null ? $r["SumWorkSgibka"] : 0;
			$SumPlusSgibka=$r["SumPlusSgibka"]!=null ? $r["SumPlusSgibka"] : 0;
			$SumMinusSgibka=$r["SumMinusSgibka"]!=null ? (-1)*$r["SumMinusSgibka"] : 0;
			$ItogSgibka=$SumWorkSgibka+$SumPlusSgibka-$SumMinusSgibka;
			$ItogAll+=$ItogSgibka;
			
			$CountWorkSvarka=$r["CountWorkSvarka"]!=null ? $r["CountWorkSvarka"] : 0;
			$SumWorkSvarka=$r["SumWorkSvarka"]!=null ? $r["SumWorkSvarka"] : 0;
			$SumPlusSvarka=$r["SumPlusSvarka"]!=null ? $r["SumPlusSvarka"] : 0;
			$SumMinusSvarka=$r["SumMinusSvarka"]!=null ? (-1)*$r["SumMinusSvarka"] : 0;
			$ItogSvarka=$SumWorkSvarka+$SumPlusSvarka-$SumMinusSvarka;
			$ItogAll+=$ItogSvarka;
			//Рамка
			$CountWorkFrame=$r["CountWorkFrame"]!=null ? $r["CountWorkFrame"] : 0;
			$SumWorkFrame=$r["SumWorkFrame"]!=null ? $r["SumWorkFrame"] : 0;
			$SumPlusFrame=$r["SumPlusFrame"]!=null ? $r["SumPlusFrame"] : 0;
			$SumMinusFrame=$r["SumMinusFrame"]!=null ? (-1)*$r["SumMinusFrame"] : 0;
			$ItogFrame=$SumWorkFrame+$SumPlusFrame-$SumMinusFrame;
			$ItogAll+=$ItogFrame;
			//Сборка
			$CountWorkSborka=$r["CountWorkSborka"]!=null ? $r["CountWorkSborka"] : 0;
			$SumWorkSborka=$r["SumWorkSborka"]!=null ? $r["SumWorkSborka"] : 0;
			$SumPlusSborka=$r["SumPlusSborka"]!=null ? $r["SumPlusSborka"] : 0;
			$SumMinusSborka=$r["SumMinusSborka"]!=null ? (-1)*$r["SumMinusSborka"] : 0;
			$ItogSborka=$SumWorkSborka+$SumPlusSborka-$SumMinusSborka;
			$ItogAll+=$ItogSborka;
			//Малярка
			$CountWorkColor=$r["CountWorkColor"]!=null ? $r["CountWorkColor"] : 0;
			$SumWorkColor=$r["SumWorkColor"]!=null ? $r["SumWorkColor"] : 0;
			$SumPlusColor=$r["SumPlusColor"]!=null ? $r["SumPlusColor"] : 0;
			$SumMinusColor=$r["SumMinusColor"]!=null ? (-1)*$r["SumMinusColor"] : 0;
			$ItogColor=$SumWorkColor+$SumPlusColor-$SumMinusColor;
			$ItogAll+=$ItogColor;
			//Упаковка
			$CountWorkUpak=$r["CountWorkUpak"]!=null ? $r["CountWorkUpak"] : 0;
			$SumWorkUpak=$r["SumWorkUpak"]!=null ? $r["SumWorkUpak"] : 0;
			$SumPlusUpak=$r["SumPlusUpak"]!=null ? $r["SumPlusUpak"] : 0;
			$SumMinusUpak=$r["SumMinusUpak"]!=null ? (-1)*$r["SumMinusUpak"] : 0;
			$ItogUpak=$SumWorkUpak+$SumPlusUpak-$SumMinusUpak;
			$ItogAll+=$ItogUpak;
			//Отгрузка
			$CountWorkShpt=$r["CountWorkShpt"]!=null ? $r["CountWorkShpt"] : 0;
			$SumWorkShpt=$r["SumWorkShpt"]!=null ? $r["SumWorkShpt"] : 0;
			$SumPlusShpt=$r["SumPlusShpt"]!=null ? $r["SumPlusShpt"] : 0;
			$SumMinusShpt=$r["SumMinusShpt"]!=null ? (-1)*$r["SumMinusShpt"] : 0;
			$ItogShpt=$SumWorkShpt+$SumPlusShpt-$SumMinusShpt;
			$ItogAll+=$ItogShpt;
			//Мастер
			$CountWorkMaster=$r["CountWorkMaster"]!=null ? $r["CountWorkMaster"] : 0;
			$SumWorkMaster=$r["SumWorkMaster"]!=null ? $r["SumWorkMaster"] : 0;
			$SumPlusMaster=$r["SumPlusMaster"]!=null ? $r["SumPlusMaster"] : 0;
			$SumMinusMaster=$r["SumMinusMaster"]!=null ? (-1)*$r["SumMinusMaster"] : 0;
			$ItogMaster=$SumWorkMaster+$SumPlusMaster-$SumMinusMaster;
			$ItogAll+=$ItogMaster;
			//Не распределен
			$CountWorkOther=0;
			$SumWorkOther=0;
			$SumPlusOther=$r["SumPlusOther"]!=null ? $r["SumPlusOther"] : 0;
			$SumMinusOther=$r["SumMinusOther"]!=null ? (-1)*$r["SumMinusOther"] : 0;
			$ItogOther=$SumWorkOther+$SumPlusOther-$SumMinusOther;
			$ItogAll+=$ItogOther;
						
			$a=array(
				//Лазер
				"CountWorkLaser"=>$CountWorkLaser,
				"SumWorkLaser"=>$SumWorkLaser,
				"SumPlusLaser"=>$SumPlusLaser,
				"SumMinusLaser"=>$SumMinusLaser,
				"ItogLaser"=>$ItogLaser,
				//Сгибка
				"CountWorkSgibka"=>$CountWorkSgibka,
				"SumWorkSgibka"=>$SumWorkSgibka,
				"SumPlusSgibka"=>$SumPlusSgibka,
				"SumMinusSgibka"=>$SumMinusSgibka,
				"ItogSgibka"=>$ItogSgibka,
				//Сварка
				"CountWorkSvarka"=>$CountWorkSvarka,
				"SumWorkSvarka"=>$SumWorkSvarka,
				"SumPlusSvarka"=>$SumPlusSvarka,
				"SumMinusSvarka"=>$SumMinusSvarka,
				"ItogSvarka"=>$ItogSvarka,
				//Рамка
				"CountWorkFrame"=>$CountWorkFrame,
				"SumWorkFrame"=>$SumWorkFrame,
				"SumPlusFrame"=>$SumPlusFrame,
				"SumMinusFrame"=>$SumMinusFrame,
				"ItogFrame"=>$ItogFrame,
				//Сборка
				"CountWorkSborka"=>$CountWorkSborka,
				"SumWorkSborka"=>$SumWorkSborka,
				"SumPlusSborka"=>$SumPlusSborka,
				"SumMinusSborka"=>$SumMinusSborka,
				"ItogSborka"=>$ItogSborka,
				//Малярка
				"CountWorkColor"=>$CountWorkColor,
				"SumWorkColor"=>$SumWorkColor,
				"SumPlusColor"=>$SumPlusColor,
				"SumMinusColor"=>$SumMinusColor,
				"ItogColor"=>$ItogColor,
				//Упаковка
				"CountWorkUpak"=>$CountWorkUpak,
				"SumWorkUpak"=>$SumWorkUpak,
				"SumPlusUpak"=>$SumPlusUpak,
				"SumMinusUpak"=>$SumMinusUpak,
				"ItogUpak"=>$ItogUpak,
				//Отгрузка
				"CountWorkShpt"=>$CountWorkShpt,
				"SumWorkShpt"=>$SumWorkShpt,
				"SumPlusShpt"=>$SumPlusShpt,
				"SumMinusShpt"=>$SumMinusShpt,
				"ItogShpt"=>$ItogShpt,
				//Мастер
				"CountWorkMaster"=>$CountWorkMaster,
				"SumWorkMaster"=>$SumWorkMaster,
				"SumPlusMaster"=>$SumPlusMaster,
				"SumMinusMaster"=>$SumMinusMaster,
				"ItogMaster"=>$ItogMaster,
				//Не распределен
				"CountWorkOther"=>$CountWorkOther,
				"SumWorkOther"=>$SumWorkOther,
				"SumPlusOther"=>$SumPlusOther,
				"SumMinusOther"=>$SumMinusOther,
				"ItogOther"=>$ItogOther,

				"ItogAll"=>$ItogAll
			);*/
			echo json_encode($a);
		break;
	};
	$m->close();
?>