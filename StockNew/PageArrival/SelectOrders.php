<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $d=$m->query("SELECT 
	o.id AS idOrder, o.Blank, o.Shet, o.Zakaz, od.id AS idDoor, od.NumPP, od.name, CONCAT(od.H,' x ', od.W, IF(od.S IS NOT NULL, CONCAT('x', od.S), IF( od.SEqual=1, 'x Равн.', ''))) AS Size, od.Count AS DoorCount
FROM Oreders o, OrderDoors od WHERE o.status=1 AND o.id=o.idOrder ORDER BY o.Blank");
    $arr=array();
    $idOrder="";
    while($r=$d->fetch_assoc()){
        if($idOrder!=$r["idOrder"]){
            $arr[]=array(
                "idOrder"=>$r["idOrder"],
                "Blank"=>$r["Blank"],
                "Shet"=>$r["Shet"],
                "Zakaz"=>$r["Zakaz"],
                "Doors"=>array()
            );

            $idOrder=$r["idOrder"];
        };
        $arr[count($arr)-1]["Doors"][]=array(
            "idDoor"=>$r["idDoor"],
            "NumPP"=>$r["NumPP"],
            "Name"=>$r["name"],
            "Size"=>$r["Size"],
            "DoorCount"=>$r["DoorCount"]
        );
    };
    echo json_encode($arr);
?>