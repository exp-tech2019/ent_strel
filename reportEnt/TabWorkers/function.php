<?php
if(isset($_POST["DateWith"])){
    $DateWithStr=$_POST["DateWith"];
    $DateByStr=$_POST["DateBy"];
    $WorkerFired=$_POST["WorkerFired"];
    header('Content-Type: application/json');
    echo json_encode(ReportQuery($DateWithStr,$DateByStr, $WorkerFired));
};
if(isset($_GET["DateWith"])){
    $DateWithStr=$_GET["DateWith"];
    $DateByStr=$_GET["DateBy"];
    $WorkerFired=$_GET["WorkerFired"];
    $WorkersView=json_decode($_GET["WorkersView"]);
    //$TypeDoorsView=$_GET["TypeDoorsView"];
    $Result=ReportQuery($DateWithStr, $DateByStr, $WorkerFired);
    $HeaderArr=array();
    $RowsArr=array();
    $flag=false;
    foreach ($Result as $Row){
        $r=array();
        foreach ($Row as $key=>$value){
            if(!$flag){
                $HeaderArr[]=$key;
            };
            $r[]=$value;
        };
        $RowsArr[]=$r;
        $flag=true;
    };
/*
    echo "<pre>";
    print_r($WorkersView);
    echo "</pre>";
    echo "<pre>";
    print_r($RowsArr);
    echo "</pre>";
*/
    $pdf=new ReportPrintPdf($HeaderArr, $DateWithStr,$DateByStr);
    foreach ($RowsArr as $Row)
        if(in_array($Row[3],$WorkersView))
        $pdf->AddRow($Row);
    $pdf->Output();
}

function ReportQuery($DateWithStr, $DateByStr, $WorkerFired){
    $XMLParams=simplexml_load_file("../../params.xml");
    $Host=$XMLParams->ConnectDB->Host;
    $User=$XMLParams->ConnectDB->User;
    $Pass=$XMLParams->ConnectDB->Pass;
    $DateBase=$XMLParams->ConnectDB->DB;

    $db=new PDO("mysql:host=$Host;dbname=$DateBase", $User, $Pass);

    $d=$db->query("SELECT Name FROM ManualTypeDoors WHERE Name NOT LIKE '%Ворота%' AND Name NOT LIKE '%Люк%' ORDER BY Name");
    $TypeDoors=array();
    if($d) {
        while ($r = $d->fetch())
            $TypeDoors[] = $r["Name"];
        $d->closeCursor();
    };

    $Result=array();
    $d=$db->query("SELECT w.id, w.num, w.FIO, d.Dolgnost FROM Workers w, ManualDolgnost d WHERE w.fired=$WorkerFired AND w.DolgnostID=d.id ORDER BY w.FIO");
    if($d){
        while($r=$d->fetch()) {
            $Result[] = array(
                "idWorker" => $r["id"],
                "Num"=>$r["num"],
                "FIO" => $r["FIO"],
                "Dolgnost" => $r["Dolgnost"]
            );
            foreach ($TypeDoors as $TypeDoor) {
                $Result[count($Result) - 1][$TypeDoor] = 0;
            };

            $Result[count($Result) - 1]["S_One"]=0;
            $Result[count($Result) - 1]["S_Two"]=0;
            $Result[count($Result) - 1]["Ворота"]=0;
            $Result[count($Result) - 1]["Люк"]=0;
            $Result[count($Result) - 1]["All"]=0;
            $Result[count($Result) - 1]["Рамка"]=0;
        };
        $d->closeCursor();
    };


    $d=$db->prepare("SELECT w.id AS idWorker, od.name AS TypeDoor, 
    od.WorkWindowCh, od.WorkWindowCh1, od.WorkWindowCh2, od.StvorkaWindowCh, od.StvorkaWindowCh1, od.StvorkaWindowCh2, od.FramugaWindowCh,
    od.WorkWindowNoFrame, od.WorkWindowNoFrame1, od.WorkWindowNoFrame2, od.StvorkaWindowNoFrame, od.StvorkaWindowNoFrame1, od.StvorkaWindowNoFrame2, od.FramugaWindowNoFrame,
     IF(od.S IS NOT NULL OR od.SEqual=1, 1, 0) AS S, w.FIO, d.Dolgnost, nc.Step FROM Workers w, ManualDolgnost d, NaryadComplite nc, Naryad n, OrderDoors od 
	WHERE w.id=nc.idWorker AND w.DolgnostID=d.id
    AND DATE(nc.DateComplite) BETWEEN STR_TO_DATE(:DateWith,'%d.%m.%Y') AND STR_TO_DATE(:DateBy,'%d.%m.%Y') 
    AND nc.idNaryad=n.id AND n.idDoors=od.id") or die(print_r($db->errorInfo()));


    $d->bindParam(":DateWith",$DateWithStr,PDO::PARAM_STR);
    $d->bindParam(":DateBy",$DateByStr,PDO::PARAM_STR);
    $d->execute();
    mb_internal_encoding('UTF-8');
    while($r=$d->fetch())
        foreach ($Result as &$tr) {
            if ($tr["idWorker"] == $r["idWorker"]) {
                $tr["All"] += (int)$r["Step"]!=4 ? 1 : 0;
                $typeDoor=mb_strtolower($r["TypeDoor"],'UTF-8');
                if(mb_strpos($typeDoor, "ворота")===false & mb_strpos($typeDoor, "люк")===false & (int)$r["Step"]!=4)
                {
                    $tr["S_One"] += $r["S"] == 0 ? 1 : 0;
                    $tr["S_Two"] += $r["S"] == 1 ? 1 : 0;
                    $tr[$r["TypeDoor"]] += 1;
                    continue;
                };
                if(mb_strpos($typeDoor, "ворота")!==false & mb_strpos($typeDoor, "люк")===false & (int)$r["Step"]!=4)
                {
                    $tr["Ворота"] += 1;
                    continue;
                };
                if(mb_strpos($typeDoor, "ворота")===false & mb_strpos($typeDoor, "люк")!==false & (int)$r["Step"]!=4)
                {
                    $tr["Люк"] += 1;
                    continue;
                };
                if($r["Step"]==4)
                {
                    $FrameCount=0;
                    $FrameCount+=$r["WorkWindowCh"]==1 & $r["WorkWindowNoFrame"]==0 ? 1 : 0;
                    $FrameCount+=$r["WorkWindowCh1"]==1 & $r["WorkWindowNoFrame1"]==0 ? 1 : 0;
                    $FrameCount+=$r["WorkWindowCh2"]==1 & $r["WorkWindowNoFrame2"]==0 ? 1 : 0;
                    $FrameCount+=$r["StvorkaWindowCh"]==1 & $r["StvorkaWindowNoFrame"]==0 ? 1 : 0;
                    $FrameCount+=$r["StvorkaWindowCh1"]==1 & $r["StvorkaWindowNoFrame1"]==0 ? 1 : 0;
                    $FrameCount+=$r["StvorkaWindowCh2"]==1 & $r["StvorkaWindowNoFrame2"]==0 ? 1 : 0;
                    $FrameCount+=$r["FramugaWindowCh"]==1 & $r["FramugaWindowNoFrame"]==0 ? 1 : 0;
                    $tr["Рамка"] += $FrameCount;
                    continue;
                };
                /*
                $tr["S_One"] += $r["S"] == 0 & (int)$r["Step"]!=4 ? 1 : 0;
                $tr["S_Two"] += $r["S"] == 1 & (int)$r["Step"]!=4 ? 1 : 0;
                $tr[(int)$r["Step"]!=4 ? $r["TypeDoor"] : "Рамка"] += 1;
                */
                break;
            };
        };



    $d=null;
    $db=null;

    return $Result;
}

class ReportPrintPdf{
    private $pdf=null;
    private $HeaderArr=array();
    private $PagNum=0;

    function __construct($HeaderArr, $DateWith, $DateBy)
    {
        $this->HeaderArr=$HeaderArr;

        require_once("../../TCPDF/tcpdf.php");
        $this->pdf = new TCPDF('R', 'mm', 'A4', true, 'UTF-8', false);
        $this->pdf->SetAuthor('Ваше имя');
        $this->pdf->SetTitle('Название нашего документа');
        $this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $this->pdf->SetMargins(10, 10, 10);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->pdf->SetFont("calibri", 'BI', 10);
        $this->pdf->AddPage();
        $this->pdf->Cell(100,0,"Период: ".$DateWith." - ".$DateBy);
        $this->pdf->ln();
        $this->pdf->SetFont("calibri", 'BI', 8);
    }

    public function AddRow($RowArr){
        if($this->PagNum==0)
            $this->AddHeader();
        $this->PagNum=$this->pdf->getNumPages();
        $this->pdf->startTransaction();
        foreach ($RowArr as $i=>$Cell) {
            if($i==0) continue;
            $StrCell=$Cell;
            $With=10;
            $Allgin="C";
            if($i==1) $With=6;
            if($i==2) {$With=50; $Allgin='L'; };
            if($i==3)  {$With=20; $Allgin='L'; };
            if($i==3) $StrCell=strlen(utf8_decode($Cell))>11 ? mb_substr($Cell,0,13,'UTF-8') : $Cell;
            $this->pdf->cell($With, 0,$StrCell, 1, 0, $Allgin,false);
        };
        switch ($this->PagNum<$this->pdf->getNumPages()){
            case true:
                $this->pdf->ln();
                $this->pdf->rollbackTransaction(true);
                $this->AddHeader();
                $this->AddRow($RowArr);
                break;
            default:
                $this->pdf->commitTransaction();
                $this->pdf->ln();
                break;
        };
    }

    public function Output(){
        $this->pdf->Output('example_1.pdf', 'I');
    }

    private function AddHeader(){
        $ArrCells=array("Num"=>"№", "FIO"=>"ФИО", "Dolgnost"=>"Должность", "All"=>"Всего", "S_One"=>"Одн.", "S_Two"=>"Двух");
        $RowArr=$this->HeaderArr;
        foreach ($RowArr as $key=>$Cell) {
            if($Cell=="idWorker") continue;
            $With=10;
            if($Cell=="Num") $With=6;
            if($Cell=="FIO") $With=50;
            if($Cell=="Dolgnost")  $With=20;

            $Cell=isset($ArrCells[$Cell]) ? $ArrCells[$Cell] : $Cell;
            $this->pdf->cell($With, 0, $Cell, 1, 0);
        };
        $this->pdf->ln();

    }
}

?>