$(document).ready(function(){
    $("#NalogTable tr td[Type=Save]").hide();
});
function EditTR(el){
    $(el).parent().parent().find("td[Type=Save]").show();
}
function EditSave(el) {
    var idDolgnost=$(el).parent().parent().attr("idDolgnost");
    var NalogPercent=$(el).parent().parent().find("td[Type=NalogPercent] input").val();
    if(NalogPercent!="")
        $.post(
            "PropertesNalog/EditNalog.php",
            {
                "idDolgnost":idDolgnost,
                "NalogPercent":NalogPercent
            },
            function (data) {
                if(data=="") $(el).hide();
            }
        );
}