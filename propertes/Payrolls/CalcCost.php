<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 11.04.2019
 * Time: 23:28
 */
/*header('Content-Type: application/xml; charset=utf-8');*/
$XMLParams = simplexml_load_file("../../params.xml");
$m = new mysqli($XMLParams->ConnectDB->Host, $XMLParams->ConnectDB->User, $XMLParams->ConnectDB->Pass, $XMLParams->ConnectDB->DB);

$idNaryad=$_GET["idNaryad"];
$idDolgnost=$_GET["idDolgnost"];
$Step=$_GET["Step"];

//Определим параметры двери
$d=$m->query("SELECT od.*, IF(od.S IS NOT NULL, 2, IF(od.SEqual=1, 2, 1)) AS SType FROM orderdoors od, naryad n WHERE od.id=n.idDoors AND n.id=$idNaryad") or die($m->error);
$r=$d->fetch_assoc();
$DoorType=$r["name"];
$od=array(
    "DoorType" => $r["name"],
    "H" => (int)$r["H"],
    "W" => (int)$r["W"],
    "S" => $r["S"],
    "SType"=>$r["SType"],
    "FramugaH"=>$r["FramugaH"],
    "SEqual" => $r["SEqual"],
    "Open" => $r["Open"],
    "Nalichnik" => $r["Nalichnik"],
    "Dovod" => $r["Dovod"],

    "WorkPetlya" => $r["WorkPetlya"],
    "WorkWindowCh" => (int)$r["WorkWindowCh"],
    "WorkWindowNoFrame" => $r["WorkWindowNoFrame"],
    "WorkWindowCh1"=>(int)$r["WorkWindowCh1"],
    "WorkWindowCh2"=>(int)$r["WorkWindowCh2"],
    "WorkUpGridCh"=>(int)$r["WorkUpGridCh"],
    "WorkDownGridCh"=>(int)$r["WorkDownGridCh"],

    "StvorkaPetlya" => $r["StvorkaPetlya"],
    "StvorkaWindowCh" => (int)$r["StvorkaWindowCh"],
    "StvorkaWindowNoFrame" => $r["StvorkaWindowNoFrame"],
    "StvorkaWindowCh1" => (int)$r["StvorkaWindowCh1"],
    "StvorkaWindowCh2" => (int)$r["StvorkaWindowCh2"],
    "StvorkaDownGridCh" => (int)$r["StvorkaDownGridCh"],
    "StvorkaUpGridCh" => (int)$r["StvorkaUpGridCh"],

    "FramugaCh" => (int)$r["FramugaCh"],
    "FramugaWindowCh" => (int)$r["FramugaWindowCh"],
    "FramugaWindowNoFrame" => $r["FramugaWindowNoFrame"],
    "FramugaUpGridCh" => $r["FramugaUpGridCh"],
    "FramugaDownGridCh" => $r["FramugaDownGridCh"],

    "Antipanik" => $r["Antipanik"],
    "Otboynik" => $r["Otboynik"],
    "name" => $r["name"],
    "Wicket" => $r["Wicket"],
    "BoxLock" => $r["BoxLock"],
    "Otvetka" => $r["Otvetka"],
    "Isolation" => $r["Isolation"]
);
$d->close();
//Определим тип расчета для должности
$d=$m->query("SELECT AlgorithmCalc FROM manualdolgnost WHERE id=$idDolgnost") or die($m->error);
$r=$d->fetch_assoc();
$AlgorithmCalc=$r["AlgorithmCalc"];
$TableDoorSize=$r["AlgorithmCalc"]=="Step" ? "payrolldoorsize_new" : "payrolldoorsize_dolgnost";
$TableConstant=$r["AlgorithmCalc"]=="Step" ? "payrollconstant" : "payrollconstant_dolgnost";
$TableConstruct=$r["AlgorithmCalc"]=="Step" ? "payrollconstruct" : "payrollconstruct_dolgnost";
$d->close();

$arrDoorSize = array();
$SQL="";
switch ($AlgorithmCalc){
    case "Step":
        $SQL="SELECT * FROM payrolldoorsize_new WHERE DoorType='$DoorType' AND Step='$Step'";
        break;
    case "Dolgnost":
        $SQL="SELECT * FROM payrolldoorsize_dolgnost WHERE DoorType='$DoorType' AND Dolgnost=$idDolgnost";
        break;
};
$d = $m->query($SQL);
if ($d->num_rows > 0)
    while ($r = $d->fetch_assoc()) {
        $HWith = $r["HWith"] == null ? "" : $r["HWith"];
        $HBy = $r["HBy"] == null ? "" : $r["HBy"];

        $WWith = $r["WWith"] == null ? "" : $r["WWith"];
        $WBy = $r["WBy"] == null ? "" : $r["WBy"];

        $S = $r["S"];
        $SWith = $r["SWith"] == null ? "" : $r["SWith"];
        $SBy = $r["SBy"] == null ? "" : $r["SBy"];

        $Framug = $r["Framug"] ? 1 : 0;
        $Sum = $r["Sum"];
        $arrDoorSize[] = array(
            "DoorType" => $r["DoorType"],
            "Step" => $r[$AlgorithmCalc],
            "HWith" => $HWith,
            "HBy" => $HBy,
            "WWith" => $WWith,
            "WBy" => $WBy,

            "S" => $S,
            "SWith" => $SWith,
            "SBy" => $SBy,

            "Framug" => $Framug,
            "Sum" => $Sum
        );
    };
$d->close();
//Константы
$arrConst = array();
$SQL="";
switch ($AlgorithmCalc){
    case "Step":
        $SQL="SELECT * FROM payrollconstant WHERE DoorType='$DoorType' AND Step='$Step'";
        break;
    case "Dolgnost":
        $SQL="SELECT * FROM payrollconstant_dolgnost WHERE DoorType='$DoorType' AND Dolgnost=$idDolgnost";
        break;
};
$d = $m->query($SQL);
if ($d->num_rows > 0)
    while ($r = $d->fetch_assoc())
        $arrConst[] = array(
            "DoorType" => $r["DoorType"],
            "Step" => $r[$AlgorithmCalc],
            "Note" => $r["Name"],
            "Sum" => $r["Sum"]
        );
$d->close();
//Конструкция двери
$arrConstruct = null;
$SQL="";
switch ($AlgorithmCalc){
    case "Step":
        $SQL="SELECT * FROM payrollconstruct WHERE DoorType='$DoorType' AND Step='$Step'";
        break;
    case "Dolgnost":
        $SQL="SELECT * FROM payrollconstruct_dolgnost WHERE DoorType='$DoorType' AND Dolgnost=$idDolgnost";
        break;
};
echo $SQL;
$d = $m->query($SQL);
if ($d->num_rows > 0)
    while($r = $d->fetch_assoc())
    {
        $arrConstruct[] = array(
            "DoorType" => $r["DoorType"],
            "Step" => $r[$AlgorithmCalc],

            "Frame" => $r["Frame"],
            "FrameCount" => $r["FrameCount"],
            "FrameSum" => $r["FrameSum"],

            "Dovod" => $r["Dovod"],
            "DovodPreparation" => $r["DovodPreparation"],
            "DovodSum" => $r["DovodSum"],

            "Nalichnik" => $r["Nalichnik"],
            "NalichnikSum" => $r["NalichnikSum"],

            "Window" => $r["Window"],
            "WindowCount" => $r["WindowCount"],
            "WindowMore" => $r["WindowMore"],
            "WindowSum" => $r["WindowSum"],

            "Framuga" => $r["Framuga"],
            "FramugaSum" => $r["FramugaSum"],

            "Petlya" => $r["Petlya"],
            "PetlyaCount" => $r["PetlyaCount"],
            "PetlyaMore" => $r["PetlyaMore"],
            "PetlyaSum" => $r["PetlyaSum"],

            "PetlyaWork" => $r["PetlyaWork"],
            "PetlyaWorkCount" => $r["PetlyaWorkCount"],
            "PetlyaWorkMore" => $r["PetlyaWorkMore"],
            "PetlyaWorkSum" => $r["PetlyaWorkSum"],

            "PetlyaStvorka" => $r["PetlyaStvorka"],
            "PetlyaStvorkaCount" => $r["PetlyaStvorkaCount"],
            "PetlyaStvorkaMore" => $r["PetlyaStvorkaMore"],
            "PetlyaStvorkaSum" => $r["PetlyaStvorkaSum"],

            "Stiffener" => $r["Stiffener"],
            "StiffenerW" => $r["StiffenerW"],
            "StiffenerSum" => $r["StiffenerSum"],

            "M2" => $r["M2"],
            "M2Sum" => $r["M2Sum"],

            "Antipanik" => $r["Antipanik"],
            "AntipanikSum" => $r["AntipanikSum"],

            "Otboynik" => $r["Otboynik"],
            "OtboynikSum" => $r["OtboynikSum"],

            "Wicket" => $r["Wicket"],
            "WicketSum" => $r["WicketSum"],

            "BoxLock" => $r["BoxLock"],
            "BoxLockSum" => $r["BoxLockSum"],

            "Otvetka" => $r["Otvetka"],
            "OtvetkaSum" => $r["OtvetkaSum"],

            "Isolation" => $r["Isolation"],
            "IsolationSum" => $r["IsolationSum"],

            "Grid" => $r["Grid"],
            "GridCount" => $r["GridCount"],
            "GridSum" => $r["GridSum"]
        );
    };
$d->close();

//Выполним расчет
$CostNew = 0;
$DoorType=$od["DoorType"];
$StepStr=$Step;
$W=(int)$od["W"];
$FramugaH=$od["FramugaH"]==null ? 0 : (int)$od["FramugaH"];
$H=(int)$od["H"]-$FramugaH;
$S=$od["S"]==null ? ($od["SEqual"]==1 ? round($W/2) : 0) : (int)$od["S"];
$SType=(int)$od["SType"];
foreach ($arrDoorSize as $ds)
    {
        $flag = true;
        if ($ds["HWith"] != "" & $ds["HBy"] == "")
            if ((int)$ds["HWith"] > $H) $flag = false;
        if ($ds["HWith"] != "" & $ds["HBy"] != "")
            if ($H < (int)$ds["HWith"] || $H > (int)$ds["HBy"]) $flag = false;
        if ($ds["HWith"] == "" & $ds["HBy"] != "")
            if ($H > (int)$ds["HBy"]) $flag = false;

        if ($ds["WWith"] != "" & $ds["WBy"] == "")
            if ((int)$ds["WWith"] > $W) $flag = false;
        if ($ds["WWith"] != "" & $ds["WBy"] != "")
            if ($W < (int)$ds["WWith"] || $W > (int)$ds["WBy"]) $flag = false;
        if ($ds["WWith"] == "" & $ds["WBy"] != "")
            if ($W > (int)$ds["WBy"]) $flag = false;
        switch ($ds["S"]) {
            case "":
                break;
            case "1":
                if ($SType==2) $flag = false;
                break;
            case "2":
                switch ($SType == 2) {
                    case false:
                        $flag = false;
                        break;
                    case true:
                        if ($ds["SWith"] != "" || $ds["SBy"] != "") {
                            $SWith = $ds["SWith"] == "" ? -1 : (int)$ds["SWith"];
                            $SBy = $ds["SBy"] == "" ? -1 : (int)$SBy;

                            if ($SWith == -1 & $SBy != -1)
                                if ($S > $SBy) $flag = false;
                            if ($SWith != -1 & $SBy != -1)
                                if ($S < $SWith || $S > $SBy) $flag = true;
                            if ($SWith != -1 & $SBy == -1)
                                if ($SWith > $S) $flag = true;
                        };
                        break;
                };
                break;
        };
        if ($flag) $CostNew = (float)$ds["Sum"];
    };
foreach ($arrConst as $con)
        $CostNew+=(float)$con["Sum"];
foreach ($arrConstruct as $c)
    {
        //Рамка
        if($c["Frame"])
        {
            $WorkWindowCh=$od["WorkWindowCh"];
            $WorkWindowNoFrame=$od["WorkWindowNoFrame"];

            $StvorkaWindowCh=$od["StvorkaWindowCh"];
            $StvorkaWindowNoFrame=$od["StvorkaWindowNoFrame"];

            $FramugaWindowCh=$od["FramugaWindowCh"];
            $FramugaWindowNoFrame=$od["FramugaWindowNoFrame"];
            if(($WorkWindowCh==1 & $WorkWindowNoFrame==0) || ($StvorkaWindowCh==1 & $StvorkaWindowNoFrame==0) || ($FramugaWindowCh==1 & $FramugaWindowNoFrame==0))
                switch ($c["FrameCount"]==1)
                {
                    case true:
                        $FrameCount=0;
                        $FrameCount+=($WorkWindowCh==1 & $WorkWindowNoFrame==0) ? 1 : 0;
                        $FrameCount+=($StvorkaWindowCh==1 & $StvorkaWindowNoFrame==0) ? 1 : 0;
                        $FrameCount+=($FramugaWindowCh==1 & $FramugaWindowNoFrame==0) ? 1 : 0;
                        $CostNew+=(float)$c["FrameSum"]*$FrameCount;
                        break;
                    case false:
                        $CostNew+=(float)$c["FrameSum"];
                        break;
                };
        };
        //Доводчик
        if($c["Dovod"]==1)
        {
            $Dovod=$od["Dovod"];
            if($Dovod=="да" & $c["DovodPreparation"]!=1)
                $CostNew+=(float)$c["DovodSum"];
            if(($Dovod=="да" || $Dovod=="нет, подготовка") & $c["DovodPreparation"]==1)
                $CostNew+=(float)$c["DovodSum"];
        };
        //Наличник
        if($c["Nalichnik"]==1 & $od["Nalichnik"]=="да")
            $CostNew+=(float)$c["NalichnikSum"];
        //Окно
        if($c["Window"]==1)
        {
            //Определим кол-во окон
            $WindowCount=0;
            $WindowCount+=$od["WorkWindowCh"]==1 ? 1 : 0;
            $WindowCount+=$od["WorkWindowCh1"]==1 ? 1 : 0;
            $WindowCount+=$od["WorkWindowCh2"]==1 ? 1 : 0;
            $WindowCount+=$od["StvorkaWindowCh"]==1 ? 1 : 0;
            $WindowCount+=$od["StvorkaWindowCh1"]==1 ? 1 : 0;
            $WindowCount+=$od["StvorkaWindowCh2"]==1 ? 1 : 0;
            $WindowCount+=$od["FramugaWindowCh"]==1 ? 1 : 0;
            if($WindowCount>0)
                switch ($c["WindowCount"])
                {
                    case 1://Зависит от кол-ва
                        switch ($c["WindowMore"])
                        {
                            case null:
                                $CostNew+=(float)$c["WindowSum"]*$WindowCount;
                                break;
                            default://Если больше
                                if($WindowCount>(int)$c["WindowMore"])
                                    $CostNew+=($WindowCount-(int)$c["WindowMore"])*(float)$c["WindowSum"];
                                break;
                        }
                        break;
                    case 0:
                        $CostNew+=$WindowCount*(float)$c["WindowSum"];
                        break;
                };
        };
        //Фрамуга
        if($c["Framuga"]==1 & $od["FramugaCh"]==1)
            $CostNew+=(float)$c["FramugaSum"];
        //Навесы
        if($c["Petlya"]==1)
        {
            $PetlyaCount=0;
            $PetlyaCount+=(int)$od["WorkPetlya"];
            $PetlyaCount+=(int)$od["StvorkaPetlya"];
            if($PetlyaCount>0)
                switch ($c["PetlyaCount"]==1)
                {
                    case true://Зависит от кол-ва
                        switch ($c["PetlyaMore"])
                        {
                            case null://Не заполненно поле болшльше
                                $CostNew+=(float)$c["PetlyaSum"]*$PetlyaCount;
                                break;
                            default:
                                $PetlyaMore=(int)$c["PetlyaMore"];
                                $CostNew+=(float)$c["PetlyaSum"]*($PetlyaCount>$PetlyaMore ? $PetlyaCount-$PetlyaMore : 0);
                                break;
                        }
                        break;
                    case false:
                        $CostNew+=(float)$c["PetlyaSum"]*$PetlyaCount;
                        break;
                };
        };
        //Навесы на рабочей створке
        if($c["PetlyaWork"]==1)
        {
            $PetlyaCount=0;
            $PetlyaCount+=(int)$od["WorkPetlya"];
            if($PetlyaCount>0)
                switch ($c["PetlyaWorkCount"]==1)
                {
                    case true://Зависит от кол-ва
                        switch ($c["PetlyaWorkMore"])
                        {
                            case null://Не заполненно поле болшльше
                                $CostNew+=(float)$c["PetlyaWorkSum"]*$PetlyaCount;
                                break;
                            default:
                                $PetlyaMore=(int)$c["PetlyaWorkMore"];
                                $CostNew+=(float)$c["PetlyaWorkSum"]*($PetlyaCount>$PetlyaMore ? $PetlyaCount-$PetlyaMore : 0);
                                break;
                        }
                        break;
                    case false:
                        $CostNew+=(float)$c["PetlyaWorkSum"]*$PetlyaCount;
                        break;
                };
        };
        //Навесы на второй створке
        if($c["PetlyaStvorka"]==1)
        {
            $PetlyaCount=0;
            $PetlyaCount+=(int)$od["StvorkaPetlya"];
            if($PetlyaCount)
                switch ($c["PetlyaStvorkaCount"]==1)
                {
                    case true://Зависит от кол-ва
                        switch ($c["PetlyaStvorkaMore"])
                        {
                            case null://Не заполненно поле болшльше
                                $CostNew+=(float)$c["PetlyaStvorkaSum"]*$PetlyaCount;
                                break;
                            default:
                                $PetlyaMore=(int)$c["PetlyaStvorkaMore"];
                                $CostNew+=(float)$c["PetlyaStvorkaSum"]*($PetlyaCount>$PetlyaMore ? $PetlyaCount-$PetlyaMore : 0);
                                break;
                        }
                        break;
                    case false:
                        $CostNew+=(float)$c["PetlyaStvorkaSum"]*$PetlyaCount;
                        break;
                };
        };
        //Ребра жесткости
        if($c["Stiffener"]==1)
            switch ($c["StiffenerW"]==1)
            {
                case true://Зависит от кв.м.
                    $CostNew+=$H*$W*(float)$c["StiffenerSum"]/1000000;
                    break;
                case false:
                    $CostNew+=(float)$c["StiffenerSum"];
                    break;
            };
        //Площадь двери
        if($c["M2"]==1)
            $CostNew+=$H*$W*(float)$c["M2Sum"]/1000000;
        //Антипаника
        if($c["Antipanik"]==1 & $od["Antipanik"]==1)
            $CostNew+=$c["AntipanikSum"];
        //Отбойник
        if($c["Otboynik"]==1 & $od["Otboynik"]==1)
            $CostNew+=(float)$c["OtboynikSum"];
        //Калитка
        if($c["Wicket"]==1 & $od["Wicket"]==1)
            $CostNew+=(float)$c["WicketSum"];
        //Замок
        if($c["BoxLock"]==1 & $od["BoxLock"]==1)
            $CostNew+=(float)$c["BoxLockSum"];
        //Ответка
        if($c["Otvetka"]==1 & $od["Otvetka"]==1)
            $CostNew+=(float)$c["OtvetkaSum"];
        //Утепление
        if($c["Isolation"]==1 & $od["Isolation"]==1)
            $CostNew+=(float)$c["IsolationSum"];
        //Вент решетка

        if($c["Grid"]==1)
        {
            $CountGrid=0;
            $CountGrid+=$od["WorkUpGridCh"]==1 ? 1 : 0;
            $CountGrid+=$od["WorkDownGridCh"]==1 ? 1 : 0;
            $CountGrid+=$od["StvorkaUpGridCh"]==1 ? 1 : 0;
            $CountGrid+=$od["StvorkaDownGridCh"]==1 ? 1 : 0;
            $CountGrid+=$od["FramugaUpGridCh"]==1 ? 1 : 0;
            $CountGrid+=$od["FramugaDownGridCh"]==1 ? 1 : 0;
            $CostNew+=$c["GridCount"]==1 ? $CountGrid * (float)$c["GridSum"] : (float)$c["GridSum"];
        };
        break;
    };
echo $CostNew;
?>