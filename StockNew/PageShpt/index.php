<script src="PageShpt/Action.js"></script>
<div class="container">
    <div class="row">
        <div class="col-md-12">
        <div class="form-inline">
            <div class="form-group">
                <label>Отобразить отгруженные акты</label>
                <input id="FilterShptCh" type="checkbox" class="form-control">
            </div>
            <div class="form-group">
                <label>Счет</label>
                <input id="FilterShet" type="text" class="form-control" placeholder="# счета">
            </div>
        </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table class="table table-responsive TablesWorkes-hover">
            <thead>
                <tr>
                    <th>Заказ</th>
                    <th>Счет</th>
                    <th>Дата создания</th>
                    <th>Мэнеджер</th>
                    <th>Дата отгрузки</th>
                </tr>
            </thead>
            <tbody id="ShptTable"></tbody>
        </table>
    </div>
</div>

<!-------- Диалог отгрузки ---------------->
<div class="modal fade" id="a_Dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Комплектация</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input id="a_idAct" type="hidden">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Счет</label>
                                </div>
                                <div class="col-md-8">
                                    <input class="form-control" id="a_Shet" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Дата создания</label>
                                </div>
                                <div class="col-md-8">
                                    <input class="form-control" id="a_DateCreate" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Менеджер</label>
                                </div>
                                <div class="col-md-8">
                                    <input class="form-control" id="a_ManagerFIO" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Отгрузка</label>
                                </div>
                                <div class="col-md-8">
                                    <input class="form-control" id="a_DateShpt" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#a_Goods" aria-controls="home" role="tab" data-toggle="tab">Комплектующие</a></li>
                        <li role="presentation"><a href="#a_Naryads" aria-controls="profile" role="tab" data-toggle="tab">Двери</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="a_Goods">
                            <table class="table table-responsive table-hover">
                                <thead>
                                    <tr>
                                        <th>Группа</th>
                                        <th>Требуется списать</th>
                                        <th>На складе</th>
                                        <th>К списанию</th>
                                    </tr>
                                </thead>
                                <tbody id="a_TableSpe"></tbody>
                            </table>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="a_Naryads">
                            <table class="table table-responsive table-hover">
                                <thead>
                                    <tr>
                                        <td>№ п/п</td>
                                        <td>Наряд</td>
                                    </tr>
                                </thead>
                                <tbody id="a_TableNaryads"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button onclick="ActSave()" type="button" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>