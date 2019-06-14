<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 18.07.2018
 * Time: 20:05
 */
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
if(isset($_POST["GroupName"]))
    if($_POST["GroupName"]){
        $GroupName=$_POST["GroupName"];
        $m->query("INSERT INTO Sp18Groups (GroupName) VALUES('$GroupName')");
        echo $m->insert_id;
    }
    else
        echo "Error";
?>