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
var EntNaraydListSelectWHERE=" AND n.UpakCompliteFlag=0 AND o.status<>-1";
var EntNaryadSelectList=new Array();
var EntNaryadRowDisplay=0;
function EntNaraydListSelect()
{
	$("#EntNaryadTableLoader").show();
	$('#EntNaryadTable').find('tr').remove();
	EntNaryadSelectList.length=0;
	EntNaryadRowDisplay=0;
	$.post(
		'enterprise/enterprise.php',
		{	'Method':'NaryadListSelect', "WHERE": EntNaraydListSelectWHERE},
		function (data)	{
			$("#EntNaryadTableLoader").hide();
			var obj=jQuery.parseJSON(data);
			var i=0;
			while((o=obj[i])!=null)
			{
				var StatusColorClass=""; if(o.Status==-1) StatusColorClass="Cancel";
				var NullPositionImg=""; if(o.Alert!="") NullPositionImg="<img src='images/error.png' width=20 title='"+o.Alert+"'>";
				var EntNaryadSelectRow={
					"StatusColorClass":StatusColorClass,
					"id":o.id,
					"Blank":o.Blank,
					"Shet":o.Shet,
					"NumInOrder":o.NumInOrder,
					"Name":o.Name,
					"H":o.H,
					"W":o.W,
					"S":o.S,
					"ColorTR":o.ColorTR,
					"Step":o.Step,
					"NullPositionImg":NullPositionImg
				};
				EntNaryadSelectList[i]=EntNaryadSelectRow;
				if(EntNaryadRowDisplay<50)
				{
					$('#EntNaryadTable').append(
						'<tr Class=" '+StatusColorClass+' " onclick="EntNaryadEditStart('+o.id+')" id=EntNaryadTableTR'+o.id+'>'+
							'<td>'+o.Blank+'</td>'+
							'<td>'+o.Shet+"/"+o.NumInOrder+'</td>'+
							'<td>'+o.Name+'</td>'+
							'<td>'+o.H+' * '+o.W+(o.S!=""?" * "+o.S:"")+'</td>'+
							'<td style="background-color:'+o.ColorTR+'">'+o.Step+'</td>'+
							//'<td tdType=note title="'+o.NaryadNote+'">'+o.NaryadNoteS+'</td>'+
							"<td>"+NullPositionImg+"</td>"+
						'</tr>'
					);
					EntNaryadRowDisplay++;
				}
				i++;
			};
		}
	)
	//setTimeout(EntNaraydListSelect,300000);//каждые 5 мин
}

//----Печать списка нарядов
function EntNaraydListPrint()
{
	$("#EntNaryadListPrintLoader").show();
	$.post(
		'enterprise/enterprise.php',
		{	'Method':'PrintDialogPrint', "PrintList":true, "WHERE": EntNaraydListSelectWHERE},
		function(data){
			$("#EntNaryadListPrintLoader").hide();
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

function EntNaraydListSelectFind()
{
	EntNaraydListSelectWHERE="";
	//if($("#EntNaryadPanelYear").val()!="") EntNaraydListSelectWHERE=EntNaraydListSelectWHERE+" AND YEAR(o."+(ParamGetValue("ViewNumOrder")=="Blank"?"BlankDate":"ShetDate")+")="+$("#EntNaryadPanelYear").val();
	if($("#EntNaryadPanelYear").val()!="") EntNaraydListSelectWHERE=EntNaraydListSelectWHERE+" AND YEAR(o.BlankDate)="+$("#EntNaryadPanelYear").val();
	if(!$("#EntNaryadPanelChStatusCancel").is(":checked")) EntNaraydListSelectWHERE=EntNaraydListSelectWHERE+" AND o.status<>-1";

	if($("#EntNaryadPanelNum").val()!="") EntNaraydListSelectWHERE=EntNaraydListSelectWHERE+" AND CONCAT(n.Num,n.NumPP) LIKE '"+$("#EntNaryadPanelNum").val()+"%' ";

	if($("#EntNaryadPanelShet").val()!="") EntNaraydListSelectWHERE=EntNaraydListSelectWHERE+" AND o.Shet='"+$("#EntNaryadPanelShet").val()+"' ";	
	if($("#EntNaryadPanelNumInOrder").val()!="") EntNaraydListSelectWHERE=EntNaraydListSelectWHERE+" AND n.NumInOrder="+$("#EntNaryadPanelNumInOrder").val()+" ";	

	if($("#EntNaryadPanelShtild").val()!="") EntNaraydListSelectWHERE=EntNaraydListSelectWHERE+" AND o1.Shtild+n.NumPP-1="+$("#EntNaryadPanelShtild").val();
	//Отображаем отгруженные наряды только если в поиске указан номер наряда 
	if($("#EntNaryadPanelShet").val()!="" & !$("#EntNaryadPanelChShpt").is(":checked")) EntNaraydListSelectWHERE=EntNaraydListSelectWHERE+" AND n.ShptCompliteFlag=0";

	var WhereFlag="";
	if($("#EntNaryadPanelChSgibka").is(":checked")) WhereFlag=WhereFlag+(WhereFlag=="" ? "" : " OR ")+"(n.SgibkaCompliteFlag=1 AND (n.SvarkaCompliteFlag=2 OR n.SvarkaCompliteFlag=0) AND n.SborkaCompliteFlag=0 AND n.ColorCompliteFlag=0 AND n.UpakCompliteFlag=0 AND n.ShptCompliteFlag=0)";
	if($("#EntNaryadPanelChSvarka").is(":checked")) WhereFlag=WhereFlag+(WhereFlag=="" ? "" : " OR ")+"(n.SvarkaCompliteFlag=1 AND n.SborkaCompliteFlag=0 AND n.ColorCompliteFlag=0 AND n.UpakCompliteFlag=0 AND n.ShptCompliteFlag=0)";
	if($("#EntNaryadPanelChSborka").is(":checked")) WhereFlag=WhereFlag+(WhereFlag=="" ? "" : " OR ")+"(n.SborkaCompliteFlag=1 AND n.ColorCompliteFlag=0 AND n.UpakCompliteFlag=0 AND n.ShptCompliteFlag=0)";
	if($("#EntNaryadPanelChColor").is(":checked")) WhereFlag=WhereFlag+(WhereFlag=="" ? "" : " OR ")+"(n.ColorCompliteFlag=1 AND n.UpakCompliteFlag=0 AND n.ShptCompliteFlag=0)";
	if($("#EntNaryadPanelChUpak").is(":checked")) WhereFlag=WhereFlag+(WhereFlag=="" ? "" : " OR ")+"(n.UpakCompliteFlag=1 AND n.ShptCompliteFlag=0)";
	if($("#EntNaryadPanelChShpt").is(":checked")) WhereFlag=WhereFlag+(WhereFlag=="" ? "" : " OR ")+"(n.ShptCompliteFlag=1)";
	EntNaraydListSelectWHERE=EntNaraydListSelectWHERE+(WhereFlag=="" ? "" : " AND ("+WhereFlag+")");
	EntNaraydListSelect();
}

function EntNaraydListSelectFindClear()
{
	$("#EntNaryadPanelNum").val("");
	$("#EntNaryadPanelShet").val("");
	$("#EntNaryadPanelNumInOrder").val("");
	$("#EntNaryadPanelShtild").val("");
	
	$("#EntNaryadPanelChSvarka").removeAttr("checked");
	$("#EntNaryadPanelChSborka").removeAttr("checked");
	$("#EntNaryadPanelChColor").removeAttr("checked");
	$("#EntNaryadPanelChUpak").removeAttr("checked");

	$("#EntNaryadPanelChNSvarka").removeAttr("checked");
	$("#EntNaryadPanelChNSborka").removeAttr("checked");
	$("#EntNaryadPanelChNColor").removeAttr("checked");
	$("#EntNaryadPanelChNUpak").removeAttr("checked");
	
	$("#EntNaryadPanelChStatusCancel").removeAttr("checked");
	
	EntNaraydListSelectWHERE="AND n.UpakCompliteFlag=0 AND o.status<>-1";
	EntNaraydListSelect();
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
			$('#EntNaryadDialogInpBlank').text(obj.Construct.Blank);
			$('#EntNaryadDialogInpName').text(obj.Construct.name+" ("+obj.Construct.H+ " x " +obj.Construct.W+(obj.Construct.S!=""? " x "+obj.Construct.S:"" )+")");
			$("#EntNaryadDialogInpNumInOrder").text(obj.Construct.NumInOrder);
			$('#EntNaryadDialogInpNalichnik').text(obj.Construct.Nalichnik);
			$('#EntNaryadDialogInpRAL').text(obj.Construct.RAL);
			$('#EntNaryadDialogInpZamok').text(obj.Construct.Zamok);
			$("#EntNaryadDialogInpShtild").text(obj.Construct.Shtild);
			
			$("#EntNaryadDialogInpImg").attr("src","enterprise/naryadimg.php?idNaryad="+id);

			$('#EntNaryadDialogStepSvarkaSum').text(obj.Construct.CostSvarka);
			$('#EntNaryadDialogStepFrameSum').text(obj.Construct.CostFrame);
			$('#EntNaryadDialogStepMdfSum').text(obj.Construct.CostMdf);
			$('#EntNaryadDialogStepSborkaSum').text(obj.Construct.CostSborka);
			$('#EntNaryadDialogStepColorSum').text(obj.Construct.CostColor);
			$('#EntNaryadDialogStepSborkaMdfSum').text(obj.Construct.CostSborkaMdf);
			$('#EntNaryadDialogStepUpakSum').text(obj.Construct.CostUpak);
			$('#EntNaryadDialogStepShptSum').text(obj.Construct.CostShpt);

			//Если нет рамки, МДФ то прячем стадии
			$('#EntNaryadDialogStepFrameSum').parent().parent().parent().show();
			$('#EntNaryadDialogStepMdfSum').parent().parent().parent().show();
			$('#EntNaryadDialogStepSborkaMdfSum').parent().parent().parent().show();
			if(obj.Construct.FrameCompliteFlag==null) $('#EntNaryadDialogStepFrameSum').parent().parent().parent().hide();
			if(obj.Construct.MdfCompliteFlag==null) $('#EntNaryadDialogStepMdfSum').parent().parent().parent().hide();
			if(obj.Construct.SborkaMdfCompliteFlag==null) $('#EntNaryadDialogStepSborkaMdfSum').parent().parent().parent().hide();
			
			$("#EntNaryadDialogStepSvarkaWorks").find("tr").remove();
			$("#EntNaryadDialogStepFrameWorks").find("tr").remove();
			$("#EntNaryadDialogStepMdfWorks").find("tr").remove();
			$("#EntNaryadDialogStepSborkaWorks").find("tr").remove();
			$("#EntNaryadDialogStepColorWorks").find("tr").remove();
			$("#EntNaryadDialogStepSborkaMdfWorks").find("tr").remove();
			$("#EntNaryadDialogStepUpakWorks").find("tr").remove();
			$("#EntNaryadDialogStepShptWorks").find("tr").remove();
			var i=0;
			while(obj.Complite[i]!=null)
			{
				var FIO=obj.Complite[i].FIO!=null?obj.Complite[i].FIO:"";
				var DateAppointment=obj.Complite[i].DateAppointment!=null?obj.Complite[i].DateAppointment:"";
				var DateComplite=obj.Complite[i].DateComplite!=null?obj.Complite[i].DateComplite:"";
				var ColorTR=obj.Complite[i].DateComplite!=null?"Complite":"Work";
				switch(parseInt(obj.Complite[i].Step))
				{
					case 1:
						$('#EntNaryadDialogInpLaser').text(obj.Complite[i].FIO);
						$('#EntNaryadDialogInpLaserComplite').text(obj.Complite[i].DateComplite);
						$('#EntNaryadDialogInpLaserSum').val(obj.Complite[i].Cost);
					break;
					case 2:
						$('#EntNaryadDialogInpSgibka').text(obj.Complite[i].FIO);
						$('#EntNaryadDialogInpSgibkaComplite').text(obj.Complite[i].DateComplite);
						$('#EntNaryadDialogInpSgibkaSum').val(obj.Complite[i].Cost);
					break;
					case 3:
						$("#EntNaryadDialogStepSvarkaWorks").append(
							"<tr Status='Load' Step='Svarka' idNaryadComplite="+obj.Complite[i].id+" Process='"+ColorTR+"'>"+
								"<td><input onchange='EntNaryadInputChange(this)' value='"+obj.Complite[i].Cost+"'></td>"+
								"<td Cursor Step='Svarka' idWorker='"+obj.Complite[i].idWorker+"' onclick='EntManualWorkersDialogOpen(this)'>"+FIO+"</td>"+
								"<td>"+DateAppointment+"</td>"+
								"<td Cursor onclick='EntNaryadSvarkaCompliteClick(this)'>"+DateComplite+"</td>"+
							"</tr>"
						);
					break;
					case 4:
						$("#EntNaryadDialogStepFrameWorks").append(
							"<tr Status='Load' Step='Frame' idNaryadComplite="+obj.Complite[i].id+" Process='"+ColorTR+"'>"+
								"<td><input onchange='EntNaryadInputChange(this)' value='"+obj.Complite[i].Cost+"'></td>"+
								"<td Cursor idWorker='"+obj.Complite[i].idWorker+"' onclick='EntManualWorkersDialogOpen(this)'>"+FIO+"</td>"+
								"<td>"+DateAppointment+"</td>"+
								"<td>"+DateComplite+"</td>"+
							"</tr>"
						);
					break;
					case 5:
						$("#EntNaryadDialogStepSborkaWorks").append(
							"<tr Status='Load' Step='Sborka' idNaryadComplite="+obj.Complite[i].id+" Process='"+ColorTR+"'>"+
								"<td><input onchange='EntNaryadInputChange(this)' value='"+obj.Complite[i].Cost+"'></td>"+
								"<td Cursor idWorker='"+obj.Complite[i].idWorker+"' onclick='EntManualWorkersDialogOpen(this)'>"+FIO+"</td>"+
								"<td>"+DateAppointment+"</td>"+
								"<td>"+DateComplite+"</td>"+
							"</tr>"
						);
					break;
					case 6:
						$("#EntNaryadDialogStepColorWorks").append(
							"<tr Status='Load' Step='Color' idNaryadComplite="+obj.Complite[i].id+" Process='"+ColorTR+"'>"+
								"<td><input onchange='EntNaryadInputChange(this)' value='"+obj.Complite[i].Cost+"'></td>"+
								"<td Cursor idWorker='"+obj.Complite[i].idWorker+"' onclick='EntManualWorkersDialogOpen(this)'>"+FIO+"</td>"+
								"<td>"+DateAppointment+"</td>"+
								"<td>"+DateComplite+"</td>"+
							"</tr>"
						);
					break;
					case 7:
						$("#EntNaryadDialogStepUpakWorks").append(
							"<tr Status='Load' Step='Upak' idNaryadComplite="+obj.Complite[i].id+" Process='"+ColorTR+"'>"+
								"<td><input onchange='EntNaryadInputChange(this)' value='"+obj.Complite[i].Cost+"'></td>"+
								"<td Cursor idWorker='"+obj.Complite[i].idWorker+"' onclick='EntManualWorkersDialogOpen(this)'>"+FIO+"</td>"+
								"<td>"+DateAppointment+"</td>"+
								"<td>"+DateComplite+"</td>"+
							"</tr>"
						);
					break;
					case 8:
						$("#EntNaryadDialogStepShptWorks").append(
							"<tr Status='Load' Step='Shpt' idNaryadComplite="+obj.Complite[i].id+" Process='"+ColorTR+"'>"+
								"<td><input onchange='EntNaryadInputChange(this)' value='"+obj.Complite[i].Cost+"'></td>"+
								"<td Cursor idWorker='"+obj.Complite[i].idWorker+"' onclick='EntManualWorkersDialogOpen(this)'>"+FIO+"</td>"+
								"<td>"+DateAppointment+"</td>"+
								"<td>"+DateComplite+"</td>"+
							"</tr>"
						);
					break;
					case 9://МДФ
						$("#EntNaryadDialogStepMdfWorks").append(
							"<tr Status='Load' Step='Mdf' idNaryadComplite="+obj.Complite[i].id+" Process='"+ColorTR+"'>"+
								"<td><input onchange='EntNaryadInputChange(this)' value='"+obj.Complite[i].Cost+"'></td>"+
								"<td Cursor idWorker='"+obj.Complite[i].idWorker+"' onclick='EntManualWorkersDialogOpen(this)'>"+FIO+"</td>"+
								"<td>"+DateAppointment+"</td>"+
								"<td>"+DateComplite+"</td>"+
							"</tr>"
						);
					break;
					case 10://Сборка МДФ
						$("#EntNaryadDialogStepSborkaMdfWorks").append(
							"<tr Status='Load' Step='SborkaMdf' idNaryadComplite="+obj.Complite[i].id+" Process='"+ColorTR+"'>"+
								"<td><input onchange='EntNaryadInputChange(this)' value='"+obj.Complite[i].Cost+"'></td>"+
								"<td Cursor idWorker='"+obj.Complite[i].idWorker+"' onclick='EntManualWorkersDialogOpen(this)'>"+FIO+"</td>"+
								"<td>"+DateAppointment+"</td>"+
								"<td>"+DateComplite+"</td>"+
							"</tr>"
						);
					break;
				};
				i++;
			};

			//Разрешить погрузку
			if(obj.Construct.ShptAllowDate!=null)
			{
				$("#EntNaryadDialogShptAllowBtn").hide();
				$("#EntNaryadDialogShptAllowFIO").show();
				$("#EntNaryadDialogShptAllowDate").show();
				$("#EntNaryadDialogShptAllowFIO").text(obj.Construct.ShptAllowWork);
				$("#EntNaryadDialogShptAllowDate").text(obj.Construct.ShptAllowDate);
			}
			else
			{
				$("#EntNaryadDialogShptAllowBtn").show();
				$("#EntNaryadDialogShptAllowFIO").hide();
				$("#EntNaryadDialogShptAllowDate").hide();
				$("#EntNaryadDialogShptAllowFIO").text("");
				$("#EntNaryadDialogShptAllowDate").text("");
			};
			
			var Master=obj.Construct.Master;
			if(obj["Master"]==null)
				Master=$("#MainComineFIO").text();
			$('#EntNaryadDialogInpMaster').text(Master);
			
			$('#EntNaryadDialogInpNote').val(obj.Construct.Note);
			$("#EntNaryadDialogInpDoorNote").text(obj.DoorNote);

			//Если наряд был отгружен, тогда запретим мастерам отмечать выполнение
			/*
			if($("#EntNaryadDialogStepShptWorks tr[Process=Complite]").length==$("#EntNaryadDialogStepShptWorks tr").length)
				$.post(
					"MainAutorize.php",
					{"Method":"GetSession"},
					function(data)
					{
						var o =jQuery.parseJSON(data);
						if(o.Type==4)
						{
							$("#EntNaryadDialogStepSvarkaWorks"). find("tr td:eq(1)").removeAttr("onclick");
							$("#EntNaryadDialogStepFrameWorks"). find("tr td:eq(1)").removeAttr("onclick");
							$("#EntNaryadDialogStepSborkaWorks"). find("tr td:eq(1)").removeAttr("onclick");
							$("#EntNaryadDialogStepColorWorks"). find("tr td:eq(1)").removeAttr("onclick");
							$("#EntNaryadDialogStepUpakWorks"). find("tr td:eq(1)").removeAttr("onclick");
							$("#EntNaryadDialogStepShptWorks"). find("tr td:eq(1)").removeAttr("onclick");
						};
					}
				);*/
			//Введем запрет изменения стоимости всем кроме администраторов
			$.post(
				"MainAutorize.php",
				{"Method":"GetSession"},
				function(data)
				{
					var o =jQuery.parseJSON(data);
					if(o.Type!=1)
					{
						$("#EntNaryadDialogStepSvarkaWorks"). find("tr td:eq(0) input").attr("disabled","true");
						$("#EntNaryadDialogStepFrameWorks"). find("tr td:eq(0) input").attr("disabled","true");
						$("#EntNaryadDialogStepSborkaWorks"). find("tr td:eq(0) input").attr("disabled","true");
						$("#EntNaryadDialogStepColorWorks"). find("tr td:eq(0) input").attr("disabled","true");
						$("#EntNaryadDialogStepUpakWorks"). find("tr td:eq(0) input").attr("disabled","true");
						$("#EntNaryadDialogStepShptWorks"). find("tr td:eq(0) input").attr("disabled","true");
					}
					else
					{
						$("#EntNaryadDialogStepSvarkaWorks"). find("tr td:eq(0) input").removeAttr("disabled");
						$("#EntNaryadDialogStepFrameWorks"). find("tr td:eq(0) input").removeAttr("disabled");
						$("#EntNaryadDialogStepSborkaWorks"). find("tr td:eq(0) input").removeAttr("disabled");
						$("#EntNaryadDialogStepColorWorks"). find("tr td:eq(0) input").removeAttr("disabled");
						$("#EntNaryadDialogStepUpakWorks"). find("tr td:eq(0) input").removeAttr("disabled");
						$("#EntNaryadDialogStepShptWorks"). find("tr td:eq(0) input").removeAttr("disabled");
					};
				}
			);

			$('#EntNaryadDialog').dialog('open');
		}
	)
}
/**
* Изменение стоимости работы в строчке
*/
function EntNaryadInputChange(el){
	var TR=$(el).parent().parent();
	if(TR.attr("Status")=="Load") TR.attr("Status","Edit");
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
	var StepName=["Svarka", "Frame","Mdf", "Sborka", "Color","SborkaMdf", "Upak", "Shpt"];
	var c=0;
	var Step=new Array();
	var Status=new Array();
	var idNaryadComplite=new Array();
	var Cost=new Array();
	var idWorker=new Array();
	var DateAppointment=new Array();
	var DateComplite=new Array();
	for(var j=0;j<8;j++)
	{
		for(var i=0;i<$("#EntNaryadDialogStep"+StepName[j]+"Works tr").length;i++)
		{
			var elTR=$("#EntNaryadDialogStep"+StepName[j]+"Works tr:eq("+i.toString()+")");
			if(elTR.attr("Status")!="Load")
			{
				Step[c]=StepName[j];
				Status[c]=elTR.attr("Status");
				idNaryadComplite[c]=elTR.attr("idNaryadComplite");
				Cost[c]=elTR.find("td:eq(0) input").val();
				idWorker[c]=elTR.find("td:eq(1)").attr("idWorker");
				DateAppointment[c]=elTR.find("td:eq(2)").text();
				DateComplite[c]=elTR.find("td:eq(3)").text();
				c++;
			};
		};
	};
	//Определим полностью выполненные стадии
	var SvarkaCompliteFlag="0";
	if($("#EntNaryadDialogStepSvarkaWorks tr").length==$("#EntNaryadDialogStepSvarkaWorks tr[Process=Complite]").length)
		SvarkaCompliteFlag="1";
	if(ParamGetValue("SkipPurposeWelder")==0 & $("#EntNaryadDialogStepSvarkaWorks tr:last td:eq(1)").text()!="" & $("#EntNaryadDialogStepSvarkaWorks tr:last td:eq(3)").text()=="")
		SvarkaCompliteFlag="2";
	var FrameCompliteFlag="Null";
	if($("#EntNaryadDialogStepFrameWorks tr").length>0)
	{
		FrameCompliteFlag="0";
		if($("#EntNaryadDialogStepFrameWorks tr").length==$("#EntNaryadDialogStepFrameWorks tr[Process=Complite]").length)
		{
			FrameCompliteFlag="1";
		}
		else
			FrameCompliteFlag="0";
	};
	var MdfCompliteFlag="Null";
	if($("#EntNaryadDialogStepMdfWorks tr").length>0)
		if($("#EntNaryadDialogStepMdfWorks tr").length==$("#EntNaryadDialogStepMdfWorks tr[Process=Complite]").length)
		{
			MdfCompliteFlag="1";
		}
		else
			MdfCompliteFlag="0";	
	var SborkaCompliteFlag="0";
	if($("#EntNaryadDialogStepSborkaWorks tr").length==$("#EntNaryadDialogStepSborkaWorks tr[Process=Complite]").length)
		SborkaCompliteFlag="1";
	var ColorCompliteFlag="0";
	if($("#EntNaryadDialogStepColorWorks tr").length==$("#EntNaryadDialogStepColorWorks tr[Process=Complite]").length)
		ColorCompliteFlag="1";
	var SborkaMdfCompliteFlag="Null";
	if($("#EntNaryadDialogStepSborkaMdfWorks tr").length>0)
		if($("#EntNaryadDialogStepSborkaMdfWorks tr").length==$("#EntNaryadDialogStepSborkaMdfWorks tr[Process=Complite]").length)
		{
			SborkaMdfCompliteFlag="1";
		}
		else
			SborkaMdfCompliteFlag="0";
	var UpakCompliteFlag="0";
	if($("#EntNaryadDialogStepUpakWorks tr").length==$("#EntNaryadDialogStepUpakWorks tr[Process=Complite]").length)
		UpakCompliteFlag="1";
	var ShptCompliteFlag="0";
	if($("#EntNaryadDialogStepShptWorks tr").length==$("#EntNaryadDialogStepShptWorks tr[Process=Complite]").length)
		ShptCompliteFlag="1";
	//Если не выполненна стадия упаковка, тогда очистим разрешение погрузки
	var ShptAllowWork="", ShptAllowDate="";
	if(UpakCompliteFlag=="1"){
		ShptAllowWork=$('#EntNaryadDialogShptAllowFIO').text();
		ShptAllowDate=$('#EntNaryadDialogShptAllowDate').text();
	}


	$.post(
		'enterprise/enterprise.php',
		{
			'Method':'NaryadSave',
			'id':$('#EntNaryadDialogInpID').val(),
			"Step[]":Step,
			"Status[]":Status,
			"idNaryadComplite[]":idNaryadComplite,
			"Cost[]":Cost,
			"idWorker[]":idWorker,
			"DateAppointment[]":DateAppointment,
			"DateComplite[]":DateComplite,

			"SvarkaCompliteFlag":SvarkaCompliteFlag,
			"FrameCompliteFlag":FrameCompliteFlag,
			"MdfCompliteFlag":MdfCompliteFlag,
			"SborkaCompliteFlag":SborkaCompliteFlag,
			"ColorCompliteFlag":ColorCompliteFlag,
			"SborkaMdfCompliteFlag":SborkaMdfCompliteFlag,
			"UpakCompliteFlag":UpakCompliteFlag,
			"ShptCompliteFlag":ShptCompliteFlag,

			'ShptAllowWork':ShptAllowWork,
			'ShptAllowDate':ShptAllowDate,

			"Master":$("#EntNaryadDialogInpMaster").text(),
			'Note':$('#EntNaryadDialogInpNote').val()
		},
		function(data){
			if(data=='ok') 
			{
				$('#EntNaryadDialog').dialog('close');
				if($("#EntNaryadTable").is(":visible"))
				{
					EntNaraydListSelect();
					EntStockSelect("1=1");
				};
				if($("#EntNaryadTempTable").is(":visible"))
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
			case "Frame": 
				if($("#EntNaryadDialogInpUpakCompliteWork").text()=="")
				{
					$("#EntNaryadDialogInpFrameCompliteWork").text(""); $("#EntNaryadDialogInpFrameComplite").text("");
				}
				else alert("Нельзя удалить статус Выполненно, т.к. наряд прошел Упаковку!");
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
			case "ShptAllow": $("#EntNaryadDialogInpShptAllowWork").text(""); $("#EntNaryadDialogInpShptAllowDate").text("");
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
	{
		$.post(
			'enterprise/enterprise.php',
			{"Method":"PrintDialogPrint", "NaryadOne":$("#EntNaryadDialogInpID").val()},
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
}

//------------------------------Склад упакованных дверей---------------------------------------------->
//Отображение списка заказов
function EntStockSelect(where)
{
	$("#EntStockTableOrders").find("tr").remove();
	$.post(
		'enterprise/enterprise.php',
		{"Method":"StockSelectOrders", "WHERE":where},
		function(data)
		{
			var o=jQuery.parseJSON(data); var i=0;
			while(o[i]!=null)
			{
				var ClassColor="";
				$("#EntStockTableOrders").append(
					"<tr type=header>"+
						"<td onclick='EntStockSelectDoors("+o[i].id+") '><img id=EntStockTableOrdersIMG"+o[i].id+" title='Открыть позиции' src='images/arrow-turn-left.png'></td>"+
						"<td onclick='EntStockSelectDoors("+o[i].id+") '>"+o[i].Blank+"</td>"+
						"<td onclick='EntStockSelectDoors("+o[i].id+") '>"+o[i].SumDoors+"</td>"+
						"<td onclick='EntStockSelectDoors("+o[i].id+") '>"+o[i].SumDoorsUpak+"</td>"+
						"<td onclick='EntStockAllowStep1("+o[i].id+", 1)' alert(435);><img title='Разрешить отгрузку' src='images/done.png'> "+
					"</tr>"+
					"<tr class=NoneHover id=EntStockTableOrdersTR"+o[i].id+"></tr>"
				);
				i++;
			};
		}
	);
};
//Список дверей в зкаказе
function EntStockSelectDoors(idOrder)
{
	if($("#EntStockTableOrdersTR"+idOrder+" td").length==0)
	{
		$("#EntStockTableOrdersIMG"+idOrder).attr("src","images/arrow_skip.png");
		$("#EntStockTableOrdersTR"+idOrder).find("td").remove();
		$.post(
			'enterprise/enterprise.php',
			{"Method":"StockSelectOrderDoors", "idOrder":idOrder},
			function (data)
			{
				var HTMLGen=""
				var o=jQuery.parseJSON(data); var i=0;
				HTMLGen=
					"<td></td><td colspan=4>"+
					"<table style='width:100%; border: solid 1px black'>"+
						"<thead>"+
							"<tr><td></td><td>№</td><td>Всего</td><td>Для отгрузки</td><td></td></tr>"+
						"</thead>"+
					"<tbody>";
				
				while(o[i]!=null)
				{
					var ClassColor="";
					HTMLGen=HTMLGen+
						"<tr >"+
							"<td onclick=' EntStockSelectNaryad("+o[i].idDoor+") '><img id=EntStockTableOrdersDoorsIMG"+o[i].idDoor+" title='Открыть наряды' src='images/arrow-turn-left.png'></td>"+
							"<td onclick=' EntStockSelectNaryad("+o[i].idDoor+") '>"+o[i].NumPP+"</td>"+
							"<td onclick=' EntStockSelectNaryad("+o[i].idDoor+") '>"+o[i].Count+"</td>"+
							"<td onclick=' EntStockSelectNaryad("+o[i].idDoor+") '>"+o[i].CountNaryad+"</td>"+
							"<td onclick=' EntStockAllowStep1("+o[i].idDoor+", 2)'><img title='Разрешить отгрузку' src='images/done.png'> "+
						"</tr>"+
						"<tr class=NoneHover id=EntStockTableOrdersDoorsTR"+o[i].idDoor+"></tr>";
					i++;
				};
				HTMLGen=HTMLGen+
					"</tbody>"+
					"</table>"+
					"</td>";
				$("#EntStockTableOrdersTR"+idOrder).html(HTMLGen);
			}
		);
	}
	else 
	{
		$("#EntStockTableOrdersTR"+idOrder).find("td").remove();
		$("#EntStockTableOrdersIMG"+idOrder).attr("src","images/arrow-turn-left.png");
	};
};
//Список нарядов в позиции
function EntStockSelectNaryad(idDoor)
{
	if($("#EntStockTableOrdersDoorsTR"+idDoor+" td").length==0)
	{
		$("#EntStockTableOrdersDoorsIMG"+idDoor).attr("src","images/arrow_skip.png");
		$("#EntStockTableOrdersDoorsTR"+idDoor).find("td").remove();
		$.post(
			'enterprise/enterprise.php',
			{"Method":"StockSelectOrderDoorsNaryad", "idDoor":idDoor},
			function (data)
			{
				var HTMLGen=""
				var o=jQuery.parseJSON(data); var i=0;
				HTMLGen=
					"<td></td><td colspan=4>"+
					"<table style='width:100%; border: solid 1px black'>"+
						"<thead>"+
							"<tr><td>№</td><td>Тип</td><td>Размер</td><td></td><td></td></tr>"+
						"</thead>"+
					"<tbody>";
				
				while(o[i]!=null)
				{
					var ColorRow=""; if(o[i].ShptAllowDate!=null) ColorRow="Complite";
					var ClassColor="";
					HTMLGen=HTMLGen+
						"<tr class='"+ColorRow+"' id=EntStockTableOrdersDoorsTR"+o[i].idNaryad+">"+
							"<td>"+o[i].Num+o[i].NumPP+"</td>"+
							"<td>"+o[i].Name+"</td>"+
							"<td>"+o[i].Size+"</td>"+
							"<td onclick='EntStockAllowStep1("+o[i].idNaryad+", 3)'><img title='Разрешить отгрузку' src='images/done.png'> "+
							"<td onclick='EntNaryadEditStart("+o[i].idNaryad+")'><img title='Редатировать наряд' src='images/edit.png'> "+
						"</tr>";
					i++;
				};
				HTMLGen=HTMLGen+
					"</tbody>"+
					"</table>"+
					"</td>";
				$("#EntStockTableOrdersDoorsTR"+idDoor).html(HTMLGen);
			}
		);
	}
	else 
	{
		$("#EntStockTableOrdersDoorsTR"+idDoor).find("td").remove();
		$("#EntStockTableOrdersDoorsIMG"+idDoor).attr("src","images/arrow-turn-left.png");
	};
};

var EntStockAllowID=-1;  var EntStockAllowTypSelect=-1;//1 - всех нарядов в заказе 2- нарядов в позиции 3- нарядов
//отображение диалогового окна - разрешить / отменить погрузку
function EntStockAllowStep1(id, TypeSelect)
{
	EntStockSelectDoorsBlock=false;
	$("#EntStockAllowDialog1").dialog("open");
	EntStockAllowID=id;
	EntStockAllowTypSelect=TypeSelect;
}
//выполнение выбранного действия
function EntStockAllowStep2()
{
	if(EntStockAllowID!=-1 & EntStockAllowTypSelect!=-1)
		$.post(
			'enterprise/enterprise.php',
			{"Method":"StockAllowStep2", "id":EntStockAllowID, "TypeSelect":EntStockAllowTypSelect, "Action":$("input[name=EntStockAllowDialogRadio]:checked").filter(':checked').val()},
			function(data)
			{
				if(data=="ok")
				{
					switch(EntStockAllowTypSelect)
					{
						//case 1:EntStockSelectDoors(EntStockAllowID); break;
						//case 2:EntStockSelectNaryad(EntStockAllowID); break;
						case 3:
							if($("input[name=EntStockAllowDialogRadio]:checked").filter(":checked").val()=="Allow")
							{
								$("#EntStockTableOrdersDoorsTR"+EntStockAllowID).attr("Class","Complite"); 
							}
							else
								$("#EntStockTableOrdersDoorsTR"+EntStockAllowID).attr("Class",""); 
						break;
					};
					$("#EntStockAllowDialog1").dialog("close");
				}
				else alert(data);
			}
		);
		
}

//Установка статуса РАЗРЕШЕНА ПОГРУЗКА (функция не используется)
function EntStockShptStatus(id)
{
	var Blank=$("#EntStockTableTR"+id+" td").first().text();
	if(confirm("Наряд: "+Blank+" Установить статус: разрешена погрузка?"))
	{
		var FIOMaster="";
		
		$.post(
			"MainAutorize.php",
			{"Method":"GetSession"},
			function(data)
			{
				var o=jQuery.parseJSON(data);
				FIOMaster=o.FIO;
				$.post(
					'enterprise/enterprise.php',
					{"Method":"ShptStatus", "id":id, "FIOMaster":FIOMaster},
					function(data)
					{
					if(data=="ok")
						$("#EntStockTableTR"+id).addClass("Complite");
					}
				);
			}
		);
	};
}

//------------------Отгруженные двери (новая редакция)-----------------
function EntShptNewOrdersSelect()
{
	$("#EntShptNewOrders").find("tr").remove();
	var OrderBy=1; if($("input[name=EntShptNewInpOrderBy]:eq(1)").is(":checked")) OrderBy=2;
	var Where="AND 1=1 ";
	if($("#EntShptNewInpBlank").val()!="") Where=Where+" AND o.Blank="+$("#EntShptNewInpBlank").val()+" ";
	$("#EntShptNewInpDateWith").css("background-color","white");
	$("#EntShptNewInpDateBy").css("background-color","white");
	if($("#EntShptNewInpDateWith").val()!="" & $("#EntShptNewInpDateBy").val()!="")
	{
		Where=Where+"AND (nc.Step=8 AND nc.DateComplite BETWEEN STR_TO_DATE('"+$("#EntShptNewInpDateWith").val()+"', '%d.%m.%Y') AND DATE_ADD(STR_TO_DATE('"+$("#EntShptNewInpDateBy").val()+"','%d.%m.%Y'), INTERVAL 1 DAY) )";
	}
	else //Делаем проверку если заполненно только одно поле
	{
		if($("#EntShptNewInpDateWith").val()=="" & $("#EntShptNewInpDateBy").val()!="") $("#EntShptNewInpDateWith").css("background-color","pink");
		if($("#EntShptNewInpDateWith").val()!="" & $("#EntShptNewInpDateBy").val()=="") $("#EntShptNewInpDateBy").css("background-color","pink");
	};
	$.post(
		'enterprise/enterprise.php',
		{"Method":"ShptNewOrdersSelect", "Where":Where, "OrderBy":OrderBy},
		function (data)
		{
			console.log(data);
			var CountAll=0, CountCompliteAll=0;
			var o=jQuery.parseJSON(data); var i=0;
			while(o[i]!=null)
			{
				$("#EntShptNewOrders").append(
					"<tr onclick='EntShptNewDoorsSelect(this)' idOrder="+o[i].id+">"+
						"<td><img src='images/arrow-turn-left.png'></td>"+
						"<td></td>"+
						"<td style='padding-right:10px'>№ "+o[i].Blank+"</td>"+
						"<td colspan=3 style='border-left:solid 1px gray; padding-left:10px'> Отгружено: "+o[i].CountComplite+" / "+o[i].Count+"</td>"+
					"</tr>"
				);
				CountAll+=parseFloat(o[i].Count); CountCompliteAll+=parseFloat(o[i].CountComplite);
				i++;
			};
			$("#EntShptNewCount").text(CountAll.toString()); $("#EntShptNewCountComplite").text(CountCompliteAll.toString());
		}
	)
}
function EntShptNewDoorsSelect(el)
{
	var TR=$(el);
	if(TR.find("td:eq(0) img").attr("src")=="images/arrow-turn-left.png")
	{
		var Where="AND 1=1 ";
		if($("#EntShptNewInpBlank").val()!="") Where=Where+" AND o.Blank="+$("#EntShptNewInpBlank").val()+" ";
		if($("#EntShptNewInpDateWith").val()!="" & $("#EntShptNewInpDateBy").val()!="")
			Where=Where+"AND (nc.Step=8 AND nc.DateComplite BETWEEN STR_TO_DATE('"+$("#EntShptNewInpDateWith").val()+"', '%d.%m.%Y') AND DATE_ADD(STR_TO_DATE('"+$("#EntShptNewInpDateBy").val()+"','%d.%m.%Y'), INTERVAL 1 DAY) )";
		$.post(
			'enterprise/enterprise.php',
			{"Method":"ShptNewDoorsSelect", "idOrder":TR.attr("idOrder"), "Where":Where},
			function (data)
			{
				var o=jQuery.parseJSON(data); var i=0;
				while(o[i]!=null)
				{
					TR.after(
						"<tr onclick='EntShptNewNaryadsSelect(this)' idDoor="+o[i].id+" OrderID="+TR.attr("idOrder")+">"+
							"<td></td>"+
							"<td><img src='images/arrow-turn-left.png'></td>"+
							"<td>"+o[i].NumPP+"</td>"+
							"<td>"+o[i].Name+"</td>"+
							"<td>"+o[i].Size+"</td>"+
							"<td> Отгружено: "+o[i].CountComplite+" / "+o[i].Count+"</td>"+
						"</tr>"
					);
					i++;
				};
			}
		);
		TR.find("td:eq(0) img").attr("src","images/arrow_skip.png");
	}
	else
	{
		$("#EntShptNewOrders").find("tr[OrderID="+TR.attr("idOrder")+"]").remove();
		TR.find("td:eq(0) img").attr("src","images/arrow-turn-left.png");
	};
}
function EntShptNewNaryadsSelect(el)
{
	var TR=$(el);
	if(TR.find("td:eq(1) img").attr("src")=="images/arrow-turn-left.png")
	{
		var Where="AND 1=1 ";
		if($("#EntShptNewInpBlank").val()!="") Where=Where+" AND o.Blank="+$("#EntShptNewInpBlank").val()+" ";
		if($("#EntShptNewInpDateWith").val()!="" & $("#EntShptNewInpDateBy").val()!="")
			Where=Where+"AND (nc.Step=8 AND nc.DateComplite BETWEEN STR_TO_DATE('"+$("#EntShptNewInpDateWith").val()+"', '%d.%m.%Y') AND DATE_ADD(STR_TO_DATE('"+$("#EntShptNewInpDateBy").val()+"','%d.%m.%Y'), INTERVAL 1 DAY)) ";
		$.post(
			'enterprise/enterprise.php',
			{"Method":"ShptNewNaryadsSelect", "idDoor":TR.attr("idDoor"), "Where":Where},
			function (data)
			{	
				var o=jQuery.parseJSON(data); var i=0;
				while(o[i]!=null)
				{
					TR.after(
						"<tr onclick='EntShptNewNaryadsSelect(this)' idNaryad="+o[i].id+" DoorID="+TR.attr("idDoor")+">"+
							"<td></td>"+
							"<td></td>"+
							"<td>"+o[i].Num+"</td>"+
							"<td>"+o[i].ShptCompliteWork+"</td>"+
							"<td>"+o[i].ShptComplite+"</td>"+
							"<td onclick='EntNaryadEditStart("+o[i].id+")'><img src='images/edit.png'></td>"+
						"</tr>"
					);
					i++;
				};
			}
		);
		TR.find("td:eq(1) img").attr("src","images/arrow_skip.png");
	}
	else
	{
		$("#EntShptNewOrders").find("tr[DoorID="+TR.attr("idDoor")+"]").remove();
		TR.find("td:eq(1) img").attr("src","images/arrow-turn-left.png");
	};
}

//-------------Отчет для налоговой-----------------------------------
function EntShptNewNalog()
{
	$.post(
		'enterprise/enterprise.php',
		{"Method":"ShptNewNalogReport","Blank":$("#EntShptNewInpBlank").val(), "DateWith":$("#EntShptNewInpDateWith").val(), "DateBy":$("#EntShptNewInpDateBy").val()},
		function(data){
			location.href="enterprise/AdvancedTable.docx";
		}
	);
}

//-----------Диалог печати------------
function EntPrintDialogOpen(){
	$("#EntPrintDialogTable").find("tr").remove();
	$("#EntPrintDialogChAll").removeAttr("checked");
	$.post(
		'enterprise/enterprise.php',
		{"Method":"PrintDialogOrderLoad"},
		function(data){
			var o=jQuery.parseJSON(data); var i=0;
			while(o[i]!=null){
				$("#EntPrintDialogTable").append(
					"<tr IDOrder="+o[i].id+">"+
						"<td><img onclick='EntPrintDialogDoorLoad(this,"+o[i].id+")' src='images/arrow-turn-left.png'></td>"+
						"<td><input onchange='EntPrintDialogChChange(this)' type='checkbox'></td>"+
						"<td colspan=4 style='min-width:100px'>"+o[i].Blank+"</td>"+
						"<td>"+o[i].CountNaryad+"</td>"+
					"</tr>"
				);
				i++;
			};
			$("#EntPrintDialog").dialog("open");
		}
	);
}

function EntPrintDialogDoorLoad(el, OrderID){
	var elTR=$(el).parent().parent();
	if(elTR.find("td:eq(0) img").attr("src")=="images/arrow-turn-left.png")
	{//Расскарытие списка
		var CheckedS=elTR.find("td:eq(1) input").is(":checked")?"checked='checked'":"";
		$.post(
			'enterprise/enterprise.php',
			{"Method":"PrintDialogDoorNaryadLoad", "Type":"Doors", "id":OrderID},
			function(data){
				var o=jQuery.parseJSON(data); var i=0;
				while(o[i]!=null){
					elTR.after(
						"<tr OrderID="+OrderID+" IDDoor="+o[i].id+">"+
							"<td></td>"+
							"<td><img onclick='EntPrintDialogNaryadLoad(this,"+o[i].id+")' src='images/arrow-turn-left.png'></td>"+
							"<td><input onchange='EntPrintDialogChChange(this)' type='checkbox' "+CheckedS+"></td>"+
							"<td colspan=3 style='min-width:100px'>"+o[i].NumPP+" ("+o[i].H+" x "+o[i].W+o[i].S+")"+"</td>"+
							"<td>"+o[i].CountNaryad+"</td>"+
						"</tr>"
					);
					i++;
				};
				elTR.find("td:eq(0) img").attr("src","images/arrow_skip.png");
			}
		);
	}
	else
	{
		$("#EntPrintDialogTable").find("tr[OrderID="+OrderID+"]").remove();
		elTR.find("td:eq(0) img").attr("src","images/arrow-turn-left.png");
	};
}

function EntPrintDialogNaryadLoad(el, DoorID){
	var elTR=$(el).parent().parent();
	if(elTR.find("td:eq(1) img").attr("src")=="images/arrow-turn-left.png")
	{//Расскарытие списка
		$.post(
			'enterprise/enterprise.php',
			{"Method":"PrintDialogDoorNaryadLoad", "Type":"Naryads", "id":DoorID},
			function(data){
				var o=jQuery.parseJSON(data); var i=0;
				var CheckedS=elTR.find("td:eq(2) input").is(":checked")?"checked='checked'":"";
				while(o[i]!=null){
					elTR.after(
						"<tr OrderID="+elTR.attr("OrderID")+" DoorID="+DoorID+" IDNaryad="+o[i].id+">"+
							"<td></td>"+
							"<td></td>"+
							"<td></td>"+
							"<td><input onchange='EntPrintDialogChChange(this)' type='checkbox' "+CheckedS+"></td>"+
							"<td colspan=2 style='min-width:100px'>"+o[i].Num+"/"+o[i].NumPP+"</td>"+
							"<td>1</td>"+
						"</tr>"
					);
					i++;
				};
				elTR.find("td:eq(1) img").attr("src","images/arrow_skip.png");
			}
		);
	}
	else
	{
		$("#EntPrintDialogTable").find("tr[DoorID="+DoorID+"]").remove();
		elTR.find("td:eq(1) img").attr("src","images/arrow-turn-left.png");
	};
}

function EntPrintDialogChChange(el){
	if(!$(el).is(":checked"))
	{
		$("#EntPrintDialogChAll").removeAttr("checked");
		if($(el).parent().parent().attr("IDNaryad")!==undefined)
		{
			$("#EntPrintDialogTable tr[IDDoor="+$(el).parent().parent().attr("DoorID")+"] td:eq(2) input").removeAttr("checked");
			$("#EntPrintDialogTable tr[IDOrder="+$(el).parent().parent().attr("OrderID")+"] td:eq(1) input").removeAttr("checked");
		};
		if($(el).parent().parent().attr("IDDoor")!==undefined)
			{
				$("#EntPrintDialogTable tr[DoorID="+$(el).parent().parent().attr("IDDoor")+"]").find("td:eq(3) input").removeAttr("checked");
				$("#EntPrintDialogTable tr[IDOrder="+$(el).parent().parent().attr("OrderID")+"] td:eq(1) input").removeAttr("checked");
			};
		if($(el).parent().parent().attr("idOrder")!==undefined)
			{
				$("#EntPrintDialogTable tr[OrderID="+$(el).parent().parent().attr("idOrder")+"]").find("td:eq(3) input").removeAttr("checked");
				$("#EntPrintDialogTable tr[OrderID="+$(el).parent().parent().attr("idOrder")+"]").find("td:eq(2) input").removeAttr("checked");
			};
	}
	else
	{
		if($(el).parent().parent().attr("IDNaryad")!==undefined)
		{
			var ch=true;
			for(var i=0; i<$("#EntPrintDialogTable tr[DoorID="+$(el).parent().parent().attr("DoorID")+"]").length; i++)
				if(!$("#EntPrintDialogTable tr[DoorID="+$(el).parent().parent().attr("DoorID")+"]:eq("+i+") td:eq(3) input").is(":checked"))
					ch=false;

			if(ch)
			{
				$("#EntPrintDialogTable tr[IDDoor="+$(el).parent().parent().attr("DoorID")+"] td:eq(2) input").prop("checked","checked");

				for(var i=0; i<$("#EntPrintDialogTable tr[OrderID="+$(el).parent().parent().attr("OrderID")+"]").length; i++)
					if($("#EntPrintDialogTable tr[OrderID="+$(el).parent().parent().attr("OrderID")+"]:eq("+i+")").attr("IDNaryad")===undefined)
						if(!$("#EntPrintDialogTable tr[OrderID="+$(el).parent().parent().attr("OrderID")+"]:eq("+i+") td:eq(2) input").is(":checked"))
							ch=false;
				if(ch)
					$("#EntPrintDialogTable tr[idOrder="+$(el).parent().parent().attr("OrderID")+"] td:eq(1) input").prop("checked","checked");
			};
		};

		if($(el).parent().parent().attr("IDDoor")!==undefined){
			$("#EntPrintDialogTable tr[DoorID="+$(el).parent().parent().attr("IDDoor")+"]").find("td:eq(3) input").prop("checked","checked");
			var ch=true;
			for(var i=0; i<$("#EntPrintDialogTable tr[OrderID="+$(el).parent().parent().attr("OrderID")+"]").length; i++)
				if($("#EntPrintDialogTable tr[OrderID="+$(el).parent().parent().attr("OrderID")+"]:eq("+i+")").attr("IDNaryad")===undefined)
					if(!$("#EntPrintDialogTable tr[OrderID="+$(el).parent().parent().attr("OrderID")+"]:eq("+i+") td:eq(2) input").is(":checked"))
						ch=false;

			if(ch)
				$("#EntPrintDialogTable tr[idOrder="+$(el).parent().parent().attr("OrderID")+"] td:eq(1) input").prop("checked","checked");
		};

		if($(el).parent().parent().attr("idOrder")!==undefined)
			{
				$("#EntPrintDialogTable tr[OrderID="+$(el).parent().parent().attr("idOrder")+"]").find("td:eq(3) input").prop("checked","checked");
				$("#EntPrintDialogTable tr[OrderID="+$(el).parent().parent().attr("idOrder")+"]").find("td:eq(2) input").prop("checked","checked");
			};
	};
}

function EntPrintDialogSelectAll(){
	if($("#EntPrintDialogChAll").is(":checked"))
	{
		$("#EntPrintDialogTable").find("tr").find("td:eq(1) input").prop("checked","checked");
		$("#EntPrintDialogTable").find("tr").find("td:eq(2) input").prop("checked","checked");
		$("#EntPrintDialogTable").find("tr").find("td:eq(3) input").prop("checked","checked");
	}
	else
	{
		$("#EntPrintDialogTable").find("tr").find("td:eq(1) input").removeAttr("checked");
		$("#EntPrintDialogTable").find("tr").find("td:eq(2) input").removeAttr("checked");
		$("#EntPrintDialogTable").find("tr").find("td:eq(3) input").removeAttr("checked");
	};
}

function EntPrintDialogPrint(){
	var OrderCh=new Array();
	var DoorCh=new Array();
	var NaryadCh=new Array();
	var c1=0, c2=0, c3=0;
	for (var i = 0; i < $("#EntPrintDialogTable tr").length; i++) {
		if($("#EntPrintDialogTable tr:eq("+i+")").attr("IDOrder")!==undefined)
			if($("#EntPrintDialogTable tr:eq("+i+") td:eq(1) input").is(":checked"))
			{
				OrderCh[c1]=$("#EntPrintDialogTable tr:eq("+i+")").attr("IDOrder");
				c1++;
			};
		if($("#EntPrintDialogTable tr:eq("+i+")").attr("IDDoor")!==undefined)
			if($("#EntPrintDialogTable tr:eq("+i+") td:eq(2) input").is(":checked"))
				if(OrderCh.indexOf($("#EntPrintDialogTable tr:eq("+i+")").attr("OrderID"))==-1)
				{
					DoorCh[c2]=$("#EntPrintDialogTable tr:eq("+i+")").attr("IDDoor");
					c2++;
				};
		if($("#EntPrintDialogTable tr:eq("+i+")").attr("IDNaryad")!==undefined)
			if($("#EntPrintDialogTable tr:eq("+i+") td:eq(3) input").is(":checked"))
				if(OrderCh.indexOf($("#EntPrintDialogTable tr:eq("+i+")").attr("OrderID"))==-1)
					if(DoorCh.indexOf($("#EntPrintDialogTable tr:eq("+i+")").attr("DoorID"))==-1)
					{
						NaryadCh[c3]=$("#EntPrintDialogTable tr:eq("+i+")").attr("IDNaryad");
						c3++;
					};
	};
	$.post(
		'enterprise/enterprise.php',
		{"Method":"PrintDialogPrint", "OrderCh[]":OrderCh, "DoorCh[]":DoorCh, "NaryadCh[]":NaryadCh},
		function(data){
			if(data=="ok") {window.open("enterprise/naryad.pdf",'_blank');} else alert(data);
		}
	);

}

//------------Пакетная печать заказ / наряд-----------------------------------
function EntNaryadPackegPrint()
{
		$.post(
			'enterprise/enterprise.php',
			{"Method":"PrintNaryadPackeg"},
			function (data)
			{
				if(data=="ok")
				{
					window.open("enterprise/naryad.pdf",'_blank');
				}
				else
				{
					alert(data);
				};
			}
		);
}

//Открытие диалога выбора ФИО сотрудника
function EntManualWorkersDialogOpen(el)
{
	$("#EntManualWorkersDialogRowIndex").val($(el).parent().index());
	$("#EntManualWorkersDialogStep").val($(el).parent().attr("Step"));
	$("#EntManualWorkersDialogTableFIO").find("tr").remove();
	$.post(
		'enterprise/enterprise.php',
		{"Method":"SelectWorkers", "Step":$(el).parent().attr("step")},
		function(data){
			var o=jQuery.parseJSON(data);
			var i=0;
			while(o[i]!=null)
			{
				$("#EntManualWorkersDialogTableFIO").append(
					"<tr onclick='EntManualDialogTableTRSelect(this)' idWorker="+o[i].id+">"+
						"<td>"+o[i].Num+"</td>"+
						"<td>"+o[i].FIO+"</td>"+
					"</tr>"
				);
				i++;
			};
			$("#EntManualWorkersDialog").dialog("open");
			//Выберем ФИО из списка
			if($(el).text()!="")
			{
				$("#EntManualWorkersDialogFindFIO").val($(el).text());
				EntManualDialogFindFIO();
				for(var i=0;i<$("#EntManualWorkersDialogTableFIO").find("tr").length; i++)
					if($("#EntManualWorkersDialogTableFIO tr:eq("+i.toString()+") td:eq(1)").text().toUpperCase().indexOf($("#EntManualWorkersDialogFindFIO").val().toUpperCase())>-1)
					{
						$("#EntManualWorkersDialogTableFIO tr:eq("+i.toString()+")").attr("Selected","true");
						break;
					};
			};
		}
	);
}

function EntManualWorkersDialogComplite(){
	var idWorker="", FIOWorker="";
	if($("#EntManualWorkersDialogTableFIO tr[Selected]").length>0)
	{
		idWorker=$("#EntManualWorkersDialogTableFIO tr[Selected]").attr("idWorker");
		FIOWorker=$("#EntManualWorkersDialogTableFIO tr[Selected] td:eq(1)").text();
	};
	var TableName="";
	switch($("#EntManualWorkersDialogStep").val())
	{
		case "Svarka":TableName="EntNaryadDialogStepSvarkaWorks";break;
		case "Frame":TableName="EntNaryadDialogStepFrameWorks";break;
		case "Mdf":TableName="EntNaryadDialogStepMdfWorks";break;
		case "Sborka":TableName="EntNaryadDialogStepSborkaWorks";break;
		case "Color":TableName="EntNaryadDialogStepColorWorks";break;
		case "SborkaMdf":TableName="EntNaryadDialogStepSborkaMdfWorks";break;
		case "Upak":TableName="EntNaryadDialogStepUpakWorks";break;
		case "Shpt":TableName="EntNaryadDialogStepShptWorks";break;
	};
	var TR=$($("#"+TableName+" tr:eq("+$("#EntManualWorkersDialogRowIndex").val()+")"));
	if(TR.attr("Status")=="Load") TR.attr("Status","Edit");
	TR.find("td:eq(1)").attr("idWorker",idWorker);
	TR.find("td:eq(1)").text(FIOWorker);
	if(FIOWorker!="" & idWorker!="")
	{
		var dt=new Date();
		TR.find("td:eq(2)").text(dt.format("dd.mm.yyyy"));
		if(ParamGetValue("SkipPurposeWelder")=="1" || TableName!="EntNaryadDialogStepSvarkaWorks")
		{
			//В случае если нет назначеня сварщика тогда установим дату выполнения
			TR.find("td:eq(3)").text(dt.format("dd.mm.yyyy HH:MM:ss"));
			TR.attr("Process","Complite");
		}
		else
		{
			//В случае если стоит обязательное назначение сварщика, тогда мы очистим поле выполнения
			TR.find("td:eq(3)").text("");
			TR.attr("Process","Work");
		};
	}
	else
	{
		TR.attr("Process","Work");
		TR.find("td:eq(2)").text("");
		TR.find("td:eq(3)").text("");
	};
	$("#EntManualWorkersDialog").dialog("close");
}

function EntManualDialogTableTRSelect(el){
	//Делаем проверку если выбранный элемент не был выбран до этого тогда установим выделени
	if($(el).attr("Selected")!="true" & $(el).attr("Selected")!="selected")
	{
		$(el).parent().find("tr").removeAttr("Selected");
		$(el).attr("Selected","true");
	}
	else
		$(el).removeAttr("Selected");
}

function EntManualDialogFindFIO(){
	for(var i=0;i<$("#EntManualWorkersDialogTableFIO").find("tr").length; i++)
		if($("#EntManualWorkersDialogTableFIO tr:eq("+i.toString()+") td:eq(1)").text().toUpperCase().indexOf($("#EntManualWorkersDialogFindFIO").val().toUpperCase())>-1)
		{
			$("#EntManualWorkersDialogTableFIO tr:eq("+i.toString()+")").show();
		}
		else
			$("#EntManualWorkersDialogTableFIO tr:eq("+i.toString()+")").hide();
}
function EntManualDialogFindClear(){
	$("#EntManualWorkersDialogFindFIO").val("");
	EntManualDialogFindFIO();
}
//Установка работа выполненна в наряде для сварщика
function EntNaryadSvarkaCompliteClick(el){
	var TR=$(el).parent();
	if(ParamGetValue("SkipPurposeWelder")=="0" & TR.attr("Step")=="Svarka" & TR.find("td:eq(1)").text()!="")
		if(confirm("Установить выполнение работы?"))
		{
			var dt=new Date();
			$(el).text(dt.format("dd.mm.yyyy HH:MM:ss"));
			TR.attr("Process","Complite");
		};
}
//В наряде нажатие ДОБАВИТЬ ВЫПОЛНЕНИЕ
function EntNaryadAddProcess(Step){
	var TableName="", Cost=0;
	switch(Step)
	{
		case "Svarka":TableName="#EntNaryadDialogStepSvarkaWorks"; Cost=$("#EntNaryadDialogStepSvarkaSum").text(); break;
		case "Frame":TableName="#EntNaryadDialogStepFrameWorks"; Cost=$("#EntNaryadDialogStepFrameSum").text(); break;
		case "Mdf":TableName="#EntNaryadDialogStepMdfWorks"; Cost=$("#EntNaryadDialogStepMdfSum").text(); break;
		case "Sborka":TableName="#EntNaryadDialogStepSborkaWorks"; Cost=$("#EntNaryadDialogStepSborkaSum").text(); break;
		case "Color":TableName="#EntNaryadDialogStepColorWorks"; Cost=$("#EntNaryadDialogStepColorSum").text(); break;
		case "SborkaMdf":TableName="#EntNaryadDialogStepSborkaMdfWorks"; Cost=$("#EntNaryadDialogStepSborkaMdfSum").text(); break;
		case "Upak":TableName="#EntNaryadDialogStepUpakWorks"; Cost=$("#EntNaryadDialogStepUpakSum").text(); break;
		case "Shpt":TableName="#EntNaryadDialogStepShptWorks"; Cost=$("#EntNaryadDialogStepShptSum").text(); break;
	};
	//Делаем проверку предыдущая  стадия выполненна
	if($(TableName+" tr:last").attr("Process")=="Complite")
	{
		$(TableName).append(
			"<tr Status='Add' Step='"+Step+"' idNaryadComplite='' Process='Work'>"+
				"<td><input value='"+Cost+"'></td>"+
				"<td Cursor Step='Svarka' idWorker='' onclick='EntManualWorkersDialogOpen(this)'></td>"+
				"<td></td>"+
				"<td Cursor "+(Step=="Svarka"? "onclick='EntNaryadSvarkaCompliteClick(this)'" : "")+"></td>"+
			"</tr>"
		);

	};
}

//Разрешение погрузки в диалоге наряда
function EntNaryadDialogShptAllowBtnClick(){
	if(confirm("Подтвердите разрешение погрузки"))
		$.post(
			"MainAutorize.php",
			{"Method":"GetSession"},
			function(data){
				var o=jQuery.parseJSON(data);
				$("#EntNaryadDialogShptAllowFIO").text(o.FIO);
				var dt=new Date();
				$("#EntNaryadDialogShptAllowDate").text(dt.format("dd.mm.yyyy"));
				$("#EntNaryadDialogShptAllowBtn").hide();
				$("#EntNaryadDialogShptAllowFIO").show();
				$("#EntNaryadDialogShptAllowDate").show();
			}
		);
}