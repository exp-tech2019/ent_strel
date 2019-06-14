<script src="PageStockMain/Action.js"></script>
<table class="tree table table-hover" id="GoodList">
    <tr Type="Header" class="treegrid-100" style="font-weight: bold;">
        <td>Артуикул</td>
        <td>Наименование</td>
        <td>Штрихкод</td>
        <td>Ед. изм.</td>
        <td>Кол-во на складе</td>
        <td>Кол-во на производстве</td>
    </tr>
    <tr class="treegrid-2 treegrid-parent-1">
        <td colspan="4">Группа 1</td>
    </tr>
    <tr class="treegrid-3 treegrid-parent-2">
        <td>Node 1-2-1</td><td>Additional info</td>
    </tr>
</table>

<!-- Диалог инверторизации -->
<div class="modal fade" id="InvertoryDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Укажите кол-во</h4>
            </div>
            <div class="modal-body">
                <input id="InvertoryInp" class="form-control" placeholder="Введите кол-во">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button  onclick='Invertory.Save()' type="button" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>