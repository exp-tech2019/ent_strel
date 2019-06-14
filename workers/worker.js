//Отображение сотрудников онлайн
function WorkerSelectOnline()
{
	$.post(
		'workers/worker.php',
		{ 'Method':'SelectOnline'},
		function (data)
			{ $('#WorkerOnlineList').html(''+data+'');}
	);
	//Таймер каждые 15 мин
	setInterval(
	function()
	{
		$.post(
			'workers/worker.php',
			{ 'Method':'SelectOnline'},
			function (data)
				{ $('#WorkerOnlineList').html(''+data+'');}
		);
	},
	900000
	)
}
//-----------------------------------------------------------

//Изменение статуса сотрудника на производстве: На производстве, Отсутствует
function WorkerDialogOnlineUpdate(id)
{
	if($("#WorkerDialogFiredStatus").text()!=1)//Нельзя менять статус для уволенных сотрудников
		$.post(
			'workers/worker.php',
			{
				'Method':'UpdateDidlayn',
				'id':id,
				'status':$('#WorkerDialogOnlineBtn').text()
			},
			function (data)
			{
				$('#WorkerDialogOnlineBtn').text(data);
				switch(data)
				{
					case 'Отсутствует':$('#WorkerDialogOnlineBtn').css('background-color','lightgray'); break;
					case 'На производстве':$('#WorkerDialogOnlineBtn').css('background-color','lightgreen'); break;
				};
			}
		);
}
//-----------------------------------------------------------------

// Отображение таблицы сотрудников
var WorkerGlobalWhere=" AND w.fired<>1"
function WorkerSelect()
{
	$.post(
		'workers/worker.php',
		{
			'Method':'Select',
			'Where':WorkerGlobalWhere
		},
		function (data)
		{
			$('#WorkerTable').find('tr').remove();
			var o=jQuery.parseJSON(data); var i=0;
			while(o[i]!=null)
			{
				var SmartCardIMG=""; if(o[i].SmartCartNum!="" & o[i].SmartCartNum!=null) SmartCardIMG="<img alt='"+o[i].SmartCartNum+"' src='images/SmartCard.png' width=25>";
				var ColorRowClass="Start"; 
				if(o[i].OnEnterprise!=null) ColorRowClass="Complite";
				if(o[i].Fired==1) ColorRowClass="Cancel";
				$('#WorkerTable').append(
					"<tr class="+ColorRowClass+" id=WorkerTableTR"+o[i].id+" onclick='WorkerTableTREdit("+o[i].id+")'>"+
						"<td>"+o[i].Num+"</td>"+
						"<td>"+o[i].FIO+"</td>"+
						"<td>"+o[i].Dolgnost+"</td>"+
						"<td>"+SmartCardIMG+"</td>"+
					"</tr>"
				);
				i++;
			};
		}
	)
}
//Поиск через панель
function WorkerFindFunct()
{
	var where="";
	if($("#WorkerFind").val()!="") where=where+"AND w.FIO LIKE '%"+$('#WorkerFind').val()+"%'  ";
	if(!$("#WorkerToolBarFiredCh").is(":checked")) where=where+"AND w.fired<>1";
	WorkerGlobalWhere=where;
	WorkerSelect();
}
//-------------------------------------------------------------

//Редактирование карточки сотрудника
function WorkerTableTREdit(id)
{
	$.post(
		'workers/worker.php',
		{
			'Method':'EditStart',
			'id':id
		},
		function (data)
		{
			var obj =jQuery.parseJSON(data);
			$('#WorkerDialogInpNum').val(obj.Num);
			$('#WorkerDialogInpFIO').val(obj.FIO);
			//$('#WorkerDialogInpNum').prop( "disabled", false ); $('#WorkerDialogInpFIO').prop( "disabled", false );
			//if(!obj.flagEditFIO) {$('#WorkerDialogInpNum').prop( "disabled", true ); $('#WorkerDialogInpFIO').prop( "disabled", true ); };
			$("#WorkerDialogInpDolgnost").val(obj.Dolgnost);
			$('#WorkerDialogInpPlacement').val(obj.Placement);
			$('#WorkerDialogInpPhone').val(obj.Phone);
			$('#WorkerDialogInpPhone1').val(obj.Phone1);
			$('#WorkerDialogInpAdress').val(obj.Adress);
			$('#WorkerDialogInpNote').val(obj.Note);
			if(obj.Dolgnost=="ст. Гибщик" || obj.Dolgnost=="Гибщик" || obj.Dolgnost=="Оператор лазера" || obj.Dolgnost=="Мастер" || obj.Dolgnost=="Старшый инженер")
				$("#WorkerDialogInpAuthPass").show()
			else
				$("#WorkerDialogInpAuthPass").hide();
			$("#WorkerDialogInpAuthPass").val(obj.AuthPass);
			$("#WorkerDialogFiredStatus").text(obj.Fired);
			$("#inputWorkerDialogInpRfidInfo").text(obj.SmartCartNum);
			//Отображаем статус сотрудника
			switch(obj.StatusOnline)
			{
				case 'Отсутствует':$('#WorkerDialogOnlineBtn').css('background-color','lightgray'); $('#WorkerDialogOnlineBtn').text('Отсутствует'); break;
				case 'На производстве':$('#WorkerDialogOnlineBtn').css('background-color','lightgreen'); $('#WorkerDialogOnlineBtn').text('На производстве'); break;
			};
			if(obj.Fired==1){ $('#WorkerDialogOnlineBtn').css('background-color','lightgray'); $('#WorkerDialogOnlineBtn').text('Уволен'); };
			
			WorkDialogAddEditStatus='Edit';
			$("#WorkerDialogInpID").val(id);
			WorkDialogID=id;
			WorkerDialogLoad(obj.Dolgnost);
		}
	);
}
//-----------------------------------------------------------------------------------

// Подготовка к отображение диалога - Карточка сотрудника
function WorkerDialogLoad(SelectVal)
{
	$("#WorkerDialogBugs1").html("");
	$.post(
		'workers/worker.php',
		{
			'Method':'SelectDolgnost'
		},
		function(data)
		{
			$('#WorkerDialogInpDolgonst').find('option').remove();
			$('#WorkerDialogInpDolgonst').append(data);
			$('#WorkerDialogInpDolgonst').val(SelectVal);
			if(WorkDialogAddEditStatus=='Add')
			{
				//Максимальный +1 идентификационный номер
				$.post(
					'workers/worker.php',
					{
						'Method':'WorkerMaxNum'
					},
					function(data)
					{$("#WorkerDialogInpNum").val(data);}
				)
				
				$("#WorkerDialogInpID").val("");
				$('#WorkerDialogInpFIO').val('');
				$('#WorkerDialogInpDolgonst').val('');
				$('#WorkerDialogInpPlacement').val('');
				$('#WorkerDialogInpPhone').val("");
				$('#WorkerDialogInpPhone1').val("");
				$('#WorkerDialogInpAdress').val("");
				$('#WorkerDialogInpNote').val("");
				$('#inputWorkerDialogInpRfidInfo').text("");
				$('#WorkerDialogInpAuthPass').val("");
				$('#WorkerDialogInpAuthPass').hide("");
				$('#WorkerDialogOnlineBtn').hide("");
			}
			else
			{
				$('#WorkerDialogOnlineBtn').show();
			};
		}
	)
	$('#WorkerDialog').dialog('open');
}
//------------------------------------------------------------------------------------

// Удаление сотрудника
function WorkerRemove(id)
{
	if(id!=0)
		if(confirm('Произвести удаление сотрудника?'))
		$.post(
		'workers/worker.php',
		{
			'Method':'Remove',
			'id':id
		},
		function (data)	{ if(data=='ok') { $('#WorkerTableTR'+id).remove(); $('#WorkerDialog').dialog('close'); } }
		);
};
//-----------------------------------------------------------------------------------

// Уволить сотрудника - утратила силу
function WorkerFiredDialogStart()
{
	$("#WorkerFireDialog").find("tr").remove();
	$.post(
		"workers/worker.php",
		{"Method":"FireNotFireSelect"},
		function(data)
		{
			var o=jQuery.parseJSON(data); var i=0;
			while(o[i]!=null) {$("#WorkerFireDialog").append("<tr id="+o[i].id+"><td>"+o[i].Num+"</td><td>"+o[i].FIO+"</td><td>"+o[i].Dolgnost+"</td></tr>"); i++;};
			$("#WorkerFireDialog").dialog("open");
		}
	);
}
//-----------------------------------------------------------------------------------

//Уволить/Восстановить сотрудника
function WorkerFiredEdit()
{
	if($("#WorkerDialogInpID").val()!="")
	{
		var str="Уволить"; if($("#WorkerDialogFiredStatus").text()=="1") str="Восстановить на работу";
		if(confirm(str+" сотрудника"))
			$.post(
				"workers/worker.php",
				{"Method":"FireEdit","id":$("#WorkerDialogInpID").val(),"Fired":$("#WorkerDialogFiredStatus").text()},
				function(data)
				{
					if(data=="ok")
					{
						$('#WorkerDialogOnlineBtn').css('background-color','lightgray');
						if($("#WorkerDialogFiredStatus").text()=="1")
						{
							$('#WorkerDialogOnlineBtn').text('Отсутствует');
						}
						else
							 $('#WorkerDialogOnlineBtn').text('Уволен');
					}
					else alert(data);
				}
			);
	};	
	//$("#WorkerDialogInpID").val(id)
}

//Назначение rfid
function WorkerRfidEditStart()
{
	//$("#WorkerDialogRfidInp").val($("#inputWorkerDialogInpRfidInfo").text());
	$("#WorkerDialogRfidInp").val("");
	$("#WorkerDialogRfidErr").text("");
	$("#WorkerDialogRfid").dialog("open");
}
//Нажимаем Назначить
function WorkerRfidEditSave()
{
	$.post(
		"workers/worker.php",
		{"Method":"RfidEditSave", "SmartCartNum":$("#WorkerDialogRfidInp").val(), "idWorker":$("#WorkerDialogInpID").val()},
		function(data)
		{
			if(data=="ok")
			{
				$("#inputWorkerDialogInpRfidInfo").text($("#WorkerDialogRfidInp").val());
				$("#WorkerDialogRfid").dialog("close");
			}
			else
				$("#WorkerDialogRfidErr").text(data);
		}
	)
}