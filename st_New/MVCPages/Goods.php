<script src="scripts/Goods.js"></script>
<section class="content-header">
    <h1>
        Справочник номенклатуры
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <div class="input-group" style="width: 300px; float: left; margin-right: 20px;">
                <input id="GoodsFind" type="text" class="form-control" placeholder="Поиск номенклатуры" aria-describedby="basic-addon2">
                <span id="GoodsFindBtn" class="input-group-addon" style="cursor: pointer;">
                    <span class="glyphicon glyphicon-search"></span>
                </span>
            </div>
            <button onclick="GoodDialog.Add()" class="btn btn-primary">Добавить номенклатуру</button>
            <button onclick="Groups_Dialog.Add()" class="btn btn-primary">Добавить группу</button>
        </div>
        <div class="box-body">
            <table class="table table-responsive table-hover">
                <thead>
                    <tr>
                        <th style="width: 30px;"></th>
                        <th>Наименование</th>
                        <th>Ед. изм.</th>
                        <th>Артикул</th>
                        <th>Производитель</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="GoodsTable"></tbody>
            </table>
        </div>
    </div>
</section>

<div id="GoodGroupsDialog" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Группа</h4>
            </div>
            <div class="modal-body">
                <p>
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Наименование:</label>
                        <div class="col-lg-8">
                            <input id="GoodGroupsDialog_Name" class="form-control danger">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Автосписание</label>
                        <div class="col-lg-8">
                            <input id="GoodGroupsDialog_AutoSalvage" type="checkbox">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Стадия:</label>
                        <div class="col-lg-8">
                            <select id="GoodGroupsDialog_Step" class="form-control">
                                <option value="-1"></option>
                                <option value="0">Без списания</option>
                                <option value="1">Лазер</option>
                                <option value="2">Гибка</option>
                                <option value="3">Сварка</option>
                                <option value="4">Рамка</option>
                                <option value="5">Сборка</option>
                                <option value="6">Покраска</option>
                                <option value="7">Упаковка</option>
                                <option value="8">Погрузка</option>
                                <option value="9">МДФ Цех</option>
                                <option value="10">МДФ сборка</option>
                            </select>
                        </div>
                    </div>
                </div>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button onclick="Groups_Dialog.Save()" type="button" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<!-- Диалог товара -->
<div id="GoodDialog" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Группа</h4>
            </div>
            <div class="modal-body">
                <p>
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Группа:</label>
                        <div class="col-lg-8">
                            <select id="GoodDialog_GroupList" class="form-control">
                                <option value="-1"></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Наименование:</label>
                        <div class="col-lg-8">
                            <input id="GoodDialog_Name" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Артикул:</label>
                        <div class="col-lg-8">
                            <input id="GoodDialog_Article" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Штриход:</label>
                        <div class="col-lg-8">
                            <input id="GoodDialog_BarCode" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Производитель:</label>
                        <div class="col-lg-8">
                            <input id="GoodDialog_Manufacturer" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Ед. измерения:</label>
                        <div class="col-lg-8">
                            <select id="GoodDialog_Unit" class="form-control">
                                <option value="-1"></option>
                            </select>
                        </div>
                    </div>
                </div>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button onclick="GoodDialog.Save()" type="button" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>