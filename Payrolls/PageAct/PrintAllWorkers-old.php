<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 01.02.2019
 * Time: 10:21
 */
require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

session_start();
include "../params.php";
$m=new mysqli($GlobalDBHost,$GlobalDBLogin,$GlobalDBPass,$GlobalDBName);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello World !');


if(isset($_GET["idAct"])){
    $idAct=$_GET["idAct"];
    $Sort=$_GET["Sort"];
    $FilterStr="";
    $FilterStr.=isset($_GET["FilterDolgnost"]) ? " AND m.Dolgnost LIKE '%".$_GET["FilterDolgnost"]."%'" : "";
    $FilterStr.=isset($_GET["FilterFIO"]) ? " AND w.FIO LIKE '%".$_GET["FilterFIO"]."%'" : "";
    $d=$m->query("SELECT *, DATE_FORMAT(DateCreate, '%d.%m.%Y') AS DateCreateS FROM TempPayrolls WHERE id=$idAct");
    $r=$d->fetch_assoc();

    $DateCreate=$r["DateCreateS"];
    $d->close();
    $sheet->mergeCells("A1:J1");
    $sheet->setCellValue("A1","Акт: $idAct от $DateCreate");
    $sheet->getStyle("A1")->getFont()->setBold(true);
    $sheet->getStyle("A1")
        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->mergeCells("A2:J2");

    $RowPos=3;
    AddRow($sheet, $RowPos++, "ФИО","Должность", "На начало периода", "Заработано", "Начисленно", "К выплате", "Налог", "К выплате (с налогом)", "Выплачено", "Остаток (с налогом)");

    $arr=array();
    $d=$m->query("SELECT * FROM Workers");
    while($r=$d->fetch_assoc())
        $arr[$r["id"]]=array(
            "SumPlus"=>0,
            "SumMinus"=>0
        );
    $d=$m->query("SELECT idWorker, SUM(COALESCE(SumPlus,0)) AS SumPlus, SUM(COALESCE(SumMinus,0)) AS SumMinus FROM TempPayrollsPayments WHERE idAct=$idAct GROUP BY idWorker");
    if($d->num_rows>0)
        while($r=$d->fetch_assoc())
            $arr[$r["idWorker"]]=array(
                "SumPlus"=>$r["SumPlus"],
                "SumMinus"=>$r["SumMinus"]
            );
    $itog_SumWith=0;
    $itog_Cost=0;
    $itog_Payment=0;
    $itog_SumPlusAll=0;
    $itog_SumPlusAllNalog=0;
    $itog_SumMinus=0;
    $itog_BalanceNalog=0;
    $d=$m->query("SELECT p.idWorker, w.FIO, m.Dolgnost, p.SumWith, p.Cost, p.SumPlus , p.NalogPercent, -1*p.SumMinus AS SumMinus FROM TempPayrollsList p, Workers w, ManualDolgnost m WHERE p.idAct=$idAct AND p.idWorker=w.id AND w.DolgnostID=m.id $FilterStr ORDER BY ".($Sort=="Dolgnost" ? "m.Dolgnost" : "w.FIO"));
    while($r=$d->fetch_assoc()){
        $SumPlusAll=(float)$r["SumWith"]+(float)$r["Cost"]+(float)$r["SumPlus"]+(float)$arr[$r["idWorker"]]["SumPlus"];
        $SumPlusAllNalog=(float)$r["Cost"]+(float)$r["SumPlus"]+(float)$arr[$r["idWorker"]]["SumPlus"];
        $SumPlusAllNalog=(float)$r["SumWith"]+($SumPlusAllNalog-$SumPlusAllNalog*(int)$r["NalogPercent"]/100);

        $SumMinus=(float)$r["SumMinus"]+(float)$arr[$r["idWorker"]]["SumMinus"];

        $BalanceNalog=$SumPlusAllNalog-$SumMinus;

        AddRow(
            $sheet,
            $RowPos++,
            $r["FIO"],
            $r["Dolgnost"],
            $r["SumWith"],
            $r["Cost"],
            (float)$r["SumPlus"]+(float)$arr[$r["idWorker"]]["SumPlus"],
            $SumPlusAll,
            $r["NalogPercent"],
            $SumPlusAllNalog,
            $SumMinus,
            $BalanceNalog
            );
        $itog_SumWith+=(float)$r["SumWith"];
        $itog_Cost+=(float)$r["Cost"];
        $itog_Payment+=(float)$r["SumPlus"]+(float)$arr[$r["idWorker"]]["SumPlus"];
        $itog_SumPlusAll+=$SumPlusAll;
        $itog_SumPlusAllNalog+=$SumPlusAllNalog;
        $itog_SumMinus+=$SumMinus;
        $itog_BalanceNalog+=$BalanceNalog;
    };
    AddRow($sheet, $RowPos++, "ИТОГ", "", $itog_SumWith, $itog_Cost, $itog_Payment, $itog_SumPlusAll, "", $itog_SumPlusAllNalog, $itog_SumMinus, $itog_BalanceNalog);
};
$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
header('Content-Disposition:attachment;filename="Акт.xlsx"');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
//$writer->save('hello world.xlsx');

function AddRow($sheet, $RowPos, $FIO, $Dolgnost, $SumWith, $Cost, $Payment, $InPayment, $Nalog, $PaymentsWithNalog, $PaymentAll, $SumEnd){
    $sheet->setCellValue('A'.$RowPos, $FIO);
    $sheet->setCellValue('B'.$RowPos, $Dolgnost);
    $sheet->setCellValue('C'.$RowPos, round($SumWith));
    $sheet->setCellValue('D'.$RowPos, round($Cost));
    $sheet->setCellValue('E'.$RowPos, round($Payment));
    $sheet->setCellValue('F'.$RowPos, round($InPayment));
    $sheet->setCellValue('G'.$RowPos, $Nalog);
    $sheet->setCellValue('H'.$RowPos, round($PaymentsWithNalog));
    $sheet->setCellValue('I'.$RowPos, round($PaymentAll));
    $sheet->setCellValue('J'.$RowPos, round($SumEnd));
    $arr=array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J");
    foreach ($arr as $a)
    {
        $sheet->getStyle($a.$RowPos)
            ->getAlignment()->setHorizontal(
                $a=="A" || $a=="B" ?
                    \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
                    :
                    \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
            );
        $sheet->getStyle($a.$RowPos)
            ->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle($a.$RowPos)
            ->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle($a.$RowPos)
            ->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle($a.$RowPos)
            ->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    };
}
?>