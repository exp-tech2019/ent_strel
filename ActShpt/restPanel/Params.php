<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 14.06.2019
 * Time: 19:34
 */
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

$AccessDolgnost=array(2, 17, 7, 12);
?>