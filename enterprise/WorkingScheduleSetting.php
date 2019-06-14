<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");

    $XMLParams=simplexml_load_file("../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
    switch($_POST["Action"]){
        case "Load":
            //Для начала выполним запрос добавлющий вновь появившиеся должности
            $m->query("INSERT INTO manualschedulecost (idDolgnost, Cost) SELECT id, 0 FROM ManualDolgnost WHERE Algorithm='H'
ON DUPLICATE KEY UPDATE Cost=Cost") or die($m->error);
            //Сформируем список
            $d=$m->query("SELECT sh.id, d.Dolgnost, sh.Cost FROM ManualScheduleCost sh, ManualDolgnost d WHERE sh.idDolgnost=d.id ORDER BY d.Dolgnost");
            $arr=array(); $i=0;
            if($d->num_rows>0)
                while($r=$d->fetch_assoc())
                    $arr[$i++]=array(
                        "id"=>$r["id"],
                        "Dolgnost"=>$r["Dolgnost"],
                        "Cost"=>$r["Cost"]
                    );
            echo json_encode($arr);
            break;
        case "Save":
            $m->autocommit(false);
            $idManualArr=$_POST["idManualArr"];
            $CostArr=$_POST["CostArr"];
            for($i=0; $i<count($idManualArr);$i++){
                $idManual=$idManualArr[$i];
                $Cost=$CostArr[$i];
                $m->query("UPDATE ManualScheduleCost SET Cost=$Cost WHERE id=$idManual") or die($m->error);
            };
            $m->commit();
            echo json_encode(array("Result"=>"ok"));
            break;
    }
?>