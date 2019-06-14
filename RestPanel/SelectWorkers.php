<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    include "param.php";
    $param=new GlobalParam();
    $XMLParams=simplexml_load_file("../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $StepWhere="1=1";
    switch($_GET["StepWhere"]){
        case "Svarka": $StepWhere="DolgnostID=2 OR DolgnostID=3"; break;
        case "Frame": $StepWhere="DolgnostID=2 OR DolgnostID=3 OR DolgnostID=15"; break;
        case "Sborka": $StepWhere="DolgnostID=2 OR DolgnostID=4"; break;
        case "Color": $StepWhere="DolgnostID=2 OR DolgnostID=6 OR DolgnostID=11"; break;
        case "Upak": $StepWhere="DolgnostID=2 OR DolgnostID=7 OR DolgnostID=12"; break;
        case "Mdf": $StepWhere="DolgnostID=2 OR DolgnostID=16"; break;
        case "MdfSborka": $StepWhere="DolgnostID=2 OR DolgnostID=4 OR DolgnostID=17"; break;
        case "Shpt": $StepWhere="DolgnostID=2 OR DolgnostID=7 OR DolgnostID=12"; break;
    };
    $d=$m->query("SELECT id, FIO FROM Workers WHERE $StepWhere ORDER BY FIO");
    $WorkerList=array(); $i=0;
    if($d->num_rows>0)
        while($r=$d->fetch_assoc()){
            $WorkerList[$i]=array(
                "id"=>$r["id"],
                "FIO"=>$r["FIO"]
            );
            $i++;
        };
    echo json_encode($WorkerList);
?>