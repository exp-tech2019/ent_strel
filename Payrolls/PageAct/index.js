/**
 * Created by anikulshin on 07.02.2017.
 */
$(document).ready(function(){
    CalcItog();
    $("#HistoryInfo").hide();
    //Округление загруженной таблицы
    var tdArr=new Array("SumWith","SumCost","SumPlus","SumPlusAll","SumPlusAllNalog","SumMinus","BalanceNalog");
    for(var i=0; i<$("#WorkerTable tr").length; i++){
        var TR=$("#WorkerTable tr:eq("+i+")");
        for(var td in tdArr)
            TR.find("td[Type="+tdArr[td]+"]").text(parseFloat(TR.find("td[Type="+tdArr[td]+"]").text()).toFixed(0));
    }
});

var ReloadListEnable=true;
function LoadFirstList() {
    if(ReloadListEnable) {
        ReloadListEnable=false;
        $("#WorkerTable").find("tr").remove();
        $.post(
            "PageAct/Action.php",
            {
                "Method": "LoadFirstList"
            },
            function (data) {
                ReloadListEnable=true;
                var o = jQuery.parseJSON(data);
                var i = 0;
                while (o[i] != null) {
                    $("#WorkerTable").append(
                        "<tr idWorker='" + o[i].idWorker + "' onclick='ActiondDialogShow(this)'>" +
                            "<td Type='FIO'>" + o[i].FIO + "</td>" +
                            "<td Type='Dolgnost'>" + o[i].Dolgnost + "</td>" +
                            "<td Type='SumWith'>" + o[i].SumWith + "</td>" +
                            "<td Type='Cost'>" + o[i].Cost + "</td>" +
                            "<td Type='SumPlus'>" + o[i].SumPlus + "</td>" +
                            "<td Type='SumMinus'>" + o[i].SumMinus + "</td>" +
                        "</tr>"
                    );
                    i++;
                }
            }
        );
    };
}

function ActiondDialogShow(el){
    $("#ActionsDialogID").val($(el).attr("idWorker"));
    $("#ActionsDialogFIO").val($(el).find("td[Type=FIO]").text());
    $("#ActionsDialogDolgnost").val($(el).find("td[Type=Dolgnost]").text());
    $("#ActionsDialogBalance").val($(el).find("td[Type=BalanceNalog]").text());
    $("#ActionsDialog").modal("show");
}
function MinusAdd(){
    $("#ActionsDialog").modal("hide");
    $("#MinusMethod").val("MinusSave");
    $("#MinusID").val($("#ActionsDialogID").val());
    $("#MinusFIO").val($("#ActionsDialogFIO").val());
    $("#MinusDolgnost").val($("#ActionsDialogDolgnost").val());
    $("#MinusBalance").val($("#ActionsDialogBalance").val());
    $("#MinusNote").val("");
    $("#MinusDialog").modal("show");
}
function PlusAdd(){
    $("#ActionsDialog").modal("hide");
    $("#MinusMethod").val("PlusSave");
    $("#MinusID").val($("#ActionsDialogID").val());
    $("#MinusFIO").val($("#ActionsDialogFIO").val());
    $("#MinusDolgnost").val($("#ActionsDialogDolgnost").val());
    $("#MinusBalance").val($("#ActionsDialogBalance").val());
    $("#MinusNote").val("");
    $("#MinusDialog").modal("show");
}
function MinusSave() {
    if($("#MinusSum").val()!="" & $("#MinusID").val()!="")
        $.post(
            "PageAct/Action.php",
            {
                "Method":$("#MinusMethod").val(),
                "idAct":$("#MainActID").val(),
                "idWorker":$("#MinusID").val(),
                "Sum":$("#MinusSum").val(),
                "Note":$("#MinusNote").val()
            },
            function(data){
                if(data=="")
                {
                    var TR=$("#WorkerTable tr[idWorker=" + $("#MinusID").val() + "]");
                    var BalanceNalog=parseFloat(TR.find("td[Type=BalanceNalog]").text());
                    var SumPlusAll=parseFloat(TR.find("td[Type=SumPlusAll]").text());
                    var SumPlusAllNalog=parseFloat(TR.find("td[Type=SumPlusAllNalog]").text());
                    switch ($("#MinusMethod").val()) {
                        case "MinusSave":
                            TR.find("td[Type=SumMinus]").text((parseFloat(TR.find("td[Type=SumMinus]").text())+parseFloat($("#MinusSum").val())).toFixed(0));
                            break;
                        case "PlusSave":
                            TR.find("td[Type=SumPlus]").text((parseFloat(TR.find("td[Type=SumPlus]").text())+parseFloat($("#MinusSum").val())).toFixed(0));
                            var SumPlusAll=parseFloat(TR.find("td[Type=SumWith]").text())+parseFloat(TR.find("td[Type=SumCost]").text())+parseFloat(TR.find("td[Type=SumPlus]").text());
                            TR.find("td[Type=SumPlusAll]").text(SumPlusAll.toFixed(0));
                            TR.find("td[Type=SumPlusAllNalog]").text((SumPlusAll-SumPlusAll*parseInt(TR.find("td[Type=NalogPercent]").text())/100).toFixed(0));
                            break;
                    };
                    var SumPlusAllNalog=parseFloat(TR.find("td[Type=SumPlusAllNalog]").text());
                    var SumMinus=parseFloat(TR.find("td[Type=SumMinus]").text())
                    TR.find("td[Type=BalanceNalog]").text((SumPlusAllNalog-SumMinus).toFixed(0));
                    if (SumPlusAllNalog-SumMinus > 0) TR.attr("class", "warning");
                    if (SumPlusAllNalog-SumMinus <= 0) TR.attr("class", "success");

                    $("#HistoryInfo").show();

                    $("#MinusDialog").modal("hide");
                }
                else
                    console.log(data);
            }
        )
}
function MinusBalance(){
    $('#MinusSum').val($('#MinusBalance').val())
}

function FilterTable(){
    var FIO=$("#FilterFIO").val();
    var Dolgnost=$("#FilterDolgnost").val().toLowerCase();
    for(var i=0; i<$("#WorkerTable tr").length; i++){
        var TR=$("#WorkerTable tr:eq("+i+")");
        var flag=true;
        var FIOtd=TR.find("td[Type=FIO]").text().toLowerCase();
        var Dolgnosttd=TR.find("td[Type=Dolgnost]").text().toLowerCase();
        if(FIO!="" & FIOtd.indexOf(FIO)==-1) flag=false;
        if(Dolgnost!="" & Dolgnosttd.indexOf(Dolgnost)==-1) flag=false;
        switch (flag){
            case true: TR.show(); break;
            case false: TR.hide(); break;
        };
    };
    CalcItog();
}

function CalcItog(){
    var SumWith=0;
    var SumCost=0;
    var SumPlus=0;
    var SumPlusAll=0;
    var SumPlusAllNalog=0;
    var SumPlusAllNalog1=0;
    var SumMinus=0;
    var SumMinus1=0;
    var BalanceNalog=0;
    for(var i=0;i<$("#WorkerTable tr").length; i++){
        var TR=$("#WorkerTable tr:eq("+i+")");
        if(TR.is(":visible")) {
            SumWith += parseFloat(TR.find("td[Type=SumWith]").text());
            SumCost += parseFloat(TR.find("td[Type=SumCost]").text());
            SumPlus += parseFloat(TR.find("td[Type=SumPlus]").text());
            SumPlusAll += parseFloat(TR.find("td[Type=SumPlusAll]").text());
            SumPlusAllNalog += parseFloat(TR.find("td[Type=SumPlusAllNalog]").text());
            SumMinus += parseFloat(TR.find("td[Type=SumMinus]").text());
            BalanceNalog += parseFloat(TR.find("td[Type=BalanceNalog]").text());
        };
        SumPlusAllNalog1 += parseFloat(TR.find("td[Type=SumPlusAllNalog]").text());
        SumMinus1 += parseFloat(TR.find("td[Type=SumMinus]").text());
    };
    $("#WorkerTableFooter tr:eq(0) th[Type=SumWith]").text(SumWith.toFixed(0));
    $("#WorkerTableFooter tr:eq(0) th[Type=SumCost]").text(SumCost.toFixed(0));
    $("#WorkerTableFooter tr:eq(0) th[Type=SumPlus]").text(SumPlus.toFixed(0));
    $("#WorkerTableFooter tr:eq(0) th[Type=SumPlusAll]").text(SumPlusAll.toFixed(0));
    $("#WorkerTableFooter tr:eq(0) th[Type=SumPlusAllNalog]").text(SumPlusAllNalog.toFixed(0));
    $("#WorkerTableFooter tr:eq(0) th[Type=SumMinus]").text(SumMinus.toFixed(0));
    $("#WorkerTableFooter tr:eq(0) th[Type=BalanceNalog]").text(BalanceNalog.toFixed(0));

    $("#MainAllPlusNalog").val(SumPlusAllNalog1.toFixed(0));
    $("#MainAllMinus").val(SumMinus1.toFixed((0)));
}

//Удаление акта
function DeleteAct(){
    if(confirm("Удалить акт?")){
        $.post(
            "PageAct/Action.php",
            {
                "Method":"DeleteAct",
                "idAct":$("#MainActID").val()
            },
            function (data){
                if(data==""){
                    window.location.href="index.php?MVCPage=PageMain";
                }
                else
                    console.log(data);
            }
        )
    }
}

//Завершение расчета
function CalcComplite(){
    if(confirm("Завршить расчет?"))
        $.post(
            "PageAct/Action.php",
            {
                "Method":"CalcComplite",
                "idAct":$("#MainActID").val()
            },
            function (data){
                if(data==""){
                    window.location.href="index.php?MVCPage=PageMain";
                }
                else
                    console.log(data);
            }
        )
}

function Print() {
    window.print();
}
function PrintOneWroker(){
    $("#ActionsDialog").modal("hide");
    $("#MinusID").val($("#ActionsDialogID").val());
    window.open("/Payrolls/PageAct/PrintOneWorker.php?idWorker="+$("#ActionsDialogID").val()+"&idAct="+idAct, "_blank");
    //window.location.href="/Payrolls/PageAct/PrintOneWorker.php?idWorker="+$("#ActionsDialogID").val()+"&idAct="+idAct;
}