<script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<link rel="stylesheet" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
<script src="scripts/ClientList.js"></script>
<section class="content-header">
    <h1>
        Список клиентов
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Гдавная</a></li>
        <li class="active"><a href="#">Клиенты</a></li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <div class="form-group col-md-6">
                        <input id="ClientFind" class="form-control" placeholder="Поиск">
                    </div>
                </div>
                <div id="bb" class="box-body">
                    <button class="btn btn-success" onclick="AddRow()">Добавить</button>
                    <table id="ClientTable" class="table table-responsive table-hover"></table>
                </div>
            </div>
        </div>
    </div>
</section>