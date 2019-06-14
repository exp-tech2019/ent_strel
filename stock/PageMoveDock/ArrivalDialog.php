<script src="PageMoveDock/ArrivalDialog.js"></script>
<?php
    $idArrival=null;
    $Date=date("d.m.y");
    $TTN="";
    $idLogin=$_SESSION["AuthorizeID"];
    $Manager=$_SESSION["AuthorizeFIO"];
    $idSupplier="";
    $SupplierName="";
    $Status=0;
    $GoodsArr=array();
    if(isset($_GET["idArrival"])){

    };
?>
<div class="panel panel-default">
    <div class="panel-heading">
        Поступление товара
    </div>
    <div class="panel-body  ">
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon1" style="min-width:200px; text-align: left;">№ акта</span>
            <input type="text" class="form-control" placeholder="№ акта" aria-describedby="basic-addon1" value="<?php echo GenerateNumAct($idArrival); ?>" disabled>
        </div>
    </div>
    <div class="panel-body  ">
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon1" style="min-width:200px; text-align: left;">Дата</span>
            <input type="text" class="form-control" placeholder="Дата" aria-describedby="basic-addon1" value="<?php echo $Date; ?>" >
        </div>
    </div>
    <div class="panel-body  ">
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon1" style="min-width:200px; text-align: left;">№ ТТН</span>
            <input type="text" class="form-control" placeholder="№ ТТН" aria-describedby="basic-addon1" value="<?php echo $TTN; ?>" >
        </div>
    </div>
    <div class="panel-body">
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon1" style="min-width:200px; text-align: left;">Ответственный</span>
            <input type="hidden" value="<?php echo $idLogin; ?>">
            <input type="text" class="form-control" placeholder="Ответственный" aria-describedby="basic-addon1" value="<?php echo $Manager; ?>" >
        </div>
    </div>
    <div class="panel-body">
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon1" style="min-width:200px; text-align: left;">Поставщик</span>
            <input type="hidden" value="<?php echo $idSupplier; ?>">
            <input type="text" onclick="OpenManualSupplier()" class="form-control" placeholder="Поставщик" aria-describedby="basic-addon1" value="<?php echo $SupplierName; ?>" >
        </div>
    </div>
</div>

<?php include "PageMoveDock/ManualSupplier.php"; ?>

<?php
    function GenerateNumAct($idAct){
        $Ret="";
        if($idAct!=null) {
            $Ret = $idAct;
            while (strlen($Ret) < 6)
                $Ret = "0" . $Ret;
        };
        return $Ret;
    };
?>