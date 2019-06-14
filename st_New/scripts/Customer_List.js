$(document).ready(function(){
    CustomerTable.Select("Load");
    $('#CustomerFind').keypress(function (e) {
        if (e.which == 13)
            CustomerTable.Select("Load");
    });
    $("#CustomerFindBtn").click(function(){
        CustomerTable.Select("Load");
    });
})
var CustomerTable={
    PageNum:1,
    PageCount:1,
    FieldOnOnePage:gl.FieldOnTable,
    "Select":function (Type) {
        $("#CustomerTable tr").remove();
        gl.Post(
            "Customer_Select",
            {
                "FindText":$("#CustomerFind").val(),
                "PageNum":this.PageNum,
                "FieldCount":CustomerTable.FieldOnOnePage
            },
            function(str){
                var o=JSON.parse(str);
                if(Type=="Load") {
                    CustomerTable.PageNum=1;
                    CustomerTable.PageCount = Math.ceil(o.CountList / CustomerTable.FieldOnOnePage);
                    $("#CustomerTablePagination li[Btn!=Next][Btn!=Back]").remove();
                    for (let i = 1; i <= CustomerTable.PageCount; i++)
                        $("#CustomerTablePagination li[Btn=Next]").before(
                            "<li onclick='CustomerTable.PageSelect(this)' PageNum='" + i + "' class='" + (i == CustomerTable.PageNum ? "active" : "") + "'><a href='#'>" + i + "</a></li>"
                        );
                };
                var CustomerList=o.CustomerList;
                for(var i in CustomerList)
                    $("#CustomerTable").append(
                        "<tr idCustomer=" + CustomerList[i].id + ">" +
                            "<td Type='Name'>" + CustomerList[i].CustomerName + "</td>" +
                            "<td Type='INN'>" + CustomerList[i].INN + "</td>" +
                            "<td Type='Phone'>" + CustomerList[i].Phone + "</td>" +
                            "<td Type='Mail'>" + CustomerList[i].eMail + "</td>" +
                            "<td Type='Adress'>" + CustomerList[i].Adress + "</td>" +
                            "<td style='width: 100px'>" +
                                "<span onclick='Customers.EditStart($(this).parent().parent())' style='cursor: pointer;' class='glyphicon glyphicon-cog col-md-5'></span>"+
                                "<span onclick='Customers.Remove(this)' style='cursor: pointer;' class='glyphicon glyphicon-remove-circle col-md-5'></span>"+
                            "</td>"+
                        "</tr>"
                    );
            }
        )
    },
    "PageNext":function () {
        if(CustomerTable.PageNum<CustomerTable.PageCount){
            CustomerTable.PageNum++;
            $("#CustomerTablePagination li").removeClass("active");
            $("#CustomerTablePagination li[PageNum="+CustomerTable.PageNum+"]").attr("class","active");
            CustomerTable.Select("Select");
        }
    },
    "PageSelect":function (el) {
        CustomerTable.PageNum=$(el).attr("PageNum");
        $("#CustomerTablePagination li").removeClass("active");
        $("#CustomerTablePagination li[PageNum="+CustomerTable.PageNum+"]").attr("class","active");
        CustomerTable.Select("Select");
    },
    "PageBack":function () {
        if(CustomerTable.PageNum>1){
            CustomerTable.PageNum--;
            $("#CustomerTablePagination li").removeClass("active");
            $("#CustomerTablePagination li[PageNum="+CustomerTable.PageNum+"]").attr("class","active");
            CustomerTable.Select("Select");
        }
    }
}

var Customers={
    "idCustomer":-1,
    "Add":function () {
        Customers.idCustomer=-1;
        $("#CustomerDialog_Name").val("");
        $("#CustomerDialog_INN").val("");
        $("#CustomerDialog_Phone").val("");
        $("#CustomerDialog_Mail").val("");
        $("#CustomerDialog_Adress").val("");
        $("#CustomerDialog").modal("show");
    },
    "EditStart":function (el) {
        Customers.idCustomer=$(el).attr("idCustomer");
        gl.Post(
            "Customer_EditStart",
            {
                "idCustomer":Customers.idCustomer
            },
            function(str){
                var o=JSON.parse(str);
                $("#CustomerDialog_Name").val(o.Name);
                $("#CustomerDialog_INN").val(o.INN);
                $("#CustomerDialog_Phone").val(o.Phone);
                $("#CustomerDialog_Mail").val(o.Mail);
                $("#CustomerDialog_Adress").val(o.Adress);
                $("#CustomerDialog").modal("show");
            }
        );
    },
    "Save":function () {
        var flagErr=false;
        flagErr=$("#CustomerDialog_Name").val()=="" ? true : false;
        $("#CustomerDialog_Name").parent().parent().attr("class", $("#CustomerDialog_Name").val()=="" ? "form-group has-error" : "form-group");
        flagErr=$("#CustomerDialog_INN").val()=="" ? true : false;
        $("#CustomerDialog_INN").parent().parent().attr("class", $("#CustomerDialog_INN").val()=="" ? "form-group has-error" : "form-group");
        if(!flagErr)
            gl.Post(
                "Customer_Save",
                {
                    "idCustomer":Customers.idCustomer,
                    "Name":$("#CustomerDialog_Name").val(),
                    "INN":$("#CustomerDialog_INN").val(),
                    "Phone":$("#CustomerDialog_Phone").val(),
                    "Mail":$("#CustomerDialog_Mail").val(),
                    "Adress":$("#CustomerDialog_Adress").val()
                },
                function(str){
                    var o=JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            $("#CustomerDialog").modal("hide");
                            switch (Customers.idCustomer){
                                case -1:
                                    CustomerTable.Select("Load");
                                    break;
                                default:
                                    var TR=$("#CustomerTable tr[idCustomer="+Customers.idCustomer+"]");
                                    TR.find("td[Type=Name]").text($("#CustomerDialog_Name").val());
                                    TR.find("td[Type=INN]").text($("#CustomerDialog_INN").val());
                                    TR.find("td[Type=Phone]").text($("#CustomerDialog_Phone").val());
                                    TR.find("td[Type=Mail]").text($("#CustomerDialog_Mail").val());
                                    TR.find("td[Type=Adress]").text($("#CustomerDialog_Adress").val());
                                    break;
                            }
                            break;
                        case "Error":
                            alert("Произошла ошибка сохранения: "+o.Note);
                            break;
                    }
                }
            );
    },
    Remove:function (el) {
        var TR=$(el).parent().parent();
        if(confirm("Удалить поставщика?"))
            gl.Post(
                "Customer_Remove",
                {
                    idCustomer:TR.attr("idCustomer")
                },
                function (str) {
                    var o=JSON.parse(str);
                    switch (o.Status){
                        case "Success":
                            TR.remove();
                            break;
                        case "Error":
                            alert(o.Note);
                            break;
                    }
                }
            )
    }
}
