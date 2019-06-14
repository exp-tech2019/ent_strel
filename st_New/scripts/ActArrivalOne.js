$(document).ready(function(){
    CustomersDialog.Select();
    GoodsDialog.Select();
    $("#CustomersDialogFind").keypress(function (e) {
        if (e.which == 13)
            CustomersDialog.Select();
    });
    $("#GoodsDialogFind").keypress(function (e) {
        if (e.which == 13)
            GoodsDialog.FindGood();
    });
    $("#GoodsTableBtnRemove").hide();

    ActArrival.EditStart();
})
var CustomersDialog={
    "OpenDialog":function () {
        $("#CustomersDialog").modal("show");
    },
    "Select":function () {
        $("#CustomersDialogTable tr").remove();
        gl.Post(
            "Customer_Select",
            {
                "FindText":$("#CustomersDialogFind").val(),
                "PageNum":1,
                "FieldCount":1000
            },
            function(str){
                var o=JSON.parse(str).CustomerList;
                for(var i in o)
                    $("#CustomersDialogTable").append(
                        "<tr onclick='CustomersDialog.SelectTR(this)' idCustomer='" + o[i].id + "' style='cursor: pointer'>" +
                            "<td Type='Name'>" + o[i].CustomerName + "</td>" +
                            "<td Type='INN'>" + o[i].INN + "</td>" +
                        "</tr>"
                    );
                $("#CustomersDialogTable tr").dblclick(function () {
                    CustomersDialog.SelectTR(this);
                    CustomersDialog.Close();
                })
            }
        )
    },
    "SelectTR":function (el) {
        var TR=$(el);
        $("#CustomersDialogTable tr").attr("class","");
        TR.attr("class","success");
    },
    "Close":function () {
        var SelectTR=$("#CustomersDialogTable tr[class=success]");
        if(SelectTR.length==1){
            $("#CustomerName").val(SelectTR.find("td[Type=Name]").text());
            $("#idCustomer").val(SelectTR.attr("idCustomer"));
            $("#CustomersDialog").modal("hide");
        }
    }
}

var GoodsDialog={
    "TRGuid":"",
    "OpenDialog":function () {
        GoodsDialog.TRGuid="";
        $("#GoodsDialog").modal("show");
    },
    "GoodEdit":function (el) {
        var TR=$(el).parent();
        GoodsDialog.TRGuid=TR.attr("TRGuid");
        $("#GoodsDialog").modal("show");
    },
    "Select":function () {
        $("#GoodsDialogTable tr").remove();
        gl.Post(
            "GroupGood_Select",
            {},
            function(str){
                var o=JSON.parse(str);
                for(var i in o){
                    $("#GoodsDialogTable").append(
                        "<tr onclick='GoodsDialog.Group_UPDown($(this))' idGroup='"+o[i].idGroup+"'>" +
                            "<td Type='Btn'><span class='glyphicon glyphicon-chevron-up' style='cursor: pointer;'></span> </td>"+
                            "<td Type='GroupName' colspan='5'>"+o[i].GroupName+"</td>"+
                        "</tr>"
                    );
                    var gd=o[i].GoodList;
                    for(var j in gd)
                        $("#GoodsDialogTable").append(
                            "<tr onclick='GoodsDialog.SelectTR(this)' idGroup='"+o[i].idGroup+"' idGood='"+gd[j].idGood+"'>" +
                                "<td></td>"+
                                "<td Type='GoodName'>"+gd[j].GoodName+"</td>"+
                                "<td Type='Article'>"+gd[j].Article+"</td>"+
                                "<td Type='Manufacturer'>"+gd[j].Manufacturer+"</td>"+
                                "<td Type='Unit'>"+gl.ManualUnits[ gd[j].Unit]+"</td>"+
                            "</tr>"
                        );
                };
                $("#GoodsDialogTable tr[idGood]").hide();
                $("#GoodsDialogTable tr[idGood]").dblclick(function(){
                    GoodsDialog.SelectTR(this);
                    GoodsDialog.Close();
                });
            }
        )
    },
    "Group_UPDown":function (el) {
        el=$(el).find("span");
        var TR=$(el).parent().parent();
        switch($(el).attr("class")){
            case "glyphicon glyphicon-chevron-up":
                $("#GoodsDialogTable tr[idGroup="+TR.attr("idGroup")+"][idGood]").show();
                $(el).attr("class","glyphicon glyphicon-chevron-down");
                break;
            case "glyphicon glyphicon-chevron-down":
                $("#GoodsDialogTable tr[idGroup="+TR.attr("idGroup")+"][idGood]").hide();
                $(el).attr("class","glyphicon glyphicon-chevron-up");
                break;
        }
    },
    "FindGood":function(){
        var findStr=$("#GoodsDialogFind").val().toLowerCase();
        for(var i=0;i<$("#GoodsDialogTable tr[idGood]").length;i++){
            var TR=$("#GoodsDialogTable tr[idGood]:eq("+i+")");
            switch(TR.find("td[Type=GoodName]").text().toLowerCase().indexOf(findStr)>-1){
                case true:
                    TR.show();
                    break;
                case false:
                    TR.hide();
                    break;
            }

        }
    },
    "SelectTR":function(el){
        var TR=$(el);
        $("#GoodsDialogTable tr").attr("class","");
        TR.attr("class","success");
    },
    "Close":function () {
        var TR=$("#GoodsDialogTable tr[class=success]");
        if(TR.length==1){
            switch (GoodsDialog.TRGuid){
                case "":
                    $("#GoodsTable").append(
                        "<tr idArrivalTR='-1' TRGuid='"+gl.Guid()+"' Status='Add'>" +
                            "<td Type='CheckBtn'><input onchange='GoodsTable.ChChange()' type='checkbox'> </td>"+
                            "<td Type='Good' onclick='GoodsDialog.GoodEdit(this)' idGood='"+TR.attr("idGood")+"' style='min-width: 300px;'>"+TR.find("td[Type=GoodName]").text()+"</td>"+
                            "<td Type='NDS'>" +
                                "<select>"+
                                    "<option value='18'>18%</option>"+
                                    "<option value='0'>Без НДС</option>"+
                                "</select>"+
                            "</td>"+
                            "<td Type='Manufacturer' onclick='GoodsDialog.GoodEdit(this)'>"+TR.find("td[Type=Manufacturer]").text()+"</td>"+
                            "<td Type='Unit' onclick='GoodsDialog.GoodEdit(this)'>"+TR.find("td[Type=Unit]").text()+"</td>"+
                            "<td Type='Count' style='width: 100px;'><input class='form-control' value='0' oninput='GoodsTable.Calc(this)'> </td>"+
                            "<td Type='Price' style='width: 100px;'><input class='form-control' value='0' oninput='GoodsTable.Calc(this)'> </td>"+
                            "<td Type='Sum' style='width: 100px;'><input class='form-control' value='0' disabled> </td>"+
                        "</tr>"
                    )
                    break;
                default:
                    var TR1=$("#GoodsTable tr[TRGuid='"+GoodsDialog.TRGuid+"']");
                    if(TR1.attr("Status")=="Load") TR1.attr("Status","Edit")
                    TR1.find("td[Type=Good]").attr("idGood",TR.attr("idGood"));
                    TR1.find("td[Type=Good]").text(TR.find("td[Type=GoodName]").text());
                    TR1.find("td[Type=Manufacturer]").text(TR.find("td[Type=Manufacturer]").text());
                    TR1.find("td[Type=Unit]").text(TR.find("td[Type=Unit]").text());
                    break;
            };
            $("#GoodsDialog").modal("hide");
        }
    }
}

var GoodsTable={
    "Calc":function (el) {
        var TR=$(el).parent().parent();
        if(TR.attr("Status")=="Load")
            TR.attr("Status","Edit");
        var Count=TR.find("td[Type=Count] input").val()!="" ? parseFloat(TR.find("td[Type=Count] input").val()) : 0;
        var Price=TR.find("td[Type=Price] input").val()!="" ? parseFloat(TR.find("td[Type=Price] input").val()) : 0;
        TR.find("td[Type=Sum] input").val((Price*Count).toFixed(2));
    },
    "ChChange":function () {
        var flagCh=false;
        for(var i=0;i<$("#GoodsTable tr").length;i++)
            if($("#GoodsTable tr:eq("+i+") td[Type=CheckBtn] input").prop("checked"))
                flagCh=true;
        switch (flagCh){
            case true:
                $("#GoodsTableBtnRemove").show();
                break;
            case false:
                $("#GoodsTableBtnRemove").hide();
                break;
        }
    },
    "ChAll":function () {
        $("#GoodsTable tr td[Type=CheckBtn] input").prop("checked",$("#GoodsTableChAll").prop("checked"));
        GoodsTable.ChChange();
    },
    "Remove":function () {
        for(var i=0; i<$("#GoodsTable tr").length;i++){
            var TR=$("#GoodsTable tr:eq("+i+")");
            if(TR.find("td[Type=CheckBtn] input").prop("checked"))
                switch (TR.attr("Status")){
                    case "Add":
                        TR.remove();
                        break;
                    default:
                        TR.find("td[Type=CheckBtn] input").prop("checked",false);
                        TR.attr("Status","Remove");
                        TR.hide();
                        break;
                };
        };
        $("#GoodsTableChAll").prop("checked",false);
        GoodsTable.ChChange();
    }
};

var ActArrival={
    "EditStart":function () {
        if($("#idArrival").val()!=-1)
            gl.Post(
                "ActArrival_EditStart",
                {
                    "idArrival":$("#idArrival").val()
                },
                function (str) {
                    var o=JSON.parse(str);
                    $("#TTNNum").val(o.TTNNum);
                    $("#TTNDate").val(o.TTNDate);
                    $("#idCustomer").val(o.idCustomer);
                    $("#CustomerName").val(o.CustomerName);
                    $("#LoginID").val(o.idLogin);
                    $("#LoginFIO").val(o.LoginFIO)
                    if(o.Accept===1){
                        $("#BtnAccept").prop("disabled",true);
                        $("#BtnSave").prop("disabled",true);
                    }
                    var g=o.Goods;
                    for(var i in g)
                        $("#GoodsTable").append(
                            "<tr idArrivalTR='"+g[i].idActArrival+"' TRGuid='"+gl.Guid()+"' Status='Load'>" +
                                "<td Type='CheckBtn'><input onchange='GoodsTable.ChChange()' type='checkbox'> </td>"+
                                "<td Type='Good' onclick='GoodsDialog.GoodEdit(this)' idGood='"+g[i].idGood+"' style='min-width: 300px;'>"+g[i].GoodName+"</td>"+
                                "<td Type='NDS'>" +
                                    "<select>"+
                                        "<option value='18'>18%</option>"+
                                        "<option value='0'>Без НДС</option>"+
                                    "</select>"+
                                "</td>"+
                                "<td Type='Manufacturer' onclick='GoodsDialog.GoodEdit(this)'>"+g[i].Manufacturer+"</td>"+
                                "<td Type='Unit' onclick='GoodsDialog.GoodEdit(this)'>"+gl.ManualUnits[g[i].Unit]+"</td>"+
                                "<td Type='Count' style='width: 100px;'><input class='form-control' value='"+g[i].Count+"' oninput='GoodsTable.Calc(this)'> </td>"+
                                "<td Type='Price' style='width: 100px;'><input class='form-control' value='"+g[i].Price+"' oninput='GoodsTable.Calc(this)'> </td>"+
                                "<td Type='Sum' style='width: 100px;'><input class='form-control' value='"+(g[i].Count*g[i].Price).toFixed(2)+"' disabled> </td>"+
                            "</tr>"
                        )
                }
            )
    },
    "Save":function (TypeSave) {
        //Проверка на заполненность
        var flagErr=false;
        flagErr=$("#TTNNum").val()=="" ? true : flagErr;
        flagErr=$("#TTNDate").val()=="" ? true : flagErr;
        flagErr=$("#idCustomer").val()=="" ? true : flagErr;
        flagErr=$("#GoodsTable tr").length==0 ? true : flagErr;
        switch (flagErr){
            case false:
                //Сформируем список Позиций
                var TRList=new Array();
                for(var i=0;i<$("#GoodsTable tr").length; i++){
                    var TR=$("#GoodsTable tr:eq("+i+")");
                    //if(TR.attr("Status")!="Load" & TypeSave!="Accept")
                        TRList[i]={
                            "idArrivalTR":TR.attr("idArrivalTR"),
                            "Status":TR.attr("Status"),
                            "idGood":TR.find("td[Type=Good]").attr("idGood"),
                            "NDS":TR.find("td[Type=NDS] select").val(),
                            "Count":TR.find("td[Type=Count] input").val(),
                            "Price":TR.find("td[Type=Price] input").val()
                        };
                };
                gl.Post(
                    "ActArrival_Save",
                    {
                        "TypeSave":TypeSave,
                        "idArrival":$("#idArrival").val(),
                        "TTNNum":$("#TTNNum").val(),
                        "TTNDate":$("#TTNDate").val(),
                        "idCustomer":$("#idCustomer").val(),
                        "idLogin":$("#idLogin").val(),
                        "Goods":TRList
                    },
                    function (str) {
                        var o=JSON.parse(str);
                        switch (o.Status){
                            case "Success":
                                window.location.href="?MVCPage=ActArrivalList"
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
    Remove:function () {
        //Добавим дополнительную проверку для удаления проведенных
        // поступлений
        var flagRemove=false;
        switch($("#BtnSave").is(":disabled"))
        {
            case true:
                if(confirm("Поступление уже проведенно, удаление изменит кол-во материала на скалде. Продолжить удаление?"))
                    flagRemove=true;
                break;
            case false:
                flagRemove=true;
                break;
        };

        if(flagRemove)
            gl.Post(
                "ActArrival_Remove",
                {
                    idArrival:$("#idArrival").val()
                },
                function (str) {
                    var o=JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            window.location.href="?MVCPage=ActArrivalList"
                            break;
                        case "Error":
                            alert("Ошибка удаления: "+o.Note);
                            break;
                    };
                }
            );
    }
}