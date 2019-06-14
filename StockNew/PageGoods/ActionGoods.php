<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    session_start();
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $Result=array();
    switch($_POST["Action"]){
        case "Add":
            $idGroup=$_POST["idGroup"];
            $GoodName=$_POST["GoodName"];
            $Article=$_POST["Article"];
            $BarCode=$_POST["BarCode"];
            $Unit=$_POST["Unit"];
            $m->query("INSERT INTO st_goods (idGroup, GoodName, Article, BarCode, Unit) VALUES($idGroup, '$GoodName', '$Article', '$BarCode', $Unit)") or die($m->error);
            break;
        case "Update":
            $idGood=$_POST["idGood"];
            $idGroup=$_POST["idGroup"];
            $Article=$_POST["Article"];
            $GoodName=$_POST["GoodName"];
            $BarCode=$_POST["BarCode"];
            $Unit=$_POST["Unit"];

            $m->query("UPDATE st_Goods SET idGroup=$idGroup, Article='$Article', GoodName='$GoodName', BarCode='$BarCode', Unit=$Unit WHERE id=$idGood") or die($m->error);
            break;
        case "Remove":
            $idGood=$_POST["idGood"];
            $m->query("DELETE FROM st_Goods WHERE id=$idGood") or die($m->error);
            break;
    };
    $Result["Result"]="ok";
    echo json_encode($Result);
?>