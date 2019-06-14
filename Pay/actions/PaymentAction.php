<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 01.03.2019
 * Time: 13:37
 */
session_start();
ini_set("max_execution_time", "2000");
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
header('Content-Type: application/json');

$result=array(
    "Status"=>"Success",
    "Note"=>""
);

$idPayment="";
$idWorker=$_POST["idWorker"];
$Action=$_POST["Action"];
$Date=$_POST["Date"];
$Sum=($Action=="Plus" ? "" : "-").$_POST["Sum"];
$Note=$_POST["Note"];
$Accountant=$_SESSION["AutorizeFIO"];
//Проверка
switch ($Sum=="" || $Sum=="-" || $Date=="" || $idWorker=="-1")
{
    case true:
        $result=array(
            "Status"=>"Error",
            "Note"=>"Есть не заполненные поля"
        );
        break;
    case false:
        $m->query("INSERT INTO paymentsworkers (idWorker, DatePayment, Sum, Note, Accountant, Status) VALUES($idWorker, STR_TO_DATE('$Date','%d.%m.%Y'), $Sum, '$Note', '$Accountant', 'Add')") or die($m->error);
        break;
};

echo json_encode($result);
?>