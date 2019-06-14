<script src="scripts/TransferInStock_EditStart.js"></script>
<section class="content-header">
    <h1>
        Возврат
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <button onclick="window.location.href='?MVCPage=TransferInStock_List'" class="btn btn-danger">Открыть ранних возвращенцев</button>
        </div>
        <div class="box-body">
            <input type="hidden" id="idTransfer" value="<?php echo $_GET["idTransfer"]; ?>">
            <div class="form-horizontal">
                <div class="form-group">
                    <label for="TransferDate" class="col-xs-1 control-label">Дата возврата</label>
                    <div class="col-xs-2">
                        <input id="TransferDate" class="form-control" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <label for="LoginFIO" class="col-xs-1 control-label">Принял</label>
                    <div class="col-xs-2">
                        <input id="idLogin" type="hidden" value="<?php echo $_SESSION["idLogin"]; ?>">
                        <input id="LoginFIO" value="<?php echo $_SESSION["FIOLogin"]; ?>" class="form-control" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <label  for="WorkerFIO" class="col-xs-1 control-label">Возвращенец</label>
                    <input id="idWorker" type="hidden">
                    <input id="idDolgnost" type="hidden">
                    <div class="col-xs-2">
                        <input id="WorkerFIO" class="form-control" disabled>
                    </div>
                </div>
                <table class="table table-responsive table-hover">
                    <thead>
                    <tr>
                        <th>Номнелатура</th>
                        <th>Ед. изм.</th>
                        <th>На производстве</th>
                        <th>Кол-во</th>
                    </tr>
                    </thead>
                    <tbody id="GoodsTable">
                    </tbody>
                </table>
            </div>
        </div>
</section>