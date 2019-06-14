<script src="scripts/SettingConstruct.js"></script>
<section class="content-header">
    <h1>
        Конструктор
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Гдавная</a></li>
        <li class=""><a href="#">Настройки</a></li>
        <li class="active"><a href="#">Конструктор</a></li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <div class="form-group col-md-6">
                    </div>
                </div>
                <div id="bb" class="box-body nav-tabs-custom">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#Tab1" aria-controls="home" role="tab" data-toggle="tab">Размеры</a></li>
                        <li onclick="RAL.Select()" role="presentation"><a href="#Tab2" aria-controls="profile" role="tab" data-toggle="tab">Краска</a></li>
                        <li role="presentation"><a href="#Tab3" aria-controls="messages" role="tab" data-toggle="tab">Доводчик</a></li>
                        <li onclick="Naves.Select()" role="presentation"><a href="#Tab4" aria-controls="settings" role="tab" data-toggle="tab">Навесы</a></li>
                        <li onclick="Furnitura.Select()" role="presentation"><a href="#Tab5" aria-controls="settings" role="tab" data-toggle="tab">Фурнитура</a></li>
                        <li onclick="Glases.Select()" role="presentation"><a href="#Tab6" aria-controls="settings" role="tab" data-toggle="tab">Остекление</a></li>
                        <li onclick="Other.Select()" role="presentation"><a href="#Tab7" aria-controls="settings" role="tab" data-toggle="tab">Дополнительно</a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <!--Размеры-->
                        <div role="tabpanel" class="tab-pane active" id="Tab1">
                            <div class="form-inline">
                                <div class="form-group">
                                    <label for="SizeAdd_TypeDoor" class="control-label">Тип</label>
                                    <select id="SizeAdd_TypeDoor" class="form-control input-sm">
                                        <option></option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="SizeAdd_HWith" class="control-label">Высота</label>
                                    <input id="SizeAdd_HWith" class="form-control input-sm" style="width:50px;" placeholder="с">
                                </div>
                                <div class="form-group">
                                    <label for="SizeAdd_HBy" class="control-label">:</label>
                                    <input id="SizeAdd_HBy" class="form-control input-sm" style="width:50px;" placeholder="по">
                                </div>
                                <div class="form-group">
                                    <label for="SizeAdd_WWith" class="control-label">Ширина</label>
                                    <input id="SizeAdd_WWith" class="form-control input-sm" style="width:50px;" placeholder="с">
                                </div>
                                <div class="form-group">
                                    <label for="SizeAdd_WBy" class="control-label">:</label>
                                    <input id="SizeAdd_WBy" class="form-control input-sm" style="width:50px;" placeholder="по">
                                </div>
                                <div class="form-group">
                                    <label for="SizeAdd_Stovrka" class="control-label">Створка</label>
                                    <select id="SizeAdd_Stovrka" class="form-control input-sm">
                                        <option>Одностворчатая</option>
                                        <option>Двухстворчатая</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="SizeAdd_Framug" class="control-label">Фрамуга</label>
                                    <select id="SizeAdd_Framug" class="form-control input-sm">
                                        <option></option>
                                        <option>Да</option>
                                        <option>Нет</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="SizeAdd_Sum" class="control-label">Стоимость фикс</label>
                                    <input id="SizeAdd_Sum" class="form-control input-sm" style="width:60px;" placeholder="руб.">
                                </div>
                                <div class="form-group">
                                    <label for="SizeAdd_SumM2" class="control-label">Стоимость м<sup>2</sup></label>
                                    <input id="SizeAdd_SumM2" class="form-control input-sm" style="width:60px;" placeholder="руб.">
                                </div>
                                <button onclick="DoorSize.Add()" class="btn btn-primary">
                                    <span class="fa fa-plus"></span>
                                </button>
                            </div>
                            <br>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Тип</th>
                                        <th colspan="2">Высота</th>
                                        <th colspan="2">Ширина</th>
                                        <th rowspan="2">Створка</th>
                                        <th rowspan="2">Фрамуга</th>
                                        <th rowspan="2">Стоимость фикс.</th>
                                        <th rowspan="2">Стоимость за м<sup>2</sup></th>
                                        <th rowspan="2"></th>
                                        <th rowspan="2"></th>
                                    </tr>
                                <tr>
                                    <th>с</th>
                                    <th>по</th>
                                    <th>с</th>
                                    <th>по</th>
                                </tr>
                                </thead>
                                <tbody id="Size_Table"></tbody>
                            </table>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="Tab2">
                            <div class="form-inline">
                                <div class="form-group">
                                    <label for="RAL_Name" class="form-label">Окрас</label>
                                    <input id="RAL_Name" class="form-control input-sm">
                                </div>
                                <div class="form-group">
                                    <label for="RAL_TypeCalc" class="form-label">Тип расчета</label>
                                    <select id="RAL_TypeCalc" class="form-control input-sm">
                                        <option value="-1"></option>
                                        <option value="1">М2</option>
                                        <option value="2">Процент от стоимости</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="RAL_Sum" class="control-label">Стоимость</label>
                                    <input id="RAL_Sum" class="form-control input-sm">
                                </div>
                                <button onclick="RAL.Add()" class="btn btn-sm btn-primary">Добавить</button>
                            </div>
                            <table class="table table-striped table-responsive">
                                <thead>
                                    <tr>
                                        <th>Наименование</th>
                                        <th>Тип расчета</th>
                                        <th>Стоимость</th>
                                        <th class="width:50px"></th>
                                    </tr>
                                </thead>
                                <tbody id="RAL_Table"></tbody>
                            </table>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="Tab3">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label for="Dovod_WithoutDovod" class="control-label col-xs-2">Без доводчика</label>
                                    <div class="col-xs-2">
                                        <input id="Dovod_WithoutDovod" oninput="DovodTab.Change(this)" class="form-control input-sm">
                                    </div>
                                    <div class="col-xs-1">
                                        <button onclick="DovodTab.Save(this)" class="btn btn-default btn-sm">
                                            <span class="fa fa-save"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Dovod_WorkDovod" class="control-label col-xs-2">Подготовка пд доводчик</label>
                                    <div class="col-xs-2">
                                        <input id="Dovod_WorkDovod" oninput="DovodTab.Change(this)" class="form-control input-sm">
                                    </div>
                                    <div class="col-xs-1">
                                        <button onclick="DovodTab.Save(this)" class="btn btn-default btn-sm">
                                            <span class="fa fa-save"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Dovod_Dovod" class="control-label col-xs-2">Доводчик</label>
                                    <div class="col-xs-2">
                                        <input id="Dovod_Dovod" oninput="DovodTab.Change(this)" class="form-control input-sm">
                                    </div>
                                    <div class="col-xs-1">
                                        <button onclick="DovodTab.Save(this)" class="btn btn-default btn-sm">
                                            <span class="fa fa-save"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="Tab4">
                            <div class="form-inline">
                                <div class="form-group">
                                    <label for="NavesAdd_Name" class="control-label">Наименование</label>
                                    <input id="NavesAdd_Name" class="form-control input-sm">
                                </div>
                                <div class="form-group">
                                    <label for="NavesAdd_HWith" class="control-label">Высота</label>
                                    <input id="NavesAdd_HWith" class="form-control input-sm" placeholder="с" style="width:50px">
                                    <label for="NavesAdd_HBy" class="control-label">:</label>
                                    <input id="NavesAdd_HBy" class="form-control input-sm" placeholder="по" style="width:50px">
                                </div>
                                <div class="form-group">
                                    <label for="NavesAdd_WWith" class="control-label">Ширина</label>
                                    <input id="NavesAdd_WWith" class="form-control input-sm" placeholder="с" style="width:50px">
                                    <label for="NavesAdd_WBy" class="control-label">:</label>
                                    <input id="NavesAdd_WBy" class="form-control input-sm" placeholder="по" style="width:50px">
                                </div>
                                <div class="form-group">
                                    <label for="NavesAdd_Stvorka" class="control-label">Створка</label>
                                    <select id="NavesAdd_Stvorka" class="form-control input-sm">
                                        <option value="0"></option>
                                        <option value="1">Одностворчатая</option>
                                        <option value="2">Двухстворчатая</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="NavesAdd_Sum" class="control-label">Стоимость</label>
                                    <input id="NavesAdd_Sum" class="form-control input-sm" placeholder="руб.">
                                </div>
                                <button onclick="Naves.Add()" class="btn btn-primary btn-sm">
                                    <span class="fa fa-plus"></span>
                                </button>
                            </div>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Наименование</th>
                                        <th colspan="2">Высота</th>
                                        <th colspan="2">Ширина</th>
                                        <th rowspan="2">Створка</th>
                                        <th rowspan="2">Стоимость</th>
                                    </tr>
                                    <tr>
                                        <th>с</th>
                                        <th>по</th>
                                        <th>с</th>
                                        <th>по</th>
                                    </tr>
                                </thead>
                                <tbody id="NavesTable"></tbody>
                            </table>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="Tab5">
                            <div class="form-inline">
                                <div class="form-group">
                                    <label for="FurnituraAdd_Name" class="control-label">Наименование</label>
                                    <input id="FurnituraAdd_Name" class="form-control input-sm">
                                </div>
                                <div class="form-group">
                                    <label for="FurinturaAdd_Valute" class="control-label">Валюта</label>
                                    <select id="FurinturaAdd_Valute" class="form-control input-sm">
                                        <option>RUB</option>
                                        <option>USD</option>
                                        <option>EUR</option>
                                        <option>UAH</option>
                                        <option>CNY</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="FurnituraAdd_Sum" class="control-label">Стоимость</label>
                                    <input id="FurnituraAdd_Sum" oninput="$(this).val(StrReplcae($(this).val()))" class="form-control input-sm">
                                </div>
                                <button onclick="Furnitura.Add()" class="btn btn-primary btn-sm">
                                    <span class="fa fa-plus"></span>
                                </button>
                            </div>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th>Наименование</th>
                                        <th>Валюта</th>
                                        <th>Стоимость</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="FurinturaAdd_Table"></tbody>
                            </table>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="Tab6">
                            <div class="form-inline">
                                <div class="form-group">
                                    <label for="GlassAdd_Name" class="control-label">Наименование</label>
                                    <input id="GlassAdd_Name" class="form-control input-sm">
                                </div>
                                <div class="form-group">
                                    <label for="GlassAdd_Sum" class="control-label">Стоимость ед.</label>
                                    <input id="GlassAdd_Sum" class="form-control input-sm">
                                </div>
                                <div class="form-group">
                                    <label for="GlassAdd_SumM2" class="control-label">Стоимость м<sup>2</sup></label>
                                    <input id="GlassAdd_SumM2" class="form-control input-sm">
                                </div>
                                <button onclick="Glases.Add()" class="btn btn-primary btn-sm">
                                    <span class="fa fa-plus"></span>
                                </button>
                            </div>
                            <table class="table table-responsive table-bordered dataTable">
                                <thead>
                                <tr>
                                    <th>Наименование</th>
                                    <th>Стоимость ед.</th>
                                    <th>Стоимость м<sup>2</sup></th>
                                    <th style="width: 50px;"></th>
                                    <th style="width: 50px;"></th>
                                </tr>
                                </thead>
                                <tbody id="GlassTable"></tbody>
                            </table>
                        </div>
                        <!-- Дополнительно -->
                        <div role="tabpanel" class="tab-pane" id="Tab7">
                            <div class="form-inline">
                                <div class="form-group">
                                    <label for="OtherAdd_Name" class="control-label">Наименование</label>
                                    <input id="OtherAdd_Name" class="form-control input-sm">
                                </div>
                                <div class="form-group">
                                    <label for="OtherAdd_Sum" class="control-label">Стоимость ед.</label>
                                    <input id="OtherAdd_Sum" class="form-control input-sm">
                                </div>
                                <div class="form-group">
                                    <label for="OtherAdd_SumM2" class="control-label">Стоимость м<sup>2</sup></label>
                                    <input id="OtherAdd_SumM2" class="form-control input-sm">
                                </div>
                                <button onclick="Other.Add()" class="btn btn-primary btn-sm">
                                    <span class="fa fa-plus"></span>
                                </button>
                            </div>
                            <table class="table table-responsive table-bordered dataTable">
                                <thead>
                                <tr>
                                    <th>Наименование</th>
                                    <th>Стоимость ед.</th>
                                    <th>Стоимость м<sup>2</sup></th>
                                    <th style="width: 50px;"></th>
                                    <th style="width: 50px;"></th>
                                </tr>
                                </thead>
                                <tbody id="OtherTable"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>