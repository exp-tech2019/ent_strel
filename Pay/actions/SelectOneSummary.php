<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 14.03.2019
 * Time: 11:46
 */
session_start();
ini_set("max_execution_time", "2000");
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
header('Content-Type: application/json');

$idWorker=$_POST["idWorker"];
$DateWith=$_POST["DateWith"];
$DateBy=$_POST["DateBy"];
//Определим уволен или нет сотрудник и его дложность
$d=$m->query("SELECT w.FIO, m.Dolgnost, w.fired FROM workers w, manualdolgnost m WHERE w.DolgnostID=m.id AND w.id=$idWorker") or die($m->error);
$r=$d->fetch_assoc();
$arr=array(
    "FIO"=>$r["FIO"],
    "Dolgnost"=>$r["Dolgnost"],
    "Fired"=>$r["fired"],
    "CountAll"=>0,
    "CostAll"=>0,
    "NaryadSummary"=>array(),
    "PaymentPlus"=>0,
    "PaymentMinus"=>0
);
//Запросим стоимость выполненных нарядов и короткое описание
$d=$m->query("SELECT t1.name, t1.S, SUM(t1.DoorCount) AS DoorCount, SUM(t1.Cost) AS Cost FROM 
(
SELECT od.name, IF(od.S IS NOT NULL , 2, IF(od.SEqual=1,2, 1)) AS S, COUNT(*) AS DoorCount, SUM(nc.Cost) AS Cost FROM orderdoors od, naryad n, naryadcomplite nc WHERE od.id=n.idDoors AND n.id=nc.idNaryad AND nc.idWorker=$idWorker AND nc.DateComplite BETWEEN STR_TO_DATE('$DateWith','%d.%m.%Y') AND DATE_ADD(STR_TO_DATE('$DateBy','%d.%m.%Y'), INTERVAL 1 DAY)
GROUP BY od.id
) t1 GROUP BY t1.name, t1.S ORDER BY t1.name, t1.S;");
$CostAll=0;
$CountAll=0;
if($d->num_rows>0)
    while (($r=$d->fetch_assoc()))
    {
        $CountAll+=(int)$r["DoorCount"];
        $CostAll+=(float)$r["Cost"];
        $arr["NaryadSummary"][] = array(
            "Name" => $r["name"],
            "S" => $r["S"],
            "Count" => $r["DoorCount"],
            "Cost" => $r["Cost"]
        );
    };
$arr["CountAll"]=$CountAll;
$arr["CostAll"]=$CostAll;
$d->close();
//Получим информацию по платежам
$d=$m->query("SELECT idWorker, SUM(IF(Sum>0, Sum, 0)) AS PaymentPlus, SUM(IF(Sum<0, Sum, 0)) AS PaymentMinus FROM paymentsworkers WHERE idWorker=$idWorker AND Status<>'Remove' AND DatePayment BETWEEN STR_TO_DATE('$DateWith', '%d.%m.%Y') AND STR_TO_DATE('$DateBy', '%d.%m.%Y')") or die($m->error);
$PayPlus=0;
$PayMinus=0;
if($d->num_rows>0)
{
    $r=$d->fetch_assoc();
    $PayPlus=$r["PaymentPlus"]==null ? 0 : $r["PaymentPlus"];
    $PayMinus=$r["PaymentMinus"]==null ? 0 : $r["PaymentMinus"];
};
$d->close();
$arr["PaymentPlus"]=$PayPlus;
$arr["PaymentMinus"]=$PayMinus;
$arr["CostAll"]=round($arr["CostAll"]);
$arr["SumAll"]=round((float)$PayPlus+$CostAll+(float)$PayMinus);
echo json_encode($arr);
?>