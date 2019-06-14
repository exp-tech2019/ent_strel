<?php
session_start();
header('Content-Type: application/json');
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB)
or die('Не удалось соединиться: ' . $m->error);

$SmartCart=$_POST["SmartCart"];
$d=$m->query("SELECT * FROM workers WHERE SmartCartNum=$SmartCart");
$arr=array("Status"=>"Error");
if($d->num_rows>0){
    $r=$d->fetch_assoc();
    $arr=array(
        "Status"=>"Success",
        "idWorker"=>$r["id"],
        "FIO"=>$r["FIO"]
    );
};
echo json_encode($arr);
?>