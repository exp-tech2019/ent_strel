var gl={
    "Post":function(Action, Values,FunctionReturn){
        var Result="";
        var http = new XMLHttpRequest();
        var url = "http://localhost:8082/Marketing/";
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function() {
            if(http.readyState == 4 && http.status == 200) {
                FunctionReturn(http.responseText);
            }

        }
        http.send("Action="+Action+"&Value="+JSON.stringify(Values));
    },
    "ManualUnits":{
        1:"кг",
        2:"шт",
        3:"л",
        4:"м. погон.",
        5:"м. кв."
    },
    "Guid":function () {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000)
                .toString(16)
                .substring(1);
            }
        return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
                s4() + '-' + s4() + s4() + s4();
    },
    //Кол-во строк отображаемых на странице
    "FieldOnTable":10,
    DateFormat:function (date) {
        return (date.getDate()<10 ? "0"+date.getDate() : date.getDate())+".0"+(date.getMonth()+1).toString()+"."+date.getFullYear();
    },
    //Замена запятой н точку у inp
    ReplaceDot:function (inp) {
        $(inp).val($(inp).val().replace(/,/, '.'));
    }
}