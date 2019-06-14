<div id="Sp18Dialog" title="Спецификация">
    <p>
        <select id="Sp18DialogGroupList">
            <option  value="-1"></option>
        </select>
        <button id="Sp18DialogGroupAdd" onclick="Sp18.GroupAdd()">Добавить группу</button>
        <table class="Sp18Table">
            <thead>
            <tr>
                <th>Группа</th>
                <th>Материал</th>
                <th>Расход на дверь</th>
            </tr>
            </thead>
        <tbody id="Sp18Table">
        
        </tbody>
        </table>
    </p>
</div>
<!--Диалог выбора материала-->
<div id="Sp18MaterialDiialog" title="Выберите материал">
    <p>
        <input id="Sp18MaterialFind" oninput="Sp18.Materials.Find()" style="width: 100%" placeholder="Поиск">
        <table>
            <thead>
            <tr>
                <th>Материал</th>
                <th>Ед. изм.</th>
            </tr>
            </thead>
            <tbody id="Sp18MaterialTable" class="Sp18MaterialTable"></tbody>
        </table>
    </p>
</div>
<script>
    $(document).ready(function(){
        $("#Sp18DialogGroupAdd").button();
        $.post(
            "Orders/Sp18/SelectGroups.php",
            {},
            function (o) {
                o.forEach(function(group){
                    var s="-1";
                    group.Groups1c.forEach(function(item){
                        s+=","+item;
                    });
                    $("#Sp18DialogGroupList").append(
                        "<option value='"+group.id+"' GroupName='"+group.GroupName+"' Group1c='"+s+"'>"+group.GroupName+"</option>"
                    );
                })
            }
        )
        $("#Sp18Dialog").dialog({
            autoOpen: false,
            height: 500,
            width: 400,
            modal:true,
            buttons: [
                {
                    text: "Сохранить",
                    click: function() {
                        Sp18.Save();
                    }
                },
                {
                    text: "Отмена",
                    click: function() {
                        $( this ).dialog( "close" );
                    }
                }
            ]
        });
        $("#Sp18MaterialDiialog").dialog({
            autoOpen: false,
            height: 500,
            width: 400,
            modal:true,
            buttons: [
                {
                    text: "Отмена",
                    click: function() {
                        $( this ).dialog( "close" );
                    }
                }
            ]
        })
    })

    var Sp18={
        DoorTR:undefined,
        GroupAdd:function(){
            if($("#Sp18DialogGroupList").val()!=-1) {
                var idGroup=$("#Sp18DialogGroupList").val();
                var GroupName=$("#Sp18DialogGroupList option:selected").attr("GroupName");
                var Group1c=$("#Sp18DialogGroupList option:selected").attr("Group1c");
                $("#Sp18Table").append(
                    "<tr Status=Add>" +
                        "<td Type=Group idGroup=" + idGroup + ">" + GroupName + "</td>" +
                        "<td Type=Material idMaterial=-1 idGroups1c='"+Group1c+"' onclick='Sp18.Materials.OpenDialog(this)'></td>" +
                        "<td Type=Count><input/></td>" +
                    "</tr>"
                );
            };
        },
        OpenDialog:function(el){
            $("#Sp18Table tr").remove();
            var tr=$(el).parent().parent();
            this.DoorTR=tr;
            var TypeDoor=tr.find("td[type=Name]").text();
            var H=tr.find("td[type=H]").text();
            var W=tr.find("td[type=W]").text();
            var Petlya=0;
            Petlya+=parseInt( tr.find("td[Type=PetlyaWrk]").text()!="" ? tr.find("td[Type=PetlyaWrk]").text() : 0);
            Petlya+=parseInt( tr.find("td[Type=PetlyaStv]").text()!="" ? tr.find("td[Type=PetlyaStv]").text() : 0);
            var Window=0;
            $.post(
                "Orders/Sp18/CalcDoor.php",
                {
                    idDoor:tr.attr("idDoor"),
                    TypeDoor:TypeDoor,
                    H:H,
                    W:W,
                    Petlya:Petlya,
                    Window:Window
                },
                function(o){
                    o.forEach(function(pos){
                        var Groups1c="-1";
                        pos.Groups1c.forEach(function(gr){
                            Groups1c+=","+gr;
                        });
                        $("#Sp18Table").append(
                            "<tr Status=Load>"+
                                "<td Type=Group idGroup="+pos.idGroup+">"+pos.GroupName+"</td>"+
                                "<td Type=Material idMaterial="+pos.idMaterial1c+" idGroups1c='"+Groups1c+"' onclick='Sp18.Materials.OpenDialog(this)'>"+pos.MaterialName+"</td>"+
                                "<td Type=Count><input value='"+pos.Count+"'></td>"+
                            "</tr>"
                        );
                    });
                    $("#Sp18Dialog").dialog("open");
                }
            )

        },
        Save:function(){
            var idDoor=$(this.DoorTR).attr("idDoor");
            var TRDoor=this.DoorTR;
            var Rows=new Array();
            $.each($("#Sp18Table tr"), function(){
                Rows.push({
                    idGroup:$(this).find("td[Type=Group]").attr("idGroup"),
                    idMaterial:$(this).find("td[Type=Material]").attr("idMaterial"),
                    Count:$(this).find("td[Type=Count] input").val()
                });
            });
            $.post(
                "Orders/Sp18/Save.php",
                {
                    idDoor:idDoor,
                    Rows:Rows
                },
                function(o){
                    switch (o.DataSuccess){
                        case "Yes":
                            $(TRDoor).find("td[Type=Spe] button").css("border","2px solid green");
                            break;
                        case "No":
                            $(TRDoor).find("td[Type=Spe] button").css("border","2px solid red");
                            break;
                    };
                    if(o.Status=="Success")
                        $("#Sp18Dialog").dialog("close");
                }
            )
        },
        Materials:{
            RowParent:undefined,
            OpenDialog:function(el){
                $("#Sp18MaterialFind").val("");
                $("#Sp18MaterialTable tr").remove();
                this.RowParent=$(el).parent();
                var idGroups1c=$(el).attr("idGroups1c");
                $.post(
                    "Orders/Sp18/Materials1cSelect.php",
                    {
                        idGroups1c:idGroups1c
                    },
                    function (o) {
                        o.forEach(function(m){
                            $("#Sp18MaterialTable").append(
                                "<tr idMaterial="+m.idMaterial+" ondblclick='Sp18.Materials.DBClick(this)' >"+
                                    "<td Type='Name'>"+m.MaterialName+"</td>"+
                                    "<td Type='Unit'>"+m.Unit+"</td>"+
                                "</tr>"
                            );
                        });
                        $("#Sp18MaterialDiialog").dialog("open");
                    }
                )
            },
            Find:function(){
                var s=$("#Sp18MaterialFind").val().toLowerCase();
                $.each($("#Sp18MaterialTable tr"),function(i, tr){
                    switch ($(tr).find("td[Type=Name]").text().toLowerCase().indexOf(s)>-1){
                        case true:
                            $(tr).show();
                            break;
                        case false:
                            $(tr).hide();
                            break;
                    }
                });
            },
            DBClick:function(el){
                var idMaterial=$(el).attr("idMaterial");
                var MaterialName=$(el).find("td[Type=Name]").text();
                var MaterialRow=$(this.RowParent).find("td[Type=Material]");
                MaterialRow.attr("idMaterial",idMaterial);
                if(MaterialRow.parent().attr("Status")=="Load")
                    MaterialRow.parent().attr("Status","Edit");
                MaterialRow.text(MaterialName);
                $("#Sp18MaterialDiialog").dialog("close");
            }
        }
    }
</script>