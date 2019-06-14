<?php

/**
 * Класс списка справочников
 * User: xasya
 * Date: 03.12.2016
 * Time: 8:28
 */
class GlobalManuals
{
    var $DovodList;
    var $Nalichnik;
    var $OpenDoor;
    var $TypeDoor;
    function GlobalManuals($m){
        //Доводчик
        $d=$m->query("SELECT * FROM manualdovoddoor ORDER BY Name");
        $i=0;
        if($d->num_rows>0)
            while($r=$d->fetch_assoc()){
                $this->DovodList[$i]=$r["Name"];
                $i++;
            };
        $d->close();
        //Наличник
        $d=$m->query("SELECT * FROM manualnalichnikdoor ORDER BY Name");
        $i=0;
        if($d->num_rows>0)
            while($r=$d->fetch_assoc()){
                $this->Nalichnik[$i]=$r["Name"];
                $i++;
            };
        $d->close();
        //Открывание
        $d=$m->query("SELECT * FROM manualopendoor ORDER BY Name");
        $i=0;
        if($d->num_rows>0)
            while($r=$d->fetch_assoc()){
                $this->OpenDoor[$i]=$r["Name"];
                $i++;
            };
        $d->close();
        //Наименование двери
        $d=$m->query("SELECT * FROM manualtypedoors ORDER BY Name");
        $i=0;
        if($d->num_rows>0)
            while($r=$d->fetch_assoc()){
                $this->TypeDoor[$i]=$r["Name"];
                $i++;
            };
        $d->close();
    }
}
?>