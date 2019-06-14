<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 22.07.2018
 * Time: 13:21
 */
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
ini_set("max_input_vars", "5000");
session_start();

$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

$TypeDoor=$_POST["TypeDoor"];
$Rows=$_POST["Rows"];
foreach ($Rows as $r) {
    $idCalc=$r["idCalc"];
    $idGroup=$r["idGroup"];
    $TypeCalc=$r["TypeCalc"];
    $Count=str_replace(",",".",$r["Count"]);
    $Save=$r["Save"];
    switch ($r["Status"]) {
        case "Add":
            $m->query("INSERT INTO Sp18Construct (TypeDoor, idGroup, TypeCalc, Count, Save) VALUES('$TypeDoor', $idGroup, $TypeCalc, $Count, $Save)");
            break;
        case "Edit":
            $m->query("UPDATE Sp18Construct SET TypeDoor='$TypeDoor', idGroup=$idGroup, TypeCalc=$TypeCalc, Count=$Count, Save=$Save WHERE id=$idCalc");
            break;
    };
};
echo json_encode(array("Status"=>"Success"));
?>