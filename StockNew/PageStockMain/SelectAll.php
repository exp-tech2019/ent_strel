<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $Groups=array(); $i=0;
    $d=$m->query("SELECT id, GroupName FROM st_GoodGroups ORDER BY GroupName");
    while ($r=$d->fetch_assoc()){
        $Groups[$i]=array(
            "idGroup"=>$r["id"],
            "GroupName"=>$r["GroupName"],
            "Goods"=>array()
        );
        $i++;
    };
    $d->close();

    //Сформируем таблицу Товаров
    $Goods=array(); $i=0;
    $d=$m->query("SELECT * FROM st_Goods ORDER BY GoodName");
    while($r=$d->fetch_assoc()){
        $Goods[$i]=array(
            "idGood"=>$r["id"],
            "idGroup"=>$r["idGroup"],
            "Article"=>$r["Article"],
            "GoodName"=>$r["GoodName"],
            "BarCode"=>$r["BarCode"],
            "Unit"=>$r["Unit"],
            "CountMain"=>0,
            "CountEnt"=>0
        );
        $i++;
    };
    $d->close();

    //олучим список товаров а основном складе
    $d=$m->query("SELECT idGood, COALESCE(SUM(CountOld),0) AS CountOld FROM st_StockMain WHERE CountOld>0 GROUP BY idGood");
    while($r=$d->fetch_assoc())
        for($i=0; $i<count($Goods); $i++)
            if($Goods[$i]["idGood"]==$r["idGood"]){
                $Goods[$i]["CountMain"]=$r["CountOld"];
                break;
            };
    $d->close();
    //Получим список остатков на производстве
    $d=$m->query("SELECT idGood, COALESCE(SUM(CountOld),0) AS CountOld FROM st_StockEnt WHERE CountOld>0 GROUP BY idGood");
    while($r=$d->fetch_assoc())
        for($i=0; $i<count($Goods); $i++)
            if($Goods[$i]["idGood"]==$r["idGood"]){
                $Goods[$i]["CountEnt"]=$r["CountOld"];
                break;
            };
    $d->close();
    //Разложим товары по группам
    for($i=0; $i<count($Groups); $i++){
        $g=0;
        foreach ($Goods as $Good)
            if($Groups[$i]["idGroup"]==$Good["idGroup"]){
                $Groups[$i]["Goods"][$g]=$Good;
                $g++;
            }
    };

    echo json_encode($Groups);
?>