<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 15.06.2019
 * Time: 23:32
 */

header('Content-Type: application/json');

include "Params.php";
$idWorker=$_POST["idWorker"];
$idAct=$_POST["idAct"];
$Naryads=$_POST["Doors"];
foreach ($Naryads as $n)
{
    $idNaryad=$n["idNaryad"];
    $idDoorInAct=$n["idDoorInAct"];
    switch ($n["Status"])
    {
        case "Add":
            $m->query("INSERT INTO actshptdoor (idAct, idNaryad) VALUES ($idAct, $idNaryad)") or die($m->error);
            $m->query("UPDATE NaryadComplite SET idWorker=$idWorker, DateComplite=NOW() WHERE Step=8 AND DateComplite IS NULL AND idNaryad=$idNaryad");
            $m->query("UPDATE Naryad SET ShptCompliteFlag=1 WHERE id=$idNaryad") or die($m->error);
            break;
        case "Remove":
            $m->query("DELETE FROM actshptdoor WHERE id=$idDoorInAct") or die($m->error);
            $m->query("UPDATE NaryadComplite SET idWorker=NULL, DateComplite=NULL WHERE Step=8 AND DateComplite IS NOT NULL AND idNaryad=$idNaryad");
            $m->query("UPDATE Naryad SET ShptCompliteFlag=0 WHERE id=$idNaryad") or die($m->error);
            break;
    };
};
echo json_encode(array("success"=>1));
?>