<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $SelectOld=(boolean)$_POST["SelectOld"];

    $d=$m->query("SELECT a.id AS idAct, o.Blank, o.Shet, DATE_FORMAT(a.DateCreate,'%d.%m.%Y') AS DateCreate, l.FIO AS ManagerFIO, DATE_FORMAT(a.DateShpt,'%d.%m.%Y') AS DateShpt FROM st_ActShpt a, Oreders o, Logins l WHERE a.idOrder=o.id AND a.idManager=l.id AND a.DateShpt IS NULL ORDER BY a.DateCreate DESC");
    $arr=array();
    if($d->num_rows>0)
        while($r=$d->fetch_assoc())
            $arr[]=array(
                "idAct"=>$r["idAct"],
                "Blank"=>$r["Blank"],
                "Shet"=>$r["Shet"],
                "DateCreate"=>$r["DateCreate"],
                "ManagerFIO"=>$r["ManagerFIO"],
                "DateShpt"=>$r["DateShpt"]
            );
    echo json_encode($arr);
?>