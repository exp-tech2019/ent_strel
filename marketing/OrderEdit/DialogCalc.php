<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 23.12.2016
 * Time: 22:06
 */
?>
<div class="modal fade" tabindex="-1" role="dialog" id="DialogCalc">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Расчет стоимости двери</h4>
            </div>
            <div class="modal-body">
                <p>
                <div class="panel panel-default">
                    <div class="panel-heading">Размер двери</div>
                    <div class="panel-body" id="CalcDoorSize">
                        Высота: <span Type="H"></span> Ширина: <span Type="W"></span> <input/>
                    </div>
                </div>
                <!--RAL--->
                <div class="panel panel-default">
                    <div class="panel-heading">RAL окрас <button onclick="RalAddStart()" class="btn btn-default">Добавить</button></div>
                    <table class="table table-responsive" id="CalcRal">

                    </table>
                </div>
                <!--Dovod--->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Доводчик: <input id="CalcDovod">
                    </div>
                </div>
                <!--Naves--->
                <div class="panel panel-default">
                    <div class="panel-heading">Навесы <button onclick="NavesAddStart()" class="btn btn-default">Добавить</button></div>
                    <table class="table table-responsive" id="CalcNaves">

                    </table>
                </div>
                <!--Furniture--->
                <div class="panel panel-default">
                    <div class="panel-heading">Фурнитура <button onclick="FurnitureAddStart()" class="btn btn-default">Добавить</button></div>
                    <table class="table table-responsive" id="CalcFurniture">

                    </table>
                </div>
                <!--Glass--->
                <div class="panel panel-default">
                    <div class="panel-heading">Остекление </div>
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Окно</th>
                                <th>Высота</th>
                                <th>Ширина</th>
                                <th>Наименование</th>
                                <th>Сумма</th>
                            </tr>
                        </thead>
                        <tbody id="CalcGlass"></tbody>
                    </table>
                </div>
                <!--Other--->
                <div class="panel panel-default">
                    <div class="panel-heading">Дополниельно <button onclick="OtherAddStart()" class="btn btn-default">Добавить</button></div>
                    <table class="table table-responsive" id="CalcOther">

                    </table>
                </div>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button onclick="CalcSave()" type="button" class="btn btn-primary">Сохарнить</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    var TREditing;
    function CalcStart(TRel){
        var TR=$(TRel).parent().parent();
        TREditing=TR;
        $("#CalcRal tr").remove();
        $("#CalcDovod").val(0);
        $("#CalcNaves tr").remove();
        $("#CalcFurniture tr").remove();
        $("#CalcGlass tr").remove();
        if(TR.find("td[Type=WindowWork] input").val()!="")
            for(var i=0; i<TR.find("td[Type=WindowWork] input").val() & i<3; i++) {
                $("#CalcGlass").append(
                    "<tr Type=WindowWork" + (i+1).toString() + ">" +
                    "<td><span onclick='GlassRemove(this)' class='glyphicon glyphicon-remove'></span></td>"+
                    "<td Type=Caption>Окно " + (i + 1).toString() + " (раб. ств.)</td>" +
                    "<td Type=H><input style='width: 40px;' value='" + TR.find("td[Type=WindowWork]").attr("Win" + (i + 1).toString() + "_H") + "'></td>" +
                    "<td Type=W><input style='width: 40px;' value='" + TR.find("td[Type=WindowWork]").attr("Win" + (i + 1).toString() + "_W") + "'></td>" +
                    "<td Type=Name><button onclick='GlassAddStart(this)' class='btn btn-default' style='width:170px;'>&nbsp;</button></td> " +
                    "<td Type=Sum><input style='width: 50px; ' value='0'></td>" +
                    "</tr>"
                );
            };
        if(TR.find("td[Type=WindowStvorka] input").val()!="")
            for(var i=0; i<TR.find("td[Type=WindowStvorka] input").val() & i<3; i++) {
                $("#CalcGlass").append(
                    "<tr Type=WindowStvorka" + (i+1).toString() + ">" +
                        "<td><span onclick='GlassRemove(this)' class='glyphicon glyphicon-remove'></span></td>"+
                        "<td Type=Caption>Окно " + (i + 1).toString() + " (вт. ств.)</td>" +
                        "<td Type=H><input style='width: 40px;' value='" + TR.find("td[Type=WindowStvorka]").attr("Win" + (i + 1).toString() + "_H") + "'></td>" +
                        "<td Type=W><input style='width: 40px;' value='" + TR.find("td[Type=WindowStvorka]").attr("Win" + (i + 1).toString() + "_W") + "'></td>" +
                        "<td Type=Name><button onclick='GlassAddStart(this)' class='btn btn-default' style='width:170px;'>&nbsp;</button></td> " +
                        "<td Type=Sum><input style='width: 50px; ' value='0'></td>" +
                    "</tr>"
                );
            };
        $("#CalcOther tr").remove();

        var Calc=TR.find("td[Type=Sum] calc");
        for(var i=0; i<Calc.find("element").length; i++) {
            var CalcOne=Calc.find("element:eq(" + i + ")");
            if(Calc.find("element:eq(" + i + ")").attr("Status")!="Remove") {
                switch (Calc.find("element:eq(" + i + ")").attr("Type")) {
                    case "DoorSize":
                        $("#CalcDoorSize span[Type=H]").text(TR.find("td[Type=H] input").val());
                        $("#CalcDoorSize span[Type=W]").text(TR.find("td[Type=W] input").val());
                        $("#CalcDoorSize input").val(CalcOne.text());
                        break;
                    case"Ral":
                        $("#CalcRal").append(
                            "<tr idCalc='" + CalcOne.attr("idCalc") + "' Status='" + CalcOne.attr("Status") + "' >" +
                            "<td><span onclick='RalRamove(this)' class='glyphicon glyphicon-remove'></span></td>" +
                            "<td Type=Name>" + CalcOne.attr("Name") + "</td>" +
                            "<td Type=Sum><input oninput='CalcEditSum(this)' value='" + CalcOne.text() + "'></td>" +
                            "</tr>"
                        );
                        break;
                    case "Dovod":
                        $("#CalcDovod").val(CalcOne.text());
                        break;
                    case "Naves":
                        $("#CalcNaves").append(
                            "<tr idCalc='" + CalcOne.attr("idCalc") + "' Status='" + CalcOne.attr("Status") + "' >" +
                            "<td><span onclick='NavesRemove(this)' class='glyphicon glyphicon-remove'></span></td>" +
                            "<td Type=Name>" + CalcOne.attr("Name") + "</td>" +
                            "<td Type=Sum><input oninput='CalcEditSum(this)' value='" + CalcOne.text() + "'></td>" +
                            "</tr>"
                        );
                        break;
                    case "Furniture":
                        $("#CalcFurniture").append(
                            "<tr idCalc='" + CalcOne.attr("idCalc") + "' Status='" + CalcOne.attr("Status") + "' >" +
                                "<td><span onclick='FurnitureRamove(this)' class='glyphicon glyphicon-remove'></span></td>"+
                                "<td Type=Name>"+CalcOne.attr("Name")+"</td>"+
                                "<td Type=Sum><input value='"+CalcOne.text()+"'></td>"+
                            "</tr>"
                        );
                        break;
                    case "WindowWork1": case "WindowWork2": case "WindowWork3": case "WindowStvorka1": case "WindowStvorka2": case "WindowStvorka3":
                        if($("#CalcGlass tr[Type="+Calc.find("element:eq(" + i + ")").attr("Type")+"]")!==undefined)
                        {
                            $("#CalcGlass tr[Type="+Calc.find("element:eq(" + i + ")").attr("Type")+"] td[Type=Name] button").text(CalcOne.attr("Name"));
                            $("#CalcGlass tr[Type="+Calc.find("element:eq(" + i + ")").attr("Type")+"] td[Type=Sum] input").val(CalcOne.text());
                        }
                        break;
                    case "Other":
                        $("#CalcOther").append(
                            "<tr idCalc='" + CalcOne.attr("idCalc") + "' Status='" + CalcOne.attr("Status") + "' >" +
                                "<td><span onclick='OtherRamove(this)' class='glyphicon glyphicon-remove'></span></td>"+
                                "<td Type=Name>"+CalcOne.attr("Name")+"</td>"+
                                "<td Type=Sum><input value='"+CalcOne.text()+"'></td>"+
                            "</tr>"
                        );
                        break;
                };
            };
        };
        $('#DialogCalc').modal('show');
    };
    function CalcEditSum(el){
        $(el).parent().parent().attr("Status","Edit");
    }
    function CalcSave() {
        TREditing.attr("Status","Edit");
        var Calc=TREditing.find("td[Type=Sum] calc");
        var SumAll=0;
        //Установим статус для позиций Remove
        Calc.find("element").attr("Status","Remove");
        for(var i=0; i<Calc.find("element").length; i++)
            if(Calc.find("element:eq("+i+")").attr("idCalc")==0)
                Calc.find("element:eq("+i+")").remove();
        //Добавим стоимость двери
        var SumDoorSize=parseFloat($("#CalcDoorSize input").val()=="" ? "0" : $("#CalcDoorSize input").val());
        Calc.append(
            "<element idCalc='' Type='DoorSize' Name='DoorSize' Status='Add' CalcGuid='"+guid()+"'>"+SumDoorSize+"</element>"
        );
        SumAll+=SumDoorSize;
        //Добавим окрас
        var SumRal=0;
        for(var i=0; i<$("#CalcRal tr").length;i++)
        {
            var TR=$("#CalcRal tr:eq("+i+")");
            var SumRalOne=TR.find("td[Type=Sum] input").val()=="" ? 0 : parseFloat(TR.find("td[Type=Sum] input").val());
            Calc.append(
                "<element idCalc='' Type='Ral' Name='"+TR.find("td[Type=Name]").text()+"' Status='Add' CalcGuid='"+guid()+"'>"+SumRalOne+"</element>"
            );
            SumRal+=SumRalOne;
        };
        SumAll+=SumRal;
        //Добавим навесы
        var MavesSum=0;
        for(var i=0; i<$("#CalcNaves tr").length;i++)
        {
            var TR=$("#CalcNaves tr:eq("+i+")");
            var SumNavesOne=TR.find("td[Type=Sum] input").val()=="" ? 0 : parseFloat(TR.find("td[Type=Sum] input").val());
            Calc.append(
                "<element idCalc='' Type='Naves' Name='"+TR.find("td[Type=Name]").text()+"' Status='Add' CalcGuid='"+guid()+"'>"+SumNavesOne+"</element>"
            );
            MavesSum+=SumNavesOne;
        };
        SumAll+=MavesSum;
        //Фурнитура
        var SumFurniture=0;
        for(var i=0; i<$("#CalcFurniture tr").length;i++)
        {
            var TR=$("#CalcFurniture tr:eq("+i+")");
            Calc.append(
                "<element idCalc='' Type='Furniture' Name='"+TR.find("td[Type=Name]").text()+"' Status='Add' CalcGuid='"+guid()+"'>"+TR.find("td[Type=Sum] input").val()+"</element>"
            );
            SumFurniture+=parseFloat(TR.find("td[Type=Sum] input").val());
        };
        SumAll+=SumFurniture;

        //Остекление
        var SumGlass=0;
        for(var i=0;i<6;i++) {
            if ($("#CalcGlass").find("tr:eq(" + i + ")").length!=0) {
                var TR = $("#CalcGlass tr:eq(" + i + ")");
                var Type = TR.attr("Type").indexOf("WindowWork") > -1 ? "WindowWork" : "WindowStvorka";
                TREditing.find("td[Type=" + Type + "]").attr("Win" + (i + 1).toString() + "_H", TR.find("td[Type=H] input").val());
                TREditing.find("td[Type=" + Type + "]").attr("Win" + (i + 1).toString() + "_W", TR.find("td[Type=W] input").val());
                Calc.append(
                    "<element idCalc='' Type='" + TR.attr("Type") + "' Name='" + TR.find("td[Type=Name]").text() + "' Status='Add' CalcGuid='"+guid()+"'>" + TR.find("td[Type=Sum] input").val() + "</element>"
                );
                SumGlass += TR.find("td[Type=Sum] input").val() != "" ? parseFloat(TR.find("td[Type=Sum] input").val()) : 0;
            };
        };
        SumAll+=SumGlass;

        //Дополнительно
        var SumOther=0;
        for(var i=0; i<$("#CalcOther tr").length;i++)
        {
            var TR=$("#CalcOther tr:eq("+i+")");
            Calc.append(
                "<element idCalc='' Type='Other' Name='"+TR.find("td[Type=Name]").text()+"' Status='Add' CalcGuid='"+guid()+"'>"+TR.find("td[Type=Sum] input").val()+"</element>"
            );
            SumOther+=parseFloat(TR.find("td[Type=Sum] input").val());
        };
        SumAll+=SumOther;

        TREditing.find("td[Type=Sum] button").text(SumAll.toFixed(2));
        $('#DialogCalc').modal('hide');
    }
</script>

<div class="modal fade" tabindex="-1" role="dialog" id="DialogCalcManualRal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Справочник</h4>
            </div>
            <div class="modal-body">
                <p>
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <td>Наименование</td>
                        <td>Процент</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $d=$m->query("SELECT * FROM TempCalcRal ORDER BY Name");
                    if($d->num_rows>0)
                        while($r=$d->fetch_assoc())
                        { ?>
                            <tr onclick="RalAddSelect(this)"><td Type="Name"><?php echo $r["Name"]; ?></td><td Type="Percent"><?php echo $r["Percent"]; ?></td></tr>
                        <?php }
                    ?>
                    </tbody>
                </table>
                </p>
            </div>
        </div>
    </div>
</div>
<script>
    function RalAddStart(){
        $("#DialogCalcManualRal").modal("show");
    }
    function RalAddSelect(el) {
        var Sum=parseInt($(el).find("td[Type=Percent]").text())*(parseInt($("#CalcDoorSize input").val())/100);
        $("#CalcRal").append(
            "<tr Status='Add'>"+
                "<td><span onclick='RalRamove(this)' class='glyphicon glyphicon-remove'></span></td>"+
                "<td Type=Name>"+$(el).find("td[Type=Name]").text()+"</td>"+
                "<td Type=Sum><input value='"+Sum+"'></td>"+
            "</tr>"
        );
        $("#DialogCalcManualRal").modal("hide");
    }
    function RalRamove(el) {
        var TR=$(el).parent().parent();
        switch (TR.attr("Status")){
            case "Add":TR.remove(); break;
            default: TR.attr("Status","Remove"); TR.hide(); break;
        }
    }
</script>

<!-------- Навесы -------->
<div class="modal fade" tabindex="-1" role="dialog" id="DialogCalcManualNaves">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Справочник</h4>
            </div>
            <div class="modal-body">
                <p>
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <td>Наименование</td>
                        <td>Сумма за 1ед.</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $d=$m->query("SELECT * FROM TempCalcNaves ORDER BY Name");
                    if($d->num_rows>0)
                        while($r=$d->fetch_assoc())
                        { ?>
                            <tr onclick="NavesAddSelect(this)"><td Type="Name"><?php echo $r["Name"]; ?></td><td Type="Sum"><?php echo $r["Sum"]; ?></td></tr>
                        <?php }
                    ?>
                    </tbody>
                </table>
                </p>
            </div>
        </div>
    </div>
</div>
<script>
    function NavesAddStart(){
        $("#DialogCalcManualNaves").modal("show");
    }
    function NavesAddSelect(el) {
        var NavesCount=0;
        NavesCount+=parseInt(TREditing.find("td[Type=NavesWork] input").val()!="" ? TREditing.find("td[Type=NavesWork] input").val() : 0);
        NavesCount+=parseInt(TREditing.find("td[Type=NavesStvorka] input").val()!="" ? TREditing.find("td[Type=NavesStvorka] input").val() : 0);
        var Sum=parseFloat($(el).find("td[Type=Sum]").text())*NavesCount;
        $("#CalcNaves").append(
            "<tr Status='Add'>"+
            "<td><span onclick='NavesRemove(this)' class='glyphicon glyphicon-remove'></span></td>"+
            "<td Type=Name>"+$(el).find("td[Type=Name]").text()+"</td>"+
            "<td Type=Sum><input value='"+Sum+"'></td>"+
            "</tr>"
        );
        $("#DialogCalcManualNaves").modal("hide");
    }
    function NavesRemove(el) {
        console.log("fdf");
        var TR=$(el).parent().parent();
        switch (TR.attr("Status")){
            case "Add":TR.remove(); break;
            default: TR.attr("Status","Remove"); TR.hide(); break;
        }
    }
</script>

<!-------- Фурнитура -------->
<div class="modal fade" tabindex="-1" role="dialog" id="DialogCalcManualFurniture">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Справочник</h4>
            </div>
            <div class="modal-body">
                <p>
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <td>Наименование</td>
                        <td>Валюта</td>
                        <td>Стоимость</td>
                        <td>Стоимость (руб)</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $d=$m->query("SELECT * FROM TempCalcFurnitura ORDER BY Name");
                    $cbr=new GetCurrencyCBR();
                    if($d->num_rows>0)
                        while($r=$d->fetch_assoc())
                        {
                            $SumCurrency=0;
                            switch ($r["Currency"])
                            {
                                case "USD": $SumCurrency=floatval($r["Sum"])*$cbr->ValuteUSD; break;
                                case "EUR": $SumCurrency=floatval($r["Sum"])*$cbr->ValuteEUR; break;
                                default: $SumCurrency=$r["Sum"];
                            }
                            ?>
                            <tr onclick="FurnitureAddSelect(this)"><td Type="Name"><?php echo $r["Name"]; ?></td><td Type="Currency"><?php echo $r["Currency"]; ?></td><td Type="Sum"><?php echo $r["Sum"]; ?></td><td Type="SumCurrency"><?php echo $SumCurrency; ?></td></tr>
                        <?php }
                    ?>
                    </tbody>
                </table>
                </p>
            </div>
        </div>
    </div>
</div>
<script>
    function FurnitureAddStart(){
        $("#DialogCalcManualFurniture").modal("show");
    }
    function FurnitureAddSelect(el) {
        console.log($("#CalcFurniture").html());
        $("#CalcFurniture").append(
            "<tr Status='Add'>"+
            "<td><span onclick='FurnitureRamove(this)' class='glyphicon glyphicon-remove'></span></td>"+
            "<td Type=Name>"+$(el).find("td[Type=Name]").text()+"</td>"+
            "<td Type=Sum><input value='"+$(el).find("td[Type=SumCurrency]").text()+"'></td>"+
            "</tr>"
        );
        $("#DialogCalcManualFurniture").modal("hide");
    }
    function FurnitureRamove(el) {
        var TR=$(el).parent().parent();
        switch (TR.attr("Status")){
            case "Add":TR.remove(); break;
            default: TR.attr("Status","Remove"); TR.hide(); break;
        }
    }
</script>

<!-------- Остекление -------->
<div class="modal fade" tabindex="-1" role="dialog" id="DialogCalcManualGlass">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Справочник</h4>
            </div>
            <div class="modal-body">
                <p>
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <td>Наименование</td>
                        <td>м<sup>2</sup></td>
                        <td>Стоимость</td>
                        <td>Доп. стоимость</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $d=$m->query("SELECT * FROM TempCalcGlass ORDER BY Name");
                    if($d->num_rows>0)
                        while($r=$d->fetch_assoc())
                        {
                            ?>
                            <tr onclick="GlassAddSelect(this)">
                                <td Type="Name"><?php echo $r["Name"]; ?></td>
                                <td Type="M2"><?php echo $r["M2"]==1 ? "Да" : "Нет";?></td>
                                <td Type="Sum"><?php echo $r["Sum"]; ?></td>
                                <td Type="SumPlus"><?php echo $r["SumPlus"]; ?></td>
                            </tr>
                        <?php }
                    ?>
                    </tbody>
                </table>
                </p>
            </div>
        </div>
    </div>
</div>
<script>
    var GlassTREdit;
    function GlassAddStart(el){
        GlassTREdit=$(el).parent().parent();
        $("#DialogCalcManualGlass").modal("show");
    }
    function GlassAddSelect(el) {

        var Sum=0;
        switch ($(el).find("td[Type=M2]").text()){
            case "Да":
                var H=GlassTREdit.find("td[Type=H] input").val()!="" ? parseInt(GlassTREdit.find("td[Type=H] input").val()) : 0;
                var W=GlassTREdit.find("td[Type=W] input").val()!="" ? parseInt(GlassTREdit.find("td[Type=W] input").val()) : 0;
                Sum=parseFloat($(el).find("td[Type=Sum]").text())*H*W;
                break;
            case "Нет":
                Sum=parseFloat($(el).find("td[Type=Sum]").text());
                break;
        };
        Sum+=$(el).find("td[Type=SumPlus]").text()!="" ? parseFloat($(el).find("td[Type=SumPlus]").text()) : 0;
        GlassTREdit.find("td[Type=Sum] input").val(Sum);
        GlassTREdit.find("td[Type=Name] button").text($(el).find("td[Type=Name]").text());
        $("#DialogCalcManualGlass").modal("hide");
    }
    function FurnitureRamove(el) {
        var TR=$(el).parent().parent();
        switch (TR.attr("Status")){
            case "Add":TR.remove(); break;
            default: TR.attr("Status","Remove"); TR.hide(); break;
        }
    }
</script>

<!-------- Доплнительно -------->
<div class="modal fade" tabindex="-1" role="dialog" id="DialogCalcManualOther">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Справочник</h4>
            </div>
            <div class="modal-body">
                <p>
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <td>Наименование</td>
                        <td>Стоимость</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $d=$m->query("SELECT * FROM TempCalcOther ORDER BY Name");
                    if($d->num_rows>0)
                        while($r=$d->fetch_assoc())
                        { ?>
                            <tr onclick="OtherAddSelect(this)"><td Type="Name"><?php echo $r["Name"]; ?></td><td Type="Sum"><?php echo $r["Sum"]; ?></td></td></tr>
                        <?php }
                    ?>
                    </tbody>
                </table>
                </p>
            </div>
        </div>
    </div>
</div>
<script>
    function OtherAddStart(){
        $("#DialogCalcManualOther").modal("show");
    }
    function OtherAddSelect(el) {
        $("#CalcOther").append(
            "<tr Status='Add'>"+
            "<td><span onclick='OtherRamove(this)' class='glyphicon glyphicon-remove'></span></td>"+
            "<td Type=Name>"+$(el).find("td[Type=Name]").text()+"</td>"+
            "<td Type=Sum><input value='"+$(el).find("td[Type=Sum]").text()+"'></td>"+
            "</tr>"
        );
        $("#DialogCalcManualOther").modal("hide");
    }
    function OtherRamove(el) {
        var TR=$(el).parent().parent();
        switch (TR.attr("Status")){
            case "Add":TR.remove(); break;
            default: TR.attr("Status","Remove"); TR.hide(); break;
        }
    }
</script>