<script src="PageAct/index.js"></script>
<?php
    $idAct=isset($idAct) ? $idAct : $_GET["idAct"];
    $d=$m->query("SELECT *, DATE_FORMAT(DateCreate, '%d.%m.%Y') AS DateCreateS FROM TempPayrolls WHERE id=$idAct");
    $r=$d->fetch_assoc();
?>
<div class="panel NoPrint">
    <div class="panel-body">
        <form class="form-inline">
            <div class="form-group">
                <label class="sr-only" for="inputEmail">Email</label>
                Акт <input type="text" class="form-control" id="MainActID" placeholder="Акт" value="<?php echo $idAct; ?>">
            </div>
            <div class="form-group">
                <label class="sr-only" for="inputPassword">Пароль</label>
                Дата: <input type="text" class="form-control" id="MainDateCreate" placeholder="Дата" value="<?php echo $r["DateCreateS"]; ?>">
            </div>
            <div class="form-group">
                <label class="sr-only" for="inputPassword">Пароль</label>
                Всего начисленно: <input type="text" class="form-control" id="MainAllPlusNalog" placeholder="Всего начисленно">
            </div>
            <div class="form-group">
                <label class="sr-only" for="inputPassword">Пароль</label>
                Выплаченно: <input type="text" class="form-control" id="MainAllMinus" placeholder="Выплаченно">
            </div>
        </form>
    </div>
</div>
<div class="panel NoPrint">
    <div class="panel-body">
        <!--<button class="btn btn-primary">Пересчитать заново</button>-->
        <button onclick="DeleteAct()" class="btn btn-primary">Удалить акт</button>
        <button onclick="CalcComplite()" class="btn btn-primary">Завершить расчет</button>
        <button onclick="Print()" class="btn btn-primary">Печать</button>
        <button onclick="$('#HistoryDialog').modal('show');" class="btn btn-primary">История платежей</button>
    </div>
</div>
<div class="panel">
    <div class="panel-body">
        <table class="table table-responsive table-bordered table-hover">
            <thead>
                <tr>
                    <th>
                        ФИО
                        <input id="FilterFIO" oninput="FilterTable()" placeholder="Поиск" class="form-control">
                    </th>
                    <th>
                        Должность
                        <input id="FilterDolgnost" oninput="FilterTable()" placeholder="Поиск" class="form-control">
                    </th>
                    <th style="vertical-align: top">На начало периода</th>
                    <th style="vertical-align: top">Заработано</th>
                    <th style="vertical-align: top">Начисленно</th>
                    <th style="vertical-align: top">К выплате</th>
                    <th style="vertical-align: top">Налог</th>
                    <th style="vertical-align: top">К выплате (с налогом)</th>
                    <th style="vertical-align: top">Выплачено</th>
                    <th style="vertical-align: top">Остаток (с налогом)</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="WorkerTable">
                <?php
                    $arr=array();
                    $d=$m->query("SELECT * FROM Workers");
                    while($r=$d->fetch_assoc())
                        $arr[$r["id"]]=array(
                            "SumPlus"=>0,
                            "SumMinus"=>0
                        );
                    $d=$m->query("SELECT idWorker, SUM(COALESCE(SumPlus,0)) AS SumPlus, SUM(COALESCE(SumMinus,0)) AS SumMinus FROM TempPayrollsPayments WHERE idAct=$idAct GROUP BY idWorker");
                    if($d->num_rows>0)
                        while($r=$d->fetch_assoc())
                            $arr[$r["idWorker"]]=array(
                                "SumPlus"=>$r["SumPlus"],
                                "SumMinus"=>$r["SumMinus"]
                            );
                    $d=$m->query("SELECT p.idWorker, w.FIO, m.Dolgnost, p.SumWith, p.Cost, p.SumPlus , p.NalogPercent, -1*p.SumMinus AS SumMinus FROM TempPayrollsList p, Workers w, ManualDolgnost m WHERE p.idAct=$idAct AND p.idWorker=w.id AND w.DolgnostID=m.id");
                    while($r=$d->fetch_assoc()){
                        $SumPlusAll=(float)$r["SumWith"]+(float)$r["Cost"]+(float)$r["SumPlus"]+(float)$arr[$r["idWorker"]]["SumPlus"];
                        $SumPlusAllNalog=(float)$r["Cost"]+(float)$r["SumPlus"]+(float)$arr[$r["idWorker"]]["SumPlus"];
                        $SumPlusAllNalog=(float)$r["SumWith"]+($SumPlusAllNalog-$SumPlusAllNalog*(int)$r["NalogPercent"]/100);

                        $SumMinus=(float)$r["SumMinus"]+(float)$arr[$r["idWorker"]]["SumMinus"];

                        $BalanceNalog=$SumPlusAllNalog-$SumMinus;
                        ?>
                        <tr onclick="ActiondDialogShow(this)" idWorker="<?php echo $r["idWorker"]; ?>" class="<?php echo $BalanceNalog<=0 ? "success" : "warning" ?>">
                            <td Type="FIO"><?php echo $r["FIO"]; ?></td>
                            <td Type="Dolgnost"><?php echo $r["Dolgnost"]; ?></td>
                            <td Type="SumWith"><?php echo $r["SumWith"]; ?></td>
                            <td Type="SumCost"><?php echo $r["Cost"] ?></td>
                            <td Type="SumPlus"><?php echo (float)$r["SumPlus"]+(float)$arr[$r["idWorker"]]["SumPlus"]; ?></td>
                            <td Type="SumPlusAll"><?php echo $SumPlusAll; ?></td>
                            <td Type="NalogPercent"><?php echo $r["NalogPercent"]; ?></td>
                            <td Type="SumPlusAllNalog"><?php echo $SumPlusAllNalog; ?></td>
                            <td Type="SumMinus"><?php echo $SumMinus; ?></td>
                            <td Type="BalanceNalog"><?php echo $BalanceNalog; ?></td>
                        </tr>
                    <?php }
                ?>
            </tbody>
            <tfoot id="WorkerTableFooter">
                <tr>
                    <th colspan="2">ИТОГ</th>
                    <th Type="SumWith"></th>
                    <th Type="SumCost"></th>
                    <th Type="SumPlus"></th>
                    <th Type="SumPlusAll"></th>
                    <th Type="NalogPercent"></th>
                    <th Type="SumPlusAllNalog"></th>
                    <th Type="SumMinus"></th>
                    <th Type="BalanceNalog"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!--Диалог выбора действия-->
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="ActionsDialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <input type="hidden" id="ActionsDialogID">
            <input type="hidden" id="ActionsDialogFIO">
            <input type="hidden" id="ActionsDialogDolgnost">
            <input type="hidden" id="ActionsDialogBalance">
            <button onclick="PlusAdd()" class="btn btn-success" style="width: 90%; margin:5px; margin-right: 0px; margin-bottom: 10px;">Начислить</button><br>
            <button onclick="MinusAdd()" class="btn btn-danger" style="width: 90%; margin:5px; margin-right: 0px;">Выплатить</button>
        </div>
    </div>
</div>

<!--Диалог выплаты-->
<div class="modal fade" id="MinusDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Выплата</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="MinusMethod">
                <input type="hidden" id="MinusID">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="MinusFIO" class="col-xs-2 control-label">Сотрудник:</label>
                        <div class="col-xs-10">
                            <input type="text" id="MinusFIO" class="form-control" disabled>
                        </div>
                    </div>
                </form>
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="MinusDolgnost" class="col-xs-2 control-label">Должность:</label>
                        <div class="col-xs-10">
                            <input type="text" id="MinusDolgnost" class="form-control" disabled >
                        </div>
                    </div>
                </form>
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="MinusBalance" class="col-xs-2 control-label">Остаток:</label>
                        <div class="col-xs-10">
                            <input type="text" id="MinusBalance" class="form-control" disabled >
                        </div>
                    </div>
                </form>
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="MinusEqual" class="col-xs-2 control-label"></label>
                        <div class="col-xs-10">
                            <button id="MinusEqual" onclick="MinusBalance()" class="btn btn-primary">=</button>
                        </div>
                    </div>
                </div>
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="MinusSum" class="col-xs-2 control-label">Сумма:</label>
                        <div class="col-xs-10">
                            <input type="text" id="MinusSum" class="form-control" value="0" >
                        </div>
                    </div>
                </form>
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="MinusSum" class="col-xs-2 control-label">Примечание:</label>
                        <div class="col-xs-10">
                            <textarea id="MinusNote" class="form-control"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button onclick="MinusSave()" type="button" class="btn btn-primary">Выплатить</button>
            </div>
        </div>
    </div>
</div>

<!--История движения платежей-->
<div class="modal fade" id="HistoryDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Выплата</h4>
            </div>
            <div class="modal-body">
                <div id="HistoryInfo" class="alert alert-success">Есть новые записи в истории, обновите страницу</div>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Начисление</th>
                            <th>Выплата</th>
                            <th>Примечание</th>
                            <th>Составил</th>
                        </tr>
                    </thead>
                    <tbody id="HistoryTable">
                        <?php
                        $d=$m->query("SELECT p.*, DATE_FORMAT(p.DateCreate,'%d.%m.%Y') AS DateCreateS, l.FIO FROM TempPayrollsPayments p, Logins l WHERE p.idAct=$idAct AND p.Manager=l.id");
                        if($d->num_rows>0)
                            while($r=$d->fetch_assoc()) { ?>
                            <tr idPayment="<?php echo $r[id]; ?>">
                                <td Type="DateCreate"><?php echo $r["DateCreateS"]; ?></td>
                                <td Type="SumPlus"><?php echo $r["SumPlus"]; ?></td>
                                <td Type="SumMinus"><?php echo $r["SumMinus"]; ?></td>
                                <td Type="Note"><?php echo $r["Note"]; ?></td>
                                <td Type="Manager"><?php echo $r["FIO"]; ?></td>
                            </tr>
                        <?php }; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>