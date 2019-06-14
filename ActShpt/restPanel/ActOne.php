<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 12.06.2019
 * Time: 1:22
 */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json');
session_start();

$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

$idAct=$_SESSION["idAct"];
$d=$m->query("SELECT * FROM ActShptList WHERE idAct=$idAct") or die($m->error);
$r=$d->fetch_assoc();
    $arr=array(
        "idAct"=>$r["idAct"],
        "ActNum"=>$r["ActNum"],
        "ShptDateStr"=>$r["ShptDateStr"],
        "Shet"=>$r["Shet"]==null ? "" : $r["Shet"],
        "OrgName"=>$r["OrgName"],
        "DoorCount"=>$r["DoorCount"],
        "Doors"=>array()
    );
$d->close();

$d=$m->query("SELECT d.id, CONCAT(n.Num,n.NumPP) AS NaryadNum, n.NumInOrder FROM Naryad n, actshptdoor d WHERE n.id=d.idNaryad AND d.idAct=$idAct") or die($m->error);
if($d->num_rows>0)
    while (($r=$d->fetch_assoc()))
        $arr["Doors"][]=array(
            "id"=>$r["id"],
            "NaryadNum"=>$r["NaryadNum"],
            "NumInOrder"=>$r["NumInOrder"]
        );
$d->close();
$m->close();
echo json_encode($arr);
?>