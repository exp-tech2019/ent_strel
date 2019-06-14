<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 23.12.2016
 * Time: 18:32
 */
    include "../params.php";
    $m=new mysqli($GlobalDBHost,$GlobalDBLogin,$GlobalDBPass,$GlobalDBName);
    switch ($_POST["Method"]){
        case "Add":
            $Name=$_POST["Name"];
            $Sum=$_POST["Sum"];
            $m->query("INSERT INTO TempCalcOther (Name, Sum) VALUES('$Name', $Sum)");
            echo json_encode(array("Result"=>"ok", "idGlass"=>$m->insert_id));
            break;
        case "Edit":
            $idOther=$_POST["idOther"];
            $Name=$_POST["Name"];
            $Sum=$_POST["Sum"];
            $m->query("UPDATE TempCalcOther SET Name='$Name', Sum=$Sum WHERE id=$idOther");
            echo json_encode(array("Result"=>"ok"));
            break;
        case "Remove":
            $idOther=$_POST["idOther"];
            if($idOther!="")
                $m->query("DELETE FROM TempCalcOther WHERE id=$idOther");
            echo json_encode(array("Result"=>"ok"));
            break;
    }
?>