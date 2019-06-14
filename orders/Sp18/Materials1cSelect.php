<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 23.07.2018
 * Time: 12:28
 */
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
ini_set("max_input_vars", "5000");
session_start();

$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

$idGroupsStr=$_POST["idGroups1c"];
$idGroups=explode(",",$idGroupsStr);
$arr=array();
$WHERE="";
foreach ($idGroups as $idGroup)
    $WHERE.=($WHERE!="" ? "OR " : "")."idGroup1c=".$idGroup." ";

$d=$m->query("SELECT * FROM Sp18Materials1c WHERE ".$WHERE);
while ($r=$d->fetch_assoc())
    $arr[]=array(
        "idMaterial"=>$r["id"],
        "MaterialName"=>$r["MaterialName"],
        "Article"=>$r["Article"],
        "Unit"=>$r["Unit"]
    );

echo json_encode($arr);
?>