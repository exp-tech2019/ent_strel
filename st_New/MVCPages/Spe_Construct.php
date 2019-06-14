<script src="scripts/Spe_Construct.js"></script>
<section class="content-header">
    <h1>
        Конструктор спецификации
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-xs-2 control-label">Выберите тип двери</label>
                    <div class="col-xs-2">
                        <select class="form-control" id="DoorList">
                            <option value="-1"></option>
                        </select>
                    </div>
                    <div class="col-xs-1">
                        <button id="BtnSave" class="btn btn-success">Сохранить</button>
                    </div>
                    <div class="col-xs-1">
                        <button id="BtnAddGroup" class="btn btn-primary">
                            <span class="glyphicon glyphicon-plus"></span>
                            Группу
                        </button>
                    </div>
                </div>
            </div>

        </div>
        <div class="box-body">
            <table class="table table-responsive table-hover">
                <thead>
                    <tr>
                        <th></th>
                        <th>Группа</th>
                        <th>Тип расчета</th>
                        <th>Кол-во</th>
                    </tr>
                </thead>
                <tbody id="CommonTable"></tbody>
            </table>
        </div>
    </div>
</section>

<!--Диалог групп-->
<div id="GroupDialog" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Выбор группы</h4>
            </div>
            <div class="modal-body">4545
                <table class="table table-responsive table-hover">
                    <thead>
                        <th>Наименование</th>
                    </thead>
                    <tbody id="GroupDialogTable"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button onclick="GroupDialog.Close()" type="button" class="btn btn-primary">Выбрать</button>
            </div>
        </div>
    </div>
</div>