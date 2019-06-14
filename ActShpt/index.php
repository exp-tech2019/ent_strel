<?php
session_start();
$XMLParams=simplexml_load_file("../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB)
or die('Не удалось соединиться: ' . $m->error);
if(isset($_POST["Login"]) & isset($_POST["Pass"]))
{
    $d=$m->query("SELECT id, FIO, Type FROM logins WHERE Login='".$_POST["Login"]."'  AND Pass='".$_POST["Pass"]."'") or die($m->error);
    switch ($d->num_rows>0)
    {
        case true:
            $r=$d->fetch_assoc();
            $_SESSION["AutorizeFIO"]=$r["FIO"];
            $_SESSION["AutorizeLogin"]=$_POST["Login"];
            $_SESSION["AutorizeType"]=$r["Type"];
            $_SESSION["AutorizeID"]=$r["id"];
            break;
        case false:
            header('Location: Login.php');
            break;
    }
};
if((!isset($_SESSION["AutorizeFIO"]) & !isset($_SESSION["AutorizeLogin"])))
    header('Location: Login.php');
$PageLoad="ActList";
$PageLoad=isset($_GET["PageLoad"]) ? $_GET["PageLoad"] : $PageLoad;
$PageTitles=array(
    "ActAdd"=>"Акт отгрузки",
    "ActList"=>"Список актов"
);
$PageTitle=$PageTitles[$PageLoad];
include "Pages/_LayotHeader.php";

?>