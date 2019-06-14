$(document).ready(function(){
    var s=new SimpleTable(
        $("#OrderTable"),
        $("#OrderPagination"),
        {
            0:{
                "ColumnName":"Blank",
                "Caption":"Заказ",
                "Width":null
            },
            1:{
                "ColumnName":"BlankDate",
                "Caption":"Дата",
                "Width":null
            },
            2:{
                "ColumnName":"Shet",
                "Caption":"Счет",
                "Width":null
            },
            3:{
                "ColumnName":"ShetDate",
                "Caption":"Счет дата",
                "Width":null
            },
            4:{
                "ColumnName":"Zakaz",
                "Caption":"Заказчик",
                "Width":null
            },
            5:{
                "ColumnName":"Status",
                "Caption":"Статус",
                "Width":null
            }
        },
        "Spe_OrderList"
    );
    s.Create();
    var OrderSelect=function(j){
        let o=j.Orders;
        let tbody=$("#OrderTable").find("tbody");
        tbody.find("tr").remove();
        for(var i in o)
            tbody.append(
                "<tr idOrder='"+o[i].idOrder+"' onclick='OrderTable.EditStart($(this))'>" +
                    "<td Type='Blank'>"+o[i].Blank+"</td>"+
                    "<td Type='BlankDate'>"+o[i].BlankDate+"</td>"+
                    "<td Type='Shet'>"+(gl.Shet!==undefined ? gl.Shet : "")+"</td>"+
                    "<td Type='ShetDate'>"+o[i].ShetDate+"</td>"+
                    "<td Type='Zakaz'>"+o[i].Zakaz+"</td>"+
                    "<td Type='Status'>"+o[i].Status+"</td>"+
                "</tr>"
            );
    };
    s.Select(
        "Load",
        OrderSelect,
        $("#OrderFind").val()
    );
    $("#OrderPagination li[Btn=Next]").click(function(){
        s.NexPage(OrderSelect);
    })
    $("#OrderPagination li[Btn=Back]").click(function(){
        s.BackPage(OrderSelect);
    })

    $('#OrderFind').keypress(function (e) {
        if (e.which == 13)
            s.Select(
                "Load",
                OrderSelect,
                $("#OrderFind").val()
            );
    });
    $("#OrderFindBtn").click(function(){
        s.Select(
            "Load",
            OrderSelect,
            $("#OrderFind").val()
        );
    })
})

var OrderTable={
    EditStart:function (el) {
        window.location.href="?MVCPage=Spe_OrderOne&idOrder="+el.attr("idOrder");
    }
}
