<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 12.06.2019
 * Time: 0:32
 */


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json');
session_start();

$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
$d=$m->query("SELECT * FROM ActShptList WHERE Status=1 ORDER BY ShptDate");
$arr=array();
while($r=$d->fetch_assoc())
    $arr[]=array(
        "idAct"=>$r["idAct"],
        "ActNum"=>$r["ActNum"],
        "ShptDateStr"=>$r["ShptDateStr"],
        "Shet"=>$r["Shet"]==null ? "" : $r["Shet"],
        "OrgName"=>$r["OrgName"],
        "DoorCount"=>$r["DoorCount"]
    );
$d->close();
$m->close();
echo json_encode($arr);
?>