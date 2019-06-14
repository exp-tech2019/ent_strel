<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 12.06.2019
 * Time: 10:43
 */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json');
session_start();

$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

$BarCode=$_POST["BarCode"];
$Step=(int)$_POST["Step"];
$idNaryad=str_replace("E","", str_replace("N","",$BarCode));
$CompliteFlag=$Step==7 ? "UpakCompliteFlag" : "ShptCompliteFlag";
$d=$m->query("SELECT id, CONCAT(Num, NumPP) AS NaryadNum, NumInOrder FROM Naryad WHERE $CompliteFlag=0 AND id=$idNaryad");
$r=$d->fetch_assoc();
echo json_encode(array(
    "idNaryad"=>$r["id"],
    "NaryadNum"=>$r["NaryadNum"],
    "NumInOrder"=>$r["NumInOrder"]
));
?>