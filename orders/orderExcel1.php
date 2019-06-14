<?php

		$ar=readExelFile("");
		echo json_encode ($ar);

function readExelFile($filepath){
	require_once "../PHPExcel/PHPExcel.php"; //подключаем наш фреймворк
	$ar=array(); // инициализируем массив
	$inputFileType = PHPExcel_IOFactory::identify("C:/xampp/htdocs/www/ent.a1120.ru/3434.xlsx");  // узнаем тип файла, excel может хранить файлы в разных форматах, xls, xlsx и другие
	$objReader = PHPExcel_IOFactory::createReader($inputFileType); // создаем объект для чтения файла
	$objPHPExcel = $objReader->load("C:/xampp/htdocs/www/ent.a1120.ru/3434.xlsx"); // загружаем данные файла в объект
	$ar = $objPHPExcel->getActiveSheet()->toArray(); // выгружаем данные из объекта в массив
	return $ar; //возвращаем массив
}
?>