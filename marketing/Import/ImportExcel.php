<?php

// Каталог, в который мы будем принимать файл:
$uploaddir = './Import/Temp/';
$uploadfile = $uploaddir.basename($_FILES['CustomerFile']['name']);

// Копируем файл из каталога для временного хранения файлов:
if (copy($_FILES['CustomerFile']['tmp_name'], $uploadfile))
{

}
else { echo "<h3>Ошибка! Не удалось загрузить файл на сервер!</h3>"; exit; }

$ResultArr=readExelFile($uploadfile);

//Загрузим справочники из БД
$Manuals=new GlobalManuals($m);


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
    foreach ($ResultArr as $r)
    if(isset($r[0]))
        if(is_numeric($r[0]))
        {
            $tr="";
            $tr=$tr."<td>".ConstructInp($r[0], "int","NumPP",30,true)."</td>";
            $tr=$tr."<td>".ConstructList($r[1],$Manuals->TypeDoor, "Dovod",150)."</td>";//Наименование
            $tr=$tr."<td>".ConstructInp($r[2], "int","Count",50,true)."</td>";//Кол-во
            $tr=$tr."<td>".ConstructInp($r[3], "int","H",50,true)."</td>";//Высота
            $tr=$tr."<td>".ConstructInp($r[4], "int","W",50,true)."</td>";//Ширина
            $OpenDoor=strtolower($r[5]);
            if(strpos($OpenDoor,"лев")!==false) $OpenDoor="Лев.";
            if(strpos($OpenDoor,"прав")!==false) $OpenDoor="Прав.";
            $tr=$tr."<td>".ConstructList($OpenDoor,$Manuals->OpenDoor, "OpenDoor",50)."</td>";//Открывание
            $tr=$tr."<td>".ConstructInp($r[6], "int","S",50)."</td>";//Рабочая створка
            $tr=$tr."<td>".ConstructInp($r[7], "string","Ral",80)."</td>";//RAL
            $tr=$tr."<td>".ConstructList($r[8],$Manuals->Nalichnik, "Dovod",65)."</td>";//Наличник
            $tr=$tr."<td>".ConstructList($r[9],$Manuals->DovodList, "Dovod",60)."</td>";//Доводчик
            $tr=$tr."<td>".ConstructInp($r[10], "int","NavesWork",30,true)."</td>";//Навесы раб. ств.
            $tr=$tr."<td>".ConstructInp($r[11], "int","NavesStvorka",30)."</td>";//Навесы вторая ств.
            $tr=$tr."<td>".ConstructInp($r[12], "int","WindowWork",30)."</td>";//Окно раб. ств.
            $tr=$tr."<td>".ConstructInp($r[13], "int","WindowStvorka",30)."</td>";//Окно вторая ств.
            $tr=$tr."<td>".ConstructInp($r[14], "int","GridWork",30)."</td>";//Решетка раб. ств.
            $tr=$tr."<td>".ConstructInp($r[15], "int","GridStvorka",30)."</td>";//Решетка вторая ств.
            $Framug=strtolower($r[16])!="" ? strtolower($r[16]) : "нет";
            $tr=$tr."<td>".ConstructList($Framug,["да", "нет"], "Dovod",50)."</td>";//Наличие фрамуги
            $tr=$tr."<td>".ConstructInp($r[17], "int","FramugH",60)."</td>";//Высота фрамуги
            $tr=$tr."<td><textarea Type=Note>".$r[18]."</textarea></td>";//Примечание

            $trStyle="success";
            if(strpos($tr,"pink")) $trStyle="danger";
            echo "<tr class='$trStyle'>$tr</tr>";
        };
?>
    </tbody>
</table>
<?php
function readExelFile($filepath){
    require_once "PHPExcel/PHPExcel.php"; //подключаем наш фреймворк
    $ar=array(); // инициализируем массив
    $inputFileType = PHPExcel_IOFactory::identify($filepath);  // узнаем тип файла, excel может хранить файлы в разных форматах, xls, xlsx и другие
    $objReader = PHPExcel_IOFactory::createReader($inputFileType); // создаем объект для чтения файла
    //$objReader=setReadDataOnly(true);
    $objPHPExcel = $objReader->load($filepath); // загружаем данные файла в объект
    $objWorksheet = $objPHPExcel->getActiveSheet();
    $ar=array();
    $r=0; $NullRowCount=0;
    foreach( $objWorksheet->getRowIterator() as $row )
        if($r<200)
        {
            $NullRow=true;
            $c=0; $ac=array();
            foreach( $row->getCellIterator() as $cell )
                if($c<25)
                {
                    $value =(string) $cell->getValue();
                    if(($value!=null & $value!="") || $r<10)
                        $NullRow=false;

                    $ac[$c]=$value==null?"":$value;
                    $c++;
                };

            if($NullRow) $NullRowCount++;
            if($NullRowCount==3) break;
            if(isset($ac[0])) if(strpos(mb_strtolower(mb_convert_encoding($ac[0],'UTF-8')),'итого')) break;

            if($r<9 || isset($ac[1]))
                $ar[$r]=$ac;
            $r++;
        }
        else
            break;
    //echo json_encode($ar);
    $objPHPExcel->disconnectWorksheets();
    return $ar;
}

/**
 * Конструирование списка для
 * @param $s
 * @param $arr
 * @param $ElName
 * @return String list
 */
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
<script src="Import/CheckOfCorrect.js"></script>
<script>
    var GloblaManuals={};
    //Подгрузим список дверей
    GloblaManuals.TypeDoors=new Array(
        <?php
            $gl=new GlobalManuals($m);
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
        form.appendChild(GenerateInputElement("idCustomer","<?php echo $_POST["idCustomer"] ?>"));
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
