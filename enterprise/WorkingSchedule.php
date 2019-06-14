<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");

    $XMLParams=simplexml_load_file("../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

    $YearWhere=$_GET["YearWhere"];
    $MonthWhere=$_GET["MonthWhere"];

    $DayFirst=new DateTime();
    $DayFirst=$DayFirst->setDate($YearWhere, $MonthWhere, 1);


    $d=$m->query("SELECT w.id AS idWorker, d.id AS idDolgnost, d.Dolgnost, w.FIO FROM ManualDolgnost d, Workers w WHERE d.Algorithm='H' AND w.DolgnostID=d.id ORDER BY d.Dolgnost, w.FIO");
    $WorkerList=array(); $i=0;
    while($r=$d->fetch_assoc()){
        $WorkerList[$i]=array("idWorker"=>$r["idWorker"], "idDolgnost"=>$r["idDolgnost"], "Dolgnost"=>$r["Dolgnost"], "FIO"=>$r["FIO"], "CountHour"=>0, "Cost"=>0);
        $i++;
    };
    //Формируем заготовку основного массива
    $arrMain=array(); $c=0;
    for($i=1;$i<=(int)$DayFirst->format("t"); $i++){
        $arrMain[$i]=$WorkerList;
    };


    $d=$m->query("SELECT d.Dolgnost, w.FIO, sh.*, DATE_FORMAT(sh.DatePayment,'%d') AS DayPayment FROM ManualDolgnost d, Workers w, WorkingSchedule sh WHERE d.id=w.DolgnostID AND w.id=sh.idWorker AND YEAR(sh.DatePayment)=$YearWhere AND MONTH(sh.DatePayment)=$MonthWhere") or die($m->error);
    if($d->num_rows>0)
        while($r=$d->fetch_assoc())
            for($i=0;$i<count($arrMain[(int)$r["DayPayment"]]); $i++)
                if($arrMain[(int)$r["DayPayment"]][$i]["idWorker"]==$r["idWorker"]){
                    $arrMain[(int)$r["DayPayment"]][$i]["CountHour"]=(int)$r["CountHour"];
                    $arrMain[(int)$r["DayPayment"]][$i]["Cost"]=(float)$r["Cost"];
                };

    echo json_encode(array("LastDay"=>(int)$DayFirst->format("t"), "WorkerList"=>$WorkerList, "Schedule"=>$arrMain));
?>