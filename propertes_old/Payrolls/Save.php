<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 08.01.2019
 * Time: 12:41
 */
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
ini_set("max_input_vars", "5000");
session_start();

$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

$TypeDoor=$_POST["TypeDoor"];
$StepName=$_POST["StepName"];

$m->query("DELETE FROM payrolldoorsize_new WHERE DoorType='$TypeDoor' AND Step='$StepName'");
if(isset($_POST["DoorSize"]))
    foreach ($_POST["DoorSize"] as $door)
    {
        $HWith=$door["HeightWith"]=="" ? "NULL" : $door["HeightWith"];
        $HBy=$door["HeightBy"]=="" ? "NULL" : $door["HeightBy"];

        $WWith=$door["WidthWith"]=="" ? "NULL" : $door["WidthWith"];
        $WBy=$door["WidthBy"]=="" ? "NULL" : $door["WidthBy"];

        $S=$door["StvorkaSelect"]=="" ? "NULL" : $door["StvorkaSelect"];
        $SWith=$door["StvorkaWith"]=="" ? "NULL" : $door["StvorkaWith"];
        $SBy=$door["StvorkaBy"]=="" ? "NULL" : $door["StvorkaBy"];

        $Framuga=$door["Framuga"];

        $Sum=$door["Sum"];

        $m->query("INSERT INTO payrolldoorsize_new (DoorType, Step, HWith, HBy, WWith, WBy, S, SWith, SBy, Framug, Sum) VALUES('$TypeDoor', '$StepName', $HWith, $HBy, $WWith, $WBy, $S, $SWith, $SBy, $Framuga, $Sum)") or die($m->error);
    };
//Константы
$m->query("DELETE FROM payrollconstant WHERE DoorType='$TypeDoor' AND Step='$StepName'");
if(isset($_POST["Const"]))
    foreach ($_POST["Const"] as $const)
    {
        $Note=$const["Note"];
        $Sum=$const["Sum"];
        $m->query("INSERT INTO payrollconstant (DoorType, Step, Name, Sum) VALUES('$TypeDoor', '$StepName', '$Note', $Sum)");
    };

//---Сохранение таблицы Конструкция двери---
$m->query("DELETE FROM PayrollConstruct WHERE DoorType='$TypeDoor' AND Step='$StepName'");
$m->query("INSERT INTO PayrollConstruct ".
    "( DoorType, Step, Frame, FrameCount, FrameSum, Dovod, DovodPreparation, DovodSum, Nalichnik, NalichnikSum, Window, WindowCount, WindowMore, WindowSum, Framuga, FramugaSum, Petlya, PetlyaCount, PetlyaMore, PetlyaSum, PetlyaWork, PetlyaWorkCount, PetlyaWorkMore, PetlyaWorkSum, PetlyaStvorka, PetlyaStvorkaCount, PetlyaStvorkaMore, PetlyaStvorkaSum, Stiffener, StiffenerW, StiffenerSum, M2, M2Sum, Antipanik, AntipanikSum, Otboynik, OtboynikSum, Wicket, WicketSum, BoxLock, BoxLockSum, Otvetka, OtvetkaSum, Isolation, IsolationSum, Grid, GridCount, GridSum) VALUE ('$TypeDoor', '$StepName',".
    "".$_POST["ConstrFrame"]." , ".
    "".$_POST["ConstrFrameCount"]." , ".
    "".$_POST["ConstrFrameSum"]." , ".

    "".$_POST["ConstrDovod"]." , ".
    "".$_POST["ConstrDovodPreparation"]." , ".
    "".$_POST["ConstrDovodSum"]." , ".

    "".$_POST["ConstrNalichnik"]." , ".
    "".$_POST["ConstrNalichnikSum"]." , ".

    "".$_POST["ConstrWindow"]." , ".
    "".$_POST["ConstrWindowCount"]." , ".
    "".$_POST["ConstrWindowMore"]." , ".
    "".$_POST["ConstrWindowSum"]." , ".

    "".$_POST["ConstrFramuga"]." , ".
    "".$_POST["ConstrFramugaSum"]." , ".
    //Навесы
    "".$_POST["ConstrPetlya"]." , ".
    "".$_POST["ConstrPetlyaCount"]." , ".
    "".$_POST["ConstrPetlyaMore"]." , ".
    "".$_POST["ConstrPetlyaSum"]." , ".
    //Навесы на рабочей створке
    "".$_POST["ConstrWorkPetlya"]." , ".
    "".$_POST["ConstrWorkPetlyaCount"]." , ".
    "".$_POST["ConstrWorkPetlyaMore"]." , ".
    "".$_POST["ConstrWorkPetlyaSum"]." , ".
    //Навесы на второй створке
    "".$_POST["ConstrStvorkaPetlya"]." , ".
    "".$_POST["ConstrStvorkaPetlyaCount"]." , ".
    "".$_POST["ConstrStvorkaPetlyaMore"]." , ".
    "".$_POST["ConstrStvorkaPetlyaSum"]." , ".
    //Ребра жесткости
    "".$_POST["ConstrStiffener"]." , ".
    "".$_POST["ConstrStiffenerW"]." , ".
    "".$_POST["ConstrStiffenerSum"].",  ".
    //Площадь двери
    "".$_POST["ConstructM2"].", ".
    "".$_POST["ConstructM2Sum"].", ".
    //Антипаника
    "".$_POST["Antipanik"].", ".
    "".$_POST["AntipanikSum"].", ".
    //Отбойник
    "".$_POST["Otboynik"].", ".
    "".$_POST["OtboynikSum"].", ".
    //Калитка
    "".$_POST["Wicket"].", ".
    "".$_POST["WicketSum"].", ".
    //Врезка замка
    "".$_POST["BoxLock"].", ".
    "".$_POST["BoxLockSum"].", ".
    //Отвветка
    "".$_POST["Otvetka"].", ".
    "".$_POST["OtvetkaSum"].", ".
    //Утепление
    "".$_POST["Isolation"].", ".
    "".$_POST["IsolationSum"]." , ".
    //Вент. решетка
    "".$_POST["Grid"]." , ".
    "".$_POST["GridCount"]." , ".
    "".$_POST["GridSum"]." ".
    ")");


?>