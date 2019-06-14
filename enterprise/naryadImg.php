<?php
//header ("Content-type: image/png");
$im = imagecreatetruecolor(400, 500);
$black = imagecolorallocate($im, 0, 0, 0);
$ink = imagecolorallocate($im, 255, 255, 255);
$gray = imagecolorallocate($im, 128, 128, 128);

imagefilledrectangle($im,0,0,400,500,$ink);
imagesetthickness($im,3);

if(isset($_GET["idNaryad"]) || isset($_GET["idDoor"]))
{
	$XMLParams=simplexml_load_file("../params.xml");
	$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
	$m->set_charset("cp1251");
	$CmdStr="";
	if(isset($_GET["idNaryad"]))
		$CmdStr="SELECT o1.* FROM orderdoors o1, naryad n WHERE o1.id=n.idDoors AND n.id=".$_GET["idNaryad"];
	if(isset($_GET["idDoor"]))
		$CmdStr="SELECT o1.* FROM orderdoors o1 WHERE o1.id=".$_GET["idDoor"];
	$d=$m->query($CmdStr);
	if($d->num_rows>0)
	{
		$r=$d->fetch_assoc();
		if($r["H"]!=null & $r["W"]!=null)
		{
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
			//Определим кол-во окон
			$WindowCount=0;
			if($r["WorkWindowCh"]==1) $WindowCount++;
			if($r["WorkWindowCh1"]==1) $WindowCount++;
			if($r["WorkWindowCh2"]==1) $WindowCount++;
			if($WindowCount>0)
			{
				$WindowNumArr=array("","1","2");
				$X0=50+$DoorOpenWorkX;
				$Y0=180; $Y1=360;
				$Yh=floor(($Y1-$Y0-$WindowCount*10)/$WindowCount);//Высота одного окна
				$Yd0=$Y0; $i=0;
				while($Yd0<$Y1)
				{
					if($r["WorkWindowCh".$WindowNumArr[$i]]==1)
					{
						imagerectangle($im,$X0,$Yd0,$X0+100,$Yd0+$Yh,$black);
						//Размеры
						if($r["WorkWindowW".$WindowNumArr[$i]]!=null)
							imagettftext($im, 18, 0, $X0+30,$Yd0+$Yh-10, $black, "arial.ttf", $r["WorkWindowW".$WindowNumArr[$i]]);
						if($r["WorkWindowH".$WindowNumArr[$i]]!=null)
							imagettftext($im, 18, 90, $X0+90,floor(($Yd0+$Yh)/2), $black, "arial.ttf", $r["WorkWindowH".$WindowNumArr[$i]]);
						
						//Решетка
						if($r["WorkWindowGain".$WindowNumArr[$i]]==1) CanvasReinforcementRect($X0,$Yd0,100,$Yh);
						if($r["WorkWindowGlass".$WindowNumArr[$i]]==1) CanvasGlassRect($X0,$Yd0,100,$Yh);
						if($r["WorkWindowGrid".$WindowNumArr[$i]]==1) CanvasGridRect($X0,$Yd0,100,$Yh);
						
						$Yd0+=$Yh+10;
					};
					$i++;
				};		
			};
			
			//Решетка
			if($r["WorkUpGridCh"]==1)
				for($i=0;$i<20; $i+=7)
					imageline($im,50+$DoorOpenWorkX,140+$i,50+$DoorOpenWorkX+100,140+$i,$black);
			if($r["WorkDownGridCh"]==1)
				for($i=0;$i<20; $i+=7)
					imageline($im,50+$DoorOpenWorkX,380+$i,50+$DoorOpenWorkX+100,380+$i,$black);
			
			//Вторая створка
			if($r["S"]!=""|| $r["SEqual"]=="1")
			{
				imagesetthickness($im,3);
				imagerectangle($im,$DoorOpenStvorkaX,110,$DoorOpenStvorkaX+$DoorStvorkaW,460,$black);
				//Окна
				//Определим кол-во окон
				$WindowCount=0;
				if($r["StvorkaWindowCh"]==1) $WindowCount++;
				if($r["StvorkaWindowCh1"]==1) $WindowCount++;
				if($r["StvorkaWindowCh2"]==1) $WindowCount++;
				if($WindowCount>0)
				{
					$WindowNumArr=array("","1","2");
					$X0=$DoorOpenStvorkaX+25;
					$Y0=180; $Y1=360;
					$Yh=floor(($Y1-$Y0-$WindowCount*10)/$WindowCount);//Высота одного окна
					$Yd0=$Y0; $i=0;
					while($Yd0<$Y1)
					{
						if($r["StvorkaWindowCh".$WindowNumArr[$i]]==1)
						{
							imagerectangle($im,$X0,$Yd0,$X0+100,$Yd0+$Yh,$black);
							//Размеры
							if($r["StvorkaWindowW".$WindowNumArr[$i]]!=null)
								imagettftext($im, 18, 0, $X0+30,$Yd0+$Yh-10, $black, "arial.ttf", $r["StvorkaWindowW".$WindowNumArr[$i]]);
							if($r["StvorkaWindowH".$WindowNumArr[$i]]!=null)
								imagettftext($im, 18, 90, $X0+90,floor(($Yd0+$Yh)/2), $black, "arial.ttf", $r["StvorkaWindowH".$WindowNumArr[$i]]);
							
							//Решетка
							if($r["StvorkaWindowGain".$WindowNumArr[$i]]==1) CanvasReinforcementRect($X0,$Yd0,100,$Yh);
							if($r["StvorkaWindowGlass".$WindowNumArr[$i]]==1) CanvasGlassRect($X0,$Yd0,100,$Yh);
							if($r["StvorkaWindowGrid".$WindowNumArr[$i]]==1) CanvasGridRect($X0,$Yd0,100,$Yh);
							
							$Yd0+=$Yh+10;
						};
						$i++;
					};		
				};
				//Окно
				/*
				if($r["StvorkaWindowCh"]==1)
				{
					imagerectangle($im,$DoorOpenStvorkaX+25,180,$DoorOpenStvorkaX+125,360,$black);
					if($r["StvorkaWindowGain"]==1) CanvasReinforcementRect(235-($DoorOpenX/3*4),160,100,200);
					if($r["StvorkaWindowGlass"]==1) CanvasGlassRect(235-($DoorOpenX/3*4),160,100,200);
					if($r["StvorkaWindowGrid"]==1) CanvasGridRect(235-($DoorOpenX/3*4),160,100,200);
					//Размеры
					if($r["StvorkaWindowW"]!=null)
						imagettftext($im, 18, 0, $DoorOpenStvorkaX+50,350, $black, "arial.ttf", $r["StvorkaWindowW"]);
					if($r["StvorkaWindowH"]!=null)
						imagettftext($im, 18, 90, $DoorOpenStvorkaX+115,280, $black, "arial.ttf", $r["StvorkaWindowH"]);
				};
				*/
				//Решетка
				if($r["StvorkaUpGridCh"]==1)
					for($i=0;$i<20; $i+=7)
						imageline($im,$DoorOpenStvorkaX+25,140+$i,$DoorOpenStvorkaX+125,140+$i,$black);
				if($r["WorkDownGridCh"]==1)
					for($i=0;$i<20; $i+=7)
						imageline($im,$DoorOpenStvorkaX+25,380+$i,$DoorOpenStvorkaX+125,380+$i,$black);
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
					imagerectangle($im,60,40,250-$FramugaXMinus+60,80,$black);
					if($r["FramugaWindowGain"]==1) CanvasReinforcementRect(60,30,250-$FramugaXMinus,60);
					if($r["FramugaWindowGlass"]==1) CanvasGlassRect(60,30,250-$FramugaXMinus,60);
					if($r["FramugaWindowGrid"]==1) CanvasGridRect(60,30,250-$FramugaXMinus,60);
					//Размеры
					if($r["FramugaWindowW"]!=null)
						imagettftext($im, 18, 0, 220-$FramugaXMinus,80, $black, "arial.ttf", $r["FramugaWindowW"]);
					if($r["FramugaWindowH"]!=null)
						imagettftext($im, 18, 90, 250-$FramugaXMinus+50,80, $black, "arial.ttf", $r["FramugaWindowH"]);
				};
				//Решетка
				if($r["FramugaUpGridCh"]==1)
					for($i=0;$i<15; $i+=5)
						imageline($im,60,20+$i,250-$FramugaXMinus+60,20+$i,$black);
				if($r["FramugaDownGridCh"]==1)
					for($i=0;$i<15; $i+=5)
						imageline($im,60,90+$i,250-$FramugaXMinus+60,90+$i,$black);
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
			imagettftext($im, 20, 0, $Tx+10,490, $black, "arial.ttf", $r["W"]);
			//Ширина рабочей створки
			if($r["S"]!="") 
			{
				if($r["Open"]=="Лев.")  imagettftext($im, 20, 0, 60,450, $black, "arial.ttf", $r["S"]);//imageString($im, 16, 60,440, $r["S"], $black);
				if($r["Open"]=="Прав.")  imagettftext($im, 20, 0, 260,450, $black, "arial.ttf", $r["S"]); //imageString($im, 16, 260,440, $r["S"], $black);
			};
			//Отметка что дверь равнопольная
			if($r["SEqual"]=="1") 
			{
				include("phprfont.php");
				$fid3 = loadfont('./fonts/SSERIFF/20');
				if($r["Open"]=="Лев.") draw_str($im, $fid3, 120, 438, 'Равнопольная', 0);
				if($r["Open"]=="Прав.") draw_str($im, $fid3, 120, 438, 'Равнопольная', 0);
				/*
				if($r["Open"]=="Лев.")  imagettftext($im, 14, 0, 60,450, $black, "arial.ttf", "RAVNOPOL");//imageString($im, 16, 60,440, $r["S"], $black);
				if($r["Open"]=="Прав.")  imagettftext($im, 14, 0, 260,450, $black, "arial.ttf", "RAVNOPOL"); //imageString($im, 16, 260,440, $r["S"], $black);
				*/
			};
			$Ty=350/2+110; if($r["FramugaWindowCh"]==1) $Ty=450/2+10;
			imagettftext($im, 20, 90, $Tx*2+40,$Ty+20, $black, "arial.ttf", $r["H"]);
			if($r["FramugaCh"]==1 & $r["FramugaH"]!=null)//Высота фрамуги
				imagettftext($im, 18, 90, 340-$FramugaXMinus+10,100, $black, "arial.ttf", $r["FramugaH"]);
		};
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