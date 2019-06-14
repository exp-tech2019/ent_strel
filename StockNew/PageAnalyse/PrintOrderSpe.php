<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/pdf");
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    $idOrder=$_GET["idOrder"];
    $d=$m->query("SELECT id as idDoor, name, H, W, IF(S IS NOT NULL, CONCAT('x', S), IF( SEqual=1, 'x Равн.', '')) AS S, Count FROM OrderDoors WHERE idOrder=$idOrder");
    $arrDoors=array();
    $idDoorStr="-1";
    while($r=$d->fetch_assoc()) {
        $arrDoors[] = array(
            "idDoor" => $r["idDoor"],
            "Size" => $r["H"] . " x " . $r["W"] . $r["S"],
            "Name" => $r["name"],
            "DoorCount" => $r["Count"],
            "Spe"=>array()
        );
        $idDoorStr.=", ".$r["idDoor"];
    };
    $d->close();

    $d=$m->query("
    SELECT tMain.*, IF(Stock.CountOld IS NULL, 0, Stock.CountOld) AS StockEntCount FROM
(
	SELECT tMain.idDoor, tMain.idGood, tMain.Count AS SpeCount, tMain.GoodName, IF(tMain.CountNC IS NULL, 0, tMain.CountNC) AS CountNC, IF(Stock.CountOld IS NULL, 0, Stock.CountOld) AS StockMainCount FROM 
	(
		SELECT t1.*, nc.CountNC
		FROM
			(SELECT sc.idDoor, sc.Count, g.id AS idGood, g.GoodName FROM spe_Common sc, spe_Detail sd, st_Goods g WHERE sc.idDoor IN ($idDoorStr) AND sc.id=sd.idCommon AND sd.idGood=g.id) t1
		LEFT JOIN
			(SELECT idGood, SUM(Count) AS CountNC FROM st_NaryadComplite WHERE idDoor IN ($idDoorStr) GROUP BY idGood) nc
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
    $arrSpe=array();
    if($d->num_rows>0)
        while($r=$d->fetch_assoc())
            foreach ($arrDoors as &$Door)
                if($Door["idDoor"]==$r["idDoor"]) {
                    $Door["Spe"][] = array(
                        "GoodName" => $r["GoodName"],
                        "SpeCount" => $r["SpeCount"],
                        "StockCount" => (float)$r["StockMainCount"] + (float)$r["StockEntCount"],
                        "CountNC" => $r["CountNC"]
                    );
                    break;
                };

    $html="";
    foreach ($arrDoors as $Door){
        $html.="<h3>Дверь: ".$Door["Name"]." ".$Door["Size"]." Кол-во".$Door["Count"];
        $html.="<table>";
            $html.="<thead>";
                $html.="<tr>";
                    $html.="<th>Материал</th>";
                    $html.="<th>Требуется</th>";
                    $html.="<th>Склад</th>";
                    $html.="<th>Списано</th>";
                $html.="</tr>";
            $html.="</thead>";
            $html.="<tbody>";
                foreach ($Door["Spe"] as $Spe){
                    $html.="<tr>";
                        $html.="<td>".$Spe["GoodName"]."</td>";
                        $html.="<td>".$Spe["SpeCount"]."</td>";
                        $html.="<td>".$Spe["StockCount"]."</td>";
                        $html.="<td>".$Spe["CountNC"]."</td>";
                    $html.="</tr>";
                }
            $html.="</tbody>";
        $html.="</table>";
    }
    include "../../MPDF53/mpdf.php";
    $mpdf = new mPDF();
    $mpdf->Bookmark('Складской отчет');
    $mpdf->WriteHTML($html);
    $mpdf->Output();
?>