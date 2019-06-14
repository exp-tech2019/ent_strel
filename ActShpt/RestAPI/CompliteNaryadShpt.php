<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 25.04.2019
 * Time: 13:41
 */
session_start();
header('Content-Type: application/json');
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB)
or die('Не удалось соединиться: ' . $m->error);
$idWorker=$_POST["idWorker"];
$NaryadList=$_POST["NaryadList"];
$arr=array("Status"=>"Success");
$idNaryadStr="-1";
foreach ($NaryadList as $idNaryad)
    $idNaryadStr.=", ".$idNaryad;
$m->query("UPDATE NaryadComplite SET idWorker=$idWorker, DateComplite=Now() WHERE idNaryad IN ($idNaryadStr) AND Step=8");
$m->query("UPDATE Naryad SET ShptCompliteFlag=1 WHERE id IN ($idNaryadStr)");
$m->close();
echo json_encode($arr);
?>