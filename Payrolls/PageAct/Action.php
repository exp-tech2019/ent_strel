<?php
    session_start();
    include "../params.php";
    $m=new mysqli($GlobalDBHost,$GlobalDBLogin,$GlobalDBPass,$GlobalDBName);
    if(isset($_POST["Method"]))
        switch($_POST["Method"]){
            case "LoadFirstList":
                $DateWith=date("01.m.Y");
                $DateBy=date("t.m.Y");
                $d=$m->query("CALL SelectPayments3('$DateWith', '$DateBy')");
                $arr=array(); $i=0;
                if($d->num_rows>0)
                    while($r=$d->fetch_assoc()){
                        $arr[$i]=array(
                            "idWorker"=>$r["idWorker"],
                            "FIO"=>$r["FIO"],
                            "Dolgnost"=>$r["Dolgnost"],
                            "SumWith"=>$r["SumWith"],
                            "Cost"=>$r["Cost"],
                            "SumPlus"=>$r["SumPlus"],
                            "SumMinus"=>$r["SumMinus"]
                        );
                        $i++;
                    };
                echo json_encode($arr);
                break;
            case "MinusSave":
                $idAct=$_POST["idAct"];
                $idWorker=$_POST["idWorker"];
                $SumMinus=$_POST["Sum"];
                $Note=$_POST["Note"];
                $Manager=$_SESSION["AuthorizeID"];
                $m->query("INSERT INTO TempPayrollsPayments (idAct, idWorker, DateCreate, SumMinus, Note, Manager) VALUES ($idAct, $idWorker, Now(), $SumMinus, '$Note', $Manager)");
                break;
            case "PlusSave":
                $idAct=$_POST["idAct"];
                $idWorker=$_POST["idWorker"];
                $SumPlus=$_POST["Sum"];
                $Note=$_POST["Note"];
                $Manager=$_SESSION["AuthorizeID"];
                $m->query("INSERT INTO TempPayrollsPayments (idAct, idWorker, DateCreate, SumPlus, Note, Manager) VALUES ($idAct, $idWorker, Now(), $SumPlus, '$Note', $Manager)");
                break;
            case "DeleteAct":
                $m->autocommit(false);
                $idAct=$_POST["idAct"];
                $m->query("DELETE FROM TempPayrolls WHERE id=$idAct") or die($m->error);
                $m->query("DELETE FROM TempPayrollsList WHERE idAct=$idAct") or die($m->error);
                $m->query("DELETE FROM TempPayrollsPayments WHERE idAct=$idAct") or die($m->error);
                $m->commit();
                break;
            case "CalcComplite":
                $idAct=$_POST["idAct"];
                $m->autocommit(false);/*
                $d=$m->query("SELECT FIO FROM Logins WHERE id=".$_SESSION["AuthorizeID"]);
                $r=$d->fetch_assoc();
                $Accountant=$r["FIO"];*/
                //$m->query("INSERT INTO paymentsworkers (idWorker, DatePayment, Sum, Note, Accountant) SELECT idWorker, Now(), -1*SumMinus, 'Зарплата', '$Accountant' FROM TempPayrollsPayments WHERE idAct=$idAct AND SumMinus>0") or die($m->error);
                //$m->query("INSERT INTO paymentsworkers (idWorker, DatePayment, Sum, Note, Accountant) SELECT idWorker, Now(), SumPlus, 'Зарплата', '$Accountant' FROM TempPayrollsPayments WHERE idAct=$idAct AND SumPlus>0") or die($m->error);
                $m->query("UPDATE TempPayrollsList AS pUpd
	INNER JOIN
    (
		SELECT 
			l.idAct,
			l.idWorker,
			l.SumWith+
			(l.Cost+l.SumPlus+SUM(COALESCE(p.SumPlus,0)))-
			(l.Cost+l.SumPlus+SUM(COALESCE(p.SumPlus,0))) *l.NalogPercent/100-
			(
				SUM(COALESCE(p.SumMinus,0))+
				-1*l.SumMinus
			) AS SumItog1
		FROM TempPayrollsList l
		LEFT JOIN TempPayrollsPayments p
		ON l.idWorker=p.idWorker AND l.idAct=p.idAct
		WHERE l.idAct=$idAct
		GROUP BY l.idWorker
	) AS tMain
	USING(idWorker, idAct)
SET pUpd.SumItog=tMain.SumItog1") or die($m->error);

                $m->query("UPDATE TempPayrolls SET Status=1 WHERE id=$idAct") or die($m->error);
                $m->commit();
                break;
        }
?>