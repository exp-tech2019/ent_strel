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
$d=$m->query("SELECT * FROM Sp18Groups1c ORDER BY GroupParent, GroupName");
while (($r=$d->fetch_assoc()))
    switch ($r["GroupParent"]){
        case null:
            $arr[]=array(
                "idGroup"=>$r["id"],
                "GroupName"=>$r["GroupName"],
                "GroupChild"=>array()
            );
            break;
        default:
            foreach ($arr as &$gr)
                if($gr["idGroup"]==$r["GroupParent"])
                    $gr["GroupChild"][]=array(
                        "idGroup"=>$r["id"],
                        "GroupName"=>$r["GroupName"]
                    );
            break;
    }

$m->close();
echo json_encode($arr);
?>