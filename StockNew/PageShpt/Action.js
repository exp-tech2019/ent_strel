$(document).ready(function(){
    SelectTable();
})

function SelectTable(){
    $("#ShptTable tr").remove();
    $.post(
        "PageShpt/SelectTable.php",
        {
            "SelectOld":$("#FilterShptCh").prop("checked") ? 1 : 0
        },
        function(o){
            var i=-1;
            while(o[++i]!=null)
                $("#ShptTable").append(
                    "<tr idAct='"+o[i].idAct+"' onclick='ActOpen(this)' style='cursor: pointer'>" +
                        "<td>"+o[i].Blank+"</td>"+
                        "<td>"+o[i].Shet+"</td>"+
                        "<td>"+o[i].DateCreate+"</td>"+
                        "<td>"+o[i].ManagerFIO+"</td>"+
                        "<td>"+(o[i].DateShpt!=null ? o[i].DateShpt : "")+"</td>"+
                    "</tr>"
                )
        }
    )
}

function ActOpen(el){
    $("#a_TableNaryads tr").remove();
    $.post(
        "PageShpt/ActOpen.php",
        {
            "idAct":$(el).attr("idAct")
        },
        function(o){
            $("#a_idAct").val(o.idAct);
            $("#a_Shet").val(o.Shet);
            $("#a_DateCreate").val(o.DateCreate);
            $("#a_ManagerFIO").val(o.ManagerFIO);
            $("#a_DateShpt").val(o.DateShpt);

            var i=-1;
            while(o.Naryads[++i]!=null)
            $("#a_TableNaryads").append(
                "<tr>" +
                    "<td>"+o.Naryads[i].NumInOrder+"</td>"+
                    "<td>"+o.Naryads[i].NaryadNum+"</td>"+
                "</tr>"
            );
            //Распарсим spe
            i=-1;
            var aSpe=o.Common;
            while(aSpe[++i]!=null){
                $("#a_TableSpe").append(
                    "<tr idCommon='"+aSpe[i].idCommon+"' idGroup='"+aSpe[i].idGroup+"' idDoor='"+aSpe[i].idDoor+"'>" +
                        "<td Type='GroupName'>"+aSpe[i].GroupName+"</td>"+
                        "<td Type='CountShpt'><input value='"+aSpe[i].Count+"' disabled></td>"+
                        "<td></td>"+
                        "<td></td>"+
                    "</tr>"
                );
                var aDetail=aSpe[i].Detail;
                var j=-1;
                while(aDetail[++j]!=null)
                    $("#a_TableSpe").append(
                        "<tr idDetail='"+aDetail[j].idDetail+"' idGood='"+aDetail[j].idGood+"'>" +
                            "<td></td>"+
                            "<td Type='GoodName'>"+aDetail[j].GoodName+"</td>"+
                            "<td Type='CountStockMain'><input value='"+aDetail[j].CountStockMain+"' disabled class='form-control' style='width: 70px;'></td>"+
                            "<td Type='CountIssue'><input value='0' class='form-control' style='width: 70px;'></td>"+
                        "</tr>"
                    );
            }

            $("#a_Dialog").modal("show");
        }
    )
}

function ActSave(){
    //Проврека на колво по списанию
    var flagCount=true;
    var Table=$("#a_TableSpe ");
    var CountPlain=0;
    var CountIssue=0;
    for(var i=0;i <Table.find("tr").length;i++){
        var TR=Table.find("tr:eq("+i+")");
        switch (TR.find("td[Type=CountShpt]").length){
            case 1:
                if(CountPlain!==CountIssue)
                    flagCount=false;
                CountPlain=parseFloat(TR.find("td[Type=CountShpt] input").val());
                CountIssue=0;
                break;
            case 0:
                CountIssue+=parseFloat(TR.find("td[Type=CountIssue] input").val());
                break;
        };
    };
    if(CountPlain!==CountIssue)
        flagCount=false;
    //Проверка: если кол-во на складе меньше кол-ва под списания
    if(flagCount)
        for(var i=0;i <Table.find("tr[idDetail]").length;i++) {
            var TR=Table.find("tr[idDetail]:eq(" + i + ")");
            if(parseFloat(TR.find("td[Type=CountStockMain] input").val())<parseFloat(TR.find("td[Type=CountIssue] input").val()))
                flagCount=false;
        };
    //Произведем создание акта
    if(flagCount){
        var arrCommon={};
        var cCommon=0;
        var arrDetail={};
        var cDetail=0;
        for(var i=Table.find("tr").length-1;i >=0;i--){
            var TR=Table.find("tr:eq("+i+")");
            switch (TR.find("td[Type=CountShpt]").length){
                case 1:
                    arrCommon[cCommon++]={
                        "idCommon":TR.attr("idCommon"),
                        "idGroup":TR.attr("idGroup"),
                        "idDoor":TR.attr("idDoor"),
                        "arrDetail":arrDetail
                    };
                    delete  arrDetail;
                    arrDetail={};
                    cDetail=0;
                    break;
                case 0:
                    if(TR.find("td[Type=CountIssue] input").val()!=0)
                        arrDetail[cDetail++]={
                            "idDetail":TR.attr("idDetail"),
                            "idGood":TR.attr("idGood"),
                            "CountIssue":TR.find("td[Type=CountIssue] input").val()
                        };
                    break;
            };
        };
        if(arrCommon.length!=0)
            $.post(
                "PageShpt/ActSave.php",
                {
                    "idAct":$("#a_idAct").val(),
                    "arrCommon":arrCommon
                },
                function(o){

                }
            )
    }

}