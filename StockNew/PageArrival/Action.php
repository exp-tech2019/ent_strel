<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $Action=$_POST["Action"];
    switch($Action){
        case "Save":
            $arr=$_POST["JSON"];
            $EditFlag=$arr["idArrival"]=="" ? false : true;
            $idArrival=$arr["idArrival"];
            $idSupplier=$arr["idSupplier"];
            $DateArrival=$arr["DateArrival"];
            $NumArrival=$arr["NumArrival"];

            $m->autocommit(false);
            switch ($arr["idArrival"]){
                case "":
                    $m->query("INSERT INTO st_Arrival (idSupplier, DateArrival, NumArrival, Note) VALUES($idSupplier, STR_TO_DATE('$DateArrival', '%d.%m.%Y'), '$NumArrival', '')") or die($m->error);
                    $idArrival=$m->insert_id;
                    break;
                default:
                    $m->query("UPDATE st_Arrival SET idSupplier=$idSupplier, DateArrival=STR_TO_DATE('$DateArrival','%d.%m.%Y'), NumArrival='$NumArrival' WHERE id=$idArrival") or die($m->error);
                    break;
            };

            if($EditFlag)
                $m->query("DELETE FROM st_ArrivalGoods WHERE idArrival=$idArrival") or die($m->error);
            foreach ($arr["Goods"] as $Good){
                $GoodText=$Good["GoodText"];
                $idGood=$Good["GoodManual"];
                $Count=$Good["Count"];
                $Price=$Good["Price"];
                $m->query("INSERT INTO st_ArrivalGoods (idArrival, TextGood, idGood, Count, Price) VALUES($idArrival, '$GoodText', $idGood, $Count, $Price)") or die($m->error);
            };/*
            for($i=1; $i<=count($arr["Goods"]); $i++){
                $Good=$arr["Goods"][$i];
                $GoodText=$Good["GoodText"];
                $idGood=$Good["GoodManual"];
                $Count=$Good["Count"];
                $Price=$Good["Price"];
                $m->query("INSERT INTO st_ArrivalGoods (idArrival, TextGood, idGood, Count, Price) VALUES($idArrival, '$GoodText', $idGood, $Count, $Price)") or die($m->error);
            };*/
            //Перенесем поступления в основной скалд
            $m->query("INSERT INTO st_StockMain (idArrival, idArrivalGood, idGood, Price, Count, CountOld) SELECT $idArrival, a.id, a.idGood, a.Price, a.Count, IF(gr.AutoUnset=0,a.Count,0) FROM st_ArrivalGoods a, st_Goods g, st_GoodGroups gr WHERE idArrival=$idArrival AND a.idGood=g.id AND g.idGroup=gr.id") or die($m->error);
            $m->query("INSERT INTO st_StockEnt (idStock, idGood, Price, Count, CountOld) SELECT s.id, s.idGood, s.Price, s.Count, s.Count FROM st_StockMain s, st_Goods g, st_GoodGroups gr WHERE s.idArrival=$idArrival AND s.idGood=g.id AND g.idGroup=gr.id AND gr.AutoUnset=1") or die($m->error);

            $m->commit();
            echo json_encode(array("Result"=>"ok"));
            break;
        case "EditStart":
            $idArrival=$_POST["idArrival"];
            $a=array();
            //echo "SELECT a.*, DATE_FORMAT(a.DateArrival,'%d.%m.%Y') AS DateArrivalStr, s.SupplierName  FROM st_Arrival a, st_Suppliers s WHERE id=$idArrival AND a.idSupplier=s.id --- ";
            $d=$m->query("SELECT a.*, DATE_FORMAT(a.DateArrival,'%d.%m.%Y') AS DateArrivalStr, s.SupplierName  FROM st_Arrival a, st_Suppliers s WHERE a.id=$idArrival AND a.idSupplier=s.id") or die($m->error);
            $r=$d->fetch_assoc();
            $a["id"]=$r["id"];
            $a["NumArrival"]=$r["NumArrival"];
            $a["DateArrival"]=$r["DateArrivalStr"];
            $a["idSupplier"]=$r["idSupplier"];
            $a["TextSupplier"]=$r["SupplierName"];

            $idGoods="-1";
            $Goods=array(); $i=0;
            $d=$m->query("SELECT ag.*,g.GoodName FROM st_ArrivalGoods ag, st_Goods g WHERE ag.idArrival=$idArrival AND ag.idGood=g.id");
            if($d->num_rows>0)
                while($r=$d->fetch_assoc()){
                    $Goods[$i]=array(
                        "id"=>$r["id"],
                        "TextGood"=>$r["TextGood"],
                        "idGood"=>$r["idGood"],
                        "GoodName"=>$r["GoodName"],
                        "Count"=>$r["Count"],
                        "Price"=>$r["Price"]
                    );
                    $i++;
                };
            $a["Goods"]=$Goods;
            //Проверим данное поступление не использовалось
            $flagNotEdit=false;
            $GoodsOtherCountArr=array();
            $GoodsOtherCountStr="-1";
            $d=$m->query("SELECT id FROM st_StockMain m WHERE m.idArrival=$idArrival AND m.Count>m.CountOld");
            if($d->num_rows>0)
                while($r=$d->fetch_assoc()){
                    $GoodsOtherCountArr[]=$r["id"];
                    $GoodsOtherCountStr=$GoodsOtherCountStr.", ".$r["id"];
                };
            $d->close();
            $d=$m->query("SELECT * FROM st_StockEnt WHERE idStock IN ($GoodsOtherCountStr)");
            $GoodsOtherCountArr1=array();
            if($d->num_rows>0)
                while($r=$d->fetch_assoc())
                    $GoodsOtherCountArr1[]=array(
                        "idMain"=>$r["idStock"],
                        "Count"=>$r["Count"],
                        "CountOld"=>$r["CountOld"]
                    );
            foreach ($GoodsOtherCountArr as $Main) {
                $flagFind=false;
                foreach ($GoodsOtherCountArr1 as $item)
                    if($Main==$item["idMain"]){
                        if($item["Count"]!=$item["CountOld"])
                            $flagNotEdit=true;
                        $flagFind=true;
                    };
                if(!$flagFind || $flagNotEdit){
                    $flagNotEdit=true;
                    break;
                }
            };
            $a["FlagNoteEdit"]=$flagNotEdit ? 1 : 0;
            echo json_encode($a);
            break;
        case "Remove":
            $idArrival=$_POST["idArrival"];
            $m->autocommit(false);
            $m->query("DELETE FROM st_StockEnt WHERE idStock IN (SELECT m.id FROM st_StockMain m WHERE m.idArrival=$idArrival)") or die($m->error);
            $m->query("DELETE FROM st_StockMain WHERE idArrival=$idArrival") or die ($m->error);
            $m->query("DELETE FROM st_ArrivalGoods WHERE idArrival=$idArrival") or die($m->error);
            $m->query("DELETE FROM st_Arrival WHERE id=$idArrival") or die($m->error);
            $m->commit();
            echo json_encode(array("Result"=>"ok"));
            break;
    };
    $m->close();
?>