<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 22.07.2018
 * Time: 12:49
 */
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
ini_set("max_input_vars", "5000");
session_start();

$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

$TypeDoor=$_POST["TypeDoor"];
$d=$m->query("SELECT * FROM Sp18Construct WHERE TypeDoor='$TypeDoor'");
$arr=array();
while($r=$d->fetch_assoc())
    $arr[]=array(
        "idCalc"=>$r["id"],
        "idGroup"=>$r["idGroup"],
        "TypeCalc"=>$r["TypeCalc"],
        "Count"=>$r["Count"],
        "Save"=>$r["Save"]
    );
echo json_encode($arr);
?>