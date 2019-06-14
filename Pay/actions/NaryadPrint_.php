<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 02.02.2019
 * Time: 2:17
 */

switch (explode('.',PHP_VERSION)[0])
{
    case "5":
        include("../../mpdf53/mpdf.php");
        $mpdf = new mPDF('utf-8', 'A4', '8', '', 10, 10, 7, 7, 10, 10);
        break;
    case "7":
        require '../vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 8
        ]);
        $mpdf->WriteHTML("p{margin:0px;} table{ border-spacing:0px; border-collapse: separate;} td {border: solid 1px black; padding:4px; padding-right:10px; margin:0px; }",\Mpdf\HTMLParserMode::HEADER_CSS);
        break;
};

session_start();
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

$idWorker=$_GET["idWorker"];
$DateWith=$_GET["DateWith"];
$DateBy=$_GET["DateBy"];
//ФИО и должность сотрудника
$d=$m->query("SELECT w.FIO, d.Dolgnost FROM workers w, manualdolgnost d WHERE w.DolgnostID=d.id AND w.id=$idWorker") or die($m->error);
$r=$d->fetch_assoc();
$FIO=$r["FIO"];
$Dolgnost=$r["Dolgnost"];
$d->close();
//Запросим список дверей

$CostAll=0;
$SumPlusAll=0;
$SumMinusAll=0;


$arrNaryads=array();
/*
 * $d=$m->query("SELECT od.idOrder, n.idDoors, nc.idNaryad, o.Shet, DATE_FORMAT(o.ShetDate,'%d.%m.%Y') AS ShetDate, od.NumPP, od.name, od.H, od.W, IF(od.SEqual=1, 'x равн', IF(od.S IS NOT NULL, CONCAT('x',od.S), '')) AS Stvorka, IF(od.S IS NOT NULL, 2, IF(od.SEqual=1, 2, 1)) AS S, od.H, od.W, CONCAT(n.Num, n.NumPP) AS NaryadNum, n.NumInOrder, COUNT(*) AS NaryadCount, SUM(nc.Cost) AS Cost, nc.DateComplite, DATE_FORMAT(nc.DateComplite,'%d.%m.%Y') AS DateComplite FROM oreders o, orderdoors od, naryad n, naryadcomplite nc
WHERE o.id=od.idOrder AND od.id=n.idDoors AND n.id=nc.idNaryad AND nc.idWorker=$idWorker AND nc.DateComplite BETWEEN STR_TO_DATE('$DateWith','%d.%m.%Y') AND DATE_ADD(STR_TO_DATE('$DateBy','%d.%m.%Y'), INTERVAL 1 DAY)
GROUP BY n.id
ORDER BY o.Shet, od.NumPP, n.NumPP") or die($m->error);
*/
$d=$m->query("CALL Pay_PrintNaryad ($idWorker, '$DateWith','$DateBy')") or die($m->error);
if($d->num_rows>0)
    while ($r=$d->fetch_assoc())
    {
        $CostAll+=$r["NumDoc"]!=null ? (float)$r["Sum"] : 0;
        $SumPlusAll+=$r["NumDoc"]==null & $r["Sum"]>0 ? $r["Sum"] : 0;
        $SumMinusAll+=$r["NumDoc"]==null & $r["Sum"]<0 ? $r["Sum"] : 0;
        $arrNaryads[]=array(
            "NumPP"=>$r["NumPP"],
            "DatePay"=>$r["DatePayStr"],
            "NumDoc"=>$r["NumDoc"],
            "Note"=>$r["Note"],
            "Sum"=>$r["Sum"]
        );
    };
$d->close();
//Выведем
$mpdf->WriteHTML("<h3>$FIO $Dolgnost</h3>");
$mpdf->WriteHTML("<h3>Период: $DateWith - $DateBy</h3>");
$mpdf->WriteHTML("<h4>Заработано: $CostAll Начислено: $SumPlusAll Удержано: $SumMinusAll</h4>");

    $mpdf->WriteHTML("
<table>
    <thead>
        <tr>
            <th></th>
            <th>Дата</th>
            <th>Счет - наряд</th>
            <th>Причина</th>
            <th>Стоимость</th>
        </tr>
    </thead>
    <tbody>
");
    $NumPP=0;
    foreach ($arrNaryads as $n) {
        ++$NumPP;
        $DatePay=$n["DatePay"];
        $NumDoc=$n["NumDoc"];
        $Note=$n["Note"];
        $Sum=$n["Sum"];
        $mpdf->WriteHTML(
            "<tr>
            <td style='border:solid 0.5px gray; padding: 2px'>$NumPP</td>        
            <td style='border:solid 0.5px gray; padding: 2px'>$DatePay</td>
            <td style='border:solid 0.5px gray; padding: 2px'>$NumDoc</td>
            <td style='border:solid 0.5px gray; padding: 2px'>$Note</td>
            <td style='border:solid 0.5px gray; padding: 2px'>$Sum</td>
        </tr>"
        );
    };
    $mpdf->WriteHTML("</tbody></table>");

header("Content-type:application/pdf");
header("Content-Disposition:attachment;filename='downloaded.pdf'");
$mpdf->Output();
?>