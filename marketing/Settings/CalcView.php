<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 19.12.2016
 * Time: 15:13
 */
/*
    $mn=new Mongo($GlobalMongoHost);
    $cursor= $mn->test1->items->find();
    echo $cursor->count()."<br>";
    foreach ($cursor as $obj)
        print_r($obj);
*/
    $gl=new GlobalManuals($m);
?>

<div>
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#Group1" aria-controls="home" role="tab" data-toggle="tab">Размеры</a></li>
        <li role="presentation"><a href="#Group2" aria-controls="profile" role="tab" data-toggle="tab">Краска</a></li>
        <li role="presentation"><a href="#Group3" aria-controls="messages" role="tab" data-toggle="tab">Доводчик</a></li>
        <li role="presentation"><a href="#Group4" aria-controls="settings" role="tab" data-toggle="tab">Навесы</a></li>
        <li role="presentation"><a href="#Group5" aria-controls="settings" role="tab" data-toggle="tab">Фурнитура</a></li>
        <li role="presentation"><a href="#Group6" aria-controls="settings" role="tab" data-toggle="tab">Остекление</a></li>
        <li role="presentation"><a href="#Group7" aria-controls="settings" role="tab" data-toggle="tab">Дополнительно</a></li>
    </ul>
    <div class="tab-content">
        <!--Размеры-->
        <div role="tabpanel" class="tab-pane active" id="Group1">
            <button onclick="DoorSizeAdd()" class="btn btn-primary">Добавить параметр</button>
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th></th>
                        <th>Тип</th>
                        <th colspan="2">Высота</th>
                        <th colspan="2">Ширина</th>
                        <th>Створка</th>
                        <th>Фрамуга</th>
                        <th>м<sup>2</sup></th>
                        <th>Стоимость</th>
                        <th style="width: 100px;"></th>
                    </tr>
                </thead>
                <tbody id="DoorSizeTable">
                <?php
                    $d=$m->query("SELECT * FROM TempCalcDoorSize ORDER BY TypeDoor, Sum");
                    if($d->num_rows>0)
                        while($r=$d->fetch_assoc()){
                        ?>
                            <tr idDoorSize="<?php echo $r["id"]; ?>">
                                <td><span onclick="DoorSizeRemove(this)" class='glyphicon glyphicon-remove'></span></td>
                                <td Type="TypeDoor"><?php echo ConstructTypeDoorList($r["TypeDoor"],$gl); ?></td>
                                <td Type="HWith">с <input style="width: 50px;" oninput="DoorSizeEdit(this)" value="<?php echo $r["HWith"]; ?>"></td>
                                <td Type="HBy">до <input style="width: 50px;" oninput="DoorSizeEdit(this)" value="<?php echo $r["HBy"]; ?>"></td>
                                <td Type="WWith">с<input style="width: 50px;" oninput="DoorSizeEdit(this)" value="<?php echo $r["WWith"]; ?>"></td>
                                <td Type="WBy">до <input style="width: 50px;" oninput="DoorSizeEdit(this)" value="<?php echo $r["WBy"]; ?>"></td>
                                <td Type="SEqual">
                                    <select onchange="DoorSizeEdit(this)">
                                        <option></option>
                                        <option <?php if($r["SEqual"]==0 & $r["SEqual"]!=null) echo "selected"; ?> >Одностворчатая</option>
                                        <option <?php if($r["SEqual"]==1) echo "selected"; ?> >Двухстворчатая</option>
                                    </select>
                                </td>
                                <td Type="Framug"><input onchange="DoorSizeEdit(this)" type="checkbox" <?php if($r["Framug"]==1) echo "checked"; ?> ></td>
                                <td Type="M2"><input onchange="DoorSizeEdit(this)" type="checkbox" <?php if($r["M2"]==1) echo "checked"; ?> ></td>
                                <td Type="Sum"><input oninput="DoorSizeEdit(this)" value="<?php echo $r["Sum"]; ?>"></td>
                                <td Type=BtnSave><button onclick="DoorSizeSave(this)" class="btn btn-primary">Сохранить</button></td>
                            </tr>
                        <?php
                        }
                ?>
                </tbody>
            </table>
        </div>
        <!-- RAL -->
        <div role="tabpanel" class="tab-pane" id="Group2">
            <div class="panel panel-default">
                <div class="panel-body">
                    <button onclick="RALAdd()" class="btn btn-primary">Добавить окрас</button>
                    <input oninput="RALFind(this)" style="border: none; float: right; width: 200px;" placeholder="Поиск">
                </div>
            </div>
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th></th>
                        <th>Наименование</th>
                        <th>Процент от стоимости</th>
                        <th style="width:100px;"></th>
                    </tr>
                </thead>
                <tbody id="RALTable">
                <?php
                    $d=$m->query("SELECT * FROM TempCalcRal ORDER BY Name");
                    if($d->num_rows>0)
                        while($r=$d->fetch_assoc())
                        {
                            ?>
                            <tr idRal="<?php echo $r["id"]; ?>">
                                <td><span onclick='RalRemove(this)' class='glyphicon glyphicon-remove-circle'></span> </td>
                                <td Type="Name"><input oninput="DoorSizeEdit(this)" value="<?php echo $r["Name"]; ?>"></td>
                                <td Type="Percent"><input oninput="DoorSizeEdit(this)" value="<?php echo $r["Percent"]; ?>"></td>
                                <td Type=BtnSave><button onclick='RALSave(this)' class='btn btn-primary'>Сохранить</button></td>
                            </tr>
                            <?php
                        };
                ?>
                </tbody>
            </table>
        </div>
        <!--Доводчик-->
        <div role="tabpanel" class="tab-pane" id="Group3">
            <?php
                $d=$m->query("SELECT * FROM TempCalcDovod");
                $r=$d->fetch_assoc();
            ?>
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">Нет доводчика</span>
                <input id="DovodNo" type="text" class="form-control" placeholder="Стоимость" aria-describedby="basic-addon1" value="<?php echo $r["DovodNo"]; ?>">
            </div>
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">Подготовка&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                <input id="DovodPodgotovka" type="text" class="form-control" placeholder="Стоимость" aria-describedby="basic-addon1" value="<?php echo $r["DovodPodgotovka"]; ?>">
            </div>
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">Есть доводчик</span>
                <input id="DovodYes" type="text" class="form-control" placeholder="Стоимость" aria-describedby="basic-addon1" value="<?php echo $r["DovodYes"]; ?>">
            </div>
            <button onclick="DovodSave()" class="btn btn-primary">Сохранить</button>
        </div>
        <!--Навесы-->
        <div role="tabpanel" class="tab-pane" id="Group4">
            <button onclick="NavesAdd()" class="btn btn-primary">Добавить</button>
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th></th>
                        <th>Наименование</th>
                        <th colspan="2">Высота</th>
                        <th colspan="2">Ширина</th>
                        <th>Створка</th>
                        <th>Сумма</th>
                        <th style="width: 100px;"></th>
                    </tr>
                </thead>
                <tbody id="NavesTable">
                <?php
                    $d=$m->query("SELECT * FROM TempCalcNaves ORDER BY Name");
                    if($d->num_rows>0)
                        while($r=$d->fetch_assoc())
                        {
                          ?>
                            <tr idNaves="<?php echo $r["id"]; ?>">
                                <td><span onclick="NavesRemove(this)" class='glyphicon glyphicon-remove'></span></td>
                                <td Type="Name"><input oninput="DoorSizeEdit(this)" value="<?php echo $r["Name"]; ?>" </td>
                                <td Type="HWith">с <input style="width: 50px;" oninput="DoorSizeEdit(this)" value="<?php echo $r["HWith"]; ?>"></td>
                                <td Type="HBy">до <input style="width: 50px;" oninput="DoorSizeEdit(this)" value="<?php echo $r["HBy"]; ?>"></td>
                                <td Type="WWith">с <input style="width: 50px;" oninput="DoorSizeEdit(this)" value="<?php echo $r["WWith"]; ?>"></td>
                                <td Type="WBy">до <input style="width: 50px;" oninput="DoorSizeEdit(this)" value="<?php echo $r["WBy"]; ?>"></td>
                                <td Type="SEqual">
                                    <select onchange="DoorSizeEdit(this)">
                                        <option></option>
                                        <option <?php if($r["SEqual"]==0 & $r["SEqual"]!=null) echo "selected"; ?> >Одностворчатая</option>
                                        <option <?php if($r["SEqual"]==1) echo "selected"; ?> >Двухстворчатая</option>
                                    </select>
                                </td>
                                <td Type="Sum"><input oninput="DoorSizeEdit(this)" value="<?php echo $r["Sum"]; ?>"></td>
                                <td Type=BtnSave><button onclick="NavesSave(this)" class="btn btn-primary">Сохранить</button></td>
                            </tr>
                        <?php
                        };
                ?>
                </tbody>
            </table>
        </div>
        <!--Фурнитура-->
        <div role="tabpanel" class="tab-pane" id="Group5">
            <button onclick="FurnitureAdd()" class="btn btn-primary">Добавить</button>
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th></th>
                        <th>Наименование</th>
                        <th>Валюта</th>
                        <th>Сумма</th>
                        <th style="width: 100px;"></th>
                    </tr>
                </thead>
                <tbody id="FurnitureTable">
                <?php
                    $d=$m->query("SELECT * FROM TempCalcFurnitura ORDER BY Name");
                    if($d->num_rows>0)
                        while($r=$d->fetch_assoc())
                        {
                            ?>
                            <tr idFurniture="<?php echo $r["id"]; ?>">
                                <td><span onclick="FurnituraRemove(this)" class='glyphicon glyphicon-remove-circle'></span></td>
                                <td Type=Name><input oninput='DoorSizeEdit(this)' value="<?php echo $r["Name"]; ?>"></td>
                                <td Type=Currency><select onchange='DoorSizeEdit(this)'><option <?php echo $r["Currency"]=="" ? "selected" : "" ?>></option><option <?php echo $r["Currency"]=="RUB" ? "selected" : "" ?>>RUB</option><option <?php echo $r["Currency"]=="EUR" ? "selected" : "" ?>>EUR</option><option <?php echo $r["Currency"]=="USD" ? "selected" : "" ?>>USD</option></select></td>
                                <td Type=Sum><input oninput='DoorSizeEdit(this)' value="<?php echo $r["Sum"]; ?>"></td>
                                <td Type=BtnSave><button onclick='FurnitureSave(this)' class='btn btn-primary'>Сохранить</button></td>
                            </tr>
                            <?php
                        };
                ?>
                </tbody>
            </table>
        </div>
        <!--Остекление-->
        <div role="tabpanel" class="tab-pane" id="Group6">
            <button onclick="GlassAdd()" class="btn btn-primary">Добавить</button>
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <td></td>
                        <th>Наименование</th>
                        <th>м<sup>2</sup></th>
                        <th>Сумма</th>
                        <th>Доп. сумма</th>
                        <th style="width: 200px;"></th>
                    </tr>
                </thead>
                <tbody id="GlassTable">
                <?php
                    $d=$m->query("SELECT * FROM TempCalcGlass ORDER BY Name");
                    if($d->num_rows>0)
                        while($r=$d->fetch_assoc())
                        { ?>
                            <tr idGlass="<?php echo $r["id"]; ?>">
                                <td><span onclick="GlassRemove(this)" class='glyphicon glyphicon-remove-circle'></span></td>
                                <td Type=Name><input oninput='DoorSizeEdit(this)' value="<?php echo $r["Name"]; ?>"></td>
                                <td Type=M2><input type="checkbox" <?php echo $r["M2"]==1 ? "checked" : ""; ?>></td>
                                <td Type=Sum><input oninput='DoorSizeEdit(this)' value="<?php echo $r["Sum"]; ?>"></td>
                                <td Type=SumPlus><input oninput='DoorSizeEdit(this)' value="<?php echo $r["SumPlus"]; ?>"></td>
                                <td Type=BtnSave><button onclick='GlassSave(this)' class='btn btn-primary'>Сохранить</button></td>
                            </tr>
                        <?php };
                ?>
                </tbody>
            </table>
        </div>
        <!--Дополнительно-->
        <div role="tabpanel" class="tab-pane" id="Group7">
            <button onclick="OtherAdd()" class="btn btn-primary">Добавить</button>
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Наименование</th>
                        <th>Сумма</th>
                        <th style="width: 200px;"></th>
                    </tr>
                </thead>
                <tbody id="OtherTable">
                <?php
                    $d=$m->query("SELECT * FROM TempCalcOther ORDER BY Name");
                    if($d->num_rows>0)
                        while($r=$d->fetch_assoc())
                        { ?>
                            <tr idOther="<?php echo $r["id"]; ?>">
                                <td><span onclick="OtherRemove(this)" class='glyphicon glyphicon-remove-circle'></span></td>
                                <td Type="Name"><input oninput="DoorSizeEdit(this)" value="<?php echo $r["Name"]; ?>"></td>
                                <td Type="Sum"><input oninput="DoorSizeEdit(this)" value="<?php echo $r["Sum"]; ?>"></td>
                                <td Type=BtnSave><button onclick='OtherSave(this)' class='btn btn-primary'>Сохранить</button></td>
                            </tr>
                        <?php }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
    function ConstructTypeDoorList($DefalutTypeDoor="", $gl){
        $Ret="<select onchange='DoorSizeEdit(this)'>";
        foreach ($gl->TypeDoor as $item)
            if($item==$DefalutTypeDoor)
            {
                $Ret.="<option selected>".$item."</option>";
            }
            else
                $Ret.="<option>".$item."</option>";
        return $Ret."</select>";
    }
    function ConstructSignBtn($TypeBtn){
        $Ret="<button onclick='DoorSizeSignCh(this)'>...</button>";
        switch ($TypeBtn)
        {
            case null: $Ret="<button onclick='DoorSizeSignCh(this)'>...</button>"; break;
            case 0: $Ret="<button onclick='DoorSizeSignCh(this)'>></button>"; break;
            case 1: $Ret="<button onclick='DoorSizeSignCh(this)'><</button>"; break;
            case 2: $Ret="<button onclick='DoorSizeSignCh(this)'>=</button>"; break;
        };
        return $Ret;
    }
    function ConstructSignEqualBtn($TypeBtn){
        $Sign="...";
        switch ($TypeBtn)
        {
            case null: $Sign="..."; break;
            case 0: $Sign="и"; break;
            case 1: $Sign="или"; break;
        };
        return "<button onclick='DoorSizeSignEqual(this)'>".$Sign."</button>";
    }
?>

<script>
    $(document).ready(function(){
       $("#DoorSizeTable td[Type=BtnSave] button").hide();
        $("#RALTable td[Type=BtnSave] button").hide();
        $("#NavesTable td[Type=BtnSave] button").hide();
        $("#FurnitureTable td[Type=BtnSave] button").hide();
        $("#GlassTable td[Type=BtnSave] button").hide();
        $("#OtherTable td[Type=BtnSave] button").hide();
    });


    var TypeDoorArr=new Array(<?php foreach ($gl->TypeDoor as $TypeDoor) echo "'".$TypeDoor."', "; echo "''"?>);

    function ConstructTypeDoorList(DefaultValue=""){
        var Ret="<select onchange='DoorSizeEdit(this)'>";
        for(var i=0; i<TypeDoorArr.length;i++)
            if(TypeDoorArr[i]==DefaultValue)
            {
                Ret=Ret+"<option selected>"+TypeDoorArr[i]+"</option>";
            }
            else
                Ret=Ret+"<option>"+TypeDoorArr[i]+"</option>";
        Ret=Ret+"</select>";
        return Ret;
    }
    function DoorSizeEdit(el) {
        $(el).parent().parent().find("td[Type=BtnSave] button").show();
    }
    function DoorSizeSignCh(el) {
        switch ($(el).text())
        {
            case "...": $(el).text(">"); break;
            case ">": $(el).text("<"); break;
            case "<": $(el).text("="); break;
            case "=": $(el).text("..."); break;
        };
        DoorSizeEdit(el);
    }
    function DoorSizeSignEqual(el) {
        switch ($(el).text()){
            case "...": $(el).text("и"); break;
            case "и": $(el).text("или"); break;
            case "или": $(el).text("..."); break;
        };
        DoorSizeEdit(el);
    }
    function DoorSizeAdd() {
        $("#DoorSizeTable").append(
            "<tr idDoorSize=''>"+
                "<td><span onclick='DoorSizeRemove(this)' class='glyphicon glyphicon-remove'></span></td>"+
                "<td Type='TypeDoor'>"+ConstructTypeDoorList()+"</td>"+
                "<td Type=HWith>с <input style='width: 50px;' oninput='DoorSizeEdit(this)'></td>"+
                "<td Type=HBy>до <input style='width: 50px;' oninput='DoorSizeEdit(this)'></td>"+
                "<td Type=WWith>с <input style='width: 50px;' oninput='DoorSizeEdit(this)'></td>"+
                "<td Type=WBy>до <input style='width: 50px;' oninput='DoorSizeEdit(this)'></td>"+
                "<td Type=SEqual><select onchange='DoorSizeEdit(this)'><option></option><option>Одностворчатая</option><option>Двухстворчатая</option></select>"+
                "<td Type=Framug><input type='checkbox' onchange='DoorSizeEdit(this)'></td> "+
                "<td Type=M2><input type='checkbox' onchange='DoorSizeEdit(this)'></td> "+
                "<td Type=Sum><input oninput='DoorSizeEdit(this)'></td> "+
                "<td Type=BtnSave><button onclick='DoorSizeSave(this)' class='btn btn-primary'>Сохранить</button></td>"+
            "</tr>"
        );
    }
    function DoorSizeSave(el) {
        var TR=$(el).parent().parent();
        var id=TR.attr("idDoorSize");
        var TypeDoor=TR.find("td[Type=TypeDoor] select").val();
        var HWith=TR.find("td[Type=HWith] input").val();
        var HBy=TR.find("td[Type=HBy] input").val();
        var WWith=TR.find("td[Type=WWith] input").val();
        var WBy=TR.find("td[Type=WBy] input").val();
        var SEqual=TR.find("td[Type=SEqual] select").val();
        var Framug=TR.find("td[Type=Framug] input").prop("checked");
        var M2=TR.find("td[Type=M2] input").prop("checked");
        var Sum=TR.find("td[Type=Sum] input").val();
        $.post(
            "Settings/CalcViewSave.php",
            {
                "Method":"AddEdit",
                "idDoorSize":id,
                "TypeDoor":TypeDoor,
                "HWith":HWith,
                "HBy":HBy,
                "WWith":WWith,
                "WBy":WBy,
                "SEqual":SEqual,
                "Framug":Framug,
                "M2":M2,
                "Sum":Sum
            },
            function(data){
                try {
                var o = jQuery.parseJSON(data);
                if(o.Result=="ok")
                {
                    $(el).hide();
                    if(id=="") TR.attr("idDoorSize",o.idDoorSize);
                };
                }
                catch(err) {console.error("Произошла ошибка сохранения: "+data);};
            }
        );
    }
    function DoorSizeRemove(el){
        if(confirm("Удалить параметр?")) {
            var TR = $(el).parent().parent();
            if (TR.attr("idDoorSize") == "") {
                TR.remove();
            }
            else
                $.post(
                    "Settings/CalcViewSave.php",
                    {"Method": "Remove", "idDoorSize": TR.attr("idDoorSize")},
                    function (data) {
                        try {
                            var o = jQuery.parseJSON(data);
                            if (o.Result == "ok")
                                TR.remove();
                        }
                        catch (err) {
                            console.error("Произошла ошибка сохранения: " + data);
                        };
                    }
                );
        };
    }

    //----------------RAL---------------------
    function RALAdd(){
        $("#RALTable").append(
            "<tr idRal=''>"+
                "<td><span onclick='RalRemove(this)' class='glyphicon glyphicon-remove-circle'></span> </td>"+
                "<td Type=Name><input oninput='DoorSizeEdit(this)'></td>"+
                "<td Type=Percent><input oninput='DoorSizeEdit(this)'></td>"+
                "<td Type=BtnSave><button onclick='RALSave(this)' class='btn btn-primary'>Сохранить</button></td>"+
            "</tr>"
        );
    }
    function RALEdit(el){
        $(el).parent().parent().find("td[Type=BtnSave] button").show();
    }
    function RALSave(el){
        var TR=$(el).parent().parent();
        $.post(
            "Settings/CalcViewRal.php",
            {
                "Method":TR.attr("idRal")=="" ? "Add" : "Edit",
                "idRal":TR.attr("idRal"),
                "Name":TR.find("td[Type=Name] input").val(),
                "Percent":TR.find("td[Type=Percent] input").val()
            },
            function (data) {
                try
                {
                    var o=jQuery.parseJSON(data);
                    if(o.Result=="ok")
                    {
                        $(el).hide();
                        if(TR.attr("idRal")=="") TR.attr("idRal",o.idRal);
                    };
                }
                catch (ex) {console.error("Ошибка сохранения: "+data);};
            }
        );
    }
    function RalRemove(el) {
        if(confirm("Удалить окрас"))
            if($(el).parent().parent().attr("idRal")!="")
                $.post(
                    "Settings/CalcViewRal.php",
                    {"Method":"Remove", "idRal":$(el).parent().parent().attr("idRal")},
                    function (data) {
                        if(data=="ok"){
                            $(el).parent().parent().remove();
                        }
                        else
                            console.error("Ошибка удаления "+data);
                    }
                )
            else
                $(el).parent().parent().remove();
    }

    function RALFind(el){
        var TRLen=$("#RALTable tr").length;
        for(var i=0;i<TRLen;i++) {
            var TR=$("#RALTable tr:eq(" + i + ")");
            var TDName=TR.find("td[Type=Name] input");
            if (TDName.val().toLowerCase().indexOf($(el).val().toLowerCase()) > -1) {
                TR.show();
            }
            else
                TR.hide();
        };
    }
    //-----------------Доводик------------------
    function DovodSave() {
        $.post(
            "Settings/CalcViewDovod.php",
            {
                "DovodNo":$("#DovodNo").val(),
                "DovodPodgotovka":$("#DovodPodgotovka").val(),
                "DovodYes":$("#DovodYes").val()
            },
            function (data) {
                if(data!="ok") console.error("Ошибка сохранения "+data);
            }
        )
    }

    //-------------------Навесы-------------------
    function NavesAdd() {
        $("#NavesTable").append(
            "<tr idNaves=''>"+
                "<td><span onclick='NavesRemove(this)' class='glyphicon glyphicon-remove-circle'></span> </td>"+
                "<td Type=Name><input oninput='DoorSizeEdit(this)'></td>"+
                "<td Type='HWith'>с <input style='width: 50px;' oninput='DoorSizeEdit(this)' ></td>"+
                "<td Type='HBy'>до <input style='width: 50px;' oninput='DoorSizeEdit(this)' ></td>"+
                "<td Type='WWith'>с <input style='width: 50px;' oninput='DoorSizeEdit(this)' ></td>"+
                "<td Type='WBy'>до <input style='width: 50px;' oninput='DoorSizeEdit(this)' ></td>"+
                "<td Type=SEqual><select><option onchange='DoorSizeEdit(this)'></option><option>Одностворчатая</option><option>Двухстворчатая</option></select></td>"+
                "<td Type=Sum><input oninput='DoorSizeEdit(this)'></td>"+
                "<td Type=BtnSave><button onclick='NavesSave(this)' class='btn btn-primary'>Сохранить</button></td>"+
            "</tr>"
        );
    }
    function NavesSave(el) {
        var TR=$(el).parent().parent();
        $.post(
            "Settings/CalcViewNaves.php",
            {
                "Method": TR.attr("idNaves")=="" ? "Add" : "Edit",
                "idNaves":TR.attr("idNaves"),
                "Name":TR.find("td[Type=Name] input").val(),
                "HWith":TR.find("td[Type=HWith] input").val(),
                "HBy":TR.find("td[Type=HBy] input").val(),
                "WWith":TR.find("td[Type=WWith] input").val(),
                "WBy":TR.find("td[Type=WBy] input").val(),
                "SEqual":TR.find("td[Type=SEqual] select").val(),
                "Sum":TR.find("td[Type=Sum] input").val()
            },
            function(data){
                try
                {
                    var o=jQuery.parseJSON(data);
                    if(o.Result=="ok")
                    {
                        $(el).hide();
                        if(TR.attr("idNaves")=="") TR.attr("idNaves",o.idNaves);
                    }
                }
                catch (ex) {console.error("Произошла ошибка: "+data)};
            }
        );
    }
    function NavesRemove(el) {
        if(confirm("Удалить навес?"))
            if($(el).parent().parent().attr("idNaves")!="")
                $.post(
                    "Settings/CalcViewNaves.php",
                    {"Method":"Remove", "idNaves":$(el).parent().parent().attr("idNaves")},
                    function (data) {
                        if(data=="ok"){
                            $(el).parent().parent().remove();
                        }
                        else
                            console.error("Ошибка удаления "+data);
                    }
                )
            else
                $(el).parent().parent().remove();
    }
    //------------------Фурнитура-------------------
    function FurnitureAdd() {
        $("#FurnitureTable").append(
            "<tr idFurniture=''>"+
                "<td><span onclick='FurnituraRemove(this)' class='glyphicon glyphicon-remove-circle'></span></td>"+
                "<td Type=Name><input oninput='DoorSizeEdit(this)'></td>"+
                "<td Type=Currency><select onchange='DoorSizeEdit(this)'><option></option><option>RUB</option><option>EUR</option><option>USD</option></select></td>"+
                "<td Type=Sum><input oninput='DoorSizeEdit(this)'></td>"+
                "<td Type=BtnSave><button onclick='FurnitureSave(this)' class='btn btn-primary'>Сохранить</button></td>"+
            "</tr>"
        );
    }
    function FurnitureSave(el) {
        var TR=$(el).parent().parent();
        $.post(
            "Settings/CalcViewFurnitura.php",
            {
                "Method": TR.attr("idFurniture")=="" ? "Add" : "Edit",
                "idFurniture":TR.attr("idFurniture"),
                "Name":TR.find("td[Type=Name] input").val(),
                "Currency":TR.find("td[Type=Currency] select").val(),
                "Sum":TR.find("td[Type=Sum] input").val()
            },
            function(data){
                try
                {
                    var o=jQuery.parseJSON(data);
                    if(o.Result=="ok")
                    {
                        $(el).hide();
                        if(TR.attr("idFurniture")=="") TR.attr("idFurniture",o.idFurniture);
                    }
                }
                catch (ex) {console.error("Произошла ошибка: "+data)};
            }
        );
    }
    function FurnituraRemove(el){
        var TR=$(el).parent().parent();
        if(confirm("Удалить фурнитуру: "+TR.find("td[Type=Name] input").val()))
            $.post(
                "Settings/CalcViewFurnitura.php",
                {"Method":"Remove", "idFurniture":TR.attr("idFurniture")},
                function (data) {
                    try
                    {
                        var o=jQuery.parseJSON(data);
                        if(o.Result=="ok") TR.remove();
                    }
                    catch (ex) {console.error("Произошла ошибка: "+data)};
                }
            );
    }
    //-------------------Остекление----------------
    function GlassAdd() {
        $("#GlassTable").append(
            "<tr idGlass=''>"+
                "<td><span onclick='GlassRemove(this)' class='glyphicon glyphicon-remove-circle'></span></td>"+
                "<td Type=Name><input oninput='DoorSizeEdit(this)'></td>"+
                "<td Type=M2><input type='checkbox' onchange='DoorSizeEdit(this)'></td>"+
                "<td Type=Sum><input oninput='DoorSizeEdit(this)'></td>"+
                "<td Type=SumPlus><input oninput='DoorSizeEdit(this)'></td>"+
                "<td Type=BtnSave><button onclick='GlassSave(this)' class='btn btn-primary'>Сохранить</button></td>"+
            "</tr>"
        );
    }
    function GlassSave(el) {
        var TR=$(el).parent().parent();
        $.post(
            "Settings/CalcViewGlass.php",
            {
                "Method": TR.attr("idGlass")=="" ? "Add" : "Edit",
                "idGlass":TR.attr("idGlass"),
                "Name":TR.find("td[Type=Name] input").val(),
                "M2":TR.find("td[Type=M2] input").prop("checked"),
                "Sum":TR.find("td[Type=Sum] input").val(),
                "SumPlus":TR.find("td[Type=SumPlus] input").val()
            },
            function(data){
                try
                {
                    var o=jQuery.parseJSON(data);
                    if(o.Result=="ok")
                    {
                        $(el).hide();
                        if(TR.attr("idGlass")=="") TR.attr("idGlass",o.idGlass);
                    }
                }
                catch (ex) {console.error("Произошла ошибка: "+data)};
            }
        );
    }
    function GlassRemove(el){
        var TR=$(el).parent().parent();
        if(confirm("Удалить фурнитуру: "+TR.find("td[Type=Name] input").val()))
            $.post(
                "Settings/CalcViewGlass.php",
                {"Method":"Remove", "idGlass":TR.attr("idGlass")},
                function (data) {
                    try
                    {
                        var o=jQuery.parseJSON(data);
                        if(o.Result=="ok") TR.remove();
                    }
                    catch (ex) {console.error("Произошла ошибка: "+data)};
                }
            );
    }
    //-------------------Дополнительно----------------
    function OtherAdd() {
        $("#OtherTable").append(
            "<tr idOther=''>"+
                "<td><span onclick='OtherRemove(this)' class='glyphicon glyphicon-remove-circle'></span></td>"+
                "<td Type=Name><input oninput='DoorSizeEdit(this)'></td>"+
                "<td Type=Sum><input oninput='DoorSizeEdit(this)'></td>"+
                "<td Type=BtnSave><button onclick='OtherSave(this)' class='btn btn-primary'>Сохранить</button></td>"+
            "</tr>"
        );
    }
    function OtherSave(el) {
        var TR=$(el).parent().parent();
        $.post(
            "Settings/CalcViewOther.php",
            {
                "Method": TR.attr("idOther")=="" ? "Add" : "Edit",
                "idOther":TR.attr("idOther"),
                "Name":TR.find("td[Type=Name] input").val(),
                "Sum":TR.find("td[Type=Sum] input").val()
            },
            function(data){
                try
                {
                    var o=jQuery.parseJSON(data);
                    if(o.Result=="ok")
                    {
                        $(el).hide();
                        if(TR.attr("idOther")=="") TR.attr("idOther",o.idGlass);
                    }
                }
                catch (ex) {console.error("Произошла ошибка: "+data)};
            }
        );
    }
    function OtherRemove(el){
        var TR=$(el).parent().parent();
        if(confirm("Удалить фурнитуру: "+TR.find("td[Type=Name] input").val()))
            $.post(
                "Settings/CalcViewOther.php",
                {"Method":"Remove", "idOther":TR.attr("idOther")},
                function (data) {
                    try
                    {
                        var o=jQuery.parseJSON(data);
                        if(o.Result=="ok") TR.remove();
                    }
                    catch (ex) {console.error("Произошла ошибка: "+data)};
                }
            );
    }
</script>

