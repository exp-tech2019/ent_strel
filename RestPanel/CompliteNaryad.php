<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    include "param.php";
    $param=new GlobalParam();
    $XMLParams=simplexml_load_file("../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $idNaryad=$_GET["idNaryad"];
    $idWorker=$_GET["idWorker"];
    $Step=null;
    switch ($_GET["StepWhere"]){
        case "Sgibka": $Step=2; break;
        case "Svarka": $Step=3; break;
        case "Frame": $Step=4; break;
        case "Sborka": $Step=5; break;
        case "Color": $Step=6; break;
        case "Upak": $Step=7; break;
        case "Shpt": $Step=8; break;
        case "Mdf": $Step=9; break;
        case "MdfSborka": $Step=10; break;
    };
    $StepWhereArr=array(
        "Sgibka"=>"SgibkaCompliteFlag",
        "Svarka"=>"SvarkaCompliteFlag",
        "Frame"=>"FrameCompliteFlag",
        "Sborka"=>"SborkaCompliteFlag",
        "Color"=>"ColorCompliteFlag",
        "Upak"=>"UpakCompliteFlag",
        "Shpt"=>"ShptCompliteFlag",
        "Mdf"=>"MdfCompliteFlag",
        "MdfSborka"=>"SborkaMdfCompliteFlag"
    );
    if($Step!=null){
        $m->autocommit(false);
        $m->query("UPDATE NaryadComplite SET idWorker=$idWorker, DateComplite=NOW() WHERE idNaryad=$idNaryad AND  Step=$Step") or die($m->error);
        $m->query("UPDATE Naryad SET ".$StepWhereArr[$_GET["StepWhere"]]."=1 WHERE id=$idNaryad") or die($m->error);
        $m->commit();
        $d=$m->query("SELECT * FROM Naryad WHERE id=$idNaryad");
        $r=$d->fetch_assoc();
        $NextStep="AllComplite";
        if($r["SgibkaCompliteFlag"]==0) $NextStep="Гибка";
        if($r["SvarkaCompliteFlag"]==0) $NextStep="Сварка";
        if($r["FrameCompliteFlag"]!=null & $r["FrameCompliteFlag"]==0) $NextStep="Рамка";
        if($r["SborkaCompliteFlag"]==0) $NextStep="Сборка";
        if($r["ColorCompliteFlag"]==0) $NextStep="Покраска";
        if($r["UpakCompliteFlag"]==0) $NextStep="Упаковка";
        if($r["ShptCompliteFlag"]==0) $NextStep="Отгрузка";
        if($r["MdfCompliteFlag"]!=null & $r["MdfCompliteFlag"]==0) $NextStep="МДФ";
        if($r["SborkaMdfCompliteFlag"]!=null & $r["SborkaMdfCompliteFlag"]==0) $NextStep="Сборка МДФ";
        echo json_encode(array("Result"=>"ok", "NextStep"=>$NextStep));
    };
?>