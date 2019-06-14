/**
 * Выполнение проверки правильности заполнения таблоицы импорта
 */
function CheckTable(ElTable, Manuals){
    ElTable=$(ElTable);
    for(var i=0;i<ElTable.find("tr").length; i++)
    {
        var ElTR=ElTable.find("tr:eq("+i+")");
        var flagErr=false;
        //Кол-во
        if (ElTR.find("td[Type=NumPP] input").val().match(/^[-\+]?\d+/) === null)
        {
            flagErr=true;
            ElTR.find("td[Type=NumPP] input").css("background-color","lightpink");
        };
    }
}