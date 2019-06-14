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
                        <div class="panel-body" >
                            <div>
                                <div style="width: 200px; float: left;">werewr</div>
                                <div style="width: 200px; float: left;">werewr</div>
                            </div>
                        </div>
                    </div>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary">Сохарнить</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>

    function CalcStart(idDoor){
        var TR=$("#DoorTable tr[idDoor="+idDoor+"]");
        var Calc=TR.find("td[Type=Sum] calc");
        for(var i=0; i<Calc.find("element").length; i++)
            switch (Calc.find("element:eq(" + i + ")").attr("Type")) {
                case "DoorsSize":
                    $("#CalcDoorSize span[Type=H]").text(TR.find("td[Type=H] input").val());
                    $("#CalcDoorSize span[Type=W]").text(TR.find("td[Type=W] input").val());
                    $("#CalcDoorSize input").val(Calc.find("element:eq(" + i + ")").text());
                    break;
            };
        $('#DialogCalc').modal('show');
    };
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
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
    function RalAddStart(){
        $("#DialogCalcManualRal").modal("show");
    }
    function RalAddSelect(el) {
        var Sum=parseInt($(el).find("td[Type=Percent]").text())*(parseInt($("#CalcDoorSize input").val())/100);
        $("#CalcRal1").append("<td><td>3434</td></tr>");
        $("#CalcRal1").append(
            "<tr>"+
                "<td><span class='glyphicon glyphicon-remove'></span></td>"+
                "<td>"+$(el).find("td[Type=Name]").text()+"</td>"+
                "<td>"+Sum+"</td>"+
            "</tr>"
        );
        $("#DialogCalcManualRal").modal("hide");
    }
</script>