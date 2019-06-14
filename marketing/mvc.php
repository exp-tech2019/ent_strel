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
            <a href="../" class="navbar-brand">Ent Маркетинг</a>
        </div>
        <nav class="collapse navbar-collapse" id="bs-navbar">
            <ul class="nav navbar-nav">
                <li> <a href="index.php?MVCPage=OrderList">Заказы</a> </li>
                <li class="active"> <a href="index.php?MVCPage=Import">Импорт</a> </li>
                <li> <a href="index.php?MVCPage=Customers">Заказчики</a> </li>
                <li> <a href="index.php?MVCPage=Settings">Настройки</a> </li>
            </ul>
        </nav>
    </div>
</header>

<div class="panel panel-info">
    <!--
    <div class="panel-heading">
        <h3>
            Маркетинг
            <span class="label label-primary"><a href="index.php?MVCPage=OrderList">Заказы</a></span>
            <span class="label label-primary"><a href="index.php?MVCPage=Import">Импорт</a></span>
            <span class="label label-primary"><a href="index.php?MVCPage=Customers">Заказчики</a></span>
            <span class="label label-primary"><a href="index.php?MVCPage=Settings">Настройки</a></span>
        </h3>
    </div>
    -->
    <div class="panel-body">
        <?php
            $MVCPage="import/index.php";
            if(isset($_GET["MVCPage"]))
                switch($_GET["MVCPage"]){
                    case"Import": $MVCPage="import/index.php"; break;
                    case"OrderList": $MVCPage="OrderList/index.php"; break;
                    case"OrderEdit": $MVCPage="OrderEdit/index.php"; break;

                    case"Customers": $MVCPage="Customers/index.php"; break;
                    case"CustomerAddEdit": $MVCPage="Customers/CustomerAddEdit.php"; break;
                    case"CustomerRemove": $MVCPage="Customers/Remove.php"; break;

                    case"Settings": $MVCPage="Settings/index.php"; break;
                    case "SettingCalc": $MVCPage="Settings/CalcView.php"; break;
                };
            if(isset($_POST["MVC"]))
                switch ($_POST["MVC"])
                {
                    case "ImportExcel": $MVCPage="Import/ImportExcel.php"; break;
                    case "ImportExcelSave": $MVCPage="Import/ImportExcelSave.php"; break;

                    case "CustomerSave": $MVCPage="Customers/Save.php"; break;
                };
            include $MVCPage;
        ?>
    </div>
</div>


