<script src="Pagegoods/action.js"></script>
<div class="panel panel-default">
    <div class="panel-body form-inline">
        <div class="form-group">
            <button onclick="AddGroup()" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Группу </button>
        </div>
        <div class="form-group">
            <button onclick="AddGood()" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Номеклатуру </button>
        </div>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <div class="form-group">
            <label>Поиск</label>
            <input oninput="Find(this)" style="width: 200px;" type="text" class="form-control" placeholder="Поиск">
        </div>
    </div>
</div>
<table class="tree table table-hover" id="Table">
    <tr Type="Header" class="treegrid-100" style="font-weight: bold;">
        <td>Артуикул</td>
        <td>Наименование</td>
        <td>Штрихкод</td>
        <td>Ед. изм.</td>
    </tr>
    <tr class="treegrid-2 treegrid-parent-1">
        <td colspan="4">Группа 1</td>
    </tr>
    <tr class="treegrid-3 treegrid-parent-2">
        <td>Node 1-2-1</td><td>Additional info</td>
    </tr>
</table>

<!--Добавление группы-->
<div class="modal fade" id="gd_Dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Группа</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-horizontal">
                            <input type="hidden" id="gd_id">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Наименование</label>
                                </div>
                                <div class="col-md-8">
                                    <input class="form-control" id="gd_GroupName">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Списание на стадии</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" id="gd_Step">
                                        <option value="0">Любая</option>
                                        <option value="1">Лазер</option>
                                        <option value="2">Гибка</option>
                                        <option value="3">Сварка</option>
                                        <option value="4">Рамка</option>
                                        <option value="9">МДФ цнх</option>
                                        <option value="5">Сборка</option>
                                        <option value="10">Сборка МДФ</option>
                                        <option value="6">Покраска</option>
                                        <option value="7">Упаковка</option>
                                        <option value="8">Погрузка</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Авто списание</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="checkbox" id="gd_AutoUnset">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button onclick="SaveGroup()" type="button" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<!--Добавление номеклатура-->
<div class="modal fade" id="g_Dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Номеклатура</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-horizontal">
                            <input type="hidden" id="g_id">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Группа</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" id="g_Group">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Наименование</label>
                                </div>
                                <div class="col-md-8">
                                    <input class="form-control" id="g_GoodName">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Артикул</label>
                                </div>
                                <div class="col-md-8">
                                    <input class="form-control" id="g_Article">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Штрих-код</label>
                                </div>
                                <div class="col-md-8">
                                    <input class="form-control" id="g_BarCode">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Ед. измерения</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" id="g_Unit">
                                        <option value="0">м <sup>2</sup></option>
                                        <option value="1">шт</option>
                                        <option value="2">кг</option>
                                        <option value="3">л</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button onclick="SaveGood()" type="button" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>