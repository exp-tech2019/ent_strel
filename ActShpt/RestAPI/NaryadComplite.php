<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 03.05.2019
 * Time: 19:37
 */
session_start();
header('Content-Type: application/json');
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB)
or die('Не удалось соединиться: ' . $m->error);

$idAct=$_POST["idAct"];
$Step=$_POST["Step"];
$Steps=array();
if($Step==11)
{
    $Steps[]=3;
    $Steps[]=5;
    $Steps[]=6;
    $Steps[]=7;
    $Steps[]=8;
}
else
    $Steps[]=$Step;
$idNaryads="-1";
$d=$m->query("SELECT idNaryad FROM actshptdoortmp WHERE idAct=$idAct");
while ($r=$d->fetch_assoc())
    $idNaryads.=", ".$r["idNaryad"];
$d->close();
if($Step!=0)
    foreach ($Steps as $step) {
        $m->query("UPDATE NaryadComplite SET idWorker=-1, DateComplite=NOW() WHERE Step=$step AND idWorker IS NULL AND idNaryad IN ($idNaryads)") or die($m->error);
        switch ($step){
            case 3:
                $m->query("UPDATE Naryad SET SvarkaCompliteFlag=1 WHERE id IN ($idNaryads)") or die($m->error);
                break;
            case 5:
                $m->query("UPDATE Naryad SET SborkaCompliteFlag=1 WHERE id IN ($idNaryads)") or die($m->error);
                break;
            case 6:
                $m->query("UPDATE Naryad SET ColorCompliteFlag=1 WHERE id IN ($idNaryads)") or die($m->error);
                break;
            case 7:
                $m->query("UPDATE Naryad SET UpakCompliteFlag=1 WHERE id IN ($idNaryads)") or die($m->error);
                break;
            case 8:
                $m->query("UPDATE Naryad SET ShptCompliteFlag=1 WHERE id IN ($idNaryads)") or die($m->error);
                break;
        }
    };
echo json_encode(array("Status"=>"Success"));
?>