<script src="scripts/ClientOne.js"></script>
<section class="content-header">
    <h1>
        Организция
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Гдавная</a></li>
        <li class="active"><a href="#">Клиенты</a></li>
    </ol>
</section>

<?php
    $idClient=isset($_GET["idClient"]) ? $_GET["idClient"] : -1;
    $OrgName="";
    $FullOrgName="";
    $INN="";
    $KPP="";
    $OGRN="";
    $AdressLegal="";
    $AdressActual="";
    if($idClient!=-1){
        include "DBConnect.php";
        $d=$db->query("SELECT * FROM mn_Clients WHERE id=$idClient");
        $r=$d->fetch_assoc();
        $OrgName=$r["OrgName"];
        $FullOrgName=$r["FullOrgName"];
        $INN=$r["INN"];
        $KPP=$r["KPP"];
        $OGRN=$r["OGRN"];
        $AdressLegal=$r["AdressLegal"];
        $AdressActual=$r["AdressActual"];
    }
?>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <!--<form role="form">-->
                    <div class="box-header">
                        <button onclick="Client.Save()" class="btn btn-success">Сохранить</button>
                        <button onclick="window.location.href='?MVCPage=ClientList'" class="btn btn-primary">Отмена</button>
                        <button onclick="Client.Remove()" class="btn btn-danger" style="float: right;">Удалить</button>
                    </div>
                    <input type="hidden" id="idClient" value="<?php echo $idClient; ?>">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="OrgName">Организация</label>
                            <input class="form-control" id="OrgName" value="<?php echo $OrgName ?>" placeholder="Наименование организации" type="text">
                        </div>
                        <div class="form-group">
                            <label for="FullOrgName">Полное наименование</label>
                            <input class="form-control" id="FullOrgName" value="<?php echo $FullOrgName ?>" placeholder="Наименование организации" type="text">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="INN">ИНН</label>
                                <input class="form-control" id="INN" value="<?php echo $INN ?>" placeholder="Наименование ИНН" type="text">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="KPP">КПП</label>
                            <input class="form-control" id="KPP" value="<?php echo $KPP ?>" placeholder="Наименование КПП" type="text">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="OGRN">ОГРН</label>
                            <input class="form-control" id="OGRN" value="<?php echo $OGRN ?>" placeholder="Наименование ОГРН" type="text">
                        </div>
                        <div class="form-group">
                            <label for="AdressLegal">Адрес юридический</label>
                            <input class="form-control" id="AdressLegal" value="<?php echo $AdressLegal ?>" placeholder="Адрес юридический" type="text">
                        </div>
                        <div class="form-group">
                            <label for="AdressActual">Адрес фактический</label>
                            <input class="form-control" id="AdressActual" value="<?php echo $AdressActual ?>" placeholder="Адрес фактический" type="text">
                        </div>
                    </div>
                    <div class="box-footer">
                        <button onclick="Client.Save()" class="btn btn-success">Сохранить</button>
                        <button onclick="window.location.href='?MVCPage=ClientList'" class="btn btn-primary">Отмена</button>
                        <button onclick="Client.Remove()" class="btn btn-danger" style="float: right;">Удалить</button>
                    </div>
                <!--</form>-->
            </div>

        </div>
    </div>
</section>