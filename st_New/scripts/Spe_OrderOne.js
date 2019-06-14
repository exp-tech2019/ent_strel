$(document).ready(function () {
    $("#BtnCalc").click(function(){
        DoorTable.Calc();
    })
    
    gl.Post(
        "Common_TypeDoor",
        {},
        function (str) {
            var o=JSON.parse(str);
            for(var i in o)
                $("#DoorFindTypeDoor").append(
                    "<option value='"+o[i].DoorName+"'>"+o[i].DoorName+"</option>"
                );
        }
    );
    $(document).on("input","#DoorFindNumPP",function(){
        Find();
    });
    $("#DoorFindTypeDoor").change(function(){
        Find();
    });
    $(document).on("input","#DoorFindH",function(){
        Find();
    });
    $(document).on("input","#DoorFindW",function(){
        Find();
    });
    $("#DoorFindOpen").change(function () {
        Find();
    });
    function Find(){
        var NumPP=$("#DoorFindNumPP").val()!="" ? $("#DoorFindNumPP").val() : null;
        var TypeDoor=$("#DoorFindTypeDoor").val()!="" ? $("#DoorFindTypeDoor").val() : null;
        var H=$("#DoorFindH").val()!="" ? $("#DoorFindH").val() : null;
        var W=$("#DoorFindW").val()!="" ? $("#DoorFindW").val() : null;
        var Open=$("#DoorFindOpen").val()!="" ? $("#DoorFindOpen").val() : null;
        for(var i=0; i<$("#DoorTable tr[Type=Door]").length;i++){
            var TR=$("#DoorTable tr[Type=Door]:eq("+i+")");
            var flagView=true;
            if(TR.find("td[Type=NumPP]").text()!=NumPP & NumPP!==null)
                flagView=false;
            if(TR.find("td[Type=Name]").text()!=TypeDoor & TypeDoor!==null)
                flagView=false;
            if(TR.find("td[Type=Size]").attr("H")!=H & H!==null)
                flagView=false;
            if(TR.find("td[Type=Size]").attr("W")!=W & W!==null)
                flagView=false;
            if(TR.find("td[Type=Open]").text()!=Open & Open!==null)
                flagView=false;
            switch (flagView){
                case false:
                    TR.hide();
                    TR.next().hide();
                    break;
                case true:
                    TR.show();
                    TR.next().show();
                    break;
            }
        }
    }
    gl.Post(
        "Spe_OrderOne_Select",
        {
            idOrder:$("#idOrder").val()
        },
        function(str){
            var o=JSON.parse(str);
            $("#Blank").text(o.Blank);
            $("#BlankDate").text(o.BlankDate);
            $("#Shet").text(o.Shet);
            $("#Zakaz").text(o.Zakaz);
            var d=o.Doors;
            for(let i in d) {
                var strGroups="";
                var Groups=d[i].Groups;
                for(let j in Groups){
                    var strGoods="";
                    var Goods=Groups[j].Goods;
                    for(let k in Goods)
                        strGoods+="<li idDoorGood='"+Goods[k].idDoorGood+"' idGood='"+Goods[k].idGood+"'>" +
                                "<span class='glyphicon glyphicon-trash'></span>"+
                                Goods[k].GoodName+
                            "</li>";
                    strGroups+="<li idDoorGroup='"+Groups[j].idDoorGroup+"' idGroup='"+Groups[j].idGroup+"'>"+
                            "<span onclick='DoorTable.Group_Remove(this)' class='glyphicon glyphicon-trash'></span>"+
                            "<span onclick='DoorTable.GoodAddStart(this)' class='glyphicon glyphicon-plus'></span>"+
                            "<div Text>"+Groups[j].GroupName+"</div>"+
                            "<select class='form-control'>"+TypeCalcStr(Groups[j].TypeCalc)+"</select>"+
                            "<input class='form-control' value='"+Groups[j].Count+"'>"+
                            "<span BtnSave class='glyphicon glyphicon-floppy-disk' style='margin-left:5px;  display: none'></span>"+
                            "<ul GoodList>"+strGoods+"</ul>"+
                        "</li>";
                };
                $("#DoorTable").append(
                    "<tr idDoor='" + d[i].idDoor + "' Type='Door'>" +
                        "<td>" +
                            "<span onclick='DoorTable.Group_AddStart(this)' class='glyphicon glyphicon-plus'></span>" +
                        "</td>" +
                        "<td Type='NumPP'>" + d[i].NumPP + "</td>" +
                        "<td Type='Name'>" + d[i].Name + "</td>" +
                        "<td Type='Count'>" + d[i].Count + "</td>" +
                        "<td Type='Size' H='" + d[i].H + "' W='" + d[i].W + "'>" + d[i].Size + "</td>" +
                        "<td Type='Open'>" + d[i].Open + "</td>" +
                        "<td></td>" +
                    "</tr>" +
                    "<tr idDoor='" + d[i].idDoor + "' Type='Spe'>" +
                        "<td colspan='7'>" +
                            "<ul GroupList class='Spe_OrderOne'>"+strGroups+"</ul>" +
                        "</td>" +
                    "</tr>"
                );
            };
            $("#DoorTable li[idDoorGroup] select").change(function () {
                $(this).parent().find("span[BtnSave]").show();
            });
            $(document).on("input","#DoorTable li[idDoorGroup] input",function(){
                $(this).parent().find("span[BtnSave]").show();
            });
            $("#DoorTable li[idDoorGroup] span[BtnSave]").click(function(){
                DoorTable.GroupChangeSave(this);
            });
            $("#DoorTable ul[GoodList] li span").click(function () {
                DoorTable.GoodRemove($(this).parent());
            })
        }
    )
})

var DoorTable={
    idDoor:null,
    Group_AddStart:function (el) {
        DoorTable.idDoor= $(el).parent().parent().attr("idDoor");
        GroupDialog.Open($(el).parent().parent().attr("idDoor"));
    },
    Group_AddEnd:function (idDoorGroup, idGroup, GroupName) {
        $("#DoorTable tr[idDoor="+GroupDialog.idDoor+"][Type=Spe] ul[GroupList]").append(
            "<li idDoorGroup='"+idDoorGroup+"' idGroup='"+idGroup+"'>"+
                "<span onclick='DoorTable.Group_Remove(this)' class='glyphicon glyphicon-trash'></span>"+
                "<span onclick='DoorTable.GoodAddStart(this)' class='glyphicon glyphicon-plus'></span>"+
                "<div Text>"+GroupName+"</div>"+
                "<select class='form-control'>"+TypeCalcStr()+"</select>"+
                "<input class='form-control' value='0'>"+
                "<span BtnSave class='glyphicon glyphicon-floppy-disk' style='margin-left:5px;  display: none'></span>"+
                "<ul GoodList></ul>"+
            "</li>"
        );
        $(document).on("input","#DoorTable li[idDoorGroup="+idDoorGroup+"] input",function(){
            $(this).parent().find("span[BtnSave]").show();
        });
        $("#DoorTable li[idDoorGroup="+idDoorGroup+"] select").change(function(){
            $(this).parent().find("span[BtnSave]").show();
        });
        $("#DoorTable li[idDoorGroup="+idDoorGroup+"] span[BtnSave]").click(function(){
            DoorTable.GroupChangeSave(this);
        });
    },
    GroupChangeSave:function (el) {
        var TR=$(el).parent();
        gl.Post(
            "Spe_OrderOne_SaveGroup",
            {
                idDoorGroup:TR.attr("idDoorGroup"),
                TypeCalc:TR.find("select").val(),
                Count:TR.find("input").val()
            },
            function(str){
                var o=JSON.parse(str);
                switch (o.Status){
                    case "Success":
                        TR.find("span[BtnSave]").hide();
                        break;
                    case "Error":
                        alert("Ошибка "+o.Note);
                        break;
                }
            }
        )
    },
    Group_Remove:function (el) {
        var LiGroup=$(el).parent();
        if(confirm("Удалить группу: "+$(el).text()+"?")){
            switch (LiGroup.find("ul li").length){
                case 0:
                    gl.Post(
                        "Spe_OrderOne_RemoveGroup",
                        {
                            idDoorGroup:LiGroup.attr("idDoorGroup")
                        },
                        function(str){
                            var o=JSON.parse(str);
                            switch (o.Status){
                                case "Success":
                                    LiGroup.remove();
                                    break;
                                case "Error":
                                    alert("Ошибка "+o.Note);
                                    break;
                            }
                        }
                    );
                    break;
                default:
                    alert("Нельзя удалить группу в которой содкжится номенклатура");
                    break;
            }
        }
    },
    GoodAddStart:function (el) {
        GoodDialog.Open($(el).parent());
    },
    GoodRemove:function (liGood) {
        if(confirm("Удалить номенклатуру: "+liGood.text()+"?"))
            gl.Post(
                "Spe_OrderOne_RemoveGood",
                {
                    idDoorGood:liGood.attr("idDoorGood")
                },
                function (str) {
                    var o=JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            TemplateSave(liGood.parent().parent().parent().parent().parent(), liGood.attr("idDoorGood"));
                            liGood.remove();
                            break;
                        case "Error":
                            alert("Ошибка: "+o.Note);
                            break;
                    }
                }
            )
    },
    Calc:function () {
        var Doors=new Array();
        for(var i=0;i<$("#DoorTable tr[Type=Door]").length;i++) {
            let TR=$("#DoorTable tr[Type=Door]:eq("+i+")");
            Doors[i] = {
                "idDoor": TR.attr("idDoor"),
                "Name": TR.find("td[Type=Name]").text(),
                "H": TR.find("td[Type=Size]").attr("H"),
                "W": TR.find("td[Type=Size]").attr("W"),
                "Open": TR.find("td[Type=Open]").text()
            };
        };
        gl.Post(
            "Spe_OrderOne_Calc",
            {
                "Doors":Doors
            },
            function(str){
                var o=JSON.parse(str);
                switch (o.Status){
                    case "Success":
                        location.reload();
                        break;
                    case "error":
                        alert("Ошибка: "+o.Note);
                        break;
                }
            }
        )
    }
}

var GroupDialog={
    Select:function () {
        $("#GroupDialogTable tr").remove();
        gl.Post(
            "GroupGood_Select",
            {},
            function (str) {
                var o = JSON.parse(str);
                for(let i in o)
                    $("#GroupDialogTable").append(
                        "<tr idGroup='" + o[i].idGroup + "'><td>" + o[i].GroupName + "</td></tr>"
                    );
                $("#GroupDialogTable tr").click(function(){
                    GroupDialog.TRClick(this);
                });
                $("#GroupDialogTable tr").dblclick(function(){
                    GroupDialog.TRDbClick(this);
                })
            }
        )
    },
    idDoor:null,
    Open:function (idDoor) {
        GroupDialog.idDoor=idDoor;
        GroupDialog.Select();
        $("#GroupDialog").modal("show");
    },
    TRClick:function(el){
        $("#GroupDialogTable tr").attr("class","");
        $(el).attr("class","success");
    },
    TRDbClick:function (el) {
        GroupDialog.TRClick(el);
        GroupDialog.Close();
    },
    Close:function () {
        var TRSelect=$("#GroupDialogTable tr[class=success]");
        if(TRSelect.length>0){
            gl.Post(
                "Spe_OrderOne_AddGroup",
                {
                    idDoor:GroupDialog.idDoor,
                    idGroup:TRSelect.attr("idGroup")
                },
                function(str){
                    var o =JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            DoorTable.Group_AddEnd(o.Note,TRSelect.attr("idGroup"), TRSelect.text());
                            $("#GroupDialog").modal("hide");
                            break;
                        case "Error":
                            alert("Ошибка: "+o.Note);
                            break;
                    }
                }
            )
        };
    }
}

var GoodDialog={
    elGroup:null,
    idDoorGroup:null,
    idGroup:null,
    Select:function () {
        $("#GoodDialogTable tr").remove();
        gl.Post(
            "GroupGood_Select",
            {},
            function (str) {
                var idGroup=GoodDialog.idGroup;
                var o=JSON.parse(str);
                for(let i in o)
                    if(o[i].idGroup==idGroup) {
                        let g = o[i].GoodList;
                        for (let j in g)
                            $("#GoodDialogTable").append(
                                "<tr idGood=" + g[j].idGood + ">" +
                                    "<td>"+g[j].GoodName+"</td>" +
                                "</tr>"
                            );
                        break;
                    };
                $("#GoodDialogTable tr").click(function () {
                    GoodDialog.TRClick(this);
                });
                $("#GoodDialogTable tr").dblclick(function () {
                    GoodDialog.TRDbClick(this);
                });
            }
        )
    },
    Open:function (elGroup) {
        GoodDialog.elGroup=$(elGroup);
        GoodDialog.idDoorGroup=$(elGroup).attr("idDoorGroup");
        GoodDialog.idGroup=$(elGroup).attr("idGroup");
        GoodDialog.Select();
        $("#GoodDialog").modal("show");
    },
    TRClick:function(el){
        $("#GoodDialogTable tr").attr("class","");
        $(el).attr("class","success");
    },
    TRDbClick:function (el) {
        GoodDialog.TRClick(el);
        GoodDialog.Close();
    },
    Close:function () {
        var TR=$("#GoodDialogTable tr[class=success]");
        if(TR.length==1){
            gl.Post(
                "Spe_OrderOne_AddGood",
                {
                    idDoorGroup:GoodDialog.idDoorGroup,
                    idGood:TR.attr("idGood")
                },
                function (str) {
                    var o=JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            GoodDialog.elGroup.find("ul[GoodList]").append(
                                "<li idDoorGood='"+o.Note+"' idGood='"+TR.attr("idGood")+"'>" +
                                    "<span class='glyphicon glyphicon-trash'></span>"+
                                    TR.text()+
                                "</li>"
                            );
                            GoodDialog.elGroup.find("ul[GoodList] li[idDoorGood="+o.Note+"] span").click(function(){
                                DoorTable.GoodRemove($(this).parent());
                            });
                            $("#GoodDialog").modal("hide");
                            TemplateSave(GoodDialog.elGroup.parent().parent().parent());
                            break;
                        case "Error":
                            alert("Ошибка: "+o.Note);
                            break;
                    }
                }
            )
        };
    }
}

function TypeCalcStr(idTypeCalc) {
    idTypeCalc=idTypeCalc!==undefined ? idTypeCalc : 0;
    return "<option value='0' "+(idTypeCalc==0 ? "selected" : "")+"></option>"+
        "<option value='1' "+(idTypeCalc==1 ? "selected" : "")+">м2</option>"+
        "<option value='2' "+(idTypeCalc==2 ? "selected" : "")+">м погонный</option>"+
        "<option value='3' "+(idTypeCalc==3 ? "selected" : "")+">на изделие</option>"+
        "<option value='4' "+(idTypeCalc==4 ? "selected" : "")+">петля</option>"+
        "<option value='5' "+(idTypeCalc==5 ? "selected" : "")+">окно</option>"+
        "<option value='6' "+(idTypeCalc==6 ? "selected" : "")+">окно м погонный</option>";
}

function TemplateSave(elTR,idGoodRemove){
    var idDoor=elTR.attr("idDoor");
    var TemplateList=new Array();
    var ArrGroup=new Array(); var gri=0;
    var c=0;
    for(var i=0; i<$("#DoorTable tr[idDoor="+idDoor+"][Type=Spe]").length; i++){
        let TRDoor=$("#DoorTable tr[idDoor="+idDoor+"][Type=Door]:eq("+i+")");
        var TypeDoor=TRDoor.find("td[Type=Name]").text();
        var TRSpe=TRDoor.next();
        var GroupList=new Array();
        for(var j=0; j<TRSpe.find("ul[GroupList] li[idDoorGroup]").length;j++){
            var Group=TRSpe.find("ul[GroupList] li[idDoorGroup]:eq("+j+")");
            if(ArrGroup.indexOf(Group.attr("idGroup"))==-1) {
                for (var k = 0; k < Group.find("li[idDoorGood]").length; k++)
                    if (idGoodRemove != Group.find("li[idDoorGood]:eq(" + k + ")").attr("idDoorGood")) {
                        ArrGroup.push(Group.attr("idGroup"));
                        TemplateList[c++] = {
                            TypeDoor: TypeDoor,
                            idGroup: Group.attr("idGroup"),
                            idGood: Group.find("li[idDoorGood]:eq(" + k + ")").attr("idGood")
                        };
                    };
            };
        };
    };
    gl.Post(
        "Spe_OrderOne_TemplateSave",
        {
            TemplateList:TemplateList
        },
        function (str) {

        }
    )

}