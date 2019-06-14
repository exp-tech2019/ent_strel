<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 11.04.2019
 * Time: 3:23
 */
header('Access-Control-Allow-Origin: *');
ini_set("max_input_vars", "5000");
session_start();

$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
foreach ($_POST["arrDolgnost"] as $dolgnost) {
    $idDolgnost=$dolgnost["idDolgnost"];
    $action=$dolgnost["Action"];
    $m->query("UPDATE manualdolgnost SET AlgorithmCalc='$action' WHERE id=$idDolgnost") or die($m->error);
    };
/*
$idDolgnost=$_POST["idDolgnost"];
$Action=$_POST["Action"];
$m->query("UPDATE manualdolgnost SET AlgorithmCalc=$Action") or die($m->error)
*/
?>