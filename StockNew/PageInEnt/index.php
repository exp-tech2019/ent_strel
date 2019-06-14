<script src="PageInEnt/Action.js"></script>
<script>
    var Global_idLogin='<?php echo $_SESSION["AuthorizeID"]; ?>';
    var Global_TextLogin='<?php echo $_SESSION["AuthorizeFIO"]; ?>';
    var Global_Date='<?php echo date('d.m.Y'); ?>';
</script>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-2">
                <button onclick="AddAct()" class="btn btn-primary">+ Передать в производство</button>
            </div>
            <div class="col-md-10">
                <div class="form-inline">
                    <div class="form-group">
                        <label>Поиск</label>
                        <input id="SupplierFind" class="form-control"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<table class="table table-hover table-responsive">
    <thead>
    <tr>
        <td></td>
        <td>Дата</td>
        <td>Ответсвенный</td>
        <td>Сотрудник</td>
    </tr>
    </thead>
    <tbody id="EntList"></tbody>
</table>

<div class="modal fade bs-example-modal-lg" id="e_Dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Передача в производство</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="e_Alert" class="bs-example bs-example-standalone" data-example-id="dismissible-alert-js">
                            <div class="alert alert-danger alert-dismissible fade in" role="alert">
                                <button class="close" aria-label="Close" type="button" data-dismiss="alert">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <p id="e_AlertText">dsfdsf</p>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>№ акта <span style="color: red;">*</span></label>
                                </div>
                                <div class="col-md-8">
                                    <input class="form-control" id="e_id" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Дата</label>
                                </div>
                                <div class="col-md-8">
                                    <div class='input-group date'>
                                        <input id='e_DateCreate' type='text' class="form-control" placeholder="дд.мм.гггг" value="<?php echo date('d.j.Y'); ?>" disabled/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Отвтственный</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input class="form-control" id="e_TextLogin" value="<?php echo $_SESSION["AuthorizeFIO"]; ?>" disabled>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-hdd"></span>
                                        </span>
                                    </div>
                                    <input type="hidden" id="e_idLogin" value="<?php echo $_SESSION["AuthorizeID"]; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Сотрудник</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input onclick="PluginWorkerList.OpenDialog('#e_idWorker','#e_FIOWorker')" class="form-control" id="e_FIOWorker" >
                                        <span onclick="w_OpenDialog()" class="input-group-addon">
                                            <span class="glyphicon glyphicon-hdd"></span>
                                        </span>
                                    </div>
                                    <input type="hidden" id="e_idWorker">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button onclick="AddRow('','','','')" class="btn btn-primary">+ Номеклатуру</button>
                <table class="table table-responsive" id="e_GoodsList">
                    <thead>
                    <tr>
                        <th>Наименование</th>
                        <td>Кол-во на складе</td>
                        <td>Кол-во</td>
                    </tr>
                    </thead>
                    <tbody id="e_Table"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button id="e_BtnSave" onclick="SaveAct()" type="button" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" id="w_Dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Вставьте карту</label>
                                </div>
                                <div class="col-md-8">
                                    <input class="form-control" id="w_SmartCard">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button onclick="w_SelectWorker()" type="button" class="btn btn-primary">Выбрать</button>
            </div>
        </div>
    </div>
</div>

<?php include "CommonDialog/ManualGoods.php" ?>
<?php include "Plugins/DialogSelectWorkerList/index.php"; ?>
