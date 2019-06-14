<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    session_start();
    $XMLParams=simplexml_load_file("../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    switch($_POST["Action"]){
        case "LoadManuals":
            //Загрузим список типов дверей
            $d=$m->query("SELECT * FROM manualtypedoors");
            $arrTypeDoors=array();
            while($r=$d->fetch_assoc())
                $arrTypeDoors[]=$r["Name"];
            $d->close();
            //Загрузим список групп
            $d=$m->query("SELECT * FROM st_GoodGroups ORDER BY GroupName");
            $arrGroups=array();
            while($r=$d->fetch_assoc())
                $arrGroups[]=array(
                    "idGroup"=>$r["id"],
                    "GroupName"=>$r["GroupName"]
                );
            $d->close();
            echo json_encode(array("TypeDoors"=>$arrTypeDoors, "Groups"=>$arrGroups));
            break;
        case "Save":
            $idSpe=$_POST["idSpe"];
            $TypeDoor=$_POST["TypeDoor"];
            $m->autocommit(false);
            if($idSpe=="") {
                $m->query("INSERT INTO spe_ConstructTypeDoors (TypeDoor) VALUES('$TypeDoor')") or die($m->error);
                $idSpe=$m->insert_id;
            };
            $arrSpe=$_POST["arrSpe"];
            foreach($arrSpe as $SpeOne) {
                $idConstruct=$SpeOne["idConstruct"];
                $idGroup=$SpeOne["idGroup"];
                $TypeCalc=$SpeOne["TypeCalc"];
                $Count=$SpeOne["Count"];
                $Petlya=$SpeOne["Petlya"];
                switch ($SpeOne["Status"]) {
                    case "Add":
                        $m->query("INSERT INTO spe_ConstructGroups (idTypeDoor, idGroup, TypeCalc, Count, Petlya) VALUES($idSpe, $idGroup, $TypeCalc, $Count, $Petlya)") or die($m->error);
                        break;
                    case "Edit":
                        $m->query("UPDATE spe_ConstructGroups SET idGroup=$idGroup, TypeCalc=$TypeCalc, Count=$Count, Petlya=$Petlya WHERE id=$idConstruct") or die($m->error);
                        break;
                    case "Remove":
                        $m->query("DELETE FROM spe_ConstructGroups WHERE id=$idConstruct") or die($m->error);
                        break;
                };
            };
            echo json_encode(array("Result"=>"Ok"));
            $m->commit();
            break;
        case "LoadSpe":
            $TypeDoor=$_POST["TypeDoor"];
            $arrSpe=array();
            $d=$m->query("SELECT gr.*, grm.GroupName FROM spe_ConstructTypeDoors d, spe_ConstructGroups gr, st_GoodGroups grm WHERE d.TypeDoor='$TypeDoor' AND d.id=gr.idTypeDoor AND grm.id=gr.idGroup");
            if($d->num_rows>0)
                while($r=$d->fetch_assoc())
                    $arrSpe[]=array(
                        "idConstruct"=>$r["id"],
                        "idTypeDoor"=>$r["idTypeDoor"],
                        "idGroup"=>$r["idGroup"],
                        "GroupName"=>$r["GroupName"],
                        "TypeCalc"=>$r["TypeCalc"],
                        "Count"=>$r["Count"],
                        "Petlya"=>$r["Petlya"]
                    );
            echo json_encode($arrSpe);
            break;
    };
    $m->close();
?>