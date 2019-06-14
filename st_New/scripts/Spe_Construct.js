$(document).ready(function(){
    SelectTypeDoor($("#DoorList"));
    $("#BtnSave").hide();
    $("#BtnAddGroup").hide();
    gl.Post(
        "GroupGood_Select",
        {

        },
        function(str){
            var o=JSON.parse(str);
            console.log(o);
            for(var i in o) {
                console.log(o[i].GroupName );
                $("#GroupDialogTable").append(
                    "<tr idGroup='" + o[i].idGroup + "'><td>" + o[i].GroupName + "</td></tr>"
                );
            };
            $("#GroupDialogTable tr").click(function(){
                GroupDialog.TRSelected(this);
            });
            $("#GroupDialogTable tr").dblclick(function () {
                GroupDialog.TRDbClick(this);
            })
        }
    )
    $("#DoorList").change(function(){
        if($("#DoorList").val()!=-1){
            $("#BtnSave").show();
            $("#BtnAddGroup").show();
            CommonTable.Select();
        }else{
            $("#BtnSave").hide();
            $("#BtnAddGroup").hide();
            $("#CommonTable tr").remove();
        };
    });
    $("#BtnAddGroup").click(function(){
        GroupDialog.Open();
    });
    $("#BtnSave").click(function(){
        CommonTable.Save();
    })
})

function SelectTypeDoor(TypeDoorControl){
    TypeDoorControl.find("option[values!=-1]").remove();
    gl.Post(
        "Common_TypeDoor",
        {},
        function(str){
            var o=JSON.parse(str);
            TypeDoorControl.append(
                "<option value='-1'></option>"
            );
            for(var i in o)
                TypeDoorControl.append(
                    "<option value='"+o[i].id+"'>"+o[i].DoorName+"</option>"
                );
        }
    )
}

var CommonTable={
    "Select":function () {
        $("#CommonTable tr").remove();
        gl.Post(
            "Construct_Select",
            {
                "idTypeDoor":$("#DoorList").val()
            },
            function(str){
                var o=JSON.parse(str);
                for(var i in o)
                    $("#CommonTable").append(
                        "<tr idCommon='"+o[i].id+"'>" +
                            "<td>" +
                                "<span onclick='CommonTable.Remove(this)' class='glyphicon glyphicon-remove-circle'></span>"+
                            "</td>"+
                            "<td onclick='CommonTable.EditGroup(this)' Type='Group' idGroup='"+o[i].idGroup+"'>"+o[i].GroupName+"</td>"+
                            "<td Type='TypeCalc' TypeCalc='"+o[i].TypeCalc+"'>" +
                                "<select class='form-control'>" +
                                    CommonTable.TypeCalcSelect(o[i].TypeCalc)+
                                "</select>"+
                            "</td>"+
                            "<td Type='Count'><input class='form-control' value='"+o[i].Count+"' </td>"+
                        "</tr>"
                    )
            }
        )
    },
    "addGroup":function () {
        GroupDialog.Open(undefined);
    },
    "EditGroup":function (el) {
        GroupDialog.Open(el);
    },
    "Save":function(){
        var flagErr=false;
        for(let i=0;i<$("#CommonTable tr").length;i++){
            let TR=$("#CommonTable tr:eq("+i+")");
            if(TR.find("td[TypeCalc] select").val()==-1) flagErr=true;
            if(TR.find("td[Count] input").val()=="") flagErr=true;
        };
        switch (flagErr){
            case true:
                alert("Заполненны не все поляя");
                break;
            case false:
                let arrTR=new Array();
                for(let i=0;i<$("#CommonTable tr").length;i++){
                        let TR=$("#CommonTable tr:eq("+i+")");
                        arrTR[i]={
                            "idCommon":TR.attr("idCommon"),
                            "Remove":TR.attr("Remove")!==undefined ? 1 : 0,
                            "idGroup":TR.find("td[Type=Group]").attr("idGroup"),
                            "TypeCalc":TR.find("td[Type=TypeCalc] select").val(),
                            "Count":TR.find("td[Type=Count] input").val()
                        };
                    };
                console.log(arrTR);
                gl.Post(
                    "Construct_Save",
                    {
                        "idTypeDoor":$("#DoorList").val(),
                        "CommonList":arrTR
                    },
                    function(str){
                        var o=JSON.parse(str);
                        switch (o.Status){
                            case "Success":
                                alert("Успешно сохранена");
                                break;
                            case "Error":
                                alert("При сохранении произощла ошибка:"+o.Note);
                                break;
                        }
                    }
                )
                break;
        }
    },
    "Remove":function (el) {
        var TR=$(el).parent().parent();
        if(confirm("Удалить: "+TR.find("td[Type=Group]").text()+"?"))
            switch (TR.attr("idCommon")){
                case -1:
                    TR.remove();
                    break;
                default:
                    TR.attr("Remove","1");
                    TR.hide();
                    break;
            }
    },
    "TypeCalc":{
        1:"м2",
        2:"м погонный",
        3:"на изделие",
        4:"петля",
        5:"окно",
        6:"окно м погонный"
    },
    "TypeCalcSelect":function (TypeCalc) {
        var Options="<option value='-1'></option>";
        var arrTypeCalc=CommonTable.TypeCalc;
        for(var i in arrTypeCalc)
            Options+="<option value='"+i+"' "+(i==TypeCalc ? "selected" : "")+">"+arrTypeCalc[i]+"</option>";
        return Options;
    }
}

var GroupDialog={
    "TR":null,
    "Open":function (el) {
        GroupDialog.TR=el===undefined ? null : $(el);
        $("#GroupDialogTable tr").attr("class","");
        $("#GroupDialog").modal("show");
    },
    "Close":function(){
        var TRSelected=$("#GroupDialogTable tr[class=success]");
        if(TRSelected.length>0)
            switch (GroupDialog.TR){
                case null:
                    $("#CommonTable").append(
                        "<tr idCommon='-1'>" +
                            "<td>"+
                                "<span onclick='CommonTable.Remove(this)' class='glyphicon glyphicon-remove-circle'></span>"+
                            "</td>"+
                            "<td onclick='CommonTable.EditGroup(this)' Type='Group' idGroup='"+TRSelected.attr("idGroup")+"'>"+TRSelected.text()+"</td>"+
                            "<td Type='TypeCalc'>" +
                                "<select class='form-control'>" +
                                    CommonTable.TypeCalcSelect(-1)+
                                "</select>"+
                            "</td>"+
                            "<td Type='Count'><input class='form-control' value='' </td>"+
                        "</tr>"
                    );
                    break;
                default:
                    GroupDialog.TR.attr("idGroup",TRSelected.attr("idGroup"));
                    GroupDialog.TR.text(TRSelected.text());
                    break;
            };
        $("#GroupDialog").modal("hide");
    },
    "TRSelected":function (el) {
        $("#GroupDialogTable tr").attr("class","");
        $(el).attr("class","success");
    },
    "TRDbClick":function (el) {
        GroupDialog.TRSelected(el);
        GroupDialog.Close();
    }
};
