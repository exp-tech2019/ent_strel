<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $ResultArr=array("Result"=>"ok");
    if(isset($_POST["Login"]) & isset($_POST["Pass"])){
        $Login=$_POST["Login"];
        $Pass=$_POST["Pass"];

        include "param.php";
        $param=new GlobalParam();
        $m=new mysqli($param->DBHost,$param->DBUser,$param->DBPass,$param->DBName) or die($m->connect_error);
        $d=$m->query("SELECT id FROM Logins WHERE Login='$Login' AND Pass='$Pass'");
        if($d->num_rows>0)
        {
            $r=$d->fetch_assoc();
            $ResultArr=array(
                "Result"=>"ok",
                "idLogin"=>$r["id"]
            );
        };
    };
    echo json_encode($ResultArr);
?>