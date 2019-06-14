<?php
session_start();
include "../params.php";
$m=@new mysqli($GlobalDBHost,$GlobalDBLogin,$GlobalDBPass,$GlobalDBName);
$FlagErr=false; $Result=array();
$data = json_decode($_POST['jsonData']);

//Проверим правильность заполнения позиций
$i=0;
foreach ($data->{"TR"} as $key=>$value)
    if($value->{"Status"}!="Remove")
    {
        $TRResult=array();
        if(!is_numeric($value->{"NumPP"})) $TRResult["NumPP"]=false;
        if(!is_numeric($value->{"Count"})) $TRResult["Count"]=false;
        if(!is_numeric($value->{"H"})) $TRResult["H"]=false;
        if(!is_numeric($value->{"W"})) $TRResult["W"]=false;

        if(count($TRResult)>0) {
            $TRResult["TRGuid"]=$value->{"TRGuid"};
            $Result[$i] = $TRResult;
            $i++;
        };
    };
if(count($Result)>0) {
    echo json_encode(array("ErrorSuccess"=>$Result));
}
else
{
    $m->autocommit(false);
    //Сохраним основные параметры
    $idOrder=$data->{"idOrder"};
    $OrderNum=$data->{"OrderNum"};
    $DateCreate=$data->{"OrderDateCreate"};
    $Shet=$data->{"OrderShet"};
    $ShetDate=$data->{"OrderShetDate"}!="" ? "STR_TO_DATE('".$data->{"OrderShetDate"}."', '%d.%m.%Y')" : "NULL";
    $idCustomer=$data->{"OrderCustomerID"};
    switch($idOrder)
    {
        case "":
            $m->query("INSERT INTO TempOrders (DateCreate, Shet, ShetDate,)") or die($FlagErr=true);
            $idOrder=$m->insert_id;
            break;
        default:
            $m->query("UPDATE TempOrders SET Shet='$Shet', ShetDate=$ShetDate, idCustomer=$idCustomer WHERE id=$idOrder") or die($ErrNote["Order"]=$m->error);
            break;
    };

    //Если все успешно сохраним измененные строки
    $ErrSQL=false;
    $TRAdd=array(); $TRAddPos=0;
    $CalAdd=array(); $CalAddPos=0;
    foreach ($data->{"TR"} as $key=>$value)
        if(!$ErrSQL)
        {
            $idDoor=$value->{"idDoor"};
            if($value->{"Status"}!="Remove") {
                $NumPP = $value->{"NumPP"};
                $Count = $value->{"Count"};
                $TypeDoor = $value->{"TypeDoor"};
                $H = $value->{"H"};
                $W = $value->{"W"};
                $Open = $value->{"Open"};
                $S = $value->{"S"};
                $SEqual = $value->{"SEqual"};
                $Ral = $value->{"Ral"};
                $Nalichnik = $value->{"Nalichnik"};
                $Dovod = $value->{"Dovod"};
                $Note = $value->{"Note"};
                $Markirovka = "";

                $NavesWork = $value->{"NavesWork"};
                $NavesStvorka = $value->{"NavesStvorka"};

                $WindowWork = $value->{"WindowWork"};
                $WindowStvorka = $value->{"WindowStvorka"};

                $GridWork = $value->{"GridWork"};
                $GridStvorka = $value->{"GridStvorka"};

                $Framug = $value->{"Framug"};
                $FramugH = $value->{"FramugH"};
            };

            switch ($value->{"Status"}) {
                case "Edit":
                    $m->query("UPDATE TempOrderDoors SET NumPP=$NumPP, Count=$Count, H=$H, W=$W, TypeDoor='$TypeDoor', S=$S, SEqual=$SEqual, Open='$Open', Nalichnik='$Nalichnik', Dovod='$Dovod', Ral='$Ral', Note='$Note', NavesWork=$NavesWork, NavesStvorka=$NavesStvorka, WindowWork=$WindowWork, WindowStvorka=$WindowStvorka, GridWork=$GridWork, GridStvorka=$GridStvorka, Framug=$Framug, FramugH=$FramugH WHERE id=$idDoor") or die($ErrSQL=true);
                    break;
                case "Add":
                    $m->query("INSERT INTO TempOrderDoors (idOrder, NumPP, Count, H, W, TypeDoor, S, SEqual, Open, Nalichnik, Dovod, Ral, Note, NavesWork, NavesStvorka, WindowWork, WindowStvorka, GridWork, GridStvorka, Framug, FramugH) VALUES ($idOrder, $NumPP, $Count, $H, $W, '$TypeDoor', $S, $SEqual, '$Open', '$Nalichnik', '$Dovod', '$Ral', '$Note', $NavesWork, $NavesStvorka, $WindowWork, $WindowStvorka,$GridWork, $GridStvorka, $Framug, $FramugH)") or die($ErrSQL=true);
                    $idDoor=$m->insert_id;
                    $TRAdd[$TRAddPos]=array(
                        "TRGuid"=>$value->{"TRGuid"},
                        "idDoor"=>$idDoor
                    );
                    $TRAddPos++;
                    break;
                case "Remove":
                    $m->query("DELETE FROM TempOrderDoors WHERE id=$idDoor");
                    break;
            };
            //Выполним SQL запросы для расчетов
            if($value->{"Status"}!="Remove")
                foreach ($value->{"Calc"} as $keyCalc=>$valueCalc){
                    $idCalc=$valueCalc->{"idCalc"};
                    if($valueCalc->{"Status"}!="Remove"){
                        $CalcGuid=$valueCalc->{"CalcGuid"};
                        $Name=$valueCalc->{"Name"};
                        $Type=$valueCalc->{"Type"};
                        $Sum=$valueCalc->{"Sum"};
                    };
                    switch ($valueCalc->{"Status"}){
                        case "Add":
                            $m->query("INSERT INTO TempOrderDoorCalc (idDoor, Type, Name, Sum) VALUES($idDoor, '$Type', '$Name', $Sum)") or die($ErrSQL=true);
                            $idCalc=$m->insert_id;
                            $CalAdd[$CalAddPos]=array(
                                "CalcGuid"=>$CalcGuid,
                                "idCalc"=>$idCalc
                            );
                            $CalAddPos++;
                            break;
                        case "Remove":
                            $m->query("DELETE FROM TempOrderDoorCalc WHERE id=$idCalc") or die($ErrSQL=true);
                            break;
                    };
                };
        };

    //Платежи
    $PaymentsAdd=array(); $PaymentsPos=0;
    foreach ($data->{"Payments"} as $key=>$value)
        if(!$ErrSQL)
        {

            $Status=$value->{"Status"};
            $idPayment=$value->{"idPayment"};
            $Date=$value->{"Date"};
            $TypePayment=$value->{"TypePayment"};
            $Sum=$value->{"Sum"}=="" ? "NULL" : $value->{"Sum"};
            $Note=$value->{"Note"};
            switch ($Status){
                case "Add":
                    $m->query("INSERT INTO TempOrderPayments (idOrder, DatePayment, TypePayment, SumPayment, Note) VALUES ($idOrder, STR_TO_DATE('$Date', '%d.%m.%Y'), $TypePayment, $Sum, '$Note')") or die($ErrSQL=true);
                    $PaymentsAdd[$PaymentsPos]=array(
                        "Guid"=>$value->{"Guid"},
                        "idPayment"=>$m->insert_id
                    );
                    $PaymentsPos++;
                    break;
                case "Edit":
                    $m->query("UPDATE TempOrderPayments SET DatePayment=STR_TO_DATE('$Date', '%d.%m.%Y'), TypePayment=$TypePayment, SumPayment=$Sum, Note='$Note' WHERE id=$idPayment") or die($ErrSQL=true);
                    break;
                case "Remove":
                    $m->query("DELETE FROM TempOrderPayments WHERE id=$idPayment") or die($ErrSQL=true);
                    break;
            };
        };

    if($ErrSQL)
    {
        $Result["Result"]="Ошибка выполнения SQL";
        $m->rollback();
    }
    else {
        $m->commit();
        $Result["TRAdd"]=$TRAdd;
        $Result["CalcAdd"]=$CalAdd;
        $Result["PaymentAdd"]=$PaymentsAdd;
        $Result["Result"]="ok";
    };
    echo json_encode($Result);
};


/*
switch ($FlagErr)
{
    case true: echo "err"; break;
    case false: echo json_encode(array("Result"=>"OK")); break;
};*/
/*
$tr=$data->{"Table"};
foreach ($tr as $key=>$value) {
    echo $value->{"NumPP"}."----";
};
*/
?>