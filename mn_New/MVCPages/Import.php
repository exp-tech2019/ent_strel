<script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<link rel="stylesheet" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
<script src="scripts/import.js"></script>
<section class="content-header">
    <h1>
        Импорт
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Гдавная</a></li>
        <li class="active"><a href="#">Импорт</a></li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    Выберите файл
                    <input type="file" id="Upload" style="width: 100px;">
                    <button onclick="ImportTable.Save()" class="btn btn-sm btn-success">Сохранить</button>
                </div>
                <div id="bb" class="box-body">
                    <div class="panel panel-default">
                        <div class="panel-body form-inline">
                            <div class="form-group">
                                <label for="Shet">Счет</label>
                                <input id="Shet" class="form-control input-sm">
                            </div>
                            <div class="form-group">
                                <label for="ShetDate">Дата</label>
                                <input id="ShetDate" class="form-control input-sm">
                            </div>
                            <div class="form-group">
                                <label for="OrgName">Заказчик</label>
                                <input id="idClient" type="hidden">
                                <input id="OrgName" onclick="ClientDlg.Open()" class="form-control input-sm">
                            </div>
                        </div>
                    </div>
                    <table class="table table-responsive table-hover">
                        <thead>
                            <tr>
                                <th rowspan="2"></th>
                                <th rowspan="2">№</th>
                                <th rowspan="2">Наименование</th>
                                <th rowspan="2">Кол-во</th>
                                <th rowspan="2">Высота</th>
                                <th rowspan="2">Ширина</th>
                                <th rowspan="2">Открывание</th>
                                <th rowspan="2">Раб ств</th>
                                <th rowspan="2">Окрас</th>
                                <th rowspan="2">Наличник</th>
                                <th rowspan="2">Доводчик</th>
                                <th colspan="2">Навесы</th>
                                <th colspan="2">Стекло</th>
                                <th colspan="2">Решетка</th>
                                <th colspan="2">Фрамуга</th>
                                <th rowspan="2">Примечание</th>
                                <th rowspan="2">Шильда</th>
                                <th rowspan="2">Маркировка</th>
                            </tr>
                            <tr>
                                <th>раб ств</th>
                                <th>2 ств</th>
                                <th>раб ств</th>
                                <th>2 ств</th>
                                <th>раб ств</th>
                                <th>2 ств</th>
                                <th>Наличие</th>
                                <th>Высота</th>
                            </tr>
                        </thead>
                        <tbody id="ImportTable"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="ClientDlg" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <input id="ClientDlgFind" class="form-control input-sm" placeholder="Поиск">
                </h4>
            </div>
            <div class="modal-body">
                <table id="ClientDlgTable" class="table table-responsive table-hover"></table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button onclick="ClientDlg.Close()" type="button" class="btn btn-primary">Выбрать</button>
            </div>
        </div>
    </div>
</div>

<div id="DialogNoteView" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Примечание</h4>
            </div>
            <div class="modal-body" id="DialogNoteView_Text">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
        </div>
    </div>
</div>