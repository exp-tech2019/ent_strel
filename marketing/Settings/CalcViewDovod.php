<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 21.12.2016
 * Time: 21:27
 */
    include "../params.php";
    $m=new mysqli($GlobalDBHost,$GlobalDBLogin,$GlobalDBPass,$GlobalDBName);
    $d=$m->query("SELECT Count(*) AS C FROM TempCalcDovod");
    $r=$d->fetch_assoc();
    if($r["C"]==0) $m->query("INSERT INTO TempCalcDovod VALUES(0,0,0)");
    $DovodNo=$_POST["DovodNo"]!="" ? $_POST["DovodNo"] : 0;
    $DovodPodgotovka=$_POST["DovodPodgotovka"] !="" ? $_POST["DovodPodgotovka"] : 0;
    $DovodYes=$_POST["DovodYes"]!="" ? $_POST["DovodYes"] : 0;
    $m->query("UPDATE TempCalcDovod SET DovodNo=$DovodNo, DovodPodgotovka=$DovodPodgotovka, DovodYes=$DovodYes");
    echo "ok";
?>