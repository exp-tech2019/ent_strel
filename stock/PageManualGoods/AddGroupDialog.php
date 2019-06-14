<div class="modal fade" id="AddGroupDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Введите наименование группыe</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="AddGroupInpID">
                <div class="input-group">
                    <span class="input-group-addon">Наименование</span>
                    <input class="form-control" id="AddGroupInpName">
                </div>
                <br>
                <div class="input-group">
                    <span class="input-group-addon">Стадия списания</span>
                    <select class="form-control" id="AddGroupInpStep">
                        <option value="1">Лазер</option>
                        <option value="2">Гибка</option>
                        <option value="3">Сварка</option>
                        <option value="4">Рамка</option>
                        <option value="5">Сборка</option>
                        <option value="6">Покраска</option>
                        <option value="7">Упаковка</option>
                        <option value="8">Погрузка</option>
                        <option value="10">МДФ</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" onclick="SaveGroup()">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<script>
    function AddGroup() {
        $("#AddGroupInpID").val("");
        $("#AddGroupInpName").val("");
        $("#AddGroupInpStep").val(1);
        $("#AddGroupDialog").modal("show");
    }
    function GroupEditStart(el) {
        var Group=$(el).parent();
        $("#AddGroupInpID").val(Group.attr('idGroup'));
        $("#AddGroupInpName").val(Group.find("span[Type=GoodName]").text());
        $("#AddGroupInpStep").val(Group.attr('Step'));
        $("#AddGroupDialog").modal("show");
    }
    function SaveGroup(){
        $.post(
            "PageManualGoods/AddGroupSave.php",
            {
                "Action":$("#AddGroupInpID").val()=="" ? "Add" : "Edit",
                "id":$("#AddGroupInpID").val(),
                "Name": $("#AddGroupInpName").val(),
                "Step": $("#AddGroupInpStep").val()
            },
            function (data){
                if(data==""){
                    window.location.href="index.php?MVCPage=PageManualGoods";
                    $("#AddGroupDialog").modal("hide");
                }
                else
                    console.log(data);
            }
        )
    }
    function RemoveGroup(el){
        var Group=$(el).parent();
        var idGroup=Group.attr('idGroup');
        var Step=Group.attr('Step');
        var GoodName=Group.find("span[Type=GoodName]").text();
        if(confirm("Удалить группу: "+GoodName+" ?"))
            $.post(
                "PageManualGoods/AddGroupSave.php",
                {
                    "Action":"Remove",
                    "id":idGroup,
                    "Name": GoodName,
                    "Step": Step
                },
                function (data){
                    if(data==""){
                        Group.parent().remove();
                    }
                    else
                        console.log(data);
                }
            )
    }
</script>