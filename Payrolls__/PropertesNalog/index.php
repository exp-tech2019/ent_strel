<script src="PropertesNalog/index.js"></script>
<table class="table table-hover">
    <thead>
        <tr>
            <th>Должность</th>
            <th>Налог</th>
            <th style="min-width: 100px;"></th>
        </tr>
    </thead>
    <tbody id="NalogTable">
    <?php
        $d=$m->query("SELECT * FROM ManualDolgnost ORDER BY Dolgnost");
        if($d->num_rows>0)
            while($r=$d->fetch_assoc()){ ?>
                <tr idDolgnost="<?php echo $r["id"]; ?>">
                    <td Type="Dolgnost"><?php echo $r["Dolgnost"]; ?></td>
                    <td Type="NalogPercent"><input oninput="EditTR(this)" class="form-control" style="width:100px;" value="<?php echo $r["NalogPercent"]; ?>"></td>
                    <td Type="Save"><button onclick="EditSave(this)" class="btn btn-primary">Сохранить</button> </td>
                </tr>
            <?php }

    ?>
    </tbody>
</table>