<?php
	 if(isset($_GET["idNaryad"])){
		include("barcode.class.php");
		$mybarcode = new barcode();
		$text=(string)$_GET["idNaryad"];
		/*
		while(strlen($text)<9)
			$text="0".$text;
			*/
		$text="N".$text."E";
		$text = "*$text*";
		$size = array(240,40);
		$mybarcode->image_create($text, $size);
		$mybarcode->show(); //See barcode.test.html
	};
?>