<?php
    header('Content-Type: application/json');
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

    $d=$m->query("SELECT od.idOrder, od.id AS idDoor, o.Blank, DATE_FORMAT(o.BlankDate,'%d.%m.%Y') AS BlankDateStr, o.Shet, o.Zakaz,od.NumPP, od.name, od.H, od.W, CONCAT(od.H,' x ',od.W,IF(od.S IS NOT NULL, CONCAT(' x ',od.S), IF(od.SEqual=1,' x Равн.',''))) AS Size, od.Open, od.Count FROM Oreders o, OrderDoors od WHERE o.status=0 AND o.id=od.idOrder ORDER BY o.Blank");
    $arr=array(); $idOrder=""; $DoorCount=0;
    while($r=$d->fetch_assoc()){
        if($idOrder!=$r["idOrder"])
            $arr[] = array(
                "idOrder" => $idOrder=$r["idOrder"],
                "Blank" => $r["Blank"],
                "BlankDate" => $r["BlankDateStr"],
                "Shet" => $r["Shet"],
                "Zakaz"=>$r["Zakaz"],
                "DoorCount" => 0,
                "OrderDoors" => array()
            );
        $arr[count($arr)-1]["OrderDoors"][]=array(
            "idDoor"=>$r["idDoor"],
            "NumPP"=>$r["NumPP"],
            "Name"=>$r["name"],
            "Count"=>$r["Count"],
            "H"=>$r["H"],
            "W"=>$r["W"],
            "Size"=>$r["Size"],
            "Open"=>$r["Open"]
        );
        $arr[count($arr)-1]["DoorCount"]+=(int)$r["Count"];
    };
    echo json_encode($arr);
    $m->close();
?>