<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 21.12.2016
 * Time: 20:58
 */
    include "../params.php";
    $m=new mysqli($GlobalDBHost,$GlobalDBLogin,$GlobalDBPass,$GlobalDBName);
    switch ($_POST["Method"])
    {
        case "Add":
            $Name=$_POST["Name"];
            $Percent=$_POST["Percent"];
            $m->query("INSERT INTO TempCalcRal (Name, Percent) VALUES ('$Name', $Percent)");
            echo json_encode(array("Result"=>"ok", "idRal"=>$m->insert_id));
            break;
        case "Edit":
            $Name=$_POST["Name"];
            $Percent=$_POST["Percent"];
            $id=$_POST["idRal"];
            $m->query("UPDATE TempCalcRal SET Name='$Name', Percent=$Percent WHERE id=$id");
            echo json_encode(array("Result"=>"ok"));
            break;
        case "Remove":
            $id=$_POST["idRal"];
            $m->query("DELETE FROM TempCalcRal WHERE id=$id");
            echo "ok";
            break;
    }
?>