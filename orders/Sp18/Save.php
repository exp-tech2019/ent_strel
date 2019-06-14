<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 23.07.2018
 * Time: 13:40
 */
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
ini_set("max_input_vars", "5000");
session_start();

$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

$idDoor=$_POST["idDoor"];
$Rows=$_POST["Rows"];
$m->query("DELETE FROM Sp18Doors WHERE idDoor=$idDoor");
foreach ($Rows as $row) {
    $idGroup=$row["idGroup"];
    $idMaterial=$row["idMaterial"];
    $Count=str_replace(",",".",$row["Count"]!="" ? $row["Count"] : 0);
    $m->query("INSERT INTO Sp18Doors (idDoor, idGroup, idMaterial, Count) VALUES($idDoor, $idGroup, $idMaterial, $Count)") or die ($m->error);
};

//Сохраним шаблон для других специцфикаций
//Определим тип двери
$d=$m->query("SELECT name FROM OrderDoors WHERE id=$idDoor");
$r=$d->fetch_assoc();
$TypeDoor=$r["name"];
$d->close();
//Опеределим группы для которых нужно сохранить шаблон
$d=$m->query("SELECT * FROM sp18construct WHERE TypeDoor='$TypeDoor'");
$arrGroupsSave=array();
if($d->num_rows>0)
    while ($r=$d->fetch_assoc())
        if($r["Save"]==1)
            $arrGroupsSave[]=$r["idGroup"];
$d->close();
//Производим сохранение шаблона
foreach ($arrGroupsSave as $idGroup)
    foreach ($Rows as $row)
        if($idGroup==$row["idGroup"] & $row["idMaterial"]!=-1) {
            $idMaterial=$row["idMaterial"];
            $m->query("INSERT INTO Sp18Equation VALUES ('$TypeDoor',$idGroup, $idMaterial) ON DUPLICATE KEY UPDATE idMaterial=$idMaterial");
        };

//Изменим статус нащей спецификации
$flagDataSuccess=true;
foreach ($Rows as $row)
    if($row["idMaterial"]==-1 || ($row["Count"]=="" || $row["Count"]=="0"))
        $flagDataSuccess=false;
$DataSuccess=$flagDataSuccess ? 1 : 0;
$d=$m->query("SELECT * FROM sp18statistics WHERE idDoor=$idDoor");
$idSp18=-1;
$StatusComplite=1;
if($d->num_rows>0)
{
    $r=$d->fetch_assoc();
    $idSp18=$r["id"];
    $StatusCompliteOld=(int)$r["StatusComplite"];
    switch ($StatusCompliteOld){
        case 0: $StatusComplite=1; break;
        case 1: $StatusComplite=2; break;
        case 2: $StatusComplite=2; break;
    };
};
$d->close();
switch ($idSp18){
    case -1:
        $m->query("INSERT INTO sp18statistics (idDoor, StatusComplite, DateTransport, DataSuccess) VALUES ($idDoor, $StatusComplite, NULL, $DataSuccess)") OR DIE ($m->error);
        break;
    default:
        $m->query("UPDATE sp18statistics SET StatusComplite=$StatusComplite, DateTransport=NULL, DataSuccess=$DataSuccess WHERE id=$idSp18") or die ($m->error);
        break;
};
echo json_encode(array("Status"=>"Success", "DataSuccess"=>$flagDataSuccess ? "Yes" : 'No'));
?>