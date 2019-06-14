<?php
$idAct=-1;
$Referent=$_SESSION["AutorizeFIO"];
if((int)$_GET["idAct"]==-1)
{
    $d=$m->query("SELECT MAX(ActNum) AS ActNum FROM ActShpt");
    $r=$d->fetch_assoc();
    $ActNum=$r["ActNum"]==null ? 1 : (int)$r["ActNum"]+1;
    $m->query("INSERT INTO ActShpt (ActNum, ActDate, Referent, Status) VALUES($ActNum, Now(), '$Referent', 0)") or die($m->error);
    $idAct=$m->insert_id;
    $m->query("INSERT INTO ActShptHistory (idAct, Referent, DateChange, Action, TypeParent, idParent, Step) VALUES($idAct, '$Referent', Now(), 'CreateAct', 'Act', -1, -1)");
}
else
{
    $idAct=$_GET["idAct"];
    $m->query("DELETE FROM ActShptDoorTmp WHERE idAct=$idAct");
    $m->query("INSERT INTO actshptdoortmp (idAct, idNaryad) SELECT idAct, idNaryad FROM actshptdoor WHERE idAct=$idAct") or die($m->error);
};

$d=$m->query("SELECT *, DATE_FORMAT(ActDate, '%d.%m.%Y') AS ActDateStr, DATE_FORMAT(ShptDate,'%d.%m.%Y') AS ShptDateStr FROM ActShpt WHERE id=$idAct") or die($m->error);
$r=$d->fetch_assoc();
?>
<div class="row">
    <div class="col-lg-12">
        <button id="btnSave" onclick="Act.Save()" class="btn btn-sm btn-success">Сохранить</button>
        <a href="index.php?PageLoad=ActList" class="btn btn-sm btn-danger">Закрыть</a>
        <button onclick="$('#HistoryModal_dialog').modal('show');" class="btn btn-sm btn-default">История</button>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-lg-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li class="" onclick="$('#Panel1').css('position','static')"><a href="#tab_2-2" data-toggle="tab" aria-expanded="false">Продукция</a></li>
                <li class="active"><a href="#tab_1-1" data-toggle="tab" aria-expanded="false">Общее</a></li>
            </ul>
            <div class="tab-content">
                <input type="hidden" id="idAct" value="<?php echo $r["id"]; ?>">
                <div class="tab-pane active" id="tab_1-1">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-lg-2 control-label" for="ActNum">№ акта</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="ActNum" placeholder="№ акта" value="<?php echo $r["ActNum"]; ?>" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 control-label" for="ActCreateDate">Дата создания</label>
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" id="ActCreateDate" class="form-control" data-inputmask="'alias': 'dd.mm.yyyy'" data-mask="" disabled  value="<?php echo $r["ActDateStr"]; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 control-label" for="ActNum">Ответственный</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="Referent" placeholder="Ответственный" value="<?php echo $r["Referent"]; ?>" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 control-label" for="ActNum">Заказчик <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <input type="text" id="OrgName" value="<?php echo $r["OrgName"]; ?>" class="form-control" placeholder="Заказчик">
                                        <span onclick="OrgName.Open()" class="input-group-btn">
                                            <span type="button" class="btn btn-default">
                                                ...
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 control-label" for="ShptDate">Дата отгрузки <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" id="ShptDate" class="form-control" data-inputmask="'alias': 'dd.mm.yyyy'" data-mask=""  value="<?php echo $r["ShptDateStr"]; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-12 control-label" for="ActNum">Адрес:</label>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="col-lg-2 control-label" for="AdressState">Область</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="AdressState" placeholder="Область" value="<?php echo $r["AdressState"]; ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 control-label" for="AdressRaion">Район</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="AdressRaion" placeholder="Город" value="<?php echo $r["AdressRaion"]; ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 control-label" for="AdressCity">Город</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="AdressCity" placeholder="Город" value="<?php echo $r["AdressCity"]; ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 control-label" for="AdressStreet">Улица, дом</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="AdressStreet" placeholder="Улица, дом" value="<?php echo $r["AdressStreet"]; ?>">
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="col-lg-2 control-label" for="Fahrer">Водитель ФИО <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="Fahrer" placeholder="Водитель ФИО" value="<?php echo $r["Fahrer"]; ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 control-label" for="CarNum">Транспортное средство <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="CarNum" placeholder="Транспортное средство" value="<?php echo $r["CarNum"]; ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <textarea id="Note" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab_2-2" style="position: relative;">
                    <div class="panel form-group form-group-sm">
                        <label class="control-label">Отметка о выполнении: </label>
                        <select id="NaryadComplite_select" class="form-control input-sm" style="display: inline-block; width: 200px;">
                            <option value="0"></option>
                            <option value="3">Сварка</option>
                            <option value="5">Сборка</option>
                            <option value="6">Покраска</option>
                            <option value="7">Упаковка</option>
                            <option value="8">Погрузка</option>
                            <option value="11">Все</option>
                        </select>
                        <button id="NaryadComplite_btn" onclick="NaryadComplite()" class="btn btn-sm btn-default">ВЫполнить</button>
                    </div>
                    <div id="Panel1" class="panel" style=" position: static; width: 100%;  background-color: white; top: 0;">
                        <div class="btn-group">
                            <button onclick="DoorAction.ChAll(this)" type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i></button>
                            <button onclick="DoorAction.RemoveAll()" class="btn btn-sm btn-default">
                                <span class="fa fa-trash"></span>
                                Удалить
                            </button>
                        </div>
                        <div class="input-group margin" style="display: inline-block; width: 250px;">
                            <input type="text" id="DoorTable_Find" class="form-control input-sm" style="width: 200px; display: inline-block" placeholder="Поиск">
                            <span onclick="DoorAction.Select()" type="button" class="btn btn-sm btn-default">
                                <span class="fa fa-search"></span>
                            </span>
                        </div>
                        <div class="btn-group open">
                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Сортировать
                                <span class="fa fa-caret-down"></span></button>
                            <ul class="dropdown-menu">
                                <li><a noHref href="#" SortCol="Shet">Счет</a></li>
                                <li><a noHref href="#" SortCol="NumInDoor">№ двери</a></li>
                                <li><a noHref href="#" SortCol="NaryadNum">№ наряда</a></li>
                                <li class="divider"></li>
                                <li><a noHref href="#" SortCol="Name">Наименование</a></li>
                                <li><a noHref href="#" SortCol="Step">Этап</a></li>
                            </ul>
                        </div>
                        <div onclick="NaryadModal.Open()" class="btn btn-sm btn-default" style="margin-left: 20px;">
                            <span class="fa fa-plus"></span>
                            Добавить продукцию
                        </div>
                        <div class="btn-group" style="margin-left: 20px;">
                            <span>Дверей: </span>
                            <span id="DoorTable_count" class="text-bold"></span>
                        </div>
                    </div>
                    <table class="table table-responsive table-hover">
                        <thead>
                        <tr>
                            <th>
                            </th>
                            <th>Счет</th>
                            <th>№ двери</th>
                            <th>№ наряда</th>
                            <th>Наименование</th>
                            <th>Размеры</th>
                            <th>Этап</th>
                        </tr>
                        </thead>
                        <tbody id="DoorTable"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Диалог поиска заказчика -->
<div class="modal fade bd-example-modal-lg" id="OrgName_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Выбор заказчика</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="input-group margin">
                            <input type="text" id="OrgName_search" class="form-control" placeholder="Счет">
                            <span onclick="NaryadSelect.SelectNaryads()" class="input-group-btn">
                                <span type="button" class="btn btn-default">
                                    <span class="fa fa-search"></span>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <table class="table table-hover table-responsive table-fixed">
                        <thead>
                        <tr>
                            <th class="col-xs-12">Заказчик</th>
                        </tr>
                        </thead>
                        <tbody id="OrgName_Table">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button onclick="OrgName.Selected()" type="button" class="btn btn-primary">Выбрать</button>
            </div>
        </div>
    </div>
</div>

<!-- Диалог выбора нарядов -->
<div class="modal fade bd-example-modal-lg" id="NaryadModal_dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 90vw; max-width: 1024px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Выбор дверей</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="input-group margin">
                            <input type="text" id="NaryadModal_Shet" class="form-control input-sm" placeholder="Счет">
                            <span onclick="NaryadModal.Find()" class="input-group-btn">
                                <span type="button" class="btn btn-sm btn-default">
                                    <span class="fa fa-search"></span>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="input-group margin">
                            <span class="input-group-btn">
                                <span type="button" class="btn btn-sm btn-default">
                                    Этапы
                                </span>
                            </span>
                            <select id="NaryadModal_stepFilter" onchange="NaryadModal.Find()" class="form-control input-sm">
                                <option value="0">Упакованы или погружены</option>
                                <option value="7">Упакованы</option>
                                <option selected value="8">Погружены</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-fixed table-responsive table-hover table-bordered">
                            <thead>
                            <tr class="row">
                                <td class="col-xs-1">
                                    <input id="NaryadModal_checkbox" onchange="NaryadModal.Checked()" type="checkbox" class="checkbox">
                                </td>
                                <td class="col-xs-1">№ двери</td>
                                <td class="col-xs-1">№ наряда</td>
                                <td class="col-xs-2">Наименование</td>
                                <td class="col-xs-2">Размеры</td>
                                <td class="col-xs-1">Откр.</td>
                                <td class="col-xs-1">RAL</td>
                                <td class="col-xs-1">Этап</td>
                            </tr>
                            </thead>
                            <tbody id="NaryadModal_table"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button onclick="NaryadModal.Selected()" type="button" class="btn btn-primary">Добавить</button>
            </div>
        </div>
    </div>
</div>

<!--Диалог выбора сотрудника-->
<div class="modal fade bd-example-modal-sm" id="ModalWorkers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Отметка сотрудника</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="input-group margin">
                            <input type="text" oninput="Workers.Find()" id="ModalWorkerFind" class="form-control" placeholder="Счет">
                            <span onclick="Workers.Find()" class="input-group-btn">
                                <span type="button" class="btn btn-default">
                                    <span class="fa fa-search"></span>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-responsive table-hover">
                            <thead>
                            <tr>
                                <th>ФИО</th>
                                <th>Должность</th>
                            </tr>
                            </thead>
                            <tbody id="ModalWorkerTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button onclick="Workers.Selected()" type="button" class="btn btn-primary">Выбор</button>
            </div>
        </div>
    </div>
</div>


<script>
    $("#ActCreateDate").datepicker({ format: 'dd-mm-yy' });
    $("#ShptDate").datepicker({ format: 'dd.mm.yyyy' });
    $('.select2').select2();
    <?php
            if($r["Status"]==2 || $r["Status"]==-1){
                echo "$('#btnSave').hide(); ";
                echo "$('#btnComplite').hide(); ";
                echo "$('#btnCancel').hide(); ";
            }
    ?>

    $(document).keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13')
        {
            if($("#OrgName_modal").is(":visible"))
                OrgName.Selected();
        }
    });

    $("a[noHref]").click(function (e) {
        e.preventDefault();
        DoorAction.Select($(this).attr("SortCol"));
        return false;
    })

    //-- Позиционирование панели 1 на вкладке продукция -----
    var PositionPanel1=$("#Panel1").offset().top;
    setInterval(function () {
        let Panel1=$("#Panel1");
        if(Panel1.is(":visible") & Panel1.css("position")=="static") {
            PositionPanel1 = $("#Panel1").offset().top;
            console.info(PositionPanel1);
        };
    },500);
    $(window).scroll(function (event) {
        if($(this).scrollTop()>=PositionPanel1 || PositionPanel1<0)
            $("#Panel1").css("position","fixed")
        else
            $("#Panel1").css("position","static");
    });
    //-------------------------------------------------------

    var OrgName={
        Open:function () {
            $("#OrgName_Table tr").remove();
            $("#OrgName_search").val("");
            $("#OrgName_modal").modal("show");
        },
        SelectTR:function (elTR) {
            $("#OrgName_Table tr").removeClass("success");
            $(elTR).addClass("success");
        },
        Selected:function () {
            if($("#OrgName_Table tr[class=success]").length>0)
                $("#OrgName").val($("#OrgName_Table tr[class=success]").text());
            $("#OrgName_modal").modal("hide");
        }
    };
    $(document).on("input","#OrgName_search",function () {
        $("#OrgName_Table tr").remove();
        $.post(
            "RestAPI/SelectOrgList.php",
            {
                OrgName:$("#OrgName_search").val()
            },
            function (o) {
                o.forEach(function (org) {
                    $("#OrgName_Table").append(
                        "<tr onclick='OrgName.SelectTR(this)'><td class='col-xs-12'>"+org+"</td></tr>"
                    );
                    $("#OrgName_Table tr").dblclick(function () {
                        $(this).addClass("success");
                        OrgName.Selected();
                    })
                })
            }
        )
    });

    /*
    var NaryadSelect={
        Open:function () {
            $("#ModalNaryadSelect").modal("show");
            $("#ModalNaryadShet").val("");
            $("#ModalNaryadTable tr").remove();
            $("#SvarkaCompliteFlag").prop("checked",false);
            $("#SborkaMdfCompliteFlag").prop("checked",false);
            $("#SborkaCompliteFlag").prop("checked",false);
            $("#ColorCompliteFlag").prop("checked",false);
            $("#UpakCompliteFlag").prop("checked",false);
            $("#ShptCompliteFlag").prop("checked",false);
        },
        SelectNaryads:function () {
            $("#ModalNaryadTable tr").remove();
            $.post(
                "RestAPI/SelectNaryadList.php",
                {
                    Shet:$("#ModalNaryadShet").val()
                },
                function(o){
                    o.forEach(function(n){
                        var Step="";
                        var ColorStep="";
                        var flag=true;
                        Step=n.SvarkaCompliteFlag==1 ? "Сварка" : Step;
                        ColorStep=n.SvarkaCompliteFlag==1 ? "#00FF00" : ColorStep;
                        Step=n.SborkaMdfCompliteFlag==1 ? "Сборка МДФ" : Step;
                        ColorStep=n.SborkaMdfCompliteFlag==1 ? "#999900" : ColorStep;
                        Step=n.SborkaCompliteFlag==1 ? "Сборка" : Step;
                        ColorStep=n.SborkaCompliteFlag==1 ? "#FFFF00" : ColorStep;
                        Step=n.ColorCompliteFlag==1 ? "Покраска" : Step;
                        ColorStep=n.ColorCompliteFlag==1 ? "#FFA500" : ColorStep;
                        Step=n.UpakCompliteFlag==1 ? "Упаковка" : Step;
                        ColorStep=n.UpakCompliteFlag==1 ? "#FF6347" : ColorStep;
                        Step=n.ShptCompliteFlag==1 ? "Погрузка" : Step;
                        ColorStep=n.ShptCompliteFlag==1 ? "green" : ColorStep;
                        flag=$("#SvarkaCompliteFlag").is(":checked") & n.SvarkaCompliteFlag==0 ? false : flag;
                        flag=$("#SborkaMdfCompliteFlag").is(":checked") & n.SborkaMdfCompliteFlag==0 ? false : flag;
                        flag=$("#SborkaCompliteFlag").is(":checked") & n.SborkaCompliteFlag==0 ? false : flag;
                        flag=$("#ColorCompliteFlag").is(":checked") & n.ColorCompliteFlag==0 ? false : flag;
                        flag=$("#UpakCompliteFlag").is(":checked") & n.UpakCompliteFlag==0 ? false : flag;
                        flag=$("#ShptCompliteFlag").is(":checked") & n.ShptCompliteFlag==0 ? false : flag;
                        if(flag)
                            $("#ModalNaryadTable").append(
                                "<tr idNaryad="+n.idNaryad+" NaryadNum='"+n.NaryadNum+"'>" +
                                    "<td><input type='checkbox' </td>"+
                                    "<td Type=NumPP>"+n.NumPP+"</td>"+
                                    "<td Type=Name>"+n.name+"</td>"+
                                    "<td Type=DoorSize>"+n.DoorSize+"</td>"+
                                    "<td Type=Open>"+n.Open+"</td>"+
                                    "<td Type=RAL>"+n.RAL+"</td>"+
                                    "<td Type=Step style='background-color: "+ColorStep+"'>"+Step+"</td>"+
                                "</tr>"
                            );
                    })
                }
            )
        },
        CheckAll:function () {
            $("#ModalNaryadTable tr td input").prop("checked",true);
        },
        Add:function () {
            let idNaryadList=new Array();
            $("#ModalNaryadTable tr").each(function () {
                let tr=$(this);
                if(tr.find("td input").is(":checked"))
                    idNaryadList.push(tr.attr("idNaryad"));
            });
            console.log(idNaryadList);
            $.post(
                "RestAPI/AddTmpNaryad.php",
                {
                    idAct:$("#idAct").val(),
                    idNaryadList:idNaryadList
                },
                function (o) {
                    if (o.Status == "Success") {
                        $("#ModalNaryadTable tr").each(function () {
                            let tr=$(this);
                            if(tr.find("td input").is(":checked"))
                                $("#DoorTable").append(
                                    "<tr idNaryad="+tr.attr("idNaryad")+">" +
                                        "<td><input type=checkbox></td>"+
                                        "<td>"+tr.attr("NaryadNum")+"</td>"+
                                        "<td>"+$("#ModalNaryadShet").val()+"</td>"+
                                        "<td>"+tr.find("td[Type=Name]").text()+"</td>"+
                                        "<td>"+tr.find("td[Type=DoorSize]").text()+"</td>"+
                                        "<td Type=Step>"+tr.find("td[Type=Step]").text()+"</td>"+
                                    "</tr>"
                                );
                            $("#ModalNaryadSelect").modal("hide");
                        });

                    };
                }
            );

        }
    };

    var Workers={
        Open:function () {
            $("#ModalWorkerTable tr").attr("class","");
            $("#ModalWorkers").modal("show");
        },
        Load:function () {
            $("#ModalWorkerTable tr").remove();
            $.post(
                "RestAPI/WorkerSelect.php",
                {},
                function (o) {
                    o.forEach(function(w){
                        $("#ModalWorkerTable").append(
                            "<tr idWorker="+w.idWorker+" onclick='Workers.SelectTR(this)' style='cursor:pointer;'>" +
                                "<td Type=FIO>"+w.FIO+"</td>"+
                                "<td Type=Dolgnost>"+w.Dolgnost+"</td>"+
                            "</tr>"
                        );
                    });
                }
            );
        },
        Find:function () {
            let findStr=$("#ModalWorkerFind").val().toLowerCase();
            $("#ModalWorkerTable tr").each(function () {
                let tr=$(this);
                switch (tr.find("td[Type=FIO]").text().toLowerCase().indexOf(findStr)>-1 || tr.find("td[Type=Dolgnost]").text().toLowerCase().indexOf(findStr)>-1) {
                    case true:
                        tr.show();
                        break;
                    case false:
                        tr.hide();
                        break;
                }
            })
        },
        SelectTR:function (el) {

            $("#ModalWorkerTable tr").attr("class","");
            $(el).attr("class","success");
        },
        Selected:function(){
            if($("#ModalWorkerTable tr[class=success]").length>0)
            {
                let idWorker=$("#ModalWorkerTable tr[class=success]").attr("idWorker");
                let NaryadList=new Array();
                $("#DoorTable tr").each(function(){
                    if($(this).find("td input").is(":checked"))
                        NaryadList.push($(this).attr("idNaryad"));
                });
                if(NaryadList.length>0)
                    $.post(
                        "RestAPI/CompliteNaryadShpt.php",
                        {
                            idWorker:idWorker,
                            NaryadList:NaryadList
                        },
                        function (o) {
                            if(o.Status="Success") {
                                $("#ModalWorkers").modal("hide");
                                $("#DoorTable tr").each(function(){
                                    if($(this).find("td input").is(":checked"))
                                        $(this).find("td[Type=Step]").text("Погружено");
                                });
                            };
                        }
                    )
                else
                    $("#ModalWorkers").modal("hide");
            }
            else
                $("#ModalWorkers").modal("hide");
        }
    };
    */
    var DoorAction={
        Select:function(SortCol){
            $("#DoorTable tr").remove();
            $.post(
                "RestApi/SelectTmpNaryad.php",
                {
                    idAct:$("#idAct").val(),
                    Find:$("#DoorTable_Find").val(),
                    SortCol:SortCol==undefined ? "id" : SortCol
                },
                function (o) {
                    $("#DoorTable_count").text(o.DoorCount);
                    o.Doors.forEach(function (n) {
                        $("#DoorTable").append(
                            "<tr idTmp="+n.idTmp+" idNaryad="+n.idNaryad+">"+
                                "<td><input type='checkbox'></td>"+
                                "<td>"+n.Shet+"</td>"+
                                "<td>"+n.NumInOrder+"</td>"+
                                "<td>"+n.NaryadNum+"</td>"+
                                "<td>"+n.Name+"</td>"+
                                "<td>"+n.DoorSize+"</td>"+
                                "<td>"+n.Step+"</td>"+
                            "</tr>"
                        )
                    })
                }
            )
        },
        ChAll:function (btn) {
            switch ($(btn).find("i").hasClass("fa-square-o")){
                case true:
                    $(btn).find("i").removeClass("fa-square-o");
                    $(btn).find("i").addClass("fa-check-square-o");
                    $("#DoorTable input").prop("checked",true);
                    break;
                case false:
                    $(btn).find("i").removeClass("fa-check-square-o");
                    $(btn).find("i").addClass("fa-square-o");
                    $("#DoorTable input").prop("checked",false);
                    break;
            }
        },
        RemoveAll:function () {
            var arrTmp=new Array();
            let arrNaryad=new Array();
            $("#DoorTable tr").each(function(){
                if($(this).find("input").prop("checked")){
                    arrTmp.push($(this).attr("idTmp"));
                    arrNaryad.push($(this).attr("idNaryad"));
                }
            });
            HistoryAction.RemoveNaryad(arrNaryad);
            if(arrTmp.length>0)
                $.post(
                    "RestAPI/RemoveTmpNaryad.php",
                    {
                        idAct:$("#idAct").val(),
                        arrTmp:arrTmp
                    },
                    function (o) {
                        if(o.Status=="Success") {
                            $(btn).find("i").removeClass("fa-check-square-o");
                            $(btn).find("i").addClass("fa-square-o");
                            $("#DoorTable tr").each(function () {
                                if ($(this).find("input").prop("checked"))
                                    $(this).remove();
                            });
                        };
                    }
                );
        }
    };
    DoorAction.Select();

    let NaryadModal={
        Open:function () {
            $("#NaryadModal_table tr").remove();
            $("#NaryadModal_dialog").modal("show");
        },
        Find:function () {
            $("#NaryadModal_table tr").remove();
            let Shet=$("#NaryadModal_Shet").val();
            let Step=$("#NaryadModal_stepFilter").val();
            $.post(
                "RestAPI/SelectNaryadList.php",
                {
                    idAct:$("#idAct").val(),
                    Shet:Shet,
                    Step:Step
                },
                function (o) {
                    o.forEach(function (n) {
                        $("#NaryadModal_table").append(
                            "<tr class='row' idNaryad="+n.idNaryad+">" +
                                "<td class='col-xs-1 text-center'>"+
                                    "<input type='checkbox' class='checkbox' style='margin-left:10px'>"+
                                "</td>"+
                                "<td class='col-xs-1'>"+n.NumInOrder+"</td>"+
                                "<td class='col-xs-1'>"+n.NaryadNum+"</td>"+
                                "<td class='col-xs-2'>"+n.name+"</td>"+
                                "<td class='col-xs-2'>"+n.DoorSize+"</td>"+
                                "<td class='col-xs-1'>"+n.Open+"</td>"+
                                "<td class='col-xs-1'>"+n.RAL+"</td>"+
                                "<td class='col-xs-1'>"+n.Step+"</td>"+
                            "</tr>"
                        );
                    })
                }
            )
        },
        Checked:function () {
            let chProp=$("#NaryadModal_checkbox").prop("checked");
            $("#NaryadModal_table input").prop("checked", chProp);
        },
        Selected:function () {
            var arrNaryadList=new Array();
            $("#NaryadModal_table tr").each(function () {
                if($(this).find("input").prop("checked"))
                    arrNaryadList.push($(this).attr("idNaryad"));
            });
            HistoryAction.AddNaryad(arrNaryadList);
            $.post(
                "RestAPI/SaveTmpNaryad.php",
                {
                    idAct:$("#idAct").val(),
                    NaryadList:arrNaryadList
                },
                function (o) {
                    if(o.Status=="Success"){
                        $("#NaryadModal_dialog").modal("hide");
                        DoorAction.Select();
                    }
                }
            );
        }
    };

    var Act={
        Save:function (Status) {
            var flagErr=false;
            switch ($("#OrgName").val()==""){
                case true:
                    flagErr=true;
                    $("#OrgName").parent().parent().parent().removeClass("has-success");
                    $("#OrgName").parent().parent().parent().addClass("has-error");
                    break;
                case false:
                    $("#OrgName").parent().parent().parent().addClass("has-success");
                    $("#OrgName").parent().parent().parent().removeClass("has-error");
                    break;
            };
            switch ($("#ShptDate").val()==""){
                case true:
                    flagErr=true;
                    $("#ShptDate").parent().parent().parent().removeClass("has-success");
                    $("#ShptDate").parent().parent().parent().addClass("has-error");
                    break;
                case false:
                    $("#ShptDate").parent().parent().parent().addClass("has-success");
                    $("#ShptDate").parent().parent().parent().removeClass("has-error");
                    break;
            };
            switch ($("#Fahrer").val()==""){
                case true:
                    flagErr=true;
                    $("#Fahrer").parent().parent().removeClass("has-success");
                    $("#Fahrer").parent().parent().addClass("has-error");
                    break;
                case false:
                    $("#Fahrer").parent().parent().addClass("has-success");
                    $("#Fahrer").parent().parent().removeClass("has-error");
                    break;
            };
            switch ($("#CarNum").val()==""){
                case true:
                    flagErr=true;
                    $("#CarNum").parent().parent().removeClass("has-success");
                    $("#CarNum").parent().parent().addClass("has-error");
                    break;
                case false:
                    $("#CarNum").parent().parent().addClass("has-success");
                    $("#CarNum").parent().parent().removeClass("has-error");
                    break;
            };
            Status=Status==undefined ? 1 : Status;
            console.log(arrHistory);
            arrHistory.push({
                Action:"EditAct",
                TypeParent:"Act",
                idParent:-1,
                Step:-1
            });
            if(!flagErr)
                $.post(
                    "RestAPI/SaveAct.php",
                    {
                        idAct:$("#idAct").val(),
                        ShptDate:$("#ShptDate").val(),
                        AdressState:$("#AdressState").val(),
                        AdressCity:$("#AdressCity").val(),
                        AdressRaion:$("#AdressRaion").val(),
                        AdressStreet:$("#AdressStreet").val(),
                        OrgName:$("#OrgName").val(),
                        Fahrer:$("#Fahrer").val(),
                        CarNum:$("#CarNum").val(),
                        Note:$("#Note").val(),
                        Status:Status,
                        History:arrHistory
                    },
                    function (o) {
                        if(o.Status=="Success")
                            window.location="index.php?PageLoad=ActList";
                    }
                );
        }
    }
    /*
    Workers.Load();
    */
    function NaryadComplite() {
        $("#NaryadComplite_btn").text("Обработка");
        $("#NaryadComplite_btn").removeClass("btn-default");
        $("#NaryadComplite_btn").addClass("btn-warning");
        $.post(
            "RestAPI/NaryadComplite.php",
            {
                idAct:$("#idAct").val(),
                Step:$("#NaryadComplite_select").val()
            },
            function (o) {
                if(o.Status=="Success")
                {
                    let arrNaryad=new Array();
                    $("#DoorTable tr").each(function () {
                        arrNaryad.push($(this).attr("idNaryad"));
                    });
                    HistoryAction.Complite(arrNaryad, $("#NaryadComplite_select").val());
                    $("#NaryadComplite_btn").text("Выполненно");
                    $("#NaryadComplite_btn").removeClass("btn-warning");
                    $("#NaryadComplite_btn").addClass("btn-success");
                    setTimeout(function () {
                        $("#NaryadComplite_btn").text("Выполнить");
                        $("#NaryadComplite_btn").removeClass("btn-success");
                        $("#NaryadComplite_btn").addClass("btn-default");
                    },1000);
                };
            }
        )
    }

    function autocomplete(inp, arr) {
        /*the autocomplete function takes two arguments,
        the text field element and an array of possible autocompleted values:*/
        var currentFocus;
        /*execute a function when someone writes in the text field:*/
        inp.addEventListener("input", function(e) {
            var a, b, i, val = this.value;
            /*close any already open lists of autocompleted values*/
            closeAllLists();
            if (!val) { return false;}
            currentFocus = -1;
            /*create a DIV element that will contain the items (values):*/
            a = document.createElement("DIV");
            a.setAttribute("id", this.id + "autocomplete-list");
            a.setAttribute("class", "autocomplete-items");
            /*append the DIV element as a child of the autocomplete container:*/
            this.parentNode.appendChild(a);
            /*for each item in the array...*/
            for (i = 0; i < arr.length; i++) {
                /*check if the item starts with the same letters as the text field value:*/
                if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                    /*create a DIV element for each matching element:*/
                    b = document.createElement("DIV");
                    /*make the matching letters bold:*/
                    b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                    b.innerHTML += arr[i].substr(val.length);
                    /*insert a input field that will hold the current array item's value:*/
                    b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                    /*execute a function when someone clicks on the item value (DIV element):*/
                    b.addEventListener("click", function(e) {
                        /*insert the value for the autocomplete text field:*/
                        inp.value = this.getElementsByTagName("input")[0].value;
                        /*close the list of autocompleted values,
                        (or any other open lists of autocompleted values:*/
                        closeAllLists();
                    });
                    a.appendChild(b);
                }
            }
        });
        /*execute a function presses a key on the keyboard:*/
        inp.addEventListener("keydown", function(e) {
            var x = document.getElementById(this.id + "autocomplete-list");
            if (x) x = x.getElementsByTagName("div");
            if (e.keyCode == 40) {
                /*If the arrow DOWN key is pressed,
                increase the currentFocus variable:*/
                currentFocus++;
                /*and and make the current item more visible:*/
                addActive(x);
            } else if (e.keyCode == 38) { //up
                /*If the arrow UP key is pressed,
                decrease the currentFocus variable:*/
                currentFocus--;
                /*and and make the current item more visible:*/
                addActive(x);
            } else if (e.keyCode == 13) {
                /*If the ENTER key is pressed, prevent the form from being submitted,*/
                e.preventDefault();
                if (currentFocus > -1) {
                    /*and simulate a click on the "active" item:*/
                    if (x) x[currentFocus].click();
                }
            }
        });
        function addActive(x) {
            /*a function to classify an item as "active":*/
            if (!x) return false;
            /*start by removing the "active" class on all items:*/
            removeActive(x);
            if (currentFocus >= x.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = (x.length - 1);
            /*add class "autocomplete-active":*/
            x[currentFocus].classList.add("autocomplete-active");
        }
        function removeActive(x) {
            /*a function to remove the "active" class from all autocomplete items:*/
            for (var i = 0; i < x.length; i++) {
                x[i].classList.remove("autocomplete-active");
            }
        }
        function closeAllLists(elmnt) {
            /*close all autocomplete lists in the document,
            except the one passed as an argument:*/
            var x = document.getElementsByClassName("autocomplete-items");
            for (var i = 0; i < x.length; i++) {
                if (elmnt != x[i] && elmnt != inp) {
                    x[i].parentNode.removeChild(x[i]);
                }
            }
        }
        /*execute a function when someone clicks in the document:*/
        document.addEventListener("click", function (e) {
            closeAllLists(e.target);
        });
    }


    var States=[
        "Алтайский край",
        "Амурская область",
        "Архангельская область",
        "Астраханская область",
        "Белгородская область",
        "Брянская область",
        "Владимирская область",
        "Волгоградская область",
        "Вологодская область",
        "Воронежская область",
        "г. Москва",
        "Еврейская автономная область",
        "Забайкальский край",
        "Ивановская область",
        "Иные территории, включая город и космодром Байконур",
        "Иркутская область",
        "Кабардино-Балкарская Республика",
        "Калининградская область",
        "Калужская область",
        "Камчатский край",
        "Карачаево-Черкесская Республика",
        "Кемеровская область",
        "Кировская область",
        "Костромская область",
        "Краснодарский край",
        "Красноярский край",
        "Курганская область",
        "Курская область",
        "Ленинградская область",
        "Липецкая область",
        "Магаданская область",
        "Московская область",
        "Мурманская область",
        "Ненецкий автономный округ",
        "Нижегородская область",
        "Новгородская область",
        "Новосибирская область",
        "Омская область",
        "Оренбургская область",
        "Орловская область",
        "Пензенская область",
        "Пермский край",
        "Приморский край",
        "Псковская область",
        "Республика Адыгея (Адыгея)",
        "Республика Алтай",
        "Республика Башкортостан",
        "Республика Бурятия",
        "Республика Дагестан",
        "Республика Ингушетия",
        "Республика Калмыкия",
        "Республика Карелия",
        "Республика Коми",
        "Республика Крым",
        "Республика Марий Эл",
        "Республика Мордовия",
        "Республика Саха (Якутия)",
        "Республика Северная Осетия - Алания",
        "Республика Татарстан (Татарстан)",
        "Республика Тыва",
        "Республика Хакасия",
        "Ростовская область",
        "Рязанская область",
        "Самарская область",
        "Санкт-Петербург",
        "Саратовская область",
        "Сахалинская область",
        "Свердловская область",
        "Севастополь",
        "Смоленская область",
        "Ставропольский край",
        "Тамбовская область",
        "Тверская область",
        "Томская область",
        "Тульская область",
        "Тюменская область",
        "Удмуртская Республика",
        "Ульяновская область",
        "Хабаровский край",
        "Ханты-Мансийский автономный округ - Югра",
        "Челябинская область",
        "Чеченская Республика",
        "Чувашская Республика - Чувашия",
        "Чукотский автономный округ",
        "Ямало-Ненецкий автономный округ",
        "Ярославская область"
        ];

    /*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
    autocomplete(document.getElementById("AdressState"), States);

    var arrZakaz=[
        <?php
        $d=$m->query("SELECT zakaz FROM oreders GROUP BY zakaz ORDER BY zakaz ");
        while ($r=$d->fetch_assoc())
            echo "'".$r["zakaz"]."',";
        $d->close();
        ?>
    ];
    autocomplete(document.getElementById("OrgName"), arrZakaz);


    //------------- История ---------------------
    var arrHistory=new Array();
    var HistoryAction={
        AddNaryad:function(NaryadList){
            NaryadList.forEach(function (idNaryad) {
                arrHistory.push({
                    Action:"Add",
                    TypeParent:"Naryad",
                    idParent:idNaryad,
                    Step:-1
                })
            })
        },
        RemoveNaryad:function (NaryadList) {
            NaryadList.forEach(function (idNaryad) {
                arrHistory.push({
                    Action:"Remove",
                    TypeParent:"Naryad",
                    idParent:idNaryad,
                    Step:-1
                })
            })
        },
        Complite:function (NaryadList, Step) {
            NaryadList.forEach(function (idNaryad) {
                arrHistory.push({
                    Action:"NaryadComplite",
                    TypeParent:"Naryad",
                    idParent:idNaryad,
                    Step:Step
                })
            })
        }
    }
</script>

<?php
?>
<!--Диалог истории-->
<div class="modal fade bd-example-modal-lg" id="HistoryModal_dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">История изменений</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-responsive table-hover">
                            <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Ответственный</th>
                                <th>Действие</th>
                                <th>Описание</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $arrSteps=array(
                                3=>"Сварка",
                                5=>"Сборка",
                                6=>"Покраска",
                                7=>"Упаковка",
                                8=>"Погрузка"
                            );
                            $arrHistory=array();
                            $d=$m->query("SELECT * FROM actshpthistory WHERE idAct=$idAct");
                            while ($r=$d->fetch_assoc())
                                $arrHistory[]=array(
                                    "DateChange"=>$r["DateChange"],
                                    "Referent"=>$r["Referent"],
                                    "Action"=>$r["Action"],
                                    "idParent"=>$r["idParent"],
                                    "Step"=>$r["Step"],
                                    "StepStr"=>$r["Step"]==-1 ? "" : $arrSteps[$r["Step"]],
                                    "Note"=>""
                                );
                            $d->close();
                            $idNaryads="-1";
                            foreach ($arrHistory as $h)
                                $idNaryads.=", ".$h["idParent"];
                            $d=$m->query("SELECT n.id, o.Shet, od.name, n.NumInOrder, CONCAT(n.Num, n.NumPP) AS NaryadNum FROM oreders o, orderdoors od, naryad n WHERE o.id=od.idOrder AND od.id=n.idDoors AND n.id IN ($idNaryads)");
                            while ($r=$d->fetch_assoc())
                                foreach ($arrHistory as &$h)
                                    if($h["idParent"]!=-1 && $h["idParent"]==$r["id"])
                                        $h["Note"]="Счет ".$r["Shet"]." дверь ".$r["NumInOrder"]." наряд ".$r["NaryadNum"]." ".$r["name"].($h["Step"]!=-1 ? " этап ".$h["StepStr"] : "");
                            $arrActions=array(
                                "CreateAct"=>"Создание акта",
                                "EditAct"=>"Изменен акт",
                                "Add"=>"Добавлена дверь",
                                "Remove"=>"Удалена дверь",
                                "NaryadComplite"=>"Выполнен наряд"
                            );
                            foreach ($arrHistory as $h){ ?>
                                <tr>
                                    <td><?php echo $h["DateChange"]; ?></td>
                                    <td><?php echo $h["Referent"]; ?></td>
                                    <td><?php echo $arrActions[$h["Action"]]; ?></td>
                                    <td><?php echo $h["Note"]; ?></td>
                                </tr>
                            <?php };
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>