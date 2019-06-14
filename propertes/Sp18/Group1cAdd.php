<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 21.07.2018
 * Time: 22:49
 */
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
ini_set("max_input_vars", "5000");
session_start();

$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
$idGroup=$_POST["idGroup"];
$idGroup1c=$_POST["idGroup1c"];
$m->query("INSERT INTO Sp18Group_1c (idGroup, idGroup1c) VALUES($idGroup, $idGroup1c)");
?>