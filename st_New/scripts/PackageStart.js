$(document).ready(function () {
    //Для диалога поиска заказа сфоримруем список годов
    for(let i=2017;i<=new Date().getFullYear();i++)
        $("#OrderDialog_Years").append(
            "<option value='"+i+"' "+(i==new Date().getFullYear() ? "selected" : "")+">"+i+"</option>"
        );
})

var OrderDialog={
    Open:function () {
        $("#OrderDialog").modal("show");
    },
    Select:function () {
        $("#OrderDialog_Table tr").remove();
        gl.Post(
            "Package_OrderSelect",
            {
                Year:$("#OrderDialog_Years").val(),
                Shet:$("#OrderDialog_FindShet").val()
            },
            function (str) {
                var o=JSON.parse(str);
                for(let i in o) {
                    $("#OrderDialog_Table").append(
                        "<tr idOrder='" + o[i].idOrder + "' class='success'>" +
                            "<td><span class='glyphicon glyphicon-chevron-down'></span> </td>" +
                            "<td Type='Blank'>" + o[i].Blank + "</td>" +
                            "<td Type='BlankDate'>" + o[i].BlankDate + "</td>" +
                            "<td Type='Shet'>" + o[i].Shet + "</td>" +
                            "<td Type='Zakaz'>" + o[i].Zakaz + "</td>" +
                            "<td colspan='2'>" + OrderDialog.StatusList[o[i].Status] + "</td>" +
                            "<td></td>" +
                        "</tr>"
                    );
                    let d=o[i].Doors;
                    for(let j in d)
                        $("#OrderDialog_Table").append(
                            "<tr OrderID='"+o[i].idOrder+"' idDoor='"+d[j].idDoor+"'>" +
                                "<td></td>"+
                                "<td Type='NumPP'>"+d[j].NumPP+"</td>"+
                                "<td Type='Name'>"+d[j].Name+"</td>"+
                                "<td Type='Size'>"+d[j].Size+"</td>"+
                                "<td Type='Open'>"+d[j].Open+"</td>"+
                                "<td Count>"+d[j].Count+"</td>"+
                                "<td CountShpt>"+d[j].CountShpt+"</td>"+
                                "<td Type='CountShpt'><input class='form-control' value=''></td>"+
                            "</tr>"
                        );
                };
                $("#OrderDialog_Table tr[idOrder] span").click(function(){
                    var idOrder=$(this).parent().parent().attr("idOrder");
                    switch ($(this).attr("class")){
                        case "glyphicon glyphicon-chevron-up":
                            $(this).attr("class","glyphicon glyphicon-chevron-down");
                            $("#OrderDialog_Table tr[OrderID="+idOrder+"]").show();
                            break;
                        case "glyphicon glyphicon-chevron-down":
                            $(this).attr("class","glyphicon glyphicon-chevron-up");
                            $("#OrderDialog_Table tr[OrderID="+idOrder+"]").hide();
                            break;
                    }
                })
            }
        )
    },
    StatusList:{
        1:"В работе",
        2:"На отгрузке",
        3:"Выполнен",
        4:"Отгружен"
    },
    Close:function () {
        var TRList=[];
        var FlagErr=false;
        for(let i=0;i<$("#OrderDialog_Table tr[idDoor]").length;i++) {
            var TR = $("#OrderDialog_Table tr[idDoor]:eq(" + i + ")");
            TR.find("td[Type=CountShpt]").removeAttr("class");
            if(TR.find("td[Type=CountShpt] input").val()!="" & TR.find("td[Type=CountShpt] input").val()!="")
                switch (parseInt(TR.find("td[CountShpt]").text())>=parseInt(TR.find("td[Type=CountShpt] input").val())){
                    case true:
                        TR.find("td[Type=CountShpt]").attr("class","has-success");
                        TRList.push({
                            idOrder:TR.attr("OrderID"),
                            Blank:$("#OrderDialog_Table tr[idOrder="+TR.attr("OrderID")+"] td[Type=Blank]").text(),
                            Shet:$("#OrderDialog_Table tr[idOrder="+TR.attr("OrderID")+"] td[Type=Shet]").text(),
                            idDoor:TR.attr("idDoor"),
                            NumPP:TR.find("td[Type=NumPP]").text(),
                            Name:TR.find("td[Type=Name]").text(),
                            Size:TR.find("td[Type=Size]").text(),
                            Open:TR.find("td[Type=Open]").text(),
                            CountShptMax:TR.find("td[CountShpt]").text(),
                            CountShpt:TR.find("td[Type=CountShpt] input").val()
                        });
                        break;
                    case false:
                        TR.find("td[Type=CountShpt]").attr("class","has-error");
                        FlagErr=true;
                        break;
                }
        };
        if(!FlagErr) {
            //Составим массив idDoor для ранее добавленных позиций
            var idDoors=[];
            for(var i=0;i<$("#OrderTable tr").length;i++)
                idDoors.push($("#OrderTable tr:eq("+i+")").attr("idDoor"));
            //Добавим или изменим кол-во в основной таблице
            for (let i in TRList)
                switch (idDoors.indexOf(TRList[i].idDoor) == -1) {
                    case true:
                        OrderTable.AddTR(
                            TRList[i].idOrder,
                            TRList[i].idDoor,
                            -1,
                            "Add",
                            {
                                Blank: TRList[i].Blank,
                                Shet: TRList[i].Shet,
                                NumPP: TRList[i].NumPP,
                                Name: TRList[i].Name,
                                Size: TRList[i].Size,
                                Open: TRList[i].Open,
                                CountShptMax: TRList[i].CountShptMax,
                                CountShpt: TRList[i].CountShpt
                            }
                        );
                        break;
                    case false:
                        let TR = $("#OrderTable tr[idDoor=" + TRList[i].idDoor + "]");
                        var inp = TR.find("td[CountShptMax] input");
                        inp.val(parseInt(inp.val()) + parseInt(TRList[i].CountShpt));
                        switch (TR.attr("Status")) {
                            case "Load":
                                TR.attr("Status", "Edit");
                                break;
                            case "Remove":
                                TR.attr("Status", "Edit");
                                TR.show();
                                break;
                        }
                        break;
                }
        };
    }
}

var OrderTable={
    Edit:function (el) {
        var TR=$(el).parent().parent();
        if(TR.attr("Status")=="Load")
            TR.attr("Status","Edit");
    },
    Remove:function (el) {
        var TR=$(el).parent().parent();
        switch (TR.attr("Status")){
            case "Add":
                TR.remove();
                break;
            default:
                TR.attr("Status","Remove");
                TR.hide();
                break;
        }
    },
    AddTR:function (idOrder, idDoor, idPackageDoor, Status, ParamRow) {
        $("#OrderTable").append(
            "<tr idOrder='" + idOrder + "' idDoor='" + idDoor + "' idPackageDoor='"+idPackageDoor+"' Status='"+Status+"'>" +
                "<td>" + ParamRow.Blank + "</td>" +
                "<td>" + ParamRow.Shet + "</td>" +
                "<td>" + ParamRow.NumPP + "</td>" +
                "<td>" + ParamRow.Name + "</td>" +
                "<td>" + ParamRow.Size + "</td>" +
                "<td>" + ParamRow.Open + "</td>" +
                "<td CountShptMax='" + ParamRow.CountShptMax + "'>" +
                    "<input oninput='OrderTable.Edit(this)' value='" + ParamRow.CountShpt + "' class='form-control'></td>" +
                "<td>" +
                    "<span onclick='OrderTable.Remove(this)' class='glyphicon glyphicon-remove'></span> "+
                "</td>" +
            "</tr>"
        );
    }
}

function Save(TypeSave){
    var TRList=[];
    var flagErr=false;
    for(var i=0;i<$("#OrderTable tr").length;i++){
        var TR=$("#OrderTable tr:eq("+i+")");
        switch (parseInt(TR.find("td[CountShptMax]").attr("CountShptMax"))>=parseInt(TR.find("td[CountShptMax] input").val())){
            case true:
                TRList.push({
                    idOrder:TR.attr("idOrder"),
                    IdDoor:TR.attr("idDoor"),
                    idPackageDoor:TR.attr("idPackageDoor"),
                    Status:TR.attr("Status"),
                    CounShpt:TR.find("td[CountShptMax] input").val()
                });
                TR.find("td[CountShptMax]").attr("class","has-success");
                break;
            case false:
                TR.find("td[CountShptMax]").attr("class","has-error");
                flagErr=true;
                break;
        }

    };
    if(!flagErr)
        gl.Post(
            "Package_OrderSave",
            {
                TypeSave:TypeSave,
                idPackage:$("#idPackage").val(),
                TTNNum:$("#Order_TTNNum").val(),
                OrgName:$("#Order_OrgName").val(),
                Adress:$("#Order_Adress").val(),
                Doors:TRList
            },
            function(str){
                var o=JSON.parse(str);
                switch (o.Status){
                    case "Success":
                        window.location.href="?MVCPage=PackageList";
                        break;
                    case "Error":
                        alert("Ошибка: "+o.Note);
                        break;
                }
            }
        )
}

function Load(){

}