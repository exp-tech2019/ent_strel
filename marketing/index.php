<?php
    session_start();
    include "params.php";
    $m=new mysqli($GlobalDBHost,$GlobalDBLogin,$GlobalDBPass,$GlobalDBName);
    include "GlobalManuals.php";
    include "GetCurrecnyCBR.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap 101 Template</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/fuelux.min.css">
    <link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css" />
    <link href="css/MyStyle.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<?php

    if(isset($_SESSION["AuthorizeID"]))
    {
        include "mvc.php";
    }
    else {
        $AuthorizeFlag="Login";
        if (isset($_POST["AuthorizeLogin"])) {
            $Login=$_POST["AuthorizeLogin"];
            $Pass=$_POST["AuthorizePass"];
            $d=$m->query("SELECT * FROM Logins WHERE Login='$Login' AND Pass='$Pass'");
            if($d->num_rows>0)
            {
                $r=$d->fetch_assoc();
                $_SESSION["AuthorizeID"]=$r["id"];
                $_SESSION["AuthorizeFIO"]=$r["FIO"];
                $AuthorizeFlag="OK";
            };
        };
        if (!isset($_POST["AuthorizeLogin"])) $AuthorizeFlag="Login";
        switch ($AuthorizeFlag)
        {
            case "Login": include "login.php"; break;
            case "OK": include "mvc.php"; break;
        };

    };
?>
</body>
</html>

