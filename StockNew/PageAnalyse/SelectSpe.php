<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $idDoor=$_POST["idDoor"];
    /*$d=$m->query("
    SELECT tMain.*, IF(Stock.CountOld IS NULL, 0, Stock.CountOld) AS StockEntCount FROM
(
	SELECT tMain.idGood, tMain.Count AS SpeCount, tMain.GoodName, IF(Stock.CountOld IS NULL, 0, Stock.CountOld) AS StockMainCount FROM
	(
		SELECT t1.*, nc.CountNC
		FROM
			(SELECT sc.Count, g.id AS idGood, g.GoodName FROM spe_Common sc, spe_Detail sd, st_Goods g WHERE sc.idDoor=$idDoor AND sc.id=sd.idCommon AND sd.idGood=g.id) t1
		LEFT JOIN
			(SELECT idGood, SUM(Count) AS CountNC FROM st_NaryadComplite WHERE idDoor=$idDoor GROUP BY idGood) nc
		ON t1.idGood=nc.idGood
	) tMain
	LEFT JOIN
		(SELECT idGood, CountOld FROM st_StockMain WHERE CountOld>0 GROUP BY idGood) Stock
	ON tMain.idGood=Stock.idGood
) tMain
LEFT JOIN
	(SELECT idGood, CountOld FROM st_StockEnt WHERE CountOld>0 GROUP BY idGood) Stock
ON tMain.idGood=Stock.idGood
    ");*/
    $d=$m->query("
    SELECT tMain.*, IF(Stock.CountOld IS NULL, 0, Stock.CountOld) AS StockEntCount FROM
(
	SELECT tMain.idGood, tMain.Count AS SpeCount, tMain.GoodName, IF(tMain.CountNC IS NULL, 0, tMain.CountNC) AS CountNC, IF(Stock.CountOld IS NULL, 0, Stock.CountOld) AS StockMainCount FROM 
	(
		SELECT t1.*, nc.CountNC
		FROM
			(SELECT sc.idDoor, sc.Count, g.id AS idGood, g.GoodName FROM spe_Common sc, spe_Detail sd, st_Goods g WHERE sc.idDoor IN ($idDoor) AND sc.id=sd.idCommon AND sd.idGood=g.id) t1
		LEFT JOIN
			(SELECT idGood, SUM(Count) AS CountNC FROM st_NaryadComplite WHERE idDoor IN ($idDoor) GROUP BY idGood) nc
		ON t1.idGood=nc.idGood
	) tMain
	LEFT JOIN
		(SELECT idGood, CountOld FROM st_StockMain WHERE CountOld>0 GROUP BY idGood) Stock
	ON tMain.idGood=Stock.idGood
) tMain
LEFT JOIN 
	(SELECT idGood, CountOld FROM st_StockEnt WHERE CountOld>0 GROUP BY idGood) Stock
ON tMain.idGood=Stock.idGood
    
    ");
    $arr=array();
    while($r=$d->fetch_assoc())
        $arr[]=array(
            "idGood"=>$r["idGood"],
            "GoodName"=>$r["GoodName"],
            "SpeCount"=>$r["SpeCount"],
            "StockMainCount"=>$r["StockMainCount"],
            "StockEntCount"=>$r["StockEntCount"],
            "CountNC"=>$r["CountNC"]
        );
    echo json_encode($arr);
?>