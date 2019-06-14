<div id="PrintRitmDialog" title="Печать нарядов для Ритма">
    <p>
        <table class="Tables">
            <thead class="BorderTablesTbody">
                <tr>
                    <th></th>
                    <th></th>
                    <th>Заказ</th>
                    <th>Дата</th>
                    <th>Счет</th>
                    <th>Заказчик</th>
                    <th>Всего дверей</th>
                </tr>
            </thead>
        <tbody id="PrintRitmTable"></tbody>
        </table>
    </p>
</div>
<script>
    $(document).ready(function(){
        $("#OrderAccordion div").append("<p class='LeftMenu' onclick='PrintRitm.Load()'>Печать для Ритма</p>");
        $("#PrintRitmDialog").dialog({
            autoOpen: false,
            modal:true,
            width: 500,
            buttons: [
                {
                    text: "Печать",
                    click: function() {
                        PrintRitm.OutExcel();
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
    });
    var PrintRitm={
        Load:function () {
            $("#PrintRitmTable tr").remove();
            $.post(
                "Orders/PrintRitm/Select.php",
                {},
                function(o){
                    var i=-1;
                    while(o[++i]!=null){
                        $("#PrintRitmTable").append(
                            "<tr idOrder='"+o[i].idOrder+"' class='Start'>" +
                                "<td onclick='PrintRitm.OrderTR_Turn($(this))'><img src='images/arrow-turn-left.png'></td>"+
                                "<td><input type='checkbox'></td>"+
                                "<td>"+o[i].Blank+"</td>"+
                                "<td>"+o[i].BlankDate+"</td>"+
                                "<td Type=Shet>"+o[i].Shet+"</td>"+
                                "<td>"+o[i].Zakaz+"</td>"+
                                "<td>"+o[i].DoorCount+"</td>"+
                            "</tr>"
                        );
                        var Doors=o[i].OrderDoors; j=-1;
                        while(Doors[++j]!=null)
                            $("#PrintRitmTable").append(
                                "<tr idOrder='"+o[i].idOrder+"' idDoor='"+Doors[j].idDoor+"'>" +
                                    "<td></td>"+
                                    "<td><input type='checkbox'></td>"+
                                     "<td>"+Doors[j].NumPP+"</td>"+
                                    "<td Type=Name>"+Doors[j].Name+"</td>"+
                                    "<td Type=Count>"+Doors[j].Count+"</td>"+
                                    "<td Type=Size H="+Doors[j].H+" W="+Doors[j].W+">"+Doors[j].Size+"</td>"+
                                    "<td Type=Open>"+Doors[j].Open+"</td>"+
                                "</tr>"
                            );
                    };
                    $("#PrintRitmTable tr[idDoor]").hide();
                    $("#PrintRitmTable tr:not([idDoor]) input").click(function(){
                        PrintRitm.InputChecked(this);
                    });
                    $("#PrintRitmDialog").dialog("open");
                }
            )
        },
        OrderTR_Turn:function (el) {
            var idOrder=el.parent().attr("idOrder");
            switch (el.find("img").attr("src")){
                case "images/arrow-turn-left.png":
                    $("#PrintRitmTable tr[idOrder="+idOrder+"][idDoor]").show();
                    el.find("img").attr("src","images/arrow_skip.png");
                    break;
                default:
                    $("#PrintRitmTable tr[idOrder="+idOrder+"][idDoor]").hide();
                    el.find("img").attr("src","images/arrow-turn-left.png");
                    break;
            }
        },
        InputChecked:function (el) {
            $("#PrintRitmTable tr[idOrder="+$(el).parent().parent().attr("idOrder")+"] input").prop("checked",$(el).prop("checked"));
        },
        OutExcel:function () {
            var body={};
            var Table=$("#PrintRitmTable");
            for(var i=0;i<Table.find("tr[idDoor] input:checked").length;i++){
                var TR=Table.find("tr[idDoor] input:checked:eq("+i+")").parent().parent();
                var TROrder=TR.parent().find("tr[idOrder="+TR.attr("idOrder")+"]:not([idDoor])");
                body[i]={
                    "Shet":TROrder.find("td[Type=Shet]").text(),
                    "Name":TR.find("td[Type=Name]").text(),
                    "H":TR.find("td[Type=Size]").attr("H"),
                    "W":TR.find("td[Type=Size]").attr("W"),
                    "Count":TR.find("td[Type=Count]").text(),
                    "Open":TR.find("td[Type=Open]").text()
                }
            };
            console.log(body);
            $.post(
                "Orders/PrintRitm/OutExcel.php",
                {
                    "OutExcel":body
                },
                function (o) {
                    console.log(o);
                    window.open("Orders/PrintRitm/Report.xls");
                }
            );
        }
    }
</script>