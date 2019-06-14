<script src="Scripts/Spe_OrderOne.js"></script>
<section class="content-header">
    <h1>
        Заказ № <b><span id="Blank"></span></b> от <span id="BlankDate"></span>
        счет <b><span id="Shet"></span></b>
        Заказчик: <span id="Zakaz"></span>
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <button id="BtnCalc" class="btn btn-primary">Расчитать</button>
            <br>
            <div class="form-inline">
                <div class="form-group">
                    <label class="sr-only" for="DoorFindNumPP">Позиция</label>
                    <input class="form-control" id="DoorFindNumPP" placeholder="Позиция">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="DoorFindTypeDoor">Тип двери</label>
                    <select id="DoorFindTypeDoor" class="form-control">
                        <option value=""></option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="sr-only" for="DoorFindH">Высота</label>
                    <input class="form-control" id="DoorFindH" placeholder="Высота">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="DoorFindW">Ширина</label>
                    <input class="form-control" id="DoorFindW" placeholder="Ширина">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="DoorFindOpen">Открывание</label>
                    <select id="DoorFindOpen" class="form-control">
                        <option value=""></option>
                        <option value="Лев.">Лев.</option>
                        <option value="Прав.">Прав.</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="box-body">
            <input type="hidden" id="idOrder" value="<?php echo $_GET["idOrder"]; ?>">
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th></th>
                        <th>№ п/п</th>
                        <th>Тип</th>
                        <th>Кол-во</th>
                        <th>Размеры</th>
                        <th>Открывание</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="DoorTable"></tbody>
            </table>
        </div>
    </div>
</section>

<!--Диалог групп-->
<div id="GroupDialog" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <input id="GroupDialogFind" class="form-control" placeholder="Поиск">
            </div>
            <div class="modal-body">
                <p>
                    <table class="table table-responsive table-hover">
                        <thead>
                            <tr>
                                <th>Наименование</th>
                            </tr>
                        </thead>
                        <tbody id="GroupDialogTable"></tbody>
                    </table>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button onclick="GroupDialog.Close()" type="button" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<!--Диалог номенклатуры-->
<div id="GoodDialog" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <input id="GoodDialogFind" class="form-control" placeholder="Поиск">
            </div>
            <div class="modal-body">
                <p>
                <table class="table table-responsive table-hover">
                    <thead>
                    <tr>
                        <th>Наименование</th>
                    </tr>
                    </thead>
                    <tbody id="GoodDialogTable"></tbody>
                </table>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button onclick="GoodDialog.Save()" type="button" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>