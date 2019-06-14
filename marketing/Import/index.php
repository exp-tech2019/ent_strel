<div class="panel panel-default">
    <!-- Default panel contents -->
    <div class="panel-heading">Импорт нового заказа</div>
    <div class="panel-body">
        <div class="form">
            <form class="form-horizontal" role="form" enctype="multipart/form-data" method="POST" action="index.php" style="width: 600px; margin: 0 auto; padding-top: 20px;">
                <div class="form-group">
                    <input type="hidden" name="MVC" value="ImportExcel">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Контрагент</label>
                        <div class="col-sm-10">
                            <input type="hidden" name="idCustomer">
                            <input type="text" class="form-control" placeholder="Контрагент" name="CustomerName" onclick="CustomersLoad()">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">Excel файл</label>
                        <div class="col-sm-10">
                            <input type="file" class="form-control" name="CustomerFile">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-default btn-sm">Далее</button>
                        </div>
                    </div>
            </form>
        </div>
        <p>
            <div class="input-group">
                <span class="input-group-addon">Выберите контрагента</span>
                <input type="text" class="form-control danger" placeholder="Контрагент">
            </div>
            <br>
            <button type="button" class="btn btn-primary">Загрузите файл</button>
        </p>
        <!--
        <table class="table">
            <thead>
            <tr>
                <th>№ п.п.</th>
                <th>Наименование</th>
                <th>Кол-во</th>
                <th>Высота</th>
                <th>Ширина</th>
                <th>Открывание</th>
                <th>Рабочая створка</th>
                <th>RAL окрас</th>
                <th>Наличник</th>
                <th>Доводчик</th>
                <th>Навес раб. ств.</th>
                <th>Навес доп. ств.</th>
                <th>Окно раю. ств.</th>
                <th>Окно доп. ств.</th>
                <th>Фрамуга</th>
                <th>Высота фрамуги</th>
                <th>Маркировка</th>
                <th>Примечание</th>
            </tr>
            </thead>
            <tbody id="ImportTable">
            </tbody>
        </table>
        -->
    </div>


</div>

<!--Справочник контрагентов-->
<div class="modal fade" id="CustomersDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Заказчики</h4>
            </div>
            <div class="modal-body">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <input style="width: 100%; border: none;" placeholder="Поиск" oninput="CustomerFind(this)">
                    </div>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Наименование</th>
                            <th>ИНН</th>
                        </tr>
                    </thead>
                    <tbody id="CustomersTable">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    var CustomersArr=new Array();
    function CustomersLoad(){
        if(CustomersArr.length==0)
            $.post(
                "Import/CustomerListLoad.php",
                function(data){
                    console.log(data);
                    var o=$.parseJSON(data); var i=0;
                    while(o[i]!=null){
                        $("#CustomersTable").append(
                            "<tr idCustomer="+o[i].id+" onclick='CustomerSelect(this)'>"+
                                "<td>"+o[i].Name+"</td>"+
                                "<td>"+o[i].INN+"</td>"+
                            "</tr>"
                        );
                        CustomersArr[i]={"id":o[i].id,"Name":o[i].Name,"INN":o[i].INN};
                        i++;
                    };
                    $("#CustomersDialog").modal("show");
                }
            )
        else
            $("#CustomersDialog").modal("show");
    }
    function CustomerFind(el) {
        var findText=$(el).val().toLowerCase();
        var TRLen=$("#CustomersTable tr").length;
        for(var i=0;i<TRLen;i++){
            var TR=$("#CustomersTable tr:eq("+i+")");
            if(TR.find("td:eq(0)").text().toLowerCase().indexOf(findText)>-1 || TR.find("td:eq(1)").text().toLowerCase().indexOf(findText)>-1){
                TR.show();
            }
            else
                TR.hide();
        };
    }
    function CustomerSelect(el) {
        $("input[name=idCustomer]").val( $(el).attr("idCustomer"));
        $("input[name=CustomerName]").val( $(el).find("td:eq(0)").text());
        $("#CustomersDialog").modal("hide");
    }
</script>