<?php
    include "../params.php";
    $m=new mysqli($GlobalDBHost,$GlobalDBLogin,$GlobalDBPass,$GlobalDBName);
    $Action=$_POST["Action"];
    $idGood=$_POST["id"];
    $idGroup=$_POST["idGroup"];
    $Name=$_POST["Name"];
    $Unit=$_POST["Unit"];
    switch($Action){
        case "Add":
            $m->query("INSERT INTO StockNewManualGood (idGroup, Name, Unit) VALUES ($idGroup, '$Name', $Unit)");
            break;
        case "Edit":
            $m->query("UPDATE StockNewManualGood SET idGroup=$idGroup, Name='$Name', Unit=$Unit WHERE id=$idGood");
            break;
        case "Remove":
            $m->query("DELETE FROM StockNewManualGood WHERE id=$idGood");
            break;
    }
?>