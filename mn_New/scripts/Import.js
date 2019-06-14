var TypeDoors=[];
var Dovod=[];
var OpenDoors=[];
var Nalichnik=[];
var ClientDlgTable;

$(document).ready(function(){
    $.post(
        "REST/Select_Manuals.php",
        {},
        function(o){
            TypeDoors=o.TypeDoor;
            Dovod=o.Dovod;
            OpenDoors=o.OpenDoor;
            Nalichnik=o.Nalichnik;
        }
    );

    $("#Upload").change(function(){
        var formData = new FormData();
        jQuery.each($('#Upload')[0].files, function(i, file) {
            formData.append('Upload', file);
        });
        $("#ImportTable tr").remove();
        $.ajax({
            url: "http://localhost:8082/Import/",
            type: "POST",
            dataType : "json",
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            success: function(o){
                //var o=JSON.parse(str);
                var i=-1;
                while(o[++i]!=null)
                    $("#ImportTable").append(
                        "<tr NumPP='"+o[i].NumPP+"'>" +
                            "<td onclick='ImportTable.Remove(this)' style='cursor: pointer'><span class='fa fa-remove'></span></td>"+
                            "<td Type='NumPP'>"+o[i].NumPP+"</td>"+
                            "<td Type='TypeDoor'>"+AddCol_TypeDoor(o[i].Name)+"</td>"+
                            "<td Type='Count'>"+AddCol_Number(o[i].Count)+"</td>"+
                            "<td Type='H'>"+AddCol_Number(o[i].H)+"</td>"+
                            "<td Type='W'>"+AddCol_Number(o[i].W)+"</td>"+
                            "<td Type='Open'>"+AddCol_Open(o[i].Open)+"</td>"+
                            "<td Type='S'>"+AddCol_S(o[i].S)+"</td>"+
                            "<td Type='Ral'><input class='form-control input-sm' value='"+o[i].Ral+"'></td>"+
                            "<td Type='Nalichnik'>"+AddCol_Nalichnik(o[i].Nalichnik)+"</td>"+
                            "<td Type='Dovod'>"+AddCol_Dovod(o[i].Dovod)+"</td>"+
                            "<td Type='WorkPetlya'>"+AddCol_NumberOrNull(o[i].WorkPetlya)+"</td>"+
                            "<td Type='StvorkaPetlya'>"+AddCol_NumberOrNull(o[i].StvorkaPetlya)+"</td>"+
                            "<td Type='WorkGlass'>"+AddCol_NumberOrNull(o[i].WorkGlass)+"</td>"+
                            "<td Type='StvorkaGlass'>"+AddCol_NumberOrNull(o[i].StvorkaGlass)+"</td>"+
                            "<td Type='WorkGrid'>"+AddCol_NumberOrNull(o[i].WorkWindowGrid)+"</td>"+
                            "<td Type='StvorkaGrid'>"+AddCol_NumberOrNull(o[i].StvorkaWindowGrid)+"</td>"+
                            "<td Type='Framug'>"+AddCol_Framug(o[i].FramugaCh)+"</td>"+
                            "<td Type='FramugH'>"+AddCol_NumberOrNull(o[i].FramugaH)+"</td>"+
                            "<td Type='Note' TextNote='"+o[i].Note+"' onclick='ViewNote(this)'>"+(o[i].Note!="" ? "<span class='fa fa-envelope'></span>" : "")+"</td>"+
                            "<td Type='Shtild'><input class='form-control input-sm' value='"+o[i].Shtild+"'></td>"+
                            "<td Type='Markirovka'><input class='form-control input-sm' value='"+o[i].Markirovka+"'></td>"+
                        "</tr>"
                    )
            }
        });
    });

    ClientDlgTable=$("#ClientDlgTable").DataTable({
        "searching": false,
        "ordering": false,
        scrollY:        '49vh',
        scrollCollapse: true,
        paging:         false,
        columns: [
            { title: "" },
            { title: "Организация" },
            { title: "ИНН" }
        ]
    });
    ClientDlg.Select();
})

function AddCol_TypeDoor(str) {
    var s=""; var i=-1;
    var flagFind=false;
    while(TypeDoors[++i]!=null) {
        s = s + "<option " + (TypeDoors[i].Name == str ? "selected" : "") + ">" + TypeDoors[i].Name + "</option>";
        if(TypeDoors[i].Name==str) flagFind=true;
    };
    if(!flagFind)
        s=s+"<option selected>"+str+"</option>";
    return "<div class='"+(!flagFind ? "has-error" : "")+"'><select class='form-control input-sm'>"+s+"</select></div>";
}

function AddCol_Number(str){
    return "<div class='"+(isNaN(str) ? "has-error" : "")+"'><input class='form-control input-sm' value='"+str+"'></div>";
}

function AddCol_NumberOrNull(str){
    return "<div class='"+(isNaN(str) & str!="" ? "has-error" : "")+"'><input class='form-control input-sm' value='"+str+"'></div>";
}

function AddCol_Open(str){
    var s=""; var i=-1;
    var flagFind=false;
    while(OpenDoors[++i]!=null) {
        s = s + "<option " + (OpenDoors[i].Name == str ? "selected" : "") + ">" + OpenDoors[i].Name + "</option>";
        if(OpenDoors[i].Name==str) flagFind=true;
    };
    if(!flagFind)
        s=s+"<option selected>"+str+"</option>";
    return "<div class='"+(!flagFind ? "has-error" : "")+"'><select class='form-control input-sm'>"+s+"</select></div>";
}

function AddCol_S(str) {
    var s=""; var flagError=false;
    if(str!="" & isNaN(str) & str!="Равн." ) flagError=true;
    return "<div class='"+(flagError ? "has-error" : "")+"'><input class='form-control input-sm' value='"+str+"'></div>";
}

function AddCol_Nalichnik(str){
    var s=""; var i=-1;
    var flagFind=false;
    while(Nalichnik[++i]!=null) {
        s = s + "<option " + (Nalichnik[i].Name == str ? "selected" : "") + ">" + Nalichnik[i].Name + "</option>";
        if(Nalichnik[i].Name==str) flagFind=true;
    };
    if(!flagFind)
        s=s+"<option selected>"+str+"</option>";
    return "<div class='"+(!flagFind ? "has-error" : "")+"'><select class='form-control input-sm'>"+s+"</select></div>";
}

function AddCol_Dovod(str){
    var s=""; var i=-1;
    var flagFind=false;
    while(Dovod[++i]!=null) {
        s = s + "<option " + (Dovod[i].Name == str ? "selected" : "") + ">" + Dovod[i].Name + "</option>";
        if(Dovod[i].Name==str) flagFind=true;
    };
    if(!flagFind)
        s=s+"<option selected>"+str+"</option>";
    return "<div class='"+(!flagFind ? "has-error" : "")+"'><select class='form-control input-sm'>"+s+"</select></div>";
}

function AddCol_Framug(str) {
    var Framugs=["да","нет"];
    var s=""; var i=-1;
    var flagFind=false;
    while(Framugs[++i]!=null) {
        s = s + "<option " + (Framugs[i] == str || (Framugs[i]=="нет" & str=="") ? "selected" : "") + ">" + Framugs[i] + "</option>";
        if(Framugs[i]==str || (Framugs[i]=="нет" & str=="")) flagFind=true;
    };
    if(!flagFind)
        s=s+"<option selected>"+str+"</option>";
    return "<div class='"+(!flagFind ? "has-error" : "")+"'><select class='form-control input-sm'>"+s+"</select></div>";
}

function ViewNote(el) {
    $("#DialogNoteView_Text").text($(el).attr("TextNote"));
    $("#DialogNoteView").modal("show");
}

var ClientDlg={
    Select:function () {
        $("#ClientDlgTable tr").remove();
        ClientDlgTable.clear();
        $.get(
            "REST/Client_Select.php",
            {
                Where:$("#ClientDlgFind").val()
            },
            function(o){
                var i=-1;
                while(o[++i]!=null)
                    ClientDlgTable.row.add([
                        o[i].id,
                        o[i].OrgName,
                        o[i].INN
                    ]);
                ClientDlgTable.draw();
                $("#ClientDlgTable tbody tr").click(function(){
                    $("#ClientDlgTable tbody tr").attr("class","");
                    $(this).attr("class","success");
                });
                $("#ClientDlgTable tbody tr").dblclick(function(){
                    $("#ClientDlgTable tbody tr").attr("class","");
                    $(this).attr("class","success");
                    ClientDlg.Close();
                });
            }
        )
    },
    Open:function () {
        $("#ClientDlg").modal("show");
    },
    Close:function () {
        if($("#ClientDlgTable tbody tr[class=success]").length>0){
            var TR=$("#ClientDlgTable tbody tr[class=success]");
            $("#idClient").val(TR.find("td:eq(0)").text());
            $("#OrgName").val(TR.find("td:eq(1)").text());
        };
        $("#ClientDlg").modal("hide");
    }
}

var ImportTable={
    Remove:function (el) {
        var TR=$(el).parent();
        if(confirm("Удалить позицию?"))
            TR.remove();
    },
    Save:function () {
        if($("#idClient").val()==""){
            alert("Не выбран заказчик");
            return false;
        };

        var Doors=[];
        for(var i=0;i<$("#ImportTable tr").length;i++){
            var TR=$("#ImportTable tr:eq("+i+")");
            Doors.push({
                Status:"Add",
                idDoor:-1,
                NumPP:TR.find("td[Type=NumPP]").text(),
                TypeDoor:TR.find("td[Type=TypeDoor] select").val(),
                Count:TR.find("td[Type=Count] input").val(),
                H:TR.find("td[Type=H] input").val(),
                W:TR.find("td[Type=W] input").val(),
                Open:TR.find("td[Type=Open] select").val(),
                S:TR.find("td[Type=S] input").val(),
                RAL:TR.find("td[Type=RAL] input").val(),
                Nalichnik:TR.find("td[Type=Nalichnik] select").val(),
                Dovod:TR.find("td[Type=Dovod] select").val(),
                WorkPetlya:TR.find("td[Type=WorkPetlya] input").val(),
                StvorkaPetlya:TR.find("td[Type=StvorkaPetlya] input").val(),

                WorkGlass:TR.find("td[Type=WorkGlass] input").val(),
                StvorkaGlass:TR.find("td[Type=StvorkaGlass] input").val(),

                WorkGrid:TR.find("td[Type=WorkGrid] input").val(),
                StvorkaGrid:TR.find("td[Type=StvorkaGrid] input").val(),

                Framug:TR.find("td[Type=Framug] select").val(),
                FramugH:TR.find("td[Type=FramugH] input").val(),

                Note:TR.find("td[Type=Note]").attr("TextNote"),
                Shtild:TR.find("td[Type=Shtild] input").val(),
                Markirovka:TR.find("td[Type=Markirovka] input").val()
            });
        };
        gl.Post(
            "Order_Save",
            {
                idOrder:-1,
                Blank:-1,
                Shet:$("#Shet").val(),
                ShetDate:$("#ShetDate").val(),
                idClient:$("#idClient").val(),
                Doors:Doors
            },
            function (str) {
                var o=JSON.parse(str);
                switch (o.Status){
                    case "Success":
                        alert("Succcesss");
                        break;
                    case "Error":
                        alert("Ошибка: "+o.Note);
                        break;
                    case "NoValidation":
                        $("#ImportTable div").attr("class","");
                        var Cols=JSON.parse(o.Note);
                        for(var i in Cols){
                            var TR=$("#ImportTable tr[NumPP="+Cols[i].NumPP+"]");
                            Cols[i].Cols.forEach(function (item) {
                                TR.find("td[Type="+item+"] div").attr("class","has-error");
                            })
                        }
                        break;
                };
            }
        )
    }
}