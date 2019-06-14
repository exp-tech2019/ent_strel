$(document).ready(function(){
    var idTransfer=$("#idTransfer").val();
    gl.Post(
        "TransferInEnt_EditStart",
        {
            idTransfer:idTransfer
        },
        function(str){
            var o=JSON.parse(str);
            $("#TransferDate").val(o.DateTransfer);
            $("#LoginFIO").val(o.LoginFIO);
            $("#WorkerFIO").val(o.WorkerFIO);
            $("#NaryadInp").val(o.NaryadNum);
            var g=o.Goods;
            for(let i in g)
                $("#GoodsTable").append(
                    "<tr>" +
                        "<td>"+g[i].GoodName+"</td>"+
                        "<td>"+gl.ManualUnits[g[i].Unit]+"</td>"+
                        "<td>"+g[i].CountStock+"</td>"+
                        "<td>"+g[i].CountNaryad+"</td>"+
                        "<td>"+g[i].CountIssueOld+"</td>"+
                        "<td>"+g[i].CountIssue+"</td>"+
                    "</tr>"
                )
        }
    )
})