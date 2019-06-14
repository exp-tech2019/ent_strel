<?php
/**
 * Created by PhpStorm.
 * User: anikulshin
 * Date: 16.01.2019
 * Time: 9:49
 */
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
ini_set("max_input_vars", "5000");
session_start();

$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);
$result=array(
    "Status"=>"Error",
    "Note"=>""
);

foreach ($_FILES as $file)
{
    $dirSave=dirname(__FILE__)."/LoadFilePropertes/".$file["name"];
    move_uploaded_file($file["tmp_name"],$dirSave);
    if(strpos($file["name"],".xml")!==false)
    {

        $doc=simplexml_load_file($dirSave);
        //Проверим все типы дверей есть в текущей бд
        $arrDoorType=array();
        $d=$m->query("SELECT * FROM manualtypedoors");
        while ($r=$d->fetch_assoc())
            $arrDoorType[]=$r["Name"];
		//var_dump($arrDoorType);
        $d->close();
        $flag=true;
		/*
		var_dump($doc->DoorTypeList);
        foreach ($doc->DoorTypeList->DoorType as $door)
        {
            $f=false;
            foreach ($arrDoorType as $od)
                if(mb_strcasecmp($door)===mb_strtoupper($od))
				{
					echo $doc->DoorTypeList->DoorType." - ".$od."\n";
                    $f=true;
				};
			echo !$f ? $doc->DoorTypeList->DoorType."\n" : "";
            $flag=$f==false ? false : $flag;
        };
		*/
        switch ($flag)
        {
            case true:
                //Загрузим размеры дверей
                //--Для начала очитим таблицу
                $m->query("DELETE FROM payrolldoorsize_new");
                foreach ($doc->DoorSizes->Door as $od)
                {
                    $DoorType=$od->DoorType;
                    $Step=$od->Step;

                    $HWith=$od->HWith=="" ? "NULL" : $od->HWith;
                    $HBy=$od->HBy=="" ? "NULL" : $od->HBy;

                    $WWith=$od->WWith=="" ? "NULL" : $od->WWith;
                    $WBy=$od->WBy=="" ? "NULL" : $od->WBy;

                    $S=$od->S=="" ? "NULL" : $od->S;
                    $SWith=$od->SWith=="" ? "NULL" : $od->SWith;
                    $SBy=$od->SBy=="" ? "NULL" : $od->SBy;

                    $Framug=$od->Framug;
                    $Sum=$od->Sum;
                    $m->query("INSERT INTO payrolldoorsize_new (DoorType, Step, HWith, HBy, WWith, WBy, S, SWith, SBy, Framug, Sum) VALUES('$DoorType', '$Step', $HWith, $HBy, $WWith, $WBy, $S, $SWith, $SBy, $Framug, $Sum)") or die($m->error);
                };

                //Загрузим константы
                $m->query("DELETE FROM payrollconstant");
                foreach ($doc->ConstList->Const as $const)
                {
                    $DoorType=$const->DoorType;
                    $Step=$const->Step;

                    $Name=$const->Name;
                    $Sum=$const->Sum;

                    $m->query("INSERT INTO payrollconstant (DoorType, Step, Name, Sum) VALUES ('$DoorType', '$Step', '$Name', $Sum)") or die($m->error);
                };

                //Загрузим доп параметры
                $m->query("DELETE FROM payrollconstruct");
                foreach ($doc->ConstructList->Construct as $constr)
                {
                    $DoorType=$constr->DoorType;
                    $Step=$constr->Step;

                    $Frame=$constr->Frame;
                    $FrameCount=$constr->FrameCount;
                    $FrameSum=$constr->FrameSum=="" ? "NULL" : $constr->FrameSum;

                    $Dovod=$constr->Dovod;
                    $DovodPreparation=$constr->DovodPreparation;
                    $DovodSum=$constr->DovodSum=="" ? "NULL" : $constr->DovodSum;

                    $Nalichnik=$constr->Nalichnik;
                    $NalichnikSum=$constr->NalichnikSum=="" ? "NULL" : $constr->NalichnikSum;

                    $Window=$constr->Window;
                    $WindowCount=$constr->WindowCount;
                    $WindowMore=$constr->WindowMore=="" ? "NULL" : $constr->WindowMore;
                    $WindowSum=$constr->WindowSum=="" ? "NULL" : $constr->WindowSum;

                    $Framuga=$constr->Framuga;
                    $FramugaSum=$constr->FramugaSum=="" ? "NULL" : $constr->FramugaSum;

                    $Petlya=$constr->Petlya;
                    $PetlyaCount=$constr->PetlyaCount;
                    $PetlyaMore=$constr->PetlyaMore=="" ? "NULL" : $constr->PetlyaMore;
                    $PetlyaSum=$constr->PetlyaSum=="" ? "NULL" : $constr->PetlyaSum;

                    $Stiffener=$constr->Stiffener;
                    $StiffenerW=$constr->StiffenerW;
                    $StiffenerSum=$constr->StiffenerSum=="" ? "NULL" : $constr->StiffenerSum;

                    $M2=$constr->M2;
                    $M2Sum=$constr->M2Sum=="" ? "NULL" : $constr->M2Sum;

                    $Antipanik=$constr->Antipanik;
                    $AntipanikSum=$constr->AntipanikSum=="" ? "NULL" : $constr->AntipanikSum;

                    $Otboynik=$constr->Otboynik;
                    $OtboynikSum=$constr->OtboynikSum=="" ? "NULL" : $constr->OtboynikSum;

                    $Wicket=$constr->Wicket;
                    $WicketSum=$constr->WicketSum=="" ? "NULL": $constr->WicketSum;

                    $BoxLock=$constr->BoxLock;
                    $BoxLockSum=$constr->BoxLockSum=="" ? "NULL" : $constr->BoxLockSum;

                    $Otvetka=$constr->Otvetka;
                    $OtvetkaSum=$constr->OtvetkaSum=="" ? "NULL" : $constr->OtvetkaSum;

                    $PetlyaWork=$constr->PetlyaWork;
                    $PetlyaWorkCount=$constr->PetlyaWorkCount;
                    $PetlyaWorkMore=$constr->PetlyaWorkMore=="" ? "NULL" : $constr->PetlyaWorkMore;
                    $PetlyaWorkSum=$constr->PetlyaWorkSum=="" ? "NULL" : $constr->PetlyaWorkSum;

                    $Isolation=$constr->Isolation;
                    $IsolationSum=$constr->IsolationSum=="" ? "NULL" : $constr->IsolationSum;

                    $PetlyaStvorka=$constr->PetlyaStvorka;
                    $PetlyaStvorkaCount=$constr->PetlyaStvorkaCount;
                    $PetlyaStvorkaMore=$constr->PetlyaStvorkaMore=="" ? "NULL" : $constr->PetlyaStvorkaMore;
                    $PetlyaStvorkaSum=$constr->PetlyaStvorkaSum=="" ? "NULL" : $constr->PetlyaStvorkaSum;

                    $Grid=$constr->Grid=="" ? "NULL" : $constr->Grid;
                    $GridCount=$constr->GridCount=="" ? "NULL" : $constr->GridCount;
                    $GridSum=$constr->GridSum=="" ? "NULL" : $constr->GridSum;

					//echo "INSERT INTO payrollconstruct (DoorType, Step, Frame, FrameCount, FrameSum, Dovod, DovodPreparation, DovodSum, Nalichnik, NalichnikSum, Window, WindowCount, WindowMore, WindowSum, Framuga, FramugaSum, Petlya, PetlyaCount, PetlyaMore, PetlyaSum, Stiffener, StiffenerW, StiffenerSum, M2, M2Sum, Antipanik, AntipanikSum, Otboynik, OtboynikSum, Wicket, WicketSum, BoxLock, BoxLockSum, Otvetka, OtvetkaSum, PetlyaWork, PetlyaWorkCount, PetlyaWorkMore, PetlyaWorkSum, Isolation, IsolationSum, PetlyaStvorka, PetlyaStvorkaCount, PetlyaStvorkaMore, PetlyaStvorkaSum, Grid, GridCount, GridSum) VALUES ('$DoorType', '$Step', $Frame, $FrameCount, $FrameSum, $Dovod, $DovodPreparation, $DovodSum, $Nalichnik, $NalichnikSum, $Window, $WindowCount, $WindowMore, $WindowSum, $Framuga, $FrameSum, $Petlya, $PetlyaCount, $PetlyaMore, $PetlyaSum, $Stiffener, $StiffenerW, $StiffenerSum, $M2, $M2Sum, $Antipanik, $AntipanikSum, $Otboynik, $OtboynikSum, $Wicket, $WicketSum, $BoxLock, $BoxLockSum, $Otvetka, $OtvetkaSum, $PetlyaWork, $PetlyaWorkCount, $PetlyaWorkMore, $PetlyaWorkSum, $Isolation, $IsolationSum, $PetlyaStvorka, $PetlyaStvorkaCount, $PetlyaStvorkaMore, $PetlyaStvorkaSum, $Grid, $GridCount, $GridSum)\n";
                    $m->query("INSERT INTO payrollconstruct (DoorType, Step, Frame, FrameCount, FrameSum, Dovod, DovodPreparation, DovodSum, Nalichnik, NalichnikSum, Window, WindowCount, WindowMore, WindowSum, Framuga, FramugaSum, Petlya, PetlyaCount, PetlyaMore, PetlyaSum, Stiffener, StiffenerW, StiffenerSum, M2, M2Sum, Antipanik, AntipanikSum, Otboynik, OtboynikSum, Wicket, WicketSum, BoxLock, BoxLockSum, Otvetka, OtvetkaSum, PetlyaWork, PetlyaWorkCount, PetlyaWorkMore, PetlyaWorkSum, Isolation, IsolationSum, PetlyaStvorka, PetlyaStvorkaCount, PetlyaStvorkaMore, PetlyaStvorkaSum, Grid, GridCount, GridSum) VALUES ('$DoorType', '$Step', $Frame, $FrameCount, $FrameSum, $Dovod, $DovodPreparation, $DovodSum, $Nalichnik, $NalichnikSum, $Window, $WindowCount, $WindowMore, $WindowSum, $Framuga, $FrameSum, $Petlya, $PetlyaCount, $PetlyaMore, $PetlyaSum, $Stiffener, $StiffenerW, $StiffenerSum, $M2, $M2Sum, $Antipanik, $AntipanikSum, $Otboynik, $OtboynikSum, $Wicket, $WicketSum, $BoxLock, $BoxLockSum, $Otvetka, $OtvetkaSum, $PetlyaWork, $PetlyaWorkCount, $PetlyaWorkMore, $PetlyaWorkSum, $Isolation, $IsolationSum, $PetlyaStvorka, $PetlyaStvorkaCount, $PetlyaStvorkaMore, $PetlyaStvorkaSum, $Grid, $GridCount, $GridSum)") or die($m->error);
                };
                $result=array(
                    "Status"=>"Success",
                    "Note"=>""
                );
                break;
            case false:
                $result=array(
                    "Status"=>"Error",
                    "Note"=>"Не соответствуют типы дверей"
                );
                break;
        };
    };
};

echo json_encode($result);
?>