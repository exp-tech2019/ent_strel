<?php
	$XMLParams=simplexml_load_file("../params.xml");
	$pdo = new PDO("mysql:host=".$XMLParams->ConnectDB->Host.";dbname=".$XMLParams->ConnectDB->DB, $XMLParams->ConnectDB->User, $XMLParams->ConnectDB->Pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	switch($_POST["Method"])
	{
		case "ListSelect":
			$s=$pdo->prepare("SELECT * FROM StockMaterialGroup ORDER BY Name");
			$s->execute();
			$a=array();$i=0;
			while($r=$s->fetch(PDO::FETCH_LAZY))
			{
				$a[$i]=array(
					"Name"=>$r->Name,
					"Delete"=>$r->Del
				);
				$i++;
			};
			echo json_encode($a);
		break;
		case "MaterialsTableSelect":
			$s=$pdo->prepare("SELECT * FROM stockmaterial WHERE idGroup=(SELECT id FROM stockmaterialgroup WHERE Name='".$_POST["GroupName"]."' LIMIT 1) ORDER BY Name");
			$s->execute();
			$a=array(); $i=0;
			while($r=$s->fetch(PDO::FETCH_LAZY))
			{
				$a[$i]=array(
					"id"=>$r->id,
					"idGroup"=>$r->idGroup,
					"Name"=>$r->Name,
					"Unit"=>$r->Unit,
					"Count"=>$r->Count,
					"Attn"=>$r->Attn,
				);
				$i++;
			};
			echo json_encode($a);
		break;
		
		//--------------------Работа с категориями------------------
		case "GroupAdd":
			$s=$pdo->prepare("INSERT INTO stockmaterialgroup (Name , Del) VALUES ('".$_POST["GroupName"]."' , 1)");
			$s->execute();
			echo "ok";
		break;
		case "GroupEdit":
			$s=$pdo->prepare("UPDATE stockmaterialgroup SET Name='".$_POST["NewName"]."' WHERE Name='".$_POST["OldName"]."'");
			$s->execute();
			echo "ok";
		break;
		case "GroupDelete":
			$s=$pdo->prepare("SELECT COUNT(*) FROM stockmaterial m, stockmaterialgroup gr WHERE gr.id=m.idGroup AND gr.Name='".$_POST["GroupName"]."'");
			$s->execute();
			$c=$s->fetchColumn();
			if((int)$c[0]==0)
			{
				$s=$pdo->prepare("DELETE FROM stockmaterialgroup WHERE Name='".$_POST["GroupName"]."'");
				$s->execute();
				echo "ok";
			}
			else echo "Удаление невозможно, в категории находтся материалы";
		break;
		
		//---------------Материал-----------------
		case "MaterialSave":
			if($_POST["id"]=="")
			{
				$s=$pdo->prepare("INSERT INTO stockmaterial ( idGroup, Name, Unit, Count, Attn) VALUES ".
					"( (SELECT m.id FROM stockmaterialgroup m WHERE m.Name=:CatName LIMIT 1), :Name, :Unit, :Count, :Attn)");
					$a=array(
						":CatName"=>(string)$_POST["CatName"],
						":Name"=>$_POST["Name"],
						":Unit"=>$_POST["Unit"],
						":Count"=>(float)$_POST["Count"],
						":Attn"=>(int)$_POST["Attn"]
					);
				try
				{
					$s->execute($a);
					echo $pdo->lastInsertId();
				}
				catch (PDOException  $e) {echo "error: ".$e->getMessage(); };
			}
			else//Произведем редактрирование
			{
				echo "2: ";
				$s=$pdo->prepare("UPDATE stockmaterial SET Name=:Name, Count=:Count, Unit=:Unit, Attn=:Attn WHERE id=".$_POST["id"]);
				$a=array(
						":Name"=>$_POST["Name"],
						":Unit"=>$_POST["Unit"],
						":Count"=>(float)$_POST["Count"],
						":Attn"=>(int)$_POST["Attn"]
					);
				try
				{
					$s->execute($a);
				}
				catch (PDOException  $e) {echo "error: ".$e->getMessage(); };
			};
		break;
		case "MaterialEditStart":
			$s=$pdo->prepare("SELECT Name, Unit, Count, Attn FROM stockmaterial WHERE id=".$_POST["id"]);
			$s->execute();
			$r=$s->fetch(PDO::FETCH_LAZY);
			$a=array(
				"Name"=>$r->Name,
				"Unit"=>$r->Unit,
				"Count"=>$r->Count,
				"Attn"=>$r->Attn
			);
			echo json_encode($a);
		break;
		case "MaterialDelete":
			$s=$pdo->prepare("DELETE FROM stockmaterial WHERE id=".$_POST["id"]);
			try
			{
				$s->execute();
				echo "ok";
			}
			catch (PDOException  $e) {echo "error: ".$e->getMessage(); };
		break;
		case "SupliersSelect":
			$s=$pdo->prepare("SELECT s.id, s.Name FROM stocksuppliers s ORDER BY s.Name");
			$s->execute();
			$a=array(); $i=0;
			while($r=$s->fetch(PDO::FETCH_LAZY))
			{
				$a[$i]=array(
					"id"=>$r->id,
					"Name"=>$r->Name
				);
				$i++;
			};
			echo json_encode($a);
		break;
		case "RecieptEditStart":
			$s=$pdo->prepare("SELECT s.id, s.idMaterial, s.Count, s.CountEnd, s.Price, s.Sum, sm.Name AS MaterialName, sm.Unit as MaterialUnit FROM stockrecieptmaterials s, stockmaterial sm WHERE s.idMaterial=sm.id AND s.idReciept=".$_POST["id"]." ORDER BY sm.Name");
			$s->execute();
			$aMaterials=array(); $i=0;
			while($r=$s->fetch(PDO::FETCH_LAZY))
			{
				$aMaterials[$i]=array(
					"id"=>$r->id,
					"idMaterial"=>$r->idMaterial,
					"Count"=>$r->Count,
					"CountEnd"=>$r->CountEnd,
					"Price"=>$r->Price,
					"Sum"=>$r->Sum,
					"MaterialName"=>$r->MaterialName,
					"MaterialUnit"=>$r->MaterialUnit
				);
				$i++;
			};
			$s=$pdo->prepare("SELECT s.id, s.Num, DATE_FORMAT(s.Date, '%d.%m.%Y') as Date, s.idSupplier, s.Note, s.Resposible, sp.Name AS SupplierName FROM stockreciept s, stocksuppliers sp WHERE s.idSupplier=sp.id AND s.id=".$_POST["id"]);
			$s->execute();
			$r=$s->fetch(PDO::FETCH_LAZY);
			$a=array(
				"id"=>$r->id,
				"Num"=>$r->Num,
				"Date"=>$r->Date,
				"idSupplier"=>$r->idSupplier,
				"Note"=>$r->Note,
				"Resposible"=>$r->Resposible,
				"SupplierName"=>$r->SupplierName,
				"Materials"=>$aMaterials
			);
			echo json_encode($a);
		break;
		//Сохранение/редактирование поступления материала
		case "RecieptSave":
			$er=false;
			$idReciept=null;
			if(isset($_POST["idReciept"])) $idReciept=$_POST["idReciept"];
			$pdo->beginTransaction();
			//Если id поступления не заполненно тогда это insert
			//if($idReciept==null)
			if($_POST["idReciept"]=="")
			{
				$s=$pdo->prepare(
					"INSERT INTO stockreciept ( Num, Date, idSupplier, Note, Resposible) VALUES (".
						":Num, STR_TO_DATE(:Date , '%d.%m.%Y'), :SuplierID, :Note, :Resposible".
					")"
				);
				session_start();
				$a=array(
					":Num"=>$_POST["Num"],
					":Date"=>$_POST["Date"],
					":SuplierID"=>$_POST["SuplierID"],
					":Note"=>$_POST["Note"],
					":Resposible"=>$_SESSION["AutorizeFIO"]
				);
				try
				{
					$s->execute($a);
					$idReciept=$pdo->lastInsertId();
				}
				catch (PDOException  $e) {echo "error: ".$e->getMessage(); $er=true; };
			}
			else
			{
				$s=$pdo->prepare("UPDATE stockreciept s SET s.Num=:Num, s.Date=STR_TO_DATE(:Date,'%d.%m.%Y'), s.idSupplier=:idSupplier, s.Note=:Note WHERE s.id=:id");
				$a=array(
					":id"=>(int)$_POST["idReciept"],
					":Num"=>(int)$_POST["Num"],
					":Date"=>$_POST["Date"],
					":idSupplier"=>(int)$_POST["SuplierID"],
					":Note"=>$_POST["Note"]
				);
				try
				{
					$s->execute($a);
					$idReciept=$_POST["idReciept"];
				}
				catch (PDOException  $e) {echo "error: ".$e->getMessage(); $er=true; };
			};
			//Обработаем строки
			$idRow=$_POST["idRow"];
			$idMaterial=$_POST["idMaterial"];
			$Status=$_POST["Status"];
			$Count=$_POST["Count"];
			$Price=$_POST["Prise"];
			$Sum=$_POST["Sum"];
			$i=0;
			if($idReciept!=null & !$er)
			{
				while(isset($Status[$i]) & !$er)
				{
					switch($Status[$i])
					{
						case "Add":
							$s=$pdo->prepare("INSERT INTO stockrecieptmaterials ( idReciept, idMaterial, Count, CountEnd, Price, Sum) VALUES (".
								":idReciept , :idMaterial, :Count, :CountEnd, :Price, :Sum ".
							")");
							$a=array(
								":idReciept"=>$idReciept,
								":idMaterial"=>$idMaterial[$i],
								":Count"=>$Count[$i],
								":CountEnd"=>$Count[$i],
								":Price"=>$Price[$i],
								":Sum"=>$Sum[$i]
							);
							try
							{
								$s->execute($a);
							}
							catch (PDOException $e) {echo "error: ".$e->getMessage(); $er=true; };
						break;
						case "Edit":
							$s=$pdo->prepare("UPDATE stockrecieptmaterials SET idMaterial=:idMaterial, Count=:Count, CountEnd=:CountEnd, Price=:Price, Sum=:Sum WHERE id=:id");
							$a=array(
								":id"=>$idRow[$i],
								":idMaterial"=>$idMaterial[$i],
								":Count"=>$Count[$i],
								":CountEnd"=>$Count[$i],
								":Price"=>$Price[$i],
								":Sum"=>$Sum[$i]
							);
							try
							{
								$s->execute($a);
							}
							catch (PDOException $e) {echo "error: ".$e->getMessage(); $er=true; };
						break;
						case "Del":
							$s=$pdo->prepare("DELETE FROM stockrecieptmaterials WHERE id=".$idRow[$i]);
							try
							{
								$s->execute();
							}
							catch (PDOException $e) {echo "error: ".$e->getMessage(); $er=true; };
						break;
					};
					$i++;
				};
			}
			else $er=true;
			
			//Завершим транзакцию
			if($er) {$pdo->rollBack(); } else $pdo->commit();
		break;
		//Определяем максимальный номер прихода
		case "RecieptMaxNum":
			$MaxNum=1;
			try
			{
				$s=$pdo->prepare("SELECT MAX(s.Num)+1 as MaxNum FROM stockreciept s");
				$s->execute();
				$r=$s->fetch(PDO::FETCH_LAZY);
				$MaxNum=$r["MaxNum"];
			} catch (PDOException $e) {};
			echo $MaxNum;
		break;
		//Отобразим список двежения
		case "TradeSelect":
			$s=$pdo->prepare("CALL StockSelectTrade ('".$_POST["DateWith"]."', '".$_POST["DateBy"]."') ");
			$s->execute(); $a=array(); $i=0;
			while($r=$s->fetch(PDO::FETCH_LAZY))
			{
				$a[$i]=array(
					"id"=>$r->id,
					"Num"=>$r->Num,
					"Date"=>$r->Date,
					"Supplier"=>$r->Supplier,
					"Sum"=>$r->Sum,
					"Count"=>$r->Count,
					"Naryad"=>$r->Naryad,
					"TypeTrade"=>$r->TypeTrade
				);
				$i++;
			};
			echo json_encode($a);
		break;
		case "TradeMaterialsSelect":
			//В заваимисти от типа: поступление или списание
			switch($_POST["TypeTrade"])
			{
				case "R":
					$s=$pdo->prepare("SELECT s.Count, s.CountEnd, s.Price, s.Sum, sm.Name, sm.Unit FROM stockrecieptmaterials s, stockmaterial sm WHERE s.idMaterial=sm.id AND s.idReciept=".$_POST["idReciept"]." ORDER BY sm.Name");
					$s->execute(); $a=array(); $i=0;
					while($r=$s->fetch(PDO::FETCH_LAZY))
					{
						$a[$i]=array(
							"Name"=>$r->Name,
							"Unit"=>$r->Unit,
							"Count"=>$r->Count,
							"CountEnd"=>$r->CountEnd,
							"Price"=>$r->Price,
							"Sum"=>$r->Sum
						);
						$i++;
					};
					echo json_encode($a);
				break;
			};
		break;
	};
?>