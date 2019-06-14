<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");

    $idNaryad=$_GET["idNaryad"];
    $Step=$_GET["Step"];

    echo json_encode(array(
        "Result"=>"Ok",
        "idNaryad"=>$idNaryad,
        "Step"=>$Step
    ));
?>