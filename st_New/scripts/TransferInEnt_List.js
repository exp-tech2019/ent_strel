$(document).ready(function(){
    var s=new SimpleTable(
        $("#Table"),
        $("#Pagination"),
        {
            0:{
                "ColumnName":"DateTransfer",
                "Caption":"Дата",
                "Width":null
            },
            1:{
                "ColumnName":"LoginFIO",
                "Caption":"Передал",
                "Width":null
            },
            2:{
                "ColumnName":"WorkerFIO",
                "Caption":"Принял",
                "Width":null
            },
            3:{
                "ColumnName":"Dolgnost",
                "Caption":"Должность",
                "Width":null
            },
            4:{
                "ColumnName":"Naryad",
                "Caption":"Наряд",
                "Width":null
            }
        },
        "TransferInEnt_Select"
    );
    s.Create();
    var Select=function(j){
        let o=j.list;
        let tbody=$("#Table").find("tbody");
        tbody.find("tr").remove();
        for(var i in o)
            tbody.append(
                "<tr idOrder='"+o[i].idTransfer+"' onclick=\"window.location.href='?MVCPage=TransferInEnt_EditStart&idTransfer="+o[i].idTransfer+"'\" onclick=''>" +
                    "<td Type='DateTransfer'>"+o[i].DateTransfer+"</td>"+
                    "<td Type='LoginFIO' idLogin='"+o[i].idLogin+"'>"+o[i].LoginFIO+"</td>"+
                    "<td Type='WorkerFIO' idWorker='"+o[i].idWorker+"'>"+o[i].WorkerFIO+"</td>"+
                    "<td Type='Dolgnost'>"+o[i].Dolgnost+"</td>"+
                    "<td Type='Naryad' idNaryad='"+o[i].idNaryad+"'>"+o[i].NaryadNum+"</td>"+
                "</tr>"
            );
    };
    s.Select(
        "Load",
        Select,
        $("#Find").val()
    );
    $("#Pagination li[Btn=Next]").click(function(){
        s.NexPage(Select);
    })
    $("#Pagination li[Btn=Back]").click(function(){
        s.BackPage(Select);
    })

    $('#Find').keypress(function (e) {
        if (e.which == 13)
            s.Select(
                "Load",
                Select,
                $("#Find").val()
            );
    });
    $("#FindBtn").click(function(){
        s.Select(
            "Load",
            Select,
            $("#Find").val()
        );
    })
})