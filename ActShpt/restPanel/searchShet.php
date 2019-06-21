<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 20.06.2019
 * Time: 20:54
 */
header('Content-Type: application/json');
session_start();
include "Params.php";

$searchText=$_POST["searchText"];
$Step=(int)$_POST["Step"];
$compliteFlag=$Step==7 ? "UpakCompliteFlag" : "ShptCompliteFlag";

$d=$m->query("SELECT o.id, o.Shet FROM oreders o, orderdoors od, naryad n WHERE o.BlankDate>STR_TO_DATE('01.01.2019','%d.%m.%Y') AND o.id=od.idOrder AND od.id=n.idDoors AND n.$compliteFlag=0 AND o.Shet LIKE '%$searchText%' GROUP BY o.id LIMIT 5") or die($m->error);
$arr=array();
while ($r=$d->fetch_assoc())
    $arr[]=array(
        "idOrder"=>$r["id"],
        "Shet"=>$r["Shet"]
    );
echo json_encode($arr);
?>