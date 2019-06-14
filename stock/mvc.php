<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="js/moment-with-locales.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
<script src="js/fuelux.min.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
<script src="js/jquery.json.min.js"></script>

<header class="bs-docs-nav navbar navbar-static-top" id="top">
    <div class="container">
        <div class="navbar-header">
            <button aria-controls="bs-navbar" aria-expanded="false" class="collapsed navbar-toggle" data-target="#bs-navbar" data-toggle="collapse" type="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span> <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="../" class="navbar-brand">Ent Склад</a>
        </div>
        <nav class="collapse navbar-collapse" id="bs-navbar">
            <ul class="nav navbar-nav">
                <li> <a href="index.php?MVCPage=PageMain">Главная</a> </li>
                <li class="active"> <a href="index.php?MVCPage=PageShet">Счета</a> </li>
                <li> <a href="index.php?MVCPage=PageMoveDock">Движения</a> </li>
                <li> <a href="index.php?MVCPage=PageStock">Скалд</a> </li>
                <li> <a href="index.php?MVCPage=PageManualGoods">Номенклатура</a> </li>
            </ul>
        </nav>
    </div>
</header>

<div class="panel panel-info">
    <div class="panel-body">
        <?php
            $MVCPage="PageMain/index.php";
            if(isset($_GET["MVCPage"]))
                switch($_GET["MVCPage"]){
                    case"PageMain": $MVCPage="PageMain/index.php"; break;
                    case"PageShet": $MVCPage="PageShet/index.php"; break;
                    case"PageMoveDock": $MVCPage="PageMoveDock/index.php"; break;
                    case"ArrivalDialog": $MVCPage="PageMoveDock/ArrivalDialog.php"; break;

                    case"PageStock": $MVCPage="PageStock/index.php"; break;
                    case"PageManualGoods": $MVCPage="PageManualGoods/index.php"; break;
                };
            include $MVCPage;
        ?>
    </div>
</div>


