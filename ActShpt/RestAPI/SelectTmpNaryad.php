<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 28.04.2019
 * Time: 0:36
 */
session_start();
header('Content-Type: application/json');
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB)
or die('Не удалось соединиться: ' . $m->error);
$idAct=$_POST["idAct"];
$Find=$_POST["Find"];
$SortCol=$_POST["SortCol"];
$arrSortTypes=array(
    "id"=>"a.id",
    "Shet"=>"o.Shet",
    "NumInDoor"=>"n.NumInOrder",
    "NaryadNum"=>"n.Num, n.NumPP",

    "Name"=>"od.name",
    "Step"=>"n.ShptCompliteFlag"
);
$SortCol=$arrSortTypes[$SortCol];

//Получим кол-во дверей
$d=$m->query("SELECT COUNT(*) AS DoorCount FROM ActShptDoorTmp WHERE idAct=$idAct") or die($m->error);
$r=$d->fetch_assoc();
$DoorCount=$r["DoorCount"];

$d=$m->query("SELECT a.id AS idTmp, n.id AS idNaryad, o.Shet, od.name, CONCAT(od.H,' x ', od.W, IF(od.S IS NOT NULL, CONCAT(' x ',od.S), IF(od.SEqual=1,' x равн',''))) AS DoorSize, n.NumInOrder, CONCAT(n.Num,n.NumPP) AS NaryadNum, 
n.SvarkaCompliteFlag, n.SborkaCompliteFlag, n.SborkaMdfCompliteFlag, n.ColorCompliteFlag, n.UpakCompliteFlag, n.ShptCompliteFlag
FROM oreders o, OrderDoors od, naryad n, ActShptDoorTmp a 
WHERE o.id=od.idOrder AND od.id=n.idDoors AND n.id=a.idNaryad AND a.idAct=$idAct AND (o.Shet LIKE '%$Find%' OR od.name LIKE '%$Find%') ORDER BY $SortCol") or die($m->error);
$arr=array();
while ($r=$d->fetch_assoc()) {
    $Step=$r["ShptCompliteFlag"]==1 ? "Погрузка" : "Упаковка";
    $arr[] = array(
        "idTmp"=>$r["idTmp"],
        "idNaryad"=>$r["idNaryad"],
        "Shet" => $r["Shet"],
        "NumInOrder" => $r["NumInOrder"],
        "NaryadNum" => $r["NaryadNum"],
        "Name" => $r["name"],
        "DoorSize" => $r["DoorSize"],
        "ShptCompliteFlag"=>$r["ShptCompliteFlag"],
        "Step" => $Step
    );
};
echo json_encode(array(
    "DoorCount"=>$DoorCount,
    "Doors"=>$arr
));
?>