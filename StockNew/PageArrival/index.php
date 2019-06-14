<script src="PageArrival/Action.js"></script>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-2">
                <button onclick="AddArrival()" class="btn btn-primary">+ Поступление</button>
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
            <th></th>
            <th>№</th>
            <td>Дата</td>
            <td>Поставщик</td>
            <td>Общяя стоимость</td>
        </tr>
    </thead>
    <tbody id="ArrivalList"></tbody>
</table>

<!-- Диалог -->
<div class="modal fade bs-example-modal-lg" id="a_Dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Поступление</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="a_Alert" class="bs-example bs-example-standalone" data-example-id="dismissible-alert-js">
                            <div class="alert alert-danger alert-dismissible fade in" role="alert">
                                <button class="close" aria-label="Close" type="button" data-dismiss="alert">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <p id="a_AlertText">dsfdsf</p>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <input type="hidden" id="a_idArrival">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>№ акта <span style="color: red;">*</span></label>
                                </div>
                                <div class="col-md-8">
                                    <input class="form-control" id="a_NumArrival">
                                    </input>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Дата</label>
                                </div>
                                <div class="col-md-8">
                                    <div class='input-group date' id="a_DateArrivalCalendar">
                                        <input id='a_DateArrival' type='text' class="form-control" placeholder="дд.мм.гггг"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                    <script>
                                        $(function () {
                                            $('#a_DateArrivalCalendar').datetimepicker(
                                                {locale: 'ru', format: "L"}
                                            );
                                        })
                                    </script>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Поставщик</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input class="form-control" id="a_TextSupplier">
                                        <span onclick="s_OpenDialog()" class="input-group-addon">
                                            <span style="cursor: pointer" class="glyphicon glyphicon-folder-open"></span>
                                        </span>
                                    </div>
                                    <input type="hidden" id="a_idSupplier">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button onclick="GoodRowAdd('','','','','')" class="btn btn-primary">+ Номеклатуру</button>
                <table class="table table-responsive">
                    <thead>
                        <tr>
                            <th>Наименование в ТТН</th>
                            <th>Наименование из справочника</th>
                            <td>Кол-во</td>
                            <td>Цена за ед.</td>
                            <td>Стоимость</td>
                        </tr>
                    </thead>
                    <tbody id="a_GoodsList"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button id="a_BtnSave" onclick="a_Save()" type="button" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<!-- Диалог справоочника поставщиков -->
<div class="modal fade" id="s_Dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Поставщики</h4>
            </div>
            <div class="modal-body">
                <button class="btn btn-primary" onclick="s_Select()">Обеовить</button>
                <button class="btn btn-primary" onclick="window.open('index.php?MVCPage=PageSupplier','s')">Справочник</button>
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th>Поставщик</th>
                        <td>ИНН</td>
                    </tr>
                    </thead>
                    <tbody id="s_SupplierList"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button onclick="s_CloseDialog()" type="button" class="btn btn-primary">Выбрать</button>
            </div>
        </div>
    </div>
</div>

<!-- Диалог справоочника номеклатуры -->
<div class="modal fade" id="g_Dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Номеклатура</h4>
            </div>
            <div class="modal-body">
                <button class="btn btn-primary" onclick="g_Select()">Обеовить</button>
                <button class="btn btn-primary" onclick="window.open('index.php?MVCPage=PageGoods','s')">Справочник</button>
                <table class="tree table table-hover" id="g_GoodList">
                    <tr Type="Header" class="treegrid-100" style="font-weight: bold;">
                        <td>Артуикул</td>
                        <td>Наименование</td>
                        <td>Штрихкод</td>
                        <td>Ед. изм.</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button onclick="g_CloseDialog()" type="button" class="btn btn-primary">Выбрать</button>
            </div>
        </div>
    </div>
</div>