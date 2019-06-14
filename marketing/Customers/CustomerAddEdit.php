<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 18.12.2016
 * Time: 10:10
 */
    $Name=""; $INN=""; $idCustomer="";
    if(isset($_GET["idCustomer"]))
    {
        $idCustomer=$_GET["idCustomer"];
        $d=$m->query("SELECT * FROM Customers WHERE id=$idCustomer");
        $r=$d->fetch_assoc();
        $Name=$r["Name"]; $INN=$r["INN"];
        $d->close();
    };
?>
<form role="form" method="post" action="index.php">
    <input type="hidden" name="MVC" value="CustomerSave">
    <input type="hidden" name="idCustomer" value="<?php echo $idCustomer; ?>">
    <div class="form-group">
        <label for="exampleInputEmail1">Наименование</label>
        <input type="text" class="form-control" placeholder="Наименование" name="Name" value="<?php echo $Name; ?>">
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">ИНН</label>
        <input type="text" class="form-control" placeholder="ИНН" name="INN"  value="<?php echo $INN; ?>">
    </div>
    <button type="submit" class="btn btn-default">Сохранить</button>
</form>
