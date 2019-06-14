$(document).ready(function(){
    //Выгрузим список счетов и дверей
    $.post(
        "pageanalyse/selectorders.php",
        {},
        function (o) {
            var i=-1;
            console.log(o);
            while(o[++i]!=null){
                $("#AnalyseTable").append(
                    "<tr style='cursor: pointer' idOrder='"+o[i].idOrder+"'>" +
                        "<td ClickType='UP'><span class='glyphicon glyphicon-chevron-up'></span></td>"+
                        "<td ClickType='UP'>"+o[i].Blank+"</td>"+
                        "<td ClickType='UP'>"+o[i].Shet+"</td>"+
                        "<td ClickType='UP'>"+o[i].Zakaz+"</td>"+
                        "<td ClickType='PRINT'><span class='glyphicon glyphicon-print'></span></td>"+
                    "</tr>"+
                    "<tr OrderID='"+o[i].idOrder+"'>" +
                        "<td></td>"+
                        "<td Type='Doors' colspan='4'>" +
                            "<table class='table table-responsive' Type='DoorTable'>" +
                                "<thead>"+
                                    "<tr>" +
                                        "<th style='width: 50px'></th>"+
                                        "<th>Тип</th>"+
                                        "<th>Размеры</th>"+
                                        "<th>Кол-во</th>"+
                                    "</tr>"+
                                "</thead>"+
                                "<tbody></tbody>"+
                            "</table>"+
                        "</td>"+
                    "</tr>"
                );
                var j=-1;
                var d=o[i].Doors;
                while(d[++j]!=null)
                    $("#AnalyseTable tr[OrderID="+o[i].idOrder+"] td[Type=Doors] table[Type=DoorTable]>tbody").append(
                        "<tr style='cursor: pointer' idDoor='"+d[j].idDoor+"'>" +
                            "<td><span class='glyphicon glyphicon-chevron-up'></span></td>"+
                            "<td>"+d[j].Name+"</td>"+
                            "<td>"+d[j].Size+"</td>"+
                            "<td>"+d[j].DoorCount+"</td>"+
                        "</tr>"+
                        "<tr DoorID='"+d[j].idDoor+"' DoorCount='"+d[j].DoorCount+"'>" +
                            "<td></td>"+
                            "<td colspan='3'>" +
                                "<table class='table table-responsive' Type='SpeTable'>" +
                                    "<thead>"+
                                        "<tr>" +
                                            "<th>Материал</th>"+
                                            "<th>Требуется</th>"+
                                            "<th>Склад</th>"+
                                            "<th>Списано</th>"+
                                        "</tr>"+
                                    "</thead>"+
                                    "<tbody></tbody>"+
                                "</table>"+
                            "</td>"+
                        "</tr>"
                    );
            };
            $("#AnalyseTable tr[OrderID]").hide();
            $("#AnalyseTable tr[DoorID]").hide();
            $("#AnalyseTable tr[idOrder] td[ClickType=UP]").click(function(){
                var TR=$("#AnalyseTable tr[OrderID="+$(this).parent().attr("idOrder")+"]");
                if(TR.is(":visible")){
                    $(this).find("td:eq(0) span").attr("class","glyphicon glyphicon-chevron-up");
                    TR.hide();
                }else {
                    $(this).find("td:eq(0) span").attr("class","glyphicon glyphicon-chevron-down");
                    TR.show();
                };
            });

            $("#AnalyseTable tr[idOrder] td[ClickType=PRINT]").click(function(){
                var idOrder=$(this).parent().attr("idOrder");
                window.open("PageAnalyse/PrintOrderSpe.php?idOrder="+idOrder,"_blank");
                /*
                $.post(
                    "PageAnalyse/PrintOrderSpe.php",
                    {
                        "idOrder":idOrder
                    },
                    function (o) {
                        console.log(o);
                    }
                )*/
            });

            $("#AnalyseTable tr[idDoor]").click(function () {
                LoadSpe($(this).next());
            })
        }
    )
});

function LoadSpe(elDoorIN){
    var elDoor=$(elDoorIN);
    if(elDoor.is(":visible")){
        elDoor.find("td:eq(0) span").attr("class","glyphicon glyphicon-chevron-up");
        elDoor.hide();
    }else{
        if(elDoor.find("tbody tr").length==0)
        $.post(
            "PageAnalyse/SelectSpe.php",
            {
                "idDoor":elDoor.attr("DoorID")
            },
            function (o) {
                var tBody=elDoor.find("tbody");
                var i=-1;
                while(o[++i]!=null)
                    tBody.append(
                        "<tr class='"+(parseFloat(o[i].SpeCount)*parseFloat(elDoor.attr("DoorCount"))-parseFloat(o[i].CountNC)>parseFloat(o[i].StockEntCount)+parseFloat(o[i].StockMainCount) ? "danger " : "")+"'>" +
                            "<td>"+o[i].GoodName+"</td>"+
                            "<td>"+(parseFloat(o[i].SpeCount)*parseFloat(elDoor.attr("DoorCount")))+"</td>"+
                            "<td>"+(parseFloat(o[i].StockMainCount)+parseFloat(o[i].StockEntCount))+"</td>"+
                            "<td>"+o[i].CountNC+"</td>"+
                        "</tr>"
                    )
            }
        );
        $(this).find("td:eq(0) span").attr("class","glyphicon glyphicon-chevron-down");
        elDoor.show();
    };
}
