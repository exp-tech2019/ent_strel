<?
header ("Content-type: image/png");
$im = imagecreatetruecolor(320, 370);
$black = imagecolorallocate($im, 0, 0, 0);
$ink = imagecolorallocate($im, 255, 255, 255);
$gray = imagecolorallocate($im, 128, 128, 128);

imagefilledrectangle($im,0,0,320,370,$ink);

if($_GET["idNaryad"]!="")
{
	$m=new mysqli('localhost','root','Rfhkcjy:bd2010','ent');
	$m->set_charset("cp1251");
	$d=$m->query("SELECT o1.* FROM orderdoors o1, naryad n WHERE o1.id=n.idDoors AND n.id=".$_GET["idNaryad"]);
	if($d->num_rows>0)
	{
		$r=$d->fetch_assoc();
		//Контур
		imagerectangle($im,5,5,295,340,$black);
		imageString($im, 12, 120, 340, $r["W"], $black);
		imageStringUp($im, 12, 300, 170, $r["H"], $black);

		//Размер второй створки
		if($r["S"]!=null)
		{
			imagerectangle($im,5,5,170,340,$black);
			imageString($im, 12, 210, 320, $r["Open"], $black);
		};
		//Открывание левое
		if($r["Open"]=="Лев.")
			imageline($im,150,150,150,190,$black);
		//Открывание правое
		if($r["Open"]=="Прав.")
			imageline($im,190,150,190,190,$black);
	};
};

imagepng($im);
imagedestroy($im);
?>