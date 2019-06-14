<?php
    $idPackage=-1;
    $TTNNum="";
    $OrgName="";
    $Adress="";
    $Status=1;
    $Doors=array();
    if(isset($_GET["idPackage"])){
        $idPackage=$_GET["idPackage"];
        $xml=simplexml_load_file("Param.xml");
        $con=new mysqli($xml->DBConnect->Host,$xml->DBConnect->User,$xml->DBConnect->Pass,$xml->DBConnect->DBName);

        $d=$con->query("SELECT * FROM stn_Package WHERE id=$idPackage");
        $r=$d->fetch_assoc();
        $TTNNum=$r["TTNNum"];
        $OrgName=$r["OrgName"];
        $Adress=$r["Adress"];
        $Status=(int)$r["Status"];


        $d=$con->query("SELECT o.id AS idOrder, o.Blank, o.Shet, od.id AS idDoor, od.NumPP, od.Count AS CountDoor, od.name, CONCAT(od.H,' x ',od.W,IF(od.S IS NOT NULL, ' x '+od.S, IF(od.SEqual IS NOT NULL, ' x равн.',''))) AS Size, od.Open, p.id AS idPackageDoor, p.CountShpt FROM stn_PackageDoors p, OrderDoors od, Oreders o WHERE p.idDoor=od.id AND o.id=od.idOrder AND p.idPackage=$idPackage ORDER BY p.id");
        $idDoors="-1";
        $idPackageDoors="-1";
        if($d)
            while($r=$d->fetch_assoc()) {
                $Doors[] = array(
                    "idOrder"=>$r["idOrder"],
                    "idDoor"=>$r["idDoor"],
                    "idPackageDoor"=>$r["idPackageDoor"],
                    "Blank" => $r["Blank"],
                    "Shet" => $r["Shet"],
                    "NumPP"=>$r["NumPP"],
                    "Name" => $r["name"],
                    "Size" => $r["Size"],
                    "Open" => $r["Open"],
                    "CountShptMax" => (int)$r["CountDoor"],
                    "CountShpt" => $r["CountShpt"]
                );
                $idDoors=$idDoors.", ".$r["idDoor"];
                $idPackageDoors=$idPackageDoors.", ".$r["idPackageDoor"];
            };
        $d=$con->query("SELECT idDoor, SUM(CountShpt) AS CountShptOld FROM stn_PackageDoors WHERE idDoor IN ($idDoors) AND id NOT IN (".$idPackageDoors.") GROUP BY idDoor");
        if($d)
            while ($r=$d->fetch_assoc())
                foreach ($Doors as &$Door)
                    if($Door["idDoor"]==$r["idDoor"])
                        $Door["CountShptMax"]=$Door["CountShptMax"]-(int)$r["CountShptOld"];

    };
?>
<script src="scripts/PackageStart.js"></script>
<section class="content-header">
    <h1>
        Комплектование - выбор списка дверей
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <button onclick="Save('Save')" <?php echo $Status==1 ? "" : "disabled"; ?> class="btn btn-primary">Сохранить</button>
            <button onclick="window.location.href='?MVCPage=PackageList'" class="btn btn-danger">Отмена</button>
            <button onclick="Save('Accept')" <?php echo $Status==1 ? "" : "disabled"; ?> class="btn btn-success">Отправить на комплектование</button>
            <div class="form-inline" style="margin-bottom: 5px; margin-top: 5px;">
                <input type="hidden" id="idPackage" value="<?php echo $idPackage; ?>">
                <div class="form-group">
                    <label for="Order_TTNNum">ТТН</label>
                    <input id="Order_TTNNum" value="<?php echo $TTNNum; ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label for="Order_OrgName">Организация</label>
                    <input id="Order_OrgName" value="<?php echo $OrgName; ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label for="Order_Adress">Адрес</label>
                    <input id="Order_Adress" value="<?php echo $Adress; ?>" class="form-control">
                </div>
            </div>
            <div class="form-inline">
                <div class="form-group">
                    <button onclick="OrderDialog.Open()" <?php echo $Status==1 ? "" : "disabled"; ?> class="btn btn-primary">Добавить дверь</button>
                </div>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-responsive table-hover">
                <thead>
                    <tr>
                        <th>Заказ</th>
                        <th>Счет</th>
                        <th>№ п/п</th>
                        <th>Наименование</th>
                        <th>Размеры</th>
                        <th>Открывание</th>
                        <th>Дверей на отгрузку</th>
                    </tr>
                </thead>
                <tbody id="OrderTable">
                <?php
                    foreach ($Doors as $d){ ?>
                        <tr idOrder='<?php echo $d["idOrder"]; ?>' idDoor='<?php echo $d["idDoor"]; ?>' idPackageDoor='<?php echo $d["idPackageDoor"]; ?>' Status='Load'>
                            <td><?php echo $d["Blank"]; ?></td>
                            <td><?php echo $d["Shet"]; ?></td>
                            <td><?php echo $d["NumPP"]; ?></td>
                            <td><?php echo $d["Name"]; ?></td>
                            <td><?php echo $d["Size"]; ?></td>
                            <td><?php echo $d["Open"]; ?></td>
                            <td CountShptMax='<?php echo $d["CountShptMax"]; ?>'>
                                <input oninput='OrderTable.Edit(this)' value='<?php  echo $d["CountShpt"]; ?>' class='form-control'>
                            </td>
                            <td>
                                <span onclick='OrderTable.Remove(this)' class='glyphicon glyphicon-remove'></span>
                            </td>
                        </tr>
                    <?php }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Диалог Выбора счета -->
<div id="OrderDialog" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Выбор счета</h4>
            </div>
            <div class="modal-body">
                <p>
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-xs-3 control-label">Номер двери:</label>
                        <div class="col-xs-4">
                            <input id="OrderDialog_FindShet" placeholder="Введите номер счета" class="form-control danger" aria-describedby="basic-addon2">
                        </div>
                        <div class="col-xs-3">
                            <select id="OrderDialog_Years" class="form-control"></select>
                        </div>
                        <button onclick="OrderDialog.Select()" class="col-xs-1 btn btn-primary">
                            <span class="glyphicon glyphicon-triangle-bottom"></span>
                        </button>
                    </div>
                </div>
                <table class="table table-responsive table-hover">
                    <thead>
                        <tr>
                            <th style="width:20px;"></th>
                            <th>№ заказа</th>
                            <th>Дата</th>
                            <th>Счет</th>
                            <th>Заказчик</th>
                            <th colspan="2">Статус</th>
                            <th style="width: 30px;">На отгрузку</th>
                        </tr>
                    </thead>
                    <tbody id="OrderDialog_Table"></tbody>
                </table>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button onclick="OrderDialog.Close()" type="button" class="btn btn-primary">Добавить</button>
            </div>
        </div>
    </div>
</div>