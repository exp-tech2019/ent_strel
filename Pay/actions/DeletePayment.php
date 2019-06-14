<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 22.03.2019
 * Time: 2:41
 */
session_start();
ini_set("max_execution_time", "2000");
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

$Accountant=$_SESSION["AutorizeFIO"];
$idPayment=$_POST["idPayment"];
//$m->query("DELETE FROM paymentsworkers WHERE id=$idPayment") or die($m->error);
$m->query("UPDATE paymentsworkers SET Status='Remove', AlterAccountant='$Accountant' WHERE id=$idPayment") or die($m->error);
?>