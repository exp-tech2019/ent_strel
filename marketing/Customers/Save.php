<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 18.12.2016
 * Time: 10:53
 */
    $idCustomer=$_POST["idCustomer"];
    $Name=$_POST["Name"];
    $INN=$_POST["INN"];
    switch ($idCustomer){
        case "": $m->query("INSERT INTO Customers (Name, INN) VALUES ('$Name', '$INN')"); break;
        default: $m->query("UPDATE Customers SET Name='$Name', INN='$INN' WHERE id=$idCustomer"); break;
    };
    include "Customers/index.php";
?>