<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 25.04.2019
 * Time: 22:32
 */
$Find=isset($_GET["Find"]) ? $_GET["Find"] : "";
$d=$m->query("SELECT * FROM ActShptList WHERE ".(isset($_GET["Status"]) ? "Status=".$_GET["Status"] : "Status<>0")." ".($Find!="" ? "AND (ActNum LIKE '%$Find%' OR OrgName LIKE '%$Find%' OR Shet LIKE '%$Find%')" : "")." ORDER BY ActDate DESC");
echo "SELECT * FROM ActShptList WHERE ".(isset($_GET["Status"]) ? "Status=".$_GET["Status"] : "Status<>0")." ".($Find!="" ? "AND (ActNum LIKE '%$Find%' OR OrgName LIKE '%$Find%' OR Shet LIKE '%$Find%')" : "")." ORDER BY ActDate DESC";
?>
<div class="panel">
    <div class="input-group margin" style="display: inline-block; width: 250px;">
        <input type="text" id="ActList_find" class="form-control input-sm" style="width: 200px; display: inline-block" placeholder="Поиск">
        <span onclick="window.location='index.php?PageLoad=ActList&Find='+$('#ActList_find').val()" type="button" class="btn btn-sm btn-default">
            <span class="fa fa-search"></span>
        </span>
    </div>
</div>
<table class="table table-hover table-responsive">
    <thead>
    <tr>
        <th></th>
        <th>№ акта</th>
        <th>Дата создания</th>
        <th>Дата отгрузки</th>
        <th>Заказчик</th>
        <th>Счета</th>
        <th>Дверей</th>
    </tr>
    </thead>
    <tbody>
    <?php
    while ($r=$d->fetch_assoc()){
        $class=$r["Status"]==1 ? "" : ($r["Status"]==2 ? "success" : "default");

        ?>
        <tr idAct="<?php echo $r["idAct"]; ?>" class="<?php echo $class; ?>">
            <td>
                <!--
                <div class="input-group-btn open">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <span class="fa fa-gears"></span>
                        <span class="fa fa-caret-down"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li class="divider"></li>
                        <li><a href="#">Separated link</a></li>
                    </ul>
                </div>
                -->
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <span class="fa fa-gears"></span>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="index.php?PageLoad=ActAdd&idAct=<?php echo $r["idAct"]; ?>">Редактировать</a></li>
                    </ul>
                </div>
            </td>
            <td><?php echo $r["ActNum"]; ?></td>
            <td><?php echo $r["ActDate"]; ?></td>
            <td><?php echo $r["ShptDate"]; ?></td>
            <td><?php echo $r["OrgName"]; ?></td>
            <td><?php echo $r["Shet"]; ?></td>
            <td><?php echo $r["DoorCount"]; ?></td>
        </tr>
    <?php }
    ?>
    </tbody>
</table>
