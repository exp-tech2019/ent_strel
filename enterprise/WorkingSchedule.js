/**
 * Created by xasya on 12.03.2017.
 */
function EntShLoad(){
    $.ajax({
        url:"Enterprise/WorkingSchedule.php",
        type:"GET",
        crossDomain:true,
        data:{
            "YearWhere":$("#EntShYearList").val(),
            "MonthWhere":$("#EntShMonthList").val()
        },
        success:function(o){
            //Очистим дни
            $("#EntWorkingScheduleTable thead tr td[Type=Day]").remove();
            $("#EntWorkingScheduleTable tbody tr").remove();
            var thead=$("#EntWorkingScheduleTable thead");
            var tbody=$("#EntWorkingScheduleTable tbody");
            //Сформируем кол-во дней в месяце
            for(var d=1;d<=o.LastDay;d++)
                thead.find("tr").append(
                    "<td Type=Day>"+d+"</td>"
                );
            //Теперь выведем список сотрудников
            //Подготовим список td  сдайто
            var tdS="";
            for(var d=1;d<=o.LastDay;d++)
                tdS+="<td Type='Day' Day='"+d+"'><input OldValue='0' oninput='EntShInpChange(this)' style='border:1px solid gray; width:20px;' value='0'></td>";
            var i=0;
            while(o.WorkerList[i]!=null){
                tbody.append(
                    "<tr idWorker='"+o.WorkerList[i].idWorker+"' idDolgnost='"+o.WorkerList[i].idDolgnost+"'>"+
                        "<td Type=FIO style='text-align: left'>"+o.WorkerList[i].FIO+"</td>"+
                        "<td Type='Dolgnost' style='text-align: left'>"+o.WorkerList[i].Dolgnost+"</td>"+
                        tdS+
                    "</tr>"
                );
                i++;
            };
            //Расставляем кол-во часов и стоимость
            var i=1;
            while(o.Schedule[i]!=null){
                var sh=o.Schedule[i];
                var w=0;
                while(sh[w]!=null){
                    tbody.find("tr[idWorker="+sh[w].idWorker+"] td[Day="+i+"] input").val(sh[w].CountHour);
                    tbody.find("tr[idWorker="+sh[w].idWorker+"] td[Day="+i+"] input").attr("OldValue",sh[w].CountHour);
                    if(sh[w].CountHour>0) {
                        tbody.find("tr[idWorker=" + sh[w].idWorker + "] td[Day=" + i + "]").css("background-color", "lightgreen");
                        tbody.find("tr[idWorker=" + sh[w].idWorker + "] td[Day=" + i + "] input").css("background-color", "lightgreen");
                    };
                    w++;
                }
                i++;
            }
        }
    });
}

function EntShInpChange(el){
    if($(el).val()>0){
        $(el).css("background-color", "lightgreen");
        $(el).parent().css("background-color", "lightgreen");
    }
    else{
        $(el).css("background-color", "white");
        $(el).parent().css("background-color", "white");
    };

    $("#EntShBtn_SvaeCancel").show();
}
function EntShInpSave(){
    var idDolgnostArr=new Array();
    var DateWhereArr=new Array();
    var idWorkerArr=new Array();
    var CountHourArr=new Array();
    //Составим массив измененых полей
    var c=0;
    for(var i=0;i<$("#EntWorkingScheduleTable tbody tr").length; i++){
        var TR=$("#EntWorkingScheduleTable tbody tr:eq("+i+")");
        for(var j=0; j<TR.find("td[Type=Day]").length; j++){
            var TD=TR.find("td[Type=Day]:eq("+j+")");
            var OldValue=TD.find("input").attr("OldValue");
            var NewValue=TD.find("input").val();
            if(OldValue!=NewValue){
                idDolgnostArr[c]=TD.parent().attr("idDolgnost");
                DateWhereArr[c]=TD.attr("Day")+"."+$("#EntShMonthList").val()+"."+$("#EntShYearList").val();
                idWorkerArr[c]=TD.parent().attr("idWorker");
                CountHourArr[c]=TD.find("input").val()!="" ? TD.find("input").val() : "0";
                c++;
            }
        }
    };
    if(idWorkerArr.length!=0)
        $.ajax({
            url:"Enterprise/WorkingScheduleSave.php",
            type:"POST",
            crossDomain:true,
            data:{
                "idDolgnostArr[]":idDolgnostArr,
                "DateWhereArr[]":DateWhereArr,
                "idWorkerArr[]":idWorkerArr,
                "CountHourArr[]":CountHourArr
            },
            success:function(o){
                if(o.Result=="ok") {
                    for (var i = 0; i < $("#EntWorkingScheduleTable tbody tr").length; i++) {
                        var TR = $("#EntWorkingScheduleTable tbody tr:eq(" + i + ")");
                        for (var j = 0; j < TR.find("td[Type=Day]").length; j++) {
                            var TDInp = TR.find("td[Type=Day]:eq(" + j + ") input");
                            TDInp.attr("OldValue", TDInp.val());
                            if (TDInp.val() > 0) {
                                TDInp.css("background-color", "lightgreen");
                                TDInp.parent().css("background-color", "lightgreen");
                            }
                        }
                    };
                    $("#EntShBtn_SvaeCancel").hide();
                };
            }
        });
}
function EntShInpCancel(){
    for (var i = 0; i < $("#EntWorkingScheduleTable tbody tr").length; i++) {
        var TR = $("#EntWorkingScheduleTable tbody tr:eq(" + i + ")");
        for (var j = 0; j < TR.find("td[Type=Day]").length; j++) {
            var TDInp = TR.find("td[Type=Day]:eq(" + j + ") input");
            TDInp.val(TDInp.attr("OldValue"));
            if (TDInp.val() > 0) {
                TDInp.css("background-color", "lightgreen");
                TDInp.parent().css("background-color", "lightgreen");
            }
            else{
                TDInp.css("background-color", "white");
                TDInp.parent().css("background-color", "white");
            };
        }
    };
    $("#EntShBtn_SvaeCancel").hide();
}

function EntShFindFIODolgnost(){
    var FindS=$("#EntShInpFind").val().toLowerCase();
    var tbody=$("#EntWorkingScheduleTable tbody");
    for(var i=0; i<tbody.find("tr").length; i++){
        var TR=tbody.find("tr:eq("+i+")");
        var FIO=TR.find("td[Type=FIO]").text().toLowerCase();
        var Dolgnost=TR.find("td[Type=Dolgnost]").text().toLowerCase();
        //console.log(FIO.indexOf(FindS)==-1 & Dolgnost.indexOf(FindS)==-1);
        if(FIO.indexOf(FindS)==-1 & Dolgnost.indexOf(FindS)==-1)
        {
            console.log("fdf");
            TR.hide();
        }
        else
            TR.show();
    }
}

//Диалог назначения стоимости по графику
function EntShSettingLoad(){
    $("#EntShSettingTableCost").find("tr").remove();
    $.ajax({
        url:"Enterprise/WorkingScheduleSetting.php",
        type:"POST",
        crossDomain:true,
        data:{
            "Action":"Load"
        },
        success:function(o){
            $("#EntShSettingDialog").dialog("open");
            for(var i=0;i<o.length; i++)
                $("#EntShSettingTableCost").append(
                    "<tr idManual='"+o[i].id+"'>"+
                        "<td Type='Dolgnost'>"+o[i].Dolgnost+"</td>"+
                        "<td Type='Cost'><input OldValue='"+o[i].Cost+"' value='"+o[i].Cost+"' style='width: 30px;'></td>"+
                    "</tr>"
                );
        }
    });
}
function EntShSettingSave(){
    var idManualArr=new Array();
    var CostArr=new Array();
    var c=0;
    for(var i=0; i<$("#EntShSettingTableCost tr").length;i++){
        var TR=$("#EntShSettingTableCost tr:eq("+i+")");
        if(TR.find("td[Type=Cost] input").attr("OldValue")!=TR.find("td[Type=Cost] input").val()){
            idManualArr[c]=TR.attr("idManual");
            CostArr[c++]=TR.find("td[Type=Cost] input").val();
        }
    };
    $.ajax({
        url:"Enterprise/WorkingScheduleSetting.php",
        type:"POST",
        crossDomain:true,
        data:{
            "Action":"Save",
            "idManualArr[]":idManualArr,
            "CostArr[]":CostArr
        },
        success:function(o){
            if(o.Result=="ok")
                $("#EntShSettingDialog").dialog("close");
        }
    });
}