$(document).ready(function () {
    $("#GoodsTable td[Type=NoShpt] input").click(function () {
        
        switch ($(this).prop("checked")){
            case true:
                $(this).parent().parent().find("td[Type=CountShpt] input").val(0);
                $(this).parent().parent().find("td[Type=CountShpt] input").prop("disabled",true);
                break;
            case false:
                $(this).parent().parent().find("td[Type=CountShpt] input").prop("disabled",false);
                break;
        }
    })
})

function Save(){
    //Сделаем проверку на превышение кол-ва номенклатуры на складе
    //А так же проверим на наличие пустых полей
    var flagMore=false;
    var flagNext=true;
    for(let i=0;i<$("#GoodsTable tr").length;i++){
        var TR=$("#GoodsTable tr:eq("+i+")");
        if(!TR.find("td[Type=NoShpt] input").prop("checked")) {
            if (parseFloat(TR.find("td[Type=CountStock]").text()) < parseFloat(TR.find("td[Type=CountShpt] input").val()))
                flagMore = true;

            switch (TR.find("td[Type=CountShpt] input").val()==""){
                case true:
                    flagNext = false;
                    TR.find("td[Type=CountShpt]").attr("class","has-error");
                    break;
                case false:
                    TR.find("td[Type=CountShpt]").attr("class","has-success");
                    break;
            };
        };
    };
    if(flagMore & flagNext)
        if(!confirm("Кол-во на складе меньше чем планируется списать. Продолжить?"))
            flagNext=false;
    //Если все успешно передем к сохранению
    if(flagNext){
        var TRList=[];
        for(let i=0;i<$("#GoodsTable tr").length;i++) {
            var TR = $("#GoodsTable tr:eq(" + i + ")");
            if (!TR.find("td[Type=NoShpt] input").prop("checked"))
                TRList.push({
                    idGood:TR.attr("idGood"),
                    CountStock:TR.find("td[Type=CountStock]").text(),
                    CountShpt:TR.find("td[Type=CountShpt] input").val()
                });
        };
        gl.Post(
            "Package_GoodSave",
            {
                idPackage:$("#idPackage").val(),
                idLogin:$("#idLogin").val(),
                Goods:TRList
            },
            function(str){
                var o = JSON.parse(str);
                switch (o.Status){
                    case "Success":
                        window.location.href="?MVCPage=PackageList";
                        break;
                    case "Error":
                        alert("Ошибка: "+o.Note);
                        break;
                }
            }
        )
    }
}