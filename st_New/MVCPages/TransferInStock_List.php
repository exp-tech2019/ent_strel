<script src="scripts/TransferInStock_List.js"></script>
<section class="content-header">
    <h1>
        Возврат
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <div class="input-group" style="width: 300px; float: left; margin-right: 20px;">
                <input id="Find" placeholder="Поиск" type="text" class="form-control" aria-describedby="basic-addon2">
                <span id="FindBtn" class="input-group-addon" style="cursor: pointer;">
                    <span class="glyphicon glyphicon-search"></span>
                </span>
            </div>
        </div>
        <div class="box-body">
            <table id="Table" class="table table-responsive table-hover datagrid"></table>
            <nav aria-label="Page navigation">
                <ul class="pagination" id="Pagination">
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