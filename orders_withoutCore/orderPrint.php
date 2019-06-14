<?php
	include("../mpdf53/mpdf.php");		
			$mpdf = new mPDF('utf-8', 'A4', '8', '', 10, 10, 7, 7, 10, 10); /*задаем формат, отступы и.т.д.*/
			$mpdf->charset_in = 'UTF8';
			$mpdf->list_indent_first_level = 0; 
			$html='<p>df</p>';
			$mpdf->WriteHTML($html, 2); /*формируем pdf*/
			$mpdf->AddPage();
			$mpdf->Output('order.pdf' , 'F');
			$file='order.pdf';
	header('Content-type: application/pdf');
header('Content-Disposition: inline; filename="the.pdf"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($file));
@readfile($file);

?>