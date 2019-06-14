//Отображение сотрудников онлайн
function EntWorkerSelectOnline()
{
	$.post(
		'enterprise/enterprise.php',
		{ 'Method':'SelectOnline'},
		function (data)
			{ $('#EntWorkerListOnline').html(''+data+'');}
	);
	//Таймер каждые 15 мин
	setInterval(
	function()
	{
		$.post(
			'workers/worker.php',
			{ 'Method':'SelectOnline'},
			function (data)
				{ $('#EntWorkerListOnline').html(''+data+'');}
		);
	},
	900000
	)
}
//-----------------------------------------------------------

//--------------Лазер--------------------------
function EntLaserSelect()
{
	$.post(
		'enterprise/enterprise.php',
		{	'Method':'LaserSelect'	},
		function (data)	{ $('#EntLaser').find('tr').remove();	$('#EntLaser').append(data);	}
	);
	setTimeout(EntLaserSelect,600000);//каждые 10 мин
}
function EntGibkaSelect()
{
	$.post(
		'enterprise/enterprise.php',
		{	'Method':'SgibkaSelect'	},
		function (data)	{ $('#EntSgibka').find('tr').remove();	$('#EntSgibka').append(data);	}
	);
	setTimeout(EntGibkaSelect,600000);//каждые 10 мин
}
//------------------------------------------------

//--------------Наряды------------------------

function EntNaryadTempListSelect()
{
	$.post(
		'enterprise/enterprise.php',
		{	'Method':'NaryadTempListSelect'	},
		function (data)	{
			$('#EntNaryadTempTable').find('tr').remove();	
			var obj=jQuery.parseJSON(data);
			var i=0;
			while(obj[i]!=null)
			{
				var bgColor='white';
				if(obj[i]['AlertStatus']==1)
					bgColor='red';
				$('#EntNaryadTempTable').last().append(
					'<tr id=EntNaryadTempTableTR'+obj[i]['id']+' style="background-color:'+bgColor+'">'+
						'<td>'+obj[i]['Blank']+'</td>'+
						'<td>'+obj[i]['name']+'</td>'+
						'<td>'+obj[i]['H']+'x'+obj[i]['W']+' '+obj[i]['S']+'</td>'+
						'<td style="cursor:pointer" title="'+obj[i]['LaserWork']+'">'+obj[i]['LaserDate']+'</td>'+
						'<td style="cursor:pointer" title="'+obj[i]['SgibkaWork']+'">'+obj[i]['SgibkaDate']+'</td>'+
						'<td><button onclick="EntNaryadEditStart('+obj[i]['id']+')">+</button><button onclick="EntNaryadTempRemove('+obj[i]['id']+')">x</button></td>'+
					'</tr>'
				);
				i++;
			};
		}
	);
	setTimeout(EntNaryadTempListSelect,900000);
}

function EntNaryadTempRemove(id)
{
	if(confirm( 'Вернуть сгибщику?'))
		$.post(
			'enterprise/enterprise.php',
			{'Method':'NaryadTempRemove' , 'id':id},
			function(data)
			{
				if(data=='ok')
					$('#EntNaryadTempTableTR'+id).css('background-color','red');
			}
		);
}

function EntNaraydListSelect()
{
	$.post(
		'enterprise/enterprise.php',
		{	'Method':'NaryadListSelect'	},
		function (data)	{ 
			$('#EntNaryadTable').find('tr').remove();	
			var obj=jQuery.parseJSON(data);
			var i=0;
			while(obj[i]!=null)
				{
					$('#EntNaryadTable').append(
						'<tr onclick="EntNaryadEditStart('+obj[i]['id']+')" id=EntNaryadTableTR'+obj[i]['id']+'>'+
							'<td>'+obj[i]['Blank']+'</td>'+
							'<td>'+obj[i]['Name']+'</td>'+
							'<td>'+obj[i]['H']+' * '+obj[i]['W']+'</td>'+
							'<td title="'+obj[i]['Note']+'" style="background-color:'+obj[i]['Color']+'">'+obj[i]['Work']+'</td>'+
							'<td tdType=note title="'+obj[i]['NoteNaryadTitle']+'">'+obj[i]['NoteNaryad']+'</td>'+
						'</tr>'
					);
					i++;
				};
		}
	)
	setTimeout(EntNaraydListSelect,300000);//каждые 5 мин
}

function EntNaryadEditStart(id)
{
	$("#EntNaryadDialogInpBugs").hide();
	$("#EntNaryadDialogInpBugs").text("f");
	$.post(
		'enterprise/enterprise.php',
		{'Method':'NarydEditStart','id':id},
		function (data)
		{
			var obj=jQuery.parseJSON(data);
			$('#EntNaryadDialogInpID').val(id);
			$('#EntNaryadDialogInpBlank').text(obj['Blank']);
			$('#EntNaryadDialogInpName').text(obj['name']+" ("+obj["W"]+ " x " +obj["H"]+" x "+obj["S"]+")");
			$('#EntNaryadDialogInpNalichnik').text(obj['Nalichnik']);
			$('#EntNaryadDialogInpRAL').text(obj['RAL']);
			$('#EntNaryadDialogInpZamok').text(obj['Zamok']);
			
			$("#EntNaryadDialogInpImg").attr("src","enterprise/naryadimg.php?idNaryad="+id);
			
			$('#EntNaryadDialogInpLaser').text(obj['LaserWork']);
			$('#EntNaryadDialogInpLaserComplite').text(obj['LaserDate']);
			$('#EntNaryadDialogInpLaserSum').val(obj['LaserSum']);
			$("#EntNaryadDialogInpSgibkaNum").text(obj['SgibkaWorkNum']);
			$('#EntNaryadDialogInpSgibka').text(obj['SgibkaWork']);
			$('#EntNaryadDialogInpSgibkaComplite').text(obj['SgibkaDate']);
			$('#EntNaryadDialogInpSgibkaSum').val(obj['SgibkaSum']);
			
			$('#EntNaryadDialogInpSvarkaWorkEdit').text(obj['SvarkaDateEdit']);
			$('#EntNaryadDialogInpSvarkaComplite').text(obj['SvarkaDate']);
			$("#EntNaryadDialogInpSvarkaWorkNum").text(obj['SvarkaCompliteWorkNum']);
			$('#EntNaryadDialogInpSvarkaWork').text(obj['SvarkaWork']);
			$('#EntNaryadDialogInpSvarkaSum').val(obj['SvarkaSum']);
			
			//Сборка
			$("#EntNaryadDialogInpSborkaCompliteWorkNum").text(obj['SborkaCompliteWorkNum']);
			$('#EntNaryadDialogInpSborkaCompliteWork').text(obj['SborkaWork']);
			$('#EntNaryadDialogInpSborkaComplite').text(obj['SborkaDate']);
			$('#EntNaryadDialogInpSborkaSum').val(obj['SborkaSum']);
			$("#EntNaryadDialogInpColorCompliteWorkNum").text(obj['ColorCompliteWorkNum']);
			$('#EntNaryadDialogInpColorCompliteWork').text(obj['ColorWork']);
			$('#EntNaryadDialogInpColorComplite').text(obj['ColorDate']);
			$('#EntNaryadDialogInpColorSum').val(obj['ColorSum']);
			$("#EntNaryadDialogInpUpakCompliteWorkNum").text(obj['UpakCompliteWorkNum']);
			$('#EntNaryadDialogInpUpakCompliteWork').text(obj['UpakWork']);
			$('#EntNaryadDialogInpUpakComplite').text(obj['UpakDate']);
			$('#EntNaryadDialogInpUpakSum').val(obj['UpakSum']);
			$("#EntNaryadDialogInpShptCompliteWorkNum").text(obj['ShptCompliteWorkNum']);
			$('#EntNaryadDialogInpShptCompliteWork').text(obj['ShptWork']);
			$('#EntNaryadDialogInpShptComplite').text(obj['ShptDate']);
			$('#EntNaryadDialogInpShptSum').val(obj['ShptSum']);
			
			$('#EntNaryadDialogInpNote').val(obj['Note']);
			$('#EntNaryadDialog').dialog('open');
		}
	)
}
var EntNaraydSelectWorksFlag=false
function EntNaraydSelectWorks(typeSelect , elSelect)
{
	if(!EntNaraydSelectWorksFlag)
	{
	$(elSelect).find('option').remove();
	$(elSelect).append('<option></option>');
	$.post(
		'enterprise/enterprise.php',
		{'Method':'NaryadSelectWorks' , 'typeSelect':typeSelect},
		function (data)
		{
			var obj=jQuery.parseJSON(data);
			var i=0;		
			while(obj[i]!=null)
			{
				$(elSelect).append('<option>'+obj[i]['FIO']+'</option>');
				i++;
			};
		}
	);
		EntNaraydSelectWorksFlag=true;
	}
	else
		EntNaraydSelectWorksFlag=false;
}
//Функция избавлет список от (0) при выборе сотрудника
function EntNaraydSelectWorksChange(el)
{
	var s=$(el).val();
	if(s.indexOf(' (')>-1)
		s=s.substring(0,s.indexOf(' ('));
	$('option:selected',$(el)).text(s);
}

function EntNaryadDialogSave()
{
	$.post(
		'enterprise/enterprise.php',
		{
			'Method':'NaryadSave',
			'id':$('#EntNaryadDialogInpID').val(),
			'SvarkaEdit':$('#EntNaryadDialogInpSvarkaWorkEdit').text(),
			'SvarkaWork':$('#EntNaryadDialogInpSvarkaWork').text(),
			'SvarkaComplite':$('#EntNaryadDialogInpSvarkaComplite').text(),
			'SvarkaSum':$('#EntNaryadDialogInpSvarkaSum').val(),
			
			'LaserSum':$('#EntNaryadDialogInpLaserSum').val(),
			'SgibkaSum':$('#EntNaryadDialogInpSgibkaSum').val(),
			
			'SborkaComplite':$('#EntNaryadDialogInpSborkaComplite').text(),
			'SborkaCompliteWork':$('#EntNaryadDialogInpSborkaCompliteWork').text(),
			'SborkaSum':$('#EntNaryadDialogInpSborkaSum').val(),
			
			'ColorComplite':$('#EntNaryadDialogInpColorComplite').text(),
			'ColorCompliteWork':$('#EntNaryadDialogInpColorCompliteWork').text(),
			'ColorSum':$('#EntNaryadDialogInpColorSum').val(),
			
			'UpakComplite':$('#EntNaryadDialogInpUpakComplite').text(),
			'UpakCompliteWork':$('#EntNaryadDialogInpUpakCompliteWork').text(),
			'UpakSum':$('#EntNaryadDialogInpUpakSum').val(),
			
			'ShptComplite':$('#EntNaryadDialogInpShptComplite').text(),
			'ShptCompliteWork':$('#EntNaryadDialogInpShptCompliteWork').text(),
			'ShptSum':$('#EntNaryadDialogInpShptSum').val(),
			
			'Note':$('#EntNaryadDialogInpNote').val()
		},
		function (data){
			if(data=='ok') 
			{
				$('#EntNaryadDialog').dialog('close'); 
				EntNaryadTempListSelect();
				var s=$('#EntNaryadDialogInpNote').val();
				if(s.length>10)
					s=s.substring(0,10)+"...";
				$("#EntNaryadTableTR"+$('#EntNaryadDialogInpID').val()+" td[tdType=note]").text(s);
				EntNaraydListSelect();
				EntNaryadTempListSelect();
			}
			else
			{
				$("#EntNaryadDialogInpBugs").show();
				$("#EntNaryadDialogInpBugs").text(data);
			};
		}
	);
}
//Удаляем статус (очищаем поле)
function EntNaryadStatusDel(status)
{
	if(confirm("Произвести удаление?"))
		switch(status)
		{
			case "SvarkaWork": 
				if($("#EntNaryadDialogInpSvarkaComplite").text()=="")
				{
					$("#EntNaryadDialogInpSvarkaWork").text("");
					$("#EntNaryadDialogInpSvarkaWorkEdit").text("");
				}
				else alert("Нельзя удалить назначенного сотрудника т.к. стоит статус Выполненно!");
			break;
			case "Svarka": 
				if($("#EntNaryadDialogInpSborkaCompliteWork").text()=="")
				{
					$("#EntNaryadDialogInpSvarkaComplite").text("");
				}
				else alert("Нельзя удалить статус Выполненно, т.к. наряд прошел сборку!");
			break;
			case "Sborka": 
				if($("#EntNaryadDialogInpColorCompliteWork").text()=="")
				{
					$("#EntNaryadDialogInpSborkaCompliteWork").text(""); $("#EntNaryadDialogInpSborkaComplite").text("");
				}
				else alert("Нельзя удалить статус Выполненно, т.к. наряд прошел покраску!");
			break;
			case "Color": 
				if($("#EntNaryadDialogInpUpakCompliteWork").text()=="")
				{
					$("#EntNaryadDialogInpColorCompliteWork").text(""); $("#EntNaryadDialogInpColorComplite").text("");
				}
				else alert("Нельзя удалить статус Выполненно, т.к. наряд прошел Упаковку!");
			break;
			case "Upak":
				if($("#EntNaryadDialogInpShptCompliteWork").text()=="")
				{
					$("#EntNaryadDialogInpUpakCompliteWork").text(""); $("#EntNaryadDialogInpUpakComplite").text("");
				}
				else alert("Нельзя удалить статус Выполненно, т.к. наряд прошел Погрузку!");
			break;
			case "Shpt": $("#EntNaryadDialogInpShptCompliteWork").text(""); $("#EntNaryadDialogInpShptComplite").text("");
			break;
		}
}

//------------------------------------------------
function EntNaryadChoseWorker(typeSelect , elWork, elDate)
{
	$("#EntNaryadChoseWorkerSelect").find('option').remove();
	$("#EntNaryadChoseWorkerLoader").show();
	$.post(
		'enterprise/enterprise.php',
		{'Method':'NaryadSelectWorks' , 'typeSelect':typeSelect},
		function (data)
		{
			$("#EntNaryadChoseWorkerLoader").hide();
			var obj=jQuery.parseJSON(data);
			var i=0;		
			while(obj[i]!=null)
			{
				$("#EntNaryadChoseWorkerSelect").append('<option>'+obj[i]['FIO']+'</option>');
				i++;
			};
			$("#EntNaryadChoseWorkerSelect").attr("size",i+1);
		}
	);
	$("#EntNaryadChoseElWorker").val(elWork);
	$("#EntNaryadChoseElDate").val(elDate);
	$("#EntNaryadChoseWorkerDialog").dialog("open");
}

//------------Печать заказ / наряд-----------------------------------
function EntNaryadDialogPrintNaryad()
{
	$("#EntNaryadDialogInpBugs").hide();
	if($("#EntNaryadDialogInpID").val()!="")
		$.post(
			'enterprise/enterprise.php',
			{"Method":"PrintNaryad", "id":$("#EntNaryadDialogInpID").val()},
			function (data)
			{
				if(data=="ok")
				{
					window.open("enterprise/naryad.pdf",'_blank');
				}
				else
				{
					$("#EntNaryadDialogInpBugs").show();
					$("#EntNaryadDialogInpBugs").html("<hr>"+data);
				};
			}
		);
}