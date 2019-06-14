<?php
/**
 * Created by PhpStorm.
 * User: anikulshin
 * Date: 23.12.2016
 * Time: 14:14
 */
    include "../params.php";
    $m=new mysqli($GlobalDBHost,$GlobalDBLogin,$GlobalDBPass,$GlobalDBName);
    switch ($_POST["Method"]){
        case "Add":
            $Name=$_POST["Name"];
            $M2=$_POST["M2"];
            $Sum=$_POST["Sum"];
            $SumPlus=$_POST["SumPlus"]!="" ? $_POST["SumPlus"] : "NULL";
            $m->query("INSERT INTO TempCalcGlass (Name, M2, Sum, SumPlus) VALUES('$Name', $M2, $Sum, $SumPlus)");
            echo json_encode(array("Result"=>"ok", "idGlass"=>$m->insert_id));
            break;
        case "Edit":
            $idGlass=$_POST["idGlass"];
            $Name=$_POST["Name"];
            $M2=$_POST["M2"];
            $Sum=$_POST["Sum"];
            $SumPlus=$_POST["SumPlus"]!="" ? $_POST["SumPlus"] : "NULL";
            $m->query("UPDATE TempCalcGlass SET Name='$Name', M2=$M2, Sum=$Sum, SumPlus=$SumPlus WHERE id=$idGlass");
            echo json_encode(array("Result"=>"ok"));
            break;
        case "Remove":
            $idGlass=$_POST["idGlass"];
            if($idGlass!="")
                $m->query("DELETE FROM TempCalcGlass WHERE id=$idGlass");
            echo json_encode(array("Result"=>"ok"));
            break;
    }
?>