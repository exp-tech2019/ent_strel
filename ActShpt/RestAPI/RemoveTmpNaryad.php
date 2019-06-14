<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 03.05.2019
 * Time: 11:41
 */
session_start();
header('Content-Type: application/json');
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB)
or die('Не удалось соединиться: ' . $m->error);

$idAct=$_POST["idAct"];
$arrTmp=$_POST["arrTmp"];
$idTmp="-1";
foreach ($arrTmp as $tmp)
    $idTmp.=", ".$tmp;
$m->query("DELETE FROM ActShptDoorTmp WHERE id IN ($idTmp) AND idAct=$idAct") or die($m->error);

echo json_encode(array("Status"=>"Success"));
?>