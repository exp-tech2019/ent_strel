<script src="scripts/ActArrivalOne.js"></script>
<input id="idArrival" type="hidden" value="<?php echo isset($_GET["idArrival"]) ? $_GET["idArrival"] : "-1"; ?>">
<section class="content-header">
    <h1>
        Поступление
    </h1>
</section>
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <button id="BtnAccept" onclick="ActArrival.Save('Accept')" class="btn btn-success btn-sm">Провести</button>
            <button id="BtnSave" onclick="ActArrival.Save('Save')" class="btn btn-primary btn-sm">Сохранить</button>
            <button id="BtnCancel" onclick="window.location.href='?MVCPage=ActArrivalList'" class="btn btn-primary btn-sm">Закрыть</button>
            <button id="BtnRemove" onclick="ActArrival.Remove()" class="btn btn-danger btn-sm">Удалить</button>
        </div>
        <div class="box-body">
            <div class="form-horizontal" style="width: 400px;">
                <div class="form-group">
                    <label class="col-lg-4 control-label">№ ТТН</label>
                    <div class="col-lg-8">
                        <input id="TTNNum" class="form-control input-sm">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-4 control-label">Дата ТТН</label>
                    <div class="col-lg-8">
                        <input id="TTNDate" class="form-control input-sm">
                        <script>
                            $(document).ready(function(){
                                $('#TTNDate').datepicker({format: 'dd.mm.yyyy',locale: 'ru'});
                            })
                        </script>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-4 control-label">Поставщик</label>
                    <div class="col-lg-8">
                        <div onclick="CustomersDialog.OpenDialog()" class="input-group">
                            <input id="idCustomer" type="hidden">
                            <input id="CustomerName" type="text" class="form-control input-sm" disabled aria-label="Amount (to the nearest dollar)">
                            <span class="input-group-addon glyphicon glyphicon-th-list"></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-4 control-label">Принял</label>
                    <div class="col-lg-8">
                        <input id="LoginID" type="hidden" value="">
                        <input id="LoginFIO" class="form-control input-sm" disabled>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <button onclick="GoodsDialog.OpenDialog()" class="btn btn-primary btn-sm">Добавить (ins)</button>
                    <button id="GoodsTableBtnRemove" onclick="GoodsTable.Remove()" class="btn btn-primary">Удалить</button>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-hover dataTable" role="grid">
                        <thead>
                            <tr>
                                <th>
                                    <input id="GoodsTableChAll" onchange="GoodsTable.ChAll()" type="checkbox" class="">
                                </th>
                                <th>Номенклатура</th>
                                <th>НДС</th>
                                <th>Производитель</th>
                                <th>Ед. измер.</th>
                                <th>Кол-во</th>
                                <th>Цена</th>
                                <th>Стоимость</th>
                            </tr>
                        </thead>
                        <tbody id="GoodsTable"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!--Диалог выбора Поставщика -->
<div id="CustomersDialog" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <input id="CustomersDialogFind" class="form-control" style="float:left; width:80%; border-radius:15px" placeholder="Поиск">
                <a href="?MVCPage=Customer_List" target="_blank">
                    <button class="btn btn-default">
                            <span class="glyphicon glyphicon-plus"></span>
                    </button>
                </a>
                <button onclick="CustomersDialog.Select()" class="btn btn-default">
                    <span class="glyphicon glyphicon-refresh"></span>
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p>
                <table class="table table-responsive table-hover">
                    <thead>
                        <tr>
                            <th>Наименование</th>
                            <th>ИНН</th>
                        </tr>
                    </thead>
                    <tbody id="CustomersDialogTable"></tbody>
                </table>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button onclick="CustomersDialog.Close()" type="button" class="btn btn-primary">Выбрать</button>
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
                                <th>Артикул</th>
                                <th>Производитель</th>
                                <th>Ед изм.</th>
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
