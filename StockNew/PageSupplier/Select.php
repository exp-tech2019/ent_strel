<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $d=$m->query("SELECT * FROM st_Suppliers ORDER BY SupplierName, INN");
    $Arr=array(); $i=0;
    if($d->num_rows>0)
        while($r=$d->fetch_assoc()){
            $Arr[$i]=array(
                "id"=>$r["id"],
                "SupplierName"=>$r["SupplierName"],
                "INN"=>$r["INN"],
                "Adress"=>$r["Adress"],
                "Phone"=>$r["Phone"]
            );
            $i++;
        };
    echo json_encode($Arr);
?>