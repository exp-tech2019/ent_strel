<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    //Загрузим список товаров
    $d=$m->query("SELECT * FROM st_Goods");
    $arrGoods=array();
    while($r=$d->fetch_assoc())
        $arrGoods[$r["id"]]=$r["GoodName"];
    $d->close();

    $d=$m->query("SELECT 
	o.id AS idOrder, o.Blank, o.Shet, o.Zakaz, od.id AS idDoor, od.NumPP, od.name, od.H, od.W, IF(od.S IS NOT NULL, CONCAT('x', od.S), IF( od.SEqual=1, 'x Равн.', '')) AS S, od.Count AS DoorCount, sc.Count AS SpeCount, sd.id AS idDetail, sd.idGood 
FROM Oreders o, OrderDoors od, spe_Common sc, spe_Detail sd 
WHERE o.status=1 AND o.id=od.idOrder AND od.id=sc.idDoor AND sc.id=sd.idCommon
ORDER BY o.Blank, od.NumPP");
    $arr=array();
    $idOrder=""; $idDoor=""; $idCommon="";
    while($r=$d->fetch_assoc()){
        if($idOrder!=$r["idOrder"]){
            $arr[]=array(
                "idOrder"=>$r["idOrder"],
                "Doors"
            )
        }
    }
?>