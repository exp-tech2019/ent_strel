<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 21.12.2016
 * Time: 12:52
 */
    include "../params.php";
    $m=new mysqli($GlobalDBHost,$GlobalDBLogin,$GlobalDBPass,$GlobalDBName);

    switch ($_POST["Method"])
    {
        case "AddEdit":
            $TypeDoor=$_POST["TypeDoor"];
            $HWith=$_POST["HWith"];
            $HBy=$_POST["HBy"];
            $WWith=$_POST["WWith"];
            $WBy=$_POST["WBy"];
            $SEqual=$_POST["SEqual"];
            $Framug=$_POST["Framug"];
            $M2=$_POST["M2"];
            $Sum=$_POST["Sum"];

            //Выполним проверку заполненности
            $flagErr=false; $errNote="";
            if($TypeDoor=="") {$flagErr=true; $errNote.="не заполненн тип двери; ";};
            if(($HWith!="" & $HBy=="") || ($HWith=="" & $HBy!="")) {$flagErr=true; $errNote="Некорректно заполненна высота";};
            if(($WWith!="" & $WBy=="") || ($WWith=="" & $WBy!="")) {$flagErr=true; $errNote="Некорректно заполненна ширина";};
            if($Sum=="") {$flagErr=true; $errNote.="не заполненна сумма; ";};
            if(!$flagErr)
            {
                $HWith=$HWith=="" ? "NULL" : $HWith;
                $HBy=$HBy=="" ? "NULL" : $HBy;
                $WWith=$WWith=="" ? "NULL" : $WWith;
                $WBy=$WBy=="" ? "NULL" : $WBy;
                switch ($SEqual){
                    case "": $SEqual="NULL"; break;
                    case "Одностворчатая": $SEqual="0"; break;
                    case "Двухстворчатая": $SEqual="1"; break;
                };
                //Произведем создание / изменение параметра
                if($_POST["idDoorSize"]=="")
                {
                    $m->query("INSERT INTO TempCalcDoorSize (TypeDoor, HWith, HBy, WWith, WBy, SEqual, Framug, M2, Sum) VALUES('$TypeDoor', $HWith, $HBy, $WWith, $WBy, $SEqual, $Framug, $M2, $Sum)");
                    echo json_encode(array("Result"=>"ok","idDoorSize"=>$m->insert_id));
                }
                else
                {
                    $idDoorSize=$_POST["idDoorSize"];
                    $m->query("UPDATE TempCalcDoorSize SET TypeDoor='$TypeDoor', HWith=$HWith, HBy=$HBy, WWith=$WWith, WBy=$WBy, SEqual=$SEqual, Framug=$Framug, M2=$M2, Sum=$Sum WHERE id=$idDoorSize");
                    echo json_encode(array("Result"=>"ok"));
                };
            }
            else
                echo $errNote;
            break;
        case "Remove":
            $idDoorSize=$_POST["idDoorSize"];
            $m->query("DELETE FROM TempCalcDoorSize WHERE id=$idDoorSize");
            echo json_encode(array("Result"=>"ok"));
            break;
    };

?>