<?php
    $DateWith='01.01.2017';
    $DateBy=date("d.m.Y");
    //Определим дату последнего акта
    $d=$m->query("SELECT DATE_FORMAT(DateCreate, '%d.%m.%Y') AS DateCreate FROM temppayrolls WHERE Status=1 ORDER BY id DESC LIMIT 1");
    if($d->num_rows>0){
        $r=$d->fetch_assoc();
        $DateWith=$r["DateCreate"];
        $DateBy=date("d.m.Y");
    };
	/*
    $DateWith="26.12.2018";
    $DateBy="19.01.2019";
	*/
    //$DateBy="18.01.2019";

    //Создадим акт
    $m->autocommit(false);
    //$m->query("INSERT INTO TempPayrolls (DateCreate, Status) VALUES( DATE(Now()), 0)") or die($m->error);
	$m->query("INSERT INTO TempPayrolls (DateCreate, Status) VALUES( STR_TO_DATE('$DateBy','%d.%m.%Y'), 0)") or die($m->error);
    $idAct=$m->insert_id;
    $d=$m->query("CALL TempPayrollsAddList($idAct, '$DateWith', '$DateBy', $NalogProp);") or die($m->error);
    $m->commit();
    header('Location:index.php?MVCPage=PageAct&idAct='.$idAct);
?>