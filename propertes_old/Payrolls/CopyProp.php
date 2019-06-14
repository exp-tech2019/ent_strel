<?php
/**
 * Created by PhpStorm.
 * User: anikulshin
 * Date: 16.01.2019
 * Time: 13:18
 */
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
ini_set("max_input_vars", "5000");
session_start();

$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

$OldDoorType=$_POST["OldDoorType"];
$NewDoorType=$_POST["NewDoorType"];
//Удалим для начала старые расчеты
$m->query("DELETE FROM payrolldoorsize_new WHERE DoorType='$NewDoorType'");
$m->query("DELETE FROM payrollconstant WHERE DoorType='$NewDoorType'");
$m->query("DELETE FROM payrollconstruct WHERE DoorType='$NewDoorType'");
//Перенос
$m->query("INSERT INTO payrolldoorsize_new (DoorType, Step, HWith, HBy, WWith, WBy, S, SWith, SBy, Framug, Sum) SELECT '$NewDoorType', Step, HWith, HBy, WWith, WBy, S, SWith, SBy, Framug, Sum FROM payrolldoorsize_new WHERE DoorType='$OldDoorType'") or die($m->error);
$m->query("INSERT INTO payrollconstant (DoorType, Step, Name, Sum) SELECT '$NewDoorType', Step, Name, Sum FROM payrollconstant WHERE DoorType='$OldDoorType'") or die($m->error);
$d=$m->query("SHOW COLUMNS FROM payrollconstruct");
$sConstruct="";
while ($r=$d->fetch_assoc())
    if(strpos($r["Field"],"id")===false & strpos($r["Field"], 'DoorType')===false)
        $sConstruct.=", ".$r["Field"];
$d->close();
echo "INSERT INTO payrollconstruct (DoorType $sConstruct ) SELECT '$NewDoorType' $sConstruct FROM payrollconstruct WHERE DoorType='$OldDoorType'";
$m->query("INSERT INTO payrollconstruct (DoorType $sConstruct ) SELECT '$NewDoorType' $sConstruct FROM payrollconstruct WHERE DoorType='$OldDoorType'") or die($m->error);
?>