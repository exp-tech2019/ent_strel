<!-- Диалог справоочника номеклатуры -->
<script src="CommonDialog/ManualGoods.js"></script>
<div class="modal fade" id="g_Dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Номеклатура</h4>
            </div>
            <div class="modal-body">
                <button class="btn btn-primary" onclick="g_Select()">Обеовить</button>
                <button class="btn btn-primary" onclick="window.open('index.php?MVCPage=PageGoods','s')">Справочник</button>
                <table class="tree table table-hover" id="g_GoodList">
                    <tr Type="Header" class="treegrid-100" style="font-weight: bold;">
                        <td>Артуикул</td>
                        <td>Наименование</td>
                        <td>Штрихкод</td>
                        <td>Ед. изм.</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button onclick="g_CloseDialog()" type="button" class="btn btn-primary">Выбрать</button>
            </div>
        </div>
    </div>
</div>