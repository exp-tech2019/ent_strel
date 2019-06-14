<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $idGood=$_POST["idGood"];
    $d=$m->query("SELECT COALESCE(SUM(CountOld),0) AS CountOld FROM st_StockMain WHERE idGood=$idGood AND CountOld>0 GROUP BY idGood");
    $CountOld=0;
    if($d->num_rows>0){
        $r=$d->fetch_assoc();
        $CountOld=(float)$r["CountOld"];
    };
    echo json_encode(array("CountOld"=>$CountOld));
?>