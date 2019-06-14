<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="js/moment-with-locales.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
<script src="js/fuelux.min.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
<script src="js/jquery.json.min.js"></script>


<div class="panel panel-info">
    <div class="panel-body">
        <?php
            $MVCPage="PageMain/index.php";
            if(isset($_GET["MVCPage"]))
                switch($_GET["MVCPage"]){
                    case"PageMain": $MVCPage="PageMain/index.php"; break;
                    case"AddAct": $MVCPage="PageAct/AddAct.php"; break;
                    case"PageAct": $MVCPage="PageAct/index.php"; break;
                    case "PropertesNalog": $MVCPage="PropertesNalog/index.php"; break;
                };
            include $MVCPage;
        ?>
    </div>
</div>


