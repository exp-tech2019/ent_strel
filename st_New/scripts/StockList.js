$(document).ready(function(){
    var s=new SimpleTable(
        $("#StockTable"),
        $("#StockPagination"),
        {
            0:{
                "ColumnName":"GoodName",
                "Caption":"Номенклатура",
                "Width":null
            },
            1:{
                "ColumnName":"GroupName",
                "Caption":"Группа",
                "Width":null
            },
            2:{
                "ColumnName":"Unit",
                "Caption":"Ед имз",
                "Width":null
            },
            3:{
                "ColumnName":"Manufacturer",
                "Caption":"Производитель",
                "Width":null
            },
            4:{
                "ColumnName":"CountStock",
                "Caption":"На складе",
                "Width":null
            },
            5:{
                "ColumnName":"CountEnt",
                "Caption":"На произвостве",
                "Width":null
            }
        },
        "Stock_Select"
    );
    s.Create();
    var StockSelect=function(j){
        let o=j.Goods;
        let tbody=$("#StockTable").find("tbody");
        tbody.find("tr").remove();
        for(var i in o)
            tbody.append(
                "<tr idStock='"+o.idStock+"' idGood='"+o[i].idGood+"' idGroup='"+o[i].idGroup+"'>" +
                    "<td Type='GoodName'>"+o[i].GoodName+"</td>"+
                    "<td Type='GroupName'>"+o[i].GroupName+"</td>"+
                    "<td Type='Unit'>"+gl.ManualUnits[o[i].Unit]+"</td>"+
                    "<td Type='Manufacturer'>"+o[i].Manufacturer+"</td>"+
                    "<td Type='CountStock' onclick='WriteOf.Start(this)'>"+o[i].CountStock+"</td>"+
                    "<td Type='CountEnt' onclick='WriteOf.Start(this)'>"+o[i].CountEnt+"</td>"+
                "</tr>"
            );
    };
    s.Select(
        "Load",
        StockSelect
    );
    $("#StockPagination li[Btn=Next]").click(function(){
        s.NexPage(StockSelect);
    })
    $("#StockPagination li[Btn=Back]").click(function(){
        s.BackPage(StockSelect);
    })
})

var WriteOf={
    TR:null,
    TypeStock:null,
    Start:function (el) {
        var TR=$(el).parent();
        WriteOf.TR=TR;
        $("#WriteOfID").val(TR.attr("idGood"));
        $("#WriteOfGoodName").text(TR.find("td[Type=GoodName]").text());
        switch ($(el).attr("Type")){
            case "CountStock":
                $("#WriteOfTypeStock").val("Stock");
                WriteOf.TypeStock="Stock";
                $("#WriteOfCountStock").text($(el).text());
                break;
            case "CountEnt":
                $("#WriteOfTypeStock").val("Ent");
                WriteOf.TypeStock="Ent";
                $("#WriteOfCountStock").text($(el).text());
                break;
        };
        $("#WriteOfCount").val(0);
        $("#WriteOfDialog").modal("show");
    },
    End:function () {
        switch (parseFloat($("#WriteOfCountStock").text())<parseFloat($("#WriteOfCount").val())){
            case true:
                alert("Превышено допустимое кол-во материала");
                break;
            case false:
                gl.Post(
                    "Stock_WriteOf",
                    {
                        idGood:$("#WriteOfID").val(),
                        TypeStock:WriteOf.TypeStock,
                        Count:$("#WriteOfCount").val(),
                        idLogin:$("#idLogin").val()
                    },
                    function (str) {
                        switch (JSON.parse(str).Status){
                            case "Success":
                                switch (WriteOf.TypeStock){
                                    case "Stock":
                                        WriteOf.TR.find("td[Type=CountStock]").text($("#WriteOfCount").val());
                                        break;
                                    case "Ent":
                                        WriteOf.TR.find("td[Type=CountEnt]").text($("#WriteOfCount").val());
                                        break;
                                };
                                $("#WriteOfDialog").modal("hide");
                                break;
                            case "Error":
                                alert("Ошибка: "+JSON.parse(str).Note);
                                break;
                        }
                    }
                );
                break;
        };
    }
}