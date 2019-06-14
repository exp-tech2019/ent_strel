<script src="scripts/ActArrivalList.js"></script>
<input id="idArrival" type="hidden" value="<?php echo isset($_GET["idArrival"]) ? $_GET["idArrival"] : "-1"; ?>">
<section class="content-header">
    <h1>
        Поступление
    </h1>
</section>
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <input id="ArrivalFind" class="form-control" placeholder="Поиск" style="width: 400px; float: left; border-radius: 15px;">
            <button id="ArrivalFinBtn" class="btn btn-primary">
                <span class="glyphicon glyphicon-search"></span>
            </button>
            <a href="?MVCPage=ActArrivalOne">
                <button class="btn btn-success">Новое поступление</button>
            </a>
        </div>
        <div class="box-body">
            <table class="table table-responsive table-hover">
                <thead>
                    <tr>
                        <th>№ ТТН</th>
                        <th>Дата ТТН</th>
                        <th>Поставщик</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody id="ArrivalTable"></tbody>
            </table>
            <nav aria-label="Page navigation">
                <ul class="pagination" id="ArrivalTablePagination">
                    <li Btn="Back" onclick="ArrivalTable.PageBack()">
                        <a href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <li Btn="Next" onclick="ArrivalTable.PageNext()">
                        <a href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</section>