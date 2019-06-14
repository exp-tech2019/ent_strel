<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $d=$m->query("SELECT s.SupplierName, a.*, DATE_FORMAT(a.DateArrival,'%d.%m.%Y') AS DateArrivalS, SUM(ag.Count) AS SumCount, SUM(ag.Price*ag.Count) AS SumPrice  FROM st_Arrival a, st_ArrivalGoods ag, st_Suppliers s WHERE a.id=ag.idArrival AND a.idSupplier=s.id GROUP BY a.id ORDER BY a.DateArrival DESC, a.NumArrival DESC");
    $arr=array(); $i=0;
    if($d->num_rows>0)
        while($r=$d->fetch_assoc()){
            $arr[$i]=array(
                "id"=>$r["id"],
                "idSupplier"=>$r["idSupplier"],
                "TextSupplier"=>$r["SupplierName"],
                "NumArrival"=>$r["NumArrival"],
                "DateArrival"=>$r["DateArrivalS"],
                "SumCount"=>$r["SumCount"],
                "SumPrice"=>$r["SumPrice"]
            );
            $i++;
        };
    echo json_encode($arr);
?>