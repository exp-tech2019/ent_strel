<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    switch($_POST["Action"]){
        case "Save":
            $m->autocommit(false);
            $Goods=$_POST["Goods"];

            //--Произведем создание Акта передачи
            $idAct=$_POST["idAct"];
            $idLogin=$_POST["idLogin"];
            $idWorker=$_POST["idWorker"];
            if($idAct=="")
            {
                $m->query("INSERT INTO st_ActInEnt (DateCreate, idLogin, idWorker) VALUES(NOW(), $idLogin, $idWorker)") or die($m->error);
                $idAct=$m->insert_id;
            };

            for($i=0; $i<count($Goods); $i++){
                $m->query("INSERT INTO st_ActInEntGoods (idAct, idGood, Count) VALUES($idAct, " . $Goods[$i]["idGood"] . ", " . $Goods[$i]["Count"] . ")") or die($m->error);
                //Наполним массив ссылками на позицию передачи
                $Goods[$i]["idInEnt"]=$m->insert_id;
            };
            //--Организация перемещения на производство
            //Составим список номеклатуры под списание для SQL запроса
            //что бы не выводить все товары
            $GoodList="-1";
            foreach($Goods as $Good)
                $GoodList=$GoodList.", ".$Good["idGood"];
            //Список на складе
            $d=$m->query("SELECT id, idGood, Price, CountOld FROM st_StockMain WHERE idGood IN ($GoodList) AND CountOld>0");
            $StockMain=array(); $c=0;
            while($r=$d->fetch_assoc()){
                $StockMain[$c]=array(
                    "id"=>$r["id"],
                    "idGood"=>$r["idGood"],
                    "CountOld"=>$r["CountOld"],
                    "Price"=>$r["Price"]
                );
                $c++;
            };
            $d->close();
            //Непосредственно перемещение
            foreach($Goods as $Good) {
                $idInEnt=$Good["idInEnt"];
                $CountOld=(float)$Good["Count"];
                foreach ($StockMain as $StockOne)
                    if ($StockOne["idGood"] == $Good["idGood"]) {
                        $CountUnset=0;
                        if($CountOld>(float)$StockOne["CountOld"]){
                            $CountUnset=(float)$StockOne["CountOld"];
                        }
                        else
                            $CountUnset=$CountOld;
                        $m->query("UPDATE st_StockMain SET CountOld=CountOld-$CountUnset WHERE id=".$StockOne["id"]) or die ($m->error);
                        $m->query("INSERT INTO st_StockEnt (idStock, idGood, Price, Count, CountOld, idInEnt) VALUES(".$StockOne["id"].", ".$StockOne["idGood"].", ".$StockOne["Price"].", $CountUnset, $CountUnset, $idInEnt)") or die($m->error);
                        $CountOld-=$CountUnset;
                        if($CountOld==0)
                            break;
                    };
            };
            $m->commit();
            echo json_encode(array("Result"=>"ok"));
            break;
        case "View":
            $idAct=$_POST["idAct"];
            $arr=array();
            $d=$m->query("SELECT DATE_FORMAT(a.DateCreate,'%d.%m.%Y') AS DateCreate, l.FIO AS LoginFIO, w.FIO AS WorkerFIO FROM st_ActInEnt a, Logins l, Workers w WHERE a.idLogin=l.id AND a.idWorker=w.id AND a.id=$idAct");
            $r=$d->fetch_assoc();
            $arr["DateCreate"]=$r["DateCreate"];
            $arr["LoginFIO"]=$r["LoginFIO"];
            $arr["WorkerFIO"]=$r["WorkerFIO"];
            $d->close();

            $d=$m->query("SELECT g.GoodName, a.Count FROM st_ActInEntGoods a, st_Goods g WHERE a.idGood=g.id AND a.id=$idAct");
            $arrGoods=array(); $i=0;
            while($r=$d->fetch_assoc())
                $arrGoods[$i++]=array(
                    "GoodName"=>$r["GoodName"],
                    "Count"=>$r["Count"]
                );
            $d->close();
            $arr["Goods"]=$arrGoods;
            echo json_encode($arr);
            break;
        case "Remove":
            $idAct=$_POST["idAct"];
            $flagNotRemove=false;
            $d=$m->query("SELECT COUNT(*) AS Count FROM st_ActInEntGoods a, st_stockent s, st_NaryadComplite nc WHERE a.id=s.idInEnt AND s.id=nc.idEnt AND idAct=$idAct");
            $r=$d->fetch_assoc();
            $d->close();
            if((int)$r["Count"]==0){
                $m->autocommit(false);
                $m->query("UPDATE st_StockMain m 
	INNER JOIN
	(SELECT e.idStock, e.Count FROM st_ActInEntGoods a, st_StockEnt e WHERE a.idAct=$idAct AND a.id=e.idInEnt) t1
    ON m.id=t1.idStock
SET m.CountOld=m.CountOld+t1.Count") or die ($m->error);
                $m->query("DELETE FROM st_stockent WHERE idInEnt IN (SELECT id FROM st_ActInEntGoods WHERE idAct=$idAct)") or die($m->error);
                $m->query("DELETE FROM st_ActInEntGoods WHERE idAct=$idAct") or die($m->error);
                $m->query("DELETE FROM st_ActInEnt WHERE id=$idAct") or die($m->error);
                $m->commit();
                echo json_encode(array("Result"=>"ok"));
            }else
                echo json_encode(array("Result"=>"NotRemove"));

            break;
        case "SelectWorkerID":
            $SmartCard=$_POST["SmartCard"];
            $d=$m->query("SELECT * FROM Workers WHERE fired=0 AND SmartCartNum='$SmartCard'");
            if($d->num_rows>0){
                $r=$d->fetch_assoc();
                echo json_encode(array(
                    "idWorker"=>$r["id"],
                    "FIO"=>$r["FIO"]
                ));
            }
            break;
    }
?>