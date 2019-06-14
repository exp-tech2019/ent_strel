<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 01.05.2019
 * Time: 23:39
 */
session_start();
header('Content-Type: application/json');
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB)
or die('Не удалось соединиться: ' . $m->error);
$OrgList=array();
$OrgName=$_POST["OrgName"];
$d=$m->query("SELECT zakaz FROM oreders WHERE zakaz LIKE '%$OrgName%' GROUP BY zakaz ORDER BY zakaz ");
while ($r=$d->fetch_assoc())
    $OrgList[]=$r["zakaz"];
$d->close();
$m->close();

echo json_encode($OrgList);
?>