<script src="scripts/OrderOne.js"></script>
<section class="content-header">
    <h1>
        Заказ
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Гдавная</a></li>
        <li class="active"><a href="#">Заказ</a></li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <div class="form-group col-md-6">
                        <button class="btn btn-success btn-sm">Сохранить</button>
                        <button class="btn btn-danger btn-sm">Отмена</button>
                    </div>
                </div>
                <div id="bb" class="box-body">
                    <div class="form-horizontal">
                        <div class="form-group row">
                            <label for="Blank" class="col-md-1">Заказ</label>
                            <div class="col-md-1">
                                <input id="Blank" class="form-control input-sm">
                            </div>
                            <label for="BlankDate" class="col-md-1" style="width: 20px;">от</label>
                            <div class="col-md-1">
                                <input id="BlankDate" class="form-control input-sm">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="Shet" class="col-md-1">Счет</label>
                            <div class="col-md-1">
                                <input id="Shet" class="form-control input-sm">
                            </div>
                            <label for="ShetDate" class="col-md-1" style="width: 20px;">от</label>
                            <div class="col-md-1">
                                <input id="ShetDate" class="form-control input-sm">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="OrgName" class="col-md-1">Заказчик</label>
                            <div class="col-md-4">
                                <input id="idClient" type="hidden">
                                <input id="OrgName" class="form-control input-sm">
                            </div>
                        </div>
                        <div class="form-group form-group-sm row">
                            <label for="DateBy" class="col-md-1">Срок изготавления</label>
                            <div class="col-md-1">
                                <input id="DateBy" class="form-control input-sm">
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">
                                    <button type="button" class="btn btn-primary btn-sm">
                                        <span class="fa fa-plus"></span>
                                        Позицию
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm">
                                        <span class="fa fa-dollar"></span>
                                        Расчитать
                                    </button>
                            </h3>
                        </div>
                        <div class="box-body table-responsive pad">
                            <table class="table table-hover table-responsive">
                                <thead>
                                <tr class="info">
                                    <th rowspan="2">№</th>
                                    <th rowspan="2">Тип двери</th>
                                    <th rowspan="2">Кол-во</th>
                                    <th colspan="2" style="text-align: center">Размеры</th>
                                    <th rowspan="2">Открыв.</th>
                                    <th rowspan="2">Раб. ств.</th>
                                    <th rowspan="2">RAL</th>
                                    <th rowspan="2">Наличник</th>
                                    <th rowspan="2">Доводчик</th>
                                    <th rowspan="2">Маркировка</th>
                                    <th rowspan="2">Шильда</th>
                                    <th colspan="2" style="text-align: center">Навесы</th>
                                    <th colspan="2" style="text-align: center">Окна</th>
                                    <th colspan="2" style="text-align: center">Фрамуга</th>
                                    <th colspan="3" style="text-align: center">Стоимость</th>
                                    <th rowspan="2"></th>
                                </tr>
                                <tr class="info">
                                    <th>Высота</th>
                                    <th>Ширина</th>
                                    <th>Раб. ств.</th>
                                    <th>2ая ств.</th>
                                    <th>Раб. ств.</th>
                                    <th>2ая ств.</th>
                                    <th>Раб. ств.</th>
                                    <th>2ая ств.</th>
                                    <th>Скидка<br>(%)</th>
                                    <th>Цена<br>ед.</th>
                                    <th>Сумма</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>