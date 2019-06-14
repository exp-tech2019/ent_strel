$(document).ready(function(){
    var idTransfer=$("#idTransfer").val();
    gl.Post(
        "TransferInStock_EditStart",
        {
            idTransfer:idTransfer
        },
        function(str){
            var o=JSON.parse(str);
            $("#TransferDate").val(o.DateTransfer);
            $("#LoginFIO").val(o.LoginFIO);
            $("#WorkerFIO").val(o.WorkerFIO);
            var g=o.Goods;
            for(let i in g)
                $("#GoodsTable").append(
                    "<tr>" +
                        "<td>"+g[i].GoodName+"</td>"+
                        "<td>"+gl.ManualUnits[g[i].Unit]+"</td>"+
                        "<td>"+g[i].CountEnt+"</td>"+
                        "<td>"+g[i].CountIssue+"</td>"+
                    "</tr>"
                )
        }
    )
})