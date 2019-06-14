<script src="Scripts/StockList.js"></script>
<section class="content-header">
    <h1>
        Склад
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
        </div>
        <div class="box-body">
            <table id="StockTable" class="table table-responsive table-hover"></table>
            <nav aria-label="Page navigation">
                <ul class="pagination" id="StockPagination">
                    <li Btn="Back">
                        <a href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <li Btn="Next">
                        <a href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</section>

<!--Списать продукцию-->
<div id="WriteOfDialog" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <input type="hidden" id="WriteOfID">
                <input type="hidden" id="WriteOfTypeStock">
                <div class="row">
                    <div class="col-xs-6">Материал</div>
                    <div class="col-xs-6" id="WriteOfGoodName"></div>
                </div>
                <br>
                <div class="row">
                    <div class="col-xs-6">На складе</div>
                    <div class="col-xs-6" id="WriteOfCountStock"></div>
                </div>
                <br>
                <div class="row">
                    <div class="col-xs-6">Остаток</div>
                    <div class="col-xs-6">
                        <input id="WriteOfCount" oninput="gl.ReplaceDot(this)" value="0" style="width: 100%;">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button onclick="WriteOf.End()" type="button" class="btn btn-primary">Списать</button>
            </div>
        </div>
    </div>
</div>