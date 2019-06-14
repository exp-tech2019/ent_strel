<?php
	$XMLParams=simplexml_load_file("../params.xml");
	$pdo = new PDO("mysql:host=".$XMLParams->ConnectDB->Host.";dbname=".$XMLParams->ConnectDB->DB, $XMLParams->ConnectDB->User, $XMLParams->ConnectDB->Pass);
	switch($_POST["Method"])
	{
		case "SelectList":
			$s=$pdo->prepare("SELECT s.id, s.Name, s.AdressFact, s.Phone, '' AS Mail, 0 AS CountTrend FROM stocksuppliers s ORDER BY s.Name");
			$s->execute();
			$a=array(); $i=0;
			while($r=$s->fetch(PDO::FETCH_LAZY))
			{
				$a[$i]=array(
					"id"=>$r["id"],
					"Name"=>$r["Name"],
					"AdressFact"=>$r["AdressFact"],
					"Phone"=>$r["Phone"],
					"Mail"=>$r["Mail"],
					"CountTrend"=>$r["CountTrend"]
				);
				$i++;
			};
			echo json_encode($a);
		break;
		
		case "Save":
			switch($_POST["Action"])
			{
				case "Add":
					try
					{
						$pdo->beginTransaction();
						$params=array(
							":Name"=>$_POST["Name"],
							":LegalForm"=>$_POST["LegalForm"],
							":FullName"=>$_POST["FullName"],
							":inn"=>$_POST["inn"],
							":kpp"=>$_POST["kpp"],
							":okpo"=>$_POST["okpo"],
							
							":BankName"=>$_POST["BankName"],
							":BankRS"=>$_POST["BankRS"],
							":BankKS"=>$_POST["BankKS"],
							":BankBik"=>$_POST["BankBik"],
							":AdressFact"=>$_POST["AdressFact"],
							":AdressUrid"=>$_POST["AdressUrid"],
							":Phone"=>$_POST["Phone"],
							":Fax"=>$_POST["Fax"],
							":Note"=>$_POST["Note"]
						);
						$s= $pdo->prepare("INSERT INTO stocksuppliers ".
							"(Name, LegalForm, FullName, inn, kpp, okpo, BankName, BankRS, BankKS, BankBik, AdressFact, AdressUrid, Phone, Fax, Note)".
							" VALUES (:Name, :LegalForm, :FullName, :inn, :kpp, :okpo, :BankName, :BankRS, :BankKS, :BankBik, :AdressFact, :AdressUrid, :Phone, :Fax, :Note)"
						);
						$s->execute($params);
						
						$s=$pdo->prepare("SELECT MAX(id) as idSupplier FROM stocksuppliers ");
						$s->execute();
						$r=$s->fetchColumn();
						$idSupplier=$r[0];
						if(isset($_POST["aContact"]))
						$aContact=$_POST["aContact"]; $i=0;
						while(isset($aContact[$i]))
						{
							$a=array(
								"idSupplier"=>$idSupplier,
								"FIO"=>$aContact[$i]["FIO"],
								"Post"=>$aContact[$i]["Post"],
								"Phone"=>$aContact[$i]["Phone"],
								"Phone1"=>$aContact[$i]["Phone1"],
								"Mail"=>$aContact[$i]["Mail"],
								"Note"=>$aContact[$i]["Note"]
							);
							$s=$pdo->prepare("INSERT INTO stocksupplierscontacts ".
								"(idSupplier, FIO, Post, Phone, Phone1, Mail, Note)".
								"VALUES (:idSupplier, :FIO, :Post, :Phone, :Phone1, :Mail, :Note)"
							);
							$s->execute($a);
							$i++;
						};
						
						$pdo->commit();
					}
					catch (Exception $e){$pdo->rollBack(); echo $e->getMessage();};
				break;
				case "Edit":
					try
					{
						$pdo->beginTransaction();
						$params=array(
							":Name"=>$_POST["Name"],
							":LegalForm"=>$_POST["LegalForm"],
							":FullName"=>$_POST["FullName"],
							":inn"=>$_POST["inn"],
							":kpp"=>$_POST["kpp"],
							":okpo"=>$_POST["okpo"],
							
							":BankName"=>$_POST["BankName"],
							":BankRS"=>$_POST["BankRS"],
							":BankKS"=>$_POST["BankKS"],
							":BankBik"=>$_POST["BankBik"],
							":AdressFact"=>$_POST["AdressFact"],
							":AdressUrid"=>$_POST["AdressUrid"],
							":Phone"=>$_POST["Phone"],
							":Fax"=>$_POST["Fax"],
							":Note"=>$_POST["Note"]
						);
						$s= $pdo->prepare("UPDATE stocksuppliers SET ".
							"Name=:Name, LegalForm=:LegalForm, FullName=:FullName, inn=:inn, kpp=:kpp, okpo=:okpo, BankName=:BankName, BankRS=:BankRS, BankKS=:BankKS, BankBik=:BankBik, AdressFact=:AdressFact, AdressUrid=:AdressUrid, Phone=:Phone, Fax=:Fax, Note=:Note ".
							"WHERE id=".$_POST["id"]
						);
						$s->execute($params);
						
						$s=$pdo->prepare("DELETE FROM stocksupplierscontacts WHERE idSupplier=".$_POST["id"]);
						$s->execute();
						
						if(isset($_POST["aContact"]))
						$aContact=$_POST["aContact"]; $i=0;
						while(isset($aContact[$i]))
						{
							$a=array(
								"idSupplier"=>$_POST["id"],
								"FIO"=>$aContact[$i]["FIO"],
								"Post"=>$aContact[$i]["Post"],
								"Phone"=>$aContact[$i]["Phone"],
								"Phone1"=>$aContact[$i]["Phone1"],
								"Mail"=>$aContact[$i]["Mail"],
								"Note"=>$aContact[$i]["Note"]
							);
							$s=$pdo->prepare("INSERT INTO stocksupplierscontacts ".
								"(idSupplier, FIO, Post, Phone, Phone1, Mail, Note)".
								"VALUES (:idSupplier, :FIO, :Post, :Phone, :Phone1, :Mail, :Note)"
							);
							$s->execute($a);
							$i++;
						};
						
						$pdo->commit();
					}
					catch (Exception $e){$pdo->rollBack(); echo $e->getMessage();};
				break;
			};
		break;
		case "EditStart":
			$s=$pdo->prepare("SELECT * FROM stocksupplierscontacts WHERE idSupplier=".$_POST["id"]);
			$s->execute();
			$aContact=array(); $i=0;
			while($r=$s->fetch(PDO::FETCH_LAZY))
			{
				$aContact[$i]=array(
					"FIO"=>$r->FIO,
					"Post"=>$r->Post,
					"Phone"=>$r->Phone,
					"Phone1"=>$r->Phone1,
					"Mail"=>$r->Mail,
					"Note"=>$r->Note
				);
				$i++;
			};
			$s=$pdo->prepare("SELECT * FROM stocksuppliers WHERE id=".$_POST["id"]);
			$s->execute();
			//$r=$s->fetchColumn();
			$r=$s->fetch(PDO::FETCH_LAZY);
			$a=array(
				"id"=>$r["id"],
				"Name"=>$r["Name"],
				"LegalForm"=>$r["LegalForm"],
				"FullName"=>$r["FullName"],
				"inn"=>$r["inn"],
				"kpp"=>$r["kpp"],
				"okpo"=>$r["okpo"],
				"BankName"=>$r["BankName"],
				"BankRS"=>$r["BankRS"],
				"BankKS"=>$r["BankKS"],
				"BankBik"=>$r["BankBik"],
				"AdressFact"=>$r["AdressFact"],
				"AdressUrid"=>$r["AdressUrid"],
				"Phone"=>$r["Phone"],
				"Fax"=>$r["Fax"],
				"Note"=>$r["Note"],
				"Contacts"=>$aContact
			);
			echo json_encode($a);
		break;
		
		case "Delete":
			try
			{
				$pdo->beginTransaction();
				$s=$pdo->prepare("DELETE FROM stocksupplierscontacts WHERE idSupplier=".$_POST["id"]);
				$s->execute();
				$s=$pdo->prepare("DELETE FROM stocksuppliers WHERE id=".$_POST["id"]);
				$s->execute();
				$pdo->commit();
				echo "ok";
			}
			catch (Exception $e){$pdo->rollBack(); echo $e->getMessage();}
		break;
	};
	
	//echo $aContact[0]["Post"];
?>
