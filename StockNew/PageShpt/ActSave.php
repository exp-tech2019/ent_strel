<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $Result=array("Result"=>"ok");
    $m->autocommit(false);

    $idAct=$_POST["idAct"];
    $arrCommon=$_POST["arrCommon"];
    session_start();
    $idLogin=$_SESSION["AuthorizeID"];

    //Сформируем требование на списание
    $idGoodStr="-1";
    $arrRequest=array();
    foreach($arrCommon as $CommonOne){
        $idDoor=$CommonOne["idDoor"];
        $arrDetail=$CommonOne["arrDetail"];
        foreach ($arrDetail as $DetailOne) {
            $arrRequest[] = array(
                "idDoor" => $idDoor,
                "idDetail" => $DetailOne["idDetail"],
                "idGood" => $DetailOne["idGood"],
                "CountIssue" => $DetailOne["CountIssue"]
            );
            $idGoodStr=$idGoodStr.", ".$DetailOne["idGood"];
        };
    };

    //Перевод с основного склада в производство
    //--Сначала составим список возожных позиций для списания
    $d=$m->query("SELECT * FROM st_StockMain WHERE idGood IN ($idGoodStr) AND CountOld>0");
    $arrMain=array();
    while($r=$d->fetch_assoc())
        $arrMain[]=array(
            "idMain"=>$r["id"],
            "idGood"=>$r["idGood"],
            "Price"=>$r["Price"],
            "CountOld"=>$r["CountOld"]
        );
    $d->close();
    //--Произведем перемещение на производство
    $arrNaryadComplite=array();
    foreach ($arrRequest as $RequestOne) {
        $CountIssue=(float)$RequestOne["CountIssue"];
        foreach ($arrMain as $MainOne)
            if ($RequestOne["idGood"] == $MainOne["idGood"]) {
                $idGood = $RequestOne["idGood"];
                $CountOld = $MainOne["CountOld"];
                $idMain = $MainOne["idMain"];
                $Price = $MainOne["Price"];
                $CountInNC=0;
                $idEnt="";
                if ($CountIssue > (float)$MainOne["CountOld"]) {
                    $m->query("UPDATE st_StockMain SET CountOld=0 WHERE id=$idMain") or die($m->error);
                    $m->query("INSERT INTO st_StockEnt (idStock, idGood, Price, Count, CountOld, idLogin) VALUES($idMain, $idGood, $Price, $CountOld,0,$idLogin)") or die($m->error);
                    $idEnt=$m->insert_id;
                    $m->query("INSERT INTO st_ActShpt_tr1 (idAct, idMain, idEnt) VALUES($idAct,$idMain,$idEnt)");
                    $CountIssue-=(float)$MainOne["CountOld"];
                    $CountInNC=(float)$MainOne["CountOld"];
                }
                else{
                    $m->query("UPDATE st_StockMain SET CountOld=CountOld-$CountIssue WHERE id=$idMain") or die($m->error);
                    $m->query("INSERT INTO st_StockEnt (idStock, idGood, Price, Count, CountOld, idLogin) VALUES($idMain, $idGood, $Price, $CountIssue,0,$idLogin)") or die($m->error);
                    $idEnt=$m->insert_id;
                    $m->query("INSERT INTO st_ActShpt_tr1 (idAct, idMain, idEnt) VALUES($idAct,$idMain,$idEnt)");
                    $CountInNC=$CountIssue;
                    $CountIssue=0;
                };

                $arrNaryadComplite[]=array(
                    "idDoor"=>$RequestOne["idDoor"],
                    "idDetail"=>$RequestOne["idDetail"],
                    "idGood"=>$RequestOne["idGood"],
                    "idEnt"=>$idEnt,
                    "Price"=>$Price,
                    "Count"=>$CountInNC
                );

                if($CountIssue==0)
                    break;
            };
    };
    //--Перенесем из производства в NaryadComplite
    foreach ($arrNaryadComplite as $NCOne){
        $idDoor=$NCOne["idDoor"];
        $idDetail=$NCOne["idDetail"];
        $idGood=$NCOne["idGood"];
        $idEnt=$NCOne["idEnt"];
        $Price=$NCOne["Price"];
        $Count=$NCOne["Count"];
        $m->query("INSERT INTO st_NaryadComplite (idDoor,idDetail,idGood,idEnt,Price,Count) VALUES($idDoor,$idDetail,$idGood,$idEnt,$Price, $Count)") or die($m->error);
        $idNC=$m->insert_id;
        $m->query("INSERT INTO st_ActShpt_tr2 (idAct, idEnt,idNC) VALUES($idAct, $idEnt,$idNC)") or die($m->error);
    };

    //Завершим форирование акта
    $m->query("UPDATE st_ActShpt SET DateShpt=NOW(), idStockManager=$idLogin WHERE id=$idAct") or die($m->error);

    $m->commit();
?>