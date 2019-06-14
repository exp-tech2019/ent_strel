var DolgnostStep=new Array();
var WorkerList=new Array();
$(document).ready(function () {
    //Загрузка списка стадий и их должностей
    gl.Post(
        "DolgnostStep_Select",
        {},
        function (str) {
            var o=JSON.parse(str);
            for(let i in o.Steps)
                DolgnostStep.push({
                    idDolgnost:o.Steps[i].idDolgnost,
                    Step:o.Steps[i].Step
                })
        }
    );

    WorkerDialog.Select();

    $("#TransferDate").val(gl.DateFormat(new Date()));
    $("#WorkerFIO").click(function(){
        WorkerDialog.Open();
    });
    //Наполним таблицу номенклатуры в диалоге добавления номенклатуры
    GoodsDialog.Select();

    $("#GoodsDialogFind").keypress(function (e) {
        if (e.which == 13)
            GoodsDialog.FindGood();
    });
})

var WorkerDialog={
    Select:function(){
        $("#WorkerDialogList tr").remove();
        gl.Post(
            "Worker_Select",
            {},
            function(str){
                var o=JSON.parse(str);
                for(var i in o) {
                    $("#WorkerDialogList").append(
                        "<tr idWorker='" + o[i].idWorker + "' idDolgnost='" + o[i].idDolgnost + "'>" +
                            "<td Type='FIO'>" + o[i].FIO + "</td>" +
                            "<td Type='Dolgnost'>" + o[i].Dolgnost + "</td>" +
                        "</tr>"
                    );
                    console.log("fdf");
                    WorkerList.push({
                        idWorker:o[i].idWorker,
                        FIO:o[i].FIO,
                        SmartCard:o[i].SmartCard,
                        idDolgnost:o[i].idDolgnost
                    });
                };
                $("#WorkerDialogList tr").click(function(){
                    WorkerDialog.SelectTR(this);
                })
                $("#WorkerDialogList tr").dblclick(function(){
                    WorkerDialog.SelectTR(this);
                    WorkerDialog.Close();
                })
            }
        )
    },
    Find:function () {
        var findStr=$("#WorkerDialogFind").val().toLowerCase();
        for(let i=0;i<$("#WorkerDialog tr").length; i++){
            let TR=$("#WorkerDialog tr:eq("+i+")");
            switch (TR.find("td[Type=FIO]").text().toLowerCase().indexOf(findStr)>-1 || TR.find("td[Type=Dolgnost]").text().toLowerCase().indexOf(findStr)>-1){
                case true:
                    TR.show();
                    break;
                case false:
                    TR.hide();
                    break;
            }
        }
    },
    Open:function () {
        $("#WorkerDialog").modal("show");
    },
    SelectTR:function (el) {
        $("#WorkerDialogList tr").attr("class","");
        $(el).attr("class","success");
    },
    Close:function () {
        let TR=$("#WorkerDialogList tr[class=success]");
        if(TR.length>0){
            $("#WorkerFIO").val(TR.find("td[Type=FIO]").text());
            $("#idWorker").val(TR.attr("idWorker"));
            $("#idDolgnost").val(TR.attr("idDolgnost"));
        };
        $("#WorkerDialog").modal("hide");
        GoodTable.DolgnostStepFilter();
    }
}

var GoodTable={
    AddRow:function (idGood, GoodName, Unit, StepGroup, CountEnt) {
        $("#GoodsTable").append(
            "<tr idGood='"+idGood+"' StepGroup='"+StepGroup+"' >" +
                "<td Type='GoodName'>"+GoodName+"</td> "+
                "<td Type='Unit'>"+gl.ManualUnits[Unit]+"</td>"+
                "<td Type='CountEnt'>"+CountEnt+"</td>"+
                "<td Type='CountIssue'><input class='form-control'> </td>"+
            "</tr>"
        );
    }
}

function Save(){
    var flagErr=false;
    flagErr=$("#idWorker").val()==""? true : flagErr;
    if(!flagErr){
        var arrTR=new Array();
        for(var i=0; i<$("#GoodsTable tr").length;i++){
            var TR=$("#GoodsTable tr:eq("+i+")");
            if(TR.find("td[Type=CountIssue] input").val()!="" & TR.find("td[Type=CountIssue] input").val()!="0")
                arrTR.push({
                    idGood:TR.attr("idGood"),
                    CountEnt:TR.find("td[Type=CountEnt]").text(),
                    CountIssue:TR.find("td[Type=CountIssue] input").val()
                });
        };
        gl.Post(
            "TransferInStock_Save",
            {
                idLogin:$("#idLogin").val(),
                idWorker:$("#idWorker").val(),
                idNaryad:$("#idNaryad").val(),
                Goods:arrTR
            },
            function(str){
                var o =JSON.parse(str);
                switch (o.Status){
                    case "Success":
                        $("#GoodsTable tr").remove();
                        break;
                    case "Error":
                        alert("Ошибка: "+o.Note);
                        break;
                }
            }
        )
    }

}

var GoodsDialog={
    "OpenDialog":function () {
        GoodsDialog.Select();
        $("#GoodsDialog").modal("show");
    },
    "Select":function () {
        $("#GoodsDialogTable tr").remove();
        gl.Post(
            "GroupGood_Select",
            {},
            function(str){
                var o=JSON.parse(str);
                for(var i in o){
                    $("#GoodsDialogTable").append(
                        "<tr onclick='GoodsDialog.Group_UPDown($(this))' idGroup='"+o[i].idGroup+"'>" +
                        "<td Type='Btn'><span class='glyphicon glyphicon-chevron-up' style='cursor: pointer;'></span> </td>"+
                        "<td Type='GroupName' colspan='5'>"+o[i].GroupName+"</td>"+
                        "</tr>"
                    );
                    var gd=o[i].GoodList;
                    for(var j in gd)
                        $("#GoodsDialogTable").append(
                            "<tr onclick='GoodsDialog.SelectTR(this)' idGroup='"+o[i].idGroup+"' idGood='"+gd[j].idGood+"' Step='"+o[i].Step+"'>" +
                            "<td></td>"+
                            "<td Type='GoodName'>"+gd[j].GoodName+"</td>"+
                            "<td Type='Unit' Unit='"+gd[j].Unit+"'>"+gl.ManualUnits[ gd[j].Unit]+"</td>"+
                            "<td Type='Manufacturer'>"+gd[j].Manufacturer+"</td>"+
                            "<td Type='CountEnt'>"+gd[j].CountEnt+"</td>"+
                            "</tr>"
                        );
                };
                $("#GoodsDialogTable tr[idGood]").hide();
                $("#GoodsDialogTable tr[idGood]").dblclick(function(){
                    GoodsDialog.SelectTR(this);
                    GoodsDialog.Close();
                });
            }
        )
    },
    "Group_UPDown":function (el) {
        el=$(el).find("span");
        var TR=$(el).parent().parent();
        switch($(el).attr("class")){
            case "glyphicon glyphicon-chevron-up":
                $("#GoodsDialogTable tr[idGroup="+TR.attr("idGroup")+"][idGood]").show();
                $(el).attr("class","glyphicon glyphicon-chevron-down");
                break;
            case "glyphicon glyphicon-chevron-down":
                $("#GoodsDialogTable tr[idGroup="+TR.attr("idGroup")+"][idGood]").hide();
                $(el).attr("class","glyphicon glyphicon-chevron-up");
                break;
        }
    },
    "FindGood":function() {
        var findStr = $("#GoodsDialogFind").val().toLowerCase();
        for (var i = 0; i < $("#GoodsDialogTable tr[idGood]").length; i++) {
            var TR = $("#GoodsDialogTable tr[idGood]:eq(" + i + ")");
            switch (TR.find("td[Type=GoodName]").text().toLowerCase().indexOf(findStr) > -1) {
                case true:
                    TR.show();
                    break;
                case false:
                    TR.hide();
                    break;
            }

        }
    },
    "SelectTR":function(el){
        var TR=$(el);
        $("#GoodsDialogTable tr").attr("class","");
        TR.attr("class","success");
    },
    "Close":function () {
        var TR=$("#GoodsDialogTable tr[class=success]");
        if(TR.length==1)
            GoodTable.AddRow(
                TR.attr("idGood"),
                TR.find("td[Type=GoodName]").text(),
                TR.find("td[Type=Unit]").attr("Unit"),
                TR.attr("Step"),
                TR.find("td[Type=CountEnt]").text(),
                0,
                0
            )
        $("#GoodsDialog").modal("hide");
    }
}
//Перехват ввода со сканера щтрих кодов или rfid считываетля
window.captureEvents(Event.KEYPRESS);
var CodeASCII={
    "31": "",    "32": " ",    "33": "!",    "34": "\"",    "35": "#",
    "36": "$",    "37": "%",    "38": "&",    "39": "'",    "40": "(",
    "41": ")",    "42": "*",    "43": "+",    "44": ",",    "45": "-",
    "46": ".",    "47": "/",    "48": "0",    "49": "1",    "50": "2",
    "51": "3",    "52": "4",    "53": "5",    "54": "6",    "55": "7",
    "56": "8",    "57": "9",    "58": ":",    "59": ";",    "60": "<",
    "61": "=",    "62": ">",    "63": "?",    "64": "@",    "65": "A",
    "66": "B",    "67": "C",    "68": "D",    "69": "E",    "70": "F",
    "71": "G",    "72": "H",    "73": "I",    "74": "J",    "75": "K",
    "76": "L",    "77": "M",    "78": "N",    "79": "O",    "80": "P",
    "81": "Q",    "82": "R",    "83": "S",    "84": "T",    "85": "U",
    "86": "V",    "87": "W",    "88": "X",    "89": "Y",    "90": "Z",
    "91": "[",    "92": "\\",    "93": "]",    "94": "^",    "95": "_",
    "96": "`",    "97": "a",    "98": "b",    "99": "c",    "100": "d",
    "101": "e",    "102": "f",    "103": "g",    "104": "h",    "105": "i",
    "106": "j",    "107": "k",    "108": "l",    "109": "m",    "110": "n",
    "111": "o",    "112": "p",    "113": "q",    "114": "r",    "115": "s",
    "116": "t",    "117": "u",    "118": "v",    "119": "w",    "120": "x",
    "121": "y",    "122": "z",    "123": "{",    "124": "|",    "125": "}",
    "126": "~",    "127": ""
};
var DeamonChar_Start=Date.now();
var DeamonChar_str="";
window.onkeypress =function(e){
    let b=Date.now();
    switch(b-DeamonChar_Start<50){
        case true:
            switch (e.which){
                case 13:
                    switch (DeamonChar_str.length){
                        case 7:
                            SpeNaryadSelect(DeamonChar_str);
                            break;
                        default:
                            for(let i in WorkerList)
                                if(WorkerList[i].SmartCard==DeamonChar_str){
                                    $("#idWorker").val(WorkerList[i].SmartCard);
                                    $("#WorkerFIO").val(WorkerList[i].FIO);
                                    $("#idDolgnost").val(WorkerList[i].idDolgnost);
                                    GoodTable.DolgnostStepFilter();
                                    break;
                                }
                            break;
                    };
                    break;
                default:
                    DeamonChar_str+=CodeASCII[e.which];
                    break;
            };
            break;
        case false:
            DeamonChar_str=CodeASCII[e.which];
            break;
    }
    DeamonChar_Start=b;
}