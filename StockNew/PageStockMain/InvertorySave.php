<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $idGood=$_POST["idGood"];
    $TypeStock=$_POST["TypeStock"];
    $Count=(float)$_POST["Count"];
    session_start();
    $idManager=$_SESSION["AuthorizeID"];

    //Получим массив массива на складе
    $arrStocks=array();
    $d=$m->query("SELECT * FROM st_$TypeStock WHERE idGood=$idGood AND CountOld>0");
    while($r=$d->fetch_assoc())
        $arrStocks[]=array(
            "idStock"=>$r["id"],
            "Price"=>$r["Price"],
            "CountOld"=>$r["CountOld"]
        );
    $d->close();

    $m->autocommit(false);
    //Формируем списание
    foreach ($arrStocks as $Stock){
        $CounOld=(float)$Stock["CountOld"];
        $idStock=$Stock["idStock"];
        $TypeStockShort=$TypeStock=="StockMain" ? "M" : "E";
        if($Count>$CounOld){
            $m->query("UPDATE st_$TypeStock SET CountOld=0 WHERE id=$idStock") or die($m->error);
            $m->query("INSERT INTO st_Invertory (DateInvertory, idStock, TypeStock, Count, idManager) VALUES(NOW(), $idStock, '$TypeStockShort', $CounOld, $idManager)") or die($m->error);
            $Count-=$CounOld;
        }
        else{
            $m->query("UPDATE st_$TypeStock SET CountOld=CountOld-$Count WHERE id=$idStock") or die($m->error);
            $m->query("INSERT INTO st_Invertory (DateInvertory, idStock, TypeStock, Count, idManager) VALUES(NOW(), $idStock, '$TypeStockShort', $Count, $idManager)") or die($m->error);
            $Count=0;
        };
        if($Count==0) break;
    };
    $m->commit();
    echo json_encode(array("Result"=>"ok"));
?>