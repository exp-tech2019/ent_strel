<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $d=$m->query("SELECT a.id, DATE_FORMAT(a.DateCreate,'%d.%m.%Y') AS DateCreate, l.FIO AS LoginFIO, w.FIO AS WorkerFIO, SUM(g.Count) AS CountGoods FROM st_ActInEnt a, st_ActInEntGoods g, Logins l, Workers w WHERE a.id=g.idAct AND a.idLogin=l.id AND a.idWorker=w.id GROUP BY a.id ORDER BY a.DateCreate DESC");
    $arr=array(); $i=0;
    if($d->num_rows>0)
        while ($r=$d->fetch_assoc())
            $arr[$i++]=array(
                "id"=>$r["id"],
                "DateCreate"=>$r["DateCreate"],
                "LoginFIO"=>$r["LoginFIO"],
                "WorkerFIO"=>$r["WorkerFIO"],
                "CountGoods"=>$r["CountGoods"]
            );
    echo json_encode($arr);
?>