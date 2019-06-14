<div class="panel panel-default">
    <div class="panel-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-2">
                    <div class="input-group input-append dropdown combobox" data-initialize="combobox" id="FilterStatus" >
                        <input type="text" class="form-control" placeholder="Статус" value="Все">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li data-value='-1'><a href='#'>Все</a></li>
                                <li data-value='0'><a href='#'>Первичный</a></li>
                                <li data-value='1'><a href='#'>В производстве</a></li>
                                <li data-value='2'><a href='#'>Выполнен</a></li>
                                <li data-value='3'><a href='#'>Отмененный</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xs-3">
                    <div class="input-group input-append dropdown combobox" data-initialize="combobox" id="FilterManager" >
                        <input type="text" class="form-control" placeholder="Менеджер">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <?php
                                    $d=$m->query("SELECT * FROM Logins ORDER BY FIO");
                                    if($d->num_rows>0)
                                        while ($r=$d->fetch_assoc()){
                                            echo "<li data-value='".$r["id"]."'><a href='#'>".$r["FIO"]."</a></li>";
                                        };
                                    $d->close();
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xs-2">
                    <input placeholder="Контрагент" class="form-control" id="FilterCustomer">
                </div>
                <div class="col-xs-1">
                    <input placeholder="Счет" class="form-control" id="FilterShet">
                </div>
            </div>
        </div>
    </div>
</div>


<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>№</th>
            <th>Статус</th>
            <th>Дата создания</th>
            <th>Счет</th>
            <th>Дата</th>
            <th>Контрагент</th>
            <th>Менеджер</th>
            <th>Дверей</th>
            <th>Стоимость дверей</th>
            <th>Оплатаё</th>
            <td></td>
        </tr>
    </thead>
    <tbody id="OrderList">
<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 08.12.2016
 * Time: 18:42
 */
    //$d=$m->query("SELECT o.*, c.Name AS CustomerName, DATE_FORMAT(o.DateCreate , '%d.%m.%Y') AS DateCreateS, DATE_FORMAT(o.ShetDate, '%d.%m.%Y') AS ShetDateS, SUM(od.Count) AS DoorCount, l.FIO AS Manager FROM TempOrders o, Logins l, temporderdoors od, Customers c WHERE o.idManager=l.id AND o.id=od.idOrder AND o.idCustomer=c.id GROUP BY o.id ORDER BY o.DateCreate");
    $d=$m->query("CALL SelectTempOrders()");
    if($d->num_rows>0)
        while($r=$d->fetch_assoc())
        {
            $NumPP=NumPPGenerate($r["id"]);
            $Status=$r["Status"];
            $StatusText="";
            switch ($Status){
                case 0: $StatusText="Новый"; break;
                case 1: $StatusText="В производстве"; break;
                case 2: $StatusText="Выполнен"; break;
                case -1: $StatusText="Отменен"; break;
            }
            $DateCreate=$r["DateCreateS"];
            $Shet=$r["Shet"];
            $ShetDate=$r["ShetDateS"];
            $Customer=$r["Customer"];
            $Manager=$r["Manager"];
            $DoorCount=$r["DoorCount"];
            $SumCalc=$r["SumCalc"];
            $PaymentSum=$r["PaymentSum"];
            ?>
            <tr onclick="window.location='index.php?MVCPage=OrderEdit&idOrder=<?php echo $r["id"]; ?>'" OrderID="<?php echo $r["id"]; ?>" OrderStatus="<?php echo $r["Status"]; ?>">
                <td Type="NumPP"><?php echo $NumPP; ?></td>
                <td Type="Status"><?php echo $StatusText; ?></td>
                <td Type="DateCreate"><?php echo $DateCreate; ?></td>
                <td Type="Shet"><?php echo $Shet; ?></td>
                <td Type="ShetDate"><?php echo $ShetDate; ?></td>
                <td Type="Customer"><?php echo $Customer; ?></td>
                <td Type="Manager"><?php echo $Manager; ?></td>
                <td Type="DoorCount"><?php echo $DoorCount; ?></td>
                <td Type="DoorSum"><?php echo $SumCalc; ?></td>
                <td Type="DoorSum"><?php echo $PaymentSum; ?></td>
                <td>
                    <button type="button" class="btn btn-default btn-md">
                        <span class="glyphicon glyphicon-play"></span> В производство
                    </button>
                </td>
            </tr>
            <?php
        };

    function NumPPGenerate($id){
        while(strlen($id)<6)
            $id="0".$id;
        return $id;
    }
?>
    </tbody>
</table>

<script>
    $(document).ready(function(){
        $("#FilterStatus div ul li").click(function(){
            FilterStatusValue=$(this).attr("data-value");
            FilterSelect();
        });
        $("#FilterManager div ul li").click(function(){
            FilterManagerValue=$(this).text();
            FilterSelect();
        });
        $("#FilterCustomer").keyup(function(){
            FilterSelect();
        });
        $("#FilterShet").keyup(function(){
            FilterSelect();
        });
    });
    var FilterManagerValue="";
    var FilterStatusValue=-1;
    function FilterSelect(){
        var Status="";
        for(var i=0;i<$("#OrderList tr").length;i++)
        {
            var tr=$("#OrderList tr:eq("+i+")");
            var FlagShow=true;
            if(FilterStatusValue!=-1 & tr.attr("OrderStatus")!=FilterStatusValue) FlagShow=false;
            if(FilterManagerValue!="" & tr.find("td[Type=Manager]").text()!=FilterManagerValue) FlagShow=false;
            if($("#FilterCustomer").val()!="" & tr.find("td[Type=Customer]").text().toLowerCase().indexOf($("#FilterCustomer").val().toLowerCase())==-1) FlagShow=false;
            if($("#FilterShet").val()!="" & tr.find("td[Type=Shet]").text().toLowerCase().indexOf($("#FilterShet").val().toLowerCase())==-1) FlagShow=false;
            switch (FlagShow){
                case false: tr.hide(); break;
                case true: tr.show(); break;
            };
        }
    }
</script>