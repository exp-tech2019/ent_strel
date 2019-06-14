<script src="PageMain/index.js"></script>
<?php
/**
 * Created by PhpStorm.
 * User: anikulshin
 * Date: 26.01.2017
 * Time: 16:13
 */
?>
<div class="panel">
    <div class="panel-body">
        <button onclick="AddAct()" class="btn btn-primary">Добавить Расчет</button>
    </div>
</div>
<table class="table table-hover table-bordered ">
    <thead>
        <tr>
            <th>№ Акта</th>
            <th>Дата</th>
            <th>Начисленно</th>
            <th>Выплаченно</th>
            <th>Осталось выплатить</th>
            <th>Статус</th>
        </tr>
    </thead>
    <tbody id="TableMain">
        <?php
        $d=$m->query("SELECT TAct.id, DATE_FORMAT(TAct.DateCreate, '%d.%m.%Y') AS DateCreate, TAct.Status, SUM(TPayments.SumPlus) AS SumPlus, SUM(TPayments.SumMinus) AS SumMinus, SUM(TPayments.SumItog) AS SumItog FROM temppayrolls TAct
LEFT JOIN
	(SELECT 
		l.idAct,
		l.idWorker,
		l.SumWith+
		(l.Cost+l.SumPlus+SUM(COALESCE(p.SumPlus,0)))-
		(l.Cost+l.SumPlus+SUM(COALESCE(p.SumPlus,0))) *l.NalogPercent/100 AS SumPlus,
		SUM(COALESCE(p.SumMinus,0))+
		-1*l.SumMinus AS SumMinus,
		l.SumWith+
		(l.Cost+l.SumPlus+SUM(COALESCE(p.SumPlus,0)))-
		(l.Cost+l.SumPlus+SUM(COALESCE(p.SumPlus,0))) *l.NalogPercent/100-
		(
			SUM(COALESCE(p.SumMinus,0))+
			-1*l.SumMinus
		) AS SumItog
	FROM TempPayrollsList l
	LEFT JOIN TempPayrollsPayments p
	ON l.idWorker=p.idWorker AND l.idAct=p.idAct
	GROUP BY l.idAct, l.idWorker) TPayments
ON TAct.id=TPayments.idAct
GROUP BY TAct.id DESC");
            if($d->num_rows>0)
                while($r=$d->fetch_assoc()){ ?>
                    <tr idAct="<?php echo $r["id"]; ?>" class="<?php echo $r["Status"]==0 ? "info" : "success" ?>" Status="<?php echo $r["Status"]; ?>" onclick="OpenAct(this)">
                        <td Type="idAct"><?php echo $r["id"]; ?></td>
                        <td Type="DateCreate"><?php echo $r["DateCreate"]; ?></td>
                        <td Type="SumPlus"><?php echo $r["SumPlus"]; ?></td>
                        <td Type="SumMinus"><?php echo (float)$r["SumMinus"]; ?></td>
                        <td Type="Difference"><?php echo (float) $r["SumItog"]; ?></td>
                        <td Type="Status"><?php echo $r["Status"]==0 ? "Формируется" : "Завершено"; ?></td>
                    </tr>
                <?php }
        ?>
    </tbody>
</table>
