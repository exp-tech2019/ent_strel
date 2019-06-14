<?php
$XMLParams=simplexml_load_file("../../params.xml");
$Host=$XMLParams->ConnectDB->Host;
$User=$XMLParams->ConnectDB->User;
$Pass=$XMLParams->ConnectDB->Pass;
$DateBase=$XMLParams->ConnectDB->DB;

$db=new PDO("mysql:host=$Host;dbname=$DateBase", $User, $Pass);

$TypeDoorCount=0;
$DolgnostCount=0;
?>
<div>
    <div style="float: left; margin-right: 20px">
        <div class="mlts_multiselect">
            <div class="mlts_selectBox" onclick="ReportWorkers_filter.ChDolgnost()">
                <select>
                    <option>Должность</option>
                </select>
                <div class="mlts_overSelect"></div>
            </div>
            <div id="ReportWorkers_FilterDolgnost" class="mlts_checkboxes">
                <?php
                $d=$db->query("SELECT Dolgnost FROM ManualDolgnost");
                if($d){
                    $i=0;
                    while($r=$d->fetch()){ ?>
                        <label for="ReportWorkers_FilterStep_<?php echo $r["Name"]; ?>">
                            <input onchange="ReportWorkers_filter.ChClick()" type="checkbox" id="ReportWorkers_FilterStep_<?php echo $i++; ?>"/>
                            <span onclick="ReportWorkers_filter.LbDolgnost(this)"><?php echo $r["Dolgnost"]; ?></span>
                        </label>
                    <?php };
                    $DolgnostCount=$i;
                    $d->closeCursor();
                }
                ?>
            </div>
        </div>
    </div>
    <div style="clear: none; display: none;">
        <div class="mlts_multiselect">
            <div class="mlts_selectBox" onclick="ReportWorkers_filter.ChTypeDoors()">
                <select>
                    <option>Двери</option>
                </select>
                <div class="mlts_overSelect"></div>
            </div>
            <div id="ReportWorkers_FilterTypeDoors" class="mlts_checkboxes">
                <?php
                $d=$db->query("SELECT * FROM ManualTypeDoors ORDER BY Name");
                $TypeDoorArr=array();
                if($d) {
                    while ($r = $d->fetch())
                        $TypeDoorArr[] = $r["Name"];
                    $d->closeCursor();
                };
                $TypeDoorArr[]="Рамка";
                $i=0;
                foreach ($TypeDoorArr as $TypeDoor){
                        ?>
                        <label for="ReportWorkers_FilterTypeDoor_<?php echo $i; ?>">
                            <input onchange="ReportWorkers_filter.ChClick()"  type="checkbox" checked id="ReportWorkers_FilterTypeDoor_<?php echo $i; ?>" />
                            <span><?php echo $TypeDoor; ?></span>
                        </label>
                    <?php
                        $i++;
                    };

                    $TypeDoorCount=$i+1;
                ?>
            </div>
        </div>
    </div>

    <button onclick="ReportWorkers_filter.FilterAllClear()" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">
        <span class="ui-button-text">Очистить фильтры</span>
    </button>

    <button onclick="ReportWorkers.Print()" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">
        <span class="ui-button-text">Печать</span>
    </button>
    <input type="checkbox" id="ReportWorkers_FilterWorkerFired">Отображать уволенных сотрудников
</div>
<table class="TablesHeight Tables">
    <thead>
        <tr class="BorderTablesThead">
            <th TypeCol="Num" TypeDoor="">№</th>
            <th TypeCol="FIO" TypeDoor="">ФИО</th>
            <th TypeCol="Dolgnost" TypeDoor="">Должность</th>
            <?php
                $d=$db->query("SELECT Name FROM ManualTypeDoors WHERE Name NOT LIKE '%Ворота%' AND Name NOT LIKE '%Люк%' ORDER BY Name");
            if($d)
                while($r=$d->fetch()){ ?>
                    <th TypeDoor="<?php echo $r["Name"]; ?>"><?php echo $r["Name"]; ?></th>
                <?php };
            ?>
            <th TypeCol="S_One" TypeDoor="" style="background-color: #f6f6f6">Одн.</th>
            <th TypeCol="S_Two" TypeDoor="">Двух.</th>
            <th TypeCol="Ворота" TypeDoor="" style="background-color: #f6f6f6">Ворота</th>
            <th TypeCol="Люк" TypeDoor="">Люк</th>
            <th TypeCol="All" TypeDoor="" style="background-color: #f6f6f6">Всего</th>
            <th TypeDoor="Рамка">Рамка</th>
            <th></th>
        </tr>
    </thead>
    <tbody id="ReportWorkers_Table"></tbody>
</table>
<?php
    $d=null;
    $db=null;
?>

<script>
    var ReportWorkers_filter={
        LbDolgnost:function (el) {
            var ch=$(el).parent().find("input");
            switch (ch.prop("checked")){
                case true: ch.prop("checked",false);
                    break;
                case false: ch.prop("checked",true);
                    break;
            };
            ReportWorkers_filter.ChClick();
        },
        ChDolgnost:function () {
            var checkboxes = document.getElementById("ReportWorkers_FilterDolgnost");
            if (checkboxes.style.display=="none") {
                checkboxes.style.display = "block";
            } else {
                checkboxes.style.display = "none";
            }
        },
        ChTypeDoors:function () {
            var checkboxes = document.getElementById("ReportWorkers_FilterTypeDoors");
            if (checkboxes.style.display=="none") {
                checkboxes.style.display = "block";
            } else {
                checkboxes.style.display = "none";
            }
        },
        ChClick:function () {
            var Steps=[];
            for(var i=0; i<<?php echo $DolgnostCount; ?>; i++)
                if($("#ReportWorkers_FilterStep_"+i).prop("checked"))
                    Steps.push($("#ReportWorkers_FilterStep_"+i).next().text());
            var Table=$("#ReportWorkers_Table");
            for(var i=0;i<Table.find("tr").length;i++){
                var TR=Table.find("tr:eq("+i+")");
                switch ( Steps.indexOf(TR.find("td[Type=Dolgnost]").text())>-1){
                    case true:
                        TR.show();
                        break;
                    case false:
                        TR.hide();
                        break;
                }
            }
            var TypeDoorsCh=[];
            for(var i=0; i<<?php echo $TypeDoorCount; ?>; i++)
                if ($("#ReportWorkers_FilterTypeDoor_" + i + "").prop("checked"))
                    TypeDoorsCh.push($("#ReportWorkers_FilterTypeDoor_" + i + "").next().text());
            var THead=Table.parent().find("thead tr");
            for(var i=0;i<THead.find("th").length;i++) {
                if(THead.find("th:eq(" + i + ")").attr("TypeDoor")!="")
                    switch (TypeDoorsCh.indexOf(THead.find("th:eq(" + i + ")").attr("TypeDoor")) > -1) {
                        case true:
                            THead.find("th:eq(" + i + ")").show();
                            for(var j=0;j<Table.find("tr").length;j++)
                                Table.find("tr:eq("+j+") td:eq("+i+")").show();
                            break;
                        case false:
                            THead.find("th:eq(" + i + ")").hide();
                            for(var j=0;j<Table.find("tr").length;j++)
                                Table.find("tr:eq("+j+") td:eq("+i+")").hide();
                            break;
                    };
            };
        },
        FilterAllClear:function () {
            $("#ReportWorkers_FilterDolgnost input").prop("checked",false);
            ReportWorkers_filter.ChClick();
        }
    };
    var ReportWorkers={
        DateWith:"",
        DateBy:"",
        Select:function (DateWith, DateBy) {
            $("#ReportWorkers_Table tr").remove();
            this.DateWith=DateWith;
            this.DateBy=DateBy;
            $.post(
                "/reportEnt/TabWorkers/function.php",
                {
                    DateWith:DateWith,
                    DateBy:DateBy,
                    WorkerFired:$("#ReportWorkers_FilterWorkerFired").prop("checked") ? 1 : 0
                },
                function(o){
                    for(var i in o) {
                        var str="<tr idWorker='"+o[i]["idWorker"]+"'>";
                        for (var tr in o[i])
                            if(tr!="idWorker")
                                str+="<td Type='"+tr+"' style='"+(tr=="S_One" || tr=="Ворота" || tr=="All" ? "background-color: #f6f6f6" : "")+"'>"+o[i][tr]+"</td>";
                        str+="</tr>";
                        $("#ReportWorkers_Table").append(str);
                    };
                    ReportWorkers_filter.ChClick();
                }
            )
        },
        Print:function () {
            var WorkersView=[];
            for(var i=0; i<<?php echo $DolgnostCount; ?>; i++)
                if($("#ReportWorkers_FilterStep_"+i).prop("checked"))
                    WorkersView.push($("#ReportWorkers_FilterStep_"+i).next().text());
            console.log(JSON.stringify(WorkersView));

            window.open("/ReportEnt/TabWorkers/function.php?DateWith="+this.DateWith+"&DateBy="+this.DateBy+"&WorkerFired="+($("#ReportWorkers_FilterWorkerFired").prop("checked")?1:0)+"&WorkersView="+JSON.stringify(WorkersView),"_blank");
        }
    }
</script>
