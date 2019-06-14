<?php
/*
    header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
    header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
    header ( "Cache-Control: no-cache, must-revalidate" );
    header ( "Pragma: no-cache" );
    header ( "Content-type: application/vnd.ms-excel" );
    header ( "Content-Disposition: attachment; filename=matrix.xls" );*/
    require_once "../../PHPExcel/PHPExcel.php";
    require_once "../../PHPExcel/PHPExcel/Writer/Excel5.php";

    $xls=new PHPExcel();
    $xls->setActiveSheetIndex(0);
    $sht=$xls->getActiveSheet();
    $sht->setTitle("Список дверей");
    //Заголвок
    $Title=array(
        0=>array(
            "Name"=>"Счет",
            "Column"=>"A",
            "Width"=>10
        ),
        1=>array(
            "Name"=>"Кол-во дверей",
            "Column"=>"B",
            "Width"=>10
        ),
        2=>array(
            "Name"=>"Открывание",
            "Column"=>"C",
            "Width"=>16
        ),
        3=>array(
            "Name"=>"Размеры",
            "Column"=>"D",
            "Width"=>20
        ),
        4=>array(
            "Name"=>"",
            "Column"=>"E",
            "Width"=>10
        )
    );
    foreach ($Title as $a){
        $sht->setCellValue($a["Column"]."1",$a["Name"]);
        $sht->getColumnDimension($a["Column"])->setWidth($a["Width"]);
        //$sht->getStyle($a["Column"]."1")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
        $sht->getStyle($a["Column"]."1")->getFont()->setBold(true);
        $sht->getStyle($a["Column"]."1")->getFont()->setSize(14);
        //Выравнивание и перенос строк
        $sht->getStyle($a["Column"]."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sht->getStyle($a["Column"]."1")->getAlignment()->setWrapText(true);
        $sht->getStyle($a["Column"]."1")->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $sht->getStyle($a["Column"]."1")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
        $sht->getStyle($a["Column"]."1")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $sht->getStyle($a["Column"]."1")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        $sht->getStyle($a["Column"]."1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        //$sht->getStyle($a["Column"]."1")->getFill()->getStartColor()->setRGB('#f0ffff');
    };
    $Out=$_POST["OutExcel"];
    $i=2;
    var_dump($Out);
    if(isset($Out))
        foreach ($Out as $tr){

            $sht->setCellValue("A".($i),$tr["Shet"]);
            $sht->getStyle("A".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sht->mergeCells("A".$i.":A".($i+1));
            $sht->getStyle("A".$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sht->setCellValue("B".($i),$tr["Count"]);
            $sht->mergeCells("B".$i.":B".($i+1));
            $sht->getStyle("B".$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sht->getStyle("B".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sht->setCellValue("C".($i),$tr["Open"]);
            $sht->mergeCells("C".$i.":C".($i+1));
            $sht->getStyle("C".$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sht->getStyle("C".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sht->setCellValue("D".($i++),"Высота - ".$tr["H"]);
            $sht->setCellValue("D".($i),"Ширина - ".$tr["W"]);
            $i--;
            $sht->setCellValue("E".($i++),((int)$tr["H"]*(int)$tr["Count"]));
            $sht->setCellValue("E".($i),((int)$tr["W"]*(int)$tr["Count"]));
            foreach (array("A","B","C","D","E") as $Col)
                $sht->getStyle($Col.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $i++;
        }
/*
    for ($i = 2; $i < 10; $i++) {
        for ($j = 2; $j < 10; $j++) {
            // Выводим таблицу умножения
            $sht->setCellValueByColumnAndRow(
                $i - 2,
                $j,
                $i . "x" .$j . "=" . ($i*$j));
            // Применяем выравнивание
            $sht->getStyleByColumnAndRow($i - 2, $j)->getAlignment()->
            setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
    };*/
    $objWriter = new PHPExcel_Writer_Excel5($xls);
    $objWriter->save("report.xls");
?>