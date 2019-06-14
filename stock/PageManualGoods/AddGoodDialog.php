<div class="modal fade" id="AddGoodDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Номеклатура</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="AddGoodID">
                <input type="hidden" id="AddGoodIDGroup">
                <div class="input-group">
                    <span class="input-group-addon">Группа</span>
                    <input class="form-control" id="AddGoodGroup" disabled>
                </div>
                <div class="input-group">
                    <span class="input-group-addon">Наименование</span>
                    <input class="form-control" id="AddGoodName">
                </div>
                <br>
                <div class="input-group">
                    <span class="input-group-addon">Ед. Измер.</span>
                    <select class="form-control" id="AddGoodUnit">
                        <option value="1">шт</option>
                        <option value="2">кг</option>
                        <option value="3">М<sup>2</sup></option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" onclick="SaveGood()">Сохранить</button>
            </div>
        </div>
    </div>
</div>
<script>
    function AddGood(el){
        var Group=$(el).parent();
        var idGroup=Group.attr("idGroup");
        var GroupName=Group.find("span[Type=GoodName]").text();
        $("#AddGoodID").val("");
        $("#AddGoodIDGroup").val(idGroup);
        $("#AddGoodGroup").val(GroupName);
        $("#AddGoodName").val("");
        $("#AddGoodUnit").val(1);
        $("#AddGoodDialog").modal("show");
    }
    function SaveGood() {
        $.post(
            "PageManualGoods/AddGoodSave.php",
            {
                "Action":$("#AddGoodID").val()=="" ? "Add" : "Edit",
                "id":$("#AddGoodID").val(),
                "idGroup":$("#AddGoodIDGroup").val(),
                "Name": $("#AddGoodName").val(),
                "Unit": $("#AddGoodUnit").val()
            },
            function (data){
                if(data==""){
                    window.location.href="index.php?MVCPage=PageManualGoods";
                }
                else
                    console.log(data);
            }
        )
    }
    function EditStartGood(el) {
        var TR=$(el).parent().parent();
        $("#AddGoodID").val(TR.attr("idGood"));
        $("#AddGoodIDGroup").val(TR.attr("idGroup"));
        $("#AddGoodGroup").val(TR.parent().parent().parent().parent().find("div:eq(0) span[Type=GoodName]").text());
        $("#AddGoodName").val(TR.find("td[Type=Name]").text());
        $("#AddGoodUnit").val(TR.find("td[Type=Unit]").attr("UnitNum"));
        $("#AddGoodDialog").modal("show");
    }
    function RemoveGood(el){
        var TR=$(el).parent().parent();
        var idGood=TR.attr("idGood");
        var GoodName=TR.find("td[Type=Name]").text();
        if(confirm("Удалить товар: "+GoodName+" ?"))
            $.post(
                "PageManualGoods/AddGoodSave.php",
                {
                    "Action":"Remove",
                    "id":idGood,
                    "idGroup":"",
                    "Name": GoodName,
                    "Unit": ""
                },
                function (data){
                    if(data==""){
                        window.location.href="index.php?MVCPage=PageManualGoods";
                    }
                    else
                        console.log(data);
                }
            )
    }
</script>