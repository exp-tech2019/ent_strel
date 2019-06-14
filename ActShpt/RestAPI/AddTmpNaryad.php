<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 28.04.2019
 * Time: 0:51
 */
session_start();
header('Content-Type: application/json');
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB)
or die('Не удалось соединиться: ' . $m->error);
$idAct=$_POST["idAct"];
$idNaryadList=$_POST["idNaryadList"];
$idNaryadListStr="-1";
foreach ($idNaryadList as $n)
    $idNaryadListStr.=", ".$n;
$m->query("INSERT INTO actshptdoortmp (idAct, idNaryad) SELECT $idAct, id FROM Naryad WHERE id IN ($idNaryadListStr)") or die($m->error);
$arr=array("Status"=>"Success");
echo json_encode($arr);
?>