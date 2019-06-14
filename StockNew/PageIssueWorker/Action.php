<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    switch ($_POST["Action"]){
        case "LoadSpecification":
            $NaryadNum=$_POST["NaryadNum"];
            $idDoor="";
            $idNaryad="";
            //Определим существует данный наряд в системе
            //-- В зависиомсти от того что передали подготавливаем условия запроса
            $Where="CONCAT(n.Num,n.NumPP)='$NaryadNum'";
            if(strpos($NaryadNum,"/")===false)
                $Where="n.id=$NaryadNum";
            $d=$m->query("SELECT id AS idNaryad, idDoors AS idDoor FROM Naryad n WHERE $Where");
            if($d->num_rows>0){
                $r=$d->fetch_assoc();
                $idDoor=$r["idDoor"];
                $idNaryad=$r["idNaryad"];
                $d->close();
            }
            else
                return false;
            //Создадим справочник товаров
            $d=$m->query("SELECT * FROM st_goods");
            $ManualGoods=array();
            while($r=$d->fetch_assoc())
                $ManualGoods[$r["id"]]=$r["GoodName"];
            $d->close();
            //Определим кол-во товара на складе
            $arrStockMain=array();
            $d=$m->query("SELECT g.id AS idGood, COALESCE(s.CountOld,0) AS CountOld FROM st_Goods g  LEFT JOIN (SELECT st.idGood, SUM(st.CountOld) AS CountOld FROM st_StockMain st WHERE CountOld>0 GROUP BY st.idGood) s ON g.id=s.idGood");
            if($d->num_rows>0)
                while($r=$d->fetch_assoc())
                    $arrStockMain[$r["idGood"]]=$r["CountOld"];
                    /*
                    $arrStockMain[]=array(
                        "idGood"=>$r["idGood"],
                        "CountOld"=>$r["CountOld"]
                    );
                    */
            $d->close();
            //Определим, что выдавалаось ранее по спецификации
            $arrOldIssue=array();
            $d=$m->query("SELECT g.id AS idGood, COALESCE(isw.CountIssue,0) AS CountIssue FROM
	(SELECT gw.idGood, SUM(gw.CountIssue) AS CountIssue FROM st_actissueworker w, st_actissueworkergoods gw WHERE w.id=gw.idAct AND w.idNaryad=$idNaryad GROUP BY idGood) isw
RIGHT JOIN st_Goods g
ON isw.idGood=g.id");
            while($r=$d->fetch_assoc())
                $arrOldIssue[$r["idGood"]]=$r["CountIssue"];

            //Получим спецификацию
            $arr=array();
            $d=$m->query("SELECT Common.*, Detail.idGood FROM
	(SELECT c.*, gr.GroupName FROM spe_Common c, st_GoodGroups gr WHERE c.idDoor=$idDoor AND c.idGroup=gr.id AND gr.Step=0 AND gr.AutoUnset=0) Common
LEFT JOIN spe_Detail Detail
ON Common.id=Detail.idCommon
ORDER BY Common.id");
            while($r=$d->fetch_assoc()) {
                $arr[] = array(
                    "idCommon" => $r["id"],
                    "idGroup" => $r["idGroup"],
                    "GroupName" => $r["GroupName"],
                    "Count" => $r["Count"],
                    "idGood" => $r["idGood"],
                    "CountStock" => $arrStockMain[$r["idGood"]],
                    "CountIssue" => $arrOldIssue[$r["idGood"]],
                    "GoodName" => $r["idGood"] == null ? null : $ManualGoods[$r["idGood"]]
                );
            };
            echo json_encode(array("idDoor"=>$idDoor, "idNaryad"=>$idNaryad,"spe"=>$arr));
            break;
        //Сохранение передачи
        case "SaveIssue":
            $idDoor=$_POST["idDoor"];
            $idNaryad=$_POST["idNaryad"];
            $idWorker=$_POST["idWorker"];
            session_start();
            $idLogin=$_SESSION["AuthorizeID"];

            $arrIssue=$_POST["arrIssue"];

            $m->autocommit(false);
            //Создадим акт
            $m->query("INSERT INTO st_ActIssueWorker (idLogin, idWorker, idNaryad, idDoor, DateCreate) VALUES($idLogin, $idWorker,  $idNaryad, $idDoor, NOW() )") or die($m->error);
            $idAct=$m->insert_id;

            $idActGoods=array();
            $GoodsStr="-1";
            foreach ($arrIssue as $iswOne) {
                $idGood=$iswOne["idGood"];
                $CountIssue=$iswOne["CountIssue"];
                $m->query("INSERT INTO st_ActIssueWorkerGoods (idAct, idGood, CountIssue) VALUES($idAct, $idGood, $CountIssue)") or die($m->error);
                $idActGoods[$idGood]=$m->insert_id;
                $GoodsStr=$GoodsStr.", $idGood";
            };

            //--Начнем перемещение со сновного скалада в производство
            //Сформируем массив нужной номеклатуры основного склада
            $arrGoodsMain=array();
            $d=$m->query("SELECT id, idGood, Price, CountOld FROM st_StockMain WHERE idGood IN ($GoodsStr) AND CountOld>0");
            while($r=$d->fetch_assoc())
                $arrStockMain[]=array(
                    "idMain"=>$r["id"],
                    "idGood"=>$r["idGood"],
                    "Price"=>(float)$r["Price"],
                    "CountOld"=>(float)$r["CountOld"]
                );
            $d->close();
            //Приступим к перемещению
            $arrEntGoods=array();// - формируем массив id для дальнейшего перемещения записей в NaryadComplite
            foreach ($arrIssue as $iswOne){
                $idGood=$iswOne["idGood"];
                $idActGood=$idActGoods[$idGood];
                $CountIssue=(float)$iswOne["CountIssue"];
                foreach($arrStockMain as $MainOne)
                    if($iswOne["idGood"]==$MainOne["idGood"]){
                        $idMain=$MainOne["idMain"];
                        $Price=$MainOne["Price"];
                        $CountOld=(float)$MainOne["CountOld"];
                        if($CountIssue<$CountOld){
                            $m->query("INSERT INTO st_StockEnt (idStock, idGood, Price, Count, CountOld, idWorker, idLogin) VALUES ($idMain, $idGood, $Price, $CountIssue, 0, $idWorker, $idLogin)") or die($m->error);
                            $idEnt=$m->insert_id;
                            $arrEntGoods[]=array(
                                "idDoor"=>$idDoor,
                                "idDetail"=>"",
                                "idGood"=>$idGood,
                                "idEnt"=>$idEnt,
                                "Price"=>$Price,
                                "Count"=>$CountIssue
                            );
                            $m->query("INSERT INTO st_ActIssueWorker_tr1 (idActGoods, idMain, idEnt) VALUES($idActGood, $idMain, $idEnt)") or die($m->error);
                            $m->query("UPDATE st_StockMain SET CountOld=CountOld-$CountIssue WHERE id=$idMain") or die($m->error);
                            $CountIssue=0;
                            break;
                        }
                        else{
                            $m->query("INSERT INTO st_StockEnt (idStock, idGood, Price, Count, CountOld, idWorker, idLogin) VALUES ($idMain, $idGood, $Price, $CountOld, 0, $idWorker, $idLogin)") or die($m->error);
                            $idEnt=$m->insert_id;
                            $arrEntGoods[]=array(
                                "idDoor"=>$idDoor,
                                "idDetail"=>"",
                                "idGood"=>$idGood,
                                "idEnt"=>$idEnt,
                                "Price"=>$Price,
                                "Count"=>$CountIssue
                            );
                            $m->query("INSERT INTO st_ActIssueWorker_tr1 (idActGoods, idMain, idEnt) VALUES($idActGood, $idMain, $idEnt)") or die($m->error);
                            $m->query("UPDATE st_StockMain SET CountOld=CountOld-$CountIssue WHERE id=$idMain") or die($m->error);
                            $CountIssue-=$CountOld;
                        };
                    };
            };

            //--Перемещаем в NaryadComplite
            //Для начала распределим idDetail для массива $arrEntGoods
            $arrDetails=array();
            $d=$m->query("SELECT d.id AS idDetail, d.idGood FROM spe_Detail d, spe_Common c WHERE c.idDoor=$idDoor AND d.idCommon=c.id AND d.idGood IN ($GoodsStr)");
            while($r=$d->fetch_assoc())
                $arrDetails[]=array(
                    "idDetail"=>$r["idDetail"],
                    "idGood"=>$r["idGood"]
                );
            $d->close();
            //Непосредственное добавление в NaryadComplite
            foreach ($arrEntGoods as $EntOne){
                $idDoor=$EntOne["idDoor"];
                $idGood=$EntOne["idGood"];
                $idEnt=$EntOne["idEnt"];
                $Price=$EntOne["Price"];
                $Count=$EntOne["Count"];
                $idDetail="";
                foreach($arrDetails as $DetailOne)
                    if($idGood==$DetailOne["idGood"]) {
                        $idDetail = $DetailOne["idDetail"];
                        break;
                    };
                $m->query("INSERT INTO st_NaryadComplite (idDoor, idDetail, idGood, idEnt, Price, Count) VALUES($idDoor, $idDetail, $idGood, $idEnt, $Price, $Count)") or die($m->error);
            };

            $m->commit();
            echo json_encode(array("Result"=>"ok"));
            break;
        //Выбор сотрудника по карте
        case "CardSelected":
            $CardNum=$_POST["CardNum"];
            $d=$m->query("SELECT id, FIO, fired FROM Workers WHERE SmartCartNum='$CardNum'");
            $Result=array(
                "Result"=>"No"
            );
            if($d->num_rows>0){
                $r=$d->fetch_assoc();
                switch ((int)$r["fired"]){
                    case 0: $Result["Result"]="Ok"; $Result["idWorker"]=$r["id"]; $Result["FIO"]=$r["FIO"]; break;
                    case 1: $Result["Result"]="WorkerFired";
                };
            };
            echo json_encode($Result);
            break;
        //Работа с уже сохраненными данными
        case"SelectList":
            $d=$m->query("SELECT a.id AS idAct, a.idDoor, DATE_FORMAT(a.DateCreate, '%d.%m.%Y') AS DateCreate, CONCAT(n.Num,n.NumPP) AS NaryadNum, l.FIO AS LoginFIO, w.FIO AS WorkerFIO FROM st_ActIssueWorker a, Logins l, Workers w, Naryad n WHERE a.idLogin=l.id AND a.idWorker=w.id AND a.idNaryad=n.id ORDER BY a.DateCreate");
            $arr=array();
            if($d->num_rows>0)
                while($r=$d->fetch_assoc())
                    $arr[]=array(
                        "idAct"=>$r["idAct"],
                        "idDoor"=>$r["idDoor"],
                        "DateCreate"=>$r["DateCreate"],
                        "NaryadNum"=>$r["NaryadNum"],
                        "LoginFIO"=>$r["LoginFIO"],
                        "WorkerFIO"=>$r["WorkerFIO"]
                    );
            echo json_encode($arr);
            break;
        case "OpenAct":
            $idAct=$_POST["idAct"];
            $idDoor=$_POST["idDoor"];
            //Теперь наложим кол-во для списания по специцфакции
            $arrCommon=array();
            $d=$m->query("SELECT d.idGood, c.Count AS SpeIssue FROM spe_Common c, spe_Detail d WHERE c.idDoor=7760 AND c.id=d.idCommon");
            while($r=$d->fetch_assoc())
                $arrCommon[$r["idGood"]]=$r["SpeIssue"];
            $d->close();
            //Для начала выведем списанные позиции
            $d=$m->query("SELECT g.id AS idGood, g.GoodName, ag.CountIssue FROM st_ActIssueWorkerGoods ag, st_Goods g WHERE ag.idAct=$idAct AND ag.idGood=g.id");
            $arr=array();
            while($r=$d->fetch_assoc())
                $arr[]=array(
                    "idGood"=>$r["idGood"],
                    "GoodName"=>$r["GoodName"],
                    "SpeIssue"=>$arrCommon[$r["idGood"]],
                    "CountIssue"=>$r["CountIssue"]
                );
            $d->close();
            echo json_encode($arr);
            break;
    }
?>