<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 15.06.2019
 * Time: 0:35
 */
require "../vendor/autoload.php";
use Endroid\QrCode\QrCode;

$qrCode = new QrCode($_GET["QrCode"]);
$qrCode->setSize(70);

header('Content-Type: '.$qrCode->getContentType());
echo $qrCode->writeString();
?>