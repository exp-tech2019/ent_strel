<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    session_start();
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $i=0; $Arr=array();
    $d=$m->query("SELECT * FROM st_GoodGroups ORDER BY GroupName");
    if($d->num_rows)
        while($r=$d->fetch_assoc()){
            $Arr[$i]=array(
                "idGroup"=>$r["id"],
                "GroupName"=>$r["GroupName"],
                "Step"=>$r["Step"],
                "AutoUnset"=>$r["AutoUnset"],
                "Goods"=>array()
            );
            $i++;
        };
    $d->close();
    //Подгрузим номеклатуру
    $i=0;
    $d=$m->query("SELECT * FROM st_Goods ORDER BY idGroup, GoodName");
    if($d->num_rows>0)
        while($r=$d->fetch_assoc()){
            foreach ($Arr as $key=>$value)
                if((int)$Arr[$key]["idGroup"]==(int)$r["idGroup"]){
                    $Good=array(
                        "idGood"=>$r["id"],
                        "GoodName"=>$r["GoodName"],
                        "Article"=>$r["Article"],
                        "BarCode"=>$r["BarCode"],
                        "Unit"=>$r["Unit"]
                    );
                    $Arr[$key]["Goods"][count($Arr[$key]["Goods"])]=$Good;
                    break;
                }
        };
    echo json_encode($Arr);
?>