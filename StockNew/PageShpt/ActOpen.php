<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $idAct=$_POST["idAct"];
    $d=$m->query("SELECT o.Blank, o.Shet, DATE_FORMAT(a.DateCreate,'%d.%m.%Y') AS DateCreate, l.FIO AS ManagerFIO, DATE_FORMAT(a.DateShpt,'%d.%m.%Y') AS DateShpt FROM st_ActShpt a, Oreders o, Logins l WHERE a.idOrder=o.id AND a.idManager=l.id AND a.id=$idAct");
    $r=$d->fetch_assoc();
    $Blank=$r["Blank"];
    $Shet=$r["Shet"];
    $DateCreate=$r["DateCreate"];
    $ManagerFIO=$r["ManagerFIO"];
    $DateShpt=$r["DateShpt"]!=null ? $r["DateShpt"] : "";
    $d->close();

    $d=$m->query("SELECT a.id AS idActNaryad, n.idDoors AS idDoor, a.idNaryad, n.NumInOrder, CONCAT(n.Num,n.NumPP) AS NaryadNum FROM st_ActShptNaryads a, Naryad n WHERE a.idAct=$idAct AND a.idNaryad=n.id ORDER BY n.NumInOrder");
    $arrNaryads=array();
    $arrDoorID=array();
    $DoorIDStr="-1";
    while($r=$d->fetch_assoc()){
        $arrNaryads[]=array(
            "idActNaryad"=>$r["idActNaryad"],
            "idDoor"=>$r["idDoor"],
            "idNaryad"=>$r["idNaryad"],
            "NumInOrder"=>$r["NumInOrder"],
            "NaryadNum"=>$r["NaryadNum"]
        );
        $idDoor=$r["idDoor"];
        $flagDoor=true;
        foreach ($arrDoorID as &$DoorID)
            if($DoorID["idDoor"]==$idDoor) {
                $flagDoor = false;
                $DoorID["Count"]++;
            };
        if($flagDoor) {
            $arrDoorID[] = array(
                "idDoor" => $idDoor,
                "Count" => 1
            );
            $DoorIDStr=$DoorIDStr." ,".$idDoor;
        };
    };
    $d->close();

    //--Формируем массив для списания
    $arrCommon=array();
    $CommonIDStr="-1";
    $d=$m->query("SELECT c.id AS idCommon, c.idDoor, c.idGroup, gr.GroupName, c.Count FROM spe_Common c, st_GoodGroups gr WHERE c.idDoor IN ($DoorIDStr) AND c.idGroup=gr.id AND gr.Step=0 AND gr.AutoUnset=0");
    while($r=$d->fetch_assoc()) {
        $arrCommon[] = array(
            "idCommon" => $r["idCommon"],
            "idDoor" => $r["idDoor"],
            "idGroup" => $r["idGroup"],
            "GroupName" => $r["GroupName"],
            "Count" => (float)$r["Count"],
            "Detail"=>array()
        );
        $CommonIDStr=$CommonIDStr.", ".$r["idCommon"];
    };
    $d->close();
    //Установим кол-во для списания на весь акт для каждой группы
    foreach ($arrCommon as &$CommonOne)
        foreach ($arrDoorID as $DoorID)
            if($CommonOne["idDoor"]==$DoorID["idDoor"]){
                $CommonOne["Count"]=$CommonOne["Count"]*$DoorID["Count"];
                break;
            };
    //Добавим возможные материалы для списания
    $GoodIDStr="-1";
    $d=$m->query("SELECT d.id AS idDetail, d.idCommon, d.idGood, g.GoodName FROM spe_Detail d, st_Goods g WHERE d.idCommon IN ($CommonIDStr) AND d.idGood=g.id");
    while($r=$d->fetch_assoc())
        foreach ($arrCommon as &$CommonOne)
            if($CommonOne["idCommon"]==$r["idCommon"]){
                $CommonOne["Detail"][]=array(
                    "idDetail"=>$r["idDetail"],
                    "idGood"=>$r["idGood"],
                    "GoodName"=>$r["GoodName"],
                    "CountStockMain"=>0
                );
                $GoodIDStr=$GoodIDStr.", ".$r["idGood"];
                //break;
            };
    $d->close();

    //Установим кол-во товара на скалде
    $d=$m->query("SELECT idGood, SUM(CountOld) AS CountOld FROM st_StockMain WHERE idGood IN($GoodIDStr) AND CountOld>0 GROUP BY idGood");
    while($r=$d->fetch_assoc())
        foreach ($arrCommon as &$CommonOne) {
            $flagFind = false;
            foreach ($CommonOne["Detail"] as &$DetailOne)
                if ($DetailOne["idGood"] == $r["idGood"]) {
                    $DetailOne["CountStockMain"] = $r["CountOld"];
                    $flagFind = true;
                    break;
                };
            if($flagFind) break;
        };

    echo json_encode(array(
        "idAct"=>$idAct,
        "Blank"=>$Blank,
        "Shet"=>$Shet,
        "DateCreate"=>$DateCreate,
        "ManagerFIO"=>$ManagerFIO,
        "DateShpt"=>$DateShpt,
        "Naryads"=>$arrNaryads,
        "Common"=>$arrCommon
    ))
?>