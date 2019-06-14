<?php
/**
 * Created by PhpStorm.
 * User: anikulshin
 * Date: 10.01.2019
 * Time: 11:45
 */
error_reporting(E_ALL);
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/xml; charset=utf-8');
header('Content-Disposition: attachment; filename="11.xml"');
ini_set("max_input_vars", "5000");
session_start();

$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

$doc = new DOMDocument('1.0',"utf-8");
// мы хотим красивый вывод
$doc->formatOutput = true;

$arrDoorType=array();
$arrStep=array();

$root = $doc->createElement('Doc');
$root = $doc->appendChild($root);

$DoorSizes=$doc->createElement("DoorSizes");
$d=$m->query("SELECT * FROM payrolldoorsize_new");
while ($r=$d->fetch_assoc())
{
    AppendDoorType($arrDoorType, $arrStep,$r["DoorType"], $r["Step"]);

    $Door=$doc->createElement("Door");
    $DoorType=$doc->createElement("DoorType");
    $DoorType->appendChild($doc->createTextNode($r["DoorType"]));
    $Door->appendChild($DoorType);
    $Step=$doc->createElement("Step");
    $Step->appendChild($doc->createTextNode($r["Step"]));
    $Door->appendChild($Step);
    $HWith=$doc->createElement("HWith");
    $HWith->appendChild($doc->createTextNode($r["HWith"]));
    $Door->appendChild($HWith);
    $HBy=$doc->createElement("HBy");
    $HBy->appendChild($doc->createTextNode($r["HBy"]));
    $Door->appendChild($HBy);
    $WWith=$doc->createElement("WWith");
    $WWith->appendChild($doc->createTextNode($r["WWith"]));
    $Door->appendChild($WWith);
    $WBy=$doc->createElement("WBy");
    $WBy->appendChild($doc->createTextNode($r["WBy"]));
    $Door->appendChild($WBy);
    $S=$doc->createElement("S");
    $S->appendChild($doc->createTextNode($r["S"]));
    $Door->appendChild($S);
    $SWith=$doc->createElement("SWith");
    $SWith->appendChild($doc->createTextNode($r["SWith"]));
    $Door->appendChild($SWith);
    $SBy=$doc->createElement("SBy");
    $SBy->appendChild($doc->createTextNode($r["SBy"]));
    $Door->appendChild($SBy);
    $Framug=$doc->createElement("Framug");
    $Framug->appendChild($doc->createTextNode($r["Framug"]));
    $Door->appendChild($Framug);
    $Sum=$doc->createElement("Sum");
    $Sum->appendChild($doc->createTextNode($r["Sum"]));
    $Door->appendChild($Sum);

    $DoorSizes->appendChild($Door);
};
$root->appendChild($DoorSizes);
$d->close();

//Константы
$d=$m->query("SELECT * FROM payrollconstant");
$ConstList=$doc->createElement("ConstList");
while($r=$d->fetch_assoc())
{
    AppendDoorType($arrDoorType, $arrStep,$r["DoorType"], $r["Step"]);
    $Const=$doc->createElement("Const");
    $DoorType=$doc->createElement("DoorType");
    $DoorType->appendChild($doc->createTextNode($r["DoorType"]));
    $Const->appendChild($DoorType);
    $Step=$doc->createElement("Step");
    $Step->appendChild($doc->createTextNode($r["Step"]));
    $Const->appendChild($Step);
    $Name=$doc->createElement("Name");
    $Name->appendChild($doc->createTextNode($r["Name"]));
    $Const->appendChild($Name);
    $Sum=$doc->createElement("Sum");
    $Sum->appendChild($doc->createTextNode($r["Sum"]));
    $Const->appendChild($Sum);
    $ConstList->appendChild($Const);
};
$root->appendChild($ConstList);
$d->close();

$d=$m->query("SELECT * FROM payrollconstruct");
$ConstructList=$doc->createElement("ConstructList");
while ($r=$d->fetch_assoc())
{
    $Construct=$doc->createElement("Construct");
    AppendDoorType($arrDoorType, $arrStep,$r["DoorType"], $r["Step"]);
    $Const=$doc->createElement("Const");
    $DoorType=$doc->createElement("DoorType");
    $DoorType->appendChild($doc->createTextNode($r["DoorType"]));
    $Construct->appendChild($DoorType);

    $Step=$doc->createElement("Step");
    $Step->appendChild($doc->createTextNode($r["Step"]));
    $Construct->appendChild($Step);

    $Frame=$doc->createElement("Frame");
    $Frame->appendChild($doc->createTextNode($r["Frame"]));
    $Construct->appendChild($Frame);

    $FrameCount=$doc->createElement("FrameCount");
    $FrameCount->appendChild($doc->createTextNode($r["FrameCount"]));
    $Construct->appendChild($FrameCount);

    $FrameSum=$doc->createElement("FrameSum");
    $FrameSum->appendChild($doc->createTextNode($r["FrameSum"]));
    $Construct->appendChild($FrameSum);

    $Dovod=$doc->createElement("Dovod");
    $Dovod->appendChild($doc->createTextNode($r["Dovod"]));
    $Construct->appendChild($Dovod);

    $DovodPreparation=$doc->createElement("DovodPreparation");
    $DovodPreparation->appendChild($doc->createTextNode($r["DovodPreparation"]));
    $Construct->appendChild($DovodPreparation);

    $DovodSum=$doc->createElement("DovodSum");
    $DovodSum->appendChild($doc->createTextNode($r["DovodSum"]));
    $Construct->appendChild($DovodSum);

    $Nalichnik=$doc->createElement("Nalichnik");
    $Nalichnik->appendChild($doc->createTextNode($r["Nalichnik"]));
    $Construct->appendChild($Nalichnik);

    $NalichnikSum=$doc->createElement("NalichnikSum");
    $NalichnikSum->appendChild($doc->createTextNode($r["NalichnikSum"]));
    $Construct->appendChild($NalichnikSum);

    $Window=$doc->createElement("Window");
    $Window->appendChild($doc->createTextNode($r["Window"]));
    $Construct->appendChild($Window);

    $WindowCount=$doc->createElement("WindowCount");
    $WindowCount->appendChild($doc->createTextNode($r["WindowCount"]));
    $Construct->appendChild($WindowCount);

    $WindowMore=$doc->createElement("WindowMore");
    $WindowMore->appendChild($doc->createTextNode($r["WindowMore"]));
    $Construct->appendChild($WindowMore);

    $WindowSum=$doc->createElement("WindowSum");
    $WindowSum->appendChild($doc->createTextNode($r["WindowSum"]));
    $Construct->appendChild($WindowSum);

    $Framuga=$doc->createElement("Framuga");
    $Framuga->appendChild($doc->createTextNode($r["Framuga"]));
    $Construct->appendChild($Framuga);

    $FramugaSum=$doc->createElement("FramugaSum");
    $FramugaSum->appendChild($doc->createTextNode($r["FramugaSum"]));
    $Construct->appendChild($FramugaSum);

    $Petlya=$doc->createElement("Petlya");
    $Petlya->appendChild($doc->createTextNode($r["Petlya"]));
    $Construct->appendChild($Petlya);

    $PetlyaCount=$doc->createElement("PetlyaCount");
    $PetlyaCount->appendChild($doc->createTextNode($r["PetlyaCount"]));
    $Construct->appendChild($PetlyaCount);

    $PetlyaMore=$doc->createElement("PetlyaMore");
    $PetlyaMore->appendChild($doc->createTextNode($r["PetlyaMore"]));
    $Construct->appendChild($PetlyaMore);

    $PetlyaSum=$doc->createElement("PetlyaSum");
    $PetlyaSum->appendChild($doc->createTextNode($r["PetlyaSum"]));
    $Construct->appendChild($PetlyaSum);

    $Stiffener=$doc->createElement("Stiffener");
    $Stiffener->appendChild($doc->createTextNode($r["Stiffener"]));
    $Construct->appendChild($Stiffener);

    $StiffenerW=$doc->createElement("StiffenerW");
    $StiffenerW->appendChild($doc->createTextNode($r["StiffenerW"]));
    $Construct->appendChild($StiffenerW);

    $StiffenerSum=$doc->createElement("StiffenerSum");
    $StiffenerSum->appendChild($doc->createTextNode($r["StiffenerSum"]));
    $Construct->appendChild($StiffenerSum);

    $M2=$doc->createElement("M2");
    $M2->appendChild($doc->createTextNode($r["M2"]));
    $Construct->appendChild($M2);

    $M2Sum=$doc->createElement("M2Sum");
    $M2Sum->appendChild($doc->createTextNode($r["M2Sum"]));
    $Construct->appendChild($M2Sum);

    $Antipanik=$doc->createElement("Antipanik");
    $Antipanik->appendChild($doc->createTextNode($r["Antipanik"]));
    $Construct->appendChild($Antipanik);

    $AntipanikSum=$doc->createElement("AntipanikSum");
    $AntipanikSum->appendChild($doc->createTextNode($r["AntipanikSum"]));
    $Construct->appendChild($AntipanikSum);

    $Otboynik=$doc->createElement("Otboynik");
    $Otboynik->appendChild($doc->createTextNode($r["Otboynik"]));
    $Construct->appendChild($Otboynik);

    $OtboynikSum=$doc->createElement("OtboynikSum");
    $OtboynikSum->appendChild($doc->createTextNode($r["OtboynikSum"]));
    $Construct->appendChild($OtboynikSum);

    $Wicket=$doc->createElement("Wicket");
    $Wicket->appendChild($doc->createTextNode($r["Wicket"]));
    $Construct->appendChild($Wicket);

    $WicketSum=$doc->createElement("WicketSum");
    $WicketSum->appendChild($doc->createTextNode($r["WicketSum"]));
    $Construct->appendChild($WicketSum);

    $BoxLock=$doc->createElement("BoxLock");
    $BoxLock->appendChild($doc->createTextNode($r["BoxLock"]));
    $Construct->appendChild($BoxLock);

    $BoxLockSum=$doc->createElement("BoxLockSum");
    $BoxLockSum->appendChild($doc->createTextNode($r["BoxLockSum"]));
    $Construct->appendChild($BoxLockSum);

    $Otvetka=$doc->createElement("Otvetka");
    $Otvetka->appendChild($doc->createTextNode($r["Otvetka"]));
    $Construct->appendChild($Otvetka);

    $OtvetkaSum=$doc->createElement("OtvetkaSum");
    $OtvetkaSum->appendChild($doc->createTextNode($r["OtvetkaSum"]));
    $Construct->appendChild($OtvetkaSum);

    $PetlyaWork=$doc->createElement("PetlyaWork");
    $PetlyaWork->appendChild($doc->createTextNode($r["PetlyaWork"]));
    $Construct->appendChild($PetlyaWork);

    $Isolation=$doc->createElement("Isolation");
    $Isolation->appendChild($doc->createTextNode($r["Isolation"]));
    $Construct->appendChild($Isolation);

    $IsolationSum=$doc->createElement("IsolationSum");
    $IsolationSum->appendChild($doc->createTextNode($r["IsolationSum"]));
    $Construct->appendChild($IsolationSum);

    $PetlyaWorkCount=$doc->createElement("PetlyaWorkCount");
    $PetlyaWorkCount->appendChild($doc->createTextNode($r["PetlyaWorkCount"]));
    $Construct->appendChild($PetlyaWorkCount);

    $PetlyaWorkMore=$doc->createElement("PetlyaWorkMore");
    $PetlyaWorkMore->appendChild($doc->createTextNode($r["PetlyaWorkMore"]));
    $Construct->appendChild($PetlyaWorkMore);

    $PetlyaWorkSum=$doc->createElement("PetlyaWorkSum");
    $PetlyaWorkSum->appendChild($doc->createTextNode($r["PetlyaWorkSum"]));
    $Construct->appendChild($PetlyaWorkSum);

    $PetlyaStvorka=$doc->createElement("PetlyaStvorka");
    $PetlyaStvorka->appendChild($doc->createTextNode($r["PetlyaStvorka"]));
    $Construct->appendChild($PetlyaStvorka);

    $PetlyaStvorkaCount=$doc->createElement("PetlyaStvorkaCount");
    $PetlyaStvorkaCount->appendChild($doc->createTextNode($r["PetlyaStvorkaCount"]));
    $Construct->appendChild($PetlyaStvorkaCount);

    $PetlyaStvorkaMore=$doc->createElement("PetlyaStvorkaMore");
    $PetlyaStvorkaMore->appendChild($doc->createTextNode($r["PetlyaStvorkaMore"]));
    $Construct->appendChild($PetlyaStvorkaMore);

    $PetlyaStvorkaSum=$doc->createElement("PetlyaStvorkaSum");
    $PetlyaStvorkaSum->appendChild($doc->createTextNode($r["PetlyaStvorkaSum"]));
    $Construct->appendChild($PetlyaStvorkaSum);

    $Grid=$doc->createElement("Grid");
    $Grid->appendChild($doc->createTextNode($r["Grid"]));
    $Construct->appendChild($Grid);

    $GridCount=$doc->createElement("GridCount");
    $GridCount->appendChild($doc->createTextNode($r["GridCount"]));
    $Construct->appendChild($GridCount);

    $GridSum=$doc->createElement("GridSum");
    $GridSum->appendChild($doc->createTextNode($r["GridSum"]));
    $Construct->appendChild($GridSum);

    $ConstructList->appendChild($Construct);
};
$root->appendChild($ConstructList);

//Сохраним список дверей
$DoorTypeList=$doc->createElement("DoorTypeList");
foreach ($arrDoorType as $doorType)
{
    $td=$doc->createElement("DoorType");
    $td->appendChild($doc->createTextNode($doorType));
    $DoorTypeList->appendChild($td);
};
$root->appendChild($DoorTypeList);
//Сохраним список тех этапов
$StepList=$doc->createElement("StepList");
foreach ($arrStep as $step)
{
    $s=$doc->createElement("Step");
    $s->appendChild($doc->createTextNode($step));
    $StepList->appendChild($s);
};
$root->appendChild($StepList);

echo $doc->saveXML();

function AppendDoorType(&$arrDoorType, &$arrStep, $DoorType, $Step)
{
    $flagDoorType=false;
    foreach ($arrDoorType as $d)
        if($d==$DoorType)
            $flagDoorType=true;
    if(!$flagDoorType)
        $arrDoorType[]=$DoorType;

    $flagStep=false;
    foreach ($arrStep as $s)
        if($s==$Step)
            $flagStep=true;
    if(!$flagStep)
        $arrStep[]=$Step;
}
?>