<?php
ini_set('MAX_EXECUTION_TIME', '-1');
?>
<div>
    <form method="post" action="Calcnull.php">
		<label for="InpWith">Дата создания заказов c</label>
        <input id="InpWith" name="With" value="<?php echo isset($_POST["With"]) ? $_POST["With"] : ""; ?>">
        <label for="InpBy">по</label>
        <input id="InpBy" name="By" value="<?php echo isset($_POST["By"]) ? $_POST["By"] : ""; ?>">
		<br>
		<label for="idOrder">id счета</label>
        <input id="idOrder" name="idOrder" value="<?php echo isset($_POST["idOrder"]) ? $_POST["idOrder"] : ""; ?>">
		<br>
        <input type="submit" value="Пересчитать">
    </form>
</div>

<?php
/**
 * Created by PhpStorm.
 * User: anikulshin
 * Date: 10.01.2019
 * Time: 11:45
 */

if(isset($_POST["With"]) & isset($_POST["By"]))
{
    
$With = $_POST["With"];
$By = $_POST["By"];
    
$XMLParams = simplexml_load_file("../../params.xml");
$m = new mysqli($XMLParams->ConnectDB->Host, $XMLParams->ConnectDB->User, $XMLParams->ConnectDB->Pass, $XMLParams->ConnectDB->DB);

$arrStepStr=array(
    "", "Лазер","Сгибка","Сварка","Рамка","Сборка","Покраска","Упаковка","Отгрузка","МДФ","Сборка МДФ"
);

$SQL="";
if($_POST["With"]!="" & $_POST["By"]!="")
	$SQL="SELECT o.Blank, DATE_FORMAT(o.BlankDate,'%d.%m.%Y') AS BlankDate, DATEDIFF(nc.DateComplite, o.BlankDate) AS DateDif, o.Shet, od.*, IF(od.S IS NOT NULL, 2, IF(od.SEqual=1, 2, 1)) AS SType, CONCAT(n.Num, n.NumPP) AS NaryadNum, nc.id AS idNC, nc.Cost, nc.Step, DATE_FORMAT(nc.DateComplite,'%d.%m.%Y') AS DateComplite, '' AS FIO, '' AS Dolgnost FROM oreders o, orderdoors od, naryad n, naryadcomplite nc WHERE o.id=od.idOrder AND od.id=n.idDoors AND n.id=nc.idNaryad AND nc.DateComplite IS NULL AND o.BlankDate BETWEEN STR_TO_DATE('$With','%d.%m.%Y') AND DATE_ADD(STR_TO_DATE('$By', '%d.%m.%Y'), INTERVAL 1 DAY)";
if($_POST["idOrder"]!=""){
	$idOrder=$_POST["idOrder"];
	$SQL="SELECT o.Blank, DATE_FORMAT(o.BlankDate,'%d.%m.%Y') AS BlankDate, DATEDIFF(nc.DateComplite, o.BlankDate) AS DateDif, o.Shet, od.*, IF(od.S IS NOT NULL, 2, IF(od.SEqual=1, 2, 1)) AS SType, CONCAT(n.Num, n.NumPP) AS NaryadNum, nc.id AS idNC, nc.Cost, nc.Step, DATE_FORMAT(nc.DateComplite,'%d.%m.%Y') AS DateComplite, '' AS FIO, '' AS Dolgnost FROM oreders o, orderdoors od, naryad n, naryadcomplite nc WHERE o.id=od.idOrder AND od.id=n.idDoors AND n.id=nc.idNaryad AND o.id=$idOrder ORDER BY nc.Step";
};
$d = $m->query($SQL) or die($m->error);

$arrDoor = array();
while ($r = $d->fetch_assoc())
    $arrDoor[] = array(
        "Blank" => $r["Blank"],
        "BlankDate" => $r["BlankDate"],
        "DateDif" => $r["DateDif"],
        "Shet" => $r["Shet"],

        "DoorType" => $r["name"],
        "H" => (int)$r["H"],
        "W" => (int)$r["W"],
        "S" => $r["S"],
        "SType"=>$r["SType"],
        "FramugaH"=>$r["FramugaH"],
        "SEqual" => $r["SEqual"],
        "Open" => $r["Open"],
        "Nalichnik" => $r["Nalichnik"],
        "Dovod" => $r["Dovod"],

        "WorkPetlya" => $r["WorkPetlya"],
        "WorkWindowCh" => (int)$r["WorkWindowCh"],
        "WorkWindowNoFrame" => $r["WorkWindowNoFrame"],
        "WorkWindowCh1"=>(int)$r["WorkWindowCh1"],
        "WorkWindowCh2"=>(int)$r["WorkWindowCh2"],
        "WorkUpGridCh"=>(int)$r["WorkUpGridCh"],
        "WorkDownGridCh"=>(int)$r["WorkDownGridCh"],

        "StvorkaPetlya" => $r["StvorkaPetlya"],
        "StvorkaWindowCh" => (int)$r["StvorkaWindowCh"],
        "StvorkaWindowNoFrame" => $r["StvorkaWindowNoFrame"],
        "StvorkaWindowCh1" => (int)$r["StvorkaWindowCh1"],
        "StvorkaWindowCh2" => (int)$r["StvorkaWindowCh2"],
        "StvorkaDownGridCh" => (int)$r["StvorkaDownGridCh"],
        "StvorkaUpGridCh" => (int)$r["StvorkaUpGridCh"],

        "FramugaCh" => (int)$r["FramugaCh"],
        "FramugaWindowCh" => (int)$r["FramugaWindowCh"],
        "FramugaWindowNoFrame" => $r["FramugaWindowNoFrame"],
        "FramugaUpGridCh" => $r["FramugaUpGridCh"],
        "FramugaDownGridCh" => $r["FramugaDownGridCh"],

        "Antipanik" => $r["Antipanik"],
        "Otboynik" => $r["Otboynik"],
        "name" => $r["name"],
        "Wicket" => $r["Wicket"],
        "BoxLock" => $r["BoxLock"],
        "Otvetka" => $r["Otvetka"],
        "Isolation" => $r["Isolation"],

        "idNC" => $r["idNC"],
        "Step" => $r["Step"],
        "StepStr"=>$arrStepStr[$r["Step"]],
        "Dolgnost" => $r["Dolgnost"],
        "FIO" => $r["FIO"],
        "NaryadNum" => $r["NaryadNum"],
        "CostOld" => $r["Cost"],
        "DateComplite" => $r["DateComplite"]
    );
$d->close();

$arrDoorSize = array();
$d = $m->query("SELECT * FROM payrolldoorsize_new");
if ($d->num_rows > 0)
    while ($r = $d->fetch_assoc()) {
        $HWith = $r["HWith"] == null ? "" : $r["HWith"];
        $HBy = $r["HBy"] == null ? "" : $r["HBy"];

        $WWith = $r["WWith"] == null ? "" : $r["WWith"];
        $WBy = $r["WBy"] == null ? "" : $r["WBy"];

        $S = $r["S"];
        $SWith = $r["SWith"] == null ? "" : $r["SWith"];
        $SBy = $r["SBy"] == null ? "" : $r["SBy"];

        $Framug = $r["Framug"] ? 1 : 0;
        $Sum = $r["Sum"];
        $arrDoorSize[] = array(
            "DoorType" => $r["DoorType"],
            "Step" => $r["Step"],
            "HWith" => $HWith,
            "HBy" => $HBy,
            "WWith" => $WWith,
            "WBy" => $WBy,

            "S" => $S,
            "SWith" => $SWith,
            "SBy" => $SBy,

            "Framug" => $Framug,
            "Sum" => $Sum
        );
    };
$d->close();
//Константы
$arrConst = array();
$d = $m->query("SELECT * FROM payrollconstant");
if ($d->num_rows > 0)
    while ($r = $d->fetch_assoc())
        $arrConst[] = array(
            "DoorType" => $r["DoorType"],
            "Step" => $r["Step"],
            "Note" => $r["Name"],
            "Sum" => $r["Sum"]
        );
$d->close();
//Конструкция двери
$arrConstruct = null;
$d = $m->query("SELECT * FROM PayrollConstruct");
if ($d->num_rows > 0)
    while($r = $d->fetch_assoc())
    {
        $arrConstruct[] = array(
            "DoorType" => $r["DoorType"],
            "Step" => $r["Step"],

            "Frame" => $r["Frame"],
            "FrameCount" => $r["FrameCount"],
            "FrameSum" => $r["FrameSum"],

            "Dovod" => $r["Dovod"],
            "DovodPreparation" => $r["DovodPreparation"],
            "DovodSum" => $r["DovodSum"],

            "Nalichnik" => $r["Nalichnik"],
            "NalichnikSum" => $r["NalichnikSum"],

            "Window" => $r["Window"],
            "WindowCount" => $r["WindowCount"],
            "WindowMore" => $r["WindowMore"],
            "WindowSum" => $r["WindowSum"],

            "Framuga" => $r["Framuga"],
            "FramugaSum" => $r["FramugaSum"],

            "Petlya" => $r["Petlya"],
            "PetlyaCount" => $r["PetlyaCount"],
            "PetlyaMore" => $r["PetlyaMore"],
            "PetlyaSum" => $r["PetlyaSum"],

            "PetlyaWork" => $r["PetlyaWork"],
            "PetlyaWorkCount" => $r["PetlyaWorkCount"],
            "PetlyaWorkMore" => $r["PetlyaWorkMore"],
            "PetlyaWorkSum" => $r["PetlyaWorkSum"],

            "PetlyaStvorka" => $r["PetlyaStvorka"],
            "PetlyaStvorkaCount" => $r["PetlyaStvorkaCount"],
            "PetlyaStvorkaMore" => $r["PetlyaStvorkaMore"],
            "PetlyaStvorkaSum" => $r["PetlyaStvorkaSum"],

            "Stiffener" => $r["Stiffener"],
            "StiffenerW" => $r["StiffenerW"],
            "StiffenerSum" => $r["StiffenerSum"],

            "M2" => $r["M2"],
            "M2Sum" => $r["M2Sum"],

            "Antipanik" => $r["Antipanik"],
            "AntipanikSum" => $r["AntipanikSum"],

            "Otboynik" => $r["Otboynik"],
            "OtboynikSum" => $r["OtboynikSum"],

            "Wicket" => $r["Wicket"],
            "WicketSum" => $r["WicketSum"],

            "BoxLock" => $r["BoxLock"],
            "BoxLockSum" => $r["BoxLockSum"],

            "Otvetka" => $r["Otvetka"],
            "OtvetkaSum" => $r["OtvetkaSum"],

            "Isolation" => $r["Isolation"],
            "IsolationSum" => $r["IsolationSum"],

            "Grid" => $r["Grid"],
            "GridCount" => $r["GridCount"],
            "GridSum" => $r["GridSum"]
        );
    };
$d->close();
?>

<table>
    <thead>
    <tr>
        <th>idNC</th>
        <th>Дата заказа</th>
        <th>Отметка</th>
        <th>Разница</th>
        <th>Наряд</th>
        <th>Должность</th>
        <th>ФИО</th>
        <th>Этап</th>
        <th>Стоимость предыдущая</th>
        <th>Стоимость новая</th>
        <th>Разница</th>
        <th>Разница %</th>
        <th>Обработан</th>
    </tr>
    </thead>
    <tbody id="CostTable">
    <?php
    //Начнем расчеты
    foreach ($arrDoor as $od) {
        $idNC=$od["idNC"];
        $CostNew = 0;
        $DoorType=$od["DoorType"];
        $StepStr=$od["StepStr"];
        $W=(int)$od["W"];
        $FramugaH=$od["FramugaH"]==null ? 0 : (int)$od["FramugaH"];
        $H=(int)$od["H"]-$FramugaH;
        $S=$od["S"]==null ? ($od["SEqual"]==1 ? round($W/2) : 0) : (int)$od["S"];
        $SType=(int)$od["SType"];
        foreach ($arrDoorSize as $ds)
            if($DoorType==$ds["DoorType"] & $StepStr==$ds["Step"])
            {
                $flag = true;
                if ($ds["HWith"] != "" & $ds["HBy"] == "")
                    if ((int)$ds["HWith"] > $H) $flag = false;
                if ($ds["HWith"] != "" & $ds["HBy"] != "")
                    if ((int)$ds["HWith"] > $H || $H < (int)$ds["HBy"]) $flag = false;
                if ($ds["HWith"] == "" & $ds["HBy"] != "")
                    if ($H > (int)$ds["HBy"]) $flag = false;
				
                if ($ds["WWith"] != "" & $ds["WBy"] == "")
                    if ((int)$ds["WWith"] > $W) $flag = false;
                if ($ds["WWith"] != "" & $ds["WBy"] != "")
                    if ((int)$ds["WWith"] > $W || $W > (int)$ds["WBy"]) $flag = false;
                if ($ds["WWith"] == "" & $ds["WBy"] != "")
                    if ($W > (int)$ds["WBy"]) $flag = false;
                switch ($ds["S"]) {
                    case "":
                        break;
                    case "1":
                        if ($SType==2) $flag = false;
                        break;
                    case "2":
                        switch ($SType == 2) {
                            case false:
                                $flag = false;
                                break;
                            case true:
                                if ($ds["SWith"] != "" || $ds["SBy"] != "") {
                                    $SWith = $ds["SWith"] == "" ? -1 : (int)$ds["SWith"];
                                    $SBy = $ds["SBy"] == "" ? -1 : (int)$SBy;

                                    if ($SWith == -1 & $SBy != -1)
                                        if ($S > $SBy) $flag = false;
                                    if ($SWith != -1 & $SBy != -1)
                                        if ($S < $SWith || $S > $SBy) $flag = true;
                                    if ($SWith != -1 & $SBy == -1)
                                        if ($SWith > $S) $flag = true;
                                };
                                break;
                        };
                        break;
                };
                if ($flag) $CostNew = (float)$ds["Sum"];
            };
        foreach ($arrConst as $con)
            if($con["DoorType"]==$DoorType & $con["Step"]==$StepStr)
                $CostNew+=(float)$con["Sum"];
        foreach ($arrConstruct as $c)
            if($DoorType==$c["DoorType"] & $StepStr==$c["Step"])
            {
                //Рамка
                if($c["Frame"])
                {
                    $WorkWindowCh=$od["WorkWindowCh"];
                    $WorkWindowNoFrame=$od["WorkWindowNoFrame"];

                    $StvorkaWindowCh=$od["StvorkaWindowCh"];
                    $StvorkaWindowNoFrame=$od["StvorkaWindowNoFrame"];

                    $FramugaWindowCh=$od["FramugaWindowCh"];
                    $FramugaWindowNoFrame=$od["FramugaWindowNoFrame"];
                    if(($WorkWindowCh==1 & $WorkWindowNoFrame==0) || ($StvorkaWindowCh==1 & $StvorkaWindowNoFrame==0) || ($FramugaWindowCh==1 & $FramugaWindowNoFrame==0))
                        switch ($c["FrameCount"]==1)
                        {
                            case true:
                                $FrameCount=0;
                                $FrameCount+=($WorkWindowCh==1 & $WorkWindowNoFrame==0) ? 1 : 0;
                                $FrameCount+=($StvorkaWindowCh==1 & $StvorkaWindowNoFrame==0) ? 1 : 0;
                                $FrameCount+=($FramugaWindowCh==1 & $FramugaWindowNoFrame==0) ? 1 : 0;
                                $CostNew+=(float)$c["FrameSum"]*$FrameCount;
                                break;
                            case false:
                                $CostNew+=(float)$c["FrameSum"];
                                break;
                        };
                };
                //Доводчик
                if($c["Dovod"]==1)
                {
                    $Dovod=$od["Dovod"];
                    if($Dovod=="да" & $c["DovodPreparation"]!=1)
                        $CostNew+=(float)$c["DovodSum"];
                    if(($Dovod=="да" || $Dovod=="нет, подготовка") & $c["DovodPreparation"]==1)
                        $CostNew+=(float)$c["DovodSum"];
                };
                //Наличник
                if($c["Nalichnik"]==1 & $od["Nalichnik"]=="да")
                    $CostNew+=(float)$c["NalichnikSum"];
                //Окно
                if($c["Window"]==1)
                {
                    //Определим кол-во окон
                    $WindowCount=0;
                    $WindowCount+=$od["WorkWindowCh"]==1 ? 1 : 0;
                    $WindowCount+=$od["WorkWindowCh1"]==1 ? 1 : 0;
                    $WindowCount+=$od["WorkWindowCh2"]==1 ? 1 : 0;
                    $WindowCount+=$od["StvorkaWindowCh"]==1 ? 1 : 0;
                    $WindowCount+=$od["StvorkaWindowCh1"]==1 ? 1 : 0;
                    $WindowCount+=$od["StvorkaWindowCh2"]==1 ? 1 : 0;
                    $WindowCount+=$od["FramugaWindowCh"]==1 ? 1 : 0;
                    if($WindowCount>0)
                        switch ($c["WindowCount"])
                        {
                            case 1://Зависит от кол-ва
                                switch ($c["WindowMore"])
                                {
                                    case null:
                                        $CostNew+=(float)$c["WindowSum"]*$WindowCount;
                                        break;
                                    default://Если больше
                                        if($WindowCount>(int)$c["WindowMore"])
                                            $CostNew+=($WindowCount-(int)$c["WindowMore"])*(float)$c["WindowSum"];
                                        break;
                                }
                                break;
                            case 0:
                                $CostNew+=$WindowCount*(float)$c["WindowSum"];
                                break;
                        };
                };
                //Фрамуга
                if($c["Framuga"]==1 & $od["FramugaCh"]==1)
                    $CostNew+=(float)$c["FramugaSum"];
                //Навесы
                if($c["Petlya"]==1)
                {
                    $PetlyaCount=0;
                    $PetlyaCount+=(int)$od["WorkPetlya"];
                    $PetlyaCount+=(int)$od["StvorkaPetlya"];
                    if($PetlyaCount>0)
                        switch ($c["PetlyaCount"]==1)
                        {
                            case true://Зависит от кол-ва
                                switch ($c["PetlyaMore"])
                                {
                                    case null://Не заполненно поле болшльше
                                        $CostNew+=(float)$c["PetlyaSum"]*$PetlyaCount;
                                        break;
                                    default:
                                        $PetlyaMore=(int)$c["PetlyaMore"];
                                        $CostNew+=(float)$c["PetlyaSum"]*($PetlyaCount>$PetlyaMore ? $PetlyaCount-$PetlyaMore : 0);
                                        break;
                                }
                                break;
                            case false:
                                $CostNew+=(float)$c["PetlyaSum"]*$PetlyaCount;
                                break;
                        };
                };
                //Навесы на рабочей створке
                if($c["PetlyaWork"]==1)
                {
                    $PetlyaCount=0;
                    $PetlyaCount+=(int)$od["WorkPetlya"];
                    if($PetlyaCount>0)
                        switch ($c["PetlyaWorkCount"]==1)
                        {
                            case true://Зависит от кол-ва
                                switch ($c["PetlyaWorkMore"])
                                {
                                    case null://Не заполненно поле болшльше
                                        $CostNew+=(float)$c["PetlyaWorkSum"]*$PetlyaCount;
                                        break;
                                    default:
                                        $PetlyaMore=(int)$c["PetlyaWorkMore"];
                                        $CostNew+=(float)$c["PetlyaWorkSum"]*($PetlyaCount>$PetlyaMore ? $PetlyaCount-$PetlyaMore : 0);
                                        break;
                                }
                                break;
                            case false:
                                $CostNew+=(float)$c["PetlyaWorkSum"]*$PetlyaCount;
                                break;
                        };
                };
                //Навесы на второй створке
                if($c["PetlyaStvorka"]==1)
                {
                    $PetlyaCount=0;
                    $PetlyaCount+=(int)$od["StvorkaPetlya"];
                    if($PetlyaCount)
                        switch ($c["PetlyaStvorkaCount"]==1)
                        {
                            case true://Зависит от кол-ва
                                switch ($c["PetlyaStvorkaMore"])
                                {
                                    case null://Не заполненно поле болшльше
                                        $CostNew+=(float)$c["PetlyaStvorkaSum"]*$PetlyaCount;
                                        break;
                                    default:
                                        $PetlyaMore=(int)$c["PetlyaStvorkaMore"];
                                        $CostNew+=(float)$c["PetlyaStvorkaSum"]*($PetlyaCount>$PetlyaMore ? $PetlyaCount-$PetlyaMore : 0);
                                        break;
                                }
                                break;
                            case false:
                                $CostNew+=(float)$c["PetlyaStvorkaSum"]*$PetlyaCount;
                                break;
                        };
                };
                //Ребра жесткости
                if($c["Stiffener"]==1)
                    switch ($c["StiffenerW"]==1)
                    {
                        case true://Зависит от кв.м.
                            $CostNew+=$H*$W*(float)$c["StiffenerSum"]/1000000;
                            break;
                        case false:
                            $CostNew+=(float)$c["StiffenerSum"];
                            break;
                    };
                //Площадь двери
                if($c["M2"]==1)
                    $CostNew+=$H*$W*(float)$c["M2Sum"]/1000000;
                //Антипаника
                if($c["Antipanik"]==1 & $od["Antipanik"]==1)
                    $CostNew+=$c["AntipanikSum"];
                //Отбойник
                if($c["Otboynik"]==1 & $od["Otboynik"]==1)
                    $CostNew+=(float)$c["OtboynikSum"];
                //Калитка
                if($c["Wicket"]==1 & $od["Wicket"]==1)
                    $CostNew+=(float)$c["WicketSum"];
                //Замок
                if($c["BoxLock"]==1 & $od["BoxLock"]==1)
                    $CostNew+=(float)$c["BoxLockSum"];
                //Ответка
                if($c["Otvetka"]==1 & $od["Otvetka"]==1)
                    $CostNew+=(float)$c["OtvetkaSum"];
                //Утепление
                if($c["Isolation"]==1 & $od["Isolation"]==1)
                    $CostNew+=(float)$c["IsolationSum"];
                //Вент решетка
                if($c["Grid"]==1)
                {
                    $CountGrid=0;
                    $CountGrid+=$od["WorkUpGridCh"]==1 ? 1 : 0;
                    $CountGrid+=$od["WorkDownGridCh"]==1 ? 1 : 0;
                    $CountGrid+=$od["StvorkaUpGridCh"]==1 ? 1 : 0;
                    $CountGrid+=$od["StvorkaDownGridCh"]==1 ? 1 : 0;
                    $CountGrid+=$od["FramugaUpGridCh"]==1 ? 1 : 0;
                    $CountGrid+=$od["FramugaDownGridCh"]==1 ? 1 : 0;
                    $CostNew+=$c["GridCount"]==1 ? $CountGrid * (float)$c["GridSum"] : (float)$c["GridSum"];
                };
                break;
            };
        ?>
        <tr Status="NoComplite" style="background-color: <?php echo $od["DateDif"]>60 ? "lightpink" : "white" ?> ">
            <td Type="idNC"><?php echo $od["idNC"]; ?></td>
            <td><?php echo $od["BlankDate"]; ?></td>
            <td><?php echo $od["DateComplite"]; ?></td>
            <td><?php echo $od["DateDif"]; ?></td>
            <td><?php echo $od["NaryadNum"]; ?></td>
            <td><?php echo $od["Dolgnost"]; ?></td>
            <td><?php echo $od["FIO"]; ?></td>
            <td><?php echo $od["Step"]; ?></td>
            <td><?php echo $od["CostOld"]; ?></td>
            <td Type="CostNew"><?php echo $CostNew; ?></td>
            <td><?php echo $od["CostOld"]-$CostNew; ?></td>
            <td><?php echo $CostNew==0 ? "" : round($od["CostOld"]*100 / $CostNew)-100; ?></td>
            <td></td>
        </tr>
        <?php
        //Сохраним изменения в БД
        $m->query("UPDATE NaryadComplite SET Cost=$CostNew WHERE id=$idNC") or die($m->error);
    };
    };
    ?>
    </tbody>
</table>
<script src="jquery-3.3.1.min.js"></script>
<script>
    function EditCost(){
        var CountRowComplite=0;
        delay
    }
    function Start(){
        $("#CostTable tr[Status=NoComplite]").each(function () {
            let tr=$(this);
            let idNC=tr.find("td[Type=idNC]").text();
            let CostNew=tr.find("td[Type=CostNew]").text();

        });
    }
</script>