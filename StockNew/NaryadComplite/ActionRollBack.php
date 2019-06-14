<?php
    header('Access-Control-Allow-Origin: *');
    //header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $idDoor=isset($_GET["idDoor"]) ? $_GET["idDoor"] : null;
    //$Step=$_GET["Step"];
    $idNaryad=isset($_GET["idNaryad"]) ? $_GET["idNaryad"] : null;
    if(isset($_GET["idNaryad"])){
        $idNaryad=$_GET["idNaryad"];
        $d=$m->query("SELECT idDoors FROM Naryad WHERE id=$idNaryad");
        $idDoor=$d->fetch_assoc()["idDoors"];
    };
    $Step=isset($_GET["Step"]) ? $_GET["Step"] : null;

    //Узнаем кол-во дверей
    $CountDoor=1;
    if(isset($_GET["idDoor"])) {
        $d = $m->query("SELECT Count FROM OrderDoors WHERE id=$idDoor");
        $r = $d->fetch_assoc();
        $CountDoor = (int)$r["Count"];
        $d->close();
    };

    //Узнаем потребности  списания
    $d=$m->query("SELECT c.id AS idCommon, idGood, Count FROM spe_Common c, spe_Detail d, st_GoodGroups gr WHERE c.idDoor=$idDoor AND c.id=d.idCommon AND c.idGroup=gr.id AND gr.Step=6 ORDER BY c.id");
    $Spe=array(); $idCommon="";
    $idGoodsStr="-1";
    while($r=$d->fetch_assoc()){
        if($idCommon!=$r["idCommon"])
            $Spe[]=array(
                "idCommon"=>$r["idCommon"],
                "CountRollback"=>(float)$r["Count"]*$CountDoor,
                "Goods"=>array()
            );
        $Spe[count($Spe)-1]["Goods"][]=array(
            "idGood"=>$r["idGood"]
        );
        $idGoodsStr.=", ".$r["idGood"];
    };
    $d->close();

    //Определим что было списано
    $d=$m->query("SELECT * FROM st_NaryadComplite WHERE idDoor=$idDoor ".(isset($_GET["idNaryad"]) ? "AND idNaryad=$idNaryad " : "")."AND idGood IN ($idGoodsStr))");
    $arrNC=array();
    while($r=$d->fetch_assoc())
        $arrNC[]=array(
            "id"=>$r["id"],
            "idEnt"=>$r["idEnt"],
            "idGood"=>$r["idGood"],
            "Count"=>(float)$r["Count"]
        );
    $d->close();
    //Определим что было списано в тасках
    $d=$m->query("SELECT * FROM st_NaryadCompliteTasks WHERE idDoor=$idDoor ".(isset($_GET["idNaryad"]) ? "AND idNaryad=$idNaryad" : ""));
    $arrTasks=array();
    while($r=$d->fetch_assoc())
        $arrTasks[]=array(
            "id"=>$r["id"],
            "idCommon"=>$r["idCommon"],
            "Count"=>(float)$r["CountOld"]
        );
    $d->close();

    //Приступим к возврату
    foreach ($Spe as &$SpeOne) {
        foreach($SpeOne["Goods"] as $Detail)
            if($SpeOne["CountRollback"]>0){
                foreach ($arrNC as $NC) {
                    if ($Detail["idGood"] == $NC["idGood"])
                        if ($SpeOne["CountRollback"] > $NC["Count"]) {
                            $CountRolback = $NC["Count"];
                            $idEnt = $NC["idEnt"];
                            $idNC = $NC["id"];
                            $m->query("UPDATE st_StockEnt SET CountOld=CountOld+$CountRolback WHERE id=$idEnt") or die($m->error);
                            $m->query("DELETE FROM st_NaryadComplite WHERE id=$idNC") or die($m->error);
                            $SpeOne["CountRollback"] -= $CountRolback;
                        } else {
                            $CountRolback = $SpeOne["CountRollback"];
                            $idEnt = $NC["idEnt"];
                            $idNC = $NC["id"];
                            $m->query("UPDATE st_NaryadComplite SET Count=Count-$CountRolback WHERE id=$idNC");
                            $SpeOne["CountRollback"]=0;
                        };
                    if($SpeOne["CountRollback"]==0)
                        break;
                };
            }
            else
                break;

    };
    //Если не хватило кол-ва для возврата, тогда поищем его в тасках
    foreach ($Spe as &$SpeOne)
        if($SpeOne["CountRollback"]>0)
            foreach ($arrTasks as $Task)
                if($SpeOne["idCommon"]==$Task["Common"])
                    if($SpeOne["CountRollback"]>$Task["Count"]){
                        $idTask=$Task["id"];
                        $SpeOne["CountRollback"]-=$Task["Count"];
                        $m->query("DELETE FROM st_NaryadCompliteTasks WHERE id=$idTask") or die($m->error);
                    }else{
                        $idTask=$Task["id"];
                        $CountRolback=$SpeOne["CountRollback"];
                        $m->query("UPDATE st_NaryadCompliteTasks SET CountOld=CountOld-$CountRolback WHERE id=$idTask") or die($m->error);
                        $SpeOne["CountRollback"]=0;
                    };
/*
    //Определим требования для списния по данной позиции на данной стадии
    $idGoodStr="-1";
    $d=$m->query("SELECT d.id AS idDetail, d.idGood, c.Count FROM spe_Common c, spe_Detail d, st_Goods g, st_GoodGroups gr WHERE c.idDoor=7762 AND c.id=d.idCommon AND d.idGood=g.id AND g.idGroup=gr.id ".($Step!=null ? "AND gr.Step=$Step" : ""));
    $arrSpe=array();
    if($d->num_rows>0)
        while($r=$d->fetch_assoc()) {
            $arrSpe[] = array(
                "idDetail" => $r["idDetail"],
                "idGood" => $r["idGood"],
                "Count" => $r["Count"] * $CountDoor
            );
            $idGoodStr=$idGoodStr.", ".$r["idGood"];
        };
    $d->close();

    //Сформируем таблицу NaryadComplite
    $arrNaryadComplite=array();
    $d=$m->query("SELECT * FROM st_NaryadComplite WHERE idDoor=$idDoor AND idGood IN ($idGoodStr) ".($idNaryad!=null ? "AND idNaryad=".$idNaryad : ""));
    while ($r=$d->fetch_assoc())
        $arrNaryadComplite[]=array(
            "idNC"=>$r["id"],
            "idDetail"=>$r["idDetail"],
            "idGood"=>$r["idGood"],
            "idEnt"=>$r["idEnt"],
            "Count"=>$r["Count"]
        );
    $d->close();

    //Будем спосотовлять и возвращать на склад
    $m->autocommit(false);
    foreach ($arrSpe as $SpeOne) {
        $flagRollBack=false;
        $Count=$SpeOne["Count"];
        $idDetail=$SpeOne["idDetail"];
        $idGood=$SpeOne["idGood"];
        foreach ($arrNaryadComplite as $NCOne)
            if ($SpeOne["idDetail"] == $NCOne["idDetail"] & $SpeOne["idGood"] == $NCOne["idGood"] & $SpeOne["Count"] == $NCOne["Count"]) {
                $idNC = $NCOne["idNC"];
                $idEnt = $NCOne["idEnt"];
                $m->query("DELETE FROM st_NaryadComplite WHERE id=$idNC") or die($m->error);
                if($idEnt!=null)
                    $m->query("UPDATE st_StockEnt SET CountOld=CountOld+$Count WHERE id=$idEnt") or die($m->error);
                $flagRollBack=true;
                if($Step!=null)
                    break;
            };
        if(!$flagRollBack)
            $m->query("INSERT INTO st_NoRollBack (idDetail, idGood, Count) VALUES ($idDetail, $idGood, $Count) ") or die($m->error);
    };
    //Очистим ТАСКИ
*/
    $m->commit();
    echo json_encode(array("Result"=>"ok"));
?>