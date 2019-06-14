<?php
	ini_set("max_execution_time", "10000");
	$m=new mysqli('localhost','root','Rfhkcjy:bd2010','ent');
	/*
	for($i=0; $i<60;$i++)
	{
		$d=new DateTime();
		$d->setdate(2015,2,1);
		$d->add(new DateInterval("P".$i."D"));
		//echo "<br>".$d->format("d.m.Y");
		//echo "INSERT INTO oreders (Blank, BlankDate, Srok, Zakaz, Contact, status) VALUES((SELECT MAX(o.Blank)+1 FROM oreders o), STR_TO_DATE('".$d->format("d.m.Y")."' , '%d.%m.%Y'), '',  30, 'Заказчик ".$i." ', '', 2)";
		$ds=$m->query("SELECT MAX(o.Blank)+1 as m FROM oreders o");
		$r=$ds->fetch_assoc();
		$max=$r["m"];
		$ds->close();
		//echo "34";
		//echo "INSERT INTO oreders (Blank, BlankDate, Srok, Zakaz, Contact, status) VALUES(".$max.", STR_TO_DATE('".$d->format("d.m.Y")."' , '%d.%m.%Y'), '',  30, 'Заказчик ".$i." ', '', 2)";
		$m->query("INSERT INTO oreders (Blank, BlankDate, Srok, Zakaz, Contact, status) VALUES(".$max.", STR_TO_DATE('".$d->format("d.m.Y")."' , '%d.%m.%Y'),  30, 'Заказчик ".$i." ', '', 1)");
		if ($m->errno) 
			die('Select Error (' . $m->errno . ') ' . $m->error);
	};*/
	
	/*
	$d=$m->query("SELECT o.id FROM oreders o");
	while($r=$d->fetch_assoc())
		if($r["id"]>159)
	{
		for($i=1;$i<=50;$i++)
			$m->query("INSERT INTO orderdoors (idOrder, NumPP, name, H, W, Open, Nalichnik, Dovod, RAL, Count) VALUES (".$r["id"].", ".$i.", 'EI-60', 2300,1700,'Прав.', 'Да','да','белый', 10)");
	};*/
	
	$d=$m->query("SELECT o.NumPP, o.Count, o.id, DATE_FORMAT(o1.BlankDate, '%d.%m.%Y') AS dt FROM orderdoors o, oreders o1 WHERE o1.id=o.idOrder AND o.NumPP IS NOT NULL AND o.Count IS NOT NULL");
	while($r=$d->fetch_assoc())
	{
		for($i=1;$i<=(int)$r["Count"];$i++)
		{
			$m->query("INSERT INTO naryad (idDoors, Num, NumPP, LaserWork, LaserDate, LaserSum, SgibkaWork, SgibkaDate, SgibkaSum, SvarkaCompliteWork, SvarkaComplite, SvarkaSum, SborkaCompliteWork, SborkaComplite, SborkaSum, ColorCompliteWork, ColorComplite, ColorSum, UpakCompliteWork, UpakComplite, UpakSum, ShptCompliteWork, ShptComplite, ShptSum) VALUES ( ".
				$r["id"]." , ".
				$r["NumPP"]." , ".
				$i." , ".
				" 'Оператор' ,".
				"STR_TO_DATE('".$r["dt"]."','%d.%m.%Y') , ".
				"300 , ".
				" 'Александров Александр Александрович' ,".
				"STR_TO_DATE('".$r["dt"]."','%d.%m.%Y') , ".
				"400 , ".
				" 'Анатольев Анатолий Анатолиевич' ,".
				"STR_TO_DATE('".$r["dt"]."','%d.%m.%Y') , ".
				"500 , ".
				" 'Афанасьев Геннадий Петрович' ,".
				"STR_TO_DATE('".$r["dt"]."','%d.%m.%Y') , ".
				"600 , ".
				" 'Ветров Генадий Петрович' ,".
				"STR_TO_DATE('".$r["dt"]."','%d.%m.%Y') , ".
				"700 , ".
				" 'Иван Петрович Маляров' ,".
				"STR_TO_DATE('".$r["dt"]."','%d.%m.%Y') , ".
				"800 , ".
				" 'Иван Петрович Маляров' ,".
				"STR_TO_DATE('".$r["dt"]."','%d.%m.%Y') , ".
				"900 ".
				")"
			);
			if ($m->errno) 
			die('Select Error (' . $m->errno . ') ' . $m->error);
		};
			
	};
?>