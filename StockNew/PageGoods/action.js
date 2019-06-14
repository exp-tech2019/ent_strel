$(document).ready(function(){
    //$('.tree').treegrid();
    SelectAll();
});

function SelectAll(){
    $("#Table tr[Type!='Header']").remove();
    $.post(
        "PageGoods/SelectAll.php",
        function(o){
            var i=0;
            while(o[i]!=null){
                var c=0; var goods="";
                while(o[i].Goods[c]!=null){
                    goods=goods+
                        "<tr Type='Good' idGood='"+o[i].Goods[c].idGood+"' idGroup='"+o[i].idGroup+"' class='treegrid-500 treegrid-parent-"+o[i].idGroup+"'>"+
                            "<td Type='Article'>"+o[i].Goods[c].Article+"</td>"+
                            "<td Type='GoodName'>"+
                                "<span Type='Text'>"+o[i].Goods[c].GoodName+"</span>"+
                                "<span onclick='EditStartGood(this)' style='margin-left: 20px; cursor: pointer;' class='glyphicon glyphicon-pencil'></span> " +
                                "<span onclick='RemoveGood(this)' style='margin-left: 5px; cursor: pointer;' class='glyphicon glyphicon-remove'></span> "+
                            "</td>"+
                            "<td Type='BarCode'>"+o[i].Goods[c].BarCode+"</td>"+
                            "<td Type='Unit' Unit='"+o[i].Goods[c].Unit+"'>"+UnitToString( o[i].Goods[c].Unit)+"</td>"+
                        "</tr>";
                    c++;
                };
                $("#Table").append(
                    "<tr Type='Group' idGroup='"+o[i].idGroup+"' Step='"+o[i].Step+"' AutoUnset='"+o[i].AutoUnset+"' class='treegrid-"+o[i].idGroup+" treegrid-parent-100'>"+
                        "<td colspan='4'>"+
                            "<span Type='Text'>"+o[i].GroupName+"</span>"+
                            "<span onclick='EditStartGroup(this)' style='margin-left: 20px; cursor: pointer;' class='glyphicon glyphicon-pencil'></span> " +
                            (goods!="" ? "" :"<span onclick='RemoveGroup(this)' style='margin-left: 5px; cursor: pointer;' class='glyphicon glyphicon-remove'></span> " )+
                        "</td>"+
                    "</tr>"+
                    goods
                );
                $("#g_Group").append("<option value='"+o[i].idGroup+"'>"+o[i].GroupName+"</option>");
                i++;
            };
            //Инициализируем дерево
            $('.tree').treegrid({
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
            $("#Table tr[Type=Header]").treegrid("expand");
        }
    );
}

function Find(el){
    var FindText=$(el).val().toLowerCase();
    for(var i=0; i<$("#Table tr").length; i++){
        var TR=$("#Table tr:eq("+i+")");
        if(TR.attr("Type")!="Header")
            if(TR.text().toLowerCase().indexOf(FindText)>-1){
                TR.show();
            }
            else
                TR.hide();
    }
}

//Добавление группы
function AddGroup(){
    $("#gd_id").val("");
    $("#gd_GroupName").val("");
    $("#gd_AutoUnset").prop("checked",false);
    $("#gd_Dialog").modal("show");
}
function EditStartGroup(el){
    var TR=$(el).parent().parent();
    $("#gd_id").val(TR.attr("idGroup"));
    $("#gd_GroupName").val(TR.find("td span[Type=Text]").text());
    $("#gd_Step").val(TR.attr("Step"));
    console.log(TR.attr("AutoUnset"));
    $("#gd_AutoUnset").prop("checked",TR.attr("AutoUnset")==0 ? false : true);
    $("#gd_Dialog").modal("show");
}

function SaveGroup(){
    var Action=$("#gd_id").val()=="" ? "Add" : "Update";
    if($("#gd_GroupName").val()!="")
        $.post(
            "PageGoods/ActionGroups.php",
            {
                "Action":Action,
                "idGroup": $("#gd_id").val(),
                "GroupName": $("#gd_GroupName").val(),
                "Step": $("#gd_Step").val(),
                "AutoUnset":$("#gd_AutoUnset").prop("checked")
            },
            function(o){
                if(o.Result=="ok") {
                    $("#gd_Dialog").modal("hide");
                    switch(Action){
                        case "Add":
                            SelectGroups();
                            break;
                        case "Update":
                            var TR=$("#Table tr[idGroup="+$("#gd_id").val()+"]");
                            TR.find("td span[Type=Text]").text($("#gd_GroupName").val());
                            TR.attr("Step",$("#gd_Step").val());
                            break;
                    };
                };
            }
        )
}

function RemoveGroup(el){
    var TR=$(el).parent().parent();
    if(confirm("Удалить?"))
        $.post(
            "PageGoods/ActionGroups.php",
            {
                "Action":"Remove",
                "idGroup": TR.attr("idGroup")
            },
            function(o){
                if(o.Result=="ok")
                    TR.remove();
            }
        )
}

//Номеклатура
function AddGood(){
    $("#g_id").val("");
    $("#g_Article").val("");
    $("#g_GoodName").val("");
    $("#g_BarCode").val("");
    $("#g_Unit").val(0);
    $("#g_Dialog").modal("show");
}
function EditStartGood(el){
    var TR=$(el).parent().parent();
    $("#g_id").val(TR.attr("idGood"));
    $("#g_Group").val(TR.attr("idGroup"));
    $("#g_Article").val(TR.find("td[Type=Article]").text());
    $("#g_GoodName").val(TR.find("td[Type=GoodName] span[Type=Text]").text());
    $("#g_BarCode").val(TR.find("td[Type=BarCode]").text());
    $("#g_Unit").val(TR.find("td[Type=Unit]").attr("Unit"));
    $("#g_Dialog").modal("show");
}

function SaveGood(){
    var Action=$("#g_id").val()=="" ? "Add" : "Update";
    $.post(
        "PageGoods/ActionGoods.php",
        {
            "Action":Action,
            "idGood": $("#g_id").val(),
            "idGroup": $("#g_Group").val(),
            "GoodName": $("#g_GoodName").val(),
            "Article": $("#g_Article").val(),
            "BarCode": $("#g_BarCode").val(),
            "Unit": $("#g_Unit").val()
        },
        function(o){
            if(o.Result=="ok") {
                switch (Action){
                    case "Add":
                        SelectAll();
                        break;
                    case "Update":
                        var TR=$("#Table tr[idGood="+$("#g_id").val()+"]");
                        TR.attr("idGroup",$("#g_Group").val());
                        TR.find("td[Type=Article]").text($("#g_Article").val());
                        TR.find("td[Type=GoodName] span[Type=Text]").text($("#g_GoodName").val());
                        TR.find("td[Type=BarCode]").text($("#g_BarCode").val());
                        TR.find("td[Type=Unit]").html(UnitToString($("#g_Unit").val()));
                        TR.find("td[Type=Unit]").attr("Unit",$("#g_Unit").val());
                        break;
                };
                $("#g_Dialog").modal("hide");
                //SelectGroups();
            };
        }
    )
}
function RemoveGood(el){
    if(confirm("Удалить номеклатуру?")){
        var TR=$(el).parent().parent();
        $.post(
            "PageGoods/ActionGoods.php",
            {
                "Action":"Remove",
                "idGood": TR.attr("idGood")
            },
            function(o){
                if(o.Result=="ok")
                    TR.remove();
            }
        )
    };
}