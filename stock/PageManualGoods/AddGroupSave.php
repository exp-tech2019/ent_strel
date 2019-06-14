<?php
/**
 * Created by PhpStorm.
 * User: anikulshin
 * Date: 26.01.2017
 * Time: 16:48
 */
    include "../params.php";
    $m=new mysqli($GlobalDBHost,$GlobalDBLogin,$GlobalDBPass,$GlobalDBName);
    $Action=$_POST["Action"];
    $idGroup=$_POST["id"];
    $Name=$_POST["Name"];
    $Step=$_POST["Step"];
    switch($Action){
        case "Add":
            $m->query("INSERT INTO stocknewmanualgroups (Name, Step) VALUES ('$Name', $Step)");
            break;
        case "Edit":
            $m->query("UPDATE stocknewmanualgroups SET Name='$Name', Step=$Step WHERE id=$idGroup AND NotRemove IS NULL");
            break;
        case "Remove":
            $d=$m->query("SELECT * FROM stocknewmanualgroups WHERE id=$idGroup AND NotRemove IS NULL");
            if($d->num_rows>0)
                $m->query("DELETE FROM stocknewmanualgroups WHERE id=$idGroup AND NotRemove IS NULL");
            break;
    }
?>