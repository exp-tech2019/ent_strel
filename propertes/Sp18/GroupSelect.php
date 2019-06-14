<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 17.07.2018
 * Time: 0:01
 */
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
ini_set("max_input_vars", "5000");
session_start();

$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

$arr=array();
$d=$m->query("SELECT * FROM sp18Groups g ORDER BY GroupName");
while($r=$d->fetch_assoc())
    $arr[]=array(
        "idGroup"=>$r["id"],
        "GroupName"=>$r["GroupName"],
        "Group1c"=>array()
    );
$d->close();
//Выберем связку группы и группы в 1с

if($d=$m->query("SELECT g.*, c.GroupName FROM Sp18Group_1c g, Sp18Groups1c c WHERE g.idGroup1c=c.id"))
    while($r=$d->fetch_assoc())
        foreach ($arr as &$group)
            if($group["idGroup"]==$r["idGroup"])
                $group["Group1c"][]=array(
                    "id"=>$r["id"],
                    "idGroup1c"=>$r["idGroup1c"],
                    "GroupName"=>$r["GroupName"]
                );

$m->close();
echo json_encode($arr);
?>