<?php
/**
 * Created by PhpStorm.
 * User: anikulshin
 * Date: 22.12.2016
 * Time: 15:29
 */
    include "../params.php";
    $m=new mysqli($GlobalDBHost,$GlobalDBLogin,$GlobalDBPass,$GlobalDBName);

    switch ($_POST["Method"])
    {
        case "Add":
            $Name=$_POST["Name"];
            $HWith=$_POST["HWith"]!="" ? $_POST["HWith"] : "NULL";
            $HBy=$_POST["HBy"]!="" ? $_POST["HBy"] : "NULL";
            $WWith=$_POST["WWith"]!="" ? $_POST["WWith"] : "NULL";
            $WBy=$_POST["WBy"]!="" ? $_POST["WBy"] : "NULL";
            $SEqual=SEqualToInt($_POST["SEqual"]);
            $Sum=$_POST["Sum"];
            $m->query("INSERT INTO TempCalcNaves (Name, HWith, HBy, WWith, WBy, SEqual, Sum) VALUES('$Name', $HWith, $HBy, $WWith, $WBy, $SEqual, $Sum)");
            echo json_encode(array("Result"=>"ok", "idNaves"=>$m->insert_id));
            break;
        case "Edit":
            $idNaves=$_POST["idNaves"];
            $Name=$_POST["Name"];
            $HWith=$_POST["HWith"]!="" ? $_POST["HWith"] : "NULL";
            $HBy=$_POST["HBy"]!="" ? $_POST["HBy"] : "NULL";
            $WWith=$_POST["WWith"]!="" ? $_POST["WWith"] : "NULL";
            $WBy=$_POST["WBy"]!="" ? $_POST["WBy"] : "NULL";
            $SEqual=SEqualToInt($_POST["SEqual"]);
            $Sum=$_POST["Sum"];
            $m->query("UPDATE TempCalcNaves SET Name='$Name', HWith=$HWith, HBy=$HBy, WWith=$WWith, WBy=$WBy, SEqual=$SEqual, Sum=$Sum WHERE id=$idNaves");
            echo json_encode(array("Result"=>"ok"));
            break;
        case "Remove":
            $idNaves=$_POST["idNaves"];
            $m->query("DELETE FROM TempCalcNaves WHERE id=$idNaves");
            echo "ok";
            break;
    }
    //Преобразует в число
    function SEqualToInt($s){
        $Ret="NULL";
        switch ($s){
            case "": $Ret="NULL"; break;
            case "Одностворчатая": $Ret="NULL"; break;
            case "Двухстворчатая": $Ret="NULL"; break;
        };
        return $Ret;
    }
?>