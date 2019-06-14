
		<script src="function.js"></script>
		<h1>Сгибка <span>Вы вошли как 
			<?php echo $_SESSION["FIOSgibka"] ?>
		</span> <button id=ButtonCloseSession>Выход</button>
		</h1>
		<script> 
			$("#ButtonCloseSession").button();
			$("#ButtonCloseSession").click(
				function() {document.location="authClose.php";}
			);
		</script>
		<img style="position:absolute; right:0; left:0; margin-left:auto; margin-right:auto;" src="images/loader.gif" id=LoaderImg>
		<script> $("#LoaderImg").hide();</script>
		
        <div style="float:left; " id="Accordion">
		    <h3>&nbsp;</h3>
            <div>
                <table class=OrderTable>
					<thead>
						<tr>
							<td>Заказ</td>
							<td>Дата</td>
							<td>Кол-во</td>
						</tr>
					</thead>
					<tbody id=WorkTable></tbody>
				</table>
            </div>
        </div>
		<div style="float:left; margin-left:40px;" id="AccordionNaryad">
		    <h3>&nbsp;</h3>
            <div>
                <table class=OrderTable>
					<thead style='text-align:center'>
						<tr>
							<td>№ наряда</td>
							<td>Наименование</td>
							<td>Высота</td>
							<td>Ширина</td>
							<td>Выполнил</td>
							<td>Время</td>
							<td></td>
						</tr>
					</thead>
					<tbody id=NaryadTableTbody>
					</tbody>
				</table>
            </div>
        </div>
        <script>
            $('#Accordion').accordion({heightStyle: "content"});
			$('#AccordionNaryad').accordion({heightStyle: "content"});
			Select();
        </script>
		<!--Диалог выбора сотрудника согнувшего дверь-->
		<div id=DialogWorkerOnline title='Сгибщики'>
			<p>
				<select id=WorkerOnline size=6 style="width:100%"></select>
				<p style="display:none"  id=DialogDoorID></p>
			</p>
		</div>
		<script>
			NaryadSelect();
		
			$( "#DialogWorkerOnline" ).dialog({
				autoOpen: false,
				modal:true,
				width: 600,
				buttons: [
					{
						text: "Выполнить",
						click: function() {
							AddNaraydStep2();
						}
					},
					{
						text: "Отмена",
						click: function() {	$( this ).dialog( "close" );	}
					}
				]
			});
		</script>
