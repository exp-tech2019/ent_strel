<?php
$data = array();
 
if( isset( $_GET['uploadfiles'] ) ){
    $error = false;
    $files = array();
     $uploaddir = './uploads/'; // . - текущая папка где находится submit.php
     // Создадим папку если её нет
     if( ! is_dir( $uploaddir ) ) mkdir( $uploaddir, 0777 );
     // переместим файлы из временной директории в указанную
    foreach( $_FILES as $file ){
        if( move_uploaded_file( $file['tmp_name'], $uploaddir . basename($file['name']) ) ){
            $files[] = realpath( $uploaddir . $file['name'] );
        }
        else{
            $error = true;
        }
    }
	//Обработки результатов
	if($error)
	{
		echo "Ошибка загрузки файла";
	}
	else
		//echo json_encode( readExelFile($files[0]));
		echo json_encode( readExelFile($files[0]) );
}

function readExelFile($filepath){
	require_once "../PHPExcel/PHPExcel.php"; //подключаем наш фреймворк
	$ar=array(); // инициализируем массив
	$inputFileType = PHPExcel_IOFactory::identify($filepath);  // узнаем тип файла, excel может хранить файлы в разных форматах, xls, xlsx и другие
	$objReader = PHPExcel_IOFactory::createReader($inputFileType); // создаем объект для чтения файла
	//$objReader=setReadDataOnly(true);
	$objPHPExcel = $objReader->load($filepath); // загружаем данные файла в объект
	$objWorksheet = $objPHPExcel->getActiveSheet();
	$ar=array();
	$r=0; $NullRowCount=0;
	foreach( $objWorksheet->getRowIterator() as $row ) 
        if($r<400)
	   {
            $NullRow=true;
            $c=0; $ac=array();
            foreach( $row->getCellIterator() as $cell )
            	if($c<25)
            	{
                	$value =(string) $cell->getValue();
                	if(($value!=null & $value!="") || $r<10) $NullRow=false;
                	$ac[$c]=$value==null?"":$value;
                	$c++;
            	};
            
            if($NullRow) $NullRowCount++;
            if($NullRowCount==3) break;
            if(isset($ac[0])) if(strpos(mb_strtolower(mb_convert_encoding($ac[0],'UTF-8')),'итого')) break;

            if($r<9 || isset($ac[1]))
            	$ar[$r]=$ac;
            $r++;
        }
        else
            break;
	$objPHPExcel->disconnectWorksheets();
	return $ar; 
}
?>