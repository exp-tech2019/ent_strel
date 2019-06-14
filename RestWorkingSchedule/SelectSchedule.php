<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $ActLastCreate=null;
    $d=$m->query("SELECT MAX(DateCreate) AS DateCreate FROM TempPayrolls") or die($m->error);
    if($d->num_rows>0){
        $r=$d->fetch_assoc();
        $ActLastCreate=$r["DateCreate"];
    };

    $idDolgnost=$_GET["idDolgnost"];
    $DateWhere=$_GET["DateWhere"];
    $d=$m->query("SELECT tWorker.idWorker, tWorker.FIO, coalesce(ws.CountHour,0) AS CountHour, coalesce(ws.Cost,0) AS Cost FROM
	(SELECT w.id AS idWorker, w.FIO FROM Workers w WHERE w.DolgnostID=$idDolgnost) tWorker
LEFT JOIN 
	(SELECT idWorker, CountHour, Cost FROM WorkingSchedule WHERE DatePayment=STR_TO_DATE('$DateWhere','%d.%m.%Y')) ws
ON tWorker.idWorker=ws.idWorker
ORDER BY tWorker.FIO") or die($m->error);
    $ScheduleList=array(); $i=0;
    if($d->num_rows>0)
        while($r=$d->fetch_assoc()){
            $ScheduleList[$i]=array(
                "idWorker"=>$r["idWorker"],
                "FIO"=>$r["FIO"],
                "CountHour"=>$r["CountHour"],
                "Cost"=>$r["Cost"]
            );
            $i++;
        };
    echo json_encode($ScheduleList);
?>