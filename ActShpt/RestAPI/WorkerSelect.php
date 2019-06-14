<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 24.04.2019
 * Time: 14:38
 */
session_start();
header('Content-Type: application/json');
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB)
or die('Не удалось соединиться: ' . $m->error);
$arr=array();
$d=$m->query("SELECT w.id AS idWorker, w.FIO, m.Dolgnost FROM Workers w, ManualDolgnost m WHERE w.DolgnostID=m.id AND fired=0 ORDER BY w.FIO");
while ($r=$d->fetch_assoc())
    $arr[]=array(
        "idWorker"=>$r["idWorker"],
        "FIO"=>$r["FIO"],
        "Dolgnost"=>$r["Dolgnost"]
    );
$d->close();
$m->close();
echo json_encode($arr);
?>