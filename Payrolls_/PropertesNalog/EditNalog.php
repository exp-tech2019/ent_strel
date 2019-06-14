<?php
    session_start();
    include "../params.php";
    $m=new mysqli($GlobalDBHost,$GlobalDBLogin,$GlobalDBPass,$GlobalDBName);
    $idDolgnost=$_POST["idDolgnost"];
    $NalogPercent=$_POST["NalogPercent"];
    $m->query("UPDATE ManualDolgnost SET NalogPercent=$NalogPercent WHERE id=$idDolgnost") or die($m->error);
?>