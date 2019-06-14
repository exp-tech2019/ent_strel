<?php
    $idPackage=$_GET["idPackage"];
    $xml=simplexml_load_file("Param.xml");
    $con=new mysqli($xml->DBConnect->Host,$xml->DBConnect->User,$xml->DBConnect->Pass,$xml->DBConnect->DBName);

    $TTNNum="";
    $OrgName="";
    $Adress="";
    $d=$con->query("SELECT * FROM stn_Package WHERE id=$idPackage");
    $r=$d->fetch_assoc();
    $TTNNum=$r["TTNNum"];
    $OrgName=$r["OrgName"];
    $Adress=$r["Adress"];
?>
<script src="scripts/PackageEnd.js"></script>
<section class="content-header">
    <h1>
        Комплектование - отгрузка
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <input type="hidden" id="idPackage" value="<?php echo $_GET["idPackage"]; ?>">
            <div class="panel panel-default">
                <div class="panel-body">
                    <button onclick="Save()" class="btn btn-success">Отгрузить</button>
                    <button class="btn btn-danger">Отмена</button>
                </div>
            </div>
            <div class="row" style="margin: 10px;">
                <div class="col-xs-3">
                    <div class="row">
                        <div class="col-xs-1"><b>ТТН</b></div>
                        <div class="col-xs-2"><?php echo $TTNNum; ?></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-1"><b>Организация</b></div>
                        <div class="col-xs-2"><?php echo $OrgName; ?></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-1"><b>Адрес</b></div>
                        <div class="col-xs-2"><?php echo $Adress; ?></div>
                    </div>
                </div>
                <div class="col-xs-2">
                    <!--<button class="btn btn-primary">Загрузить список номенклатуры</button>-->
                </div>
            </div>

            <div>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#TabGoods" aria-controls="home" role="tab" data-toggle="tab">Номенклатура</a></li>
                    <li role="presentation"><a href="#TabDoors" aria-controls="profile" role="tab" data-toggle="tab">Двери</a></li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="TabGoods">
                        <table class="table table-responsive table-hover dataTable">
                            <thead>
                            <tr>
                                <th>Номенклатура</th>
                                <th>Кол-во на складе</th>
                                <th>Требуется списать</th>
                                <th style="width: 100px;">Списать</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody id="GoodsTable">
                            <?php
                            //Создадим список групп которые без стадии списания
                            $d=$con->query("SELECT id AS idGroup FROM stn_GoodGroups WHERE Step=0");
                            $idGroups="-1";
                            while($r=$d->fetch_assoc())
                                $idGroups=$idGroups.", ".$r["idGroup"];

                            //На основании спецификации выведем список номенклатуры, подлжежащей псисанию
                            $d=$con->query("SELECT tMain.*, st.CountStock FROM
	(SELECT SUM(o.Count*pd.CountShpt) AS Count, sg.idGood, g.GoodName FROM stn_SpeOrders o, stn_SpeOrderGoods sg, stn_Goods g, stn_PackageDoors pd WHERE pd.idPackage=$idPackage AND g.idGroup IN($idGroups) AND pd.idDoor=o.idDoor AND o.id=sg.idDoorGroup AND sg.idGood=g.id GROUP BY g.id) tMain
LEFT JOIN stn_Stock st
ON tMain.idGood=st.idGood");
                            while ($r=$d->fetch_assoc()){?>
                                <tr idGood="<?php echo $r["idGood"]; ?>">
                                    <td><?php echo $r["GoodName"]; ?></td>
                                    <td Type="CountStock"><?php echo $r["CountStock"]; ?></td>
                                    <td Type="Count"><?php echo $r["Count"]; ?></td>
                                    <td Type="CountShpt"><input value="<?php echo $r["Count"]; ?>" class="form-control"></td>
                                    <td Type="NoShpt"><input type="checkbox"> Не отгружать</td>
                                </tr>
                            <?php };
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="TabDoors">
                        <table class="table table-responsive table-hover dataTable">
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
                            <tbody>
                            <?php
                                $d=$con->query("SELECT o.Blank, DATE_FORMAT(o.BlankDate,'%d.%m.%Y') AS BlankDate, o.Shet, od.NumPP, od.name, CONCAT(od.H,' x ',od.W,IF(od.S IS NOT NULL, ' x '+od.S, IF(od.SEqual IS NOT NULL, ' x равн.',''))) AS Size, od.Open, pd.CountShpt FROM stn_PackageDoors pd, OrderDoors od, Oreders o WHERE pd.idPackage=$idPackage AND pd.idDoor=od.id AND od.idOrder=o.id");
                                while($r=$d->fetch_assoc()){?>
                                    <tr>
                                        <td><?php echo $r["Blank"]; ?></td>
                                        <td><?php echo $r["Shet"]; ?></td>
                                        <td><?php echo $r["NumPP"]; ?></td>
                                        <td><?php echo $r["name"]; ?></td>
                                        <td><?php echo $r["Size"]; ?></td>
                                        <td><?php echo $r["Open"]; ?></td>
                                        <td Type="CountShpt"><?php echo $r["CountShpt"]; ?></td>
                                    </tr>
                                <?php };
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>
    </div>
</section>