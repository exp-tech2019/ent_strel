$(document).ready(function(){
    var tdArr=new Array("SumPlus","SumMinus","Difference");
    for(var i=0; i<$("#TableMain tr").length;i++){
        var TR=$("#TableMain tr:eq("+i+")");
        for(var td in tdArr)
            TR.find("td[Type=" + tdArr[td] + "]").text(parseFloat(TR.find("td[Type=" + tdArr[td] + "]").text()).toFixed(0));
    }
})

function AddAct(){
    var flag=true;
    for(var i=0; i<$("#TableMain tr").length; i++)
        if($("#TableMain tr:eq("+i+")").attr("Status")==0)
            flag=false;
    switch (flag){
        case false: alert("Есть акты на стадии формирования"); break;
        case true: window.location.href="index.php?MVCPage=AddAct"; break;
    };
}

function OpenAct(el){
    window.location.href="index.php?MVCPage=PageAct&idAct="+$(el).attr("idAct");
}