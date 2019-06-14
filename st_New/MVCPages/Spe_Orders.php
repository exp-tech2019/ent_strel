<script src="Scripts/Spe_Orders.js"></script>
<section class="content-header">
    <h1>
        Заказы
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <div class="input-group" style="width: 300px; float: left; margin-right: 20px;">
                <input id="OrderFind" type="text" class="form-control" placeholder="Поиск номенклатуры" aria-describedby="basic-addon2">
                <span id="OrderFindBtn" class="input-group-addon" style="cursor: pointer;">
                    <span class="glyphicon glyphicon-search"></span>
                </span>
            </div>
        </div>
        <div class="box-body">
            <table id="OrderTable" class="table table-responsive table-hover"></table>
            <nav aria-label="Page navigation">
                <ul class="pagination" id="OrderPagination">
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