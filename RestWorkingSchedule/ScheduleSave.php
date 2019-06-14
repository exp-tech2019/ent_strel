<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);
    $m->autocommit(false);

    $idDolgnost=$_POST["idDolgnost"];
    $DateWhere=$_POST["DateWhere"];
    $ChangeWorkerID=$_POST["ChangeWorkerID"];
    $ChangeHourCount=$_POST["ChangeHourCount"];

    for($i=0; $i<count($ChangeHourCount); $i++){
        $idWorker=$ChangeWorkerID[$i];
        $CountHour=$ChangeHourCount[$i];
        $m->query("INSERT INTO WorkingSchedule (idWorker, DatePayment, CountHour, Cost) VALUES ($idWorker, STR_TO_DATE('$DateWhere', '%d.%m.%Y'), $CountHour, coalesce((SELECT ms.Cost FROM ManualScheduleCost ms WHERE ms.idDolgnost=$idDolgnost),0)) 
ON DUPLICATE KEY UPDATE CountHour=$CountHour") or die($m->error);
    };
    $m->commit();
    echo json_encode(array("Result"=>"ok"));
?>