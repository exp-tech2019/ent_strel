function st_ShptCreate(){
    if($("#orderDialogInputID").val()!="") {
        $("#st_ShptDlgCreate_Table tr").remove();
        $.post(
            "Orders/st_Shpt/CreateAct.php",
            {
                "Action": "ActLoad",
                "idOrder":$("#orderDialogInputID").val()
            },
            function(o){
                var i=-1;
                while(o[++i]!=null)
                    $("#st_ShptDlgCreate_Table").append(
                        "<tr idNaryad='"+o[i].idNaryad+"' idDoor='"+o[i].idDoor+"' onclick='st_ShptCreate_TRSelect(this)'>" +
                            "<td>"+o[i].NumInOrder+"</td>"+
                            "<td>"+o[i].NumNaryad+"</td>"+
                            "<td>"+o[i].Name+"</td>"+
                            "<td>"+o[i].Size+"</td>"+
                        "</tr>"
                    );
                $("#st_ShptDlgCreate").dialog("open");
            }
        )
    };
}
function st_ShptCreate_TRSelect(el){
    var TR=$(el);
    if(TR.attr("class")=="Complite"){
        TR.removeAttr("class");
    }
    else
        TR.attr("class","Complite");
}
function st_ShptCreate_TRSelectAll(){
    $("#st_ShptDlgCreate_Table tr").attr("class","Complite");
}
function st_ShptCreate_TRNoSelectAll(){
    $("#st_ShptDlgCreate_Table tr").removeAttr("class");
}
function st_ShptCreate_TRFind(){
    var FindStr=$("#st_ShptDlgCreate_Find").val();
    for(var i=0; i<$("#st_ShptDlgCreate_Table tr").length;i++){
        var TR=$("#st_ShptDlgCreate_Table tr:eq("+i+")");
        if(TR.find("td:eq(0)").text().indexOf(FindStr)>-1 || TR.find("td:eq(1)").text().indexOf(FindStr)>-1){
            TR.show();
        }
        else
            TR.hide();
    };
}
//Создание нового акта на отгрузку
function st_ShptCreate_Save(){
    if($("#st_ShptDlgCreate_Table tr[class=Complite]").length>0){
        var NaryadSelected={};
        for(var i=0;i<$("#st_ShptDlgCreate_Table tr[class=Complite]").length; i++){
            var TR=$("#st_ShptDlgCreate_Table tr[class=Complite]:eq("+i+")");
            NaryadSelected[i]={
                "idDoor":TR.attr("idDoor"),
                "idNaryad":TR.attr("idNaryad")
            };
        };

        $.post(
            "Orders/st_Shpt/CreateAct.php",
            {
                "Action":"ActCreate",
                "idOrder":$("#orderDialogInputID").val(),
                "NaryadList":NaryadSelected
            },
            function(o){
                if(o.Result=="Ok")
                    $( "#st_ShptDlgCreate" ).dialog("close");
            }
        )
    }
}

function st_ShptHistory(){

}