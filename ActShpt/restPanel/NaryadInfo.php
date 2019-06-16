<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 15.06.2019
 * Time: 21:42
 */
session_start();
header('Content-Type: application/json');
include "Params.php";
//$idNaryad=$_POST["idNaryad"];
$idNaryad=str_replace(" ","",
    str_replace("E","",
        str_replace("N","",$_POST["idNaryad"])
    )
);
$d=$m->query("SELECT o.Shet, o.Zakaz, CONCAT(n.Num, n.NumPP) AS NaryadNum, n.NumInOrder, od.name, CONCAT(od.H, ' x ', od.W, IF(od.SEqual=1,' x равн.',IF(od.S IS NOT NULL, CONCAT(' x ',od.S), ''))) AS Size, od.Open, od.RAL, od.Shtild, od.Dovod, od.Nalichnik FROM Oreders o, OrderDoors od, Naryad n WHERE o.id=od.idOrder AND od.id=n.idDoors AND n.id=$idNaryad") or die($m->error);
$r=$d->fetch_assoc();
$arr=array(
    "Shet"=>$r["Shet"],
    "Zakaz"=>$r["Zakaz"],
    "NaryadNum"=>$r["NaryadNum"],
    "NumInOrder"=>$r["NumInOrder"],
    "name"=>$r["name"],
    "Size"=>$r["Size"],
    "RAL"=>$r["RAL"],
    "Shtild"=>$r["Shtild"],
    "Dovod"=>$r["Dovod"],
    "Nalichnik"=>$r["Nalichnik"]
);
echo json_encode($arr);
?>