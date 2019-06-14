<script src="scripts/Customer_List.js"></script>
<section class="content-header">
    <h1>
        Поставщики
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <div class="input-group" style="width: 300px; float: left; margin-right: 20px;">
                <input id="CustomerFind" type="text" class="form-control" placeholder="Поиск" aria-describedby="basic-addon2">
                <span id="CustomerFindBtn" class="input-group-addon" style="cursor: pointer;">
                    <span class="glyphicon glyphicon-search"></span>
                </span>
            </div>
            <button onclick="Customers.Add()" class="btn btn-primary">Добавить</button>
        </div>
        <div class="box-body">
            <table class="table table-responsive table-hover">
                <thead>
                    <tr>
                        <th>Наименование</th>
                        <th>ИНН</th>
                        <th>Телефон</th>
                        <th>eMail</th>
                        <th>Адрес</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="CustomerTable"></tbody>
            </table>
            <nav aria-label="Page navigation">
                <ul class="pagination" id="CustomerTablePagination">
                    <li Btn="Back" onclick="CustomerTable.PageBack()">
                        <a href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <li Btn="Next" onclick="CustomerTable.PageNext()">
                        <a href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="box-footer">
        </div>
    </div>
</section>

<!-- Диалог Поставщика -->
<div id="CustomerDialog" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Поставщик</h4>
            </div>
            <div class="modal-body">
                <p>
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Наименование:</label>
                            <div class="col-lg-8">
                                <input id="CustomerDialog_Name" class="form-control danger">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">ИНН:</label>
                            <div class="col-lg-8">
                                <input id="CustomerDialog_INN" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Телефон:</label>
                            <div class="col-lg-8">
                                <input id="CustomerDialog_Phone" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">eMail:</label>
                            <div class="col-lg-8">
                                <input id="CustomerDialog_Mail" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Адрес:</label>
                            <div class="col-lg-8">
                                <input id="CustomerDialog_Adress" class="form-control">
                            </div>
                        </div>
                    </div>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button onclick="Customers.Save()" type="button" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>