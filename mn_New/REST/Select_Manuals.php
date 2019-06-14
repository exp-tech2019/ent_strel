<?php
header('Content-Type: application/json');
include "../DBConnect.php";
$d=$db->query("SELECT * FROM ent.manualtypedoors ORDER BY Name");
$TypeDoors=array();
while ($r=$d->fetch_assoc())
    $TypeDoors[]=array(
        "id"=>$r["id"],
        "Name"=>$r["Name"]
    );
$d=$db->query("SELECT * FROM ent.manualdovoddoor");
$Dovods=array();
while($r=$d->fetch_assoc())
    $Dovods[]=array(
        "id"=>$r["id"],
        "Name"=>$r["Name"]
    );
$d=$db->query("SELECT * FROM ent.manualopendoor");
$OpenDoors=array();
while($r=$d->fetch_assoc())
    $OpenDoors[]=array(
        "id"=>$r["id"],
        "Name"=>$r["Name"]
    );
$d=$db->query("SELECT * FROM ent.manualnalichnikdoor");
$Nalichnik=array();
while($r=$d->fetch_assoc())
    $Nalichnik[]=array(
        "id"=>$r["id"],
        "Name"=>$r["Name"]
    );
echo json_encode(array(
    "TypeDoor"=>$TypeDoors,
    "Dovod"=>$Dovods,
    "OpenDoor"=>$OpenDoors,
    "Nalichnik"=>$Nalichnik
));
$db->close();
?>