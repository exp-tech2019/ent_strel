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
$d=$m->query("SELECT od.idOrder, n.idDoors, nc.idNaryad, o.Shet, DATE_FORMAT(o.ShetDate,'%d.%m.%Y') AS ShetDate, od.NumPP, od.name, IF(od.S IS NOT NULL, 2, IF(od.SEqual=1, 2, 1)) AS S, od.H, od.W, n.NumInOrder, CONCAT(n.Num, n.NumPP) AS NaryadNum, COUNT(*) AS NaryadCount, SUM(nc.Cost) AS Cost FROM oreders o, orderdoors od, naryad n, naryadcomplite nc 
WHERE o.id=od.idOrder AND od.id=n.idDoors AND n.id=nc.idNaryad AND nc.idWorker=$idWorker AND nc.DateComplite BETWEEN STR_TO_DATE('$DateWith','%d.%m.%Y') AND DATE_ADD(STR_TO_DATE('$DateBy','%d.%m.%Y'), INTERVAL 1 DAY)
GROUP BY n.id
ORDER BY o.Shet, od.NumPP, n.NumPP") or die($m->error);
$arr=array();
$idOrder=-1;
$idDoor=-1;
if($d->num_rows>0)
    while ($r=$d->fetch_assoc())
    {
        switch($r["idOrder"]!=$idOrder)
        {
            case true:
                $arr[]=array(
                    "idOrder"=>$r["idOrder"],
                    "Shet"=>$r["Shet"],
                    "ShetDate"=>$r["ShetDate"],
                    "DoorCount"=>(int)$r["NaryadCount"],
                    "Cost"=>(float)$r["Cost"],
                    "Doors"=>array(
                            array(
                                "NumPP"=>$r["NumPP"],
                                "Name"=>$r["name"],
                                "H"=>$r["H"],
                                "W"=>$r["W"],
                                "S"=>$r["S"],
                                "DoorCount"=>$r["NaryadCount"],
                                "Cost"=>$r["Cost"],
                                "Naryads"=>array(
                                    array(
                                        "NumInOrder"=>$r["NumInOrder"],
                                        "NaryadNum"=>$r["NaryadNum"],
                                        "NaryadCount"=>$r["NaryadCount"],
                                        "Cost"=>$r["Cost"]
                                    )
                                )
                        )
                    )
                );
                $idOrder=$r["idOrder"];
                $idDoor=$r["idDoors"];
                break;
            case false:
                $o=&$arr[count($arr)-1];
                $o["DoorCount"]+=(int)$r["NaryadCount"];
                $o["Cost"]+=(float)$r["Cost"];
                switch ($idDoor!=$r["idDoors"]){
                    case true:
                        $o["Doors"][]=array(
                            "NumPP"=>$r["NumPP"],
                            "Name"=>$r["name"],
                            "H"=>$r["H"],
                            "W"=>$r["W"],
                            "S"=>$r["S"],
                            "DoorCount"=>$r["NaryadCount"],
                            "Cost"=>$r["Cost"],
                            "Naryads"=>array(
                                array(
                                    "NumInOrder"=>$r["NumInOrder"],
                                    "NaryadNum"=>$r["NaryadNum"],
                                    "NaryadCount"=>$r["NaryadCount"],
                                    "Cost"=>$r["Cost"]
                                )
                            )
                        );
                        $idDoor=$r["idDoors"];
                        break;
                    case false:
                        $od=&$o["Doors"][count($o["Doors"])-1];
                        $od["DoorCount"]+=(int)$r["NaryadCount"];
                        $od["Cost"]+=(float)$r["Cost"];
                        $od["Naryads"][]=array(
                            "NumInOrder"=>$r["NumInOrder"],
                            "NaryadNum"=>$r["NaryadNum"],
                            "NaryadCount"=>$r["NaryadCount"],
                            "Cost"=>$r["Cost"]
                        );
                        break;
                };

                break;
        };
    };
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
$(function(){
	// Initialize the tree inside the <div>element.
	// The tree structure is read from the contained <ul> tag.
	$("#tree").fancytree({
		checkbox: false
	});
});
</script>


</head>
<body class="example">
	<div id="tree" style="width: 100%">
		<ul>
            <?php
            foreach ($arr as $o1){ ?>
                <li class="">
                    Счет - <strong><?php echo $o1["Shet"] ?></strong> Кол-во: <?php echo $o1["DoorCount"]; ?> Сумма - <?php echo $o1["Cost"] ?>
                    <ul>
                        <?php
                        foreach ($o1["Doors"] as $od1){ ?>
                            <li>
                                <?php echo $od1["Name"]; ?> Кол-во <strong><?php echo $od1["DoorCount"]; ?></strong> Сумма - <strong><?php echo $od1["Cost"]; ?></strong>
                                <ul>
                                    <?php
                                    foreach ($od1["Naryads"] as $n){ ?>
                                        <li>Дверь - <strong><?php echo $n["NumInOrder"]; ?></strong> Наряд - <strong><?php echo $n["NaryadNum"]; ?></strong> Сумма - <strong><?php echo $n["Cost"] ?></strong></li>
                                    <?php };
                                    ?>
                                </ul>
                            </li>
                        <?php }
                        ?>
                    </ul>
                </li>
            <?php };
            ?>
		</ul>
	</div>
</body>
</html>
