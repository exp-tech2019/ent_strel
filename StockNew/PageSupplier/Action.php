<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    session_start();
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    switch($_POST["Action"]){
        case "Add":
            $SupplierName=$_POST["SupplierName"];
            $INN=$_POST["INN"];
            $Adress=$_POST["Adress"];
            $Phone=$_POST["Phone"];
            $m->query("INSERT INTO st_Suppliers (SupplierName, INN, Adress, Phone) VALUES ('$SupplierName', '$INN', '$Adress', '$Phone')") or die ($m->error);
            break;
        case "Update":
            $idSupplier=$_POST["idSupplier"];
            $SupplierName=$_POST["SupplierName"];
            $INN=$_POST["INN"];
            $Adress=$_POST["Adress"];
            $Phone=$_POST["Phone"];
            $m->query("UPDATE st_Suppliers SET SupplierName='$SupplierName', INN='$INN', Adress='$Adress', Phone='$Phone' WHERE id=$idSupplier") or die ($m->error);
            break;
        case "Remove":
            $idSupplier=$_POST["idSupplier"];
            $m->query("DELETE FROM st_Suppliers WHERE id=$idSupplier") or die($m->error);
            break;
    }
    echo json_encode(array("Result"=>"ok"));
?>