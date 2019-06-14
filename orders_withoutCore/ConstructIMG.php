<?php
header ("Content-type: image/png");
$im = imagecreatetruecolor(400, 500);
$black = imagecolorallocate($im, 0, 0, 0);
$ink = imagecolorallocate($im, 255, 255, 255);
$gray = imagecolorallocate($im, 128, 128, 128);

imagefilledrectangle($im,0,0,400,500,$ink);
imagesetthickness($im,3);
if($_GET["idDoor"]!="")
{
	$m=new mysqli('localhost','root','Rfhkcjy:bd2010','ent');
	$m->set_charset("cp1251");
	$d=$m->query("SELECT * FROM orderdoors WHERE id=".$_GET["idDoor"]);
	if($d->num_rows>0)
	{
		$r=$d->fetch_assoc();
		//Открывание
		if($r["Open"]=="Прав." & $r["S"]=="" & $r["SEqual"]!="1") imageline($im,30,250,30,280, $black);
		if($r["Open"]=="Лев." & $r["S"]=="" & $r["SEqual"]!="1") imageline($im,190,250,190,280, $black);
		if($r["Open"]=="Прав." & $r["S"]!="" & $r["SEqual"]!="1") imageline($im,190,250,190,280, $black);
		if($r["Open"]=="Лев." & $r["S"]!="" & $r["SEqual"]!="1") imageline($im,190,250,190,280, $black);
		if($r["Open"]=="Прав." & $r["SEqual"]=="1") imageline($im,215,250,215,280, $black);
		if($r["Open"]=="Лев." & $r["SEqual"]=="1") imageline($im,170,250,170,280, $black);
	
		
	$DoorOpenX=0; if($r["Open"]=="Прав." & $r["S"]!="") $DoorOpenX=150;
	$DoorOpenWorkX=10; 
	if($r["Open"]=="Прав." & $r["S"]!="") $DoorOpenWorkX=160;
	if($r["Open"]=="Прав." & $r["SEqual"]=="1") $DoorOpenWorkX=185;
	$DoorOpenStvorkaX=210; 
	if($r["Open"]=="Прав." & ($r["S"]!="" || $r["SEqual"]=="1")) $DoorOpenStvorkaX=10;
	if($r["Open"]=="Лев." & $r["SEqual"]=="1") $DoorOpenStvorkaX=185;
	
	$DoorWorkW=($r["SEqual"]=="1")?175:200; 
	$DoorStvorkaW=($r["SEqual"]=="1")?175:150;
		
	//Рабочая створка
	imagerectangle($im,$DoorOpenWorkX,110,$DoorOpenWorkX+$DoorWorkW,460,$black);
	
	//Окно
	if($r["WorkWindowCh"]==1)
	{
		imagerectangle($im,50+$DoorOpenWorkX,160,50+$DoorOpenWorkX+100,360,$black);
		//Решетка
		if($r["WorkWindowGrid"]==1) CanvasReinforcementRect(50+$DoorOpenWorkX,160,100,200);
		if($r["WorkWindowGlass"]==1) CanvasGlassRect(50+$DoorOpenWorkX,160,100,200);
	};
	
	//Вторая створка
	if($r["S"]!=""|| $r["SEqual"]=="1")
	{
		imagesetthickness($im,3);
		imagerectangle($im,$DoorOpenStvorkaX,110,$DoorOpenStvorkaX+$DoorStvorkaW,460,$black);
		//Окно
		if($r["StvorkaWindowCh"]==1)
		{
			imagerectangle($im,$DoorOpenStvorkaX+25,160,$DoorOpenStvorkaX+125,360,$black);
			if($r["StvorkaWindowGrid"]==1) CanvasReinforcementRect(235-($DoorOpenX/3*4),160,100,200);
			if($r["StvorkaWindowGlass"]==1) CanvasGlassRect(235-($DoorOpenX/3*4),160,100,200);
		};
	};
		//Фрамуга
		$FramugaXMinus=0; if($r["S"]=="" & $r["SEqual"]!="1") $FramugaXMinus=150;
		if($r["FramugaCh"]==1)
		{
			imagesetthickness($im,3);
			imagerectangle($im,10,10,350-$FramugaXMinus+10,110,$black);
			//Окно
			if($r["FramugaWindowCh"]==1)
			{	
				imagerectangle($im,60,30,250-$FramugaXMinus+60,90,$black);
				if($r["FramugaWindowGrid"]==1) CanvasReinforcementRect(60,30,250-$FramugaXMinus,60);
				if($r["FramugaWindowGlass"]==1) CanvasGlassRect(60,30,250-$FramugaXMinus,60);
			};
		};
		//Рабочая створка -> Петли
		if((int) $r["WorkPetlya"]>0)
		{
			$xPetlya=10;
			if($r["Open"]=="Прав." & $r["S"]=="" & $r["SEqual"]!="1") $xPetlya=210;
			if($r["Open"]=="Прав." & ($r["S"]!="" || $r["SEqual"]=="1")) $xPetlya=360;
			$sPetlya=350/((int) $r["WorkPetlya"]+1);
			$c=$sPetlya;
			for($i=1;$i<=(int) $r["WorkPetlya"];$i++)
			{
				CanvasLineCross($xPetlya,$c+110);
				$c+=$sPetlya;
			};
		};
		
		//Вторая створка -> Петли
		if((int) $r["StvorkaPetlya"]>0 & ($r["S"]!="" || $r["SEqual"]=="1"))
		{
			$xPetlya=360;
			if($r["Open"]=="Прав.") $xPetlya=10;
			$sPetlya=350/((int) $r["StvorkaPetlya"]+1);
			$c=$sPetlya;
			for($i=1;$i<=(int) $r["StvorkaPetlya"];$i++)
			{
				CanvasLineCross($xPetlya,$c+110);
				$c+=$sPetlya;
			};
		};
		
		//Размеры
		$Tx=350/2; if($r["S"]=="" & $r["SEqual"]!="1") $Tx=200/2;
		$text=$r["W"]; $font="arial.ttf";
		imagettftext($im, 18, 0, $Tx+10,480, $black, "arial.ttf", $r["W"]);
		//Ширина рабочей створки
		if($r["S"]!="") 
		{
			if($r["Open"]=="Лев.")  imagettftext($im, 18, 0, 60,450, $black, "arial.ttf", $r["S"]);//imageString($im, 16, 60,440, $r["S"], $black);
			if($r["Open"]=="Прав.")  imagettftext($im, 18, 0, 260,450, $black, "arial.ttf", $r["S"]); //imageString($im, 16, 260,440, $r["S"], $black);
		};
		//Отметка что дверь равнопольная
		if($r["SEqual"]=="1") 
		{
			if($r["Open"]=="Лев.")  imagettftext($im, 18, 0, 60,450, $black, "arial.ttf", "RAVNOPOL");//imageString($im, 16, 60,440, $r["S"], $black);
			if($r["Open"]=="Прав.")  imagettftext($im, 18, 0, 260,450, $black, "arial.ttf", "RAVNOPOL"); //imageString($im, 16, 260,440, $r["S"], $black);
		};
		$Ty=350/2+110; if($r["FramugaWindowCh"]==1) $Ty=450/2+10;
		imagettftext($im, 18, 90, $Tx*2+34,$Ty+20, $black, "arial.ttf", $r["H"]);
		if($r["FramugaCh"]==1 & $r["FramugaH"]!=null)//Высота фрамуги
			imagettftext($im, 18, 90, 340-$FramugaXMinus+10,100, $black, "arial.ttf", $r["FramugaH"]);
	};
};

imagepng($im);
imagedestroy($im);

function CanvasReinforcementRect($x0,$y0,$w,$h)
{
	global $im,$black;
	$x1=$x0+$w; $y1=$y0+$h;
	$s=round( ($y1-$y0) / 6 );
	imagesetthickness($im,1);
	
	//for($Sy=$y0+20; $Sy<$y1;$Sy+=20)
	for($Sy=$y1-$s; $Sy>=$y1 - round( ($y1-$y0) / 2 ); $Sy-=$s)
		imageline($im,$x0,$Sy,$x1,$Sy, $black);
}

function CanvasGridRect($x0,$y0,$w,$h)
{
	global $im,$black;
	$x1=$x0+$w; $y1=$y0+$h;
	imagesetthickness($im,1);
	for($Sx=$x0+20; $Sx<$x1;$Sx+=20)
		imageline($im,$Sx,$y0,$Sx,$y1, $black);
	for($Sy=$y0+20; $Sy<$y1;$Sy+=20)
		imageline($im,$x0,$Sy,$x1,$Sy, $black);
}

function CanvasGlassRect($x0,$y0,$w,$h)
{
	global $im,$black;
	$x1=$x0+$w; $y1=$y0+$h;
	imagesetthickness($im,1);
	for( $Sx=$x0+20;$Sx<=$x1;$Sx+=20)
		imageline($im,$Sx-20,$y0,$Sx,$y1, $black);
}
function CanvasLineCross($x,$y)
{
	global $im,$black;
	imagesetthickness($im,3);
	$x1=$x-10; $y1=$y-10;
	imageline($im,$x1,$y1,$x1+20,$y1+20, $black);
	imageline($im,$x1,$y1+20,$x1+20,$y1, $black);
}
?>