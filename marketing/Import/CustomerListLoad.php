<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 18.12.2016
 * Time: 17:06
 */
    include "../params.php";
    $m=new mysqli($GlobalDBHost,$GlobalDBLogin,$GlobalDBPass,$GlobalDBName);
    $d=$m->query("SELECT * FROM Customers ORDER BY Name");
    $a=array(); $i=0;
    while($r=$d->fetch_assoc()){
        $a[$i]=array("id"=>$r["id"], "Name"=>$r["Name"], "INN"=>$r["INN"]);
        $i++;
    };
    echo json_encode($a);
?>