$(document).ready(function(){

});

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
    if(idGood!="")
        g_ActionAfterClose(idGood,g_ArrivalTR.parent());
}