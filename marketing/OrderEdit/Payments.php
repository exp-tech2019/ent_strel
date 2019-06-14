<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 19.01.2017
 * Time: 20:48
 */

?>
<button class="btn btn-primary" onclick="PaymentAdd()">Добавить оплату</button>
<table class="table table-responsive">
    <thead>
        <tr>
            <th></th>
            <th>Дата</th>
            <th>Тип</th>
            <th>Сумма</th>
            <th>Примечание</th>
        </tr>
    </thead>
    <tbody id="PaymentsTable">
    <?php
        $d=$m->query("SELECT *, DATE_FORMAT(DatePayment, '%d.%m.%Y') AS DatePaymentFormat FROM TempOrderPayments WHERE idOrder=$idOrder");
        if($d->num_rows>0)
            while($r=$d->fetch_assoc()){ ?>
                <tr idPayment="<?php echo $r["id"]; ?>" Status="Load">
                    <td><span onclick="PaymentRemove(this)" class="glyphicon glyphicon-remove-circle"></span></td>
                    <td Type="Date"><input oninput='PaymentEdit(this)' onchange='PaymentEdit(this)' class="form-control" value="<?php echo $r["DatePaymentFormat"]; ?>"></td>
                    <td Type="Type">
                        <select onchange='PaymentEdit(this)' class="form-control">
                            <option <?php echo $r["TypePayment"]==0 ? "selected" : ""; ?>>Платеж</option>
                            <option <?php echo $r["TypePayment"]==1 ? "selected" : ""; ?>>Гарантийное письмо</option>
                        </select>
                    </td>
                    <td Type="Sum"><input oninput='PaymentEdit(this)' class="form-control" value="<?php echo $r["SumPayment"]; ?>"></td>
                    <td Type="Note"><input oninput='PaymentEdit(this)' class="form-control" value="<?php echo $r["Note"]; ?>"></td>
                </tr>
            <?php }
    ?>
    </tbody>
</table>
<script>
    $(document).ready(function(){
        $("#PaymentsTable tr td[Type=Date]").datetimepicker(
            {pickTime: false,language: 'ru'}
        );
        for(var i=0; i<$("#PaymentsTable tr").length; i++)
            $("#PaymentsTable tr:eq("+i+")").attr("PaymentGuid",guid());
    })
    function PaymentAdd(){
        $("#PaymentsTable").append(
            "<tr idPayment='' Status='Add' PaymentGuid='"+guid()+"'>"+
                "<td><span onclick='PaymentRemove(this)' class='glyphicon glyphicon-remove-circle'></span></td>"+
                "<td Type='Date'><input oninput='PaymentEdit(this)' class='form-control' value=''></td>"+
                "<td Type='Type'>"+
                    "<select onchange='PaymentEdit(this)' class='form-control'><option>Платеж</option><option>Гарантийное письмо</option></select> "+
                "</td>"+
                "<td Type='Sum'><input oninput='PaymentEdit(this)' class='form-control' value=''></td>"+
                "<td Type='Note'><input oninput='PaymentEdit(this)' class='form-control' value=''></td>"+
            "</tr>"
        );
        $("#PaymentsTable tr td[Type=Date]").datetimepicker(
            {pickTime: false,language: 'ru'}
        );
    }
    function PaymentEdit(el){
        if($(el).parent().parent().attr("Status")=="Load") $(el).parent().parent().attr("Status", "Edit");
        SumChange();
    }
    function PaymentRemove(el){
        if(confirm("Удалить оплату?"))
        switch($(el).parent().parent().attr("Status")){
            case "Add": $(el).parent().parent().remove(); break;
            default : $(el).parent().parent().attr("Status","Remove"); $(el).parent().parent().hide(); break;
        }
    }
</script>
