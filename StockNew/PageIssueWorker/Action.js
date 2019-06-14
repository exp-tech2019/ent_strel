$(document).ready(function(){
    SelectActs();
});
function AddIssue(){
    $("#isw_Dialog_WorkerID").val("");
    $("#isw_Dialog_WorkerFIO").val("");
    $("#isw_Dialog_NaryadNum").val("");
    $("#isw_Dialog_Table tr").remove();
    $("#isw_Dialog_BtnSave").show();
    $("#isw_Dialog").modal("show");
}

function LoadSpecification(){
    $("#isw_Dialog_Table tr").remove();
    if($("#isw_Dialog_NaryadNum").val()!="")
    $.post(
        "PageIssueWorker/Action.php",
        {
            "Action":"LoadSpecification",
            "NaryadNum":$("#isw_Dialog_NaryadNum").val()
        },
        function(o1){
            $("#isw_Dialog_idDoor").val(o1.idDoor);
            $("#isw_Dialog_idNaryad").val(o1.idNaryad);
            var o=o1.spe;
            var i=-1;
            var idCommonOld=-1;
            while (o[++i]!=null) {
                if (idCommonOld != o[i].idCommon) {
                    $("#isw_Dialog_Table").append(
                        "<tr idCommon='" + o[i].idCommon + "' idGroup='"+o[i].idGroup+"'>" +
                            "<td Type='GroupName'>" + o[i].GroupName + "</td>" +
                            "<td Type='CommonCount'><input value='" + o[i].Count + "' disabled></td>" +
                            "<td></td>"+
                            "<td></td>"+
                            "<td></td>"+
                        "</tr>"
                    );
                    idCommonOld = o[i].idCommon;
                };
                if (o[i].idGood != null)
                    $("#isw_Dialog_Table").append(
                        "<tr idCommon='" + o[i].idCommon + "' idGood='" + o[i].idGood + "'>" +
                            "<td></td>"+
                            "<td Type='GoodName'>" + o[i].GoodName + "</td>" +
                            "<td Type='CountStockMain'><input value='"+o[i].CountStock+"' disabled style='width: 70px;'></td>"+
                            "<td Type='CountIssueOld'><input value='"+o[i].CountIssue+"' disabled style='width: 70px;'></td>"+
                            "<td Type='CountIssue'><input value='0' style='width: 70px;'></td>"+
                        "</tr>"
                    );
            };

        }
    )
}

function SaveIssue(){
    //Проверим выбран сотрудник
    if($("#isw_Dialog_WorkerID").val()=="")
        return false;
    var flagCountBig=false;
    //Провери не указана кол-во больше чем есть на складе
    for(var i=0;i<$("#isw_Dialog_Table tr[idGood]").length;i++) {
        var TR=$("#isw_Dialog_Table tr[idGood]:eq("+i+")");
        if (TR.find("td[Type=CountIssue] input").val() != "0" & TR.find("td[Type=CountIssue] input").val() != "")
            if (parseFloat(TR.find("td[Type=CountStockMain] input").val()) < parseFloat(TR.find("td[Type=CountIssue] input").val()))
                flagCountBig = true;
    };
    console.log(flagCountBig);
    if(flagCountBig) return false;
    //Создадим массив для выдачи
    var arrIssue={}; var c=0;
    for(var i=0;i<$("#isw_Dialog_Table tr[idGood]").length;i++) {
        var TR=$("#isw_Dialog_Table tr[idGood]:eq("+i+")");
        if (TR.find("td[Type=CountIssue] input").val() != "0" & TR.find("td[Type=CountIssue] input").val() != "")
            arrIssue[c++] = {
                "idGood": TR.attr("idGood"),
                "CountIssue": TR.find("td[Type=CountIssue] input").val()
            };
    }

    //Если нет элементов для списания отменим сохранение
    if(arrIssue.length==0) return false;
    //Передадим запрос
    $.post(
        "PageIssueWorker/Action.php",
        {
            "Action":"SaveIssue",
            "idDoor":$("#isw_Dialog_idDoor").val(),
            "idNaryad":$("#isw_Dialog_idNaryad").val(),
            "idWorker":$("#isw_Dialog_WorkerID").val(),
            "arrIssue":arrIssue
        },
        function(o){
            if(o.Result=="ok")
                $("#isw_Dialog").modal("hide");
        }
    )
}

//----------- Карта сотрудника -------------------
function CardDialogLoad() {
    $("#isw_Dialog_WorkerID").val("");
    $("#card_Dialog_Num").val("");
    $("#card_Dialog").modal("show");
}
//Выбрана карта
function CardSelected() {
    if($("#card_Dialog_Num").val()!="")
        $.post(
            "PageIssueWorker/Action.php",
            {
                "Action":"CardSelected",
                "CardNum":$("#card_Dialog_Num").val()
            },
            function(o){
                switch(o.Result){
                    case "WorkerFired":
                        alert("Сотрудник уволен!");
                        break;
                    case "Ok":
                        $("#isw_Dialog_WorkerFIO").val(o.FIO);
                        $("#isw_Dialog_WorkerID").val(o.idWorker);
                        break;
                };
                $("#card_Dialog").modal("hide");
            }
        )
}

function SelectActs(){
    $("#isw_Table tr").remove();
    $.post(
        "PageIssueWorker/Action.php",
        {
            "Action":"SelectList"
        },
        function(o){
            var i=-1;
            while(o[++i]!=null)
                $("#isw_Table").append(
                    "<tr idAct='"+o[i].idAct+"' idDoor='"+o[i].idDoor+"' onclick='OpenAct(this)' style='cursor: pointer'>" +
                        "<td Type='DateCreate'>"+o[i].DateCreate+"</td>"+
                        "<td Type='NaryadNum'>"+o[i].NaryadNum+"</td>"+
                        "<td Type='LoginFIO'>"+o[i].LoginFIO+"</td>"+
                        "<td Type='WorkerFIO'>"+o[i].WorkerFIO+"</td>"+
                    "</tr>"
                )
        }
    )
}
function OpenAct(el){
    var idAct=$(el).attr("idAct");
    var idDoor=$(el).attr("idDoor");
    $.post(
        "PageIssueWorker/Action.php",
        {
            "Action":"OpenAct",
            "idAct":idAct,
            "idDoor":idDoor
        },
        function(o){
            $("#isw_Dialog_Table tr").remove();
            var i=-1;
            while(o[++i]!=null)
            $("#isw_Dialog_Table").append(
                "<tr>" +
                    "<td Type='GoodName'>" + o[i].GoodName + "</td>" +
                    "<td Type='CountStockMain'><input value='"+o[i].SpeIssue+"' disabled style='width: 70px;'></td>"+
                    "<td Type='CountIssueOld'><input value='' disabled style='width: 70px;'></td>"+
                    "<td Type='CountIssueOld'><input value='' disabled style='width: 70px;'></td>"+
                    "<td Type='CountIssue'><input value='"+o[i].CountIssue+"' disabled style='width: 70px;'></td>"+
                "</tr>"
            );
            $("#isw_Dialog_NaryadNum").val($(el).find("td[Type=NaryadNum]").text());
            $("#isw_Dialog_WorkerFIO").val($(el).find("td[Type=WorkerFIO]").text());
            $("#isw_Dialog_BtnSave").hide();
            $("#isw_Dialog").modal("show");
        }
    )
}

//Контроллер нажатия на клавиши
window.onkeydown  = pressed;
function pressed(e) {
    //События для диалога Выадчи
    if($("#isw_Dialog").is(":visible")) {
        var NaryadNum=$("#isw_Dialog_NaryadNum").val();
        switch (e.which) {
            case 27:
                $("#isw_Dialog").modal("show");
                break;
            case 48:
                $("#isw_Dialog_NaryadNum").val(NaryadNum+"0");
                break;
            case 49:
                $("#isw_Dialog_NaryadNum").val(NaryadNum+"1");
                break;
            case 50:
                $("#isw_Dialog_NaryadNum").val(NaryadNum+"2");
                break;
            case 51:
                $("#isw_Dialog_NaryadNum").val(NaryadNum+"3");
                break;
            case 52:
                $("#isw_Dialog_NaryadNum").val(NaryadNum+"4");
                break;
            case 53:
                $("#isw_Dialog_NaryadNum").val(NaryadNum+"5");
                break;
            case 54:
                $("#isw_Dialog_NaryadNum").val(NaryadNum+"6");
                break;
            case 55:
                $("#isw_Dialog_NaryadNum").val(NaryadNum+"7");
                break;
            case 56:
                $("#isw_Dialog_NaryadNum").val(NaryadNum+"8");
                break;
            case 57:
                $("#isw_Dialog_NaryadNum").val(NaryadNum+"9");
                break;
            case 13:
                LoadSpecification();
                break;

        }
    }
    if($("#card_Dialog").is(":visible"))
        $("#card_Dialog_Num").val($("#card_Dialog_Num").val()+String.fromCharCode(e.which));
}