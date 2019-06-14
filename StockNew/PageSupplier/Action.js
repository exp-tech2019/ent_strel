$(document).ready(function(){
    SelectSupplier();
})
function SelectSupplier(){
    $("#SupplierList tr").remove();
    $.post(
        "PageSupplier/Select.php",
        function(o){
            for(var i=0; i<o.length; i++)
                $("#SupplierList").append(
                    "<tr idSupplier='"+o[i].id+"'>"+
                        "<td Type='SupplierName'>"+
                            "<span Type='Text'>"+o[i].SupplierName+"</span>"+
                            "<span onclick='EditStart(this)' style='margin-left: 10px; cursor: pointer;' class='glyphicon glyphicon-pencil'></span>"+
                            "<span onclick='RemoveSupplier(this)' style='margin-left: 10px; cursor: pointer;' class='glyphicon glyphicon-remove'></span>"+
                        "</td>"+
                        "<td Type='INN'>"+o[i].INN+"</td>"+
                        "<td Type='Adress'>"+o[i].Adress+"</td>"+
                        "<td Type='Phone'>"+o[i].Phone+"</td>"+
                    "</tr>"
                );
        }
    )
}
function SupplierFind(el){
    var s=$(el).val().toLowerCase();
    for(var i=0; i<$("#SupplierList tr").length;i++){
        var TR=$("#SupplierList tr:eq("+i+")");
        if(TR.find("td[Type=SupplierName] span[Type=Text]").text().toLowerCase().indexOf(s)>-1 || TR.find("td[Type=INN]").text().toLowerCase().indexOf(s)>-1){
            TR.show();
        }
        else
            TR.hide();
    }
}

function AddSupplier(){
    $("#s_id").val("");
    $("#s_SupplierName").val("");
    $("#s_INN").val("");
    $("#s_Adress").val("");
    $("#s_Phone").val("");
    $("#s_Alert").hide();
    $("#s_Dialog").modal("show");
}
function EditStart(el){
    var TR=$(el).parent().parent();
    $("#s_id").val(TR.attr("idSupplier"));
    $("#s_SupplierName").val(TR.find("td[Type=SupplierName] span[Type=Text]").text());
    $("#s_INN").val(TR.find("td[Type=INN]").text());
    $("#s_Adress").val(TR.find("td[Type=Adress]").text());
    $("#s_Phone").val(TR.find("td[Type=Phone]").text());
    $("#s_Alert").hide();
    $("#s_Dialog").modal("show");
}
function SaveSupplier(){
    var Action=$("#s_id").val()=="" ? "Add" : "Update";
    if($("#s_SupplierName").val()!="" & $("#s_INN").val()!=""){
        $.post(
            "PageSupplier/Action.php",
            {
                "Action":Action,
                "idSupplier":$("#s_id").val(),
                "SupplierName":$("#s_SupplierName").val(),
                "INN":$("#s_INN").val(),
                "Adress":$("#s_Adress").val(),
                "Phone":$("#s_Phone").val()
            },
            function(o){
                if(o.Result=="ok") {
                    switch (Action) {
                        case "Add":
                            SelectSupplier();
                            break;
                        case "Update":
                            var TR=$("#SupplierList tr[idSupplier="+$("#s_id").val()+"]");
                            TR.find("td[Type=SupplierName] span[Type=Text]").text($("#s_SupplierName").val());
                            TR.find("td[Type=INN]").text($("#s_INN").val());
                            TR.find("td[Type=Adress]").text($("#s_Adress").val());
                            TR.find("td[Type=Phone]").text($("#s_Phone").val());
                            break;
                    }
                    $("#s_Dialog").modal("hide");
                }
                else{
                    $("#s_AlertText").text(o);
                    $("#s_Alert").show();
                }
            }
        )
    }
    else{
        $("#s_AlertText").text("Не заполненны поля");
        $("#s_Alert").show();
    };
}
function RemoveSupplier(el){
    var TR=$(el).parent().parent();
    if(confirm("Удалить контрагента?"))
        $.post(
            "PageSupplier/Action.php",
            {
                "Action":"Remove",
                "idSupplier":TR.attr("idSupplier")
            },
            function(o){
                if(o.Result=="ok")
                    TR.remove();
            }
        )
}

