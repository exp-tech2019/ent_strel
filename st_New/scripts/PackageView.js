function AfterSave(){
    //Сделаем проверку на превышение кол-ва номенклатуры на складе
    //А так же проверим на наличие пустых полей
    var flagMore=false;
    var flagNext=true;
    for(let i=0;i<$("#ShptAfterTable tr").length;i++) {
        var TR = $("#ShptAfterTable tr:eq(" + i + ")");
        if (parseFloat(TR.find("td[CountStock]").text()) < parseFloat(TR.find("td[CountShpt] input").val()))
            flagMore = true;

        switch (TR.find("td[CountShpt] input").val() == "") {
            case true:
                flagNext = false;
                TR.find("td[CountShpt]").attr("class", "has-error");
                break;
            case false:
                TR.find("td[CountShpt]").attr("class", "has-success");
                break;
        };
    };
    if(flagMore & flagNext)
        if(!confirm("Кол-во на складе меньше чем планируется списать. Продолжить?"))
            flagNext=false;
    //Если все успешно передем к сохранению
    if(flagNext){
        var TRList=[];
        for(let i=0;i<$("#ShptAfterTable tr").length;i++) {
            var TR = $("#ShptAfterTable tr:eq(" + i + ")");
            TRList.push({
                idGood: TR.attr("idGood"),
                CountStock: TR.find("td[CountStock]").text(),
                CountShpt: TR.find("td[CountShpt] input").val()
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
                console.log(o);
                switch (o.Status){
                    case "Success":
                        window.location.href="?MVCPage=PackageView&idPackage="+$("#idPackage").val();
                        break;
                    case "Error":
                        alert("Ошибка: "+o.Note);
                        break;
                }
            }
        )
    }
}