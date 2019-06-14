<script src="scripts/TransferInEnt_One.js"></script>
<section class="content-header">
    <h1>
        Выдача сотруднику
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <button onclick="window.location.href='?MVCPage=TransferInEnt_List'" class="btn btn-danger">Открыть ранние выдачи</button>
            <button onclick="Save()" class="btn btn-success">Выдать</button>
        </div>
        <div class="box-body">
            <div class="form-horizontal">
                <div class="form-group">
                    <label for="TransferDate" class="col-xs-1 control-label">Дата выдачи</label>
                    <div class="col-xs-2">
                        <input id="TransferDate" class="form-control" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <label for="LoginFIO" class="col-xs-1 control-label">Выдал</label>
                    <div class="col-xs-2">
                        <input id="idLogin" type="hidden" value="<?php echo $_SESSION["idLogin"]; ?>">
                        <input id="LoginFIO" value="<?php echo $_SESSION["FIOLogin"]; ?>" class="form-control" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <label  for="WorkerFIO" class="col-xs-1 control-label">Получил</label>
                    <input id="idWorker" type="hidden">
                    <input id="idDolgnost" type="hidden">
                    <div class="col-xs-2">
                        <input id="WorkerFIO" class="form-control" style="cursor: pointer;">
                    </div>
                </div>
                <div class="form-group">
                    <label for="NaryadInp" class="col-xs-1 control-label">Наряд</label>
                    <div class="col-xs-2">
                        <div class="input-group">
                            <input id="idNaryad" type="hidden">
                            <input id="NaryadInp" class="form-control" placeholder="Введите номер наряда: 1/1/1">
                            <span id="NaryadBtn" class="input-group-addon" style="cursor: pointer;">
                                <span class="glyphicon glyphicon-plus"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-2">
                                <button onclick="GoodsDialog.OpenDialog()" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-plus"></span>
                                    Номенклатуру
                                </button>
                            </div>
                            <div class="col-xs-2">
                                <div class="checkbox">
                                    <label>
                                        <input id="DolgnostStepFilter" type="checkbox" checked> Показать всю номнеклатуру
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <table class="table table-responsive table-hover">
                <thead>
                    <tr>
                        <th>Номнелатура</th>
                        <th>Ед. изм.</th>
                        <th>На складе</th>
                        <th>Требуется по наряду</th>
                        <th>Выдано ранее</th>
                        <th>Кол-во</th>
                    </tr>
                </thead>
                <tbody id="GoodsTable">
                </tbody>
            </table>
        </div>
    </div>
</section>

<!--Диалог нарядов -->
<div id="WorkerDialog" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <input id="WorkerDialogFind" oninput="WorkerDialog.Find()" class="form-control input-sm" style="float:left; width:80%; border-radius:15px" placeholder="Поиск">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p>
                    <table class="table table-responsive table-hover">
                        <thead>
                        <tr>
                            <th>ФИО</th>
                            <th>Должность</th>
                        </tr>
                        </thead>
                        <tbody id="WorkerDialogList"></tbody>
                    </table>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button onclick="WorkerDialog.Close()" type="button" class="btn btn-primary">Выбрать</button>
            </div>
        </div>
    </div>
</div>

<!--Диалог выбора товаров -->
<div id="GoodsDialog" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width:900px;">
        <div class="modal-content">
            <div class="modal-header">
                <input id="GoodsDialogFind" class="form-control" style="float:left; width:80%; border-radius:15px" placeholder="Поиск">
                <a href="?MVCPage=Goods" target="_blank">
                    <button class="btn btn-default">
                        <span class="glyphicon glyphicon-plus"></span>
                    </button>
                </a>
                <button onclick="GoodsDialog.Select()" class="btn btn-default">
                    <span class="glyphicon glyphicon-refresh"></span>
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p>
                <table class="table table-responsive table-hover">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Наименование</th>
                        <th>Ед изм.</th>
                        <th>Производитель</th>
                        <th>На складе</th>
                    </tr>
                    </thead>
                    <tbody id="GoodsDialogTable"></tbody>
                </table>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button onclick="GoodsDialog.Close()" type="button" class="btn btn-primary">Выбрать</button>
            </div>
        </div>
    </div>
</div>