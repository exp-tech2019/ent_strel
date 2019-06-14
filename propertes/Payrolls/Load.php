<?php
/**
 * Created by PhpStorm.
 * User: anikulshin
 * Date: 10.01.2019
 * Time: 9:41
 */
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
ini_set("max_input_vars", "5000");
session_start();

$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

$TypeDoor=$_POST["TypeDoor"];
$StepName=$_POST["StepName"];
$Action=!isset($_POST["Action"]) ? "Step" : $_POST["Action"];

$arrDoorSize=array();
$DoorSizeTable=$Action=="Step" ? "payrolldoorsize_new" : "payrolldoorsize_dolgnost";
$d=$m->query("SELECT * FROM $DoorSizeTable WHERE DoorType='$TypeDoor' AND $Action='$StepName'");
if($d->num_rows>0)
    while ($r=$d->fetch_assoc())
    {
        $HWith=$r["HWith"]==null ? "" : $r["HWith"];
        $HBy=$r["HBy"]==null ? "" : $r["HBy"];

        $WWith=$r["WWith"]==null ? "" : $r["WWith"];
        $WBy=$r["WBy"]==null ? "" : $r["WBy"];

        $S=$r["S"];
        $SWith=$r["SWith"]==null ? "" : $r["SWith"];
        $SBy=$r["SBy"]==null ? "" : $r["SBy"];

        $Framug=$r["Framug"] ? 1 : 0;
        $Sum=$r["Sum"];
        $arrDoorSize[]=array(
            "HWith"=>$HWith,
            "HBy"=>$HBy,
            "WWith"=>$WWith,
            "WBy"=>$WBy,

            "S"=>$S,
            "SWith"=>$SWith,
            "SBy"=>$SBy,

            "Framug"=>$Framug,
            "Sum"=>$Sum
        );
    };
$d->close();

//Константы
$arrConst=array();
$ConstatnTable=$Action=="Step" ? "payrollconstant" : "payrollconstant_dolgnost";
$d=$m->query("SELECT * FROM $ConstatnTable WHERE DoorType='$TypeDoor' AND $Action='$StepName'");
if($d->num_rows>0)
    while ($r=$d->fetch_assoc())
        $arrConst[]=array(
            "Note"=>$r["Name"],
            "Sum"=>$r["Sum"]
        );
$d->close();

//Конструкция двери
$arrConstruct=null;
$ConstructTable=$Action=="Step" ? "PayrollConstruct" : "payrollconstruct_dolgnost";
$d=$m->query("SELECT * FROM $ConstructTable WHERE DoorType='$TypeDoor' AND $Action='$StepName'");
if($d->num_rows>0)
{
    $r=$d->fetch_assoc();
    $arrConstruct=array(
        "Frame"=>$r["Frame"],
        "FrameCount"=>$r["FrameCount"],
        "FrameSum"=>$r["FrameSum"],

        "Dovod"=>$r["Dovod"],
        "DovodPreparation"=>$r["DovodPreparation"],
        "DovodSum"=>$r["DovodSum"],

        "Nalichnik"=>$r["Nalichnik"],
        "NalichnikSum"=>$r["NalichnikSum"],

        "Window"=>$r["Window"],
        "WindowCount"=>$r["WindowCount"],
        "WindowMore"=>$r["WindowMore"],
        "WindowSum"=>$r["WindowSum"],

        "Framuga"=>$r["Framuga"],
        "FramugaSum"=>$r["FramugaSum"],

        "Petlya"=>$r["Petlya"],
        "PetlyaCount"=>$r["PetlyaCount"],
        "PetlyaMore"=>$r["PetlyaMore"],
        "PetlyaSum"=>$r["PetlyaSum"],

        "PetlyaWork"=>$r["PetlyaWork"],
        "PetlyaWorkCount"=>$r["PetlyaWorkCount"],
        "PetlyaWorkMore"=>$r["PetlyaWorkMore"],
        "PetlyaWorkSum"=>$r["PetlyaWorkSum"],

        "PetlyaStvorka"=>$r["PetlyaStvorka"],
        "PetlyaStvorkaCount"=>$r["PetlyaStvorkaCount"],
        "PetlyaStvorkaMore"=>$r["PetlyaStvorkaMore"],
        "PetlyaStvorkaSum"=>$r["PetlyaStvorkaSum"],

        "Stiffener"=>$r["Stiffener"],
        "StiffenerW"=>$r["StiffenerW"],
        "StiffenerSum"=>$r["StiffenerSum"],

        "M2"=>$r["M2"],
        "M2Sum"=>$r["M2Sum"],

        "Antipanik"=>$r["Antipanik"],
        "AntipanikSum"=>$r["AntipanikSum"],

        "Otboynik"=>$r["Otboynik"],
        "OtboynikSum"=>$r["OtboynikSum"],

        "Wicket"=>$r["Wicket"],
        "WicketSum"=>$r["WicketSum"],

        "BoxLock"=>$r["BoxLock"],
        "BoxLockSum"=>$r["BoxLockSum"],

        "Otvetka"=>$r["Otvetka"],
        "OtvetkaSum"=>$r["OtvetkaSum"],

        "Isolation"=>$r["Isolation"],
        "IsolationSum"=>$r["IsolationSum"],

        "Grid"=>$r["Grid"],
        "GridCount"=>$r["GridCount"],
        "GridSum"=>$r["GridSum"]
    );
};
$d->close();

echo json_encode(array(
    "TypeDoor"=>$TypeDoor,
    "StepName"=>$StepName,
    "DoorSize"=>$arrDoorSize,
    "Const"=>$arrConst,
    "Construct"=>$arrConstruct
));
?>