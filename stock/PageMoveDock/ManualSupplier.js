/**
 * Created by anikulshin on 30.01.2017.
 */
function SupplierFind(){
    var Name=$("#ManualSupplierFindName").val();
    var INN=$("#ManualSupplierFindINN").val();
    for(var i=0; i<$("#ManualSupplierTable tr").length; i++){
        var TR=$("#ManualSupplierTable tr:eq("+i+")");
        if(TR.find("td[Type=Name]").text().indexOf(Name)>-1 & TR.find("td[Type=INN]").text().indexOf(INN)>-1)
        {
            TR.show();
        }
        else
            TR.hide();
    }
}
function SupplierAdd(){
    $("#ManualSupplierAddID").val("");
    $("#ManualSupplierAddName").val("");
    $("#ManualSupplierAddINN").val("");
    $("#ManualSupplierAddDialog").modal("show");
}
function SupplierEditStart(el) {
    var TR=$(el).parent().parent();
    $("#ManualSupplierAddID").val(TR.attr("idSupplier"));
    $("#ManualSupplierAddName").val(TR.find("td[Type=Name]").text());
    $("#ManualSupplierAddINN").val(TR.find("td[Type=INN]").text());
    $("#ManualSupplierAddDialog").modal("show");
}
function SupplierSave() {
    $.post(
        "PageMoveDock/ManualSupplierAction.php",
        {
            "Action":$("#ManualSupplierAddID").val()=="" ? "Add" : "Edit",
            "idSupplier":$("#ManualSupplierAddID").val(),
            "Name":$("#ManualSupplierAddName").val(),
            "INN":$("#ManualSupplierAddINN").val()
        },
        function(data){
            var o=jQuery.parseJSON(data);
            if($("#ManualSupplierAddID").val()=="" & o.id!==undefined)
                $("#ManualSupplierTable").append(
                    "<tr idSupplier='"+o.id+"'>"+
                        "<td Type='Name'>"+$("#ManualSupplierAddName").val()+"</td>"+
                        "<td Type='INN'>"+$("#ManualSupplierAddINN").val()+"</td>"+
                    "</tr>"
                );
            if($("#ManualSupplierAddID").val()=="" & o.id===undefined)
            {
                $("#ManualSupplierTable tr[idSupplier='"+$("#ManualSupplierAddID").val()+"'] td[Type=Name]").text($("#ManualSupplierAddName").val());
                $("#ManualSupplierTable tr[idSupplier='"+$("#ManualSupplierAddID").val()+"'] td[Type=Name]").text($("#ManualSupplierAddINN").val());
            };
            $("#ManualSupplierAddDialog").modal("hide");
        }
    )
}