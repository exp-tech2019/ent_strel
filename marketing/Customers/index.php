<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 18.12.2016
 * Time: 9:41
 */
?>
<div class="panel panel-default">
    <div class="panel-body">
        <button type="button" class="btn btn-primary" onclick="window.location.href='index.php?MVCPage=CustomerAddEdit'">Добавить</button>
        <input style="width: 300px; float:right; border: none;" placeholder="Поиск" id="FinDInp" oninput="FindTR()">
    </div>
</div>
<table class='table table-striped table-condensed table-hover'>
    <tr>
    <tr>
        <td></td>
        <td>Наименование</td>
        <td>ИНН</td>
    </tr>
    </thead>
    <tbody id="CustomerTable">
<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 18.12.2016
 * Time: 9:41
 */
    $d=$m->query("SELECT * FROM Customers Order BY Name");
    if($d->num_rows>0)
        while($r=$d->fetch_assoc())
        {
          ?>
            <tr idCustomer="<?php echo $r["id"]; ?>">
                <td><span onclick="Remove(this)" class="glyphicon glyphicon-remove-circle"></span> </td>
                <td onclick="Edit(this)"><?php echo $r["Name"]; ?></td>
                <td onclick="Edit(this)"><?php echo $r["INN"]; ?></td>
            </tr>
            <?php
        };
?>
    </tbody>
</table>
<script>
    function Edit(el){
        var idCustomer=$(el).parent().attr("idCustomer");
        window.location.href="index.php?MVCPage=CustomerAddEdit&idCustomer="+idCustomer;
    }
    function Remove(el){
        var idCustomer=$(el).parent().parent().attr("idCustomer");
        var Name=$(el).parent().parent().find("td:eq(0)").text();
        if(confirm("Удалить: "+Name+"?"))
            window.location.href="index.php?MVCPage=CustomerRemove&idCustomer="+idCustomer;
    }
    function FindTR() {
        var FindText=$("#FinDInp").val().toLowerCase();
        var TRLen=$("#CustomerTable tr").length;
        for (var i=0; i<TRLen;i++)
        {
            var TR=$("#CustomerTable tr:eq("+i+")");
            if(TR.find("td:eq(0)").text().toLowerCase().indexOf(FindText)>-1 || TR.find("td:eq(1)").text().toLowerCase().indexOf(FindText)>-1)
            {
                TR.show();
            }
            else
                TR.hide();
        };
    }
</script>