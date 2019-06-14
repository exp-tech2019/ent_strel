<?php
ini_set('MAX_EXECUTION_TIME', '-1');

$XMLParams = simplexml_load_file("../../params.xml");
$m = new mysqli($XMLParams->ConnectDB->Host, $XMLParams->ConnectDB->User, $XMLParams->ConnectDB->Pass, $XMLParams->ConnectDB->DB);

$arrStepStr=array(
    "Сварка","Сборка","Покраска"
);
$d=$m->query("SELECT o.Blank, o.BlankDate, o.Shet, od.*, IF(od.S IS NOT NULL, 2, IF(od.SEqual=1, 2, 1)) AS SType FROM orderdoors od, oreders o WHERE o.BlankDate> str_to_date('01.12.2018','%d.%m.%Y') AND o.id=od.idOrder GROUP BY od.name, od.H, od.W");
$arrDoor = array();
while ($r = $d->fetch_assoc())
    for($i=0; $i<count($arrStepStr); $i++)
    $arrDoor[] = array(
        "Blank" => $r["Blank"],
        "BlankDate" => $r["BlankDate"],
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

        "Step" => $i,
        "StepStr"=>$arrStepStr[$i]
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

?>

<table>
    <thead>
    <tr>
        <th>Счет</th>
        <th>Дата</th>
        <th>Стадия</th>
        <th>Наименование</th>
        <th>Высота</th>
        <th>Ширина</th>
        <th>Створка</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($arrDoor as $od) {
        $CostNew = 0;
        $DoorType = $od["DoorType"];
        $StepStr = $od["StepStr"];
        $W = (int)$od["W"];
        $FramugaH = $od["FramugaH"] == null ? 0 : (int)$od["FramugaH"];
        $H = (int)$od["H"] - $FramugaH;
        $S = $od["S"] == null ? ($od["SEqual"] == 1 ? round($W / 2) : 0) : (int)$od["S"];
        $SType = (int)$od["SType"];
        foreach ($arrDoorSize as $ds)
            if ($DoorType == $ds["DoorType"] & $StepStr == $ds["Step"]) {
                $flag = true;
                if ($ds["HWith"] != "" & $ds["HBy"] == "")
                    if ((int)$ds["HWith"] > $H) $flag = false;
                if ($ds["HWith"] != "" & $ds["HBy"] != "")
                    if ($H < (int)$ds["HWith"] || $H > (int)$ds["HBy"]) $flag = false;
                if ($ds["HWith"] == "" & $ds["HBy"] != "")
                    if ($H > (int)$ds["HBy"]) $flag = false;
				
                if ($ds["WWith"] != "" & $ds["WBy"] == "")
                    if ((int)$ds["WWith"] > $W) $flag = false;
                if ($ds["WWith"] != "" & $ds["WBy"] != "")
                    if ($W < (int)$ds["WWith"] || $W > (int)$ds["WBy"]) $flag = false;
                if ($ds["WWith"] == "" & $ds["WBy"] != "")
                    if ($W > (int)$ds["WBy"]) $flag = false;
                switch ($ds["S"]) {
                    case "":
                        break;
                    case "1":
                        if ($SType == 2) $flag = false;
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
        if($CostNew==0){ ?>
            <tr>
                <td><?php echo $od["Shet"]; ?></td>
                <td><?php echo $od["BlankDate"]; ?></td>
                <td><?php echo $od["StepStr"]; ?></td>
                <td><?php echo $od["DoorType"]; ?></td>
                <td><?php echo $H ?></td>
                <td><?php echo $W ?></td>
                <td><?php echo $SType==1 ? "Одностворчатая" : "Двухстворчатая" ?></td>
            </tr>
        <?php };
    };
    ?>
    </tbody>
</table>
