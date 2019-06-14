<?php

?>
<div class="panel panel-default">
    <div class="panel-body">
        <button class="btn btn-primary" onclick="AddGroup()">Добавить группу</button>
    </div>
</div>

<?php
    $dGroup=$m->query("SELECT * FROM stocknewmanualgroups ORDER BY Name");
    if($dGroup->num_rows>0)
    while($rGroup=$dGroup->fetch_assoc()){
        $idGroup=$rGroup["id"];
?>
        <div class="panel panel-default">
            <div class="panel panel-heading" idGroup="<?php echo $rGroup["id"]; ?>" Step="<?php echo $rGroup["Step"]; ?>">
                <span Type="GoodName"><?php echo $rGroup["Name"]; ?></span>
                &nbsp;&nbsp;
                <button onclick="AddGood(this)" class="btn btn-default">Добавить товар</button>
                <?php if($rGroup["NotRemove"]==null) { ?>
                    <span onclick="RemoveGroup(this)" class="glyphicon glyphicon-remove" style="float: right; padding-left: 15px; font-size: 18px;"></span>
                    &nbsp;&nbsp;
                    <span onclick="GroupEditStart(this)" class="glyphicon glyphicon-pencil" style="float: right; font-size: 18px; cursor: pointer"></span>
                <?php }; ?>
            </div>
            <div class="panel-body">
                <table class="table table-responsive">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Наименование</th>
                            <th>Ед. измер.</th>
                        </tr>
                    </thead>
                    <tbody id="GoodTable">
                        <?php
                            $dGood=$m->query("SELECT * FROM StockNewManualGood WHERE idGroup=$idGroup ORDER BY Name");
                            if($dGood->num_rows>0)
                                while($rGood=$dGood->fetch_assoc()){ ?>
                                    <tr idGood="<?php  echo $rGood["id"]; ?>" idGroup="<?php  echo $rGood["idGroup"]; ?>">
                                        <td>
                                            <span onclick="RemoveGood(this)" class="glyphicon glyphicon-remove" style="padding-right: 15px; font-size: 18px; cursor: pointer;"></span>
                                            <span onclick="EditStartGood(this)" class="glyphicon glyphicon-pencil" style="font-size: 19px; cursor: pointer;"></span>
                                        </td>
                                        <td Type="Name"><?php echo $rGood["Name"]; ?></td>
                                        <td Type="Unit" UnitNum="<?php echo $rGood["Unit"]; ?>"><?php echo UnitToString($rGood["Unit"]); ?></td>
                                    </tr>
                            <?php };
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
<?php
    };
?>


<?php
    include "PageManualGoods/AddGroupDialog.php";
    include "PageManualGoods/AddGoodDialog.php";

    function UnitToString($Unit){
        $ret="";
        switch ($Unit){
            case 1: $ret="шт"; break;
            case 2: $ret="кг"; break;
            case 3: $ret="М<sup>2</sup>"; break;
        };
        return $ret;
    }
?>