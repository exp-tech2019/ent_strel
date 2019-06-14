<?php
/**
 * Created by PhpStorm.
 * User: xasya
 * Date: 07.01.2017
 * Time: 15:12
 */
?>


<div class="modal fade" id="CustomersDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Заказчики</h4>
            </div>
            <div class="modal-body">
                <table class="table table-responsive">
                    <thead>
                        <tr>
                            <th>Наименование</th>
                            <th>ИНН</th>
                        </tr>
                    </thead>
                    <tbody id="CustomersTable">
                        <?php
                            $d=$m->query("SELECT * FROM Customers ORDER BY Name");
                            if($d->num_rows)
                                while($r=$d->fetch_assoc()){ ?>
                                    <tr idCustomer="<?php echo $r["id"]; ?>" onclick="CustomerSelect(this)">
                                        <td Type="Name"><?php echo $r["Name"]; ?></td>
                                        <td Type="INN"><?php echo $r["INN"]; ?></td>
                                    </tr>
                                <?php };
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary">Сохранить изменения</button>
            </div>
        </div>
    </div>
</div>

<script>
    function CustomersStart(){
        $("#CustomersDialog").modal("show");
    }
    function CustomerSelect(el){
        $("#OrderCustomerID").val($(el).attr("idCustomer"));
        $("#OrderCustomer").val($(el).find("td[Type=Name]").text());
        $("#CustomersDialog").modal("hide");
    }
</script>