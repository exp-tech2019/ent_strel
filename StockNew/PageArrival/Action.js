$(document).ready(function(){
    Select();
    //Поиск
    $(document).on("input","#SupplierFind",function () {
        var FindText=$(this).val().toLowerCase();
        $("#ArrivalList tr").each(function(){
            var TR=$(this);
            if(TR.text().toLowerCase().indexOf(FindText)>-1){
                TR.show();
            }
            else
                TR.hide();
        });
    })
});

//Вывод списка все поступлений
function Select(){
    $("#ArrivalList tr").remove();
    $.post(
        "PageArrival/Select.php",
        function(o){
            for(var i=0; i<o.length; i++){
                var el=o[i];
                $("#ArrivalList").append(
                    "<tr idArrival='"+el.id+"'>" +
                        "<td>" +
                            "<span onclick='EditArrival($(this).parent())' class='glyphicon glyphicon-pencil' style='margin-right: 10px; cursor: pointer;'></span>"+
                            "<span onclick='RemoveArrival($(this).parent())' class='glyphicon glyphicon-remove' style='margin-right: 10px; cursor: pointer;'></span>"+
                        "</td>"+
                        "<td Type='NumArrival' onclick='EditArrival(this)' style='cursor:pointer'>"+el.NumArrival+"</td>"+
                        "<td Type='DateArrival' onclick='EditArrival(this)' style='cursor:pointer'>"+el.DateArrival+"</td>"+
                        "<td Type='Supplier' idSupplier='"+el.idSupplier+"' onclick='EditArrival(this)' style='cursor:pointer'>"+el.TextSupplier+"</td>"+
                        "<td Type='SumPrice' onclick='EditArrival(this)' style='cursor:pointer'>"+el.SumPrice+"</td>"+
                    "</tr>"
                );
            };
        }
    )
}

function AddArrival(){
    $("#a_idArrival").val("");
    $("#a_NumArrival").val("");
    $("#a_DateArrival").val("");
    $("#a_TextSupplier").val("");
    $("#a_Alert").hide();
    $("#a_idSupplier").val("");
    $("#a_GoodsList tr").remove();
    $("#a_BtnSave").show();
    $("#a_Dialog").modal("show");
}

function EditArrival(el){
    var idArrival=$(el).parent().attr("idArrival");
    $.post(
        "PageArrival/Action.php",
        {
            "Action":"EditStart",
            "idArrival":idArrival
        },
        function(o){
            $("#a_idArrival").val(o.id);
            $("#a_NumArrival").val(o.NumArrival);
            $("#a_DateArrival").val(o.DateArrival);
            $("#a_idSupplier").val(o.idSupplier);
            $("#a_TextSupplier").val(o.TextSupplier);
            $("#a_GoodsList tr").remove();
            var Goods=o.Goods;
            for(var i=0; i<Goods.length;i++)
                GoodRowAdd(Goods[i].TextGood, Goods[i].idGood, Goods[i].GoodName, Goods[i].Count, Goods[i].Price)
            $("#a_Alert").hide();
            $("#a_AlertText").text("");

            $("#a_BtnSave").show();
            if(o.FlagNoteEdit==1)
                $("#a_BtnSave").hide();

            $("#a_Dialog").modal("show");
        }
    )
}

function RemoveArrival(el){
    var TR=$(el).parent().parent();
    if(confirm("Удалить поступление?"))
        $.post(
            "PageArrival/Action.php",
            {
                "Action":"Remove",
                "idArrival":TR.attr("idArrival")
            },
            function(o){
                if(o.Result=="ok")
                    TR.remove();
            }
        )
}

//Добавление позиции номеклатуры
function GoodRowAdd(GoodText, idGood, GoodName, Count, Price){
    $("#a_GoodsList").append(
        "<tr>" +
            "<td Type='GoodText'>" +
                '<div class="input-group">'+
                    '<input class="form-control" value="'+GoodText+'">'+
                    '<span Type="btn" class="input-group-addon">'+
                        '<span style="cursor: pointer" class="glyphicon glyphicon-backward"></span>'+
                    '</span>'+
                '</div>'+
            //"<input class='form-control' value='"+GoodText+"'> </td>"+
            "<td Type='GoodManual' idGood='"+idGood+"'>" +
                '<div class="input-group">'+
                    '<input class="form-control" value="'+GoodName+'">'+
                    '<span onclick="g_OpenDialog(this)" class="input-group-addon">'+
                        '<span style="cursor: pointer" class="glyphicon glyphicon-folder-open"></span>'+
                    '</span>'+
                '</div>'+
            "</td>"+
            "<td Type='Count'><input class='form-control' value='"+Count+"'> </td>"+
            "<td Type='Price'><input class='form-control' value='"+Price+"'> </td>"+
            "<td Type='Sum'><input class='form-control' value='"+(Count*Price)+"' disabled> </td>"+
        "</tr>"
    )
    $("#a_GoodsList tr:last() td[Type=GoodText] span[Type=btn]").click(function(){
        $(this).parent().find("input").val($(this).parent().parent().parent().find("td[Type=GoodManual] input").val());
    });
    $("#a_GoodsList tr:last() td[Type=Count] input").on("input",function(){
        var TR=$(this).parent().parent();
        if($(this).val()!="" & TR.find("td[Type=Price] input").val()!="")
            TR.find("td[Type=Sum] input").val(parseFloat($(this).val()) * parseFloat(TR.find("td[Type=Price] input").val()));
    });
    $("#a_GoodsList tr:last() td[Type=Price] input").on("input",function(){
        var TR=$(this).parent().parent();
        if($(this).val()!="" & TR.find("td[Type=Count] input").val()!="")
            TR.find("td[Type=Sum] input").val(parseFloat($(this).val()) * parseFloat(TR.find("td[Type=Count] input").val()));
    });
}

//Сохранение прихода
function a_Save(){
    var flagValidation=true;
    if($("#a_NumArrival").val()=="") flagValidation=false;
    if($("#a_DateArrival").val()=="") flagValidation=false;
    if($("#a_idSupplier").val()=="") flagValidation=false;
    for(var i=0; i<$("#a_GoodsList tr").length; i++)
    {
        var TR=$("#a_GoodsList tr:eq("+i+")");
        if(TR.find("td[Type=GoodText] input").val()=="") flagValidation=false;
        if(TR.find("td[Type=GoodManual]").attr("idGood")=="") flagValidation=false;
        if(TR.find("td[Type=Count] input").val()=="") flagValidation=false;
        if(TR.find("td[Type=Price] input").val()=="") flagValidation=false;
    };

    if(flagValidation){
        $("#a_Alert").hide();
        var Goods={};
        for(var i=0; i<$("#a_GoodsList tr").length; i++)
        {
            var TR=$("#a_GoodsList tr:eq("+i+")");
            Goods[i]={
                "GoodText":TR.find("td[Type=GoodText] input").val(),
                "GoodManual":TR.find("td[Type=GoodManual]").attr("idGood"),
                "Count":TR.find("td[Type=Count] input").val(),
                "Price":TR.find("td[Type=Price] input").val()
            };
        };
        console.log(Goods);
        var o={
            "idArrival":$("#a_idArrival").val(),
            "NumArrival":$("#a_NumArrival").val(),
            "DateArrival":$("#a_DateArrival").val(),
            "idSupplier":$("#a_idSupplier").val(),
            "Goods":Goods
        };
        $.post(
            "PageArrival/Action.php",
            {
                "Action":"Save",
                "JSON":o
            },
            function(o){
                if(o.Result=="ok") {
                    $("#a_Dialog").modal("hide");
                    Select();
                };
            }
        )
    }
    else{
        $("#a_AlertText").text("Заполненныне все поля");
        $("#a_Alert").show();
    }
}

//-------- Диалог поставщиков --------
function s_OpenDialog(){
    $("#s_Dialog").modal("show");
    s_Select();
}
//Отображение списка поставщиков
function s_Select(){
    $("#s_SupplierList tr").remove();
    $.post(
        "PageSupplier/select.php",
        function (o) {
            for(var i=0; i<o.length; i++)
                $("#s_SupplierList").append(
                    "<tr idSupplier='"+o[i].id+"' onclick='s_SelectOne(this)'>" +
                        "<td Type='SupplierName'>"+o[i].SupplierName+"</td>"+
                        "<td Type='INN'>"+o[i].INN+"</td>"+
                    "</tr>"
                )
        }
    )
}
//Выбор строки
function s_SelectOne(el){
    $("#s_SupplierList tr").removeAttr("class");
    $(el).attr("class","success");
}
//Перенос поставщика в основной диалог, закрытие справочника
function s_CloseDialog(){
    var idSupplier="";
    var TextSupplier="";
    if($("#s_SupplierList tr[class=success]").length==1){
        idSupplier=$("#s_SupplierList tr[class=success]").attr("idSupplier");
        TextSupplier=$("#s_SupplierList tr[class=success]").find("td[Type=SupplierName]").text();
    };
    $("#a_idSupplier").val(idSupplier);
    $("#a_TextSupplier").val(TextSupplier);
    $("#s_Dialog").modal("hide");
}

//--------- Номеклатура ----------------
var g_ArrivalTR; // - Содержит объект строки которая вызвала справочник
//Открытие справочника
function g_OpenDialog(el){
    g_ArrivalTR=$(el).parent().parent();
    $("#g_Dialog").modal("show");
    g_Select();
}
//Построение таблицы
function g_Select(){
    $("#g_GoodList tr[Type!='Header']").remove();
    $.post(
        "PageGoods/SelectAll.php",
        function(o){
            var i=0;
            while(o[i]!=null){
                var c=0; var goods="";
                while(o[i].Goods[c]!=null){
                    goods=goods+
                        "<tr onclick='g_SelectOne(this)' Type='Good' idGood='"+o[i].Goods[c].idGood+"' idGroup='"+o[i].idGroup+"' class='treegrid-500 treegrid-parent-"+o[i].idGroup+"'>"+
                            "<td Type='Article'>"+o[i].Goods[c].Article+"</td>"+
                            "<td Type='GoodName'>"+o[i].Goods[c].GoodName+"</td>"+
                            "<td Type='BarCode'>"+o[i].Goods[c].BarCode+"</td>"+
                            "<td Type='Unit' Unit='"+o[i].Goods[c].Unit+"'>"+UnitToString( o[i].Goods[c].Unit)+"</td>"+
                        "</tr>";
                    c++;
                };
                $("#g_GoodList").append(
                    "<tr Type='Group' idGroup='"+o[i].idGroup+"' Step='"+o[i].Step+"' class='treegrid-"+o[i].idGroup+" treegrid-parent-100'>"+
                    "<td colspan='4'>"+o[i].GroupName+"</td>"+
                    "</tr>"+
                    goods
                );
                i++;
            };
            //Инициализируем дерево
            $('#g_GoodList').treegrid({
                initialState:'collapsed',
                onExpand:function(){
                },
                onCollapse:function(){
                    if($(this).attr("Type")!="Header"){
                        console.log("fdf");
                        var idGroup=$(this).attr("idGroup");
                        //$("#Table tr[Type=Good&idGroup="+idGroup+"]").remove();
                    }
                }
            });
            $("#g_GoodList tr[Type=Header]").treegrid("expand");
        }
    );
}
//Выбор строки
function g_SelectOne(el){
    for(var i=0; i<$("#g_GoodList tr[Type=Good]").length; i++)
    {
        var TR=$("#g_GoodList tr[Type=Good]:eq("+i+")");
        TR.attr("class", TR.attr("class").replace("success",""));
    };
    $(el).attr("class",$(el).attr("class")+" success");
}
//Закрытие диалога и установка значения из справочника
function g_CloseDialog(){
    var idGood="";
    var TextGood="";
    if($("#g_GoodList tr[class*=success]").length==1){
        TextGood=$("#g_GoodList tr[class*=success] td[Type=GoodName]").text();
        idGood=$("#g_GoodList tr[class*=success]").attr("idGood");
    };
    g_ArrivalTR.attr("idGood",idGood);
    g_ArrivalTR.find("div input").val(TextGood);
    $("#g_Dialog").modal("hide");
}