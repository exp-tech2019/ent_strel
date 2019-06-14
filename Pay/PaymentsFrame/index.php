<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 14.03.2019
 * Time: 16:22
 */
session_start();
ini_set("max_execution_time", "2000");
$XMLParams=simplexml_load_file("../../params.xml");
$m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB);

$idWorker=$_GET["idWorker"];
$DateWith=$_GET["DateWith"];
$DateBy=$_GET["DateBy"];
//Узнаем дату последднего акта
$d=$m->query("SELECT DATE_FORMAT(MAX(DateCreate),'%d.%m.%Y') AS DateCreate FROM temppayrolls");
$r=$d->fetch_assoc();
$ActDateCreate=$r["DateCreate"];
$arrPayments=array();
$d=$m->query("SELECT id, DATE_FORMAT(DatePayment,'%d.%m.%Y') AS DatePayment, IF(Sum>0,'Plus','Minus') AS Type, Sum, Note, IF(STR_TO_DATE('$ActDateCreate','%d.%m.%Y') > DatePayment, 0, 1) AS CanDelete FROM paymentsworkers WHERE DatePayment BETWEEN STR_TO_DATE('$DateWith','%d.%m.%Y') AND STR_TO_DATE('$DateBy','%d.%m.%Y') AND idWorker=$idWorker") or die($m->error);
if($d->num_rows>0)
    while ($r=$d->fetch_assoc())
        if($r["Sum"]!=0)
            $arrPayments[]=array(
                    "id"=>$r["id"],
                "DatePayment"=>$r["DatePayment"],
                "Type"=>$r["Type"]=="Plus",
                "Sum"=>$r["Sum"],
                "Note"=>$r["Note"],
                "CanDelete"=>$r["CanDelete"]
            );
$d->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
	<title>Ent</title>

	<script src="lib/jquery.js"></script>
	<script src="src/jquery-ui-dependencies/jquery.fancytree.ui-deps.js"></script>

	<link href="src/skin-win8/ui.fancytree.css" rel="stylesheet">
	<script src="src/jquery.fancytree.js"></script>

	<!-- Start_Exclude: This block is not part of the sample code -->
	<link href="lib/prettify.css" rel="stylesheet">
	<script src="lib/prettify.js"></script>
	<!-- End_Exclude -->

<script type="text/javascript">
</script>
<style>
    table{
        border-spacing: 0px;
    }
    th, td{
        border: dotted 1px #c5c5c5;
        padding: 2px;
    }
    th:nth-child(2), td:nth-child(2), th:nth-child(4), td:nth-child(4){
        width: 100px;
    }
    th:nth-child(3), td:nth-child(3){
        width: 400px;
    }
    th:nth-child(5), td:nth-child(5){
        width: 25px;
    }
    img{
        width: 20px;
    }
    img[Delete]{
        cursor: pointer;
    }
</style>
</head>
<body class="example">
    <table>
        <thead>
        <tr>
            <th></th>
            <th>Дата</th>
            <th>Коментарий</th>
            <th>Сумма</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($arrPayments as $p){ ?>
            <tr idPayment="<?php echo $p["id"]; ?>">
                <td><?php echo $p["Type"]==1 ? "+" : "-"; ?></td>
                <td><?php echo $p["DatePayment"]; ?></td>
                <td><?php echo $p["Note"]; ?></td>
                <td><?php echo $p["Sum"]; ?></td>
                <td><?php echo $p["CanDelete"]==0 ? "" : "<img onclick='DeletePayment(this)' Delete src='../../images/delete.png' style='width:20px;'>"; ?></td>
            </tr>
        <?php }; ?>
        </tbody>
    </table>
<script>
    function DeletePayment(el) {
        if(confirm("Удалить выплату?"))
            $.post(
                "../actions/DeletePayment.php",
                {
                    idPayment:$(el).parent().parent().attr("idPayment")
                },
                function (o) {
                    if(o=="")
                        $(el).parent().parent().remove();
                }
            )
    }
</script>
</body>
</html>
