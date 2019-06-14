<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="js/moment-with-locales.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
<script src="js/fuelux.min.js"></script>
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/moment-with-locales.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
<script src="js/jquery.json.min.js"></script>
<script src="js/jquery.treegrid.min.js"></script>
<script src="js/jquery.treegrid.bootstrap3.js"></script>
<script src="GlobalFunctions.js"></script>

<div class="panel panel-info">
    <div class="panel-body">
        <?php
        $MVCPage="PageAnalyse/index.php";
        switch($MVCPageName){
            case"PageAnalyse": $MVCPage="PageAnalyse/index.php"; break;
            case"PageGoods": $MVCPage="PageGoods/index.php"; break;
            case"PageSupplier": $MVCPage="PageSupplier/index.php"; break;

            case"PageArrival": $MVCPage="PageArrival/index.php"; break;
            case"PageIssueWorker": $MVCPage="PageIssueWorker/index.php"; break;
            case"PageShpt": $MVCPage="PageShpt/index.php"; break;

            case"PageStockMain": $MVCPage="PageStockMain/index.php"; break;
            case"PageInEnt": $MVCPage="PageInEnt/index.php"; break;
        };
        include $MVCPage;
        ?>
    </div>
</div>


