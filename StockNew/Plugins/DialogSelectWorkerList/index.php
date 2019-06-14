<div class="modal fade" id="PluginWorkerList_Dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Сотрудники</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input id="PluginWorkerList_FindInp" oninput="PluginWorkerList_Find()" class="form-control" placeholder="Поиск">
                        <br>
                        <table class="table table-responsive table-hover">
                            <thead>
                                <tr>ФИО</tr>
                                <tr>Должность</tr>
                            </thead>
                            <tbody id="PluginWorkerList_Table">
                                <?php

                                    $XMLParams=simplexml_load_file("../params.xml");
                                    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);
                                    $d=$m->query("SELECT w.id, w.FIO, d.Dolgnost FROM Workers w, ManualDolgnost d WHERE w.DolgnostID=d.id ORDER BY w.FIO");
                                    while($r=$d->fetch_assoc()) {
                                        ?>
                                        <tr idWorker="<?php echo $r["id"] ?>" onclick="PluginWorkerList.SelectTR(this)">
                                            <td Type="FIO"><?php echo $r["FIO"]; ?></td>
                                            <td Type="Dolgnost"><?php echo $r["Dolgnost"]; ?></td>
                                        </tr>
                                        <?php
                                    };
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button onclick="PluginWorkerList.DialogSelected()" type="button" class="btn btn-primary">Выбрать</button>
            </div>
        </div>
    </div>
</div>
<script>
    var PluginWorkerList={
        idElement_WorkerID:"",
        idElement_WorkerFIO:"",
        OpenDialog:function(idWoker, FIOWorker){
            this.idElement_WorkerID=idWoker;
            this.idElement_WorkerFIO=FIOWorker;
            $("#PluginWorkerList_Dialog").modal("show");
        },
        SelectTR:function(el){
            $("#PluginWorkerList_Table tr").removeAttr("class");
            $(el).attr("class","success");
        },
        DialogSelected:function () {
            if($("#PluginWorkerList_Table tr[class=success]").length>0){
                var TR=$("#PluginWorkerList_Table tr[class=success]");
                $(this.idElement_WorkerID).val(TR.attr("idWorker"));
                $(this.idElement_WorkerFIO).val(TR.find("td[Type=FIO]").text());
            };
            $("#PluginWorkerList_Dialog").modal("hide");
        },
        DialogClose:function () {
            $("#PluginWorkerList_Dialog").modal("hide");
        }
    };

    function PluginWorkerList_Find() {
        var FindText=$("#PluginWorkerList_FindInp").val().toLowerCase();
        for(var i=0; i<$("#PluginWorkerList_Table tr").length; i++){
            var TR=$("#PluginWorkerList_Table tr:eq("+i+")");
            if(TR.text().toLowerCase().indexOf(FindText)>-1){
                TR.show();
            }else
                TR.hide();
        }
    }
</script>