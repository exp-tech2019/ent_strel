<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    include "param.php";
    $param=new GlobalParam();
    $XMLParams=simplexml_load_file("../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);
    $SQL="";
    $ShetWhere=$_GET["ShetWhere"];
    $StepWhere=$_GET["StepWhere"];
    switch($ShetWhere){
        case "":
            switch ($StepWhere){
                case "All":
                    $SQL="SELECT *, id AS idOrder FROM Oreders WHERE status=1 OR status=2";
                    break;
                default:
                    $StepArr=array(
                        "Sgibka"=>"SgibkaCompliteFlag",
                        "Svarka"=>"SvarkaCompliteFlag",
                        "Frame"=>"FrameCompliteFlag",
                        "Sborka"=>"SborkaCompliteFlag",
                        "Color"=>"ColorCompliteFlag",
                        "Upak"=>"UpakCompliteFlag",
                        "Shpt"=>"ShptCompliteFlag",
                        "Mdf"=>"MdfCompliteFlag",
                        "MdfSborka"=>"SborkaMdfCompliteFlag"
                    );
                    $SQL="SELECT *, o.id AS idOrder FROM Oreders o, OrderDoors od, Naryad n WHERE o.status=1 AND o.id=od.idOrder AND od.id=n.idDoors AND (n.".$StepArr[$StepWhere]."=0 OR n.".$StepArr[$StepWhere]."=2) GROUP BY o.id";
                    break;
            };
            break;
        default:
            $SQL="SELECT *, id AS idOrder FROM Oreders WHERE ".($XMLParams->Global->ViewNumOrder=="Blank" & is_int($ShetWhere) ? "Blank=$ShetWhere" : "Shet='$ShetWhere'")." OR Zakaz LIKE '%$ShetWhere%'";
            break;
    };
    $d=$m->query($SQL) or die("Ошибка SQL:".$m->error);
    $OrderList=array(); $i=0;
    if($d->num_rows>0)
        while($r=$d->fetch_assoc()) {
            $OrderList[$i] = array(
                "idOrder"=>$r["idOrder"],
                "Num" => $XMLParams->Global->ViewNumOrder=="Blank" ? $r["Blank"] : $r["Shet"],
                "Zakaz" => $r["Zakaz"]
            );
            $i++;
        };
    echo json_encode($OrderList);

    //Старый алгоритм поиска
    /*
     *
    $ShetWhere=$_GET["ShetWhere"]=="" ? "o.status=1 OR o.status=2" : "o.Blank=".$_GET["ShetWhere"]." OR o.Shet='".$_GET["ShetWhere"]."'";


    $StepWhere="";
    switch($_GET["StepWhere"]){
        case "All": $StepWhere="n.SvarkaCompliteFlag=0 OR n.SborkaCompliteFlag=0 OR n.ColorCompliteFlag=0 OR n.UpakCompliteFlag=0 OR n.ShptCompliteFlag=0"; break;
        case "Svarka": $StepWhere="n.SvarkaCompliteFlag=0 "; break;
    }

    $d=$m->query("SELECT tOrder.*, SUM(tNaryad.DoorCount) AS DoorCount, SUM(tNaryad.DoorNaryadCount) AS DoorNaryadCount FROM
	(SELECT o.id, o.Blank, o.Shet, o.Zakaz FROM Oreders o WHERE $ShetWhere) tOrder
INNER JOIN
	(SELECT od.idOrder, od.Count AS DoorCount, COUNT(*) AS DoorNaryadCount FROM OrderDoors od, Naryad n WHERE od.id=n.idDoors AND (n.SvarkaCompliteFlag=0 OR n.SborkaCompliteFlag=0 OR n.ColorCompliteFlag=0 OR n.UpakCompliteFlag=0 OR n.ShptCompliteFlag=0) GROUP BY od.id) tNaryad
ON tOrder.id=tNaryad.idOrder
GROUP BY tOrder.id ORDER BY tOrder.Blank");
     */
?>