<?php
    $TTNNum=isset($_GET["TTNNum"]) ? $_GET["TTNNum"] : "";
    $OrgName=isset($_GET["OrgName"]) ? $_GET["OrgName"] : "";
    $Adress=isset($_GET["Adress"]) ? $_GET["Adress"] : "";
    $Shet=isset($_GET["Shet"]) ? $_GET["Shet"] : "";
?>
<script src="scripts/TransferInStock_EditStart.js"></script>
<section class="content-header">
    <h1>
        Комплектование
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <button onclick="window.location.href='?MVCPage=PackageStart'" class="btn btn-primary">Создать</button>
            <div class="form-inline">
                <div class="form-group">
                    <label for="FindTTNNum">ТТН</label>
                    <input id="FindTTNNum" class="form-control" value="<?php echo $TTNNum; ?>">
                </div>
                <div class="form-group">
                    <label for="FindOrgName">Организация</label>
                    <input id="FindOrgName" class="form-control" value="<?php echo $OrgName ?>">
                </div>
                <div class="form-group">
                    <label for="FindAdress">Адрес</label>
                    <input id="FindAdress" class="form-control" value="<?php echo $Adress ?>">
                </div>
                <div class="form-group">
                    <label for="FindShet">Счет</label>
                    <input id="FindShet" class="form-control" value="<?php echo $Shet ?>">
                </div>
                <button onclick="Find()" class="btn btn-primary">Поиск</button>
            </div>
        </div>
        <div class="box-body">

            <table class="table table-responsive table-hover dataTable dataTables_scrollBody">
                <thead>
                    <tr>
                        <th>Номер ТТН</th>
                        <th>Дата создания</th>
                        <th>Организация</th>
                        <th>Адрес</th>
                        <th style="width: 100px;">Статус</th>
                        <th style="width: 30px;"></th>
                    </tr>
                </thead>
                <tbody id="PackageTable">
                    <?php
                        $xml=simplexml_load_file("Param.xml");
                        $con=new mysqli($xml->DBConnect->Host,$xml->DBConnect->User,$xml->DBConnect->Pass,$xml->DBConnect->DBName);
                        $StatusArr=array("","Формируется", "Комплектуется", "Отгружен", "Частичная отгрузка");
                        $StatusColor=array("","info","warning","success","danger");
                        $PageView=isset($_GET["PageView"]) ? (int)$_GET["PageView"] : 1;
                        $ViewRow=$xml->Common->ViewRow;

                        $WHERE="WHERE 1=1";
                        $WHERE=$TTNNum!="" ? $WHERE=$WHERE." AND p.TTNNum LIKE '%".$_GET["TTNNum"]."%'" : $WHERE;
                        $WHERE=$OrgName!="" ? $WHERE=$WHERE." AND p.OrgName LIKE '%".$_GET["OrgName"]."%'" : $WHERE;
                        $WHERE=$Adress!="" ? $WHERE=$WHERE." AND p.Adress LIKE '%".$_GET["Adress"]."%'" : $WHERE;
                        $SQL="SELECT *, DATE_FORMAT(p.TTNDate,'%d.%m.%Y') AS TTNDateStr FROM stn_Package p $WHERE ORDER BY p.id DESC LIMIT ".(($PageView-1)*$ViewRow).",".$ViewRow;
                        $idDoors="-1";
                        if($Shet!="") {
                            $d = $con->query("SELECT od.id AS idDoor FROM Oreders o, OrderDoors od WHERE o.id=od.idOrder AND o.Shet LIKE '%$Shet%'");
                            while ($r = $d->fetch_assoc()) $idDoors = $idDoors . ", " . $r["idDoor"];
                            $SQL = "SELECT p.*, DATE_FORMAT(p.TTNDate,'%d.%m.%Y') AS TTNDateStr FROM stn_Package p, stn_PackageDoors pd $WHERE AND p.id=pd.idPackage AND pd.idDoor IN ($idDoors) GROUP BY p.id ORDER BY p.id DESC LIMIT " . (($PageView - 1) * $ViewRow) . "," . $ViewRow;
                        };
                        $d=$con->query($SQL);
                        while($r=$d->fetch_assoc()) {
                            ?>
                            <tr idPackage="<?php echo $r["id"] ?>" Status="<?php echo $r["Status"]; ?>" style="cursor: pointer;">
                                <td onclick="EditStart(this)"><?php echo $r["TTNNum"]; ?></td>
                                <td onclick="EditStart(this)"><?php echo $r["TTNDateStr"]; ?></td>
                                <td onclick="EditStart(this)"><?php echo $r["OrgName"]; ?></td>
                                <td onclick="EditStart(this)"><?php echo $r["Adress"]; ?></td>
                                <td onclick="EditStart(this)" class="<?php echo $StatusColor[$r["Status"]]; ?>"><?php echo $StatusArr[$r["Status"]]; ?></td>
                                <td>
                                    <?php if($r["Status"]==1) {?><span class="glyphicon glyphicon-remove"></span><?php }; ?>
                                </td>
                            </tr>
                            <?php
                        };
                    ?>
                </tbody>
            </table>
            <?php
                $SQL="SELECT COUNT(*) AS Count FROM stn_Package p $WHERE";
                if($Shet!="")
                    $SQL="SELECT COUNT(*) AS Count FROM stn_Package p, stn_PackageDoors pd $WHERE AND p.id=pd.idPackage AND pd.idDoor IN ($idDoors)  GROUP BY p.id";
                $d=$con->query($SQL);
                $r=$d->fetch_assoc();
                $CountRow=$r["Count"];
            ?>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li>
                        <a href="?MVCPage=PackageList&PageView=<?php echo $PageView-1!=0 ? $PageView-1 : 1; ?>&<?php echo "TTNNum=$TTNNum&OrgName=$OrgName&Adress=$Adress&Shet=$Shet"; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php
                        $PageCount=ceil($CountRow/$ViewRow);
                        for($i=1;$i<=$PageCount; $i++){ ?>
                            <li class="<?php echo $PageView==$i ? "active" : ""; ?>"><a href="?MVCPage=PackageList&PageView=<?php echo $i; ?>&<?php echo "TTNNum=$TTNNum&OrgName=$OrgName&Adress=$Adress&Shet=$Shet"; ?>"><?php echo $i; ?></a></li>
                        <?php }
                    ?>
                    <li>
                        <a href="?MVCPage=PackageList&PageView=<?php echo $PageView+1>$PageCount ? $PageView : $PageView+1 ?>&<?php echo "TTNNum=$TTNNum&OrgName=$OrgName&Adress=$Adress&Shet=$Shet"; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>

        </div>
    </div>
</section>

<script>
    function Find(){
        var TTNNum=$("#FindTTNNum").val();
        var OrgName=$("#FindOrgName").val();
        var Adress=$("#FindAdress").val();
        var Shet=$("#FindShet").val();
        window.location.href="?MVCPage=PackageList&PageView=1&TTNNum="+TTNNum+"&OrgName="+OrgName+"&Adress="+Adress+"&Shet="+Shet;
    }
    function EditStart(el){
        console.log($(el).parent().attr("Status"));
        switch (parseInt($(el).parent().attr("Status"))){
            case 1:
                window.location.href="?MVCPage=PackageStart&idPackage="+$(el).parent().attr("idPackage");
                break;
            case 2:
                window.location.href="?MVCPage=PackageEnd&idPackage="+$(el).parent().attr("idPackage");
                break;
            case 3: case 4:
                window.location.href="?MVCPage=PackageView&idPackage="+$(el).parent().attr("idPackage");
                break;
        }

    }
</script>