<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 22.07.2018
 * Time: 22:11
 */
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
ini_set("max_input_vars", "5000");
session_start();

$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

$idDoor=$_POST["idDoor"];
$TypeDoor=$_POST["TypeDoor"];
$H=$_POST["H"];
$W=$_POST["W"];
$Petlya=$_POST["Petlya"];
$Window=$_POST["Window"];

$arr=array();
//Зададим массив групп и их наименований
/*
$d=$m->query("SELECT * FROM sp18groups");
$arrGroups=array();
while($r=$d->fetch_assoc())
    $arrGroups[]=array(
        "idGroup"=>$r["id"],
        "GroupName"=>$r["GroupName"]
    );
$d->close();
*/
//Сделаем запрос, была ли ранее созданнная спека
$flagCreated=false;
//$d=$m->query("SELECT sp.*, gr.GroupName, m.MaterialName FROM Sp18Doors sp, sp18groups gr, sp18materials1c m WHERE idDoor=$idDoor AND sp.idMaterial=m.id AND gr.id=sp.idGroup");
$d=$m->query("SELECT t.*, m.MaterialName FROM
 (SELECT sp.*, gr.GroupName FROM Sp18Doors sp, sp18groups gr WHERE idDoor=$idDoor AND gr.id=sp.idGroup) t
LEFT JOIN sp18materials1c m
ON t.idMaterial=m.id");
while ($r=$d->fetch_assoc()){
    $flagCreated=true;

    $arr[]=array(
        "idGroup"=>$r["idGroup"],
        "GroupName"=>$r["GroupName"],
        "idMaterial1c"=>$r["idMaterial"]==null ? "" : $r["idMaterial"],
        "MaterialName"=>$r["MaterialName"]==null ? "" : $r["MaterialName"],
        "Count"=>$r["Count"],
        "Groups1c"=>array()
    );
}
//Запросим шаблоны для спецификации
$arrTemplate=array();
$d=$m->query("SELECT sp.*, m.MaterialName FROM Sp18Equation sp, sp18materials1c m WHERE sp.TypeDoor='$TypeDoor' AND sp.idMaterial=m.id");
while($r=$d->fetch_assoc())
    $arrTemplate[]=array(
        "idGroup"=>$r["idGroup"],
        "idMaterial"=>$r["idMaterial"],
        "MaterialName"=>$r["MaterialName"]
    );
$d->close();
//Если спецификация ранее не создавалась, то построим ее
if(!$flagCreated) {
    $d = $m->query("SELECT c.*, gr.GroupName FROM Sp18Construct c, Sp18Groups gr WHERE c.idGroup=gr.id AND TypeDoor='$TypeDoor'");
    while ($r = $d->fetch_assoc()) {
        $Count = 0;
        switch ($r["TypeCalc"]) {
            case 1:
                $Count = (int)$_POST["H"] * (int)$_POST["W"] * (float)$r["Count"]*0.000001;
                break;
            case 2:
                $Count= ((int)$_POST["H"] + (int)$_POST["W"]) * 2 * (float)$r["Count"]*0.001;
                break;
            case 3:
                $Count=(int)$Petlya*(float)$r["Count"];
                break;
            case 4:
                $Count=(int)$Window*(float)$r["Count"];
                break;
			case 5:
				$Count=1;
				break;
        };
        $Count=round($Count,4);
        $idMaterial=-1;
        $MaterialName="";
        foreach ($arrTemplate as $template)
            if($r["idGroup"]==$template["idGroup"]) {
                $idMaterial = $template["idMaterial"];
                $MaterialName=$template["MaterialName"];
            };
        $arr[] = array(
            "idGroup" => $r["idGroup"],
            "GroupName" => $r["GroupName"],
            "idMaterial1c" => $idMaterial,
            "MaterialName" => $MaterialName,
            "Count" => $Count,
            "Groups1c" => array()
        );
    };
};
//Выведем группы 1с
$d=$m->query("SELECT * FROM Sp18Group_1c");
while($r=$d->fetch_assoc())
    foreach ($arr as &$row)
        if($row["idGroup"]==$r["idGroup"])
            $row["Groups1c"][]=$r["idGroup1c"];

echo json_encode($arr);
?>