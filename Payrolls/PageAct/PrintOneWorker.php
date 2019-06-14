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
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML("p{margin:0px;} table{ border-spacing:0px; border-collapse: separate;} td {border: solid 1px black; padding:4px; padding-right:10px; margin:0px; }",\Mpdf\HTMLParserMode::HEADER_CSS);
        break;
};

session_start();
include "../params.php";
$m=new mysqli($GlobalDBHost,$GlobalDBLogin,$GlobalDBPass,$GlobalDBName);

if(isset($_GET["idAct"]) & isset($_GET["idWorker"]))
{
    $idAct=$_GET["idAct"];
    $idWorker=$_GET["idWorker"];
    $d=$m->query("SELECT DATE_FORMAT(DateCreate, '%d.%m.%Y') AS DateCreate FROM temppayrolls WHERE id=$idAct");
    $r=$d->fetch_assoc();
    $DateCreate=$r["DateCreate"];
    $d->close();
    $mpdf->WriteHTML("<h3>Акт № $idAct от $DateCreate</h3>");
    //Выплаты/начисления сотрулнику
    $d=$m->query("SELECT SUM(COALESCE(SumPlus,0)) AS SumPlus, SUM(COALESCE(SumMinus,0)) AS SumMinus FROM TempPayrollsPayments WHERE idAct=$idAct AND idWorker=$idWorker");
    $r=$d->fetch_assoc();
    $PaymentPlus=(float)$r["SumPlus"];
    $PaymentMinus=(float)$r["SumMinus"];
    $d->close();
    //Запросим данные о сотруднике
    $d=$m->query("SELECT p.*, w.FIO, d.Dolgnost FROM temppayrollslist p, workers w, manualdolgnost d WHERE p.idAct=$idAct AND p.idWOrker=$idWorker AND p.idWorker=w.id AND w.DolgnostID=d.id");
    $r=$d->fetch_assoc();
    $FIO=$r["FIO"];
    $Dolgnost=$r["Dolgnost"];
    $mpdf->WriteHTML("<h3>$FIO $Dolgnost</h3>");

    $SumPlusAll=(float)$r["SumWith"]+(float)$r["Cost"]+(float)$r["SumPlus"]+$PaymentPlus;
    $SumPlusAllNalog=(float)$r["Cost"]+(float)$r["SumPlus"]+$PaymentPlus;
    $SumPlusAllNalog=(float)$r["SumWith"]+($SumPlusAllNalog-$SumPlusAllNalog*(int)$r["NalogPercent"]/100);

    $SumMinus=-1*(float)$r["SumMinus"]+$PaymentMinus;

    $BalanceNalog=$SumPlusAllNalog-$SumMinus;

    $mpdf->WriteHTML("<p style='margin:0px;'>На начало периода - ".$r["SumWith"]."</p>");
    $mpdf->WriteHTML("<p style='margin:0px;'>Заработано - ".$r["Cost"]."</p>");
    $mpdf->WriteHTML("<p style='margin:0px;'>Начисленно - ".((float)$r["SumPlus"]+$PaymentPlus)."</p>");
    $mpdf->WriteHTML("<p style='margin:0px;'>К выплате - ".$SumPlusAll."</p>");
    $mpdf->WriteHTML("<p style='margin:0px;'>Налог - ".$r["NalogPercent"]."</p>");
    $mpdf->WriteHTML("<p style='margin:0px;'>К выплате (с налогом) - ".$SumPlusAllNalog."</p>");
    $mpdf->WriteHTML("<p style='margin:0px;'>Выплачено - ".$SumMinus."</p>");
    $mpdf->WriteHTML("<p style='margin:0px;'>Остаток (с налогом) - ".$BalanceNalog."</p>");
    $d->close();

    //Выведем таблицу выполненных нарядов
	$styleTD=" style='border: solid 1px black; padding:4px; padding-right:10px;'";
    $mpdf->WriteHTML("<table style='border-spacing:0px; border-collapse: separate;'>");
    $mpdf->WriteHTML("<thead>");
    $mpdf->WriteHTML("<tr><td $styleTD>Наряд</td><td $styleTD>Дата</td><td $styleTD>Сумма</td><td></td></tr>");
    $mpdf->WriteHTML("</thead>");
    $mpdf->WriteHTML("<tbody>");

    //Определим дату предыдущего дня создания акта
    $idActOld=$idAct-1;
    $DateCreatePrev="";
    while($idActOld>1 & $DateCreatePrev=="")
    {
        $d=$m->query("SELECT DATE_FORMAT(DateCreate, '%d.%m.%Y') AS DateCreate FROM temppayrolls WHERE id=$idActOld");
        $r=$d->fetch_assoc();
        $DateCreatePrev=$r["DateCreate"];
        $d->close();
        $idActOld--;
    };
    //Выведем список выполненных нарядов
    $CostAll=0;
    $d=$m->query("SELECT od.name, CONCAT(n.Num,n.NumPP) AS NaryadNum, n.NumInOrder, DATE_FORMAT(nc.DateComplite, '%d.%m.%Y') AS DateComplite, nc.Cost FROM orderdoors od, naryad n, naryadcomplite nc 
WHERE od.id=n.idDoors AND n.id=nc.idNaryad AND nc.idWorker=$idWorker AND nc.DateComplite BETWEEN STR_TO_DATE('$DateCreatePrev','%d.%m.%Y') AND STR_TO_DATE('$DateCreate','%d.%m.%Y') ORDER BY n.idDoors") or die($m->error);
    if($d->num_rows>0)
        while ($r=$d->fetch_assoc())
        {
            $mpdf->WriteHTML("<tr>");
            $mpdf->WriteHTML("<td $styleTD>Наряд - <b>".$r["NaryadNum"]."</b> Дверь - <b>".$r["NumInOrder"]."</b> (".$r["name"].")</td><td $styleTD>".$r["DateComplite"]."</td><td $styleTD>".$r["Cost"]."</td><td style='width:100px;'></td>");
            $mpdf->WriteHTML("</tr>");
            $CostAll+=(float)$r["Cost"];
        };

    $mpdf->WriteHTML("</tbody>");
    $mpdf->WriteHTML("</table>");
};
header("Content-type:application/pdf");
header("Content-Disposition:attachment;filename='downloaded.pdf'");
$mpdf->Output();