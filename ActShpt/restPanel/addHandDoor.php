<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 20.06.2019
 * Time: 23:15
 */
header('Content-Type: application/json');
session_start();
include "Params.php";

$Shet=$_POST["Shet"];
$Position=$_POST["Position"];
$Step=(int)$_POST["Step"];
$compliteFlag=$Step==7 ? "UpakCompliteFlag" : "ShptCompliteFlag";


$d=$m->query("SELECT n.id AS idNaryad, o.Shet, CONCAT(n.Num, n.NumPP) AS NaryadNum, n.NumInOrder FROM oreders o, orderdoors od, naryad n WHERE o.id=od.idOrder AND od.id=n.idDoors AND n.$compliteFlag=0 AND o.Shet='$Shet' AND n.NumInOrder=$Position") or die($m->error);
$arr=array("Status"=>"", "idNaryad"=>-1, "NaryadNum"=>"", "Shet"=>"", "NumInOrder"=>"");
switch ($d->num_rows){
    case 0:
        $arr["Status"]="Error";
        break;
    default:
        $r=$d->fetch_assoc();
        $arr["Status"]="Success";
        $arr["idNaryad"]=$r["idNaryad"];
        $arr["Shet"]=$r["Shet"];
        $arr["NaryadNum"]=$r["NaryadNum"];
        $arr["NumInOrder"]=$r["NumInOrder"];
        break;
};
echo json_encode($arr);