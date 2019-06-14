<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 25.04.2019
 * Time: 23:27
 */
session_start();
header('Content-Type: application/json');
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB)
or die('Не удалось соединиться: ' . $m->error);
$arr=array("Status","Success");
$idAct=$_POST["idAct"];
$idNaryadList=$_POST["idNaryadList"];

echo json_encode($arr);
?>