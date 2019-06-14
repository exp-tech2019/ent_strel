var Client={
    Save:function(){
        gl.Post(
            "ClientSave",
            {
                id:$("#idClient").val(),
                OrgName:$("#OrgName").val(),
                FullOrgName:$("#FullOrgName").val(),
                INN:$("#INN").val(),
                KPP:$("#KPP").val(),
                OGRN:$("#OGRN").val(),
                AdressLegal:$("#AdressLegal").val(),
                AdressActual:$("#AdressActual").val()
            },
            function(str){
                var o=JSON.parse(str);
                switch(o.Status){
                    case "Success":
                        window.location.href="?MVCPage=ClientList";
                        break;
                    case "Error": alert("Ошибка: "+o.Note);
                        break;
                }
            }
        )
    },
    Remove:function () {
        switch ($("#idClient").val()){
            case -1:
                window.location.href="?MVCPage=ClientList";
                break;
            default:
                if(confirm("Удалить организацию?"))
                gl.Post(
                    "ClientRemove",
                    {
                        idClient:$("#idClient").val()
                    },
                    function (str) {
                        var o=JSON.parse(str);
                        switch(o.Status){
                            case "Success":
                                window.location.href="?MVCPage=ClientList";
                                break;
                            case "Error": alert("Ошибка: "+o.Note);
                                break;
                        }
                    }
                )
                break;
        }
    }
}