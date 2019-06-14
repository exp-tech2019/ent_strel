<?php
    session_start();
    switch(isset($_SESSION["idLogin"])){
        case false:
            switch (isset($_POST["Login"]) & isset($_POST["Pass"])){
                case true:
                    $Login=$_POST["Login"];
                    $Pass=$_POST["Pass"];
                    $m=new mysqli("localhost","root","Rfhkcjy:bd2010","ent");
                    $d=$m->query("SELECT id ,FIO, Type FROM Logins WHERE Login='$Login' AND Pass='$Pass'");
                    $flagAutorizeSuccess=false;
                    if($d->num_rows>0){
                        $r=$d->fetch_assoc();
                        $_SESSION["idLogin"]=$r["id"];
                        $_SESSION["FIOLogin"]=$r["FIO"];
                        //Авторизация для Ent Core
                        $_SESSION["AutorizeFIO"]=$r["FIO"];
                        $_SESSION["AutorizeLogin"]=$Login;
                        $_SESSION["AutorizeType"]=$r["Type"];
                        $flagAutorizeSuccess=true;
                    };
                    switch ($flagAutorizeSuccess){
                        case true:
                            include "MVCPage.php";
                            break;
                        case false:
                            include "login.html";
                            break;
                    };
                    break;
                case false:
                    include "login.html";
                    break;
            }

            break;
        case true:
            include "MVCPage.php";
            break;
    }
?>