
		<script src="function.js"></script>
		<h1>Лазер <span>Вы вошли как 
			<?php echo $_SESSION["FIOLaser"] ?>
		</span> <button id=ButtonCloseSession>Выход</button>
		</h1>
		<script> 
			$("#ButtonCloseSession").button();
			$("#ButtonCloseSession").click(
				function() {document.location="authClose.php";}
			);
		</script>
        <div style="float:left; " id="Accordion">
		    <h3>Наряды для выполнения</h3>
            <div>
				<button onclick="select()">Обновить</button>
                <table class=OrderTable id=Table></table>
            </div>
        </div>
		<div style="float:left; margin-left:40px;" id="AccordionNaryad">
		    <h3>Выполненные наряды</h3>
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
			NaryadSelect();
        </script>
