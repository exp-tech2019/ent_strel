<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 18.12.2016
 * Time: 11:23
 */
    $idCustomer=$_GET["idCustomer"];
    $m->query("DELETE FROM Customers WHERE id=$idCustomer");
    include "Customers/index.php";
?>