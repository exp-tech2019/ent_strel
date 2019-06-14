$(document).ready(function () {
    //Загрузим список ед имзерения
    for(var i in gl.ManualUnits)
        $("#GoodDialog_Unit").append(
            "<option value='"+i+"'>"+gl.ManualUnits[i]+"</option>"
        );

    GoodGroups_Table.Select();

    //Поиск
    $('#GoodsFind').keypress(function (e) {
        if (e.which == 13)
            GoodGroups_Table.FindGood();
    });
    $("#GoodsFindBtn").click(function(){
        GoodGroups_Table.FindGood();
    });
})
var GoodGroups_Table={
    "Select":function(){
        $("#GoodsTable tr").remove();
        $("#GoodDialog_GroupList option").remove();
        gl.Post(
            "GroupGood_Select",
            {},
            function(str){
                var o=JSON.parse(str);
                for(var i in o){
                    $("#GoodsTable").append(
                        "<tr idGroup='"+o[i].idGroup+"'>" +
                            "<td Type='Btn' onclick='GoodGroups_Table.Group_UPDown($(this))'><span class='glyphicon glyphicon-chevron-up' style='cursor: pointer;'></span> </td>"+
                            "<td Type='GroupName' colspan='5'>"+
                                "<span>"+o[i].GroupName+"</span>"+
                                "<span onclick='Groups_Dialog.EditStart(this)' class='glyphicon glyphicon-cog' style='margin-left: 20px; margin-right: 10px;cursor: pointer;'></span>"+
                                "<span onclick='Groups_Dialog.Remove(this)' class='glyphicon glyphicon-remove' style='cursor: pointer;'></span>"+
                            "</td>"+
                        "</tr>"
                    );
                    //Добавим в список диалога товаров
                    $("#GoodDialog_GroupList").append(
                        "<option VALUE='"+o[i].idGroup+"'>"+o[i].GroupName+"</option>"
                    )

                    var gd=o[i].GoodList;
                    for(var j in gd)
                        $("#GoodsTable").append(
                            "<tr idGroup='"+o[i].idGroup+"' idGood='"+gd[j].idGood+"'>" +
                                "<td></td>"+
                                "<td Type='GoodName' onclick='GoodDialog.EditStart($(this).parent())'>"+gd[j].GoodName+"</td>"+
                                "<td Type='Unit' onclick='GoodDialog.EditStart($(this).parent())'>"+gl.ManualUnits[ gd[j].Unit]+"</td>"+
                                "<td Type='Article' onclick='GoodDialog.EditStart($(this).parent())'>"+gd[j].Article+"</td>"+
                                "<td Type='Manufacturer' onclick='GoodDialog.EditStart($(this).parent())'>"+gd[j].Manufacturer+"</td>"+
                                "<td>" +
                                    "<span onclick='GoodDialog.EditStart($(this).parent().parent())' class='glyphicon glyphicon-cog' style='margin-right: 20px;cursor: pointer;'></span>"+
                                    "<span onclick='GoodDialog.Remove(this)' class='glyphicon glyphicon-remove' style='cursor: pointer;'></span>"+
                                "</td>"+
                            "</tr>"
                        );
                };
                $("#GoodsTable tr[idGood]").hide();
            }
        )
    },
    "Group_UPDown":function (el) {
        el=$(el).find("span");
        var TR=$(el).parent().parent();
        switch($(el).attr("class")){
            case "glyphicon glyphicon-chevron-up":
                $("#GoodsTable tr[idGroup="+TR.attr("idGroup")+"][idGood]").show();
                $(el).attr("class","glyphicon glyphicon-chevron-down");
                break;
            case "glyphicon glyphicon-chevron-down":
                $("#GoodsTable tr[idGroup="+TR.attr("idGroup")+"][idGood]").hide();
                $(el).attr("class","glyphicon glyphicon-chevron-up");
                break;
        }
    },
    "FindGood":function(){
        if($("#GoodsFind").val()!="") {
            var findStr = $("#GoodsFind").val().toLowerCase();
            for (var i = 0; i < $("#GoodsTable tr[idGood]").length; i++) {
                var TR = $("#GoodsTable tr[idGood]:eq(" + i + ")");
                switch (TR.find("td[Type=GoodName]").text().toLowerCase().indexOf(findStr) > -1) {
                    case true:
                        TR.show();
                        break;
                    case false:
                        TR.hide();
                        break;
                }

            }
            ;
        };
    }
}
var Groups_Dialog={
    "idGroup":-1,
    "Add":function () {
        Groups_Dialog.idGroup=-1;
        $("#GoodGroupsDialog_Name").val("");
        $("#GoodGroupsDialog_AutoSalvage").prop("checked",false);
        $("#GoodGroupsDialog_Step").val(-1);
        $("#GoodGroupsDialog").modal("show");
    },
    "EditStart":function (el) {
        var TR=$(el).parent().parent();
        Groups_Dialog.idGroup=TR.attr("idGroup");
        gl.Post(
            "Group_EditStart",
            {
                "idGroup":Groups_Dialog.idGroup
            },
            function(str){
                var o=JSON.parse(str);
                $("#GoodGroupsDialog_Name").val(o.GroupName);
                $("#GoodGroupsDialog_AutoSalvage").prop("checked", o.AutoSalvage==1? true : false);
                $("#GoodGroupsDialog_Step").val(o.Step);
                $("#GoodGroupsDialog").modal("show");
            }
        )
    },
    "Save":function () {
        var flagErr=false;
        flagErr=$("#GoodGroupsDialog_Name").val()==""? true : false;
        $("#GoodGroupsDialog_Name").parent().parent().attr("class", $("#GoodGroupsDialog_Name").val()=="" ? "form-group has-error" : "form-group");
        flagErr=$("#GoodGroupsDialog_Step").val()==-1? true : false;
        $("#GoodGroupsDialog_Step").parent().parent().attr("class", $("#GoodGroupsDialog_Step").val()-1 ? "form-group has-error" : "form-group");

        if(!flagErr)
            gl.Post(
                "Group_Save",
                {
                    "idGroup":Groups_Dialog.idGroup,
                    "GroupName":$("#GoodGroupsDialog_Name").val(),
                    "AutoSalvage":$("#GoodGroupsDialog_AutoSalvage").prop("checked") ? 1 : 0,
                    "Step":$("#GoodGroupsDialog_Step").val()
                },
                function(str){
                    var o=JSON.parse(str);
                    switch(o.Status){
                        case "Success":
                            $("#GoodGroupsDialog").modal("hide");
                            switch (Groups_Dialog.idGroup){
                                case -1:
                                    GoodGroups_Table.Select();
                                    break;
                                default:
                                    $("#GoodsTable tr[idGroup="+Groups_Dialog.idGroup+"] td[Type=GroupName] span:first()").text($("#GoodGroupsDialog_Name").val());
                                    break;
                            }
                            break;
                        case "Error":
                            alert("Ошибка "+o.Note);
                            break;
                    }
                }
            )
    },
    "Remove":function (el) {
        var TR=$(el).parent().parent();
        switch ($("#GoodsTable tr[idGroup="+TR.attr("idGroup")+"][idGood]").length){
            case 0:
                if(confirm("Удалить группу?")){
                    gl.Post(
                        "Group_Remove",
                        {
                            "idGroup":TR.attr("idGroup")
                        },
                        function (str) {
                            var o=JSON.parse(str);
                            switch (o.Status){
                                case "Success":
                                    TR.remove();
                                    break;
                                case "Error":
                                    alert(o.Note);
                                    break;
                            }
                        }
                    )
                }
                break;
            default:
                alert("Невозможно удалить группу, удалите товары в группе!");
                break;
        }
    }
}

var GoodDialog={
    "idGood":-1,
    "Add":function () {
        GoodDialog.idGood=-1;
        $("#GoodDialog_GroupList").val(-1);
        $("#GoodDialog_Name").val("");
        $("#GoodDialog_Article").val("");
        $("#GoodDialog_BarCode").val("");
        $("#GoodDialog_Manufacturer").val("");
        $("#GoodDialog_Unit").val(-1);
        $("#GoodDialog").modal("show");
    },
    "EditStart":function (el) {
        var TR=$(el);
        GoodDialog.idGood=TR.attr("idGood");
        gl.Post(
            "Good_EditStart",
            {
                "idGood":GoodDialog.idGood
            },
            function(str){
                var o=JSON.parse(str);
                $("#GoodDialog_Name").val(o.GoodName);
                $("#GoodDialog_GroupList").val(o.idGroup);
                $("#GoodDialog_Article").val(o.Article);
                $("#GoodDialog_BarCode").val(o.BarCode);
                $("#GoodDialog_Manufacturer").val(o.Manufacturer);
                $("#GoodDialog_Unit").val(o.Unit);
                $("#GoodDialog").modal("show");
            }
        )
    },
    "Save":function () {
        var flagErr=false;
        flagErr= $("#GoodDialog_Name").val()=="" ? true : flagErr;
        flagErr= $("#GoodDialog_GroupList").val()==-1 ? true : flagErr;
        flagErr= $("#GoodDialog_Unit").val()==-1 ? true : flagErr;
        switch (flagErr){
            case false:
                gl.Post(
                    "Good_Save",
                    {
                        "idGood":GoodDialog.idGood,
                        "GoodName":$("#GoodDialog_Name").val(),
                        "idGroup":$("#GoodDialog_GroupList").val(),
                        "Article":$("#GoodDialog_Article").val(),
                        "BarCode":$("#GoodDialog_BarCode").val(),
                        "Manufacturer":$("#GoodDialog_Manufacturer").val(),
                        "Unit":$("#GoodDialog_Unit").val()
                    },
                    function(str){
                        var o=JSON.parse(str);
                        switch (o.Status){
                            case "Success":
                                switch (GoodDialog.idGood){
                                    case -1:
                                        GoodGroups_Table.Select();
                                        break;
                                    default:
                                        var TR=$("#GoodsTable tr[idGood="+GoodDialog.idGood+"]");
                                        TR.find("td[Type=GoodName]").text($("#GoodDialog_Name").val());
                                        TR.find("td[Type=Article]").text($("#GoodDialog_Article").val());
                                        TR.find("td[Type=Manufacturer]").text($("#GoodDialog_Manufacturer").val());
                                        TR.find("td[Type=Unit]").text(gl.ManualUnits[$("#GoodDialog_Unit").val()]);
                                        break;
                                };
                                $("#GoodDialog").modal("hide");
                                break;
                            case "Error":
                                alert(o.Note);
                                break;
                        }
                    }
                )
                break;
            case true:
                alert("Заполненны не все поля");
                break;
        }
    },
    "Remove":function (el) {
        var TR=$(el).parent().parent();
        if(confirm("Удалить номенклатуру?"))
            gl.Post(
                "Good_Remove",
                {
                    "idGood":TR.attr("idGood")
                },
                function (str) {
                    var o=JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            TR.remove();
                            break;
                        case "Error":
                            alert(o.Note);
                                break;
                    }
                }
            )
    }
}