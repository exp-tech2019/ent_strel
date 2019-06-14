<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 12.06.2019
 * Time: 1:15
 */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json');
session_start();
$_SESSION["idAct"]=$_POST["idAct"];
echo json_encode(array("Success"=>1));
?>