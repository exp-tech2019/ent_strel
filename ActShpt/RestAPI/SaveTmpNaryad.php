<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 02.05.2019
 * Time: 20:02
 */
session_start();
header('Content-Type: application/json');
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB)
or die('Не удалось соединиться: ' . $m->error);
$idAct=$_POST["idAct"];
$NaryaList=$_POST["NaryadList"];
//$m->query("DELETE FROM ActShptDoorTmp WHERE idAct=$idAct") or die($m->error);
foreach ($NaryaList as $n)
    $m->query("INSERT INTO ActShptDoorTmp (idAct, idNaryad) VALUES ($idAct, $n)") or die($m->error);
echo json_encode(array("Status"=>"Success"));
?>