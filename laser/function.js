function Select()
{
	$.post(
		'function.php',
		{'Method':'Select'},
		function(data)
		{
			$('#Table').find('tr').remove();
			$('#Table').last().append(data);
		}
	);
}

function SelectDoors(idOrder)
{
	switch($('#OrderTR'+idOrder).css('display'))
	{
		case 'none':
			$.post(
				'function.php',
				{'Method':'SelectDoors' , 'id':idOrder},
				function(data)
				{
					$('#OrderTR'+idOrder).find('table').remove();
					$('#OrderTR'+idOrder).show();
					$('#OrderTR'+idOrder).append(data);
				}
			);
		break;
		case 'table-cell': $('#OrderTR'+idOrder).hide();
		break;
	};
}

function AddNaryadAll(id, el){
	var TR=$(el).parent().parent();
	var All=parseInt(TR.find("td[Type=All]").text());
	var Complite=parseInt(TR.find("td[Type=Complite]").text());
	if(All-Complite>0 & All-Complite!=0) {
		TR.find("input").val(All - Complite);
		AddNarayd(id);
	};
}

function AddNarayd(id)
{
	$.post(
		'function.php',
		{'Method':'AddNaryad' , 'id':id , 'Count':$('#InpDoor'+id).val()},
		function(data)
		{
			if(data=="ok")
			{
				NaryadSelect();
				var DoorTRCount=$('#DoorTDComplite'+id).parent().parent().find('tr').length;
				var OrderDelID=$('#DoorTDComplite'+id).parent().parent().parent().parent().attr('id');
				var flagDelete=false;
				
				$('#'+OrderDelID.replace(new RegExp("OrderTR",'g'),"OrderDoorComplite")).text(parseInt($('#'+OrderDelID.replace(new RegExp("OrderTR",'g'),"OrderDoorComplite")).text())+parseInt($('#InpDoor'+id).val()));
				
				$('#DoorTDComplite'+id).text(parseInt($('#DoorTDComplite'+id).text())+parseInt($('#InpDoor'+id).val()));
				if(parseInt($('#DoorTDCount'+id).text())-parseInt($('#DoorTDComplite'+id).text())==0)
				{	
					$('#DoorTDComplite'+id).parent().remove();
					flagDelete=true;
				};
				if(DoorTRCount==2 & flagDelete)
				{
					$('#'+OrderDelID).remove();
					$('#'+OrderDelID.replace(new RegExp("OrderTR",'g'),"OrderTRHeader")).remove();
				};
			}
			else
				alert(data);
		}
	);
}

function NaryadSelect()
{
	$.post(
		'function.php',
		{'Method':'NaryadSelect'},
		function(data)
		{
			$('#NaryadTableTbody').find('tr').remove();;
			$('#NaryadTableTbody').append(data);
		}
	);
}

function NaryadDelete(id)
{
	if(confirm('Произвести удаление выполненого заказа?'))
	$.post(
	'function.php',
		{'Method':'NaryadDelete', 'id':id},
		function(data)
		{
			if(data=="ok")
			{
				$('#NaryadTableTR'+id).remove();
				Select();
			}
			else
				alert(data);
		}
	);
}