<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 26.03.2019
 * Time: 0:51
 */
session_start();
ini_set("max_execution_time", "2000");
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
header('Content-Type: application/json');

$idWorker=$_POST["idWorker"];
$DateWith=$_POST["DateWith"];
$DateBy=$_POST["DateBy"];
$d=$m->query("SELECT *, DATE_FORMAT(DatePayment, '%d.%m.%Y') AS DatePaymentStr FROM paymentsworkers WHERE idWorker=$idWorker AND DatePayment BETWEEN STR_TO_DATE('$DateWith','%d.%m.%Y') AND STR_TO_DATE('$DateBy','%d.%m.%Y') ORDER BY DatePayment") or die($m->error);
$arr=array();
while($r=$d->fetch_assoc())
    $arr[]=array(
        "id"=>$r["id"],
        "DatePayment"=>$r["DatePaymentStr"],
        "Sum"=>$r["Sum"],
        "Note"=>$r["Note"],
        "Accountant"=>$r["Accountant"]==null ? "" : $r["Accountant"],
        "Status"=>$r["Status"],
        "AlterAccountant"=>$r["AlterAccountant"]==null ? "" : $r["AlterAccountant"]
    );
$d->close();
echo json_encode($arr);
?>