<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    include "param.php";
    $param=new GlobalParam();
    $XMLParams=simplexml_load_file("../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);
    $SQL="";
    $idOrder=$_GET["idOrder"];
    $NumWhere=$_GET["NumWhere"];
    $StepWhere=$_GET["StepWhere"];
    switch($NumWhere){
        case "":
            switch ($StepWhere){
                case "All":
                    $SQL="SELECT n.id, od.name, n.NumInOrder, CONCAT(n.Num,n.NumPP) AS NaryadNum,
	n.SgibkaCompliteFlag,
    n.SvarkaCompliteFlag,
    n.FrameCompliteFlag,
    n.SborkaCompliteFlag,
    n.SborkaMdfCompliteFlag,
    n.MdfCompliteFlag,
    n.ColorCompliteFlag,
    n.UpakCompliteFlag,
    n.ShptCompliteFlag
FROM OrderDoors od, Naryad n WHERE od.idOrder=$idOrder AND od.id=n.idDoors
ORDER BY n.NumInOrder";
                    break;
                default:
                    $StepArr=array(
                        "Sgibka"=>"SgibkaCompliteFlag",
                        "Svarka"=>"SvarkaCompliteFlag",
                        "Frame"=>"FrameCompliteFlag",
                        "Sborka"=>"SborkaCompliteFlag",
                        "Color"=>"ColorCompliteFlag",
                        "Upak"=>"UpakCompliteFlag",
                        "Shpt"=>"ShptCompliteFlag",
                        "Mdf"=>"MdfCompliteFlag",
                        "MdfSborka"=>"SborkaMdfCompliteFlag"
                    );
                    $SQL="SELECT n.id, od.name, n.NumInOrder, CONCAT(n.Num,n.NumPP) AS NaryadNum,
	n.SgibkaCompliteFlag,
    n.SvarkaCompliteFlag,
    n.FrameCompliteFlag,
    n.SborkaCompliteFlag,
    n.SborkaMdfCompliteFlag,
    n.MdfCompliteFlag,
    n.ColorCompliteFlag,
    n.UpakCompliteFlag,
    n.ShptCompliteFlag
FROM OrderDoors od, Naryad n WHERE od.idOrder=$idOrder AND od.id=n.idDoors AND (n.".$StepArr[$StepWhere]."=0)
ORDER BY n.NumInOrder";
                    break;
            };
            break;
        default:
            $WhereColumn=$XMLParams->Enterprise->ViewNaryadNum=="NaryadNum" ? "CONCAT(n.Num,n.NumPP) LIKE '%$NumWhere%'" : "NumInOrder=$NumWhere";
            $SQL="SELECT n.id, od.name, n.NumInOrder, CONCAT(n.Num,n.NumPP) AS NaryadNum,
	n.SgibkaCompliteFlag,
    n.SvarkaCompliteFlag,
    n.FrameCompliteFlag,
    n.SborkaCompliteFlag,
    n.MdfCompliteFlag,
    n.SborkaMdfCompliteFlag,
    n.ColorCompliteFlag,
    n.UpakCompliteFlag,
    n.ShptCompliteFlag
FROM OrderDoors od, Naryad n WHERE od.idOrder=$idOrder AND od.id=n.idDoors AND $WhereColumn
ORDER BY n.NumInOrder";
            break;
    };
    $d=$m->query($SQL) or die("Ошибка SQL:".$m->error);
    $InfoDoorCount=0;
    $InfoSgibka=0;
    $InfoSvarka=0;
    $InfoFrame=0;
    $InfoFrameNull=0;
    $InfoSborka=0;
    $InfoMdf=0;
    $InfoMdfNull=0;
    $InfoSborkaMdf=0;
    $InfoSborkaMdfNull=0;
    $InfoColor=0;
    $InfoUpak=0;
    $InfoShpt=0;

    $NaryadList=array(); $i=0;
    if($d->num_rows>0)
        while($r=$d->fetch_assoc()) {
            //echo "---";
            $LastStep=""; $NaryadComplite=0;
            if($r["ShptCompliteFlag"]==0) $LastStep="Отгрузка";
            if($r["UpakCompliteFlag"]==0) $LastStep="Упаковка";
            if($r["ColorCompliteFlag"]==0) $LastStep="Покраска";
            if($r["SborkaMdfCompliteFlag"]!=null & $r["SborkaMdfCompliteFlag"]==0) $LastStep="Сборка МДФ";
            if($r["SborkaCompliteFlag"]==0) $LastStep="Сборка";
            if($r["SvarkaCompliteFlag"]==0) $LastStep="Сварка";
            if($r["SgibkaCompliteFlag"]==0) $LastStep="Гибка";

            if($LastStep=="") $NaryadComplite=1;
            $NaryadList[$i]=array(
                "id"=>$r["id"],
                "Name"=>$r["name"],
                "NaryadNum"=>$XMLParams->Enterprise->ViewNaryadNum=="NaryadNum" ? $r["NaryadNum"] : $r["NumInOrder"],
                "LastStep"=>$LastStep,
                "NaryadComplite"=>$NaryadComplite
            );

            $InfoDoorCount++;
            if($r["SgibkaCompliteFlag"]==1) $InfoSgibka++;
            if($r["SvarkaCompliteFlag"]==1) $InfoSvarka++;
            if($r["FrameCompliteFlag"]==null) $InfoFrameNull++;
            if($r["FrameCompliteFlag"]==1) $InfoFrame++;
            if($r["SborkaCompliteFlag"]==1) $InfoSborka++;
            if($r["MdfCompliteFlag"]==null) $InfoMdfNull++;
            if($r["MdfCompliteFlag"]==1) $InfoMdf++;
            if($r["SborkaMdfCompliteFlag"]==null) $InfoSborkaMdfNull++;
            if($r["SborkaMdfCompliteFlag"]==1) $InfoSborkaMdf++;
            if($r["ColorCompliteFlag"]==1) $InfoColor++;
            if($r["UpakCompliteFlag"]==1) $InfoUpak++;
            if($r["ShptCompliteFlag"]==1) $InfoShpt++;

            $i++;
        };
    echo json_encode(array(
        "Info"=>array(
            array(
                "Step"=>"Кол-во дверей",
                "Count"=>$InfoDoorCount
            ),
            array(
                "Step"=>"Сварка",
                "Count"=>$InfoSvarka
            ),
            array(
                "Step"=>"Рамка",
                "Count"=>$InfoDoorCount==$InfoFrameNull ? "Нет" : $InfoFrame
            ),
            array(
                "Step"=>"Сборка",
                "Count"=>$InfoSborka
            ),
            array(
                "Step"=>"МДФ",
                "Count"=>$InfoDoorCount==$InfoMdfNull ? "Нет" : $InfoMdf
            ),
            array(
                "Step"=>"Сборка МДФ",
                "Count"=>$InfoDoorCount==$InfoSborkaMdfNull ? "Нет" : $InfoSborkaMdf
            ),
            array(
                "Step"=>"Покраска",
                "Count"=>$InfoColor
            ),
            array(
                "Step"=>"Упаковка",
                "Count"=>$InfoUpak
            ),
            array(
                "Step"=>"Погрузка",
                "Count"=>$InfoShpt
            )
        ),
        "NaryadList"=>$NaryadList
    ));
?>