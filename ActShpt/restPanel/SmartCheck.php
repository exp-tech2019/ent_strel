<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 11.06.2019
 * Time: 10:56
 */
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
session_start();

$idWorker=-1;
$FIO="";
if(isset($_POST["SmartCart"]))
{
    $SmartCart=$_POST["SmartCart"];
    include "Params.php";
    $d=$m->query("SELECT id, FIO, DolgnostID FROM Workers WHERE SmartCartNum='$SmartCart' AND fired=0") or die($m->error);
    if($d->num_rows>0)
    {
        $r=$d->fetch_assoc();
        if(in_array((int)$r["DolgnostID"],$AccessDolgnost))
        {
            $_SESSION["idWorker"]=$r["id"];
            $_SESSION["FIO"]=$r["FIO"];
            $idWorker=$r["id"];
        };
    };
};

if(isset($_SESSION["idWorker"])) {
    $idWorker = $_SESSION["idWorker"];
    $FIO=$_SESSION["FIO"];
};

echo json_encode(array("idWorker"=>$idWorker, "FIO"=>$FIO));
?>
