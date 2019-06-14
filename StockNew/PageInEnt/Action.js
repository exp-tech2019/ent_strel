$(document).ready(function(){
    Select();
    //Поиск
    $(document).on("input","#SupplierFind",function () {
        var FindText=$(this).val().toLowerCase();
        $("#EntList tr").each(function(){
            var TR=$(this);
            if(TR.text().toLowerCase().indexOf(FindText)>-1){
                TR.show();
            }
            else
                TR.hide();
        });
    })
})

function Select(){
    $("#EntList tr").remove();
    $.post(
        "PageInEnt/SelectAll.php",
        function(o){
            for(var i=0;i<o.length; i++)
            $("#EntList").append(
                "<tr idAct='"+o[i].id+"' style='cursor: pointer;'>" +
                    "<td>" +
                        "<span onclick='ViewSelect($(this).parent().parent())' style='cursor: pointer; width:30px;' class='input-group-addon'>" +
                            '<span class="glyphicon glyphicon-pencil"></span>'+
                        "</span>"+
                        "<span onclick='RemoveAct($(this).parent().parent())' style='cursor: pointer; width:30px;' class='input-group-addon'>" +
                            '<span class="glyphicon glyphicon-remove"></span>'+
                        "</span>"+
                    "</td>"+
                    "<td onclick='ViewSelect($(this).parent())'>"+o[i].DateCreate+"</td>"+
                    "<td onclick='ViewSelect($(this).parent())'>"+o[i].LoginFIO+"</td>"+
                    "<td onclick='ViewSelect($(this).parent())'>"+o[i].WorkerFIO+"</td>"+
                "</tr>"
            );
        }
    )
}

function ViewSelect(el){
    var idAct=$(el).attr("idAct");
    $("#e_Table tr").remove();
    $("#e_Alert").hide();
    $("#e_BtnSave").hide();
    $.post(
        "PageInEnt/Action.php",
        {
            "Action":"View",
            "idAct":idAct
        },
        function (o) {
            $("#e_DateCreate").val(o.DateCreate);
            $("#e_TextLogin").val(o.WorkerFIO);
            $("#e_FIOWorker").val(o.WorkerFIO);
            for(var i=0;i<o.Goods.length; i++)
                AddRow("",o.Goods[i].GoodName, o.Goods[i].Count, "");
            $("#e_Dialog").modal("show");
        }
    )
}

function AddAct(){
    $("#e_BtnSave").show();
    $("#e_Alert").hide();
    $("#e_id").val("");
    $("#e_idLogin").val(Global_idLogin);
    $("#e_TextLogin").val(Global_TextLogin);
    $("#e_DateCreate").val(Global_Date);
    $("#e_idWorker").val("");
    $("#e_FIOWorker").val("");
    $("#e_Table tr").remove();
    $("#e_Dialog").modal("show");
}

function AddRow(idGood, TextGood, Count, CountMain){
    $("#e_Table").append(
        "<tr>" +
            "<td Type='GoodManual' idGood='"+idGood+"'>" +
                '<div class="input-group">'+
                    '<input class="form-control" value="'+TextGood+'">'+
                    '<span onclick="g_OpenDialog(this)" class="input-group-addon">'+
                        '<span style="cursor: pointer" class="glyphicon glyphicon-folder-open"></span>'+
                    '</span>'+
                '</div>'+
            "</td>"+
            "<td Type='CountMain'><input class='form-control' value='"+CountMain+"' disabled> </td>"+
            "<td Type='Count'><input class='form-control' value='"+Count+"'> </td>"+
        "</tr>"
    );
}

function g_ActionAfterClose(idGood, TR) {
    //Подсчитаем кол-во на складе
    $.post(
        "PageInEnt/CountGoodToStockMain.php",
        {
            "idGood":idGood
        },
        function(o){
            TR.find("td[Type=CountMain] input").val(o.CountOld);
        }
    )
}

function SaveAct(){
    var flagErr=true;
    if($("#e_idWorker").val()=="") flagErr=false;
    for(var i=0;i<$("#e_Table tr").length; i++){
        var TR=$("#e_Table tr:eq("+i+")");
        if(TR.attr("idGood")=="") flagErr=false;
        if(parseFloat(TR.find("td[Type=CountMain] input").val())<parseFloat(TR.find("td[Type=Count] input").val())) flagErr=false;
    };
    var Goods={};
    for(var i=0;i<$("#e_Table tr").length; i++) {
        var TR = $("#e_Table tr:eq(" + i + ")");
        Goods[i]={
            "idGood":TR.find("td[Type=GoodManual]").attr("idGood"),
            "Count":TR.find("td[Type=Count] input").val()
        };
    };
    if(flagErr)
        $.post(
            "PageInEnt/Action.php",
            {
                "Action":"Save",
                "idAct":$("#e_id").val(),
                "idLogin":$("#e_idLogin").val(),
                "idWorker":$("#e_idWorker").val(),
                "Goods":Goods
            },
            function(o){
                if(o.Result=="ok"){
                    $("#e_Dialog").modal("hide");
                    Select();
                };
            }
        )
}

function RemoveAct(el){
    if(confirm("Удалить акт?")) {
        var TR = $(el);
        $.post(
            "PageInEnt/Action.php",
            {
                "Action": "Remove",
                "idAct": TR.attr("idAct")
            },
            function (o) {
                switch (o.Result){
                    case "ok":
                        TR.remove();
                        break;
                    case "NotRemove":
                        alert("Невозможно удалить акт!");
                        break;
                }
            }
        );
    };
}

//Карта сотрудника
function w_OpenDialog(){
    $("#w_SmartCard").val("");

    $("#w_Dialog").modal("show");
    $("#w_SmartCard").focus();
    document.getElementById("w_SmartCard").focus();
}

function w_SelectWorker(){
    if($("#w_SmartCard").val()!="")
        $.post(
            "PageInEnt/Action.php",
            {
                "Action":"SelectWorkerID",
                "SmartCard":$("#w_SmartCard").val()
            },
            function (o) {
                $("#e_FIOWorker").val(o.FIO);
                $("#e_idWorker").val(o.idWorker);
            }
        );
    $("#w_Dialog").modal("hide");
}