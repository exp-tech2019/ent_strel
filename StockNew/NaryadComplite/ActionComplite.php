<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $idDoor=isset($_GET["idDoor"]) ? $_GET["idDoor"] : null;
    $Step=$_GET["Step"];
    $idNaryad=isset($_GET["idNaryad"]) ? $_GET["idNaryad"] : "NULL";
    if(isset($_GET["idNaryad"])){
        $idNaryad=$_GET["idNaryad"];
        $d=$m->query("SELECT idDoors FROM Naryad WHERE id=$idNaryad");
        $idDoor=$d->fetch_assoc()["idDoors"];
    };
    //Узнаем кол-во дверей
    $d=$m->query("SELECT Count FROM OrderDoors WHERE id=$idDoor");
    $r=$d->fetch_assoc();
    $CountDoor=(int)$r["Count"];
    $d->close();
    //Формируем требования согласно спецификации
    $d=$m->query("SELECT c.id AS idCommon, d.id AS idDetail, d.idGood AS idGood, c.Count AS CommonCount FROM spe_Common c, spe_Detail d, st_GoodGroups g WHERE c.idDoor=$idDoor AND c.idGroup=g.id AND g.Step=$Step AND c.id=d.idCommon ORDER BY c.id, d.id");
    $Spe=array();
    $idGoodList="-1";
    $idCommon="";
    while ($r=$d->fetch_assoc()) {
        if ($idCommon != $r["idCommon"]) {
            $Spe[] = array(
                "idCommon" => $r["idCommon"],
                "CommonCount" => $r["CommonCount"],
                "Details"=>array()
            );
            $idCommon=$r["idCommon"];
        };
        $Spe[count($Spe)-1]["Details"][]=array(
            "idDetail"=>$r["idDetail"],
            "idGood"=>$r["idGood"]
        );
        $idGoodList=$idGoodList." ,".$r["idGood"];
    };
    $d->close();
    //Формируем список склада
    $d=$m->query("SELECT id AS idStock, idGood, Price, CountOld FROM st_StockEnt WHERE idGood IN ($idGoodList) AND CountOld>0");
    $Stock=array();
    while ($r=$d->fetch_assoc()){
        $Stock[]=array(
            "idStock"=>$r["idStock"],
            "idGood"=>$r["idGood"],
            "Price"=>$r["Price"],
            "CountOld"=>$r["CountOld"]
        );
    };
    $d->close();
    //Делаем списание
    $m->autocommit(false);
    foreach ($Spe as &$SpeOne){
        $CommonCount=(float)$SpeOne["CommonCount"];
        foreach ($SpeOne["Details"] as &$Detail) {
            $idDetail=$Detail["idDetail"];
            $idGood=$Detail["idGood"];
            foreach ($Stock as $StockOne)
                if ($Detail["idGood"] == $StockOne["idGood"] & (float)$StockOne["CountOld"]>0) {
                    $idStock=$StockOne["idStock"];
                    $Price=$StockOne["Price"];
                    $CountOld=(float)$StockOne["CountOld"];
                    $CountIssue=0;
                    switch ($CommonCount>$CountOld){
                        case true:
                            $m->query("UPDATE st_StockEnt SET CountOld=0 WHERE id=$idStock") or die($m->error);
                            $CountIssue=$CountOld;
                            break;
                        case false:
                            $m->query("UPDATE st_StockEnt SET CountOld=CountOld-$CommonCount WHERE id=$idStock") or die($m->error);
                            $CountIssue=$CommonCount;
                            break;
                    };
                    $m->query("INSERT INTO st_NaryadComplite (idDoor, idDetail, idGood, idEnt, Count, Price, idNaryad) VALUES($idDoor, $idDetail, $idGood, $idStock, $CountIssue, $Price, $idNaryad)") or die($m->error);
                    $CommonCount-=$CountIssue;
                    if($CommonCount==0)
                        break;
                };
            if($CommonCount==0)
                break;
        };
        //Добавим таски
        if($CommonCount>0)
            $m->query("INSERT INTO st_NaryadCompliteTasks (idCommon, CountIssue, CountOld, idNaryad) VALUES($idCommon, $CommonCount, $CommonCount, $idNaryad)") or die($m->error);
    }
    /*
    $DetailArr=array();
    $idGoodList="-1";
    if($d->num_rows>0)
        while($r=$d->fetch_assoc()) {
            $DetailArr[] = array(
                "idDetail" => $r["idDetail"],
                "idGood" => $r["idGood"],
                "CommonCount" => (float)$r["CommonCount"]*(!isset($_GET["idDoor"]) ? 1 : $CountDoor)
            );
            $idGoodList=$idGoodList." ,".$r["idGood"];
        };
    $d->close();
    //Составим список возможных списаний
    $d=$m->query("SELECT id AS idStock, idGood, Price, CountOld FROM st_StockEnt WHERE idGood IN ($idGoodList) AND CountOld>0");
    $StockArr=array();
    if($d->num_rows>0)
        while($r=$d->fetch_assoc())
            $StockArr[]=array(
                "idStock"=>$r["idStock"],
                "idGood"=>$r["idGood"],
                "Price"=>$r["Price"],
                "CountOld"=>$r["CountOld"]
            );
    //Произведем списание
    $m->autocommit(false);
    foreach($DetailArr as $DetailOne){
        $idDetail=$DetailOne["idDetail"];
        $idGood=$DetailOne["idGood"];
        $CommonCount=(float)$DetailOne["CommonCount"];
        foreach ($StockArr as $StockOne) {
            if ($DetailOne["idGood"] == $StockOne["idGood"]) {
                $CountCancellation = 0;
                $idStock=$StockOne["idStock"];
                $CountOld = (float)$StockOne["CountOld"];
                $Price=$StockOne["Price"];
                if ($CommonCount > $CountOld) {
                    $m->query("UPDATE st_StockEnt SET CountOld=0 WHERE id=$idStock") or die($m->error);
                    $CountCancellation=$CountOld;
                    $CommonCount-=$CountOld;
                }
                else{
                    $m->query("UPDATE st_StockEnt SET CountOld=CountOld-$CommonCount WHERE id=$idStock") or die($m->error);
                    $CountCancellation=$CommonCount;
                    $CommonCount=0;
                };
                $m->query("INSERT INTO st_NaryadComplite (idDoor, idDetail, idGood, idEnt, Count, Price) VALUES($idDoor, $idDetail, $idGood, $idStock, $CountCancellation, $Price)") or die($m->error);
                if($CommonCount==0) break;
            }
        };
        if($CommonCount>0)
            $m->query("INSERT INTO st_NaryadComplite (idDoor, idDetail, idGood, Count) VALUES ($idDoor, $idDetail, $idGood, $CommonCount)") or die($m->error);
    };*/
    $m->commit();

    echo json_encode(array("Result"=>"ok"));
?>