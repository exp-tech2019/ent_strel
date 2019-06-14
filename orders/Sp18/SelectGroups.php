<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 28.08.2018
 * Time: 9:39
 */
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
ini_set("max_input_vars", "5000");
session_start();

$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

$d=$m->query("SELECT * FROM sp18groups ORDER BY GroupName");
$arr=array();
while ($r=$d->fetch_assoc())
    $arr[]=array(
        "id"=>$r["id"],
        "GroupName"=>$r["GroupName"],
        "Groups1c"=>array()
    );
$d->close();
//Добавим список групп из 1с
$d=$m->query("SELECT * FROM sp18group_1c ");
while($r=$d->fetch_assoc())
    foreach ($arr as &$group)
        if($r["idGroup"]==$group["id"]){
            $group["Groups1c"][]=$r["idGroup1c"];
            break;
        };
$d->close();
echo json_encode($arr);
?>