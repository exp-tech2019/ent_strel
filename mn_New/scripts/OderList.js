var t;
$(document).ready(function () {
    t=$("#Table").DataTable({
        //data: dataSet,
        "searching": false,
        "ordering": false,
        scrollY:        '100vh',
        scrollCollapse: true,
        paging:         false,
        columns: [
            { title: "" },
            { title: "Заказ" },
            { title: "Дата" },
            { title: "Счет" },
            { title: "Заказчк" },
            { title: "Кол-во дверей" },
            { title: "Срок до" }
        ]
    });
    //t.column(0).visible(false);
    Table.Select();

    $('#Find').keydown(function (e){
        if(e.keyCode == 13){
            Table.Select();
        }
    })
})

var Table={
    Select:function(){
        t.clear();
        var FindStr=$("#Find").val();
        gl.Post(
            "Order_SelectList",
            {
                FindStr:FindStr
            },
            function (str) {
                var o=JSON.parse(str);
                for(var i in o)
                    t.row.add([
                        o[i].id,
                        o[i].Blank,
                        o[i].BlankDate,
                        o[i].Shet,
                        o[i].Client,
                        o[i].DoorCount,
                        o[i].DateBy
                    ]);
                t.draw();
                $("#Table tbody tr").click(function(){
                    window.location.href="?MVCPage=OrderOne&idOrder="+$(this).find("td:eq(0)").text();
                })
            }
        )
    }
}