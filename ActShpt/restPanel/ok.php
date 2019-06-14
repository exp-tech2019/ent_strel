<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 09.06.2019
 * Time: 15:19
 */

header('Content-Type: application/json');

echo json_encode(array("success"=>$_POST["SmartCart"]));
?>