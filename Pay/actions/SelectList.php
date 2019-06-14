<?php
session_start();
ini_set("max_execution_time", "2000");
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
header('Content-Type: application/json');

$arr=array();
$arrDolgnost=array();
if((isset($_POST["DateWith"]) & isset($_POST["DateBy"])) || isset($_POST["FIO"]) || isset($_POST["Dolgnost"])){
    $FIO=!isset($_POST["FIO"]) ? "" : $_POST["FIO"];
    /*
    $DolgnostListID="-1";
    foreach ($_POST["DolgnostCh"] as $idDolgnost)
        $DolgnostListID.=", $idDolgnost";
    */
    $Fired=$_POST["Fired"];

    $idDolgnost="-1";
    /* $d=$m->query("SELECT * FROM manualdolgnost WHERE id IN ($DolgnostListID) ORDER BY Dolgnost") or die($m->error); */
    $d=$m->query("SELECT * FROM manualdolgnost ORDER BY Dolgnost") or die($m->error);
    while ($r=$d->fetch_assoc()) {
        $arr[] = array(
            "idDolgnost" => $r["id"],
            "Dolgnost" => $r["Dolgnost"],
            "Workers" => array(),
            "NaryadCount"=>0,
            "Cost" => 0,
            "PaymentPlus" => 0,
            "PaymentMinus" => 0,
            "SumEnd"=>0
        );
        $idDolgnost.=", ".$r["id"];
    };

    $idWorkers="-1";
    $d=$m->query("SELECT w.id, w.FIO, w.DolgnostID FROM Workers w WHERE w.fired=$Fired AND w.FIO LIKE '%$FIO%' AND w.DolgnostID IN ($idDolgnost) ORDER BY w.FIO");
    while ($r=$d->fetch_assoc()) {
        foreach ($arr as &$dolgnost)
            if($dolgnost["idDolgnost"]===$r["DolgnostID"]){
                $dolgnost["Workers"][]=array(
                    "idWorker"=>$r["id"],
                    "FIO"=>$r["FIO"],
                    "NaryadCount"=>0,
                    "Cost" => 0,
                    "PaymentPlus" => 0,
                    "PaymentMinus" => 0,
                    "SumEnd"=>0
                );
                break;
            };
        $idWorkers.=", ".$r["id"];
    };
    $d->close();

    $DateWith=!isset($_POST["DateWith"]) ? date("01.m.Y") : $_POST["DateWith"];
    $currentDate = time();

    $DateBy=!isset($_POST["DateBy"]) ? date('t.m.Y', $currentDate) : $_POST["DateBy"];
    //Заработал по нарядам
    $d=$m->query("SELECT idWorker, COUNT(*) AS NaryadCount, SUM(Cost) AS Cost FROM NaryadComplite WHERE DateComplite BETWEEN STR_TO_DATE('$DateWith','%d.%m.%Y') AND DATE_ADD(STR_TO_DATE('$DateBy','%d.%m.%Y'), INTERVAL 1 DAY) AND idWorker IN ($idWorkers) GROUP BY idWorker") or die($m->error);
    while ($r=$d->fetch_assoc()){
        $idWorker=$r["idWorker"];
        $Cost=(float)$r["Cost"];
        foreach ($arr as &$dolgnost)
            foreach ($dolgnost["Workers"] as &$worker)
                if($worker["idWorker"]===$r["idWorker"])
                {
                    $dolgnost["Cost"]+=$Cost;
                    $dolgnost["NaryadCount"]+=(int)$r["NaryadCount"];
                    $worker["Cost"]+=$Cost;
                    $worker["NaryadCount"]=(int)$r["NaryadCount"];

                    break;
                };
    };
    $d->close();
    //Платежи
    $d=$m->query("SELECT idWorker, SUM(IF(Sum>0, Sum, 0)) AS PaymentPlus, SUM(IF(Sum<0, Sum, 0)) AS PaymentMinus FROM paymentsworkers WHERE idWorker IN ($idWorkers) AND Status<>'Remove' AND DatePayment BETWEEN STR_TO_DATE('$DateWith', '%d.%m.%Y') AND STR_TO_DATE('$DateBy', '%d.%m.%Y') GROUP BY idWorker") or die($m->error);
    while ($r=$d->fetch_assoc()){
        $idWorker=$r["idWorker"];
        $PaymentPlus=(float)$r["PaymentPlus"];
        $PaymentMinus=(float)$r["PaymentMinus"];
        foreach ($arr as &$dolgnost)
            foreach ($dolgnost["Workers"] as &$worker)
                if($worker["idWorker"]===$r["idWorker"])
                {
                    $dolgnost["PaymentPlus"]+=$PaymentPlus;
                    $dolgnost["PaymentMinus"]+=$PaymentMinus;
                    $worker["PaymentPlus"]+=$PaymentPlus;
                    $worker["PaymentMinus"]+=$PaymentMinus;
                    break;
                };
    };
    $d->close();
    //Округлим значения и подсчитаем итог
    foreach ($arr as &$dolgnost)
    {
        foreach ($dolgnost["Workers"] as &$worker){
            $worker["Cost"]=round($worker["Cost"]);
            $worker["PaymentPlus"]=round($worker["PaymentPlus"]);
            $worker["PaymentMinus"]=round($worker["PaymentMinus"]);
            $worker["SumEnd"]=round($worker["Cost"]+$worker["PaymentPlus"]+$worker["PaymentMinus"]);
        };
        $dolgnost["Cost"]=round($dolgnost["Cost"]);
        $dolgnost["PaymentPlus"]=round($dolgnost["PaymentPlus"]);
        $dolgnost["PaymentMinus"]=round($dolgnost["PaymentMinus"]);
        $dolgnost["SumEnd"]=round($dolgnost["Cost"]+$dolgnost["PaymentPlus"]+$dolgnost["PaymentMinus"]);
    }
    //Акты
    /*
    $d=$m->query("SELECT idWorker, SUM(SumPlus) AS SumPlus, SUM(SumMinus) AS SumMinus FROM temppayrollspayments WHERE DateCreate BETWEEN STR_TO_DATE('$DateWith','%d.%m.%Y') AND STR_TO_DATE('$DateBy','%d.%m.%Y') AND idWorker IN ($idWorkers) GROUP BY idWorker") or die($m->error);
    while($r=$d->fetch_assoc())
        foreach ($arr as &$worker)
            if($worker["id"]===$r["idWorker"]){
                $worker["ActPlus"]+=(float)$r["SumPlus"];
                $worker["ActMinus"]+=(float)$r["SumMinus"];
                break;
            };
    $d->close();
    */
};
echo json_encode($arr);
?>