<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 10.04.2019
 * Time: 23:42
 */

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
ini_set("max_input_vars", "5000");
session_start();

$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

$d=$m->query("SELECT * FROM manualdolgnost ORDER BY Dolgnost");
$arr=array();
while ($r=$d->fetch_assoc())
    $arr[]=array(
        "id"=>$r["id"],
        "Dolgnost"=>$r["Dolgnost"],
        "Algorithm"=>$r["Algorithm"],
        "AlgorithmCalc"=>$r["AlgorithmCalc"]
    );
$d->close();
echo json_encode($arr);
?>