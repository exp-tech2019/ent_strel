<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 16.06.2019
 * Time: 2:21
 */
session_start();
header('Content-Type: application/json');
session_unset();
session_destroy();
echo json_encode(array("success"=>1));
?>