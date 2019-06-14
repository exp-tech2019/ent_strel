<?php
header('Content-Type: application/json');
include "../DBConnect.php";
$WHEREGet=isset($_GET["Where"]) ? $_GET["Where"] : "";
$Where=$WHEREGet=="" ? "" : "WHERE OrgName LIKE '%$WHEREGet%' OR INN LIKE '%$WHEREGet%'";
$d=$db->query("SELECT id, OrgName, INN, AdressActual FROM mn_Clients $Where LIMIT 0,100");
$array=array();
if($d)
    while($r=$d->fetch_assoc())
        $array[]=array(
            "id"=>$r["id"],
            "OrgName"=>$r["OrgName"],
            "INN"=>$r["INN"],
            "AdressActual"=>$r["AdressActual"],
            "OrderCount"=>0,
            "Sum"=>0
        );
echo json_encode($array);
?>