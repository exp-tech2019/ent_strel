var TypeDoors=[];
var Dovod=[];
var OpenDoors=[];
var Nalichnik=[];

$(document).ready(function(){
    $.post(
        "REST/Select_Manuals.php",
        {},
        function(o){
            TypeDoors=o.TypeDoor;
            Dovod=o.Dovod;
            OpenDoors=o.OpenDoor;
            Nalichnik=o.Nalichnik;
            DoorSize.Select();
            TypeDoors.forEach(function(item){
                $("#SizeAdd_TypeDoor").append(
                    "<option>"+item.Name+"</option>"
                );
            })
        }
    );

    //--Доводчик
    $("#Tab3 button[Type=BtnSave]").hide();
    DovodTab.Select();
});

var DoorSize={
    Select:function(){
        gl.Post(
            "Construct_DoorSize_Select",
            {},
            function(str){
                var o =JSON.parse(str); var i=-1;
                while(o[++i]!=null)
                    DoorSize.AddRow(
                        o[i].idSize,
                        o[i].TypeDoor,
                        o[i].HWith,
                        o[i].HBy,
                        o[i].WWith,
                        o[i].WBy,
                        o[i].Stvorka,
                        o[i].Framug,
                        o[i].Sum,
                        o[i].SumM2
                    )
            }
        )
    },
    Add:function(){
        if($("#SizeAdd_TypeDoor").val()!="" & $("#SizeAdd_Sum").val()!="" & $("#SizeAdd_SumM2").val()!="" & ($("#SizeAdd_HWith").val()!="" || $("#SizeAdd_WWith").val()!=""))
        gl.Post(
            "Construct_DoorSize_Save",
            {
                id:-1,
                TypeDoor:$("#SizeAdd_TypeDoor").val(),

                HWith:$("#SizeAdd_HWith").val(),
                HBy:$("#SizeAdd_HBy").val(),
                WWith:$("#SizeAdd_WWith").val(),
                WBy:$("#SizeAdd_WBy").val(),

                Stvorka:$("#SizeAdd_Stovrka").val(),
                Framug:$("#SizeAdd_Framug").val(),
                Sum:$("#SizeAdd_Sum").val(),
                SumM2:$("#SizeAdd_SumM2").val()
            },
            function(str){
                var o =JSON.parse(str);
                switch (o.Status){
                    case "Success":
                        DoorSize.AddRow(
                            o.Note,
                            $("#SizeAdd_TypeDoor").val(),
                            $("#SizeAdd_HWith").val(),
                            $("#SizeAdd_HBy").val(),
                            $("#SizeAdd_WWith").val(),
                            $("#SizeAdd_WBy").val(),
                            $("#SizeAdd_Stovrka").val(),
                            $("#SizeAdd_Framug").val(),
                            $("#SizeAdd_Sum").val(),
                            $("#SizeAdd_SumM2").val()
                        )
                        break;
                    case "Error":
                        alert(o.Note);
                        break;
                }
            }
        )
    },
    EditStart:function(elInp){
        $(elInp).parent().parent().find("td[Type=BtnSave] button").show();
        if(elInp.nodeName=="INPUT")
            elInp.value=StrReplcae(elInp.value);
    },
    Edit:function(el){
        var TR=$(el).parent().parent();
        if(TR.find("td[Type=TypeDoor] select").val()!="" & TR.find("td[Type=Sum] input").val()!="")
            gl.Post(
                "Construct_DoorSize_Save",
                {
                    id:TR.attr("idSize"),
                    TypeDoor:TR.find("td[Type=TypeDoor] select").val(),

                    HWith:TR.find("td[Type=HWith] input").val(),
                    HBy:TR.find("td[Type=HBy] input").val(),
                    WWith:TR.find("td[Type=WWith] input").val(),
                    WBy:TR.find("td[Type=WBy] input").val(),

                    Stvorka:TR.find("td[Type=Stvorka] select").val(),
                    Framug:TR.find("td[Type=Framug] select").val(),
                    Sum:TR.find("td[Type=Sum] input").val(),
                    SumM2:TR.find("td[Type=SumM2] input").val()
                },
                function(str){
                    var o =JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            TR.find("td[Type=BtnSave] button").hide();
                            break;
                        case "Error":
                            alert(o.Note);
                            break;
                    }
                }
            )
    },
    Remove:function (el) {
        if(confirm("Удалить позоцию?")){
            var TR=$(el).parent().parent();
            gl.Post(
                "Construct_DoorSize_Remove",
                {
                    idDoorSize:TR.attr("idSize")
                },
                function (str) {
                    var o=JSON.parse(str);
                    switch(o.Status){
                        case "Success":
                            TR.remove();
                            break;
                        case "Error":
                            alert(o.Note);
                            break;
                    }
                }
            )
        }
    },
    AddRow:function(id, TypeDoor, HWith, HBy, WWith, WBy, Stvorka, Framug, Sum, SumM2){
        var TypeDoorOption=function(){
            var s="";
            for(let i=0;i<TypeDoors.length;i++)
                s=s+"<option "+(TypeDoors[i].Name==TypeDoor ? "selected" : "")+">"+TypeDoors[i].Name+"</option>";
            return "<select onchange='DoorSize.EditStart(this)' class='form-control input-sm'>"+s+"</select>";
        };
        var StvorkaOption=function(val){
            var s=""; var StvorkaArr=["Одностворчатая", "Двухстворчатая"];
            for(let i=0; i<StvorkaArr.length;i++)
                s+="<option "+(StvorkaArr[i]==val ? "selected" : "")+">"+StvorkaArr[i]+"</option>";
            return "<select onchange='DoorSize.EditStart(this)' class='form-control input-sm'>"+s+"</select>";
        };
        var YesNoOption=function(val){
            var s=""; var arr=["","Да", "Нет"];
            for(let i=0; i<arr.length;i++)
                s+="<option "+(arr[i]==val ? "selected" : "")+">"+arr[i]+"</option>";
            return "<select onchange='DoorSize.EditStart(this)' class='form-control input-sm'>"+s+"</select>";
        };
        $("#Size_Table").prepend(
            "<tr idSize='"+id+"'>" +
                "<td Type='TypeDoor'>"+
                    TypeDoorOption()+
                "</td>"+
                "<td Type='HWith'><input value='"+HWith+"' oninput='DoorSize.EditStart(this)' class='form-control input-sm'></td>"+
                "<td Type='HBy'><input value='"+HBy+"' oninput='DoorSize.EditStart(this)' class='form-control input-sm'></td>"+
                "<td Type='WWith'><input value='"+WWith+"' oninput='DoorSize.EditStart(this)' class='form-control input-sm'></td>"+
                "<td Type='WBy'><input value='"+WBy+"' oninput='DoorSize.EditStart(this)' class='form-control input-sm'></td>"+
                "<td Type='Stvorka'>" +
                    StvorkaOption(Stvorka)+
                "</td>"+
                "<td Type='Framug'>" +
                    YesNoOption(Framug)+
                "</td>"+
                "<td Type='Sum'><input value='"+StrReplcae(Sum)+"' oninput='DoorSize.EditStart(this)' class='form-control input-sm'></td>"+
                "<td Type='SumM2'><input value='"+StrReplcae(SumM2)+"' oninput='DoorSize.EditStart(this)' class='form-control input-sm'></td>"+
                "<td Type='BtnRemove'>"+
                    "<button onclick='DoorSize.Remove(this)' Type='BtnRemove' class='btn btn-default btn-sm'>" +
                        "<span class='fa fa-remove'></span>"+
                    "</button>"+
                "</td>"+
                "<td Type='BtnSave'>" +
                    "<button onclick='DoorSize.Edit(this)' Type='BtnSave' class='btn btn-default btn-sm' style='display: none'>" +
                        "<span class='fa fa-save'></span>"+
                    "</button>"+
                "</td>"+
            "</tr>"
        )
    }
};

var DovodTab={
    Select:function () {
        gl.Post(
            "Construct_Dovod_Select",
            {},
            function(str){
                var o=JSON.parse(str);
                $("#Dovod_WithoutDovod").val(StrReplcae(o.WithoutDovod));
                $("#Dovod_WorkDovod").val(StrReplcae(o.WorkDovod));
                $("#Dovod_Dovod").val(StrReplcae(o.Dovod));
            }
        )
    },
    Save:function (el) {
        gl.Post(
            "Construct_Dovod_Save",
            {
                WithoutDovod:$("#Dovod_WithoutDovod").val(),
                WorkDovod:$("#Dovod_WorkDovod").val(),
                Dovod:$("#Dovod_Dovod").val()
            },
            function(str){
                var o=JSON.parse(str);
                switch (o.Status){
                    case "Success":
                        $(el).parent().find("button").hide();
                        break;
                    case "Error":
                        alert("Ошибка: "+o.Note);
                        break;
                }
            }
        )
    },
    Change:function (el) {
        $(el).val(StrReplcae($(el).val()));
        $(el).parent().parent().find("button").show();
    }
};

var RAL={
    Add:function () {
        if($("#RAL_TypeCalc").val()!="-1" & $("#RAL_Sum").val()!="" & $("#RAL_Name").val()!="")
            gl.Post(
                "Construct_RAL_Save",
                {
                    idRAL:-1,
                    Name:$("#RAL_Name").val(),
                    TypeCalc:$("#RAL_TypeCalc").val(),
                    Sum:$("#RAL_Sum").val()
                },
                function (str) {
                    var o=JSON.parse(str);
                    switch(o.Status){
                        case "Success":
                            RAL.AddRow(o.Note, $("#RAL_Name").val(), $("#RAL_TypeCalc").val(), $("#RAL_Sum").val());
                            break;
                        case "Error":
                            alert(o.Note);
                            break;
                    }
                }
            )
    },
    Select:function () {
        $("#RAL_Table tr").remove();
        gl.Post(
            "Construct_RAL_Select",
            {},
            function(str){
                var o=JSON.parse(str); var i=-1;
                while(o[++i]!=null)
                    RAL.AddRow(
                        o[i].idRAL,
                        o[i].Name,
                        o[i].TypeCalc,
                        o[i].Sum
                    )
            }
        )
    },
    AddRow:function (idRAL,Name,TypeCalc,Sum) {
        var Table=$("#RAL_Table");
        Table.append(
            "<tr idRAL="+idRAL+">" +
                "<td Type='Name'><input oninput='RAL.EditStart(this)' value='"+Name+"' class='form-control input-sm'></td>"+
                "<td Type='TypeCalc'>" +
                    "<select onchange='RAL.EditStart(this)' class='form-control input-sm'>" +
                        "<option value=-1></option>"+
                        "<option value='1' "+(TypeCalc=="1" ? "selected" : "")+">М2</option>"+
                        "<option value='2' "+(TypeCalc=="2" ? "selected" : "")+">Процент от стоимости</option>"+
                    "</select>"+
                "</td>"+
                "<td Type='Sum'><input oninput='RAL.EditStart(this)' value='"+Sum+"' class='form-control input-sm'></td>"+
                "<td>" +
                    "<button Type='BtnRemove' onclick='RAL.Remove(this)' class='btn btn-sm btn-default'><span class='fa fa-remove'></span></button>"+
                "</td>"+
                "<td>" +
                    "<button Type='BtnSave' onclick='RAL.Save(this)' class='btn btn-sm btn-default'><span class='fa fa-save'></span></button>"+
                "</td>"+
            "</tr>"
        );
        Table.find("tr[idRAL="+idRAL+"] button[Type=BtnSave]").hide();
    },
    EditStart:function(el){
        $(el).parent().parent().find("button").show();
    },
    Save:function(el){
        var TR=$(el).parent().parent();
        var idRAL=TR.attr("idRAL");
        var Name=TR.find("td[Type=Name] input").val();
        var TypeCalc=TR.find("td[Type=TypeCalc] select").val();
        var Sum=TR.find("td[Type=Sum] input").val();
        if(Name!="" & TypeCalc!=-1 & Sum!="")
            gl.Post(
                "Construct_RAL_Save",
                {
                    idRAL:idRAL,
                    Name:Name,
                    TypeCalc:TypeCalc,
                    Sum:Sum
                },
                function(str){
                    var o=JSON.parse(str);
                    switch(o.Status){
                        case "Success":
                            $(el).hide();
                            break;
                        case "Error":
                            alert(o.Note);
                            break;
                    }
                }
            )
    },
    Remove:function (el) {
        if(confirm("Удалить строку?")){
            var TR=$(el).parent().parent();
            gl.Post(
                "Construct_RAL_Remove",
                {
                    idRAL:TR.attr("idRAL")
                },
                function(str){
                    var o=JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            TR.remove();
                            break;
                        case "Error":
                            alert(o.Note);
                            break;
                    }
                }
            )
        }
    }
}

var Naves={
    Select:function(){
        $("#NavesTable tr").remove();
        gl.Post(
            "Construct_Naves_Select",
            {},
            function (str) {
                var o=JSON.parse(str); var i=-1;
                while(o[++i]!=null)
                    Naves.RowAdd(
                        o[i].idNaves,
                        o[i].Name,
                        o[i].HWith,
                        o[i].HBy,
                        o[i].WWith,
                        o[i].WBy,
                        o[i].Stvorka,
                        o[i].Sum
                    );
            }
        )
    },
    Add:function () {
        if($("#NavesAdd_Name").val()!="" & $("#NavesAdd_Sum").val()!="")
            gl.Post(
                "Construct_Naves_Save",
                {
                    idNaves:-1,
                    Name:$("#NavesAdd_Name").val(),
                    HWith:$("#NavesAdd_HWith").val(),
                    HBy:$("#NavesAdd_HBy").val(),
                    WWith:$("#NavesAdd_WWith").val(),
                    WBy:$("#NavesAdd_WBy").val(),
                    Stvorka:$("#NavesAdd_Stvorka").val(),
                    Sum:StrReplcae( $("#NavesAdd_Sum").val())
                },
                function(str){
                    var o=JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            Naves.RowAdd(
                                o.Note,
                                $("#NavesAdd_Name").val(),
                                $("#NavesAdd_HWith").val(),
                                $("#NavesAdd_HBy").val(),
                                $("#NavesAdd_WWith").val(),
                                $("#NavesAdd_WBy").val(),
                                $("#NavesAdd_Stvorka").val(),
                                StrReplcae( $("#NavesAdd_Sum").val())
                            );
                            break;
                        case "Error":
                            alert(o.Note);
                            break;
                    }
                }
            )
    },
    EditStart:function (el) {
        var TR=$(el).parent().parent();
        TR.find("button[Type=BtnSave]").show();
        if(el.nodeName=="INPUT")
            el.value=StrReplcae(el.value);
    },
    Edit:function (el) {
        var TR=$(el).parent().parent();
        if(TR.find("td[Type=Name] input").val()!="" & TR.find("td[Type=Sum] input").val()!="")
            gl.Post(
                "Construct_Naves_Save",
                {
                    idNaves:TR.attr("idNaves"),
                    Name:TR.find("td[Type=Name] input").val(),
                    HWith:TR.find("td[Type=HWith] input").val(),
                    HBy:TR.find("td[Type=HBy] input").val(),
                    WWith:TR.find("td[Type=WWith] input").val(),
                    WBy:TR.find("td[Type=WBy] input").val(),
                    Stvorka:TR.find("td[Type=Stvorka] select").val(),
                    Sum:TR.find("td[Type=Sum] input").val()
                },
                function (str) {
                    var o=JSON.parse(str);
                    switch (o.Status){
                        case"Success":
                            TR.find("td[Type=BtnSave] button").hide();
                            break;
                        case "Error":
                            alert(o.Note);
                            break;
                    }
                }
            )
    },
    RowAdd:function (idNaves, Name, HWith, HBy, WWith, WBy, Stvorka, Sum) {
        var StvorkaOption=function(val){
            var s=""; var StvorkaArr=["","Одностворчатая", "Двухстворчатая"];
            for(let i=0; i<StvorkaArr.length;i++)
                s+="<option value='"+i+"' "+(i==val ? "selected" : "")+">"+StvorkaArr[i]+"</option>";
            return "<select onchange='Naves.EditStart(this)' class='form-control input-sm'>"+s+"</select>";
        };
        $("#NavesTable").prepend(
            "<tr idNaves='"+idNaves+"'>" +
                "<td Type='Name'><input oninput='Naves.EditStart(this)' class='form-control input-sm' value='"+Name+"'></td>"+
                "<td Type='HWith'><input oninput='Naves.EditStart(this)' class='form-control input-sm' value='"+HWith+"'></td>"+
                "<td Type='HBy'><input oninput='Naves.EditStart(this)' class='form-control input-sm' value='"+HBy+"'></td>"+
                "<td Type='WWith'><input oninput='Naves.EditStart(this)' class='form-control input-sm' value='"+WWith+"'></td>"+
                "<td Type='WBy'><input oninput='Naves.EditStart(this)' class='form-control input-sm' value='"+WBy+"'></td>"+
                "<td Type='Stvorka'>"+
                    StvorkaOption(Stvorka)+
                "</td>"+
                "<td Type='Sum'><input oninput='Naves.EditStart(this)' class='form-control input-sm' value='"+StrReplcae(Sum)+"'></td>"+
                "<td Type='BtnRemove'>"+
                    "<button onclick='Naves.Remove(this)' Type='BtnRemove' class='btn btn-default btn-sm'>" +
                        "<span class='fa fa-remove'></span>"+
                    "</button>"+
                "</td>"+
                "<td Type='BtnSave'>" +
                    "<button onclick='Naves.Edit(this)' Type='BtnSave' class='btn btn-default btn-sm' style='display: none'>" +
                        "<span class='fa fa-save'></span>"+
                    "</button>"+
                "</td>"+
            "</tr>"
        );
    },
    Remove:function (el) {
        var TR=$(el).parent().parent();
        if(confirm("Удалить параметр?"))
            gl.Post(
                "Construct_Naves_Remove",
                {
                    idNaves:TR.attr("idNaves")
                },
                function (str) {
                    var o=JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            TR.remove();
                            break;
                        case "Error":
                            alert(o.Note);
                            break;
                    }
                }
            )
    }
}

var Furnitura={
    Select:function () {
        $("#FurinturaAdd_Table tr").remove();
        gl.Post(
            "Construct_Furnitura_Select",
            {},
            function(str){
                var o=JSON.parse(str); var i=-1;
                while(o[++i]!=null)
                    Furnitura.RowAdd(
                        o[i].idFurnitura,
                        o[i].Name,
                        o[i].Valute,
                        o[i].Sum
                    );
            }
        )
    },
    Add:function () {
        if($("#FurnituraAdd_Name").val()!="" & $("#FurnituraAdd_Sum").val()!="")
            gl.Post(
                "Construct_Furnitura_Save",
                {
                    idFurnitura:-1,
                    Name:$("#FurnituraAdd_Name").val(),
                    Valute:$("#FurinturaAdd_Valute").val(),
                    Sum:$("#FurnituraAdd_Sum").val()
                },
                function (str) {
                    var o=JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            Furnitura.RowAdd(
                                o.Note,
                                $("#FurnituraAdd_Name").val(),
                                $("#FurinturaAdd_Valute").val(),
                                $("#FurnituraAdd_Sum").val()
                            );
                            break;
                        case "Error":
                            alert(o.Note);
                            break;
                    }
                }
            )
    },
    EditStart:function (el) {
        var TR=$(el).parent().parent();
        TR.find("button[Type=BtnSave]").show();
        if(el.nodeName=="INPUT")
            el.value=StrReplcae(el.value);
    },
    Edit:function (el) {
        var TR=$(el).parent().parent();
        if(TR.find("td[Type=Name] input").val()!="" & TR.find("td[Type=Sum] input").val()!="")
            gl.Post(
                "Construct_Furnitura_Save",
                {
                    idFurnitura:TR.attr("idFurnitura"),
                    Name:TR.find("td[Type=Name] input").val(),
                    Valute:TR.find("td[Type=Valute] select").val(),
                    Sum:TR.find("td[Type=Sum] input").val()
                },
                function (str) {
                    var o=JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            TR.find("td[Type=BtnSave] button").hide();
                            break;
                        case "Error":
                            alert(o.Note);
                            break;
                    }
                }
            )
    },
    RowAdd:function (idFurnitura, Name, Valute, Sum) {
        var ValuteSelect=function(Valute){
            var Valutes=[];
            for(var i=0;i<$("#FurinturaAdd_Valute option").length;i++)
                Valutes.push($("#FurinturaAdd_Valute option:eq("+i+")").text());
            var options="";
            for(var v in Valutes)
                options += "<option " + (Valutes[v] == Valute ? "selected" : "") + ">" + Valutes[v] + "</option>";
            return "<select onchange='Furnitura.EditStart(this)' class='form-control input-sm'>"+options+"</select>";
        };
        ValuteSelect("RUB");
        $("#FurinturaAdd_Table").prepend(
            "<tr idFurnitura='"+idFurnitura+"'>" +
                "<td Type='Name'><input oninput='Furnitura.EditStart(this)' value='"+Name+"' class='form-control input-sm'></td>"+
                "<td Type='Valute'>"+ValuteSelect(Valute)+"</td>"+
                "<td Type='Sum'><input oninput='Furnitura.EditStart(this)' value='"+Sum+"' class='form-control input-sm'></td>"+
                "<td Type='BtnRemove'>"+
                    "<button onclick='Furnitura.Remove(this)' Type='BtnRemove' class='btn btn-default btn-sm'>" +
                        "<span class='fa fa-remove'></span>"+
                    "</button>"+
                "</td>"+
                "<td Type='BtnSave'>" +
                    "<button onclick='Furnitura.Edit(this)' Type='BtnSave' class='btn btn-default btn-sm' style='display: none'>" +
                        "<span class='fa fa-save'></span>"+
                    "</button>"+
                "</td>"+
            "</tr>"
        )
    },
    Remove:function (el) {
        if(confirm("Удалить позицию?")){
            var TR=$(el).parent().parent();
            gl.Post(
                "Construct_Furnitura_Remove",
                {
                    idFurnitura:TR.attr("idFurnitura")
                },
                function (str) {
                    var o=JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            TR.remove();
                            break;
                        case "Error":
                            alert(o.Note);
                            break;
                    }
                }
            )
        }
    }
}

var Glases={
    Select:function () {
        $("#GlassTable tr").remove();
        gl.Post(
            "Construct_Glases_Select",
            {

            },
            function (str) {
                var o=JSON.parse(str);
                for(var i in o)
                    Glases.RowAdd(
                        o[i].idGlass, o[i].GlassName, o[i].Sum, o[i].SumM2
                    );
            }
        )
    },
    Add:function () {
        if($("#GlassAdd_Name").val()!="" & ($("#GlassAdd_Sum").val()!="" || $("#GlassAdd_SumM2").val()!=""))
            gl.Post(
                "Construct_Glases_Save",
                {
                    idGlass:-1,
                    GlassName:$("#GlassAdd_Name").val(),
                    Sum:$("#GlassAdd_Sum").val(),
                    SumM2:$("#GlassAdd_SumM2").val()
                },
                function (str) {
                    var o=JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            Glases.RowAdd(
                                o.Note,
                                $("#GlassAdd_Name").val(),
                                $("#GlassAdd_Sum").val(),
                                $("#GlassAdd_SumM2").val(),
                            );
                            $("#GlassAdd_Name").val("");
                            $("#GlassAdd_Sum").val("");
                            $("#GlassAdd_SumM2").val("");
                            break;
                        case "Error":
                            alert(o.Note);
                            break;
                    }
                }
            )
    },
    RowAdd:function (idGlass, Name, Sum, SumM2) {
        $("#GlassTable").prepend(
            "<tr idGlass='"+idGlass+"'>" +
                "<td Type='GlassName'><input oninput='Glases.EditStart(this)' value='"+Name+"' class='form-control input-sm'></td>"+
                "<td Type='Sum'><input oninput='Glases.EditStart(this)' value='"+Sum+"' class='form-control input-sm'></td>"+
                "<td Type='SumM2'><input oninput='Glases.EditStart(this)' value='"+SumM2+"' class='form-control input-sm'></td>"+
                "<td Type=''>" +
                    "<button onclick='Glases.Edit(this)' Type='BtnSave' class='btn btn-primary btn-sm'><span class='fa fa-save'></span></button>" +
                "</td>"+
                "<td Type=''>" +
                    "<button onclick='Glases.Remove(this)' Type='BtnRemove' class='btn btn-primary btn-sm'><span class='fa fa-remove'></span></button>" +
                "</td>"+
            "</tr>"
        );
        $("#GlassTable button[Type=BtnSave]").hide();
    },
    EditStart:function (el) {
        $(el).parent().parent().find("button[Type=BtnSave]").show();
        if(el.nodeName=="INPUT")
            el.value=StrReplcae(el.value);
    },
    Edit:function (el) {
        var TR=$(el).parent().parent();
        var idGlass=TR.attr("idGlass");
        var GlassName=TR.find("td[Type=GlassName] input").val();
        var Sum=TR.find("td[Type=Sum] input").val();
        var SumM2=TR.find("td[Type=SumM2] input").val();
        if(GlassName!="" & (Sum!="" || SumM2!=""))
            gl.Post(
                "Construct_Glases_Save",
                {
                    idGlass:idGlass,
                    GlassName:GlassName,
                    Sum:Sum,
                    SumM2:SumM2
                },
                function (str) {
                    var o= JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            $(el).hide();
                            break;
                        case "Error":
                            alert(o.Note);
                            break;
                    }
                }
            )
    },
    Remove:function (el) {
        var TR=$(el).parent().parent();
        if(confirm("Удалить "+TR.find("td[Type=GlassName] input").val()))
            gl.Post(
                "Construct_Glases_Remove",
                {
                    idGlass:TR.attr("idGlass")
                },
                function (str) {
                    var o=JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            TR.remove();
                            break;
                        case "Error":
                            alert(o.Note);
                            break;
                    }
                }
            )
    }
}

var Other={
    Select:function () {
        $("#OtherTable tr").remove();
        gl.Post(
            "Construct_Other_Select",
            {

            },
            function (str) {
                var o=JSON.parse(str);
                for(var i in o)
                    Other.RowAdd(
                        o[i].idOther, o[i].OtherName, o[i].Sum, o[i].SumM2
                    );
            }
        )
    },
    Add:function () {
        if($("#OtherAdd_Name").val()!="" & ($("#OtherAdd_Sum").val()!="" || $("#OtherAdd_SumM2").val()!=""))
            gl.Post(
                "Construct_Other_Save",
                {
                    idOther:-1,
                    OtherName:$("#OtherAdd_Name").val(),
                    Sum:$("#OtherAdd_Sum").val(),
                    SumM2:$("#OtherAdd_SumM2").val()
                },
                function (str) {
                    var o=JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            Other.RowAdd(
                                o.Note,
                                $("#OtherAdd_Name").val(),
                                $("#OtherAdd_Sum").val(),
                                $("#OtherAdd_SumM2").val(),
                            );
                            $("#OtherAdd_Name").val("");
                            $("#OtherAdd_Sum").val("");
                            $("#OtherAdd_SumM2").val("");
                            break;
                        case "Error":
                            alert(o.Note);
                            break;
                    }
                }
            )
    },
    RowAdd:function (idOther, Name, Sum, SumM2) {
        $("#OtherTable").prepend(
            "<tr idOther='"+idOther+"'>" +
                "<td Type='OtherName'><input oninput='Other.EditStart(this)' value='"+Name+"' class='form-control input-sm'></td>"+
                "<td Type='Sum'><input oninput='Other.EditStart(this)' value='"+Sum+"' class='form-control input-sm'></td>"+
                "<td Type='SumM2'><input oninput='Other.EditStart(this)' value='"+SumM2+"' class='form-control input-sm'></td>"+
                "<td Type=''>" +
                    "<button onclick='Other.Edit(this)' Type='BtnSave' class='btn btn-primary btn-sm'><span class='fa fa-save'></span></button>" +
                "</td>"+
                "<td Type=''>" +
                    "<button onclick='Other.Remove(this)' Type='BtnRemove' class='btn btn-primary btn-sm'><span class='fa fa-remove'></span></button>" +
                "</td>"+
            "</tr>"
        );
        $("#OtherTable button[Type=BtnSave]").hide();
    },
    EditStart:function (el) {
        $(el).parent().parent().find("button[Type=BtnSave]").show();
        if(el.nodeName=="INPUT")
            el.value=StrReplcae(el.value);
    },
    Edit:function (el) {
        var TR=$(el).parent().parent();
        var idOther=TR.attr("idOther");
        var OtherName=TR.find("td[Type=OtherName] input").val();
        var Sum=TR.find("td[Type=Sum] input").val();
        var SumM2=TR.find("td[Type=SumM2] input").val();
        if(OtherName!="" & (Sum!="" || SumM2!=""))
            gl.Post(
                "Construct_Other_Save",
                {
                    idOther:idOther,
                    OtherName:OtherName,
                    Sum:Sum,
                    SumM2:SumM2
                },
                function (str) {
                    var o= JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            $(el).hide();
                            break;
                        case "Error":
                            alert(o.Note);
                            break;
                    }
                }
            )
    },
    Remove:function (el) {
        var TR=$(el).parent().parent();
        if(confirm("Удалить "+TR.find("td[Type=OtherName] input").val()))
            gl.Post(
                "Construct_Other_Remove",
                {
                    idOther:TR.attr("idOther")
                },
                function (str) {
                    var o=JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            TR.remove();
                            break;
                        case "Error":
                            alert(o.Note);
                            break;
                    }
                }
            )
    }
}

function StrReplcae(str){
    return str.replace(",",".");
}