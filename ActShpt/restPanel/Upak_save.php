<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 16.06.2019
 * Time: 1:47
 */
header('Content-Type: application/json');
session_start();
include "Params.php";

$idWorker=$_POST["idWorker"];
$Doors=$_POST["Doors"];
foreach ($Doors AS $d){
    $idNaryad=$d["idNaryad"];
    $m->query("UPDATE Naryad SET UpakCompliteFlag=1 WHERE id=$idNaryad") or die($m->error);
    $m->query("UPDATE NaryadComplite SET idWorker=$idWorker, DateComplite=NOW() WHERE Step=7 AND DateComplite IS NULL AND idNaryad=$idNaryad") or die($m->error);
};
echo json_encode(array("success"=>1));
?>