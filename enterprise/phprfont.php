<?php
  function setfontorient( & $fid, $orient)
  {
     if (!function_exists('imagerotate')) return FALSE;

     $old_orient = $fid['orient'];
     $rot = (int)(($orient - $old_orient)*90);

     if ($rot == 0) return TRUE; // уже повернут

     $fid['orient'] = $orient;

     // так как при повороте изображени€ палитра почему-то
     // ломаетс€ и нет возможности ее восстановить
     // то поворачиваем $fid['im'] по-хитрому,
     // создава€ промежуточную картинку

     $sx = ImageSX($fid['im']);
     $sy = ImageSY($fid['im']);
     if ((abs($orient - $old_orient)%2) > 0) { $tmp=$sx; $sx=$sy; $sy=$tmp; }
     $imnew = imagecreate($sx, $sy);
     imagepalettecopy($imnew, $fid['im']);
     imagecopy($imnew, imagerotate($fid['im'], $rot, 0), 0,0,0,0, $sx, $sy);
     imagedestroy($fid['im']);
     $fid['im'] = $imnew;

     /*
     if (isset($fid['imt'])) {
        imagedestroy($fid['imt']);
        unset($fid['imt']);

        $src_w = ImageSX($fid['im']);
        $src_h = ImageSY($fid['im']);
        $tc_img = imagecreatetruecolor($src_w, $src_h);
        imagecopy($tc_img, $fid['im'], 0,0,0,0, $src_w, $src_h);
        $carr = imagecolorsforindex($fid['im'], 255);
        imagecolortransparent($tc_img,
           imagecolorallocate($tc_img, $carr[red], $carr[green], $carr[blue]));
        $fid['imt'] = $tc_img;
     }
     */
     refresh_imt($fid);

     return TRUE;
  }

  // загрузить шрифт
  function loadfont( $fontpath , $orient=0)
  {
     $fid = array();

     // пытаемс€ считать таблицу размеров символов
     $fsize = filesize( $fontpath.".wid" );
     $wf = @fopen( $fontpath.".wid", "rb");
     if ($wf) {
        $wid = fread($wf, $fsize);
        fclose($wf);
        $wid = split(',', $wid);
        // пытаемс€ считать изображени€ символов
        $im = imagecreatefrompng( $fontpath.".png" );
        imagecolortransparent($im, imagecolorallocate($im, 255, 255, 255));
        $fid['wid'] = $wid;
        $fid['im'] = $im;
        $sum = 0;
        $ofs = array();
        for ($i=0; $i<256; $i++) {
           $ofs[$i] = $sum;
           $sum += $wid[$i];
        }
        $fid['ofs'] = $ofs;
        $fid['h'] = imagesy($im);
        $fid['ofsx'] = $sum; // смещение символа-промежутка

        $fid['orient'] = 0;

        if ($orient != 0) setfontorient($fid, $orient);
        return $fid;
     }
     return $fid;
  }

  // выгрузить фонт, освободить пам€ть
  function unloadfont( & $fid )
  {
     if (isset($fid['im']))  imagedestroy($fid['im']);
     if (isset($fid['imt'])) imagedestroy($fid['imt']);
     unset($fid);
  }

  function refresh_imt(& $fid)
  {
     if (isset($fid['imt'])) {
        imagedestroy($fid['imt']);
        unset($fid['imt']);

        $src_w = ImageSX($fid['im']);
        $src_h = ImageSY($fid['im']);
        $tc_img = imagecreatetruecolor($src_w, $src_h);
        imagecopy($tc_img, $fid['im'], 0,0,0,0, $src_w, $src_h);
        $carr = imagecolorsforindex($fid['im'], 255);
        imagecolortransparent($tc_img,
           imagecolorallocate($tc_img, $carr[red], $carr[green], $carr[blue]));
        $fid['imt'] = $tc_img;
     }
  }


  function setfontcolor(& $fid, $symc_r, $symc_g, $symc_b,
                        $bgc_r, $bgc_g, $bgc_b)
  {
     imagecolorset($fid['im'], 0, $symc_r, $symc_g, $symc_b);
     imagecolorset($fid['im'], 255, $bgc_r, $bgc_g, $bgc_b);

     /*
     if (isset($fid['imt'])) {
        imagedestroy($fid['imt']);
        unset($fid['imt']);

        $src_w = ImageSX($fid['im']);
        $src_h = ImageSY($fid['im']);
        $tc_img = imagecreatetruecolor($src_w, $src_h);
        imagecopy($tc_img, $fid['im'], 0,0,0,0, $src_w, $src_h);
        $carr = imagecolorsforindex($fid['im'], 255);
        imagecolortransparent($tc_img,
           imagecolorallocate($tc_img, $carr[red], $carr[green], $carr[blue]));
        $fid['imt'] = $tc_img;
     }
     */
     refresh_imt($fid);
  }


  // spc - промежутки между символами в пикселах
  function eval_str_width(& $fid, $str, $spc)
  {
     $cn = 0;
     $wid = 0;
     $spc_add = 0;
     $rar = unpack("C*", $str);
     foreach($rar as $c) {
        $wid += ($fid['wid'][(int)$c] + $spc_add);
        $spc_add = $spc;
     }
     return $wid;
  }

  function draw_str($im, & $fid, $x, $y, $str, $spc, $mrg=100)
  {
     if (strlen($str)==0) return;

     $rar = unpack("C*", $str);
     $i = 1;
     $strofs = 0;
     $sflag = 0; //признак не левого символа
     if ($spc > $fid['h']) $spc = $fid['h'];
     $ofsx = $fid['ofsx'];
     $orient = $fid['orient'];
     $esw = eval_str_width($fid, $str, $spc);
     $imtsy = imagesy($fid['im']);
     $imtsx = imagesx($fid['im']);

     while (isset($rar[$i])) {

        if ($sflag) // перед каждым символом кроме первого рисуем промежуток
        {
           if ($spc > 0) {
              if ($mrg==100)
                 switch ($orient) {
                   case 0:
                     imagecopy($im, $fid['im'], $x+$strofs, $y, $ofsx, 0, $spc, $fid['h']);
                     break;
                   case 1:
                     imagecopy($im, $fid['im'], $x, $y+$esw-$strofs-$spc, 0, $imtsy-$ofsx-$widc, $fid['h'], $spc);
                     break;
                   case 2:
                     imagecopy($im, $fid['im'], $x+$esw-$strofs-$spc, $y, $imtsx-$ofsx-$widc, 0, $spc, $fid['h']);
                     break;
                   case 3:
                     imagecopy($im, $fid['im'], $x, $y+$strofs, 0, $ofsx, $fid['h'], $spc);
                     break;
                 }
              else
                 switch ($orient) {
                   case 0:
                     imagecopymerge($im, $fid['im'], $x+$strofs, $y, $ofsx, 0, $spc, $fid['h'], $mrg);
                     break;
                   case 1:
                     imagecopymerge($im, $fid['im'], $x, $y+$esw-$strofs-$spc, 0, $imtsy-$ofsx-$widc, $fid['h'], $spc, $mrg);
                     break;
                   case 2:
                     imagecopymerge($im, $fid['im'], $x+$esw-$strofs-$spc, $y, $imtsx-$ofsx-$widc, 0, $spc, $fid['h'], $mrg);
                     break;
                   case 3:
                     imagecopymerge($im, $fid['im'], $x, $y+$strofs, 0, $ofsx, $fid['h'], $spc, $mrg);
                     break;
                 }

              $strofs += $spc;
           }
        }
        else $sflag = 1;

        $c = $rar[$i];

        $widc = $fid['wid'][(int)$c];
        $ofsc = $fid['ofs'][(int)$c];
        if ($mrg==100)
           switch ($orient) {
             case 0:
               imagecopy($im, $fid['im'], $x+$strofs, $y, $ofsc, 0, $widc, $fid['h']);
               break;
             case 1:
               imagecopy($im, $fid['im'], $x, $y+$esw-$strofs-$widc, 0, $imtsy-$ofsc-$widc, $fid['h'], $widc);
               break;
             case 2:
               imagecopy($im, $fid['im'], $x+$esw-$strofs-$widc, $y, $imtsx-$ofsc-$widc, 0, $widc, $fid['h']);
               break;
             case 3:
               imagecopy($im, $fid['im'], $x, $y+$strofs, 0, $ofsc, $fid['h'], $widc);
               break;
           }
        else
           switch ($orient) {
             case 0:
               imagecopymerge($im, $fid['im'], $x+$strofs, $y, $ofsc, 0, $widc, $fid['h'], $mrg);
               break;
             case 1:
               imagecopymerge($im, $fid['im'], $x, $y+$esw-$strofs-$widc, 0, $imtsy-$ofsc-$widc, $fid['h'], $widc, $mrg);
               break;
             case 2:
               imagecopymerge($im, $fid['im'], $x+$esw-$strofs-$widc, $y, $imtsx-$ofsc-$widc, 0, $widc, $fid['h'], $mrg);
               break;
             case 3:
               imagecopymerge($im, $fid['im'], $x, $y+$strofs, 0, $ofsc, $fid['h'], $widc, $mrg);
               break;
           }

        $strofs += $widc;
        $i++;
     }
  }

  // вывод строки с прозрачным фоном символов
  function draw_strx($im, & $fid, $x, $y, $str, $spc, $mrg=100)
  {
     if (strlen($str)==0) return;

     // создаем imt truecolor image
     if (!isset($fid['imt'])) {
        $src_w = ImageSX($fid['im']);
        $src_h = ImageSY($fid['im']);
        $tc_img = imagecreatetruecolor($src_w, $src_h);
        imagecopy($tc_img, $fid['im'], 0,0,0,0, $src_w, $src_h);
        $carr = imagecolorsforindex($fid['im'], 255);
        imagecolortransparent($tc_img,
           imagecolorallocate($tc_img, $carr[red], $carr[green], $carr[blue]));
        $fid['imt'] = $tc_img;
     }

     $rar = unpack("C*", $str);
     $i = 1;
     $strofs = 0;
     $sflag = 0; //признак не левого символа
     if ($spc > $fid['h']) $spc = $fid['h'];
     $ofsx = $fid['ofsx'];
     $orient = $fid['orient'];
     $esw = eval_str_width($fid, $str, $spc);
     $imtsy = imagesy($fid['imt']);
     $imtsx = imagesx($fid['imt']);

     while (isset($rar[$i])) {

        if ($sflag) // перед каждым символом кроме первого рисуем промежуток
        {
           $strofs += $spc;
        }
        else $sflag = 1;

        $c = $rar[$i];

        $widc = $fid['wid'][(int)$c];
        $ofsc = $fid['ofs'][(int)$c];

        switch ($orient) {
          case 0:
            imagecopymerge($im, $fid['imt'], $x+$strofs, $y, $ofsc, 0, $widc, $fid['h'], $mrg);
            break;
          case 1:
            imagecopymerge($im, $fid['imt'], $x, $y+$esw-$strofs-$widc, 0, $imtsy-$ofsc-$widc, $fid['h'], $widc, $mrg);
            break;
          case 2:
            imagecopymerge($im, $fid['imt'], $x+$esw-$strofs-$widc, $y, $imtsx-$ofsc-$widc, 0, $widc, $fid['h'], $mrg);
            break;
          case 3:
            imagecopymerge($im, $fid['imt'], $x, $y+$strofs, 0, $ofsc, $fid['h'], $widc, $mrg);
            break;
        }

        $strofs += $widc;
        $i++;
     }
  }
?>