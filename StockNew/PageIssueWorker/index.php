<script src="PageIssueWorker/Action.js"></script>

<div class="panel panel-default">
    <div class="panel-body">
        <button onclick="AddIssue()" class="btn btn-primary">Выдать</button>
    </div>
</div>

<table class="table table-responsive table-hover">
    <thead>
        <tr>
            <th>Дата</th>
            <th>Наряд</th>
            <th>Выдал</th>
            <th>Кому выдан</th>
        </tr>
    </thead>
    <tbody id="isw_Table"></tbody>
</table>
<!-- Диалог выбора наряда-->
<div class="modal fade" id="isw_Dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Выдача по наряду</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-horizontal">
                            <input id="isw_Dialog_idDoor" type="hidden">
                            <input id="isw_Dialog_idNaryad" type="hidden">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Сотрудник</label>
                                </div>
                                <div class="col-md-8">
                                    <div class='input-group date'>
                                        <input id="isw_Dialog_WorkerID" type="hidden">
                                        <input id='isw_Dialog_WorkerFIO' onclick="CardDialogLoad()" type='text' class="form-control" placeholder="Выберите сотрудника"/>
                                        <span class="input-group-addon" onclick="CardDialogLoad()">
                                            <span class="glyphicon glyphicon-folder-open"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Наряд</label>
                                </div>
                                <div class="col-md-8">
                                    <div class='input-group date'>
                                        <input id='isw_Dialog_NaryadNum' type='text' class="form-control" placeholder="Введите номер наряда или счета"/>
                                        <span class="input-group-addon" onclick="LoadSpecification()">
                                            <span class="glyphicon glyphicon-folder-open"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-responsive table-hover">
                                <thead>
                                    <tr>
                                        <th>Номеклатура</th>
                                        <th>Кол-во списание</th>
                                        <th>На складе</th>
                                        <th>Уже выдано</th>
                                        <th>К выдаче</th>
                                    </tr>
                                </thead>
                                <tbody id="isw_Dialog_Table"></tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button id="isw_Dialog_BtnSave" onclick="SaveIssue()" type="button" class="btn btn-primary">Передать</button>
            </div>
        </div>
    </div>
</div>

<!-- Диалог ввода карты сотрудника-->
<div class="modal fade" id="card_Dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Сотрудник</h4>
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
                                    <input id="card_Dialog_idWorker" type="hidden">
                                    <input id='card_Dialog_Num' type='text' class="form-control" placeholder="№ карты"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button onclick="CardSelected()" type="button" class="btn btn-primary">Выбрать</button>
            </div>
        </div>
    </div>
</div>