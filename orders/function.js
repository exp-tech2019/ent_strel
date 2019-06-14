var AutorizeType=0;
var AutorizeFIO="";
$.post(
	"MainAutorize.php",
	{"Method":"GetSession"},
	function(data)
	{
		var o =jQuery.parseJSON(data);
		AutorizeType=o.Type;
		AutorizeFIO=o.FIO;
	}
);
//--------Массив типов дверей------------------
var OrderGlobalTypesDoor=new Array();
var OrderGlobalTypesDoorNew=new Array();
var OrderGlobalOpenDoor=new Array();
var OrderGlobalNalichnikDoor=new Array();
var OrderGlobalDovodDoor=new Array();
$(document).ready(function(){
	$.post(
		"orders/order.php",
		{"method":"SelectTypeDoors"},
		function (data){
			var o=jQuery.parseJSON(data); var i=0;
			var html="";
			while(o.TypeDoor[i]!=null)
			{
				OrderGlobalTypesDoor[i]=o.TypeDoor[i].Name;
				OrderGlobalTypesDoorNew[i]={
					"Name":o.TypeDoor[i].Name,
					"ValueNull":o.TypeDoor[i].ValueNull
				}
				html=html+"<option>"+o.TypeDoor[i].Name+"</option>";
				i++;
			};
			$("#OrderDialogTableInputName").html(html);
			$("#OrderDialogDoorInputName").html(html);
			$("#OrderFilterDoorsName").html(html);
			//Открывание
			html=""; i=0;
			while(o.TypeOpenDoor[i]!=null)
			{
				OrderGlobalOpenDoor[i]=o.TypeOpenDoor[i];
				html=html+"<option>"+o.TypeOpenDoor[i]+"</option>";
				i++;
			};
			$("#OrderDialogTableInputOpen").html(html);
			$("#OrderDialogDoorInputOpen").html(html);
			//Наличник
			html=""; i=0;
			while(o.TypeNalichnikDoor[i]!=null)
			{
				OrderGlobalNalichnikDoor[i]=o.TypeNalichnikDoor[i];
				html=html+"<option>"+o.TypeNalichnikDoor[i]+"</option>";
				i++;
			};
			$("#OrderDialogTableInputNalichnik").html(html);
			$("#OrderDialogDoorInputNalichnik").html(html);
			//Доводчик
			html=""; i=0;
			while(o.TypeDovodDoor[i]!=null)
			{
				OrderGlobalDovodDoor[i]=o.TypeDovodDoor[i];
				html=html+"<option>"+o.TypeDovodDoor[i]+"</option>";
				i++;
			};
			$("#OrderDialogTableInputDovod").html(html);
			$("#OrderDialogDoorInputDovod").html(html);
		}
	);
});
//--------Фильтр левое меню--------------------
var OrdersWhere="AND (o.status=0 OR o.status=1 OR o. status=2) ";
//Поиск по параметрам заказа
function OrderFilterZakaz()
{
	var where='';
	if($('#OrderFilterBlank').val()!='')
		where=' AND o.Blank='+$('#OrderFilterBlank').val();
	if($('#OrderFilterBlankDateWith').val()!='')
		where=where + "AND o.BlankDate>=STR_TO_DATE('"+$('#OrderFilterBlankDateWith').val()+"', '%d.%m.%Y')";
	if($('#OrderFilterBlankDateDo').val()!='')
		where=where + "AND o.BlankDate<=STR_TO_DATE('"+$('#OrderFilterBlankDateDo').val()+"', '%d.%m.%Y')";
	if($('#OrderFilterShet').val()!='')
		where=where + " AND o.Shet='"+$('#OrderFilterShet').val()+"'";
	if($('#OrderFilterZakaz').val()!='')
		where=where + " AND o.Zakaz LIKE '%"+$('#OrderFilterZakaz').val()+"%'";
	if($('#OrderFilterContact').val()!='')
		where=where + " AND o.Contact LIKE '%"+$('#OrderFilterContact').val()+"%'";
	OrdersWhere=where;
	OrderSelect();
}
//Поиск по параметрам дверей
function OrderFilterDoorsZakaz()
{
	var where='';
	if($('#OrderFilterDoorsH').val()!='')
		where=where +' AND od.H='+$('#OrderFilterDoorsH').val();
		if($('#OrderFilterDoorsW').val()!='')
		where=where +' AND od.W='+$('#OrderFilterDoorsW').val();
	if($('#OrderFilterDoorsName').val()!='')
		where=where + " AND od.Name = '"+$('#OrderFilterDoorsName').val()+"'";
	if($('#OrderFilterDoorsRAL').val()!='')
		where=where + " AND od.RAL LIKE '%"+$('#OrderFilterDoorsRAL').val()+"%'";
	if($("#OrderFilterDoorsShtild").val()!="")
		where=where + " AND od.Shtild<="+$("#OrderFilterDoorsShtild").val()+" AND od.Shtild+d.Count>"+$("#OrderFilterDoorsShtild").val();
	OrdersWhere=where;
	OrderSelect();
}

//------------Функции работы с заказами*---------------------------------------------
//Отображение зааза по статусу
function OrderRefresh(status)
{
	var where="";
	switch(status)
	{
		case 'All': where="AND (o.status=0 OR o.status=1 OR o.status=2 OR o.status=3)";
		break;
        case 'New': where="AND (o.status=0) ";
            break;
		case 'Work': where="AND (o.status=1 OR o. status=2) ";
		break;
		case 'Complite': where="AND o.status=3";
		break;
		case "Cancel": where="AND o.status=-1";
		break;
	};
	OrdersWhere=where;
	OrderSelect();
}
//Список заказов
var OrderSelectList=new Array();
var RowDisplay=0;
function OrderSelect()
{
	$('#OrderTable').find('tr').remove();
	RowDisplay=0;
	$("#OrderTableLoader").show();
	$.post(
		'orders/order.php',
		{
			'method':'select',
			'Where':OrdersWhere
		},
		function (data)
		{
			OrderSelectList.length=0;
			$("#OrderTableLoader").hide();
			var o=jQuery.parseJSON(data);
			var i=0;
			while(o[i]["id"]!=null)
			{
				var OrderSelectRow={
					id:o[i]['id'],
					Color:o[i]['Color'],
					UserBlock:o[i].UserBlock,
					Blank:o[i].Blank,
					BlankDate:o[i].BlankDate,
					Shet:o[i].Shet,
					ShetDate:o[i].ShetDate,
					ColorAlertTD:o[i].ColorAlertTD,
					Srok:o[i].Srok,
					DoorsCount:o[i].DoorsCount,
					Zakaz:o[i].Zakaz,
					Manager:o[i].Manager
				};
				OrderSelectList[i]=OrderSelectRow;
				
				if(RowDisplay<30)
				{
					$("#OrderTable").append(
						'<tr class='+o[i]['Color']+' id=OrderTableTR'+o[i]['id']+' onClick="OrderEditFunction('+o[i]['id']+')">'+
							'<td>'+(o[i].UserBlock!=null? "<img src='images/lock.png' width=15 title='"+o[i].UserBlock+"'>" : "")+'</td>'+
							'<td>'+o[i]['Blank']+'</td>'+
							'<td>'+o[i]['BlankDate']+'</td>'+
							'<td>'+o[i]['Shet']+'</td>'+
							'<td>'+o[i]['ShetDate']+'</td>'+
							'<td title="3434" class='+o[i]['ColorAlertTD']+'>'+o[i]['Srok']+'</td>'+
							'<td>'+o[i].DoorsCount+'</td>'+
							'<td>'+o[i]['Zakaz']+'</td>'+
							'<td>'+o[i]['Manager']+'</td>'+
						'</tr>'
					);
					RowDisplay++;
				};
				i++;
			};

		}
	)
	.fail( function(){ 
		alert('Произошла ошибка сохранения');
	});
}
//------Подгрузка контента-----
$(document).ready(function(){
	var OrederDiv=$("#OrderTableDiv");
	var OrederTable=$("#OrderTable");
	$("#OrderTableDiv").scroll(function(){
		if(OrederDiv.offset().top+OrederDiv.height()+10>OrederTable.find("tr:last").offset().top+OrederTable.find("tr:last td:first").height())
		{
			for(var i=0;i<15;i++)
			{
				var RW=OrderSelectList[RowDisplay];
				$("#OrderTable").append(
					'<tr class='+RW.Color+' id=OrderTableTR'+RW.id+' onClick="OrderEditFunction('+RW.id+')">'+
						'<td>'+(RW.UserBlock!=null? "<img src='images/lock.png' width=15 title='"+RW.UserBlock+"'>" : "")+'</td>'+
						'<td>'+RW.Blank+'</td>'+
						'<td>'+RW.BlankDate+'</td>'+
						'<td>'+RW.Shet+'</td>'+
						'<td>'+RW.ShetDate+'</td>'+
						'<td class='+RW.ColorAlertTD+'>'+RW.Srok+'</td>'+
						'<td>'+RW.DoorsCount+'</td>'+
						'<td>'+RW.Zakaz+'</td>'+
						'<td>'+RW.Manager+'</td>'+
					'</tr>'
				);
				RowDisplay++;
			};
		};
	});
});

//Создание дубля заявки
function OrderCopy()
{
	if($("#orderDialogInputID").val()=="")
	{
		alert("Невозможно скопировать новую заявку!");
	}
	else
	if(confirm("Произвести копирование заявки: "+$("#orderDialogInputBlank").val()+"?"))
		$.post(
			'orders/order.php',
			{"method":"OderCopy", "idOld":$("#orderDialogInputID").val()},
			function (data)
			{
				if(data=="ok")
				{	alert("Заказ скопирован.");	}
				else alert("Произошли следующие ошибки: "+data);
			}
		)
}

//Удаление заказа
function OrderDelete ()
{
	if($("#orderDialogInputID").val()=="")
	{
		alert("Нельзя удалить новый заказ!");
	}
	else
		if(confirm('Удалить заказ?'))
			$.post(
				'orders/order.php',
				{ 'method':'Delete', 'id':$("#orderDialogInputID").val() },
				function (data)
				{
					if(data=="ok")
					{
					$( '#orderDialog' ).dialog( "close" );
					$("#OrderTableTR"+$("#orderDialogInputID").val()).remove();
					}
					else alert("При удалении произошли следующие ошибки: "+data)
				}
			);
}
//Установка/Снятие статуса ОТМЕНЕН
function OrderSetNoSetStatusCancel()
{
	var Operation="Set"; var str="Снять с производства";
	if($("#orderDlalogBtnStatusStr").text()=="-1") {Operation="NoSet"; str="Вернуть в производство"; };
	if(confirm(str+"?"))
	{
				$.post(
					"orders/order.php",
					{"method":"SetNoSetStatusCancel", "id":$("#orderDialogInputID").val(), "Operation":Operation},
					function(data)
					{
						if(data==true)
						{$("#orderDialog").dialog("close");}
						else { alert(data); };
					}
				);
	};
}
//Открытие заказа на редактирование
function OrderEditFunction(idOrder)
{
	$.post(
		'orders/order.php',
		{ 'method':'EditStart', 'id':idOrder },
		function (data)
		{

			$("#orderDialogInputID").val(idOrder);
			OrderDialogLoad();
			var obj =jQuery.parseJSON(data);
			$("#orderDialogInputGUID").val(obj.DialogGUID);
			//Обработка блокировки заказа другим сотрудником
			if(obj.UserBlock!=null & obj.UserBlock!=AutorizeFIO & AutorizeType!=4)
			{
				$("#OrderDialogPanelButton").hide();
				$('.ui-dialog-buttonpane button:contains("Сохранить")').button().hide();
				$('.ui-dialog-buttonpane button:contains("Удалить")').button().hide();
				$("#OrderDialogBlockStatus").text("Заказ заблокирован: "+obj.UserBlock);
			};
			
			if(AutorizeType!=4) if(obj.Status==-1 )  { $("#orderDlalogBtnStatus0").show(); $("#orderDlalogBtnStatus1").hide(); } else {$("#orderDlalogBtnStatus0").hide(); $("#orderDlalogBtnStatus1").show(); };
			$("#orderDlalogBtnStatusStr").text(obj.Status);
			$('#orderDialogInputBlankDate').val(obj.BlankDate);
			$('#orderDialogInputBlank').val(obj.Blank);
			$('#orderDialogInputShet').val(obj.Shet);
			$('#orderDialogInputShetDate').val(obj.ShetDate);
			$('#OrderDialogInputSrok').val(obj.Srok);
			$('#OrderDialogInputZakaz').val(obj.Zakaz);
			$('#OrderDialogInputContact').val(obj.Contact);
			$('#OrderDialogInputNote').val(obj.NoteZakaz);
			$("#OrderDialogInputManager").text(obj.Manager);
			//Обработка статуса заказа
			$("#orderDialogInputStatus").val(obj.Status);
			if(obj.Status=="2")
			{
				$( "#orderDialogInputStatusCh" ).show();
				$("#orderDialogInputStatusCh").attr("class","StatusBlue");
			};
			if(obj.Status=="3")
			{
				$( "#orderDialogInputStatusCh" ).show();
				$("#orderDialogInputStatusCh").attr("class","StatusGreen");
			};
						
			var i=0;
			OderDialogTablePOS=1;
			var DoorCountAll=0;
			var DoorUpakCountAll=0;
			var DoorShptCountAll=0;
			while( obj.Doors[i]!=null)
			{
				//Расчитаем общще кол-во упакованных и отгруженных дверей
				DoorCountAll+=parseInt(obj.Doors[i].Count);
				DoorUpakCountAll+=parseInt(obj.Doors[i].NaryadCompliteCount);
				DoorShptCountAll+=parseInt(obj.Doors[i].NaryadShptCount);
				//
				var c=i+1;
				var S0=obj.Doors[i].S;
				if(S0==0) S0='';
				//Расчитаем кол-во упакованных дверей
				var DoorTRColor="Start";
				if(obj.Doors[i].NaryadCount!=0) DoorTRColor="Work";
				if(obj.Doors[i].Count==obj.Doors[i].NaryadCompliteCount & obj.Doors[i].NaryadCompliteCount!=0) DoorTRColor="Complite";
				//Проверка на заполненность поля стоимость, если не заполненно обводим красным
				var ColorButtonCost=" border:2px solid green; ";
				if(obj.Doors[i].CostSvarka==0) ColorButtonCost=" border:2px solid red; ";
				if(obj.Doors[i].CostFrame==0 & ((obj.Doors[i].WorkWindowCh=="true" & obj.Doors[i].WorkWindowNoFrame!="true") || (obj.Doors[i].StvorkaWindowCh=="true" & obj.Doors[i].StvorkaWindowNoFrame!="true") || (obj.Doors[i].FramugaWindowCh=="true" & obj.Doors[i].FramugaWindowNoFrame!="true") )) ColorButtonCost=" border:2px solid red; ";
				if(obj.Doors[i].CostSborka==0) ColorButtonCost=" border:2px solid red; ";
				if(obj.Doors[i].CostColor==0) ColorButtonCost=" border:2px solid red; ";
				if(obj.Doors[i].CostUpak==0) ColorButtonCost=" border:2px solid red; ";
                //Определение, заполнениа спецификация
                var ColorSpeStatistics="";
                if(obj.Doors[i].StatusComplite==0) ColorSpeStatistics="border:2px solid blue";
                if(obj.Doors[i].StatusComplite!=0 & obj.Doors[i].DataSuccess==0) ColorSpeStatistics="border:2px solid red";
                if(obj.Doors[i].StatusComplite!=0 & obj.Doors[i].DataSuccess==1) ColorSpeStatistics="border:2px solid green";
				
				var NoteStr=obj.Doors[i].Note; if(obj.Doors[i].Note.length>65) NoteStr=obj.Doors[i].Note.substring(0,65)+" <img src='images/DocumentNext.png'>";
				var BtnEdit='<td onclick="OrderDoorEditStart(this)"><button title="Редактировать">e</button></td>'; //if(DoorTRColor=="Complite" || (DoorTRColor=="Work" & AutorizeType!=1)) BtnEdit="<td></td>";
				var BtnInProduction="";
				if(DoorTRColor=="Start" /*& obj.Doors[i].name.indexOf("труба")>-1*/)
					BtnInProduction="<button onClick='OrderTubeInProduction(this)' title='Передать в производство'>></button>";
				if(DoorTRColor=="Work" /*& obj.Doors[i].name.indexOf("труба")>-1*/)
					BtnInProduction="<button onClick='OrderTubeInProduction(this)' title='Вернуть из производства'><</button>";
				var BtnDel='<td onclick="OrderDialogTableDelRow(this)"><button title="Удалить">x</button></td>'; if(DoorTRColor=="Complite" || DoorTRColor=="Work" || AutorizeType==4) BtnDel="<td></td>";
				$('#OrderDialogTable').last().append(
					'<tr status="Load" class='+DoorTRColor+' id="OTR'+guidSmall()+'" idDoor='+obj.Doors[i].id+'>'+
						BtnEdit+
						'<td type=Num>'+obj.Doors[i].NumPP+'</td>'+
						'<td type=Name>'+obj.Doors[i].name+'</td>'+
						'<td type=Count>'+obj.Doors[i].Count+'</td>'+
						'<td type=H>'+obj.Doors[i].H+'</td>'+
						'<td type=W>'+obj.Doors[i].W+'</td>'+
						'<td type=Open>'+obj.Doors[i].Open+'</td>'+
						'<td type=S>'+S0+'</td>'+
						'<td type=RAL>'+obj.Doors[i].RAL+'</td>'+
						'<td type=Nalichnik>'+obj.Doors[i].Nalichnik+'</td>'+
						'<td type=Dovod>'+obj.Doors[i].Dovod+'</td>'+
						'<td type=Note style="text-align:left;" onclick=" OrderDialogNoteView(this)">'+NoteStr+' <note style="display:none">'+obj.Doors[i].Note+'</note></td>'+
						'<td type=Markirovka>'+obj.Doors[i].Markirovka+'</td>'+
						'<td type=Shtild>'+obj.Doors[i].Shtild+'</td>'+
						'<td type=PetlyaWrk>'+obj.Doors[i].WorkPetlya+'</td>'+
						'<td type=PetlyaStv>'+obj.Doors[i].StvorkaPetlya+'</td>'+
						'<td type=WindowWrk>'+(obj.Doors[i].WorkWindowCh=="true" ? "<input type=checkbox checked disabled>" : "<input type=checkbox disabled>")+'</td>'+
						'<td type=WindowStv>'+(obj.Doors[i].StvorkaWindowCh=="true" ? "<input type=checkbox checked disabled>" : "<input type=checkbox disabled>")+'</td>'+
						'<td type=FramugaCh>'+(obj.Doors[i].FramugaCh=="true" ? "<input type=checkbox checked disabled>" : "<input type=checkbox disabled>")+'</td>'+
						"<td type=FramugaH>"+obj.Doors[i].FramugaH+"</td>"+
						'<td onclick=OrderDoorProcessingOpen('+obj.Doors[i].id+')><button title="Выполнение">...</button></td>'+
						'<td type=Construct><button style="'+(AutorizeType!=1 & DoorTRColor!="Start"? "display:none; ":"")+'" onclick=OrderDoorConstructOpen(this) title="Конструктор">...</button>'+
							'<span style="display:none;">'+
								'<WorkPetlya>'+obj.Doors[i].WorkPetlya+'</WorkPetlya>'+
								'<WorkWindowCh>'+obj.Doors[i].WorkWindowCh+'</WorkWindowCh>'+
									'<WorkWindowNoFrame>'+obj.Doors[i].WorkWindowNoFrame+'</WorkWindowNoFrame>'+
									'<WorkWindowH>'+obj.Doors[i].WorkWindowH+'</WorkWindowH>'+
									'<WorkWindowW>'+obj.Doors[i].WorkWindowW+'</WorkWindowW>'+
									'<WorkWindowGain>'+obj.Doors[i].WorkWindowGain+'</WorkWindowGain>'+
									'<WorkWindowGlass>'+obj.Doors[i].WorkWindowGlass+'</WorkWindowGlass>'+
									'<WorkWindowGlassType>'+obj.Doors[i].WorkWindowGlassType+'</WorkWindowGlassType>'+
									'<WorkWindowGrid>'+obj.Doors[i].WorkWindowGrid+'</WorkWindowGrid>'+
								'<WorkWindowCh1>'+obj.Doors[i].WorkWindowCh1+'</WorkWindowCh1>'+
									'<WorkWindowNoFrame1>'+obj.Doors[i].WorkWindowNoFrame1+'</WorkWindowNoFrame1>'+
									'<WorkWindowH1>'+obj.Doors[i].WorkWindowH1+'</WorkWindowH1>'+
									'<WorkWindowW1>'+obj.Doors[i].WorkWindowW1+'</WorkWindowW1>'+
									'<WorkWindowGain1>'+obj.Doors[i].WorkWindowGain1+'</WorkWindowGain1>'+
									'<WorkWindowGlass1>'+obj.Doors[i].WorkWindowGlass1+'</WorkWindowGlass1>'+
									'<WorkWindowGlassType1>'+obj.Doors[i].WorkWindowGlassType1+'</WorkWindowGlassType1>'+
									'<WorkWindowGrid1>'+obj.Doors[i].WorkWindowGrid1+'</WorkWindowGrid1>'+
								'<WorkWindowCh2>'+obj.Doors[i].WorkWindowCh2+'</WorkWindowCh2>'+
									'<WorkWindowNoFrame2>'+obj.Doors[i].WorkWindowNoFrame2+'</WorkWindowNoFrame2>'+
									'<WorkWindowH2>'+obj.Doors[i].WorkWindowH2+'</WorkWindowH2>'+
									'<WorkWindowW2>'+obj.Doors[i].WorkWindowW2+'</WorkWindowW2>'+
									'<WorkWindowGain2>'+obj.Doors[i].WorkWindowGain2+'</WorkWindowGain2>'+
									'<WorkWindowGlass2>'+obj.Doors[i].WorkWindowGlass2+'</WorkWindowGlass2>'+
									'<WorkWindowGlassType2>'+obj.Doors[i].WorkWindowGlassType2+'</WorkWindowGlassType2>'+
									'<WorkWindowGrid2>'+obj.Doors[i].WorkWindowGrid2+'</WorkWindowGrid2>'+
								"<WorkUpGridCh>"+obj.Doors[i].WorkUpGridCh+'</WorkUpGridCh>'+
								"<WorkDownGridCh>"+obj.Doors[i].WorkDownGridCh+'</WorkDownGridCh>'+
								'<StvorkaCh>'+obj.Doors[i].StvorkaCh+'</StvorkaCh>'+
									'<StvorkaPetlya>'+obj.Doors[i].StvorkaPetlya+'</StvorkaPetlya>'+
									'<StvorkaWindowCh>'+obj.Doors[i].StvorkaWindowCh+'</StvorkaWindowCh>'+
										'<StvorkaWindowNoFrame>'+obj.Doors[i].StvorkaWindowNoFrame+'</StvorkaWindowNoFrame>'+
										'<StvorkaWindowH>'+obj.Doors[i].StvorkaWindowH+'</StvorkaWindowH>'+
										'<StvorkaWindowW>'+obj.Doors[i].StvorkaWindowW+'</StvorkaWindowW>'+
										'<StvorkaWindowGain>'+obj.Doors[i].StvorkaWindowGain+'</StvorkaWindowGain>'+
										'<StvorkaWindowGlass>'+obj.Doors[i].StvorkaWindowGlass+'</StvorkaWindowGlass>'+
										'<StvorkaWindowGlassType>'+obj.Doors[i].StvorkaWindowGlassType+'</StvorkaWindowGlassType>'+
										'<StvorkaWindowGrid>'+obj.Doors[i].StvorkaWindowGrid+'</StvorkaWindowGrid>'+
									'<StvorkaWindowCh1>'+obj.Doors[i].StvorkaWindowCh1+'</StvorkaWindowCh1>'+
										'<StvorkaWindowNoFrame1>'+obj.Doors[i].StvorkaWindowNoFrame1+'</StvorkaWindowNoFrame1>'+
										'<StvorkaWindowH1>'+obj.Doors[i].StvorkaWindowH1+'</StvorkaWindowH1>'+
										'<StvorkaWindowW1>'+obj.Doors[i].StvorkaWindowW1+'</StvorkaWindowW1>'+
										'<StvorkaWindowGain1>'+obj.Doors[i].StvorkaWindowGain1+'</StvorkaWindowGain1>'+
										'<StvorkaWindowGlass1>'+obj.Doors[i].StvorkaWindowGlass1+'</StvorkaWindowGlass1>'+
										'<StvorkaWindowGlassType1>'+obj.Doors[i].StvorkaWindowGlassType1+'</StvorkaWindowGlassType1>'+
										'<StvorkaWindowGrid1>'+obj.Doors[i].StvorkaWindowGrid1+'</StvorkaWindowGrid1>'+
									'<StvorkaWindowCh2>'+obj.Doors[i].StvorkaWindowCh2+'</StvorkaWindowCh2>'+
										'<StvorkaWindowNoFrame2>'+obj.Doors[i].StvorkaWindowNoFrame2+'</StvorkaWindowNoFrame2>'+
										'<StvorkaWindowH2>'+obj.Doors[i].StvorkaWindowH2+'</StvorkaWindowH2>'+
										'<StvorkaWindowW2>'+obj.Doors[i].StvorkaWindowW2+'</StvorkaWindowW2>'+
										'<StvorkaWindowGain2>'+obj.Doors[i].StvorkaWindowGain2+'</StvorkaWindowGain2>'+
										'<StvorkaWindowGlass2>'+obj.Doors[i].StvorkaWindowGlass2+'</StvorkaWindowGlass2>'+
										'<StvorkaWindowGlassType2>'+obj.Doors[i].StvorkaWindowGlassType2+'</StvorkaWindowGlassType2>'+
										'<StvorkaWindowGrid2>'+obj.Doors[i].StvorkaWindowGrid2+'</StvorkaWindowGrid2>'+
									"<StvorkaUpGridCh>"+obj.Doors[i].StvorkaUpGridCh+'</StvorkaUpGridCh>'+
									"<StvorkaDownGridCh>"+obj.Doors[i].StvorkaDownGridCh+'</StvorkaDownGridCh>'+
								'<FramugaCh>'+obj.Doors[i].FramugaCh+'</FramugaCh>'+
									'<FramugaH>'+obj.Doors[i].FramugaH+'</FramugaH>'+
									'<FramugaWindowCh>'+obj.Doors[i].FramugaWindowCh+'</FramugaWindowCh>'+
										'<FramugaWindowNoFrame>'+obj.Doors[i].FramugaWindowNoFrame+'</FramugaWindowNoFrame>'+
										'<FramugaWindowH>'+obj.Doors[i].FramugaWindowH+'</FramugaWindowH>'+
										'<FramugaWindowW>'+obj.Doors[i].FramugaWindowW+'</FramugaWindowW>'+
										'<FramugaWindowGain>'+obj.Doors[i].FramugaWindowGain+'</FramugaWindowGain>'+
										'<FramugaWindowGlass>'+obj.Doors[i].FramugaWindowGlass+'</FramugaWindowGlass>'+
										'<FramugaWindowGlassType>'+obj.Doors[i].FramugaWindowGlassType+'</FramugaWindowGlassType>'+
										'<FramugaWindowGrid>'+obj.Doors[i].FramugaWindowGrid+'</FramugaWindowGrid>'+
									"<FramugaUpGridCh>"+obj.Doors[i].FramugaUpGridCh+'</FramugaUpGridCh>'+
									"<FramugaDownGridCh>"+obj.Doors[i].FramugaDownGridCh+'</FramugaDownGridCh>'+
									"<Antipanik>"+obj.Doors[i].Antipanik+"</Antipanik>"+
									"<Otboynik>"+obj.Doors[i].Otboynik+"</Otboynik>"+
									"<Wicket>"+obj.Doors[i].Wicket+"</Wicket>"+
									"<BoxLock>"+obj.Doors[i].BoxLock+"</BoxLock>"+
									"<Otvetka>"+obj.Doors[i].Otvetka+"</Otvetka>"+
									"<Isolation>"+obj.Doors[i].Isolation+"</Isolation>"+
							'</span>'+
						'  </td>'+
						'<td type=Cost><button onclick=" OrderCostDialogLoad(this)" style=" '+((AutorizeType==4 || AutorizeType==3) ? "display:none; " : "")+'; '+ColorButtonCost+' " title="Стоимость">...</button>'+
							'<span style="display:none;  ">'+
								'<CostLaser>'+obj.Doors[i].CostLaser+'</CostLaser>'+
								'<CostSgibka>'+obj.Doors[i].CostSgibka+'</CostSgibka>'+
								'<CostSvarka>'+obj.Doors[i].CostSvarka+'</CostSvarka>'+
								'<CostFrame>'+obj.Doors[i].CostFrame+'</CostFrame>'+
								'<CostMdf>'+obj.Doors[i].CostMdf+'</CostMdf>'+
								'<CostSborka>'+obj.Doors[i].CostSborka+'</CostSborka>'+
								'<CostSborkaMdf>'+obj.Doors[i].CostSborkaMdf+'</CostSborkaMdf>'+
								'<CostColor>'+obj.Doors[i].CostColor+'</CostColor>'+
								'<CostUpak>'+obj.Doors[i].CostUpak+'</CostUpak>'+
								'<CostShpt>'+obj.Doors[i].CostShpt+'</CostShpt>'+
							'</span>'+
						'</td>'+
                    "<td Type=Spe><button style='"+(AutorizeType!=1 & DoorTRColor!="Start"? "display:none; ":"")+ColorSpeStatistics+"' onclick='Sp18.OpenDialog(this)' title='Спецификация'>...</button></td>"+
						"<td type=TubeInProduction>"+BtnInProduction+"</td>"+
						BtnDel+
					'</tr>'
				);
				OderDialogTablePOS++;
				i++;
			};
			//Вверхней части диалога выведем кол-во дверей, упакованных и отгруженных
			$("#OrderDialogPanelSummary").show();
			$("#OrderDialogPanelSummary button span").text("Всего:"+DoorCountAll.toString()+" Упаков: "+DoorUpakCountAll.toString()+" Отгружен: "+DoorShptCountAll.toString());
		}
	)
	.fail(function ()	{ alert('Произошла ошибка'); });
}
//Передать изделие из трубы в производство
function OrderTubeInProduction(el){
	var elTR=$(el).parent().parent();
	if(elTR.attr("status")=="Load" & (elTR.attr("class")=="Start" || elTR.attr("class")=="Work") & elTR.attr("idDoor")!=""){
		if(confirm("Выполнить действие?"))
		{
			elTR.find("td[Type=TubeInProduction] button").hide();
			$.post(
				"orders/order.php",
				{"method":"OrderTubeInProduction", "idDoor":elTR.attr("idDoor"), "Status":elTR.attr("class")},
				function (data){
					if(data=="ok"){
						elTR.find("td[Type=TubeInProduction] button").show();
						switch(elTR.attr("class"))
						{
							case "Start":
								elTR.find("td[Type=TubeInProduction] button").text("<");
							 	elTR.attr("class","Work");
								$("#OrderTable tr[id=OrderTableTR"+$("#orderDialogInputID").val()+"]").attr("class","Work");
								$("#orderDialogInputStatus").val(1);
							break;
							case "Work":
								elTR.find("td[Type=TubeInProduction] button").text(">");
								elTR.attr("class","Start");
								var flagWork=false;
								for(var i=0; i<$("#OrderDialogTable tr").length; i++)
									if($("#OrderDialogTable tr:eq("+i+")").attr("class")=="Work")
										flagWork=true;
								if(!flagWork)
								{
									$("#OrderTable tr[id=OrderTableTR"+$("#orderDialogInputID").val()+"]").attr("class","Start");
									$("#orderDialogInputStatus").val(0);
								};
							break;
						};
					}
					else alert(data);
				}
			);
		};
	}
	else
		alert("Не сохраненную позицию нельзя передать в производство!");
}
//Событие закрытия диалога заказа (для сброса блокировки)
function OrderClose()
{
	if($("#orderDialogInputID").val()!="" & AutorizeType!=4 & $("#OrderDialogPanelButton").is(":visible"))
		$.post(
			"orders/order.php",
			{"method":"OrederClose","idOrder":$("#orderDialogInputID").val()},
			function (data){}
		)
}
//Печать списка заказов
function OrderPrint()
{
	$.post(
		'orders/order.php',
		{ 'method':'OrdersPrint', 'Table':$('#OrderTable').html() },
		function(data)
		{
			window.open('orders/OrdersList.pdf','_blank');
		}
	);
}
//Печать карточки заказа
function OrderDialogPrint()
{
	var OrderDialogTableTDNumArr=[];
	var OrderDialogTableTDNameArr=[];
	var OrderDialogTableTDHArr=[];
	var OrderDialogTableTDWArr=[];
	var OrderDialogTableTDSArr=[];
	var OrderDialogTableTDOpenArr=[];
	var OrderDialogTableTDNalichnikArr=[];
	var OrderDialogTableTDDovodArr=[];
	var OrderDialogTableTDRALArr=[];
	var OrderDialogTableTDNoteArr=[];
	var OrderDialogTableTDMarkirovkaArr=[];
	var OrderDialogTableTDCountArr=[];
	var OrderDialogTableTDShtildArr=[];
	var p=0;
	for(var i=0;i<$("#OrderDialogTable tr").length;i++)
		if($("#OrderDialogTable tr:eq("+i.toString()+")").attr("status")!="Del")
		{
			OrderDialogTableTDNumArr[p]=$('#OrderDialogTable tr:eq('+i.toString()+") td[type=Num]").text();
			OrderDialogTableTDNameArr[p]=$('#OrderDialogTable tr:eq('+i.toString()+") td[type=Name]").text();
			OrderDialogTableTDHArr[p]=$('#OrderDialogTable tr:eq('+i.toString()+") td[type=H]").text();
			OrderDialogTableTDWArr[p]=$('#OrderDialogTable tr:eq('+i.toString()+") td[type=W]").text();
			OrderDialogTableTDSArr[p]=$('#OrderDialogTable tr:eq('+i.toString()+") td[type=S]").text();
			OrderDialogTableTDOpenArr[p]=$('#OrderDialogTable tr:eq('+i.toString()+") td[type=Open]").text();
			OrderDialogTableTDNalichnikArr[p]=$('#OrderDialogTable tr:eq('+i.toString()+") td[type=Nalichnik]").text();
			OrderDialogTableTDDovodArr[p]=$('#OrderDialogTable tr:eq('+i.toString()+") td[type=Dovod]").text();
			OrderDialogTableTDRALArr[p]=$('#OrderDialogTable tr:eq('+i.toString()+") td[type=RAL]").text();
			OrderDialogTableTDNoteArr[p]=$('#OrderDialogTable tr:eq('+i.toString()+") td[type=Note] note").text();
			OrderDialogTableTDMarkirovkaArr[p]=$('#OrderDialogTable tr:eq('+i.toString()+") td[type=Markirovka]").text();
			OrderDialogTableTDCountArr[p]=$('#OrderDialogTable tr:eq('+i.toString()+") td[type=Count]").text();
			OrderDialogTableTDShtildArr[p]=$('#OrderDialogTable tr:eq('+i.toString()+") td[type=Shtild]").text();
			p++;
		};
		$.post(
			'orders/order.php',
			{
				'method':'PDF',
				'Blank':$('#orderDialogInputBlank').val(),
				'BlankDate':$('#orderDialogInputBlankDate').val(),
				'Blank':$('#orderDialogInputBlank').val(),
				'Shet':$('#orderDialogInputShet').val(),
				'ShetDate':$('#orderDialogInputShetDate').val(),
				'Srok':$('#OrderDialogInputSrok').val(),
				'Zakaz':$('#OrderDialogInputZakaz').val(),
				'Contact':$('#OrderDialogInputContact').val(),
				'Manager':$('#OrderDialogInputManager').text(),
				'Note':$('#OrderDialogInputNote').val(),
				'OrderDialogTableTDNumArr[]':OrderDialogTableTDNumArr,
				'OrderDialogTableTDNameArr[]':OrderDialogTableTDNameArr,
				'OrderDialogTableTDHArr[]':OrderDialogTableTDHArr,
				'OrderDialogTableTDWArr[]':OrderDialogTableTDWArr,
				'OrderDialogTableTDSArr[]':OrderDialogTableTDSArr,
				'OrderDialogTableTDOpenArr[]':OrderDialogTableTDOpenArr,
				'OrderDialogTableTDNalichnikArr[]':OrderDialogTableTDNalichnikArr,
				'OrderDialogTableTDDovodArr[]':OrderDialogTableTDDovodArr,
				'OrderDialogTableTDRALArr[]':OrderDialogTableTDRALArr,
				'OrderDialogTableTDNoteArr[]':OrderDialogTableTDNoteArr,
				'OrderDialogTableTDMarkirovkaArr[]':OrderDialogTableTDMarkirovkaArr,
				'OrderDialogTableTDCountArr[]':OrderDialogTableTDCountArr,
				'OrderDialogTableTDShtildArr[]':OrderDialogTableTDShtildArr
			},
			function(data)
			{
				window.open('orders/Zakaz'+$('#orderDialogInputBlank').val()+'.pdf','_blank');
			}
		);
}
//Печать Маркировки
function OrderDialogPrintMarkirovka()
{
	var aDoorPos=new Array();
	var aName=new Array();
	var aH=new Array();
	var aW=new Array();
	var aS=new Array();
	var aOpen=new Array();
	var aMarkirovka=new Array();
	var aRAL=new Array();
	var aShtild=new Array();
	var aCount=new Array();
	for(var i=0;i<$("#OrderDialogTable tr").length;i++)
	{
		aDoorPos[i]=$("#OrderDialogTable tr:eq("+i.toString()+") td[type=Num]").text();
		aName[i]=$("#OrderDialogTable tr:eq("+i.toString()+") td[type=Name]").text();
		aH[i]=$("#OrderDialogTable tr:eq("+i.toString()+") td[type=H]").text();
		aW[i]=$("#OrderDialogTable tr:eq("+i.toString()+") td[type=W]").text();
		aS[i]=$("#OrderDialogTable tr:eq("+i.toString()+") td[type=S]").text();
		aOpen[i]=$("#OrderDialogTable tr:eq("+i.toString()+") td[type=Open]").text();
		aMarkirovka[i]=$("#OrderDialogTable tr:eq("+i.toString()+") td[type=Markirovka]").text();
		aRAL[i]=$("#OrderDialogTable tr:eq("+i.toString()+") td[type=RAL]").text();
		aShtild[i]=$("#OrderDialogTable tr:eq("+i.toString()+") td[type=Shtild]").text();
		aCount[i]=$("#OrderDialogTable tr:eq("+i.toString()+") td[type=Count]").text();
	};
	$.post(
		'orders/order.php',
		{
			'method':'OrdersPrintMarkirovka',
			"OrderBlank":$("#orderDialogInputBlank").val(),
			"OrderShet":$("#orderDialogInputShet").val(),
			"OrderShetDate":$("#orderDialogInputShetDate").val(),
			"aDoorPos":aDoorPos,
			"aName[]":aName,
			"aH[]":aH,
			"aW[]":aW,
			"aS[]":aS,
			"aOpen[]":aOpen,
			"aMarkirovka[]":aMarkirovka,
			"aRAL[]":aRAL,
			"aShtild[]":aShtild,
			"aCount[]":aCount,
			"OrderZakaz":$("#OrderDialogInputZakaz").val()
		},
		function (data)
		{
			if(data=="ok") window.open('orders/Markirovka.pdf','_blank');
		}
	);
}

//------------------Работа с заказом в диалоге--------------------------------------------
var OderDialogTablePOS=1;
//Изменение номеров позиций
function OrderDialogPositionTR()
{
	var pos=1;
	for(var r=0;r<$('#OrderDialogTable tr').length;r++)
	{
		if($('#OrderDialogTable tr:eq('+r+')').hasClass("Work") || $('#OrderDialogTable tr:eq('+r+')').hasClass("Complite"))
			pos=r+2;
		if($('#OrderDialogTable tr:eq('+r+')').is(":visible") & !($('#OrderDialogTable tr:eq('+r+')').hasClass("Work") || $('#OrderDialogTable tr:eq('+r+')').hasClass("Complite")))
		{
			$('#OrderDialogTable tr:eq('+r+') td:eq(1)').text((pos).toString());
			pos++;
		};
	};
}
//Добавление строки
function OrderDialogTableAddRow()
{
	$("#OrderDialogTableTRAddBugs").html("");//Баги
	var flagErr=false;
	var sErr="";
	var ValueNull=0;
	for(var d=0; d<OrderGlobalTypesDoorNew.length; d++)
		if(OrderGlobalTypesDoorNew[d].Name==$('#OrderDialogTableInputName').val())
		{
			ValueNull=OrderGlobalTypesDoorNew[d].ValueNull;
			break;
		};
	if(ValueNull==0 & $('#OrderDialogTableInputH').val()=="")	{ flagErr=true; sErr=sErr+"Не заполненна высота<br>"; };
	if(ValueNull==0 & $('#OrderDialogTableInputW').val()=="")	{ flagErr=true; sErr=sErr+"Не заполненна ширина<br>"; };
	if($('#OrderDialogTableInputCount').val()=="")	{ flagErr=true; sErr=sErr+"Не заполненно количество<br>"; };
	$("#OrderDialogTableTRAddBugs").html("<hr>"+sErr);
	
	if(!flagErr)
	{
		var StvorkaPetlya=""; if($("#OrderDialogTableInputS").val()!="") StvorkaPetlya="2";
		var NoteStr=$('#OrderDialogTableInputNote').val(); if($('#OrderDialogTableInputNote').val().length>65) NoteStr=$('#OrderDialogTableInputNote').val().substring(0,65)+" <img src='images/DocumentNext.png'>";
		id="OTR"+guidSmall();
		$('#OrderDialogTable').append(
		'<tr status="Add" class=Start idDoor="" id="'+id+'">'+
			'<td onclick="OrderDoorEditStart(this)"><button title="Редактировать">e</button></td>'+
			'<td type=Num></td>'+
			'<td type=Name>'+$('#OrderDialogTableInputName').val()+'</td>'+
			'<td type=Count>'+$('#OrderDialogTableInputCount').val()+'</td>'+
			'<td type=H>'+$('#OrderDialogTableInputH').val()+'</td>'+
			'<td type=W>'+$('#OrderDialogTableInputW').val()+'</td>'+
			'<td type=Open>'+$('#OrderDialogTableInputOpen').val()+'</td>'+
			'<td type=S>'+($('#OrderDialogTableInputSEqual').is(":checked")? "Равн.":$('#OrderDialogTableInputS').val())+'</td>'+
			'<td type=RAL>'+$('#OrderDialogTableInputRAL').val()+'</td>'+
			'<td type=Nalichnik>'+$('#OrderDialogTableInputNalichnik').val()+'</td>'+
			'<td type=Dovod>'+$('#OrderDialogTableInputDovod').val()+'</td>'+
			'<td type=Note style="text-align:left;" onclick=" OrderDialogNoteView(this)">'+NoteStr+' <note style="display:none">'+$('#OrderDialogTableInputNote').val()+'</note></td>'+
			'<td type=Markirovka>'+$('#OrderDialogTableInputMarkirovka').val()+'</td>'+
			'<td type=Shtild >'+$('#OrderDialogTableInputShtild').val()+'</td>'+
			'<td type=PetlyaWrk >'+($('#OrderDialogTableInputPetlyaWrk').val()!=""?$('#OrderDialogTableInputPetlyaWrk').val():"2")+'</td>'+
			'<td type=PetlyaStv >'+($('#OrderDialogTableInputPetlyaStv').val()!=""?$('#OrderDialogTableInputPetlyaStv').val():($('#OrderDialogTableInputSEqual').is(":checked") || $('#OrderDialogTableInputS').val()!=""?"2":""))+'</td>'+
			'<td type=WindowWrk >'+($('#OrderDialogTableInputWindowWrk').is(":checked") ? "<input type=checkbox checked disabled>" : "<input type=checkbox disabled>")+'</td>'+
			'<td type=WindowStv >'+($('#OrderDialogTableInputWindowStv').is(":checked") ? "<input type=checkbox checked disabled>" : "<input type=checkbox disabled>")+'</td>'+
			'<td type=FramugaCh >'+($('#OrderDialogTableInputFramuga').is(":checked") ? "<input type=checkbox checked disabled>" : "<input type=checkbox disabled>")+'</td>'+
			"<td type=FramugaH>"+$('#OrderDialogTableInputFramugaH').val()+"</td>"+
			'<td></td>'+
			'<td type=Construct><button onclick=OrderDoorConstructOpen(this) title="Конструктор">...</button>'+
				'<span style="display:none;">'+
					//--Рабочая створка
					'<WorkPetlya>'+($('#OrderDialogTableInputPetlyaWrk').val()!=""?$('#OrderDialogTableInputPetlyaWrk').val():"2")+'</WorkPetlya>'+
					//Окно
					'<WorkWindowCh>'+($('#OrderDialogTableInputWindowWrk').is(":checked") ? "true" : "false")+'</WorkWindowCh>'+
						'<WorkWindowNoFrame></WorkWindowNoFrame>'+
						'<WorkWindowH></WorkWindowH>'+
						'<WorkWindowW></WorkWindowW>'+
						'<WorkWindowGain></WorkWindowGain>'+
						'<WorkWindowGlass></WorkWindowGlass>'+
						'<WorkWindowGlassType></WorkWindowGlassType>'+
						'<WorkWindowGrid></WorkWindowGrid>'+
					//Окно 1
					'<WorkWindowCh1></WorkWindowCh1>'+
						'<WorkWindowNoFrame1></WorkWindowNoFrame1>'+
						'<WorkWindowH1></WorkWindowH1>'+
						'<WorkWindowW1></WorkWindowW1>'+
						'<WorkWindowGain1></WorkWindowGain1>'+
						'<WorkWindowGlass1></WorkWindowGlass1>'+
						'<WorkWindowGlassType1></WorkWindowGlassType1>'+
						'<WorkWindowGrid1></WorkWindowGrid1>'+
					//Окно 2
					'<WorkWindowCh2></WorkWindowCh2>'+
						'<WorkWindowNoFrame2></WorkWindowNoFrame2>'+
						'<WorkWindowH2></WorkWindowH2>'+
						'<WorkWindowW2></WorkWindowW2>'+
						'<WorkWindowGain2></WorkWindowGain2>'+
						'<WorkWindowGlass2></WorkWindowGlass2>'+
						'<WorkWindowGlassType2></WorkWindowGlassType2>'+
						'<WorkWindowGrid2></WorkWindowGrid2>'+
					//Окно
					'<WorkUpGridCh></WorkUpGridCh>'+
					'<WorkDownGridCh></WorkDownGridCh>'+
					//--Вторая створка
					'<StvorkaCh></StvorkaCh>'+
						'<StvorkaPetlya>'+($('#OrderDialogTableInputPetlyaStv').val()!=""?$('#OrderDialogTableInputPetlyaStv').val():($('#OrderDialogTableInputSEqual').is(":checked") || $('#OrderDialogTableInputS').val()!=""?"2":""))+'</StvorkaPetlya>'+
						//Окно
						'<StvorkaWindowCh>'+($('#OrderDialogTableInputWindowStv').is(":checked") ? "true" : "false")+'</StvorkaWindowCh>'+
							'<StvorkaWindowNoFrame></StvorkaWindowNoFrame>'+
							'<StvorkaWindowH></StvorkaWindowH>'+
							'<StvorkaWindowW></StvorkaWindowW>'+
							'<StvorkaWindowGain></StvorkaWindowGain>'+
							'<StvorkaWindowGlass></StvorkaWindowGlass>'+
							'<StvorkaWindowGlassType></StvorkaWindowGlassType>'+
							'<StvorkaWindowGrid></StvorkaWindowGrid>'+
						//Окно 1
						'<StvorkaWindowCh1></StvorkaWindowCh1>'+
							'<StvorkaWindowNoFrame1></StvorkaWindowNoFrame1>'+
							'<StvorkaWindowH1></StvorkaWindowH1>'+
							'<StvorkaWindowW1></StvorkaWindowW1>'+
							'<StvorkaWindowGain1></StvorkaWindowGain1>'+
							'<StvorkaWindowGlass1></StvorkaWindowGlass1>'+
							'<StvorkaWindowGlassType1></StvorkaWindowGlassType1>'+
							'<StvorkaWindowGrid1></StvorkaWindowGrid1>'+
						//Окно 2
						'<StvorkaWindowCh2></StvorkaWindowCh2>'+
							'<StvorkaWindowNoFrame2></StvorkaWindowNoFrame2>'+
							'<StvorkaWindowH2></StvorkaWindowH2>'+
							'<StvorkaWindowW2></StvorkaWindowW2>'+
							'<StvorkaWindowGain2></StvorkaWindowGain2>'+
							'<StvorkaWindowGlass2></StvorkaWindowGlass2>'+
							'<StvorkaWindowGlassType2></StvorkaWindowGlassType2>'+
							'<StvorkaWindowGrid2></StvorkaWindowGrid2>'+
						//Вент решетка
						'<StvorkaUpGridCh></StvorkaUpGridCh>'+
						'<StvorkaDownGridCh></StvorkaDownGridCh>'+
					//--Фрамуга
					'<FramugaCh>'+($('#OrderDialogTableInputFramuga').is(":checked") ? "true" : "false")+'</FramugaCh>'+
						"<FramugaH>"+$('#OrderDialogTableInputFramugaH').val()+"</FramugaH>"+
						//Окно
						'<FramugaWindowCh></FramugaWindowCh>'+
							'<FramugaWindowNoFrame></FramugaWindowNoFrame>'+
							'<FramugaWindowH></FramugaWindowH>'+
							'<FramugaWindowW></FramugaWindowW>'+
							'<FramugaWindowGain></FramugaWindowGain>'+
							'<FramugaWindowGlass></FramugaWindowGlass>'+
							'<FramugaWindowGlassType></FramugaWindowGlassType>'+
							'<FramugaWindowGrid></FramugaWindowGrid>'+
						//Вент решетка
							'<FramugaUpGridCh></FramugaUpGridCh>'+
							'<FramugaDownGridCh></FramugaDownGridCh>'+
						//Дополнительно
							"<Antipanik>0</Antipanik>"+
							"<Otboynik>0</Otboynik>"+
							"<Wicket>0</Wicket>"+
							"<BoxLock>0</BoxLock>"+
							"<Otvetka>0</Otvetka>"+
							"<Isolation>0</Isolation>"+
				'</span>'+
			'</td>'+
			'<td type=Cost><button onclick="OrderCostDialogLoad(this)" style="border: 2px solid red" title="Зарплата">...</button>'+
				'<span style="display:none;">'+
					'<CostLaser>0</CostLaser>'+
					'<CostSgibka>0</CostSgibka>'+
					'<CostSvarka>0</CostSvarka>'+
					'<CostFrame>0</CostFrame>'+
					'<CostMdf>0</CostMdf>'+
					'<CostSborka>0</CostSborka>'+
					'<CostSborkaMdf>0</CostSborkaMdf>'+
					'<CostColor>0</CostColor>'+
					'<CostUpak>0</CostUpak>'+
					'<CostShpt>0</CostShpt>'+
				'</span>'+
			'</td>'+
			"<td></td>"+
			"<td></td>"+
			'<td onclick="OrderDialogTableDelRow(this)"><button title="Удалить">x</button></td>'+
		'</tr>'
		);
		OderDialogTablePOS++;
		
		$("#OrderDialogTableInputShtild").val("");
		OrderPrlCalcAddEditRow(id);
	};
	OrderDialogPositionTR();
}

//Функция открытия на просмотр примечания, если больше 70 символов
function OrderDialogNoteView(id)
{
	$("#OrderDialogNoteView p").text($(id).find("note").text());
	$("#OrderDialogNoteView").dialog("open");
}

//Очищаем поля
function OrderDialogTableAddClear()
{
	$('#OrderDialogTableInputH').val("");
	$('#OrderDialogTableInputW').val("");
	$('#OrderDialogTableInputS').val("");
	$('#OrderDialogTableInputS').removeAttr("disabled");
	$('#OrderDialogTableInputSEqual').removeAttr("checked");
	$('#OrderDialogTableInputRAL').val("");
	$('#OrderDialogTableInputNote').val("");
	$('#OrderDialogTableInputNoteSpan').html("&nbsp;");
	$('#OrderDialogTableInputMarkirovka').val("");
	$('#OrderDialogTableInputCount').val("");
	$('#OrderDialogTableInputShtild').val("");
	$("#OrderDialogTableInputPetlyaWrk").val("");
	$("#OrderDialogTableInputPetlyaStv").val("");
	$("#OrderDialogTableInputWindowWrk").removeAttr("checked");
	$("#OrderDialogTableInputWindowStv").removeAttr("checked");
	$("#OrderDialogTableInputFramuga").removeAttr("checked");
	$("#OrderDialogTableInputFramugaH").val("");
}

//Удаление строки
function OrderDialogTableDelRow(el)
{
	var elTR=$(el).parent();
	if(elTR.hasClass("Work") || elTR.hasClass("Complite"))
	{
		alert("Невозможно удалить позицию находящуюся в работе!");
	}
	else
		if(confirm("Подтвердите удаление"))
		{
			if(elTR.attr("status")=="Load" || elTR.attr("status")=="Edit")
			{
				elTR.attr("status","Del");
				elTR.hide();
			}
			else
				elTR.remove();
			OrderDialogPositionTR();
		};	
}


//Сохранение диалога
function OrderDialogSave()
{
	//Проверяем заполненность
	var flagErr=false;
	var sErr="";
	$("#OrderDialogBugs").html("");
	if($("#orderDialogInputBlankDate").val()=="") {flagErr=true; sErr="Не заполненно поле дата заказа<br>"};
	if($("#orderDialogInputBlank").val()=="") {flagErr=true; sErr="Не заполненно поле номер заказа<br>"};
	if($("#OrderDialogTable").find("tr").length==0){flagErr=true; sErr="Отсутствует список дверей<br>"};
	$("#OrderDialogBugs").html("<hr>"+sErr);
	
	if(!flagErr)
	{
		//Массивы строк таблицы, формирование на запись
		var OrderDialogTableTDIDArr=[];
		var OrderDialogTableTDStatusArr=[];
		var OrderDialogTableTDWorkStatusArr=[];
		var OrderDialogTableTDNumArr=[];
		var OrderDialogTableTDNameArr=[];
		var OrderDialogTableTDHArr=[];
		var OrderDialogTableTDWArr=[];
		var OrderDialogTableTDSArr=[];
		var OrderDialogTableTDOpenArr=[];
		var OrderDialogTableTDNalichnikArr=[];
		var OrderDialogTableTDDovodArr=[];
		var OrderDialogTableTDRALArr=[];
		var OrderDialogTableTDNoteArr=[];
		var OrderDialogTableTDMarkirovkaArr=[];
		var OrderDialogTableTDCountArr=[];
		var OrderDialogTableTDShtildArr=[];
		//--Рабочая створка
		var OrderDialogTableTDConstructWorkPetlya=[];
		//Окно
		var OrderDialogTableTDConstructWorkWindowCh=[];
		var OrderDialogTableTDConstructWorkWindowNoFrame=[];
		var OrderDialogTableTDConstructWorkWindowH=[];
		var OrderDialogTableTDConstructWorkWindowW=[];
		var OrderDialogTableTDConstructWorkWindowGain=[];
		var OrderDialogTableTDConstructWorkWindowGlass=[];
		var OrderDialogTableTDConstructWorkWindowGlassType=[];
		var OrderDialogTableTDConstructWorkWindowGrid=[];
		//Окно 1
		var OrderDialogTableTDConstructWorkWindowCh1=[];
		var OrderDialogTableTDConstructWorkWindowNoFrame1=[];
		var OrderDialogTableTDConstructWorkWindowH1=[];
		var OrderDialogTableTDConstructWorkWindowW1=[];
		var OrderDialogTableTDConstructWorkWindowGain1=[];
		var OrderDialogTableTDConstructWorkWindowGlass1=[];
		var OrderDialogTableTDConstructWorkWindowGlassType1=[];
		var OrderDialogTableTDConstructWorkWindowGrid1=[];
		//Окно 2
		var OrderDialogTableTDConstructWorkWindowCh2=[];
		var OrderDialogTableTDConstructWorkWindowNoFrame2=[];
		var OrderDialogTableTDConstructWorkWindowH2=[];
		var OrderDialogTableTDConstructWorkWindowW2=[];
		var OrderDialogTableTDConstructWorkWindowGain2=[];
		var OrderDialogTableTDConstructWorkWindowGlass2=[];
		var OrderDialogTableTDConstructWorkWindowGlassType2=[];
		var OrderDialogTableTDConstructWorkWindowGrid2=[];
		//Вент решетки
		var OrderDialogTableTDConstructWorkUpGridCh=[];
		var OrderDialogTableTDConstructWorkDownGridCh=[];
		//--Вторая створка
		var OrderDialogTableTDConstructStvorkaCh=[];
		var OrderDialogTableTDConstructStvorkaPetlya=[];
		//Окно
		var OrderDialogTableTDConstructStvorkaWindowCh=[];
		var OrderDialogTableTDConstructStvorkaWindowNoFrame=[];
		var OrderDialogTableTDConstructStvorkaWindowH=[];
		var OrderDialogTableTDConstructStvorkaWindowW=[];
		var OrderDialogTableTDConstructStvorkaWindowGain=[];
		var OrderDialogTableTDConstructStvorkaWindowGlass=[];
		var OrderDialogTableTDConstructStvorkaWindowGlassType=[];
		var OrderDialogTableTDConstructStvorkaWindowGrid=[];
		//Окно 1
		var OrderDialogTableTDConstructStvorkaWindowCh1=[];
		var OrderDialogTableTDConstructStvorkaWindowNoFrame1=[];
		var OrderDialogTableTDConstructStvorkaWindowH1=[];
		var OrderDialogTableTDConstructStvorkaWindowW1=[];
		var OrderDialogTableTDConstructStvorkaWindowGain1=[];
		var OrderDialogTableTDConstructStvorkaWindowGlass1=[];
		var OrderDialogTableTDConstructStvorkaWindowGlassType1=[];
		var OrderDialogTableTDConstructStvorkaWindowGrid1=[];
		//Окно 2
		var OrderDialogTableTDConstructStvorkaWindowCh2=[];
		var OrderDialogTableTDConstructStvorkaWindowNoFrame2=[];
		var OrderDialogTableTDConstructStvorkaWindowH2=[];
		var OrderDialogTableTDConstructStvorkaWindowW2=[];
		var OrderDialogTableTDConstructStvorkaWindowGain2=[];
		var OrderDialogTableTDConstructStvorkaWindowGlass2=[];
		var OrderDialogTableTDConstructStvorkaWindowGlassType2=[];
		var OrderDialogTableTDConstructStvorkaWindowGrid2=[];
		//Вент решетка
		var OrderDialogTableTDConstructStvorkaUpGridCh=[];
		var OrderDialogTableTDConstructStvorkaDownGridCh=[];
		//--Фрамуга
		var OrderDialogTableTDConstructFramugaCh=[];
		var OrderDialogTableTDConstructFramugaH=[];
		//Окно
		var OrderDialogTableTDConstructFramugaWindowCh=[];
		var OrderDialogTableTDConstructFramugaWindowNoFrame=[];
		var OrderDialogTableTDConstructFramugaWindowH=[];
		var OrderDialogTableTDConstructFramugaWindowW=[];
		var OrderDialogTableTDConstructFramugaWindowGain=[];
		var OrderDialogTableTDConstructFramugaWindowGlass=[];
		var OrderDialogTableTDConstructFramugaWindowGlassType=[];
		var OrderDialogTableTDConstructFramugaWindowGrid=[];
		//Вент решетка
		var OrderDialogTableTDConstructFramugaUpGridCh=[];
		var OrderDialogTableTDConstructFramugaDownGridCh=[];
		//Дополнительно
		var Antipanik=[];
		var Otboynik=[];
		var Wicket=[];
		var BoxLock=[];
		var Otvetka=[];
		var Isolation=[];
		//--Зарплата
		var CostLaser=[];
		var CostSgibka=[];
		var CostSvarka=[];
		var CostFrame=[];
		var CostMdf=[];
		var CostSborka=[];
		var CostSborkaMdf=[];
		var CostColor=[];
		var CostUpak=[];
		var CostShpt=[];
				
		for(var i=0; i<$("#OrderDialogTable tr").length;i++)
			{
				var el=$("#OrderDialogTable tr:eq("+i+")");
				OrderDialogTableTDIDArr[i]=el.attr("idDoor");
				OrderDialogTableTDStatusArr[i]=el.attr("status");
				OrderDialogTableTDWorkStatusArr[i]=el.attr("class");
				OrderDialogTableTDNumArr[i]=el.find("td[type=Num]").text();
				OrderDialogTableTDNameArr[i]=el.find("td[type=Name]").text();
				OrderDialogTableTDHArr[i]=el.find("td[type=H]").text();
				OrderDialogTableTDWArr[i]=el.find("td[type=W]").text();
				OrderDialogTableTDSArr[i]=el.find("td[type=S]").text();
				OrderDialogTableTDOpenArr[i]=el.find("td[type=Open]").text();
				OrderDialogTableTDNalichnikArr[i]=el.find("td[type=Nalichnik]").text();
				OrderDialogTableTDDovodArr[i]=el.find("td[type=Dovod]").text();
				OrderDialogTableTDRALArr[i]=el.find("td[type=RAL]").text();
				OrderDialogTableTDNoteArr[i]=el.find("td[type=Note] note").text();
				OrderDialogTableTDMarkirovkaArr[i]=el.find("td[type=Markirovka]").text();
				OrderDialogTableTDCountArr[i]=el.find("td[type=Count]").text();
				OrderDialogTableTDShtildArr[i]=el.find("td[type=Shtild]").text();
				//--Рабочая створка
				OrderDialogTableTDConstructWorkPetlya[i]="null"; if(el.find("td[type=Construct] WorkPetlya").text()!="") OrderDialogTableTDConstructWorkPetlya[i]=el.find("td[type=Construct] WorkPetlya").text();
				//Окно
				OrderDialogTableTDConstructWorkWindowCh[i]="false"; if(el.find("td[type=Construct] WorkWindowCh").text()=="true") OrderDialogTableTDConstructWorkWindowCh[i]=el.find("td[type=Construct] WorkWindowCh").text();
				OrderDialogTableTDConstructWorkWindowNoFrame[i]="false"; if(el.find("td[type=Construct] WorkWindowNoFrame").text()=="true") OrderDialogTableTDConstructWorkWindowNoFrame[i]=el.find("td[type=Construct] WorkWindowNoFrame").text();
				OrderDialogTableTDConstructWorkWindowH[i]="null"; if(el.find("td[type=Construct] WorkWindowH").text()!="") OrderDialogTableTDConstructWorkWindowH[i]=el.find("td[type=Construct] WorkWindowH").text();
				OrderDialogTableTDConstructWorkWindowW[i]="null"; if(el.find("td[type=Construct] WorkWindowW").text()!="") OrderDialogTableTDConstructWorkWindowW[i]=el.find("td[type=Construct] WorkWindowW").text();
				OrderDialogTableTDConstructWorkWindowGain[i]="false"; if(el.find("td[type=Construct] WorkWindowGain").text()=="true") OrderDialogTableTDConstructWorkWindowGain[i]="true";
				OrderDialogTableTDConstructWorkWindowGlass[i]="false"; if(el.find("td[type=Construct] WorkWindowGlass").text()=="true") OrderDialogTableTDConstructWorkWindowGlass[i]=el.find("td[type=Construct] WorkWindowGlass").text();
				OrderDialogTableTDConstructWorkWindowGlassType[i]=el.find("td[type=Construct] WorkWindowGlassType").text();
				OrderDialogTableTDConstructWorkWindowGrid[i]="false"; if(el.find("td[type=Construct] WorkWindowGrid").text()=="true") OrderDialogTableTDConstructWorkWindowGrid[i]="true";
				//Окно 1
				OrderDialogTableTDConstructWorkWindowCh1[i]="false"; if(el.find("td[type=Construct] WorkWindowCh1").text()=="true") OrderDialogTableTDConstructWorkWindowCh1[i]=el.find("td[type=Construct] WorkWindowCh1").text();
				OrderDialogTableTDConstructWorkWindowNoFrame1[i]="false"; if(el.find("td[type=Construct] WorkWindowNoFrame1").text()=="true") OrderDialogTableTDConstructWorkWindowNoFrame1[i]=el.find("td[type=Construct] WorkWindowNoFrame1").text();
				OrderDialogTableTDConstructWorkWindowH1[i]="null"; if(el.find("td[type=Construct] WorkWindowH1").text()!="") OrderDialogTableTDConstructWorkWindowH1[i]=el.find("td[type=Construct] WorkWindowH1").text();
				OrderDialogTableTDConstructWorkWindowW1[i]="null"; if(el.find("td[type=Construct] WorkWindowW1").text()!="") OrderDialogTableTDConstructWorkWindowW1[i]=el.find("td[type=Construct] WorkWindowW1").text();
				OrderDialogTableTDConstructWorkWindowGain1[i]="false"; if(el.find("td[type=Construct] WorkWindowGain1").text()=="true") OrderDialogTableTDConstructWorkWindowGain1[i]="true";
				OrderDialogTableTDConstructWorkWindowGlass1[i]="false"; if(el.find("td[type=Construct] WorkWindowGlass1").text()=="true") OrderDialogTableTDConstructWorkWindowGlass1[i]=el.find("td[type=Construct] WorkWindowGlass1").text();
				OrderDialogTableTDConstructWorkWindowGlassType1[i]=el.find("td[type=Construct] WorkWindowGlassType1").text();
				OrderDialogTableTDConstructWorkWindowGrid1[i]="false"; if(el.find("td[type=Construct] WorkWindowGrid1").text()=="true") OrderDialogTableTDConstructWorkWindowGrid1[i]="true";
				//Окно 2
				OrderDialogTableTDConstructWorkWindowCh2[i]="false"; if(el.find("td[type=Construct] WorkWindowCh2").text()=="true") OrderDialogTableTDConstructWorkWindowCh2[i]=el.find("td[type=Construct] WorkWindowCh2").text();
				OrderDialogTableTDConstructWorkWindowNoFrame2[i]="false"; if(el.find("td[type=Construct] WorkWindowNoFrame2").text()=="true") OrderDialogTableTDConstructWorkWindowNoFrame2[i]=el.find("td[type=Construct] WorkWindowNoFrame2").text();
				OrderDialogTableTDConstructWorkWindowH2[i]="null"; if(el.find("td[type=Construct] WorkWindowH2").text()!="") OrderDialogTableTDConstructWorkWindowH2[i]=el.find("td[type=Construct] WorkWindowH2").text();
				OrderDialogTableTDConstructWorkWindowW2[i]="null"; if(el.find("td[type=Construct] WorkWindowW2").text()!="") OrderDialogTableTDConstructWorkWindowW2[i]=el.find("td[type=Construct] WorkWindowW2").text();
				OrderDialogTableTDConstructWorkWindowGain2[i]="false"; if(el.find("td[type=Construct] WorkWindowGain2").text()=="true") OrderDialogTableTDConstructWorkWindowGain2[i]="true";
				OrderDialogTableTDConstructWorkWindowGlass2[i]="false"; if(el.find("td[type=Construct] WorkWindowGlass2").text()=="true") OrderDialogTableTDConstructWorkWindowGlass2[i]=el.find("td[type=Construct] WorkWindowGlass2").text();
				OrderDialogTableTDConstructWorkWindowGlassType2[i]=el.find("td[type=Construct] WorkWindowGlassType2").text();
				OrderDialogTableTDConstructWorkWindowGrid2[i]="false"; if(el.find("td[type=Construct] WorkWindowGrid2").text()=="true") OrderDialogTableTDConstructWorkWindowGrid2[i]="true";
				//Вент решетка
				OrderDialogTableTDConstructWorkUpGridCh[i]="false"; if(el.find("td[type=Construct] WorkUpGridCh").text()=="true") OrderDialogTableTDConstructWorkUpGridCh[i]="true";
				OrderDialogTableTDConstructWorkDownGridCh[i]="false"; if(el.find("td[type=Construct] WorkDownGridCh").text()=="true") OrderDialogTableTDConstructWorkDownGridCh[i]="true";
				//alert(el.find("td[type=Construct] WorkWindowGrid").text()+"   "+OrderDialogTableTDConstructWorkWindowGrid[i]);
				//--Вторая створка
				OrderDialogTableTDConstructStvorkaCh[i]="false"; if(el.find("td[type=Construct] StvorkaCh").text()=="true") OrderDialogTableTDConstructStvorkaCh[i]=el.find("td[type=Construct] StvorkaCh").text();
				OrderDialogTableTDConstructStvorkaPetlya[i]="null"; if(el.find("td[type=Construct] StvorkaPetlya").text()!="") OrderDialogTableTDConstructStvorkaPetlya[i]=el.find("td[type=Construct] StvorkaPetlya").text();
				//Окно
				OrderDialogTableTDConstructStvorkaWindowCh[i]="false"; if(el.find("td[type=Construct] StvorkaWindowCh").text()=="true") OrderDialogTableTDConstructStvorkaWindowCh[i]=el.find("td[type=Construct] StvorkaWindowCh").text();
				OrderDialogTableTDConstructStvorkaWindowNoFrame[i]="false"; if(el.find("td[type=Construct] StvorkaWindowNoFrame").text()=="true") OrderDialogTableTDConstructStvorkaWindowNoFrame[i]=el.find("td[type=Construct] StvorkaWindowNoFrame").text();
				OrderDialogTableTDConstructStvorkaWindowH[i]="null"; if(el.find("td[type=Construct] StvorkaWindowH").text()!="") OrderDialogTableTDConstructStvorkaWindowH[i]=el.find("td[type=Construct] StvorkaWindowH").text();
				OrderDialogTableTDConstructStvorkaWindowW[i]="null"; if(el.find("td[type=Construct] StvorkaWindowW").text()!="") OrderDialogTableTDConstructStvorkaWindowW[i]=el.find("td[type=Construct] StvorkaWindowW").text();
				OrderDialogTableTDConstructStvorkaWindowGain[i]="false"; if(el.find("td[type=Construct] StvorkaWindowGain").text()=="true") OrderDialogTableTDConstructStvorkaWindowGain[i]=el.find("td[type=Construct] StvorkaWindowGain").text();
				OrderDialogTableTDConstructStvorkaWindowGlass[i]="false"; if(el.find("td[type=Construct] StvorkaWindowGlass").text()=="true") OrderDialogTableTDConstructStvorkaWindowGlass[i]=el.find("td[type=Construct] StvorkaWindowGlass").text();
				OrderDialogTableTDConstructStvorkaWindowGlassType[i]=el.find("td[type=Construct] StvorkaWindowGlassType").text();
				OrderDialogTableTDConstructStvorkaWindowGrid[i]="false"; if(el.find("td[type=Construct] StvorkaWindowGrid").text()=="true") OrderDialogTableTDConstructStvorkaWindowGrid[i]=el.find("td[type=Construct] StvorkaWindowGrid").text();
				//Окно 1
				OrderDialogTableTDConstructStvorkaWindowCh1[i]="false"; if(el.find("td[type=Construct] StvorkaWindowCh1").text()=="true") OrderDialogTableTDConstructStvorkaWindowCh1[i]=el.find("td[type=Construct] StvorkaWindowCh1").text();
				OrderDialogTableTDConstructStvorkaWindowNoFrame1[i]="false"; if(el.find("td[type=Construct] StvorkaWindowNoFrame1").text()=="true") OrderDialogTableTDConstructStvorkaWindowNoFrame1[i]=el.find("td[type=Construct] StvorkaWindowNoFrame1").text();
				OrderDialogTableTDConstructStvorkaWindowH1[i]="null"; if(el.find("td[type=Construct] StvorkaWindowH1").text()!="") OrderDialogTableTDConstructStvorkaWindowH1[i]=el.find("td[type=Construct] StvorkaWindowH1").text();
				OrderDialogTableTDConstructStvorkaWindowW1[i]="null"; if(el.find("td[type=Construct] StvorkaWindowW1").text()!="") OrderDialogTableTDConstructStvorkaWindowW1[i]=el.find("td[type=Construct] StvorkaWindowW1").text();
				OrderDialogTableTDConstructStvorkaWindowGain1[i]="false"; if(el.find("td[type=Construct] StvorkaWindowGain1").text()=="true") OrderDialogTableTDConstructStvorkaWindowGain1[i]=el.find("td[type=Construct] StvorkaWindowGain1").text();
				OrderDialogTableTDConstructStvorkaWindowGlass1[i]="false"; if(el.find("td[type=Construct] StvorkaWindowGlass1").text()=="true") OrderDialogTableTDConstructStvorkaWindowGlass1[i]=el.find("td[type=Construct] StvorkaWindowGlass1").text();
				OrderDialogTableTDConstructStvorkaWindowGlassType1[i]=el.find("td[type=Construct] StvorkaWindowGlassType1").text();
				OrderDialogTableTDConstructStvorkaWindowGrid1[i]="false"; if(el.find("td[type=Construct] StvorkaWindowGrid1").text()=="true") OrderDialogTableTDConstructStvorkaWindowGrid1[i]=el.find("td[type=Construct] StvorkaWindowGrid1").text();
				//Окно 2
				OrderDialogTableTDConstructStvorkaWindowCh2[i]="false"; if(el.find("td[type=Construct] StvorkaWindowCh2").text()=="true") OrderDialogTableTDConstructStvorkaWindowCh2[i]=el.find("td[type=Construct] StvorkaWindowCh").text();
				OrderDialogTableTDConstructStvorkaWindowNoFrame2[i]="false"; if(el.find("td[type=Construct] StvorkaWindowNoFrame2").text()=="true") OrderDialogTableTDConstructStvorkaWindowNoFrame2[i]=el.find("td[type=Construct] StvorkaWindowNoFrame2").text();
				OrderDialogTableTDConstructStvorkaWindowH2[i]="null"; if(el.find("td[type=Construct] StvorkaWindowH2").text()!="") OrderDialogTableTDConstructStvorkaWindowH2[i]=el.find("td[type=Construct] StvorkaWindowH2").text();
				OrderDialogTableTDConstructStvorkaWindowW2[i]="null"; if(el.find("td[type=Construct] StvorkaWindowW2").text()!="") OrderDialogTableTDConstructStvorkaWindowW2[i]=el.find("td[type=Construct] StvorkaWindowW2").text();
				OrderDialogTableTDConstructStvorkaWindowGain2[i]="false"; if(el.find("td[type=Construct] StvorkaWindowGain2").text()=="true") OrderDialogTableTDConstructStvorkaWindowGain2[i]=el.find("td[type=Construct] StvorkaWindowGain2").text();
				OrderDialogTableTDConstructStvorkaWindowGlass2[i]="false"; if(el.find("td[type=Construct] StvorkaWindowGlass2").text()=="true") OrderDialogTableTDConstructStvorkaWindowGlass2[i]=el.find("td[type=Construct] StvorkaWindowGlass2").text();
				OrderDialogTableTDConstructStvorkaWindowGlassType2[i]=el.find("td[type=Construct] StvorkaWindowGlassType2").text();
				OrderDialogTableTDConstructStvorkaWindowGrid2[i]="false"; if(el.find("td[type=Construct] StvorkaWindowGrid2").text()=="true") OrderDialogTableTDConstructStvorkaWindowGrid2[i]=el.find("td[type=Construct] StvorkaWindowGrid2").text();
				//Вент решетка
				OrderDialogTableTDConstructStvorkaUpGridCh[i]="false"; if(el.find("td[type=Construct] StvorkaUpGridCh").text()=="true") OrderDialogTableTDConstructStvorkaUpGridCh[i]="true";
				OrderDialogTableTDConstructStvorkaDownGridCh[i]="false"; if(el.find("td[type=Construct] StvorkaDownGridCh").text()=="true") OrderDialogTableTDConstructStvorkaDownGridCh[i]="true";
				//--Фрамуга
				OrderDialogTableTDConstructFramugaCh[i]="false"; if(el.find("td[type=Construct] FramugaCh").text()=="true") OrderDialogTableTDConstructFramugaCh[i]=el.find("td[type=Construct] FramugaCh").text();
				OrderDialogTableTDConstructFramugaH[i]=el.find("td[type=Construct] FramugaH").text()==""? "NULL" : el.find("td[type=Construct] FramugaH").text();
				//Окно
				OrderDialogTableTDConstructFramugaWindowCh[i]="false"; if(el.find("td[type=Construct] FramugaWindowCh").text()=="true") OrderDialogTableTDConstructFramugaWindowCh[i]=el.find("td[type=Construct] FramugaWindowCh").text();
				OrderDialogTableTDConstructFramugaWindowNoFrame[i]="false"; if(el.find("td[type=Construct] FramugaWindowNoFrame").text()=="true") OrderDialogTableTDConstructFramugaWindowNoFrame[i]=el.find("td[type=Construct] FramugaWindowNoFrame").text();
				OrderDialogTableTDConstructFramugaWindowH[i]="null"; if(el.find("td[type=Construct] FramugaWindowH").text()!="") OrderDialogTableTDConstructFramugaWindowH[i]=el.find("td[type=Construct] FramugaWindowH").text();
				OrderDialogTableTDConstructFramugaWindowW[i]="null"; if(el.find("td[type=Construct] FramugaWindowW").text()!="") OrderDialogTableTDConstructFramugaWindowW[i]=el.find("td[type=Construct] FramugaWindowW").text();
				OrderDialogTableTDConstructFramugaWindowGain[i]="false"; if(el.find("td[type=Construct] FramugaWindowGain").text()=="true") OrderDialogTableTDConstructFramugaWindowGain[i]=el.find("td[type=Construct] FramugaWindowGain").text();
				OrderDialogTableTDConstructFramugaWindowGlass[i]="false"; if(el.find("td[type=Construct] FramugaWindowGlass").text()=="true") OrderDialogTableTDConstructFramugaWindowGlass[i]=el.find("td[type=Construct] FramugaWindowGlass").text();
				OrderDialogTableTDConstructFramugaWindowGlassType[i]=el.find("td[type=Construct] FramugaWindowGlassType").text();
				OrderDialogTableTDConstructFramugaWindowGrid[i]="false"; if(el.find("td[type=Construct] FramugaWindowGrid").text()=="true") OrderDialogTableTDConstructFramugaWindowGrid[i]=el.find("td[type=Construct] FramugaWindowGrid").text();
				//Решетка
				OrderDialogTableTDConstructFramugaUpGridCh[i]="false"; if(el.find("td[type=Construct] FramugaUpGridCh").text()=="true") OrderDialogTableTDConstructFramugaUpGridCh[i]="true";
				OrderDialogTableTDConstructFramugaDownGridCh[i]="false"; if(el.find("td[type=Construct] FramugaDownGridCh").text()=="true") OrderDialogTableTDConstructFramugaDownGridCh[i]="true";
				//Дополнительно
				Antipanik[i]=el.find("td[type=Construct] Antipanik").text();
				Otboynik[i]=el.find("td[type=Construct] Otboynik").text();
				Wicket[i]=el.find("td[type=Construct] Wicket").text();
				BoxLock[i]=el.find("td[type=Construct] BoxLock").text();
				Otvetka[i]=el.find("td[type=Construct] Otvetka").text();
				Isolation[i]=el.find("td[type=Construct] Isolation").text();
				//--Зарплата
				CostLaser[i]=el.find("td[type=Cost] span CostLaser").text();
				CostSgibka[i]=el.find("td[type=Cost] span CostSgibka").text();
				CostSvarka[i]=el.find("td[type=Cost] span CostSvarka").text();
				CostFrame[i]=el.find("td[type=Cost] span CostFrame").text();
				CostMdf[i]=el.find("td[type=Cost] span CostMdf").text();
				CostSborka[i]=el.find("td[type=Cost] span CostSborka").text();
				CostSborkaMdf[i]=el.find("td[type=Cost] span CostSborkaMdf").text();
				CostColor[i]=el.find("td[type=Cost] span CostColor").text();
				CostUpak[i]=el.find("td[type=Cost] span CostUpak").text();
				CostShpt[i]=el.find("td[type=Cost] span CostShpt").text();
			};
		$.post(
			'orders/order.php',
			{
				'method':"OrderSave",
				"DialogGUID":$("#orderDialogInputGUID").val(),
				'idOrder':$("#orderDialogInputID").val(),
				'BlankDate':$('#orderDialogInputBlankDate').val(),
				'Blank':$('#orderDialogInputBlank').val(),
				'Shet':$('#orderDialogInputShet').val(),
				'ShetDate':$('#orderDialogInputShetDate').val(),
				'Srok':$('#OrderDialogInputSrok').val(),
				'Zakaz':$('#OrderDialogInputZakaz').val(),
				'Contact':$('#OrderDialogInputContact').val(),
				'Note':$('#OrderDialogInputNote').val(),
				"status":$("#orderDialogInputStatus").val(),
				'OrderDialogTableTDIDArr[]':OrderDialogTableTDIDArr,
				'OrderDialogTableTDStatusArr[]':OrderDialogTableTDStatusArr,
				'OrderDialogTableTDWorkStatusArr[]':OrderDialogTableTDWorkStatusArr,
				'OrderDialogTableTDNumArr[]':OrderDialogTableTDNumArr,
				'OrderDialogTableTDNameArr[]':OrderDialogTableTDNameArr,
				'OrderDialogTableTDHArr[]':OrderDialogTableTDHArr,
				'OrderDialogTableTDWArr[]':OrderDialogTableTDWArr,
				'OrderDialogTableTDSArr[]':OrderDialogTableTDSArr,
				'OrderDialogTableTDOpenArr[]':OrderDialogTableTDOpenArr,
				'OrderDialogTableTDNalichnikArr[]':OrderDialogTableTDNalichnikArr,
				'OrderDialogTableTDDovodArr[]':OrderDialogTableTDDovodArr,
				'OrderDialogTableTDRALArr[]':OrderDialogTableTDRALArr,
				'OrderDialogTableTDNoteArr[]':OrderDialogTableTDNoteArr,
				'OrderDialogTableTDMarkirovkaArr[]':OrderDialogTableTDMarkirovkaArr,
				'OrderDialogTableTDCountArr[]':OrderDialogTableTDCountArr,
				'OrderDialogTableTDShtildArr[]':OrderDialogTableTDShtildArr,
				//--Рабочая створка
				'OrderDialogTableTDConstructWorkPetlya[]':OrderDialogTableTDConstructWorkPetlya,
				//Окно
				'OrderDialogTableTDConstructWorkWindowCh[]':OrderDialogTableTDConstructWorkWindowCh,
				'OrderDialogTableTDConstructWorkWindowNoFrame[]':OrderDialogTableTDConstructWorkWindowNoFrame,
				'OrderDialogTableTDConstructWorkWindowH[]':OrderDialogTableTDConstructWorkWindowH,
				'OrderDialogTableTDConstructWorkWindowW[]':OrderDialogTableTDConstructWorkWindowW,
				'OrderDialogTableTDConstructWorkWindowGain[]':OrderDialogTableTDConstructWorkWindowGain,
				'OrderDialogTableTDConstructWorkWindowGlass[]':OrderDialogTableTDConstructWorkWindowGlass,
				'OrderDialogTableTDConstructWorkWindowGlassType[]':OrderDialogTableTDConstructWorkWindowGlassType,
				'OrderDialogTableTDConstructWorkWindowGrid[]':OrderDialogTableTDConstructWorkWindowGrid,
				//Окно 1
				'OrderDialogTableTDConstructWorkWindowCh1[]':OrderDialogTableTDConstructWorkWindowCh1,
				'OrderDialogTableTDConstructWorkWindowNoFrame1[]':OrderDialogTableTDConstructWorkWindowNoFrame1,
				'OrderDialogTableTDConstructWorkWindowH1[]':OrderDialogTableTDConstructWorkWindowH1,
				'OrderDialogTableTDConstructWorkWindowW1[]':OrderDialogTableTDConstructWorkWindowW1,
				'OrderDialogTableTDConstructWorkWindowGain1[]':OrderDialogTableTDConstructWorkWindowGain1,
				'OrderDialogTableTDConstructWorkWindowGlass1[]':OrderDialogTableTDConstructWorkWindowGlass1,
				'OrderDialogTableTDConstructWorkWindowGlassType1[]':OrderDialogTableTDConstructWorkWindowGlassType1,
				'OrderDialogTableTDConstructWorkWindowGrid1[]':OrderDialogTableTDConstructWorkWindowGrid1,
				//Окно 2
				'OrderDialogTableTDConstructWorkWindowCh2[]':OrderDialogTableTDConstructWorkWindowCh2,
				'OrderDialogTableTDConstructWorkWindowNoFrame2[]':OrderDialogTableTDConstructWorkWindowNoFrame2,
				'OrderDialogTableTDConstructWorkWindowH2[]':OrderDialogTableTDConstructWorkWindowH2,
				'OrderDialogTableTDConstructWorkWindowW2[]':OrderDialogTableTDConstructWorkWindowW2,
				'OrderDialogTableTDConstructWorkWindowGain2[]':OrderDialogTableTDConstructWorkWindowGain2,
				'OrderDialogTableTDConstructWorkWindowGlass2[]':OrderDialogTableTDConstructWorkWindowGlass2,
				'OrderDialogTableTDConstructWorkWindowGlassType2[]':OrderDialogTableTDConstructWorkWindowGlassType2,
				'OrderDialogTableTDConstructWorkWindowGrid2[]':OrderDialogTableTDConstructWorkWindowGrid2,
				//Вент решетки
				'OrderDialogTableTDConstructWorkUpGridCh[]':OrderDialogTableTDConstructWorkUpGridCh,
				'OrderDialogTableTDConstructWorkDownGridCh[]':OrderDialogTableTDConstructWorkDownGridCh,
				//--Вторая створка
				'OrderDialogTableTDConstructStvorkaCh[]':OrderDialogTableTDConstructStvorkaCh,
				'OrderDialogTableTDConstructStvorkaPetlya[]':OrderDialogTableTDConstructStvorkaPetlya,
				//Окно
				'OrderDialogTableTDConstructStvorkaWindowCh[]':OrderDialogTableTDConstructStvorkaWindowCh,
				'OrderDialogTableTDConstructStvorkaWindowNoFrame[]':OrderDialogTableTDConstructStvorkaWindowNoFrame,
				'OrderDialogTableTDConstructStvorkaWindowH[]':OrderDialogTableTDConstructStvorkaWindowH,
				'OrderDialogTableTDConstructStvorkaWindowW[]':OrderDialogTableTDConstructStvorkaWindowW,
				'OrderDialogTableTDConstructStvorkaWindowGain[]':OrderDialogTableTDConstructStvorkaWindowGain,
				'OrderDialogTableTDConstructStvorkaWindowGlass[]':OrderDialogTableTDConstructStvorkaWindowGlass,
				'OrderDialogTableTDConstructStvorkaWindowGlassType[]':OrderDialogTableTDConstructStvorkaWindowGlassType,
				'OrderDialogTableTDConstructStvorkaWindowGrid[]':OrderDialogTableTDConstructStvorkaWindowGrid,
				//Окно 1
				'OrderDialogTableTDConstructStvorkaWindowCh1[]':OrderDialogTableTDConstructStvorkaWindowCh1,
				'OrderDialogTableTDConstructStvorkaWindowNoFrame1[]':OrderDialogTableTDConstructStvorkaWindowNoFrame1,
				'OrderDialogTableTDConstructStvorkaWindowH1[]':OrderDialogTableTDConstructStvorkaWindowH1,
				'OrderDialogTableTDConstructStvorkaWindowW1[]':OrderDialogTableTDConstructStvorkaWindowW1,
				'OrderDialogTableTDConstructStvorkaWindowGain1[]':OrderDialogTableTDConstructStvorkaWindowGain1,
				'OrderDialogTableTDConstructStvorkaWindowGlass1[]':OrderDialogTableTDConstructStvorkaWindowGlass1,
				'OrderDialogTableTDConstructStvorkaWindowGlassType1[]':OrderDialogTableTDConstructStvorkaWindowGlassType1,
				'OrderDialogTableTDConstructStvorkaWindowGrid1[]':OrderDialogTableTDConstructStvorkaWindowGrid1,
				//Окно 2
				'OrderDialogTableTDConstructStvorkaWindowCh2[]':OrderDialogTableTDConstructStvorkaWindowCh2,
				'OrderDialogTableTDConstructStvorkaWindowNoFrame2[]':OrderDialogTableTDConstructStvorkaWindowNoFrame2,
				'OrderDialogTableTDConstructStvorkaWindowH2[]':OrderDialogTableTDConstructStvorkaWindowH2,
				'OrderDialogTableTDConstructStvorkaWindowW2[]':OrderDialogTableTDConstructStvorkaWindowW2,
				'OrderDialogTableTDConstructStvorkaWindowGain2[]':OrderDialogTableTDConstructStvorkaWindowGain2,
				'OrderDialogTableTDConstructStvorkaWindowGlass2[]':OrderDialogTableTDConstructStvorkaWindowGlass2,
				'OrderDialogTableTDConstructStvorkaWindowGlassType2[]':OrderDialogTableTDConstructStvorkaWindowGlassType2,
				'OrderDialogTableTDConstructStvorkaWindowGrid2[]':OrderDialogTableTDConstructStvorkaWindowGrid2,
				//Вент решетки
				'OrderDialogTableTDConstructStvorkaUpGridCh[]':OrderDialogTableTDConstructStvorkaUpGridCh,
				'OrderDialogTableTDConstructStvorkaDownGridCh[]':OrderDialogTableTDConstructStvorkaDownGridCh,
				//--Фрамуга
				'OrderDialogTableTDConstructFramugaCh[]':OrderDialogTableTDConstructFramugaCh,
				'OrderDialogTableTDConstructFramugaH[]':OrderDialogTableTDConstructFramugaH,
				//Окно
				'OrderDialogTableTDConstructFramugaWindowCh[]':OrderDialogTableTDConstructFramugaWindowCh,
				'OrderDialogTableTDConstructFramugaWindowNoFrame[]':OrderDialogTableTDConstructFramugaWindowNoFrame,
				'OrderDialogTableTDConstructFramugaWindowH[]':OrderDialogTableTDConstructFramugaWindowH,
				'OrderDialogTableTDConstructFramugaWindowW[]':OrderDialogTableTDConstructFramugaWindowW,
				'OrderDialogTableTDConstructFramugaWindowGain[]':OrderDialogTableTDConstructFramugaWindowGain,
				'OrderDialogTableTDConstructFramugaWindowGlass[]':OrderDialogTableTDConstructFramugaWindowGlass,
				'OrderDialogTableTDConstructFramugaWindowGlassType[]':OrderDialogTableTDConstructFramugaWindowGlassType,
				'OrderDialogTableTDConstructFramugaWindowGrid[]':OrderDialogTableTDConstructFramugaWindowGrid,
				//Вент решетка
				'OrderDialogTableTDConstructFramugaUpGridCh[]':OrderDialogTableTDConstructFramugaUpGridCh,
				'OrderDialogTableTDConstructFramugaDownGridCh[]':OrderDialogTableTDConstructFramugaDownGridCh,
				//Дополнительно
				"Antipanik[]":Antipanik,
				"Otboynik[]":Otboynik,
				"Wicket[]":Wicket,
				"BoxLock[]":BoxLock,
				"Otvetka[]":Otvetka,
				"Isolation[]":Isolation,
				//Зарплата
				'CostLaser[]':CostLaser,
				'CostSgibka[]':CostSgibka,
				'CostSvarka[]':CostSvarka,
				'CostFrame[]':CostFrame,
				'CostMdf[]':CostMdf,
				'CostSborka[]':CostSborka,
				'CostSborkaMdf[]':CostSborkaMdf,
				'CostColor[]':CostColor,
				'CostUpak[]':CostUpak,
				'CostShpt[]':CostShpt
			},
			function(data) {
				if(data.indexOf("ok")!==false)
					{ 
						$(  "#orderDialog"  ).dialog( "close" );
						OrderSelect();
					}
					else 
						$("#OrderDialogBugs").html(data);
			}
		);
	};
}
//Загрузка диалога
function OrderDialogLoad()
{
	$("#OrderDialogBlockStatus").text("");
	$("#OrderDialogTableTRAddBugs").html("");//Очищаем баги
	$("#OrderDialogBugs").html("");
	$("#orderDialogInputStatusCh").hide();
	//Отображаем верхнюю панель диалога и кнопку сохранить
	$("#OrderDialogPanelButton").show();
	$('.ui-dialog-buttonpane button:contains("Сохранить")').button().show();
	//По необходимости прячем кнопку удалить
	if($("#orderDialogInputID").val()!="")
	{
		$('#OrderDialogBtnDelete').show();
	}
	else
	{
		$('#OrderDialogBtnDelete').hide();
		var d=new Date();
		$('#orderDialogInputBlankDate').val(d.format('dd.mm.yyyy'));
		$('#orderDialogInputBlank').val('');
		$('#orderDialogInputShet').val('');
		$('#orderDialogInputShetDate').val('');
		$('#OrderDialogInputSrok').val('');
		$('#OrderDialogInputZakaz').val('');
		$('#OrderDialogInputContact').text($("#MainComineFIO").text());
		$("#orderDialogInputStatus").val(0);
		$("#OrderDialogInputManager").text(AutorizeFIO);
		
		$("OrderDialogTableInputH").val("");
		$("OrderDialogTableInputW").val("");
		$("OrderDialogTableInputS").val("");
		$("OrderDialogTableInputRAL").val("");
		$("OrderDialogTableInputNoteSpan").text("");
		$("OrderDialogTableInputNote").val("");
		$("OrderDialogTableInputMarkirovka").val("");
		$("OrderDialogTableInputCount").val("");
		$("OrderDialogTableInputShtild").val
		$("#OrderDialogTableInputPetlyaWrk").val("");
		$("#OrderDialogTableInputPetlyaStv").val("");
		$("#OrderDialogTableInputWindowWrk").removeAttr("checked");
		$("#OrderDialogTableInputWindowStv").removeAttr("checked");
		$("#OrderDialogTableInputFramuga").removeAttr("checked");
		$("#OrderDialogTableInputFramugaH").val("");
		//Выводим номер заказа макс+1
		$.post(
			'orders/order.php',
			{'method':'OrderAddMaxBlank'},
			function (data) { $('#orderDialogInputBlank').val(data)}
		);
	};
	
	OderDialogTablePOS=1;
	$('#OrderDialogTable').find('tr').remove();
	$( "#orderDialog" ).dialog( "open" );
}

//Функция редатирования двери
function OrderDoorEditStart(el)
{
	var elTR=$(el).parent();
	var OrderDialogDoorInputs=$("#OrderDialogDoorInputs");
	switch (elTR.attr("class")){
		case "Complite":
			OrderDialogDoorInputs.find("input").prop("disabled",true);
			OrderDialogDoorInputs.find("select").prop("disabled",true);
			$("#OrderDialogDoorInputShtild").prop("disabled",false);
			break;
		default:
			OrderDialogDoorInputs.find("input").prop("disabled",false);
			OrderDialogDoorInputs.find("select").prop("disabled",false);
			break;
	}

	$("#OrderDialogDoorBugs").html("");
	$("#OrderDialogDoorID").val(elTR.attr("id"));
	$("#OrderDialogDoorInputName").val(elTR.find("td[type=Name]").text());
	$("#OrderDialogDoorInputH").val(elTR.find("td[type=H]").text());
	$("#OrderDialogDoorInputW").val(elTR.find("td[type=W]").text());
	if(elTR.find("td[type=S]").text()=="Равн.")
	{
		$("#OrderDialogDoorInputS").prop("disabled","true");
		$("#OrderDialogDoorInputSEqual").prop("checked","true");
		$("#OrderDialogDoorInputS").val("");
	}
	else
	{
		$("#OrderDialogDoorInputS").val(elTR.find("td[type=S]").text());
		$("#OrderDialogDoorInputS").removeAttr("disabled");
		$("#OrderDialogDoorInputSEqual").removeAttr("checked");
	};
	$("#OrderDialogDoorInputOpen").val(elTR.find("td[type=Open]").text());
	$("#OrderDialogDoorInputNalichnik").val(elTR.find("td[type=Nalichnik]").text());
	$("#OrderDialogDoorInputDovod").val(elTR.find("td[type=Dovod]").text());
	$("#OrderDialogDoorInputRAL").val(elTR.find("td[type=RAL]").text());
	$("#OrderDialogDoorInputNote").val(elTR.find("td[type=Note] note").text());
	$("#OrderDialogDoorInputNoteSpan").html("&nbsp;");
	if(elTR.find("td[type=Note] note").text()!="")
		$("#OrderDialogDoorInputNoteSpan").text(elTR.find("td[type=Note] note").text().substring(0,10));
	$("#OrderDialogDoorInputMarkirovka").val(elTR.find("td[type=Markirovka]").text());
	$("#OrderDialogDoorInputCount").prop("disabled",true);
	if(elTR.hasClass("Start")) $("#OrderDialogDoorInputCount").prop("disabled",false);
	$("#OrderDialogDoorInputCount").val(elTR.find("td[type=Count]").text());
	$("#OrderDialogDoorInputShtild").val(elTR.find("td[type=Shtild]").text());
	$("#OrderDialogDoorInputPetlyaWrk").val(elTR.find("td[type=PetlyaWrk]").text());
	$("#OrderDialogDoorInputPetlyaStv").val(elTR.find("td[type=PetlyaStv]").text());
	$("#OrderDialogDoorInputWindowWrk").prop("checked", elTR.find("td[type=WindowWrk] input").is(":checked"));
	$("#OrderDialogDoorInputWindowStv").prop("checked", elTR.find("td[type=WindowStv] input").is(":checked"));
	$("#OrderDialogDoorInputFramuga").prop("checked", elTR.find("td[type=FramugaCh] input").is(":checked"));
	$("#OrderDialogDoorInputFramugaH").val(elTR.find("td[type=FramugaH]").text());
	$( "#OrderDialogDoor" ).dialog("open");
}
//Функция сохранения после редактирование двери
function OrderDoorEditSave()
{
	var flagErr=false;
	var sErr="<hr>";
	var ValueNull=0;
	for(var d=0; d<OrderGlobalTypesDoorNew.length; d++)
	{
		if(OrderGlobalTypesDoorNew[d].Name==$('#OrderDialogDoorInputName').val())
		{
			ValueNull=OrderGlobalTypesDoorNew[d].ValueNull;
			break;
		};
	};
	if(ValueNull==0 & $("#OrderDialogDoorInputH").val()=="") {flagErr=true; sErr=sErr+"Не задана высота<br>"};
	if(ValueNull==0 & $("#OrderDialogDoorInputW").val()=="") {flagErr=true; sErr=sErr+"Не задана ширина<br>"};
	//if($("#OrderDialogDoorInputS").val()=="") {flagErr=true; sErr=sErr+"Не задана высота<br>"};
	if($("#OrderDialogDoorInputCount").val()=="") {flagErr=true; sErr=sErr+"Не задано количество<br>"};
	if(!flagErr)
	{
		var id=$("#OrderDialogDoorID").val();
		if($("#"+id).attr("status")=="Load") $("#"+id).attr("status","Edit");
		$("#"+id).find("td[type=Name]").text($("#OrderDialogDoorInputName").val());
		$("#"+id).find("td[type=H]").text($("#OrderDialogDoorInputH").val());
		$("#"+id).find("td[type=W]").text($("#OrderDialogDoorInputW").val());
		$("#"+id).find("td[type=S]").text($("#OrderDialogDoorInputSEqual").is(":checked")? "Равн.":$("#OrderDialogDoorInputS").val());
		$("#"+id).find("td[type=Open]").text($("#OrderDialogDoorInputOpen").val());
		$("#"+id).find("td[type=Nalichnik]").text($("#OrderDialogDoorInputNalichnik").val());
		$("#"+id).find("td[type=Dovod]").text($("#OrderDialogDoorInputDovod").val());
		$("#"+id).find("td[type=RAL]").text($("#OrderDialogDoorInputRAL").val());
		var NoteStr=$('#OrderDialogDoorInputNote').val(); if($('#OrderDialogDoorInputNote').val().length>65) NoteStr=$('#OrderDialogDoorInputNote').val().substring(0,65)+" <img src='images/DocumentNext.png'>";
		$("#"+id).find("td[type=Note]").html(NoteStr+' <note style="display:none" onclick=" OrderDialogNoteView(this)">'+$('#OrderDialogDoorInputNote').val()+'</note>');
		$("#"+id).find("td[type=Markirovka]").text($("#OrderDialogDoorInputMarkirovka").val());
		$("#"+id).find("td[type=Count]").text($("#OrderDialogDoorInputCount").val());
		$("#"+id).find("td[type=Shtild]").text($("#OrderDialogDoorInputShtild").val());
		$("#"+id).find("td[type=PetlyaWrk]").text($("#OrderDialogDoorInputPetlyaWrk").val());
		$("#"+id).find("td[type=Construct] span WorkPetlya").text($("#OrderDialogDoorInputPetlyaWrk").val());
		$("#"+id).find("td[type=PetlyaStv]").text($("#OrderDialogDoorInputSEqual").is(":checked") || $("#OrderDialogDoorInputS").val()!=""? ($("#OrderDialogDoorInputPetlyaStv").val()!=""? $("#OrderDialogDoorInputPetlyaStv").val() : "2") : "");
		$("#"+id).find("td[type=Construct] span stvorkapetlya").text($("#OrderDialogDoorInputSEqual").is(":checked") || $("#OrderDialogDoorInputS").val()!=""? ($("#OrderDialogDoorInputPetlyaStv").val()!=""? $("#OrderDialogDoorInputPetlyaStv").val() : "2") : "");
		$("#"+id).find("td[type=WindowWrk] input").prop("checked", $("#OrderDialogDoorInputWindowWrk").is(":checked"));
		$("#"+id).find("td[type=Construct] span WorkWindowCh").text($("#OrderDialogDoorInputWindowWrk").is(":checked")? "true":"false" );
		$("#"+id).find("td[type=WindowStv] input").prop("checked", $("#OrderDialogDoorInputWindowStv").is(":checked"));
		$("#"+id).find("td[type=Construct] span StvorkaWindowCh").text($("#OrderDialogDoorInputWindowStv").is(":checked")? "true":"false" );
		$("#"+id).find("td[type=FramugaCh] input").prop("checked", $("#OrderDialogDoorInputFramuga").is(":checked"));
		$("#"+id).find("td[type=Construct] span FramugaCh").text($("#OrderDialogDoorInputFramuga").is(":checked")? "true":"false" );
		$("#"+id).find("td[type=FramugaH]").text($("#OrderDialogDoorInputFramugaH").val());
		$("#"+id).find("td[type=Construct] span FramugaH").text($("#OrderDialogDoorInputFramugaH").val());
		$( "#OrderDialogDoor" ).dialog("close");
		//if(AutorizeType==1 || AutorizeType==2) if(confirm("Пересчитать зарплату сотрудникам?")) OrderPrlCalcAddEditRow($("#OrderDialogDoorID").val());
	}
	else $("#OrderDialogDoorBugs").html(sErr);
}

//----------------------------Корзина---------------------------------------------
function OrderTrashSelect()
{
	$.post(
		'orders/order.php',
		{
			'method':'selectTrash'
		},
		function(data){
			$('#OrderTrashTable').find('tr').remove();
			var o=jQuery.parseJSON(data);
			var i=0;
			while(o[i]!=null)
			{
				$("#OrderTrashTable").append(
					'<tr id=OrdeTrashTableTR'+o[i]['id']+' >'+
						'<td><button onClick="OrderTrashDelete('+o[i]['id']+')">Удалить</button><button onClick="OrderTrashRecovery('+o[i]['id']+')">Восстановить</button></td>'+
						'<td>'+o[i]['Blank']+'</td>'+
						'<td>'+o[i]['bd']+'</td>'+
						'<td>'+o[i]['Shet']+'</td>'+
						'<td>'+o[i]['sd']+'</td>'+
						'<td>'+o[i]['Srok']+'</td>'+
						'<td>'+o[i]['Zakaz']+'</td>'+
						'<td>'+o[i]['Contact']+'</td>'+
					'</tr>'
				);
				i++;
			};
			$('#orderTrashDialog').dialog('open');
		}
	);
}

function OrderTrashRecovery(id)
{
	if(confirm('Восстановить заказ?'))
		$.post(
			'orders/order.php',
			{ 'method':'recoveryTrash', 'id':id},
			function (data)	{ $('#OrdeTrashTableTR'+id).remove();}
		);
}

//Удалить заказ навсегда
function OrderTrashDelete(id)
{
	if(confirm('Удалить заказ?'))
	$.post(
		'orders/order.php',
		{ 'method':'deleteTrash', 'id':id},
		function (data)
		{
			if(data=='ok') {$('#OrdeTrashTableTR'+id).remove();}
			else alert("Ошибка удаления: "+data);
		}
	);
}

//-----------------------------Процесс выполнение дверей------------------
function OrderDoorProcessingOpen(idDoor)
{
	var elTR=$("#OrderDialogTable tr[idDoor="+idDoor+"]");
	$("#OrderDoorProcessingMdf").parent().parent().hide();
	$("#OrderDoorProcessingSborkaMdf").parent().parent().hide();
	if(elTR.find("td[type=name]").text().indexOf("МДФ")>-1)
	{
		$("#OrderDoorProcessingMdf").parent().parent().show();
		$("#OrderDoorProcessingSborkaMdf").parent().parent().show();
	};

	$("#OrderDoorProcessingLoader").show();
	$("#OrderDoorProcessingLaserText").text(0);
	$("#OrderDoorProcessingLaser").progressbar( "option", "value", 0);
	$("#OrderDoorProcessingSgibkaText").text(0);
	$("#OrderDoorProcessingSgibka").progressbar( "option", "value", 0 );
	$("#OrderDoorProcessingSvarkaText").text(0);
	$("#OrderDoorProcessingSvarka").progressbar( "option", "value",0 );
	$("#OrderDoorProcessingMdfText").text(0);
	$("#OrderDoorProcessingMdf").progressbar( "option", "value",0 );
	$("#OrderDoorProcessingSborkaText").text(0);
	$("#OrderDoorProcessingSborka").progressbar( "option", "value", 0 );
	$("#OrderDoorProcessingSborkaMdfText").text(0);
	$("#OrderDoorProcessingSborkaMdf").progressbar( "option", "value", 0 );
	$("#OrderDoorProcessingColorText").text(0);
	$("#OrderDoorProcessingColor").progressbar( "option", "value", 0 );
	$("#OrderDoorProcessingUpakText").text(0);
	$("#OrderDoorProcessingUpak").progressbar( "option", "value", 0);
	
	$.post(
		'orders/order.php',
		{ 'method':'OrderDoorProcessing', 'idDoor':idDoor},
		function (data)
		{
			$("#OrderDoorProcessingLoader").hide();
			var o=jQuery.parseJSON(data);
			$("#OrderDoorProcessingLaserText").text(o.CountLaser);
			$("#OrderDoorProcessingLaser").progressbar( "option", "value", parseInt(o.CountLaserPersent) );
			$("#OrderDoorProcessingSgibkaText").text(o.CountSgibka);
			$("#OrderDoorProcessingSgibka").progressbar( "option", "value", parseInt(o.CountSgibkaPersent) );
			$("#OrderDoorProcessingSvarkaText").text(o.CountSvarka);
			$("#OrderDoorProcessingSvarka").progressbar( "option", "value", parseInt(o.CountSvarkaPersent) );
			$("#OrderDoorProcessingMdfText").text(o.CountMdf);
			$("#OrderDoorProcessingMdf").progressbar( "option", "value", parseInt(o.CountMdfPersent) );
			$("#OrderDoorProcessingSborkaText").text(o.CountSborka);
			$("#OrderDoorProcessingSborka").progressbar( "option", "value", parseInt(o.CountSborkaPersent) );
			$("#OrderDoorProcessingColorText").text(o.CountColor);
			$("#OrderDoorProcessingColor").progressbar( "option", "value", parseInt(o.CountColorPersent) );
			$("#OrderDoorProcessingSborkaMdfText").text(o.CountSborkaMdf);
			$("#OrderDoorProcessingSborkaMdf").progressbar( "option", "value", parseInt(o.CountSborkaMdfPersent) );
			$("#OrderDoorProcessingUpakText").text(o.CountUpak);
			$("#OrderDoorProcessingUpak").progressbar( "option", "value", parseInt(o.CountUpakPersent) );
		}
	)
	
	$( "#OrderDoorProcessingDialog" ).dialog("open");
}

//--------------------------------------------------Конструктор---------------------------------------------------------
//Открытие диалога конструции двери (открытие конструктора)
function OrderDoorConstructOpen(el)
{
	var elTR=$(el).parent().parent();
	if(elTR.attr("status")=="Load" & elTR.attr("class")!="Work" & elTR.attr("class")!="Complite") elTR.attr("status","Edit");
	$("#OrderDialogConstructID").val(elTR.attr("id"));
	$("#OrderDialogConstructWorkSectionOpen").val(elTR.find("td[type=Open]").text());
	$("#OrderDialogConstructWorkSectionW").val(elTR.find("td[type=W]").text());
	$("#OrderDialogConstructWorkSectionH").val(elTR.find("td[type=H]").text());
	$("#OrderDialogConstructStvorkaS").val(elTR.find("td[type=S]").text());
	//--Рабочая секция
	$("#OrderDialogConstructWorkSectionPetlya").val(elTR.find("td[type=Construct] span WorkPetlya").text());
	//Окно
	var NumDoorArr=new Array("","1","2");
	for(var i=0;i<3;i++)
	{
		if(elTR.find("td[type=Construct] span WorkWindowCh"+NumDoorArr[i]).text()=="true")
		{
			$("#OrderDialogConstructWorkWindowCh"+NumDoorArr[i]).prop("checked",true);
			$("#OrderDialogConstructWorkWindowP"+NumDoorArr[i]).show();
		}
		else
		{
			$("#OrderDialogConstructWorkWindowCh"+NumDoorArr[i]).removeAttr("checked");
			$("#OrderDialogConstructWorkWindowP"+NumDoorArr[i]).hide();
		};
		$("#OrderDialogConstructWorkWindowNoFrame"+NumDoorArr[i]).removeAttr("checked");
		if(elTR.find("td[type=Construct] span WorkWindowNoFrame"+NumDoorArr[i]).text()=="true") $("#OrderDialogConstructWorkWindowNoFrame"+NumDoorArr[i]).prop("checked",true);
		$("#OrderDialogConstructWorkWindowH"+NumDoorArr[i]).val(elTR.find("td[type=Construct] span WorkWindowH"+NumDoorArr[i]).text());
		$("#OrderDialogConstructWorkWindowW"+NumDoorArr[i]).val(elTR.find("td[type=Construct] span WorkWindowW"+NumDoorArr[i]).text());
		$("#OrderDialogConstructWorkWindowGain"+NumDoorArr[i]).removeAttr("checked");
		$("#OrderDialogConstructWorkWindowGlass"+NumDoorArr[i]).removeAttr("checked");
		$("#OrderDialogConstructWorkWindowGrid"+NumDoorArr[i]).removeAttr("checked");
		if(elTR.find("td[type=Construct] span WorkWindowGain"+NumDoorArr[i]).text()=="true") $("#OrderDialogConstructWorkWindowGain"+NumDoorArr[i]).prop("checked",true);
		if(elTR.find("td[type=Construct] span WorkWindowGlass"+NumDoorArr[i]).text()=="true") $("#OrderDialogConstructWorkWindowGlass"+NumDoorArr[i]).prop("checked",true);
		if(elTR.find("td[type=Construct] span WorkWindowGrid"+NumDoorArr[i]).text()=="true") $("#OrderDialogConstructWorkWindowGrid"+NumDoorArr[i]).prop("checked",true);
		$("#OrderDialogConstructWorkWindowGlassType"+NumDoorArr[i]).prop("disabled",true);
		if(elTR.find("td[type=Construct] span WorkWindowGlass"+NumDoorArr[i]).text()=="true") $("#OrderDialogConstructWorkWindowGlassType"+NumDoorArr[i]).prop("disabled",false);
		$("#OrderDialogConstructWorkWindowGlassType"+NumDoorArr[i]).val("Простое");
	};
	//Вент решетка
	$("#OrderDialogConstructWorkUpGridCh").removeAttr("checked");
	if(elTR.find("td[type=Construct] span WorkUpGridCh").text()=="true") $("#OrderDialogConstructWorkUpGridCh").prop("checked",true);
	$("#OrderDialogConstructWorkDownGridCh").removeAttr("checked");
	if(elTR.find("td[type=Construct] span WorkDownGridCh").text()=="true") $("#OrderDialogConstructWorkDownGridCh").prop("checked",true);
	
	
	if(elTR.find("td[type=Construct] span WorkWindowGlassType").text()!="") $("#OrderDialogConstructWorkWindowGlassType").val(elTR.find("td[type=Construct] span WorkWindowGlassType").text());
	//--Вторая створка
	{
		$("#OrderDialogConstructStvorkaPetlya").val(elTR.find("td[type=Construct] span StvorkaPetlya").text());
		//Окно
		for(var i=0; i<3;i++)
		{
			if(elTR.find("td[type=Construct] span StvorkaWindowCh"+NumDoorArr[i]).text()=="true")
			{
				$("#OrderDialogConstructStvorkaWindowCh"+NumDoorArr[i]).prop("checked",true);
				$("#OrderDialogConstructStvorkaWindowP"+NumDoorArr[i]).show();
			}
			else
			{
				$("#OrderDialogConstructStvorkaWindowCh"+NumDoorArr[i]).removeAttr("checked");
				$("#OrderDialogConstructStvorkaWindowP"+NumDoorArr[i]).hide();
			};
			$("#OrderDialogConstructStvorkaWindowNoFrame"+NumDoorArr[i]).removeAttr("checked");
			if(elTR.find("td[type=Construct] span StvorkaWindowNoFrame"+NumDoorArr[i]).text()=="true") $("#OrderDialogConstructStvorkaWindowNoFrame"+NumDoorArr[i]).prop("checked",true);
			$("#OrderDialogConstructStvorkaWindowH"+NumDoorArr[i]).val(elTR.find("td[type=Construct] span StvorkaWindowH"+NumDoorArr[i]).text());
			$("#OrderDialogConstructStvorkaWindowW"+NumDoorArr[i]).val(elTR.find("td[type=Construct] span StvorkaWindowW"+NumDoorArr[i]).text());
			$("#OrderDialogConstructStvorkaWindowGain"+NumDoorArr[i]).removeAttr("checked");
			$("#OrderDialogConstructStvorkaWindowGlass"+NumDoorArr[i]).removeAttr("checked");
			$("#OrderDialogConstructStvorkaWindowGrid"+NumDoorArr[i]).removeAttr("checked");
			if(elTR.find("td[type=Construct] span StvorkaWindowGain"+NumDoorArr[i]).text()=="true") $("#OrderDialogConstructStvorkaWindowGain"+NumDoorArr[i]).prop("checked",true);
			if(elTR.find("td[type=Construct] span StvorkaWindowGlass"+NumDoorArr[i]).text()=="true") $("#OrderDialogConstructStvorkaWindowGlass"+NumDoorArr[i]).prop("checked",true);
			if(elTR.find("td[type=Construct] span StvorkaWindowGrid"+NumDoorArr[i]).text()=="true") $("#OrderDialogConstructStvorkaWindowGrid"+NumDoorArr[i]).prop("checked",true);
			
			$("#OrderDialogConstructStvorkaWindowGlassType"+NumDoorArr[i]).prop("disabled",true);
			if(elTR.find("td[type=Construct] span StvorkaWindowGlass"+NumDoorArr[i]).text()=="true") $("#OrderDialogConstructStvorkaWindowGlassType"+NumDoorArr[i]).prop("disabled",false);
			$("#OrderDialogConstructStvorkaWindowGlassType"+NumDoorArr[i]).val("Простое");
			if(elTR.find("td[type=Construct] span StvorkaWindowGlassType"+NumDoorArr[i]).text()!="") $("#OrderDialogConstructStvorkaWindowGlassType"+NumDoorArr[i]).val(elTR.find("td[type=Construct] span StvorkaWindowGlassType"+NumDoorArr[i]).text());
		};
		$("#OrderDialogConstructStvorkaUpGridCh").removeAttr("checked");
		if(elTR.find("td[type=Construct] span StvorkaUpGridCh").text()=="true") $("#OrderDialogConstructStvorkaUpGridCh").prop("checked",true);
		$("#OrderDialogConstructStvorkaDownGridCh").removeAttr("checked");
		if(elTR.find("td[type=Construct] span StvorkaDownGridCh").text()=="true") $("#OrderDialogConstructStvorkaDownGridCh").prop("checked",true);
	};
	//Фрамуга
	if(elTR.find("td[type=Construct] span FramugaCh").text()=="true")
		{	$("#OrderDialogConstructMenu3 div").show();}
		else $("#OrderDialogConstructMenu3 div").hide();
	$("#OrderDialogConstructFramugH").val(elTR.find("td[type=Construct] span FramugaH").text());
	if(elTR.find("td[type=Construct] span FramugaWindowCh").text()=="true")
	{
		$("#OrderDialogConstructFramugWindowCh").prop("checked",true);
		$("#OrderDialogConstructFramugWindowP").show();
	}
	else
	{
		$("#OrderDialogConstructFramugWindowCh").removeAttr("checked");
		$("#OrderDialogConstructFramugWindowP").hide();
	};
	$("#OrderDialogConstructFramugaWindowNoFrame").removeAttr("checked");
	if(elTR.find("td[type=Construct] span FramugaWindowNoFrame").text()=="true") $("#OrderDialogConstructFramugaWindowNoFrame").prop("checked",true);
	$("#OrderDialogConstructFramugWindowH").val(elTR.find("td[type=Construct] span FramugaWindowH").text());
	$("#OrderDialogConstructFramugWindowW").val(elTR.find("td[type=Construct] span FramugaWindowW").text());
	$("#OrderDialogConstructFramugWindowGain").removeAttr("checked");
	$("#OrderDialogConstructFramugWindowGlass").removeAttr("checked");
	$("#OrderDialogConstructFramugWindowGrid").removeAttr("checked");
	if(elTR.find("td[type=Construct] span FramugaWindowGain").text()=="true") $("#OrderDialogConstructFramugWindowGain").prop("checked",true);
	if(elTR.find("td[type=Construct] span FramugaWindowGlass").text()=="true") $("#OrderDialogConstructFramugWindowGlass").prop("checked",true);
	if(elTR.find("td[type=Construct] span FramugaWindowGrid").text()=="true") $("#OrderDialogConstructFramugWindowGrid").prop("checked",true);
	
	$("#OrderDialogConstructFramugWindowGlassType").prop("disabled",true);
	if(elTR.find("td[type=Construct] span FramugaWindowGlass").text()=="true") $("#OrderDialogConstructFramugWindowGlassType").prop("disabled",false);
	$("#OrderDialogConstructFramugWindowGlassType").val("Простое");
	if(elTR.find("td[type=Construct] span FramugaWindowGlassType").text()!="") $("#OrderDialogConstructFramugWindowGlassType").val(elTR.find("td[type=Construct] span FramugaWindowGlassType").text());
		
	$("#OrderDialogConstructFramugUpGridCh").removeAttr("checked");
	if(elTR.find("td[type=Construct] span FramugaUpGridCh").text()=="true") $("#OrderDialogConstructFramugUpGridCh").prop("checked",true);
	$("#OrderDialogConstructFramugDownGridCh").removeAttr("checked");
	if(elTR.find("td[type=Construct] span FramugaDownGridCh").text()=="true") $("#OrderDialogConstructFramugDownGridCh").prop("checked",true);

	if($("#OrderDialogConstructStvorkaS").val()=="") {$("#OrderDialogConstructMenu2").hide();} else { $("#OrderDialogConstructMenu2").show()};
	//Дополнительно
	var OtherElementArr=["Antipanik","Otboynik","Wicket","BoxLock","Otvetka","Isolation"];
	for(var e in OtherElementArr)
	{
		switch(parseInt(elTR.find("td[type=Construct] span "+OtherElementArr[e]).text()))
		{
			case 0: $("#OrderDialogConstructOther"+OtherElementArr[e]).val(""); break;
			case 1: $("#OrderDialogConstructOther"+OtherElementArr[e]).val("Да"); break;
			case 2: $("#OrderDialogConstructOther"+OtherElementArr[e]).val("Нет"); break;
		};
	};
	$("#OrderDialogConstruct").dialog("open");
	OrderConstructDraw();
}

function OrderDoorConstructSave(idDoor)
{
	if(idDoor!='')
	{
		//--Рабочая створка
		$("#"+idDoor+" td[type=Construct] span WorkPetlya").text($("#OrderDialogConstructWorkSectionPetlya").val());
		$("#"+idDoor+" td[type=PetlyaWrk]").text($("#OrderDialogConstructWorkSectionPetlya").val());
		//Окна
		var NumDoorArr=new Array("","1","2");
		for(var i=0;i<3;i++)
		{
			$("#"+idDoor+" td[type=Construct] span WorkWindowCh"+NumDoorArr[i]).text($("#OrderDialogConstructWorkWindowCh"+NumDoorArr[i]).is(":checked"));
			//Переключатель на форме заказа - работает для первого окна
			$("#"+idDoor+" td[type=WindowWrk] input").prop("checked",$("#OrderDialogConstructWorkWindowCh").is(":checked"));
			
			$("#"+idDoor+" td[type=Construct] span WorkWindowNoFrame"+NumDoorArr[i]).text($("#OrderDialogConstructWorkWindowNoFrame"+NumDoorArr[i]).is(":checked"));
			$("#"+idDoor+" td[type=Construct] span WorkWindowH"+NumDoorArr[i]).text($("#OrderDialogConstructWorkWindowH"+NumDoorArr[i]).val());
			$("#"+idDoor+" td[type=Construct] span WorkWindowW"+NumDoorArr[i]).text($("#OrderDialogConstructWorkWindowW"+NumDoorArr[i]).val());
			$("#"+idDoor+" td[type=Construct] span WorkWindowGain"+NumDoorArr[i]).text($("#OrderDialogConstructWorkWindowGain"+NumDoorArr[i]).is(":checked"));
			$("#"+idDoor+" td[type=Construct] span WorkWindowGlass"+NumDoorArr[i]).text($("#OrderDialogConstructWorkWindowGlass"+NumDoorArr[i]).is(":checked"));
			$("#"+idDoor+" td[type=Construct] span WorkWindowGlassType"+NumDoorArr[i]).text($("#OrderDialogConstructWorkWindowGlassType"+NumDoorArr[i]).val());
			$("#"+idDoor+" td[type=Construct] span WorkWindowGrid"+NumDoorArr[i]).text($("#OrderDialogConstructWorkWindowGrid"+NumDoorArr[i]).is(":checked"));
		};
		//Вент решетки
		$("#"+idDoor+" td[type=Construct] span WorkUpGridCh").text($("#OrderDialogConstructWorkUpGridCh").is(":checked"));
		$("#"+idDoor+" td[type=Construct] span WorkDownGridCh").text($("#OrderDialogConstructWorkDownGridCh").is(":checked"));
		//--Створка
		$("#"+idDoor+" td[type=Construct] span StvorkaPetlya").text($("#OrderDialogConstructStvorkaPetlya").val());
		$("#"+idDoor+" td[type=PetlyaStv]").text($("#OrderDialogConstructStvorkaPetlya").val());
		//Окна
		for(var i=0;i<3;i++)
		{
			$("#"+idDoor+" td[type=Construct] span StvorkaWindowCh"+NumDoorArr[i]).text($("#OrderDialogConstructStvorkaWindowCh"+NumDoorArr[i]).is(":checked"));
			//Переключатель на форме заказа - работает для первого окна
			$("#"+idDoor+" td[type=WindowStv] input").prop("checked", $("#OrderDialogConstructStvorkaWindowCh").is(":checked"));
			$("#"+idDoor+" td[type=Construct] span StvorkaWindowNoFrame"+NumDoorArr[i]).text($("#OrderDialogConstructStvorkaWindowNoFrame"+NumDoorArr[i]).is(":checked"));
			$("#"+idDoor+" td[type=Construct] span StvorkaWindowH"+NumDoorArr[i]).text($("#OrderDialogConstructStvorkaWindowH"+NumDoorArr[i]).val());
			$("#"+idDoor+" td[type=Construct] span StvorkaWindowW"+NumDoorArr[i]).text($("#OrderDialogConstructStvorkaWindowW"+NumDoorArr[i]).val());
			$("#"+idDoor+" td[type=Construct] span StvorkaWindowGain"+NumDoorArr[i]).text($("#OrderDialogConstructStvorkaWindowGain"+NumDoorArr[i]).is(":checked"));
			$("#"+idDoor+" td[type=Construct] span StvorkaWindowGlass"+NumDoorArr[i]).text($("#OrderDialogConstructStvorkaWindowGlass"+NumDoorArr[i]).is(":checked"));
			$("#"+idDoor+" td[type=Construct] span StvorkaWindowGlassType"+NumDoorArr[i]).text($("#OrderDialogConstructStvorkaWindowGlassType"+NumDoorArr[i]).val());
			$("#"+idDoor+" td[type=Construct] span StvorkaWindowGrid"+NumDoorArr[i]).text($("#OrderDialogConstructStvorkaWindowGrid"+NumDoorArr[i]).is(":checked"));
		};
		//Вент решетки
		$("#"+idDoor+" td[type=Construct] span StvorkaUpGridCh").text($("#OrderDialogConstructStvorkaUpGridCh").is(":checked"));
		$("#"+idDoor+" td[type=Construct] span StvorkaDownGridCh").text($("#OrderDialogConstructStvorkaDownGridCh").is(":checked"));
		//--Фрамуга
		$("#"+idDoor+" td[type=Construct] span FramugaCh").text("false");
		if($("#OrderDialogConstructMenu3 div").is(":visible")) $("#"+idDoor+" td[type=Construct] span FramugaCh").text("true");
		$("#"+idDoor+" td[type=FramugaCh] input").prop("checked",$("#OrderDialogConstructMenu3 div").is(":visible"));
		$("#"+idDoor+" td[type=Construct] span FramugaH").text($("#OrderDialogConstructFramugH").val());
		$("#"+idDoor+" td[type=FramugaH]").text($("#OrderDialogConstructFramugH").val());

		$("#"+idDoor+" td[type=Construct] span FramugaWindowCh").text($("#OrderDialogConstructFramugWindowCh").is(":checked"));
		$("#"+idDoor+" td[type=Construct] span FramugaWindowNoFrame").text($("#OrderDialogConstructFramugaWindowNoFrame").is(":checked"));
		$("#"+idDoor+" td[type=Construct] span FramugaWindowH").text($("#OrderDialogConstructFramugWindowH").val());
		$("#"+idDoor+" td[type=Construct] span FramugaWindowW").text($("#OrderDialogConstructFramugWindowW").val());
		$("#"+idDoor+" td[type=Construct] span FramugaWindowGain").text($("#OrderDialogConstructFramugWindowGain").is(":checked"));
		$("#"+idDoor+" td[type=Construct] span FramugaWindowGlass").text($("#OrderDialogConstructFramugWindowGlass").is(":checked"));
		$("#"+idDoor+" td[type=Construct] span FramugaWindowGlassType").text($("#OrderDialogConstructFramugWindowGlassType").val());
		$("#"+idDoor+" td[type=Construct] span FramugaWindowGrid").text($("#OrderDialogConstructFramugWindowGrid").is(":checked"));

		$("#"+idDoor+" td[type=Construct] span FramugaUpGridCh").text($("#OrderDialogConstructFramugUpGridCh").is(":checked"));
		$("#"+idDoor+" td[type=Construct] span FramugaDownGridCh").text($("#OrderDialogConstructFramugDownGridCh").is(":checked"));

		//Дополнительно
		var OtherElementArr=["Antipanik","Otboynik","Wicket","BoxLock","Otvetka","Isolation"];
		for(var e in OtherElementArr)
			switch($("#OrderDialogConstructOther"+OtherElementArr[e]).val())
			{
				case "": $("#"+idDoor+" td[type=Construct] span "+OtherElementArr[e]).text("0"); break;
				case "Да": $("#"+idDoor+" td[type=Construct] span "+OtherElementArr[e]).text("1"); break;
				case "Нет": $("#"+idDoor+" td[type=Construct] span "+OtherElementArr[e]).text("2"); break;
			};
	};
	
	$("#OrderDialogConstruct").dialog("close");
}

//Рисование конструкции двери
function OrderConstructDraw(){
	var canvas = document.getElementById('OrderDialogConstructCanvas');
	var ctx = canvas.getContext('2d');
	ctx.clearRect (0, 0, 400, 500);
	ctx.strokeStyle="black";
	ctx.lineWidth =1;
	ctx.strokeRect (0, 0, 400, 500);
		
	//Открывание
	if($("#OrderDialogConstructWorkSectionOpen").val()=="Прав." & $("#OrderDialogConstructStvorkaS").val()=="") CanvasLine(30,250,30,280, 'OrderDialogConstructCanvas');
	if($("#OrderDialogConstructWorkSectionOpen").val()=="Лев." & $("#OrderDialogConstructStvorkaS").val()=="") CanvasLine(190,250,190,280, 'OrderDialogConstructCanvas');
	if($("#OrderDialogConstructWorkSectionOpen").val()=="Прав." & $("#OrderDialogConstructStvorkaS").val()!="" & $("#OrderDialogConstructStvorkaS").val()!="Равн.") CanvasLine(190,250,190,280, 'OrderDialogConstructCanvas');
	if($("#OrderDialogConstructWorkSectionOpen").val()=="Лев." & $("#OrderDialogConstructStvorkaS").val()!="" & $("#OrderDialogConstructStvorkaS").val()!="Равн.") CanvasLine(190,250,190,280, 'OrderDialogConstructCanvas');
	if($("#OrderDialogConstructWorkSectionOpen").val()=="Прав." & $("#OrderDialogConstructStvorkaS").val()=="Равн.") CanvasLine(215,250,215,280, 'OrderDialogConstructCanvas');
	if($("#OrderDialogConstructWorkSectionOpen").val()=="Лев." & $("#OrderDialogConstructStvorkaS").val()=="Равн.") CanvasLine(170,250,170,280, 'OrderDialogConstructCanvas');
		
	var DoorOpenWorkX=10; 
	if($("#OrderDialogConstructWorkSectionOpen").val()=="Прав." & $("#OrderDialogConstructStvorkaS").val()!="") DoorOpenWorkX=160;
	if($("#OrderDialogConstructWorkSectionOpen").val()=="Прав." & $("#OrderDialogConstructStvorkaS").val()=="Равн.") DoorOpenWorkX=185;
	var DoorOpenStvorkaX=210; 
	if($("#OrderDialogConstructWorkSectionOpen").val()=="Прав." & $("#OrderDialogConstructStvorkaS").val()!="") DoorOpenStvorkaX=10;
	if($("#OrderDialogConstructWorkSectionOpen").val()!="Прав." & $("#OrderDialogConstructStvorkaS").val()=="Равн.") DoorOpenStvorkaX=185;

	var DoorWorkW=$("#OrderDialogConstructStvorkaS").val()!="Равн."?200:175;
	var DoorStvorkaW=$("#OrderDialogConstructStvorkaS").val()!="Равн."?150:175;
		
	//-----Рабочая створка-----
	ctx.lineWidth=2;
	ctx.strokeStyle="blue";
	ctx.strokeRect(DoorOpenWorkX,110,DoorWorkW,350);
		
	//--Окно
	//определим кол-во окно на рабочей створке
	var WindowCount=0;
	if($("#OrderDialogConstructWorkWindowCh").is(":checked")) WindowCount++;
	if($("#OrderDialogConstructWorkWindowCh1").is(":checked")) WindowCount++;
	if($("#OrderDialogConstructWorkWindowCh2").is(":checked")) WindowCount++;
	if(WindowCount>0)
	{
		var X0=50+DoorOpenWorkX;
		var Y0=180;
		var XH=100;
		//180 - высота всего места под окна, 10 - расстояние между окнами
		var HW=Math.floor((180-WindowCount*10)/WindowCount); // - высота одного окна
		var ArrWindowNames=new Array("","1","2");
		var Yd0=Y0;//текущая начальная координата двери
		for(var i=0;WindowCount>0;i++)
			if($("#OrderDialogConstructWorkWindowCh"+ArrWindowNames[i]).is(":checked"))
			{
				ctx.lineWidth=1;
				ctx.strokeStyle="red";
				ctx.strokeRect(X0,Yd0,100,HW);
				//Решетка
				if($("#OrderDialogConstructWorkWindowGain"+ArrWindowNames[i]).is(":checked")) CanvasReinforcementRect(X0,Yd0,100,HW,"OrderDialogConstructCanvas");
				if($("#OrderDialogConstructWorkWindowGlass"+ArrWindowNames[i]).is(":checked")) CanvasGlassRect(X0,Yd0,100,HW,"OrderDialogConstructCanvas");
				if($("#OrderDialogConstructWorkWindowGrid"+ArrWindowNames[i]).is(":checked")) CanvasGridRect(X0,Yd0,100,HW,"OrderDialogConstructCanvas");
				Yd0+=HW+10;
				WindowCount--;
			};
	};
	//Вент решетка на рабочей створке
	if($("#OrderDialogConstructWorkUpGridCh").is(":checked"))
	{
		ctx.lineWidth=1;
		ctx.strokeStyle="red";
		for(var i=0;i<30;i+=10)
		{
			ctx.moveTo(50+DoorOpenWorkX, 130+i);
			ctx.lineTo(150+DoorOpenWorkX, 130+i);
			ctx.stroke();
		};
	};
	if($("#OrderDialogConstructWorkDownGridCh").is(":checked"))
	{
		ctx.lineWidth=1;
		ctx.strokeStyle="red";
		for(var i=0;i<30;i+=10)
		{
			ctx.moveTo(50+DoorOpenWorkX, 380+i);
			ctx.lineTo(150+DoorOpenWorkX, 380+i);
			ctx.stroke();
		};
	};
		
	//-----Вторая створка-----
	if($("#OrderDialogConstructStvorkaS").val()!="")
	{
		ctx.lineWidth=2;
		ctx.strokeStyle="blue";
		ctx.strokeRect((DoorOpenStvorkaX),110,DoorStvorkaW,350);
		//--Окно
		//определим кол-во окно на рабочей створке
		var WindowCount=0;
		if($("#OrderDialogConstructStvorkaWindowCh").is(":checked")) WindowCount++;
		if($("#OrderDialogConstructStvorkaWindowCh1").is(":checked")) WindowCount++;
		if($("#OrderDialogConstructStvorkaWindowCh2").is(":checked")) WindowCount++;
		if(WindowCount>0)
		{
			var X0=25+DoorOpenStvorkaX;
			var Y0=180;
			var XH=100;
			//180 - высота всего места под окна, 10 - расстояние между окнами
			var HW=Math.floor((180-WindowCount*10)/WindowCount); // - высота одного окна
			var ArrWindowNames=new Array("","1","2");
			var Yd0=Y0;//текущая начальная координата двери
			for(var i=0;WindowCount>0;i++)
				if($("#OrderDialogConstructStvorkaWindowCh"+ArrWindowNames[i]).is(":checked"))
				{
					ctx.lineWidth=1;
					ctx.strokeStyle="red";
					ctx.strokeRect(X0,Yd0,100,HW);
					//Решетка
					if($("#OrderDialogConstructStvorkaWindowGain"+ArrWindowNames[i]).is(":checked")) CanvasReinforcementRect(X0,Yd0,100,HW,"OrderDialogConstructCanvas");
					if($("#OrderDialogConstructStvorkaWindowGlass"+ArrWindowNames[i]).is(":checked")) CanvasGlassRect(X0,Yd0,100,HW,"OrderDialogConstructCanvas");
					if($("#OrderDialogConstructStvorkaWindowGrid"+ArrWindowNames[i]).is(":checked")) CanvasGridRect(X0,Yd0,100,HW,"OrderDialogConstructCanvas");
					Yd0+=HW+10;
					WindowCount--;
				};
		};
		//Вент решетка
		if($("#OrderDialogConstructStvorkaUpGridCh").is(":checked"))
		{
			ctx.lineWidth=1;
			ctx.strokeStyle="red";
			for(var i=0;i<30;i+=10)
			{
				ctx.moveTo(25+DoorOpenStvorkaX, 130+i);
				ctx.lineTo(125+DoorOpenStvorkaX, 130+i);
				ctx.stroke();
			};
		};
		if($("#OrderDialogConstructStvorkaDownGridCh").is(":checked"))
		{
			ctx.lineWidth=1;
			ctx.strokeStyle="red";
			for(var i=0;i<30;i+=10)
			{
				ctx.moveTo(25+DoorOpenStvorkaX, 380+i);
				ctx.lineTo(125+DoorOpenStvorkaX, 380+i);
				ctx.stroke();
			};
		};
	};
		
	//Фрамуга
	var FramugaXMinus=0; if($("#OrderDialogConstructStvorkaS").val()=="") FramugaXMinus=150;
	if($("#OrderDialogConstructMenu3 div").is(":visible")) 
	{
		ctx.lineWidth=2;
		ctx.strokeStyle="blue";
		ctx.strokeRect(10,10,350-FramugaXMinus,100);
		//Окно
		if($("#OrderDialogConstructFramugWindowCh").is(":checked"))
		{
			ctx.lineWidth=1;
			ctx.strokeStyle="red";
			ctx.strokeRect(60,40,250-FramugaXMinus,40);
			if($("#OrderDialogConstructFramugWindowGain").is(":checked")) CanvasReinforcementRect(60,30,250-FramugaXMinus,60,"OrderDialogConstructCanvas");
			if($("#OrderDialogConstructFramugWindowGlass").is(":checked")) CanvasGlassRect(60,30,250-FramugaXMinus,60,"OrderDialogConstructCanvas");
			if($("#OrderDialogConstructFramugWindowGrid").is(":checked")) CanvasGridRect(60,30,250-FramugaXMinus,60,"OrderDialogConstructCanvas");
		};
		//Вент решетка
		if($("#OrderDialogConstructFramugUpGridCh").is(":checked"))
		{
			ctx.lineWidth=1;
			ctx.strokeStyle="red";
			for(var i=0;i<15;i+=5)
			{
				ctx.moveTo(60, 20+i);
				ctx.lineTo(310-FramugaXMinus, 20+i);
				ctx.stroke();
			};
		};
		if($("#OrderDialogConstructFramugDownGridCh").is(":checked"))
		{
			ctx.lineWidth=1;
			ctx.strokeStyle="red";
			for(var i=0;i<15;i+=5)
			{
				ctx.moveTo(60, 90+i);
				ctx.lineTo(310-FramugaXMinus, 90+i);
				ctx.stroke();
			};
		};
	};
		
	//Рабочая створка -> Петли
	if($("#OrderDialogConstructWorkSectionPetlya").val()!="" & $("#OrderDialogConstructWorkSectionPetlya").val()>0)
	{
		var xPetlya=10;
		if($("#OrderDialogConstructWorkSectionOpen").val()=="Прав." & $("#OrderDialogConstructStvorkaS").val()=="") xPetlya=210;
		if($("#OrderDialogConstructWorkSectionOpen").val()=="Прав." & $("#OrderDialogConstructStvorkaS").val()!="") xPetlya=360;
		var sPetlya=350/(parseInt($("#OrderDialogConstructWorkSectionPetlya").val())+1);
		var c=sPetlya;
		for(var i=1; i<=parseInt($("#OrderDialogConstructWorkSectionPetlya").val());i++)
		{
			CanvasLineCross(xPetlya,c+110,"OrderDialogConstructCanvas");
			c+=sPetlya;
		};
	};
		
	//Вторая створка -> Петли
	if($("#OrderDialogConstructStvorkaS").val()!="" & $("#OrderDialogConstructStvorkaPetlya").val()!="" & $("#OrderDialogConstructStvorkaPetlya").val()>0)
	{
		var xPetlya=360;
		if($("#OrderDialogConstructWorkSectionOpen").val()=="Прав.") xPetlya=10;
		var sPetlya=350/(parseInt($("#OrderDialogConstructStvorkaPetlya").val())+1);
		var c=sPetlya;
		for(var i=1; i<=parseInt($("#OrderDialogConstructStvorkaPetlya").val());i++)
		{
			CanvasLineCross(xPetlya,c+110,"OrderDialogConstructCanvas");
			c+=sPetlya;
		};
	};
		
	//Размеры
	var Tx=350/2; if($("#OrderDialogConstructStvorkaS").val()=="") Tx=200/2;
	ctx.fillText($("#OrderDialogConstructWorkSectionW").val(), Tx+10,470);//Ширина всего
	if($("#OrderDialogConstructStvorkaS").val()!="") //Ширина второй створки
	{
		if($("#OrderDialogConstructWorkSectionOpen").val()=="Лев." ) ctx.fillText($("#OrderDialogConstructStvorkaS").val(), 60,455);
		if($("#OrderDialogConstructWorkSectionOpen").val()=="Прав.") ctx.fillText($("#OrderDialogConstructStvorkaS").val(), 260,455);
	};
	var Ty=350/2+110; if( $("#OrderDialogConstructMenu3 div").is(':visible') ) Ty=450/2+10;
	ctx.fillText($("#OrderDialogConstructWorkSectionH").val(),Tx*2+15,Ty);
	if($("#OrderDialogConstructMenu3 div").is(":visible"))//Размер высоты фрамуги
		ctx.fillText($("#OrderDialogConstructFramugH").val(),335-FramugaXMinus,70);
}
	
function CanvasLineCross(x,y, idElement)
{
	var canvas=document.getElementById(idElement)
	var obCanvas=canvas.getContext("2d");
	var x1=x-7.5; var y1=y-7.5;
	obCanvas.beginPath();
	obCanvas.lineWidth = 1;
	obCanvas.strokeStyle = 'red';
	obCanvas.moveTo(x1, y1);
	obCanvas.lineTo(x1+15, y1+15);
	obCanvas.stroke();
		
	obCanvas.beginPath();
	obCanvas.lineWidth = 1;
	obCanvas.strokeStyle = 'red';
	obCanvas.moveTo(x1, y1+15);
	obCanvas.lineTo(x1+15, y1);
	obCanvas.stroke();
}

//Функция рисования усиления
function CanvasReinforcementRect(x0,y0,w,h, idElement)
{
	var x1=x0+w; var y1=y0+h;
	var canvas=document.getElementById(idElement);
	var obCanvas=canvas.getContext("2d");
	obCanvas.lineWidth = 1;
	obCanvas.strokeStyle = 'gray';
	var S=Math.floor((y1-y0)/6);
	for(var Sy=y1-S; Sy>=( y1 - Math.floor( (y1-y0) / 2 ) ); Sy-=S)
	{
		obCanvas.beginPath();
		obCanvas.moveTo(x0, Sy);
		obCanvas.lineTo(x1, Sy);
		obCanvas.stroke();
	};
}

//Функция рисования решетки заданной области
function CanvasGridRect(x0,y0,w,h, idElement)
{
	var x1=x0+w; var y1=y0+h;
	var canvas=document.getElementById(idElement);
	var obCanvas=canvas.getContext("2d");
	obCanvas.lineWidth = 1;
	obCanvas.strokeStyle = 'gray';
	for(var Sx=x0+20; Sx<x1;Sx+=20)
	{
		obCanvas.beginPath();
		obCanvas.moveTo(Sx, y0);
		obCanvas.lineTo(Sx, y1);
		obCanvas.stroke();
	};
	for(var Sy=y0+20; Sy<y1;Sy+=20)
	{
		obCanvas.beginPath();
		obCanvas.moveTo(x0, Sy);
		obCanvas.lineTo(x1, Sy);
		obCanvas.stroke();
	};
}
	
function CanvasGlassRect(x0,y0,w,h, idElement)
{
	var x1=x0+w; var y1=y0+h;
	var canvas=document.getElementById(idElement);
	var obCanvas=canvas.getContext("2d");
	obCanvas.lineWidth = 1;
	obCanvas.strokeStyle = 'gray';
	for( var Sx=x0+20;Sx<=x1;Sx+=20)
	{
		obCanvas.beginPath();
		obCanvas.moveTo(Sx-20, y0);
		obCanvas.lineTo(Sx, y1);
		obCanvas.stroke();
	};
}
	
function CanvasLine(x0,y0,x1,y1, idElement)
{
	var c=document.getElementById(idElement);
	var o=c.getContext("2d");
	o.lineWidth=2;
	o.strokeStyle="blue";
	o.beginPath();
	o.moveTo(x0,y0);
	o.lineTo(x1,y1);
	o.stroke();
}
//-------------------------------------------/ Конструктор--------------------------------------------------------------------

//Определение максимального номера Штильды
function OrderSelectMaxShtild(el)
{
	if($(el).val()=="")
	{
		$(el).css("background","url(images/loader.gif) no-repeat");
		$(el).css("background-size","contain");
		$.post(
			'orders/order.php',
			{"method":"SelectShtildMax"},
			function(data)
			{
				var LengthStr=data.length;
				var ShtildNum=0;
				for(var i=0;i<400;i++)
					if($("#OrderDialogTableTDShtild"+i).text()!="")
						if(parseInt(data)-1<=parseInt($("#OrderDialogTableTDShtild"+i).text()))
						{
							ShtildNum+=parseInt($("#OrderDialogTableTDCount"+i).text());
						};
				$(el).val(parseInt(data)+ShtildNum);
				while(data.length>$(el).val().length)
					$(el).val("0"+$(el).val());
				$(el).css("background","none");
				$(el).css("border","1px solid gray");
			}
		);
	};
}

//-------------------------Зарплата------------------------------------------------------
function OrderCostDialogLoad(el)
{
	var elTR=$(el).parent().parent();
	//if(elTR.attr("status")=="Load" & elTR.attr("class")!="Complite") elTR.attr("status","Edit");
	$("#OrderCostDialogID").text(elTR.attr("id"));
	$("#OrderCostDialogHeaderZakazNum").text($("#orderDialogInputBlank").val());
	$("#OrderCostDialogHeaderDorrPos").text(elTR.find("td").eq(1).text());
	
	$("#OrderCostDialogLaser").val(elTR.find("td[type=Cost] span CostLaser").text());
	$("#OrderCostDialogSgibka").val(elTR.find("td[type=Cost] span CostSgibka").text());
	$("#OrderCostDialogSvarka").val(elTR.find("td[type=Cost] span CostSvarka").text());
	$("#OrderCostDialogSborka").val(elTR.find("td[type=Cost] span CostSborka").text());
	$("#OrderCostDialogColor").val(elTR.find("td[type=Cost] span CostColor").text());
	$("#OrderCostDialogUpak").val(elTR.find("td[type=Cost] span CostUpak").text());
	$("#OrderCostDialogShpt").val(elTR.find("td[type=Cost] span CostShpt").text());
	$("#OrderCostDialogFrame").val(0);
	$("#OrderCostDialogFrame").hide();
	$("#OrderCostDialogFrame").parent().find("a").hide();
	$("#OrderCostDialogMdf").val(0);
	$("#OrderCostDialogMdf").hide();
	$("#OrderCostDialogMdf").parent().find("a").hide();
	$("#OrderCostDialogSborkaMdf").val(0);
	$("#OrderCostDialogSborkaMdf").hide();
	$("#OrderCostDialogSborkaMdf").parent().find("a").hide();
	//Если окна нет, то не отображаем поле и значение =0
	if((elTR.find("td[type=Construct] span WorkWindowCh").text()=="true" & elTR.find("td[type=Construct] span WorkWindowNoFrame").text()!="true") || (elTR.find("td[type=Construct] span StvorkaWindowCh").text()=="true" & elTR.find("td[type=Construct] span StvorkaWindowNoFrame").text()!="true") || (elTR.find("td[type=Construct] span FramugaWindowCh").text()=="true" & elTR.find("td[type=Construct] span FramugaWindowNoFrame").text()!="true")  )
	{	
		$("#OrderCostDialogFrame").val(elTR.find("td[type=Cost] span CostFrame").text());
		$("#OrderCostDialogFrame").show();
		$("#OrderCostDialogFrame").parent().find("a").show();
	};
	//Если указа тип двери двери - МДФ, тогда отобразим поле
	if(elTR.find("td[type=Name]").text().indexOf("МДФ")>-1)
	{	
		$("#OrderCostDialogMdf").val(elTR.find("td[type=Cost] span CostMdf").text());
		$("#OrderCostDialogMdf").show();
		$("#OrderCostDialogMdf").parent().find("a").show();
		$("#OrderCostDialogSborkaMdf").val(elTR.find("td[type=Cost] span CostSborkaMdf").text());
		$("#OrderCostDialogSborkaMdf").show();
		$("#OrderCostDialogSborkaMdf").parent().find("a").show();
	};
	$("#OrderCostDialog").dialog("open");
}
function OrderCostDialogSave(id)
{
	var elTR=$("#"+id);
	if(elTR.attr("status")=="Load"/* & $("#"+id).attr("class")!="Complite"*/) elTR.attr("status","Edit");
	var elCost=elTR.find("td[type=Cost] span");
	elCost.find("CostLaser").text($("#OrderCostDialogLaser").val());
	elCost.find("CostSgibka").text($("#OrderCostDialogSgibka").val());
	elCost.find("CostSvarka").text($("#OrderCostDialogSvarka").val());
	elCost.find("CostFrame").text($("#OrderCostDialogFrame").val());
	elCost.find("CostMdf").text($("#OrderCostDialogMdf").val());
	elCost.find("CostSborka").text($("#OrderCostDialogSborka").val());
	elCost.find("CostSborkaMdf").text($("#OrderCostDialogSborkaMdf").val());
	elCost.find("CostColor").text($("#OrderCostDialogColor").val());
	elCost.find("CostUpak").text($("#OrderCostDialogUpak").val());
	elCost.find("CostShpt").text($("#OrderCostDialogShpt").val());
	$("#OrderCostDialog").dialog("close");
	//В случае не заполнения полей или установки значения =0, красим кнопку стоимость
	elTR.find("td[type=Cost] button").css("border","2px solid green");
	//elTR.find("td[type=Cost] button").parent().find("button").css("border",$("#ButtonDefailtStyle").css("border"));
	/*if($("#OrderCostDialogLaser").val()=="0" || $("#OrderCostDialogLaser").val()=="")
		$("#"+id+" td[type=Cost] button").parent().find("button").css("border","solid 2px red");
	if($("#").val()=="0" || $("#OrderCostDialogSgibka").val()=="")
		$("#"+id+" td[type=Cost] button").parent().find("button").css("border","solid 2px red");*/
	
	if($("#OrderCostDialogSvarka").val()=="0" || $("#OrderCostDialogSvarka").val()=="")
		elTR.find("td[type=Cost] button").parent().find("button").css("border","solid 2px red");
	
	if((elTR.find("td[type=Construct] span WorkWindowCh").text()=="true" & elTR.find("td[type=Construct] span WorkWindowNoFrame").text()!="true") || (elTR.find("td[type=Construct] span StvorkaWindowCh").text()=="true" & elTR.find("td[type=Construct] span StvorkaWindowNoFrame").text()!="true") || (elTR.find("td[type=Construct] span FramugaWindowCh").text()=="true" & elTR.find("td[type=Construct] span FramugaWindowNoFrame").text()!="true")  )
		if($("#OrderCostDialogFrame").val()=="0" || $("#OrderCostDialogFrame").val()=="")
			elTR.find("td[type=Cost] button").parent().find("button").css("border","solid 2px red");
	if(elTR.find("tr[type=Name]").text().indexOf("МДФ")>-1)
		if($("#OrderCostDialogSborkaMdf").val()=="0")
			elTR.find("td[type=Cost] button").parent().find("button").css("border","solid 2px red");

	if($("#OrderCostDialogSborka").val()=="0" || $("#OrderCostDialogSborka").val()=="")
		elTR.find("td[type=Cost] button").parent().find("button").css("border","solid 2px red");
	if($("#OrderCostDialogColor").val()=="0" || $("#OrderCostDialogColor").val()=="")
		elTR.find("td[type=Cost] button").parent().find("button").css("border","solid 2px red");
	if($("#OrderCostDialogUpak").val()=="0" || $("#OrderCostDialogUpak").val()=="")
		elTR.find("td[type=Cost] button").parent().find("button").css("border","solid 2px red");
	
	/*
	if($("#OrderCostDialogShpt").val()=="0" || $("#OrderCostDialogShpt").val()=="")
		$("#"+id+" td[type=Cost] button").parent().find("button").css("border","solid 2px red");
	*/
}

//--------------------------Изменение стоимости работ---------------------------------------------
function OrderEditWorkSumDialogOpen()
{
	$("#OrderEditWorkSumDialogInpOrderNum").val("");
	$("#OrderEditWorkSumDialogBugs").text("");
	$("#OrderEditWorkSumDialogTable").find("tr").remove();
	$("#OrderEditWorkSumDialog").dialog("open");
}
function OrderEditWorkSumLoad()
{
	$("#OrderEditWorkSumDialogTable").find("tr").remove();
	var OrderNum=parseInt($("#OrderEditWorkSumDialogInpOrderNum").val());
	$.post(
		'orders/order.php',
		{"method":"EditWorkSumLoad", "Blank":$("#OrderEditWorkSumDialogInpOrderNum").val()},
		function(data)
		{
			var o=jQuery.parseJSON(data); var i=0;
			while(o[i]!=null)
			{
				$("#OrderEditWorkSumDialogTable").append(
					"<tr idDoor="+o[i].id+">"+
						"<td>"+o[i].NumPP+"</td>"+
						"<td>"+o[i].name+"</td>"+
						"<td>"+o[i].Size+"</td>"+
						"<td>"+o[i].Count+"</td>"+
						"<td><input style='width:50px' onchange='OrderEditWorkSumInpChg(this)' SumOld='"+o[i].CostLaser+"' value='"+o[i].CostLaser+"'></td>"+
						"<td><input style='width:50px' onchange='OrderEditWorkSumInpChg(this)' SumOld='"+o[i].CostSgibka+"' value='"+o[i].CostSgibka+"'></td>"+
						"<td><input style='width:50px' onchange='OrderEditWorkSumInpChg(this)' SumOld='"+o[i].CostSvarka+"' value='"+o[i].CostSvarka+"'></td>"+
						"<td>"+(o[i].FrameComplite==1? "<input style='width:50px' onchange='OrderEditWorkSumInpChg(this)' SumOld='"+o[i].CostFrame+"' value='"+o[i].CostFrame+"'>" : "")+"</td>"+
						"<td>"+(o[i].name.indexOf("МДФ")>-1? "<input style='width:50px' onchange='OrderEditWorkSumInpChg(this)' SumOld='"+o[i].CostMdf+"' value='"+o[i].CostMdf+"'>" : "")+"</td>"+
						"<td><input style='width:50px' onchange='OrderEditWorkSumInpChg(this)' SumOld='"+o[i].CostSborka+"' value='"+o[i].CostSborka+"'></td>"+
						"<td>"+(o[i].name.indexOf("МДФ")>-1? "<input style='width:50px' onchange='OrderEditWorkSumInpChg(this)' SumOld='"+o[i].CostSborkaMdf+"' value='"+o[i].CostSborkaMdf+"'>" : "")+"</td>"+
						"<td><input style='width:50px' onchange='OrderEditWorkSumInpChg(this)' SumOld='"+o[i].CostColor+"' value='"+o[i].CostColor+"'></td>"+
						"<td><input  style='width:50px' onchange='OrderEditWorkSumInpChg(this)' SumOld='"+o[i].CostUpak+"' value='"+o[i].CostUpak+"'></td>"+
						"<td><input style='width:50px' onchange='OrderEditWorkSumInpChg(this)' SumOld='"+o[i].CostShpt+"' value='"+o[i].CostShpt+"'></td>"+
						"<td>"+o[i].CostAll+"</td>"+
						"<td>"+o[i].CostAllOnCount+"</td>"
				);
				i++;
			};
		}
	);
}
function OrderEditWorkSumInpChg(el){
	var elTR=$(el).parent().parent();
	var SumAll=0;
	for(var i=4;i<=11;i++)
		if(elTR.find("td:eq("+i+") input").length>0)
			SumAll+=parseFloat(elTR.find("td:eq("+i+") input").val());
	elTR.find("td:eq(14)").text(SumAll);
	elTR.find("td:eq(15)").text(SumAll*parseInt(elTR.find("td:eq(3)").text()));
}
function OrderEditWorkSumComplite()
{
	var idDoor=new Array();
	var Pos=new Array();
	var Name=new Array();
	var Size=new Array();
	var Count=new Array();
	var CostLaserOld=new Array();
	var CostLaser=new Array();
	var CostSgibkaOld=new Array();
	var CostSgibka=new Array();
	var CostSvarkaOld=new Array();
	var CostSvarka=new Array();
	var CostFrameOld=new Array();
	var CostFrame=new Array();
	var CostMdfOld=new Array();
	var CostMdf=new Array();
	var CostSborkaOld=new Array();
	var CostSborka=new Array();
	var CostSborkaMdfOld=new Array();
	var CostSborkaMdf=new Array();
	var CostColorOld=new Array();
	var CostColor=new Array();
	var CostUpakOld=new Array();
	var CostUpak=new Array();
	var CostShptOld=new Array();
	var CostShpt=new Array();
	for(var i=0; i<$("#OrderEditWorkSumDialogTable").find("tr").length;i++)
	{
		idDoor[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+")").attr("idDoor");
		Pos[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(0)").text();
		Name[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(1)").text();
		Size[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(2)").text();
		Count[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(3)").text();
		CostLaserOld[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(4) input").attr("SumOld");
		CostLaser[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(4) input").val();
		CostSgibkaOld[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(5) input").attr("SumOld");
		CostSgibka[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(5) input").val();
		CostSvarkaOld[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(6) input").attr("SumOld");
		CostSvarka[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(6) input").val();
		CostFrameOld[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(7) input").attr("SumOld");
		CostFrame[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(7) input").val();
		CostMdfOld[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(8) input").attr("SumOld");
		CostMdf[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(8) input").val();
		CostSborkaOld[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(9) input").attr("SumOld");
		CostSborka[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(9) input").val();
		CostSborkaMdfOld[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(10) input").attr("SumOld");
		CostSborkaMdf[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(10) input").val();
		CostColorOld[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(11) input").attr("SumOld");
		CostColor[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(11) input").val();
		CostUpakOld[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(12) input").attr("SumOld");
		CostUpak[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(12) input").val();
		CostShptOld[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(13) input").attr("SumOld");
		CostShpt[i]=$("#OrderEditWorkSumDialogTable tr:eq("+i+") td:eq(13) input").val();
	};
	$.post(
		'orders/order.php',
		{
			"method":"EditWorkSumCompite",
			"idDoor[]":idDoor,
			"CostLaserOld[]":CostLaserOld,
			"CostLaser[]":CostLaser,
			"CostSgibkaOld[]":CostSgibkaOld,
			"CostSgibka[]":CostSgibka,
			"CostSvarkaOld[]":CostSvarkaOld,
			"CostSvarka[]":CostSvarka,
			"CostFrameOld[]":CostFrameOld,
			"CostFrame[]":CostFrame,
			"CostMdfOld[]":CostMdfOld,
			"CostMdf[]":CostMdf,
			"CostSborkaOld[]":CostSborkaOld,
			"CostSborka[]":CostSborka,
			"CostSborkaMdfOld[]":CostSborkaMdfOld,
			"CostSborkaMdf[]":CostSborkaMdf,
			"CostColorOld[]":CostColorOld,
			"CostColor[]":CostColor,
			"CostUpakOld[]":CostUpakOld,
			"CostUpak[]":CostUpak,
			"CostShptOld[]":CostShptOld,
			"CostShpt[]":CostShpt
		},
		function(data)
		{
			if(data=="") {
				if(confirm("Вывести на печать?")){
					$.post(
						'orders/order.php',
						{
							"method":"EditWorkSumCompitePrint",
							"OrderNum":$("#OrderEditWorkSumDialogInpOrderNum").val(),
							"Pos[]":Pos,
							"Name[]":Name,
							"Size[]":Size,
							"Count[]":Count,
							"CostLaserOld[]":CostLaserOld,
							"CostLaser[]":CostLaser,
							"CostSgibkaOld[]":CostSgibkaOld,
							"CostSgibka[]":CostSgibka,
							"CostSvarkaOld[]":CostSvarkaOld,
							"CostSvarka[]":CostSvarka,
							"CostFrameOld[]":CostFrameOld,
							"CostFrame[]":CostFrame,
							"CostMdfOld[]":CostMdfOld,
							"CostMdf[]":CostMdf,
							"CostSborkaOld[]":CostSborkaOld,
							"CostSborka[]":CostSborka,
							"CostSborkaMdfOld[]":CostSborkaMdfOld,
							"CostSborkaMdf[]":CostSborkaMdf,
							"CostColorOld[]":CostColorOld,
							"CostColor[]":CostColor,
							"CostUpakOld[]":CostUpakOld,
							"CostUpak[]":CostUpak,
							"CostShptOld[]":CostShptOld,
							"CostShpt[]":CostShpt
						},
						function (data){
							if(data=="") {
								window.open("orders/CostChange.pdf",'_blank');
								$("#OrderEditWorkSumDialog").dialog("close");
							} else alert(data);
						}
					);
				}
				else
					$("#OrderEditWorkSumDialog").dialog("close");
			} else {$("#OrderEditWorkSumDialogBugs").text(data); console.log(data);};
		}
	);
}
//-------------------------------Импорт-----------------------------
function OrderImportDialogSave()
{	
	var arrStatus=[];
	var arrWorkStatus=[];
	var arrNum=[];
	var arrName=[];
	var arrH=[];
	var arrW=[];
	var arrS=[];
	var arrOpen=[];
	var arrNalichnik=[];
	var arrDovod=[];
	var arrRAL=[];
	var arrNote=[];
	var arrMark=[];
	var arrCount=[];
	var arrShtild=[];
	//--Рабочая створка
	var arrConstructWorkPetlya=[];
	//Окно
	var arrConstructWorkWindowCh=[];
	var arrConstructWorkWindowNoFrame=[];
	var arrConstructWorkWindowH=[];
	var arrConstructWorkWindowW=[];
	var arrConstructWorkWindowGain=[];
	var arrConstructWorkWindowGlass=[];
	var arrConstructWorkWindowGlassType=[];
	var arrConstructWorkWindowGrid=[];
	//Окно 1
	var arrConstructWorkWindowCh1=[];
	var arrConstructWorkWindowNoFrame1=[];
	var arrConstructWorkWindowH1=[];
	var arrConstructWorkWindowW1=[];
	var arrConstructWorkWindowGain1=[];
	var arrConstructWorkWindowGlass1=[];
	var arrConstructWorkWindowGlassType1=[];
	var arrConstructWorkWindowGrid1=[];
	//Окно 2
	var arrConstructWorkWindowCh2=[];
	var arrConstructWorkWindowNoFrame2=[];
	var arrConstructWorkWindowH2=[];
	var arrConstructWorkWindowW2=[];
	var arrConstructWorkWindowGain2=[];
	var arrConstructWorkWindowGlass2=[];
	var arrConstructWorkWindowGlassType2=[];
	var arrConstructWorkWindowGrid2=[];
	//Вент решетки
	var arrConstructWorkUpGridCh=[];
	var arrConstructWorkDownGridCh=[];
	//--Вторая створка
	var arrConstructStvorkaCh=[];
	var arrConstructStvorkaPetlya=[];
	//Окно
	var arrConstructStvorkaWindowCh=[];
	var arrConstructStvorkaWindowNoFrame=[];
	var arrConstructStvorkaWindowH=[];
	var arrConstructStvorkaWindowW=[];
	var arrConstructStvorkaWindowGain=[];
	var arrConstructStvorkaWindowGlass=[];
	var arrConstructStvorkaWindowGlassType=[];
	var arrConstructStvorkaWindowGrid=[];
	//Окно 1
	var arrConstructStvorkaWindowCh1=[];
	var arrConstructStvorkaWindowNoFrame1=[];
	var arrConstructStvorkaWindowH1=[];
	var arrConstructStvorkaWindowW1=[];
	var arrConstructStvorkaWindowGain1=[];
	var arrConstructStvorkaWindowGlass1=[];
	var arrConstructStvorkaWindowGlassType1=[];
	var arrConstructStvorkaWindowGrid1=[];
	//Окно 2
	var arrConstructStvorkaWindowCh2=[];
	var arrConstructStvorkaWindowNoFrame2=[];
	var arrConstructStvorkaWindowH2=[];
	var arrConstructStvorkaWindowW2=[];
	var arrConstructStvorkaWindowGain2=[];
	var arrConstructStvorkaWindowGlass2=[];
	var arrConstructStvorkaWindowGlassType2=[];
	var arrConstructStvorkaWindowGrid2=[];
	//Вент решетки
	var arrConstructStvorkaUpGridCh=[];
	var arrConstructStvorkaDownGridCh=[];
	//--Фрамуга
	var arrConstructFramugaCh=[];
	var arrConstructFramugaH=[];
	//Окно
	var arrConstructFramugaWindowCh=[];
	var arrConstructFramugaWindowNoFrame=[];
	var arrConstructFramugaWindowH=[];
	var arrConstructFramugaWindowW=[];
	var arrConstructFramugaWindowGain=[];
	var arrConstructFramugaWindowGlass=[];
	var arrConstructFramugaWindowGlassType=[];
	var arrConstructFramugaWindowGrid=[];
	//Вент решетки
	var arrConstructFramugaUpGridCh=[];
	var arrConstructFramugaDownGridCh=[];
	//Дополнительно
	var Antipanik=[];
	var Otboynik=[];
	var Wicket=[];
	var BoxLock=[];
	var Otvetka=[];
	var Isolation=[];
	//--Зарплата
	var arrCostLaser=[];
	var arrCostSgibka=[];
	var arrCostSvarka=[];
	var arrCostFrame=[];
	var arrCostMdf=[];
	var arrCostSborka=[];
	var arrCostSborkaMdf=[];
	var arrCostColor=[];
	var arrCostUpak=[];
	var arrCostShpt=[];
	
	var posArr=new Array();
	posArr["posPP"]=-1;
	posArr["posName"] =-1;
	posArr["posH"] =-1;
	posArr["posW"] =-1;
	posArr["posS"] =-1;
	posArr["posOpen"] =-1;
	posArr["posNalichnik"] =-1;
	posArr["posDovod"] =-1;
	posArr["posRAL"] =-1;
	posArr["posNote"] =-1;
	posArr["posMark"] =-1;
	posArr["posCount"] =-1;
	posArr["posShtild"] =-1;
	posArr["posPetlyaWrk"] =-1;
	posArr["posPetlyaStv"] =-1;
	posArr["posWindowWrk"] =-1;
	posArr["posWindowStv"] =-1;
	posArr["posWorkUpGrid"] =-1;
	posArr["posWorkDownGrid"] =-1;
	posArr["posStvUpGrid"] =-1;
	posArr["posStvDownGrid"] =-1;
	posArr["posFramugaUpGrid"] =-1;
	posArr["posFramugaDownGrid"] =-1;
	posArr["posFramuga"] =-1;
	posArr["posFramugaH"] =-1;
	for(var i=0; i<$("#OrderImportTableTHEAD tr:eq(0)").find("td").length;i++)
		if($("#OrderImportTableTHEAD tr:eq(0) td:eq("+i.toString()+")").is(":visible"))
		switch($("#OrderImportTableTHEAD tr:eq(0) td:eq("+i.toString()+") select").val())
		{
			case "№ п/п": posArr["posPP"]=i; break;
			case "Наименование": posArr["posName"]=i; break;
			case "Высота": posArr["posH"] =i; break;
			case "Ширина": posArr["posW"] =i; break;
			case "Рабочая створка": posArr["posS"] =i; break;
			case "Открывание": posArr["posOpen"] =i; break;
			case "Наличник": posArr["posNalichnik"] =i; break;
			case "Доводчик":posArr["posDovod"] =i; break;
			case "RAL окрас": posArr["posRAL"] =i; break;
			case "Примечание": posArr["posNote"] =i; break;
			case "Маркировка": posArr["posMark"] =i; break;
			case "Кол-во": posArr["posCount"] =i; break;
			case "№ шильды": posArr["posShtild"] =i; break;
			case "Навес раб. ств.": posArr["posPetlyaWrk"] =i; break;
			case "Навес вторая ств.": posArr["posPetlyaStv"] =i; break;
			case "Окно раб. ств.": posArr["posWindowWrk"] =i; break;
			case "Окно вторая ств.": posArr["posWindowStv"] =i; break;
			case "Решетка раб. ств.(верх)": posArr["posWorkUpGrid"] =i; break;
			case "Решетка раб. ств.(низ)": posArr["posWorkDownGrid"] =i; break;
			case "Решетка вторая ств.(верх)": posArr["posStvUpGrid"] =i; break;
			case "Решетка вторая ств.(низ)": posArr["posStvDownGrid"] =i; break;
			case "Решетка фрамуга (верх)": posArr["posFramugaUpGrid"] =i; break;
			case "Решетка фрамуга (низ)": posArr["posFramugaDownGrid"] =i; break;
			case "Фрамуга": posArr["posFramuga"] =i; break;
			case "Высота фрамуги": posArr["posFramugaH"] =i; break;
		};
	//Теперь получим данные по остальным ячейкам
	var r=0;
	for(var i=0;i<$("#OrderImportTableTBODY").find("tr").length;i++)
		if($("#OrderImportTableTBODY").find("tr:eq("+i+")").is(":visible"))
		{
			arrStatus[r]="Add";
			arrWorkStatus[r]="Start";
			arrNum[r]=""; if(posArr["posPP"]!=-1) arrNum[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posPP"]+") input").val();
			arrName[r]=""; if(posArr["posName"]!=-1) arrName[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posName"]+") select").val();
			arrH[r]=""; if(posArr["posH"]!=-1) arrH[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posH"]+") input").val();
			arrW[r]=""; if(posArr["posW"]!=-1) arrW[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posW"]+") input").val();
			arrS[r]=""; 
				if(posArr["posS"]!=-1)
					if($("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posS"]+") input").val().toUpperCase().indexOf("РАВН")>-1)
					{
						arrS[r]="Равн.";
					}
					else
						arrS[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posS"]+") input").val();
			arrOpen[r]="Прав."; if(posArr["posOpen"]!=-1) arrOpen[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posOpen"]+") select").val();
			arrNalichnik[r]="Нет"; if(posArr["posNalichnik"]!=-1) arrNalichnik[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posNalichnik"]+") select").val();
			arrDovod[r]="нет"; if(posArr["posDovod"]!=-1) arrDovod[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posDovod"]+") select").val();
			arrRAL[r]=""; if(posArr["posRAL"]!=-1) arrRAL[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posRAL"]+") input").val();
			arrNote[r]=""; if(posArr["posNote"]!=-1) arrNote[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posNote"]+") textarea").val();
			arrMark[r]=""; if(posArr["posMark"]!=-1) arrMark[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posMark"]+") input").val();
			arrMark[r]=arrMark[r]!==undefined?arrMark[r]:"";
			arrCount[r]="1"; if(posArr["posCount"]!=-1) arrCount[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posCount"]+") input").val();
			arrShtild[r]=""; if(posArr["posShtild"]!=-1) arrShtild[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posShtild"]+") input").val();
			arrShtild[r]=arrShtild[r]!==undefined?arrShtild[r]:"";
			//--Рабочая створка
			arrConstructWorkPetlya[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posPetlyaWrk"]+") input").val();
			//Окно
			var WindowWorkChInput=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posWindowWrk"]+") input").val();
			arrConstructWorkWindowCh[r]=(WindowWorkChInput=="1" || WindowWorkChInput=="2" || WindowWorkChInput=="3" || WindowWorkChInput=="2" || WindowWorkChInput=="3" || WindowWorkChInput.toLowerCase()=="да") ? "true" : "false";
			if(arrConstructWorkWindowCh[r]===undefined) arrConstructWorkWindowCh[r]="false";
			arrConstructWorkWindowNoFrame[r]=ParamGetValue("NoFrameFromImport")==1?"true":"false";
			arrConstructWorkWindowH[r]="null";
			arrConstructWorkWindowW[r]="null";
			arrConstructWorkWindowGain[r]="false";
			arrConstructWorkWindowGlass[r]="false";
			arrConstructWorkWindowGlassType[r]="";
			arrConstructWorkWindowGrid[r]="false";
			//Окно 1
			arrConstructWorkWindowCh1[r]=(WindowWorkChInput=="2" || WindowWorkChInput=="3") ? "true" : "false";
			arrConstructWorkWindowNoFrame1[r]="false";
			arrConstructWorkWindowH1[r]="null";
			arrConstructWorkWindowW1[r]="null";
			arrConstructWorkWindowGain1[r]="false";
			arrConstructWorkWindowGlass1[r]="false";
			arrConstructWorkWindowGlassType1[r]="";
			arrConstructWorkWindowGrid1[r]="false";
			//Окно 2
			arrConstructWorkWindowCh2[r]=(WindowWorkChInput=="3") ? "true" : "false";
			arrConstructWorkWindowNoFrame2[r]="false";
			arrConstructWorkWindowH2[r]="null";
			arrConstructWorkWindowW2[r]="null";
			arrConstructWorkWindowGain2[r]="false";
			arrConstructWorkWindowGlass2[r]="false";
			arrConstructWorkWindowGlassType2[r]="";
			arrConstructWorkWindowGrid2[r]="false";
			//Вент решетка
			arrConstructWorkUpGridCh[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posWorkUpGrid"]+") select").val()=="да"? "true" : "false";
			arrConstructWorkDownGridCh[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posWorkDownGrid"]+") select").val()=="да"? "true" : "false";
			//--Вторая створка
			arrConstructStvorkaCh[r]="false";
			arrConstructStvorkaPetlya[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posPetlyaStv"]+") input").val()==""? "null" : $("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posPetlyaStv"]+") input").val();
			//Окно
			var StvorkaWindowInput=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posWindowStv"]+") input").val();
			arrConstructStvorkaWindowCh[r]=(StvorkaWindowInput=="1" || StvorkaWindowInput=="2" || StvorkaWindowInput=="3" || StvorkaWindowInput.toLowerCase()=="да") ? "true" : "false";
			if(arrConstructStvorkaWindowCh[r]===undefined) arrConstructStvorkaWindowCh[r]="NULL";
			arrConstructStvorkaWindowNoFrame[r]=ParamGetValue("NoFrameFromImport")==1?"true":"false";
			arrConstructStvorkaWindowH[r]="null";
			arrConstructStvorkaWindowW[r]="null";
			arrConstructStvorkaWindowGain[r]="false";
			arrConstructStvorkaWindowGlass[r]="false";
			arrConstructStvorkaWindowGlassType[r]="";
			arrConstructStvorkaWindowGrid[r]="false";
			//Окно 1
			arrConstructStvorkaWindowCh1[r]=(StvorkaWindowInput=="2" || StvorkaWindowInput=="3") ? "true" : "false";
			arrConstructStvorkaWindowNoFrame1[r]="false";
			arrConstructStvorkaWindowH1[r]="null";
			arrConstructStvorkaWindowW1[r]="null";
			arrConstructStvorkaWindowGain1[r]="false";
			arrConstructStvorkaWindowGlass1[r]="false";
			arrConstructStvorkaWindowGlassType1[r]="";
			arrConstructStvorkaWindowGrid1[r]="false";
			//Окно 2
			arrConstructStvorkaWindowCh2[r]=(StvorkaWindowInput=="3") ? "true" : "false";
			arrConstructStvorkaWindowNoFrame2[r]="false";
			arrConstructStvorkaWindowH2[r]="null";
			arrConstructStvorkaWindowW2[r]="null";
			arrConstructStvorkaWindowGain2[r]="false";
			arrConstructStvorkaWindowGlass2[r]="false";
			arrConstructStvorkaWindowGlassType2[r]="";
			arrConstructStvorkaWindowGrid2[r]="false";
			//Вент решетки
			arrConstructStvorkaUpGridCh[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posStvUpGrid"]+") select").val()=="да"? "true" : "false";
			arrConstructStvorkaDownGridCh[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posStvDownGrid"]+") select").val()=="да"? "true" : "false";
			//--Фрамуга
			arrConstructFramugaCh[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posFramuga"]+") select").val()=="да"? "true" : "false";
			arrConstructFramugaH[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posFramugaH"]+") input").val()==""? "NULL" : $("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posFramugaH"]+") input").val();
			//Окно
			if(arrConstructFramugaH[r]===undefined) arrConstructFramugaH[r]="NULL";
			arrConstructFramugaWindowCh[r]="false";
			arrConstructFramugaWindowNoFrame[r]=ParamGetValue("NoFrameFromImport")==1?"true":"false";
			arrConstructFramugaWindowH[r]="null";
			arrConstructFramugaWindowW[r]="null";
			arrConstructFramugaWindowGain[r]="false";
			arrConstructFramugaWindowGlass[r]="false";
			arrConstructFramugaWindowGlassType[r]="false";
			arrConstructFramugaWindowGrid[r]="false";
			//Вент решетки
			arrConstructFramugaUpGridCh[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posFramugaUpGrid"]+") select").val()=="да"? "true" : "false";
			arrConstructFramugaDownGridCh[r]=$("#OrderImportTableTBODY tr:eq("+i+") td:eq("+posArr["posFramugaDownGrid"]+") select").val()=="да"? "true" : "false";
			//Дполнительно
			Antipanik[r]="0";
			Otboynik[r]="0";
			Wicket[r]="0";
			BoxLock[r]="0";
			Otvetka[r]="0";
			Isolation[r]="0";
			//--Зарплата
			arrCostLaser[r]="0";
			arrCostSgibka[r]="0";
			arrCostSvarka[r]="0";
			arrCostFrame[r]="0";
			arrCostMdf[r]="0";
			arrCostSborka[r]="0";
			arrCostSborkaMdf[r]="0";
			arrCostColor[r]="0";
			arrCostUpak[r]="0";
			arrCostShpt[r]="0";
			
			r++;
		};
	$.post(
		'orders/order.php',
		{
			'method':"OrderSave",
			'idOrder':"",
			'BlankDate':$('#OrderImportInputBlankDate').val(),
			'Blank':$('#OrderImportInputBlank').val(),
			'Shet':$('#OrderImportInputShet').val(),
			'ShetDate':$('#OrderImportInputShetDate').val(),
			'Srok':$('#OrderImportInputSrok').val(),
			'Zakaz':$('#OrderImportInputZakaz').val(),
			'Contact':$('#OrderImportInputContact').val(),
			'Note':$('#OrderImportInputNote').val(),
			"status":"",
			'OrderDialogTableTDStatusArr[]':arrStatus,
			'OrderDialogTableTDWorkStatusArr[]':arrWorkStatus,
			'OrderDialogTableTDNumArr[]':arrNum,
			'OrderDialogTableTDNameArr[]':arrName,
			'OrderDialogTableTDHArr[]':arrH,
			'OrderDialogTableTDWArr[]':arrW,
			'OrderDialogTableTDSArr[]':arrS,
			'OrderDialogTableTDOpenArr[]':arrOpen,
			'OrderDialogTableTDNalichnikArr[]':arrNalichnik,
			'OrderDialogTableTDDovodArr[]':arrDovod,
			'OrderDialogTableTDRALArr[]':arrRAL,
			'OrderDialogTableTDNoteArr[]':arrNote,
			'OrderDialogTableTDMarkirovkaArr[]':arrMark,
			'OrderDialogTableTDCountArr[]':arrCount,
			'OrderDialogTableTDShtildArr[]':arrShtild,
			//--Рабочая створка
			'OrderDialogTableTDConstructWorkPetlya[]':arrConstructWorkPetlya,
			//Окно
			'OrderDialogTableTDConstructWorkWindowCh[]':arrConstructWorkWindowCh,
			'OrderDialogTableTDConstructWorkWindowNoFrame[]':arrConstructWorkWindowNoFrame,
			'OrderDialogTableTDConstructWorkWindowH[]':arrConstructWorkWindowH,
			'OrderDialogTableTDConstructWorkWindowW[]':arrConstructWorkWindowW,
			'OrderDialogTableTDConstructWorkWindowGain[]':arrConstructWorkWindowGain,
			'OrderDialogTableTDConstructWorkWindowGlass[]':arrConstructWorkWindowGlass,
			'OrderDialogTableTDConstructWorkWindowGlassType[]':arrConstructWorkWindowGlassType,
			'OrderDialogTableTDConstructWorkWindowGrid[]':arrConstructWorkWindowGrid,
			//Окно 1
			'OrderDialogTableTDConstructWorkWindowCh1[]':arrConstructWorkWindowCh1,
			'OrderDialogTableTDConstructWorkWindowNoFrame1[]':arrConstructWorkWindowNoFrame1,
			'OrderDialogTableTDConstructWorkWindowH1[]':arrConstructWorkWindowH1,
			'OrderDialogTableTDConstructWorkWindowW1[]':arrConstructWorkWindowW1,
			'OrderDialogTableTDConstructWorkWindowGain1[]':arrConstructWorkWindowGain1,
			'OrderDialogTableTDConstructWorkWindowGlass1[]':arrConstructWorkWindowGlass1,
			'OrderDialogTableTDConstructWorkWindowGlassType1[]':arrConstructWorkWindowGlassType1,
			'OrderDialogTableTDConstructWorkWindowGrid1[]':arrConstructWorkWindowGrid1,
			//Окно 2
			'OrderDialogTableTDConstructWorkWindowCh2[]':arrConstructWorkWindowCh2,
			'OrderDialogTableTDConstructWorkWindowNoFrame2[]':arrConstructWorkWindowNoFrame2,
			'OrderDialogTableTDConstructWorkWindowH2[]':arrConstructWorkWindowH2,
			'OrderDialogTableTDConstructWorkWindowW2[]':arrConstructWorkWindowW2,
			'OrderDialogTableTDConstructWorkWindowGain2[]':arrConstructWorkWindowGain2,
			'OrderDialogTableTDConstructWorkWindowGlass2[]':arrConstructWorkWindowGlass2,
			'OrderDialogTableTDConstructWorkWindowGlassType2[]':arrConstructWorkWindowGlassType2,
			'OrderDialogTableTDConstructWorkWindowGrid2[]':arrConstructWorkWindowGrid2,
			//Вент решетки
			'OrderDialogTableTDConstructWorkUpGridCh[]':arrConstructWorkUpGridCh,
			'OrderDialogTableTDConstructWorkDownGridCh[]':arrConstructWorkDownGridCh,
			//--Вторая створка
			'OrderDialogTableTDConstructStvorkaCh[]':arrConstructStvorkaCh,
			'OrderDialogTableTDConstructStvorkaPetlya[]':arrConstructStvorkaPetlya,
			//Окно
			'OrderDialogTableTDConstructStvorkaWindowCh[]':arrConstructStvorkaWindowCh,
			'OrderDialogTableTDConstructStvorkaWindowNoFrame[]':arrConstructStvorkaWindowNoFrame,
			'OrderDialogTableTDConstructStvorkaWindowH[]':arrConstructStvorkaWindowH,
			'OrderDialogTableTDConstructStvorkaWindowW[]':arrConstructStvorkaWindowW,
			'OrderDialogTableTDConstructStvorkaWindowGain[]':arrConstructStvorkaWindowGain,
			'OrderDialogTableTDConstructStvorkaWindowGlass[]':arrConstructStvorkaWindowGlass,
			'OrderDialogTableTDConstructStvorkaWindowGlassType[]':arrConstructStvorkaWindowGlassType,
			'OrderDialogTableTDConstructStvorkaWindowGrid[]':arrConstructStvorkaWindowGrid,
			//Окно 1
			'OrderDialogTableTDConstructStvorkaWindowCh1[]':arrConstructStvorkaWindowCh1,
			'OrderDialogTableTDConstructStvorkaWindowNoFrame1[]':arrConstructStvorkaWindowNoFrame1,
			'OrderDialogTableTDConstructStvorkaWindowH1[]':arrConstructStvorkaWindowH1,
			'OrderDialogTableTDConstructStvorkaWindowW1[]':arrConstructStvorkaWindowW1,
			'OrderDialogTableTDConstructStvorkaWindowGain1[]':arrConstructStvorkaWindowGain1,
			'OrderDialogTableTDConstructStvorkaWindowGlass1[]':arrConstructStvorkaWindowGlass1,
			'OrderDialogTableTDConstructStvorkaWindowGlassType1[]':arrConstructStvorkaWindowGlassType1,
			'OrderDialogTableTDConstructStvorkaWindowGrid1[]':arrConstructStvorkaWindowGrid1,
			//Окно 2
			'OrderDialogTableTDConstructStvorkaWindowCh2[]':arrConstructStvorkaWindowCh2,
			'OrderDialogTableTDConstructStvorkaWindowNoFrame2[]':arrConstructStvorkaWindowNoFrame2,
			'OrderDialogTableTDConstructStvorkaWindowH2[]':arrConstructStvorkaWindowH2,
			'OrderDialogTableTDConstructStvorkaWindowW2[]':arrConstructStvorkaWindowW2,
			'OrderDialogTableTDConstructStvorkaWindowGain2[]':arrConstructStvorkaWindowGain2,
			'OrderDialogTableTDConstructStvorkaWindowGlass2[]':arrConstructStvorkaWindowGlass2,
			'OrderDialogTableTDConstructStvorkaWindowGlassType2[]':arrConstructStvorkaWindowGlassType2,
			'OrderDialogTableTDConstructStvorkaWindowGrid2[]':arrConstructStvorkaWindowGrid2,
			//Вент решетки
			'OrderDialogTableTDConstructStvorkaUpGridCh[]':arrConstructStvorkaUpGridCh,
			'OrderDialogTableTDConstructStvorkaDownGridCh[]':arrConstructStvorkaDownGridCh,
			//--Фрамуга
			'OrderDialogTableTDConstructFramugaCh[]':arrConstructFramugaCh,
			'OrderDialogTableTDConstructFramugaH[]':arrConstructFramugaH,
			//Окно
			'OrderDialogTableTDConstructFramugaWindowCh[]':arrConstructFramugaWindowCh,
			'OrderDialogTableTDConstructFramugaWindowNoFrame[]':arrConstructFramugaWindowNoFrame,
			'OrderDialogTableTDConstructFramugaWindowH[]':arrConstructFramugaWindowH,
			'OrderDialogTableTDConstructFramugaWindowW[]':arrConstructFramugaWindowW,
			'OrderDialogTableTDConstructFramugaWindowGain[]':arrConstructFramugaWindowGain,
			'OrderDialogTableTDConstructFramugaWindowGlass[]':arrConstructFramugaWindowGlass,
			'OrderDialogTableTDConstructFramugaWindowGlassType[]':arrConstructFramugaWindowGlassType,
			'OrderDialogTableTDConstructFramugaWindowGrid[]':arrConstructFramugaWindowGrid,
			//Вент решетки
			'OrderDialogTableTDConstructFramugaUpGridCh[]':arrConstructFramugaUpGridCh,
			'OrderDialogTableTDConstructFramugaDownGridCh[]':arrConstructFramugaDownGridCh,
			//Дополнительно
			"Antipanik[]":Antipanik,
			"Otboynik[]":Otboynik,
			"Wicket[]":Wicket,
			"BoxLock[]":BoxLock,
			"Otvetka[]":Otvetka,
			"Isolation[]":Isolation,
			//--Зарплата
			'CostLaser[]':arrCostLaser,
			'CostSgibka[]':arrCostSgibka,
			'CostSvarka[]':arrCostSvarka,
			'CostFrame[]':arrCostFrame,
			'CostMdf[]':arrCostMdf,
			'CostSborka[]':arrCostSborka,
			'CostSborkaMdf[]':arrCostSborkaMdf,
			'CostColor[]':arrCostColor,
			'CostUpak[]':arrCostUpak,
			'CostShpt[]':arrCostShpt
		},
		function(data) {
			if(data=="ok")
				{ 
					$(  "#OrderImportDialog"  ).dialog( "close" ); 
					OrderSelect();
				}
				else 
					$("#OrderImportBugs").html(data);
		}
	);
}

function OrderInportAddCol()
{
	for(var i=0;i<$("#OrderImportTableTBODY").find("tr").length;i++) 
		$("#OrderImportTableTBODY tr:eq("+i+")").append("<td></td>");
	$("#OrderImportTableTHEAD tr:eq(0)").append(
		"<td>"+
			"<select onchange='OrderImportChangeColType("+($("#OrderImportTableTHEAD tr:eq(0)").find("td").length).toString()+")'>"+
				"<option></option>"+
				"<option>№ п/п</option>"+
				"<option>Наименование</option>"+
				"<option>Высота</option>"+
				"<option>Ширина</option>"+
				"<option>Рабочая створка</option>"+
				"<option>Открывание</option>"+
				"<option>Наличник</option>"+
				"<option>Доводчик</option>"+
				"<option>RAL окрас</option>"+
				"<option>Примечание</option>"+
				"<option>Маркировка</option>"+
				"<option>Кол-во</option>"+
				"<option>№ шильды</option>"+
				"<option>Навес раб. ств.</option>"+
				"<option>Навес вторая ств.</option>"+
				"<option>Окно раб. ств.</option>"+
				"<option>Окно вторая ств.</option>"+
				"<option>Фрамуга</option>"+
				"<option>Высота фрамуги</option>"+
				"<option>Решетка раб. ств.(верх)</option>"+
				"<option>Решетка раб. ств.(низ)</option>"+
				"<option>Решетка вторая ств.(верх)</option>"+
				"<option>Решетка вторая ств.(низ)</option>"+
				"<option>Решетка фрамуга (верх)</option>"+
				"<option>Решетка фрамуга (низ)</option>"+
			"</select>"+
		"</td>"
	);
	$("#OrderImportTableTHEAD tr:eq(1)").append("<td onclick='OrderImportColDel("+($("#OrderImportTableTHEAD tr:eq(0)").find("td").length-1).toString()+")'>[ удалить ]</td>");
	//Вывод в историю
	OrderImportHystoryAdd("ColAdd",$("#OrderImportTableTHEAD tr:eq(0)").find("td").length-1);
}

//История
var OrderImportHystoryList=new Array();
var OrderImportHystoryPos=0;
function OrderImportHystoryAdd(ActionS, pos)
{
	$("#OrderImportInpBack").button( "option", "disabled", false );
	for (var i=OrderImportHystoryPos;i<OrderImportHystoryList.length;i++)
		delete OrderImportHystoryList[i];
	OrderImportHystoryList[OrderImportHystoryPos]={"Action":ActionS, "Pos":pos};
	OrderImportHystoryPos++;
	//Проверим активность кнопок
	
	if(OrderImportHystoryList.length==OrderImportHystoryPos)
		$("#OrderImportInpNext").button( "option", "disabled", true);
}
function OrderImportHystoryBack()
{
	var pos=OrderImportHystoryList[OrderImportHystoryPos-1].Pos;
	switch(OrderImportHystoryList[OrderImportHystoryPos-1].Action)
	{
		case "RowDel":
			$("#OrderImportTableTBODY").find("tr:eq("+pos.toString()+")").show();
			OrderImportHystoryPos--;
		break;
		case "ColDel":
			$("#OrderImportTableTHEAD").find("tr").find("td:eq("+pos+")").show();
			$("#OrderImportTableTBODY").find("tr").find("td:eq("+pos+")").show();
			OrderImportHystoryPos--;
		break;
		case "ColAdd":
			$("#OrderImportTableTHEAD").find("tr").find("td:eq("+pos+")").hide();
			$("#OrderImportTableTBODY").find("tr").find("td:eq("+pos+")").hide();
			OrderImportHystoryPos--;
		break;
	};
	if(OrderImportHystoryPos==0) $("#OrderImportInpBack").button( "option", "disabled", true );
	$("#OrderImportInpNext").button( "option", "disabled", false );
}
function OrderImportHystoryNext()
{
	var pos=OrderImportHystoryList[OrderImportHystoryPos].Pos;
	switch(OrderImportHystoryList[OrderImportHystoryPos].Action)
	{
		case "RowDel":
			$("#OrderImportTableTBODY").find("tr:eq("+pos.toString()+")").hide();
			OrderImportHystoryPos++;
		break;
		case "ColDel":
			$("#OrderImportTableTHEAD").find("tr").find("td:eq("+pos+")").hide();
			$("#OrderImportTableTBODY").find("tr").find("td:eq("+pos+")").hide();
			OrderImportHystoryPos++;
		break;
		case "ColAdd":
			$("#OrderImportTableTHEAD").find("tr").find("td:eq("+pos+")").show();
			$("#OrderImportTableTBODY").find("tr").find("td:eq("+pos+")").show();
			OrderImportHystoryPos++;
		break;
	};
	if(OrderImportHystoryList.length==OrderImportHystoryPos)
		$("#OrderImportInpNext").button( "option", "disabled", true);
	$("#OrderImportInpBack").button( "option", "disabled", false );
}

//-------------- Спецификация -----------------------------
var spe_DoorTR;
function OrederSpecificLoad(elTD)
{
	$("#OrderSpecificTable").find("tr").remove();
	var elTR=$(elTD).parent().parent();
    spe_DoorTR=elTR;
	$("#OrderSpecificidDoor").val(elTR.attr("idDoor"));
	$.post(
		"Orders/Specifications.php",
		{
			"Action":"Load",
			"idDoor":elTR.attr("idDoor")
		},
		function(o){
			var i=-1;
			var idCommonOld=-1;
			while (o[++i]!=null) {
                if (idCommonOld != o[i].idCommon) {
                    $("#OrderSpecificTable").append(
                        "<tr idCommon='" + o[i].idCommon + "' idGroup='"+o[i].idGroup+"'>" +
							"<td Type='GroupName'>" + o[i].GroupName + "</td>" +
							"<td Type='CommonCount'><input oninput='spe_CountChange(this)' value='" + o[i].Count + "'></td>" +
							"<td><button onclick='spe_GoodSelectLoad(this)'>Добавить</button></td>" +
							"<td Type='BtnSave'><button onclick='spe_CountChanged(this)'>Сохранить</button></td>"+
                        	"<td Type='BtnDel' onclick='spe_GroupRemove(this)'>"+(o[i].NoDelete=="0" ? "x" : "")+"</td>"+
                        "</tr>"
                    );
                    idCommonOld = o[i].idCommon;
                };
                if (o[i].idGood != null)
                    $("#OrderSpecificTable").append(
                        "<tr CommonID='" + o[i].idCommon + "' idGood='" + o[i].idGood + "' idDetail='"+o[i].idDetail+"'>" +
							"<td Type='GroupName'></td>" +
							"<td Type='CommonCount'></td>" +
							"<td Type='GoodName'>" + o[i].GoodName + "</td>" +
                        	"<td Type='BtnDel' onclick='spe_GoodRemove(this)'>"+(o[i].NoDelete=="0" ? "x" : "")+"</td>"+
                        "</tr>"
                    );
            };
			//Для позиций, по которым проходило списание запретим менять кол-во
            for(var i=0; i<$("#OrderSpecificTable tr[idDetail]").length; i++){
            	var TR=$("#OrderSpecificTable tr[idDetail]:eq("+i+")");
            	if(TR.find("td[Type=BtnDel]").text()=="")
                    $("#OrderSpecificTable tr[idCommon=" + TR.attr("CommonID") + "] td[Type=CommonCount] input").prop('disabled', true);
            	$("#OrderSpecificTable tr[idCommon=" + TR.attr("CommonID") + "] td[Type=BtnDel]").text("");
			};

			//Скроим кнопку сохранить кол-во для групп
            $("#OrderSpecificTable tr[idGroup] td[Type=BtnSave]").hide();
		}
	);
	$("#OrderSpecificDialog").dialog("open");
}
function spe_CountChange(el){
	$(el).parent().parent().find("td[Type=BtnSave]").show();
}
function spe_CountChanged(el){
	var TR=$(el).parent().parent();
	var Count=TR.find("td[Type=CommonCount] input").val();
	$.post(
        "Orders/Specifications.php",
		{
			"Action":"CountChanged",
			"idCommon":TR.attr("idCommon"),
			"Count":Count
		},
		function(o){
        	if(o.Result=="ok")
        		$(el).parent().hide();
		}
	)
}
function spe_GroupRemove(el){
	var TR=$(el).parent();
	$.post(
        "Orders/Specifications.php",
		{
			"Action":"RemoveGroup",
			"idCommon":TR.attr("idCommon")
		},
		function(o){
        	if(o.Result=="Ok") TR.remove();
		}
	)
}
//Добавить группу
function spe_AddGroupLoad(){
	$("#spe_GroupTable tr").remove();
	$.post(
        "Orders/Specifications.php",
		{
			"Action":"SelectGroups"
		},
		function(o){
        	var i=-1;
        	while(o[++i]!=null)
				$("#spe_GroupTable").append(
					"<tr idGroup='"+o[i].idGroup+"' onclick='spe_GroupSelecTR(this)'>" +
						"<td>"+o[i].GroupName+"</td>"+
					"</tr>"
				);
        	$("#spe_GroupDialog").dialog("open");
		}
	)
}
function spe_GroupSelecTR(el){
	$("#spe_GroupTable tr").attr("TypeSelect","NoSelect");
	$(el).attr("TypeSelect","Selected");
}
function spe_GroupSelected(){
	//Определим не была создана группа
	var GroupNameSelected=$("#spe_GroupTable tr[TypeSelect=Selected] td").text();
	/*
	if($("#OrderSpecificTable tr[idGroup] td[Type=GroupName]:contains('"+GroupNameSelected+"')").length>0){
        $("#spe_GroupDialog").dialog("close");
        alert("Данная группа уже добавлена!");
		return false;
	}
	*/
	if($("#spe_GroupTable tr[TypeSelect=Selected]").length==0){
		$("#spe_GroupDialog").dialog("close");
	}
	else
		$.post(
            "Orders/Specifications.php",
			{
				"Action":"AddGroup",
				"idDoor":spe_DoorTR.attr("idDoor"),
				"idGroup":$("#spe_GroupTable tr[TypeSelect=Selected]").attr("idGroup")
			},
			function(o){
            	if(o.Result=="ok"){
            		var idCommon=o.idCommon;
            		var idGroup=$("#spe_GroupTable tr[TypeSelect=Selected]").attr("idGroup");
            		var GroupName=$("#spe_GroupTable tr[TypeSelect=Selected] td").text();
                    $("#OrderSpecificTable").append(
                        "<tr idCommon='" + idCommon + "' idGroup='"+idGroup+"'>" +
							"<td Type='GroupName'>" + GroupName + "</td>" +
							"<td Type='CommonCount'><input oninput='spe_CountChange(this)' value='0'></td>" +
							"<td><button onclick='spe_GoodSelectLoad(this)'>Добавить</button></td>" +
                        	"<td Type='BtnSave'><button onclick='spe_CountChanged(this)'>Сохранить</button></td>"+
							"<td Type='BtnDel' onclick='spe_GroupRemove(this)'>x</td>"+
                        "</tr>"
                    );
                    $("#spe_GroupDialog").dialog("close");
				};
                //Скроим кнопку сохранить кол-во для групп
                $("#OrderSpecificTable tr[idGroup] td[Type=BtnSave]").hide();
			}
		)
}
//Выбор Материала
var spe_CommonTR;
function spe_GoodSelectLoad(el){
    $("#spe_GoodDialogList tr").remove();
    spe_CommonTR=$(el).parent().parent();
    $.post(
        "Orders/Specifications.php",
		{
			"Action":"SelectGood",
			"idGroup":spe_CommonTR.attr("idGroup")
		},
		function(o){
        	var i=-1;
			while(o[++i]!=null)
                $("#spe_GoodDialogList").append(
					"<tr idGood='"+o[i].idGood+"' onclick='spe_GoodSelectTR(this)'>" +
						"<td Type='GoodName'>"+o[i].GoodName+"</td>"+
                       	"<td Type='CountMain'>"+o[i].CountMain+"</td>"+
                       	"<td Type='CountEnt'>"+o[i].CountEnt+"</td>"+
					"</tr>"
                );
            $("#spe_GoodDialog").dialog("open");
		}
	);
}
function spe_GoodSelectTR(el){
	$("#spe_GoodDialogList tr").attr("Status","NoSelect");
	$(el).attr("Status","Selected");
}
function spe_GoodSelected(){
	//Проверим не существует выбранная номеклатура
    var GoodName=$("#spe_GoodDialogList tr[Status=Selected] td[Type=GoodName]").text();
    var idCommon=spe_CommonTR.attr("idCommon");
	if($("#OrderSpecificTable tr[idCommon="+idCommon+"][idGood] td[Type=GoodName]:contains('"+GoodName+"')").length>0){
        $("#spe_GoodDialog").dialog("close");
        alert("Выбранная номеклатура уже добавленна!");
		return false;
	};

	if($("#spe_GoodDialogList tr[Status=Selected]").length==0){
        $("#spe_GoodDialog").dialog("close");
	}
	else
		$.post(
            "Orders/Specifications.php",
			{
				"Action":"AddGood",
				"idCommon":spe_CommonTR.attr("idCommon"),
				"idGood":$("#spe_GoodDialogList tr[Status=Selected]").attr("idGood")
			},
			function(o){
            	if(o.Result=="ok"){
            		var idGood=$("#spe_GoodDialogList tr[Status=Selected]").attr("idGood");
                    $("#OrderSpecificTable tr[idCommon="+idCommon+"][idGroup]").after(
                        "<tr idCommon='" + idCommon + "' idGood='" + idGood + "'>" +
							"<td Type='GroupName'></td>" +
							"<td Type='CommonCount'></td>" +
							"<td Type='GoodName'>" + GoodName + "</td>" +
                        "</tr>"
                    );
                    $("#spe_GoodDialog").dialog("close");
				}
			}
		)
}
//Удалить материал
function spe_GoodRemove(el){
	if(confirm("Удалить позицию?")) {
        var TR = $(el).parent();
        $.post(
            "Orders/Specifications.php",
            {
                "Action": "RemoveGood",
                "idDetail": TR.attr("idDetail")
            },
            function (o) {
                if (o.Result == "ok")
                    TR.remove();
            }
        )
    };
}
//-----------------------------------------------------------

//--------------------------Контрагенты----------------------
function OrderContragentListLoad(el, elContact){
	$("#OrderContragentListDialogFindInp").val($("#"+el).val());
	$("#OrderContragentListDialog").dialog("open");
	$("#OrderContragentListDialogElIN").val(el);
	$("#OrderContragentListDialogElINContact").val(elContact);
	OrderContragentListSelect();
}
function OrderContragentListSelect(){
	$("#OrderContragentListDialogTable").find("tr").remove();
	$.post(
		"orders/order.php",
		{"method":"ContragentListSelect", "Where":$("#OrderContragentListDialogFindInp").val()==""?"1=1":"o.Alias LIKE '%"+$("#OrderContragentListDialogFindInp").val()+"%'"},
		function(data){
			var o=jQuery.parseJSON(data); var i=0;
			while(o[i]!=null)
			{
				$("#OrderContragentListDialogTable").append(
					"<tr idContragent="+o[i].id+">"+
						"<td width='250' onClick='OrderContragentSelect(this)'>"+o[i].Alias+"</td>"+
						"<td width='130' onClick='OrderContragentSelect(this)'>"+o[i].INN+"</td>"+
						"<td width='130' onClick='OrderContragentSelect(this)'>"+(o[i].Phone1!=null?o[i].Phone1:"")+"</td>"+
						"<td width='20'><img onClick='OrderContragenEditStart(this);' src='images/edit.png' width=15></td>"+
						"<td width='20'><img src='images/delete.png' width=15></td>"+
					"</tr>"
				);
				i++;
			};
		}
	);
}
function OrderContragentSelect(el)
{
	$("#OrderContragentListDialogTable tr").removeAttr("class");
	$(el).parent().attr("class","Complite");
}
//Добавление нового контрагента
function OrderContragenAdd(){
	$("#OrderContragenDialog").dialog("open");
	$("#OrderContragenDialogID").val("");
	$("#OrderContragenDialogAdressAlias").val("");
	$("#OrderContragenDialogAlias").css("background-color","white");
	$("#OrderContragenDialogAdressName").val("");
	$("#OrderContragenDialogAdressUrid").val("");
	$("#OrderContragenDialogAdressFact").val("");
	$("#OrderContragenDialogAdressShpt").val("");
	$("#OrderContragenDialogINN").val("");
	$("#OrderContragenDialogKPP").val("");
	$("#OrderContragenDialogOKPO").val("");
	$("#OrderContragenDialogOGRN").val("");
	$("#OrderContragenDialogNote").val("");
	$("#OrderContragenDialogBugs").text("");
	$("#OrderContragenDialogContactList").find("tr").remove();
}
function OrderContragenSave(){
	if($("#OrderContragenDialogAlias").val()=="")
	{
		$("#OrderContragenDialogAlias").css("background-color","pink");
		return true;
	};

	var ContactID=new Array();
	var ContactStatus=new Array();
	var ContactFIO=new Array();
	var ContactPost=new Array();
	var ContactPhone1=new Array();
	var ContactPhone2=new Array();
	var ContacteMail=new Array();
	var ContactNote=new Array();
	var ContactAgent=new Array();
	for(var i=0;i<$("#OrderContragenDialogContactList").find("tr").length;i++)
	{
		ContactID[i]=$("#OrderContragenDialogContactList tr:eq("+i.toString()+")").attr("idContact");
		ContactStatus[i]=$("#OrderContragenDialogContactList tr:eq("+i.toString()+")").attr("status");
		ContactFIO[i]=$("#OrderContragenDialogContactList tr:eq("+i.toString()+") td:eq(0)").text();
		ContactPost[i]=$("#OrderContragenDialogContactList tr:eq("+i.toString()+") td:eq(1)").text();
		ContactPhone1[i]=$("#OrderContragenDialogContactList tr:eq("+i.toString()+") td:eq(2)").text();
		ContactPhone2[i]=$("#OrderContragenDialogContactList tr:eq("+i.toString()+")").attr("Phone2");
		ContacteMail[i]=$("#OrderContragenDialogContactList tr:eq("+i.toString()+") td:eq(3)").text();
		ContactNote[i]=$("#OrderContragenDialogContactList tr:eq("+i.toString()+")").attr("Note");
		ContactAgent[i]=$("#OrderContragenDialogContactList tr:eq("+i.toString()+")").attr("Agent");
	};
		$.post(
			"orders/order.php",
			{
				"method":"ContragentSave",
				"id":$("#OrderContragenDialogID").val(),
				"Alias":$("#OrderContragenDialogAlias").val(),
				"Name":$("#OrderContragenDialogName").val(),
				"AdressUrid":$("#OrderContragenDialogAdressUrid").val(),
				"AdressFact":$("#OrderContragenDialogAdressFact").val(),
				"AdressShpt":$("#OrderContragenDialogAdressShpt").val(),
				"INN":$("#OrderContragenDialogINN").val(),
				"KPP":$("#OrderContragenDialogKPP").val(),
				"OKPO":$("#OrderContragenDialogOKPO").val(),
				"OGRN":$("#OrderContragenDialogOGRN").val(),
				"Note":$("#OrderContragenDialogNote").val(),
				"ContactID[]":ContactID,
				"ContactStatus[]":ContactStatus,
				"ContactFIO[]":ContactFIO,
				"ContactPost[]":ContactPost,
				"ContactPhone1[]":ContactPhone1,
				"ContactPhone2[]":ContactPhone2,
				"ContacteMail[]":ContacteMail,
				"ContactNote[]":ContactNote,
				"ContactAgent[]":ContactAgent
			},
			function(data){
				try
				{
					var o=jQuery.parseJSON(data);
					switch(o.Status)
					{
						case "ok":
							if($("#OrderContragenDialogID").val()=="")
							{
								$("#OrderContragentListDialogTable tr").removeAttr("class");
								var Phone1="";
								if($("#OrderContragenDialogContactList tr[class=Complite]").length>0) 
									Phone1=$("#OrderContragenDialogContactList tr[class=Complite]:eq(0) td:eq(2)").text();
								if(Phone1=="")
									if($("#OrderContragenDialogContactList tr").length>0)
										Phone1=$("#OrderContragenDialogContactList tr:eq(0) td:eq(2)").text();
								$("#OrderContragentListDialogTable").prepend(
									"<tr idContragent="+o.idContragent+" class=Complite>"+
										"<td width='250' onClick='OrderContragentSelect(this)'>"+$("#OrderContragenDialogAlias").val()+"</td>"+
										"<td width='130' onClick='OrderContragentSelect(this)'>"+$("#OrderContragenDialogINN").val()+"</td>"+
										"<td width='130' onClick='OrderContragentSelect(this)'>"+Phone1+"</td>"+
										"<td width='20'><img onClick='OrderContragenEditStart(this);' src='images/edit.png' width=15></td>"+
										"<td width='20'><img src='images/delete.png' width=15></td>"+
									"</tr>"
								);
							}
							else{
								$("#OrderContragentListDialogTable tr[idContragent="+$("#OrderContragenDialogID").val()+"] td:eq(0)").text($("#OrderContragenDialogAlias").val());
								$("#OrderContragentListDialogTable tr[idContragent="+$("#OrderContragenDialogID").val()+"] td:eq(1)").text($("#OrderContragenDialogINN").val());
							};
							$("#OrderContragenDialog").dialog("close");	
						break;
						case "err":
							$("#OrderContragenDialogBugs").text(o.ErrMsg);
						break;
					};
				}
				catch(e){$("#OrderContragenDialogBugs").text(data);};
			}
		);
	
}
function OrderContragenEditStart(el){
	var elTR=$(el).parent().parent();
	$.post(
		"orders/order.php",
		{
			"method":"ContragentEditStart",
			"id":elTR.attr("idContragent")
		},
		function(data){
			var o=jQuery.parseJSON(data);
			$("#OrderContragenDialogID").val(elTR.attr("idContragent"));
			$("#OrderContragenDialogAlias").val(o.Alias);
			$("#OrderContragenDialogAlias").css("background-color","white");
			$("#OrderContragenDialogName").val(o.Name);
			$("#OrderContragenDialogAdressUrid").val(o.AdressUrid);
			$("#OrderContragenDialogAdressFact").val(o.AdressFact);
			$("#OrderContragenDialogAdressShpt").val(o.AdressShpt);
			$("#OrderContragenDialogINN").val(o.INN);
			$("#OrderContragenDialogKPP").val(o.KPP);
			$("#OrderContragenDialogOKPO").val(o.OKPO);
			$("#OrderContragenDialogOGRN").val(o.OGRN);
			$("#OrderContragenDialogNote").val(o.Note);
			$("#OrderContragenDialogBugs").text("");
			$("#OrderContragenDialogContactList").find("tr").remove();
			var i=0;
			while(o.Contacts[i]!=null)
			{
				$("#OrderContragenDialogContactList").append(
					"<tr idContact='"+o.Contacts[i].id+"' guid='"+guidSmall()+"' status='Load' "+(o.Contacts[i].Agent=="1" ? "class='Complite'":"")+
						"Phone2='"+o.Contacts[i].Phone2+"' "+
						"Note='"+o.Contacts[i].Note+"' "+
						"Agent='"+o.Contacts[i].Agent+"' >"+
						"<td width=120 onClick='OrderContragenContactEditStart(this)'>"+o.Contacts[i].FIO+"</td>"+
						"<td width=120 onClick='OrderContragenContactEditStart(this)'>"+o.Contacts[i].Post+"</td>"+
						"<td width=120 onClick='OrderContragenContactEditStart(this)'>"+o.Contacts[i].Phone1+"</td>"+
						"<td width=120 onClick='OrderContragenContactEditStart(this)'>"+o.Contacts[i].eMail+"</td>"+
						"<td width=20 onClick='OrderContragenContactDel(this)'><img src='images/delete.png' width=15></td>"+
					"</tr>"
				);
				i++;
			};
			$("#OrderContragenDialog").dialog("open");
		}
	);
}
//Добавление нового контакта
function OrderContragenContactAdd(){
	$("#OrderContragenContactDialogID").val("");
	$("#OrderContragenContactDialogFIO").val("");
	$("#OrderContragenContactDialogFIO").css("background-color","white");
	$("#OrderContragenContactDialogPost").val("");
	$("#OrderContragenContactDialogPhone1").val("");
	$("#OrderContragenContactDialogPhone2").val("");
	$("#OrderContragenContactDialogeMail").val("");
	$("#OrderContragenContactDialogNote").val("");
	$("#OrderContragenContactDialogAgent").removeAttr("checked");
	$("#OrderContragenContactDialog").dialog("open");
}
function OrderContragenContactEditStart(el){
	var elTR=$(el).parent();
	$("#OrderContragenContactDialogID").val(elTR.attr("guid"));
	$("#OrderContragenContactDialogFIO").val(elTR.find("td:eq(0)").text());
	$("#OrderContragenContactDialogPost").val(elTR.find("td:eq(1)").text());
	$("#OrderContragenContactDialogPhone1").val(elTR.find("td:eq(2)").text());
	$("#OrderContragenContactDialogPhone2").val(elTR.attr("Phone2"));
	$("#OrderContragenContactDialogeMail").val(elTR.find("td:eq(3)").text());
	$("#OrderContragenContactDialogNote").val(elTR.attr("Note"));
	$("#OrderContragenContactDialogAgent").removeAttr("checked");
	$("#OrderContragenContactDialogAgent").prop("checked",(elTR.attr("Agent")=="1"? true:false));
	$("#OrderContragenContactDialog").dialog("open");
}
function OrderContragenContactSave(){
	if($("#OrderContragenContactDialogFIO").val()=="")
	{
		$("#OrderContragenContactDialogFIO").css("background-color","pink");
	}
	else
	{
		if($("#OrderContragenContactDialogID").val()=="")
		{
			if($("#OrderContragenContactDialogAgent").is(":checked"))
			{
			 	$("#OrderContragenDialogContactList tr").removeAttr("class");
			 	$("#OrderContragenDialogContactList tr").attr("Agent","0");
			}
			$("#OrderContragenDialogContactList").append(
					"<tr idContact='' guid='"+guidSmall()+"' status='Add' "+($("#OrderContragenContactDialogAgent").is(":checked") ? "class='Complite'":"")+
						"Phone2='"+$("#OrderContragenContactDialogPhone2").val()+"' "+
						"Note='"+$("#OrderContragenContactDialogNote").val()+"' "+
						"Agent='"+($("#OrderContragenContactDialogAgent").is(":checked") ? "1":"0")+"' >"+
						"<td width=120 onClick='OrderContragenContactEditStart(this)'>"+$("#OrderContragenContactDialogFIO").val()+"</td>"+
						"<td width=120 onClick='OrderContragenContactEditStart(this)'>"+$("#OrderContragenContactDialogPost").val()+"</td>"+
						"<td width=120 onClick='OrderContragenContactEditStart(this)'>"+$("#OrderContragenContactDialogPhone1").val()+"</td>"+
						"<td width=120 onClick='OrderContragenContactEditStart(this)'>"+$("#OrderContragenContactDialogeMail").val()+"</td>"+
						"<td width=20 onClick='OrderContragenContactDel(this)'><img src='images/delete.png' width=15></td>"+
					"</tr>"
				);
		}
		else
		{
			var id=$("#OrderContragenContactDialogID").val();
			$("#OrderContragenDialogContactList tr[guid='"+id+"']").attr("Phone2",$("#OrderContragenContactDialogPhone2").val());
			$("#OrderContragenDialogContactList tr[guid='"+id+"']").attr("Note",$("#OrderContragenContactDialogNote").val());
			if($("#OrderContragenContactDialogAgent").is(":checked"))
			{
			 	$("#OrderContragenDialogContactList tr").removeAttr("class");
			 	$("#OrderContragenDialogContactList tr").attr("Agent","0");
				$("#OrderContragenDialogContactList tr[guid='"+id+"']").attr("class","Complite") 	;
			};
			$("#OrderContragenDialogContactList tr[guid='"+id+"']").attr("Agent",($("#OrderContragenContactDialogAgent").is(":checked") ? "1":"0"));
			$("#OrderContragenDialogContactList tr[guid='"+id+"'] td:eq(0)").text($("#OrderContragenContactDialogFIO").val());
			$("#OrderContragenDialogContactList tr[guid='"+id+"'] td:eq(1)").text($("#OrderContragenContactDialogPost").val());
			$("#OrderContragenDialogContactList tr[guid='"+id+"'] td:eq(2)").text($("#OrderContragenContactDialogPhone1").val());
			$("#OrderContragenDialogContactList tr[guid='"+id+"'] td:eq(3)").text($("#OrderContragenContactDialogeMail").val());
			if($("#OrderContragenDialogContactList tr[guid='"+id+"']").attr("status")=="Load")
				$("#OrderContragenDialogContactList tr[guid='"+id+"']").attr("status","Edit");
		};
		$("#OrderContragenContactDialog").dialog("close");
	};
}
function OrderContragenContactDel(el){
	if(confirm("Произвести удлаение?"))
		if($(el).parent().attr("status")=="Add")
		{
			$(el).parent().remove();
		}
		else
		{
			$(el).parent().attr("status","Del");
			$(el).parent().hide();
		};
}

//----------- Расчет зарплаты (Payroll)---------------------
/*
	OrderPrlReadMoreOpen - вызывает диалоговое окно подробностей расчета
	Фукнция заполняет таблицу расчета: OrderPrlCalcTable и 
	две таблицы основание расчета
*/
function OrderPrlReadMoreOpen(el){
	var Step=$(el).parent().parent().find("td:eq(0)").text();
	$("#OrderPrlRowStep").val(Step);
	var RowID=$("#OrderCostDialogID").text();
	var RowEl=$("#OrderDialogTable tr[id="+RowID+"]");
	var HFramug=RowEl.find("td[type=FramugaH]").text()!="" ? parseFloat(RowEl.find("td[type=FramugaH]").text()) : 0;
	var H=parseFloat(RowEl.find("td[type=H]").text())-HFramug;
	var W=RowEl.find("td[type=W]").text();
	var S=RowEl.find("td[type=S]").text()!=""?true:false;
	var Open=RowEl.find("td[type=Open]").text();
	var Framuga=RowEl.find("td[type=FramugaCh] input").is(":checked");
	$("#OrderPrlBaseDoorType").find("tr").remove();
	$("#OrderPrlBaseConst").find("tr").remove();
	$("#OrderPrlCalcTable").find("tr").remove();
	$.post(
		"propertes/prop.php",
		{"Method":"PropPrlLoad", "DoorType":RowEl.find("td[type=Name]").text(), "Step":Step},
		function(data){
			var o=jQuery.parseJSON(data); var i=0;
			var SizeSum=0; var SizeNote="";
			while(o.Size[i]!=null){
				var HSign="n/a";
				switch(o.Size[i].HSign)
				{
					case "0":HSign="n/a"; break;
					case "1":HSign="<"; break;
					case "2":HSign=">"; break;
					case "3":HSign="="; break;
				};
				var WSign="n/a";
				switch(o.Size[i].WSign)
				{
					case "0":WSign="n/a"; break;
					case "1":WSign="<"; break;
					case "2":WSign=">"; break;
					case "3":WSign="="; break;
				};
				$("#OrderPrlBaseDoorType").append(
					"<tr>"+
						"<td>"+HSign+" "+o.Size[i].HVal+"</td>"+
						"<td>"+WSign+" "+o.Size[i].WVal+"</td>"+
						"<td>"+(o.Size[i].S==1?"Одностворчатая":"")+(o.Size[i].S==2?"Двухстворчатая":"")+"</td>"+
						"<td>"+o.Size[i].Open+"</td>"+
						"<td>"+(o.Size[i].Framug==1?"Да":"")+"</td>"+
						"<td>"+o.Size[i].Sum+"</td>"+
					"</tr>"
				);
				//Расчет, в случае если строка подходит по параметрам то присвоим значение переменным
				var CalcOk=true;
				switch(o.Size[i].HSign)
				{
					case "1": if(parseFloat(H)>=parseFloat(o.Size[i].HVal)) CalcOk=false; break;
					case "2": if(parseFloat(H)<=parseFloat(o.Size[i].HVal)) CalcOk=false; break;
					case "3": if(parseFloat(H)!=parseFloat(o.Size[i].HVal)) CalcOk=false; break;
				};
				switch(o.Size[i].WSign)
				{
					case "1": if(parseFloat(W)>=parseFloat(o.Size[i].WVal)) CalcOk=false; break;
					case "2": if(parseFloat(W)<=parseFloat(o.Size[i].WVal)) CalcOk=false; break;
					case "3": if(parseFloat(W)!=parseFloat(o.Size[i].WVal)) CalcOk=false; break;
				};

				if((S & o.Size[i].S==1) || (!S & o.Size[i].S==2)) CalcOk=false;
				if(o.Size[i].Open!=""  & (o.Size[i].Open!=Open)) CalcOk=false;
				if(!Framuga & o.Size[i].Framug==1) CalcOk=false;

				if(CalcOk) 
				{
					SizeNote="Высота: "+HSign+" "+o.Size[i].HVal+", Ширина: "+WSign+" "+o.Size[i].WVal+""+(o.Size[i].S==1?"Одностворчатая":"")+(o.Size[i].S==2?"Двухстворчатая":"")+(o.Size[i].Open!=""?" , Открывание: "+o.Size[i].Open:"")+(o.Size[i].Framug==1?" , Фрамуга":"");
					SizeSum=o.Size[i].Sum;
				};

				i++;
			};
			//Добавим строки в таблицы
			$("#OrderPrlCalcTable").append(
					"<tr>"+
						"<td>"+SizeNote+"</td>"+
						"<td><input value='"+SizeSum+"'></td>"+
					"</tr>"
				);

			var i=0;
			while(o.Const[i]!=null){
				$("#OrderPrlBaseConst").append(
					"<tr>"+
						"<td>"+o.Const[i].Name+"</td>"+
						"<td>"+o.Const[i].Sum+"</td>"+
					"</tr>"
				);
				$("#OrderPrlCalcTable").append(
					"<tr>"+
						"<td>"+o.Const[i].Name+"</td>"+
						"<td><input value='"+o.Const[i].Sum+"'></td>"+
					"</tr>"
				);
				i++;
			};
			
			//Расчет з/п взависимости от конструкции
			//Рамка
			if(o.Constructor.Frame==1)
			{
				var workwindowch= RowEl.find("td[type=Construct] span workwindowch").text();
				var workwindownoframe= RowEl.find("td[type=Construct] span workwindownoframe").text();

				var stvorkawindowch= RowEl.find("td[type=Construct] span stvorkawindowch").text();
				var stvorkawindownoframe= RowEl.find("td[type=Construct] span stvorkawindownoframe").text();

				var framugawindowch= RowEl.find("td[type=Construct] span framugawindowch").text();
				var framugawindownoframe= RowEl.find("td[type=Construct] span framugawindownoframe").text();				
				if((workwindowch=="true" & workwindownoframe=="false") || (stvorkawindowch=="true" & stvorkawindownoframe=="false") || (framugawindowch=="true" & framugawindownoframe=="false"))
					if(o.Constructor.FrameCount==1)
					{
						var PetlyaCount=(workwindowch=="true" & workwindownoframe=="false") ? 1 :0;
						PetlyaCount+=(stvorkawindowch=="true" & stvorkawindownoframe=="false") ? 1 :0;
						PetlyaCount+=(framugawindowch=="true" & framugawindownoframe=="false") ? 1 :0;
						$("#OrderPrlCalcTable").append(
							"<tr>"+
								"<td>Рамка, кол-во: "+PetlyaCount+"</td>"+
								"<td><input value='"+(parseFloat( o.Constructor.FrameSum)*PetlyaCount).toString()+"'></td>"+
							"</tr>"
						);
					}
					else
					{
						$("#OrderPrlCalcTable").append(
							"<tr>"+
								"<td>Рамка</td>"+
								"<td><input value='"+o.Constructor.FrameSum+"'></td>"+
							"</tr>"
						);
					};
			};
			//Доводчик
			if(o.Constructor.Dovod==1 )
			{
				if(RowEl.find("td[type=Dovod]").text().toUpperCase()=="ДА" & o.Constructor.DovodPreparation!=1)
					$("#OrderPrlCalcTable").append(
						"<tr>"+
							"<td>Доводчик</td>"+
							"<td><input value='"+o.Constructor.DovodSum+"'></td>"+
						"</tr>"
					);
				if(o.Constructor.DovodPreparation==1)
					if(RowEl.find("td[type=Dovod]").text().toUpperCase()=="НЕТ, ПОДГОТОВКА" || RowEl.find("td[type=Dovod]").text().toUpperCase()=="ДА")
						$("#OrderPrlCalcTable").append(
							"<tr>"+
								"<td>Доводчик или подготовка доводчик</td>"+
								"<td><input value='"+o.Constructor.DovodSum+"'></td>"+
							"</tr>"
						);
			};
			//Наличник
			if(o.Constructor.Nalichnik==1)
				if(RowEl.find("td[type=Nalichnik]").text().toUpperCase().indexOf("ДА")>-1)
					$("#OrderPrlCalcTable").append(
						"<tr>"+
							"<td>Наличник</td>"+
							"<td><input value='"+o.Constructor.NalichnikSum+"'></td>"+
						"</tr>"
					);
			//Окно
			if(o.Constructor.Window==1)
			{
				var workwindowch= RowEl.find("td[type=Construct] span workwindowch").text();
				var stvorkawindowch= RowEl.find("td[type=Construct] span stvorkawindowch").text();
				var framugawindowch= RowEl.find("td[type=Construct] span framugawindowch").text();
				if(workwindowch=="true" || stvorkawindowch=="true" || framugawindowch=="true")
					if(o.Constructor.WindowCount==1)//Зависит от кол-ва
					{
						var WindowCount=workwindowch=="true" ? 1 : 0;
						WindowCount+=stvorkawindowch=="true" ? 1 : 0;
						WindowCount+=framugawindowch=="true" ? 1 : 0;
						if(o.Constructor.WindowMore!=null)//Если больше
						{
							if(WindowCount>parseInt(o.Constructor.WindowMore))
								$("#OrderPrlCalcTable").append(
									"<tr>"+
										"<td>Окна (если больше: "+o.Constructor.WindowMore+")</td>"+
										"<td><input value='"+((WindowCount-parseInt(o.Constructor.WindowMore))*parseFloat(o.Constructor.WindowSum))+"'></td>"+
									"</tr>"
								);
						}
						else
						{
							$("#OrderPrlCalcTable").append(
								"<tr>"+
									"<td>Окно (кол-во: "+WindowCount+")</td>"+
									"<td><input value='"+(WindowCount*parseFloat(o.Constructor.WindowSum))+"'></td>"+
								"</tr>"
							);
						};
					}
					else
					{
						$("#OrderPrlCalcTable").append(
							"<tr>"+
								"<td>Окно</td>"+
								"<td><input value='"+o.Constructor.WindowSum+"'></td>"+
							"</tr>"
						);
					};
			};
			//Фрамуга
			if(o.Constructor.Framuga==1 & RowEl.find("td[type=Construct] span framugach").text()=="true")
				$("#OrderPrlCalcTable").append(
					"<tr>"+
						"<td>Фрамуга</td>"+
						"<td><input value='"+o.Constructor.FramugaSum+"'></td>"+
					"</tr>"
				);
			//Навесы
			if(o.Constructor.Petlya==1)
			{
				var PetlyaCount=0;
				PetlyaCount+=parseInt(RowEl.find("td[type=Construct] span workpetlya").text());
				PetlyaCount+=parseInt(RowEl.find("td[type=Construct] span stvorkapetlya").text());
				if(PetlyaCount!=0)
					if(o.Constructor.PetlyaCount==1)//Зависит от кол-ва
					{
						if(PetlyaCount>parseInt(o.Constructor.PetlyaMore))
							$("#OrderPrlCalcTable").append(
								"<tr>"+
									"<td>Петли (больше чем: "+o.Constructor.PetlyaMore+")</td>"+
									"<td><input value='"+((PetlyaCount-parseInt(o.Constructor.PetlyaMore))*parseFloat(o.Constructor.PetlyaSum))+"'></td>"+
								"</tr>"
							);
					}
					else
					{
						$("#OrderPrlCalcTable").append(
							"<tr>"+
								"<td>Петли</td>"+
								"<td><input value='"+o.Constructor.PetlyaSum+"'></td>"+
							"</tr>"
						);
					};
			};
			//Навесы на рабочей створке
			if(o.Constructor.PetlyaWork==1)
			{
				var PetlyaCount=0;
				PetlyaCount+=parseInt(RowEl.find("td[type=Construct] span workpetlya").text());
				if(PetlyaCount!=0)
					if(o.Constructor.PetlyaWorkCount==1)//Зависит от кол-ва
					{
						if(PetlyaCount>parseInt(o.Constructor.PetlyaWorkMore))
							$("#OrderPrlCalcTable").append(
								"<tr>"+
									"<td>Навесы на раб. ств. (больше чем: "+o.Constructor.PetlyaWorkMore+")</td>"+
									"<td><input value='"+((PetlyaCount-parseInt(o.Constructor.PetlyaWorkMore))*parseFloat(o.Constructor.PetlyaWorkSum))+"'></td>"+
								"</tr>"
							);
					}
					else
						$("#OrderPrlCalcTable").append(
							"<tr>"+
								"<td>Навесы на раб. ств.</td>"+
								"<td><input value='"+o.Constructor.PetlyaWorkSum+"'></td>"+
							"</tr>"
						);
			};
			//Навесы на второй створке
			if(o.Constructor.PetlyaStvorka==1)
			{
				var PetlyaCount=0;
				PetlyaCount+=parseInt(RowEl.find("td[type=Construct] span stvorkapetlya").text());
				if(PetlyaCount!=0)
					if(o.Constructor.PetlyaStvorkaCount==1)//Зависит от кол-ва
					{
						if(PetlyaCount>parseInt(o.Constructor.PetlyaStvorkaMore))
							$("#OrderPrlCalcTable").append(
								"<tr>"+
									"<td>Навесы на 2ой ств. (больше чем: "+o.Constructor.PetlyaStvorkaMore+")</td>"+
									"<td><input value='"+((PetlyaCount-parseInt(o.Constructor.PetlyaStvorkaMore))*parseFloat(o.Constructor.PetlyaStvorkaSum))+"'></td>"+
								"</tr>"
							);
					}
					else
						$("#OrderPrlCalcTable").append(
							"<tr>"+
								"<td>Навесы на 2ой ств.</td>"+
								"<td><input value='"+o.Constructor.PetlyaStvorkaSum+"'></td>"+
							"</tr>"
						);
			};
			//Ребра жесткости
			if(o.Constructor.Stiffener==1)
				if(o.Constructor.StiffenerW==1)//Зависит от кв.м.
				{
					$("#OrderPrlCalcTable").append(
						"<tr>"+
							"<td>Ребра жесткости (за м<sup>2</sup>)</td>"+
							"<td><input value='"+(parseFloat(H)*parseFloat(W)*parseFloat(o.Constructor.StiffenerSum)/1000000)+"'></td>"+
						"</tr>"
					);
				}
				else
				{
					$("#OrderPrlCalcTable").append(
						"<tr>"+
							"<td>Ребра жесткости</td>"+
							"<td><input value='"+o.Constructor.StiffenerSum+"'></td>"+
						"</tr>"
					);
				};
			//Площадь двери
			if(o.Constructor.M2==1)
				$("#OrderPrlCalcTable").append(
						"<tr>"+
							"<td>Площадь двери (за м<sup>2</sup>)</td>"+
							"<td><input value='"+(parseFloat(H)*parseFloat(W)*parseFloat(o.Constructor.M2Sum)/1000000)+"'></td>"+
						"</tr>"
					);
			//Антипаника
			if(o.Constructor.Antipanik==1 & parseInt(RowEl.find("td[type=Construct] span Antipanik").text())==1)
				$("#OrderPrlCalcTable").append(
						"<tr>"+
							"<td>Антипаника</td>"+
							"<td><input value='"+parseFloat(o.Constructor.AntipanikSum)+"'></td>"+
						"</tr>"
					);
			//Отбойник
			if(o.Constructor.Otboynik==1 & parseInt(RowEl.find("td[type=Construct] span Otboynik").text())==1)
				$("#OrderPrlCalcTable").append(
						"<tr>"+
							"<td>Отбойник</td>"+
							"<td><input value='"+parseFloat(o.Constructor.OtboynikSum)+"'></td>"+
						"</tr>"
					);
			//Калитка
			if(o.Constructor.Wicket==1 & parseInt(RowEl.find("td[type=Construct] span Wicket").text())==1)
				$("#OrderPrlCalcTable").append(
						"<tr>"+
							"<td>Калитка</td>"+
							"<td><input value='"+parseFloat(o.Constructor.WicketSum)+"'></td>"+
						"</tr>"
					);
			//Врезка замка
			if(o.Constructor.BoxLock==1 & parseInt(RowEl.find("td[type=Construct] span BoxLock").text())==1)
				$("#OrderPrlCalcTable").append(
						"<tr>"+
							"<td>Врезка замка</td>"+
							"<td><input value='"+parseFloat(o.Constructor.BoxLockSum)+"'></td>"+
						"</tr>"
					);
			//Отвветка
			if(o.Constructor.Otvetka==1 & parseInt(RowEl.find("td[type=Construct] span Otvetka").text())==1)
				$("#OrderPrlCalcTable").append(
						"<tr>"+
							"<td>Отвветка</td>"+
							"<td><input value='"+parseFloat(o.Constructor.OtvetkaSum)+"'></td>"+
						"</tr>"
					);
		}
	);
	
	$('#OrderPrlDialog').dialog('open');
}
//Сохранение результат при закрытии диалога ПОДРОБНЫЙ РАСЧЕТ
function OrderPrlReadMoreSave(){
	var Sum=0;
	for(var i=0; i<$("#OrderPrlCalcTable tr").length;i++){
		Sum+=parseFloat($("#OrderPrlCalcTable tr:eq("+i+") td:eq(1) input").val());
	};
	switch($("#OrderPrlRowStep").val()){
		case "Лазер": $("#OrderCostDialogLaser").val(Sum); break;
		case "Сгибка": $("#OrderCostDialogSgibka").val(Sum); break;
		case "Сварка": $("#OrderCostDialogSvarka").val(Sum); break;
		case "Рамка": $("#OrderCostDialogFrame").val(Sum); break;
		case "МДФ": $("#OrderCostDialogMdf").val(Sum); break;
		case "Сборка": $("#OrderCostDialogSborka").val(Sum); break;
		case "Покраска": $("#OrderCostDialogColor").val(Sum); break;
		case "Сборка МДФ": $("#OrderCostDialogSborkaMdf").val(Sum); break;
		case "Упаковка": $("#OrderCostDialogUpak").val(Sum); break;
		case "Отгрузка": $("#OrderCostDialogShpt").val(Sum); break;
	};
	$("#OrderPrlDialog").dialog("close");
}
//Делаем расчет всех стадий при изменении или добавлении строки
function OrderPrlCalcAddEditRow(RowID){


	var Step=new Array("Лазер","Сгибка","Сварка","Рамка","МДФ","Сборка","Покраска","Сборка МДФ","Упаковка","Отгрузка");
	var RowEl=$("#OrderDialogTable tr[id="+RowID+"]");
	var HFramug=RowEl.find("td[type=FramugaH]").text()!="" ? parseInt(RowEl.find("td[type=FramugaH]").text()) : 0;
	var H=parseInt(RowEl.find("td[type=H]").text())-HFramug;
	var W=RowEl.find("td[type=W]").text();
	var S=RowEl.find("td[type=S]").text()!=""?true:false;
	var Sv=RowEl.find("td[type=S]").text()=="Равн." ? parseFloat(W)/2 : parseFloat(RowEl.find("td[type=S]").text());
	var Open=RowEl.find("td[type=Open]").text();
	var Framuga=RowEl.find("td[type=FramugaCh] input").is(":checked");
	for(var j=0;j<Step.length;j++)
	$.post(
		"propertes/Payrolls/Load.php",
		{"TypeDoor":RowEl.find("td[type=Name]").text(), "StepName":Step[j]},
		function(o){
			var Sum=0;
			//Размеры двери
			o.DoorSize.forEach(function(od){
				var HWith=od.HWith=="" ? -1 : parseInt(od.HWith);
				var HBy=od.HBy=="" ? -1 : parseInt(od.HBy);
				var WWith=od.WWith=="" ? -1 : parseInt(od.WWith);
				var WBy=od.WWby=="" ? -1 : parseInt(od.WBy);

				var flag=true;
				if(od.HWith!="" & od.HBy=="")
					if(HWith>H) flag=false;
				if(od.HWith!="" & od.HBy!="")
					if(H<HWith || H>HBy) flag=false;
				if(od.HWith=="" & od.HBy!="")
                    if (H > HBy) flag=false;

                if(od.WWith!="" & od.WBy=="")
                    if(WWith>W) flag=false;
                if(od.WWith!="" & od.WBy!="")
                    if(W<WWith || W>WBy) flag=false;
                if(od.WWith=="" & od.WBy!="")
                    if(W>WBy) flag=false;
                switch (od.S) {
					case "":
						break;
					case "1":
						if(S) flag=false;
						break;
					case "2":
						switch (S){
							case false:
								flag=false;
								break;
							case true:
								if(od.SWith!="" || od.SBy!=""){
                                    var SWith=od.SWith=="" ? -1 : parseInt(od.SWith);
                                    var SBy=od.SBy=="" ? -1 : parseInt(od.SBy);

                                    if(od.SWith=="" & od.SBy!="")
                                        if(Sv>SBy) flag=false;
                                    if(od.SWith!="" & od.SBy!="")
                                        if(Sv<=SWith || Sv>SBy) flag=false;
                                    if(od.SWith!="" & od.SBy=="")
                                        if(SWith>=Sv) flag=false;
								};
								break;
						};
						break;
				};
                if(flag)
                	Sum=parseFloat(od.Sum);
			});
			//Постоянные значения
			o.Const.forEach(function (cons) {
				Sum+=parseFloat(cons.Sum);
			});

			console.log(o.StepName + " - "+ Sum);
			//Особенносит конструкции двери
			if(o.Construct!=null)
			{
                //Рамка
                if(o.Construct.Frame==1)
                {
                    var workwindowch= RowEl.find("td[type=Construct] span workwindowch").text();
                    var workwindownoframe= RowEl.find("td[type=Construct] span workwindownoframe").text();

                    var stvorkawindowch= RowEl.find("td[type=Construct] span stvorkawindowch").text();
                    var stvorkawindownoframe= RowEl.find("td[type=Construct] span stvorkawindownoframe").text();

                    var framugawindowch= RowEl.find("td[type=Construct] span framugawindowch").text();
                    var framugawindownoframe= RowEl.find("td[type=Construct] span framugawindownoframe").text();
                    if((workwindowch=="true" & workwindownoframe=="false") || (stvorkawindowch=="true" & stvorkawindownoframe=="false") || (framugawindowch=="true" & framugawindownoframe=="false"))
                        if(o.Construct.FrameCount==1)
                        {
                            var PetlyaCount=(workwindowch=="true" & workwindownoframe=="false") ? 1 :0;
                            PetlyaCount+=(stvorkawindowch=="true" & stvorkawindownoframe=="false") ? 1 :0;
                            PetlyaCount+=(framugawindowch=="true" & framugawindownoframe=="false") ? 1 :0;
                            Sum+=parseFloat( o.Construct.FrameSum)*PetlyaCount;
                        }
                        else
                        {
                            Sum+=parseFloat(o.Construct.FrameSum);
                        };
                };
                //Доводчик
                if(o.Construct.Dovod==1 )
                {
                    if(RowEl.find("td[type=Dovod]").text().toUpperCase()=="ДА" & o.Construct.DovodPreparation!=1)
                        Sum+=parseFloat(o.Construct.DovodSum);
                    if(o.Construct.DovodPreparation==1)
                        if(RowEl.find("td[type=Dovod]").text().toUpperCase()=="НЕТ, ПОДГОТОВКА" || RowEl.find("td[type=Dovod]").text().toUpperCase()=="ДА")
                            Sum+=parseFloat(o.Construct.DovodSum);
                };
                //Наличник
                if(o.Construct.Nalichnik==1)
                    if(RowEl.find("td[type=Nalichnik]").text().toUpperCase().indexOf("ДА")>-1)
                        Sum+=parseFloat(o.Construct.NalichnikSum);
                //Окно
                if(o.Construct.Window==1)
                {
                    //Определим кол-во окон
                    var WindowCount=0;
                    WindowCount+=RowEl.find("td[type=Construct] span workwindowch").text()=="true" ? 1 : 0;
                    WindowCount+=RowEl.find("td[type=Construct] span workwindowch1").text()=="true" ? 1 : 0;
                    WindowCount+=RowEl.find("td[type=Construct] span workwindowch2").text()=="true" ? 1 : 0;

                    WindowCount+=RowEl.find("td[type=Construct] span stvorkawindowch").text()=="true" ? 1 : 0;
                    WindowCount+=RowEl.find("td[type=Construct] span stvorkawindowch1").text()=="true" ? 1 : 0;
                    WindowCount+=RowEl.find("td[type=Construct] span stvorkawindowch2").text()=="true" ? 1 : 0;

                    WindowCount+=RowEl.find("td[type=Construct] span framugawindowch").text()=="true" ? 1 : 0;
					
                    if(WindowCount!=0)
                        switch (o.Construct.WindowCount){
                            case "1"://Зависит от кол-ва
                                //Если больше
                                switch (o.Construct.WindowMore){
                                    case "": case null:
                                        Sum+=WindowCount*parseFloat(o.Construct.WindowSum);
                                        break;
                                    default:
                                        if(WindowCount>parseInt(o.Construct.WindowMore))
                                            Sum+=(WindowCount-parseInt(o.Construct.WindowMore))*parseFloat(o.Construct.WindowSum);
                                        break;
                                }
                                break;
                            default://Не зависит от кол-ва
                                Sum+=parseFloat(o.Construct.WindowSum);
                                break;
                        };
                };
                //Фрамуга
                if(o.Construct.Framuga==1 & RowEl.find("td[type=Construct] span framugach").text()=="true")
                    Sum+=parseFloat(o.Construct.FramugaSum);
                //Навесы
                if(o.Construct.Petlya==1)
                {
                    var PetlyaSum=parseFloat(o.Construct.PetlyaSum);

                    var PetlyaCount=0;
                    var WorkPetlya1=RowEl.find("td[type=Construct] span workpetlya").text();
                    PetlyaCount+=WorkPetlya1!="" ? parseInt(WorkPetlya1) : WorkPetlya1;
                    var StvorkaPetlya1=RowEl.find("td[type=Construct] span stvorkapetlya").text();
                    PetlyaCount+=StvorkaPetlya1!="" ? parseInt(StvorkaPetlya1) : StvorkaPetlya1;
                    if(PetlyaCount!=0)
                        if(o.Construct.PetlyaCount==1)//Зависит от кол-ва
                        {
                            if(o.Construct.PetlyaMore==null)//Не заполненно поле болшльше
                                PetlyaSum=PetlyaCount*parseFloat(o.Construct.PetlyaSum);
                            //Заполненно поле больше, т.е. расчитываем по разнице
                            if(PetlyaCount>parseInt(o.Construct.PetlyaMore) & o.Construct.PetlyaMore!=null)
                            {
                                PetlyaSum=(PetlyaCount-parseInt(o.Construct.PetlyaMore))*parseFloat(o.Construct.PetlyaSum);
                            }
                            else
                                PetlyaSum=0;
                        }
                        else
                            PetlyaSum=parseFloat(o.Construct.PetlyaSum);
                    Sum+=PetlyaSum;
                };
                //Навесы на рабочей створке
                if(o.Construct.PetlyaWork==1 & RowEl.find("td[type=Construct] span workpetlya").text()!="")
                {
                    var PetlyaSum=parseFloat(o.Construct.PetlyaWorkSum);

                    var PetlyaCount=0;
                    PetlyaCount+=parseInt(RowEl.find("td[type=Construct] span workpetlya").text());
                    if(PetlyaCount!=0)
                        if(o.Construct.PetlyaWorkCount==1)//Зависит от кол-ва
                        {
                            if(o.Construct.PetlyaWorkMore==null)//Не заполненно поле болшльше
                                PetlyaSum=PetlyaCount*parseFloat(o.Construct.PetlyaWorkSum);
                            //Заполненно поле больше, т.е. расчитываем по разнице
                            if(PetlyaCount>parseInt(o.Construct.PetlyaWorkMore) & o.Construct.PetlyaWorkMore!=null)
                            {
                                PetlyaSum=(PetlyaCount-parseInt(o.Construct.PetlyaWorkMore))*parseFloat(o.Construct.PetlyaWorkSum);
                            }
                            else
                                PetlyaSum=0;
                        }
                        else
                            PetlyaSum=parseFloat(o.Construct.PetlyaWorkSum);
                    Sum+=PetlyaSum;
                };
                //Навесы на второй створке
                if(o.Construct.PetlyaStvorka==1 & RowEl.find("td[type=Construct] span stvorkapetlya").text()!="")
                {
                    var PetlyaSum=parseFloat(o.Construct.PetlyaStvorkaSum);

                    var PetlyaCount=0;
                    PetlyaCount+=parseInt(RowEl.find("td[type=Construct] span stvorkapetlya").text());
                    if(PetlyaCount!=0)
                        if(o.Construct.PetlyaStvorkaCount==1)//Зависит от кол-ва
                        {
                            if(o.Construct.PetlyaStvorkaMore==null)//Не заполненно поле болшльше
                                PetlyaSum=PetlyaCount*parseFloat(o.Construct.PetlyaStvorkaSum);
                            //Заполненно поле больше, т.е. расчитываем по разнице
                            if(PetlyaCount>parseInt(o.Construct.PetlyaStvorkaMore) & o.Construct.PetlyaStvorkaMore!=null)
                            {
                                PetlyaSum=(PetlyaCount-parseInt(o.Construct.PetlyaStvorkaMore))*parseFloat(o.Construct.PetlyaStvorkaSum);
                            }
                            else
                            //В случае если петель меньше, тогда з/п не начисляем
                                PetlyaSum=0;
                        }
                        else
                            PetlyaSum=parseFloat(o.Construct.PetlyaStvorkaSum);
                    Sum+=PetlyaSum;
                };
                //Ребра жесткости
                if(o.Construct.Stiffener==1)
                    if(o.Construct.StiffenerW==1)//Зависит от кв.м.
                    {
                        Sum+=parseFloat(H)*parseFloat(W)*parseFloat(o.Construct.StiffenerSum)/1000000;
                    }
                    else
                    {
                        Sum+=parseFloat(o.Construct.StiffenerSum);
                    };
                //Площадь двери
                if(o.Construct.M2==1)
                    Sum+=parseFloat(H)*parseFloat(W)*parseFloat(o.Construct.M2Sum)/1000000;
                //Антипаника
                if(o.Construct.Antipanik==1 & RowEl.find("td[type=Construct] span Antipanik").text()=="1")
                    Sum+=parseFloat(o.Construct.AntipanikSum);
                //Антипаника
                if(o.Construct.Otboynik==1 & RowEl.find("td[type=Construct] span Otboynik").text()=="1")
                    Sum+=parseFloat(o.Construct.OtboynikSum);
                //Антипаника
                if(o.Construct.Wicket==1 & RowEl.find("td[type=Construct] span Wicket").text()=="1")
                    Sum+=parseFloat(o.Construct.WicketSum);
                //Антипаника
                if(o.Construct.BoxLock==1 & RowEl.find("td[type=Construct] span BoxLock").text()=="1")
                    Sum+=parseFloat(o.Construct.BoxLockSum);
                //Ответка
                if(o.Construct.Otvetka==1 & RowEl.find("td[type=Construct] span Otvetka").text()=="1")
                    Sum+=parseFloat(o.Construct.OtvetkaSum);
                //Утепление
                if(o.Construct.Isolation==1 & RowEl.find("td[type=Construct] span Isolation").text()=="1")
                    Sum+=parseFloat(o.Construct.IsolationSum);
                //Вент решетка
                if(o.Construct.Grid==1)
                {
                    var CountGrid=0;
                    CountGrid+=RowEl.find("td[type=Construct] span WorkUpGridCh").text().toUpperCase()=="TRUE" ? 1 : 0;
                    CountGrid+=RowEl.find("td[type=Construct] span WorkDownGridCh").text().toUpperCase()=="TRUE" ? 1 : 0;
                    CountGrid+=RowEl.find("td[type=Construct] span StvorkaUpGridCh").text().toUpperCase()=="TRUE" ? 1 : 0;
                    CountGrid+=RowEl.find("td[type=Construct] span StvorkaDownGridCh").text().toUpperCase()=="TRUE" ? 1 : 0;
                    CountGrid+=RowEl.find("td[type=Construct] span FramugaUpGridCh").text().toUpperCase()=="TRUE" ? 1 : 0;
                    CountGrid+=RowEl.find("td[type=Construct] span FramugaDownGridCh").text().toUpperCase()=="TRUE" ? 1 : 0;
                    Sum+=o.Construct.GridCount==1 ? CountGrid*parseFloat(o.Construct.GridSum) : parseFloat(o.Construct.GridSum);
                };
			};
			//Выводим значения в соответствующие поля
			switch(o.StepName){
				case "Лазер": RowEl.find("td[type=Cost] span costlaser").text(Sum); break;
				case "Сгибка": RowEl.find("td[type=Cost] span CostSgibka").text(Sum); break;
				case "Сварка":
                    console.log(o.StepName + " -= "+ Sum);
					RowEl.find("td[type=Cost] span CostSvarka").text(Sum); break;
				case "Рамка": RowEl.find("td[type=Cost] span CostFrame").text(Sum); break;
				case "МДФ": RowEl.find("td[type=Cost] span CostMdf").text(Sum); break;
				case "Сборка": RowEl.find("td[type=Cost] span CostSborka").text(Sum); break;
				case "Покраска": RowEl.find("td[type=Cost] span CostColor").text(Sum); break;
				case "Сборка МДФ": RowEl.find("td[type=Cost] span CostSborkaMdf").text(Sum); break;
				case "Упаковка": RowEl.find("td[type=Cost] span CostUpak").text(Sum); break;
				case "Отгрузка": RowEl.find("td[type=Cost] span CostShpt").text(Sum); break;
			};
			//Проверим на 0 зарплату и в этом случаем подсветим кнопку красным
			var CostTR=RowEl.find("td[type=Cost]");
			CostTR.find("button").css("border","none");
			var CostZero=false;
			if(CostTR.find("span CostSvarka").text()=="0") CostZero=true;
			if(CostTR.find("span CostSborka").text()=="0") CostZero=true;
			if(CostTR.find("span CostColor").text()=="0") CostZero=true;
			if(CostTR.find("span CostUpak").text()=="0") CostZero=true;
			if(CostZero) 
			{
				CostTR.find("button").css("border","2px solid red");
			}
			else
				CostTR.find("button").css("border","2px solid green");
		}
	);
}
//Расчет зарплаты для всех позиций
function OrderPrlCalcAllRows(){
	$("#orderDlalogBtnPrlCalc").hide();
	$("#orderDlalogImgPrlCalc").show();
	for(var i=0; i<$("#OrderDialogTable").find("tr").length;i++)
	{
		OrderPrlCalcAddEditRow($("#OrderDialogTable tr:eq("+i+")").attr("id"));
		if($("#OrderDialogTable tr:eq("+i+")").attr("Status")=="Load")
			$("#OrderDialogTable tr:eq("+i+")").attr("Status","Edit");
	};
	$("#orderDlalogBtnPrlCalc").show();
	$("#orderDlalogImgPrlCalc").hide();
}
//Попытка привести к общей функции для удобства
function OrderPrlCalc(DoorType, Step, H, W, S, Open, Framug){
	var ret=new Array();
	ret[0]={"Note":"Описание", "Sum":0};
	
	return ret;
}

//-------Печать нарядов--------
/*
* Печать нарядов производится по заранее сохраненным позициям
*/
function OrderPrintNaryads(){
	//Определим сохренены позиции
	if($("#OrderDialogTable").find("tr").length!=$("#OrderDialogTable").find("tr[status=Load]").length) {jqUI.alert({text:"<img src='images/AlertError.png' style='float:left; width:50px'> <h2>Есть не сохраненные позиции</h2>", title:""}); return false;};
	//Определяем расчитана стоимость
	for(var i=0; i<$("#OrderDialogTable").find("tr").length;i++)
	{
		var CostTD=$("#OrderDialogTable tr:eq("+i+") td[type=Cost]");
		var ZeroCost=false;
		if(parseFloat(CostTD.find("span CostSvarka").text())==0)
			ZeroCost=true;
		if(parseFloat(CostTD.find("span CostSborka").text())==0)
			ZeroCost=true;
		if(parseFloat(CostTD.find("span CostColor").text())==0)
			ZeroCost=true;
		if(parseFloat(CostTD.find("span CostUpak").text())==0)
			ZeroCost=true;
		if(ZeroCost)
		{
			jqUI.alert({text:"<img src='images/AlertError.png' style='float:left; width:50px'><h3>Есть позиции с не расчитанной стоимостью работ</h3>", title:""}); return false;
		};
	};
	//В случае успеха передадим скрипту
	$("#orderDlalogBtnPrintNaryad").hide();
	$("#orderDlalogImgPrintNaryad").show();
	$.post(
		"orders/order.php",
		{"method":"PrintNaryad","idOrder":$("#orderDialogInputID").val()},
		function (data){
			$("#orderDlalogBtnPrintNaryad").show();
			$("#orderDlalogImgPrintNaryad").hide();
			if(data=="ok")
			{
				window.open("orders/OrderNaryad.pdf",'_blank');
			}
			else
			{
				jqUI.alert("<img src='images/AlertError.png' style='float:left; width:50px'> <h2>Ошибка печати: "+data+"</h2>");
			};
		}
	)
}

//Печать подробного списка выполнения позиций
function OrderSummaryPrint(){
	var idOrder=$("#orderDialogInputID").val();
	$.post(
		"orders/order.php",
		{"method":"SummaryPrint","idOrder":idOrder},
		function(data){
			window.open('orders/OrderSummary.pdf','_blank');
		}
	);
}