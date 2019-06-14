<script src="PageMoveDock/ManualSupplier.js"></script>

<div class="modal fade bs-example-modal-lg" id="ManualSupplierDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Поставщики <button onclick="SupplierAdd()" class="btn btn-primary">Добавить постащика</button></h4>
            </div>
            <div class="modal-body">
                <div class="panel">
                    <div class="panel-body">
                        <div class="form-inline">
                            <div class="form-group">
                                <label>Наименование</label>
                                <input oninput="SupplierFind()" id="ManualSupplierFindName" class="form-control" style="width: 200px; clear: none;" placeholder="Поиск по Наименованию">
                            </div>
                            <div class="form-group">
                                <label>ИНН</label>
                                <input oninput="SupplierFind()" id="ManualSupplierFindINN" class="form-control" style="width: 200px; clear: none;" placeholder="Поиск по ИНН">
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-responsive table-bordered">
                    <thead>
                        <tr>
                            <th>Поставщик</th>
                            <th>ИНН</th>
                        </tr>
                    </thead>
                    <tbody id="ManualSupplierTable">
                        <?php
                            $d=$m->query("SELECT * FROM StockNewSupplier ORDER BY NAME ");
                            if($d->num_rows)
                                while($r=$d->fetch_assoc()){ ?>
                                    <tr idSupplier="<?php echo $r["id"]; ?>">
                                        <td>
                                            <span onclick="SupplierEditStart(this)" class="glyphicon glyphicon-edit"></span>
                                        </td>
                                        <td Type="Name"><?php echo $r["Name"]; ?></td>
                                        <td Type="INN"><?php echo $r["INN"]; ?></td>
                                    </tr>
                                <?php }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<!-- Дилог добавления, редктирования Поставщика -->
<div class="modal fade bs-example-modal-lg" id="ManualSupplierAddDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="panel">
                    <div class="panel-body">
                        <input type="hidden" id="ManualSupplierAddID">
                        <div class="form-group">
                            <label>Наименование</label>
                            <input id="ManualSupplierAddName" class="form-control"  placeholder="Поиск по Наименованию">
                        </div>
                        <div class="form-group">
                            <label>ИНН</label>
                            <input id="ManualSupplierAddINN" class="form-control" placeholder="Поиск по ИНН">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button onclick="SupplierSave()" type="button" class="btn btn-primary">Добавить</button>
            </div>
        </div>
    </div>
</div>