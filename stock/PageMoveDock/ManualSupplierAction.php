<?php
$Result=array();
if(isset($_POST["Action"]))
{
    include "../params.php";
    $m=new mysqli($GlobalDBHost,$GlobalDBLogin,$GlobalDBPass,$GlobalDBName);
    $idSupplier=$_POST["idSupplier"];
    $Name=$_POST["Name"];
    $INN=$_POST["INN"];
    switch ($_POST["Action"]){
        case "Add":
            $m->query("INSERT INTO StockNewSupplier (Name, INN) VALUES('$Name', '$INN')");
            $Result["Result"]="ok";
            $Result["id"]=$m->insert_id;
            break;
        case "Edit":
            $m->query("UPDATE StockNewSupplier SET Name='$Name', INN='$INN' WHERE id=$idSupplier");
            $Result["Result"]="ok";
            break;
    };
};
echo json_encode($Result);
?>