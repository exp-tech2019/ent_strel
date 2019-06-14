<?php
	$XMLParams=simplexml_load_file("../params.xml");
	$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
	switch ($_POST['Method'])
	{
		case "SelectManual":
			$d=$m->query("SELECT * FROM materialsmanual WHERE Type='".$_POST['Type']."'");
			$a=array();
			$i=0;
			if($d->num_rows>0)
				while($r=$d->fetch_assoc())
				{
					$a[$i]=array(
						"id"=>$r["id"],
						"Name"=>$r["Name"],
						"SI"=>$r["SI"]
					);
					$i++;
				};
			echo json_encode($a);
		break;
		case "AddMaterial":
			$er="ok";
			$d=$m->query("SELECT count(*) as c FROM stoсk WHERE idMaterial=".$_POST['idMaterial'] ) or die ($er="Ошибка SQL 1");
			$r=$d->fetch_assoc();
			switch($r["c"])
			{
				case 0:$m->query("INSERT INTO stoсk (idMaterial, Price, Count) VALUES (".$_POST["idMaterial"]." , ".$_POST["Price"]." , ".$_POST["Count"].")") or die ($er="Ошибка SQL 2");
				break;
				case 1: $m->query("UPDATE stoсk SET Price=".$_POST["Price"].", Count=Count+(".$_POST["Count"].") WHERE idMaterial=".$_POST["idMaterial"])  or die ($er="Ошибка SQL 3");
				break;
			}
			echo $er;
		break;
		
		case "SelectMaterils":
			$d=$m->query("SELECT * FROM stoсk s, materialsmanual m WHERE m.id=s.idMaterial AND ".$_POST["Where"]);
			$i=0;
			$a=array();
			while($r=$d->fetch_assoc())
			{
				$a[$i]=array(
					"Name"=>$r["Name"],
					"Price"=>$r["Price"],
					"SI"=>$r["SI"],
					"Count"=>$r["Count"]
				);
				$i++;
			};
			echo json_encode($a);
		break;
		
		//----------------------Справочники групп-----------------------------------------------------------
		case "ManualGroupAdd":
			$er="ok";
			$m->query("INSERT INTO manualmaterialgroup (Name , BlockDelete) VALUES ('".$_POST["Name"]."' , true)") or die($er="Ошибка SQL");
			echo $er;
		break;
		
		case "ManualGroupSelect":
			$d=$m->query("SELECT * FROM manualmaterialgroup ORDER BY Name");
			$a=array();
			$i=0;
			while($r=$d->fetch_assoc())
			{
				$a[$i]=array(
					"id"=>$r["id"],
					"Name"=>$r["Name"],
					"BlockDelete"=>$r["BlockDelete"]
				);
				$i++;
			};
			echo json_encode($a);
		break;
		
		case "ManualGroupEditStart":
			$d=$m->query("SELECT * FROM manualmaterialgroup WHERE id=".$_POST["id"]);
			$a=array();
			if($d->num_rows>0)
			{
				$r=$d->fetch_assoc();
				$a=array(
					"Name"=>$r["Name"],
					"BlockDelete"=>$r["BlockDelete"]
				);
			};
			echo json_encode($a);
		break;
		
		case "ManualGroupEdit":
			$er="ok";
			$m->query("UPDATE manualmaterialgroup SET Name='".$_POST["Name"]."' WHERE id=".$_POST["id"]) or die($er="Ошибка SQL");
			echo $er;
		break;
		
		case "ManualSelectGroup":
			$r=$m->query("SELECT * FROM manualmaterialgroup ORDER BY Name");
			$a=array(); $i=0;
			while($d=$r->fetch_assoc())
			{
				$a[$i]=array(
					"id"=>$d["id"],
					"Name"=>$d["Name"],
					"BlockDelete"=>$d["BlockDelete"]
				);
				$i++;
			};
			echo json_encode($a);
		break;
		
		case "ManualSelectMaterial":
			$r=$m->query("SELECT * FROM materialsmanual WHERE idGroup=".$_POST["GroupId"]." ORDER BY Name");
			$a=array(); $i=0;
			while($d=$r->fetch_assoc())
			{
				$a[$i]=array(
					"id"=>$d["id"],
					"Name"=>$d["Name"]
				);
				$i++;
			};
			echo json_encode($a);
		break;
		
		case "ManualAddGroup":
			$er="";
			$r=$m->query("SELECT MAX(id)+1 as NewId FROM manualmaterialgroup");
			$d=$r->fetch_assoc();
			$NewId=$d["NewId"];
			$r->close();
			$er=$NewId;
			$m->query("INSERT INTO manualmaterialgroup (id, Name, BlockDelete) VALUES (".$NewId.", '".$_POST["Name"]."', 0 )") or die ($er="Ошибка добавления записи");
			echo $er;
		break;
	};
?>