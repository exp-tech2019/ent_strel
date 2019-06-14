function Select()
{
	$("#LoaderImg").show();
	$.post(
		'function.php',
		{'Method':'Select'},
		function(data)
		{
			$("#LoaderImg").hide();
			$('#WorkTable').find('tr').remove();
			$('#WorkTable').last().append(data);
		}
	);
	
	setTimeout(Select,120000)//каждые 2 мин
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

function AddNarayd(id)
{
	$.post(
		'function.php',
		{'Method':'AddNaryad', 'id':id},
		function(data)
		{
			if($('#DoorTr'+id).parent().find('tr').length==2)
			{
				var idO=$('#DoorTr'+id).parent().parent().parent().attr('id');
				idO=idO.replace(new RegExp("OrderTR",'g'),"");
				$('#OrderTR'+idO).remove();
				$('#OrderTRHeader'+idO).remove();
			}
			else
				$('#DoorTr'+id).remove();
			NaryadSelect();
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
			//alert(data);
			$('#NaryadTableTR'+id).remove();
			Select();
		}
	);
}

