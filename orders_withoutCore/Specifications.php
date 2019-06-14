<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    session_start();
    $XMLParams=simplexml_load_file("../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

    switch ($_POST["Action"]){
        case "Load":
            $idDoor=$_POST["idDoor"];
            $arr=array();
            $d=$m->query("SELECT COUNT(*) AS CountRow FROM spe_Common WHERE idDoor=$idDoor");
            $r=$d->fetch_assoc();
            $d->close();
            if((int) $r["CountRow"]==0)
            {
                $d=$m->query("SELECT name, H, W, WorkPetlya, StvorkaPetlya FROM OrderDoors WHERE id=$idDoor") or die($m->error);
                if($d->num_rows>0){
                    $r=$d->fetch_assoc();
                    $TypeDoor=$r["name"];
                    $H=(float)$r["H"];
                    $W=(float)$r["W"];
                    $PetlyaCount=($r["WorkPetlya"]!=null ? $r["WorkPetlya"] : 0) + ($r["StvorkaPetlya"]!=null ? $r["StvorkaPetlya"] : 0);
                    $d->close();
                    //Подберем подходящий конструктор
                    $d=$m->query("SELECT gr.* FROM spe_ConstructTypeDoors d, spe_ConstructGroups gr WHERE d.TypeDoor='$TypeDoor' AND d.id=gr.idTypeDoor") or die($m->error);
                    $arrSpeGroups=array();
                    //Приступим к расчетам и наполнению массива
                    if($d->num_rows>0){
                        while($r=$d->fetch_assoc()) {
                            $Count=0;
                            switch ((int)$r["TypeCalc"]) {
                                case 1:
                                    $Count=$H*$W*(float)$r["Count"];
                                    break;
                                case 2:
                                    $Count=($H+$W)*2*(float)$r["Count"];
                                    break;
                                case 3:
                                    if($PetlyaCount!=null & $r["Petlya"]==1)
                                        $Count=(int)$PetlyaCount*(float)$r["Count"];
                                    break;
                            };
                            $arrSpeGroups[]=array(
                                "idGroup"=>$r["idGroup"],
                                "Count"=>$Count
                            );
                        };
                    };
                    $d->close();
                    foreach ($arrSpeGroups as $SpeOne) {
                        $m->query("INSERT INTO spe_Common (idDoor, idGroup, Count) VALUES($idDoor, " . $SpeOne["idGroup"] . ", " . $SpeOne["Count"] . ")") or die($m->error);
                    };
                    $m->commit();
                }
                else
                    return false;
            };
            //Создадим справочник товаров
            $d=$m->query("SELECT * FROM st_goods");
            $ManualGoods=array();
            while($r=$d->fetch_assoc())
                $ManualGoods[$r["id"]]=$r["GoodName"];
            $d->close();
            //Подгрузим список Common и Detail
            $CommonNoDel=array();
            $DetailIDStr="-1";
            $d=$m->query("SELECT tCommon.*, d.id AS idDetail, d.idGood FROM
	(SELECT c.id AS idCommon, gr.id AS idGroup, gr.GroupName, c.Count AS CountGood FROM spe_Common c, st_GoodGroups gr WHERE c.idDoor=$idDoor AND c.idGroup=gr.id) tCommon
LEFT JOIN
	spe_detail d
ON tCommon.idCommon=d.idCommon
ORDER BY tCommon.idCommon, d.idGood");
            $i=0;
            while($r=$d->fetch_assoc()) {
                $arr[$i++] = array(
                    "idCommon" => $r["idCommon"],
                    "idGroup" => $r["idGroup"],
                    "GroupName" => $r["GroupName"],
                    "Count" => $r["CountGood"],
                    "idDetail" => $r["idDetail"],
                    "idGood" => $r["idGood"],
                    "GoodName" => $r["idGood"] == null ? null : $ManualGoods[$r["idGood"]],
                    "NoDelete"=>0
                );
                if($r["idDetail"]!=null) {
                    $DetailIDStr = $DetailIDStr . ", " . $r["idDetail"];
                    $CommonNoDel[]=$r["idCommon"];
                };
            };
            $d->close();/*
            //Определим, для какой позиции проводилось удаление
            foreach ($arr as &$one)
                if(in_array($one["idCommon"],$CommonNoDel))
                    $one["NoDelete"]=1;*/



            //Определим, для какой позиции проводилось удаление
            $DetailNoDel=array();
            $d=$m->query("SELECT * FROM st_NaryadComplite WHERE idDetail IN ($DetailIDStr)");
            if($d->num_rows)
                while ($r=$d->fetch_assoc())
                    foreach ($arr as &$one)
                        if($r["idDetail"]==$one["idDetail"])
                            $one["NoDelete"]=1;
            $d->close();

            echo json_encode($arr);
            break;
        case "CountChanged":
            $idCommon=$_POST["idCommon"];
            $Count=$_POST["Count"];
            $m->query("UPDATE spe_Common SET Count=$Count WHERE id=$idCommon") or die($m->error);
            echo json_encode(array("Result"=>"ok"));
            break;

        case "SelectGroups":
            $d=$m->query("SELECT id, GroupName FROM st_GoodGroups ORDER BY GroupName");
            $arr=array(); $i=0;
            while($r=$d->fetch_assoc())
                $arr[$i++]=array(
                    "idGroup"=>$r["id"],
                    "GroupName"=>$r["GroupName"]
                );
            echo json_encode($arr);
            break;
        case "AddGroup":
            $idDoor=$_POST["idDoor"];
            $idGroup=$_POST["idGroup"];
            $m->query("INSERT INTO spe_Common (idDoor, idGroup, Count) VALUES($idDoor, $idGroup, 0)") or die($m->error);
            echo json_encode(array(
                "Result"=>"ok",
                "idCommon"=>$m->insert_id
            ));
            break;
        case "RemoveGroup":
            $idCommon=$_POST["idCommon"];
            $m->query("DELETE FROM spe_Common WHERE id=$idCommon") or die($m->error);
            echo json_encode(array("Result"=>"Ok"));
            break;

        case "SelectGood":
            $idGroup=$_POST["idGroup"];
            $d=$m->query("SELECT t1.*, COALESCE(SUM(e.CountOld),0) AS CountEnt FROM
	(SELECT g.id AS idGood, g.GoodName, COALESCE(SUM(m.CountOld),0) AS CountMain FROM st_Goods g
	LEFT JOIN st_StockMain m
	ON g.id=m.idGood
	WHERE g.idGroup=$idGroup
	GROUP BY g.id) t1
LEFT JOIN st_StockEnt e
ON t1.idGood=e.idGood
GROUP BY t1.idGood");
            $arrGoods=array(); $i=0;
            if($d->num_rows>0)
                while($r=$d->fetch_assoc())
                    $arrGoods[$i++]=array(
                        "idGood"=>$r["idGood"],
                        "GoodName"=>$r["GoodName"],
                        "CountMain"=>$r["CountMain"],
                        "CountEnt"=>$r["CountEnt"]
                    );
            echo json_encode($arrGoods);
            break;
        case"AddGood":
            $idCommon=$_POST["idCommon"];
            $idGood=$_POST["idGood"];
            $m->query("INSERT INTO spe_Detail (idCommon, idGood) VALUES($idCommon, $idGood)") or die($m->error);
            echo json_encode(array("Result"=>"ok"));
            break;
        case"RemoveGood":
            $idDetail=$_POST["idDetail"];
            $m->query("DELETE FROM spe_Detail WHERE id=$idDetail") or die($m->error);
            echo json_encode(array("Result"=>"ok"));
            break;
    }
?>