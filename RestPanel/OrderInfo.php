<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $idOrder=$_GET["idOrder"];
    $d=$m->query("SELECT COUNT(*) AS DoorCount, 
	SUM(IF(n.SgibkaCompliteFlag=1, 1, 0)) AS SgibkaCompliteCount,
    SUM(IF(n.SvarkaCompliteFlag=1, 1, 0)) AS SvarkaCompliteCount,
    SUM(IF(n.FrameCompliteFlag=1, 1, 0)) AS FrameCompliteCount,
    SUM(IF(n.FrameCompliteFlag IS NULL, 1, 0)) AS FrameNULL,
    SUM(IF(n.SborkaCompliteFlag=1, 1, 0)) AS SborkaCompliteCount,
    SUM(IF(n.ColorCompliteFlag=1, 1, 0)) AS ColorCompliteCount,
    SUM(IF(n.UpakCompliteFlag=1, 1, 0)) AS UpakCompliteCount,
    SUM(IF(n.ShptCompliteFlag=1, 1, 0)) AS ShptCompliteCount,
    SUM(IF(n.MdfCompliteFlag=1, 1, 0)) AS MdfCompliteCount,
    SUM(IF(n.MdfCompliteFlag IS NULL, 1, 0)) AS MdfNULL,
    SUM(IF(n.SborkaMdfCompliteFlag=1, 1, 0)) AS SborkaMdfCompliteCount,
    SUM(IF(n.SborkaMdfCompliteFlag IS NULL, 1, 0)) AS SborkaMdfNULL
FROM OrderDoors od, Naryad n WHERE od.id=n.idDoors AND od.idOrder=$idOrder GROUP BY od.idOrder");


    $OrderList=array(); $i=0;
    if($d->num_rows>0)
    {
        $r=$d->fetch_assoc();
        echo json_encode(
            array(
                array(
                    "Step"=>"Всего дверей",
                    "Count"=>$r["DoorCount"]
                ),
                array(
                    "Step"=>"Сварка",
                    "Count"=>$r["SvarkaCompliteCount"]
                ),
                array(
                    "Step"=>"Рамка",
                    "Count"=>(int)$r["DoorCount"]==(int)$r["FrameNULL"] ? "Нет" : $r["FrameCompliteCount"]
                ),
                array(
                    "Step"=>"Сборка",
                    "Count"=>$r["SborkaCompliteCount"]
                ),
                array(
                    "Step"=>"Покарска",
                    "Count"=>$r["ColorCompliteCount"]
                ),
                array(
                    "Step"=>"Упаковка",
                    "Count"=>$r["ColorCompliteCount"]
                ),
                array(
                    "Step"=>"МДФ Цех",
                    "Count"=>(int)$r["DoorCount"]==(int)$r["MdfNULL"] ? "Нет" : $r["MdfCompliteCount"]
                ),
                array(
                    "Step"=>"МДФ Сборка",
                    "Count"=>(int)$r["DoorCount"]==(int)$r["SborkaMdfNULL"] ? "Нет" : $r["SborkaMdfCompliteCount"]
                )
            )
        );
    };
?>