<script src="scripts/TransferInEnt_EditStart.js"></script>
<section class="content-header">
    <h1>
        Выдача сотруднику
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <button onclick="window.location.href='?MVCPage=TransferInEnt_List'" class="btn btn-danger">Открыть ранние выдачи</button>
        </div>
        <div class="box-body">
            <input type="hidden" id="idTransfer" value="<?php echo $_GET["idTransfer"]; ?>">
            <div class="form-horizontal">
                <div class="form-group">
                    <label for="TransferDate" class="col-xs-1 control-label">Дата выдачи</label>
                    <div class="col-xs-2">
                        <input id="TransferDate" class="form-control" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <label for="LoginFIO" class="col-xs-1 control-label">Выдал</label>
                    <div class="col-xs-2">
                        <input id="idLogin" type="hidden" value="<?php echo $_SESSION["idLogin"]; ?>">
                        <input id="LoginFIO" value="<?php echo $_SESSION["FIOLogin"]; ?>" class="form-control" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <label  for="WorkerFIO" class="col-xs-1 control-label">Получил</label>
                    <input id="idWorker" type="hidden">
                    <input id="idDolgnost" type="hidden">
                    <div class="col-xs-2">
                        <input id="WorkerFIO" class="form-control" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <label for="NaryadInp" class="col-xs-1 control-label">Наряд</label>
                    <div class="col-xs-2">
                        <div class="input-group">
                            <input id="idNaryad" type="hidden">
                            <input id="NaryadInp" class="form-control" disabled>
                            <span id="NaryadBtn" class="input-group-addon" style="cursor: pointer;">
                                <span class="glyphicon glyphicon-plus"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <table class="table table-responsive table-hover">
                    <thead>
                    <tr>
                        <th>Номнелатура</th>
                        <th>Ед. изм.</th>
                        <th>На складе</th>
                        <th>Требуется по наряду</th>
                        <th>Выдано ранее</th>
                        <th>Кол-во</th>
                    </tr>
                    </thead>
                    <tbody id="GoodsTable">
                    </tbody>
                </table>
            </div>
        </div>
</section>