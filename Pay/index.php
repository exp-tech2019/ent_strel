<?php
$XMLParams=simplexml_load_file("../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
?>
<div class="Pay" style="float: left;">
    <div id="PayFliterPanel" style="width: 300px;">
        <h2>Фильтр</h2>
        <div id="PayFilter" class="Pay-from-group">
            <div>
                <label class="Pay-label-control">период</label>
            </div>
            <div>
                <label class="Pay-label-control" for="PayFilterWith">с:</label>
                <input id="PayFilterWith" value="<?php echo date("01.m.Y"); ?>" placeholder="с">
            </div>
            <div>
                <label class="Pay-label-control" for="PayFilterBy">по:</label>
                <input id="PayFilterBy" value="<?php echo date("t.m.Y"); ?>" placeholder="по">
            </div>
            <!--
            <div>
                <label class="Pay-label-control">ФИО</label>
                <input id="PayFilterFIO" placeholder="ФИО">
            </div>
            -->
            <div>
                <label class="Pay-label-control">Уволенные</label>
                <input id="PayFilterFired" type="checkbox" class="Pay-inp">
            </div>
            <!--
            <div>
                <label>должность</label>
            </div>
            <div>
                <label class="Pay-label-control">все</label>
                <input name="PayFilterDolgnost[]" onchange="Pay.CheckboxChange(this)" class="Pay-inp" type="checkbox" value="-1" checked>
            </div>
            <?php
            /*
            $d=$m->query("SELECT * FROM manualdolgnost ORDER BY Dolgnost") or die($m->error);
            while ($r=$d->fetch_assoc()){ ?>
                <div>
                    <label class="Pay-label-control"><?php echo $r["Dolgnost"]; ?></label>
                    <input name="PayFilterDolgnost[]" onchange="Pay.CheckboxChange(this)" class="Pay-inp" type="checkbox" value="<?php echo $r["id"]; ?>" checked>
                </div>
            <?php };
            $d->close();
            */
            ?>
            -->
            <button onclick="Pay.SelectWorkers()">отобразить</button>

        </div>
    </div>
</div>
<div style="margin-left: 25px; margin-top: 25px; float: left;">
    <div>

    </div>
    <table class="Pay-table">
        <thead>
        <tr>
            <th rowspan="2">
                <input onkeypress="Pay.FindFIO(event)" id="PayFilterFIO" placeholder="Поиск ФИО" style="width: 80%">
            </th>
            <th rowspan="2">Должность</th>
            <th colspan="2">Наряды кол-во</th>
            <th colspan="2">Наряды сумм</th>
            <th colspan="2">Начислено</th>
            <th colspan="2">Удержано</th>
            <th rowspan="2">Итог</th>
        </tr>
        </thead>
        <tbody id="PayTableList"></tbody>
    </table>
</div>
<div id="PayPaymentsForm" title="Расчеты">
    <div class="Pay-from-group">
        <input type="hidden" id="PayPaymentsForm_idWorker">
        <div class="">
            <label for="PayPaymentsForm_Action">Движение</label>
            <select id="PayPaymentsForm_Action" style="width: 200px;">
                <option value="Plus">Начисление</option>
                <option value="Minus">Удержание</option>
            </select>
        </div>
        <div>
            <label for="PayPaymentsForm_FIO">Сотрудник</label>
            <select id="PayPaymentsForm_FIO" style="width: 200px;">
                <option value="-1"></option>
                <?php
                $d=$m->query("SELECT id, FIO FROM workers WHERE fired=0 ORDER BY FIO");
                while ($r=$d->fetch_assoc()){ ?>
                    <option value="<?php echo $r["id"]; ?>"><?php echo $r["FIO"]; ?></option>
                <?php };
                ?>
            </select>
        </div>
        <div>
            <label for="PayPaymentsForm_Date">Дата</label>
            <input id="PayPaymentsForm_Date" style="width: 200px;">
        </div>
        <div>
            <label for="PayPaymentsForm_Sum">Сумма</label>
            <input id="PayPaymentsForm_Sum" style="width: 200px;">
        </div>
        <div>
            <label for="PayPaymentsForm_Note">Примечание</label>
            <textarea id="PayPaymentsForm_Note" style="width: 200px;"></textarea>
        </div>
    </div>
</div>
<div id="PayOneDialog" title="Карточка сотрулника">
    <div>
        <input id="PayDialog_idWorker" type="hidden">
        <h3><span id="PayDialog_FIO"></span> - <span id="PayDialog_Dolgnost"></span></h3>
        <div class="">
            <button class="Pay-btn" onclick="PayAction.OpenDialog('Plus');">Начислить</button>
            <button class="Pay-btn" onclick="PayAction.OpenDialog('Minus');">Удержать</button>
        </div>
        <div id="PayOneDialog_tabs">
            <ul>
                <li><a href="#PayOneDialog_tab1">Общее</a> </li>
                <li><a href="#PayOneDialog_tab2">Наряды</a></li>
                <li><a href="#PayOneDialog_tab3">Начисления/Удержания</a> </li>
            </ul>
            <div id="PayOneDialog_tab1">
                <p class="Pay-from-group">
                    <div class="Pay-cart-group">
                        <label>Наряды сумма</label>
                        &nbsp;
                        <span id="PayDialog_CostAll"></span>
                    </div>
                    <div class="Pay-cart-group">
                        <label>Кол-во нарядов</label>
                        &nbsp;
                        <span id="PayDialog_CountAll"></span>
                    </div>
                    <div class="Pay-cart-group">
                        <label id="">Начислено</label>
                        &nbsp;
                        <span id="PayDialog_PaymentPlus"></span>
                    </div>
                    <div class="Pay-cart-group">
                        <label id="">Удержано</label>
                        &nbsp;
                        <span id="PayDialog_PaymentMinus"></span>
                    </div>
                    <div class="Pay-cart-group">
                        <label id=""><b>Итого</b></label>
                        &nbsp;
                        <span id="PayDialog_SumAll"></span>
                    </div>
                <div>
                    <span style="text-decoration: underline; color: #0a7bb1; cursor: pointer" onclick="PayDialog.SummaryDoorComlite()">Подробнее</span>
                    <table>
                        <thead>
                        <tr>
                            <th>Дверь</th>
                            <th>Открывание</th>
                            <th>Кол-во</th>
                            <th>Стоимость</th>
                        </tr>
                        </thead>
                        <tbody id="PayDialog_NaryadTable"></tbody>
                    </table>
                </div>
                </p>
            </div>
            <div id="PayOneDialog_tab2">
                <p>
                    <div class="Pay" style="width: 100px;">
                        <a id="PayDialog_NaryadPrint" href="#" class="btn" target="_blank">Печать списка</a>
                    </div>
                    <iframe id="PayOneDialog_NaryadFrame" src="Pay/frame/sample-api.php" style="display: block; width: 100%; height: 600px;">

                    </iframe>
                </p>
            </div>
            <div id="PayOneDialog_tab3">
                <p>
                    <table class="TablesHeight Tables">
                        <thead class="BorderTablesThead">
                        <tr>
                            <td>Статус</td>
                            <td>Дата</td>
                            <td>Примечание</td>
                            <td>Сумма</td>
                            <td>Ответственный</td>
                            <td></td>
                        </tr>
                        </thead>
                        <tbody id="PayOneDialog_TablePayments" class="BorderTablesTbody"></tbody>
                    </table>
                </p>
            </div>
        </div>
    </div>
</div>
<script>
    var Pay={
        SelectWorkers:function () {
            $("#PayTableList tr").remove();
            var arrCh=new Array();
            $("#PayFilter input:checkbox:checked").each(function(){
                if(this.id!=="PayFilterFired")
                    arrCh.push($(this).val());
            });
            $.post(
                "Pay/actions/SelectList.php",
                {
                    DateWith:$("#PayFilterWith").val(),
                    DateBy:$("#PayFilterBy").val(),
                    FIO:$("#PayFilterFIO").val(),
                    Fired:$("#PayFilterFired").is(":checked") ? 1 : 0
                    /* DolgnostCh:arrCh */
                },
                function (o) {
                    o.forEach(function(dolgnost){
                        let flagFirstRow=true;
                        dolgnost.Workers.forEach(function(worker){
                            $("#PayTableList").append(
                                "<tr idWorker='"+worker.idWorker+"' onclick='PayDialog.Open($(this).attr(\"idWorker\"))'>" +
                                    "<td class='Pay-td-align-left' style='text-align: left'>"+worker.FIO+"</td>"+
                                    "<td class='Pay-td-align-left' style='text-align: left'>"+(flagFirstRow ? dolgnost.Dolgnost : "")+"</td>"+
                                    "<td>"+worker.NaryadCount+"</td>"+
                                    "<td>"+worker.Cost+"</td>"+
                                    "<td>"+worker.PaymentPlus+"</td>"+
                                    "<td>"+worker.PaymentMinus+"</td>"+
                                    "<td>"+worker.SumEnd+"</td>"+
                                "</tr>"
                            );
                            flagFirstRow=false;
                        });
                        if(dolgnost.Workers.length>0)
                            $("#PayTableList").append(
                                "<tr>" +
                                    "<th class='Pay-td-align-left' colspan='2'>Итог ("+dolgnost.Dolgnost+")</th>"+
                                    "<th colspan='4'></th>"+
                                    "<th>"+dolgnost.SumEnd+"</th>"+
                                "</td>"
                            );
                    })
                }
            )
        },
        CheckboxChange:function (el) {
            switch ($(el).val()){
                case "-1":
                    switch ($(el).is(":checked")){
                        case true:
                            $("#PayFilter input:checkbox").each(function(){
                                if(this.id!=="PayFilterFired")
                                    $(this).prop("checked",true);
                            });
                            break;
                        case false:
                            $("#PayFilter input:checkbox").each(function(){
                                if(this.id!=="PayFilterFired")
                                    $(this).prop("checked",false);
                            });
                            break;
                    }
                    break;
                default:
                    switch ($(el).is(":checked")){
                        case true:
                            let checkboxLen=$("#PayFilter input:checkbox").length-1;
                            let checkedLen=$("#PayFilter input:checkbox:checked").length;
                            checkedLen=$("#PayFilterFired").is(":checked") ? checkedLen-1 : checkedLen
                            //checkedLen+=$("#PayFilter input:checkbox[value=-1]").is(":checked") ? 0 :-1;
                            if(checkedLen>=checkboxLen-1)
                                //$("#PayFilter input:checkbox").prop("checked",true);
                                $("#PayFilter input:checkbox").each(function(){
                                    if(this.id!=="PayFilterFired")
                                        $(this).prop("checked",true);
                                });
                            break;
                        case false:
                            if(el.id!=="PayFilterFired")
                                $("#PayFilter input:checkbox[value=-1]").prop("checked",false);
                            break;
                    };
                    break;
            } 
        },
        FindFIO:function (e) {
            if(e.which===13)
                Pay.SelectWorkers();
        }
    };
    var PayAction={
        OpenDialog:function(Action, idWorker){
            $("#PayPaymentsForm_Action").val(Action);
            $("#PayPaymentsForm_idWorker").val($("#PayDialog_idWorker").val());
            $("#PayPaymentsForm_FIO").val($("#PayDialog_idWorker").val());
            var dt=new Date();
            $("#PayPaymentsForm_Date").val(dt.format("dd.mm.yyyy"));
            $("#PayPaymentsForm_Sum").val("");
            $("#PayPaymentsForm_Note").val("");
            $("#PayPaymentsForm").dialog("open");
        },
        Save:function () {
            var flag=true;
            var sum=$("#PayPaymentsForm_Sum").val();
            $("#PayPaymentsForm_Sum").css("background-color", sum=="" ? "#ffaeb6" : "white");
            flag=sum=="" ? false : flag;
            var note=$("#PayPaymentsForm_Note").val();
            $("#PayPaymentsForm_Note").css("background-color", note=="" ? "#ffaeb6" : "white");
            flag=note=="" ? false : flag;
            if(flag)
                $.post(
                    "Pay/actions/PaymentAction.php",
                    {
                        Action:$("#PayPaymentsForm_Action").val(),
                        idWorker:$("#PayPaymentsForm_FIO").val(),
                        Date:$("#PayPaymentsForm_Date").val(),
                        Sum:$("#PayPaymentsForm_Sum").val(),
                        Note:$("#PayPaymentsForm_Note").val()
                    },
                    function (o) {
                        switch (o.Status){
                            case "Success":
                                $("#PayPaymentsForm").dialog("close");
                                Pay.SelectWorkers();
                                break;
                            case "Error":
                                alert("Ошибка: "+o.Note);
                                break;
                        };
                    }
                );
        },
        Remove:function (el) {
            var idPayment=$(el).parent().parent().attr("idPayment");
            if(confirm("Пометить как удалена?"))
                $.post(
                    "Pay/actions/DeletePayment.php",
                    {
                        idPayment:idPayment
                    },
                    function (o) {
                        if(o=="")
                            PayDialog.Open($("#PayDialog_idWorker").val());
                    }
                )
        }
    };
    var PayDialog={
        Open:function (idWorker) {
            $("#PayOneDialog_TablePayments tr").remove();
            //var idWorker=$(tr).attr("idWorker");
            $("#PayDialog_idWorker").val(idWorker);
            $.post(
                "Pay/actions/SelectOneSummary.php",
                {
                    idWorker:idWorker,
                    DateWith:$("#PayFilterWith").val(),
                    DateBy:$("#PayFilterBy").val()
                },
                function(o){
                    $("#PayDialog_FIO").text(o.FIO);
                    $("#PayDialog_Dolgnost").text(o.Dolgnost);
                    $("#PayDialog_CostAll").text(o.CostAll);
                    $("#PayDialog_CountAll").text(o.CountAll);
                    $("#PayDialog_PaymentPlus").text(o.PaymentPlus);
                    $("#PayDialog_PaymentMinus").text(o.PaymentMinus);
                    $("#PayDialog_SumAll").text(o.SumAll);
                    var DateWith=$("#PayFilterWith").val();
                    var DateBy=$("#PayFilterBy").val();
                    $('#PayOneDialog_NaryadFrame').attr('src','Pay/frame/sample-api.php?idWorker='+idWorker+'&DateWith='+DateWith+'&DateBy='+DateBy);

                    $("#PayDialog_NaryadPrint").attr("href", "Pay/actions/NaryadPrint.php?idWorker="+idWorker+"&DateWith="+DateWith+"&DateBy="+DateBy);
                    o.NaryadSummary.forEach(function(od){
                        $("#PayDialog_NaryadTable").append(
                            "<tr>" +
                                "<td Left>"+od.Name+'</td>'+
                                "<td Left>"+(od.S==1 ? "Одностворчатая" : "Двухстворчатая")+"</td>"+
                                "<td Right>"+od.Count+"</td>"+
                                "<td Right>"+od.Cost+"</td>"+
                            "</tr>"
                        )
                    });
                    $("#PayDialog_NaryadTable").parent().hide();
                    $("#PayOneDialog").dialog("open");
                }
            );
            $.post(
                "Pay/actions/SelectOnePayments.php",
                {
                    idWorker:idWorker,
                    DateWith:$("#PayFilterWith").val(),
                    DateBy:$("#PayFilterBy").val()
                },
                function (o) {
                    o.forEach(function (p) {
                        var status=p.Status=="Edit" ? "Изменен" : (p.Status=="Remove" ? "Удален" : '');
                        var accountant=p.AlterAccountant=="" ? p.Accountant : p.AlterAccountant;

                        $("#PayOneDialog_TablePayments").append(
                            "<tr idPayment='"+p.id+"'>"+
                                "<td>"+status+"</td>"+
                                "<td>"+p.DatePayment+"</td>"+
                                "<td>"+p.Note+"</td>"+
                                "<td>"+p.Sum+"</td>"+
                                "<td>"+accountant+"</td>"+
                                "<td>"+
                                    (p.Status=="Remove" ? "" : "<img onclick='PayAction.Remove(this)' src='images/delete.png' style='width:20px'>" )+
                                "</td>"+
                            "</tr>"
                        );
                    })
                }
            )
        },
        SelectOrders:function(){
            $.post(
                "Pay/actions/SelectOneDoors.php",
                {
                    idWorker:$("#PayDialog_idWorker").val(),
                    DateWith:$("#PayFilterWith").val(),
                    DateBy:$("#PayFilterBy").val()
                },
                function(o){

                }
            )
        },
        SummaryDoorComlite:function () {
            switch ($("#PayDialog_NaryadTable").parent().is(":visible"))
            {
                case true:
                    $("#PayDialog_NaryadTable").parent().hide();
                    break;
                case false:
                    $("#PayDialog_NaryadTable").parent().show();
                    break;
            }
        }
    };
    $( function() {
        $(window).scroll(
            function () {
                switch ($(this).scrollTop()>10){
                    case true:
                        $("#PayTableList").parent().find("thead").addClass("Pay-table-headerFixed");
                        break;
                    case false:
                        $("#PayTableList").parent().find("thead").removeClass("Pay-table-headerFixed");
                        break;
                }
            }
        );
        $( "#PayFliterPanel" ).accordion();
        $("#PayFilterWith").datepicker();
        $("#PayPaymentsForm_Date").datepicker();
        $("#PayFilterBy").datepicker();
        $("#PayPanelAction").accordion();
        Pay.SelectWorkers();
        $("#PayPaymentsForm").dialog({
            autoOpen:false,
            modal:true,
            buttons:[
                {
                    text:"Сохранить",
                    click:function(){
                        PayAction.Save();
                    }
                },
                {
                    text:"Отмена",
                    click:function () {
                        $( this ).dialog( "close" );
                    }
                }
            ]
        });
        $("#PayOneDialog").dialog({
            autoOpen:false,
            modal:true,
            width:800,
            buttons:[
                {
                    text:"Печать",
                    click:function(){
                        PayAction.Save();
                    }
                },
                {
                    text:"Отмена",
                    click:function () {
                        $( this ).dialog( "close" );
                    }
                }
            ]
        });
        $("#PayOneDialog_tabs").tabs();
    } );
</script>
