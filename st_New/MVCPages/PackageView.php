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
<script src="scripts/PackageView.js"></script>
<section class="content-header">
    <h1>
        Комплектование - Просмотр
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <input type="hidden" id="idPackage" value="<?php echo $idPackage; ?>">
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
                        <?php
                            //Сформируем массив отгрженного материала
                            $d=$con->query("SELECT pg.idGood, g.GoodName, pg.CountShpt, DATE_FORMAT(pg.DateShpt,'%d.%m.%Y') AS DateShpt, l.Login FROM stn_packagegoods pg, stn_Goods g, Logins l WHERE pg.idPackage=$idPackage AND g.id=pg.idGood AND pg.idLogin=l.id ORDER BY pg.DateShpt, g.GoodName");
                            $DateShpt=""; $idGood=-1;
                            $PackageGoods=array();
                            while($r=$d->fetch_assoc())
                                $PackageGoods[] = array(
                                    "DateShpt" => $r["DateShpt"],
                                    "Login"=>$r["Login"],
                                    "idGood" => $r["idGood"],
                                    "GoodName" => $r["GoodName"],
                                    "CountShpt" => (float)$r["CountShpt"]
                                );
                                /*
                                switch($r["DateShpt"]==$DateShpt) {
                                    case true:
                                        switch ((int)$r["idGood"] == $idGood) {
                                            case true:
                                                $PackageGoods[count($PackageGoods) - 1]["CountShpt"] += (float)$r["CountShpt"];
                                                break;
                                            case false:
                                                $PackageGoods[] = array(
                                                    "DateShpt" => $r["DateShpt"],
                                                    "idGood" => $r["idGood"],
                                                    "GoodName" => $r["GoodName"],
                                                    "CountShpt" => (float)$r["CountShpt"]
                                                );
                                                $idGood = (int)$r["idGood"];
                                                break;
                                        }
                                        break;
                                    case false:
                                        $PackageGoods[] = array(
                                            "DateShpt" => $r["DateShpt"],
                                            "idGood" => $r["idGood"],
                                            "GoodName" => $r["GoodName"],
                                            "CountShpt" => (float)$r["CountShpt"]
                                        );
                                        $DateShpt = $r["DateShpt"];
                                        $idGood = (int)$r["idGood"];
                                        break;
                                };*/

                            //Создадим список групп которые без стадии списания
                            $d=$con->query("SELECT id AS idGroup FROM stn_GoodGroups WHERE Step=0");
                            $idGroups="-1";
                            while($r=$d->fetch_assoc())
                                $idGroups=$idGroups.", ".$r["idGroup"];

                            //Создадим запрос номенклатуры, которая требуется для спи сания по спецификации
                            $d=$con->query("SELECT tMain.*, st.CountStock FROM
	(SELECT SUM(o.Count*pd.CountShpt) AS Count, sg.idGood, g.GoodName FROM stn_SpeOrders o, stn_SpeOrderGoods sg, stn_Goods g, stn_PackageDoors pd WHERE pd.idPackage=$idPackage AND g.idGroup IN($idGroups) AND pd.idDoor=o.idDoor AND o.id=sg.idDoorGroup AND sg.idGood=g.id GROUP BY g.id) tMain
LEFT JOIN stn_Stock st
ON tMain.idGood=st.idGood");
                            $SpeGoods=array();
                            while ($r=$d->fetch_assoc())
                                $SpeGoods[]=array(
                                    "idGood"=>$r["idGood"],
                                    "GoodName"=>$r["GoodName"],
                                    "CountStock"=>$r["CountStock"],
                                    "Count"=>(float)$r["Count"]
                                );
                            //Произведем сравнение и если требуется создадим таблицу на списание
                            foreach ($SpeGoods as &$sg)
                                foreach ($PackageGoods as $pg)
                                    if ($sg["idGood"] == $pg["idGood"])
                                        $sg["Count"] -= $pg["CountShpt"];
                            unset($sg); unset($pg);
                            //Проверим на наличие не до отгруженной продукции
                            $flag=false;
                            foreach ($SpeGoods as $sg)
                                if($sg["Count"]>0) $flag=true;
                            unset($sg);
                            if($flag){?>
                                <div class="panel panel-warning panel-default">
                                    <div class="panel-heading">
                                        Осталось отгрузить
                                        <button onclick="AfterSave()" class="btn btn-sm btn-primary">Отгрузить</button>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-hover table-responsive dataTable">
                                            <thead>
                                                <tr>
                                                    <th>Номенклатура</th>
                                                    <th>На складе</th>
                                                    <th>Требуется списать</th>
                                                    <th style="width: 150px;">Списать</th>
                                                </tr>
                                            </thead>
                                            <tbody id="ShptAfterTable">
                                            <?php
                                            foreach ($SpeGoods as $sg){?>
                                                <tr idGood="<?php echo $sg["idGood"]; ?>">
                                                    <td><?php echo $sg["GoodName"]; ?></td>
                                                    <td CountStock><?php echo $sg["CountStock"]; ?></td>
                                                    <td><?php echo $sg["Count"]; ?></td>
                                                    <td CountShpt><input class="form-control" value=""></td>
                                                </tr>
                                            <?php };
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php }
                        ?>
                        <table class="table table-hover table-responsive dataTable">
                            <thead>
                                <tr>
                                    <td>Дата отгрузки</td>
                                    <td>Отгрузил</td>
                                    <td>Номенклатура</td>
                                    <td>Отгруженно</td>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                foreach ($PackageGoods as $pg){?>
                                    <tr idGood="<?php echo $pg["idGood"]; ?>">
                                        <td><?php echo $pg["DateShpt"]; ?></td>
                                        <td><?php echo $pg["Login"]; ?></td>
                                        <td><?php echo $pg["GoodName"]; ?></td>
                                        <td><?php echo $pg["CountShpt"]; ?></td>
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