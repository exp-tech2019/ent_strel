<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    session_start();
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $Result=array();
    switch($_POST["Action"]){
        case "Add":
            $GroupName=$_POST["GroupName"];
            $Step=$_POST["Step"];
            $AutoUnset=$_POST["AutoUnset"];
            $m->query("INSERT INTO st_goodgroups (GroupName, Step, AutoUnset) VALUES('$GroupName', $Step, $AutoUnset)") or die($m->error);
            break;
        case "Update":
            $idGroup=$_POST["idGroup"];
            $GroupName=$_POST["GroupName"];
            $Step=$_POST["Step"];
            $AutoUnset=$_POST["AutoUnset"];
            $m->query("UPDATE st_GoodGroups SET GroupName='$GroupName', Step=$Step, AutoUnset=$AutoUnset WHERE id=$idGroup") or die($m->error);
            break;
        case "Remove":
            $idGroup=$_POST["idGroup"];
            $m->query("DELETE FROM st_GoodGroups WHERE id=$idGroup") or die($m->error);
            break;
    };
    $Result["Result"]="ok";

    echo json_encode($Result);
?>