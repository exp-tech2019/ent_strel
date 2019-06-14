<script src="PageSupplier/Action.js"></script>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-2">
                <button onclick="AddSupplier()" class="btn btn-primary">+ Поставщика</button>
            </div>
            <div class="col-md-10">
                <div class="form-inline">
                    <div class="form-group">
                        <label>Поиск</label>
                        <input oninput="SupplierFind(this)" class="form-control"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<table class="table table-border table-hover">
    <thead>
        <tr>
            <th>Наименование</th>
            <th>ИНН</th>
            <th>Адрес</th>
            <th>Телефон</th>
        </tr>
    </thead>
    <tbody id="SupplierList"></tbody>
</table>

<!--Диалог Контрагента-->
<div class="modal fade" id="s_Dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Контрагент</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="s_Alert" class="bs-example bs-example-standalone" data-example-id="dismissible-alert-js">
                            <div class="alert alert-danger alert-dismissible fade in" role="alert">
                                <button class="close" aria-label="Close" type="button" data-dismiss="alert">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <p id="s_AlertText">dsfdsf</p>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <input type="hidden" id="s_id">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Наименование <span style="color: red;">*</span></label>
                                </div>
                                <div class="col-md-8">
                                    <input class="form-control" id="s_SupplierName">
                                    </input>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>ИНН <span style="color: red;">*</span></label>
                                </div>
                                <div class="col-md-8">
                                    <input class="form-control" id="s_INN">
                                    </input>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Адрес</label>
                                </div>
                                <div class="col-md-8">
                                    <input class="form-control" id="s_Adress">
                                    </input>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Телефон</label>
                                </div>
                                <div class="col-md-8">
                                    <input class="form-control" id="s_Phone">
                                    </input>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button onclick="SaveSupplier()" type="button" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>