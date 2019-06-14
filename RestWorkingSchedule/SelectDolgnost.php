<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    /*include "../param.php";
    $param=new GlobalParam();*/
    $XMLParams=simplexml_load_file("../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $SelectMonth=$_GET["SelectMonth"];
    $SelectYear=$_GET["SelectYear"];
    $d=$m->query("SELECT tDolgnost.id, tDolgnost.Dolgnost, COALESCE(tSh.CountHourSum,0) AS CountHourAll, COALESCE(tSh.CostSum,0) AS CostSum FROM
(SELECT * FROM ManualDolgnost WHERE Algorithm='H' ORDER BY Dolgnost) tDolgnost
LEFT JOIN 
	(SELECT w.DolgnostID, SUM(ws.CountHour) AS CountHourSum, SUM(ws.Cost) AS CostSum FROM workers w, workingschedule ws WHERE w.id=ws.idWorker AND MONTH(ws.DatePayment)=$SelectMonth AND YEAR(ws.DatePayment)=$SelectYear GROUP BY w.DolgnostID) tSh
ON tDolgnost.id=tSh.DolgnostID
ORDER BY tDolgnost.Dolgnost");
    $DolgnostList=array(); $i=0;
    while($r=$d->fetch_assoc()){
        $DolgnostList[$i]=array(
            "idDolgnost"=>$r["id"],
            "Dolgnost"=>$r["Dolgnost"],
            "CountHourAll"=>$r["CountHourAll"],
            "CostSum"=>$r["CostSum"]
        );
        $i++;
    };
    echo json_encode($DolgnostList);
?>