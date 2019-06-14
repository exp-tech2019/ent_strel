<?php
/**
 * Created by PhpStorm.
 * User: anikulshin
 * Date: 23.12.2016
 * Time: 13:10
 */
    include "../params.php";
    $m=new mysqli($GlobalDBHost,$GlobalDBLogin,$GlobalDBPass,$GlobalDBName);
    switch ($_POST["Method"]){
        case "Add":
            $Name=$_POST["Name"];
            $Currency=$_POST["Currency"];
            $Sum=$_POST["Sum"];
            //echo "INSERT INTO tempcalcfurnitura (Name, Currency, Sum) VALUES('$Name', '$Currency', $Sum)";
            $m->query("INSERT INTO tempcalcfurnitura (Name, Currency, Sum) VALUES('$Name', '$Currency', $Sum)");
            echo json_encode(array("Result"=>"ok", "idFurniture"=>$m->insert_id));
            break;
        case "Edit":
            $idFurniture=$_POST["idFurniture"];
            $Name=$_POST["Name"];
            $Currency=$_POST["Currency"];
            $Sum=$_POST["Sum"];
            $m->query("UPDATE TempCalcFurnitura SET Name='$Name', Currency='$Currency', Sum=$Sum WHERE id=$idFurniture");
            echo json_encode(array("Result"=>"ok"));
            break;
        case "Remove":
            $idFurniture=$_POST["idFurniture"];
            if($idFurniture!="")
                $m->query("DELETE FROM TempCalcFurnitura WHERE id=$idFurniture");
            echo json_encode(array("Result"=>"ok"));
            break;
    }
?>