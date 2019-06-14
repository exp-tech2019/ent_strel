<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 08.12.2016
 * Time: 21:47
 */
    $idOrder="";
    $OrderNum="";
    $DateCreate="";
    $Shet="";
    $ShetDate="";
    $Customer="";
$idCustomer="";
    $Manager="";
    $Status=0;
    $StatusText="";
    if( isset($_GET["idOrder"]))
    {
        $idOrder=$_GET["idOrder"];
        $OrderNum=OrderNumGenerate($_GET["idOrder"]);
        $d=$m->query("SELECT o.*, c.id AS idCustomer, c.Name AS CustomerName, DATE_FORMAT(o.DateCreate,'%d.%m.%Y') AS DateCreateS, DATE_FORMAT(o.ShetDate, '%d.%m.%Y') AS ShetDateS, l.FIO AS Manager FROM TempOrders o, Logins l, Customers c WHERE o.id=$idOrder AND o.idManager=l.id AND o.idCustomer=c.id");
        $r=$d->fetch_assoc();
        $DateCreate=$r["DateCreateS"];
        $Shet=$r["Shet"];
        $ShetDate=$r["ShetDateS"];
        $idCustomer=$r["idCustomer"];
        $Customer=$r["CustomerName"];
        $Manager=$r["Manager"];
        $Status=$r["Status"];
    }
    else
    {

    };
    switch ($Status){
        case 0: $StatusText="Новый"; break;
        case 1: $StatusText="В производстве"; break;
        case 2: $StatusText="Выполнен"; break;
        case -1: $StatusText="Отменен"; break;
    };
?>
<div class="panel panel-default">
    <div class="panel-body">
        <button id="BtnSave" type="button" onclick="SaveChange()" class="btn btn-primary">Сохранить</button>
        <span class="alert alert-info" id="MainStatus">Статус: <span Type="Status"><?php echo $StatusText; ?></span></span>
        <span class="alert alert-info" id="MainSum">Общяя стоимость: <span Type="OrderSum"></span> Оплачено: <span Type="PaymentSum"></span></span>
    </div>
</div>

<div class="alert alert-danger" id="AlertDiv">...</div>
<ul class="nav nav-tabs">
    <li class="active"><a href="#TabOrder" data-toggle="tab">Заказ</a></li>
    <li><a href="#TabDoors" data-toggle="tab">Позиции</a></li>
    <li><a href="#TabCustomer" data-toggle="tab">Платежи</a></li>
    <li><a href="#TabNotes" data-toggle="tab">Комментарии</a></li>
</ul>


<!-- Tab panes -->
<div class="tab-content">
    <!--Заказ-->
    <div class="tab-pane active" id="TabOrder">
        <form role="form">
            <input type="hidden" id="idOrder" value="<?php echo $idOrder; ?>">
            <div class="form-group">
                <label for="exampleInputEmail1">Заказ</label>
                <input type="text" class="form-control" id="OrderNum" placeholder="Заказ" value="<?php echo $OrderNum; ?>">
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Дата создания</label>
                <input type="text" class="form-control" id="OrderDateCreate" placeholder="Дата создания" value="<?php echo $DateCreate; ?>">
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Счет</label>
                <input type="text" class="form-control" id="OrderShet" placeholder="Счет" value="<?php echo $Shet; ?>"/>
            </div>
            <div class="form-group">
                <label for="OrderShetDate">Дата счета</label>
                <div class="input-group date" id="OrderShetDate">
                    <input type="text" class="form-control" id="OrderShetDate" value="<?php echo $ShetDate; ?>" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
                <script>
                    $(function(){
                        $('#OrderShetDate').datetimepicker(
                            {pickTime: false,language: 'ru'}
                        );
                    })
                </script>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Заказчик</label>
                <input type="hidden" id="OrderCustomerID" value="<?php echo $idCustomer; ?>">
                <input type="text" class="form-control" id="OrderCustomer" placeholder="Заказчик" value="<?php echo $Customer; ?>" onclick="CustomersStart()">
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Менеджер</label>
                <input type="text" class="form-control" id="OrderManager" placeholder="Менеджер" value="<?php echo $Manager; ?>">
            </div>
        </form>
    </div>
    <!--Список дверей-->
    <div class="tab-pane" id="TabDoors">
        <button id="BtnSave" type="button" onclick="AddRow()" class="btn btn-primary">Добавить позициюь</button>
        <button onclick="CalculationAll()" class="btn btn-primary">Расчитать стоимость</button>
        <table class='table table-striped table-condensed table-hover'>
            <tr>
            <tr>
                <td></td>
                <td>№</td>
                <td>Наименование</td>
                <td>Кол-во</td>
                <td>Высота</td>
                <td>Ширина</td>
                <td>Откр.</td>
                <td>Раб. ств.</td>
                <td>Окрас</td>
                <td>Наличник</td>
                <td>Доводчик</td>
                <td>Навес р.ств.</td>
                <td>Навес вт.ств.</td>
                <td>Окно р.ств.</td>
                <td>Окно вт.ств.</td>
                <td>Решетка р.ств.</td>
                <td>Решетка вт.ств.</td>
                <td>Фрамуга</td>
                <td>Высота фрамуги</td>
                <td>Примечание</td>
            </tr>
            </thead>
            <tbody id="DoorTable">
            <?php
            $glm=new GlobalManuals($m);

            if( isset($_GET["idOrder"])) {
                $d = $m->query("SELECT * FROM TempOrderDoors WHERE idOrder=$idOrder");
                if ($d->num_rows > 0)
                    while ($r = $d->fetch_assoc()) {
                        $S="";
                        if($r["S"]!=null & $r["S"]!="" & $r["SEqual"]==0) $S=$r["S"];
                        $Framug=$r["Framug"]==1 ? "да" : "нет";
                        ?>
                        <tr Status="Load" idDoor="<?php echo $r["id"]; ?>" TRGuid="">
                            <td><span class="glyphicon glyphicon-remove-circle" onclick="RemoveRow(this)"></span> </td>
                            <td Type="NumPP"><input style="width: 30px;" width="30" value="<?php echo $r["NumPP"];?>" onchange="RowChange(this)"></td>
                            <td Type="TypeDoor"><select style="width: 150px;" onchange="RowChange(this)"><?php echo ConstructList($r["TypeDoor"], $glm->TypeDoor);?></select></td>
                            <td Type="Count"><input style="width: 50px;" value="<?php echo $r["Count"];?>" onchange="RowChange(this)"></td>
                            <td Type="H"><input style="width: 50px;" value="<?php echo $r["H"];?>" onchange="RowChange(this)"></td>
                            <td Type="W"><input style="width: 50px;" value="<?php echo $r["W"];?>" onchange="RowChange(this)"></td>
                            <td Type="Open"><select style="width: 150px;" onchange="RowChange(this)"><?php echo ConstructList($r["Open"], $glm->OpenDoor);?></select></td>
                            <td Type="S"><input type="text" style="width: 50px;" value="<?php echo $S ;?>" onchange="RowChange(this)">  <input type="checkbox" <?php echo $r["SEqual"]==1 ? "checked":"" ?> onchange="RowChange(this)"> равн</td>
                            <td Type="Ral"><input style="width: 80px;" value="<?php echo $r["Ral"];?>" onchange="RowChange(this)"></td>
                            <td Type="Nalichnik"><select style="width: 65px;" onchange="RowChange(this)"><?php echo ConstructList($r["Nalichnik"], $glm->Nalichnik);?></select></td>
                            <td Type="Dovod"><select style="width: 60px;" onchange="RowChange(this)"><?php echo ConstructList($r["Dovod"], $glm->DovodList);?></select></td>

                            <td Type="NavesWork"><input style="width: 30px;" value="<?php echo $r["NavesWork"]!=0 ? $r["NavesWork"] : "" ;?>" onchange="RowChange(this)"></td>
                            <td Type="NavesStvorka"><input style="width: 30px;" value="<?php echo $r["NavesStvorka"]!=0 ? $r["NavesStvorka"] : "" ;?>" onchange="RowChange(this)"></td>

                            <td Type="WindowWork" Win1_H="0" Win1_W="0" Win2_H="0" Win2_W="0" Win3_H="0" Win3_W="0"><input style="width: 30px;" value="<?php echo $r["WindowWork"]!=0 ? $r["WindowWork"] : "" ;?>" onchange="RowChange(this)"></td>
                            <td Type="WindowStvorka" Win1_H="0" Win1_W="0" Win2_H="0" Win2_W="0" Win3_H="0" Win3_W="0"><input style="width: 30px;" value="<?php echo $r["WindowStvorka"]!=0 ? $r["WindowStvorka"] : "" ;?>" onchange="RowChange(this)"></td>

                            <td Type="GridWork"><input style="width: 30px;" value="<?php echo $r["GridWork"]!=0 ? $r["GridWork"] : "" ;?>" onchange="RowChange(this)"></td>
                            <td Type="GridStvorka"><input style="width: 30px;" value="<?php echo $r["GridStvorka"]!=0 ? $r["GridStvorka"] : "" ;?>" onchange="RowChange(this)"></td>

                            <td Type="Framug"><select style="width: 50px;" onchange="RowChange(this)"><?php echo ConstructList($Framug, array("да", "нет"));?></select></td>
                            <td Type="FramugH"><input style="width: 60px;" value="<?php echo $r["FramugH"]!=0 ? $r["FramugH"] : "" ;?>" onchange="RowChange(this)"></td>

                            <td Type="Note"><textarea onchange="RowChange(this)"><?php echo $r["Note"]; ?></textarea></td>
                            <?php
                                $CalcSumAll=0;
                                $d1=$m->query("SELECT SUM(Sum) AS SumAll FROM TempOrderDoorCalc WHERE idDoor=".$r["id"]);
                                if($d1->num_rows>0)
                                {$r1=$d1->fetch_assoc(); $CalcSumAll=$r1["SumAll"]!=null ? $r1["SumAll"] : 0;};
                            ?>
                            <td Type='Sum'><button style="min-width: 70px;" onclick="CalcStart(this)" class='btn btn-default'><?php echo $CalcSumAll; ?></button>
                                <calc style='display: none'>
                                    <?php
                                        $d1=$m->query("SELECT * FROM TempOrderDoorCalc WHERE idDoor=".$r["id"]);
                                        if($d1->num_rows>0)
                                            while ($r1=$d1->fetch_assoc())
                                            { ?>
                                                <element CalcGuid="" idCalc="<?php echo $r1["id"] ?>" Type="<?php echo $r1["Type"] ?>" Name="<?php echo $r1["Name"] ?>" Status="Load"><?php echo $r1["Sum"]; ?></element>
                                            <?php };
                                    ?>
                                </calc>
                            </td>
                        </tr>
                        <?php
                    };
            };
            ?>
            </tbody>
        </table>
    </div>
    <div class="tab-pane" id="TabCustomer">
        <?php include "OrderEdit/Payments.php" ?>
    </div>
    <div class="tab-pane" id="TabNotes">4</div>
</div>

<?php
    function OrderNumGenerate($id){
        while(strlen($id)<6)
            $id="0".$id;
        return $id;
    };
    function ConstructList($s, $arr){
        $Return="";
        foreach ($arr as $a)
            if($a==$s)
            {
                $Return=$Return."<option selected>$a</option>";
            }
            else
                $Return=$Return."<option>$a</option>";
        return $Return;
    };
?>


<script>
    //Назначим Guid Строкам которые были загруженны при формированиии страницы
    $(document).ready(function() {
        $("#AlertDiv").hide();
        for (var i = 0; i < $("#DoorTable tr").length; i++) {
            var TR = $("#DoorTable tr:eq(" + i + ")");
            TR.attr("TRGuid", guid());
            for (var j = 0; j < TR.find("td[Type=Sum] calc element").length; j++)
                TR.find("td[Type=Sum] calc element:eq(" + j + ")").attr("CalcGuid", guid());
        };
        SumChange();
    })

    var GloblaManuals={};
    //Подгрузим список дверей
    GloblaManuals.TypeDoors=new Array(
        <?php
        $s="";
        foreach ($glm->TypeDoor as $c)
            $s=$s."'".$c."', ";
        echo $s;
        ?>
        "");
    GloblaManuals.Dovod=new Array(
        <?php
        $s="";
        foreach ($glm->DovodList as $c)
            $s=$s."'".$c."', ";
        echo $s;
        ?>
        null);
    GloblaManuals.Nalichnik=new Array(
        <?php
        $s="";
        foreach ($glm->Nalichnik as $c)
            $s=$s."'".$c."', ";
        echo $s;
        ?>
        "");
    GloblaManuals.OpenDoor=new Array(
        <?php
        $s="";
        foreach ($glm->OpenDoor as $c)
            $s=$s."'".$c."', ";
        echo $s;
        unset($glm);
        unset($s);
        ?>
        null);
    //Добавить строку
    function AddRow() {
        $("#DoorTable").append(

            "<tr Status='Add' idDoor='' TRGuid='"+guid()+"'> "+
                "<td><span class='glyphicon glyphicon-remove-circle' onclick='RemoveRow(this)'></span></td>"+
                "<td Type='NumPP'><input style='width: 30px;' value='' onchange='RowChange(this)'></td>"+
                "<td Type='TypeDoor'><select style='width: 150px;' onchange='RowChange(this)'>"+ConstructSelectList("",GloblaManuals.TypeDoors)+"</select></td>"+
                "<td Type='Count'><input style='width: 50px;' value='' onchange='RowChange(this)'></td>"+
                "<td Type='H'><input style='width: 50px;' value='' onchange='RowChange(this)'></td>"+
                "<td Type='W'><input style='width: 50px;' value='' onchange='RowChange(this)'></td>"+
                "<td Type='Open'><select style='width: 150px;' onchange='RowChange(this)'>"+ConstructSelectList("",GloblaManuals.OpenDoor, true)+"</select></td>"+
                "<td Type='S'><input type='text' style='width: 50px;' value='' onchange='RowChange(this)'>  <input type='checkbox' onchange='RowChange(this)'> равн</td>"+
                "<td Type='Ral'><input style='width: 80px;' value='' onchange='RowChange(this)'></td>"+
                "<td Type='Nalichnik'><select style='width: 65px;' onchange='RowChange(this)'>"+ConstructSelectList("",GloblaManuals.Nalichnik, true)+"</select></td>"+
                "<td Type='Dovod'><select style='width: 60px;' onchange='RowChange(this)'>"+ConstructSelectList("",GloblaManuals.Dovod, true)+"</select></td>"+

                "<td Type='NavesWork'><input style='width: 30px;' value='' onchange='RowChange(this)'></td>"+
                "<td Type='NavesStvorka'><input style='width: 30px;' value='' onchange='RowChange(this)'></td>"+

                "<td Type='WindowWork'><input style='width: 30px;' value='' onchange='RowChange(this)'></td>"+
                "<td Type='WindowStvorka'><input style='width: 30px;' value='' onchange='RowChange(this)'></td>"+

                "<td Type='GridWork'><input style='width: 30px;' value='' onchange='RowChange(this)'></td>"+
                "<td Type='GridStvorka'><input style='width: 30px;' value='' onchange='RowChange(this)'></td>"+

                "<td Type='Framug'><select style='width: 50px;' onchange='RowChange(this)'>"+ConstructSelectList("",new Array("да","нет",""), true)+"</select></td>"+
                "<td Type='FramugH'><input style='width: 60px;' value='' onchange='RowChange(this)'></td>"+

                "<td Type='Note'><textarea></textarea></td>"+
                "<td Type='Sum'><button onclick='CalcStart(this)' class='btn btn-default'>0</button>"+
                    "<calc style='display: none'>"+
                    "</calc>"+
                "</td>"+
            "</tr>"
        );
    }

    function ConstructSelectList(DefaultS, Arr, NotNull=false){
        var Ret="";
        for(var i=0; i<Arr.length;i++)
            if((NotNull & Arr[i]!=null) || !NotNull)
            {
                var SelectFlag="";
                Ret=Ret+"<option "+(DefaultS==Arr[i] ? "selected" : "")+">"+Arr[i]+"</option>";
            };
        return Ret;
    }

    function RemoveRow(el){
        if(confirm("Удалить позицию?"))
            if($(el).parent().parent().attr("Status")=="Add")
            {
                $(el).parent().parent().remove();
            }
            else {
                $(el).parent().parent().hide();
                $(el).parent().parent().attr("Status","Remove");
            }
    }

    function RowChange(el){
        if($(el).parent().parent().attr("Status")=="Load")
            $(el).parent().parent().attr("Status","Edit");
    }

    function  SaveChange() {
        $("#AlertDiv").hide();
        var formData = {
            "idOrder":$("#idOrder").val(),
            "OrderNum":$("#OrderNum").val(),
            "OrderDateCreate":$("#OrderDateCreate").val(),
            "OrderShet":$("#OrderShet").val(),
            "OrderShetDate":$("#OrderShetDate").val(),
            "OrderCustomerID":$("#OrderCustomerID").val()!="" ? $("#OrderCustomerID").val() : "NULL",
            "OrderManager":$("#OrderManager").val(),
            "Table":{}
        };
        formData.TR=new Array();
        var c=0;
        for(var i=0; i<$("#DoorTable tr").length; i++)
            if($("#DoorTable tr:eq("+i+")").attr("Status")!="Load")
            {
                var TR=$("#DoorTable tr:eq("+i+")");
                switch(TR.attr("Status"))
                {
                    case "Add": case "Edit":
                        var Status=TR.attr("Status");
                        var idDoor=TR.attr("idDoor");
                        var TRGuid=TR.attr("TRGuid");
                        var NumPP=TR.find("td[Type=NumPP] input").val();
                        var Count=TR.find("td[Type=Count] input").val();
                        var TypeDoor=TR.find("td[Type=TypeDoor] select").val();
                        var H=TR.find("td[Type=H] input").val();
                        var W=TR.find("td[Type=W] input").val();
                        var Open=TR.find("td[Type=Open] select").val();
                        var S=TR.find("td[Type=S] input[type=text]").val();
                        var SEqual=TR.find("td[Type=S] input[type=checkbox]").prop("checked")
                        if(S=="") S="null";
                        if(SEqual) {S="NULL"; SEqual="1";};
                        if(!SEqual) SEqual="0";
                        var Ral=TR.find("td[Type=Ral] input").val();
                        var Nalichnik=TR.find("td[Type=Nalichnik] select").val();
                        var Dovod=TR.find("td[Type=Dovod] select").val();
                        var Note=TR.find("td[Type=Note] textarea").val();
                        var Markirovka='';

                        var NavesWork=TR.find("td[Type=NavesWork] input").val()!="" ? TR.find("td[Type=NavesWork] input").val() : "NULL";
                        var NavesStvorka=TR.find("td[Type=NavesStvorka] input").val()!="" ? TR.find("td[Type=NavesStvorka] input").val() : "NULL";

                        var WindowWork=TR.find("td[Type=WindowWork] input").val()!="" ? TR.find("td[Type=WindowWork] input").val() : "NULL";
                        var WindowStvorka=TR.find("td[Type=WindowStvorka] input").val()!="" ? TR.find("td[Type=WindowStvorka] input").val() : "NULL";

                        var GridWork=TR.find("td[Type=GridWork] input").val()!="" ? TR.find("td[Type=GridWork] input").val() : "NULL";
                        var GridStvorka=TR.find("td[Type=GridStvorka] input").val()!="" ? TR.find("td[Type=GridStvorka] input").val() : "NULL";

                        var Framug=TR.find("td[Type=Framug] select").val()=="да" ? "1" : "0";
                        var FramugH=TR.find("td[Type=FramugH] input").val()!="" ? TR.find("td[Type=FramugH] input").val() : "NULL";

                        formData.TR[c]={
                            "Status":Status,
                            "idDoor":idDoor,
                            "TRGuid":TRGuid,
                            "NumPP":NumPP,
                            "Count":Count,
                            "TypeDoor":TypeDoor,
                            "H":H,
                            "W":W,
                            "Open":Open,
                            "S":S,
                            "SEqual":SEqual,
                            "Ral":Ral,
                            "Nalichnik":Nalichnik,
                            "Dovod":Dovod,
                            "Note":Note,
                            "Markirovka":Markirovka,

                            "NavesWork":NavesWork,
                            "NavesStvorka":NavesStvorka,

                            "WindowWork":WindowWork,
                            "WindowStvorka":WindowStvorka,

                            "GridWork":GridWork,
                            "GridStvorka":GridStvorka,

                            "Framug":Framug,
                            "FramugH":FramugH
                        };
                        formData.TR[c].Calc=new Array();
                        var ca=0;
                        //Пересмотримм параметры расчета стоимости
                        var Calc=TR.find("td[Type=Sum] calc");
                        for(var j=0; j<Calc.find("element").length; j++)
                            if(Calc.find("element:eq(" + j + ")").attr("Status")!="Load")
                            {
                                var CalcOne = Calc.find("element:eq(" + j + ")");
                                switch (CalcOne.attr("Status")) {
                                    case "Add":
                                        var ClacGuid = CalcOne.attr("CalcGuid");
                                        var idCalc = CalcOne.attr("idCalc");
                                        var Type = CalcOne.attr("Type");
                                        var Name = CalcOne.attr("Name");
                                        var Status = CalcOne.attr("Status");
                                        var Sum = CalcOne.text();
                                        formData.TR[c].Calc[ca]={
                                            "Status":Status,
                                            "idCalc":idCalc,
                                            "CalcGuid":ClacGuid,
                                            "Type":Type,
                                            "Name":Name,
                                            "Sum":Sum
                                        };
                                        break;
                                    case "Remove":
                                        var idCalc = CalcOne.attr("idCalc");
                                        var Status = CalcOne.attr("Status");
                                        formData.TR[c].Calc[ca]={
                                            "Status":Status,
                                            "idCalc":idCalc
                                        };
                                        break;
                                };
                                ca++;
                            };
                        break;
                    case "Remove":
                        var Status=TR.attr("Status");
                        var idDoor=TR.attr("idDoor");
                        var TRGuid=TR.attr("TRGuid");
                        formData.TR[c]={
                            "Status":Status,
                            "idDoor":idDoor,
                            "TRGuid":TRGuid
                        }
                        break;
                };
                c++;
            };
        //Формируем платежи
        var PaymentsArr=new Array(); var c=0;
        for(var i=0; i<$("#PaymentsTable tr").length; i++)
            if($("#PaymentsTable tr:eq("+i+")").attr("Status")!="Load")
            {
                var TypePayment=$("#PaymentsTable tr:eq("+i+") td[Type=Type] select").val()=="Платеж" ? 0 : 1;

                PaymentsArr[c]={
                    "Status":$("#PaymentsTable tr:eq("+i+")").attr("Status"),
                    "Guid":$("#PaymentsTable tr:eq("+i+")").attr("PaymentGuid"),
                    "idPayment":$("#PaymentsTable tr:eq("+i+")").attr("idPayment"),
                    "Date":$("#PaymentsTable tr:eq("+i+") td[Type=Date] input").val(),
                    "TypePayment":TypePayment,
                    "Sum":$("#PaymentsTable tr:eq("+i+") td[Type=Sum] input").val(),
                    "Note":$("#PaymentsTable tr:eq("+i+") td[Type=Note] input").val()
                };
                c++;
            };
        formData.Payments=PaymentsArr;
        $.ajax({
            url:'OrderEdit/OrderSave.php'
            , type:'POST'
            , data:'jsonData=' + $.toJSON(formData)
            , success: function(res) {
                try {
                    var o = JSON.parse(res);
                    if(o.ErrorSuccess!==undefined)
                        for(var i=0; i<o.ErrorSuccess.length; i++)
                            for(var j=0; j<$("#DoorTable tr").length; j++)
                                if(o.ErrorSuccess[i].TRGuid==$("#DoorTable tr:eq("+j+")").attr("TRGuid"))
                                {
                                    var TR=$("#DoorTable tr:eq("+j+")");
                                    TR.find("td[Type=NumPP] input").css("background-color", "white");
                                    TR.find("td[Type=Count] input").css("background-color", "white");
                                    TR.find("td[Type=H] input").css("background-color", "white");
                                    TR.find("td[Type=W] input").css("background-color", "white");
                                    if (o.ErrorSuccess[i].NumPP == false) TR.find("td[Type=NumPP] input").css("background-color", "lightpink");
                                    if (o.ErrorSuccess[i].Count == false) TR.find("td[Type=Count] input").css("background-color", "lightpink");
                                    if (o.ErrorSuccess[i].H == false) TR.find("td[Type=H] input").css("background-color", "lightpink");
                                    if (o.ErrorSuccess[i].W == false) TR.find("td[Type=W] input").css("background-color", "lightpink");
                                    break;
                                };
                    if(o.Result!==undefined)
                        if(o.Result!="ok")
                        {
                            $("#AlertDiv").show();
                            $("#AlertDiv").text(o.Result);
                        }
                        else
                        {
                            //Если мы сохранили новый заказ, тогда установим idOrder
                            if (o.idOrder !== undefined)
                                $("#idOrder").val(o.idOrder);
                            //Назначем id из БД для новых позиций
                            if(o.TRAdd!==undefined)
                                for(var i=0; i<o.TRAdd.length; i++)
                                    for(var j=0; j<$("#DoorTable tr").length; j++)
                                        if(o.TRAdd[i].TRGuid==$("#DoorTable tr:eq("+j+")").attr("TRGuid"))
                                            $("#DoorTable tr:eq("+j+")").attr("idDoor",o.TRAdd[i].idDoor);
                            //Теперь пройдемся по таблице и изменим статусы на Load а так же удалим позиции
                            var i=0;
                            while(i<$("#DoorTable tr").length) {
                                var TR=$("#DoorTable tr:eq("+i+")");
                                switch (TR.attr("Status")){
                                    case "Add": case "Edit": TR.attr("Status","Load"); break;
                                    case "Remove": TR.remove(); i--; break;
                                }
                                i++;
                            }
                            //Назначим id из БД для новых расчетов
                            if(o.CalcAdd!==undefined)
                                for(var i=0; i<o.CalcAdd.length; i++)
                                    for(var j=0; j<$("#DoorTable tr").length; j++)
                                    {
                                        var Calc=$("#DoorTable tr:eq("+j+") td[Type=Sum] calc");
                                        for(var c=0; c<Calc.find("element").length; c++)
                                            if(o.CalcAdd[i].CalcGuid==Calc.find("element:eq("+c+")").attr("CalcGuid"))
                                                Calc.find("element:eq("+c+")").attr("idCalc",o.CalcAdd[i].idCalc);
                                    };
                            //Имзеним статусы для расчетов и удалим со статусом Remove
                            for(var i=0; i<$("#DoorTable tr").length; i++){
                                var Calc=$("#DoorTable tr:eq("+i+") td[Type=Sum] calc");
                                var ca=0;
                                while(ca<Calc.find("element").length) {
                                    switch (Calc.find("element:eq(" + ca + ")").attr("Status")) {
                                        case "Add":
                                            Calc.find("element:eq(" + ca + ")").attr("Status", "Load");
                                            break;
                                        case "Remove":
                                            Calc.find("element:eq(" + ca + ")").remove();
                                            ca--;
                                            break;
                                    }
                                    ca++;
                                }
                            };
                            //Назначим id из БД для новых платежей
                            if(o.PaymentAdd!==undefined)
                                for(var i=0; i<o.PaymentAdd.length; i++)
                                    for(var j=0; j<$("#PaymentsTable tr").length; j++)
                                    {
                                        var TR=$("#PaymentsTable tr:eq("+j+")");
                                        if(TR.attr("PaymentGuid")==o.PaymentAdd[i].Guid) {
                                            TR.attr("idPayment", o.PaymentAdd[i].idPayment);
                                            TR.attr("Status","Load");
                                        };
                                    };
                            //Удалим платежи со статусом Remove и переведем статус Edit на Load
                            for(var i=0;i<$("#PaymentsTable tr").length; i++)
                                switch ($("#PaymentsTable tr:eq(" + i + ")").attr("Status")) {
                                    case "Remove":
                                        $("#PaymentsTable tr:eq(" + i + ")").remove();
                                        i--;
                                        break;
                                    case "Edit":
                                        $("#PaymentsTable tr:eq(" + i + ")").attr("Status", "Load");
                                        break;
                                };
                        };
                }
                catch (err) {
                    $("#AlertDiv").show();
                    $("#AlertDiv").text(res);
                };
            }
        });

    }
</script>

<script>
    function CalculationAll(){
        var DoorSize=new Array(
            <?php
                $d=$m->query("SELECT * FROM TempCalcDoorSize");
                if($d->num_rows>0)
                    while($r=$d->fetch_assoc())
                    {
                        echo "{";
                        echo "'TypeDoor':'".$r["TypeDoor"]."',";
                        echo "'HWith':".($r["HWith"]==null ? "null" : $r["HWith"]).",";
                        echo "'HBy':".($r["HBy"]==null ? "null" : $r["HBy"]).",";
                        echo "'WWith':".($r["WWith"]==null ? "null" : $r["WWith"]).",";
                        echo "'WBy':".($r["WBy"]==null ? "null" : $r["WBy"]).",";
                        echo "'SEqual':".($r["SEqual"]==null ? "null" : $r["SEqual"]).",";
                        echo "'Framug':".$r["Framug"].",";
                        echo "'M2':".$r["M2"].",";
                        echo "'Sum':".$r["Sum"].",";
                        echo "},";
                    };
            ?>
            null
        );
        var RalList=new Array(
            <?php
                $d=$m->query("SELECT * FROM TempCalcRal");
                if($d->num_rows>0)
                    while($r=$d->fetch_assoc())
                    {
                        echo "{";
                        echo "'Name':'".$r["Name"]."',";
                        echo "'Percent':".$r["Percent"];
                        echo "},";
                    };
            ?>
            null
        );
        var DovodList=
            <?php
                $d=$m->query("SELECT * FROM TempCalcDovod");
                if($d->num_rows>0)
                {
                    $r=$d->fetch_assoc();
                    echo "{'DovodNo':".$r["DovodNo"].", 'DovodPodgotovka':".$r["DovodPodgotovka"].", 'DovodYes':".$r["DovodYes"]."};";
                }
            ?>
        var NavesList=new Array(
            <?php
            $d=$m->query("SELECT * FROM TempCalcNaves");
            if($d->num_rows>0)
                while($r=$d->fetch_assoc())
                {
                    echo "{";
                    echo "'Name':'".$r["Name"]."', ";
                    echo "'HWith':".($r["HWith"]!=null ? $r["HWith"] : "null").", ";
                    echo "'HBy':".($r["HBy"]!=null ? $r["HBy"] : "null").", ";
                    echo "'WWith':".($r["WWith"]!=null ? $r["WWith"] : "null").", ";
                    echo "'WBy':".($r["WBy"]!=null ? $r["WBy"] : "null").", ";
                    echo "'SEqual':".($r["SEqual"]==null ? "null" : $r["SEqual"]).",";
                    echo "'Sum':".$r["Sum"].",";
                    echo "}, ";
                }
            ?>
            null
        );
        //Установим предыдущие расчеты в Remove
        for(var i=0;i<$("#DoorTable tr").length; i++)
            if($("#DoorTable tr:eq("+i+")").attr("Status")!="Remove")
            {
                var Calc=$("#DoorTable tr:eq("+i+") td[Type=Sum] calc");
                for(var j=0; j<Calc.find("element").length; j++)
                {
                    var CalcOne=Calc.find("element:eq("+j+")");
                    if(CalcOne.attr("Status")!="Add") CalcOne.attr("Status","Remove");
                    if(CalcOne.attr("Status")=="Add") CalcOne.remove();
                };
            };


        for(var d=0;d<$("#DoorTable tr").length; d++)
            if($("#DoorTable tr:eq("+d+")").attr("Status")!="Remove")
            {
                var TR=$("#DoorTable tr:eq("+d+")");
                if(TR.attr("Status")=="Load")
                    TR.attr("Status","Edit");
                var SumAll=0
                //Расчитаем стоимость двери;
                var SumDoor=0;
                for (var i = 0; i < DoorSize.length; i++)
                    if (DoorSize[i] != null) {
                        var flagOK = true;
                        if (DoorSize[i].TypeDoor != TR.find("td[Type=TypeDoor] select").val()) flagOK = false;
                        if(DoorSize[i].HWith!=null & DoorSize[i].HBy!=null & TR.find("td[Type=H] input").val()!="")
                        {
                            var H=parseInt(TR.find("td[Type=H] input").val());
                            var flagH=false;
                            if(parseInt(DoorSize[i].HWith)<H & H<=DoorSize[i].HBy) flagH=true;
                            if(!flagH) flagOK=false;
                        };
                        if(DoorSize[i].WWith!=null & DoorSize[i].WBy!=null & TR.find("td[Type=W] input").val()!="")
                        {
                            var W=parseInt(TR.find("td[Type=W] input").val());
                            var flagW=false;
                            if(DoorSize[i].WWith<W & W<=DoorSize[i].WBy) flagH=true;
                            if(!flagW) flagOK=false;
                        };
                        switch(DoorSize[i].SEqual){
                            case null: break;
                            case 0: if(TR.find("td[Type=S] input[type=text]").val()!="" || !TR.find("td[Type=S] input[type=checkbox]").prop("checked")) flagOK=false; break;
                            case 1: if(TR.find("td[Type=S] input[type=text]").val()=="" || TR.find("td[Type=S] input[type=checkbox]").prop("checked")) flagOK=false; break;
                        };
                        if(DoorSize[i].Framug & TR.find("td[Type=Framug] select").val()=="нет") flagOK=false;
                        if(flagOK)
                            switch (DoorSize[i].M2){
                                case 0: SumDoor=DoorSize[i].Sum; break;
                                case 1: SumDoor=parseInt(TR.find("td[Type=H] input").val()) * parseInt(TR.find("td[Type=W] input").val()) * parseFloat(DoorSize[i].Sum);
                            };
                    };
                TR.find("td[Type=Sum] calc").append(
                    "<element idCalc='' Type='DoorSize' Name='DoorSize' Status='Add' CalcGuid='"+guid()+"'>"+SumDoor+"</element>"
                );
                SumAll+=SumDoor;
                //Расчитаем RAL
                var SumRal=0;
                for(var i=0;i<RalList.length; i++)
                    if(RalList[i]!=null)
                    if(TR.find("td[Type=Ral] input").val().toLowerCase().indexOf(RalList[i].Name.toLowerCase())>-1){
                        SumRal=SumDoor*RalList[i].Percent/100;
                        TR.find("td[Type=Sum] calc").append(
                            "<element idCalc='' Type='Ral' Name='"+RalList[i].Name+"' Status='Add' CalcGuid='"+guid()+"'>"+SumRal+"</element>"
                        );
                    };
                SumAll+=SumRal;
                //Расчитаем доводчик
                var SumDovod=0;
                switch (TR.find("td[Type=Dovod] select").val()){
                    case "да":SumDovod=DovodList.DovodYes; break;
                    case "нет":SumDovod=DovodList.DovodNo; break;
                    case "нет, подготовка":SumDovod=DovodList.DovodPodgotovka; break;
                };
                TR.find("td[Type=Sum] calc").append(
                    "<element idCalc='' Type='Dovod' Name='Dovod' Status='Add' CalcGuid='"+guid()+"'>"+SumDovod+"</element>"
                );
                SumAll+=SumDovod;
                //Расчитаем навесы
                var SumNaves=0;
                var NavesCount=0;
                NavesCount+=parseInt(TR.find("td[Type=NavesWork] input").val()!="" ? TR.find("td[Type=NavesWork] input").val() : 0);
                NavesCount+=parseInt(TR.find("td[Type=NavesStvorka] input").val()!="" ? TR.find("td[Type=NavesStvorka] input").val() : 0);
                for(var i=0;i<NavesList.length;i++)
                    if(NavesList[i]!=null)
                    {
                        var flagOK = true;
                        if(NavesList[i].HWith!=null & NavesList[i].HBy!=null & TR.find("td[Type=H] input").val()!="")
                        {
                            var H=parseInt(TR.find("td[Type=H] input").val());
                            var flagH=false;
                            if(parseInt(NavesList[i].HWith)<H & H<=NavesList[i].HBy) flagH=true;
                            if(!flagH) flagOK=false;
                        };
                        if(NavesList[i].WWith!=null & NavesList[i].WBy!=null & TR.find("td[Type=W] input").val()!="")
                        {
                            var W=parseInt(TR.find("td[Type=W] input").val());
                            var flagW=false;
                            if(NavesList[i].WWith<W & W<=NavesList[i].WBy) flagH=true;
                            if(!flagW) flagOK=false;
                        };
                        switch(NavesList[i].SEqual){
                            case null: break;
                            case 0: if(TR.find("td[Type=S] input[type=text]").val()!="" || !TR.find("td[Type=S] input[type=checkbox]").prop("checked")) flagOK=false; break;
                            case 1: if(TR.find("td[Type=S] input[type=text]").val()=="" || TR.find("td[Type=S] input[type=checkbox]").prop("checked")) flagOK=false; break;
                        };
                        if(flagOK) {
                            SumNaves = NavesCount * parseFloat(NavesList[i].Sum);
                            TR.find("td[Type=Sum] calc").append(
                                "<element idCalc='' Type='Naves' Name='"+NavesList[i].Name+"' Status='Add' CalcGuid='"+guid()+"'>"+SumNaves+"</element>"
                            );
                        };
                    };
                SumAll+=SumNaves;

                TR.find("td[Type=Sum] button").text(SumAll);
            };
    }
    //Перерасчет стоимости
    function SumChange(){
        var SumOrder=0;
        for(var i=0; i<$("#DoorTable tr").length; i++){
            var CalcTR=$("#DoorTable tr:eq("+i+") td[Type=Sum] calc");
            for(var j=0; j<CalcTR.find("element").length; j++)
                SumOrder+=CalcTR.find("element:eq("+j+")").text()!="" ? parseFloat(CalcTR.find("element:eq("+j+")").text()) : 0;
        };

        var SumPayment=0;
        for(var i=0; i<$("#PaymentsTable tr").length; i++)
            SumPayment+=$("#PaymentsTable tr:eq("+i+") td[Type=Sum] input").val()!="" ? parseFloat($("#PaymentsTable tr:eq("+i+") td[Type=Sum] input").val()) : 0;

        $("#MainSum span[Type=OrderSum]").text(SumOrder);
        $("#MainSum span[Type=PaymentSum]").text(SumPayment);
        $("#MainSum").removeClass();
        if(SumOrder<=SumPayment) $("#MainSum").addClass("alert alert-success");
        if(SumOrder>SumPayment) $("#MainSum").addClass("alert alert-info");
    }

    function guid() {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000)
                .toString(16)
                .substring(1);
        }
        return s4() + s4() +  s4() + s4() + s4();
    }
</script>

<?php include "OrderEdit/DialogCalc.php"; ?>
<?php include "OrderEdit/CustomersManual.php"; ?>


