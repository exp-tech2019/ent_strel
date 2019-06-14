$(document).ready(function(){
    //$('.tree').treegrid();
    SelectAll();
});

function SelectAll() {
    $("#GoodList tr[Type!='Header']").remove();
    $.post(
        "PageStockMain/SelectAll.php",
        function (o) {
            var i = 0;
            while (o[i] != null) {
                var c = 0;
                var goods = "";
                while (o[i].Goods[c] != null) {
                    goods = goods +
                        "<tr Type='Good' idGood='" + o[i].Goods[c].idGood + "' idGroup='" + o[i].idGroup + "' class='treegrid-500 treegrid-parent-" + o[i].idGroup + "'>" +
                            "<td Type='Article'>" + o[i].Goods[c].Article + "</td>" +
                            "<td Type='GoodName'>" +o[i].Goods[c].GoodName + "</td>" +
                            "<td Type='BarCode'>" + o[i].Goods[c].BarCode + "</td>" +
                            "<td Type='Unit' Unit='" + o[i].Goods[c].Unit + "'>" + UnitToString(o[i].Goods[c].Unit) + "</td>" +
                            "<td Type='StockMain'><button onclick='Invertory.Load(this)' class='btn btn-default'>" + o[i].Goods[c].CountMain + "</button></td>" +
                            "<td Type='StockEnt'><button onclick='Invertory.Load(this)' class='btn btn-default'>" + o[i].Goods[c].CountEnt + "</button></td>" +
                        "</tr>";
                    c++;
                }
                ;
                $("#GoodList").append(
                    "<tr Type='Group' idGroup='" + o[i].idGroup + "' Step='" + o[i].Step + "' class='treegrid-" + o[i].idGroup + " treegrid-parent-100'>" +
                        "<td colspan='6'>" +o[i].GroupName + "</td>" +
                    "</tr>" +
                    goods
                );
                i++;
            }
            ;
            //Инициализируем дерево
            $('#GoodList').treegrid({
                initialState: 'collapsed',
                onExpand: function () {
                },
                onCollapse: function () {
                    if ($(this).attr("Type") != "Header") {
                        var idGroup = $(this).attr("idGroup");
                        //$("#Table tr[Type=Good&idGroup="+idGroup+"]").remove();
                    }
                }
            });
            $("#GoodList tr[Type=Header]").treegrid("expand");
        }
    );
}

//----- Инверторизация --------------
var Invertory={
    TR:null,
    idGood:null,
    CountOld:null,
    TypeStock:null,
    Load:function (el) {
        this.idGood=$(el).parent().parent().attr("idGood");
        this.CountOld=$(el).text();
        this.TypeStock=$(el).parent().attr("Type");
        this.TR=$(el).parent().parent();

        $("#InvertoryInp").val(this.CountOld);
        $("#InvertoryDialog").modal("show");
    },
    Save:function () {
        if(parseFloat(this.CountOld)<parseFloat($("#InvertoryInp").val())){
            alert("Введено кол-во больше допустимого");
            return false;
        };
        var idGood=this.idGood;
        var TypeStock=this.TypeStock;
        $.post(
            "PageStockMain/InvertorySave.php",
            {
                "idGood":this.idGood,
                "TypeStock":this.TypeStock,
                "Count":parseFloat(this.CountOld)-parseFloat($("#InvertoryInp").val())
            },
            function (o) {
                if(o.Result=="ok"){
                    $("#GoodList tr[idGood="+idGood+"] td[Type="+TypeStock+"] button").text($("#InvertoryInp").val());
                    $("#InvertoryDialog").modal("hide");
                }
            }
        )

    }
};