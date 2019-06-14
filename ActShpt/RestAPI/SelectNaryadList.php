<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 24.04.2019
 * Time: 14:38
 */
session_start();
header('Content-Type: application/json');
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB)
or die('Не удалось соединиться: ' . $m->error);
$idAct=$_POST["idAct"];
$Shet=$_POST["Shet"];
$Step=$_POST["Step"];
$StepSQL="AND 1=1";
switch ((int)$Step){
    case 0:
        $StepSQL="(n.UpakCompliteFlag=1 OR n.ShptCompliteFlag=1)";
        break;
    case 7:
        $StepSQL="n.UpakCompliteFlag=1 AND n.ShptCompliteFlag=0";
        break;
    case 8:
        $StepSQL="n.ShptCompliteFlag=1";
        break;
};

$d=$m->query("SELECT n.id AS idNaryad, CONCAT(n.Num,n.NumPP) AS NaryadNum, n.NumInOrder, od.NumPP, od.name, od.Open, od.RAL, CONCAT(od.H,' x ', od.W, IF(od.S IS NOT NULL, CONCAT(' x ',od.S), IF(od.SEqual=1,' x равн',''))) AS DoorSize, 
n.SvarkaCompliteFlag, n.SborkaMdfCompliteFlag, n.SborkaCompliteFlag, n.ColorCompliteFlag, n.UpakCompliteFlag, n.ShptCompliteFlag 
FROM Oreders o, Orderdoors od, naryad n WHERE o.id=od.idOrder AND od.id=n.idDoors 
AND n.id NOT IN (SELECT idNaryad FROM ActShpt a, actshptdoor d WHERE a.id=d.idAct AND d.idAct<>$idAct AND a.Status<>-1) 
AND n.id NOT IN (SELECT idNaryad FROM ActShpt a, actshptdoortmp d WHERE a.id=d.idAct AND (d.idAct=$idAct OR a.Status<>0)) 
AND o.Shet='$Shet' AND $StepSQL") or die($m->error);
$arr=array();
while ($r=$d->fetch_assoc()) {
    $Step=$r["ShptCompliteFlag"]==1 ? "Погрузка":"Упаковка";
    $arr[] = array(
        "idNaryad" => $r["idNaryad"],
        "NaryadNum" => $r["NaryadNum"],
        "NumInOrder" => $r["NumInOrder"],
        "NumPP" => $r["NumPP"],
        "name" => $r["name"],
        "Open" => $r["Open"],
        "RAL" => $r["RAL"],
        "DoorSize" => $r["DoorSize"],
        "UpakCompliteFlag" => $r["UpakCompliteFlag"],
        "ShptCompliteFlag" => $r["ShptCompliteFlag"],
        "Step"=>$Step
    );
};
$d->close();
$m->close();
echo json_encode($arr);
?>