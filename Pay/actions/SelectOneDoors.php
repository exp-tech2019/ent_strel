<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 14.03.2019
 * Time: 16:22
 */
session_start();
ini_set("max_execution_time", "2000");
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
header('Content-Type: application/json');

$idWorker=$_POST["idWorker"];
$DateWith=$_POST["DateWith"];
$DateBy=$_POST["DateBy"];

$d=$m->query("SELECT o.Shet, DATE_FORMAT(o.ShetDate,'%d.%m.%Y') AS ShetDate, od.idOrder, od.NumPP, od.name, IF(od.S IS NOT NULL, 2, IF(od.SEqual=1, 2, 1)) AS S, od.H, od.W, COUNT(*) AS DoorCount, SUM(nc.Cost) AS Cost FROM oreders o, orderdoors od, naryad n, naryadcomplite nc 
WHERE o.id=od.idOrder AND od.id=n.idDoors AND n.id=nc.idNaryad AND nc.idWorker=$idWorker AND nc.DateComplite BETWEEN STR_TO_DATE('$DateWith','%d.%m.%Y') AND DATE_ADD(STR_TO_DATE('$DateBy','%d.%m.%Y'), INTERVAL 1 DAY)
GROUP BY od.id
ORDER BY o.id") or die($m->error);
$arr=array();
$idOrder=-1;
if($d->num_rows>0)
    while ($r=$d->fetch_assoc())
    {
        switch($r["idOrder"]!=$idOrder)
        {
            case true:
                $arr[]=array(
                    "idOrder"=>$r["idOrder"],
                    "Shet"=>$r["Shet"],
                    "ShetDate"=>$r["ShetDate"],
                    "DoorCount"=>(int)$r["DoorCount"],
                    "Doors"=>array(
                        "NumPP"=>$r["NumPP"],
                        "Name"=>$r["name"],
                        "H"=>$r["H"],
                        "W"=>$r["W"],
                        "S"=>$r["S"],
                        "DoorCount"=>$r["DoorCount"],
                        "Cost"=>$r["DoorCount"]
                    )
                );
                $idOrder=$r["idOrder"];
                break;
            case false:
                $o=&$arr[count($arr)-1];
                $o["DoorCount"]+=(int)$r["DoorCount"];
                $o["Doors"][]=array(
                    "NumPP"=>$r["NumPP"],
                    "Name"=>$r["name"],
                    "H"=>$r["H"],
                    "W"=>$r["W"],
                    "S"=>$r["S"],
                    "DoorCount"=>$r["DoorCount"],
                    "Cost"=>$r["DoorCount"]
                );
                break;
        };
    };
echo json_encode($arr);
?>