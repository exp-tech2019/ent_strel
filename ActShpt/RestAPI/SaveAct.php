<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 03.05.2019
 * Time: 14:22
 */

session_start();
$Referent=$_SESSION["AutorizeFIO"];
header('Content-Type: application/json');
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB)
or die('Не удалось соединиться: ' . $m->error);

$idAct=$_POST["idAct"];
$ShptDate=$_POST["ShptDate"];
$AdressState=$_POST["AdressState"];
$AdressCity=$_POST["AdressCity"];
$AdressRaion=$_POST["AdressRaion"];
$AdressStreet=$_POST["AdressStreet"];
$OrgName=$_POST["OrgName"];
$Fahrer=$_POST["Fahrer"];
$CarNum=$_POST["CarNum"];
$Note=$_POST["Note"];
$Status=$_POST["Status"];
$m->query("UPDATE ActShpt SET ShptDate=STR_TO_DATE('$ShptDate','%d.%m.%Y'), AdressState='$AdressState', AdressCity='$AdressCity', AdressRaion='$AdressRaion', AdressStreet='$AdressStreet', OrgName='$OrgName', Fahrer='$Fahrer', CarNum='$CarNum', Note='$Note', Status=$Status, Referent='$Referent' WHERE id=$idAct") or die($m->error);
$m->query("DELETE FROM actshptdoor WHERE idAct=$idAct") or die($m->error);
$m->query("INSERT INTO actshptdoor (idAct, idNaryad) SELECT idAct, idNaryad FROM actshptdoortmp WHERE idAct=$idAct") or die($m->error);
$m->query("DELETE FROM actshptdoortmp WHERE idAct=$idAct") or die($m->error);
//Сохраним историю
$arrHistory=$_POST["History"];
/*
$m->query("INSERT INTO actshpthistory (idAct, DateChange, Referent, Action, TypeParent, idParent, Step) VALUES($idAct, Now(), '$Referent', 'EditAct', '', -1, -1)") or die($m->error);
*/
foreach ($arrHistory as $h){
    $Action=$h["Action"];
    $TypeParent=$h["TypeParent"];
    $idParent=$h["idParent"];
    $Step=$h["Step"];
    $m->query("INSERT INTO actshpthistory (idAct, DateChange, Referent, Action, TypeParent, idParent, Step) VALUES($idAct, Now(), '$Referent', '$Action', '$TypeParent', $idParent, $Step)") or die($m->error);
}

$m->close();

echo json_encode(array("Status"=>"Success"));
?>