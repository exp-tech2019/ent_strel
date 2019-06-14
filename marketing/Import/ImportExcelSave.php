<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 04.12.2016
 * Time: 22:25
 */
    $NumPP=$_POST["NumPP"];
    $TypeDoor=$_POST["TypeDoor"];
    $Count=$_POST["Count"];
    $H=$_POST["H"];
    $W=$_POST["W"];
    $Open=$_POST["Open"];
    $S=$_POST["S"];
    $Ral=$_POST["Ral"];
    $Nalichnik=$_POST["Nalichnik"];
    $Dovod=$_POST["Dovod"];
    $NavesWork=$_POST["NavesWork"];
    $NavesStvorka=$_POST["NavesStvorka"];
    $WindowWork=$_POST["WindowWork"];
    $WindowStvorka=$_POST["WindowStvorka"];
    $GridWork=$_POST["GridWork"];
    $GridStvorka=$_POST["GridStvorka"];
    $Framug=$_POST["Framug"];
    $FramugH=$_POST["FramugH"];
    $Note=$_POST["Note"];

    $FlagErr=false;
    $gl=new GlobalManuals($m);
    for($i=0; $i<count($NumPP); $i++)
        if(isset($NumPP[$i]))
        {
            if($NumPP[$i]=="" || !is_numeric($NumPP[$i])) $FlagErr=true;
            if(!in_array($TypeDoor[$i], $gl->TypeDoor)) $FlagErr=true;
            if($Count[$i]=="" || !is_numeric($Count[$i])) $FlagErr=true;
            if($H[$i]=="" || !is_numeric($H[$i])) $FlagErr=true;
            if($W[$i]=="" || !is_numeric($W[$i])) $FlagErr=true;
            if(!in_array($Open[$i], $gl->OpenDoor)) $FlagErr=true;
            if($S[$i]!="" & (!is_numeric($S[$i]) & !strpos(strtolower($S[$i]),"равн"))) $FlagErr=true;
            if($Ral[$i]=="") $FlagErr=true;
            if(!in_array($Nalichnik[$i], $gl->Nalichnik)) $FlagErr=true;
            if(!in_array(strtolower($Dovod[$i]), $gl->DovodList)) $FlagErr=true;
            if($NavesWork[$i]!="" & !is_numeric($NavesWork[$i])) $FlagErr=true;
            if($NavesStvorka[$i]!="" & !is_numeric($NavesStvorka[$i])) $FlagErr=true;
            if(!in_array(strtolower($WindowWork[$i]), array("да","нет", "")) & !is_numeric($WindowStvorka[$i])) $FlagErr=true;
            if(!in_array(strtolower($WindowStvorka[$i]), array("да","нет", "")) & !is_numeric($WindowStvorka[$i])) $FlagErr=true;
            if(!in_array(strtolower($GridWork[$i]), array("да","нет", "")) & !is_numeric($GridWork[$i])) $FlagErr=true;
            if(!in_array(strtolower($GridStvorka[$i]), array("да","нет", "")) & !is_numeric($GridStvorka[$i])) $FlagErr=true;
            if(!in_array(strtolower($Framug[$i]), array("да","нет", ""))) $FlagErr=true;
            if($FramugH[$i]!="" & !is_numeric($FramugH[$i])) $FlagErr=true;
        };
    if(!$FlagErr)
    {

        $m->autocommit(false);
        $idCustomer=$_POST["idCustomer"];
        $m->query("INSERT INTO TempOrders (DateCreate, Shet, ShetDate, idCustomer, Note) VALUES (NOW(), '', NULL, $idCustomer,'')") or die($FlagErr=true);
        $idOrder=$m->insert_id;

        for($i=0; $i<count($NumPP); $i++)
            if(isset($NumPP[$i]) & !$FlagErr)
            {
                echo "tt<br>";
                $NumPPS=$NumPP[$i];
                $TypeDoorS=$TypeDoor[$i];
                $CountS=$Count[$i];
                $Hs=$H[$i];
                $Ws=$W[$i];
                $OpenS=$Open[$i];
                $Ss="NULL"; $SEqualS="NULL";
                if(is_numeric($S[$i]))
                {
                    $Ss=$S[$i];
                    $SEqualS="0";
                };
                if(strpos(strtolower($S[$i]),"равн"))
                {
                    $Ss="NULL";
                    $SEqualS="1";
                };
                $RalS=$Ral[$i];
                $NalichnikS=$Nalichnik[$i];
                $DovodS=$Dovod[$i];
                $NavesWorkS=is_numeric($NavesWork[$i]) ? $NavesWork[$i] : "0";
                $NavesStvorkaS=is_numeric($NavesStvorka[$i]) ? $NavesStvorka[$i] : "0";

                $WindowWorkS=is_numeric($WindowWork[$i]) ? $WindowWork[$i] : "0";
                $WindowStvorkaS=is_numeric($WindowStvorka[$i]) ? $WindowStvorka[$i] : "0";

                $GridWorkS=is_numeric($GridWork[$i]) ? $GridWork[$i] : "0";
                $GridStvorkaS=is_numeric($GridStvorka[$i]) ? $GridStvorka[$i] : "0";

                $FramugS=strtolower($Framug[$i])=="да" ? "1": "0";
                $FramugHs=($FramugS=="0" & is_numeric($FramugH[$i])) ? $FramugH[$i] : "0";

                $NoteS=$Note[$i];

                $m->query("INSERT INTO `ent`.`temporderdoors`(idOrder,NumPP,TypeDoor,Count,H,W,S,SEqual,Open,Nalichnik,Dovod,Ral,Note,Markirovka,NavesWork,NavesStvorka,WindowWork,WindowStvorka,GridWork,GridStvorka,Framug,FramugH) VALUES (".
                    "$idOrder, $NumPPS, '$TypeDoorS', $CountS, $Hs, $Ws, $Ss, $SEqualS, '$OpenS', '$NalichnikS', '$DovodS', '$RalS', '$NoteS', '', $NavesWorkS, $NavesStvorkaS, $WindowWorkS, $WindowStvorkaS, $GridWorkS, $GridStvorkaS, $FramugS, $FramugHs".
                ")") or die($FlagErr=true);
            };
        echo "FlagErr=".$FlagErr;
        if($FlagErr)
        {
            $m->rollback();
        }
        else {
            $m->commit();
            echo "Заказ успшено загружен в систему. Перейти к просмотру заказа?";
        };
    };
    if($FlagErr)
    {

        ?>
        <button id="BtnSave" type="button" class="btn btn-primary">Проверить и сохранить</button>
        <br><br>
        <table class='table table-striped table-condensed table-hover'>
            <tr>
            <tr>
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
            <tbody id="ImportTable">
            <?php
                for($i=0; $i<count($NumPP); $i++)
                    if(isset($NumPP[$i]))
                    {
                        $tr="";
                        $tr=$tr."<td>".ConstructInp($NumPP[$i], "int","NumPP",30,true)."</td>";
                        $tr=$tr."<td>".ConstructList($TypeDoor[$i],$gl->TypeDoor, "Dovod",150)."</td>";//Наименование
                        $tr=$tr."<td>".ConstructInp($Count[$i], "int","Count",50,true)."</td>";//Кол-во
                        $tr=$tr."<td>".ConstructInp($H[$i], "int","H",50,true)."</td>";//Высота
                        $tr=$tr."<td>".ConstructInp($W[$i], "int","W",50,true)."</td>";//Ширина
                        $OpenDoorS=strtolower($Open[$i]);
                        if(strpos($OpenDoorS,"лев")!==false) $OpenDoorS="Лев.";
                        if(strpos($OpenDoorS,"прав")!==false) $OpenDoorS="Прав.";
                        $tr=$tr."<td>".ConstructList($OpenDoorS,$gl->OpenDoor, "OpenDoor",50)."</td>";//Открывание
                        $tr=$tr."<td>".ConstructInp($S[$i], "int","S",50)."</td>";//Рабочая створка
                        $tr=$tr."<td>".ConstructInp($Ral[$i], "string","Ral",80)."</td>";//RAL
                        $tr=$tr."<td>".ConstructList($Nalichnik[$i],$gl->Nalichnik, "Dovod",65)."</td>";//Наличник
                        $tr=$tr."<td>".ConstructList($Dovod[$i],$gl->DovodList, "Dovod",60)."</td>";//Доводчик
                        $tr=$tr."<td>".ConstructInp($NavesWork[$i], "int","NavesWork",30,true)."</td>";//Навесы раб. ств.
                        $tr=$tr."<td>".ConstructInp($NavesStvorka[$i], "int","NavesStvorka",30)."</td>";//Навесы вторая ств.
                        $tr=$tr."<td>".ConstructInp($WindowWork[$i], "int","WindowWork",30)."</td>";//Окно раб. ств.
                        $tr=$tr."<td>".ConstructInp($WindowStvorka[$i], "int","WindowStvorka",30)."</td>";//Окно вторая ств.
                        $tr=$tr."<td>".ConstructInp($GridWork[$i], "int","GridWork",30)."</td>";//Решетка раб. ств.
                        $tr=$tr."<td>".ConstructInp($GridStvorka[$i], "int","GridStvorka",30)."</td>";//Решетка вторая ств.
                        $FramugS=strtolower($Framug[$i])!="" ? strtolower($Framug[$i]) : "нет";
                        $tr=$tr."<td>".ConstructList($FramugS,["да", "нет"], "Dovod",50)."</td>";//Наличие фрамуги
                        $tr=$tr."<td>".ConstructInp($FramugH[$i], "int","FramugH",60)."</td>";//Высота фрамуги
                        $tr=$tr."<td><textarea Type=Note>".$Note[$i]."</textarea></td>";//Примечание

                        $trStyle="success";
                        if(strpos($tr,"pink")) $trStyle="danger";
                        echo "<tr class='$trStyle'>$tr</tr>";
                    };
            ?>
            </tbody>
        </table>
    <?php
    };
function ConstructList($s, $arr, $ElName, $Width=100){
    $Return="";
    $flagOk=false;
    $Style="";
    foreach ($arr as $a)
        if($a==$s)
        {
            $flagOk=true;
            $Return=$Return."<option selected>$a</option>";
        }
        else
            $Return=$Return."<option>$a</option>";
    if(!$flagOk)
    {
        $Return=$Return."<option selected>$s</option>";
        $Style="background-color:lightpink; ";
    }
    $Width="width:".$Width."px";
    return "<select Type=$ElName style='$Style $Width'>$Return</select>";
};

function ConstructInp($s, $Type, $ElName, $Width=100, $isNotNull=false){
    $Result="";
    switch($Type){
        case "int": $Result="<input Type=$ElName value='$s' style='width:".$Width."px' ".(!$isNotNull & $s=="" ? "" : (is_numeric($s) ? "" : "style='background-color:lightpink;'")).">"; break;
        default: $Result="<input Type=$ElName value='$s' style='width:".$Width."px' ".(!$isNotNull ? "" : ($isNotNull & $s!="" ? "" : "style='background-color:lightpink;'")).">";
    };
    return $Result;
}
?>

<script>
    var GloblaManuals={};
    //Подгрузим список дверей
    GloblaManuals.TypeDoors=new Array(
        <?php
        $s="";
        foreach ($gl->TypeDoor as $c)
            $s=$s."'".$c."', ";
        echo $s;
        ?>
        null);
    GloblaManuals.Dovod=new Array(
        <?php
        $s="";
        foreach ($gl->DovodList as $c)
            $s=$s."'".$c."', ";
        echo $s;
        ?>
        null);
    GloblaManuals.Nalichnik=new Array(
        <?php
        $s="";
        foreach ($gl->Nalichnik as $c)
            $s=$s."'".$c."', ";
        echo $s;
        ?>
        null);
    GloblaManuals.OpenDoor=new Array(
        <?php
        $s="";
        foreach ($gl->OpenDoor as $c)
            $s=$s."'".$c."', ";
        echo $s;
        unset($gl);
        unset($s);
        ?>
        null);
    $("#BtnSave").click(function(){
        var form = document.createElement('form');
        form.setAttribute('action', "index.php");
        form.setAttribute('method', 'post');
        form.appendChild(GenerateInputElement("MVC","ImportExcelSave"));
        for(var i=0;i<$("#ImportTable tr").length; i++)
        {
            form.appendChild(GenerateInputElement("NumPP[]",$("#ImportTable tr:eq("+i+") td:eq(0) input").val()));
            form.appendChild(GenerateInputElement("TypeDoor[]",$("#ImportTable tr:eq("+i+") td:eq(1) select").val()));
            form.appendChild(GenerateInputElement("Count[]",$("#ImportTable tr:eq("+i+") td:eq(2) input").val()));
            form.appendChild(GenerateInputElement("H[]",$("#ImportTable tr:eq("+i+") td:eq(3) input").val()));
            form.appendChild(GenerateInputElement("W[]",$("#ImportTable tr:eq("+i+") td:eq(4) input").val()));
            form.appendChild(GenerateInputElement("Open[]",$("#ImportTable tr:eq("+i+") td:eq(5) select").val()));
            form.appendChild(GenerateInputElement("S[]",$("#ImportTable tr:eq("+i+") td:eq(6) input").val()));
            form.appendChild(GenerateInputElement("Ral[]",$("#ImportTable tr:eq("+i+") td:eq(7) select").val()));
            form.appendChild(GenerateInputElement("Nalichnik[]",$("#ImportTable tr:eq("+i+") td:eq(8) select").val()));
            form.appendChild(GenerateInputElement("Dovod[]",$("#ImportTable tr:eq("+i+") td:eq(9) select").val()));
            form.appendChild(GenerateInputElement("NavesWork[]",$("#ImportTable tr:eq("+i+") td:eq(10) input").val()));
            form.appendChild(GenerateInputElement("NavesStvorka[]",$("#ImportTable tr:eq("+i+") td:eq(11) input").val()));
            form.appendChild(GenerateInputElement("WindowWork[]",$("#ImportTable tr:eq("+i+") td:eq(12) input").val()));
            form.appendChild(GenerateInputElement("WindowStvorka[]",$("#ImportTable tr:eq("+i+") td:eq(13) input").val()));
            form.appendChild(GenerateInputElement("GridWork[]",$("#ImportTable tr:eq("+i+") td:eq(14) input").val()));
            form.appendChild(GenerateInputElement("GridStvorka[]",$("#ImportTable tr:eq("+i+") td:eq(15) input").val()));
            form.appendChild(GenerateInputElement("Framug[]",$("#ImportTable tr:eq("+i+") td:eq(16) select").val()));
            form.appendChild(GenerateInputElement("FramugH[]",$("#ImportTable tr:eq("+i+") td:eq(17) input").val()));
            form.appendChild(GenerateInputElement("Note[]",$("#ImportTable tr:eq("+i+") td:eq(18) textarea").val()));
        };
        form.submit();
    });
    function GenerateInputElement(Name, Value){
        var input = document.createElement('input');
        input.setAttribute('type', 'hidden');
        input.setAttribute('name', Name);
        input.setAttribute('value', Value);
        return input;
    }
</script>
