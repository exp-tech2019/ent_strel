<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);
    $m->autocommit(false);

    $idDolgnostArr=$_POST["idDolgnostArr"];
    $DateWhereArr=$_POST["DateWhereArr"];
    $idWorkerArr=$_POST["idWorkerArr"];
    $CountHourArr=$_POST["CountHourArr"];
    for($i=0; $i<count($idDolgnostArr); $i++){
        $idWorker=$idWorkerArr[$i];
        $idDolgnost=$idDolgnostArr[$i];
        $DateWhere=$DateWhereArr[$i];
        $CountHour=$CountHourArr[$i];
        $m->query("INSERT INTO WorkingSchedule (idWorker, DatePayment, CountHour, Cost) VALUES ($idWorker, STR_TO_DATE('$DateWhere', '%d.%m.%Y'), $CountHour, coalesce((SELECT ms.Cost FROM ManualScheduleCost ms WHERE ms.idDolgnost=$idDolgnost),0)) 
    ON DUPLICATE KEY UPDATE CountHour=$CountHour") or die($m->error);
    };
    $m->commit();
    echo json_encode(array("Result"=>"ok"));
?>