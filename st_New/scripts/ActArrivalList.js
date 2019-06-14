$(document).ready(function () {
    ArrivalTable.Select("Load");
    $("#ArrivalFind").keypress(function (e) {
        if (e.which == 13)
            ArrivalTable.Select("Load");
    });
    $("#ArrivalFinBtn").click(function () {
        ArrivalTable.Select("Load");
    })
})

var ArrivalTable={
    PageNum:1,
    PageCount:1,
    FieldOnOnePage:gl.FieldOnTable,
    "Select":function (Type) {
        $("#ArrivalTable tr").remove();
        gl.Post(
            "ActArrival_Select",
            {
                "FindText":$("#ArrivalFind").val(),
                "PageNum":this.PageNum,
                "FieldCount":ArrivalTable.FieldOnOnePage
            },
            function(str){
                var o=JSON.parse(str);
                if(Type=="Load") {
                    ArrivalTable.PageNum=1;
                    ArrivalTable.PageCount = Math.ceil(o.CountList / ArrivalTable.FieldOnOnePage);
                    $("#ArrivalTablePagination li[Btn!=Next][Btn!=Back]").remove();
                    for (let i = 1; i <= ArrivalTable.PageCount; i++)
                        $("#ArrivalTablePagination li[Btn=Next]").before(
                            "<li onclick='ArrivalTable.PageSelect(this)' PageNum='" + i + "' class='" + (i == ArrivalTable.PageNum ? "active" : "") + "'><a href='#'>" + i + "</a></li>"
                        );
                };
                var ArrivalList=o.ArrivalList;
                for(var i in ArrivalList)
                    $("#ArrivalTable").append(
                        "<tr idArrival=" + ArrivalList[i].id + ">" +
                            "<td onclick='ArrivalTable.EditStart($(this).parent())' Type='TTNNum'>" + ArrivalList[i].TTNNum + "</td>" +
                            "<td onclick='ArrivalTable.EditStart($(this).parent())' Type='TTNDate'>" + ArrivalList[i].TTNDate + "</td>" +
                            "<td onclick='ArrivalTable.EditStart($(this).parent())' Type='Customer'>" + ArrivalList[i].Customer + "</td>" +
                            "<td onclick='ArrivalTable.EditStart($(this).parent())' Type='Status'>" + (ArrivalList[i].Accept===0 ? "Не провден" : "Проведен") + "</td>" +
                            "<td>" +
                                "<span onclick='ArrivalTable.EditStart($(this).parent().parent())' style='cursor: pointer;' class='glyphicon glyphicon-cog col-md-5'>"+
                                "<span style='cursor: pointer;' class='glyphicon glyphicon-remove-circle col-md-5'>"+
                            "</td>"+
                        "</tr>"
                    );
            }
        )
    },
    "PageNext":function () {
        if(ArrivalTable.PageNum<ArrivalTable.PageCount){
            ArrivalTable.PageNum++;
            $("#ArrivalTablePagination li").removeClass("active");
            $("#ArrivalTablePagination li[PageNum="+ArrivalTable.PageNum+"]").attr("class","active");
            ArrivalTable.Select("Select");
        }
    },
    "PageSelect":function (el) {
        ArrivalTable.PageNum=$(el).attr("PageNum");
        $("#ArrivalTablePagination li").removeClass("active");
        $("#ArrivalTablePagination li[PageNum="+ArrivalTable.PageNum+"]").attr("class","active");
        ArrivalTable.Select("Select");
    },
    "PageBack":function () {
        if(ArrivalTable.PageNum>1){
            ArrivalTable.PageNum--;
            $("#ArrivalTablePagination li").removeClass("active");
            $("#ArrivalTablePagination li[PageNum="+ArrivalTable.PageNum+"]").attr("class","active");
            ArrivalTable.Select("Select");
        }
    },
    "EditStart":function (el) {
        var TR=$(el);
        window.location.href="?MVCPage=ActArrivalOne&idArrival="+TR.attr("idArrival");
    }
}